<?php

namespace App\Services;

use App\Models\SystemLog;

class AuditLogger
{
    public static function record(string $acao, ?string $detalhe = null, ?int $usuarioId = null): void
    {
        SystemLog::query()->create([
            'acao' => $acao,
            'detalhe' => $detalhe,
            'usuario_id' => $usuarioId,
        ]);
    }
}
