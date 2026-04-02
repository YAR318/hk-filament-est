<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailDigest extends Model
{
    protected $fillable = [
        'user_id',
        'digest_date',
        'emails_count',
        'summary',
        'emails_data',
    ];

    protected $casts = [
        'digest_date' => 'date',
        'emails_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
