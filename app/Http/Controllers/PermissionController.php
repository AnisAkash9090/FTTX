<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionTable;
use App\Models\RollManagement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    // ==========================================
    // 1. PERMISSION MASTER LIST (permission_table)
    // ==========================================

    /**
     * List all system permissions.
     */
    public function indexPermissions()
    {
        $permissions = PermissionTable::orderBy('id', 'asc')->get();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Store a new permission in `permission_table`.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'permission' => 'required|string|max:255',
            'url'        => 'required|string|max:255',
            'details'    => 'nullable|string',
        ]);

        PermissionTable::create([
            'permission' => $request->permission,
            'url'        => $request->url,
            'details'    => $request->details,
        ]);

        return redirect()->back()->with('success', 'Master permission created successfully!');
    }

    // ==========================================
    // 2. ROLE MANAGEMENT (rollmanagement)
    // ==========================================

    /**
     * List all roles and fetch all permissions for assignment.
     */
    public function indexRoles()
    {
        $roles = RollManagement::orderBy('id', 'desc')->get();
        $permissions = PermissionTable::orderBy('id', 'asc')->get();

        return view('permissions.roles', compact('roles', 'permissions'));
    }

    /**
     * Create a new role with comma-separated permission IDs (e.g., "1,3,5,6").
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'identity_permission' => 'required|array', // Array of permission_table IDs
        ]);

        RollManagement::create([
            'name'                => $request->name,
            'identity_permission' => implode(',', $request->identity_permission),
            'createby'            => Auth::id() ?? 1,
            'createtime'          => now(),
        ]);

        return redirect()->back()->with('success', 'Role created with mapped permissions!');
    }

    /**
     * Update an existing role's permissions.
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'identity_permission' => 'required|array',
        ]);

        $role = RollManagement::findOrFail($id);
        $role->update([
            'name'                => $request->name,
            'identity_permission' => implode(',', $request->identity_permission),
            'updateby'            => Auth::id() ?? 1,
            'updatetime'          => now(),
        ]);

        return redirect()->back()->with('success', 'Role updated successfully!');
    }

    /**
     * Delete a role.
     */
    public function deleteRole($id)
    {
        $role = RollManagement::findOrFail($id);
        $role->delete();

        return redirect()->back()->with('success', 'Role deleted successfully!');
    }

    // ==========================================
    // 3. USER ROLE ASSIGNMENT (permissions table)
    // ==========================================

    /**
     * Show role assignment page for users.
     */
public function indexAssignPermission(Request $request)
{
    $users = User::all();
    $roles = RollManagement::all();
    
    // Check if an attendence_id is passed via ?id=2
    $selectedUserId = $request->query('id');
    $selectedUser = null;
    $assignedRoleIds = [];

    if ($selectedUserId) {
        $selectedUser = User::where('attendece_id', $selectedUserId)->first();
        
        // Fetch existing access record (comma-separated string e.g. "1,3,4")
        $userPermission = Permission::where('attendence_id', $selectedUserId)->first();
        if ($userPermission && $userPermission->access) {
            $assignedRoleIds = array_map('trim', explode(',', $userPermission->access));
        }
    }

    return view('permissions.assign', compact('users', 'roles', 'selectedUser', 'assignedRoleIds'));
}

    /**
     * Assign role IDs (e.g., "1,2,3") to a user's attendence_id in the access column.
     */
    public function storeAssignPermission(Request $request)
    {
        $request->validate([
            'attendence_id' => 'required|integer',
            'roles'         => 'required|array', // Selected role IDs from rollmanagement
        ]);

        Permission::updateOrCreate(
            ['attendence_id' => $request->attendence_id],
            [
                'access'      => implode(',', $request->roles),
                'update_info' => 'Updated by User ID: ' . (Auth::id() ?? 1),
                'create_by'   => Auth::id() ?? 1,
                'createdate'  => now(),
            ]
        );

        return redirect()->back()->with('success', 'User roles assigned successfully!');
    }
}