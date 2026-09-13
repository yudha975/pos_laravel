<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $branchId = auth()->check() ? (auth()->user()->branch_id ?? 1) : 1;
        
        $query = AuditLog::with(['user', 'branch'])->latest();

        // Superadmin bisa melihat semua log, yang lain hanya log cabangnya
        if (!auth()->user()->hasRole('superadmin')) {
            $query->where('branch_id', $branchId);
        }

        $logs = $query->paginate(20);

        return view('pages.audit.index', compact('logs'));
    }
}
