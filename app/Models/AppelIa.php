<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppelIa extends Model
{
    use HasFactory;

    // La table s'appelle appel_ias (et non appels_ias, que Laravel devinerait)
    protected $table = 'appel_ias';

    protected $fillable = ['user_id', 'contexte', 'modele', 'reussi', 'duree_ms'];

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
