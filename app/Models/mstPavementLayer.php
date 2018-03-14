<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class mstPavementLayer extends Model
{
    protected $table = 'mstPavement_layer';

    protected $appends = ["layer_name"];

    public function pavementTypes()
    {
        return $this->hasMany('App\Models\mstPavementType', 'pavement_layer_id', 'parent_id');
    }

    function getLayerNameAttribute()
    {
        $lang = (\App::isLocale('en')) ? 'en' : 'vn';
        return $this->{"name_{$lang}"};
    }
    
    static function allToOption($has_all = FALSE)
    {
        $data = mstPavementLayer::all();

        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset += array(
                -1 => $has_all,
            );
        }
        foreach ($data as $r)
        {
            $dataset += array(
                $r->id => (Config::get('app.locale') == 'en') ? $r->name_en : $r->name_vn,
            );
        }

        return $dataset;
    }

    static function allOptionToAjax($has_all = FALSE, $demo = FALSE, $parent = FALSE)
    {
        $data;
        if ($demo !== FALSE)
        {
            $data = mstPavementLayer::where('id', '<=', 1)->get();
        }
        elseif ($parent !== FALSE)
        {
            $data = mstPavementLayer::where('parent_id', null)->get();
        }
        else
        {
            $data = mstPavementLayer::all();
        }


        $dataset = array();
        if ($has_all !== FALSE)
        {
            $dataset += array(
                -1 => $has_all,
            );
        }

        foreach ($data as $r)
        {
            $dataset[$r->id] = array(
                'name' => (Config::get('app.locale') == 'en') ? $r->name_en : $r->name_vn,
                'value' => $r->id,
                'selected' => FALSE,
            );
        }

        return $dataset;
    }
}
