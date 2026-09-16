<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class ServerHealthController extends Controller
{
    public function index()
    {
        // 1. PHP Version
        $phpVersion = phpversion();

        // 2. Active Sessions with Details
        $sessionDetails = $this->getSessionDetails();
        $sessions = count($sessionDetails['sessions']);

        // 3. MySQL Database Size & Connection Info
        $dbInfo = $this->getDatabaseInfo();

        // 4. /var Storage Capacity
        $varStorage = $this->getStorageInfo('/var');

        // 5. Reverb Status
        $reverbStatus = @fsockopen('127.0.0.1', 8080, $errno, $errstr, 1) ? 'Running' : 'Stopped';

        // 6. System Information
        $systemInfo = $this->getSystemInfo();

        // 7. Process Information (Top CPU/RAM consumers)
        $topProcesses = $this->getTopProcesses();

        // 8. Network Information
        $networkInfo = $this->getNetworkInfo();

        // 9. Disk I/O Stats
        $diskStats = $this->getDiskIOStats();

        // 10. Logged-in Users
        $loggedInUsers = $this->getLoggedInUsers();

        // 11. Environment Info
        $environmentInfo = [
            'app_env' => env('APP_ENV'),
            'app_debug' => env('APP_DEBUG'),
            'queue_driver' => env('QUEUE_CONNECTION', 'sync'),
            'cache_driver' => env('CACHE_DRIVER', 'file'),
        ];

        return view('server-health', compact(
            'phpVersion',
            'sessions',
            'sessionDetails',
            'dbInfo',
            'varStorage',
            'reverbStatus',
            'systemInfo',
            'topProcesses',
            'networkInfo',
            'diskStats',
            'loggedInUsers',
            'environmentInfo'
        ));
    }

    /**
     * Get detailed session information
     */
  /**
     * Get detailed session information
     */
    private function getSessionDetails()
    {
        $sessionPath = storage_path('framework/sessions');
        $sessions = [];

        if (is_dir($sessionPath)) {
            $files = File::files($sessionPath);
            
            foreach ($files as $file) {
                // Skip hidden files like .gitignore
                if ($file->getFilename()[0] === '.') {
                    continue;
                }

                $sessionId = $file->getFilename();
                $content = @unserialize(file_get_contents($file->getPathname()));
                
                $userId = 'Guest';

                // Laravel stores the auth ID dynamically (e.g., 'login_web_59ba...')
                if (is_array($content)) {
                    foreach ($content as $key => $value) {
                        if (str_starts_with($key, 'login_') && !empty($value)) {
                            $userId = $value;
                            break;
                        }
                    }
                }
                
                $sessions[] = [
                    'id' => substr($sessionId, 0, 20) . '...',
                    'full_id' => $sessionId,
                    'size' => formatBytes($file->getSize()),
                    'created' => date('Y-m-d H:i:s', $file->getCTime()),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                    'user_id' => $userId,
                    'ip' => $content['_ip'] ?? 'N/A',
                ];
            }
        }

        // Sort by most recently modified so active users appear at the top
        usort($sessions, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return [
            'total' => count($sessions),
            'sessions' => array_slice($sessions, 0, 10) // Show last 10
        ];
    }

    /**
     * Get database connection info and size
     */
    private function getDatabaseInfo()
    {
        $dbName = env('DB_DATABASE');
        
        // Database size
        $dbSizeQuery = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
        $dbSize = $dbSizeQuery[0]->size_mb ?? 0;

        // Active connections
        try {
            $connections = DB::select("SHOW PROCESSLIST");
            $activeQueries = collect($connections)->filter(fn($c) => $c->Command !== 'Sleep')->count();
        } catch (\Exception $e) {
            $connections = [];
            $activeQueries = 0;
        }

        // Database variables
        $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'");
        $maxConnectionsValue = $maxConnections[0]->Value ?? 'N/A';

        return [
            'name' => $dbName,
            'size_mb' => $dbSize,
            'total_connections' => count($connections),
            'active_queries' => $activeQueries,
            'max_connections' => $maxConnectionsValue,
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
        ];
    }

    /**
     * Get storage information
     */
    private function getStorageInfo($path)
    {
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;
        $percent = round(($used / $total) * 100, 2);

        return [
            'path' => $path,
            'total_gb' => round($total / 1073741824, 2),
            'used_gb' => round($used / 1073741824, 2),
            'free_gb' => round($free / 1073741824, 2),
            'percent' => $percent,
            'status' => $percent > 80 ? 'critical' : ($percent > 60 ? 'warning' : 'ok'),
        ];
    }

    /**
     * Get system information
     */
    private function getSystemInfo()
    {
        // Get uptime
        $uptime = shell_exec('uptime -p 2>/dev/null') ?: 'N/A';

        // Get load average
        $loadAvg = sys_getloadavg();
        $cores = (int) shell_exec('nproc');

        // Get CPU model
        $cpuModel = shell_exec("grep -m 1 'model name' /proc/cpuinfo | cut -d ':' -f2 2>/dev/null") ?: 'N/A';

        // Get OS info
        $osInfo = php_uname('s') . ' ' . php_uname('r');

        return [
            'uptime' => trim($uptime),
            'load_average' => round($loadAvg[0], 2),
            'load_5min' => round($loadAvg[1], 2),
            'load_15min' => round($loadAvg[2], 2),
            'cpu_cores' => $cores,
            'cpu_model' => trim($cpuModel),
            'os' => $osInfo,
            'hostname' => gethostname(),
            'kernel' => php_uname('r'),
        ];
    }

    /**
     * Get top processes by CPU and RAM
     */
    private function getTopProcesses()
    {
        $topCpuCmd = "ps aux --sort=-%cpu | head -6 | tail -5";
        $topRamCmd = "ps aux --sort=-%mem | head -6 | tail -5";

        $topCpuOutput = shell_exec($topCpuCmd);
        $topRamOutput = shell_exec($topRamCmd);

        $topCpuProcesses = [];
        $topRamProcesses = [];

        if ($topCpuOutput) {
            foreach (explode("\n", trim($topCpuOutput)) as $line) {
                if (trim($line)) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 11) {
                        $topCpuProcesses[] = [
                            'pid' => $parts[1],
                            'cpu' => $parts[2],
                            'mem' => $parts[3],
                            'command' => implode(' ', array_slice($parts, 10)),
                        ];
                    }
                }
            }
        }

        if ($topRamOutput) {
            foreach (explode("\n", trim($topRamOutput)) as $line) {
                if (trim($line)) {
                    $parts = preg_split('/\s+/', trim($line));
                    if (count($parts) >= 11) {
                        $topRamProcesses[] = [
                            'pid' => $parts[1],
                            'cpu' => $parts[2],
                            'mem' => $parts[3],
                            'command' => implode(' ', array_slice($parts, 10)),
                        ];
                    }
                }
            }
        }

        return [
            'top_cpu' => $topCpuProcesses,
            'top_ram' => $topRamProcesses,
        ];
    }

    /**
     * Get network information
     */
    private function getNetworkInfo()
    {
        $ipOutput = shell_exec("hostname -I 2>/dev/null") ?: 'N/A';
        $netstatOutput = shell_exec("netstat -tuln 2>/dev/null | grep LISTEN | wc -l") ?: 'N/A';
        $dnsServers = shell_exec("cat /etc/resolv.conf 2>/dev/null | grep nameserver | head -2") ?: 'N/A';

        return [
            'ip_address' => trim($ipOutput),
            'listening_ports' => trim($netstatOutput),
            'dns_servers' => trim($dnsServers),
        ];
    }

    /**
     * Get disk I/O statistics
     */
    private function getDiskIOStats()
    {
        $iostatOutput = shell_exec("iostat -dx 1 2 2>/dev/null | tail -n +4") ?: 'N/A';
        
        return [
            'iostat' => $iostatOutput,
        ];
    }

    /**
     * Get logged-in users
     */
    private function getLoggedInUsers()
    {
        $whoOutput = shell_exec("who 2>/dev/null") ?: 'No users logged in';
        $users = [];

        foreach (explode("\n", trim($whoOutput)) as $line) {
            if (trim($line)) {
                $parts = preg_split('/\s+/', trim($line));
                $users[] = [
                    'user' => $parts[0] ?? 'N/A',
                    'terminal' => $parts[1] ?? 'N/A',
                    'login_time' => implode(' ', array_slice($parts, 2, 4)) ?? 'N/A',
                ];
            }
        }

        return $users;
    }

    /**
     * Endpoint for live chart data
     */
    public function getLiveMetrics()
    {
        // CPU Usage
        $cpuLoad = sys_getloadavg();
        $cores = (int) shell_exec('nproc');
        $cpuPercent = min(100, round(($cpuLoad[0] / $cores) * 100, 2));

        // RAM Usage
        $free = shell_exec('free -m');
        $free_arr = explode("\n", trim($free));
        $mem = explode(" ", preg_replace('/\s+/', ' ', $free_arr[1]));
        $ramTotal = $mem[1];
        $ramUsed = $mem[2];
        $ramPercent = round(($ramUsed / $ramTotal) * 100, 2);

        // Disk I/O
        $diskReads = shell_exec("cat /proc/diskstats | awk '{reads+=$4} END {print reads}'") ?: 0;
        $diskWrites = shell_exec("cat /proc/diskstats | awk '{writes+=$8} END {print writes}'") ?: 0;

        return response()->json([
            'cpu' => $cpuPercent,
            'ram' => $ramPercent,
            'disk_reads' => intval($diskReads),
            'disk_writes' => intval($diskWrites),
            'time' => now()->format('H:i:s')
        ]);
    }
}

/**
 * Helper function to format bytes to human readable
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}