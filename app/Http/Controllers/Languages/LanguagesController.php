<?php

namespace App\Http\Controllers\Languages;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Redirect;

class LanguagesController extends Controller
{
    public function lang($lang)
    {
        if (!\Session::has('locale'))
        {
            Session::put('locale', $lang);
        }
        else
        {
            Session::set('locale', $lang);
        }
        // dd(Session::get('locale'));
        return Redirect::back();
    }
}
