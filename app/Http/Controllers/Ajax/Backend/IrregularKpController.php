<?php

namespace App\Http\Controllers\Ajax\BackEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Models\mstIrregularKp;
use DB;
use App\Classes\Helper;

class IrregularKpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $irregular_kp = DB::table('mstIrregular_kps');
        // $irregular_kp = $this->filterByUser($planned_section);
        $irregular_kp->select('mstIrregular_kps.id', 'mstIrregular_kps.branch_id', 'mstIrregular_kps.direction', 'mstIrregular_kps.kp', 'mstIrregular_kps.section_length', 'mstIrregular_kps.note', 'tblBranch.name_en as branch_name_en', 'tblBranch.name_vn as branch_name_vn')->join('tblBranch', 'mstIrregular_kps.branch_id', '=', 'tblBranch.id');
        $irregular_kp = $this->filterDropDown($irregular_kp, 'direction', 'direction', $request);
        $irregular_kp = $this->filterByCondition($irregular_kp, $request);
        $irregular_kp = $this->filterSuperInput($irregular_kp, 'kp' , 'kp', $request);

        //$irregular_kp = mstIrregularKp::with('branch')->get();
        return Datatables::of($irregular_kp)
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
            ->addColumn('action', function ($ir) {
                $result = [];
               
                $result[] = \Form::lbButton(
                    '/admin/irregular_kp/'.$ir->id.'/edit', 
                    'GET', 
                    "<i class='fa fa-pencil' aria-hidden='true'></i>", 
                    ["class" => "btn btn-xs btn-warning"]
                )->toHtml();
            
                $result[] = \Form::lbButton(
                    url('admin/irregular_kp/' . $ir->id), 
                    'DELETE', 
                    "<i class='fa fa-trash-o' aria-hidden='true'></i>", 
                    [
                        "class" => "btn btn-xs btn-danger", 
                        "onclick" => "return confirm('" . trans('back_end.are_you_sure') . "')"
                    ]
                )->toHtml();
                
                return implode(' ', $result);
            })->make(true);
        
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
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $branch_name = "branch_name_{$lang}";
        $lang2 = $lang == 'en' ? 'name_en' : 'name_vn';
        // if ((int)$request->$rmb_name != '')
        // {
        //     $query = $query->where('O.rmb_id', $request->$rmb_name);
        // }
        // if ((int)$request->$sb_name != '')
        // {
        //     $query = $query->where('O.id', $request->$sb_name);
        // }
        if ($request->$branch_name != '')
        {
            $query = $query->where("tblBranch.{$lang2}", $request->$branch_name);
        }
        // if ((int)$request->branch_number != '')
        // {
        //     $query = $query->where("tblBranch.branch_number", $request->branch_number);
        // }

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
                $query =  $query->where("mstIrregular_kps.{$key_in_db}" ,$parseSuperInput[0], $parseSuperInput[1]);
            }
            else
            {
                $query = $query->where("mstIrregular_kps.{$key_in_db}" , null);
            }
        }
        return $query;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
