<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function resumo(Request $request): JsonResponse
    {
        $uid = $request->user()->id;

        $total = Task::query()->where('usuario_id', $uid)->count();
        $concluidas = Task::query()
            ->where('usuario_id', $uid)
            ->where('concluida', true)
            ->count();
        $pendentes = $total - $concluidas;

        return response()->json([
            'usuarioId' => $uid,
            'total' => $total,
            'concluidas' => $concluidas,
            'pendentes' => $pendentes,
        ]);
    }
}
