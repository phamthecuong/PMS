<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblWorkPlanning;
class wp_excel_clist4 extends WorkPlanningExcel implements ShouldQueue
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
        /*** CList4 **/
        try
        {
            chdir(__DIR__ . '/../../public/application/process/work_planning/' . $this->session_id . '/');
            //shell_exec('./work_planning_excel.sh');
            $list = $this->list;
            $template = "Output_WP_". $list ."_en.xlsx";
            include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
            $xlsx = new \eiseXLSX($template);
            $xml = $xlsx->arrXMLs['/xl/worksheets/sheet12.xml'];
            $xml = $this->_writeSheet($this->session_id, 7, $xml, $list, 'en');
            $xlsx->arrXMLs['/xl/worksheets/sheet12.xml'] = $xml;
            $xlsx->Output("Output_WP_". $list ."_en.xlsx", "F");

            $template = "Output_WP_". $list ."_vn.xlsx";
            $xlsx = new \eiseXLSX($template);
            $xml = $xlsx->arrXMLs['/xl/worksheets/sheet12.xml'];
            $xml = $this->_writeSheet($this->session_id, 7, $xml, $list, 'vn');
            $xlsx->arrXMLs['/xl/worksheets/sheet12.xml'] = $xml;
            $xlsx->Output("Output_WP_". $list ."_vn.xlsx", "F");

            $work_planning = tblWorkPlanning::findOrFail($this->session_id);
            $work_planning->{"excel_flg_{$list}"} = 11;
            $work_planning->save();
            $work_planning_excel = (new \App\Jobs\wp_excel_clist5($this->session_id, $list))->onQueue('work_planning_excel');
            dispatch($work_planning_excel);
        }
        catch (\Exception $e)
        {
            \Log::info('work_planning_excel_12');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }
}
