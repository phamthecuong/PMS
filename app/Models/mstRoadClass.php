<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class   mstRoadClass extends Model
{
    protected $table = 'mstRoad_class';

    protected $appends = ["name", 'creater', 'updater'];

    public function getNameAttribute()
    {
        $lang = \App::getLocale();
        $lang == 'en' ? $key = "name_".$lang : $key = "name_vn";
        return $this->attributes[$key];
    }

    public function getCreaterAttribute() {
    	return  @$this->userCreate->name ? @$this->userCreate->name :'';

    }

    public function getUpdaterAttribute() {
    	return @$this->userUpdate->name ? @$this->userUpdate->name : '';
    }

    public function userCreate()
    {
        return $this->belongsTo('App\Models\user', 'created_by' , 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo('App\Models\user', 'updated_by' , 'id');
    }
	
	static function allToOption($has_all = FALSE)
	{
		$data = mstRoadClass::all();
					
		$dataset = array();
		if ($has_all != FALSE)
		{
			$dataset += array(
				-1 => $has_all,
			);
		}
		foreach ($data as $r)
		{
			$dataset += array(
				$r->id => (Config::get('app.locale') == 'en') ? $r->name_en : $r->name_vn,
			);
		}
		
		return $dataset;
	}
	
	static function allOptionToAjax($has_all = FALSE, $road_type = -1, $value_as_name = false, $has_name = FALSE, $has_code = FALSE, $has_text_id = FALSE)
	{
		$data;
		if ($road_type != -1)
		{
			
			$data = mstRoadClass::where('code_id', '>', 0);
			
			if ($road_type == 0)
			{
				$data = $data->where('code_id', '<=', 4);
			}
			else if ($road_type == 1)
			{
				$data = $data->where('code_id', '<=', 6);
			}
			$data = $data->get();
		}
		else
		{
			$data = mstRoadClass::whereNotIn('code_id', [99])->get();			
		}
					
		$dataset = array();
		if ($has_all != FALSE)
		{
			$dataset[] = array(
				'name' => $has_all,
				'value' => -1,
			);
		}
		
		foreach ($data as $r)
		{
			$dataset[] = array(
				'name' => (\App::isLocale('en')) ? $r->name_en : $r->name_vn,
				'value' => $value_as_name ? ((\App::isLocale('en')) ? $r->name_en : $r->name_vn) : $r->code_id
			);
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? $r->name_vn : $r->name_en,
                    'value' => $r->id);
            }
            if ($has_code)
            {
                $dataset[] =  array(
                    'name' => $r->code_id,
                    'value' => $r->id);
            }
            if ($has_text_id)
            {
                $dataset[] = array(
                    'name' => $r->text_id,
                    'value' => $r->id);
            }
		}
		return $dataset;
	}
 }
