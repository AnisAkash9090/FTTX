<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
abstract class Controller
{
    //
    public function index()
{
    // The user is already logged in when this function runs
  Auth::user()->attendece_id; 

  // This shares the variable with EVERY page on your site
    View::composer('*', function ($view) {
        if (Auth::check()) {
            $userInfo = DB::table('userlists')
                          ->where('attendece_id', Auth::user()->attendece_id)
                          ->first();
            $view->with('userInfo', $userInfo);
        }
    });
}
}
