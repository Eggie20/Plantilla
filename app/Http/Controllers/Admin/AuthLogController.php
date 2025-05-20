<?php

// app/Http/Controllers/Admin/AuthLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthLog;

class AuthLogController extends Controller
{
    public function index()
    {
        $logs = AuthLog::latest()->paginate(50); // or however you're fetching logs
        return view('admin.authlogs.index', compact('logs'));
    }
}


