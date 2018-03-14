<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, Session;
use Illuminate\Routing\Redirector;

class DeteriorationController extends Controller
{
	function __construct(Redirector $redirect)
	{
		if (!Auth::user())
		{
			Auth::logout();
		}
		else if (Auth::user()->hasPermission('deterioration.deterioration') == false)
        {
        	Session::flash('class', 'alert alert-danger');
	        Session::flash('message', trans('back_end.not_have_permission'));   
	        $redirect->to('/user/home')->send();
		}
	}
}