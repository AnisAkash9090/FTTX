<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use app\Models\User;


use Illuminate\Support\Facades\Gate;
class AppServiceProvider extends ServiceProvider
{
 public function boot(): void
 { Gate::define('has-permission', function (User $user, $permissionName) {
 // Add the \ before the function name
 return \checkPermission($permissionName);});

    View::composer('*', function ($view) {
        // Only run this if a user is logged in
        if (Auth::check()) {
            $userInfo = DB::table('userlists')
                          ->where('attendece_id', Auth::user()->attendece_id)
                          ->first();
            
            // This sends the variable to the view
            $view->with('userInfo', $userInfo);
        }
    });
}
}
