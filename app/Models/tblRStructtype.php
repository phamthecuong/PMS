<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class tblRStructtype extends Model
{
    protected $table = 'tblR_structtype';
    protected $fillable = ["name_en", "name_vi"];
    protected $appends = ["name"];

    public function getNameAttribute()
    {
        $lang = App::getLocale();
        $lang == 'en' ? $key = "name_".$lang : $key = "name_vn";
        return $this->attributes[$key];
    }

    static function allToOption($has_all = FALSE)
	{
		$data = tblRStructtype::get();
		$dataset = array();
		foreach ($data as $r)
        {
            $dataset[] = array(
                'name' => (\App::isLocale('en')) ? $r->name_en : $r->name_vn,
                'value' => $r->id
            );
        }
		return $dataset;
	}
}
