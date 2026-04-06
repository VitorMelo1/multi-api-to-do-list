<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::query()
            ->where('usuario_id', $request->user()->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn (Task $t) => $this->taskPayload($t));

        return response()->json(['data' => $tasks]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:500'],
        ]);

        $task = Task::query()->create([
            'titulo' => $validated['titulo'],
            'concluida' => false,
            'usuario_id' => $request->user()->id,
        ]);

        AuditLogger::record(
            'criacao_tarefa',
            "Tarefa #{$task->id}: {$task->titulo}",
            $request->user()->id
        );

        return response()->json(['data' => $this->taskPayload($task)], 201);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'titulo' => ['sometimes', 'string', 'max:500'],
            'concluida' => ['sometimes', 'boolean'],
        ]);

        $wasIncomplete = ! $task->concluida;

        $task->fill($validated);
        $task->save();

        if ($wasIncomplete && $task->concluida) {
            AuditLogger::record(
                'conclusao_tarefa',
                "Tarefa #{$task->id} marcada como concluída",
                $request->user()->id
            );
        }

        return response()->json(['data' => $this->taskPayload($task->fresh())]);
    }

    public function destroy(Request $request, Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $id = $task->id;
        $task->delete();

        AuditLogger::record(
            'exclusao_tarefa',
            "Tarefa #{$id} removida",
            $request->user()->id
        );

        return response()->json(null, 204);
    }

    private function taskPayload(Task $task): array
    {
        return [
            'id' => $task->id,
            'titulo' => $task->titulo,
            'concluida' => $task->concluida,
            'usuarioId' => $task->usuario_id,
        ];
    }
}
