<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class tblRClassification extends Model
{
    protected $table = 'tblR_classification';
    protected $fillable = ["name_en", "name_vi"];
    protected $appends = ["name"];

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        $lang == 'en' ? $key = "name_".$lang : $key = "name_vn";
        return $this->attributes[$key];
    }

    static function allToOption($has_name = FALSE, $has_code = FALSE)
	{
		$data = tblRClassification::get();
		$dataset = array();
		foreach ($data as $r)
        {
            $dataset[] = array(
                'name' => (\App::isLocale('en')) ? $r->name_en : $r->name_vn,
                'value' => $r->id
            );
            if ($has_name)
            {
                $dataset[] = array(
                    'name' => (\App::isLocale('en')) ? $r->name_vn : $r->name_en,
                    'value' => $r->id);
            }
            if ($has_code)
            {
                $dataset[] =  array(
                    'name' => $r->code,
                    'value' => $r->id);
            }
        }
		return $dataset;
	}

}
