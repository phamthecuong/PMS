<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblWorkPlanning;

class wp_excel_rmd extends WorkPlanningExcel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use DispatchesJobs;

    protected $session_id;
    protected $list;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session_id, $list)
    {
        $this->session_id = $session_id;
        $this->list = $list;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*** Road Main Details ***/
        try
        {
            chdir(__DIR__ . '/../../public/application/process/work_planning/' . $this->session_id . '/');
            //shell_exec('./work_planning_excel.sh');
            $list = $this->list;
            $template = "Output_WP_". $list . "_en.xlsx";
            //$template = public_path('excel_templates/wp_template.xlsx');
            include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
            $xlsx = new \eiseXLSX($template);
            $xml = $this->_writeRMD($this->session_id, $xlsx, 'en');
            $xlsx->arrXMLs['/xl/worksheets/sheet16.xml'] = $xml;
            /** ADD AUTOFILTER **/
            // $table = $xlsx->arrXMLs['/xl/tables/table6.xml'];
            // $table['ref'] = "B12:AQ". count($tmp);
            // $table->autoFilter['ref'] = "B12:AQ". count($tmp);
            /** END **/
            $xlsx->Output("Output_WP_". $list . "_en.xlsx", "F");

            $template = "Output_WP_". $list . "_vn.xlsx";
            $xlsx = new \eiseXLSX($template);

            $xml = $this->_writeRMD($this->session_id, $xlsx, 'vn');
            $xlsx->arrXMLs['/xl/worksheets/sheet16.xml'] = $xml;

            

            $xlsx->Output("Output_WP_". $list . "_vn.xlsx", "F");
            $work_planning = tblWorkPlanning::findOrFail($this->session_id);
            $work_planning->{"excel_flg_{$list}"} = 5;
            $work_planning->save();
            $work_planning_excel = (new \App\Jobs\wp_excel_invalid_sections($this->session_id, $list))->onQueue('work_planning_excel');
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
