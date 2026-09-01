<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id', 'user_id', 'type', 'titre', 'contenu',
        'statut', 'motif_moderation', 'epingle_le', 'reponse_retenue_id',
    ];

    protected function casts(): array
    {
        return [
            'epingle_le' => 'datetime',
        ];
    }

    //----------------------------------------------------- Relations

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class);
    }

    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class);
    }

    public function reponseRetenue(): BelongsTo
    {
        return $this->belongsTo(Reponse::class, 'reponse_retenue_id');
    }

    //-------------------------------------------------------- Scopes

    public function scopeDeLaPromotion(Builder $query, int $promotionId): void
    {
        $query->where('promotion_id', $promotionId);
    }

    public function scopeVisibles(Builder $query): void
    {
        $query->where('statut', 'publie');
    }

    public function scopeQuestions(Builder $query): void
    {
        $query->where('type', 'question');
    }

    public function scopePosts(Builder $query): void
    {
        $query->where('type', 'post');
    }
}
