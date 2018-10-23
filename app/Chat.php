<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';
    protected $primaryKey = 'chat_id';
    const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator', 'chat_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function message()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function chatmember()
    {
        return $this->hasMany(chatmember::class, 'chat_id');
    }
}