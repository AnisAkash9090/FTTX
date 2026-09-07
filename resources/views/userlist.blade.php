<x-app-layout>
    <div class="py-4 px-3">
        <!-- Page Title Bar -->
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4 border-left-primary">
            <h4 class="mb-0 text-dark font-weight-normal">
                <i class="fa fa-users text-primary me-2"></i>User Registration & Accounts
            </h4>
            <button type="button" class="btn btn-success font-weight-bold shadow-sm" data-toggle="modal" data-target="#AddUser">
                <i class="fa fa-user-plus me-1"></i> + Add User
            </button>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="AddUser" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold">
                            <i class="mdi mdi-account-plus me-1"></i> Create New User Account
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <form action="{{ route('users.storedata') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4 bg-light">
                            
                            <!-- Personal Details Section -->
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold mb-3">
                                        <i class="mdi mdi-account-details-outline me-1"></i> Personal Details
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label font-weight-bold">User Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required placeholder="e.g. John Doe">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="contact" class="form-label font-weight-bold">Contact Number</label>
                                            <input type="text" name="contact" id="contact" class="form-control" value="{{ old('contact') }}" placeholder="e.g. +8801700000000">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="address" class="form-label font-weight-bold">Address <span class="text-danger">*</span></label>
                                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" required placeholder="Full Address">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Organizational Details Section -->
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold mb-3">
                                        <i class="mdi mdi-office-building-outline me-1"></i> Organization & System ID
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="company" class="form-label font-weight-bold">Company Name <span class="text-danger">*</span></label>
                                            <select class="form-control" name="company" id="company" required>
                                                <option value="">-- Select Company --</option>
                                                <option value="Skynet Chowmuhani">Skynet Chowmuhani</option>
                                                <option value="Hello Tech">Hello Tech</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="employee_id" class="form-label font-weight-bold">Attendance ID (Auto) <span class="text-danger">*</span></label>
                                            <input type="text" name="attendece_id" id="employee_id" class="form-control bg-white font-weight-bold" value="{{ $nextAttendanceId }}" readonly required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account & Security Section (Always Active) -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-primary font-weight-bold mb-3">
                                        <i class="mdi mdi-lock-outline me-1"></i> Login Credentials & Avatar
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6 mb-3">
                                            <label for="user_id" class="form-label font-weight-bold">Login Email <span class="text-danger">*</span></label>
                                            <input type="email" name="user_id" id="user_id" class="form-control" value="{{ old('user_id') }}" required placeholder="user@domain.com">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="pass" class="form-label font-weight-bold">Password <span class="text-danger">*</span></label>
                                            <input type="password" name="pass" id="pass" class="form-control" required placeholder="Minimum 6 characters">
                                        </div>
                                        <!-- <div class="col-md-12">
                                            <label for="image_upload" class="form-label font-weight-bold">Profile Image (Optional)</label>
                                            <input type="file" name="img" id="image_upload" class="form-control">
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer bg-white">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 font-weight-bold">Save User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Redesigned User List Table -->
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fa fa-list text-secondary me-2"></i> Registered Users Directory
                </h6>
                <span class="badge badge-light text-dark font-weight-normal border px-3 py-2">
                    Total Registered: <strong>{{ count($users) }}</strong>
                </span>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase text-secondary font-weight-bold small">
                            <tr>
                                <th class="py-3 px-4" style="width: 100px;">User ID</th>
                                <th class="py-3">Profile</th>
                                <th class="py-3">User Details</th>
                                <th class="py-3">Address</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3">Created By</th>
                                  @can('has-permission', '1,12') <th class="py-3">Action</th> @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-4 fw-bold text-primary">
                                        <span class="badge badge-primary text-light bg-opacity-10 text-primary font-monospace px-2 py-1">
                                            #{{ $user->user_id }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->img)
                                                <img src="{{ asset('images/user/' . $user->img) }}" alt="{{ $user->name }}" class="rounded-circle border border-2 shadow-sm" width="42" height="42" style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center font-weight-bold shadow-sm" style="width: 42px; height: 42px; font-size: 14px;">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark mb-0" style="font-size: 14px;">{{ $user->name }}</div>
                                        <small class="text-muted d-block">
                                            <i class="fa fa-envelope text-secondary mr-1"></i> {{ $user->email ?? 'No login email' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-secondary small">
                                            <i class="fa fa-map-marker-alt text-danger mr-1"></i> {{ $user->address }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($user->sts == 1)
                                            <span class="badge badge-success px-3 py-1 font-weight-normal">
                                                <i class="fa fa-circle text-white small mr-1"></i> Active
                                            </span>
                                        @else
                                            <span class="badge badge-danger px-3 py-1 font-weight-normal">
                                                <i class="fa fa-circle text-white small mr-1"></i> Inactive
                                            </span>
                                        @endif
                                    </td> 
                                    <td>
                                        <small class="text-dark font-weight-bold d-block">
                                            @php $creator = function_exists('userlistsfn') ? userlistsfn($user->createinfo) : ['name' => $user->createinfo]; @endphp
                                            {{ $creator['name'] ?? 'System' }}
                                        </small>
                                    </td> 
                                    <!-- New Permission Action Button -->
        
                                 @can('has-permission', '1,12')      <td class="text-center">
                <a href="{{ route('permissions.assign.index', ['id' => $user->user_id]) }}" 
                   class="btn btn-sm {{ request('id') == $user->user_id ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fa fa-user-shield mr-1"></i> Assign Roles
                </a>
            </td>@endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-user-slash fa-2x mb-2 d-block text-secondary"></i>
                                        No users found. Click "+ Add User" to register a new user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>