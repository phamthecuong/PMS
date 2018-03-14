<?php

namespace App\Http\Controllers\Auth\FrontEnd;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator, Session, DB, Input;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\tblBudgetSimulation;

class LoginController extends Controller
{
    
    public function login()
    {
  //   	if (Auth::check())
  //   	{
		// 	// return redirect()->route('user.budget.init');
		// 	return redirect('home');
		// }
        return view('front-end.auth.login');
    }

      /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
  
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/user/home';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
  
    public function do_login(Request $request)
    {
        // login 
        // $user = User::where('name', $request->username)->where('password', md5($request->password))->first();
        // $user = Auth::attempt(array(
                // 'name' => Input::get('username'),
                // 'password' => Input::get('password'),
            // ), Input::get('remember'));
        if (Auth::attempt(['name' => $request->username, 'password' => $request->password]))
		{
            // if (Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlvl2') || Auth::user()->hasRole('userlvl3') || Auth::user()->hasRole('userlvl1p'))
            // {
            return redirect()->route('user.home');
            // }
            // else
            // {
                // Session::flash('class', 'alert alert-danger');
                // Session::flash('message', trans('back_end.not_have_permission'));   
                // Auth::logout();
                // return redirect()->route('user.login')->withInput();
            // }
		}
		else
		{
			Session::flash('class', 'alert alert-danger');
	        Session::flash('message', trans('back_end.invalid_username_password'));   
	        return redirect()->route('user.login')->withInput();
		}
    }

    public function logout()
    {
        Auth::logout();
        return view('front-end.auth.login');
    }

    public function register()
    {
        return view();
    }
    
    public function do_register(Request $request)
    {
        // dd($data);
        return tbluser::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
           // 'token' => makeRandomToken(),
        ]);
    }
   
	
}
