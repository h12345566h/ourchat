<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blacks extends Model
{
    protected $table = 'blacks';
    protected $primaryKey = 'black_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'chat_id', 'black_account', 'blacked_account','created_at'
    ];

}