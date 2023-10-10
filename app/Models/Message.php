<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = false;

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return  $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public  function getIsOwnerAttribute() {
        return (int) $this->user_id === (int) auth()->id();
    }
}
