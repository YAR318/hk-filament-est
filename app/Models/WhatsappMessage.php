<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'source',
        'message_id',
        'from_number',
        'to_number',
        'message_body',
        'message_type',
        'instance_name',
        'raw_data',
        'received_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'received_at' => 'datetime',
    ];
}
