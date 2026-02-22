<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'Log Name' => $this->log_name,
            'Description' => $this->description,
            'Event' => $this->event,
            'Status' => $this->status,
            'Created By' => $this->createdUser->name ?? 'unknown',
            'Created At' => $this->created_at->format('H:i:s d-M-Y')
        ];
    }
}
