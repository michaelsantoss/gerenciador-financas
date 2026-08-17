<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteArquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClienteArquivoController extends Controller
{
    private const LIMITE_BYTES = 5 * 1024 * 1024; // 5MB
    private const LIMITE_POR_TIPO = 2;
    private const EXTENSOES = [
        'foto' => ['jpg', 'jpeg', 'png', 'webp'],
        'anexo' => ['pdf', 'jpg', 'jpeg', 'png'],
    ];

    public function store(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'tipo' => 'required|in:foto,anexo',
            'arquivo' => 'required|file|max:10240',
        ]);

        $tipo = $dados['tipo'];
        $arquivo = $request->file('arquivo');
        $extensao = strtolower($arquivo->getClientOriginalExtension());

        if (!in_array($extensao, self::EXTENSOES[$tipo], true)) {
            return back()->withErrors('Tipo de arquivo não permitido para ' . ($tipo === 'foto' ? 'fotos' : 'anexos') . '.');
        }

        if ($cliente->arquivos()->where('tipo', $tipo)->count() >= self::LIMITE_POR_TIPO) {
            return back()->withErrors('Limite de ' . self::LIMITE_POR_TIPO . ' ' . ($tipo === 'foto' ? 'fotos' : 'anexos') . ' por cliente atingido. Exclua um antes de adicionar outro.');
        }

        $conteudo = null;
        $mime = $arquivo->getMimeType();
        $tamanho = $arquivo->getSize();
        $ehImagem = in_array($extensao, ['jpg', 'jpeg', 'png', 'webp'], true);

        if ($tamanho > self::LIMITE_BYTES) {
            if (!$ehImagem) {
                return back()->withErrors('Arquivo muito grande (máx. 5MB). PDFs não podem ser redimensionados automaticamente.');
            }

            $reduzido = $this->redimensionarImagem($arquivo->getRealPath(), $extensao);

            if (!$reduzido) {
                return back()->withErrors('Não foi possível reduzir a imagem para caber no limite de 5MB.');
            }

            [$conteudo, $mime] = $reduzido;
            $tamanho = strlen($conteudo);
            $extensao = 'jpg';
        }

        $pasta = "{$cliente->empresa_id}/{$cliente->id}/" . ($tipo === 'foto' ? 'fotos' : 'anexos');
        $nomeArquivo = (string) Str::uuid() . '.' . $extensao;
        $caminho = "{$pasta}/{$nomeArquivo}";

        if ($conteudo !== null) {
            Storage::disk('local')->put($caminho, $conteudo);
        } else {
            Storage::disk('local')->putFileAs($pasta, $arquivo, $nomeArquivo);
        }

        ClienteArquivo::create([
            'cliente_id' => $cliente->id,
            'tipo' => $tipo,
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho' => $caminho,
            'mime' => $mime,
            'tamanho' => $tamanho,
        ]);

        return back()->with('success', ($tipo === 'foto' ? 'Foto adicionada' : 'Anexo adicionado') . ' com sucesso!');
    }

    public function show(Cliente $cliente, ClienteArquivo $arquivo)
    {
        abort_if($arquivo->cliente_id !== $cliente->id, 404);

        return response()->file(Storage::disk('local')->path($arquivo->caminho));
    }

    public function destroy(Cliente $cliente, ClienteArquivo $arquivo)
    {
        abort_if($arquivo->cliente_id !== $cliente->id, 404);

        $arquivo->delete();

        return back()->with('success', 'Arquivo removido com sucesso!');
    }

    private function redimensionarImagem(string $caminhoTemp, string $extensao): ?array
    {
        // Cap de segurança: servidor de produção tem só 512MB de RAM.
        // Recusa decodificar imagens absurdamente grandes em vez de arriscar
        // derrubar o processo (GD precisa de ~4 bytes por pixel só pro buffer).
        $info = @getimagesize($caminhoTemp);
        if (!$info || ($info[0] * $info[1]) > 24_000_000) {
            return null;
        }

        $limiteMemoriaAnterior = ini_get('memory_limit');
        ini_set('memory_limit', '256M');

        try {
            $origem = match ($extensao) {
                'png' => @imagecreatefrompng($caminhoTemp),
                'webp' => @imagecreatefromwebp($caminhoTemp),
                default => @imagecreatefromjpeg($caminhoTemp),
            };

            if (!$origem) {
                return null;
            }

            $larguraOriginal = imagesx($origem);
            $alturaOriginal = imagesy($origem);
            $maxLado = 1920;
            $qualidade = 85;

            for ($tentativa = 0; $tentativa < 5; $tentativa++) {
                $escala = min(1, $maxLado / max($larguraOriginal, $alturaOriginal));
                $novaLargura = max(1, (int) round($larguraOriginal * $escala));
                $novaAltura = max(1, (int) round($alturaOriginal * $escala));

                $destino = imagecreatetruecolor($novaLargura, $novaAltura);
                imagecopyresampled($destino, $origem, 0, 0, 0, 0, $novaLargura, $novaAltura, $larguraOriginal, $alturaOriginal);

                ob_start();
                imagejpeg($destino, null, $qualidade);
                $conteudo = ob_get_clean();
                imagedestroy($destino);

                if (strlen($conteudo) <= self::LIMITE_BYTES) {
                    imagedestroy($origem);
                    return [$conteudo, 'image/jpeg'];
                }

                $maxLado = (int) ($maxLado * 0.8);
                $qualidade = max(50, $qualidade - 10);
            }

            imagedestroy($origem);
            return null;
        } finally {
            ini_set('memory_limit', $limiteMemoriaAnterior);
        }
    }
}
