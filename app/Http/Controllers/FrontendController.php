<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Show the main application interface
     */
    public function index()
    {
        return view('frontend.app');
    }

    /**
     * Show the login page
     */
    public function login()
    {
        return view('frontend.login');
    }

    /**
     * Show the dashboard
     */
    public function dashboard()
    {
        return view('frontend.dashboard');
    }

    /**
     * Show the users management page
     */
    public function users()
    {
        return view('frontend.users');
    }

    /**
     * Show the create user page
     */
    public function createUser()
    {
        return view('frontend.create-user');
    }

    /**
     * Show the bulk create users page
     */
    public function bulkCreate()
    {
        return view('frontend.bulk-create');
    }
}
