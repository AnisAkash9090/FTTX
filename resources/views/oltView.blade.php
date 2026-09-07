<x-app-layout>
    <div class="">

        <!-- Enhanced Search Form Filter Section -->
        <div class="search-form-wrapper">
            <div class="form-card shadow-lg">
           

                <form action="{{ route('olt.search.submit') }}" method="POST" class="form-body">
                    @csrf

                    <div class="row g-3 align-items-end">

                        <!-- OLT Selection with Search -->
                        <div class="col-md-5">
                            <label class="form-label fw-bold">
                                <i class="fa fa-server"></i> Select OLT Device
                            </label>
                            <select id="oltIDS" class="form-control form-select-enhanced" name="oltID" required>
                                <option value="">Choose OLT...</option>
                                @foreach($olts as $resname)
                                    <option value="{{ $resname->id }}" {{ isset($OLT_ID) && $OLT_ID == $resname->id ? 'selected' : '' }}>
                                        {{ $resname->olt_name }} -- {{ $resname->olt_ip }} ({{ $resname->id }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fa fa-lightbulb-o"></i> Type to search OLT by name
                            </small>
                        </div>

                        <!-- PON Selection with Search -->
                        <div class="col-md-5">
                            <label for="showpon" class="form-label fw-bold">
                                <i class="fa fa-sitemap"></i> Select PON [<span id="ponCount">{{ $showponsend ?? '0' }}</span>]
                            </label>
                            <select class="form-control form-select-enhanced" required name="showponsend" id="showpon">
                                <option value="">Choose PON...</option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fa fa-lightbulb-o"></i> Type to search PON ports
                            </small>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-search btn-lg w-100" name="subOLT">
                                <i class="fa fa-search"></i> Search
                            </button>
                                <small class="text-muted d-block mt-1">
                                <i class="fa fa-lightbulb-o"></i> submit
                            </small>
                        </div>

                    </div>

                    <!-- Form State Indicator -->
                    <div class="form-state-indicator" id="formState">
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i> Select both OLT and PON to view results
                        </small>
                    </div>
                </form>
            </div>
        </div>

        <hr class="my-4">

        <!-- Results Table Section -->
        @if(isset($results))
            @php
                $ONU_REASON_MAP = [
                    "0"  => "Normal",
                    "1"  => "Dying Gasp (power off)",
                    "2"  => "Laser Always On",
                    "3"  => "Admin Down",
                    "4"  => "OMCC Down",
                    "5"  => "Unknown",
                    "6"  => "PON LOS (fiber cut)",
                    "7"  => "LCDG",
                    "8"  => "Wire Down",
                    "9"  => "OMCI Mismatch",
                    "10" => "Password Mismatch",
                    "11" => "Reboot",
                    "12" => "Ranging Failed"
                ];

                if (!function_exists('getRxRangeClass')) {
                    function getRxRangeClass($rxVal) {
                        if (!is_numeric($rxVal)) {
                            return 'rx-unknown'; 
                        }
                        $rxRaw = intval($rxVal);
                        if ($rxRaw <= -150 && $rxRaw >= -269) return 'rx-good';
                        if ($rxRaw <= -270 && $rxRaw >= -279) return 'rx-warn';
                        if (($rxRaw <= 0 && $rxRaw >= -149) || $rxRaw <= -280) return 'rx-bad';
                        return 'rx-default';
                    }
                }

                if (!function_exists('parseDayHourMinSec')) {
                    function parseDayHourMinSec($seconds) {
                        if (!is_numeric($seconds) || $seconds <= 0) {
                            return "N/F";
                        }
                        $secondsInt = intval($seconds);
                        $days = floor($secondsInt / 86400);
                        $hours = floor(($secondsInt % 86400) / 3600);
                        $minutes = floor(($secondsInt % 3600) / 60);
                        $secondsRemain = $secondsInt % 60;
                        return "{$days}d {$hours}h {$minutes}m {$secondsRemain}s";
                    }
                }
            @endphp

            @if(!$results->isEmpty())
                <!-- Stats Information Block -->
                <div class="stats-container shadow-lg">
                    <div class="stats-left">
                        @php $firstRow = $results->first(); @endphp
                        <div class="stat-item">
                            <i class="fa fa-link stat-icon"></i>
                            <div class="stat-content">
                                <span class="stat-label">OLT IP Address</span>
                                <span class="stat-value">{{ $resrouter['olt_ip'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fa fa-sitemap stat-icon"></i>
                            <div class="stat-content">
                                <span class="stat-label">Branch</span>
                                <span class="stat-value">{{ $firstRow->sys_from ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fa fa-hdd-o stat-icon"></i>
                            <div class="stat-content">
                                <span class="stat-label">Device</span>
                                <span class="stat-value">{{ $firstRow->sys_device ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="stat-item">
                            <i class="fa fa-refresh stat-icon"></i>
                            <div class="stat-content">
                                <span class="stat-label">Last Sync</span>
                                <span class="stat-value badge bg-info">{{ $firstRow->sync_time ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-right">
                        <style>
                            .dashboard-wrapper { display: flex; gap: 20px; background: linear-gradient(135deg, #e0e0e0 0%, #616161 100%); padding: 15px; border-radius: 12px; }
                            .chart-card { flex: 1; text-align: center; min-width: 180px; }
                            .canvas-holder { position: relative; height: 140px; width: 100%; margin-top: 10px;}
                        </style>

                        <div class="dashboard-wrapper shadow-lg">
                            <!-- Donut Chart Widget -->
                            <div class="chart-card">
                                <h6 class="text-center text-light font-monospace m-0" style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">
                                    <i class="fa fa-circle"></i> ONU Status
                                </h6>
                                <div class="canvas-holder">
                                    <canvas id="donutChart"></canvas>
                                </div>
                            </div>

                            <!-- Horizontal Bar Chart Widget -->
                            <div class="chart-card" style="flex: 1.5;">
                                <h6 class="text-center text-light font-monospace m-0" style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">
                                    <i class="fa fa-signal"></i> RX Signal Quality
                                </h6>
                                <div class="canvas-holder">
                                    <canvas id="berBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search Bar for Results -->
            @if(!$results->isEmpty())
            <div class="results-search-container mt-4">
                <div class="input-group search-results-group">
                    <span class="input-group-text search-icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" id="resultsSearch" class="form-control search-results-input" 
                           placeholder="🔍 Search results by Port, MAC, Status, Vendor, Model...">
                    <button class="btn btn-outline-danger" type="button" id="clearResultsSearch" style="display:none;">
                        <i class="fa fa-times"></i> Clear
                    </button>
                </div>
                <div id="searchResultsStats" class="search-results-stats"></div>
            </div>
            @endif

            <!-- Main Data Grid Presentation Details -->
            <div class="card mt-4 shadow-lg">
                <div class="card-header bg-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">
                            <i class="fa fa-table"></i> ONU Devices
                        </h5>
                        <span class="badge bg-info text-dark">
                            {{ $results->total() ?? 0 }} Total Records
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($results->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            <h5>No records found</h5>
                            <p>Try adjusting your OLT and PON selection</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle border" id="onuTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Port</th>
                                        <th>ONU MAC/SN</th>
                                        <th>Router MAC</th>
                                        <th class="text-end">TX [dBm]</th>
                                        <th class="text-end">RX [dBm]</th>
                                        <th></th>
                                        <th class="text-end">Distance [M]</th>
                                        <th class="text-end">Model</th>
                                        <th class="text-end">Vendor</th>
                                        <th class="text-end">Last Change</th>
                                        <th class="text-end">Last Reason</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="onuTableBody">
                                    @foreach($results as $row)
                                        @php
                                            $sysTx         = $row->sys_tx ?? 'N/A';
                                            $sysRx         = $row->sys_rx ?? 'N/A';
                                            $sysPort       = $row->sys_port ?? '';
                                            $sysMac        = $row->sys_mac ?? '';
                                            $routerMac     = $row->router_mac ?? '';
                                            $sysDistance   = $row->sys_distance ?? '';
                                            $sysModel      = $row->sys_model ?? '';
                                            $sysVendor     = $row->sys_vendor ?? '';
                                            $sysLastChange = $row->sys_lastChange ?? 0;
                                            $syncTime      = $row->sync_time ?? '';
                                            $reasonCode    = $row->reason ?? '';
                                            $sysSts        = $row->sys_sts ?? '';

                                            $tx = is_numeric($sysTx) ? number_format(floatval($sysTx) / 10, 2) : 'N/A';
                                            $rx = is_numeric($sysRx) ? number_format(floatval($sysRx) / 10, 2) : 'N/A';
                                            
                                            $rescl = getRxRangeClass($sysRx);

                                            $isUp = ($sysSts == '1');
                                            $statusText = $isUp ? 'Up' : 'Down';
                                            $statusClass = $isUp ? 'status-up text-success fw-bold' : 'status-down text-danger fw-bold';

                                            $lcDisplay = ($sysLastChange > 0) ? parseDayHourMinSec($sysLastChange) : 'N/F';
                                            $reasonText = $ONU_REASON_MAP[$reasonCode] ?? 'Unknown';
                                        @endphp
                                        <tr class="onu-row">
                                            <td><strong>{{ $sysPort }}</strong></td>
                                            <td><code class="bg-light p-1 rounded">{{ $sysMac }}</code></td>
                                            <td><code class="bg-light p-1 rounded text-end">{{ $routerMac }}</code></td>
                                            <td class="text-end font-monospace">{{ $tx }}</td>
                                            <td class="text-end font-monospace">{{ $rx }}</td>
                                            <td>
                                                <div class="rx-indicator {{ $rescl }}" title="RX Signal: {{ $rx }} dBm"></div>
                                            </td>
                                            <td class="text-end">{{ $sysDistance }}</td>
                                            <td class="text-end"><small>{{ $sysModel }}</small></td>
                                            <td class="text-end"><small>{{ $sysVendor }}</small></td>
                                            <td class="text-end" style="font-size:11px; line-height:1.6;">
                                                <div class="text-muted">
                                                    <span class="fw-semibold">Last Change:</span>
                                                    <br><span class="text-dark">{{ $lcDisplay }}</span>
                                                </div>
                                                <div class="text-muted mt-1">
                                                    <span class="fw-semibold">Last OLT:</span>
                                                    <br><span class="text-dark">{{ $syncTime }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end"><span class="badge bg-warning">{{ $reasonText }}</span></td>
                                            <td><span class="{{ $statusClass }}">{{ $statusText }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="12" class="p-3">
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                                <div class="fw-bold text-secondary">
                                                    <i class="fa fa-info-circle"></i> Showing {{ $results->firstItem() ?? 0 }} to {{ $results->lastItem() ?? 0 }} of {{ $results->total() }} Total Rows
                                                </div>
                                                <div class="laravel-pagination">
                                                    {{ $results->appends(request()->input())->links('pagination::bootstrap-5') }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <!-- Styles -->
    <style>
        /* ====== ANIMATIONS ====== */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* ====== FORM STYLES ====== */
        .search-form-wrapper {
            animation: slideInDown 0.5s ease;
            margin-bottom: 20px;
        }

        .form-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            animation: slideInDown 0.6s ease;
        }

        .form-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-bottom: 3px solid #34dba3;
        }

        .form-header h5 {
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .form-body {
            padding: 25px;
        }

        .form-label {
            color: #2c3e50;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .form-label:hover {
            color: #667eea;
        }

        .form-control, .form-select-enhanced {
            border: 2px solid #e0e0e0 !important;
            border-radius: 8px !important;
            padding: 12px 14px !important;
            font-size: 13px !important;
            transition: all 0.3s ease !important;
        }

        .form-control:focus, .form-select-enhanced:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
            transform: translateY(-2px);
        }

        .form-control:hover, .form-select-enhanced:hover {
            border-color: #667eea !important;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.15) !important;
        }

        .form-state-indicator {
            margin-top: 15px;
            padding: 12px 15px;
            background: rgba(52, 219, 163, 0.1);
            border-left: 4px solid #34dba3;
            border-radius: 6px;
            animation: slideInUp 0.4s ease;
        }

        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .btn-search:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-search:active {
            transform: translateY(-1px);
        }

        /* ====== STATS SECTION ====== */
        .stats-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            animation: slideInUp 0.5s ease;
        }

        .stats-left {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            flex: 1;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInUp 0.5s ease;
        }

        .stat-icon {
            font-size: 24px;
            color: #667eea;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .stat-item:hover .stat-icon {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-value {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 700;
        }

        .stats-right {
            flex: 1.2;
            min-width: 300px;
        }

        /* ====== SEARCH RESULTS STYLES ====== */
        .results-search-container {
            animation: slideInUp 0.5s ease;
        }

        .search-results-group {
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            overflow: hidden;
            border: none;
            transition: all 0.3s ease;
        }

        .search-results-group:focus-within {
            box-shadow: 0 8px 30px rgba(52, 219, 163, 0.3);
            transform: translateY(-2px);
        }

        .search-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-size: 16px;
            padding: 12px 16px;
        }

        .search-results-input {
            border: none;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
        }

        .search-results-input:focus {
            box-shadow: none;
            outline: none;
        }

        #clearResultsSearch {
            border: none;
            padding: 12px 16px;
            font-weight: 600;
        }

        #clearResultsSearch:hover {
            background-color: #f5576c !important;
            color: white !important;
        }

        .search-results-stats {
            margin-top: 8px;
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        .search-results-stats.active {
            color: #34dba3;
            background: rgba(52, 219, 163, 0.1);
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-block;
            animation: slideInUp 0.4s ease;
        }

        /* ====== TABLE STYLES ====== */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            animation: slideInUp 0.5s ease;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1) !important;
        }

        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            border-bottom: 3px solid #667eea;
            padding: 20px !important;
        }

        .card-header h5 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .table thead th {
            background: #2c3e50 !important;
            color: white !important;
            padding: 14px 8px !important;
            font-size: 12px !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #667eea !important;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e0e0e0;
        }

        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.08) !important;
            box-shadow: inset 4px 0 0 0 #667eea;
            transform: scale(1.005);
        }

        .table tbody tr.hidden {
            display: none;
        }

        .table tbody td {
            padding: 12px 8px !important;
            font-size: 12px !important;
            vertical-align: middle;
        }

        .table tfoot {
            background: #f8f9fa !important;
        }

        .table-responsive {
            border-radius: 0 0 12px 12px;
        }

        /* ====== STATUS INDICATORS ====== */
        .status-up {
            animation: pulse 2s ease-in-out infinite;
        }

        .status-down {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .rx-indicator {
            height: 12px;
            width: 12px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 8px currentColor;
            transition: all 0.3s ease;
        }

        .rx-indicator:hover {
            transform: scale(1.3);
        }

        .rx-good {
            background-color: #2ed573;
            color: #2ed573;
        }

        .rx-warn {
            background-color: #ffa502;
            color: #ffa502;
        }

        .rx-bad {
            background-color: #ff4757;
            color: #ff4757;
        }

        .rx-unknown {
            background-color: #999;
            color: #999;
        }

        /* ====== SELECT2 INTEGRATION ====== */
        .select2-container--default .select2-selection--single {
            border: 2px solid #e0e0e0 !important;
            border-radius: 8px !important;
            height: 44px !important;
            padding: 0 !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #667eea !important;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #2c3e50;
            line-height: 42px !important;
            font-size: 13px !important;
        }

        .select2-dropdown {
            border-color: #667eea !important;
            border-radius: 8px !important;
        }

        .select2-results__option--highlighted {
            background-color: #667eea !important;
        }

        .select2-results__option--selected {
            background-color: #34dba3 !important;
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 768px) {
            .stats-container {
                flex-direction: column;
                gap: 20px;
            }

            .stats-left {
                flex-direction: column;
            }

            .stats-right {
                width: 100%;
            }

            .dashboard-wrapper {
                flex-direction: column !important;
            }

            .chart-card {
                width: 100% !important;
            }

            .table {
                font-size: 11px !important;
            }

            .form-body {
                padding: 15px;
            }
        }

        /* ====== BADGES ====== */
        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    </style>

    <!-- AJAX Port Sync Logic -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <script>
    $(document).ready(function () {
        // Initialize Select2 for searchable dropdowns
        $('#oltIDS').select2({
            placeholder: 'Type to search OLT...',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
        });

        $('#showpon').select2({
            placeholder: 'Type to search PON...',
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: true
        });

        function loadPorts(selectedOltId, currentPon) {
            if (selectedOltId > 0) {
                $.ajax({
                    url: "{{ route('ajax.olt.ports') }}",
                    type: 'GET',
                    data: { 
                        oltid: selectedOltId,
                        selectedPON: currentPon
                    },
                    success: function (data) {
                        $('#showpon').html(data).select2({
                            placeholder: 'Type to search PON...',
                            allowClear: true,
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                        updateFormState();
                    },
                    error: function() {
                        $('#showpon').html('<option value="">Error fetching ports</option>');
                    }
                });
            } else {
                $('#showpon').html('<option value="">Select PON...</option>').select2({
                    placeholder: 'Type to search PON...',
                    allowClear: true,
                    width: '100%',
                    dropdownAutoWidth: true
                });
            }
        }

        $('#oltIDS').change(function () {
            loadPorts($(this).val(), "{{ $showponsend ?? '' }}");
        });

        $('#showpon').change(function() {
            var ponValue = $(this).val();
            var ponText = $(this).find('option:selected').text();
            $('#ponCount').text(ponValue || '0');
            updateFormState();
        });

        function updateFormState() {
            var oltVal = $('#oltIDS').val();
            var ponVal = $('#showpon').val();
            var stateIndicator = $('#formState');

            if (oltVal && ponVal) {
                stateIndicator.html('<small class="text-success"><i class="fa fa-check-circle"></i> Ready to search - Click "Search" button</small>');
            } else if (oltVal) {
                stateIndicator.html('<small class="text-warning"><i class="fa fa-exclamation-circle"></i> Select a PON to continue</small>');
            } else {
                stateIndicator.html('<small class="text-muted"><i class="fa fa-info-circle"></i> Select both OLT and PON to view results</small>');
            }
        }

        var existingOlt = $('#oltIDS').val();
        if (existingOlt > 0) {
            loadPorts(existingOlt, "{{ $showponsend ?? '' }}");
        }

        // Form state initialization
        updateFormState();
    });
    </script>

    <!-- Results Table Search Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('resultsSearch');
        const clearBtn = document.getElementById('clearResultsSearch');
        const statsDiv = document.getElementById('searchResultsStats');
        const tableRows = document.querySelectorAll('.onu-row');

        if (searchInput) {
            searchInput.addEventListener('input', performTableSearch);
            clearBtn.addEventListener('click', clearTableSearch);

            function performTableSearch() {
                const query = searchInput.value.trim().toLowerCase();
                let visibleCount = 0;

                if (query === '') {
                    clearBtn.style.display = 'none';
                    statsDiv.textContent = '';
                    statsDiv.classList.remove('active');
                } else {
                    clearBtn.style.display = 'inline-block';
                }

                tableRows.forEach(row => {
                    const text = row.textContent.toLowerCase();

                    if (text.includes(query)) {
                        row.classList.remove('hidden');
                        row.style.display = '';
                        row.style.animation = 'slideInUp 0.3s ease';
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                        row.style.display = 'none';
                    }
                });

                if (query !== '') {
                    statsDiv.textContent = `📊 Found ${visibleCount} of ${tableRows.length} records`;
                    statsDiv.classList.add('active');
                }
            }

            function clearTableSearch() {
                searchInput.value = '';
                clearBtn.style.display = 'none';
                statsDiv.textContent = '';
                statsDiv.classList.remove('active');
                tableRows.forEach(row => {
                    row.classList.remove('hidden');
                    row.style.display = '';
                });
                searchInput.focus();
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && searchInput.value !== '') {
                    clearTableSearch();
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            // Search tips
            searchInput.addEventListener('focus', function() {
                if (!this.value) {
                    statsDiv.textContent = '💡 Tip: Ctrl+F to search, ESC to clear';
                    statsDiv.classList.add('active');
                }
            });

            searchInput.addEventListener('blur', function() {
                if (!this.value) {
                    statsDiv.textContent = '';
                    statsDiv.classList.remove('active');
                }
            });
        }
    });
    </script>

    <!-- ChartJS Render Scripts Configuration Section -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if(isset($results) && !$results->isEmpty())
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // 1. --- DONUT CHART (UP / DOWN STATUS COUNTERS) ---
        const donutCtx = document.getElementById('donutChart').getContext('2d');
        const upData = {{ $uponu ?? 0 }};
        const downData = {{ $downonu ?? 0 }};
        const total = upData + downData;

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Up', 'Down'],
                datasets: [{
                    data: [upData, downData],
                    backgroundColor: ['#2ed573', '#ff4757'],
                    borderWidth: 0,
                    cutout: '75%', 
                    borderRadius: 5
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw: function(chart) {
                    const { width, height, ctx } = chart;
                    ctx.restore();
                    
                    ctx.font = "bold 1.8em sans-serif";
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = "#2c3e50";
                    const text = total.toString();
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2 - 8;
                    ctx.fillText(text, textX, textY);

                    ctx.font = "0.8em sans-serif";
                    ctx.fillStyle = "rgba(44, 62, 80, 0.6)";
                    const label = "TOTAL";
                    const labelX = Math.round((width - ctx.measureText(label).width) / 2);
                    const labelY = height / 2 + 18;
                    ctx.fillText(label, labelX, labelY);

                    ctx.save();
                }
            }]
        });

        // 2. --- HORIZONTAL ACCUMULATIVE STACKED RX BAR CHART ---
        const berCtx = document.getElementById('berBarChart').getContext('2d');
        
        const goodVal = {{ $good ?? 0 }};
        const warnVal = {{ $warn ?? 0 }};
        const badVal  = {{ $bad ?? 0 }};
        const maxBarLimit = goodVal + warnVal + badVal;

        new Chart(berCtx, {
            type: 'bar',
            data: {
                labels: ['Signal Matrix'], 
                datasets: [
                    { label: 'Good', data: [goodVal], backgroundColor: '#2ed573' },
                    { label: 'Warning', data: [warnVal], backgroundColor: '#ffa502' },
                    { label: 'Risk', data: [badVal], backgroundColor: '#ff4757' }
                ]
            },
            options: {
                indexAxis: 'y', 
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top', 
                        labels: { color: '#333', boxWidth: 12, font: { size: 11 } } 
                    }
                },
                scales: {
                    x: { 
                        stacked: true, 
                        grid: { display: true, color: 'rgba(0,0,0,0.05)' },
                        ticks: { color: '#666', font: { size: 10 } },
                        max: maxBarLimit > 0 ? maxBarLimit : 10
                    },
                    y: { 
                        stacked: true, 
                        display: false, 
                        grid: { display: false }
                    }
                }
            }
        });
    });
    </script>
    @endif
</x-app-layout>