<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;

class AtividadeController extends Controller
{
    public function index()
    {
        $logs = AdminActivityLog::with(['user', 'empresa'])
            ->latest()
            ->paginate(30);

        return view('admin.atividades.index', compact('logs'));
    }
}
