<x-app-layout>
    <div class="py-4 px-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
            <h4 class="mb-0 text-dark font-weight-normal">
                <i class="fa fa-user-shield text-primary mr-2"></i>Role Management
            </h4>
            <button type="button" class="btn btn-primary font-weight-bold" data-toggle="modal" data-target="#CreateRoleModal">
                <i class="fa fa-plus mr-1"></i> Add New Role
            </button>
        </div>

        <!-- Role List Table -->
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase text-secondary font-weight-bold small">
                            <tr>
                                <th class="py-3 px-4">#ID</th>
                                <th class="py-3">Role Name</th>
                                <th class="py-3">Assigned Permissions (`identity_permission`)</th>
                                <th class="py-3">Created Date</th>
                                <th class="py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td class="px-4 font-weight-bold text-primary">#{{ $role->id }}</td>
                                    <td class="font-weight-bold text-dark">{{ $role->name }}</td>
                                    <td>
                                        @php $permIds = array_filter(explode(',', $role->identity_permission)); @endphp
                                        @forelse($permIds as $pId)
                                            <span class="badge badge-info px-2 py-1 mr-1">ID: {{ trim($pId) }}</span>
                                        @empty
                                            <span class="badge badge-secondary">No Permissions</span>
                                        @endforelse
                                    </td>
                                    <td><small class="text-muted">{{ $role->createtime ?? $role->created_at }}</small></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning mr-1" data-toggle="modal" data-target="#EditRoleModal{{ $role->id }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('roles.delete', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Role Modal -->
                                <div class="modal fade" id="EditRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title font-weight-bold"><i class="fa fa-edit mr-1"></i> Edit Role: {{ $role->name }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body text-left">
                                                    <div class="form-group mb-3">
                                                        <label class="font-weight-bold">Role Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="font-weight-bold d-block mb-2">Select Master Permissions</label>
                                                        <div class="row border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                                            @php $activePerms = explode(',', $role->identity_permission); @endphp
                                                            @foreach($permissions as $perm)
                                                                <div class="col-md-6 mb-2">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="identity_permission[]" value="{{ $perm->id }}" id="edit_perm_{{ $role->id }}_{{ $perm->id }}" {{ in_array($perm->id, $activePerms) ? 'checked' : '' }}>
                                                                        <label class="form-check-label font-weight-bold" for="edit_perm_{{ $role->id }}_{{ $perm->id }}">
                                                                            {{ $perm->permission }}
                                                                            <small class="text-muted d-block">{{ $perm->url }}</small>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-warning text-white font-weight-bold">Update Role</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No roles created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Role Modal -->
    <div class="modal fade" id="CreateRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fa fa-plus-circle mr-1"></i> Create New Role</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Accounts Manager" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold d-block mb-2">Assign Permissions from `permission_table`</label>
                            <div class="row border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                                @forelse($permissions as $perm)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="identity_permission[]" value="{{ $perm->id }}" id="create_perm_{{ $perm->id }}">
                                            <label class="form-check-label font-weight-bold" for="create_perm_{{ $perm->id }}">
                                                {{ $perm->permission }}
                                                <small class="text-muted d-block">{{ $perm->url }}</small>
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted">No records found in `permission_table`.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary font-weight-bold">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>