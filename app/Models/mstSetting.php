<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mstSetting extends Model
{
    protected $table = 'mstSetting' ;
    protected $fillable = [
      'description',
    ];
    public $timestamps = false;
}
