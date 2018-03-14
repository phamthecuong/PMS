<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class mstMethodUnit extends Model
{
    protected $table = 'mstMethod_unit';
	
	static function allToOption()
	{
		$data = mstMethodUnit::all();
					
		$dataset = array();
		// if ($has_all !== FALSE)
		// {
		// 	$dataset += array(
		// 		-1 => $has_all,
		// 	);
		// }
		foreach ($data as $r)
		{
			$dataset[] = array(
				'value' => $r->id,
				'name' => $r->code_name,
			);
		}
		
		return $dataset;
	}
}
