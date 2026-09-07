<?php
namespace App\Http\Controllers;

use App\Models\Userlist;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller

{


public function index()
{
    // Join Userlist with User on matching attendece_id to pull User.id as user_id
    $users = Userlist::leftJoin('users', 'userlists.attendece_id', '=', 'users.attendece_id')
        ->select(
            'userlists.*',
            'users.id as user_id' // Primary key from users table needed for role assignment
        )
        ->get();

    // Auto-generate next Attendance ID
    $maxId = Userlist::max('attendece_id');
    $nextAttendanceId = $maxId ? ((int)$maxId + 1) : 1001;

    return view('userlist', compact('users', 'nextAttendanceId'));
}

public function store(Request $request)
{
    $sess_att = Auth::user()->attendece_id ?? Auth::id();

    // 1. Validation Rules
    $request->validate([
        'username'     => 'required|string|max:255',
        'address'      => 'required|string',
        'company'      => 'required',
        'attendece_id' => 'required|unique:userlists,attendece_id',
        'user_id'      => 'required|email|unique:userlists,email|unique:users,email', 
        'pass'         => 'required|min:6',
        'img'          => 'nullable|url', // Validates that the input is a valid URL link
    ]);

    // 2. Save to Userlist Table
    $userlist = new Userlist();
    $userlist->name         = $request->username;
    $userlist->address      = $request->address;
    $userlist->email        = $request->user_id;
    $userlist->password     = $request->pass;
    $userlist->attendece_id = $request->attendece_id;
    $userlist->createinfo   = $sess_att;
    $userlist->img          = $request->img; // Save the image link string directly
    $userlist->save();

    // 3. Save to User Table
    User::create([
        'name'         => $request->username,
        'email'        => $request->user_id,
        'password'     => Hash::make($request->pass),
        'attendece_id' => $request->attendece_id,
    ]);

    return redirect()->back()->with('success', 'User created successfully!');
}
}