<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function store(StoreRequest $request) {
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
