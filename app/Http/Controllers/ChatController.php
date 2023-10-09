<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::whereNot('id', auth()->id())->get();
        $users = UserResource::collection($users)->resolve();

        $chats = auth()->user()
            ->chats()
            ->has('messages')
            ->get();
        $chats = ChatResource::collection($chats)->resolve();

        return Inertia::render('Chat/Index',
            compact('users', 'chats')
        );
    }

    /**
     * @throws \Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $userIds = array_merge($data['users'], [auth()->id()]);
        sort($userIds);
        $userIdsString = implode('-', $userIds);
        $chat = null;

        try {
            DB::beginTransaction();

            $chat = Chat::firstOrCreate([
                'users' => $userIdsString,
            ], [
                'title' => $data['title'],
            ]);

            $chat->users()->sync($userIds);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }

        if ($chat) {
            $chat = ChatResource::make($chat)->resolve();
        }

        return redirect()->route('chats.show', $chat['id']);
    }

    public function show(Chat $chat) {
        $users = $chat->users()->get();
        $users = UserResource::collection($users)->resolve();
        $chat = ChatResource::make($chat)->resolve();

        return Inertia::render(
            'Chat/Show',
            compact('chat', 'users')
        );
    }
}
