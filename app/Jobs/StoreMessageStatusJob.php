<?php

namespace App\Jobs;

use App\Events\StoreMessageStatusEvent;
use App\Models\Chat;
use App\Models\MessageStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreMessageStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $message;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $message)
    {

        $this->data = $data;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $chat = Chat::where('id', $this->data['chat_id'])->first();

        $userIds = $chat->users()
            ->where('users.id', '!=', auth()->id())
            ->pluck('users.id')
            ->all();
        foreach ($userIds as $userId) {
            MessageStatus::create([
                'message_id' => $this->message->id,
                'user_id' => $userId,
                'chat_id' => $chat->id,
            ]);

            $count = MessageStatus::where('chat_id', $this->data['chat_id'])
                ->where('user_id', $userId)
                ->where('is_read', false)
                ->count();

            broadcast(new StoreMessageStatusEvent($count, $this->data['chat_id'], $userId, $this->message));
        }
    }
}
