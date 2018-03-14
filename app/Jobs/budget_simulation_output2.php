<?php

namespace App\Jobs;

use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblBudgetSimulation;

class budget_simulation_output2 extends BudgetExcel implements ShouldQueue
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
            chdir(__DIR__ . '/../../public/application/process/budget_simulation/' . $this->session_id . '/');
            $re = shell_exec('./budget.sh');
    
            $rec = tblBudgetSimulation::findOrFail($this->session_id);
            $log = [$rec->log_text, date('Y-m-d H:i:s') . ': budget_simulation_output2.'];
            $rec->log_text = implode(PHP_EOL , $log);
            $rec->output_2_flg = 1;
            $rec->save();

            $this->_writeExcel($rec);

            $log = [$rec->log_text, date('Y-m-d H:i:s') . ': budget_simulation_output0.'];
            $rec->log_text = implode(PHP_EOL , $log);
            $rec->output_2_flg = 2;
            $rec->status = null;
            $rec->save();
        }
        catch (\Exception $e)
        {
            \Log::info('budget_simulation_output2');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }

    function _writeExcel($budget_simulation)
    {
        $path = public_path('application/process/budget_simulation/' . $budget_simulation->id . '/');
        $template = public_path('excel_templates/Output_02_budget.xlsx');
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");

        $scenario = 2;
        $crack_rank = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
        $rut_rank = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get();

        $xlsx = new \eiseXLSX($template);

        $this->_writeTableSheet($xlsx, $path, $scenario, $budget_simulation);
        $this->_writeCrackSheet($xlsx, $path, $scenario, $crack_rank);
        $this->_writeRutSheet($xlsx, $path, $scenario, $rut_rank);
        $this->_drawChart($xlsx, $crack_rank, $rut_rank, $budget_simulation);

        $xlsx->Output("Output_02_budget_cri_flg={$scenario}.xlsx", "F");
    }
}
