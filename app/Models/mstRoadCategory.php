<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mstRoadCategory extends Model
{
    protected $table = 'mstRoad_category';
	
	static function allToOption($has_all = FALSE)
	{
		$data = mstRoadCategory::all();
					
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
				$r->id => $r->classification,
			);
		}
		
		return $dataset;
	}
	
	static function allOptionToAjax($has_all = FALSE, $demo = FALSE)
	{
		$data;
		if ($demo !== FALSE)
		{
			$data = mstRoadCategory::where('code_id', '<=', 1)->get();	
		}
		else
		{
			$data = mstRoadCategory::all();
		}
		
					
		$dataset = array();
		if ($has_all !== FALSE)
		{
			$dataset[] = array(
				'name' => $has_all,
				'value' => -1,
			);
		}
		
		foreach ($data as $r)
		{
			if ($r->code_id == 0 ) {
				$dataset[] = array(
					'name' => (\App::isLocale('en')) ? $r->code_name : trans('budget.exp'),
					'value' => $r->code_id,
					// 'selected' => FALSE,
				);
			}
			else if ($r->code_id == 1 ) {
				$dataset[] = array(
					'name' => (\App::isLocale('en')) ? $r->code_name : trans('budget.nh'),
					'value' => $r->code_id,
					// 'selected' => FALSE,
				);
			}
			else
			{
				$dataset[] = array(
					'name' => $r->code_name,
					'value' => $r->code_id,
					// 'selected' => FALSE,
				);
			}
		}
		
		return $dataset;
	}

}
