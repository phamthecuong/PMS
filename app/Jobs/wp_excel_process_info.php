<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblWorkPlanning;

class wp_excel_process_info extends WorkPlanningExcel implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use DispatchesJobs;

    protected $session_id;
    protected $user_id;
    protected $list;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session_id, $user_id, $list)
    {
        $this->session_id = $session_id;
        $this->user_id = $user_id;
        $this->list = $list;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*** Processing Information ***/
        try
        {
            chdir(__DIR__ . '/../../public/application/process/work_planning/' . $this->session_id . '/');
            //shell_exec('./work_planning_excel.sh');
            $list = $this->list;
            $template = public_path('excel_templates/wp_template_en.xlsx');
            include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
            $xlsx = new \eiseXLSX($template);
            $this->_writeSheetProcessInfo($this->session_id, $xlsx, $this->user_id, 'en');
            $xlsx->Output("Output_WP_". $list . "_en.xlsx", "F");

            $template = public_path('excel_templates/wp_template_vn.xlsx');
            $xlsx = new \eiseXLSX($template);
            $this->_writeSheetProcessInfo($this->session_id, $xlsx, $this->user_id, 'vn');

            $xlsx->Output("Output_WP_". $list . "_vn.xlsx", "F");
            $work_planning = tblWorkPlanning::findOrFail($this->session_id);
            $work_planning->{"excel_flg_{$list}"} = 2;
            $work_planning->save();
            $work_planning_excel = (new \App\Jobs\wp_excel_repair_standard($this->session_id, $this->list))->onQueue('work_planning_excel');
            dispatch($work_planning_excel);

        }
        catch (\Exception $e)
        {
            \Log::info('work_planning_excel_2');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }
}
