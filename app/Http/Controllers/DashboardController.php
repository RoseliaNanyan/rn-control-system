<?php

namespace App\Http\Controllers;

use App\Models\Log;

class DashboardController extends Controller
{
    public function index()
{
    $logs = Log::with('user')
        ->latest()
        ->limit(10)
        ->get();

   return view('dashboard', compact('logs'));

}
}