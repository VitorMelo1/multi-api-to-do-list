<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Services\AuditLogger;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $this->allowOwnerOrLog($user, $task, 'visualizar');
    }

    public function update(User $user, Task $task): bool
    {
        return $this->allowOwnerOrLog($user, $task, 'atualizar');
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->allowOwnerOrLog($user, $task, 'excluir');
    }

    private function allowOwnerOrLog(User $user, Task $task, string $verb): bool
    {
        if ($user->id === $task->usuario_id) {
            return true;
        }

        AuditLogger::record(
            'acesso_indevido',
            "Tentativa de {$verb} da tarefa #{$task->id} de outro usuário",
            $user->id
        );

        return false;
    }
}
