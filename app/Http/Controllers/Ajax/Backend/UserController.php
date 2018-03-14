<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->hasRole('superadmin'))
        {
            $users = User::with('roles', 'organizations')
                ->whereHas('roles', function ($query)
                {
                    $query->where('code', 'like', '%'.'user'.'%');
                })->get();
        }
        else
        {
            $code = substr(Auth::user()->roles[0]->name, 7, 7);
            $users = User::with('roles', 'organizations')
                ->whereHas('roles', function ($query) use($code)
                {
                    $query->where('code', 'like', '%'.'userlv'.$code.'%')
                        ->orWhere('code', 'like', '%'.'userlvl'.$code.'p'.'%');
                })->get();
        }
        return Datatables::of($users)
            ->addColumn('organization', function ($u){
                $organization = @$u->organizations->organization_name;
                if ($organization != null)
                {
                    return $organization;
                }
                else
                {
                    return "";
                }
            })
            ->addColumn('action', function($u) {
                $action = [];
                $action[] = \Form::lbButton(
                    "admin/user_manager/{$u->id}/edit",
                    'get',
                    trans('back_end.edit'),
                    ["class" => "btn btn-xs btn-primary"]
                )->toHtml();
                $action[] = \Form::lbButton(
                    "admin/user_manager/{$u->id}/change_password",
                    'get',
                    trans('back_end.change_password'),
                    ["class" => "btn btn-xs btn-warning"]
                )->toHtml();
                $action[] = \Form::lbButton(
                    "admin/user_manager/$u->id",
                    'delete',
                    trans('back_end.delete'),
                    [
                        "class" => "btn btn-xs btn-danger",
                        "onclick" => "return confirm('Are you sure?')"
                    ]
                )->toHtml();

                return implode(' ', $action);
            })
            ->make(true);
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
