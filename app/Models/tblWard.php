<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblWard extends Model
{
    protected $table = 'mstWard';

    protected $appends = ["name"];

    public function getNameAttribute()
    {
        $key = "name_".\App::getLocale();
        if ($key == "name_vi")
        {
        	$key = "name_vn";
        }
        return $this->attributes[$key];
    }

    public function district()
    {
        return $this->belongsTo('App\Models\tblDistrict', 'district_id');
    }

    static public function allToOption($has_name =false)
    {
        $wards = tblWard::take(100)->get();
        $array = [];
        foreach ($wards as $row) 
        {
            if ($has_name)
            {
                $array[] = array(
                    'name' => $row->name_vn,
                    'value' => $row->id
                );
                $array[] = array(
                    'name' => $row->name_en,
                    'value' => $row->id
                );
            }
            else
            {
                $array[] = [
                    'name' => \App::isLocale('en') ? $row->name_en : $row->name_vn,
                    'value' => $row->id
                ];
            }
        }
        return $array;
    }
    static public function allOptionToAjax($check = null, $has_name =false)
    {
        if($check !== null)
        {
            $wards = tblWard::where('district_id', $check)->take(100)->get();
        }
        else
        {
            $wards = tblWard::take(100)->get();
        }

        $array = [];
        foreach ($wards as $row) 
        {
            if ($has_name)
            {
                $array[] = array(
                    'name' => $row->name_vn,
                    'value' => $row->id
                );
                $array[] = array(
                    'name' => $row->name_en,
                    'value' => $row->id
                );
            }
            else
            {
                $array[] = [
                    'name' => \App::isLocale('en') ? $row->name_en : $row->name_vn,
                    'value' => $row->id
                ];
            }
        }
        return $array;
    }

    static function findWard($district_id)
    {
        return tblWard::where('district_id', $district_id)->pluck('id')->toArray();
    }

}
