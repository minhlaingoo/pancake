<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtocolProcess extends Model
{
    protected $fillable = [
        'protocol_id',
        'uid',
        'ended_at'
    ];

    protected $casts = [
        'ended_at'   => 'datetime',
    ];

    public function protocol()
    {
        return $this->belongsTo(Protocol::class);
    }

    public function getData()
    {
        return MqttMessage::query()
            ->whereJsonContains('message->protocol_process_id', $this->uid)
            ->get();
    }
}
