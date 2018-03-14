<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mstIrregularKp extends Model
{
    protected $table = 'mstIrregular_kps';

    protected $fillable = ['branch_id', 'direction', 'kp', 'section_length', 'note'];

    public function branch()
    {
    	return $this->belongsTo('App\Models\tblBranch', 'branch_id');
    }

    static public function getListNote()
    {
    	return [
    		[
                'name' => '',
                'value' => ''
            ],
            [
                'name' => trans('back_end.virtual_kp'),
                'value' => 'Virtual KP'
            ],
            [
                'name' => trans('back_end.skipped_kp'),
                'value' => 'Skipped KP'
            ]
        ];
    }
}
