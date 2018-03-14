<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class tblSectionSurvey extends Model
{
   
     protected $table = 'tblsection_survey' ;
	 
	
	static function get1()
	{
		// echo 344457;
		return 1;
	}

	  
    static  function get_survey_date()
	{
		// $survey_date = new tblSection_survey();
		// $survey_date->group_by('survey_year');
		// $survey_date->order_by('survey_year', 'desc');
		// $survey_date->get();
		$survey_date = DB::table('tblSection_survey')->groupBy('survey_year')->orderBy('survey_year','DESC')->get();
		
		$rows = array();
		foreach ($survey_date as $record)
		{
			$year = $record->survey_year;
			$rows[] = $year;
		}
		// return array_unique($rows);
		return $rows;
	}
}
