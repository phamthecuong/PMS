<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblCity extends Model
{
    protected $table = 'mstProvince';

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

    public function districts()
    {
        return $this->hasMany('App\Models\tblDistrict', 'province_id');
    }

    static function allToOption($has_all = FALSE, $has_name = FALSE)
    {
        $data = tblCity::get();
        $dataset = [['name'=> trans('back_end.please_choose'), 'value' => '']];
        if ($has_all !== FALSE)
        {
            $dataset+= array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r)
        {
            $dataset[$r->id] = array(
                'name' => $r->name,
                'value' => $r->id,
                'selected' => FALSE,
            );
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? $r->name_vn : $r->name_en,
                    'value' => $r->id
                );
            }
        }
        return $dataset;
    }

    static function allToOptionAjax($has_all = FALSE, $has_name = FALSE)
    {
        $data = tblCity::get();
        $dataset = [];
        if ($has_all !== FALSE)
        {
            $dataset+= array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r)
        {
            $dataset[$r->id] = array(
                'name' => $r->name,
                'value' => $r->id,
                'selected' => FALSE,
            );
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? $r->name_vn : $r->name_en,
                    'value' => $r->id);
            }
        }
        return $dataset;
    }
}
