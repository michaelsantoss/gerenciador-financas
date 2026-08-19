<?php

namespace App\Support;

class MensagemCobranca
{
    public static function gerar(string $tipo, float $valor, string $nomeCliente = '', $parcelas = null): string
    {
        $saudacao = self::saudacao();
        $primeiroNome = trim(explode(' ', trim($nomeCliente))[0] ?? '');
        $cabecalho = $primeiroNome !== '' ? "{$saudacao} {$primeiroNome}!" : "{$saudacao}!";
        $valorFormatado = number_format($valor, 2, ',', '.');

        $linha = match ($tipo) {
            'atrasado' => "Temos um pagamento pendente em atraso: R$ {$valorFormatado}",
            'geral' => "Temos um saldo em aberto: R$ {$valorFormatado}",
            default => "Temos um pagamento pendente vencendo hoje: R$ {$valorFormatado}",
        };

        $corpo = "{$cabecalho}\n{$linha}";

        if ($parcelas !== null && count($parcelas) > 0) {
            $corpo .= "\n\n" . self::resumoParcelas($parcelas);
        }

        return "{$corpo}\n\nAguardo seu retorno.";
    }

    private static function resumoParcelas($parcelas): string
    {
        $ordenadas = collect($parcelas)->sortBy('data_vencimento');

        $rotulos = [
            'pago' => 'Pago',
            'parcial' => 'Parcial',
            'atrasado' => 'Atrasada',
            'pendente' => 'Pendente',
        ];

        return $ordenadas->map(function ($parcela) use ($rotulos) {
            $data = $parcela->data_vencimento->format('d/m');
            $valor = number_format($parcela->valor, 2, ',', '.');
            $status = $rotulos[$parcela->status] ?? ucfirst($parcela->status);
            $linha = "{$data} - R$ {$valor} - {$status}";

            return $parcela->status === 'atrasado' ? "*{$linha}*" : $linha;
        })->implode("\n");
    }

    private static function saudacao(): string
    {
        $hora = now()->hour;

        if ($hora < 12) {
            return 'Bom dia';
        }

        return $hora < 18 ? 'Boa tarde' : 'Boa noite';
    }
}
