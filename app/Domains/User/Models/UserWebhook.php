<?php

namespace App\Domains\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWebhook extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'url',
        'headers',
        'secret'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
