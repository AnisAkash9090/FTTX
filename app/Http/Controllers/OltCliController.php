<?php

namespace App\Http\Controllers;

use App\Events\OltCliOutput;
use App\Models\OltConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;

class OltCliController extends Controller
{
    /**
     * Show CLI interface page
     */
    public function index()
    {
        $oltConfigs = OltConfig::where('sts', 'active')->get();
        return view('olt_cli', compact('oltConfigs'));
    }

    /**
     * Connect to OLT via SSH or Telnet
     * POST /olt-cli/connect
     *
     * Accepts an optional username/password from the request body.
     * If omitted (or blank), falls back to the OLT's saved credentials.
     */
    public function connect(Request $request)
    {
        try {
            $validated = $request->validate([
                'olt_id'   => 'required|integer',
                'protocol' => 'required|in:ssh,telnet',
                'username' => 'nullable|string|max:255',
                'password' => 'nullable|string|max:255',
            ]);

            // Get OLT configuration
            $olt = OltConfig::findOrFail($validated['olt_id']);

            $host = $olt->olt_ip;
            $protocol = $validated['protocol'];
            $port = $protocol === 'ssh' ? 22 : 23;

            // Prefer manually entered credentials; fall back to saved OLT credentials
            $username = $validated['username'] ?? $olt->useradmin ?? 'admin';
            $password = $validated['password'] ?? $olt->pass ?? 'admin';

            // Test connection
            if ($protocol === 'ssh') {
                $this->testSsh($host, $port, $username, $password);
            } else {
                $this->testTelnet($host, $port, $username, $password);
            }

            // Create session
            $sessionId = (string) Str::uuid();
            Cache::put('olt_cli_session_' . $sessionId, [
                'host'     => $host,
                'port'     => $port,
                'username' => $username,
                'password' => $password,
                'protocol' => $protocol,
                'olt_id'   => $olt->id,
                'olt_name' => $olt->olt_name,
                'user_id'  => auth()->id(),
                'ssh'      => null,  // Will store SSH connection if needed
                'telnet'   => null,  // Will store Telnet socket if needed
                'created_at' => now(),
            ], now()->addHours(2));

            Log::info('CLI Session created', [
                'session_id' => $sessionId,
                'olt' => $olt->olt_name,
                'used_manual_credentials' => isset($validated['username']) || isset($validated['password']),
            ]);

            // Broadcast connection success
            broadcast(new OltCliOutput($sessionId, 'connected', "Connected to {$olt->olt_name} via " . strtoupper($protocol)));

            return response()->json([
                'status'     => 'success',
                'session_id' => $sessionId,
                'olt_name'   => $olt->olt_name,
                'protocol'   => $protocol,
                'message'    => "Connected to {$olt->olt_name}",
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('CLI Connect error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Execute CLI command
     * POST /olt-cli/command
     */
    public function command(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'command'    => 'required|string|max:2000',
            ]);

            $sessionId = $validated['session_id'];
            $command = trim($validated['command']);

            // Get session
            $session = Cache::get('olt_cli_session_' . $sessionId);
            if (!$session) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Session expired or invalid',
                ], 401);
            }

            // Security check
            if ($this->isCommandBlocked($command)) {
                broadcast(new OltCliOutput($sessionId, 'error', '❌ Command blocked for security reasons'));
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Command blocked for security',
                ], 400);
            }

            // Echo command
            broadcast(new OltCliOutput($sessionId, 'echo', $command));

            // Execute
            $output = '';
            if ($session['protocol'] === 'ssh') {
                $output = $this->runSshCommand(
                    $session['host'],
                    $session['port'],
                    $session['username'],
                    $session['password'],
                    $command,
                    $sessionId
                );
            } else {
                $output = $this->runTelnetCommand(
                    $session['host'],
                    $session['port'],
                    $session['username'],
                    $session['password'],
                    $command,
                    $sessionId
                );
            }

            // Return bulk command output through HTTP; Reverb has a message-size limit.
            // Keep session alive
            $session['last_activity'] = now();
            Cache::put('olt_cli_session_' . $sessionId, $session, now()->addHours(2));

            return response()->json([
                'status' => 'success',
                'output' => $output,
            ]);

        } catch (\Throwable $e) {
            Log::error('CLI Command error: ' . $e->getMessage());
            broadcast(new OltCliOutput($sessionId ?? '', 'error', '❌ Error: ' . $e->getMessage()));
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disconnect session
     * POST /olt-cli/disconnect
     */
    public function disconnect(Request $request)
    {
        try {
            $validated = $request->validate(['session_id' => 'required|string']);
            $sessionId = $validated['session_id'];

            Cache::forget('olt_cli_session_' . $sessionId);
            broadcast(new OltCliOutput($sessionId, 'disconnected', 'Session closed.'));

            Log::info('CLI Session disconnected', ['session_id' => $sessionId]);

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ===== SSH METHODS =====
     */

    protected function testSsh($host, $port, $user, $pass)
    {
        $ssh = new SSH2($host, $port, 10);
        if (!$ssh->login($user, $pass)) {
            throw new \Exception('SSH login failed. Check credentials.');
        }
        $ssh->disconnect();
    }

    protected function runSshCommand($host, $port, $user, $pass, $cmd, $sessionId)
    {
        $ssh = new SSH2($host, $port, 15);
        $ssh->setTimeout(20);

        if (!$ssh->login($user, $pass)) {
            throw new \Exception('SSH login failed');
        }

        $ssh->enablePTY();
        $ssh->setTimeout(15);

        // Clear login banner
        $ssh->read();

        // Try enable mode
        $ssh->write("enable\n");
        $buf = $ssh->read();
        if (str_contains($buf, ':') || str_contains(strtolower($buf), 'password')) {
            $ssh->write($pass . "\n");
            $ssh->read();
        }

        // Disable paging
        $ssh->write("terminal length 0\n");
        $ssh->read();

        // Send command
        $ssh->write($cmd . "\n");

        // Read output with timeout
        $output = '';
        $timeout = 15;
        $start = time();

        while ((time() - $start) < $timeout) {
            $chunk = $ssh->read();
            if ($chunk === false || $chunk === '') {
                usleep(100000); // 100ms
                continue;
            }
            $output .= $chunk;

            // Handle paging
            if (str_contains($output, '--More--')) {
                $ssh->write(" ");
                $output = str_replace('--More--', '', $output);
                $start = time(); // Reset timeout
                continue;
            }

            // Stop on prompt
            if (preg_match('/(>|#|\]|\$|\(config.*\)#)\s*$/m', $output)) {
                break;
            }
        }

        $ssh->disconnect();
        return $this->cleanOutput($output);
    }

    /**
     * ===== TELNET METHODS =====
     */

    protected function testTelnet($host, $port, $user, $pass)
    {
        $fp = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$fp) {
            throw new \Exception("Telnet failed: {$errstr}");
        }

        stream_set_timeout($fp, 5);

        // Wait for login prompt
        $data = $this->telnetRead($fp, 2);
        if (!str_contains($data, ':') && !str_contains($data, 'ogin')) {
            fclose($fp);
            throw new \Exception('Telnet: No login prompt');
        }

        // Send username
        fwrite($fp, $user . "\r\n");
        sleep(1);

        // Send password
        fwrite($fp, $pass . "\r\n");
        sleep(1);

        // Verify login
        $data = $this->telnetRead($fp, 2);
        if (!str_contains($data, '#') && !str_contains($data, '>')) {
            fclose($fp);
            throw new \Exception('Telnet: Login failed');
        }

        fwrite($fp, "exit\r\n");
        fclose($fp);
    }

    protected function runTelnetCommand($host, $port, $user, $pass, $cmd, $sessionId)
    {
        $fp = @fsockopen($host, $port, $errno, $errstr, 10);
        if (!$fp) {
            throw new \Exception("Telnet connect failed: {$errstr}");
        }

        stream_set_timeout($fp, 3);

        // === LOGIN ===
        $this->telnetRead($fp, 2); // Read login prompt

        fwrite($fp, $user . "\r\n");
        $this->telnetRead($fp, 2); // Read password prompt

        fwrite($fp, $pass . "\r\n");
        $afterLogin = $this->telnetRead($fp, 3); // Read prompt after login

        if (empty(trim($afterLogin))) {
            fclose($fp);
            throw new \Exception('Telnet login failed');
        }

        // === ENABLE MODE (if not already) ===
        if (strpos($afterLogin, '#') === false) {
            fwrite($fp, "enable\r\n");
            $checkEnable = $this->telnetRead($fp, 2);

            if (str_contains($checkEnable, ':')) {
                fwrite($fp, $pass . "\r\n");
                $this->telnetRead($fp, 2);
            }
        }

        // === DISABLE PAGING ===
        fwrite($fp, "terminal length 0\r\n");
        $this->telnetRead($fp, 2);

        // === SEND COMMAND ===
        fwrite($fp, $cmd . "\r\n");

        // === READ OUTPUT ===
        $output = $this->telnetRead($fp, 10);

        // Handle --More-- pagination
        while (str_contains($output, '--More--')) {
            fwrite($fp, " ");
            $more = $this->telnetRead($fp, 3);
            $output = str_replace('--More--', '', $output) . $more;
        }

        fclose($fp);
        return $this->cleanOutput($output);
    }

    protected function telnetRead($fp, $seconds = 2)
    {
        $data = '';
        $endTime = microtime(true) + $seconds;

        while (microtime(true) < $endTime) {
            $read = [$fp];
            $write = $except = null;

            $remain = max(0, $endTime - microtime(true));
            if ($remain <= 0) break;

            $changed = @stream_select($read, $write, $except, (int)$remain, (int)(($remain - (int)$remain) * 1e6));
            if ($changed === false || $changed === 0) break;

            $chunk = fread($fp, 4096);
            if ($chunk === false || $chunk === '') break;

            $data .= $chunk;

            // Check for prompt to stop early
            if (preg_match('/[#>\$]\s*$/', trim($data))) {
                usleep(200000); // Extra wait for trailing data
                break;
            }
        }

        return $data;
    }

    /**
     * ===== UTILITIES =====
     */

    protected function cleanOutput($raw)
    {
        if (!$raw) return '(no output)';

        // Remove control characters (except \n \r \t)
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $raw);

        // Fix encoding
        if (!mb_check_encoding($clean, 'UTF-8')) {
            $clean = mb_convert_encoding($clean, 'UTF-8', 'UTF-8');
        }
        $clean = iconv('UTF-8', 'UTF-8//IGNORE', $clean) ?: '';

        return trim($clean) ?: '(no output)';
    }

    protected function isCommandBlocked($cmd)
    {
        $blocked = [
            'rm -rf', 'mkfs', 'dd if=', 'shutdown', 'reboot',
            'init 0', 'init 6', 'halt', 'poweroff', 'kill -9'
        ];

        $lower = strtolower($cmd);
        foreach ($blocked as $bad) {
            if (str_contains($lower, $bad)) {
                return true;
            }
        }

        return false;
    }
}