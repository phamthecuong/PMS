<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblSectionPC extends Model
{
    protected $table = 'tblSection_PC';

    public function latest()
    {
    	return $this->hasMany('App\Models\tblSectionPCHistory', 'section_id')
    				->orderBy('date_y','desc')
                    ->groupBy('date_y')
    	            ->skip(0)->take(1);
    }

    public function secondLatest()
    {
    	return $this->hasMany('App\Models\tblSectionPCHistory', 'section_id')
    	            ->groupBy('date_y')
    	            ->orderBy('date_y','desc')
    	            ->skip(1)
                    ->take(1);
    }

    static function scopeSecondLatest($query, $year = null)
    {
        if ($year == 'latest')
        {
            return $query->with('latest')->whereHas('latest', function($query) use ($year) {
                $query->where('date_y', '<', $year);
            });
        }
        else if ($year == 'second_latest')
        {
            return $query->with('secondLatest')->whereHas('secondLatest', function($query) use ($year) {
                $query->where('date_y', '<', $year);
            });
        }
        else
        {
            return $query->with('history')->whereHas('history', function($query) use ($year){
                $query->where('date_y', $year);
            });
        }
    }

    // public function sectionPCDate()
    // {
    //     return $this->hasMany('App\Models\tblSectionPCHistory', 'date_y', 'date_y');
    // }
    public function history()
    {
        return $this->hasMany('App\Models\tblSectionPCHistory', 'section_id');
    }
}
