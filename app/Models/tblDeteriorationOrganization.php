<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\UuidForKey;
use App, DB, Config, Helper;
class tblDeteriorationOrganization extends Model
{
    protected $table = 'tblDeterioration_organization';

    public function tblOrganization()
    {
    	return $this->hasOne('App\Models\tblOrganization', 'id');
    }
	
	static function getNameOrganization($session_id)
	{
		$text = '';
		$organizations = tblDeteriorationOrganization::where('deterioration_id', $session_id)->get();
		foreach ($organizations as $dnd)
		{
			$organization_name = tblOrganization::where('id',$dnd->organization_id)->firstOrFail();
			$text .= (Config::get('app.locale') == 'en') ? ', '.$organization_name->name_en : ', '.$organization_name->name_vn;	
		}
		$text = ltrim($text,' ,');
		
		return $text;
	}
	
}
