<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $logs = SystemLog::query()
            ->where('usuario_id', $request->user()->id)
            ->orderByDesc('id')
            ->limit(200)
            ->get()
            ->map(fn (SystemLog $log) => [
                'id' => $log->id,
                'acao' => $log->acao,
                'detalhe' => $log->detalhe,
                'usuarioId' => $log->usuario_id,
                'timestamp' => $log->registrado_em?->toIso8601String(),
            ]);

        return response()->json(['data' => $logs]);
    }
}
