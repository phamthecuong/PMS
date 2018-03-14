<?php

namespace App\Models;

use DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use LIBRESSLtd\DeepPermission\Traits\DPUserModelTrait;

class User extends Authenticatable
{
    use DPUserModelTrait;
    protected $table = 'users';
    protected $appends = [ 'organization_name', 'role_name'];
    
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_roles', 'user_id', 'role_id');
    }

    public function organizations()
    {
        return $this->belongsTo('App\Models\tblOrganization', 'organization_id');
    }

    public function getOrganizationNameAttribute()
    {
        $organization = $this->organizations['organization_name'];
        if ($organization != null)
        {
            return $organization;
        }
        else
        {
            return '';
        }
    }

    public function getRoleNameAttribute()
    {
        $role_name = $this->roles[0]->name;
        if ($role_name != null)
        {
            return $role_name;
        }
        else
        {
            return '';
        }
    }

}
