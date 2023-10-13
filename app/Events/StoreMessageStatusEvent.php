<?php

namespace App\Events;

use App\Http\Resources\Message\MessageToOtherResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreMessageStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Message $message;
    private $count;
    private $chatId;
    private $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($count, $chatId, $userId)
    {
        $this->count = $count;
        $this->chatId = $chatId;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('users.' . $this->userId),
        ];
    }

    public function broadcastAs():string {
        return 'store-message-status';
    }

    public function broadcastWith():array {
        return [
            'count' => $this->count,
            'chat_id' =>$this->chatId,
        ];
    }
}
