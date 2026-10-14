<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\View\View;

class AdminAuditController extends Controller
{
    public function index(): View
    {
        return view('admin.audit.index', ['logs' => AuditLog::with('user')->latest()->paginate(30)]);
    }
}
