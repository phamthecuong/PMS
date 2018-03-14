<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BackendRequest\ChangePasswordRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User_role;
use App\Http\Requests\BackendRequest\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
	function __construct()
	{
		if(Auth::user()->hasRole('userlvl1p') || Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlv3'))
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
        if(Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlvl1p') || Auth::user()->hasRole('userlv2') || Auth::user()->hasRole('userlv3'))
        {
            Auth::logout();
        }
        else
        {
            return view('admin.user.change_password',['id' => $id]);
        }
    }

    public function postChange(ChangePasswordRequest $request,$id)
    {
        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect(url('admin/user_manager'))->with('change', trans('back_end.change_password_success'));
    }

    public function index()
    {
        return view('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
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
            $users = new User();
            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);
            $users->created_by = Auth::user()->id;
            $users->active = '0';
            if (Auth::user()->hasRole('superadmin'))
            {
                $users->organization_id = $request->organization;
            }
            else
            {
                $users->organization_id = Auth::user()->organizations->id;
            }
            $users->save();
            $users->roles()->attach($selected_role);
            $data = array(
                'password' => $new_password,
                'user_name' => $users->name,
                'link' => 'pms.libre.com.vn/user_manager/'.$users->id.'/active'
            );
            Mail::send('admin.mail_templates.mail_register', $data, function($message) use ($request)
            {
                $message->to($request->email)->subject(trans('back_end.user_name_and_password'));
            });
            DB::commit();
            return redirect(url('admin/user_manager'))->with('warning', trans('back_end.created_user_success'));
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
        $user = User::find($id);
        return view('admin.user.add', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
       /* if (count(User::where('name', $request->name)->whereNotIn('id', [$id])->get()))
        {
            return redirect()->back()->with('exist', trans('back_end.user-exist'));
        }*/
        DB::beginTransaction();
        try
        {
            $selected_role = $request->roles;
            User_role::where('user_id', $id)->delete();
            $users = User::find($id);
            $users->name = $request->name;
            $users->email = $request->email;
            $users->updated_by = Auth::user()->id;
            if (Auth::user()->hasRole('superadmin'))
            {
                $users->organization_id = $request->organization;
            }
            else
            {
                $users->organization_id = Auth::user()->organizations->id;
            }
            $users->save();
            $users->roles()->attach($selected_role);
            /* $data = array(
                 'password' => $new_password,
                 'user_name' => $users->name,
                 'link' => 'pms.libre.com.vn/user_manager/'.$users->id.'/active'
             );
             Mail::send('admin.mail_templates.mail_register', $data, function($message) use ($request)
             {
                 $message->to($request->email)->subject(trans('back_end.user_name_and_password'));
             });*/
            DB::commit();
            return redirect(url('admin/user_manager'))->with('update', trans('back_end.updated_admin_success'));
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
            $users = User::find($id);
            User_role::where('user_id', $id)->delete();
            $users->delete();
            DB::commit();
            return back()->with('delete', trans('back_end.delete_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    public function getActive($id)
    {
        $user = User::find($id);
        $user->active = "1";
        $user->save();
        Session::put('class', 'alert alert-success');
        Session::put('message', trans('back_end.active_success'));
        return redirect()->route('user_manager.index');
    }
    
    public function getNotActive($id)
    {
        $user = User::find($id);
        $user->active = "0";
        $user->save();
        Session::put('class', 'alert alert-success');
        Session::put('message', trans('back_end.inactive_success'));
        return redirect()->route('user_manager.index');
    }
}
