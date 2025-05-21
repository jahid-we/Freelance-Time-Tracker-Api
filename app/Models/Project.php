<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'description',
        'status',
        'deadline',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
