<?php

namespace App\Support;

class MensagemCobranca
{
    public static function gerar(string $tipo, float $valor, string $nomeCliente = ''): string
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

        return "{$cabecalho}\n{$linha}\n\nAguardo seu retorno.";
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
