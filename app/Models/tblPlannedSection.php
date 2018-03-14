<?php

namespace App\Models;

use App\Classes\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class tblPlannedSection extends Model
{
    protected $table = "tblPlanned_sections";

    protected $appends = ['repair_method_name', 'repair_classification_name', 'sb_name', 'branch_name', 'branch_numbers'];

    public function sb()
    {
    	return $this->belongsTo('App\Models\tblOrganization', 'sb_id');
    }

    public function branch()
    {
    	return $this->belongsTo('App\Models\tblBranch', 'branch_id');
    }

    public function getRepairMethodNameAttribute()
	{
		$lang = (\App::isLocale('en')) ? 'en' : 'vn';
		return $this->{"repair_method_{$lang}"};
	}

	public function getRepairClassificationNameAttribute()
	{
		$lang = (\App::isLocale('en')) ? 'en' : 'vn';
		return $this->{"repair_classification_{$lang}"};
	}

	public function getSbNameAttribute()
	{
        $data = $this->where('id', $this->id)->first();
		return $data->sb->organization_name;
	}

	public function getBranchNameAttribute()
	{
        $data = $this->where('id', $this->id)->first();
		return $data->branch->name;
	}

	public function getBranchNumbersAttribute()
	{
        $data = $this->where('id', $this->id)->first();
		return $data->branch->branch_number;
	}

    function scopeFilterByUser($query)
    {
        $user_organization = Auth::user()->organization_id;
        $organization = tblOrganization::where('parent_id', $user_organization)->get();
        $organization_id = [];
        foreach ($organization as $item)
        {
            $organization_id[] = $item->id;
        }
        if(Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlvl1p'))
        {
            return $query;
        }
        elseif (Auth::user()->hasRole('userlv2'))
        {
            $query->whereIn('sb_id', $organization_id);
            // $query->whereHas('segment', function ($query) use ($organization_id) {
            //     $query->whereIn('SB_id', $organization_id);
            // });
        }
        else
        {
            $query->where('sb_id', $user_organization);
            // $query->whereHas('segment', function ($query) use ($user_organization) {
            //     $query->where('SB_id', $user_organization);
            // });
        }

    }

    function scopeFilterByCondition($query, $request)
    {
        if ($request->branch_numbers != '')
        {
            $query->whereHas('branch', function ($query) use($request)
            {
                $query->where('branch_number' , $request->branch_numbers);
            })->get();
        }
        if ($request->rmb_name != '')
        {
            $query->whereHas('sb.rmb', function ($query) use($request)
            {
                $query->where('id', $request->rmb_name);
            })->get();
        }
        if ($request->sb_name != '')
        {
            $query->whereHas('sb', function ($query) use($request)
            {
                $query->where('SB_id', $request->sb_name);
            })->get();
        }
        if ($request->branch_name != '')
        {
            $name = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
            $query->whereHas('branch', function ($query) use($request, $name) {
                $query->where($name , $request->branch_name);
            })->get();
        }
    }

    function scopeFilterDropdown($query, $key_in_request, $key_in_db, $request)
    {
        if (isset($request->{$key_in_request}) && !empty($request->{$key_in_request}))
        {
            $query->where($key_in_db, $request->{$key_in_request})->get();
        }
    }

    function scopeFilterSuperInput($query, $key_in_request, $key_in_db,  $request)
    {
        if ($request->{$key_in_request} != '')
        {
            $data_request = $request->{$key_in_request};
            $parseSuperInput = Helper::parseSuperInput($data_request);
            if (is_numeric($parseSuperInput[1]))
            {
                $query->where($key_in_db ,$parseSuperInput[0], $parseSuperInput[1])->get();
            }
            else
            {
                $query->where($key_in_db , null)->get();
            }
        }
    }

	static public function config()
	{
		$object = [
			'section_id' => [
                'title' => trans('back_end.section_id'),
                'type' => 'text',
                'index' => 0,
                'validate' => ''
            ],
            'route_name' => [
                'title' => trans('wp.RouteName'),
                'type' => 'select',
                'modelCheck' => tblBranch::allOptionToAjax($has_all = FALSE, $value_as_name = FALSE, $has_name = true),
                'item' => tblBranch::allOptionToAjax(),
                'index' => 1,
                'validate' => 'required'
            ],
            'branch_number' => [
                'title' => trans('wp.branch_number'),
                'type' => 'select',
                'modelCheck' => tblBranch::branchNumberOptionToAjax(),
                'item' => tblBranch::branchNumberOptionToAjax(),
                'index' => 2,
                'validate' => 'required'
            ],
            'rmb' => [
                'title' => trans('wp.rmb'),
                'type' => 'select',
                'modelCheck' => tblOrganization::getListRmb($has_all = FALSE, $has_name = true),
                'item' => tblOrganization::getListRmb(),
                'index' => 3,
                'validate' => 'required'
            ],
            'sb' => [
                'title' => trans('wp.sb'),
                'type' => 'select',
                'modelCheck' => tblOrganization::getListSB($has_all = FALSE, $has_name = true),
                'item' => tblOrganization::getListSB(),
                'index' => 4,
                'validate' => 'required'
            ],
            'km_from' => [
                'title' => trans('wp.from_km'),
                'type' => 'text',
                'index' => 5,
                'validate' => ['required','integer','min:0'],
            ],
            'm_from' => [
                'title' => trans('wp.from_m'),
                'type' => 'text',
                'index' => 6,
                'validate' => ['required','integer','min:0'],
            ],
            'km_to' => [
                'title' => trans('wp.to_km'),
                'type' => 'text',
                'index' => 7,
                'validate' => ['required','integer','min:0'],
            ],
            'm_to' => [
                'title' => trans('wp.to_m'),
                'type' => 'text',
                'index' => 8,
                'validate' => ['required','integer','min:0'],
            ],
            'length' => [
               	'title' => trans('wp.Lenght, m'),
                'type' => 'text',
                'index' => 9,
                'validate' => array('required', 'regex:/^(?=.+)(?:[1-9]\d*|0)?(?:\.\d+)?$/'),
            ],
            'direction' => [
                'title' => trans('wp.UpOr Down'),
				'type' => 'check_select',
                'items' => [
                    ['name' => trans('back_end.left'), 'value' => 1],
                    ['name' => trans('back_end.right'), 'value' => 2],
                    ['name' => trans('back_end.single'), 'value' => 3]
                ],
                'index' => 10,
                'validate' => 'required'
            ],
            'lane_pos_no' => [
                'title' => trans('wp.lane_pos_no'),
                'type' => 'text',
                'index' => 11,
                'validate' => ['required','integer','min:0']
            ],
            'planned_year' => [
                'title' => trans('wp.planned_year'),
                'type' => 'text',
                'index' => 12,
                'validate' => ['required','integer']
            ],
            'repair_method' => [
                'title' => trans('wp.repair_method'),
                'type' => 'select',
                'modelCheck' => \App\Models\mstRepairMethod::allToOptionTwo($has_name = true, $has_code = true),
                'item' => \App\Models\mstRepairMethod::allToOptionTwo(),
                'index' => 13,
                'validate' => 'required'
            ],
            'repair_classification' => [
                'title' => trans('wp.repair_classification'),
                'type' => 'select',
                'modelCheck' => \App\Models\tblRClassification::allToOption($has_name = true, $has_code = true),
                'item' => \App\Models\tblRClassification::allToOption(),
                'index' => 14,
                'validate' => 'required'
            ],
            'unit_cost' => [
                'title' => trans('wp.unit_cost'),
                'type' => 'text',
                'index' => 15,
                'validate' => ['required', 'integer', 'min:0']
            ],
            'repair_quantity' => [
                'title' => trans('wp.repair_work_quantity'),
                'type' => 'text',
                'index' => 16,
                'validate' => ['required', 'integer', 'min:0']
            ],
            'repair_amount' => [
                'title' => trans('wp.repair_cost'),
                'type' => 'text',
                'index' => 17,
                'validate' => ['required', 'integer', 'min:0']
            ],
            'remarks' => [
                'title' => trans('wp.remarks'),
                'type' => 'text',
                'index' => 18,
                'validate' => ''
            ],
		];
        return $object;
	}
}
