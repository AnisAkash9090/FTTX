<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OltInformationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OltConnectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OltCliController;
use App\Http\Controllers\ServerHealthController;
// Change this route to redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware(['auth', 'verified'])->group(function () {
    // Standard Dashboard
 
    // Standard Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/olt/metrics', [DashboardController::class, 'getMetrics'])->name('olt.metrics');
    Route::get('/olt/loss-list', [DashboardController::class, 'getLossList'])->name('olt.losslist');
Route::get('/mac-finder/search', [DashboardController::class, 'search'])->name('mac.finder.search');
 /*    // FIX: Keep ONLY this one for the User List
    Route::get('/userlist',[UserController::class,'userindex']); */
    Route::get('/userlist', [UserController::class, 'index'])->name('userlist');
Route::get('/oltView', [OltInformationController::class, 'olt'])->name('oltView');
Route::get('/oltView-sec', [OltInformationController::class, 'olt2'])->name('olt.index');
Route::get('/oltAdd',[OltInformationController::class,'oltadd'])->name('oltAdd');
Route::post('/olt-store', [OltInformationController::class, 'store'])->name('olt.store');
Route::post('/user-store', [UserController::class, 'store'])->name('users.storedata');
Route::post('/olt-search', [OltInformationController::class, 'search'])->name('olt.search.submit');
Route::get('/oltConn', [OltConnectionController::class, 'olt'])->name('oltConn');
Route::match(['get', 'post'], '/olt/run-snmp', [OltConnectionController::class, 'runSnmp'])->name('olt.runSnmp');
Route::post('/olt/live-walk', [OltConnectionController::class, 'liveWalk'])->name('olt.liveWalk');
  // === OLT CLI ROUTES ===
    Route::get('/olt-cli', [OltCliController::class, 'index'])
        ->name('olt-cli.index');
    
    Route::post('/olt-cli/connect', [OltCliController::class, 'connect'])
        ->name('olt-cli.connect');
    
    Route::post('/olt-cli/command', [OltCliController::class, 'command'])
        ->name('olt-cli.command');
    
    Route::post('/olt-cli/disconnect', [OltCliController::class, 'disconnect'])
        ->name('olt-cli.disconnect');
    }); 
    Route::match(['put', 'post'], '/olt/{id}', [OltInformationController::class, 'update'])
        ->name('olt.update');
Route::patch('/olt/{id}/status', [OltInformationController::class, 'toggleStatus'])->name('olt.status');
// Change it to:
Route::match(['get', 'post'], '/olt-search', [OltInformationController::class, 'search'])->name('olt.search.submit');

// AJAX Endpoint for fetching structured unique PON ports
Route::get('/ajax/olt-ports', [OltInformationController::class, 'getUniquePorts'])->name('ajax.olt.ports');
// Add this line for your OLT Devices
/* Route::get('/olts', function () {
    return view('olts.index'); // Make sure this view exists later!
})->middleware(['auth'])->name('olt.index'); */
// --- Role Management Routes ---
    Route::get('/roles', [PermissionController::class, 'indexRoles'])->name('roles.index');
    Route::post('/roles/store', [PermissionController::class, 'storeRole'])->name('roles.store');
    Route::put('/roles/update/{id}', [PermissionController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/delete/{id}', [PermissionController::class, 'deleteRole'])->name('roles.delete');

// View route matching your sidebar link
Route::get('/server-health', [ServerHealthController::class, 'index'])->name('serverHealth');

// AJAX route for the live chart data
Route::get('/server-health/metrics', [ServerHealthController::class, 'getLiveMetrics'])->name('serverHealth.metrics');

    // --- User Permission Assignment Routes ---
    Route::get('/assign-permissions', [PermissionController::class, 'indexAssignPermission'])->name('permissions.assign.index');
    Route::post('/assign-permissions/store', [PermissionController::class, 'storeAssignPermission'])->name('permissions.assign.store');
    
Route::post('/olt/{id}/sync-async', [OltInformationController::class, 'syncOltAsync'])->name('olt.sync.async');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';