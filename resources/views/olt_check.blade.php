<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid p-4">

        <!-- Header + Search -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="mb-0 font-weight-bold">OLT SNMP Monitor</h2>
            <div class="form-group mb-0" style="min-width: 280px;">
                <input type="text" id="oltSearch" class="form-control" placeholder="Search OLT name or IP...">
            </div>
        </div>

        <!-- OLT Cards -->
        <div id="oltList">
            @forelse($oltConfigs as $olt)
                @php
                    $oids = $oltOids[$olt->id] ?? collect();
                    $oidRow = $oids->first(); // one row per OLT typically
                @endphp
                <div class="card mb-3 shadow-sm olt-card"
                     id="olt-block-{{ $olt->id }}"
                     data-name="{{ strtolower($olt->olt_name) }}"
                     data-ip="{{ $olt->olt_ip }}">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1 font-weight-bold">{{ $olt->olt_name }}</h5>
                                <p class="text-muted mb-0 small">
                                    <i class="fas fa-network-wired mr-1"></i> {{ $olt->olt_ip }}
                                    @if($olt->olt_community)
                                        <span class="ml-2"><i class="fas fa-key mr-1"></i> Community set</span>
                                    @endif
                                </p>
                            </div>

                            <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
                                <span class="badge badge-secondary mr-2 d-none olt-status" id="status-{{ $olt->id }}"></span>

                                <!-- Existing Python SNMP button -->
                                <button type="button"
                                        class="btn btn-primary run-snmp-btn mr-2 mb-1"
                                        data-id="{{ $olt->id }}"
                                        data-name="{{ $olt->olt_name }}"
                                        data-ip="{{ $olt->olt_ip }}">
                                    <span class="btn-text">
                                        <i class="fas fa-terminal mr-1"></i> Run SNMP Check
                                    </span>
                                </button>

                                <!-- New Live OID Walk button -->
                                <button type="button"
                                        class="btn btn-info live-walk-btn mb-1"
                                        data-id="{{ $olt->id }}"
                                        data-name="{{ $olt->olt_name }}"
                                        data-ip="{{ $olt->olt_ip }}"
                                        data-community="{{ $olt->olt_community }}"
                                        @if(!$oidRow) disabled title="No OID configured" @endif>
                                    <span class="btn-text">
                                        <i class="fas fa-search mr-1"></i> Live OID Walk
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- Hidden OID data for this OLT (used by Live Walk modal) -->
                        @if($oidRow)
                        <script type="application/json" id="oid-data-{{ $olt->id }}">
                            {!! json_encode([
                                'port_names'  => $oidRow->port_names,
                                'tx_values'   => $oidRow->tx_values,
                                'alterTX'     => $oidRow->alterTX,
                                'rx_values'   => $oidRow->rx_values,
                                'up_value'    => $oidRow->up_value,
                                'down_values' => $oidRow->down_values,
                                'sts'         => $oidRow->sts,
                                'lcv'         => $oidRow->lcv,
                                'sn_values'   => $oidRow->sn_values,
                                'reason'      => $oidRow->reason,
                                'onumod'      => $oidRow->onumod,
                                'mac_get'     => $oidRow->mac_get,
                                'onu_dist'    => $oidRow->onu_dist,
                                'onuvendor'   => $oidRow->onuvendor,
                                'router_mac'  => $oidRow->router_mac,
                            ]) !!}
                        </script>
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-info">No OLT configurations found.</div>
            @endforelse
        </div>

        <div id="noResults" class="alert alert-warning d-none">No OLT matched your search.</div>
    </div>

    <!-- ===================== Modal: Run SNMP Check (Python) ===================== -->
    <div class="modal fade" id="snmpModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-satellite-dish mr-2"></i>
                        SNMP Check — <span id="modalOltName">—</span>
                    </h5>
                    <button type="button" class="close text-white" id="modalCloseBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modalLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-warning" style="width:3rem;height:3rem;" role="status"></div>
                        <p class="mt-3 mb-0 font-weight-bold text-warning">Executing Python SNMP script in background...</p>
                        <p class="text-muted small mb-0">You can close this modal — the check will keep running.<br>Click the button again anytime to re-open.</p>
                    </div>
                    <div id="modalResult" class="d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modalFooterClose">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== Modal: Live OID Walk ===================== -->
    <div class="modal fade" id="walkModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-search mr-2"></i>
                        Live OID Walk — <span id="walkOltName">—</span>
                    </h5>
                    <button type="button" class="close text-white" id="walkCloseBtn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- Step 1: Select OIDs -->
                    <div id="walkSelectPanel">
                        <p class="text-muted small mb-3">Select one or more OID columns to walk (SNMPv2c):</p>
                        <div class="row" id="oidCheckboxList">
                            <!-- Filled by JS -->
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectAllOids">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="clearAllOids">Clear</button>
                        </div>
                    </div>

                    <!-- Step 2: Loading -->
                    <div id="walkLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-info" style="width:3rem;height:3rem;" role="status"></div>
                        <p class="mt-3 mb-0 font-weight-bold text-info">Running live SNMP walk...</p>
                        <p class="text-muted small mb-0">You can close this modal — the walk continues in background.<br>Click the button again to re-open.</p>
                    </div>

                    <!-- Step 3: Results -->
                    <div id="walkResult" class="d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="walkFooterClose">Close</button>
                    <button type="button" class="btn btn-info" id="startWalkBtn">
                        <i class="fas fa-play mr-1"></i> Start Walk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function () {

        // ========== Shared state ==========
        const runningJobs  = {};   // Python SNMP
        const lastResults  = {};
        let currentModalId = null;

        const walkJobs     = {};   // Live walk jobs
        const walkResults  = {};
        let currentWalkId  = null;
        let pendingWalk    = null; // { oltId, name, ip, community, oids }

        // Human labels for OID columns
        const OID_LABELS = {
            port_names:  'Port Names',
            tx_values:   'TX Values',
            alterTX:     'Alter TX',
            rx_values:   'RX Values',
            up_value:    'Up Value',
            down_values: 'Down Values',
            sts:         'Status (sts)',
            lcv:         'LCV',
            sn_values:   'SN Values',
            reason:      'Reason',
            onumod:      'ONU Mod',
            mac_get:     'MAC Get',
            onu_dist:    'ONU Distance',
            onuvendor:   'ONU Vendor',
            router_mac:  'Router MAC'
        };

        // ========== Search ==========
        $('#oltSearch').on('input', function () {
            const query = $(this).val().toLowerCase().trim();
            let visible = 0;
            $('.olt-card').each(function () {
                const name = $(this).data('name') || '';
                const ip   = ($(this).data('ip') || '').toString().toLowerCase();
                if (name.includes(query) || ip.includes(query)) {
                    $(this).removeClass('d-none'); visible++;
                } else {
                    $(this).addClass('d-none');
                }
            });
            $('#noResults').toggleClass('d-none', !(visible === 0 && query !== ''));
        });

        // ========== Modal close helpers ==========
        $('#modalCloseBtn, #modalFooterClose').on('click', () => $('#snmpModal').modal('hide'));
        $('#walkCloseBtn, #walkFooterClose').on('click', () => $('#walkModal').modal('hide'));

        $('#snmpModal').on('hidden.bs.modal', () => { currentModalId = null; });
        $('#walkModal').on('hidden.bs.modal', () => { currentWalkId = null; });

        // ================================================================
        //  PYTHON SNMP CHECK  (existing feature)
        // ================================================================
        function openSnmpModal(oltId, oltName, oltIp) {
            currentModalId = oltId;
            $('#modalOltName').text(oltName + ' (' + oltIp + ')');
            if (runningJobs[oltId]) {
                $('#modalLoading').removeClass('d-none');
                $('#modalResult').addClass('d-none').empty();
            } else if (lastResults[oltId]) {
                $('#modalLoading').addClass('d-none');
                $('#modalResult').removeClass('d-none').html(lastResults[oltId].html);
            } else {
                $('#modalLoading').removeClass('d-none');
                $('#modalResult').addClass('d-none').empty();
            }
            $('#snmpModal').modal('show');
        }

        function setSnmpBtnRunning(oltId) {
            const btn = $('.run-snmp-btn[data-id="' + oltId + '"]');
            btn.removeClass('btn-primary').addClass('btn-warning');
            btn.find('.btn-text').html('<i class="fas fa-spinner fa-spin mr-1"></i> Running... (click to view)');
            $('#status-' + oltId).removeClass('d-none badge-success badge-danger badge-secondary')
                                 .addClass('badge-warning').text('Running...');
        }

        function setSnmpBtnDone(oltId, success) {
            const btn = $('.run-snmp-btn[data-id="' + oltId + '"]');
            btn.removeClass('btn-warning').addClass('btn-primary');
            btn.find('.btn-text').html('<i class="fas fa-terminal mr-1"></i> Run SNMP Check');
            const badge = $('#status-' + oltId);
            badge.removeClass('badge-warning')
                 .addClass(success ? 'badge-success' : 'badge-danger')
                 .text(success ? 'Done' : 'Failed')
                 .removeClass('d-none');
        }

        $('.run-snmp-btn').on('click', function () {
            const btn = $(this);
            const oltId   = String(btn.data('id'));
            const oltName = btn.data('name');
            const oltIp   = btn.data('ip');

            if (runningJobs[oltId]) {
                openSnmpModal(oltId, oltName, oltIp);
                return;
            }

            runningJobs[oltId] = true;
            setSnmpBtnRunning(oltId);
            openSnmpModal(oltId, oltName, oltIp);

            $.ajax({
                url: "{{ route('olt.runSnmp') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", olt_id: oltId },
                success: function (response) {
                    let html = '', ok = false;
                    if (response.status === 'success') {
                        ok = true;
                        html += '<div class="alert alert-success mb-3"><strong>✓ Python SNMP Script Executed Successfully</strong></div>';
                        const data = typeof response.data === 'object'
                            ? JSON.stringify(response.data, null, 2) : response.data;
                        html += '<pre class="bg-dark text-success p-3 rounded small mb-0" style="max-height:400px;overflow:auto;white-space:pre-wrap;">' + data + '</pre>';
                    } else {
                        html += '<div class="alert alert-danger mb-0"><strong>Execution Failed:</strong> ' + (response.message || 'Unknown') + '</div>';
                    }
                    finishSnmpJob(oltId, ok, html);
                },
                error: function (xhr) {
                    let err = 'Request failed';
                    try {
                        const j = JSON.parse(xhr.responseText);
                        err = j.detail || j.message || err;
                    } catch(e) { err = xhr.responseText || err; }
                    const html = '<div class="alert alert-danger"><strong class="d-block mb-2">✗ Python Execution Failed</strong><pre class="bg-dark text-danger p-3 rounded small mb-0" style="max-height:400px;overflow:auto;white-space:pre-wrap;">' + err + '</pre></div>';
                    finishSnmpJob(oltId, false, html);
                }
            });
        });

        function finishSnmpJob(oltId, success, html) {
            lastResults[oltId] = { success, html };
            delete runningJobs[oltId];
            setSnmpBtnDone(oltId, success);
            if (String(currentModalId) === String(oltId) && $('#snmpModal').hasClass('show')) {
                $('#modalLoading').addClass('d-none');
                $('#modalResult').removeClass('d-none').html(html);
            }
        }

        // ================================================================
        //  LIVE OID WALK
        // ================================================================
        $('.live-walk-btn').on('click', function () {
            const btn       = $(this);
            const oltId     = String(btn.data('id'));
            const oltName   = btn.data('name');
            const oltIp     = btn.data('ip');
            const community = btn.data('community');

            // If already walking → just re-open modal
            if (walkJobs[oltId]) {
                openWalkModal(oltId, oltName, oltIp);
                return;
            }

            // If has previous result and not starting new → show result
            // Always show select panel first for a fresh walk
            pendingWalk = { oltId, oltName, oltIp, community };

            // Build checkbox list from OID data
            const raw = $('#oid-data-' + oltId).text();
            let oidMap = {};
            try { oidMap = JSON.parse(raw); } catch(e) {}

            let checkHtml = '';
            let hasAny = false;
            Object.keys(OID_LABELS).forEach(function (col) {
                const val = oidMap[col];
                if (val && val !== 'NULL' && String(val).trim() !== '') {
                    hasAny = true;
                    checkHtml += `
                        <div class="col-md-6 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input oid-check"
                                       id="oid_${oltId}_${col}" value="${col}" data-oid="${val}">
                                <label class="custom-control-label" for="oid_${oltId}_${col}">
                                    <strong>${OID_LABELS[col]}</strong>
                                    <br><code class="small text-muted">${val}</code>
                                </label>
                            </div>
                        </div>`;
                }
            });

            if (!hasAny) {
                checkHtml = '<div class="col-12"><div class="alert alert-warning mb-0">No valid OIDs configured for this OLT.</div></div>';
            }

            $('#oidCheckboxList').html(checkHtml);
            $('#walkOltName').text(oltName + ' (' + oltIp + ')');
            $('#walkSelectPanel').removeClass('d-none');
            $('#walkLoading').addClass('d-none');
            $('#walkResult').addClass('d-none').empty();
            $('#startWalkBtn').removeClass('d-none').prop('disabled', !hasAny);

            currentWalkId = oltId;
            $('#walkModal').modal('show');
        });

        // Select / Clear all
        $('#selectAllOids').on('click', () => $('.oid-check').prop('checked', true));
        $('#clearAllOids').on('click', () => $('.oid-check').prop('checked', false));

        // Start Walk
        $('#startWalkBtn').on('click', function () {
            if (!pendingWalk) return;

            const selected = [];
            $('.oid-check:checked').each(function () {
                selected.push({
                    column: $(this).val(),
                    label:  OID_LABELS[$(this).val()] || $(this).val(),
                    oid:    $(this).data('oid')
                });
            });

            if (selected.length === 0) {
                alert('Please select at least one OID column.');
                return;
            }

            const { oltId, oltName, oltIp, community } = pendingWalk;

            // Mark as running
            walkJobs[oltId] = true;
            setWalkBtnRunning(oltId);

            // Switch modal to loading
            $('#walkSelectPanel').addClass('d-none');
            $('#walkLoading').removeClass('d-none');
            $('#walkResult').addClass('d-none').empty();
            $('#startWalkBtn').addClass('d-none');
            currentWalkId = oltId;

            // Fire AJAX (background)
            $.ajax({
                url: "{{ route('olt.liveWalk') }}",
                type: "POST",
                data: {
                    _token:    "{{ csrf_token() }}",
                    olt_id:    oltId,
                    olt_ip:    oltIp,
                    community: community,
                    oids:      selected
                },
                success: function (response) {
                    let html = '', ok = false;
                    if (response.status === 'success') {
                        ok = true;
                        html += '<div class="alert alert-success mb-3"><strong>✓ Live SNMP Walk Completed</strong></div>';
                        html += renderWalkResults(response.data);
                    } else {
                        html += '<div class="alert alert-danger mb-0"><strong>Walk Failed:</strong> ' + (response.message || 'Unknown') + '</div>';
                    }
                    finishWalkJob(oltId, ok, html);
                },
                error: function (xhr) {
                    let err = 'Request failed';
                    try {
                        const j = JSON.parse(xhr.responseText);
                        err = j.detail || j.message || err;
                    } catch(e) { err = xhr.responseText || err; }
                    const html = '<div class="alert alert-danger"><strong class="d-block mb-2">✗ Live Walk Failed</strong><pre class="bg-dark text-danger p-3 rounded small mb-0" style="max-height:400px;overflow:auto;white-space:pre-wrap;">' + err + '</pre></div>';
                    finishWalkJob(oltId, false, html);
                }
            });
        });

        function openWalkModal(oltId, oltName, oltIp) {
            currentWalkId = oltId;
            $('#walkOltName').text(oltName + ' (' + oltIp + ')');
            $('#startWalkBtn').addClass('d-none');

            if (walkJobs[oltId]) {
                $('#walkSelectPanel').addClass('d-none');
                $('#walkLoading').removeClass('d-none');
                $('#walkResult').addClass('d-none').empty();
            } else if (walkResults[oltId]) {
                $('#walkSelectPanel').addClass('d-none');
                $('#walkLoading').addClass('d-none');
                $('#walkResult').removeClass('d-none').html(walkResults[oltId].html);
            }
            $('#walkModal').modal('show');
        }

        function setWalkBtnRunning(oltId) {
            const btn = $('.live-walk-btn[data-id="' + oltId + '"]');
            btn.removeClass('btn-info').addClass('btn-warning');
            btn.find('.btn-text').html('<i class="fas fa-spinner fa-spin mr-1"></i> Walking... (click to view)');
        }

        function setWalkBtnDone(oltId, success) {
            const btn = $('.live-walk-btn[data-id="' + oltId + '"]');
            btn.removeClass('btn-warning').addClass('btn-info');
            btn.find('.btn-text').html('<i class="fas fa-search mr-1"></i> Live OID Walk');
        }

        function finishWalkJob(oltId, success, html) {
            walkResults[oltId] = { success, html };
            delete walkJobs[oltId];
            setWalkBtnDone(oltId, success);

            if (String(currentWalkId) === String(oltId) && $('#walkModal').hasClass('show')) {
                $('#walkLoading').addClass('d-none');
                $('#walkSelectPanel').addClass('d-none');
                $('#walkResult').removeClass('d-none').html(html);
            }
        }

        function renderWalkResults(data) {
            // data expected: array of { column, label, oid, output, error? }
            if (!Array.isArray(data)) {
                return '<pre class="bg-dark text-success p-3 rounded small">' + JSON.stringify(data, null, 2) + '</pre>';
            }

            let html = '';
            data.forEach(function (item) {
                html += '<div class="card mb-3">';
                html += '<div class="card-header py-2 d-flex justify-content-between align-items-center">';
                html += '<strong>' + (item.label || item.column) + '</strong>';
                html += '<code class="small text-muted">' + (item.oid || '') + '</code>';
                html += '</div>';
                html += '<div class="card-body p-0">';
                if (item.error) {
                    html += '<pre class="bg-dark text-danger p-3 mb-0 small" style="max-height:250px;overflow:auto;white-space:pre-wrap;">' + item.error + '</pre>';
                } else {
                    html += '<pre class="bg-dark text-success p-3 mb-0 small" style="max-height:250px;overflow:auto;white-space:pre-wrap;">' + (item.output || '(empty)') + '</pre>';
                }
                html += '</div></div>';
            });
            return html;
        }
    });
    </script>
</x-app-layout>