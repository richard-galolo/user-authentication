<?php

namespace App\Http\Controllers\Admin;

class HomeController
{
    public function index()
    {
        if (! auth()->user()->is_admin) {
            return view('welcome');
        }

        return view('admin.home');
    }
}
