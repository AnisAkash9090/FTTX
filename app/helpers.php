<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
if (!function_exists('userlistsfn')) {
    function userlistsfn($sess_att) {
        $employee = DB::table('userlists')
                      ->where('attendece_id', $sess_att)
                      ->first();

        if ($employee) {
            return [
                'image'   => $employee->img,
                'name'    => $employee->name,
                'email'   => $employee->email,
                'address' => $employee->address,
                'status'  => $employee->sts,
            ];
        }

        return ['name' => 'N/F'];
    }
};

function checkPermission($permissions) {
    // This variable stays in memory for the duration of the page load
    static $userPermissions = null;

    $user = Auth::user();
    if (!$user) return false;

    // Only query the database ONCE per page load
    if ($userPermissions === null) {
        $permissionRow =DB::table('permission')
            ->where('attendence_id', $user->id)
            ->first();

        if (!$permissionRow || empty($permissionRow->access)) {
            $userPermissions = [];
        } else {
            $accessIds = explode(',', $permissionRow->access);
            $roles = DB::table('rollmanagement')
                ->whereIn('id', $accessIds)
                ->pluck('identity_permission');

            $all = [];
            foreach ($roles as $roleString) {
                foreach (explode(',', $roleString) as $p) {
                    $all[trim($p)] = true;
                }
            }
            $userPermissions = $all;
        }
    }

    // Now checking the permission is instant (no DB query)
    $required = is_array($permissions) ? $permissions : explode(',', $permissions);
    foreach ($required as $req) {
        if (isset($userPermissions[trim($req)])) return true;
    }

    return false;
}

