<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewEkskulRegistration implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $pendingCount;
    public string $ekskulName;
    public string $studentName;

    /**
     * Create a new event instance.
     */
    public function __construct(int $pendingCount, string $ekskulName, string $studentName)
    {
        $this->pendingCount = $pendingCount;
        $this->ekskulName = $ekskulName;
        $this->studentName = $studentName;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'registration.new';
    }
}
