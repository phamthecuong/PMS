<?php

namespace App\Models;
use DB, Config;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\App;

class tblOrganization extends Model
{
    //
    protected $table = 'tblOrganization' ;

    protected $appends = ["organization_name"];


    // public function getNameAttribute()
    // {
    //     $key = "name_".\App::getLocale();
    //     if ($key == "name_vi")
    //     {
    //         $key = "name_vn";
    //     }
    //     return $this->attributes[$key];
    // }
	public function rmb()
	{
		return $this->belongsTo('App\Models\tblOrganization', 'parent_id');
	}

    public function segments()
    {
        return $this->hasMany('App\Models\tblSegment', 'SB_id');
    }

	public function repairCosts()
	{
		return $this->hasMany('App\Models\tblRepairMethodCost', 'organization_id', 'id');
	}

	public function getOrganizationNameAttribute()
	{
		$lang = (\App::isLocale('en')) ? 'en' : 'vn';
		return $this->{"name_{$lang}"};
	}

	
 //    static function init($init = array(), $code_to_throw = FALSE)
	// {
	// 	$model = new tblOrganization;
		
	// 	if (count($init) > 0)
	// 	{
	// 		foreach ($init as $key => $value)
	// 		{
	// 			$model->where($key, $value);
	// 			// echo "<br>";
	// 			// echo $key.$value;
	// 		}
	// 		$model->get();
	// 		if (!$model->exists() && ($code_to_throw !== FALSE))
	// 		{
	// 			throw new Exception("", $code_to_throw);
	// 			// echo "ngocduc";
	// 		}
	// 	}
	// 	return $model;
	// }
	
	static function allToOption($has_all = FALSE)
	{
		$data = tblOrganization::get();
		$dataset = array();
		if ($has_all !== FALSE)
		{
			$dataset+= array(
				-1 => $has_all,
			);
		}
        foreach ($data as $r)
        {
            $dataset[$r->id] = array(
                'name' => \App::isLocale('en') ? $r->name_en : $r->name_vn,
                'value' => $r->id,
                'selected' => FALSE,
            );
        }
		return $dataset;
	}

    static function getOptionByRole($has_all = FALSE)
    {
        $organization = [];
        $organization_id = User::select('organization_id')->whereHas('roles', function ($query){
            $query->where('code', 'like', '%'.'adminl'.'%');
        })->get();
        foreach ($organization_id as $oi)
        {
            $organization[] = $oi->organization_id;
        }
        $data = tblOrganization::wherein('id', $organization)->get();
        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset+= array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r)
        {
            $dataset[$r->id] = array(
                'name' => \App::isLocale('en') ? $r->name_en : $r->name_vn,
                'value' => $r->id,
                'selected' => FALSE,
            );
        }
        return $dataset;
    }
    public function scopeToOption($query, $name = "name", $value = "id", $additional_array = array())
    {
        $items = $query->get();
        $array = $additional_array;
        foreach ($items as $item)
        {
            $comps = explode(".", $name);
            $object = $item;
            foreach ($comps as $c)
            {
                $object = $object->$c;
            }
            $array[] = [
                "name" => $object,
                "value" => $item->$value
            ];
        }
        return $array;
    }
	static function getListRmb($has_all = FALSE, $has_name = FALSE)
	{  
        $rmb = array();
        if (Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('userlvl1p'))
        {
            $data = tblOrganization::where('level', 2)->get();
            
            if ($has_all !== FALSE)
            {
                $rmb[] = array(
                    'name' => ($has_all == 0) ? trans('general.select_a_rmb') : trans('general.all_rmb'),
                    'value' => -1,
                );
            } 
        }
        else if (Auth::user()->hasRole('userlv2'))
        {
           $data = tblOrganization::where('id', Auth::user()->organization_id)->get();
        }
        else 
        {   
            $parent_id = tblOrganization::find(Auth::user()->organization_id)->parent_id;
            $data = tblOrganization::where('id', $parent_id)->get();
        }
		
		foreach ($data as $p)
		{

            if ($has_name)
            {
                $rmb[]= array(
                    'name' => App::getLocale() == 'nen' ? $p->name_en : $p->name_vn,
                    'value' => $p->id,
                );
                $rmb[] = array(
                    'name' => App::getLocale() == 'nen' ? $p->name_en : $p->name_vn,
                    'value' => $p->id,);
            }
            else
            {
                $rmb[$p->id]= array(
                    'name' => (\App::isLocale('en')) ? $p->name_en : $p->name_vn,
                    'value' => $p->id,
                );
            }
		}
		return $rmb;
	}

    static function getListSB($has_all = FALSE, $has_name = FALSE)
    {   
        $rmb = array();
        if (Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlv2')|| Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('userlvl1p'))
        {
            if ($has_all !== FALSE)
            {
               
                $rmb[] = array(
                    'name' => ($has_all == 0) ? trans('general.select_a_sb') : trans('general.all_sb'),
                    'value' => -1,
                );
            }
        }
        if (Auth::user()->hasRole('userlv1')|| Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('userlvl1p'))
        {
            $data = tblOrganization::where('level', 3)->whereNotNull('parent_id')->get();
        }
        else if(Auth::user()->hasRole('userlv2'))
        {
            $data = tblOrganization::where('parent_id', Auth::user()->organization_id)->get();
        }
        else
        {
            $data = tblOrganization::where('id', Auth::user()->organization_id)->get();
        }
        foreach ($data as $p)
        {
            if ($has_name)
            {
                $rmb[]= array(
                    'name' => $p->name_en,
                    'value' => $p->id,
                );
                $rmb[] = array(
                    'name' => $p->name_vn,
                    'value' => $p->id
                );
            }
            else
            {
                $rmb [$p->id]= array(
                    'name' => (\App::isLocale('en')) ? $p->name_en : $p->name_vn,
                    'value' => $p->id,
                );
            }
        }
        return $rmb;
    }

    // static function getRMBLevel()
    // {   
    //     $dataset = array();
    //     if (\Auth::user()->hasRole("userlv1"))
    //     {   
    //         $data = tblOrganization::where('level', 2)->get();
    //         $dataset[-1] = ['name' => trans('map.all_rmb'), 'value' => '-1'];
    //     }
    //     else if(\Auth::user()->hasRole("userlv2"))
    //     {
    //         $data =  tblOrganization::where('id', \Auth::user()->organization_id)->get();
    //     }
       
    //     if (isset($data))
    //     {
    //         foreach ($data as $p)
    //         {
    //             $dataset[$p->id] = array(
    //                 'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn,
    //                 'value' => $p->id,
    //                 'selected' => FALSE,
    //             );
    //         }
           
    //     }
    //     return $dataset;
    // }

    // Static function getSBLevel($has_all = FALSE)
    // {
    //     $dataset = array();
    //     if (\Auth::user()->hasRole("userlv1"))
    //     {   
    //         $data = tblOrganization::where('parent_id', '!=', '')->get();
    //     }
    //     else if(\Auth::user()->hasRole("userlv2"))
    //     {
    //         $data = tblOrganization::where('parent_id', \Auth::user()->organization_id)->get();
    //     }
    //     else
    //     {
    //         $data = tblOrganization::where('id', \Auth::user()->organization_id)->get();
    //     }
       
    //     if ($has_all !== FALSE)
    //     {
    //         $dataset+= array(
    //             -1 => $has_all,
    //         );
    //     }
    //     foreach ($data as $p)
    //     {
    //         $dataset[$p->id] = array(
    //             'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn,
    //             'value' => $p->id,
    //             'selected' => FALSE,
    //         );
    //     }
       
    //     return $dataset;
    // }


    static function findSb($rmb_id)
    {
        return tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
    }

    static function listRMBByUserRole() 
    {
        if (\Auth::user()->hasRole("userlv2"))
        {
            return tblOrganization::where('id', \Auth::user()->organization_id)->get();
        }
        else
        {
            return tblOrganization::where('level', 2)->get();
        }
    }

}
