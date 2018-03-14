<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\tbManagement;
use App\Models\User;
use App\Models\tblOrganization;
use App\Models\tblSection;
use App\Models\tblSegment;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataTV;
use App\Models\tblMergeSplit;
use App\Models\tblMergeSplitDetail;
use App\Models\Role;
use App\Models\User_role;
use Input, Validator, DB, DateTime, Auth, Session, Form, Hash;
use Illuminate\Support\MessageBag;
use App\Models\RollBackUser;
use App\Models\convertdata;
use App\Models\tblRoad;
use App\Models\tblConditionRank;
use App;

class ConditionRankController extends Controller
{
    public function __construct()
    {
        $this->middleware('dppermission:condition_rank.all');
        // dd(Auth::user()->hasPermission("condition_rank.view"));
    	// if(!Auth::user()->hasRole('superadmin'))
        // {
        	// return redirect('/user/logout');
		// }
        // if (1)
        // {
            // return ;
        // }
        // else
        // {
        	// return redirect('/user/logout');
        // }
    }
//  view index 
       public function index()
    {
        // return view('admin.condition_rank.index1');
        $crack = tblConditionRank::where('target_type', 1)->orderBy('rank','ASC')->get();
        $rut = tblConditionRank::where('target_type', 2)->orderBy('rank','ASC')->get();
        $iri = tblConditionRank::where('target_type', 3)->orderBy('rank','ASC')->get();
        $mci = tblConditionRank::where('target_type', 4)->orderBy('rank','ASC')->get();
        //dd($crack);
        $total = DB::table('tblCondition_rank')->count();
        $total += 1;
        // dd($total);
        return view('admin.condition_rank.index')->with(array(
                'crack' => $crack,
                'rut' => $rut,
                'iri' => $iri,
                'mci' => $mci,
                'case' => 'rank',
                'total_rank' => $total
                ));
        // return view('admin.condition_rank.index1');
    }
// show view table 
    public function create()
    {
        return view('admin.condition_rank.create');
    }
// 
    public function store(Request $request)
    {
        // dd($request->all());
  //   	$rank = $request->total;
		// for($i = 0 ; $i < $rank-1 ; $i ++)
		// {
		// 	if (trim($request->{'from'.$i} ) == '' || trim($request->{'to'.$i} ) == ''){
		// 		Session()->put('class', 'alert alert-danger');
  //               Session()->put('message', trans('back_end.name_en_not_null'));
  //               return back()->withInput();
		// 	}
		// }
		
  //   	// dd($request->all());die;
  //   	DB::table('tblCondition_rank')
  //           ->where('target_type', $request->disabled)
  //           ->delete();

  //       // $rank = $request->total;
  //       $id_user = Auth::user()->id;
        
  //       for($i = 0 ; $i < $rank ; $i ++)
  //       {
  //           $tblConditionRank = new tblConditionRank;
  //           $tblConditionRank->rank = $i+1;
  //           $tblConditionRank->from = $request->{'from'.$i};
  //           $tblConditionRank->to = $request->{'to'.$i}  ;
		// 	$tblConditionRank->created_by = $id_user;
  //           $tblConditionRank->save();
  //           // dd($request->{'from'.$i});die;
  //       }
  //       // $tblConditionRank->save();
  //       return redirect()->route('condition_rank.index');
    }
// show table 
	public function table(Request $request)
    {
        // dd($request->type);die;
        if ($request->type == 1)
        {
            $type = "C";
        }
        else 
        {
            $type = "R";
        }
        return view('admin.condition_rank.table')->with([
            "type"  =>  $type,
            "rank"  =>  $request->rank
            ]);
    }
    public function update_condition(Request $request)
    {
        $rank_crack = $request->total_crack;
        $rank_rut = $request->total_rut;
        $rank_iri = $request->total_iri;
        $rank_mci = $request->total_mci;
        $id_user = Auth::user()->id;
        for($i = 0 ; $i < $rank_crack ; $i ++)
        {
            $crack = tblConditionRank::find($request->{'id_crack_'.$i});
            $crack->from = $request->{'crack_from'.$i};
            if ($request->{'crack_from'.$i} != null) {
                $crack->to = $request->{'crack_to'.$i};
            }
            else
            {
                $crack->to = Null;
            }
            $crack->updated_at = $id_user;
            $crack->save();
        }
        for($i = 0 ; $i < $rank_rut ; $i ++)
        {
            $rut = tblConditionRank::find($request->{'id_rut_'.$i});
            $rut->from = $request->{'rut_from'.$i};
            if ($request->{'rut_from'.$i} != null) {
                $rut->to = $request->{'rut_to'.$i};
            }
            else
            {
                $rut->to = Null;
            }
            $rut->updated_at = $id_user;
            $rut->save();
        }
        for($i = 0 ; $i < $rank_iri ; $i ++)
        {
            $iri = tblConditionRank::find($request->{'id_iri_'.$i});
            $iri->from = $request->{'iri_from'.$i};
            if ($request->{'iri_from'.$i} != null) {
                $iri->to = $request->{'iri_to'.$i};
            }
            else
            {
                $iri->to = Null;
            }
            $iri->updated_at = $id_user;
            $iri->save();
        }
        for($i = 0 ; $i < $rank_mci ; $i ++)
        {
            $mci = tblConditionRank::find($request->{'id_mci_'.$i});
            $mci->from = $request->{'mci_from'.$i};
            if ($request->{'mci_from'.$i} != null) {
                $mci->to = $request->{'mci_to'.$i};
            }
            else
            {
                $mci->to = Null;
            }
            $mci->updated_at = $id_user;
            $mci->save();
        }
        Session()->put('class', 'alert alert-success');
        Session()->put('message', trans('back_end.update_success'));
        return redirect()->route('condition_rank.index');
    }

    public function show($id)
    {
        // return view('admin.condition_rank.create')->with(array('total' => $id));
    }

    
    public function edit($id)
    {
    }

   
    public function update(Request $request, $id)
    {
        
    }

    
    public function destroy($id)
    {
        //
    }

    
}
