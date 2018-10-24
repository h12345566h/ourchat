<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    protected $table = 'chatmember';
    protected $primaryKey = 'cm_id';
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    public function message()
    {
        return $this->hasMany(Message::class, 'cm_id', 'cm_id');
    }

}