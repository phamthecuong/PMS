<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Redirect;
use Mail, Config;

class TestController extends Controller {
    public function test() 
    {
        // $convert = convert::get();
        // dd($convert);
        return view('test');
    }

    public function chooser(Request $request) {
        return redirect() -> back();
    }

    public function pstest()
    {
        $segment = DB::table('tblSegment')
                            ->leftjoin('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id')
                            ->leftjoin('tblRoad', 'tblRoad.id', '=', 'tblBranch.route_id')
                            ->leftjoin('tblOrganizations as SB', 'SB.id', '=', 'tblSegment.SB_id')
                            ->leftjoin('tblOrganizations as RMB', 'RMB.id', '=', 'SB.parent_id')
                            ->leftjoin('mstDistrict as dfrom', 'dfrom.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstProvince as cfrom', 'cfrom.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstDistrict as dto', 'dto.id', '=', 'tblSegment.distfrom_id')
                            ->leftjoin('mstProvince as cto', 'cto.id', '=', 'tblSegment.distfrom_id')
                            ->select('tblSegment.id', 'tblSegment.segname_en as sname_en' , 'tblSegment.segname_vn as sname_vn' , 'tblRoad.name_en as rname_en', 'tblRoad.name_vn as rname_vn' , 
                                     'tblBranch.name_en as bname_en', 'tblBranch.name_vn as bname_vn', 'SB.name_en as oname_en',
                                     'SB.name_vn as oname_vn', 'RMB.name_en as ontext', 
                                     'tblSegment.km_from', 'tblSegment.m_from', 'tblSegment.km_to', 'tblSegment.m_to', 'cfrom.name_en as cfname_en',
                                     'cfrom.name_vn as cfname_vn', 'dfrom.name_en as dfname_en', 'dfrom.name_vn as dfname_vn', 'cto.name_en as ctname_en', 'cto.name_vn as ctname_vn',
                                     'dto.name_vn as dtname_vn', 'dto.name_en as dtname_en', 'tblSegment.commune_from as comf','tblSegment.commune_to as comt' );
        return Datatables::of($segment)->make(true);
    }

    public function ptest($lang) {
        if (!\Session::has('locale')) {
            \Session::put('locale', $lang);
        } else {
            \Session::set('locale', $lang);
        }
        return Redirect::back();
    }

}
