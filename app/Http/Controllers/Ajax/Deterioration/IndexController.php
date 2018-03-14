<?php
namespace App\Http\Controllers\Ajax\Deterioration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblDeterioration;
use Auth, Helper, Session, Config, Excel, DB;
use Yajra\Datatables\Facades\Datatables;

class IndexController extends Controller
{
	public function getCoreDataset()
	{	
		$deterioration = DB::table('tblDeterioration')
			->join('tblOrganization' , 'tblOrganization.id' , '=' , 'tblDeterioration.organization_id')
			->select('tblOrganization.name_vn' ,'tblOrganization.name_en' ,'tblDeterioration.year_of_dataset', 'tblDeterioration.updated_at')
			->where('dataset_flg', 1)
			->orderBy('year_of_dataset', 'desc')
			->get();
		
		return Datatables::of($deterioration) 
		    ->make(true);
	}
}

