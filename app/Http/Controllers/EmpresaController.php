<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\AtividadeLog;
use App\Services\BackupExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Auth::user()->empresa;
        return view('empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
        ]);

        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return back()->withErrors('Empresa não encontrada para este usuário.');
        }

        $empresa->update($dados);

        AtividadeLog::registrar('empresa.editada', null, 'Dados da empresa atualizados');

        return back()->with('success', 'Dados da empresa atualizados!');
    }

    public function backup(BackupExportService $service)
    {
        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return back()->withErrors('Empresa não encontrada para este usuário.');
        }

        $zipPath = $service->exportar($empresa);

        AtividadeLog::registrar('empresa.backup_exportado', null, 'Backup dos dados exportado');

        $nomeArquivo = 'backup_' . now()->format('Y-m-d_His') . '.zip';

        return response()->download($zipPath, $nomeArquivo)->deleteFileAfterSend(true);
    }
}
