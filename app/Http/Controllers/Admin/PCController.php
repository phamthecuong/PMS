<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BackendRequest\MigratePCRequest;
use App\Models\tblMigratePC;

class PCController extends Controller
{
    function __construct()
    {
        $this->middleware("dppermission:PC.migrate", ['only' => ['migratepc', 'migratepcCreate']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RepairMethodRequest $request)
    {
        try 
        {
            
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try
        {
            
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RepairMethodRequest $request, $id)
    {
        try
        {
            
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    function migratepc()
    {
        return view('admin.pc.migrate.index');
    }

    function migratepcCreate()
    {
        $pc_list = $this->_getFilesInFolder(public_path('pcimport/pcfile'));
        $image_list = $this->_getFilesInFolder(public_path('pcimport/imagefile'));
        return view('admin.pc.migrate.add', [
                'pc_list' => $pc_list,
                'image_list' => $image_list
            ]);
    }

    private function _getFilesInFolder($dir)
    {
        $manuals = [[
            'name' => trans('back_end.please_select'),
            'value' => ''
        ]];
        $filesInFolder = \File::files($dir);

        foreach($filesInFolder as $path)
        {
            $tmp = pathinfo($path);
            $manuals[] = [
                'name' => $tmp['filename'],
                'value' => $tmp['filename']
            ];
        }
        return $manuals;
    }

    public function migratepcStore(MigratePCRequest $request)
    {
        \DB::beginTransaction();
        try 
        {
            $model = new tblMigratePC;
            $model->pc_file = $request->pc_file;
            $model->image_file = $request->image_file;
            $model->created_by = \Auth::user()->id;
            $model->save();

            // import file
            $pc_source = 'pcimport/pcfile/' . $request->pc_file . '.csv';
            $image_source = 'pcimport/imagefile/' . $request->image_file . '.csv';
            
            $process_id = $model->id;

            $csv = public_path($pc_source);
            $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE pavement_condition_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\\r\\n' IGNORE 1 LINES (`section_id`, `geographical_area`, `rmb`, `sb`, `road_category`,`route_number`, `road_number_supplement`, `branch_number`, `route_name`, `kp_from`, `m_from`, `kp_to`, `m_to`, `section_length`, `analysis_area`, `structure`, `intersection`, `overlapping`, `number_of_lane_u`, `number_of_lane_d`, `direction`, `survey_lane`, `surface_type`, `survey_year`, `survey_month`, `cracking`, `patching`, `pothole`, `cracking_ratio`, `rutting_max`, `rutting_average`, `iri`, `mci`, `note`, @process_id ) SET process_id = '" . $process_id . "'", addslashes($csv));
            \DB::connection()->getpdo()->exec($query);

            $csv = public_path($image_source);
            $query = sprintf("LOAD DATA local INFILE '%s' INTO TABLE image_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\"' LINES TERMINATED BY '\\n' IGNORE 0 LINES (`section_id`, `image_id`, `road_category`, `route_number`, `road_number_supplement`, `branch_number`, `direction`, `survey_lane`, `latitude`, `longitude`, `height`, `image_path`, @process_id ) SET process_id = '" . $process_id . "'", addslashes($csv));

            \DB::connection()->getpdo()->exec($query);

            $total_km = \App\Models\PavementConditionTable::where('process_id', $process_id)->sum('section_length');
            $model->total_km = 0.001 * $total_km;
            $model->save();

            \DB::commit();

            $exitCode = \Artisan::queue('pms:migrate_pc', [
                'process_id' => $process_id
            ], 'migrate_pc');

            return redirect()->route('migrate_pc');
        }
        catch (\Exception $e)
        {
            \DB::rollback();
            dd($e);
        }
    }
}
