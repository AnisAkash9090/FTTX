<x-app-layout>

<div class=" mt-4">




    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Add Olt</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"  aria-controls="nav-profile" aria-selected="false">View OLT</a>
      </div>
    </nav>

    <div class="tab-content text-left mt-3" id="nav-tabContent">
   <!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- ENHANCED OLT CONFIGURATION - Form & Table with Beautiful Design -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->

<style>
/* ═══════════════════════════════════════════════════════════════════════ */
/* BREADCRUMB NAVIGATION */
/* ═══════════════════════════════════════════════════════════════════════ */

.unique-breadcrumb {
    display: inline-flex;
    align-items: center;
    border-radius: 50px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    width: fit-content;
    gap: 12px;
    padding: 8px 16px;
    background: rgba(79, 70, 229, 0.08);
}

.crumb {
    color: var(--bs-primary, #4f46e5);
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    border-radius: 30px;
    padding: 6px 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.crumb:hover {
    background: rgba(79, 70, 229, 0.15);
    transform: translateY(-1px);
}

.crumb.active {
    color: var(--bs-primary, #4f46e5);
    font-weight: 700;
    background: rgba(79, 70, 229, 0.12);
}

.arrow {
    color: var(--bs-primary, #4f46e5);
    font-size: 16px;
    font-weight: bold;
    opacity: 0.7;
}

/* ═══════════════════════════════════════════════════════════════════════ */
/* FORM STYLING */
/* ═══════════════════════════════════════════════════════════════════════ */

.form-section {
    background: transparent;
    border-radius: 12px;
    padding: 0;
}

.form-group-wrapper {
    display: grid;
    gap: 16px;
}

.form-group-wrapper.two-col {
    grid-template-columns: 1fr 1fr;
}

.form-group-wrapper.three-col {
    grid-template-columns: repeat(3, 1fr);
}

.mb-3 {
    margin-bottom: 0 !important;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: inherit;
    opacity: 0.8;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-label::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 4px;
    background: var(--bs-primary, #4f46e5);
    border-radius: 50%;
}

.form-control,
.form-select {
    border-radius: 8px;
    border: 1px solid var(--bs-border-color, #dee2e6);
    padding: 6px 14px;
    font-size: 14px;
    background: transparent;
    color: inherit;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--bs-primary, #4f46e5);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.08);
    outline: none;
}

.form-control::placeholder {
    opacity: 0.6;
}

/* Form Section Headers */
.form-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--bs-border-color, #dee2e6);
}

.form-section-header h6 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-section-header::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 20px;
    background: var(--bs-primary, #4f46e5);
    border-radius: 2px;
}

/* Submit Button */
.btn-submit {
    border-radius: 8px;
    padding: 12px 32px;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 24px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(79, 70, 229, 0.2);
}

.btn-submit:active {
    transform: translateY(0);
}

/* ═══════════════════════════════════════════════════════════════════════ */
/* TABLE STYLING */
/* ═══════════════════════════════════════════════════════════════════════ */

.table-wrapper {
    border-radius: 12px;
    overflow: hidden;
    background: transparent;
}

.table {
    margin: 0;
    font-size: 13px;
}

.table thead {
    background: rgba(79, 70, 229, 0.08);
    border-bottom: 2px solid var(--bs-border-color, #dee2e6);
}

.table thead th {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 12px;
    padding: 14px 12px;
    border: none;
    color: var(--bs-primary, #4f46e5);
}

.table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid var(--bs-border-color, #dee2e6);
}

.table tbody tr:hover {
    background: rgba(79, 70, 229, 0.04);
    transform: scale(1.01);
    transform-origin: center;
}

.table tbody td {
    padding: 14px 12px;
    vertical-align: middle;
    border: none;
}

/* Status Badge */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-block;
}

.badge-success {
    background: rgba(25, 135, 84, 0.15);
    color: var(--bs-success, #198754);
}

.badge-secondary {
    background: rgba(108, 117, 125, 0.15);
    color: var(--bs-secondary, #6c757d);
}

/* Action Buttons */
.btn-action {
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 12px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: 600;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-action.btn-primary {
    background: var(--bs-primary, #4f46e5);
    color: white;
}

.btn-action.btn-danger {
    background: var(--bs-danger, #dc3545);
    color: white;
}

.btn-action.btn-success {
    background: var(--bs-success, #198754);
    color: white;
}

.action-group {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

/* Cell Icons */
.cell-icon {
    display: inline-block;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    margin-right: 6px;
}

/* Data Type Indicators */
.data-type-user {
    background: rgba(79, 70, 229, 0.15);
    color: var(--bs-primary, #4f46e5);
}

.data-type-olt {
    background: rgba(25, 135, 84, 0.15);
    color: var(--bs-success, #198754);
}

.data-type-ip {
    background: rgba(255, 193, 7, 0.15);
    color: var(--bs-warning, #ffc107);
}

.data-type-brand {
    background: rgba(23, 162, 184, 0.15);
    color: var(--bs-info, #17a2b8);
}

/* Form Result Message */
#formResult {
    border-radius: 8px;
    padding: 16px;
    display: none;
    animation: slideIn 0.3s ease;
}

#formResult.success {
    background: rgba(25, 135, 84, 0.15);
    border-left: 4px solid var(--bs-success, #198754);
    color: var(--bs-success, #198754);
    display: block;
}

#formResult.error {
    background: rgba(220, 53, 69, 0.15);
    border-left: 4px solid var(--bs-danger, #dc3545);
    color: var(--bs-danger, #dc3545);
    display: block;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .form-group-wrapper.three-col {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .form-group-wrapper.two-col,
    .form-group-wrapper.three-col {
        grid-template-columns: 1fr;
    }
    
    .action-group {
        flex-direction: column;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
    
    .table {
        font-size: 12px;
    }
    
    .table thead th,
    .table tbody td {
        padding: 10px 8px;
    }
}
</style>

<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- TAB NAVIGATION -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->



<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- TAB CONTENT -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->

<div class="tab-content">
    
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: ADD OLT FORM -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    
    <div class="tab-pane fade show active" id="nav-home" role="tabpanel">
        <form id="myForm">
            @csrf
            
            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="form-section-header">
                    <h6>Basic Information</h6>
                </div>
                
                <div class="form-group-wrapper two-col">
                  

                    <div>
                        <label class="form-label">
                            <i class="fas fa-server"></i> OLT Name
                        </label>
                        <input type="text" name="olt_name" class="form-control" placeholder="e.g., OLT-001" required>
                    </div>
                         <div class="form-group-wrapper two-col">
                    <div>
                        <label class="form-label">
                            <i class="fas fa-network-wired"></i> OLT IP
                        </label>
                        <input type="text" name="olt_ip" class="form-control" placeholder="192.168.1.1" required>
                    </div>

                    <div>
                        <label class="form-label">
                            <i class="fas fa-key"></i> OLT Community
                        </label>
                        <input type="text" name="olt_community" class="form-control" placeholder="public" required>
                    </div>
                </div>
                </div>

           
            </div>

            <!-- Connection Settings Section -->
            <div class="form-section" style="margin-top: 30px;">
                <div class="form-section-header">
                    <h6>Connection Settings</h6>
                </div>
                
                <div class="form-group-wrapper three-col">
                    <div>
                        <label class="form-label">
                            <i class="fas fa-link"></i> Access Type
                        </label>
                        <select name="olt_access" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="ssh">SSH</option>
                            <option value="telnet">Telnet</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">
                            <i class="fas fa-user-lock"></i> SSH/Telnet Username
                        </label>
                        <input type="text" name="stusername" class="form-control" placeholder="Username">
                    </div>

                    <div>
                        <label class="form-label">
                            <i class="fas fa-lock"></i> SSH/Telnet Password
                        </label>
                        <input type="password" name="oltpass" class="form-control" placeholder="Password">
                    </div>
                </div>
            </div>

            <!-- TX/RX & Interface Section -->
            <div class="form-section" style="margin-top: 30px;">
                <div class="form-section-header">
                    <h6>TX/RX Configuration</h6>
                </div>
                
                <div class="form-group-wrapper three-col">
                    <div>
                        <label class="form-label">
                            <i class="fas fa-signal"></i> TX/RX via SSH/Telnet
                        </label>
                        <select name="olt_txrx" class="form-select" required>
                            <option value="">Enable?</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">
                            <i class="fas fa-plug"></i> Interface Port
                        </label>
                        <select name="olt_interface" class="form-select" required>
                            <option value="">Select Port</option>
                            @for ($i = 1; $i <= 16; $i++)
                                <option value="{{ $i }}">Port {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div style="visibility: hidden;"></div>
                </div>
            </div>

            <!-- OLT Type & Brand Section -->
            <div class="form-section" style="margin-top: 30px;">
                <div class="form-section-header">
                    <h6>OLT Classification</h6>
                </div>
                
                <div class="form-group-wrapper two-col">
                    <div>
                        <label class="form-label">
                            <i class="fas fa-cube"></i> OLT Type
                        </label>
                        <select name="olt_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="epon">EPON</option>
                            <option value="gpon">GPON</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">
                            <i class="fas fa-building"></i> OLT Brand
                        </label>
                        <select name="olt_brand" class="form-select" required>
                            <option value="">Select Brand</option>
                            <option value="BDCOM">BDCOM</option>
                            <option value="vsol">VSOL</option>
                            <option value="cdata">C-DATA</option>
                            <option value="Huawei">Huawei</option>
                            
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Submit Configuration
            </button>

                 
        </form>
         <div id="formResult" style="margin-top:20px;"></div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: OLT LIST TABLE -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->

    <div class="tab-pane fade" id="nav-profile" role="tabpanel">
        <div class="table-wrapper" >
            <div class="table-responsive" style="padding:10px;">
                <table class="table table-hover" id="oltTable">
                    <thead>
                           <tr>
                            <th style="width: 3%;">ID</th>
                            <th style="width: 8%;">OLT Name</th>
                            <th style="width: 10%;">IP Address</th>
                            <th style="width: 10%;">Community</th>
                            <th style="width: 8%;">SSH User</th>
                            <th style="width: 5%;">Type</th>
                            <th style="width: 8%;">Brand</th>
                            <th style="width: 5%;">Connection</th>
                            <th style="width: 4%;">Ports</th>
                            <th style="width: 4%;">TX/RX</th>
                            <th style="width: 7%;">Status</th>
                            <th style="width: 12%;">Created</th>
                            <th style="width: 12%; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($olts as $olt)
                        @php
    $statusMap = [
        'active'   => ['class' => 'bg-success',   'icon' => 'check-circle'],
        'inactive' => ['class' => 'bg-danger',    'icon' => 'times-circle'],
        'warning'  => ['class' => 'bg-warning',   'icon' => 'exclamation-circle'],
    ];
    $currentSts = $statusMap[$olt->sts] ?? ['class' => 'bg-secondary', 'icon' => 'minus-circle'];
@endphp
                           <tr id="olt-row-{{ $olt->id }}" class="olt-row" data-status="{{ $olt->sts }}">
                            <!-- ID -->
                            <td>
                                <span class="badge bg-primary">{{ $olt->id }}</span>
                            </td>
 
                            <!-- OLT Name -->
                            <td>
                                <strong>{{ $olt->olt_name }}</strong>
                            </td>
 
                            <!-- IP Address -->
                            <td>
                                <code class="text-info">{{ $olt->olt_ip }}</code>
                            </td>
 
                            <!-- Community -->
                            <td>
                                <small class="text-muted">{{ $olt->olt_community }}</small>
                            </td>
 
                            <!-- SSH User -->
                            <td>
                                <small>{{ $olt->useradmin ?? 'N/A' }}</small>
                            </td>
 
                            <!-- Type (epon/gpon) -->
                            <td>
                                <span class="badge bg-info">{{ strtoupper($olt->type) }}</span>
                            </td>
 
                            <!-- Brand -->
                            <td>
                                <span class="badge bg-secondary">{{ $olt->olt_brand }}</span>
                            </td>
 
                            <!-- Connection Type -->
                            <td>
                                <span class="badge {{ $olt->typeconnection === 'ssh' ? 'bg-success' : 'bg-warning' }}">
                                    {{ strtoupper($olt->typeconnection ?? 'N/A') }}
                                </span>
                            </td>
 
                            <!-- Port Count -->
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $olt->port ?? 0 }}</span>
                            </td>
 
                            <!-- TX/RX Support -->
                            <td class="text-center">
                                @if($olt->txrxcmd)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Yes
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times"></i> No
                                    </span>
                                @endif
                            </td>
 
                            <!-- Status -->
                            <td>
                              <span class="badge badge-sm {{ $currentSts['class'] }}">
    <i class="fas fa-{{ $currentSts['icon'] }} me-1"></i>
    {{ ucfirst($olt->sts ?? 'Unknown') }}
</span>
                            </td>
 
                            <!-- Created At -->
                            <td>
                                <small class="text-muted">
                                    {{ $olt->created_at ? $olt->created_at->format('M d, Y') : 'N/A' }}
                                </small>
                            </td>
 
                            <td>
                                <div class="action-group">
                                <!-- Edit Button -->
                                    <button type="button"
                                            class="btn btn-sm btn-primary btn-edit-olt"
                                            data-id="{{ $olt->id }}"
                                            data-olt_name="{{ $olt->olt_name }}"
                                            data-olt_ip="{{ $olt->olt_ip }}"
                                            data-olt_community="{{ $olt->olt_community }}"
                                            data-useradmin="{{ $olt->useradmin }}"
                                            data-pass="{{ $olt->pass }}"
                                            data-type="{{ $olt->type }}"
                                            data-olt_brand="{{ $olt->olt_brand }}"
                                            data-typeconnection="{{ $olt->typeconnection }}"
                                            data-port="{{ $olt->port }}"
                                            data-txrxcmd="{{ $olt->txrxcmd }}"
                                            data-sts="{{ $olt->sts }}"
                                            title="Edit OLT"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editOltModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    @if($olt->sts === 'active')
                                    <button type="button" 
                                            class="btn btn-action btn-danger btn-toggle-status" 
                                            data-id="{{ $olt->id }}" 
                                            data-action="inactive"
                                            title="Deactivate this OLT">
                                        <i class="fa fa-ban"></i> Inactive
                                    </button>
                                    @else
                                    <button type="button" 
                                            class="btn btn-action btn-success btn-toggle-status" 
                                            data-id="{{ $olt->id }}" 
                                            data-action="active"
                                            title="Activate this OLT">
                                        <i class="fa fa-check"></i> Activate
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- SCRIPTS (No functionality changes - just styling) -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->



<!-- Edit Modal -->
               
<div class="modal fade" id="editOltModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit OLT Configuration
                </h5>
                 <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
 
            <form id="editOltForm">
                @csrf
                @method('PUT')
 
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
 
                    <!-- Basic Info Section -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted mb-3">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </h6>
 
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_olt_name" class="form-label">OLT Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="olt_name" id="edit_olt_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_olt_ip" class="form-label">IP Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="olt_ip" id="edit_olt_ip" required>
                            </div>
                        </div>
 
                        <div class="mb-3">
                            <label for="edit_olt_community" class="form-label">SNMP Community <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="olt_community" id="edit_olt_community" required>
                        </div>
                    </div>
 
                    <hr>
 
                    <!-- Connection Settings Section -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted mb-3">
                            <i class="fas fa-plug"></i> Connection Settings
                        </h6>
 
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_typeconnection" class="form-label">Connection Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="typeconnection" id="edit_typeconnection" required>
                                    <option value="">-- Select --</option>
                                    <option value="ssh">SSH</option>
                                    <option value="telnet">Telnet</option>
                                    <option value="snmp">SNMP Only</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_useradmin" class="form-label">SSH/Telnet Username</label>
                                <input type="text" class="form-control" name="useradmin" id="edit_useradmin">
                            </div>
                        </div>
 
                        <div class="mb-3">
                            <label for="edit_pass" class="form-label">SSH/Telnet Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="pass" id="edit_pass">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
 
                    <hr>
 
                    <!-- OLT Configuration Section -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted mb-3">
                            <i class="fas fa-cog"></i> OLT Configuration
                        </h6>
 
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_type" class="form-label">OLT Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="type" id="edit_type" required>
                                    <option value="">-- Select --</option>
                                    <option value="epon">EPON</option>
                                    <option value="gpon">GPON</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_olt_brand" class="form-label">OLT Brand <span class="text-danger">*</span></label>
                                <select class="form-control" name="olt_brand" id="edit_olt_brand" required>
                                    <option value="">-- Select --</option>
                                    <option value="BDCOM">BDCOM</option>
                                    <option value="vsol">VSOL</option>
                                    <option value="cdata">C-DATA</option>
                                    <option value="Huawei">Huawei</option>
                                    <option value="ZTE">ZTE</option>
                                    <option value="FiberHome">FiberHome</option>
                                    <option value="Raisecom">Raisecom</option>
                                </select>
                            </div>
                        </div>
 
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_port" class="form-label">Port Count</label>
                                <input type="number" class="form-control" name="port" id="edit_port" min="1" value="4">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_txrxcmd" class="form-label">TX/RX Support</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" name="txrxcmd" id="edit_txrxcmd" value="1">
                                    <label class="form-check-label" for="edit_txrxcmd">
                                        Enable TX/RX Commands
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_sts" class="form-label">Status</label>
                                <select class="form-control" name="sts" id="edit_sts">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
    </div>

</div>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Bootstrap 4 CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">

<!-- DataTables core CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<!-- SweetAlert2 CSS (optional, mostly bundled in JS, but safe to include) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- ENHANCED JAVASCRIPT - Better Feedback & Form Handling -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    // ════════════════════════════════════════════════════════════════════════════
    // SUBMIT OLT FORM
    // ════════════════════════════════════════════════════════════════════════════
    
    $('#submitBtn').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var form = $('#myForm');
        
        // Validate form
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        // Clear previous result
        $('#formResult').html('');
        
        // Show loading state
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Saving Data...');

        // Get form data
        var formData = form.serialize();
        
        console.log('Submitting OLT form...', formData);

        $.ajax({
            url: "{{ route('olt.store') }}",
            type: 'POST',
            data: formData,
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            
            success: function(res) {
                console.log('Success Response:', res);
                
                $btn.prop('disabled', false);
                $btn.html('<i class="fas fa-paper-plane"></i> Submit Configuration');

                if (res.success) {
                    // ✅ SUCCESS MESSAGE
                    showSuccessMessage(res.message || 'OLT Configuration added successfully!');
                    
                    // Clear form fields
                    form[0].reset();
                    console.log('Form cleared');
                    
                    // Reload table after 2 seconds
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                    
                } else {
                    // ❌ ERROR MESSAGE (from server)
                    showErrorMessage(res.message || 'Failed to add OLT configuration');
                }
            },
            
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                    response: xhr.responseJSON,
                    text: xhr.responseText
                });
                
                $btn.prop('disabled', false);
                $btn.html('<i class="fas fa-paper-plane"></i> Submit Configuration');

                var errMsg = 'An error occurred during submission.';
                
                // Handle different error types
                if (xhr.status === 422) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        errMsg = 'Validation Error: ';
                        $.each(errors, function(key, value) {
                            errMsg += value[0] + ' ';
                        });
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                } else if (status === 'timeout') {
                    errMsg = 'Request timeout. Please check your connection.';
                } else if (status === 'error') {
                    errMsg = 'Network error. Please try again.';
                }
                
                showErrorMessage(errMsg);
            }
        });
    });
    
    // ════════════════════════════════════════════════════════════════════════════
    // SUCCESS MESSAGE FUNCTION
    // ════════════════════════════════════════════════════════════════════════════
    
    function showSuccessMessage(message) {
        // Show SweetAlert notification
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
        
        // Also show in form result div
        var resultDiv = $('#formResult');
        resultDiv.html(`
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        resultDiv.show();
        
        console.log('✅ ' + message);
    }
    
    // ════════════════════════════════════════════════════════════════════════════
    // ERROR MESSAGE FUNCTION
    // ════════════════════════════════════════════════════════════════════════════
    
    function showErrorMessage(message) {
        // Show SweetAlert notification
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: true,
            position: 'top-end',
            toast: true
        });
        
        // Also show in form result div
        var resultDiv = $('#formResult');
        resultDiv.html(`
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        resultDiv.show();
        
        console.error('❌ ' + message);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // INITIALIZE DATATABLE
    // ════════════════════════════════════════════════════════════════════════════
    
    var oltTable = $('#oltTable').DataTable({
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 } // Action column not sortable
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    // ════════════════════════════════════════════════════════════════════════════
    // EDIT OLT - POPULATE MODAL
    // ════════════════════════════════════════════════════════════════════════════
    
$('#oltTable').on('click', '.btn-edit-olt', function () {
    var btn = $(this);
    
    // Primary Key
    $('#edit_id').val(btn.data('id'));
    
    // Basic Info
    $('#edit_olt_name').val(btn.data('olt_name'));
    $('#edit_olt_ip').val(btn.data('olt_ip'));
    $('#edit_olt_community').val(btn.data('olt_community'));
    
    // Connection Settings
    $('#edit_typeconnection').val(btn.data('typeconnection')); // Target the connection type select
    $('#edit_useradmin').val(btn.data('useradmin'));           // Matched with HTML id="edit_useradmin"
    $('#edit_pass').val(btn.data('pass'));                     // Matched with HTML id="edit_pass"
    
    // OLT Configuration
    $('#edit_type').val(btn.data('type'));                     // Matched with HTML id="edit_type"
    $('#edit_olt_brand').val(btn.data('olt_brand'));
    $('#edit_port').val(btn.data('port') || 4);
    $('#edit_sts').val(btn.data('sts') || 'active');
    
    // Checkbox Handling
    $('#edit_txrxcmd').prop('checked', btn.data('txrxcmd') == 1);

    $('#editOltModal').modal('show');
});

    // ════════════════════════════════════════════════════════════════════════════
    // SUBMIT EDIT FORM
    // ════════════════════════════════════════════════════════════════════════════
$('#editOltForm').on('submit', function (e) {
    e.preventDefault();
    
    var id = $('#edit_id').val();
    var formData = $(this).serialize();

    $.ajax({
        url: `{{ route('olt.update', ':id') }}`.replace(':id', id),
        type: 'POST', // Send via POST; Laravel method spoofing converts this to PUT
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            $('#editOltModal').modal('hide');
            
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: res.message || 'OLT updated successfully.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        },
        error: function (xhr) {
            console.error('Update Error:', xhr.responseJSON);
            
            var msg = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'Something went wrong while updating OLT.';
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg,
                showConfirmButton: true
            });
        }
    });
});

    // ════════════════════════════════════════════════════════════════════════════
    // TOGGLE OLT STATUS (ACTIVE/INACTIVE)
    // ════════════════════════════════════════════════════════════════════════════
    
    $('#oltTable').on('click', '.btn-toggle-status', function () {
        var btn = $(this);
        var id = btn.data('id');
        var action = btn.data('action'); // 'active' or 'inactive'
        var currentStatus = action === 'active' ? 'Active' : 'Inactive';
        
        console.log('Toggle status - ID:', id, 'Action:', action);
        
        var confirmText = action === 'inactive'
            ? 'This OLT will be marked as Inactive and will stop monitoring.'
            : 'This OLT will be marked as Active and will resume monitoring.';

        Swal.fire({
            title: 'Confirm Status Change',
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'inactive' ? '#dc3545' : '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, ' + action,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Confirmed - Updating status to:', action);
                
                // Disable button during request
                btn.prop('disabled', true);
                
                var url = "{{ route('olt.status', ':id') }}".replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'PATCH',
                        sts: action
                    },
                    success: function (res) {
                        console.log('Status update success:', res);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Done!',
                            text: res.message || 'Status updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        console.error('Status update error:', xhr.responseJSON);
                        
                        btn.prop('disabled', false);
                        
                        var errorMsg = xhr.responseJSON?.message || 'Could not update status.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error (' + xhr.status + ')',
                            text: errorMsg,
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });

});
</script>

<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- CSS STYLES FOR MESSAGES -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->

<style>
/* Alert Styling */
.alert {
    border-radius: 8px;
    border: none;
    padding: 16px 20px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideInDown 0.3s ease;
}

.alert-success {
    background: rgba(25, 135, 84, 0.12);
    color: var(--bs-success, #198754);
    border-left: 4px solid var(--bs-success, #198754);
}

.alert-danger {
    background: rgba(220, 53, 69, 0.12);
    color: var(--bs-danger, #dc3545);
    border-left: 4px solid var(--bs-danger, #dc3545);
}

.alert i {
    font-size: 16px;
}

.btn-close {
    opacity: 0.7;
    padding: 4px;
}

.btn-close:hover {
    opacity: 1;
}

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

/* SweetAlert Customization */
.swal2-popup {
    border-radius: 12px;
}

.swal2-title {
    font-size: 18px;
    font-weight: 700;
}

.swal2-html-container {
    font-size: 14px;
    margin: 10px 0;
}

/* Disabled button state */
button:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

/* Form submission feedback */
#submitBtn {
    transition: all 0.3s ease;
}

#submitBtn:disabled {
    pointer-events: none;
}

#submitBtn i {
    margin-right: 6px;
}
</style>
<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Popper.js (required for Bootstrap 4 modals, dropdowns, tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<!-- Bootstrap 4 JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>

<!-- DataTables core + Bootstrap 4 integration -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Your custom JS (the DataTable init / modal / AJAX code) -->
<script src="{{ asset('js/olt-table.js') }}"></script>
</x-app-layout>