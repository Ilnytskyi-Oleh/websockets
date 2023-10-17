<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageStatus;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $chat = Chat::where('id', $data['chat_id'])->first();
        $userIds = $chat->users()
            ->where('users.id', '!=', auth()->id())
            ->pluck('users.id')
            ->all();

        try {
            DB::beginTransaction();

            $message = Message::create($data);

            foreach ($userIds as $userId) {
                MessageStatus::create([
                    'message_id' => $message->id,
                    'user_id' => $userId,
                    'chat_id' => $chat->id,
                ]);

                $count = MessageStatus::where('chat_id', $data['chat_id'])
                    ->where('user_id', $userId)
                    ->where('is_read', false)
                    ->count();

                broadcast(new StoreMessageStatusEvent($count, $data['chat_id'], $userId, $message));
            }

            broadcast(new StoreMessageEvent($message))->toOthers();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'Error',
                'message' => $exception->getMessage(),
            ]);
        }

        return MessageResource::make($message)->resolve();
    }
}
