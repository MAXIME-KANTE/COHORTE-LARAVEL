<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Mise à jour du fillable pour inclure les champs du projet
    protected $fillable = [
        'name',
        'email',
        'password',
        'promotion_id',
        'role',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //----------------------------------------------------- Relations

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class);
    }

    public function appelsIa(): HasMany
    {
        return $this->hasMany(AppelIa::class);
    }

    //----------------------------------------------------- Méthodes métier

    public function estEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    public function estDelegue(): bool
    {
        return $this->role === 'delegue';
    }

    //----------------------------------------------------- Quota IA

    public function appelsIaAujourdhui(): int
    {
        return $this->appelsIa()
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    public function quotaIaRestant(): int
    {
        return max(0, config('cohorte.quota_ia_quotidien') - $this->appelsIaAujourdhui());
    }

    public function peutAppelerIa(): bool
    {
        return $this->quotaIaRestant() > 0;
    }
}
