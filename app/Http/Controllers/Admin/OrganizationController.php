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
use App;


class OrganizationController extends Controller
{
	public function __construct()
    {
    	// if(!Auth::user()->hasRole('superadmin'))
        // {
        	// return redirect('/user/logout');
		// }
	}
    
/*show view index RMB*/
    public function index(Request $request)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
        	
    		if ( $request->name != NULL){
    			Session()->put('class', 'alert alert-success ');
           		Session()->put('message', trans('back_end.merger_rmb_successful'));
				return redirect()->route('organization.index');
    		}
			
			if ( $request->RMB_creat != NULL){
    			Session()->put('class', 'alert alert-success ');
           		Session()->put('message', trans('back_end.create_rmb_successful'));
				return redirect()->route('organization.index');
    		}
			
			if ( $request->update_RMB != NULL){
    			Session()->put('class', 'alert alert-success ');
           		Session()->put('message', trans('back_end.update_rmb_successful'));
				return redirect()->route('organization.index');
    		}
            return view('admin.organization.RMB.index')->with(array('case' => 'organization'));
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

/*show view create new RMB*/
    public function create()
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {

            return view('admin.organization.RMB.create');
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

/* handling port view create*/
    public function store(Request $request)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
        	$id_user = Auth::user()->id;
			// dd($id_user);die;
            $x = trim($request->name_vn);
            $y = trim($request->name_en);
            $a = tblOrganization::where('name_vn', $x)->first();
            $b = tblOrganization::where('name_en', $y)->first();
			
			if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $x))
            {
				Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_only_text_number'));
                return redirect()->route('organization.create');

			}
			if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $y))
            {
				Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_only_text_number'));
                return redirect()->route('organization.create');
			}
            // name_en empty 
            if (trim($request->name_en) == '')
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_en_not_null'));
                return redirect()->route('organization.create');
            }
            // name_vn empty
            if (trim($request->name_vn) == '')
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_vn_not_null'));
                return redirect()->route('organization.create');
            }
            // name_vn exit
            if ($a != NULL )
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_vn_exist'));
                return redirect()->route('organization.create');
            }
            // name_en exit
            if ($b != NULL)
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_en_exist'));
                return redirect()->route('organization.create');
            }

            $h = date('Y-m-d');
            $tblOrganization = new tblOrganization;
            $tblOrganization->name_vn = $x;
            $tblOrganization->name_en = $y;
            $tblOrganization->level = 2;
            $tblOrganization->number_text = '';
            $tblOrganization->headquarter_en = '';
            $tblOrganization->headquarter_vn = '';
            $tblOrganization->created_by = $id_user;
            $tblOrganization->effect_at = $h;
            $tblOrganization->created_at = date("Y-m-d H:i:s");
            $tblOrganization->parent_id = null;
            // echo $tblOrganization;die;
            $tblOrganization->save();
            return redirect()->route('organization.index',['RMB_creat=true']);
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }


    public function show($id)
    {
        //
    }

 /*show view edit */
    public function edit($id)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
            $Role = Role::get();
            $user = tblOrganization::find($id);
            $a = $user->name_en;
            $a1 = $user->name_vn;
            $a2 = $user->headquarter_en ;
            $a3 = $user->headquarter_vn;
            return view('admin.organization.RMB.create')->with(array('user' => $user ,'name_en' => $a , 'name_vn' => $a1 ,'headquarter_en' => $a2 ,'headquarter_vn' => $a3 ,'id' => $id));
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

/* handing post edit */
    public function update(Request $request, $id)
    {
        
    	$id_user = auth::user()->id;
		// dd($id_user);die;
        $x = trim($request->name_vn);
        $y = trim($request->name_en);
        $c = tblOrganization::find($id);
        $name_vn_exist  = tblOrganization::where('name_vn',$x)->where('name_vn','!=',$c->name_vn)->first();
        $name_en_exist  = tblOrganization::where('name_en',$y)->where('name_en','!=',$c->name_en)->first();
		if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $x)){
			Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_only_text_number'));
            return redirect()->route('organization.create');
		}
		if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $y)){
			Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_only_text_number'));
            return redirect()->route('organization.create');
		}
        // when no modify
        if ($c->name_en == $y && $c->name_vn == $x)
        {
            Session()->put('class', 'alert alert-success');
            Session()->put('message', trans('organization.edit_rmb_success'));
            return redirect()->route('organization.index');
        }
        // when name_en exit
        if ( $name_en_exist != NULL)
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_en_exist'));
            return redirect()->back();
        }
        // when name_vn exit
        if ( $name_vn_exist != NULL)
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_vn_exist'));
            return redirect()->back();
        }
        // empty name vn
        if (trim($request->name_vn) == '')
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_vn_not_null'));
            return redirect()->back();
        }
        // empty name en
        if (trim($request->name_en) == '')
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_en_not_null'));
            return redirect()->back();
        }
        $tblOrganization = tblOrganization::find($id);
        $tblOrganization->name_vn = $request->name_vn;
        $tblOrganization->name_en = $request->name_en;
        $tblOrganization->level = 2;
        $tblOrganization->number_text = '';
		$tblOrganization->updated_by = $id_user;
		$tblOrganization->updated_at = date("Y-m-d H:i:s");
        $tblOrganization->save();
        Session()->put('class', 'alert alert-success');
        Session()->put('message', trans('back_end.edit_rmb_success'));
        return redirect()->route('organization.index',['update_RMB=true']);

    }

    /**
     * handing method delete from button delete in view list (index)
     */
    public function destroy($id)
    {
    	// dd($id);die;
        $user = tblOrganization::find($id);
        $a = $user->id;
        $b = tblOrganization::where('parent_id', $a)->first();
        if ($b == NULL) {
            $user->delete();
            return back();
        }
        Session()->put('class', 'alert alert-danger');
        Session()->put('message', trans('back_end.cant_delete'));
        return back();
    }

    /**
     * show view merge SB
     */
	public function Merge($id,$idSB)
	{
		if (Auth::user()->hasPermission("admin_management.view"))
        {
        	$tblOrganization = tblOrganization::where('parent_id',$id)->get();
			$RMD = tblOrganization::find($id);
            $RMD=$RMD->id;
			$SB = tblOrganization::find($idSB);
			$SB_id = array();
			$SB_name = array();
			if ( App::getLocale() == 'en' ) 
			{
				$SB = $SB->name_en;
				foreach ($tblOrganization as $key => $value) 
				{
					$SB_id [] = $value->id;
					$SB_name[] = $value->name_en;
				}
			}
			
			else 
			{
				$SB = $SB->name_vn;
				foreach ($tblOrganization as $key => $value) 
				{
					$SB_id [] = $value->id;
					$SB_name[] = $value->name_vn;
				}
			}
            return view('admin.organization.SB.merge')->with(array(
            'case' => 'Merge', 
            'id' => $SB_id,
            'name' => $SB_name,
			'custom' => 'sb_id='.$id,
			'RMD' => $RMD,
			'SB' => $SB
			));
        }

        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
	}
/*handing post view merge SB*/
    public function MergeSB(Request $request ){
    	// dd($request->all());die;
        $user_id = Auth::user()->id;
		// dd($user_id);die;
        $newOrganization = new tblOrganization;
        $newOrganization->name_vn = $request->name_vi;
        $newOrganization->name_en = $request->name_en;
        $newOrganization->number_text = '';
        $newOrganization->headquarter_en = '';
        $newOrganization->headquarter_vn = '';
        $newOrganization->level = 3;
		$newOrganization->created_by = $user_id;
        $newOrganization->created_at = date("Y-m-d");
		$newOrganization->parent_id = $request->RMD_id;
        $newOrganization->effect_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
        $newOrganization->save();
// get new organization 
        $newOrganization::where('name_en' , $request->name_en)->first();
        $new_SB_id = $newOrganization->id;
        
        if ( App::getLocale() == 'en' ) 
        {
            $data = array();
            foreach ($request->segment as $key => $value)
            {
                $tblOrganization = tblOrganization::where('name_en',$value)->first();
                    $data[] = array(
                    'name_vn' => $tblOrganization->name_vn,
                    'name_en' => $tblOrganization->name_en,
                    'id' => $tblOrganization->id,
                    'RMB_id' => $tblOrganization->parent_id,
                );
            }
        }
        else
        {
            $data = array();
            foreach ($request->segment as $key => $value)
            {
                $tblOrganization = tblOrganization::where('name_vn',$value)->first();
                $data[] = array(
                    'name_vn' => $tblOrganization->name_vn,
                    'name_en' => $tblOrganization->name_en,
                    'id' => $tblOrganization->id,
                    'RMB_id' => $tblOrganization->parent_id,
                );
            }
        }
        $data_segment = array();
        foreach ($data as $key => $value )
        {
            $SB_id = ($value['id']);
// updade nullity_at = efect_at tblorganization
			$Organization_time = tblOrganization::find($SB_id);
 			$Organization_time->nullity_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
			$Organization_time->save();
// update tblSegment
			DB::table('tblSegment')
            ->where('SB_id', $SB_id)
            ->update(['SB_id' => $new_SB_id]);
// update tblSection (SB_id = new ) 			
			DB::table('tblSection')
            ->where('SB_id', $SB_id)
            ->update(['SB_id' => $new_SB_id]);
// 			
            $tblMergeSplit = new tblMergeSplit;
            $tblMergeSplit->user_id = $user_id;
            $tblMergeSplit->action = 1;
            $tblMergeSplit->object = 2;
            $tblMergeSplit->save();
            $merge_split_id = $tblMergeSplit->id;
            // dd($merge_split_id);die;
            $tblMergeSplitDetail = new tblMergeSplitDetail;
            $tblMergeSplitDetail->merge_split_id = $merge_split_id;
            $tblMergeSplitDetail->from = $SB_id;
            $tblMergeSplitDetail->to = $new_SB_id ;
            $tblMergeSplitDetail->save();
			
        } 
        if ( App::getLocale() == 'en' ) 
        {
            foreach ($request->segment as $key => $value)
            {
                $tblOrganization = tblOrganization::where('name_en',$value)->first();
                $tblOrganization->delete();
            }
        }
        else
        {
            foreach ($request->segment as $key => $value)
            {
                $tblOrganization = tblOrganization::where('name_vn',$value)->first();
				
                $tblOrganization->delete();
            }
        }
//       
        return response()->json(array(
                'code' => 200,
                'description' => 'success',
                'RMB_id' => $request->RMD_id,
            ));
    }
/* show view merge RMB */
	public function MergeRMB ( $id ){
       if (Auth::user()->hasPermission("admin_management.view"))
        {
            // echo $id ;die;
            $name = tblOrganization::find($id);
            // dd($name->name_en);die;
            if ( App::getLocale() == 'en' ){
                $name = $name->name_en;
            }
            else {
                $name = $name->name_vn;
            }

            // dd($name->name_en);die;
            return view('admin.organization.RMB.merge')->with(array(
                'case' => 'MergeRMB',
                'id'   => $id,
                'name' => $name
                // 'rmb'  =>
                ));
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }
/* handing post view merge RMB */
    public function PostMergeRMB (Request $request ){
    	// dd($request->all());die;
       if (Auth::user()->hasPermission("admin_management.view"))
        {
        /*create new organization*/
            $newOrganization = new tblOrganization;
            $newOrganization->name_vn = $request->name_vi;
            $newOrganization->name_en = $request->name_en;
            $newOrganization->number_text = '';
            $newOrganization->level = 2;
            $newOrganization->headquarter_en = '';
            $newOrganization->headquarter_vn = '';
            $newOrganization->parent_id = 0;
			
			$newOrganization->created_by = Auth::user()->id;
			$newOrganization->created_at = date("Y-m-d H:i:s");
            $newOrganization->effect_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
            $newOrganization->save();
        /* id RMB */
            $newOrganization::where('name_en' , $request->name_en)->first();
            $new_RMB_id = $newOrganization->id;
        /*userlogin_id*/
            $user_id = Auth::user()->id;
        /* get data RMB meger*/
            if ( App::getLocale() == 'en' ) 
            {
                $data = array();
                foreach ($request->segment as $key => $value)
                {
                    $tblOrganization = tblOrganization::where('name_en',$value)->first();
                        $data[] = array(
                        'name_vn'   =>  $tblOrganization->name_vn,
                        'name_en'   =>  $tblOrganization->name_en,
                        'id'        =>  $tblOrganization->id,
                    );
                }
            }
            else
            {
                $data = array();
                foreach ($request->segment as $key => $value)
                {
                    $tblOrganization = tblOrganization::where('name_vn',$value)->first();
                    $data[] = array(
                        'name_vn' => $tblOrganization->name_vn,
                        'name_en' => $tblOrganization->name_en,
                        'id' => $tblOrganization->id,
                    );
                }
            }
        /* use data[]  */
            foreach ($data as $key => $value)
            {

            /*id RMB*/
                $id_RMB = $value['id'];
			// updade nullity_at = efect_at tblorganization
				$Organization_time = tblOrganization::find($id_RMB);
	 			$Organization_time->nullity_at = date("Y-m-d H:i:s", strtotime($request->date_effected));
				$Organization_time->save();
            /*find parent_id  change parent_id SB */
                $sb = tblOrganization::where('parent_id',$id_RMB)->first();
                // var_dump($sb);
                if ( $sb != NULL){
                    $sb->parent_id = $new_RMB_id;
                    $sb->save();
                }
            /*tblMergeSplit */
                $tblMergeSplit = new tblMergeSplit;
                $tblMergeSplit->user_id = $user_id;
                $tblMergeSplit->action = 1;
                $tblMergeSplit->object = 2;
                $tblMergeSplit->save();
                $merge_split_id = $tblMergeSplit->id;
            /*tblMergeSplitDetail*/
                $tblMergeSplitDetail = new tblMergeSplitDetail;
                $tblMergeSplitDetail->merge_split_id = $merge_split_id;
                $tblMergeSplitDetail->from = $id_RMB;
                $tblMergeSplitDetail->to = $new_RMB_id ;
                $tblMergeSplitDetail->save();
            }
        /*delete RMB*/
            if ( App::getLocale() == 'en' ) 
            {
                foreach ($request->segment as $key => $value)
                {
                    $tblOrganization = tblOrganization::where('name_en',$value)->first();
                    $tblOrganization->delete();
                }
            }
            else
            {
                foreach ($request->segment as $key => $value)
                {
                    $tblOrganization = tblOrganization::where('name_vn',$value)->first();
                    $tblOrganization->delete();
                }
            }
            /**/
            return response()->json(array(
                'code' => 200,
                'description' => 'success',
                // 'segment_id' => $request->RMD_id,
            ));

        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

    public function showlistSB($id , Request $request)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
        	// dd($id);die;
        		if ( $request->name != NULL){
        			Session()->put('class', 'alert alert-success ');
               		Session()->put('message', trans('back_end.merger_sb_successful'));
                	// return redirect()->back();
					 return redirect()->route('listSB',[$id]);
				}
				
				if ( $request->update_SB != NULL){
        			Session()->put('class', 'alert alert-success ');
               		Session()->put('message', trans('back_end.update_sb_successful'));
					// return redirect()->route('listSB');
					return redirect()->route('listSB',[$id]);
					
        		}
				
				if ( $request->create_sb != NULL){
        			Session()->put('class', 'alert alert-success ');
               		Session()->put('message', trans('back_end.create_sb_successful'));
					// return redirect()->route('listSB');
					return redirect()->route('listSB',[$id]);
        		}
        	
        	
        	// dd($id);die;
        	$name_rmd = tblOrganization::find($id);
			// dd($name_rmd->name_vn);die;
			
            if ( App::getLocale() == 'en')
            {
                $name_rmd = $name_rmd->name_en ;
            }
            else 
            {
                $name_rmd = $name_rmd->name_vn ;
            }

            // dd($name);die;


            return view('admin.organization.SB.index')->with(array(
                'case' => 'SB', 
                // id RMB
                'id' => $id,
                'name_rmd' => $name_rmd,
                'custom' => 'sb_id='.$id ,
                // 'successfull' => $successful 
            ));
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

    public function sbcreate($id)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
            return view('admin.organization.SB.create')->with(array('id' => $id));
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

    public function sbstore(Request $request)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
            $idd = $request->idd;
            $x = trim($request->name_vn);
            $y = trim($request->name_en);
            $a = tblOrganization::where('name_vn', $x)->first();
            $b = tblOrganization::where('name_en', $y)->first();
			
			if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $x)){
				Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_only_text_number'));
                return redirect()->route('organization.create');

			}
			if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $y)){
				Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_only_text_number'));
                return redirect()->route('organization.create');
			}
            // name_en null
            if (trim($request->name_en) == '')
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_en_not_null'));
                return redirect()->route('sbcreate',[$idd]);
            }
            // name_vn null
            if (trim($request->name_vn) == '')
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_vn_not_null'));
                return redirect()->route('sbcreate',[$idd]);
            }
            // name_en exit 
            if ($b != NULL)
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_en_exist'));
                return redirect()->route('sbcreate',[$idd]);
            }
            // name_vn exit
            if ($a != NULL )
            {
                Session()->put('class', 'alert alert-danger');
                Session()->put('message', trans('back_end.name_vn_exist'));
                return redirect()->route('sbcreate',[$idd]);
            }
                $h = date('Y-m-d');
                $tblOrganization = new tblOrganization;
                $tblOrganization->name_vn = $x;
                $tblOrganization->name_en = $y;
                $tblOrganization->level = 3;
                $tblOrganization->number_text = '';
                $tblOrganization->parent_id = $idd;
                $tblOrganization->headquarter_en = '';
                $tblOrganization->headquarter_vn = '';
				$tblOrganization->created_by = Auth::user()->id;
                $tblOrganization->effect_at =  $h;
                $tblOrganization->created_at = $h;
                $tblOrganization->save();
                return redirect()->route('listSB',[$idd , 'create_sb=true']);
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

    public function editSB($id)
    {
        if (Auth::user()->hasPermission("admin_management.view"))
        {
            $Role = Role::get();
            $user = tblOrganization::find($id);
            $a = $user->name_en;
            $a1 = $user->name_vn;
            $a2 = $user->headquarter_en ;
            $a3 = $user->headquarter_vn;
            return view('admin.organization.SB.create')->with(array('user' => $user ,'name_en' => $a , 'name_vn' => $a1 ,'id' => $id));
        }
        else
        {
        	return redirect('/user/logout');
            // Auth::logout();
            // return redirect()->route('back.end');
        }
    }

    public function updateSB(Request $request, $id)
    {
        $x = trim($request->name_vn);
        $y = trim($request->name_en);
        $c = tblOrganization::find($id);
        $a = tblOrganization::where('name_vn', $x)->where('name_vn','!=',$c->name_vn)->first();
        $b = tblOrganization::where('name_en', $y)->where('name_en','!=',$c->name_en)->first();
		
		if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $x)){
			Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_only_text_number'));
            return redirect()->route('organization.create');

		}
		if (preg_match('/[\'^£$%&*()}{@#~?!><>,|=+¬]/', $y)){
			Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_only_text_number'));
            return redirect()->route('organization.create');
		}
        
        //  when no modify 
        if ($c->name_en == $y && $c->name_vn == $x)
        {
            return redirect()->route('listSB',[$c->parent_id]);
			
        }
        // when name_en exit 
        if ( $b != NULL)
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_en_exist'));
            return redirect()->back();
        }
        // when name_vn exit 
        if (  $a != NULL)
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_vn_exist'));
            return redirect()->back();
        }
        // name_vn empty
        if (trim($request->name_vn) == '')
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_vn_not_null'));
            return redirect()->back();
        }
        // name_en empty
        if (trim($request->name_en) == '')
        {
            Session()->put('class', 'alert alert-danger');
            Session()->put('message', trans('back_end.name_en_not_null'));
            return redirect()->back();
        }
        $tblOrganization = tblOrganization::find($id);
        $tblOrganization->name_vn = $request->name_vn;
        $tblOrganization->name_en = $request->name_en;
        $tblOrganization->level = 3;
        $tblOrganization->number_text = '';
        $tblOrganization->parent_id = $c->parent_id;
		$tblOrganization->updated_by = Auth::user()->id;
		$tblOrganization->updated_at = date("Y-m-d H:i:s");
        $tblOrganization->save();
        return redirect()->route('listSB',[$c->parent_id , 'update_SB=true']);
    }

    public function destroySB($id)
    {
        // dd($id);die;
        $tblOrganization = tblOrganization::find($id);
        // $a = $user->id;
        $b = tblSegment::where('SB_id', $id)->first();
        // dd($b);die();
        if ($b == NULL) {
            $tblOrganization->delete();
			Session::put('class', 'alert alert-success');
        	Session::put('message', trans('back_end.delete_succes'));
            return back();
        }
        Session()->put('class', 'alert alert-danger');
        Session()->put('message', trans('back_end.cant_delete_exist_segment'));
		
        return back();
    }

}
