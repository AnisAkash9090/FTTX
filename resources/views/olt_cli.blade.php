<x-app-layout>

<div class="container-fluid olt-cli-page mt-4">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- HEADER -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->

    <div class="row mb-4 cli-hero">
        <div class="col-12">
            <div class="cli-kicker"><span></span> NETWORK OPERATIONS / LIVE CONSOLE</div>
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                <div>
                    <h2 class="mb-1"><i class="fas fa-terminal"></i> OLT CLI Terminal</h2>
                    <p class="cli-subtitle mb-0">Interactive command channel for remote optical line terminals</p>
                </div>
                <div class="cli-hero-readout"><strong id="heroConnectionState">STANDBY</strong><small>SESSION STATUS</small></div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- MAIN CONTAINER -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->

    <div class="row">
        <!-- LEFT: OLT Selection & Connection -->
        <div class="col-xl-3 col-lg-4 mb-4">
            <div class="card shadow-sm cli-control-panel">
                <div class="card-header">
                    <h6 class="mb-0">
                        <span class="panel-index">01</span> CONNECTION
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Status Indicator -->
                    <div class="alert alert-secondary mb-4" id="statusAlert">
                        <i class="fas fa-circle text-warning"></i> <span>Disconnected</span>
                    </div>

                    <!-- OLT Selection -->
                    <div class="mb-3">
                        <label class="form-label"><span>01</span> TARGET DEVICE</label>
                        <select class="form-control" id="oltSelect">
                            <option value="">-- Choose OLT --</option>
                            @foreach($oltConfigs as $olt)
                            <option value="{{ $olt->id }}"
                                    data-protocol="{{ $olt->typeconnection }}"
                                    data-name="{{ $olt->olt_name }}">
                                {{ $olt->olt_name }} ({{ strtoupper($olt->typeconnection) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Connection Type -->
                    <div class="mb-3">
                        <label class="form-label"><span>02</span> TRANSPORT</label>
                        <select class="form-control" id="protocolSelect">
                            <option value="ssh">SSH (Port 22)</option>
                            <option value="telnet">Telnet (Port 23)</option>
                        </select>
                    </div>

                    <!-- Connect Button -->
                    <button class="btn btn-success w-100 mb-2 cli-connect-btn" id="connectBtn">
                        <i class="fas fa-power-off"></i> Initialize Link
                    </button>

                    <!-- Disconnect Button (Hidden) -->
                    <button class="btn btn-danger w-100 mb-3 cli-connect-btn" id="disconnectBtn" style="display: none;">
                        <i class="fas fa-power-off"></i> Close Session
                    </button>

                    <hr>

                    <!-- Session Info -->
                    <div id="sessionInfo" class="session-info" style="display: none;">
                        <small class="text-muted">
                            <strong>SESSION ID</strong><br>
                            <code id="sessionId" style="font-size: 10px;"></code>
                        </small>
                        <hr>
                        <small class="text-muted">
                            <strong>CONNECTED DEVICE</strong><br>
                            <span id="connectedOlt"></span>
                        </small>
                    </div>

                    <!-- History -->
                    <div class="mt-4 command-history-panel">
                        <label class="form-label"><span>03</span> COMMAND ARCHIVE</label>
                        <div id="historyList" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 8px;">
                            <small class="text-muted">No history</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary mt-2 w-100" id="clearHistoryBtn"><i class="fas fa-eraser"></i> Clear Archive</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Terminal -->
        <div class="col-xl-9 col-lg-8 mb-4">
            <div class="card shadow-sm cli-terminal-panel">
                <div class="card-header terminal-toolbar">
                    <div class="terminal-title"><span class="panel-index">02</span><i class="fas fa-wave-square"></i> LIVE COMMAND STREAM</div>
                    <div class="terminal-lights"><span></span><span></span><span></span></div>
                    <div class="terminal-mode">MATRIX / UTF-8</div>
                </div>
                <div class="card-body p-0 terminal-frame">

                    <!-- Terminal Output -->
                    <div id="terminalOutput" class="terminal-output-area" style="
                        flex: 1;
                        overflow-y: auto;
                        padding: 15px;
                        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                        font-size: 13px;
                        color: #0f0;
                        white-space: pre-wrap;
                        word-wrap: break-word;
                        line-height: 1.5;
                    ">
                        <span class="terminal-welcome">SYSTEM READY // SELECT A TARGET TO OPEN THE COMMAND CHANNEL</span><br>
                        <span class="terminal-muted">Awaiting authenticated transport...</span><br>
                    </div>

                    <!-- Input Area -->
                    <div class="terminal-input-dock">
                        <div class="input-label"><span class="prompt-caret">›</span> COMMAND INPUT <span class="input-hint">ENTER TO EXECUTE</span></div>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   id="commandInput"
                                   placeholder="show running-config"
                                   disabled
                                   style="background: #1e1e1e; color: #0f0; border: 1px solid #444; font-family: monospace;">
                            <button class="btn btn-success" id="sendBtn" disabled>
                                <i class="fas fa-arrow-up"></i> Execute
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-keyboard"></i> Press Enter or click Send
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- STYLES -->
<!-- ═══════════════════════════════════════════════════════════════════ -->

<style>
    .olt-cli-page {
        --cli-bg: #07100d;
        --cli-panel: #0c1713;
        --cli-panel-2: #101e18;
        --cli-line: rgba(111, 255, 151, 0.18);
        --cli-green: #71ff9a;
        --cli-green-dim: #3fbf72;
        --cli-ink: #d9ffe2;
        color: var(--cli-ink);
    }

    .cli-hero {
        border-bottom: 1px solid var(--cli-line);
        padding-bottom: 18px;
    }

    .cli-kicker, .terminal-mode, .input-label, .form-label, .panel-index,
    .cli-hero-readout small {
        color: var(--cli-green-dim);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.14em;
    }

    .cli-kicker span {
        display: inline-block;
        width: 7px;
        height: 7px;
        margin-right: 7px;
        border-radius: 50%;
        background: var(--cli-green);
        box-shadow: 0 0 12px var(--cli-green);
    }

    .cli-hero h2 { color: #10291c; font-weight: 800; letter-spacing: -0.02em; }
    .cli-subtitle { color: #547262; font-size: 13px; }
    .cli-hero-readout { text-align: right; }
    .cli-hero-readout strong { display: block; color: var(--cli-green-dim); font: 700 20px/1 monospace; }
    .cli-hero-readout small { display: block; margin-top: 5px; }

    .cli-control-panel, .cli-terminal-panel {
        overflow: hidden;
        border: 1px solid var(--cli-line) !important;
        border-radius: 10px !important;
        background: var(--cli-panel) !important;
        box-shadow: 0 18px 45px rgba(0, 20, 10, 0.16) !important;
    }

    .cli-control-panel .card-header, .cli-terminal-panel .card-header {
        background: var(--cli-panel-2) !important;
        border-bottom: 1px solid var(--cli-line) !important;
        color: var(--cli-ink) !important;
    }

    .cli-control-panel .card-body { padding: 20px !important; }
    .panel-index { margin-right: 10px; color: var(--cli-green); }
    .form-label { color: #83b593; }
    .form-label span { color: var(--cli-green); margin-right: 6px; }
    .olt-cli-page select, .olt-cli-page #commandInput {
        border: 1px solid var(--cli-line) !important;
        border-radius: 5px !important;
        background: #07110d !important;
        color: var(--cli-ink) !important;
        box-shadow: none !important;
    }
    .olt-cli-page select:focus, .olt-cli-page #commandInput:focus {
        border-color: var(--cli-green) !important;
        box-shadow: 0 0 0 2px rgba(113, 255, 154, 0.12) !important;
    }
    .cli-control-panel .alert { border: 1px solid var(--cli-line); background: rgba(113, 255, 154, 0.06); color: #a8dcb5; }
    .cli-connect-btn { border: 0; border-radius: 5px; padding: 11px; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; }
    .cli-connect-btn.btn-success { background: var(--cli-green); color: #06120a; }
    .session-info { padding: 12px; border: 1px dashed var(--cli-line); background: rgba(0, 0, 0, 0.14); }
    .session-info strong { color: #70a982; font-size: 10px; letter-spacing: 0.1em; }
    .session-info code, .session-info span { color: var(--cli-green); }
    .command-history-panel .form-label { display: block; }
    #historyList { border-color: var(--cli-line) !important; background: #07110d !important; }
    #historyList .history-item { background: #0d1d15; color: #a8dcb5; border-left-color: var(--cli-green-dim); }
    #historyList .history-item:hover { background: #153222; }
    #clearHistoryBtn { border-color: var(--cli-line); color: #80b791; }

    .terminal-toolbar { display: flex; align-items: center; gap: 14px; min-height: 55px; }
    .terminal-title { flex: 1; font-size: 12px; font-weight: 700; letter-spacing: 0.1em; }
    .terminal-title i { margin-right: 8px; color: var(--cli-green); }
    .terminal-lights { display: flex; gap: 5px; }
    .terminal-lights span { width: 7px; height: 7px; border-radius: 50%; background: #345543; }
    .terminal-lights span:first-child { background: var(--cli-green); box-shadow: 0 0 8px var(--cli-green); }
    .terminal-frame { min-height: 600px; display: flex; flex-direction: column; background: var(--cli-bg) !important; }
    .terminal-output-area { position: relative; flex: 1; min-height: 400px; background-color: var(--cli-bg) !important; background-image: linear-gradient(rgba(113,255,154,.025) 1px, transparent 1px), linear-gradient(90deg, rgba(113,255,154,.025) 1px, transparent 1px); background-size: 28px 28px; }
    .terminal-output-area::before { content: '01001011 01001100 01001001'; position: absolute; right: 20px; top: 18px; color: rgba(113,255,154,.08); font: 10px monospace; letter-spacing: .18em; pointer-events: none; }
    .terminal-welcome { color: var(--cli-green); text-shadow: 0 0 10px rgba(113,255,154,.55); }
    .terminal-muted { color: #547262; }
    .terminal-input-dock { border-top: 1px solid var(--cli-line); padding: 16px 18px 18px; background: #09140f; }
    .input-label { margin-bottom: 8px; }
    .prompt-caret { margin-right: 6px; color: var(--cli-green); font-size: 18px; }
    .input-hint { float: right; color: #547262; font-size: 9px; }
    .terminal-input-dock .input-group { border: 1px solid var(--cli-line); border-radius: 6px; padding: 4px; background: #06100b; }
    .terminal-input-dock #commandInput { border: 0 !important; }
    .terminal-input-dock #sendBtn { border: 0; border-radius: 4px; min-width: 112px; background: var(--cli-green); color: #06120a; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }

    #terminalOutput {
        font-size: 13px !important;
        line-height: 1.6 !important;
    }

    .terminal-error {
        color: #ff4444;
    }

    .terminal-success {
        color: #44ff44;
    }

    .terminal-command {
        color: #ffff00;
        font-weight: bold;
    }

    .terminal-prompt {
        color: #4488ff;
    }

    .terminal-echo {
        color: #88ff88;
    }

    .terminal-output {
        position: relative;
        color: #b7ffbd;
        text-shadow: 0 0 6px rgba(68, 255, 68, 0.45);
    }

    .terminal-output-area .terminal-output::after { left: 0; right: auto; width: 100%; }

    .terminal-output::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        height: 1px;
        background: rgba(110, 255, 130, 0.3);
        box-shadow: 0 0 8px rgba(110, 255, 130, 0.5);
        animation: terminal-scan 1.8s linear infinite;
        pointer-events: none;
    }

    @keyframes terminal-scan {
        from { top: 0; }
        to { top: 100%; }
    }

    .card {
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .card-header {
        border-radius: 8px 8px 0 0;
        border: none;
    }

    #historyList {
        background: #f8f9fa;
    }

    #historyList .history-item {
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 4px;
        margin-bottom: 4px;
        background: white;
        border-left: 3px solid #0d6efd;
        transition: all 0.2s;
    }

    #historyList .history-item:hover {
        background: #e7f3ff;
        padding-left: 12px;
    }

    .alert-info {
        background: rgba(13, 110, 253, 0.1);
        border: 1px solid rgba(13, 110, 253, 0.3);
        color: #0d6efd;
    }

    .alert-success {
        background: rgba(25, 135, 84, 0.1);
        border: 1px solid rgba(25, 135, 84, 0.3);
        color: #198754;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #dc3545;
    }
</style>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- JAVASCRIPT -->
<!-- ═══════════════════════════════════════════════════════════════════ -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const oltSelect = document.getElementById('oltSelect');
    const protocolSelect = document.getElementById('protocolSelect');
    const connectBtn = document.getElementById('connectBtn');
    const disconnectBtn = document.getElementById('disconnectBtn');
    const sendBtn = document.getElementById('sendBtn');
    const commandInput = document.getElementById('commandInput');
    const terminalOutput = document.getElementById('terminalOutput');
    const statusAlert = document.getElementById('statusAlert');
    const sessionInfo = document.getElementById('sessionInfo');
    const historyList = document.getElementById('historyList');
    const clearHistoryBtn = document.getElementById('clearHistoryBtn');
    const heroConnectionState = document.getElementById('heroConnectionState');

    let sessionId = null;
    let isConnected = false;
    let commandHistory = JSON.parse(localStorage.getItem('cliHistory') || '[]');

    // ═══════════════════════════════════════════════════════════════════
    // CONNECT
    // ═══════════════════════════════════════════════════════════════════

    connectBtn.addEventListener('click', async () => {
        const oltId = oltSelect.value;
        if (!oltId) {
            alert('Please select an OLT');
            return;
        }

        connectBtn.disabled = true;
        connectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connecting...';

        try {
            const response = await fetch(CLI_ROUTES.connect, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Socket-ID': window.Echo?.socketId?.() || '',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    olt_id: oltId,
                    protocol: protocolSelect.value,
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                sessionId = data.session_id;
                isConnected = true;
                heroConnectionState.textContent = 'ONLINE';
                heroConnectionState.style.color = '#71ff9a';

                // Update UI
                connectBtn.style.display = 'none';
                disconnectBtn.style.display = 'block';
                commandInput.disabled = false;
                sendBtn.disabled = false;
                oltSelect.disabled = true;
                protocolSelect.disabled = true;

                statusAlert.className = 'alert alert-success';
                statusAlert.innerHTML = '<i class="fas fa-circle text-success"></i> Connected';
                sessionInfo.style.display = 'block';
                document.getElementById('sessionId').textContent = sessionId.substring(0, 20) + '...';
                document.getElementById('connectedOlt').textContent = data.olt_name;

                terminal('info', `Connected to ${data.olt_name} via ${data.protocol.toUpperCase()}`);

                // Listen for WebSocket updates
                listenWebSocket();
            } else {
                alert('Connection failed: ' + data.message);
            }
        } catch (err) {
            alert('Error: ' + err.message);
        } finally {
            connectBtn.disabled = false;
            connectBtn.innerHTML = '<i class="fas fa-link"></i> Connect';
        }
    });

    // ═══════════════════════════════════════════════════════════════════
    // DISCONNECT
    // ═══════════════════════════════════════════════════════════════════

    disconnectBtn.addEventListener('click', async () => {
        if (!sessionId) return;

        disconnectBtn.disabled = true;

        try {
            await fetch(CLI_ROUTES.disconnect, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Socket-ID': getEchoSocketId(),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ session_id: sessionId })
            });

            // Reset UI
            isConnected = false;
            sessionId = null;
            heroConnectionState.textContent = 'STANDBY';
            heroConnectionState.style.color = '';
            disconnectBtn.style.display = 'none';
            connectBtn.style.display = 'block';
            commandInput.disabled = true;
            sendBtn.disabled = true;
            oltSelect.disabled = false;
            protocolSelect.disabled = false;
            oltSelect.value = '';

            statusAlert.className = 'alert alert-secondary';
            statusAlert.innerHTML = '<i class="fas fa-circle text-warning"></i> Disconnected';
            sessionInfo.style.display = 'none';

            terminal('info', 'Disconnected from OLT');

        } catch (err) {
            alert('Error disconnecting: ' + err.message);
        } finally {
            disconnectBtn.disabled = false;
        }
    });

    // ═══════════════════════════════════════════════════════════════════
    // SEND COMMAND
    // ═══════════════════════════════════════════════════════════════════

    sendBtn.addEventListener('click', sendCommand);
    commandInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendCommand();
        }
    });

    async function sendCommand() {
        const command = commandInput.value.trim();
        if (!command) return;

        if (!sessionId || !isConnected) {
            alert('Not connected to OLT');
            return;
        }

        // Add to history
        if (!commandHistory.includes(command)) {
            commandHistory.unshift(command);
            commandHistory = commandHistory.slice(0, 50); // Keep last 50
            localStorage.setItem('cliHistory', JSON.stringify(commandHistory));
            updateHistoryUI();
        }

        // Show command in terminal
        terminal('command', command);
        commandInput.value = '';

        // Send to server
        sendBtn.disabled = true;
        commandInput.disabled = true;

        try {
            const response = await fetch(CLI_ROUTES.command, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    command: command
                })
            });

            const data = await response.json();

            if (data.status === 'success' && data.output) {
                handleOutput('output', data.output);
            } else if (data.status !== 'success') {
                terminal('error', 'Error: ' + data.message);
            }
        } catch (err) {
            terminal('error', 'Error: ' + err.message);
        } finally {
            sendBtn.disabled = false;
            commandInput.disabled = false;
            commandInput.focus();
        }
    }

    function getEchoSocketId() {
        try {
            return typeof window.Echo?.socketId === 'function'
                ? window.Echo.socketId() || ''
                : window.Echo?.connector?.pusher?.connection?.socket_id || '';
        } catch (error) {
            return '';
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    // WebSocket LISTENER
    // ═══════════════════════════════════════════════════════════════════

    function listenWebSocket() {
        // Using Laravel Echo if available
        if (typeof window.Echo !== 'undefined' && sessionId) {
            window.Echo.private(`olt-cli.${sessionId}`)
                .listen('.cli.output', (e) => {
                    handleOutput(e.type, e.data);
                });
        }
    }

    function handleOutput(type, data) {
        if (type === 'connected') {
            terminal('success', data);
        } else if (type === 'disconnected') {
            terminal('error', data);
        } else if (type === 'error') {
            terminal('error', data);
        } else if (type === 'echo') {
            terminal('echo', data);
        } else if (type === 'output') {
            terminal('output', data);
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    // TERMINAL OUTPUT
    // ═══════════════════════════════════════════════════════════════════

    function terminal(type, message) {
        const line = document.createElement('div');

        if (type === 'command') {
            line.className = 'terminal-command';
            line.textContent = '$ ' + message;
        } else if (type === 'error') {
            line.className = 'terminal-error';
            line.textContent = '❌ ' + message;
        } else if (type === 'success') {
            line.className = 'terminal-success';
            line.textContent = '✅ ' + message;
        } else if (type === 'info') {
            line.className = 'terminal-prompt';
            line.textContent = 'ℹ️ ' + message;
        } else if (type === 'echo') {
            line.className = 'terminal-echo';
            line.textContent = message;
        } else if (type === 'output') {
            line.className = 'terminal-output';
            terminalOutput.appendChild(line);
            revealMatrixText(line, String(message ?? ''));
            terminalOutput.scrollTop = terminalOutput.scrollHeight;
            return;
        } else {
            line.style.color = '#0f0';
            line.textContent = message;
        }

        terminalOutput.appendChild(line);
        terminalOutput.scrollTop = terminalOutput.scrollHeight;
    }

    function revealMatrixText(element, message) {
        const matrixChars = '01アイウエオカキクケコサシスセソ<>[]{}#$%';
        const revealLength = Math.min(message.length, 12000);
        let position = 0;

        const reveal = () => {
            const visible = message.slice(0, position);
            const noise = Array.from({ length: Math.min(18, revealLength - position) }, () =>
                matrixChars[Math.floor(Math.random() * matrixChars.length)]
            ).join('');

            element.textContent = visible + noise;
            terminalOutput.scrollTop = terminalOutput.scrollHeight;

            if (position < revealLength) {
                position += 4;
                window.setTimeout(reveal, 12);
                return;
            }

            element.textContent = message;
        };

        reveal();
    }

    // ═══════════════════════════════════════════════════════════════════
    // HISTORY
    // ═══════════════════════════════════════════════════════════════════

    function updateHistoryUI() {
        if (commandHistory.length === 0) {
            historyList.innerHTML = '<small class="text-muted">No history</small>';
            return;
        }

        historyList.innerHTML = commandHistory.map((cmd, idx) => `
            <div class="history-item" onclick="
                document.getElementById('commandInput').value = '${cmd.replace(/'/g, "\\'")}';
                document.getElementById('commandInput').focus();
            ">
                <small>${cmd.substring(0, 30)}${cmd.length > 30 ? '...' : ''}</small>
            </div>
        `).join('');
    }

    clearHistoryBtn.addEventListener('click', () => {
        if (confirm('Clear command history?')) {
            commandHistory = [];
            localStorage.removeItem('cliHistory');
            updateHistoryUI();
        }
    });

    // Load history
    updateHistoryUI();
});
</script>

<!-- Laravel Echo for WebSocket (Reverb) -->
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@2.5.0/dist/echo.iife.js"></script>
<script>
    window.Pusher = Pusher;
    window.Echo = new Echo.default({
        broadcaster: 'reverb',
        key: '{{ config("broadcasting.connections.reverb.key") }}',
        wsHost: '{{ env("VITE_REVERB_HOST", config("broadcasting.connections.reverb.options.host")) }}',
        wsPort: '{{ env("VITE_REVERB_PORT", config("broadcasting.connections.reverb.options.port")) }}',
        wssPort: '{{ env("VITE_REVERB_PORT", config("broadcasting.connections.reverb.options.port")) }}',
        scheme: '{{ env("VITE_REVERB_SCHEME", config("broadcasting.connections.reverb.options.scheme")) }}',
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });
</script>
<script>
    const CLI_ROUTES = {
        connect: "{{ route('olt-cli.connect') }}",
        command: "{{ route('olt-cli.command') }}",
        disconnect: "{{ route('olt-cli.disconnect') }}",
    };
</script>
</x-app-layout>