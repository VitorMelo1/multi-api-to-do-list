<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['acao', 'detalhe', 'usuario_id', 'registrado_em'])]
class SystemLog extends Model
{
    public $timestamps = false;

    protected $table = 'logs';

    protected function casts(): array
    {
        return [
            'registrado_em' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
