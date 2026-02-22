<?php

namespace App\Models;

use Kopaing\SimpleLog\Models\ActivityLog as KPActivityLog;

class ActivityLog extends KPActivityLog {

    public function createdUser(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
