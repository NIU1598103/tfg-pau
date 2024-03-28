<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Month;
use App\Models\Day;
use Inertia\Inertia;

class PreferencesController extends Controller
{
    public function index(){
        $users = User::all();
        $months = Month::all();
        return Inertia::render('Preferences/Index', [
            'users' => $users,
            'months' => $months,
        ]);
    }

    
}
