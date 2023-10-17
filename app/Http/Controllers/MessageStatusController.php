<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageStatuses\UpdateRequest;
use App\Models\MessageStatus;


class MessageStatusController extends Controller
{
    public function update(UpdateRequest $request) {
        $data = $request->validated();

        MessageStatus::where('message_id', $data['message_id'])
            ->where('user_id', auth()->id())
            ->update([
                'is_read' => true
            ]);
    }
}
