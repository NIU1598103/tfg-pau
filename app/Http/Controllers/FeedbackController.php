<?php

namespace App\Http\Controllers;
use Inertia\Inertia;

use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        
        return Inertia::render('Feedback/Index', [
        ]);
    }
}