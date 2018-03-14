<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class mstSurface extends Model
{
    protected $table = 'mstSurface';

    // public function mstPavementLayer() {
    //     return $this->belongsTo('App\Models\mstPavementLayer', 'pavement_layer_id', 'id');
    // }

    // public function userCreate()
    // {
    //     return $this->belongsTo('App\Models\user', 'created_by' , 'id');
    // }

    // public function userUpdate()
    // {
    //     return $this->belongsTo('App\Models\user', 'updated_by' , 'id');
    // }

    public function repair_categories()
    {
        return $this->hasMany('App\Models\tblRCategory', 'surface_id');
    }

    static function allToOption($data = FALSE, $has_all = FALSE)
    {
        $data = mstSurface::get();
        $dataset = array();
        foreach ($data as $r)
        {
            if ($data !== FALSE) {
                $dataset[] = array(
                    'name' => trans('back_end.referred '),
                    'value' => 0
                );
            }
            if ($r->code_name == "*")
            {
                $dataset[] = array(
                    'name' => 'Other',
                    'value' => $r->id
                );
            }
            else
            {
                $dataset[] = array(
                    'name' => $r->code_name,
                    'value' => $r->id
                );
            }
        }
        return $dataset;
    }
    
    static public function getData($value_as_name = false)
    {
        $pavement = mstSurface::whereIn('code_id', [1, 2, 3])->get();
        foreach ($pavement as $p)
        {
            $select[] = [
                'name' => (\App::isLocale('en')) ? $p->code_name : trans('budget.'.$p->code_name.''),
                'value'=> $value_as_name ? $p->code_name : $p->code_id
            ];
        }
        return $select;
    }
}
