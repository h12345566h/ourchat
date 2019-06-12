<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $primaryKey = 'chat_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'creator', 'chat_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'account', 'creator');
    }

    public function chatMembers()
    {
        return $this->hasMany(chatmember::class, 'chat_id', 'chat_id');
    }
}