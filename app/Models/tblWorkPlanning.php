<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Ramsey\Uuid\Uuid;
use App\Traits\UuidForKey;
use App, DB, Config, Helper;

class tblWorkPlanning extends Model
{
	use UuidForKey;

	protected $table = 'tblWork_planning';

	protected $appends = ['organization_name'];

	public function organizations()
	{
		return $this->belongsToMany('App\Models\tblOrganization', 'tblWork_planning_organization', 'work_planning_id', 'organization_id');
	}
	
	public function repairMatrix()
	{
		return $this->belongsTo('App\Models\tblRepairMatrix', 'default_repair_matrix_id');
	}

	public function getAllRepiarMethod()
	{
		return  $this->repairMatrix->repairMethods;	
	}

	public function getOrganizationNameAttribute()
	{
		return $this->getInfoOrganization();
	}

	public function getInfoOrganization($session_id = -1)
	{
		$text = '';
		$organizations;
		if ($session_id != -1)
		{
			$organizations = tblWorkPlanning::find($session_id)->organizations;
		}
		else
		{
			$organizations = $this->organizations;	
		}
		
		foreach ($organizations as $p)
		{
			$text .= (Config::get('app.locale') == 'en') ? ', '.$p->name_en : ', '.$p->name_vn;	
		}
		$text = ltrim($text,' ,');
		
		return $text;
	}

	public function getAllRepairMethodToArray($has_all = FALSE, $get_field = 'name')
	{
		$repair_methods = $this->getAllRepiarMethod();
		
		$result = array();
		if ($has_all !== FALSE)
		{
			$result+= array(
				'' => $has_all,
			);
		}
		
		$name = (Config::get('app.locale')) ? 'name_en' : 'name_vn';
		
		foreach ($repair_methods as $method)
		{
			if ($get_field == 'name')
			{
				$result += array(
					$method->id => $method->$name,
				);
			}
			else
			{
				$result += array(
					$method->id => array(
						'name' => $method->$name,
						'color' => $method->$get_field
					)
				);
			}
		}
		
		return $result;
	}

	static public function getDataHaveList()
	{
		$work_planning = tblWorkPlanning::where('created_by', \Auth::user()->id)->orderBy('created_at', "DESC")->get();
		$wp_data = [];
		$i = 0;
		foreach ($work_planning as $wp_item)
		{
			$check = 0;
			$path = public_path('application/process/work_planning/'. $wp_item->id .'/data/input_final.csv');
			if (file_exists($path))
			{
				$check = 1;
				$title = $wp_item->organization_name . " - " . $wp_item->created_at. " - " . trans("wp.view_candidate");
			}
			$path = public_path('application/process/work_planning/'. $wp_item->id .'/data/input_proposal.csv');
			if (file_exists($path))
			{
				$check = 2;
				$title = $wp_item->organization_name . " - " . $wp_item->created_at. " - " . trans("wp.view_proposal");
			}
			$path = public_path('application/process/work_planning/'. $wp_item->id .'/data/input_planned.csv');
			if (file_exists($path))
			{
				$check = 3;
				$title = $wp_item->organization_name . " - " . $wp_item->created_at. " - " . trans("wp.view_final");
			}
			if ($check > 0 && $i < 3)
			{
				$wp_data[] = [
					'title' => $title,
					'icon' => "fa-save",
					'url' => 'user/work_planning/planned/'. $wp_item->id,
					'id' => $wp_item->id,
				];
				$i++;
			}
		}
		return $wp_data;
	}

}