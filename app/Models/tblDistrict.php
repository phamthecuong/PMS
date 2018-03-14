<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblDistrict extends Model
{
    protected $table = 'mstDistrict';

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

    public function wards()
    {
    	return $this->hasMany('App\Models\tblWard', 'district_id');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\tblCity', 'province_id');
    }

    static function allToOption($has_all = FALSE, $has_name =false)
    {
        $data = tblDistrict::get();
        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset+= array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r)
        {
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => $r->name_vn,
                    'value' => $r->id
                );
                $dataset[] = array(
                    'name' => $r->name_en,
                    'value' => $r->id
                );
            }
            else
            {
                $dataset[$r->id] = array(
                    'name' => \App::isLocale('en') ? $r->name_en : $r->name_vn,
                    'value' => $r->id,
                );
            }
        }
        return $dataset;
    }
    static function allOptionToAjax($check = '', $has_all = FALSE, $has_name =false)
    {
        if($check !== '')
        {
            $data = tblDistrict::where('province_id', $check)->get();
        }
        else
        {
            $data = tblDistrict::get();
        }
        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset+= array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r)
        {
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => $r->name_vn,
                    'value' => $r->id
                );
                $dataset[] = array(
                    'name' => $r->name_en,
                    'value' => $r->id
                );
            }
            else
            {
                $dataset[$r->id] = array(
                    'name' => \App::isLocale('en') ? $r->name_en : $r->name_vn,
                    'value' => $r->id,
                );
            }
        }
        return $dataset;
    }

    static function findDistrict($province_id)
    {
        return tblDistrict::where('province_id', $province_id)->pluck('id')->toArray();
    }
}
