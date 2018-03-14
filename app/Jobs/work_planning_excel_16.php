<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblWorkPlanning;

class work_planning_excel_16 extends WorkPlanningExcel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use DispatchesJobs;

    protected $session_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session_id)
    {
         $this->session_id = $session_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            chdir(__DIR__ . '/../../public/application/process/work_planning/' . $this->session_id . '/');
            //shell_exec('./work_planning_excel.sh');

            $template = "Output_WP.xlsx";
            //$template = public_path('excel_templates/wp_template.xlsx');
            include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
            $xlsx = new \eiseXLSX($template);
            //$xml = $xlsx->arrXMLs['/xl/worksheets/sheet17.xml'];
            $xml = $this->_writeRMD($this->session_id, $xlsx);
            $xlsx->arrXMLs['/xl/worksheets/sheet17'] = $xml;
            /** ADD AUTOFILTER **/
            // $table = $xlsx->arrXMLs['/xl/tables/table6.xml'];
            // $table['ref'] = "B12:AQ". count($tmp);
            // $table->autoFilter['ref'] = "B12:AQ". count($tmp);
            /** END **/
            $xlsx->Output("Output_WP.xlsx", "F");
            $work_planning = tblWorkPlanning::findOrFail($this->session_id);
            $work_planning->excel_flg = 5;
            $work_planning->save();
            $work_planning_excel = (new \App\Jobs\work_planning_excel_6($this->session_id))->onQueue('work_planning_excel');
            dispatch($work_planning_excel);
        }
        catch (\Exception $e)
        {
            \Log::info('work_planning_excel_16');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }
}
