<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Membership;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $todayCheckIns = CheckIn::with('user')
            ->whereDate('checked_in_at', today())
            ->latest('checked_in_at')
            ->get();

        $overdueCount = Membership::whereIn('status', ['overdue', 'expired'])->count();
        $dueCount = Membership::where('status', 'due')->count();
        $activeCount = Membership::where('status', 'active')->count();
        $totalMembers = User::where('role', 'member')->count();

        return view('admin.dashboard', compact(
            'todayCheckIns',
            'overdueCount',
            'dueCount',
            'activeCount',
            'totalMembers'
        ));
    }
}
