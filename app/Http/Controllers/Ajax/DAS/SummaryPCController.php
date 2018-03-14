<?php

namespace App\Http\Controllers\Ajax\DAS;

use App\Classes\Helper;
use App\Models\tblBranch;
use App\Models\tblConditionRank;
use App\Models\tblOrganization;
use App\Models\tblSectionPC;
use App\Models\tblSectionPCHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class SummaryPCController extends Controller
{
    var $distress_type = [
        '1' => 'cracking_ratio_total',
        '2' => 'rutting_depth_max',
        '3' => 'rutting_depth_ave',
        '4' => 'IRI',
        '5' => 'MCI',
    ];
    var $target_type = [
        '1' => 1,
        '2' => 2,
        '3' => 2,
        '4' => 3,
        '5' => 4,
    ];
    var $unit = [
        '1' => '%',
        '2' => 'mm',
        '3' => 'mm',
        '4' => 'mm/m',
        '5' => '',
    ];
    var $sheet = [
        '0' => '0' ,
        '1' => '1' ,
        '2' => '6' ,
        '3' => '7' ,
        '4' => '2' ,
        '5' => '3' ,
        '6' => '4' ,
        '7' => '5' ,
        '8' => '8' ,
        '9' => '9' ,
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConditionRank($request, $condition_rank, $i, $distress = false)
    {
        if ($distress)
        {
            $distress_type = $this->distress_type[$distress];
        }
        else
        {
            $distress_type = $this->distress_type[$request->distress];
        }
        $query = '';
        $j = count($condition_rank);
        foreach ($condition_rank as $cr)
        {
            $end = $i < $j ? ',' : '';
            if ($cr->from != null && $cr->to != null)
            {
                $query .=  'Sum(CASE WHEN '.$distress_type.' >= '.$cr->from.' AND '.$distress_type.' < '.$cr->to.' THEN section_length ELSE 0 END) as rank'.$i.$end;
            }
            else if ($cr->from == null)
            {
                $query .=  'Sum(CASE WHEN '.$distress_type.' < '.$cr->to.' THEN section_length ELSE 0 END) as rank'.$i.$end;
            }
            else
            {
                $query .=  'Sum(CASE WHEN '.$distress_type.' >= '.$cr->from.' THEN section_length ELSE 0 END) as rank'.$i.$end;
            }
            $i++;
        }
        return $query;
    }

    public function getYear($id)
    {
        $sb_id = tblOrganization::where('parent_id', $id)->pluck('id')->toArray();
        $year = tblSectionPCHistory::groupBy('date_y')
            ->orderBy('date_y', 'desc')->whereIn('SB_id', $sb_id)->pluck('date_y')->toArray();
        $data[] = ['value'=> 'latest', 'text' => trans('map.latest_year')];
        foreach ($year as $y)
        {
            $data[] = ['value'=> $y, 'text'=> $y];
        }
        return response($data);
    }

    public function getRoad($id)
    {
        $data[] = ['id'=> 'all', 'name' => trans('das.all')];
        $sb_id = tblOrganization::where('parent_id', $id)->pluck('id')->toArray();
        $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
        $branch = DB::table('tblBranch')->whereIn('id', $branch_id)->get();
        $lang = App::getLocale() == 'en' ? "name_en" : "name_vn";
        foreach ($branch as $r)
        {
            $data[] = ['id'=> $r->id, 'name'=> $r->$lang];
        }
        return response($data);
    }

    public function firstChart(Request $request){
        $label = [];
        $target_type = $this->target_type[$request->distress];
        $distress_type = $this->distress_type[$request->distress];
        $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        $condition_rank = DB::table('tblCondition_rank')->select('from','to')
            ->where('target_type', $target_type)->get();
        $query = $this->getConditionRank($request, $condition_rank,  1);
        if($request->year == 'latest')
        {
            $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
            $result = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC.branch_id')
                ->select(DB::raw($query . ',tblBranch.name_en as name_en, tblBranch.name_vn as name_vi,
                sum(section_length) / 1000 as branch_total, Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total'))->groupBy('branch_id')->get();
        }
        else
        {
            $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)->where('date_y', $request->year)
                ->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
            $result = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id')->where('date_y', $request->year)
                ->select(DB::raw($query.',tblBranch.name_en as name_en, tblBranch.name_vn as name_vi,
                sum(section_length) / 1000 as branch_total,Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total'))->groupBy('branch_id')->get();
        }
        foreach ($condition_rank as $c)
        {
            if ($c->from < 1) $c->from = 0;
            if($c->to != null)
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress] : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
            }
            else
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - ' : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
            }
        }
        $data = [];
        foreach ($result as $r)
        {
            foreach ($r as $key=>$value)
            {
                if ($key != 'branch_total' && $key != 'name_en'
                    && $key != 'name_vi' && $key != 'total')
                {
                    if ($value != 0)
                    {
                        $data[] = $r;
                        break;
                    }
                }
            }
        }
        return [$data, $label];
    }

    public function secondChart(Request $request)
    {
        $label = [];
        $data = (object)[];
        $target_type = $this->target_type[$request->distress];
        $distress_type = $this->distress_type[$request->distress];
        $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        $condition_rank = tblConditionRank::select('from','to')->where('target_type', $target_type)->get();
        $query = $this->getConditionRank($request, $condition_rank, 0);
        $num = 0;
        $j = count($condition_rank);
        for ($num; $num <= $j-1; $num++)
        {
            $data->$num = 0;
        }
//        $branch_id = tblBranch::orderBy('branch_number')->whereHas('segments.tblOrganization', function($query) use($request) {
//            $query->where('parent_id', $request->rmb);
//        })->pluck('id')->toArray();
        if ($request->year == 'latest')
        {
            if ($request->road == 'all')
            {
                $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                    ->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
                $result = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                    ->select(DB::raw($query.'sum(section_length) / 1000 as branch_total,
                    Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total'))
                    ->get();
            }
            else
            {
                $result = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', $request->road)
                ->select(DB::raw($query.'sum(section_length) / 1000 as branch_total,
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total'))->get();
            }
        }
        else
        {
            if ($request->road == 'all')
            {
                $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                    ->where('date_y', $request->year)->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
                $result = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('date_y', $request->year)
                    ->whereIn('branch_id', $branch_id)->select(DB::raw($query.'sum(section_length) / 1000 as branch_total,
                    Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total'))->get();
            }
            else
            {
                $result = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', $request->road)
                    ->where('date_y', $request->year)->select(DB::raw($query.' sum(section_length) / 1000 as branch_total,
                    Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total'))->get();
            }
        }

        foreach ($condition_rank as $c)
        {
            if ($c->from < 1) $c->from = 0;
            if($c->to != null)
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress] : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
            }
            else
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - ' : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
            }
        }
        $lang = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
        return [$label, $result, $request->road == 'all' ? tblOrganization::find($request->rmb)->$lang: tblBranch::find($request->road)->name];
    }

    public function thirdChartPC(Request $request)
    {
        $data = [];
        $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        $distress_type = $this->distress_type[$request->distress];
        /*$branch_segment = tblBranch::orderBy('branch_number')->whereHas('segments.tblOrganization', function($query) use($request) {
            $query->where('parent_id', $request->rmb);
        })->pluck('id')->toArray();*/
        if($request->year == 'latest')
        {
            $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                ->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
            $query = Helper::calculateAVGPCIndex($distress_type, $request->rmb, -1, false, true);
            $average = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
              ->select(DB::raw('sum(section_length) / 1000 as branch_total, Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average '))
              ->first();
        }
        else
        {
            $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                ->where('date_y', $request->year)->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
            $query = Helper::calculateAVGPCIndex($distress_type, $request->rmb, $request->year, false, true);
            $average = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)->where('date_y',$request->year)
                ->select(DB::raw('sum(section_length) / 1000 as branch_total, Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                    Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average '))
                ->first();
        }
        foreach ($query as $d)
        {
            $data[] = $d;
        }
        $lang = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
        return [$data, $average->average, tblOrganization::find($request->rmb)->$lang, $average->branch_total];
    }

    public function fourthChartPC(Request $request)
    {
        $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        $data = [];
        $label = [];
        $target_type = $this->target_type[$request->distress];
        $distress_type = $this->distress_type[$request->distress];
        if ($request->road != 'all')
        {
            $condition_rank = tblConditionRank::select('from','to')->where('target_type', $target_type)->get();
            $query = $this->getConditionRank($request, $condition_rank, 1);
            if($request->year == 'latest')
            {
                $total = DB::table('tblSection_PC')->where('branch_id', $request->road)->whereIn('SB_id', $sb_id)
                    ->select(DB::raw($query.',Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                    Sum(section_length) as branch_total,
                     (case when direction = "L" OR direction = "U" then 3 else 4 END) as new_direction,
                     (case when direction = "L" OR direction = "U" then (9 - lane_position_no) else (9 + lane_position_no) END)as A'),
                    'lane_position_no')
                    ->get();
                $result = DB::table('tblSection_PC')->where('branch_id', $request->road)->whereIn('SB_id', $sb_id)
                    ->select(DB::raw($query.',Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                    Sum(section_length) as branch_total,
                     (case when direction = "L" OR direction = "U" then 1 else 2 END) as new_direction,
                      (case when direction = "L" OR direction = "U" then (9 - lane_position_no) else (9 + lane_position_no) END)as A'), 'lane_position_no')
                    ->groupBy('branch_id')->groupBy('new_direction')->groupBy('lane_position_no')->orderBy('A', 'asc')->get();
            }
            else
            {
                $total = DB::table('tblSection_PC_history')->where('branch_id', $request->road)->where('date_y', $request->year)->whereIn('SB_id', $sb_id)
                    ->select(DB::raw($query.',Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                     Sum(section_length) as branch_total,
                    (case when direction = "L" OR direction = "U" then 3 else 4 END) as new_direction,
                    (case when direction = "L" OR direction = "U" then (9 - lane_position_no) else (9 + lane_position_no) END)as A'
                    ), 'lane_position_no')->get();
                $result = DB::table('tblSection_PC_history')->where('branch_id', $request->road)->where('date_y', $request->year)->whereIn('SB_id', $sb_id)
                    ->select(DB::raw($query.',Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total, 
                    Sum(section_length) as branch_total,
                    (case when direction = "L" OR direction = "U" then 1 else 2 END) as new_direction,
                    (case when direction = "L" OR direction = "U" then (9 - lane_position_no) else (9 + lane_position_no) END)as A'), 'lane_position_no')
                    ->groupBy('branch_id')->groupBy('new_direction')->groupBy('lane_position_no')->orderBy('A', 'asc')->get();
            }
            foreach ($condition_rank as $c)
            {
                if ($c->from < 1) $c->from = 0;
                if($c->to != null)
                {
                    $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress] : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
                }
                else
                {
                    $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - ' : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
                }
            }
            foreach ($result as $d)
            {
                $data[] = $d;
            }
            $data[] = $total[0];
            $name = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
            return [$label, $data, tblOrganization::find($request->rmb)->$name, tblBranch::find($request->road)->name];
        }
        else
        {
            return $data['err'] = 'error';
        }
    }

    public function getDataTable(Request $request)
    {
        $name = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
        $label = [];
        $target_type = $this->target_type[$request->distress];
        $distress_type = $this->distress_type[$request->distress];
        $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        $condition_rank = DB::table('tblCondition_rank')->select('from','to')
            ->where('target_type', $target_type)->get();
        $query = $this->getConditionRank($request, $condition_rank,  1);
       /* $branch_id = tblBranch::orderBy('branch_number')->whereHas('segments.tblOrganization', function($query) use($request) {
            $query->where('parent_id', $request->rmb);
        })->pluck('id')->toArray();*/
        if($request->year == 'latest')
        {
            $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                ->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
            $result = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC.branch_id')
                ->select(DB::raw($query . ',tblBranch.name_en as name_en, tblBranch.name_vn as name_vi,
                sum(section_length) / 1000 as branch_total,Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average '))
                ->groupBy('branch_id')->get();
            $rmb = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                ->select(DB::raw($query . ',sum(section_length) / 1000 as branch_total, 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average'))
                ->first();
        }
        else
        {
            $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)->where('date_y', $request->year)
                ->where($distress_type, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
            $result = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id')->where('date_y', $request->year)
                ->select(DB::raw($query.',tblBranch.name_en as name_en, tblBranch.name_vn as name_vi,
                sum(section_length) / 1000 as branch_total,Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average '))
                ->groupBy('branch_id')->get();
            $rmb = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)->where('date_y', $request->year)
                ->select(DB::raw($query.',sum(section_length) / 1000 as branch_total,
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total,
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                    Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average'))
                ->first();
        }
        foreach ($condition_rank as $c)
        {
            if ($c->from < 1) $c->from = 0;
            if($c->to != null)
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress] : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
            }
            else
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - ' : $label[] = number_format($c->from, 0).$this->unit[$request->distress].' - '.number_format($c->to, 0).$this->unit[$request->distress];
            }
        }
        return [$result, $label, $rmb, tblOrganization::find($request->rmb)->$name];
    }

    public function exportData(Request $request)
    {
        $all_data = [];
        $flag = false;
        foreach ($this->distress_type as $d_key => $distress)
        {
            $table1 = [];
            $table2 = [];
            $data = [];
            $result_road = null;
            $total_road= null;
            $name = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
            if (substr($request->road, 7) != 'all') $flag = true;
            $organization = tblOrganization::find($request->rmb)->$name;
            $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
            $target_type = $this->target_type[$d_key];
            $condition_rank = DB::table('tblCondition_rank')->select('from','to')->where('target_type', $target_type)->get();
            $query = $this->getConditionRank($request, $condition_rank,  1, $d_key);
            if(substr($request->year, 7) == 'latest')
            {
                $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                    ->where($distress, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
                if ($flag)
                {
                    $total_road = DB::table('tblSection_PC')->where('branch_id', substr($request->road, 7))->whereIn('SB_id', $sb_id)
                        ->select(DB::raw($query.', Sum(section_length) as total,
                        (case when direction = "L" OR direction = "U" then 3 else 4 END) as new_direction'), 'lane_position_no')
                        ->first();
                    $result_road = DB::table('tblSection_PC')->where('branch_id', substr($request->road, 7))->whereIn('SB_id', $sb_id)
                        ->select(DB::raw($query.', Sum(section_length) as total,
                         (case when direction = "L" OR direction = "U" then 1 else 2 END) as new_direction,
                      (case when direction = "L" OR direction = "U" then (9 - lane_position_no) else (9 + lane_position_no) END)as A'), 'lane_position_no')
                    ->groupBy('branch_id')->groupBy('new_direction')->groupBy('lane_position_no')->orderBy('A', 'asc')->get();
                }
                $result = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                    ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC.branch_id')
                    ->join('tblOrganization', 'tblOrganization.id', '=', 'tblSection_PC.SB_id')
                    ->select(DB::raw($query.',tblBranch.name_en as name_en, tblBranch.name_vn as name_vi,tblOrganization.name_en as sb_en, tblOrganization.name_vn as sb_vi,
                    sum(section_length) as branch_total,Sum(CASE WHEN '.$distress.' >= 0 THEN section_length ELSE 0 END) as total,
                    Sum(CASE WHEN '.$distress.' >= 0 THEN ('.$distress.' * section_length) ELSE 0 END) / 
                    Sum(CASE WHEN '.$distress.' >= 0 THEN section_length ELSE 0 END) as average '))
                    ->groupBy('branch_id')->get();
                $average = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                    ->select(DB::raw('Sum(CASE WHEN '.$distress.' >= 0 THEN ('.$distress.' * section_length) ELSE 0 END) / 
                    Sum(CASE WHEN '.$distress.' >= 0 THEN section_length ELSE 0 END) as average '))
                    ->first();
            }
            else
            {
                $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)->where('date_y', substr($request->year, 7))
                    ->where($distress, '>=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
                if ($flag)
                {

                    $total_road = DB::table('tblSection_PC_history')->where('branch_id', substr($request->road, 7))->where('date_y', substr($request->year, 7))->whereIn('SB_id', $sb_id)
                        ->select(DB::raw($query.', Sum(section_length) as total,
                         (case when direction = "L" OR direction = "U" then 3 else 4 END) as new_direction'
                        ),'lane_position_no')->first();
                    $result_road = DB::table('tblSection_PC_history')->where('branch_id', substr($request->road, 7))->where('date_y', substr($request->year, 7))->whereIn('SB_id', $sb_id)
                        ->select(DB::raw($query.', Sum(section_length) as total,
                        (case when direction = "L" OR direction = "U" then 1 else 2 END) as new_direction,
                      (case when direction = "L" OR direction = "U" then (9 - lane_position_no) else (9 + lane_position_no) END)as A'), 'lane_position_no')
                    ->groupBy('branch_id')->groupBy('new_direction')->groupBy('lane_position_no')->orderBy('A', 'asc')->get();
                }
                $result = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)
                    ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id')
                    ->join('tblOrganization', 'tblOrganization.id', '=', 'tblSection_PC_history.SB_id')->where('date_y', substr($request->year, 7))
                    ->select(DB::raw($query.',tblBranch.name_en as name_en, tblBranch.name_vn as name_vi,tblOrganization.name_en as sb_en, tblOrganization.name_vn as sb_vi,
                    sum(section_length) as branch_total,Sum(CASE WHEN '.$distress.' >= 0 THEN section_length ELSE 0 END) as total,
                    Sum(CASE WHEN '.$distress.' >= 0 THEN ('.$distress.' * section_length) ELSE 0 END) / 
                    Sum(CASE WHEN '.$distress.' >= 0 THEN section_length ELSE 0 END) as average '))
                    ->groupBy('branch_id')->get();
                $average = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->whereIn('branch_id', $branch_id)->where('date_y', substr($request->year, 7))
                    ->select(DB::raw('Sum(CASE WHEN '.$distress.' >= 0 THEN ('.$distress.' * section_length) ELSE 0 END) / 
                    Sum(CASE WHEN '.$distress.' >= 0 THEN section_length ELSE 0 END) as average '))
                    ->first();
            }
            $sb = App::getLocale() == 'en' ? 'sb_en' : 'sb_vi';
            $branch = App::getLocale() == 'en' ? 'name_en' : 'name_vi';
            foreach ($result as $r)
            {
                $r->average = $distress == 'MCI' ? round($r->average, 1) : round($r->average, 2);
                $average->average = $distress == 'MCI' ? round($average->average, 1) : round($average->average, 2);
                $record['organization'] = $organization;
                $record['sb'] = $r->$sb;
                $record['road'] = $r->$branch;
                foreach ($r as $key => $value)
                {
                    if ($key != 'average' && $key != 'branch_total' && $key != 'name_en'
                        && $key != 'name_vi' && $key != 'sb_en' && $key != 'sb_vi' && $key != 'total')
                    {
                        $record[$key] = $value;
                    }
                }
                array_push($record, $r->total, $r->average, $average->average);
                $table1[] = $record;
                $record = [];
            }
            $all_data[] = $table1;
            if ($flag)
            {
                $data[] = $total_road;
                foreach ($result_road as $d)
                {
                    $data[] = $d;
                }
                foreach ($data as $dt)
                {
                    if ($dt->new_direction <= 2)
                    {
                        $label = $dt->new_direction == 1 ? "L".$dt->lane_position_no : 'R'.$dt->lane_position_no;
                    }
                    else
                    {
                        $label = trans('das.total');
                    }
                    $record1['label'] = $label;
                    foreach ($dt as $key => $value)
                    {
                        if ($key != 'total' && $key != 'new_direction' && $key != 'lane_position_no' && $key != 'A')
                        {
                            $record1[$key] = $value;
                        }
                    }
                    array_push($record1, $dt->total);
                    $table2[] = $record1;
                    $record1 = [];
                }
                $all_data[] = $table2;
            }
            else
            {
                $all_data[] = '';
            }
        }
        $lang = App::getLocale() == 'en' ? 'EN' : 'VN';
        $tpl_file = public_path('excel_templates/DAS/SummaryPC/DAS_SummaryOfPavementCondition_'.$lang.'.xlsx');
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
        $xlsx = new \eiseXLSX($tpl_file);
        foreach ($this->sheet as $k => $sv)
        {
            if (!$flag && $k%2 == 1) continue;
            $index = 7;
            $columns1 = $k == 4 || $k == 5 || $k == 6 || $k == 7 ? 'Q' : 'M';
            $columns2 = $k == 4 || $k == 5 || $k == 6|| $k == 7 ? 'M' : 'I';
            if ($k%2 == 0)
            {
                $xml = $xlsx->arrXMLs['/xl/worksheets/sheet'. ($k+($k/2+2)).'.xml'];
                $row_8 = $xml->sheetData->row[7];
                $dom = dom_import_simplexml($row_8);
                $dom->parentNode->removeChild($dom);
                $table = $xlsx->arrXMLs['/xl/tables/table'.($k+1) .'.xml'];
                $table['ref'] = "A7:".$columns1.(count($all_data[$k]) + $index);
                $table->autoFilter['ref'] = "A7:".$columns1.(count($all_data[$k]) + $index);
                foreach ($all_data[$sv] as $ad_key => $ad_value)
                {
                    $new_row = $xml->sheetData->addChild('row');
                    foreach ($ad_value as $key => $v)
                    {
                        $new_cell = $new_row->addChild('c');
                        if (is_numeric($v))
                        {
                            $new_cell->addAttribute('t', "n");
                            $new_v = $new_cell->addChild('v', $v);
                        }
                        else if ($v == null)
                        {
                            $new_cell->addAttribute('t', 'n');
                        }
                        else
                        {
                            $new_cell->addAttribute('t', "inlineStr");
                            $new_is = $new_cell->addChild('is');
                            if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v);
                            $new_T = $new_is->addChild('t', htmlspecialchars($v));
                        }
                    }
                }
                $xlsx->arrXMLs['/xl/worksheets/sheet'. ($k+($k/2+2)) .'.xml'] = $xml;
            }
            elseif ($flag && $k%2 == 1)
            {
                $row_check = $k == 3 ? 7 : 6;
                $xml = $xlsx->arrXMLs['/xl/worksheets/sheet'. ($k+(((int)($k/2))+2)).'.xml'];
                $row_7 = $xml->sheetData->row[$row_check];
                $dom = dom_import_simplexml($row_7);
                $dom->parentNode->removeChild($dom);
                $table = $xlsx->arrXMLs['/xl/tables/table'.($k+1) .'.xml'];
                $table['ref'] = "A7:".$columns2.(count($all_data[$k]) + $index);
                $table->autoFilter['ref'] = "A7:".$columns2.(count($all_data[$k]) + $index);
                foreach ($all_data[$sv] as $sub_key => $sub_v)
                {
                    $new_row = $xml->sheetData->addChild('row');
                    foreach ($sub_v as $key => $v)
                    {
                        $new_cell = $new_row->addChild('c');
                        if (is_numeric($v))
                        {
                            $new_cell->addAttribute('t', "n");
                            $new_v = $new_cell->addChild('v', $v);
                        }
                        else if ($v == null)
                        {
                            $new_cell->addAttribute('t', 'n');
                        }
                        else
                        {
                            $new_cell->addAttribute('t', "inlineStr");
                            $new_is = $new_cell->addChild('is');
                            if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v);
                            $new_T = $new_is->addChild('t', htmlspecialchars($v));
                        }
                    }
                }
                $xlsx->arrXMLs['/xl/worksheets/sheet'. ($k+(((int)($k/2))+2)).'.xml'] = $xml;
            }
        }
        setcookie(
            'fileDownloadTokenPC',
            $request->downloadTokenValuePC,
            time() + 60*60,
            '/das'
        );
        $xlsx->Output('DAS_SummaryOfPavementCondition_'.$lang.'.xlsx', "D");
    }

    public function index()
    {
        //
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
