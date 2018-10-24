<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EchoToken extends Model
{
    protected $table = 'echo_tokens';
    protected $primaryKey = 'et_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account', 'type', 'token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'account', 'account');
    }
}