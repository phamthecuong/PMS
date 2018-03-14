<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class mstRepairMethod extends Model
{
    protected $table = 'mstRepair_method';
    protected $appends = ['name', 'creater', 'updater'];

    public function getCreaterAttribute() {
        return  @$this->userCreate->name ? @$this->userCreate->name :'';
    }
    public function getUpdaterAttribute() {
        return @$this->userUpdate->name ? @$this->userUpdate->name : '';
    }

    function getNameAttribute()
    {
        $lang = (\App::isLocale('en')) ? 'en' : 'vn';
        return $this->{"name_{$lang}"};
    }

    public function userCreate()
    {
        return $this->belongsTo('App\Models\user', 'created_by' , 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('App\Models\user', 'updated_by' , 'id');
    }

    public function repairCategory()
    {
        return $this->belongsTo('App\Models\tblRCategory', 'zone_id');
    }
	
	static function allToOption($has_all = FALSE)
	{
		$data = mstRepairMethod::all();
					
		$dataset = array();
		if ($has_all !== FALSE)
		{
			$dataset += array(
				-1 => $has_all,
			);
		}
		foreach ($data as $r)
		{
			$dataset += array(
				$r->id => (Config::get('app.locale')) ? $r->name_en : $r->name_vn,
			);
		}
		
		return $dataset;
	}

	static function allToOptionTwo($has_name = FALSE, $has_code = FALSE, $all = FALSE)
	{
		$data = mstRepairMethod::get();
		$dataset = array();
		foreach ($data as $r)
        {   
            if ($all) 
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? 'All' : 'Tất cả',
                    'value' => -1
                );
            }
            $dataset[] = array(
                'name' => (\App::isLocale('en')) ? $r->name_en : $r->name_vn,
                'value' => $r->id
            );
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? $r->name_vn : $r->name_en,
                    'value' => $r->id);
            }
            if ($has_code)
            {
                $dataset[] = array(
                    'name' => $r->code,
                    'value' => $r->id);
            }
        }
		return $dataset;
	}
	
	public function surface()
	{
        return $this->belongsTo('App\Models\mstSurface', 'pavement_type', 'id');
    }

    public function costs()
	{
        return $this->hasMany('App\Models\tblRepairMethodCost', 'repair_method_id', 'id');
    }

    public function unit()
    {
    	return $this->belongsTo('App\Models\mstMethodUnit', 'unit_id');
    }

    public function classification()
    {
    	return $this->belongsTo('App\Models\tblRClassification', 'classification_id');
    }
}
