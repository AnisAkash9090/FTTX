<x-app-layout>
    <div class="py-4 px-3">
        <!-- Main Top Header -->
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
            <h4 class="mb-0 text-dark font-weight-normal">
                <i class="fa fa-user-tag text-success mr-2"></i>Assign Roles to User
            </h4>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <form action="{{ route('permissions.assign.store') }}" method="POST">
                        @csrf
                        
                        <!-- Hidden Target User ID -->
                        <input type="hidden" name="attendence_id" value="{{ $selectedUser->attendece_id ?? request('id') }}">

                        <!-- Card Header with User Title & Top Save Button -->
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center sticky-top shadow-sm" style="z-index: 100;">
                            <h6 class="m-0 font-weight-bold text-dark">
                                <i class="fa fa-user-shield text-primary mr-1"></i> 
                                Roles for: <span class="text-primary">{{ $selectedUser->name ?? 'User #' . request('id') }}</span>
                            </h6>
                            <button type="submit" class="btn btn-sm btn-success font-weight-bold px-3">
                                <i class="fa fa-save mr-1"></i> Save Roles
                            </button>
                        </div>

                        <div class="card-body">
                            <!-- User Profile Summary -->
                            @if(isset($selectedUser))
                                <div class="d-flex align-items-center bg-light p-3 rounded mb-3 border">
                                    @if(!empty($selectedUser->img))
                                        <img src="{{ asset('images/user/' . $selectedUser->img) }}" alt="{{ $selectedUser->name }}" class="rounded-circle border mr-3" width="44" height="44" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center font-weight-bold mr-3 shadow-sm" style="width: 44px; height: 44px; font-size: 15px;">
                                            {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ $selectedUser->name }}</h6>
                                        <small class="text-muted d-block">ID: #{{ $selectedUser->attendece_id }} | {{ $selectedUser->email ?? 'No email set' }}</small>
                                    </div>
                                </div>
                            @endif

                            <!-- Filter Counters Bar -->
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-xs btn-outline-dark active filter-btn" data-filter="all">
                                        <input type="radio" name="role_filter" checked> All (<span id="cnt-all">0</span>)
                                    </label>
                                    <label class="btn btn-xs btn-outline-success filter-btn" data-filter="assigned">
                                        <input type="radio" name="role_filter"> Assigned (<span id="cnt-assigned">0</span>)
                                    </label>
                                    <label class="btn btn-xs btn-outline-secondary filter-btn" data-filter="unassigned">
                                        <input type="radio" name="role_filter"> Unassigned (<span id="cnt-unassigned">0</span>)
                                    </label>
                                </div>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="btn-select-all">Select Visible</button>
                            </div>

                            <!-- Live Search Input -->
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fa fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="role-search-input" class="form-control border-left-0" placeholder="Search roles by name...">
                            </div>

                            <!-- Scrollable Roles List Container -->
                            <div class="border rounded p-2 bg-light role-scroll-box" style="max-height: 360px; overflow-y: auto;">
                                @forelse($roles as $rl)
                                    @php 
                                        $isAssigned = in_array((string)$rl->id, array_map('strval', $assignedRoleIds ?? [])); 
                                    @endphp
                                    <div class="role-item mb-2 p-2 rounded bg-white border {{ $isAssigned ? 'border-success shadow-sm' : '' }}" 
                                         data-role-name="{{ strtolower($rl->name) }}"
                                         data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                                        <div class="form-check d-flex align-items-center pl-4">
                                            <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $rl->id }}" id="role_check_{{ $rl->id }}" {{ $isAssigned ? 'checked' : '' }}>
                                            <label class="form-check-label font-weight-bold text-dark d-flex justify-content-between align-items-center w-100 pl-2" for="role_check_{{ $rl->id }}" style="cursor: pointer;">
                                                <span class="role-title-text">{{ $rl->name }}</span>
                                                <span class="badge {{ $isAssigned ? 'badge-success' : 'badge-light text-muted' }} status-badge small">
                                                    {{ $isAssigned ? 'Assigned' : 'Unassigned' }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-3 small">No roles available in database.</div>
                                @endforelse

                                <div id="no-roles-found" class="text-center text-muted py-3 small d-none">
                                    No roles match your search filter.
                                </div>
                            </div>
                        </div>

                     
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Filtering & Counter Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('role-search-input');
            const roleItems = document.querySelectorAll('.role-item');
            const noResults = document.getElementById('no-roles-found');
            
            const cntAll = document.getElementById('cnt-all');
            const cntAssigned = document.getElementById('cnt-assigned');
            const cntUnassigned = document.getElementById('cnt-unassigned');
            
            let activeFilter = 'all';

            function updateCountsAndVisibility() {
                const query = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;
                let assignedCount = 0;
                let unassignedCount = 0;
                let totalCount = roleItems.length;

                roleItems.forEach(item => {
                    const checkbox = item.querySelector('.role-checkbox');
                    const isChecked = checkbox.checked;
                    const roleName = item.getAttribute('data-role-name');
                    const badge = item.querySelector('.status-badge');

                    // Sync visual state & badge when user checks/unchecks dynamically
                    if (isChecked) {
                        assignedCount++;
                        item.setAttribute('data-assigned', 'true');
                        item.classList.add('border-success', 'shadow-sm');
                        badge.className = 'badge badge-success status-badge small';
                        badge.textContent = 'Assigned';
                    } else {
                        unassignedCount++;
                        item.setAttribute('data-assigned', 'false');
                        item.classList.remove('border-success', 'shadow-sm');
                        badge.className = 'badge badge-light text-muted status-badge small';
                        badge.textContent = 'Unassigned';
                    }

                    // Filtering Logic (Search Query + Tab Filter)
                    const matchesSearch = roleName.includes(query);
                    let matchesFilter = true;

                    if (activeFilter === 'assigned') {
                        matchesFilter = isChecked;
                    } else if (activeFilter === 'unassigned') {
                        matchesFilter = !isChecked;
                    }

                    if (matchesSearch && matchesFilter) {
                        item.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        item.classList.add('d-none');
                    }
                });

                // Update counter numbers
                cntAll.textContent = totalCount;
                cntAssigned.textContent = assignedCount;
                cntUnassigned.textContent = unassignedCount;

                // Toggle empty state
                if (visibleCount === 0 && totalCount > 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            }

            // Real-time Checkbox State Change Listener
            roleItems.forEach(item => {
                const checkbox = item.querySelector('.role-checkbox');
                checkbox.addEventListener('change', updateCountsAndVisibility);
            });

            // Live Search Input Listener
            searchInput.addEventListener('input', updateCountsAndVisibility);

            // Filter Tabs Click Listener
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    activeFilter = this.getAttribute('data-filter');
                    updateCountsAndVisibility();
                });
            });

            // Quick Select/Deselect All Visible Items
            const selectAllBtn = document.getElementById('btn-select-all');
            let selectAllToggle = true;

            selectAllBtn.addEventListener('click', function () {
                roleItems.forEach(item => {
                    if (!item.classList.contains('d-none')) {
                        const checkbox = item.querySelector('.role-checkbox');
                        checkbox.checked = selectAllToggle;
                    }
                });
                selectAllToggle = !selectAllToggle;
                this.textContent = selectAllToggle ? 'Select Visible' : 'Deselect Visible';
                updateCountsAndVisibility();
            });

            // Initial Execution
            updateCountsAndVisibility();
        });
    </script>
</x-app-layout>