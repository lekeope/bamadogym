<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        return view('home', compact('plans'));
    }
}
