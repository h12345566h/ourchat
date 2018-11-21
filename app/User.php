<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'user';
    protected $primaryKey = 'account';
    public $incrementing = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'account', 'email', 'password', 'name','profile_pic'
    ];

    protected $hidden = [
        'password', 'created_at'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function message()
    {
        return $this->hasMany(Message::class, 'account');
    }

    public function ChatMember()
    {
        return $this->hasMany(ChatMember::class, 'account');
    }

    public function Chat()
    {
        return $this->hasMany(Chat::class, 'creator');
    }

    public function echo_tokens()
    {
        return $this->hasMany(echo_tokens::class, 'account');
    }
}