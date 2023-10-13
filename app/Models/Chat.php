<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $guarded = false;

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_user', 'chat_id', 'user_id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'chat_id', 'id');
    }

    public function getUnreadCount(): int
    {
        return $this->hasMany(MessageStatus::class, 'chat_id', 'id')
            ->where('user_id', auth()->id())
            ->where('is_read', false)->count();
    }

    public function unreadMessageStatuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageStatus::class, 'chat_id', 'id')
            ->where('user_id', auth()->id())
            ->where('is_read', false);
    }
}
