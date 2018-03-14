<?php

namespace App\Http\Controllers\Ajax\DAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblOrganization;
use App\Models\tblSectionPCHistory;
use App, DB;
use App\Classes\Helper;


class TransitionPCController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $distress_type = $this->distress_type[$request->distress];
        $lang = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
        if ($request->rmb == -1)
        {
            $total = Helper::calculateAVGPCIndexNotIntegrated($distress_type, -1);
            $organization = [];
            foreach ($total as $t)
            {
                if (!in_array($t->parent, $organization))
                {
                    $math = [];
                    $year = [];
                }
                $math[] = $request->distress == 5 ? round($t->total, 1) : round($t->total, 2);
                $year[] = $t->year;
                $array[$t->parent] = [
                    'year' => $year,
                    'total' => $math,
                    'name' => DB::table('tblOrganization')->where('id', $t->parent)->first()->$lang,
                ];
                $organization [] = $t->parent;
            }
            return [
                'all_data' => $array,
                'type' => 'all'
            ];

        }
        else
        {
            $total = Helper::calculateAVGPCIndexNotIntegrated($distress_type, $request->rmb);
            $organization = [];
            foreach ($total as $t)
            {
                if (!in_array($t->parent, $organization))
                {
                    $math = [];
                    $year = [];
                }
                $math[] = $request->distress == 5 ? round($t->total, 1) : round($t->total, 2);
                $year[] = $t->year;
                $array[0] = [
                    'year' => $year,
                    'total' => $math,
                    'name' => DB::table('tblOrganization')->where('id', $t->parent)->first()->$lang,
                ];
                $organization [] = $t->parent;
            }
            return [
                'name' => $array[0]['name'],
                'year' => $array[0]['year'],
                'total' =>  $array[0]['total'],
                'type' => 'one'
            ];
        }

        // $lang = App::getLocale() == 'en' ? 'en' : 'vn';
        // $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        // if($request->branch_id != 'all')
        // {
        //     // $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)
        //     //     ->where('branch_id', '!=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
        //     $branch_id = $request->branch_id;

        //     $data = DB::table('tblSection_PC_history')->where('branch_id', $branch_id)->where($distress_type, '>=', 0)
        //         // ->join('tblBranch', 'tblSection_PC.branch_id', '=', 'tblBranch.id')
        //         ->select(DB::raw('date_y, sum('.$distress_type .'* section_length)/ sum(section_length) as total'))->groupBy('date_y')->get();

        //     $array = $data;
        //     // dd($data);
        //     // $line = 0;
        //     // foreach ($data as $d)
        //     // {
        //     //     $line += $d->total;
        //     // }
        //     // $result = $line/count($branch_id);
        // }
        // else
        // {
        //     $branches = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)
        //         ->join('tblBranch', 'tblSection_PC_history.branch_id', '=', 'tblBranch.id')
        //         ->where('branch_id', '!=', 0)->groupBy('branch_id')->get();
        //     $array = [];
        //     foreach ($branches as $branch)
        //     {
        //         $branch_id = $branch->id;
        //         $data = DB::table('tblSection_PC')->where('branch_id', $branch_id)->where($distress_type, '>=', 0)
        //         ->select(DB::raw('date_y, sum('.$distress_type .'* section_length)/ sum(section_length) as total'))->groupBy('date_y')->get();
        //         $array[$branch->name_en] = $data;
        //     }
        // }
        // return $array;
    }

   /* public function indexOld(Request $request)
    {
        // distress = 1 => cracking
        // distress = 2 => rutting depth ave
        // distress = 3 => rutting depth max
        // distress = 4 => IRI
        // distress = 5 => MCI
        $distress_type = $this->distress_type[$request->distress];
        if ($request->rmb == -1)
        {
            $rmbs = tblOrganization::where('level', 2)->get();
            $array = [];
            foreach ($rmbs as $rmb)
            {
                $sb_id = tblOrganization::where('parent_id', $rmb->id)->pluck('id')->toArray();
                $year = tblSectionPCHistory::groupBy('date_y')
                    ->orderBy('date_y')->whereIn('SB_id', $sb_id)->pluck('date_y')->toArray();
                $data = [];
                foreach ($year as $y)
                {
                    $a = microtime(true);
                    $total = Helper::calculateAVGPCIndex($distress_type, $rmb->id, $y, true);
                    $b = microtime(true);
                    dd($b-$a);
                    if ($request->distress == 5)
                    {
                        $data[] = round($total[0]->total, 1);
                    }
                    else
                    {
                        $data[] = round($total[0]->total, 2);
                    }
                }
                $array[] = [
                    'year' => $year,
                    'total' =>  $data,
                    'name' => $rmb->organization_name
                ];
            }

            // return $array;
            return [
                'all_data' => $array,
                'type' => 'all'
            ];

        }
        else
        {
            $rmb = tblOrganization::where('id', $request->rmb)->first();
            $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
            $year = tblSectionPCHistory::groupBy('date_y')
                ->orderBy('date_y')->whereIn('SB_id', $sb_id)->pluck('date_y')->toArray();
            $data = [];
            foreach ($year as $y)
            {
                $total = Helper::calculateAVGPCIndex($distress_type, $request->rmb, $y, true);
                if ($request->distress == 5)
                {
                    $data[] = round($total[0]->total, 1);
                }
                else
                {
                    $data[] = round($total[0]->total, 2);
                }
            }

            return [
                'name' => $rmb->organization_name,
                'year' => $year,
                'total' =>  $data,
                'type' => 'one'
            ];
        }

        // $lang = App::getLocale() == 'en' ? 'en' : 'vn';
        // $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        // if($request->branch_id != 'all')
        // {
        //     // $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)
        //     //     ->where('branch_id', '!=', 0)->groupBy('branch_id')->pluck('branch_id')->toArray();
        //     $branch_id = $request->branch_id;

        //     $data = DB::table('tblSection_PC_history')->where('branch_id', $branch_id)->where($distress_type, '>=', 0)
        //         // ->join('tblBranch', 'tblSection_PC.branch_id', '=', 'tblBranch.id')
        //         ->select(DB::raw('date_y, sum('.$distress_type .'* section_length)/ sum(section_length) as total'))->groupBy('date_y')->get();

        //     $array = $data;
        //     // dd($data);    
        //     // $line = 0;
        //     // foreach ($data as $d)
        //     // {
        //     //     $line += $d->total;
        //     // }
        //     // $result = $line/count($branch_id);
        // }
        // else
        // {
        //     $branches = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)
        //         ->join('tblBranch', 'tblSection_PC_history.branch_id', '=', 'tblBranch.id')
        //         ->where('branch_id', '!=', 0)->groupBy('branch_id')->get();
        //     $array = [];
        //     foreach ($branches as $branch) 
        //     {
        //         $branch_id = $branch->id;
        //         $data = DB::table('tblSection_PC')->where('branch_id', $branch_id)->where($distress_type, '>=', 0)
        //         ->select(DB::raw('date_y, sum('.$distress_type .'* section_length)/ sum(section_length) as total'))->groupBy('date_y')->get();
        //         $array[$branch->name_en] = $data;
        //     }
        // }
        // return $array;
    }*/

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
