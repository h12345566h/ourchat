<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EchoToken extends Model
{
    protected $table = 'echo_tokens';
    protected $primaryKey = 'et_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'account', 'type', 'token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'account', 'account');
    }
}