<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mstRoadNumberSupplement extends Model
{
    protected $table = 'mstRoad_number_supplement';

/*    static public function getData($has_all = false)
    {
        $road_number_supplement = mstRoadNumberSupplement::get();


        $data = array();
        if ($has_all !== false)
        {
            $data[] = array(
                'name' => '',
                'value' => null,
            );
        }
        foreach ($road_number_supplement as $rms)
        {
            $data[] = array(
                'name' => $rms->code_name,
                'value' => $rms->code_id
            );
        }
        return $data;
    }*/
    static function allToOption($has_all = FALSE)
    {
        $data = mstRoadNumberSupplement::all();

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
                $r->id => $r->classification,
            );
        }

        return $dataset;
    }

    static function allOptionToAjax($has_all = FALSE, $demo = FALSE)
    {
        $data;
        if ($demo !== FALSE)
        {
            $data = mstRoadNumberSupplement::where('code_id', '<=', 1)->get();
        }
        else
        {
            $data = mstRoadNumberSupplement::all();
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
            $dataset[$r->code_id] = array(
                'name' => $r->code_name,
                'value' => $r->code_id,
                'selected' => FALSE,
            );
        }

        return $dataset;
    }

}
