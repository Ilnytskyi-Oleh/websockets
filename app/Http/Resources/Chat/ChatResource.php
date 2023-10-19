<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? $this->chatWith->name,
            'users' => $this->users,
            'unread_count' => $this->unread_message_statuses_count,
            'last_message' => $this->lastMessage
                ? MessageResource::make($this->lastMessage)->resolve()
                : null,
        ];
    }
}
