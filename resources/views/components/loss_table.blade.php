@if($records->count() > 0)
    <style>
        .modal-table-wrapper {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
            background: #fff;
        }
        .modal-toolbar {
            padding: 12px 16px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .fullModalTableContainer {
            flex: 1;
            overflow-y: auto;
            width: 100%;
        }
        .fullModalTableContainer thead th {
            position: sticky;
            top: 0;
            background-color: #212529 !important;
            color: #fff !important;
            z-index: 10;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
        }
        .fullModalTableContainer thead th:hover {
            background-color: #343a40 !important;
        }
        .sort-icon {
            font-size: 0.75rem;
            margin-left: 4px;
            opacity: 0.6;
        }
        .table-pagination-footer {
            padding: 10px 16px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>

    <div class="modal-table-wrapper">
        <!-- Toolbar: Search + Copy Button -->
        <div class="modal-toolbar d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-success fw-bold" onclick="copyTableToClipboard()">
                    <i class="fa fa-copy me-1"></i> Copy for Excel
                </button>
                <span id="copyFeedback" class="badge bg-success d-none">Copied!</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">Search:</span>
                <input type="text" id="modalTableSearch" class="form-control form-control-sm" placeholder="Filter MAC, Port, Branch..." style="width: 240px;">
            </div>
        </div>

        <!-- Table Container -->
        <div class="fullModalTableContainer">
            <table class="table table-hover align-middle mb-0" id="lossListTable">
                <thead>
                    <tr>
                        <th onclick="sortTable(0, 'number')"># <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(1, 'string')">Port <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(2, 'number')">TX [dBm] <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(3, 'number')">RX [dBm] <span class="sort-icon">↕</span></th>
                        
                        <th onclick="sortTable(4, 'string')">Onu MAC <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(5, 'string')">Router MAC <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(6, 'string')">Status <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(7, 'string')">Device <span class="sort-icon">↕</span></th>
                        <th onclick="sortTable(8, 'string')">Branch <span class="sort-icon">↕</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $index => $row)
                        <tr class="table-row-item">
                            <td data-sort="{{ $index + 1 }}">{{ $index + 1 }}</td>
                            <td data-sort="{{ $row->sys_port }}">{{ $row->sys_port }}</td>
                            <td data-sort="{{ $row->sys_tx }}">{{ $row->sys_tx / 10 }}</td>
                            <td data-sort="{{ $row->sys_rx }}">{{ $row->sys_rx / 10 }}</td>
                            <td data-sort="{{ $row->sys_mac }}"><code>{{ $row->sys_mac }}</code></td>
                            <td data-sort="{{ $row->router_mac }}"><code>{{ $row->router_mac }}</code></td>
                            <td data-sort="{{ $row->sys_sts }}">
                                @if($row->sys_sts == 1)
                                    <span class="badge bg-success">Up</span>
                                @elseif($row->sys_sts == 0)
                                    <span class="badge bg-danger">Down</span>
                                @else
                                    <span class="badge bg-warning text-dark">N/F</span>
                                @endif
                            </td>
                            <td data-sort="{{ $row->sys_device }}">{{ $row->sys_device }}</td>
                            <td data-sort="{{ $row->sys_from }}">{{ $row->sys_from }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="table-pagination-footer d-flex justify-content-between align-items-center">
            <span class="small text-muted" id="pageRecordCountInfo">Showing 0 to 0 of {{ $records->count() }} entries</span>
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary" id="btnPrevPage" onclick="changePage(-1)">Previous</button>
                <button type="button" class="btn btn-outline-primary disabled" id="btnPageIndicator">Page 1</button>
                <button type="button" class="btn btn-outline-secondary" id="btnNextPage" onclick="changePage(1)">Next</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const pageSize = 100;
            let currentPage = 1;
            let visibleRows = [];
            let sortDirections = {};

            const table = document.getElementById('lossListTable');
            let allRows = Array.from(table.querySelectorAll('tbody tr'));
            const searchInput = document.getElementById('modalTableSearch');

            function applyFilterAndPaginate() {
                const query = searchInput.value.toLowerCase().trim();

                // 1. Search Filter
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
                    visibleRows[i].style.display = '';
                }

                // 4. Update Footer Info
                const totalCount = visibleRows.length;
                const fromCount = totalCount > 0 ? startIdx + 1 : 0;
                document.getElementById('pageRecordCountInfo').textContent = 
                    `Showing ${fromCount} to ${endIdx} of ${totalCount} entries` + (query ? ' (filtered)' : '');

                document.getElementById('btnPageIndicator').textContent = `Page ${currentPage} of ${totalPages}`;
                document.getElementById('btnPrevPage').disabled = (currentPage === 1);
                document.getElementById('btnNextPage').disabled = (currentPage === totalPages || totalPages === 0);
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
                    if (idx === columnIndex) {
                        icon.textContent = isAsc ? '▲' : '▼';
                        icon.style.opacity = '1';
                    } else {
                        icon.textContent = '↕';
                        icon.style.opacity = '0.6';
                    }
                });

                // Sort DOM Rows
                allRows.sort((a, b) => {
                    const valA = a.children[columnIndex].getAttribute('data-sort') || a.children[columnIndex].innerText.trim();
                    const valB = b.children[columnIndex].getAttribute('data-sort') || b.children[columnIndex].innerText.trim();

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

            window.changePage = function(direction) {
                currentPage += direction;
                applyFilterAndPaginate();
                document.querySelector('.fullModalTableContainer').scrollTop = 0;
            };

            searchInput.addEventListener('input', function() {
                currentPage = 1;
                applyFilterAndPaginate();
            });

            // Initial view setup
            applyFilterAndPaginate();
        })();

        // Copy Table Data for Excel
        function copyTableToClipboard() {
            const table = document.getElementById('lossListTable');
            if (!table) return;

            // 1. Build HTML Table Payload for Excel Clipboard
            let htmlTable = '<table border="1"><thead><tr>';
            const headers = Array.from(table.querySelectorAll('thead th'));
            const headerNames = headers.map(th => th.innerText.replace(/[↕▲▼]/g, '').trim());
            
            headerNames.forEach(name => {
                htmlTable += `<th>${name}</th>`;
            });
            htmlTable += '</tr></thead><tbody>';

            // Get only current visible (filtered & paginated) rows
            const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(r => r.style.display !== 'none');
            
            visibleRows.forEach(row => {
                htmlTable += '<tr>';
                Array.from(row.querySelectorAll('td')).forEach(td => {
                    let text = td.innerText.replace(/\r?\n|\r/g, ' ').trim();
                    htmlTable += `<td>${text}</td>`;
                });
                htmlTable += '</tr>';
            });
            htmlTable += '</tbody></table>';

            // 2. Build Unquoted Clean TSV Fallback
            let tsvText = headerNames.join('\t') + '\n';
            visibleRows.forEach(row => {
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

            // 3. Write HTML + Plain Text formats into Clipboard API
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

        // DOM Range Selection Fallback for Non-Secure HTTP environments
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
@else
    <div class="p-5 text-center alert alert-warning m-4">No records found for this range.</div>
@endif