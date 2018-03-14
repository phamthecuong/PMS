<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App;
use Yajra\Datatables\Datatables;
use App\Models\User;
use App\Models\tblOrganization;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB, Auth;
use App\Models\tblSegment;
use App\Models\tblRepairMatrix;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function __construct()
    {
        
    }
    
    function GetTableAjaxData($name, Request $request)
    {
        switch ($name) {
            case 'admin':
                $user = DB::table('users')->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                                          ->join('roles as r', 'r.id', '=', 'user_roles.role_id')

                                          ->join('tblOrganization as o', 'o.id', '=', 'users.organization_id')

                                          ->select('users.id', 'users.name as user_name', 'users.created_at', 'r.name as role_name' , 'o.name_en as oname_en' , 'o.name_vn as oname_vn' )

                                          ->where(function ($query) {
                                          	$query->where('r.name', 'adminlv1')
												->where('r.code', 'adminlv1');
                                          })
                                          ->orWhere(function ($query) {
                                          	$query->where('r.name', 'adminlv2')
												->where('r.code', 'adminlv2');
                                          })
                                          ->orWhere(function ($query) {
                                          	$query->where('r.name', 'adminlv3')
												->where('r.code', 'adminlv3');
                                          })
                                          ->get();
                $change_password = trans('back_end.change_password');
                $delete = trans('back_end.delete');
                $edit = trans('back_end.edit');
                return Datatables::of($user)
                    ->addColumn('action', function ($user) use($edit, $delete, $change_password){
                        $actions = [];
                        if (Auth::user()->hasPermission('admin_management.edit'))
                        {
                            $actions+= ['edit' => ['edit' => $edit, 'link' => 'admin_manager/'.$user->id.'/edit']];
                        }
                        if (Auth::user()->hasPermission('admin_management.delete'))
                        {
                            $actions+= ['delete' => ['id' => $user->id, 'action' => 'admin_manager.destroy','delete' => $delete ,'title' => trans('back_end.sure_want_delete')]];
                        }
                        if (Auth::user()->hasPermission('admin_management.change_password'))
                        {
                            $actions+= ['change_password' => ['change_password' => $change_password, 'id' => $user->id, 'link' => 'admin_manager/'.$user->id.'/change_password']];
                        }

                        return view('admin.templates.btn')->with($actions);
                    })
                    ->make(true);
                break;
                //  duc.dn  08.11.2016
            case 'organization':
                $user = DB::table('tblOrganization')
                                                  ->select('tblOrganization.id as id', 'tblOrganization.name_en as name_en' ,'tblOrganization.name_vn as name_vn')
                                                  ->where('level', '=', 2)
                                                  ->get();
                $SB = trans('back_end.SB');
                $delete = trans('back_end.delete');
                $edit = trans('back_end.edit');
                $Merge = trans('back_end.Merge');

                return Datatables::of($user)
                    ->addColumn('action', function ($user) use($edit, $delete, $SB,$Merge) {
                        return view('admin.templates.btn')->with(array(
                              'edit' => ['edit' => $edit,'id' => $user->id, 'link' => 'organization/'.$user->id.'/edit'], 
                              'delete' => ['id' => $user->id, 'action' => 'organization.destroy','delete' => $delete ,'title' => trans('back_end.sure_want_delete')],
                              'SB' => ['id' => $user->id,'SB' => $SB ,'title' => trans('back_end.SB'),'link' => 'SB/'.$user->id],
                              'Merge' => ['title' => $Merge,'id' => $user->id, 'link' => 'MergeRMB/'.$user->id ]
                          ));
                    })
                    ->make(true);
                break;
            case 'SB':
                $user = DB::table('tblOrganization')
                                                  ->join('tblOrganization as RMB_name', 'RMB_name.id', '=', 'tblOrganization.parent_id')
                                                  ->select('tblOrganization.id as id', 'tblOrganization.name_en as name_en' ,'tblOrganization.name_vn as name_vn','tblOrganization.parent_id as RMB ','RMB_name.name_en as RMB_name_en','RMB_name.name_vn as RMB_name_vn')
                                                  ->where('tblOrganization.parent_id', '=', $request->sb_id) 
                                                  ->get();
                $SB = trans('back_end.SB');
                $delete = trans('back_end.delete');
                $edit = trans('back_end.edit');
		            $Merge = trans('back_end.Merge');
                return Datatables::of($user)
                    ->addColumn('action', function ($user) use($edit, $delete, $SB, $Merge) {
                        return view('admin.templates.btn')->with(array(
                              'edit' => ['edit' => $edit,'id' => $user->id, 'link' => 'editSB/'.$user->id], 

                              'deleteSB' => ['id' => $user->id,
                                             'action' => 'destroySB',
                                             'delete' => $delete ,
                                             'title' => trans('back_end.sure_want_delete')
                                             ],

                              'Merge' => ['title' => $Merge,'id' => $user->id, 'link' => 'MergeSB/'.$user->RMB.'/'.$user->id ]
                          ));
                    })
                    ->make(true);
                break;
  			    case 'Merge':
      				        // dd($request->sb_id);die;
                      $merge = DB::table('tblOrganization')
                      ->select('tblOrganization.id as id', 'tblOrganization.name_en as sname_en' ,'tblOrganization.name_vn as sname_vn','tblOrganization.parent_id as RMB')
                      ->where('parent_id', '=', $request->sb_id)
                      ->get();
      			           	// dd($merge);die;
      				        return Datatables::of($merge)->make(true);

                      // end modify
            case 'MergeRMB':
                        // dd($request->sb_id);die;
                      $MergeRMB = DB::table('tblOrganization')
                      ->select('tblOrganization.id as id', 'tblOrganization.name_en as sname_en' ,'tblOrganization.name_vn as sname_vn','tblOrganization.parent_id as RMB')
                      ->where('level', 2)
                      ->get();
                        return Datatables::of($MergeRMB)->make(true);
            case 'rank':
                $rank = DB::table('tblCondition_rank')
					->select('tblCondition_rank.id as id', 'tblCondition_rank.target_type as type' ,'tblCondition_rank.rank as rank','tblCondition_rank.from as from' ,'tblCondition_rank.to as to' )
					->where(function($query) use($request){
						if ($request->type != null)
						{
							$query->where('tblCondition_rank.target_type', $request->type);
						
						}
					})
					->orderBy('rank', 'asc');
					return Datatables::of($rank)
					->editColumn('type', function ($rank) {
						if ($rank->type == 1) {
							return 'Crack ratio';
						}
						else if ($rank->type == 2) {
							return 'rutting depth';
						}
						else{
							return 'IRI';
						}
					})
					  
                        // ->editColumn('infor',  '{{$from}} < C <= {{$to}}')
						
					->editColumn('infor', function ($rank) {
						if ($rank->type == 1){
							if ($rank->to == null) {
								return 'C >'.($rank->from+ 0);
							}
							else if ($rank->to == $rank->from+ 0) {
								return 'C = '.($rank->from+ 0);
							}
							else {
								return ($rank->from + 0).'< C <='.($rank->to + 0);
							}
						}
						else {
							if ($rank->to == null) {
								return 'R >'.($rank->from + 0);
							}
							else if ($rank->to == $rank->from) {
								return 'R = '.($rank->from+ 0);
							}
							else {
								return ($rank->from+ 0).'< R <='.($rank->to+ 0);
							}
						}
						
				})
				->make(true);
            case 'user':
                $user;
                if (Auth::user()->hasRole('adminlv1'))
                {
                    $user = DB::table('users')
                                  ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                                  ->join('roles as r', 'r.id', '=', 'user_roles.role_id')
                                  ->join('tblOrganization', 'tblOrganization.id' , '=', 'users.organization_id')
                                  ->select(DB::raw('DISTINCT users.id'),'users.active', 'tblOrganization.level as olevel', 'tblOrganization.name_en as oname_en', 'tblOrganization.name_vn as oname_vn' , 'users.name as user_name', 'users.created_at', 'r.name as role_name')
                                  ->where(function ($query) {
                                  		$query->where('r.name', 'userlv1')
											                    ->where('r.code', 'userlv1');
                                  })
                                  ->get();

                }
                else if(Auth::user()->hasRole('adminlv2'))
                {
                    $user = DB::table('users')
                                  ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                                  ->join('roles as r', 'r.id', '=', 'user_roles.role_id')
                                  ->join('tblOrganization', 'tblOrganization.id' , '=', 'users.organization_id')
                                  ->select(DB::raw('DISTINCT users.id'),'users.active', 'tblOrganization.level as olevel', 'tblOrganization.name_en as oname_en', 'tblOrganization.name_vn as oname_vn' , 'users.name as user_name', 'users.created_at', 'r.name as role_name')
                                  ->where(function ($query) {
                                  		$query->where('r.name', 'userlv2')
											->where('r.code', 'userlv2');
                                  })
                                  ->get();
                }
                else if(Auth::user()->hasRole('adminlv3'))
                {
                    $user = DB::table('users')
                                  ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                                  ->join('roles as r', 'r.id', '=', 'user_roles.role_id')
                                  ->join('tblOrganization', 'tblOrganization.id' , '=', 'users.organization_id')
                                  ->select(DB::raw('DISTINCT users.id'),'users.active', 'tblOrganization.level as olevel', 'tblOrganization.name_en as oname_en', 'tblOrganization.name_vn as oname_vn' , 'users.name as user_name', 'users.created_at', 'r.name as role_name')
                                  ->where(function ($query) {
                                  		$query->where('r.name', 'userlv3')
											->where('r.code', 'userlv3');
                                  })
                                  ->get();
                }
                else if(Auth::user()->hasRole('superadmin'))
                {
                    $user = DB::table('users')
                                  ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                                  ->join('roles as r', 'r.id', '=', 'user_roles.role_id')
                                  ->join('tblOrganization', 'tblOrganization.id' , '=', 'users.organization_id')
                                  ->select(DB::raw('DISTINCT users.id'),'users.active', 'users.name as user_name', 'tblOrganization.level as olevel', 'tblOrganization.name_en as oname_en', 'tblOrganization.name_vn as oname_vn' , 'users.created_at', 'r.name as role_name')
                                  ->where(function ($query) {
                                  		$query->where('r.name', 'userlv1')
											->where('r.code', 'userlv1');
                                  })
                                  ->orWhere(function ($query) {
                                  		$query->where('r.name', 'userlv2')
											->where('r.code', 'userlv2');
                                  })
                                  ->orWhere(function ($query) {
                                  		$query->where('r.name', 'userlv3')
											->where('r.code', 'userlv3');
                                  })
                                  ->get();
                }
                $change_password = trans('back_end.change_password');
                $delete = trans('back_end.delete');
                $edit = trans('back_end.edit');
                $active = trans('back_end.active');
                $not_active = trans('back_end.not_active');
                return Datatables::of($user)
                    ->addColumn('action', function ($user) use ($not_active, $change_password, $delete, $edit, $active)  {

                        $actions = [];
                        if (Auth::user()->hasPermission('user_management.edit'))
                        {
                            $actions+= ['edit' => ['edit' => $edit, 'link' => 'user_manager/' . $user->id . '/edit']];
                        }
                        if (Auth::user()->hasPermission('user_management.delete'))
                        {
                            $actions+= ['delete' => ['id' => $user->id, 'action' => 'user_manager.destroy', 'delete' => $delete, 'title' => trans('back_end.sure_want_delete')]];
                        }
                        if (Auth::user()->hasPermission('user_management.change_password'))
                        {
                            $actions+= ['change_password' => ['change_password' => $change_password, 'id' => $user->id, 'link' => 'user_manager/' . $user->id . '/change_password'],];
                        }

                        if (Auth::user()->hasPermission('admin_management.edit') || Auth::user()->hasPermission('admin_management.delete')|| Auth::user()->hasPermission('admin_management.change_password')) {
                            return view('admin.templates.btn')->with(
                                $actions,
                                array(
                                'active' => ['active' => $active, 'link' => 'user_manager/' . $user->id . '', 'check' => $user->active, 'not_active' => $not_active]
                            ));
                        }
                    })
                    ->make(true);
                break;
            case 'branch':
                $user = DB::table('users')
                                  ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
                                  ->join('roles as r', 'r.id', '=', 'user_roles.role_id')
                                  ->join('tblorganization', 'tblorganization.id' , '=', 'users.organization_id')
                                  ->select('users.id', 'users.name as user_name', 'tblorganization.level as olevel', 'tblorganization.name_en as oname_en', 'tblorganization.name_vn as oname_vn' , 'users.created_at', 'r.name as role_name')
                                  ->where('r.id', '=', 13)
                                  ->orWhere('r.id', '=', 14)
                                  ->orWhere('r.id', '=', 15)
                                  ->get();
                return Datatables::of($user)
                    ->addColumn('action', function ($user) use ($change_password, $delete, $edit) {
                        return view('admin.templates.edit')->with(array( 'id' => $user->id, 'link' => 'user_manager')) .
                               view('admin.templates.change_password')->with(array( 'id' => $user->id, 'link' => 'user_manager')).
                               view('admin.templates.delete')->with(array( 'id' => $user->id, 'action' => 'user_manager.destroy', 'title' => trans('back_end.sure_want_delete')));
                    })
                    ->make(true);
                 break;
            case 'segment':
                $segment = DB::table('tblSegment')
                            ->leftjoin('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id')
                            // ->leftjoin('tblRoad', 'tblRoad.id', '=', 'tblBranch.route_id')
                            ->leftjoin('tblOrganization as SB', 'SB.id', '=', 'tblSegment.SB_id')
                            ->leftjoin('tblOrganization as RMB', 'RMB.id', '=', 'SB.parent_id')
                            ->leftjoin('mstDistrict as dfrom', 'dfrom.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstProvince as cfrom', 'cfrom.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstDistrict as dto', 'dto.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstProvince as cto', 'cto.id', '=', 'tblSegment.distfrom_id')
							->where(function($query) {
								$organization_id = Auth::user()->organization_id;
								if (Auth::user()->hasRole('adminlv2'))
								{
									$query-> where('RMB.id', $organization_id);
								}
								else if (Auth::user()->hasRole('adminlv3'))
								{
									$query-> where('SB.id', $organization_id);
								}
							})
                            ->where(function($query) use($request){
                              //check seg name
                              if ($request->segment_name != null)
                              {
                                if (App::getLocale() == 'en')
                                {
                                  $query->where('tblSegment.segname_en', 'like', '%'.$request->segment_name.'%');
                                }
                                else
                                {
                                  $query->where('tblSegment.segname_vn', 'like', '%'.$request->segment_name.'%');
                                }
                              }
                            })
                            ->where(function($query) use ($request){
                              //check route
                              if ($request->route != 0)
                              {
                                $query->where('tblBranch.id', $request->route);
                              }
                            })
                            ->where(function($query) use ($request){
                              //check branch
                              if ($request->branch != 0)
                              {
                                $query->where('tblBranch.id', $request->branch);
                              }
                            })
                            ->where(function($query) use($request){
                              //check rmb
                              if ($request->rmb != 0)
                              {
                                $query->where('RMB.id', $request->rmb);
                              }
                            })
                            ->where(function($query) use($request){
                              //check sb
                              if ($request->sb != 0)
                              {
                                $query->where('SB.id', $request->sb);
                              }
                            })
                            ->where(function($query) use($request){
                              //check km_from
                              if ($request->km_f !=0)
                              {
                                $query->where('tblSegment.km_from', '>=' , $request->km_f);
                              }
                            })
                            ->where(function($query) use($request){
                              //check km_to
                              if ($request->km_t !=0)
                              {
                                $query->where('tblSegment.km_to', '<=' , $request->km_t);
                              }
                            })
                            // ->orderBy('tblSegment.id', 'desc')
                            ->select('tblSegment.id as id', 'tblSegment.segname_en as sname_en' , 'tblSegment.segname_vn as sname_vn' ,
                             		 // 'tblRoad.name_en as rname_en', 'tblRoad.name_vn as rname_vn' , 
                                     'tblBranch.name_en as bname_en', 'tblBranch.name_vn as bname_vn', 'tblBranch.branch_number',
                                     'SB.name_en as oname_en',
                                     'SB.name_vn as oname_vn', 'RMB.name_en as ontext','RMB.name_vn as ontext_vn', 
                                     'tblSegment.km_from as km_f', 'tblSegment.m_from as m_f', 'tblSegment.km_to as km_t', 'tblSegment.m_to as m_t', 'cfrom.name_en as cfname_en',
                                     'cfrom.name_vn as cfname_vn', 'dfrom.name_en as dfname_en', 'dfrom.name_vn as dfname_vn', 'cto.name_en as ctname_en', 'cto.name_vn as ctname_vn',
                                     'dto.name_vn as dtname_vn', 'dto.name_en as dtname_en', 'tblSegment.commune_from as comf','tblSegment.commune_to as comt' );
							                // ->get();
                return Datatables::of($segment)
                    ->editColumn('km_from', '{{$km_f}} + {{$m_f}}')
                    ->editColumn('km_to', '{{$km_t}} + {{$m_t}}')
                    ->make(true);
                break;
				
			case 'merge_segment':
                $segment = DB::table('tblSegment')
                            ->leftjoin('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id')
                            // ->leftjoin('tblRoad', 'tblRoad.id', '=', 'tblBranch.route_id')
                            ->leftjoin('tblOrganization as SB', 'SB.id', '=', 'tblSegment.SB_id')
                            ->leftjoin('tblOrganization as RMB', 'RMB.id', '=', 'SB.parent_id')
                            ->leftjoin('mstDistrict as dfrom', 'dfrom.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstProvince as cfrom', 'cfrom.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstDistrict as dto', 'dto.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstProvince as cto', 'cto.id', '=', 'tblSegment.distfrom_id')
                            ->select('tblSegment.id', 'tblSegment.segname_en as sname_en' , 'tblSegment.segname_vn as sname_vn' , 
                                     'tblBranch.name_en as bname_en', 'tblBranch.name_vn as bname_vn', 'SB.name_en as oname_en',
                                     'SB.name_vn as oname_vn', 'RMB.name_en as ontext', 'cfrom.name_en as cfname_en',
                                     DB::raw("CONCAT(tblSegment.km_from, ' + ', tblSegment.m_from) AS from_segment"),DB::raw("CONCAT(tblSegment.km_to, ' + ', tblSegment.m_to) AS to_segment"), 
                                     'cfrom.name_vn as cfname_vn', 'dfrom.name_en as dfname_en', 'dfrom.name_vn as dfname_vn', 'cto.name_en as ctname_en', 'cto.name_vn as ctname_vn',
                                     'dto.name_vn as dtname_vn', 'dto.name_en as dtname_en', 'tblSegment.commune_from as comf','tblSegment.commune_to as comt' );
				if (isset($request->branch_id) && isset($request->sb_id))
				{
					$segment = $segment->where('tblSegment.branch_id', $request->branch_id)->where('tblSegment.SB_id', $request->sb_id);
				}
                $segment = $segment->orderBy('km_from', 'asc')->orderBy('m_from', 'asc')->get();
               return Datatables::of($segment)
                    ->make(true);
                break;
				
			case 'repair_matrix':
                 // $repair_matrix = tblRepairMatrix::leftJoin('users as u', 'u.id', '=', 'tblRepair_matrix.created_by')
					                          // ->select('tblRepair_matrix.id as id', 'tblRepair_matrix.name as name',"tblRepair_matrix.created_at as aaaaa",'u.name as username');
					                          	// DB::raw("DATE_FORMAT(tblRepair_matrix.created_at, '%d-%m-%Y') as created_at"),'u.name as username')
					                          // ->get();
				$repair_matrix = DB::table('tblRepair_matrix')
									->whereNull('tblRepair_matrix.deleted_at')
									->leftJoin('users as u', 'u.id', '=', 'tblRepair_matrix.created_by')
		                          	->select('tblRepair_matrix.id as id', 'tblRepair_matrix.name as name',"tblRepair_matrix.created_at as aaaaa",'u.name as username',
		                          		DB::raw("DATE_FORMAT(tblRepair_matrix.created_at, '%d-%m-%Y') as created_at"),'u.name as username')
									->get();		  
                $delete = trans('back_end.delete');
                $edit = trans('back_end.edit');

                return Datatables::of($repair_matrix)
					// ->editColumn('created_at', date('d-m-Y', '{{$created_at}}'))
                    ->addColumn('action', function ($repair_matrix) use($edit, $delete) {
                        return view('admin.templates.btn')->with(array(
                              'edit' => ['edit' => $edit,'id' => $repair_matrix->id, 'link' => 'repair_matrix/'.$repair_matrix->id], 
                              'delete' => ['id' => $repair_matrix->id, 'action' => 'repair_matrix.destroy','delete' => $delete ,'title' => trans('back_end.sure_want_delete')],
                          ));
                    })
                    ->make(true);
                break;
				
            default:
                
                break;
        }
    }
    
    static function GetConfigTable($name)
    {
        switch ($name) {
            case 'admin':
            if(App::getLocale() == 'en')
                {
                  $collumns = [
                      ['data' => 'user_name', 'header' => trans('back_end.name_admin'), 'name' => 'user_name'],
                      ['data' => 'created_at', 'header' => trans('back_end.created'), 'name' => 'users.created_at'],
                      ['data' => 'role_name', 'header' => trans('back_end.level'), 'name' => 'role_name'],

                      ['data' => 'oname_en', 'header' => trans('back_end.organization'), 'name' => 'organization'],
                      ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                  ];
                  return ($collumns);
                  break;
                }
            else 
            {
                $collumns = [
                      ['data' => 'user_name', 'header' => trans('back_end.name_admin'), 'name' => 'user_name'],
                      ['data' => 'created_at', 'header' => trans('back_end.created'), 'name' => 'users.created_at'],
                      ['data' => 'role_name', 'header' => trans('back_end.level'), 'name' => 'role_name'],

                      ['data' => 'oname_vn', 'header' => trans('back_end.organization'), 'name' => 'organization'],
                      ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                  ];
                  return ($collumns);
                  break;
            }
                //  duc.dn  08.11.2016
            case 'organization':
                if(App::getLocale() == 'en')
                {
                  $collumns = [
                      ['data' => 'id', 'header' => trans('back_end.no'), 'name' => 'id'],
                      ['data' => 'name_en', 'header' => trans('back_end.name'), 'name' => 'name_en'],
                      ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                  ];
                }
                else
                {
                  $collumns = [
                      ['data' => 'id', 'header' => trans('back_end.no'), 'name' => 'id'],
                      ['data' => 'name_vn', 'header' => trans('back_end.name'), 'name' => 'name_vn'],
                      ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                  ];
                }
                return ($collumns);
                break;
            case 'SB':
                if(App::getLocale() == 'en')
                {
                  $collumns = [
                      ['data' => 'id', 'header' => trans('back_end.no'), 'name' => 'id'],
                      ['data' => 'name_en', 'header' => trans('back_end.name'), 'name' => 'name_en'],
                      // ['data' => 'RMB_name_en', 'header' => trans('back_end.name_RMB'), 'name' => 'RMB_name'],
                      ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                  ];
                }
                else
                {
                  $collumns = [
                      ['data' => 'id', 'header' => trans('back_end.no'), 'name' => 'id'],
                      ['data' => 'name_vn', 'header' => trans('back_end.name'), 'name' => 'name_vn'],
                      // ['data' => 'RMB_name_vn', 'header' => trans('back_end.name_RMB'), 'name' => 'RMB_name'],
                      ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                  ];
                }
                return ($collumns);
                break;
			      case 'Merge':
                if(App::getLocale() == 'en')
                {
                  $collumns = [
                      ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => false],
                      ['data' => 'sname_en', 'name' => 'sname_en']
                  ];
                }
                else
                {
                  $collumns = [
                      ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => false],
                      ['data' => 'sname_vn', 'name' => 'sname_vn']
                      
                  ];
                }
                return ($collumns);
                break;

            case 'MergeRMB':
                if(App::getLocale() == 'en')
                {
                  $collumns = [
                      ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => false],
                      ['data' => 'sname_en', 'name' => 'sname_en']
                  ];
                }
                else
                {
                  $collumns = [
                      ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => false],
                      ['data' => 'sname_vn', 'name' => 'sname_vn']
                      
                  ];
                }
                return ($collumns);
                break;
            case 'rank':
                
                  $collumns = [
                      ['data' => 'rank', 'name' => 'rank'],
                      ['data' => 'type', 'name' => 'type'],
                      ['data' => 'infor', 'name' => 'info','orderable' => false, 'searchable' => true]
                  ];
                
                
                return ($collumns);
                break;
                // end modify
            case 'user':
                if(App::getLocale() == 'en')
                $collumns = [
                    ['data' => 'user_name', 'header' => trans('back_end.name_user'), 'name' => 'user_name'],
                    ['data' => 'oname_en', 'header' => trans('back_end.organization'), 'name' => 'oname_en'],
                    ['data' => 'olevel', 'header' => trans('back_end.level'), 'name' => 'olevel'],
                    ['data' => 'created_at', 'header' => trans('back_end.created'), 'name' => 'users.created_at'],
                    ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                ];
                else
                {
                    $collumns = [
                    ['data' => 'user_name', 'header' => trans('back_end.name_user'), 'name' => 'user_name'],
                    ['data' => 'oname_vn', 'header' => trans('back_end.organization'), 'name' => 'oname_vn'],
                    ['data' => 'olevel', 'header' => trans('back_end.level'), 'name' => 'olevel'],
                    ['data' => 'created_at', 'header' => trans('back_end.created'), 'name' => 'users.created_at'],
                    ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                ];
                }
                return ($collumns);
                break;
            case 'branch':
                $collumns = [
                    ['data' => 'user_name', 'header' => trans('back_end.name_user'), 'name' => 'user_name'],
                    ['data' => 'oname_vn', 'header' => trans('back_end.organization'), 'name' => 'oname_vn'],
                    ['data' => 'olevel', 'header' => trans('back_end.level'), 'name' => 'olevel'],
                    ['data' => 'created_at', 'header' => trans('back_end.created'), 'name' => 'users.created_at'],
                    ['data' => 'action', 'header' => trans('back_end.action'),'name' => 'action' , 'orderable' => false, 'searchable' => false]
                ];
                return ($collumns);
                break;
            case 'segment':
                if(App::getLocale() == 'en')
                {
                    $collumns = [
                        // ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => false],
                        ['data' => 'id', 'name' => 'id'],
                        ['data' => 'sname_en', 'name' => 'sname_en', 'orderable' => true, 'searchable' => true],
                        ['data' => 'bname_en', 'name' => 'bname_en', 'orderable' => true, 'searchable' => false],
                        ['data' => 'branch_number', 'name' => 'branch_number', 'orderable' => true, 'searchable' => false],
                        ['data' => 'ontext', 'name' => 'ontext', 'orderable' => true, 'searchable' => false],
                        ['data' => 'oname_en', 'name' => 'oname_en', 'orderable' => true, 'searchable' => false],
                        ['data' => 'km_from', 'name' => 'km_f', 'orderable' => true, 'searchable' => false],
                        ['data' => 'km_to', 'name' => 'km_t', 'orderable' => true, 'searchable' => false],
                        // ['data' => 'cfname_en', 'name' => 'cfname_en'],
                        // ['data' => 'dfname_en', 'name' => 'dfname_en'],
                        // ['data' => 'comf', 'name' => 'comf'],
                        // ['data' => 'ctname_en', 'name' => 'ctname_en'],
                        // ['data' => 'dtname_en', 'name' => 'dtname_en'],
                        // ['data' => 'comt', 'name' => 'comt']
                    ];
                }
                else
                {
                    $collumns = [
                        // ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => false],
                        ['data' => 'id', 'name' => 'id'],
                        ['data' => 'sname_vn', 'name' => 'sname_vn', 'orderable' => true, 'searchable' => false],
                        ['data' => 'bname_vn', 'name' => 'bname_vn', 'orderable' => true, 'searchable' => false],
                        ['data' => 'branch_number', 'name' => 'branch_number', 'orderable' => true, 'searchable' => false],
                        ['data' => 'ontext_vn', 'name' => 'ontext_vn', 'orderable' => true, 'searchable' => false],
                        ['data' => 'oname_vn', 'name' => 'oname_vn', 'orderable' => true, 'searchable' => false],
                        ['data' => 'km_from', 'name' => 'km_f', 'orderable' => true, 'searchable' => false],
                        ['data' => 'km_to', 'name' => 'tblSegment.km_to', 'orderable' => true, 'searchable' => false],
                        // ['data' => 'cfname_vn', 'name' => 'cfname_vn'],
                        // ['data' => 'dfname_vn', 'name' => 'dfname_vn'],
                        // ['data' => 'comf', 'name' => 'comf'],
                        // ['data' => 'ctname_vn', 'name' => 'ctname_vn'],
                        // ['data' => 'dtname_vn', 'name' => 'dtname_vn'],
                        // ['data' => 'comt', 'name' => 'comt']
                    ];
                }
                return $collumns;
                break;
				
			case 'merge_segment':
                if(App::getLocale() == 'en')
                {
                    $collumns = [
                        ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => true],
                        ['data' => 'id', 'name' => 'id', 'orderable' => false, 'searchable' => true],
                        ['data' => 'sname_en', 'name' => 'sname_en', 'orderable' => false, 'searchable' => true],
                        ['data' => 'from_segment', 'name' => 'from_segment', 'orderable' => false, 'searchable' => true],
                        ['data' => 'to_segment', 'name' => 'to_segment', 'orderable' => false, 'searchable' => true],
                    ];
                }
                else
                {
                    $collumns = [
                        ['data' => '', 'name' => '', 'orderable' => false, 'searchable' => true],
                        ['data' => 'id', 'name' => 'id', 'orderable' => false, 'searchable' => true],
                        ['data' => 'sname_vn', 'name' => 'sname_vn', 'orderable' => false, 'searchable' => true],
                        ['data' => 'from_segment', 'name' => "from_segment", 'orderable' => false, 'searchable' => true],
                        ['data' => 'to_segment', 'name' => 'to_segment', 'orderable' => false, 'searchable' => true],
                    ];
                }
                return $collumns;
                break;
				
			case 'repair_matrix':
                $collumns = [
                    ['data' => 'id', 'name' => 'id', 'header' => trans('back_end.id'),'orderable' => false, 'searchable' => true],
                    ['data' => 'name', 'name' => 'name','header' => trans('back_end.name'), 'orderable' => false, 'searchable' => true],
                    ['data' => 'created_at', 'name' => "created_at",'header' => trans('back_end.created_at'), 'orderable' => false, 'searchable' => true],
                    ['data' => 'username', 'name' => 'username','header' => trans('back_end.username'), 'orderable' => false, 'searchable' => true],
                    ['data' => 'action', 'name' => 'action' ,'header' => trans('back_end.action'), 'orderable' => false, 'searchable' => false]
                ];
                return $collumns;
                break;	
            default:
                
                break;
        }
    }
}
