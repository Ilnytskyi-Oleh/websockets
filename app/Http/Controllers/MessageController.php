<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Jobs\StoreMessageStatusJob;
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

        $message = Message::create($data);

        StoreMessageStatusJob::dispatch($data, $message)->onQueue('store_message');

        broadcast(new StoreMessageEvent($message))->toOthers();

        return MessageResource::make($message)->resolve();
    }
}
