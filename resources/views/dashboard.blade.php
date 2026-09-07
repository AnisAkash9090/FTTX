<x-app-layout>
<div class="">
    <div class="row g-3">
        
        <!-- 1. OLT Status: Standard Classic Ring (Green/Red) -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent fw-bold py-2 border-bottom text-muted small">
                    OLT STATUS
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-2">
                    <div id="oltStatusChart"></div>
                </div>
            </div>
        </div>

        <!-- 2. OLT Types: Thin Ring / Modern Hollow Style (Purple/Cyan/Amber) -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent fw-bold py-2 border-bottom text-muted small">
                    OLT TYPES
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-2">
                    <div id="oltTypeChart"></div>
                </div>
            </div>
        </div>

        <!-- 3. ONU Status: Semi-Circle Gauge Donut Style (Teal/Dark) -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent fw-bold py-2 border-bottom d-flex justify-content-between align-items-center">
                    <span class="text-muted small">ONU STATUS</span>
                    <span class="badge bg-light text-dark border fw-normal"><a type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#oltSummaryModal">
    <i class="fa fa-server me-1"></i> Total: {{ $totalOnus }}
</a></span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-2">
                    <div id="onuStatusChart"></div>
                </div>
            </div>
        </div>

        <!-- 4. OLT Brand: Rounded Bar Chart -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-transparent fw-bold py-2 border-bottom text-muted small">
                    OLT BRANDS
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-2">
                    <div id="oltBrandChart" class="w-100"></div>
                </div>
            </div>
        </div>

    </div>
</div>
<br>
<!-- Summary Cards Grid -->
<div class="row mb-4">
    <!-- Total Active OLTs -->
    <div class="col-md-3">
        <div class="card bg-primary text-white cursor-pointer" data-toggle="modal" data-target="#oltSummaryModal" onclick="filterOltTable('all')">
            <div class="card-body">
                <h5 class="card-title">Total Active OLTs</h5>
                <h2 class="mb-0">{{ count($oltOnuSummaries) }}</h2>
                <small class="text-light">Click to view all OLTs</small>
            </div>
        </div>
    </div>

    <!-- Healthy OLTs (Both OK) -->
 <!-- Healthy OLTs (Strict check: BOTH must be 'success') -->
<div class="col-md-3">
    <div class="card bg-success text-white cursor-pointer" data-toggle="modal" data-target="#oltSummaryModal" onclick="filterOltTable('healthy')">
        <div class="card-body">
            <h5 class="card-title">Healthy OLTs</h5>
            <h2 class="mb-0">
                {{ $oltOnuSummaries->where('snmp_alert', 'success')->where('ssh_alert', 'success')->count() }}
            </h2>
            <small class="text-light">Both SNMP & SSH OK</small>
        </div>
    </div>
</div>

    <!-- Warnings (Store Failed) -->
    <div class="col-md-3">
        <div class="card bg-warning text-dark cursor-pointer" data-toggle="modal" data-target="#oltSummaryModal" onclick="filterOltTable('warning')">
            <div class="card-body">
                <h5 class="card-title">Warnings</h5>
                <h2 class="mb-0">
                    {{ $oltOnuSummaries->filter(fn($i) => $i->snmp_alert === 'warning' || $i->ssh_alert === 'warning')->count() }}
                </h2>
                <small>DB Store Issues</small>
            </div>
        </div>
    </div>

    <!-- Critical Failures -->
    <div class="col-md-3">
        <div class="card bg-danger text-white cursor-pointer" data-toggle="modal" data-target="#oltSummaryModal" onclick="filterOltTable('danger')">
            <div class="card-body">
                <h5 class="card-title">Failed Connections</h5>
                <h2 class="mb-0">
                    {{ $oltOnuSummaries->filter(fn($i) => $i->snmp_alert === 'danger' || $i->ssh_alert === 'danger')->count() }}
                </h2>
                <small class="text-light">Unreachable / Login Fail</small>
            </div>
        </div>
    </div>
</div>
<style>
/* Full screen modal overrides for Bootstrap 4 */
.modal-fullscreen {
    padding: 0 !important;
}

.modal-fullscreen .modal-dialog {
    width: 100%;
    max-width: 100%;
    height: 100%;
    margin: 0;
}

.modal-fullscreen .modal-content {
    height: 100%;
    border: 0;
    border-radius: 0;
}

.modal-fullscreen .modal-body {
    overflow-y: auto;
}
</style>
<!-- Bootstrap 4 OLT Summary Modal -->
<div class="modal fade" id="oltSummaryModal" tabindex="-1" role="dialog" aria-labelledby="oltSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-width: 95%!important;">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="oltSummaryModalLabel">
                    <i class="fas fa-server mr-2"></i>OLT Health & ONU Summary Status
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <!-- Table Controls -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <input type="text" id="modalTableSearch" class="form-control w-25" placeholder="Search OLTs...">
    <div>
        <button class="btn btn-sm btn-outline-success mr-2" onclick="copyTableToClipboard()">
            <i class="fas fa-copy"></i> Copy for Excel
        </button>
        <span id="copyFeedback" class="badge badge-success d-none">Copied!</span>
    </div>
</div>

<!-- Table -->
<table class="table table-bordered table-hover mb-0" id="oltStatusTable">
    <thead class="thead-light">
        <tr>
            <th onclick="sortTable(0, 'number')" class="cursor-pointer">
                #ID <span class="sort-icon">↕</span>
            </th>
            <th onclick="sortTable(1, 'string')" class="cursor-pointer">
                OLT Name <span class="sort-icon">↕</span>
            </th>
            <th onclick="sortTable(2, 'string')" class="cursor-pointer">
                SNMP Status <span class="sort-icon">↕</span>
            </th>
            <th onclick="sortTable(3, 'string')" class="cursor-pointer">
                SSH / Telnet Status <span class="sort-icon">↕</span>
            </th>
            <th onclick="sortTable(4, 'number')" class="text-center cursor-pointer">
                Total ONUs <span class="sort-icon">↕</span>
            </th>
            <th onclick="sortTable(5, 'number')" class="text-center cursor-pointer">
                Online <span class="sort-icon">↕</span>
            </th>
            <th onclick="sortTable(6, 'number')" class="text-center cursor-pointer">
                Offline <span class="sort-icon">↕</span>
            </th>
        </tr>
    </thead>
  <tbody>
    @forelse($oltOnuSummaries as $olt)
        <tr class="olt-row" 
            data-snmp="{{ $olt->snmp_alert }}" 
            data-ssh="{{ $olt->ssh_alert }}">
            
            <!-- 1. OLT ID -->
            <td data-sort="{{ $olt->id }}">
                <strong>#{{ $olt->id }}</strong>
            </td>

            <!-- 2. OLT Name -->
            <td>
                <strong>{{ $olt->olt_name }}</strong>
            </td>

            <!-- 3. SNMP Status Alert -->
            <td>
                <span class="badge badge-{{ $olt->snmp_alert === 'warning' ? 'warning' : ($olt->snmp_alert === 'danger' ? 'danger' : 'success') }}">
                    {{ $olt->snmp_badge }}
                </span>
                @if($olt->snmp_alert !== 'success' && !empty($olt->details_snmp))
                    <br><small class="text-danger font-weight-bold">{{ $olt->details_snmp }}</small>
                @endif
            </td>

            <!-- 4. SSH / Telnet Status Alert -->
            <td>
                <span class="badge badge-{{ $olt->ssh_alert === 'warning' ? 'warning' : ($olt->ssh_alert === 'danger' ? 'danger' : 'success') }}">
                    {{ $olt->ssh_badge }}
                </span>
                @if($olt->ssh_alert !== 'success' && !empty($olt->details_sshtelnet))
                    <br><small class="text-danger font-weight-bold">{{ $olt->details_sshtelnet }}</small>
                @endif
            </td>

            <!-- 5. Total ONUs -->
            <td class="text-center font-weight-bold" data-sort="{{ $olt->total_onu }}">
                {{ $olt->total_onu }}
            </td>

            <!-- 6. Online ONUs -->
            <td class="text-center text-success font-weight-bold" data-sort="{{ $olt->online_onu }}">
                {{ $olt->online_onu }}
            </td>

            <!-- 7. Offline ONUs -->
            <td class="text-center text-danger font-weight-bold" data-sort="{{ $olt->offline_onu }}">
                {{ $olt->offline_onu }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted py-3">
                No active OLT record found.
            </td>
        </tr>
    @endforelse
</tbody>
</table>

<!-- Footer Pagination Controls -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div id="pageRecordCountInfo" class="text-muted small"></div>
    <div>
        <button id="btnPrevPage" class="btn btn-sm btn-secondary" onclick="changePage(-1)">Previous</button>
        <span id="btnPageIndicator" class="mx-2 font-weight-bold small"></span>
        <button id="btnNextPage" class="btn btn-sm btn-secondary" onclick="changePage(1)">Next</button>
    </div>
</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
window.filterOltTable = function(filterType) {
    if (searchInput) searchInput.value = '';

    const allDOMRows = Array.from(table.querySelectorAll('tbody tr'));

    if (filterType === 'all') {
        allRows = allDOMRows;
    } else if (filterType === 'healthy') {
        allRows = allDOMRows.filter(r => 
            r.getAttribute('data-snmp') === 'success' && r.getAttribute('data-ssh') === 'success'
        );
    } else if (filterType === 'warning') {
        allRows = allDOMRows.filter(r => 
            r.getAttribute('data-snmp') === 'warning' || r.getAttribute('data-ssh') === 'warning'
        );
    } else if (filterType === 'danger') {
        allRows = allDOMRows.filter(r => 
            r.getAttribute('data-snmp') === 'danger' || r.getAttribute('data-ssh') === 'danger'
        );
    }

    currentPage = 1;
    applyFilterAndPaginate();
};
</script>

<style>
.cursor-pointer {
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
}
.cursor-pointer:hover {
    transform: translateY(-3px);
}
</style>

<style>/* ===== Unique MAC Finder Floating Button ===== */
.mac-finder-btn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 1040;
    
    display: flex;
    align-items: center;
    gap: 10px;
    
    padding: 14px 22px 14px 16px;
    border: none;
    border-radius: 50px;
    
    background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
    color: white;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: 0.3px;
    
    box-shadow: 
        0 8px 25px rgba(13, 110, 253, 0.45),
        0 0 0 0 rgba(102, 16, 242, 0.4);
    
    cursor: pointer;
    overflow: hidden;
    outline: none;
    
    /* Initial state - hidden for animation */
    opacity: 0;
    transform: translateY(40px) scale(0.85);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

/* Entrance animation - starts 2 seconds after page load */
.mac-finder-btn.animate-in {
    opacity: 1;
    transform: translateY(0) scale(1);
    animation: softPulse 3s ease-in-out 2.8s infinite;
}

/* Soft continuous pulse after entrance */
@keyframes softPulse {
    0%, 100% {
        box-shadow: 
            0 8px 25px rgba(13, 110, 253, 0.45),
            0 0 0 0 rgba(102, 16, 242, 0.35);
    }
    50% {
        box-shadow: 
            0 10px 32px rgba(13, 110, 253, 0.6),
            0 0 0 12px rgba(102, 16, 242, 0);
    }
}

/* Icon container */
.mac-finder-btn .btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    font-size: 14px;
    transition: transform 0.3s ease;
}

/* Text */
.mac-finder-btn .btn-text {
    position: relative;
    z-index: 2;
}

/* Glow effect on hover */
.mac-finder-btn .btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.25),
        transparent
    );
    transition: left 0.55s ease;
}

/* Hover effects */
.mac-finder-btn:hover {
    transform: translateY(-4px) scale(1.04);
    box-shadow: 
        0 14px 35px rgba(13, 110, 253, 0.55),
        0 0 0 0 rgba(102, 16, 242, 0.4);
    background: linear-gradient(135deg, #0b5ed7 0%, #5a0fc8 100%);
}

.mac-finder-btn:hover .btn-icon {
    transform: rotate(12deg) scale(1.1);
}

.mac-finder-btn:hover .btn-glow {
    left: 100%;
}

/* Active / click */
.mac-finder-btn:active {
    transform: translateY(-1px) scale(0.98);
}

/* Optional: make it slightly smaller on mobile */
@media (max-width: 576px) {
    .mac-finder-btn {
        padding: 12px 18px 12px 14px;
        font-size: 14px;
        bottom: 20px;
        right: 20px;
    }
    
    .mac-finder-btn .btn-icon {
        width: 28px;
        height: 28px;
        font-size: 13px;
    }
}</style>
<!-- Floating MAC Finder Button (Bottom-Right) -->
<!-- Unique Floating MAC Finder Button -->
<button type="button"
        id="macFinderBtn"
        class="mac-finder-btn"
        data-toggle="modal"
        data-target="#macFinderModal"
        title="Open MAC Finder">
    <span class="btn-icon">
        <i class="fa fa-search"></i>
    </span>
    <span class="btn-text">MAC Finder</span>
    <span class="btn-glow"></span>
</button>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('macFinderBtn');
        
        // Wait 2 seconds after page load, then animate the button in
        setTimeout(function () {
            btn.classList.add('animate-in');
        }, 400);
    });
</script>
<!-- MAC Finder Modal (Bootstrap 4 Compatible) -->
<div class="modal fade" id="macFinderModal" tabindex="-1" role="dialog" aria-labelledby="macFinderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="macFinderModalLabel">
                    <i class="fa fa-network-wired text-primary mr-2"></i> MAC Finder & Lookup
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Search Bar -->
                <form id="macSearchForm">
                    <div class="input-group input-group-lg mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light"><i class="fa fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="macAddressInput" class="form-control" placeholder="Enter MAC (e.g. 4C:AE:1C:00:39:C0 or 4cae1c0039c0)" required>
                        <div class="input-group-append">
                            <button class="btn btn-primary font-weight-bold px-4" type="submit" id="btnSearchMac">
                                Search
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Loading State -->
                <div id="macSearchLoader" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Querying IEEE registry & local database...</p>
                </div>

                <!-- Result Container -->
                <div id="macResultContainer" class="d-none">
                    
                    <!-- 1. Local Database Results -->
                    <div class="card border-0 bg-light mb-4 shadow-sm">
                        <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-database text-primary mr-2"></i> Records in Your Database</span>
                            <span id="localRecordBadge" class="badge badge-secondary">0 Found</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 200px;">
                                <table class="table table-hover align-middle mb-0 small">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>MAC</th>
                                            <th>Port</th>
                                            <th>Device</th>
                                            <th>Branch</th>
                                            <th>RX/TX</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="localDbMatchRows">
                                        <!-- JS populates this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 2. IEEE Vendor Information -->
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white font-weight-bold text-dark">
                            <i class="fa fa-building text-info mr-2"></i> IEEE Vendor & Block Details
                        </div>
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small d-block font-weight-bold text-uppercase mb-0">Vendor / Company</label>
                                    <span id="resVendorCompany" class="font-weight-bold text-dark">N/A</span>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small d-block font-weight-bold text-uppercase mb-0">Address Prefix</label>
                                    <code id="resVendorPrefix" class="font-weight-bold">N/A</code>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small d-block font-weight-bold text-uppercase mb-0">Is Private?</label>
                                    <span id="resIsPrivate" class="badge badge-secondary">No</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small d-block font-weight-bold text-uppercase mb-0">Company Address</label>
                                    <span id="resVendorAddress" class="small text-secondary">N/A</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small d-block font-weight-bold text-uppercase mb-0">Country</label>
                                    <span id="resVendorCountry" class="small font-weight-bold text-dark">N/A</span>
                                </div>
                            </div>

                            <hr class="my-2">

                            <div class="row small">
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Block Type:</span>
                                    <strong id="resBlockType" class="d-block">N/A</strong>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Start Address:</span>
                                    <code id="resBlockStart" class="d-block text-dark">N/A</code>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">End Address:</span>
                                    <code id="resBlockEnd" class="d-block text-dark">N/A</code>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-muted">Registry Updated:</span>
                                    <strong id="resBlockUpdated" class="d-block">N/A</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. Global Setup for AJAX (CSRF Token + JSON Headers)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // 2. Focus input field when modal opens
    $('#macFinderModal').on('shown.bs.modal', function () {
        $('#macAddressInput').trigger('focus').select();
    });

    // 3. Reset form and results when modal closes
    $('#macFinderModal').on('hidden.bs.modal', function () {
        $('#macSearchForm')[0].reset();
        $('#macResultContainer').addClass('d-none');
        $('#macSearchLoader').addClass('d-none');
        $('#btnSearchMac').prop('disabled', false);
    });

    // 4. Form Submission & AJAX Query
    $('#macSearchForm').on('submit', function(e) {
        e.preventDefault();
        
        var rawMac = $('#macAddressInput').val();
        var mac = rawMac ? rawMac.trim() : '';

        if (!mac) {
            alert('Please enter a MAC address.');
            return;
        }

        // Show spinner, disable submit button, hide previous results
        $('#macSearchLoader').removeClass('d-none');
        $('#macResultContainer').addClass('d-none');
        $('#btnSearchMac').prop('disabled', true);

        $.ajax({
            url: "{{ route('mac.finder.search') }}",
            type: "GET",
            dataType: "json",
            data: { mac: mac },
            success: function(response) {
                $('#macSearchLoader').addClass('d-none');
                $('#btnSearchMac').prop('disabled', false);

                if (!response || !response.success) {
                    alert((response && response.message) ? response.message : 'Error processing request.');
                    return;
                }

                // --- A. Render Local Database Records ---
                var $localRows = $('#localDbMatchRows');
                var $localBadge = $('#localRecordBadge');
                $localRows.empty();

                if (response.local_records && response.local_records.length > 0) {
                    $localBadge.attr('class', 'badge badge-success').text(response.local_records.length + ' Found in System');

                    $.each(response.local_records, function(idx, row) {
                        var statusBadge = (parseInt(row.sys_sts) === 1) 
                            ? '<span class="badge badge-success">Up</span>' 
                            : '<span class="badge badge-danger">Down</span>';

                        var rxVal = row.sys_rx ? (parseFloat(row.sys_rx) / 10).toFixed(1) : '0.0';
                        var txVal = row.sys_tx ? (parseFloat(row.sys_tx) / 10).toFixed(1) : '0.0';
                        var rxTx = rxVal + ' / ' + txVal + ' dBm';

                    var macDisplay = row.sys_mac ? row.sys_mac : (row.router_mac ? row.router_mac : 'N/A');

var tr = '<tr>' +
    '<td><code class="font-weight-bold text-dark">' + macDisplay + '</code></td>' +
    '<td>' + (row.sys_port || 'N/A') + '</td>' +
    '<td>' + (row.sys_device || 'N/A') + '</td>' +
    '<td>' + (row.sys_from || 'N/A') + '</td>' +
    '<td><small class="font-weight-bold">' + rxTx + '</small></td>' +
    '<td>' + statusBadge + '</td>' +
'</tr>';
                        
                        $localRows.append(tr);
                    });
                } else {
                    $localBadge.attr('class', 'badge badge-warning text-dark').text('0 Matches in System');
                    $localRows.html('<tr><td colspan="6" class="text-center text-muted py-3"><i class="fa fa-info-circle mr-1"></i> No matching MAC address found in your local system database.</td></tr>');
                }

                // --- B. Render IEEE Vendor Details ---
                var v = response.vendor || {};
                $('#resVendorCompany').text(v.company || 'Unknown Vendor');
                $('#resVendorPrefix').text(v.prefix || 'N/A');
                $('#resIsPrivate').text(v.is_private || 'No');
                $('#resVendorAddress').text(v.address || 'N/A');
                $('#resVendorCountry').text(v.country || 'N/A');
                $('#resBlockType').text(v.block_type || 'N/A');
                $('#resBlockStart').text(v.block_start || 'N/A');
                $('#resBlockEnd').text(v.block_end || 'N/A');
                $('#resBlockUpdated').text(v.updated || 'N/A');

                // Display result block
                $('#macResultContainer').removeClass('d-none');
            },
            error: function(xhr, status, error) {
                $('#macSearchLoader').addClass('d-none');
                $('#btnSearchMac').prop('disabled', false);

                console.error("XHR Status:", xhr.status);
                console.error("XHR Response:", xhr.responseText);

                var errorMsg = "Failed to fetch MAC details.";
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMsg = "Route 'mac.finder.search' not found (404). Check web.php.";
                } else if (xhr.status === 500) {
                    errorMsg = "Server Error (500). Check storage/logs/laravel.log for backend exceptions.";
                } else if (xhr.status === 419) {
                    errorMsg = "CSRF Token expired (419). Please refresh the page.";
                }

                alert('Error (' + xhr.status + '): ' + errorMsg);
            }
        });
    });
});
</script>


<div class="">
    <h4 class="text-center gradient-text"><i class="fa fa-bolt"></i> System Metrics</h4>
    <small>E.G.: Loss DB = Tx - (-RX)</small>
    <style>
        /* Custom Fullscreen Modal for Bootstrap 4 */
.modal-fullscreen {
    width: 100vw;
    max-width: 100%;
    height: 100vh;
    margin: 0;
    padding: 0;
}

.modal-fullscreen .modal-content {
    height: 100vh;
    border: 0;
    border-radius: 0;
}

.modal-fullscreen .modal-body {
    overflow-y: auto;
}
    </style>
    <style>
    .fullModalTableContainer { 
        overflow-y: auto; 
        height: calc(100vh - 110px); /* Stretches to fill vertical height */
        width: 100%;
    } 
    .fullModalTableContainer thead th { 
        position: sticky; 
        top: 0; 
        background-color: #212529 !important; 
        color: #fff !important; 
        z-index: 10;
    }
</style>
<style>
    .metric-card {
        background: #ffffff0c;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 1rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1);
        border-color: rgba(203, 213, 225, 1);
    }

    /* Subtle top accent bar on hover */
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: currentColor;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    .metric-card:hover::before {
        opacity: 1;
    }

    .icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: transform 0.25s ease;
    }

    .metric-card:hover .icon-wrapper {
        transform: scale(1.08);
    }

    /* Custom Theme Accent Light Backgrounds */
    .bg-blue-subtle-custom { background-color: #eff6ff; color: #2563eb; }
    .bg-purple-subtle-custom { background-color: #faf5ff; color: #9333ea; }
    .bg-pink-subtle-custom { background-color: #fdf2f8; color: #db2777; }
    .bg-indigo-subtle-custom { background-color: #eef2ff; color: #4f46e5; }
    .bg-emerald-subtle-custom { background-color: #ecfdf5; color: #059669; }
    .bg-orange-subtle-custom { background-color: #fff7ed; color: #ea580c; }
    .bg-teal-subtle-custom { background-color: #f0fdfa; color: #0d9488; }
    .bg-cyan-subtle-custom { background-color: #ecfeff; color: #0891b2; }
</style>

<div class="row g-3">
    @php
        $cards = [
            ['label' => '24+', 'color' => 'text-blue-600', 'bg' => 'bg-blue-subtle-custom', 'key' => '24db', 'first' => '240', 'last' => '249'],
            ['label' => '25+', 'color' => 'text-purple-600', 'bg' => 'bg-purple-subtle-custom', 'key' => '25db', 'first' => '250', 'last' => '259'],
            ['label' => '26+', 'color' => 'text-pink-600', 'bg' => 'bg-pink-subtle-custom', 'key' => '26db', 'first' => '260', 'last' => '269'],
            ['label' => '27+', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-subtle-custom', 'key' => '27db', 'first' => '270', 'last' => '279'],
            ['label' => '28+', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-subtle-custom', 'key' => '28db', 'first' => '280', 'last' => '289'],
            ['label' => '29+', 'color' => 'text-orange-600', 'bg' => 'bg-orange-subtle-custom', 'key' => '29db', 'first' => '290', 'last' => '299'],
            ['label' => '30+', 'color' => 'text-teal-600', 'bg' => 'bg-teal-subtle-custom', 'key' => '30db', 'first' => '300', 'last' => '309'],
            ['label' => '31+', 'color' => 'text-cyan-600', 'bg' => 'bg-cyan-subtle-custom', 'key' => '31dbp', 'first' => '310', 'last' => '12240'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-12 col-sm-6 col-lg-3 mt-2">
            <div class="metric-card p-3 shadow-sm h-100 {{ $card['color'] }}" 
                 style="cursor: pointer;"
                 onclick="getlist('{{ $card['first'] }}', '{{ $card['last'] }}', '{{ $card['label'] }}')">
                
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <span class="text-uppercase tracking-wider fw-semibold text-muted small d-block mb-1">
                            Signal Loss
                        </span>
                        <h6 class="fw-bold mb-0 {{ $card['color'] }} fs-5">
                            dBm {{ $card['label'] }}
                        </h6>
                    </div>
                    
                    <div class="icon-wrapper {{ $card['bg'] }}">
                        <i class="fa-solid fa-server"></i>
                    </div>
                </div>

                <div class="d-flex align-items-baseline justify-content-between pt-1 border-top">
                    <p class="metric-value Count fs-2 fw-extrabold  mb-0" data-count-name="{{ $card['key'] }}">
                        0
                    </p>
                    <span class="badge rounded-pill {{ $card['bg'] }} px-2 py-1 small">
                        Records <i class="fa-solid fa-chevron-right ms-1 style="font-size: 0.7rem;""></i>
                    </span>
                </div>
                
            </div>
        </div>
    @endforeach
</div>
<!-- Fullscreen Bootstrap Modal -->
<div class="modal fade" id="dbshow" tabindex="-1" role="dialog" aria-labelledby="dbshowLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="dbshowLabel"><i class="fa fa-list"></i> dBm <b class="dbshowp"></b> Records</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="showdatadb" class="h-100"></div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function getlist(id, lastid, label) {
        $('.dbshowp').html(label);
        $('#showdatadb').html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading ONU records...</p></div>');
        
        var myModal = new bootstrap.Modal(document.getElementById('dbshow'));
        myModal.show();

        $.ajax({
            url: "{{ route('olt.losslist') }}",
            method: "GET",
            data: { 
                lossidfirst: id, 
                lossidlast: lastid 
            },
            success: function(response) {
                $('#showdatadb').html(response);
            },
            error: function() {
                $('#showdatadb').html('<div class="alert alert-danger m-4">Failed to load data from server.</div>');
            }
        });
    }

    function fetchMetrics() {
        $.ajax({
            url: "{{ route('olt.metrics') }}",
            method: "GET",
            dataType: "json",
            success: function(response) {
                $('.Count').each(function() {
                    var key = $(this).attr('data-count-name');
                    if (response[key] !== undefined) {
                        $(this).text(response[key]);
                    } else {
                        $(this).text('0');
                    }
                });
            }
        });
    }

    setInterval(fetchMetrics, 5000);
    fetchMetrics();
</script>
<br>
<style>
/* ===== Compact OLT Card ===== */
.olt-card-compact {
    position: relative;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eef2f7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    transition: all 0.25s ease;
    height: 100%;
}

.olt-card-compact:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
}

.olt-accent {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #0d6efd, #6610f2);
}

.olt-content {
    padding: 12px 14px 12px 16px;
}

.olt-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 70%;
}

/* Badge */
.olt-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
}

.olt-badge.good {
    background: #d1fae5;
    color: #065f46;
}
.olt-badge.warning {
    background: #fef3c7;
    color: #92400e;
}
.olt-badge.critical {
    background: #fee2e2;
    color: #991b1b;
}

/* Stats */
.olt-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.stat {
    text-align: center;
    flex: 1;
}

.stat-num {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.68rem;
    color: #64748b;
}

/* Progress */
.olt-progress {
    display: flex;
    height: 5px;
    border-radius: 6px;
    overflow: hidden;
    background: #e2e8f0;
}

.olt-progress .bar.online {
    background: linear-gradient(90deg, #10b981, #34d399);
}
.olt-progress .bar.offline {
    background: linear-gradient(90deg, #ef4444, #f87171);
}
</style>


<!-- OLT Summary Fullscreen Modal (Bootstrap 4) -->
<div class="modal fade" id="oltSummaryModal" tabindex="-1" role="dialog" aria-labelledby="oltSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document" style="max-width: 98vw; margin: 10px auto;">
        <div class="modal-content bg-dark text-light border-secondary">
            
            <!-- Modal Header -->
            <div class="modal-header border-secondary bg-secondary text-white">
                <h5 class="modal-title font-weight-bold" id="oltSummaryModalLabel">
                    <i class="fa fa-network-wired"></i> OLT Network Summary
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body bg-dark">
                <div class="container-fluid">
                    <div class="row gr-2">
                        @foreach($oltOnuSummaries as $olt)
                            @php
                                $total = $olt->total_onu ?: 1;
                                $onlinePercent = round(($olt->online_onu / $total) * 100);
                                $statusClass = $onlinePercent >= 80 ? 'success' : ($onlinePercent >= 50 ? 'warning' : 'danger');
                            @endphp

                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                                <div class="olt-card-compact border border-secondary rounded bg-secondary p-3 position-relative shadow-sm overflow-hidden h-100">
                                    
                                    <!-- Dynamic Status Accent Bar -->
                                    <div class="olt-accent status-{{ $statusClass }}"></div>

                                    <div class="olt-content ml-2">
                                        <!-- Card Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="olt-title font-weight-bold text-truncate mb-0 text-white" title="{{ $olt->olt_name }}">
                                                {{ $olt->olt_name }}
                                            </h6>
                                            <span class="olt-badge badge-{{ $statusClass }} px-2 py-1 rounded-pill small">
                                                {{ $onlinePercent }}%
                                            </span>
                                        </div>

                                        <!-- Stats Metrics Grid -->
                                        <div class="olt-stats d-flex justify-content-between text-center bg-dark rounded p-2 mb-2 border border-secondary">
                                            <div class="stat flex-fill border-right border-secondary">
                                                <span class="stat-num d-block font-weight-bold text-primary">{{ $olt->total_onu }}</span>
                                                <span class="stat-label text-uppercase text-muted small" style="font-size: 10px;">Total</span>
                                            </div>
                                            <div class="stat flex-fill border-right border-secondary">
                                                <span class="stat-num d-block font-weight-bold text-success">{{ $olt->online_onu }}</span>
                                                <span class="stat-label text-uppercase text-muted small" style="font-size: 10px;">Active</span>
                                            </div>
                                            <div class="stat flex-fill">
                                                <span class="stat-num d-block font-weight-bold text-danger">{{ $olt->offline_onu }}</span>
                                                <span class="stat-label text-uppercase text-muted small" style="font-size: 10px;">Offline</span>
                                            </div>
                                        </div>

                                        <!-- Stacked Connection Progress Bar -->
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $onlinePercent }}%" aria-valuenow="{{ $onlinePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $onlinePercent }}%" aria-valuenow="{{ 100 - $onlinePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-secondary bg-dark">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // -------------------------------------------------------------
        // CHART 1: OLT Status (Classic Thick Donut with Center Total)
        // -------------------------------------------------------------
        new ApexCharts(document.querySelector("#oltStatusChart"), {
            series: [{{ $activeCount }}, {{ $inactiveCount }}],
            labels: ['Active', 'Inactive'],
            chart: {
                type: 'donut',
                height: 220
            },
            colors: ['#22c55e', '#ef4444'], // Emerald Green & Crimson Red
            stroke: { width: 2, colors: ['#fff'] },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%', // Thicker ring
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total OLTs',
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        }
                    }
                }
            },
            legend: { position: 'bottom', fontSize: '11px' }
        }).render();

        // -------------------------------------------------------------
        // CHART 2: OLT Types (Thin Hollow Modern Ring + Unique Colors)
        // -------------------------------------------------------------
        var typeLabels = {!! json_encode($typeCounts->pluck('type')->map(fn($t) => strtoupper($t ?: 'Unknown'))) !!};
        var typeData = {!! json_encode($typeCounts->pluck('total')) !!};

        new ApexCharts(document.querySelector("#oltTypeChart"), {
            series: typeData,
            labels: typeLabels,
            chart: {
                type: 'donut',
                height: 220
            },
            colors: ['#8b5cf6', '#06b6d4', '#f59e0b', '#ec4899'], // Violet, Electric Cyan, Amber, Pink
            stroke: { width: 5, colors: ['#f8fafc'] }, // Separated arc styling
            plotOptions: {
                pie: {
                    donut: {
                        size: '82%', // Thin outer ring style
                        labels: {
                            show: true,
                            value: { fontSize: '16px', fontWeight: 'bold' }
                        }
                    }
                }
            },
            legend: { position: 'bottom', fontSize: '11px' }
        }).render();

        // -------------------------------------------------------------
        // CHART 3: ONU Status (Semi-Circle / Gauge Style Donut)
        // -------------------------------------------------------------
        new ApexCharts(document.querySelector("#onuStatusChart"), {
            series: [{{ $onlineOnus }}, {{ $offlineOnus }}],
            labels: ['Online', 'Offline'],
            chart: {
                type: 'donut',
                height: 220
            },
            colors: ['#0d9488', '#475569'], // Dark Teal & Slate Gray
            stroke: { width: 0 },
            plotOptions: {
                pie: {
                    startAngle: -90, // Semi-circle cut
                    endAngle: 90,
                    offsetY: 10,
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: { offsetY: -20 },
                            value: { offsetY: -10, fontSize: '16px', fontWeight: 'bold' }
                        }
                    }
                }
            },
            grid: { padding: { bottom: -80 } },
            legend: { position: 'bottom', fontSize: '11px', offsetY: -20 }
        }).render();

        // -------------------------------------------------------------
        // CHART 4: OLT Brands (Flat Rounded Bar Chart)
        // -------------------------------------------------------------
        var brandLabels = {!! json_encode($brandCounts->pluck('olt_brand')->map(fn($b) => strtoupper($b ?: 'Unknown'))) !!};
        var brandData = {!! json_encode($brandCounts->pluck('total')) !!};

        new ApexCharts(document.querySelector("#oltBrandChart"), {
            series: [{ name: 'OLTs', data: brandData }],
            chart: {
                type: 'bar',
                height: 200,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    distributed: true,
                    columnWidth: '45%'
                }
            },
            dataLabels: { enabled: true, style: { fontSize: '10px' } },
            xaxis: {
                categories: brandLabels,
                labels: { style: { fontSize: '10px' } }
            },
            yaxis: { show: false },
            legend: { show: false },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#64748b']
        }).render();

    });
</script>
<script>
    (function() {
        const pageSize = 10; // Updated page size to 10 entries per page
        let currentPage = 1;
        let visibleRows = [];
        let sortDirections = {};

        const table = document.getElementById('oltStatusTable');
        if (!table) return;

        let allRows = Array.from(table.querySelectorAll('tbody tr'));
        const searchInput = document.getElementById('modalTableSearch');

        function applyFilterAndPaginate() {
            const query = searchInput ? searchInput.value.toLowerCase().trim() : '';

            // 1. Search Filter (Checks row text & data attributes)
            visibleRows = allRows.filter(row => {
                const text = row.textContent.toLowerCase();
                return text.includes(query);
            });

            const totalPages = Math.ceil(visibleRows.length / pageSize) || 1;
            if (currentPage > totalPages) currentPage = 1;

            // 2. Hide all rows
            allRows.forEach(row => row.style.display = 'none');

            // 3. Display current page rows
            const startIdx = (currentPage - 1) * pageSize;
            const endIdx = Math.min(startIdx + pageSize, visibleRows.length);

            for (let i = startIdx; i < endIdx; i++) {
                if (visibleRows[i]) visibleRows[i].style.display = '';
            }

            // 4. Update Footer Pagination Info
            const totalCount = visibleRows.length;
            const fromCount = totalCount > 0 ? startIdx + 1 : 0;
            
            const countInfo = document.getElementById('pageRecordCountInfo');
            if (countInfo) {
                countInfo.textContent = `Showing ${fromCount} to ${endIdx} of ${totalCount} entries` + (query ? ' (filtered)' : '');
            }

            const pageIndicator = document.getElementById('btnPageIndicator');
            if (pageIndicator) pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;

            const prevBtn = document.getElementById('btnPrevPage');
            if (prevBtn) prevBtn.disabled = (currentPage === 1);

            const nextBtn = document.getElementById('btnNextPage');
            if (nextBtn) nextBtn.disabled = (currentPage === totalPages || totalPages === 0);
        }

        // Column Sorting Functionality
        window.sortTable = function(columnIndex, type) {
            const isAsc = !sortDirections[columnIndex];
            sortDirections = {}; // Reset all directions
            sortDirections[columnIndex] = isAsc;

            // Update Header Icons
            const headers = table.querySelectorAll('thead th');
            headers.forEach((th, idx) => {
                const icon = th.querySelector('.sort-icon');
                if (icon) {
                    if (idx === columnIndex) {
                        icon.textContent = isAsc ? '▲' : '▼';
                        icon.style.opacity = '1';
                    } else {
                        icon.textContent = '↕';
                        icon.style.opacity = '0.6';
                    }
                }
            });

            // Sort DOM Rows
            allRows.sort((a, b) => {
                const cellA = a.children[columnIndex];
                const cellB = b.children[columnIndex];

                const valA = cellA.getAttribute('data-order') || cellA.getAttribute('data-sort') || cellA.innerText.trim();
                const valB = cellB.getAttribute('data-order') || cellB.getAttribute('data-sort') || cellB.innerText.trim();

                if (type === 'number') {
                    const numA = parseFloat(valA) || 0;
                    const numB = parseFloat(valB) || 0;
                    return isAsc ? numA - numB : numB - numA;
                } else {
                    return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
                }
            });

            // Re-append sorted rows to tbody
            const tbody = table.querySelector('tbody');
            allRows.forEach(row => tbody.appendChild(row));

            // Re-apply filter and reset to page 1
            currentPage = 1;
            applyFilterAndPaginate();
        };

        // Custom Dashboard Card Filter Trigger
        window.filterOltTable = function(filterType) {
            if (searchInput) searchInput.value = ''; // Reset standard search box

            if (filterType === 'all') {
                allRows = Array.from(table.querySelectorAll('tbody tr'));
            } else if (filterType === 'healthy') {
                allRows = Array.from(table.querySelectorAll('tbody tr')).filter(r => 
                    r.getAttribute('data-snmp') === 'success' && r.getAttribute('data-ssh') === 'success'
                );
            } else if (filterType === 'warning') {
                allRows = Array.from(table.querySelectorAll('tbody tr')).filter(r => 
                    r.getAttribute('data-snmp') === 'warning' || r.getAttribute('data-ssh') === 'warning'
                );
            } else if (filterType === 'danger') {
                allRows = Array.from(table.querySelectorAll('tbody tr')).filter(r => 
                    r.getAttribute('data-snmp') === 'danger' || r.getAttribute('data-ssh') === 'danger'
                );
            }

            currentPage = 1;
            applyFilterAndPaginate();
        };

        window.changePage = function(direction) {
            currentPage += direction;
            applyFilterAndPaginate();
            const container = document.querySelector('.table-responsive') || document.querySelector('.modal-body');
            if (container) container.scrollTop = 0;
        };

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                // Re-populate all rows when manually searching
                allRows = Array.from(table.querySelectorAll('tbody tr'));
                currentPage = 1;
                applyFilterAndPaginate();
            });
        }

        // Initial setup
        applyFilterAndPaginate();
    })();

    // Copy Table Data for Excel (Copies ALL Filtered/Visible Rows, not just current page)
    function copyTableToClipboard() {
        const table = document.getElementById('oltStatusTable');
        if (!table) return;

        let htmlTable = '<table border="1"><thead><tr>';
        const headers = Array.from(table.querySelectorAll('thead th'));
        const headerNames = headers.map(th => th.innerText.replace(/[↕▲▼]/g, '').trim());
        
        headerNames.forEach(name => {
            htmlTable += `<th>${name}</th>`;
        });
        htmlTable += '</tr></thead><tbody>';

        // Get all filtered rows (ignoring pagination display:none)
        const query = (document.getElementById('modalTableSearch')?.value || '').toLowerCase().trim();
        const allRows = Array.from(table.querySelectorAll('tbody tr'));
        
        const filteredRows = allRows.filter(row => {
            return row.textContent.toLowerCase().includes(query);
        });
        
        filteredRows.forEach(row => {
            htmlTable += '<tr>';
            Array.from(row.querySelectorAll('td')).forEach(td => {
                let text = td.innerText.replace(/\r?\n|\r/g, ' ').trim();
                htmlTable += `<td>${text}</td>`;
            });
            htmlTable += '</tr>';
        });
        htmlTable += '</tbody></table>';

        let tsvText = headerNames.join('\t') + '\n';
        filteredRows.forEach(row => {
            let line = Array.from(row.querySelectorAll('td'))
                .map(td => td.innerText.replace(/\r?\n|\r/g, ' ').trim())
                .join('\t');
            tsvText += line + '\n';
        });

        const showFeedback = () => {
            const badge = document.getElementById('copyFeedback');
            if (badge) {
                badge.classList.remove('d-none');
                setTimeout(() => badge.classList.add('d-none'), 2000);
            }
        };

        if (navigator.clipboard && window.isSecureContext && typeof ClipboardItem !== 'undefined') {
            const blobHtml = new Blob([htmlTable], { type: 'text/html' });
            const blobText = new Blob([tsvText], { type: 'text/plain' });
            const data = [new ClipboardItem({
                'text/html': blobHtml,
                'text/plain': blobText
            })];

            navigator.clipboard.write(data).then(showFeedback).catch(() => {
                fallbackDOMCopy(htmlTable, showFeedback);
            });
        } else {
            fallbackDOMCopy(htmlTable, showFeedback);
        }
    }

    function fallbackDOMCopy(htmlString, successCallback) {
        const container = document.createElement('div');
        container.innerHTML = htmlString;
        container.style.position = 'fixed';
        container.style.pointerEvents = 'none';
        container.style.opacity = '0';
        document.body.appendChild(container);

        window.getSelection().removeAllRanges();
        const range = document.createRange();
        range.selectNode(container);
        window.getSelection().addRange(range);

        try {
            const successful = document.execCommand('copy');
            window.getSelection().removeAllRanges();
            document.body.removeChild(container);

            if (successful) {
                successCallback();
            } else {
                alert('Unable to copy table data.');
            }
        } catch (err) {
            window.getSelection().removeAllRanges();
            document.body.removeChild(container);
            alert('Copy failed: ' + err);
        }
    }
</script>
</x-app-layout>