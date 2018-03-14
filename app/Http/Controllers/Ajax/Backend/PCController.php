<?php

namespace App\Http\Controllers\Ajax\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\tblMigratePC;

class PCController extends Controller
{
    public function getProcess()
    {
        $data = tblMigratePC::all();
        
        return Datatables::of($data)
            ->addColumn('data_file', function($d) {
                return "<strong>PC:</strong> " . $d->pc_file . "<br /><strong>Image:</strong> " . $d->image_file;
            })
            ->addColumn('creator_name', function($d) {
                return $d->creator->name;
            })
            ->addColumn('progress', function($d) {
                $total = $d->total_km;
                $remaining = 0.001 * \App\Models\PavementConditionTable::where('process_id', $d->id)->sum('section_length');

                if ($total == 0)
                {
                    $percentage = 0;
                }
                else
                {
                    $percentage = round(100 * ($total - $remaining) / $total);
                }

                // awkward script
                if ($percentage == 0) $percentage = 0;
                
                if ($percentage != 100)
                {
                    $info = '<span class="pull-right semi-bold text-muted">' . $percentage . '%</span>';
                }
                else
                {
                    $info = '<span class="pull-right semi-bold text-muted"><i class="fa fa-check text-success"></i> ' . trans('back_end.complete') . '</span>';
                }  

                return    
                    '<span>
                        <div class="bar-holder no-padding">
                            <p class="margin-bottom-5"><strong>' . trans('back_end.total_km') . ':</strong> <i>' . $total . '</i>' . $info . '</p>
                            <div class="progress progress-micro">
                                <div class="progress-bar progress-bar-success" style="width: ' . $percentage . '%;"></div>
                            </div>
                            <em class="note no-margin"><strong>' . trans('back_end.remaining_km') . ':</strong> ' . $remaining . '</em>
                        </div>
                    </span>';
            })
            ->make(true);
    }
}