<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inflash, Validator, DB, DateTime, Auth, Session, Form, Hash, App;
use App\Models\tblSegment;
use App\Models\tblSegmentHistory;
use App\Models\tblOrganization;
// use App\Models\tblRoad;
use App\Models\tblBranch;
use App\Models\tblDistrict;
use App\Models\tblCity;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataTV;
use App\Models\tblMergeSplitDetail;
use App\Models\tblMergeSplit;
use App\Classes\Helper;

class SegmentController extends Controller
{
	public function __construct()
    {
    	$this->middleware("dppermission:segment_management.view");
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tblBranch = tblBranch::get();
		$road_number = tblBranch::groupBy('branch_number')->get();
		if (Auth::user()->hasRole('adminlv2'))
		{
			$organization = tblOrganization::where('level', 2)->where('id', Auth::user()->organization_id)->get();
			$sub = tblOrganization::where('parent_id', $organization[0]->id)->get();
		}
		else if (Auth::user()->hasRole('adminlv3'))
		{
			$sub = tblOrganization::where('id', Auth::user()->organization_id)->get();
			$organization = tblOrganization::where('id', $sub[0]->parent_id)->get();
		}
		else
		{
			$organization = tblOrganization::where('level', 2)->get();
            $sub = tblOrganization::all();
		}
        return view('admin.segment.index')->with(array(
        	'case' => 'segment',
        	'road_number' => $road_number,
        	'tblBranch' => $tblBranch,
        	'tblOrganization' => $organization,
			'sub' => $sub
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $road = tblOrganization::get();
		if (Auth::user()->hasRole('adminlv2'))
		{
			$road = tblOrganization::where('level', 2)->where('id', Auth::user()->organization_id)->get();
			$sub = tblOrganization::where('parent_id', $road[0]->id)->get();
			$disable = 'disabled';
		}
		else if (Auth::user()->hasRole('adminlv3'))
		{
			$sub = tblOrganization::where('id', Auth::user()->organization_id)->get();
			$road = tblOrganization::where('id', $sub[0]->parent_id)->get();
			$disable = 'disabled';
		}
		else if (Auth::user()->hasRole('adminlv1') || Auth::user()->hasRole('superadmin'))
		{
			$road = tblOrganization::where('level', 2)->get();
			$sub = tblOrganization::where('level', 3)->get();
			$disable = '';
		}
        // $route = @tblRoad::get();
        $branch = tblBranch::get();
        $distric = tblDistrict::get();
        $city = tblCity::get();
        return view('admin.segment.create')->with(array(
            'road' => $road,
            // 'route' => $route, 
            'branch' => $branch,
            'distric' => $distric,
            'city' => $city,
            'sub' => $sub,
            'disable' => $disable
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (empty($request->name_en) || empty($request->name_vn))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.segment_name_not_null'));
            return redirect()->back()->withInput();
        }

        // if ($request->year == null)
        // {
        //     Session()->flash('class', 'alert alert-danger');
        //     Session()->flash('message', trans('back_end.year_not_null'));
        //     return redirect()->back()->withInput();
        // }
        
        $inflash_from = $request->f_km*10000 + $request->f_m;
        $inflash_to = $request->t_km*10000 + $request->t_m;
        if ($request->f_km == $request->t_km && $request->f_m == $request->t_m)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.place_from_equal_to'));
            return redirect()->back()->withInput();
        }

        if ($inflash_to < $inflash_from)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.error_inflash'));
            return redirect()->back()->withInput();
        }

        if (ctype_space($request->name_en) || ctype_space($request->name_vn))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.name_space'));
            return redirect()->back()->withInput();
        }
        if ($request->f_km < 0 || $request->f_m < 0 || $request->t_km < 0 || $request->t_m < 0)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.km_m_field_from_to'));
            return redirect()->back()->withInput();
        }
        if ($request->f_m > 9999 || $request->t_m > 9999 || $request->t_m < 0 ||$request->f_m < 0)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.m_field_from_to'));
            return redirect()->back()->withInput();
        }
        // H.ANH  06.12.2016  remove bad code
        // $segment = tblSegment::where('branch_id', $request->branch)->where('SB_id', $request->bureau)->get();
        // foreach ($segment as $s)
        // {
        //     $start_point = $s->km_from * 10000 + $s->m_from;
        //     $end_point = $s->km_to * 10000 + $s->m_to;
        //     if ($start_point < $inflash_from && $inflash_from < $end_point)
        //     {
        //         Session()->flash('class', 'alert alert-danger');
        //         Session()->flash('message', trans('back_end.identical_segments'));
        //         return redirect()->back()->withInput();
        //     }
        //     if ($start_point < $inflash_to && $inflash_to < $end_point)
        //     {
        //         Session()->flash('class', 'alert alert-danger');
        //         Session()->flash('message', trans('back_end.identical_segments'));
        //         return redirect()->back()->withInput();
        //     }
        // }
        $segment = tblSegment::getRelatedSegments($request->f_km, $request->f_m, $request->t_km, $request->t_m, $request->branch_slect, $request->bureau); 
        if (count($segment) > 0)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.identical_segments'));
            return redirect()->back()->withInput();
        }
        // end modification
        if ($request->f_km == null || $request->f_m == null || $request->t_km == null || $request->t_m == null)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.Chainage_not_null'));
            return redirect()->back()->withInput();
        }
        if (!is_numeric($request->f_km) || !is_numeric($request->f_m) || !is_numeric($request->t_km) || !is_numeric($request->t_m) )
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.Chainage_not_number'));
            return redirect()->back()->withInput();
        }
       
        if (intval(($request->f_km)) != floatval($request->f_km) || intval(($request->t_km))!= floatval($request->t_km))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.km_from_or_km_to_not_float'));
            return redirect()->back()->withInput();
        }

        if (empty($request->name_en))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.name_not_null'));
            return redirect()->back()->withInput();
        }

		if ($request->fro_pro > 0 || $request->fro_to > 0) {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.you_have_to_chose_province_from_and_to'));
            return redirect()->back()->withInput();
        }
        if ($request->dis_pro  > 0  || $request->dis_to > 0) {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.you_have_to_chose_district_from_and_to'));
            return redirect()->back()->withInput();
        }
        if ($request->commune_fro > 0 || $request->commune_to > 0) {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.you_have_to_chose_commune_from_and_to'));
            return redirect()->back()->withInput();
        }
        $s = new tblSegment;
        $s->branch_id = $request->branch_slect;
        $s->segname_en = $request->name_en;
        $s->segname_vn = $request->name_vn;
        $s->km_from = $request->f_km;
        $s->m_from = $request->f_m;
        $s->km_to = $request->t_km;
        $s->m_to = $request->t_m;
        $s->prfrom_id = $request->fro_pro;
        $s->prto_id = $request->fro_to;
        $s->distfrom_id = $request->dis_pro;
        $s->distto_id = $request->dis_to;
        $s->SB_id = $request->bureau;
        $s->commune_from = $request->commune_fro;
        $s->commune_to = $request->commune_to;
        //$s->effect_at = $request->year;
        $s->nullity_at = null;
        $s->save();
        Session()->flash('class', 'alert alert-success');
        Session()->flash('message', trans('back_end.add_segment_success'));
        return redirect()->route('manager_segment.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $segment = tblSegment::with('tblCity_from', 'tblCity_to', 'tblDistrict_from', 'tblDistrict_to', 'tblward_to', 'tblward_from')->find($id);
		if (Auth::user()->hasRole('adminlv2'))
		{
			$rmb = tblOrganization::where('level', 2)->where('id', Auth::user()->organization_id)->get();
			$sub = tblOrganization::where('parent_id', $road[0]->id)->get();
			$disable = 'disabled';
		}
		else if (Auth::user()->hasRole('adminlv3'))
		{
			$sub = tblOrganization::where('id', Auth::user()->organization_id)->get();
			$rmb = tblOrganization::where('id', $sub[0]->parent_id)->get();
			$disable = 'disabled';
		}
		else
		{
			$rmb = tblOrganization::where('level', 2)->get();
			$sub = tblOrganization::where('level', 3)->get();
			$disable = '';
		}

        $road_bureau;
        if (isset($segment->tblOrganization))
        {
            $road_bureau = tblOrganization::where('parent_id', $segment->tblOrganization->parent_id)->get();   
        }
        else
        {
            $road_bureau = tblOrganization::whereNotNull('parent_id')->where('parent_id', '<>', 0)->get();       
        }
        
        $branch = tblBranch::get();
        $distric = tblDistrict::get();
        $city = tblCity::get();
        return view('admin.segment.create')->with(array(
            'segment_id' => $id,
            'road' => $rmb,
            'branch' => $branch,
            'distric' => $distric,
            'city' => $city,
            'segment' => $segment,
            'road_bureau' => $road_bureau,
            'disable' => $disable
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    function check_segment_update($km_from, $m_from, $km_to, $m_to, $km_in_from, $m_in_from, $km_in_to, $m_in_to)
    {
        $length = max($m_from, $m_to, $m_in_from, $m_in_to);
        // $factor = pow(10, strlen($length));
        $factor = 10000;
        $start_in = $km_in_from*$factor + $m_in_from ;
        $end_in = $km_in_to*$factor + $m_in_to ;
        $start_first = $km_from*$factor + $m_from;
        $end_first = $km_to*$factor + $m_to;
        if ((($end_in - $start_in) < ($end_first - $start_first)) && ($start_first <= $start_in) && ($end_in <= $end_first))
        {
            if ($start_first == $start_in)
            {
                return 'case_short_1';
            }
            else if ($end_first == $end_in)
            {
                return 'case_short_2';
            }
            else
            {
                return 'case_short_3';
            }
        }
        else if ((($end_in - $start_in) == ($end_first - $start_first)) && ($start_first == $start_in) && ($end_in == $end_first))
        {
            return 'case_neutral';
        }
        // else if ( ( ($end_in - $start_in) > ($end_first - $start_first) ) && ($start_first >= $start_in) && ($end_in >= $end_first))
        // {
        //     return 'case_long';
        // }
        else if (($start_in < $start_first) && ($start_first < $end_in) && ($end_in < $end_first))
        {
            return 'overlap_front';
        }
        else if (($start_in < $end_first) && ($end_first < $end_in) && ($start_first < $end_first))
        {
            return 'overlap_end';
        }
        else if ($start_in > $start_first && $end_in < $end_first)
        {
            return 'whole';
        }

    }
     
    public function update(Request $request, $id)
    {
        if ($request->f_km < 0 || $request->f_m < 0 || $request->t_km < 0 || $request->t_m < 0)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.km_m_field_from_to'));
            return redirect()->back()->withInput();
        }
        if (ctype_space($request->name_en) || ctype_space($request->name_vn))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.name_space'));
            return redirect()->back()->withInput();
        }
        if(empty($request->name_en) || empty($request->name_vn))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.name_not_null'));
            return redirect()->back()->withInput();
        }
        $start_in = $request->f_km*10000 + $request->f_m;
        $end_in = $request->t_km*10000 + $request->t_m;
        if ($request->f_km == $request->t_km && $request->f_m == $request->t_m)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.place_from_equal_to'));
            return redirect()->back()->withInput();
        }
        if($end_in < $start_in)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.error_inflash'));
            return redirect()->back()->withInput();
        }
        if ($request->f_m > 9999 || $request->t_m > 9999 || $request->t_m < 0 ||$request->f_m < 0)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.m_field_from_to'));
            return redirect()->back()->withInput();
        }
        if ($request->f_km < 0 || $request->t_km < 0)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.m_field_be_hon_khong'));
            return redirect()->back()->withInput();
        }
        if ($request->f_km == null || $request->f_m == null || $request->t_km == null || $request->t_m == null)
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.Chainage_not_null'));
            return redirect()->back()->withInput();
        }
        if (is_numeric($request->f_km) == false || is_numeric($request->f_m) == false || is_numeric($request->t_km) == false || is_numeric($request->t_m) == false )
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.Chainage_not_number'));
            return redirect()->back()->withInput();
        }

        if (intval(($request->f_km)) != floatval($request->f_km) || intval(($request->t_km))!= floatval($request->t_km))
        {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.km_from_or_km_to_not_float'));
            return redirect()->back()->withInput();
        }
       
        if ($request->fro_pro == 0 || $request->fro_to == 0) {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.you_have_to_chose_province'));
            return redirect()->back()->withInput();
        }
        if ($request->dis_pro == -1 || $request->dis_to == -1) {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.you_have_to_chose_district'));
            return redirect()->back()->withInput();
        }
        if ($request->commune_fro == -1 || $request->commune_to == -1) {
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', trans('back_end.you_have_to_chose_commune'));
            return redirect()->back()->withInput();
        }

        // begin transaction
        DB::beginTransaction();
        try
        {
            // retrieve current segment and update
            $current_seg = tblSegment::findOrFail($id);
            $old_data = clone $current_seg;

            $current_seg->segname_en = $request->name_en;
            $current_seg->segname_vn = $request->name_vn;
            $current_seg->km_from = $request->f_km;
            $current_seg->m_from = $request->f_m;
            $current_seg->km_to = $request->t_km;
            $current_seg->m_to = $request->t_m;
            $current_seg->prfrom_id = $request->fro_pro;
            $current_seg->prto_id = $request->fro_to;
            $current_seg->distfrom_id = $request->dis_pro;
            $current_seg->distto_id = $request->dis_to;
            $current_seg->SB_id = $request->bureau;
            $current_seg->commune_from = $request->commune_fro;
            $current_seg->commune_to = $request->commune_to;
            //$current_seg->effect_at = $request->year." 00:00:00";
            $current_seg->created_by = Auth::user()->id;
            $current_seg->save();

            // get related segments that have chainage conflict with current segment
            $related_segs = tblSegment::getRelatedSegments($request->f_km, $request->f_m, $request->t_km, $request->t_m, $current_seg->branch_id, $current_seg->id); 
            
            foreach ($related_segs as $rec) 
            {
                
                if (\Helper::compareTwoPoint($current_seg->km_from, $current_seg->m_from, $rec->km_from, $rec->m_from) <= 0 && \Helper::compareTwoPoint($current_seg->km_to, $current_seg->m_to, $rec->km_to, $rec->m_to) >= 0)
                {
                    // this is whole overlapping case
                    DB::table('tblSectiondata_MH')
                        ->where('segment_id', $rec->id)
                        ->update([
                            'segment_id' => $current_seg->id,
                            'effect_at' => $current_seg->effect_at,
                            'updated_by' => Auth::user()->id
                        ]);
                    DB::table('tblSectiondata_TV')
                        ->where('segment_id', $rec->id)
                        ->update([
                            'segment_id' => $current_seg->id,
                            'effect_at' => $current_seg->effect_at,
                            'updated_by' => Auth::user()->id
                        ]);
                    DB::table('tblSectiondata_RMD')
                        ->where('segment_id', $rec->id)
                        ->update([
                            'segment_id' => $current_seg->id,
                            'effect_at' => $current_seg->effect_at,
                            'updated_by' => Auth::user()->id
                        ]);
                    $tmp = tblSegment::findOrFail($rec->id);
                    $tmp->nullity_at = $current_seg->effect_at;
                    $tmp->updated_by = Auth::user()->id;
                    $tmp->save();
                    $tmp->delete();
                }
                else
                {
                    $overlapside = 1;// right
                    if (\Helper::compareTwoPoint($current_seg->km_from, $current_seg->m_from, $rec->km_from, $rec->m_from) < 0)
                    {
                        $overlapside = -1;//left
                    }
                    // partial overlapping case
                    $tmp = tblSegment::findOrFail($rec->id);
                    if ($overlapside == -1)
                    {
                        $tmp->km_from = $current_seg->km_to;
                        $tmp->m_from = $current_seg->m_to;
                    }
                    else
                    {
                        $tmp->km_to = $current_seg->km_from;
                        $tmp->m_to = $current_seg->m_from; 
                    }
                    $tmp->effect_at = $current_seg->effect_at;
                    $tmp->updated_by = Auth::user()->id;
                    $tmp->save();

                    $this->moveComponentToSegment($tmp->id, $current_seg->id, $overlapside);
                }
            }
            
            if (\Helper::compareTwoPoint($old_data->km_from, $old_data->m_from, $current_seg->km_from, $current_seg->m_from) < 0)
            {
                if (tblSectiondataRMD::getOutsideBoundarySection($current_seg->id, -1)->count() + tblSectiondataMH::getOutsideBoundarySection($current_seg->id, -1)->count() + tblSectiondataTV::getOutsideBoundarySection($current_seg->id, -1)->count() > 0)
                {
                    $new_seg = new tblSegment;
                    $new_seg->segname_en = $current_seg->tblBranch->name_en . ' Km' . $old_data->km_from . '+' . $old_data->m_from . '-Km' . $current_seg->km_from . '+' . $current_seg->m_from;
                    $new_seg->segname_vn = $current_seg->tblBranch->name_vn . ' Km' . $old_data->km_from . '+' . $old_data->m_from . '-Km' . $current_seg->km_from . '+' . $current_seg->m_from;  
                    $new_seg->km_from = $old_data->km_from;
                    $new_seg->m_from = $old_data->m_from;
                    $new_seg->km_to = $current_seg->km_from;
                    $new_seg->m_to = $current_seg->m_from;
                    $new_seg->prfrom_id = $old_data->prfrom_id;
                    $new_seg->prto_id = $old_data->prto_id;
                    $new_seg->distfrom_id = $old_data->distfrom_id;
                    $new_seg->distto_id = $old_data->distto_id;
                    $new_seg->SB_id = $old_data->SB_id;
                    $new_seg->branch_id = $old_data->branch_id;
                    $new_seg->commune_from = $old_data->commune_from;
                    $new_seg->commune_to = $old_data->commune_to;
                    $new_seg->effect_at = $current_seg->effect_at;
                    $new_seg->created_by = Auth::user()->id;
                    $new_seg->save();
                    $this->moveComponentToSegment($current_seg->id, $new_seg->id, -1);
                }
            }
            
            if (\Helper::compareTwoPoint($old_data->km_to, $old_data->m_to, $current_seg->km_to, $current_seg->m_to) > 0)
            {
                if (tblSectiondataRMD::getOutsideBoundarySection($current_seg->id, 1)->count() + tblSectiondataMH::getOutsideBoundarySection($current_seg->id, 1)->count() + tblSectiondataTV::getOutsideBoundarySection($current_seg->id, 1)->count() > 0)
                {
                    $new_seg = new tblSegment;
                    $new_seg->segname_en = $current_seg->tblBranch->name_en . ' Km' . $current_seg->km_to . '+' . $current_seg->m_to . '-Km' . $old_data->km_to . '+' . $old_data->m_to;
                    $new_seg->segname_vn = $current_seg->tblBranch->name_vn . ' Km' . $current_seg->km_to . '+' . $current_seg->m_to . '-Km' . $old_data->km_to . '+' . $old_data->m_to;
                    $new_seg->km_from = $current_seg->km_to;
                    $new_seg->m_from = $current_seg->m_to;
                    $new_seg->km_to = $old_data->km_to;
                    $new_seg->m_to = $old_data->m_to;
                    $new_seg->prfrom_id = $old_data->prfrom_id;
                    $new_seg->prto_id = $old_data->prto_id;
                    $new_seg->distfrom_id = $old_data->distfrom_id;
                    $new_seg->distto_id = $old_data->distto_id;
                    $new_seg->SB_id = $old_data->SB_id;
                    $new_seg->commune_from = $old_data->commune_from;
                    $new_seg->commune_to = $old_data->commune_to;
                    $new_seg->effect_at = $current_seg->effect_at;
                    $new_seg->created_by = Auth::user()->id;
                    $new_seg->branch_id = $old_data->branch_id;
                    $new_seg->save();
                    $this->moveComponentToSegment($current_seg->id, $new_seg->id, 1);
                }
            }

            // successfully
            DB::commit();    
            Session()->flash('class', 'alert alert-success');
            Session()->flash('message', trans('back_end.edit_segment_success'));
            return redirect()->route('manager_segment.index');
        }
        catch (Exception $e)
        {
            DB::rollBack();
            Session()->flash('class', 'alert alert-danger');
            Session()->flash('message', $e->getMessage());
            return redirect()->route('manager_segment.index');
        }


        // $array_segment = array();
        // $s = tblSegment::find($id);
        // $start_first = 1000*$s->km_from + $s->m_from;
        // $end_first = 1000*$s->km_to + $s->m_to;
        // $start_in = $request->f_km*1000 + $request->f_m;
        // $end_in = $request->t_km*1000 + $request->t_m;
        // $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $request->f_km, $request->f_m, $request->t_km, $request->t_m);
        // // echo $check;die;
        // $segment = new tblSegment;
        // $segment->segname_en = $s->segname_en;
        // $segment->segname_vn = $s->segname_vn;
        // $segment->branch_id = $s->branch_id;
        // $segment->prfrom_id = $s->prfrom_id;
        // $segment->prto_id = $s->prto_id;
        // $segment->distfrom_id = $s->distfrom_id;
        // $segment->distto_id = $s->distto_id;
        // $segment->SB_id = $s->SB_id;
        // $segment->commune_from = $s->commune_from;
        // $segment->commune_to = $s->commune_to;
        // $segment->nullity_at = $s->nullity_at;
        // if ($request->year == null)
        // {
        //     Session()->flash('class', 'alert alert-danger');
        //     Session()->flash('message', trans('back_end.year_not_null'));
        //     return redirect()->back()->withInput();
        // }
        // else 
        // {
        //     $segment->effect_at = $request->year." 00:00:00";
        // }
        // if ($check == 'case_short_1')
        // {
        //     $segment->km_from = $request->t_km;
        //     $segment->m_from = $request->t_m;
        //     $segment->km_to = $s->km_to;
        //     $segment->m_to = $s->m_to;
        //     $segment->save();
        //     // $s->segname_en = $request->name_en;
        //     // $s->segname_vn = $request->name_vn;
        //     // $s->km_from = $request->f_km;
        //     // $s->m_from = $request->f_m;
        //     // $s->km_to = $request->t_km;
        //     // $s->m_to = $request->t_m;
        //     // $s->prfrom_id = $request->fro_pro;
        //     // $s->prto_id = $request->fro_to;
        //     // $s->distfrom_id = $request->dis_pro;
        //     // $s->distto_id = $request->dis_to;
        //     // $s->SB_id = $request->bureau;
        //     // $s->commune_from = $request->commune_fro;
        //     // $s->commune_to = $request->commune_to;
        //     // $s->nullity_at = $s->effect_at;
        //     // if ($request->year == null)
        //     // {
        //     //     $s->effect_at = null;
        //     // }
        //     // else 
        //     // {
        //     //     $s->effect_at = $request->year." 00:00:00";
        //     // }
        //     // $s->save();
        //     $tblSectiondataMH = tblSectiondataMH::where('segment_id', $id)->get();
        //     foreach ($tblSectiondataMH as $t)
        //     {
        //         $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $t->km_from, $t->m_from, $t->km_to, $t->m_to);
        //         if ($check == 'overlap_front')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataMH;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->completion_date = $t->completion_date;
        //             $tblSectiondataMH1->repair_duration = $t->repair_duration;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->total_width_repair_lane = $t->total_width_repair_lane;
        //             $tblSectiondataMH1->r_classification_id = $t->r_classification_id;
        //             $tblSectiondataMH1->r_structType_id = $t->r_structType_id;
        //             $tblSectiondataMH1->r_category_id = $t->r_category_id;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->distance = $t->distance;
        //             $tblSectiondataMH1->direction_running = $t->direction_running;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $s->km_to;
        //             $t->m_to = $s->m_to;
        //             $t->save();
        //         }
        //         else if ($check == 'overlap_end')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataMH;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->completion_date = $t->completion_date;
        //             $tblSectiondataMH1->repair_duration = $t->repair_duration;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->total_width_repair_lane = $t->total_width_repair_lane;
        //             $tblSectiondataMH1->r_classification_id = $t->r_classification_id;
        //             $tblSectiondataMH1->r_structType_id = $t->r_structType_id;
        //             $tblSectiondataMH1->r_category_id = $t->r_category_id;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->distance = $t->distance;
        //             $tblSectiondataMH1->direction_running = $t->direction_running;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $s->km_to;
        //             $t->m_to = $s->m_to;
        //             $t->save();
        //         }
        //         else if ($check == 'whole')
        //         {
        //             $t->segment_id = $segment->id;
        //             $t->save();
        //         }
        //     }
        //     $tblSectiondataRMD = tblSectiondataRMD::get();
        //     foreach($tblSectiondataRMD as $t)
        //     {
        //         $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $t->km_from, $t->m_from, $t->km_to, $t->m_to);
        //         if ($check == 'overlap_front')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataRMD;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $t->km_from;
        //             $tblSectiondataMH1->m_from = $t->m_from;
        //             $tblSectiondataMH1->km_to = $s->km_from;
        //             $tblSectiondataMH1->m_to = $s->m_from;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->terrian_type_id = $t->terrian_type_id;
        //             $tblSectiondataMH1->road_class_id = $t->road_class_id;
        //             $tblSectiondataMH1->lane_width = $t->lane_width;
        //             $tblSectiondataMH1->no_lane = $t->no_lane;
        //             $tblSectiondataMH1->construct_year = $t->construct_year;
        //             $tblSectiondataMH1->service_start_year = $t->service_start_year;
        //             $tblSectiondataMH1->temperature = $t->temperature;
        //             $tblSectiondataMH1->annual_precipitation = $t->annual_precipitation;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_from = $s->km_from;
        //             $t->m_from = $s->m_from;
        //             $t->save();
        //         }
        //         else if ($check == 'overlap_end')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataRMD;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->terrian_type_id = $t->terrian_type_id;
        //             $tblSectiondataMH1->road_class_id = $t->road_class_id;
        //             $tblSectiondataMH1->lane_width = $t->lane_width;
        //             $tblSectiondataMH1->no_lane = $t->no_lane;
        //             $tblSectiondataMH1->construct_year = $t->construct_year;
        //             $tblSectiondataMH1->service_start_year = $t->service_start_year;
        //             $tblSectiondataMH1->temperature = $t->temperature;
        //             $tblSectiondataMH1->annual_precipitation = $t->annual_precipitation;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $tblSectiondataMH1->km_from;
        //             $t->m_to = $tblSectiondataMH1->m_from;
        //             $t->save();
        //         }
        //         else if ($check == 'whole')
        //         {
        //             $t->segment_id = $segment->id;
        //             $t->save();
        //         }
        //     }
        //     $tblSectiondataTV = tblSectiondataTV::get();
        //     foreach ($tblSectiondataTV as $t)
        //     {
        //         $check = '';
        //         $length = max($t->m_station, $s->m_from, $s->m_to);
        //         $factor = pow(10, strlen($length));
        //         $start_in = $t->km_station*$factor + $t->m_station ;
        //         $start_first = $s->km_from*$factor + $s->m_from;
        //         $end_first = $s->km_to*$factor + $s->m_to;
        //         if ($start_first < $start_in && $start_in < $end_first)
        //         {
        //             $t->segment_id = $s->id;
        //             $t->save();
        //         }
        //     }
        //     Session()->flash('class', 'alert alert-success');
        //     Session()->flash('message', trans('back_end.edit_segment_success'));
        //     return redirect()->route('manager_segment.index');
        // }
        // else if ($check == 'case_short_2')
        // {
        //     $segment->km_from = $s->km_from;
        //     $segment->m_from = $s->m_from;
        //     $segment->km_to = $request->f_km;
        //     $segment->m_to = $request->f_m;
        //     $segment->save();
        //     $s->segname_en = $request->name_en;
        //     $s->segname_vn = $request->name_vn;
        //     $s->km_from = $request->f_km;
        //     $s->m_from = $request->f_m;
        //     $s->km_to = $request->t_km;
        //     $s->m_to = $request->t_m;
        //     $s->prfrom_id = $request->fro_pro;
        //     $s->prto_id = $request->fro_to;
        //     $s->distfrom_id = $request->dis_pro;
        //     $s->distto_id = $request->dis_to;
        //     $s->SB_id = $request->bureau;
        //     $s->commune_from = $request->commune_fro;
        //     $s->commune_to = $request->commune_to;
        //     $s->nullity_at = $s->effect_at;
        //     if ($request->year == null)
        //     {
        //         $s->effect_at = null;
        //     }
        //     else 
        //     {
        //         $s->effect_at = $request->year." 00:00:00";
        //     }
        //     $s->save();
        //     $tblSectiondataMH = tblSectiondataMH::where('segment_id', $id)->get();
        //     foreach ($tblSectiondataMH as $t)
        //     {
        //         $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $t->km_from, $t->m_from, $t->km_to, $t->m_to);
        //         if ($check == 'overlap_front')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataMH;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->completion_date = $t->completion_date;
        //             $tblSectiondataMH1->repair_duration = $t->repair_duration;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->total_width_repair_lane = $t->total_width_repair_lane;
        //             $tblSectiondataMH1->r_classification_id = $t->r_classification_id;
        //             $tblSectiondataMH1->r_structType_id = $t->r_structType_id;
        //             $tblSectiondataMH1->r_category_id = $t->r_category_id;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->distance = $t->distance;
        //             $tblSectiondataMH1->direction_running = $t->direction_running;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $s->km_to;
        //             $t->m_to = $s->m_to;
        //             $t->save();
        //         }
        //         else if ($check == 'overlap_end')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataMH;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->completion_date = $t->completion_date;
        //             $tblSectiondataMH1->repair_duration = $t->repair_duration;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->total_width_repair_lane = $t->total_width_repair_lane;
        //             $tblSectiondataMH1->r_classification_id = $t->r_classification_id;
        //             $tblSectiondataMH1->r_structType_id = $t->r_structType_id;
        //             $tblSectiondataMH1->r_category_id = $t->r_category_id;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->distance = $t->distance;
        //             $tblSectiondataMH1->direction_running = $t->direction_running;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $s->km_to;
        //             $t->m_to = $s->m_to;
        //             $t->save();
        //         }
        //     }
        //     $tblSectiondataRMD = tblSectiondataRMD::get();
        //     foreach($tblSectiondataRMD as $t)
        //     {
        //         $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $t->km_from, $t->m_from, $t->km_to, $t->m_to);
        //         if ($check == 'overlap_front')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataRMD;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $t->km_from;
        //             $tblSectiondataMH1->m_from = $t->m_from;
        //             $tblSectiondataMH1->km_to = $s->km_from;
        //             $tblSectiondataMH1->m_to = $s->m_from;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->terrian_type_id = $t->terrian_type_id;
        //             $tblSectiondataMH1->road_class_id = $t->road_class_id;
        //             $tblSectiondataMH1->lane_width = $t->lane_width;
        //             $tblSectiondataMH1->no_lane = $t->no_lane;
        //             $tblSectiondataMH1->construct_year = $t->construct_year;
        //             $tblSectiondataMH1->service_start_year = $t->service_start_year;
        //             $tblSectiondataMH1->temperature = $t->temperature;
        //             $tblSectiondataMH1->annual_precipitation = $t->annual_precipitation;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_from = $s->km_from;
        //             $t->m_from = $s->m_from;
        //             $t->save();
        //         }
        //         else if ($check == 'overlap_end')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataRMD;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->terrian_type_id = $t->terrian_type_id;
        //             $tblSectiondataMH1->road_class_id = $t->road_class_id;
        //             $tblSectiondataMH1->lane_width = $t->lane_width;
        //             $tblSectiondataMH1->no_lane = $t->no_lane;
        //             $tblSectiondataMH1->construct_year = $t->construct_year;
        //             $tblSectiondataMH1->service_start_year = $t->service_start_year;
        //             $tblSectiondataMH1->temperature = $t->temperature;
        //             $tblSectiondataMH1->annual_precipitation = $t->annual_precipitation;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $tblSectiondataMH1->km_from;
        //             $t->m_to = $tblSectiondataMH1->m_from;
        //             $t->save();
        //         }
        //     }
        //     $tblSectiondataTV = tblSectiondataTV::get();
        //     foreach ($tblSectiondataTV as $t)
        //     {
        //         $check = '';
        //         $length = max($t->m_station, $s->m_from, $s->m_to);
        //         $factor = pow(10, strlen($length));
        //         $start_in = $t->km_station*$factor + $t->m_station ;
        //         $start_first = $s->km_from*$factor + $s->m_from;
        //         $end_first = $s->km_to*$factor + $s->m_to;
        //         if ($start_first < $start_in && $start_in < $end_first)
        //         {
        //             $t->segment_id = $s->id;
        //             $t->save();
        //         }
        //     }
        //     Session()->flash('class', 'alert alert-success');
        //     Session()->flash('message', trans('back_end.edit_segment_success'));
        //     return redirect()->route('manager_segment.index');
            
        // }
        // else if ($check == 'case_short_3')
        // {
        //     $segment->km_from = $s->km_from;
        //     $segment->m_from = $s->m_from;
        //     $segment->km_to = $request->f_km;
        //     $segment->m_to = $request->f_m;
        //     $segment->save();
        //     $array_segment[] = $segment;
        //     $segment1 = new tblSegment;
        //     $segment1->segname_en = $s->segname_en;
        //     $segment1->segname_vn = $s->segname_vn;
        //     $segment1->branch_id = $s->branch_id;
        //     $segment1->prfrom_id = $s->prfrom_id;
        //     $segment1->prto_id = $s->prto_id;
        //     $segment1->distfrom_id = $s->distfrom_id;
        //     $segment1->distto_id = $s->distto_id;
        //     $segment1->SB_id = $s->SB_id;
        //     $segment1->commune_from = $s->commune_from;
        //     $segment1->commune_to = $s->commune_to;
        //     $segment1->nullity_at = $s->nullity_at;
        //     if ($request->year == null)
        //     {
        //         $segment1->effect_at = null;
        //     }
        //     else 
        //     {
        //         $segment1->effect_at = $request->year." 00:00:00";
        //     }
        //     $segment1->km_from = $request->t_km;
        //     $segment1->m_from = $request->t_m;
        //     $segment1->km_to = $s->km_to;
        //     $segment1->m_to = $s->m_to;
        //     $segment1->save();
        //     $s->segname_en = $request->name_en;
        //     $s->segname_vn = $request->name_vn;
        //     $s->km_from = $request->f_km;
        //     $s->m_from = $request->f_m;
        //     $s->km_to = $request->t_km;
        //     $s->m_to = $request->t_m;
        //     $s->prfrom_id = $request->fro_pro;
        //     $s->prto_id = $request->fro_to;
        //     $s->distfrom_id = $request->dis_pro;
        //     $s->distto_id = $request->dis_to;
        //     $s->SB_id = $request->bureau;
        //     $s->commune_from = $request->commune_fro;
        //     $s->commune_to = $request->commune_to;
        //     $s->nullity_at = $s->effect_at;
        //     if ($request->year == null)
        //     {
        //         $s->effect_at = null;
        //     }
        //     else 
        //     {
        //         $s->effect_at = $request->year." 00:00:00";
        //     }
        //     $s->save();
        //     $tblSectiondataMH = tblSectiondataMH::where('segment_id', $id)->get();
        //     foreach ($tblSectiondataMH as $t)
        //     {
        //         $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $t->km_from, $t->m_from, $t->km_to, $t->m_to);
        //         if ($check == 'overlap_front')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataMH;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->completion_date = $t->completion_date;
        //             $tblSectiondataMH1->repair_duration = $t->repair_duration;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->total_width_repair_lane = $t->total_width_repair_lane;
        //             $tblSectiondataMH1->r_classification_id = $t->r_classification_id;
        //             $tblSectiondataMH1->r_structType_id = $t->r_structType_id;
        //             $tblSectiondataMH1->r_category_id = $t->r_category_id;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->distance = $t->distance;
        //             $tblSectiondataMH1->direction_running = $t->direction_running;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $s->km_to;
        //             $t->m_to = $s->m_to;
        //             $t->save();
        //         }
        //         else if ($check == 'overlap_end')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataMH;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment1->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->completion_date = $t->completion_date;
        //             $tblSectiondataMH1->repair_duration = $t->repair_duration;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->total_width_repair_lane = $t->total_width_repair_lane;
        //             $tblSectiondataMH1->r_classification_id = $t->r_classification_id;
        //             $tblSectiondataMH1->r_structType_id = $t->r_structType_id;
        //             $tblSectiondataMH1->r_category_id = $t->r_category_id;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->distance = $t->distance;
        //             $tblSectiondataMH1->direction_running = $t->direction_running;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $s->km_to;
        //             $t->m_to = $s->m_to;
        //             $t->save();
        //         }
        //     }
        //     $tblSectiondataRMD = tblSectiondataRMD::get();
        //     foreach($tblSectiondataRMD as $t)
        //     {
        //         $check = $this->check_segment_update($s->km_from, $s->m_from, $s->km_to, $s->m_to, $t->km_from, $t->m_from, $t->km_to, $t->m_to);
        //         if ($check == 'overlap_front')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataRMD;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment->id;
        //             $tblSectiondataMH1->km_from = $t->km_from;
        //             $tblSectiondataMH1->m_from = $t->m_from;
        //             $tblSectiondataMH1->km_to = $s->km_from;
        //             $tblSectiondataMH1->m_to = $s->m_from;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->terrian_type_id = $t->terrian_type_id;
        //             $tblSectiondataMH1->road_class_id = $t->road_class_id;
        //             $tblSectiondataMH1->lane_width = $t->lane_width;
        //             $tblSectiondataMH1->no_lane = $t->no_lane;
        //             $tblSectiondataMH1->construct_year = $t->construct_year;
        //             $tblSectiondataMH1->service_start_year = $t->service_start_year;
        //             $tblSectiondataMH1->temperature = $t->temperature;
        //             $tblSectiondataMH1->annual_precipitation = $t->annual_precipitation;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_from = $s->km_from;
        //             $t->m_from = $s->m_from;
        //             $t->save();
        //         }
        //         else if ($check == 'overlap_end')
        //         {
        //             $tblSectiondataMH1 = new tblSectiondataRMD;
        //             $tblSectiondataMH1->sectiondata_id = $t->sectiondata_id;
        //             $tblSectiondataMH1->segment_id = $segment1->id;
        //             $tblSectiondataMH1->km_from = $s->km_to;
        //             $tblSectiondataMH1->m_from = $s->m_to;
        //             $tblSectiondataMH1->km_to = $t->km_to;
        //             $tblSectiondataMH1->m_to = $t->m_to;
        //             $tblSectiondataMH1->from_lat = $t->from_lat;
        //             $tblSectiondataMH1->from_lng = $t->from_lng;
        //             $tblSectiondataMH1->to_lat = $t->to_lat;
        //             $tblSectiondataMH1->to_lng = $t->to_lng;
        //             $tblSectiondataMH1->survey_time = $t->survey_time;
        //             $tblSectiondataMH1->direction = $t->direction;
        //             $tblSectiondataMH1->actual_length = $t->actual_length;
        //             $tblSectiondataMH1->lane_pos_number = $t->lane_pos_number;
        //             $tblSectiondataMH1->terrian_type_id = $t->terrian_type_id;
        //             $tblSectiondataMH1->road_class_id = $t->road_class_id;
        //             $tblSectiondataMH1->lane_width = $t->lane_width;
        //             $tblSectiondataMH1->no_lane = $t->no_lane;
        //             $tblSectiondataMH1->construct_year = $t->construct_year;
        //             $tblSectiondataMH1->service_start_year = $t->service_start_year;
        //             $tblSectiondataMH1->temperature = $t->temperature;
        //             $tblSectiondataMH1->annual_precipitation = $t->annual_precipitation;
        //             $tblSectiondataMH1->created_by = $t->created_by;
        //             $tblSectiondataMH1->updated_by = $t->updated_by;
        //             $tblSectiondataMH1->sb_id = $t->sb_id;
        //             $tblSectiondataMH1->branch_id = $t->branch_id;
        //             $tblSectiondataMH1->remark = $t->remark;
        //             $tblSectiondataMH1->created_at = $t->created_at;
        //             $tblSectiondataMH1->updated_at = $t->updated_at;
        //             $tblSectiondataMH1->save();
        //             $t->km_to = $tblSectiondataMH1->km_from;
        //             $t->m_to = $tblSectiondataMH1->m_from;
        //             $t->save();
        //         }
        //     }
        //     $tblSectiondataTV = tblSectiondataTV::get();
        //     foreach ($tblSectiondataTV as $t)
        //     {
        //         $check = '';
        //         $length = max($t->m_station, $s->m_from, $s->m_to);
        //         $factor = pow(10, strlen($length));
        //         $start_in = $t->km_station*$factor + $t->m_station ;
        //         $start_first = $s->km_from*$factor + $s->m_from;
        //         $end_first = $s->km_to*$factor + $s->m_to;
        //         if ($start_first < $start_in && $start_in < $end_first)
        //         {
        //             $t->segment_id = $s->id;
        //             $t->save();
        //         }
        //     }
            // Session()->flash('class', 'alert alert-success');
            // Session()->flash('message', trans('back_end.edit_segment_success'));
            // return redirect()->route('manager_segment.index');
        // }
    }

    /**
     * move RI, MH, TV from a segment to another segment
     * @param segfrom_id: number, ID of tblSegment
     * @param segto_id: number, ID of tblSegment
     * @param side of segmentFrom that loses RI, MH, TV, -1: left, 1: right
     */
    public function moveComponentToSegment($segfrom_id, $segto_id, $side)
    {
        $segfrom = tblSegment::find($segfrom_id);
        $need_move_rmds = tblSectiondataRMD::getOutsideBoundarySection($segfrom_id, $side);
        
        foreach ($need_move_rmds as $rec) 
        {
            if (\Helper::compareTwoPoint($rec->km_to, $rec->m_to, $segfrom->km_from, $segfrom->m_from) <= 0 || \Helper::compareTwoPoint($segfrom->km_to, $segfrom->m_to, $rec->km_from, $rec->m_from) <= 0)
            {
                // whole moving
                DB::table('tblSectiondata_RMD')
                    ->where('id', $rec->id)
                    ->update([
                        'segment_id' => $segto_id,
                        'effect_at' => $segfrom->effect_at,
                        'updated_by' => Auth::user()->id
                    ]);
            }   
            else
            {
                // partial moving
                $this->separateRMDSection($rec, $segfrom, $segto_id, $side);
            } 
        }

        $need_move_mhs = tblSectiondataMH::getOutsideBoundarySection($segfrom_id, $side);

        foreach ($need_move_mhs as $rec) 
        {
            if (\Helper::compareTwoPoint($rec->km_to, $rec->m_to, $segfrom->km_from, $segfrom->m_from) <= 0 || \Helper::compareTwoPoint($segfrom->km_to, $segfrom->m_to, $rec->km_from, $rec->m_from) <= 0)
            {
                // whole moving
                DB::table('tblSectiondata_MH')
                    ->where('id', $rec->id)
                    ->update([
                        'segment_id' => $segto_id,
                        'effect_at' => $segfrom->effect_at,
                        'updated_by' => Auth::user()->id
                    ]);
            }   
            else
            {
                // partial moving
                $this->separateMHSection($rec, $segfrom, $segto_id, $side);
            } 
        }

        $need_move_tvs = tblSectiondataTV::getOutsideBoundarySection($segfrom_id, $side);
        foreach ($need_move_tvs as $rec) 
        {
            DB::table('tblSectiondata_TV')
                ->where('id', $rec->id)
                ->update([
                    'segment_id' => $segto_id,
                    'effect_at' => $segfrom->effect_at,
                    'updated_by' => Auth::user()->id
                ]);
        }
    }

    /**
     * @param origin_rec: tblSectiondataRMD object
     * @param segfrom: tblSegment object
     */
    private function separateRMDSection($origin_rec, $segfrom, $segto_id, $side)
    {
        $new_rmd_left = new tblSectiondataRMD;
        $new_rmd_left->segment_id = ($side == -1) ? $segto_id : $segfrom->id;
        $new_rmd_left->terrian_type_id = $origin_rec->terrian_type_id;
        $new_rmd_left->road_class_id = $origin_rec->road_class_id;
        $new_rmd_left->from_lat = $origin_rec->from_lat;
        $new_rmd_left->from_lng = $origin_rec->from_lng;
        $new_rmd_left->to_lat = $origin_rec->to_lat;
        $new_rmd_left->to_lng = $origin_rec->to_lng;
        $new_rmd_left->km_from = $origin_rec->km_from;
        $new_rmd_left->m_from = $origin_rec->m_from;
        $new_rmd_left->km_to = ($side == -1) ? $segfrom->km_from : $segfrom->km_to;
        $new_rmd_left->m_to = ($side == -1) ? $segfrom->m_from : $segfrom->m_to;
        $new_rmd_left->survey_time = $origin_rec->survey_time;
        $new_rmd_left->direction = $origin_rec->direction;
        $new_rmd_left->lane_pos_number = $origin_rec->lane_pos_number;
        $new_rmd_left->lane_width = $origin_rec->lane_width;
        $new_rmd_left->no_lane = $origin_rec->no_lane;
        $new_rmd_left->construct_year = $origin_rec->construct_year;
        $new_rmd_left->service_start_year = $origin_rec->service_start_year;
        $new_rmd_left->temperature = $origin_rec->temperature;
        $new_rmd_left->annual_precipitation = $origin_rec->annual_precipitation;
        $new_rmd_left->actual_length = $origin_rec->actual_length;
        $new_rmd_left->created_by = Auth::user()->id;
        $new_rmd_left->remark = $origin_rec->remark;
        $new_rmd_left->effect_at = $segfrom->effect_at;
        $new_rmd_left->save();
        $new_rmd_right = new tblSectiondataRMD;
        $new_rmd_right->segment_id = ($side == -1) ? $segfrom->id : $segto_id;
        $new_rmd_right->terrian_type_id = $origin_rec->terrian_type_id;
        $new_rmd_right->road_class_id = $origin_rec->road_class_id;
        $new_rmd_right->from_lat = $origin_rec->from_lat;
        $new_rmd_right->from_lng = $origin_rec->from_lng;
        $new_rmd_right->to_lat = $origin_rec->to_lat;
        $new_rmd_right->to_lng = $origin_rec->to_lng;
        $new_rmd_right->km_to = $origin_rec->km_to;
        $new_rmd_right->m_to = $origin_rec->m_to;
        $new_rmd_right->km_from = ($side == -1) ? $segfrom->km_from : $segfrom->km_to;
        $new_rmd_right->m_from = ($side == -1) ? $segfrom->m_from : $segfrom->m_to;
        $new_rmd_right->survey_time = $origin_rec->survey_time;
        $new_rmd_right->direction = $origin_rec->direction;
        $new_rmd_right->lane_pos_number = $origin_rec->lane_pos_number;
        $new_rmd_right->lane_width = $origin_rec->lane_width;
        $new_rmd_right->no_lane = $origin_rec->no_lane;
        $new_rmd_right->construct_year = $origin_rec->construct_year;
        $new_rmd_right->service_start_year = $origin_rec->service_start_year;
        $new_rmd_right->temperature = $origin_rec->temperature;
        $new_rmd_right->annual_precipitation = $origin_rec->annual_precipitation;
        $new_rmd_right->actual_length = $origin_rec->actual_length;
        $new_rmd_right->created_by = Auth::user()->id;
        $new_rmd_right->remark = $origin_rec->remark;
        $new_rmd_right->effect_at = $segfrom->effect_at;
        $new_rmd_right->save();

        $tmp = tblSectiondataRMD::findOrFail($origin_rec->id);
        $tmp->nullity_at = $segfrom->effect_at;
        $tmp->updated_by = Auth::user()->id;
        $tmp->save();
        $tmp->delete();
    }

    /**
     * @param origin_rec: tblSectiondataMH object
     * @param segfrom: tblSegment object
     */
    private function separateMHSection($origin_rec, $segfrom, $segto_id, $side)
    {
        $new_mh_left = new tblSectiondataMH;
        $new_mh_left->segment_id = ($side == -1) ? $segto_id : $segfrom->id;
        $new_mh_left->from_lat = $origin_rec->from_lat;
        $new_mh_left->from_lng = $origin_rec->from_lng;
        $new_mh_left->to_lat = $origin_rec->to_lat;
        $new_mh_left->to_lng = $origin_rec->to_lng;
        $new_mh_left->km_from = $origin_rec->km_from;
        $new_mh_left->m_from = $origin_rec->m_from;
        $new_mh_left->km_to = ($side == -1) ? $segfrom->km_from : $segfrom->km_to;
        $new_mh_left->m_to = ($side == -1) ? $segfrom->m_from : $segfrom->m_to;
        $new_mh_left->survey_time = $origin_rec->survey_time;
        $new_mh_left->completion_date = $origin_rec->completion_date;
        $new_mh_left->repair_duration = $origin_rec->repair_duration;
        $new_mh_left->direction = $origin_rec->direction;
        $new_mh_left->lane_pos_number = $origin_rec->lane_pos_number;
        $new_mh_left->total_width_repair_lane = $origin_rec->total_width_repair_lane;
        $new_mh_left->r_classification_id = $origin_rec->r_classification_id;
        $new_mh_left->r_structType_id = $origin_rec->r_structType_id;
        $new_mh_left->r_category_id = $origin_rec->r_category_id;
        $new_mh_left->distance = $origin_rec->distance;
        $new_mh_left->direction_running = $origin_rec->direction_running;
        $new_mh_left->actual_length = $origin_rec->actual_length;
        $new_mh_left->created_by = Auth::user()->id;
        $new_mh_left->remark = $origin_rec->remark;
        $new_mh_left->effect_at = $segfrom->effect_at;
        $new_mh_left->save();
        $new_mh_right = new tblSectiondataMH;
        $new_mh_right->segment_id = ($side == -1) ? $segfrom->id : $segto_id;
        $new_mh_right->from_lat = $origin_rec->from_lat;
        $new_mh_right->from_lng = $origin_rec->from_lng;
        $new_mh_right->to_lat = $origin_rec->to_lat;
        $new_mh_right->to_lng = $origin_rec->to_lng;
        $new_mh_right->km_to = $origin_rec->km_to;
        $new_mh_right->m_to = $origin_rec->m_to;
        $new_mh_right->km_from = ($side == -1) ? $segfrom->km_from : $segfrom->km_to;
        $new_mh_right->m_from = ($side == -1) ? $segfrom->m_from : $segfrom->m_to;
        $new_mh_right->survey_time = $origin_rec->survey_time;
        $new_mh_right->completion_date = $origin_rec->completion_date;
        $new_mh_right->repair_duration = $origin_rec->repair_duration;
        $new_mh_right->direction = $origin_rec->direction;
        $new_mh_right->lane_pos_number = $origin_rec->lane_pos_number;
        $new_mh_right->total_width_repair_lane = $origin_rec->total_width_repair_lane;
        $new_mh_right->r_classification_id = $origin_rec->r_classification_id;
        $new_mh_right->r_structType_id = $origin_rec->r_structType_id;
        $new_mh_right->r_category_id = $origin_rec->r_category_id;
        $new_mh_right->distance = $origin_rec->distance;
        $new_mh_right->direction_running = $origin_rec->direction_running;
        $new_mh_right->actual_length = $origin_rec->actual_length;
        $new_mh_right->created_by = Auth::user()->id;
        $new_mh_right->remark = $origin_rec->remark;
        $new_mh_right->effect_at = $segfrom->effect_at;
        $new_mh_right->save();

        $tmp = tblSectiondataMH::findOrFail($origin_rec->id);
        $tmp->nullity_at = $segfrom->effect_at;
        $tmp->updated_by = Auth::user()->id;
        $tmp->save();
        $tmp->delete();
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
    
    public function delete($id)
    {
        $manager = tblSegment::find($id);
        $manager->delete();
        $history = tblSegmentHistory::where('segment_id', $id)->first();
        $history->updated_by = Auth::user()->id;
        $history->updated_at = date("Y-m-d h:i:s");
        Session()->flash('class', 'alert alert-success');
        Session()->flash('message', trans('back_end.delete_segment_success'));
        return redirect()->back()->withInput();
    }
	
	/**
     * show list segment
     * @param 
     * @return list segment
     */
	public function getListSegment($action, $segment_id, Request $request)
    {
        // if(Auth::user()->hasPermission("admin_management.view"))
        // {
        	if (isset($request->success))
        	{
				Session::flash('class', 'alert alert-success');
				Session::flash('message', ($action == 'merge') ? trans('segment.merge_success') : trans('segment.split_success'));
			}
        	$branch = tblSegment::find($segment_id);
			$sb = @$branch->tblOrganization()->first();
			$sb_name = '';
			$rmb_name = '';
			if (App::getLocale() == 'en')
			{
				if (isset($sb))
				{
					$sb_name = $sb->name_en;
					$rmb = $sb->rmb;
					$rmb_name = (isset($rmb)) ? $rmb->name_en : trans('system.waiting_data_update');
				}
				else
				{
					$sb_name = $rmb_name = trans('system.waiting_data_update');
				}
			}
			else
			{
				if (isset($sb))
				{
					$sb_name = $sb->name_vn;
					$rmb = $sb->rmb;
					$rmb_name = (isset($rmb)) ? $rmb->name_vn : trans('system.waiting_data_update');
				}
				else
				{
					$sb_name = $rmb_name = trans('system.waiting_data_update');
				}
			}
			$title = $rmb_name.' - '.$sb_name;
			$view = ($action == 'merge') ? 'admin.segment.merge' : 'admin.segment.split';
            return view($view)->with(array(
            	'case' => 'merge_segment',
            	'branch_id' => $branch->branch_id,
            	'sb_id' => $branch->SB_id,
            	'custom_branch' => 'branch_id='.$branch->branch_id,
            	'custom_sb' => 'sb_id='.$branch->SB_id,
            	'title' => $title
			));
        // }
        // else
        // {
        	// return redirect('/user/logout');
        // }
    }
	
	/**
     * merger segment
     * @param Request $request
     * @return code 200 = success or 901 == error
     */
	public function postMergeSegment(Request $request)
    {
        $data = array();
        foreach ($request->segment as $key => $value)
        {
            $segment = tblSegment::find($value);
            $data[] = array(
                'km_from' => $segment->km_from,
                'km_to' => $segment->km_to,
                'm_from' => $segment->m_from,
                'm_to' => $segment->m_to,
                'segment_id' => $segment->id,
            );
        }
      
        usort($data, array($this, 'sortByOrder'));
        $check = tblSegment::check_exit_segment($data, $request->branch_id, $request->sb_id);
        if ($check)
        {
            $old = tblSegment::find($data[0]['segment_id']);
            $user_id = Auth::user()->id;
       
            $new = new tblSegment;
            $new->branch_id = $request->branch_id;
            $new->segname_en = $request->name_en;
            $new->segname_vn = $request->name_vi;
            $new->km_from = $data[0]['km_from'];
            $new->m_from = $data[0]['m_from'];
            $new->km_to = $data[count($data) - 1]['km_to'];
            $new->m_to = $data[count($data) - 1]['m_to'];
            $new->prfrom_id = $old->prfrom_id;
            $new->prto_id = $old->prto_id;
            $new->distfrom_id = $old->distfrom_id;
            $new->distto_id = $old->distto_id;
            $new->SB_id = $old->SB_id;
            $new->commune_from = $old->commune_from;
            $new->commune_to = $old->commune_to;
            $new->created_by = $user_id;
            $new->effect_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
            $new->save();
       
            $log_merge = new tblMergeSplit;
            $log_merge->user_id = $user_id;
            $log_merge->action = 1;
            $log_merge->object = 1;
            $log_merge->save();
       
	        foreach ($request->segment as $key => $value)
	        {
	        	$segment_old = tblSegment::find($value);
	        	$segment_old->nullity_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
				$segment_old->save();
				
	            $segment_rmd = tblSectiondataRMD::where('segment_id', $value)
	                                            ->update(array(
	                                             'segment_id' => $new->id,
	                                             'updated_by'=> $user_id
	                                            ));
	                
	            $segment_mh = tblSectiondataMH::where('segment_id', $value)
	                                            ->update(array(
	                                             'segment_id' => $new->id,
	                                             'updated_by' => $user_id
	                                            ));
	                
	            $segment_tv = tblSectiondataTV::where('segment_id', $value)
	                                            ->update(array(
	                                             'segment_id' => $new->id,
	                                             'updated_by'=> $user_id
	                                            ));   
	                 
	            $log_merge_detail = new tblMergeSplitDetail;
	            $log_merge_detail->merge_split_id = $log_merge->id;
	            $log_merge_detail->from = $value;
	            $log_merge_detail->to = $new->id;
	            $log_merge_detail->save();
	                     
	            $segment_old->delete();
	        }
       		
			Session()->flash('class', 'alert alert-success');
			Session()->flash('message', trans('segment.merge_success'));
			
            return response()->json(array(
                'code' => 200,
                'description' => 'success',
                'segment_id' => $new->id,
            ));
        }
        else
        {
            return response()->json(array(
                'code' => 901,
                'description' => trans('validation.recheck_data_merge')
            ));
        }
    }
	
	/**
     * split segment
     * @param Request $request
     * @return code 200 = success or 901 == error
     */
	public function postSplitSegment(Request $request)
	{
		$check_overlap = tblSegment::check_data_overlap($request->km_first, $request->m_first, $request->km_mid, $request->m_mid, $request->km_last, $request->m_last);
		if (!$check_overlap)
		{
			return response()->json(array(
				'code' => 901,
				'description' => trans('validation.recheck_data_merge')
			));
		}
		
		$user_id = Auth::user()->id;
		$segment_old = tblSegment::find($request->segment_id);
		$segment_old->nullity_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
		$segment_old->save();
		
		// INSERTS new segment
		$array_segment = array(
			1 => array(
				'km_from' => $request->km_first,
				'm_from' => $request->m_first,
				'km_to' => $request->km_mid,
				'm_to' => $request->m_mid,
			),
			2 => array(
				'km_from' => $request->km_mid,
				'm_from' => $request->m_mid,
				'km_to' => $request->km_last,
				'm_to' => $request->m_last,
			),
		);
		
		foreach ($array_segment as $key => $value)
		{
			${'new_segment_'.$key} = new tblSegment;
			${'new_segment_'.$key}->branch_id = $segment_old->branch_id;
	        ${'new_segment_'.$key}->segname_en = $request->{'name_en_segment_'.$key};
	        ${'new_segment_'.$key}->segname_vn = $request->{'name_vi_segment_'.$key};
			${'new_segment_'.$key}->km_from = $value['km_from'];
	        ${'new_segment_'.$key}->m_from = $value['m_from'];
	        ${'new_segment_'.$key}->km_to = $value['km_to'];
	        ${'new_segment_'.$key}->m_to = $value['m_to'];
	        ${'new_segment_'.$key}->prfrom_id = $segment_old->prfrom_id;
	        ${'new_segment_'.$key}->prto_id = $segment_old->prto_id;
	        ${'new_segment_'.$key}->distfrom_id = $segment_old->distfrom_id;
	        ${'new_segment_'.$key}->distto_id = $segment_old->distto_id;
	        ${'new_segment_'.$key}->SB_id = $segment_old->SB_id;
	        ${'new_segment_'.$key}->commune_from = $segment_old->commune_from;
	        ${'new_segment_'.$key}->commune_to = $segment_old->commune_to;
	        ${'new_segment_'.$key}->created_by = $user_id;
			${'new_segment_'.$key}->effect_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
	        ${'new_segment_'.$key}->save();
		}
		
		$check_rmd = tblSegment::check_point_middel('tblSectiondataRMD', $request->km_mid, $request->m_mid, $segment_old->id);
		if ($check_rmd->count() > 0)
		{
			foreach ($check_rmd as $p)
			{
				$old = tblSectiondataRMD::find($p->id);
				foreach ($array_segment as $key => $value)
				{
					${'segment_rmd_'.$key} = new tblSectiondataRMD;
					${'segment_rmd_'.$key}->segment_id = ${'new_segment_'.$key}->id;
					${'segment_rmd_'.$key}->terrian_type_id = $old->terrian_type_id;
					${'segment_rmd_'.$key}->road_class_id = $old->road_class_id;
					${'segment_rmd_'.$key}->from_lat = $old->from_lat;
					${'segment_rmd_'.$key}->from_lng = $old->from_lng;
					${'segment_rmd_'.$key}->to_lat = $old->to_lat;
					${'segment_rmd_'.$key}->to_lng = $old->to_lng;
					if ($key == 1)
					{
						${'segment_rmd_'.$key}->km_from = $old->km_from;
						${'segment_rmd_'.$key}->m_from = $old->m_from;
						${'segment_rmd_'.$key}->km_to = $value['km_to'];
						${'segment_rmd_'.$key}->m_to = $value['m_to'];						
					}
					else
					{
						${'segment_rmd_'.$key}->km_from = $value['km_from'];
						${'segment_rmd_'.$key}->m_from = $value['m_from'];
						${'segment_rmd_'.$key}->km_to = $old->km_to;
						${'segment_rmd_'.$key}->m_to = $old->m_to;
					}
					${'segment_rmd_'.$key}->survey_time = $old->survey_time;
					${'segment_rmd_'.$key}->direction = $old->direction;
					${'segment_rmd_'.$key}->lane_pos_number = $old->lane_pos_number;
					${'segment_rmd_'.$key}->lane_width = $old->lane_width;
					${'segment_rmd_'.$key}->no_lane = $old->no_lane;
					${'segment_rmd_'.$key}->construct_year = $old->construct_year;
					${'segment_rmd_'.$key}->service_start_year = $old->service_start_year;
					${'segment_rmd_'.$key}->temperature = $old->temperature;
					${'segment_rmd_'.$key}->annual_precipitation = $old->annual_precipitation;
					${'segment_rmd_'.$key}->actual_length = $old->actual_length;
					${'segment_rmd_'.$key}->created_by = $user_id;
					${'segment_rmd_'.$key}->remark = $old->remark;
					${'segment_rmd_'.$key}->save();
				}
				$old->delete();
			}			
		}
		
		$check_mh = tblSegment::check_point_middel('tblSectiondataMH', $request->km_mid, $request->m_mid, $segment_old->id);
		if ($check_mh->count() > 0)
		{
			foreach ($check_mh as $p)
			{
				$old = tblSectiondataMH::find($p->id);
				foreach ($array_segment as $key => $value)
				{
					${'segment_mh_'.$key} = new tblSectiondataMH;
					${'segment_mh_'.$key}->segment_id = ${'new_segment_'.$key}->id;
					if ($key == 1)
					{
						${'segment_mh_'.$key}->km_from = $old->km_from;
						${'segment_mh_'.$key}->m_from = $old->m_from;
						${'segment_mh_'.$key}->km_to = $value['km_to'];
						${'segment_mh_'.$key}->m_to = $value['m_to'];						
					}
					else
					{
						${'segment_mh_'.$key}->km_from = $value['km_from'];
						${'segment_mh_'.$key}->m_from = $value['m_from'];
						${'segment_mh_'.$key}->km_to = $old->km_to;
						${'segment_mh_'.$key}->m_to = $old->m_to;
					}
					${'segment_mh_'.$key}->from_lat = $old->from_lat;
					${'segment_mh_'.$key}->from_lng = $old->from_lng;
					${'segment_mh_'.$key}->to_lat = $old->to_lat;
					${'segment_mh_'.$key}->to_lng = $old->to_lng;
					${'segment_mh_'.$key}->survey_time = $old->survey_time;
					${'segment_mh_'.$key}->completion_date = $old->completion_date;
					${'segment_mh_'.$key}->repair_duration = $old->repair_duration;
					${'segment_mh_'.$key}->direction = $old->direction;
					${'segment_mh_'.$key}->actual_length = $old->actual_length;
					${'segment_mh_'.$key}->lane_pos_number = $old->lane_pos_number;
					${'segment_mh_'.$key}->total_width_repair_lane = $old->total_width_repair_lane;
					${'segment_mh_'.$key}->r_classification_id = $old->r_classification_id;
					${'segment_mh_'.$key}->r_structType_id = $old->r_structType_id;
					${'segment_mh_'.$key}->r_category_id = $old->r_category_id;
					${'segment_mh_'.$key}->created_by = $user_id;
					${'segment_mh_'.$key}->updated_by = $user_id;
					${'segment_mh_'.$key}->distance = $old->distance;
					${'segment_mh_'.$key}->direction_running = $old->direction_running;
					${'segment_mh_'.$key}->remark = $old->remark;
					${'segment_mh_'.$key}->save();
				}
				$old->delete();
			}			
		}

		$check_tv = tblSegment::check_point_middel('tblSectiondataTV', $request->km_mid, $request->m_mid, $segment_old->id);
		if (isset($check_tv))
		{
			$check_tv->segment_id = $new_segment_2->id;
			$check_tv->updated_by= $user_id;
			$check_tv->save();
		}
		
		$log_merge = new tblMergeSplit;
		$log_merge->user_id = $user_id;
		$log_merge->action = 2;
		$log_merge->object = 1;
		$log_merge->save();
		
		foreach ($array_segment as $key => $value)
		{
			$segment_rmd = tblSectiondataRMD::where('segment_id', $segment_old->id);
			if ($key == 1)
			{
				$segment_rmd = $segment_rmd->where(function ($query) use ($value) {
										$query->where(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>=', $value['m_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<', $value['m_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>=', $value['m_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<', $value['m_to']);
										});
									});
			}
			else
			{
				$segment_rmd = $segment_rmd->where(function ($query) use ($value) {
										$query->where(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>', $value['m_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<=', $value['m_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>', $value['m_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<=', $value['m_to']);
										});
									});
			}
									
			$segment_rmd = $segment_rmd->update(array(
				'segment_id' => ${'new_segment_'.$key}->id,
				'updated_by'=> $user_id
			));
			
			$segment_mh = tblSectiondataMH::where('segment_id', $segment_old->id);
			if ($key == 1)
			{
				$segment_mh = $segment_mh->where(function ($query) use ($value) {
										$query->where(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>=', $value['m_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<', $value['m_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>=', $value['m_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<', $value['m_to']);
										});
									});
			}
			else
			{
				$segment_mh = $segment_mh->where(function ($query) use ($value) {
										$query->where(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>', $value['m_from'])
												->where('km_to', '<', $value['km_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', '>', $value['km_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<=', $value['m_to']);
										})
										->orwhere(function ($sql) use ($value) {
											$sql->where('km_from', $value['km_from'])
												->where('m_from', '>', $value['m_from'])
												->where('km_to', $value['km_to'])
												->where('m_to', '<=', $value['m_to']);
										});
									});
			}
									
			$segment_mh = $segment_mh->update(array(
				'segment_id' => ${'new_segment_'.$key}->id,
				'updated_by'=> $user_id
			));
											
			$segment_tv = tblSectiondataTV::where('segment_id', $segment_old->id);
			if ($key == 1)
			{
				$segment_tv = $segment_tv->where(function ($query) use ($value) {
					$query->where(function ($sql) use ($value) {
						$sql->where('km_station', '>', $value['km_from'])
							->where('km_station', '<', $value['km_to']);	
					})
					->orwhere(function ($sql) use ($value) {
						$sql->where('km_station', $value['km_from'])
							->where('km_station', '<', $value['km_to'])
							->where('m_station', '>=', $value['m_from']);	
					})
					->orwhere(function ($sql) use ($value) {
						$sql->where('km_station', '>',$value['km_from'])
							->where('km_station', $value['km_to'])
							->where('m_station', '<', $value['m_to']);	
					});
				});											
			}
			else
			{
				$segment_tv = $segment_tv->where(function ($query) use ($value) {
					$query->where(function ($sql) use ($value) {
						$sql->where('km_station', '>', $value['km_from'])
							->where('km_station', '<', $value['km_to']);	
					})
					->orwhere(function ($sql) use ($value) {
						$sql->where('km_station', $value['km_from'])
							->where('km_station', '<', $value['km_to'])
							->where('m_station', '>', $value['m_from']);	
					})
					->orwhere(function ($sql) use ($value) {
						$sql->where('km_station', '>',$value['km_from'])
							->where('km_station', $value['km_to'])
							->where('m_station', '<=', $value['m_to']);	
					});
				});			
			}
			$segment_tv = $segment_tv->update(array(
				'segment_id' => ${'new_segment_'.$key}->id,
				'updated_by'=> $user_id
			));							
			
			$log_merge_detail = new tblMergeSplitDetail;
			$log_merge_detail->merge_split_id = $log_merge->id;
			$log_merge_detail->from = $segment_old->id;
			$log_merge_detail->to = ${'new_segment_'.$key}->id;
			$log_merge_detail->save();
		}

		$segment_old->delete();
		
		Session()->flash('class', 'alert alert-success');
		Session()->flash('message', trans('segment.split_success'));
		return response()->json(array(
			'code' => 200,
			'description' => 'success',
		));
	}
	
	/**
     * Sort Multi-dimensional Array by Value
     * @param $a, $b array data
     * @return value -1, 0, 1for sorting
     */
	public function sortByOrder($a, $b) {
		if ($a['km_from'] == $b['km_from'])
		{
			return ($a['m_from'] > $b['m_from']) ? 1 : -1;
		}
	    return $a['km_from'] - $b['km_from'];
	}
	
    // return segment list in json format
	public function getSegmentsInBranch(Request $request)
	{
        try 
        {
            $segment = tblSegment::where('branch_id', $request->branch_id)
                ->where('id', '<>', $request->segment_id)
                ->orderBy('km_from', 'asc')
                ->orderBy('m_from', 'asc')
                ->get();    
            
            return response()->json(array(
                'code' => 200,
                'data' => $segment
            ));
        } 
        catch (Exception $e)
        {
            return response()->json(array(
                'code' => 0,
                'description' => $e->getMessage()
            ));
        }
	}
	
    function get_header_Branch(Request $request)
    {
        if($request->route != 0)
        {
            if(App::getLocale() == 'en')
            {
                $tblBranch = tblBranch::where('route_id', $request->route)->select('name_en as name', 'id')->get();
            }
            else
            {
                $tblBranch = tblBranch::where('route_id', $request->route)->select('name_vn as name', 'id')->get();
            }
        }
        else
        {
            if(App::getLocale() == 'en')
            {
                $tblBranch = tblBranch::select('name_en as name', 'id')->get();
            }
            else
            {
                $tblBranch = tblBranch::select('name_vn as name', 'id')->get();
            }
        }
        return $tblBranch;
    }

    function get_header_SB(Request $request)
    {
        if($request->rmb != 0)
        {
            if (App::getLocale() == 'en')
            {
                $tblOrganization = tblOrganization::where('parent_id', $request->rmb)->select('name_en as name', 'id')->get();
            }
            else
            {
                $tblOrganization = tblOrganization::where('parent_id', $request->rmb)->select('name_vn as name', 'id')->get();
            }
        }
        else
        {
            if (App::getLocale() == 'en')
            {
                $tblOrganization = tblOrganization::select('name_en as name', 'id')->whereNotNull('parent_id')->get();
            }
            else
            {
                $tblOrganization = tblOrganization::select('name_vn as name', 'id')->whereNotNull('parent_id')->get();
            }
        }
        return $tblOrganization;
    }
    
    public function getBureau($id)
    {
        $bureau = tblOrganization::find($id);
        $t = tblOrganization::where('parent_id', $bureau->id)->get();
        return $t;
    }
    
    public function getRoute($id)
    {
        $t = DB::table('tblOrganization')->join('tblSegment', 'tblSegment.SB_id', '=', 'tblOrganization.id')
                                         ->join('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id')
                                         ->join('tblRoad', 'tblRoad.id', '=', 'tblBranch.route_id')
                                         ->where('tblOrganization.id', '=',$id)
                                         ->select('tblBranch.route_id')
                                         ->groupBy('tblBranch.route_id')
                                         ->get();
        $id = array();
        $s = json_decode(json_encode($t),TRUE); 
        foreach($s as $key => $value )
        {
            foreach($value as $v => $a)
            {
                $id[] = $a;
            }
        }
        $t = tblRoad::find($id);
        return $t;
    }
    
    public function getBranch($id)
    {
        $t = DB::table('tblRoad')->join('tblBranch', 'tblBranch.route_id', '=', 'tblRoad.id')->where('tblRoad.id', $id)->get();
        return $t;
    }

    public function CheckSegmentExits(Request $request)
    {
        if (count(tblSectiondataMH::where('segment_id', $request->id)->get()) > 0)
        {
            return 1;
        }
        if (count(tblSectiondataRMD::where('segment_id', $request->id)->get()) > 0)
        {
            return 1;
        }
        if (count(tblSectiondataTV::where('segment_id', $request->id)->get()) > 0)
        {
            return 1;
        }
        return "error";
    }
    
    /**
     * @param km_f
     * @param m_f
     * @param km_t
     * @param m_t
     * @param branch_id
     * @param excluded segment id
     * @return list of involving segments
     */
    private function checkSegment($km_f, $m_f, $km_t, $m_t, $branch_id, $segment_id = -1)
    {
        $segs = tblSegment::getRelatedSegments($km_f, $m_f, $km_t, $m_t, $branch_id, $segment_id);
        
        if (\Helper::compareTwoPoint($km_f, $m_f, $km_t, $m_t) > 0)
        {
            return 'error_point_in';
        }
        $data = [];
        foreach ($segs as $rec) 
        {
            $tmp = [];
            $tmp['id'] = $rec->id;
            $tmp['name'] = (App::getLocale() == 'en') ? $rec->segname_en : $rec->segname_vn;       
            if (\Helper::compareTwoPoint($km_f, $m_f, $rec->km_from, $rec->m_from) <= 0 && \Helper::compareTwoPoint($km_t, $m_t, $rec->km_to, $rec->m_to) >= 0)
            {
                // this is whole overlapping case
                $tmp['status'] = 2;    
            }
            else
            {
                // partial overlapping case
                $tmp['status'] = 1;
            }
            $data[] = $tmp;
        }
        return $data;
    }
    
    public function postNeutral(Request $request)
    {
        $list_segment = $this->checkSegment($request->f_km, $request->f_m, $request->t_km, $request->t_m, $request->branch, $request->id);
        $segment = tblSegment::findOrFail($request->id);
        if ($list_segment == 'error_point_in')
        {
            return "error";
        }
        $data = [];
        foreach ($list_segment as $l)
        {
            if ($l['status'] == 1)
            {
                $data[] = "<tr><td>".$l['name']."</td><td>".trans('segment.partial')."</td><td>".trans('segment.description_partial')."</td><td>"."<a class='btn btn-block btn-primary' target='_blank' href='".
                '/manager_segment/'.$l['id'].'/edit'."'>".trans('segment.View_iformation')."</a>"."</td></tr>";
            }
            else if ($l['status'] == 2)
            {
                $data[] = "<tr><td>".$l['name']."</td><td>".trans('segment.whole')."</td><td>".trans('segment.description_whole')."</td><td>"."<a class='btn btn-block btn-primary' target='_blank' href='".
                '/manager_segment/'.$l['id'].'/edit'."'>".trans('segment.View_information')."</a>"."</td></tr>";
            }
        }
        $TV = tblSectiondataTV::where('segment_id', $request->id)->first();
        if (tblSectiondataMH::getOutsideBoundarySection($request->id, 1, $request->f_km, $request->f_m, $request->t_km, $request->t_m)->count() > 0)
        {
            $data[] = "<p>".trans('back_end.MH_gonna_be_lost_from').'Km'.$request->t_km."+".$request->t_m.trans('back_end.to').'Km'.$segment->km_to."+".$segment->m_to."</p>";
        }
        if (tblSectiondataRMD::getOutsideBoundarySection($request->id, 1, $request->f_km, $request->f_m, $request->t_km, $request->t_m)->count() > 0)
        {
            $data[] = "<p>".trans('back_end.RMD_gonna_be_lost_from').'Km'.$request->t_km."+".$request->t_m.trans('back_end.to').'Km'.$segment->km_to."+".$segment->m_to."</p>";
        }
        if (tblSectiondataTV::getOutsideBoundarySection($request->id, 1, $request->f_km, $request->f_m, $request->t_km, $request->t_m)->count() > 0)
        {
            $data[] = "<p>".trans('back_end.TV_gonna_be_lost_station').' Km'.$TV->km_station .'+'. $TV->m_station."</p>";
        }

        if (tblSectiondataMH::getOutsideBoundarySection($request->id, -1, $request->f_km, $request->f_m, $request->t_km, $request->t_m)->count() > 0)
        {
            $data[] = "<p>".trans('back_end.MH_gonna_be_lost_from').'Km'.$segment->km_from."+".$segment->m_from.trans('back_end.to').'Km'.$request->f_km."+".$request->f_m."</p>";
        }
        if (tblSectiondataRMD::getOutsideBoundarySection($request->id, -1, $request->f_km, $request->f_m, $request->t_km, $request->t_m)->count() > 0)
        {
            $data[] = "<p>".trans('back_end.RMD_gonna_be_lost_from').'Km'.$segment->km_from."+".$segment->m_from.trans('back_end.to').'Km'.$request->f_km."+".$request->f_m."</p>";
        }
        if (tblSectiondataTV::getOutsideBoundarySection($request->id, -1, $request->f_km, $request->f_m, $request->t_km, $request->t_m)->count() > 0)
        {
            $data[] = "<p>".trans('back_end.TV_gonna_be_lost_station').' Km'.$TV->km_station.'+'.$TV->m_station."</p>";
        }
        $from = 10000*$request->f_km + $request->f_m;
        $to = 10000*$request->t_km + $request->t_m;
        $segment_from = 10000*$segment->km_from + $segment->m_from;
        $segment_to = 10000*$segment->km_to + $segment->m_to;
        // if ($from == $segment_from && $to < $segment_to)
        // {
            // $data[] = "<p>".trans('segment.system_auto_add_segment_from')." ".$request->t_km."+".$request->t_m.trans('back_end.to').$segment->km_to."+".$segment->m_to."</p>";
        // }
        // else if ($from > $segment_from && $to == $segment_to)
        // {
            // $data[] = "<p>".trans('segment.system_auto_add_segment_from')." ".$segment->km_from."+".$segment->km_to.trans('back_end.to').$request->f_km."+".$request->f_m."</p>";
        // }
        // else if ($from > $segment_from && $to < $segment_to)
        // {
            // $data[] = "<p>".trans('segment.system_auto_add_segment_from')." ".$request->t_km."+".$request->t_m.trans('back_end.to').$segment->km_to."+".$segment->m_to."</p>";
            // $data[] = "<p>".trans('segment.system_auto_add_segment_from')." ".$segment->km_from."+".$segment->km_to.trans('back_end.to').$request->f_km."+".$request->f_m."</p>";
        // }
        return $data;
    }

    public function component(Request $request)
    {
        $segment = tblSectiondataMH::where('segment_id', $request->id_segment)->get();
        $data = [];
        $start_first = $request->f_km*10000 + $request->f_m;
        $end_first = $request->t_km*10000 + $request->t_m;
        foreach ($segment as $s)
        {
            $start = $s->km_from*10000 + $s->m_from;
            $end = $s->km_to*10000 + $s->m_to;
            if ($start < $start_first && $start_first < $end)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.RI_gonna_be_lost_from').$request->f_km."+".$request->f_m."+".trans('back_end.to').$s->km_from."+".$s->m_from."</label></div>";
            }
            if ($start_first < $end && $end < $end_first && $start_first > $start)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.RI_gonna_be_lost_from').$s->km_from."+".$s->m_from."+".trans('back_end.to').$request->t_km."+".$request->t_m."</label></div>";
            }
            if ($start_first < $start && $end < $end_first)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.RI_gonna_be_lost_from').$s->km_from."+".$s->m_from."+".trans('back_end.to').$s->km_to."+".$s->m_to."</label></div>";
            }
        }
        $segment = tblSectiondataRMD::where('segment_id', $request->id_segment)->get();
        foreach ($segment as $s)
        {
            $start = $s->km_from*10000 + $s->m_from;
            $end = $s->km_to*10000 + $s->m_to;
            if ($start < $start_first && $start_first < $end)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.RMD_gonna_be_lost_from').$request->f_km."+".$request->f_m."+".trans('back_end.to').$s->km_from."+".$s->m_from."</label></div>";
            }
            if ($start_first < $end && $end < $end_first && $start_first > $start)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.RMD_gonna_be_lost_from').$s->km_from."+".$s->m_from."+".trans('back_end.to').$request->t_km."+".$request->t_m."</label></div>";
            }
            if ($start_first < $start && $end < $end_first)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.RMD_gonna_be_lost_from').$s->km_from."+".$s->m_from."+".trans('back_end.to').$s->km_to."+".$s->m_to."</label></div>";
            }
        }
        $tblSectiondataTV = tblSectiondataTV::where('segment_id', $request->id_segment)->get();
        foreach ($tblSectiondataTV as $t)
        {
            $tv = $t->km_station*10000+$t->m_station;
            if ($start_first < $tv && $tv < $end_first)
            {
                $data[] = "<div class='modal-body'><label for='TV' class='control-label'>".trans('back_end.TV_gonna_be_lost_from').$request->f_km."+".$request->f_m."+".trans('back_end.to').$request->t_km."+".$request->t_m."</label></div>";
            }
        }
        if (count($data) > 0)
        {
            return $data;
        }
        else
        {
            return ['<div class="modal-body">
                    <label for="TV" class="control-label">'.
                    trans('back_end.no_data').'</label>
                </div>'];
        }
    }

    // H.ANH  05.12.2016  remove unused code
    // public function proceed(Request $request)
    // {
    //     $id = explode(',', $request->array_segment);
    //     $segment = tblSegment::find($request->id);
    //     $segment->km_from = $request->f_km;
    //     $segment->m_from = $request->f_m;
    //     $segment->km_to = $request->t_km;
    //     $segment->m_to = $request->t_m;
    //     $segment->save();
    //     $start_first = $segment->km_from*10000 + $segment->m_from;
    //     $end_first = $segment->km_to*10000 + $segment->m_to;
    //     foreach ($id as $i)
    //     {
    //         $tblSegment = tblSegment::find($i);
    //         $start = $tblSegment->km_from*10000 + $tblSegment->m_from;
    //         $end = $tblSegment->km_to*10000 + $tblSegment->m_to;
    //         if ($start_first <= $start && $end <= $end_first)
    //         {
    //             $tblSegment->delete();
    //         }
    //         else if ( $start_first > $start && $start_first < $end && $end_first > $end)
    //         {
    //             $tblSegment->km_to = $segment->km_from;
    //             $tblSegment->m_to = $segment->m_from;
    //             $tblSegment->save();
    //         }
    //         else if ( $start_first < $start && $start < $end_first && $end_first < $end)
    //         {
    //             $tblSegment->km_from = $segment->km_to;
    //             $tblSegment->m_from = $segment->m_to;
    //             $tblSegment->save();
    //         }
    //         //MH
    //         $tblSectiondataMH = tblSectiondataMH::where('segment_id', $i)->get();
    //         foreach ($tblSectiondataMH as $tbl)
    //         {
    //             $start = $tbl->km_from*10000 + $tbl->m_from;
    //             $end = $tbl->km_to*10000 + $tbl->m_to;
    //             if ($start_first <= $start && $end <= $end_first)
    //             {
    //                 $tbl->segment_id = $segment->id;
    //                 $tbl->save();
    //             }
    //             else if ($start_first < $start && $start < $end_first && $end_first < $end)
    //             {
    //                 $tblSectiondataMH1 = new tblSectiondataMH;
    //                 $tblSectiondataMH1->sectiondata_id = $tbl->sectiondata_id;
    //                 $tblSectiondataMH1->segment_id = $tblSegment->id;
    //                 $tblSectiondataMH1->km_from = $segment->km_to;
    //                 $tblSectiondataMH1->m_from = $segment->km_to;
    //                 $tblSectiondataMH1->km_to = $tbl->km_to;
    //                 $tblSectiondataMH1->m_to = $tbl->m_to;
    //                 $tblSectiondataMH1->from_lat = $tbl->from_lat;
    //                 $tblSectiondataMH1->from_lng = $tbl->from_lng;
    //                 $tblSectiondataMH1->to_lat = $tbl->to_lat;
    //                 $tblSectiondataMH1->to_lng = $tbl->to_lng;
    //                 $tblSectiondataMH1->survey_time = $tbl->survey_time;
    //                 $tblSectiondataMH1->direction = $tbl->direction;
    //                 $tblSectiondataMH1->actual_length = $tbl->actual_length;
    //                 $tblSectiondataMH1->lane_pos_number = $tbl->lane_pos_number;
    //                 $tblSectiondataMH1->terrian_type_id = $tbl->terrian_type_id;
    //                 $tblSectiondataMH1->road_class_id = $tbl->road_class_id;
    //                 $tblSectiondataMH1->lane_width = $tbl->latblne_width;
    //                 $tblSectiondataMH1->no_lane = $tbl->no_lane;
    //                 $tblSectiondataMH1->construct_year = $tbl->construct_year;
    //                 $tblSectiondataMH1->service_start_year = $tbl->service_start_year;
    //                 $tblSectiondataMH1->temperature = $tbl->temperature;
    //                 $tblSectiondataMH1->annual_precipitation = $tbl->annual_precipitation;
    //                 $tblSectiondataMH1->created_by = $tbl->created_by;
    //                 $tblSectiondataMH1->updated_by = $tbl->updated_by;
    //                 $tblSectiondataMH1->sb_id = $tbl->sb_id;
    //                 $tblSectiondataMH1->branch_id = $tbl->branch_id;
    //                 $tblSectiondataMH1->remark = $tbl->remark;
    //                 $tblSectiondataMH1->created_at = $tbl->created_at;
    //                 $tblSectiondataMH1->updated_at = $tbl->updated_at;
    //                 $tblSectiondataMH1->save();
    //                 $tbl->km_to = $segment->km_to;
    //                 $tbl->km_to = $segment->km_to;
    //                 $tbl->save();
    //             }
    //             else if ($start_first > $start && $start_first < $end && $end_first > $end)
    //             {
    //                 $tblSectiondataMH1 = new tblSectiondataMH;
    //                 $tblSectiondataMH1->sectiondata_id = $tbl->sectiondata_id;
    //                 $tblSectiondataMH1->segment_id = $tblSegment->id;
    //                 $tblSectiondataMH1->km_from = $segment->km_from;
    //                 $tblSectiondataMH1->m_from = $segment->m_from;
    //                 $tblSectiondataMH1->km_to = $tbl->km_to;
    //                 $tblSectiondataMH1->m_to = $tbl->m_to;
    //                 $tblSectiondataMH1->from_lat = $tbl->from_lat;
    //                 $tblSectiondataMH1->from_lng = $tbl->from_lng;
    //                 $tblSectiondataMH1->to_lat = $tbl->to_lat;
    //                 $tblSectiondataMH1->to_lng = $tbl->to_lng;
    //                 $tblSectiondataMH1->survey_time = $tbl->survey_time;
    //                 $tblSectiondataMH1->direction = $tbl->direction;
    //                 $tblSectiondataMH1->actual_length = $tbl->actual_length;
    //                 $tblSectiondataMH1->lane_pos_number = $tbl->lane_pos_number;
    //                 $tblSectiondataMH1->terrian_type_id = $tbl->terrian_type_id;
    //                 $tblSectiondataMH1->road_class_id = $tbl->road_class_id;
    //                 $tblSectiondataMH1->lane_width = $tbl->latblne_width;
    //                 $tblSectiondataMH1->no_lane = $tbl->no_lane;
    //                 $tblSectiondataMH1->construct_year = $tbl->construct_year;
    //                 $tblSectiondataMH1->service_start_year = $tbl->service_start_year;
    //                 $tblSectiondataMH1->temperature = $tbl->temperature;
    //                 $tblSectiondataMH1->annual_precipitation = $tbl->annual_precipitation;
    //                 $tblSectiondataMH1->created_by = $tbl->created_by;
    //                 $tblSectiondataMH1->updated_by = $tbl->updated_by;
    //                 $tblSectiondataMH1->sb_id = $tbl->sb_id;
    //                 $tblSectiondataMH1->branch_id = $tbl->branch_id;
    //                 $tblSectiondataMH1->remark = $tbl->remark;
    //                 $tblSectiondataMH1->created_at = $tbl->created_at;
    //                 $tblSectiondataMH1->updated_at = $tbl->updated_at;
    //                 $tblSectiondataMH1->save();
    //                 $tbl->km_to = $segment->km_from;
    //                 $tbl->km_to = $segment->km_from;
    //                 $tbl->save();
    //             }
    //         }
    //         //RMD
    //         $tblSectiondataMH = tblSectiondataRMD::where('segment_id', $i)->get();
    //         foreach ($tblSectiondataMH as $tbl)
    //         {
    //             $start = $tbl->km_from*10000 + $tbl->m_from;
    //             $end = $tbl->km_to*10000 + $tbl->m_to;
    //             if ($start_first <= $start && $end <= $end_first)
    //             {
    //                 $tbl->segment_id = $segment->id;
    //                 $tbl->save();
    //             }
    //             else if ($start_first < $start && $start < $end_first && $end_first < $end)
    //             {
    //                 $tblSectiondataMH1 = new tblSectiondataRMD;
    //                 $tblSectiondataMH1->sectiondata_id = $tbl->sectiondata_id;
    //                 $tblSectiondataMH1->segment_id = $tblSegment->id;
    //                 $tblSectiondataMH1->km_from = $segment->km_to;
    //                 $tblSectiondataMH1->m_from = $segment->m_to;
    //                 $tblSectiondataMH1->km_to = $tbl->km_to;
    //                 $tblSectiondataMH1->m_to = $tbl->m_to;
    //                 $tblSectiondataMH1->from_lat = $tbl->from_lat;
    //                 $tblSectiondataMH1->from_lng = $tbl->from_lng;
    //                 $tblSectiondataMH1->to_lat = $tbl->to_lat;
    //                 $tblSectiondataMH1->to_lng = $tbl->to_lng;
    //                 $tblSectiondataMH1->survey_time = $tbl->survey_time;
    //                 $tblSectiondataMH1->direction = $tbl->direction;
    //                 $tblSectiondataMH1->actual_length = $tbl->actual_length;
    //                 $tblSectiondataMH1->lane_pos_number = $tbl->lane_pos_number;
    //                 $tblSectiondataMH1->terrian_type_id = $tbl->terrian_type_id;
    //                 $tblSectiondataMH1->road_class_id = $tbl->road_class_id;
    //                 $tblSectiondataMH1->lane_width = $tbl->lane_width;
    //                 $tblSectiondataMH1->no_lane = $tbl->no_lane;
    //                 $tblSectiondataMH1->construct_year = $tbl->construct_year;
    //                 $tblSectiondataMH1->service_start_year = $tbl->service_start_year;
    //                 $tblSectiondataMH1->temperature = $tbl->temperature;
    //                 $tblSectiondataMH1->annual_precipitation = $tbl->annual_precipitation;
    //                 $tblSectiondataMH1->created_by = $tbl->created_by;
    //                 $tblSectiondataMH1->updated_by = $tbl->updated_by;
    //                 $tblSectiondataMH1->sb_id = $tbl->sb_id;
    //                 $tblSectiondataMH1->branch_id = $tbl->branch_id;
    //                 $tblSectiondataMH1->remark = $tbl->remark;
    //                 $tblSectiondataMH1->created_at = $tbl->created_at;
    //                 $tblSectiondataMH1->updated_at = $tbl->updated_at;
    //                 $tblSectiondataMH1->save();
    //                 $tbl->km_to = $segment->km_to;
    //                 $tbl->km_to = $segment->km_to;
    //                 $tbl->save();
    //             }
    //             else if ($start_first > $start && $start_first < $end && $end_first > $end)
    //             {
    //                 $tblSectiondataMH1 = new tblSectiondataRMD;
    //                 $tblSectiondataMH1->sectiondata_id = $tbltbl->sectiondata_id;
    //                 $tblSectiondataMH1->segment_id = $tblSegment->id;
    //                 $tblSectiondataMH1->km_from = $segment->km_from;
    //                 $tblSectiondataMH1->m_from = $segment->m_from;
    //                 $tblSectiondataMH1->km_to = $tbl->km_to;
    //                 $tblSectiondataMH1->m_to = $tbl->m_to;
    //                 $tblSectiondataMH1->from_lat = $tbl->from_lat;
    //                 $tblSectiondataMH1->from_lng = $tbl->from_lng;
    //                 $tblSectiondataMH1->to_lat = $tbl->to_lat;
    //                 $tblSectiondataMH1->to_lng = $tbl->to_lng;
    //                 $tblSectiondataMH1->survey_time = $tbl->survey_time;
    //                 $tblSectiondataMH1->direction = $tbl->direction;
    //                 $tblSectiondataMH1->actual_length = $tbl->actual_length;
    //                 $tblSectiondataMH1->lane_pos_number = $tbl->lane_pos_number;
    //                 $tblSectiondataMH1->terrian_type_id = $tbl->terrian_type_id;
    //                 $tblSectiondataMH1->road_class_id = $tbl->road_class_id;
    //                 $tblSectiondataMH1->lane_width = $tbl->lane_width;
    //                 $tblSectiondataMH1->no_lane = $tbl->no_lane;
    //                 $tblSectiondataMH1->construct_year = $tbl->construct_year;
    //                 $tblSectiondataMH1->service_start_year = $tbl->service_start_year;
    //                 $tblSectiondataMH1->temperature = $tbl->temperature;
    //                 $tblSectiondataMH1->annual_precipitation = $tbl->annual_precipitation;
    //                 $tblSectiondataMH1->created_by = $tbl->created_by;
    //                 $tblSectiondataMH1->updated_by = $tbl->updated_by;
    //                 $tblSectiondataMH1->sb_id = $tbl->sb_id;
    //                 $tblSectiondataMH1->branch_id = $tbl->branch_id;
    //                 $tblSectiondataMH1->remark = $tbl->remark;
    //                 $tblSectiondataMH1->created_at = $tbl->created_at;
    //                 $tblSectiondataMH1->updated_at = $tbl->updated_at;
    //                 $tblSectiondataMH1->save();
    //                 $tbl->km_to = $segment->km_from;
    //                 $tbl->km_to = $segment->km_from;
    //                 $tbl->save();
    //             }
    //         }
    //         //TV
    //         $tblSectiondataTV = tblSectiondataTV::where('segment_id', $i)->get();
    //         foreach ($tblSectiondataTV as $tbl)
    //         {
    //             $start = $tbl->km_from*10000 + $tbl->m_from;
    //             if ($start_first < $start && $start < $end_first)
    //             {
    //                 $tbl->segment_id = $segment->id;
    //             }
    //         }
    //     }
    //     Session()->flash('class', 'alert alert-success');
    //     Session()->flash('message', trans('back_end.edit_segment_success'));
    //     return redirect()->route('manager_segment.index');
    // }
}
