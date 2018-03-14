<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alsofronie\Uuid\Uuid32ModelTrait;
use LIBRESSLtd\LBForm\Traits\LBDatatableTrait;
use Auth;

class Role extends Model
{
    use Uuid32ModelTrait, LBDatatableTrait;

	protected $fillable = array('code');
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', "role_permissions", "role_id", "permission_id");
    }
	
    public function users()
    {
        return $this->belongsToMany('App\Models\User', "user_roles", "role_id", "user_id");
    }
	
	static public function addIfNotExist($role_name, $role_code)
	{
		$group = Role::firstOrNew(array("code" => $role_code));
		if ($role_name !== false)
		{
			$group->name = $role_name;
		}
		$group->save();
		return $group;
	}
    static function getListRoleAdmin($has_all = FALSE)
    {
        $data = Role::where('code', 'like', '%'.'admin'.'%')->where('code', '!=', 'superadmin')->get();
        $dataset = array();
        if ($has_all !== FALSE) {
            $dataset += array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r) {
            $dataset[$r->id] = array(
                'name' => $r->code,
                'value' => $r->id,
                'selected' => FALSE,
            );
        }
        return $dataset;
    }
    static function getListRoleUser($has_all = FALSE)
    {
        $role = substr(Auth::user()->roles[0]->name, 0, 4);
        if (Auth::user()->hasRole('superadmin'))
        {
            $data = Role::where('code', 'like', '%'.'user'.'%')->get();
        }
        elseif (!Auth::user()->hasRole('superadmin') || $role != 'user')
        {
            $code = substr(Auth::user()->roles[0]->name, 7, 7);
                $data = Role::where('code', 'like', '%'.'userlv'.$code.'%')->orWhere('code', 'like', '%'.'userlvl'.$code.'p'.'%')->get();
        }
        $dataset = array();
        if ($has_all !== FALSE) {
            $dataset += array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r) {
            $dataset[$r->id] = array(
                'name' => $r->code,
                'value' => $r->id,
                'selected' => FALSE,
            );
        }
        return $dataset;
    }
    static public function boot()
    {
    	Role::bootUuid32ModelTrait();
        Role::saving(function ($role) {
        	if (Auth::user())
        	{
	            if ($role->id)
	            {
	            	$role->updated_by = Auth::user()->id;
	            }
	            else
	            {
					$role->created_by = Auth::user()->id;
	            }
	        }
        });
    }
}
