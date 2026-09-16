<x-app-layout>
<div class="container-fluid mt-4 pb-5" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="row mb-4 pt-3">
        <div class="col-12">
            <h2 class="mb-0 font-weight-bold" style="color: #2c3e50;">
                <i class="fas fa-server text-primary mr-2"></i> Server Diagnostics
            </h2>
            <p class="text-muted">Real-time health monitoring and administration</p>
        </div>
    </div>

    <!-- Compute accurate Active Users from the array you provided -->
    @php
        $activeUsersCount = collect($sessionDetails['sessions'] ?? [])->where('user_id', '!=', 'Guest')->count();
        $totalSessions = count($sessionDetails['sessions'] ?? []);
    @endphp

   <!-- Modern Quick Stats Cards -->
    <div class="row mb-4">
        <!-- Active Users Card -->
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card card-stats bg-gradient-primary text-white h-100 border-0 shadow-sm rounded-lg">
                <div class="card-body position-relative overflow-hidden">
                    <i class="fas fa-users icon-background"></i>
                    <h6 class="text-uppercase mb-1 text-primary" style="opacity: 0.9; font-size: 0.8rem;">Active Users</h6>
                    <h2 class="font-weight-bold mb-0 text-warning">{{ $activeUsersCount }}</h2>
                    <small class="mt-2 d-block text-primary" style="opacity: 0.9;">
                        Total Sessions: <span class="badge badge-light text-primary ml-1 shadow-sm">{{ $totalSessions }}</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- PHP Version Card -->
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card card-stats bg-gradient-info text-white h-100 border-0 shadow-sm rounded-lg">
                <div class="card-body position-relative overflow-hidden">
                    <i class="fab fa-php icon-background"></i>
                    <h6 class="text-uppercase mb-1 text-info" style="opacity: 0.9; font-size: 0.8rem;">PHP Version</h6>
                    <h3 class="font-weight-bold mb-0 mt-2">
                        <span class="badge badge-light text-info shadow-sm px-3 py-2">{{ $phpVersion }}</span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Reverb WebSocket Card -->
        <div class="col-md-2 col-sm-6 mb-3">
            <div class="card card-stats {{ $reverbStatus == 'Running' ? 'bg-gradient-success' : 'bg-gradient-danger' }} text-white h-100 border-0 shadow-sm rounded-lg">
                <div class="card-body position-relative overflow-hidden">
                    <i class="fas fa-broadcast-tower icon-background"></i>
                    <h6 class="text-uppercase mb-1" style="opacity: 0.9; font-size: 0.8rem;">Reverb WebSocket</h6>
                    <h3 class="font-weight-bold mb-0 mt-2">
                        <span class="badge badge-light {{ $reverbStatus == 'Running' ? 'text-success' : 'text-danger' }} shadow-sm px-3 py-2">
                            <i class="fas fa-circle mr-1" style="font-size: 0.5rem; vertical-align: middle;"></i>{{ $reverbStatus }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- /var Storage Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-stats {{ $varStorage['status'] == 'critical' ? 'bg-gradient-danger' : ($varStorage['status'] == 'warning' ? 'bg-gradient-warning' : 'bg-gradient-dark') }} text-white h-100 border-0 shadow-sm rounded-lg">
                <div class="card-body position-relative overflow-hidden">
                    <i class="fas fa-hdd icon-background"></i>
                    <h6 class="text-uppercase mb-1 text-warning" style="opacity: 0.9; font-size: 0.8rem;"> Storage</h6>
                    <h3 class="font-weight-bold mb-0 text-warning">{{ $varStorage['percent'] }}%</h3>
                    <div class="progress mt-2 mb-2" style="height: 5px; background-color: rgba(255,255,255,0.2);">
                        <div class="progress-bar {{ $varStorage['percent'] > 80 ? 'bg-danger' : 'bg-warning' }}" style="width: {{ $varStorage['percent'] }}%"></div>
                    </div>
                    <small class="d-block">
                        <span class="badge badge-light text-dark shadow-sm">{{ $varStorage['used_gb'] }} GB</span> 
                        <span style="opacity: 0.9;" class="mx-1">used of</span> 
                        <span class="badge badge-light text-dark shadow-sm">{{ $varStorage['total_gb'] }} GB</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- MySQL Database Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-stats bg-white text-dark h-100 border-0 shadow-sm rounded-lg">
                <div class="card-body position-relative overflow-hidden">
                    <i class="fas fa-database icon-background text-light"></i>
                    <h6 class="text-uppercase mb-1 text-muted" style="font-size: 0.8rem;">MySQL Database</h6>
                    <h3 class="font-weight-bold mb-0 text-primary">{{ $dbInfo['size_mb'] }} <small class="text-muted" style="font-size: 1rem;">MB</small></h3>
                    <small class="d-block mt-2" style="font-size: 0.85rem;">
                        <span class="badge badge-danger shadow-sm px-2 py-1">{{ $dbInfo['active_queries'] }}</span> <span class="text-muted">active</span> 
                        <span class="mx-1 text-muted">|</span> 
                        <span class="badge badge-info shadow-sm px-2 py-1">{{ $dbInfo['max_connections'] }}</span> <span class="text-muted">max</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <!-- Sleek Tabs Navigation -->
    <div class="nav-wrapper position-relative mb-4">
        <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="healthTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active shadow-sm" id="overview-tab" data-toggle="tab" href="#overview" role="tab"><i class="fas fa-chart-line mr-2"></i>Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 shadow-sm" id="sessions-tab" data-toggle="tab" href="#sessions" role="tab"><i class="fas fa-user-shield mr-2"></i>Sessions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 shadow-sm" id="processes-tab" data-toggle="tab" href="#processes" role="tab"><i class="fas fa-microchip mr-2"></i>Processes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 shadow-sm" id="system-tab" data-toggle="tab" href="#system" role="tab"><i class="fas fa-info-circle mr-2"></i>System</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 shadow-sm" id="database-tab" data-toggle="tab" href="#database" role="tab"><i class="fas fa-database mr-2"></i>Database</a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 shadow-sm" id="network-tab" data-toggle="tab" href="#network" role="tab"><i class="fas fa-network-wired mr-2"></i>Network</a>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="healthTabsContent">
        
        <!-- OVERVIEW TAB -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-lg-8 col-md-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <h5 class="font-weight-bold text-dark mb-0">Live CPU & RAM Usage</h5>
                            <small class="text-muted">Updates every 3 seconds</small>
                        </div>
                        <div class="card-body">
                            <div style="height: 350px;">
                                <canvas id="healthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="row h-100">
                        <div class="col-6 mb-3">
                            <div class="card border-0 shadow-sm rounded-lg h-100 text-center py-4 bg-white">
                                <h6 class="text-muted text-uppercase mb-3" style="font-size:0.8rem; letter-spacing: 1px;">Avg CPU</h6>
                                <h2 class="font-weight-bold mb-0 text-danger" id="avg-cpu">0.0%</h2>
                                <small class="text-muted">Rolling Avg</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card border-0 shadow-sm rounded-lg h-100 text-center py-4 bg-white">
                                <h6 class="text-muted text-uppercase mb-3" style="font-size:0.8rem; letter-spacing: 1px;">Avg RAM</h6>
                                <h2 class="font-weight-bold mb-0 text-primary" id="avg-ram">0.0%</h2>
                                <small class="text-muted">Rolling Avg</small>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-lg h-100">
                                <div class="card-header bg-white border-0 pt-3 pb-0">
                                    <h6 class="font-weight-bold text-dark mb-0">System Load Average</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">1 Minute</span>
                                        <span class="badge badge-pill badge-primary px-3 py-2">{{ $systemInfo['load_average'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">5 Minutes</span>
                                        <span class="badge badge-pill badge-info px-3 py-2">{{ $systemInfo['load_5min'] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted">15 Minutes</span>
                                        <span class="badge badge-pill badge-secondary px-3 py-2">{{ $systemInfo['load_15min'] }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Cores Available</span>
                                        <span class="font-weight-bold text-dark">{{ $systemInfo['cpu_cores'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SESSIONS TAB -->
        <div class="tab-pane fade" id="sessions" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="font-weight-bold mb-0">Active Sessions <span class="badge badge-primary ml-2">{{ count($sessionDetails['sessions']) }}</span></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="border-0 pl-4">Session ID</th>
                                    <th class="border-0">User</th>
                                    <th class="border-0">IP Address</th>
                                    <th class="border-0">Size</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessionDetails['sessions'] as $session)
                                <tr>
                                    <td class="pl-4">
                                        <code class="text-primary bg-light px-2 py-1 rounded">{{ $session['id'] }}</code>
                                    </td>
                                    <td>
    @if($session['user_id'] !== 'Guest')
        <span class="badge badge-success px-2 py-1 shadow-sm">{{ $session['user_name'] }}</span>
        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">User ID: {{ $session['user_id'] }}</small>
    @else
        <span class="badge badge-secondary px-2 py-1 shadow-sm">Guest</span>
    @endif
</td>
                                    <td><span class="text-muted">{{ $session['ip'] }}</span></td>
                                    <td><small class="text-muted">{{ $session['size'] }}</small></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('{{ $session['full_id'] }}')">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">No active sessions found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROCESSES TAB -->
        <div class="tab-pane fade" id="processes" role="tabpanel">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-3">
                            <h5 class="font-weight-bold mb-0 text-danger"><i class="fas fa-fire mr-2"></i>Top by CPU</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr><th class="pl-4">PID</th><th>CPU</th><th>Command</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($topProcesses['top_cpu'] as $process)
                                    <tr>
                                        <td class="pl-4"><code>{{ $process['pid'] }}</code></td>
                                        <td><span class="font-weight-bold text-danger">{{ $process['cpu'] }}%</span></td>
                                        <td><small class="text-muted" title="{{ $process['command'] }}">{{ substr($process['command'], 0, 35) }}...</small></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-3">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-3">
                            <h5 class="font-weight-bold mb-0 text-primary"><i class="fas fa-memory mr-2"></i>Top by RAM</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr><th class="pl-4">PID</th><th>RAM</th><th>Command</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($topProcesses['top_ram'] as $process)
                                    <tr>
                                        <td class="pl-4"><code>{{ $process['pid'] }}</code></td>
                                        <td><span class="font-weight-bold text-primary">{{ $process['mem'] }}%</span></td>
                                        <td><small class="text-muted" title="{{ $process['command'] }}">{{ substr($process['command'], 0, 35) }}...</small></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-3">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SYSTEM TAB -->
        <div class="tab-pane fade" id="system" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="font-weight-bold mb-0">System Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Hostname:</strong> <br><span class="text-muted">{{ $systemInfo['hostname'] }}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>OS:</strong> <br><span class="text-muted">{{ $systemInfo['os'] }}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Kernel:</strong> <br><span class="text-muted">{{ $systemInfo['kernel'] }}</span>
                        </div>
                        <div class="col-md-8 mb-3">
                            <strong>CPU Model:</strong> <br><span class="text-muted">{{ $systemInfo['cpu_model'] }}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Uptime:</strong> <br><span class="text-muted">{{ $systemInfo['uptime'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATABASE TAB -->
        <div class="tab-pane fade" id="database" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="font-weight-bold mb-0">Database Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 text-center py-3 rounded-lg h-100">
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Database Name</h6>
                                <code class="text-primary font-weight-bold" style="font-size: 1rem;">{{ $dbInfo['name'] }}</code>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 text-center py-3 rounded-lg h-100">
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Database Size</h6>
                                <h4 class="font-weight-bold text-dark mb-0">{{ $dbInfo['size_mb'] }} <small>MB</small></h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 text-center py-3 rounded-lg h-100">
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Active Connections</h6>
                                <h4 class="font-weight-bold text-dark mb-0">{{ $dbInfo['total_connections'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 text-center py-3 rounded-lg h-100">
                                <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem;">Active Queries</h6>
                                <h4 class="font-weight-bold text-dark mb-0">{{ $dbInfo['active_queries'] }}</h4>
                            </div>
                        </div>
                    </div>
                    <table class="table table-borderless table-sm w-50">
                        <tr><td class="text-muted">Host:</td><td><code class="bg-light px-2 py-1 rounded">{{ $dbInfo['host'] }}</code></td></tr>
                        <tr><td class="text-muted">Port:</td><td><code class="bg-light px-2 py-1 rounded">{{ $dbInfo['port'] }}</code></td></tr>
                        <tr><td class="text-muted">Max Connections:</td><td><span class="badge badge-info px-2 py-1">{{ $dbInfo['max_connections'] }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- NETWORK TAB -->
        <div class="tab-pane fade" id="network" role="tabpanel">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-3">
                            <h5 class="font-weight-bold mb-0">Network Configuration</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted w-25">IP Address:</td>
                                    <td><code class="bg-light px-2 py-1 rounded">{{ $networkInfo['ip_address'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Listening Ports:</td>
                                    <td><span class="badge badge-info px-2 py-1">{{ $networkInfo['listening_ports'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-middle">DNS Servers:</td>
                                    <td><pre class="bg-light p-2 rounded text-muted mb-0" style="font-size: 0.8rem;"><code>{{ $networkInfo['dns_servers'] }}</code></pre></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-header bg-white border-0 pt-4 pb-3">
                            <h5 class="font-weight-bold mb-0">Disk I/O Statistics</h5>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded text-muted" style="font-size: 0.75rem; max-height: 300px; overflow-y: auto;"><code>{{ $diskStats['iostat'] }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scripts & Styles -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => { alert('Session ID copied to clipboard!'); });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('healthChart').getContext('2d');
        
        // Variables for calculating rolling averages
        let cpuHistory = [];
        let ramHistory = [];
        
        // Create sleek gradient fills for the chart
        let gradientCpu = ctx.createLinearGradient(0, 0, 0, 350);
        gradientCpu.addColorStop(0, 'rgba(255, 99, 132, 0.5)');
        gradientCpu.addColorStop(1, 'rgba(255, 99, 132, 0.0)');
        
        let gradientRam = ctx.createLinearGradient(0, 0, 0, 350);
        gradientRam.addColorStop(0, 'rgba(54, 162, 235, 0.5)');
        gradientRam.addColorStop(1, 'rgba(54, 162, 235, 0.0)');

        const healthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'CPU Usage (%)',
                        borderColor: '#ff6384',
                        backgroundColor: gradientCpu,
                        data: [],
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'RAM Usage (%)',
                        borderColor: '#36a2eb',
                        backgroundColor: gradientRam,
                        data: [],
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { 
                        min: 0, max: 100, 
                        grid: { borderDash: [5, 5], color: '#e0e0e0', drawBorder: false }
                    },
                    x: { grid: { display: false, drawBorder: false } }
                },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } }
                }
            }
        });

        // Fetch live data every 3 seconds and update averages
        setInterval(() => {
            fetch('{{ route("serverHealth.metrics") }}')
                .then(response => response.json())
                .then(data => {
                    if (healthChart.data.labels.length > 20) {
                        healthChart.data.labels.shift();
                        healthChart.data.datasets[0].data.shift();
                        healthChart.data.datasets[1].data.shift();
                    }

                    healthChart.data.labels.push(data.time);
                    healthChart.data.datasets[0].data.push(data.cpu);
                    healthChart.data.datasets[1].data.push(data.ram);
                    healthChart.update();

                    // Update Rolling Averages
                    cpuHistory.push(parseFloat(data.cpu));
                    ramHistory.push(parseFloat(data.ram));
                    
                    if(cpuHistory.length > 20) { cpuHistory.shift(); ramHistory.shift(); }

                    let avgC = (cpuHistory.reduce((a, b) => a + b, 0) / cpuHistory.length).toFixed(1);
                    let avgR = (ramHistory.reduce((a, b) => a + b, 0) / ramHistory.length).toFixed(1);

                    document.getElementById('avg-cpu').innerText = avgC + '%';
                    document.getElementById('avg-ram').innerText = avgR + '%';
                })
                .catch(error => console.error('Error fetching metrics:', error));
        }, 3000);
    });
</script>

<style>
    /* Custom CSS for SaaS-style Dashboard */
    .bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: #2c3e50 !important; }
    .bg-gradient-warning { background: linear-gradient(135deg, #fccb90 0%, #d57eeb 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .bg-gradient-dark { background: linear-gradient(135deg, #434343 0%, #000000 100%); }
    
    .card-stats { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-stats:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    
    .icon-background {
        position: absolute;
        right: -10px;
        bottom: -15px;
        font-size: 5rem;
        opacity: 0.15;
        transform: rotate(-10deg);
    }

    /* Customizing the Tabs */
    .nav-pills .nav-link {
        color: #6c757d;
        background-color: #fff;
        border-radius: 8px;
        margin-right: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-pills .nav-link:hover { color: #007bff; background-color: #f8f9fa; }
    .nav-pills .nav-link.active {
        color: #fff;
        background-color: #007bff;
        box-shadow: 0 4px 10px rgba(0,123,255,0.3) !important;
    }

    /* Table Adjustments */
    .table th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    code { padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; }
</style>
</x-app-layout>