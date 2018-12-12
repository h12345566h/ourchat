<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'report_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'type', 'id'
    ];
}
