<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\ClienteArquivo;
use App\Models\Emprestimo;
use App\Models\Empresa;
use App\Models\Endereco;
use App\Models\Pagamento;
use App\Models\Parcela;
use ZipArchive;

class BackupExportService
{
    /**
     * Gera um .zip com um CSV por tabela e retorna o caminho do arquivo.
     * Cada tabela é lida em chunks e escrita direto em disco, para não
     * carregar todos os registros da empresa em memória de uma vez.
     */
    public function exportar(Empresa $empresa): string
    {
        $pasta = storage_path('app/tmp/backup_' . $empresa->id . '_' . time());
        mkdir($pasta, 0755, true);

        $this->exportarTabela($pasta, 'clientes.csv', Cliente::withTrashed()->where('empresa_id', $empresa->id), [
            'id', 'nome', 'apelido', 'telefone', 'cpf', 'rg', 'email',
            'quem_indicou', 'observacao', 'created_at', 'updated_at', 'deleted_at',
        ]);

        $this->exportarTabela($pasta, 'enderecos.csv', Endereco::where('empresa_id', $empresa->id), [
            'id', 'cliente_id', 'cep', 'logradouro', 'numero', 'complemento',
            'bairro', 'cidade', 'estado', 'created_at', 'updated_at',
        ]);

        $this->exportarTabela($pasta, 'emprestimos.csv', Emprestimo::where('empresa_id', $empresa->id), [
            'id', 'cliente_id', 'valor', 'taxa_juros', 'valor_total',
            'frequencia_pagamento', 'data_vencimento', 'status', 'created_at', 'updated_at',
        ]);

        $this->exportarTabela($pasta, 'parcelas.csv', Parcela::where('empresa_id', $empresa->id), [
            'id', 'emprestimo_id', 'valor', 'valor_pago', 'data_vencimento',
            'data_pagamento', 'status', 'created_at', 'updated_at',
        ]);

        $this->exportarTabela($pasta, 'pagamentos.csv', Pagamento::where('empresa_id', $empresa->id), [
            'id', 'emprestimo_id', 'parcela_id', 'valor_pago', 'data_pagamento',
            'observacoes', 'created_at', 'updated_at',
        ]);

        $this->exportarTabela($pasta, 'cliente_arquivos.csv', ClienteArquivo::where('empresa_id', $empresa->id), [
            'id', 'cliente_id', 'tipo', 'nome_original', 'caminho', 'mime', 'tamanho', 'created_at',
        ]);

        $zipPath = $pasta . '.zip';
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (glob($pasta . '/*.csv') as $arquivoCsv) {
            $zip->addFile($arquivoCsv, basename($arquivoCsv));
        }

        $zip->close();

        foreach (glob($pasta . '/*.csv') as $arquivoCsv) {
            unlink($arquivoCsv);
        }
        rmdir($pasta);

        return $zipPath;
    }

    private function exportarTabela(string $pasta, string $arquivo, $query, array $colunas): void
    {
        $handle = fopen($pasta . '/' . $arquivo, 'w');
        fputcsv($handle, $colunas);

        $query->orderBy('id')->chunk(500, function ($registros) use ($handle, $colunas) {
            foreach ($registros as $registro) {
                fputcsv($handle, array_map(
                    fn ($coluna) => $registro->{$coluna},
                    $colunas
                ));
            }
        });

        fclose($handle);
    }
}
