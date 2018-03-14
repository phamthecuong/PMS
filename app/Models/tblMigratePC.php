<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use App\Traits\UuidForKey;

class tblMigratePC extends Model
{
    use UuidForKey;
    
    protected $table = 'tblMigrate_PC';

    public function creator()
	{
		return $this->belongsTo("App\Models\User", "created_by");
	}
}
