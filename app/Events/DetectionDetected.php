<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DetectionDetected implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $detection;

    /**
     * Create a new event instance.
     */
    public function __construct(array $detection)
    {
        $this->detection = $detection;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('detections'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DetectionDetected';
    }

    public function broadcastWith(): array
    {
        return $this->detection;
    }
}
