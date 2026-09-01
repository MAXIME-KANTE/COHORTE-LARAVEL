<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppelIa extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'contexte', 'modele', 'reussi'];

    protected function casts(): array
    {
        return [
            'reussi' => 'boolean',
        ];
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
