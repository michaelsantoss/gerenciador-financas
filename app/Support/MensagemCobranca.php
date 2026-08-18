<?php

namespace App\Support;

class MensagemCobranca
{
    public static function gerar(string $tipo, float $valor): string
    {
        $saudacao = self::saudacao();
        $valorFormatado = number_format($valor, 2, ',', '.');

        return match ($tipo) {
            'atrasado' => "{$saudacao}! Temos um pagamento pendente em atraso:\nR$ {$valorFormatado}\n\nPoderia me avisar quando for possível regularizar?",
            'geral' => "{$saudacao}! Passando para falar sobre seu empréstimo em aberto.\nSaldo atual: R$ {$valorFormatado}",
            default => "{$saudacao}! Temos um pagamento pendente vencendo hoje:\nR$ {$valorFormatado}",
        };
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
