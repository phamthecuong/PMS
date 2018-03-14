<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BackendRequest\AdminRequest;
use App\Http\Requests\BackendRequest\ChangePasswordRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tbManagement;
use App\Models\User;
use App\Models\User_role;
use App\Models\convertdata;
use App\Models\tblRoad;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
	function __construct()
	{
		if(!Auth::user()->hasRole('superadmin'))
        {
            Auth::logout();
		}
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword($id)
    {
        if(Auth::user()->hasRole('superadmin'))
        {
            return view('admin.user.change_password_admin',['id' => $id]);
        }
        else
        {
            Auth::logout();
        }
    }

    public function postChange(ChangePasswordRequest $request,$id)
    {
        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect(url('admin/admin_manager'))->with('change', trans('back_end.change_password_success'));
    }

    public function index()
    {
        return view('admin.user.index_admin');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.add_admin');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRequest $request)
    {
        /*if (count(User::where('name', $request->name)->get()))
        {
            return redirect()->back()->with('exist', trans('back_end.user-exist'));
        }*/
        DB::beginTransaction();
        try
        {
            $selected_role = $request->roles;
            $new_password = $request->password;
            $admin = new User();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->organization_id = $request->organization;
            $admin->active = '0';
            $admin->created_by = Auth::user()->id;
            $admin->save();
            $admin->roles()->attach($selected_role);
            $data = array(
                'password' => $new_password,
                'user_name' => $admin->name,
                'link' => 'pms.libre.com.vn/user_manager/'.$admin->id.'/active'
            );
            Mail::send('admin.mail_templates.mail_register', $data, function($message) use ($request)
            {
                $message->to($request->email)->subject(trans('back_end.user_name_and_password'));
            });
            DB::commit();
            return redirect(url('admin/admin_manager'))->with('warning', trans('back_end.created_admin_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
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
        $admin = User::find($id);
        return view('admin.user.add_admin', ['admin' => $admin]);
       /* if (Auth::user()->hasPermission("admin_management.view"))
        {

        }
        else
        {
        	return redirect('/user/logout');
        }*/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRequest $request, $id)
    {
        /*if (count(User::where('name', $request->name)->whereNotIn('id', [$id])->get()))
        {
            return redirect()->back()->with('exist', trans('back_end.user-exist'));
        }*/
        DB::beginTransaction();
        try
        {
            $selected_role = $request->roles;
            User_role::where('user_id', $id)->delete();
            $admin = User::find($id);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->organization_id = $request->organization;
            $admin->updated_by = Auth::user()->id;
            $admin->save();
            $admin->roles()->attach($selected_role);
           /* $data = array(
                'password' => $new_password,
                'user_name' => $admin->name,
                'link' => 'pms.libre.com.vn/user_manager/'.$admin->id.'/active'
            );
            Mail::send('admin.mail_templates.mail_register', $data, function($message) use ($request)
            {
                $message->to($request->email)->subject(trans('back_end.user_name_and_password'));
            });*/
            DB::commit();
            return redirect(url('admin/admin_manager'))->with('update', trans('back_end.updated_admin_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try
        {
            $user = User::find($id);
            User_role::where('user_id', $id)->delete();
            $user->delete();
            DB::commit();
            return back()->with('delete', trans('back_end.delete_success'));
        } catch (\Exception $e) {
        DB::rollBack();
        dd($e->getMessage());
}
    }
    

}
