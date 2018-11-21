<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'account', 'content', 'type', 'chat_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'account', 'account');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }
}