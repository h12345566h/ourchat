<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    protected $table = 'chat_members';
    protected $primaryKey = 'cm_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'account', 'chat_id', 'status'
    ];

    /** ChatMember status 自行加入:0 邀請加入:1 已加入:2 **/
    public function user()
    {
        return $this->belongsTo(User::class, 'account', 'account');
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'chat_id');
    }

}