<x-app-layout>

<div class="container-fluid mt-4">

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- HEADER -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-terminal"></i> OLT CLI Terminal
            </h2>
            <small class="text-muted">Execute commands on OLT devices via SSH/Telnet</small>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- MAIN CONTAINER -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->

    <div class="row">
        <!-- LEFT: OLT Selection & Connection -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-plug"></i> Connection
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Status Indicator -->
                    <div class="alert alert-secondary mb-3" id="statusAlert">
                        <i class="fas fa-circle text-warning"></i> Disconnected
                    </div>

                    <!-- OLT Selection -->
                    <div class="mb-3">
                        <label class="form-label">Select OLT</label>
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
                        <label class="form-label">Protocol</label>
                        <select class="form-control" id="protocolSelect">
                            <option value="ssh">SSH (Port 22)</option>
                            <option value="telnet">Telnet (Port 23)</option>
                        </select>
                    </div>

                    <!-- Connect Button -->
                    <button class="btn btn-success w-100 mb-2" id="connectBtn">
                        <i class="fas fa-link"></i> Connect
                    </button>

                    <!-- Disconnect Button (Hidden) -->
                    <button class="btn btn-danger w-100 mb-3" id="disconnectBtn" style="display: none;">
                        <i class="fas fa-unlink"></i> Disconnect
                    </button>

                    <hr>

                    <!-- Session Info -->
                    <div id="sessionInfo" style="display: none;">
                        <small class="text-muted">
                            <strong>Session ID:</strong><br>
                            <code id="sessionId" style="font-size: 10px;"></code>
                        </small>
                        <hr>
                        <small class="text-muted">
                            <strong>Connected OLT:</strong><br>
                            <span id="connectedOlt"></span>
                        </small>
                    </div>

                    <!-- History -->
                    <div class="mt-4">
                        <label class="form-label">Command History</label>
                        <div id="historyList" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 8px;">
                            <small class="text-muted">No history</small>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary mt-2 w-100" id="clearHistoryBtn">Clear History</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Terminal -->
        <div class="col-md-9 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-monitor"></i> Terminal
                    </h6>
                </div>
                <div class="card-body p-0" style="background: #1e1e1e; min-height: 500px; display: flex; flex-direction: column;">

                    <!-- Terminal Output -->
                    <div id="terminalOutput" style="
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
                        <span style="color: #888;">Welcome to OLT CLI Terminal</span><br>
                        <span style="color: #888;">Select an OLT and click Connect to start</span><br>
                    </div>

                    <!-- Input Area -->
                    <div style="border-top: 1px solid #444; padding: 15px; background: #0a0a0a;">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   id="commandInput"
                                   placeholder="Enter command (e.g., show running-config)..."
                                   disabled
                                   style="background: #1e1e1e; color: #0f0; border: 1px solid #444; font-family: monospace;">
                            <button class="btn btn-success" id="sendBtn" disabled>
                                <i class="fas fa-paper-plane"></i> Send
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
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ session_id: sessionId })
            });

            // Reset UI
            isConnected = false;
            sessionId = null;
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

            if (data.status !== 'success') {
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
        } else {
            line.style.color = '#0f0';
            line.textContent = message;
        }

        terminalOutput.appendChild(line);
        terminalOutput.scrollTop = terminalOutput.scrollHeight;
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
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.14.0/dist/echo.iife.js"></script>
<script>
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ config("broadcasting.connections.reverb.key") }}',
        wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
        wsPort: '{{ config("broadcasting.connections.reverb.options.port") }}',
        wssPort: '{{ config("broadcasting.connections.reverb.options.port") }}',
        scheme: '{{ config("broadcasting.connections.reverb.options.scheme") }}',
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