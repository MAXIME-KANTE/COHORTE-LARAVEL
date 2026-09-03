<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = ['publication_id', 'user_id', 'motif'];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }
}
