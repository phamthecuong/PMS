<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;

class tblTerrainType extends Model
{
    protected $table = 'mstTerrain_type';
    
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

    static function allToOption($has_all = FALSE, $has_name = FALSE, $has_code = FALSE, $has_code_name = FALSE)
    {
        $data = tblTerrainType::where('code_name', 'F')->orWhere('code_name', 'M')->get();
        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset+= array(
                -1 => $has_all,
            );
        }
        foreach ($data as $t)
        {
            $dataset[$t->id] = array(
                'name' => (Config::get('app.locale') == 'en') ? $t->name_en : $t->name_vn,
                'value' => $t->id,
                'selected' => FALSE,
            );
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? $t->name_vn : $t->name_en,
                    'value' => $t->id);
            }
            if ($has_code)
            {
                $dataset[] = array(
                    'name' => $t->code_id,
                    'value' => $t->id);
            }
            if ($has_code_name)
            {
                $dataset[] = array(
                    'name' => $t->code_name,
                    'value' => $t->id);
            }
        }
        return $dataset;
    }

}
