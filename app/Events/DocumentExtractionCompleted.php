<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentExtractionCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Document $document) {}

    public function broadcastOn(): Channel
    {
        return new Channel('document.' . $this->document->id);
    }

    public function broadcastAs(): string
    {
        return 'extraction.done';
    }

    public function broadcastWith(): array
    {
        return [
            'id'        => $this->document->id,
            'ai_status' => $this->document->ai_status,
            'name'      => $this->document->original_name,
        ];
    }
}
