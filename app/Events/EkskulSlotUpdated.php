<?php

namespace App\Events;

use App\Models\Ekstrakurikuler;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EkskulSlotUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $ekskulId;
    public int $availableSlots;
    public bool $isSlotFull;

    /**
     * Create a new event instance.
     */
    public function __construct(Ekstrakurikuler $ekskul)
    {
        $this->ekskulId = $ekskul->id;
        $this->availableSlots = $ekskul->available_slots;
        $this->isSlotFull = $ekskul->is_slot_full;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('ekskul'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'slot.updated';
    }
}
