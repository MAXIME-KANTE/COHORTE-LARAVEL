<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code_invitation', 'annee', 'ouverte'];

    protected function casts(): array
    {
        return [
            'ouverte' => 'boolean',
            'annee' => 'integer',
        ];
    }

    public function membres(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }
}
