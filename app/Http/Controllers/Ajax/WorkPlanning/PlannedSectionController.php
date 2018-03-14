<?php

namespace App\Http\Controllers\Ajax\WorkPlanning;

use App\Classes\Helper;
use App\Models\tblOrganization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use App\Models\tblPlannedSection;

class PlannedSectionController extends Controller
{
    public function index(Request $request)
    {
        $planned_section = DB::table('tblPlanned_sections');
        $planned_section = $this->filterByUser($planned_section);
        $planned_section->select('tblPlanned_sections.id', 'tblPlanned_sections.sb_id',
            'tblPlanned_sections.branch_id', 'tblBranch.name_en as branch_name_en ','tblBranch.branch_number',
            'tblBranch.name_vn as branch_name_vn', 'O.sb_name_en', 'O.sb_name_vn', 'O.rmb_name_en', 'O.rmb_name_vn',
            'O.rmb_id', 'tblPlanned_sections.km_from', 'tblPlanned_sections.m_from', 'tblPlanned_sections.unit_cost',
            'tblPlanned_sections.km_to', 'tblPlanned_sections.m_to', 'tblPlanned_sections.section_length',
            'tblPlanned_sections.direction', 'tblPlanned_sections.lane_pos_no', 'tblPlanned_sections.planned_year',
            'tblPlanned_sections.repair_quantity', 'tblPlanned_sections.repair_cost', 'tblPlanned_sections.repair_method_en',
            'tblPlanned_sections.repair_method_vn', 'tblPlanned_sections.repair_classification_en',
            'tblPlanned_sections.repair_classification_vn')
            ->join(DB::raw('(select sb.id as id,sb.name_en as sb_name_en, sb.name_vn as sb_name_vn, rmb.id as rmb_id ,rmb.name_en as rmb_name_en, rmb.name_vn as rmb_name_vn from tblOrganization as sb, tblOrganization as rmb where sb.parent_id = rmb.id) as O'), 'O.id','=', 'tblPlanned_sections.sb_id' )
            ->join('tblBranch', 'tblPlanned_sections.branch_id', '=', 'tblBranch.id');
        $planned_section = $this->filterDropDown($planned_section, 'direction', 'direction', $request);
        $planned_section = $this->filterByCondition($planned_section, $request);
        $planned_section = $this->filterSuperInput($planned_section, 'km_from' , 'km_from', $request);
        $planned_section = $this->filterSuperInput($planned_section, 'm_from' , 'm_from', $request);
        $planned_section = $this->filterSuperInput($planned_section, 'planned_year' , 'planned_year', $request);
        $planned_section = $this->filterSuperInput($planned_section, 'unit_cost' , 'unit_cost', $request);
        $planned_section = $this->filterSuperInput($planned_section, 'repair_quantity' , 'repair_quantity', $request);
        $planned_section = $this->filterSuperInput($planned_section, 'repair_cost' , 'repair_cost', $request);
        $planned_section = $this->filterSuperInput($planned_section, 'lane_pos_number' , 'lane_pos_number', $request);
        return Datatables::of($planned_section)
            ->editColumn('direction', function($r) {
                switch ($r->direction)
                {
                    case 1:
                        return trans('back_end.left_direction');
                    case 2:
                        return trans('back_end.right_direction');
                    case 3:
                        return trans('back_end.single_direction');
                    default:
                        return '';
                }
            })
            ->make(true);
    }

    function filterByUser($query)
    {
        $user_organization = Auth::user()->organization_id;
        $organization = DB::table('tblOrganization')->where('parent_id', $user_organization)->get();
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
            return $query;
        }
        else
        {
            $query->where('sb_id', $user_organization);
            return $query;
        }

    }

    function filterByCondition($query, $request)
    {
        $lang = App::isLocale('en') ? 'en' : 'vn';
        $rmb_name = "rmb_name_{$lang}";
        $sb_name = "sb_name_{$lang}";
        $branch_name = "branch_name_{$lang}";
        $lang2 = $lang == 'en' ? 'name_en' : 'name_vn';
        if ((int)$request->$rmb_name != '')
        {
            $query = $query->where('O.rmb_id', $request->$rmb_name);
        }
        if ((int)$request->$sb_name != '')
        {
            $query = $query->where('O.id', $request->$sb_name);
        }
        if ($request->$branch_name != '')
        {
            $query = $query->where("tblBranch.{$lang2}", $request->$branch_name);
        }
        if ((int)$request->branch_number != '')
        {
            $query = $query->where("tblBranch.branch_number", $request->branch_number);
        }

        return $query;
    }

    function filterDropDown($query, $key_in_request, $key_in_db, $request)
    {
        if (isset($request->{$key_in_request}) && !empty($request->{$key_in_request}))
        {
            $query =  $query->where($key_in_db, $request->{$key_in_request});
        }
        return $query;
    }

    function filterSuperInput($query, $key_in_request, $key_in_db,  $request)
    {
        if ($request->{$key_in_request} != '')
        {
            $data_request = $request->{$key_in_request};
            $parseSuperInput = Helper::parseSuperInput($data_request);
            if (is_numeric($parseSuperInput[1]))
            {
                $query =  $query->where("tblPlanned_sections.{$key_in_db}" ,$parseSuperInput[0], $parseSuperInput[1]);
            }
            else
            {
                $query = $query->where("tblPlanned_sections.{$key_in_db}" , null);
            }
        }
        return $query;
    }

    public function getImportErrorData($file_name)
    {
        
    }
}
