<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\tblDeterioration;
use Illuminate\Foundation\Bus\DispatchesJobs;

class rut_section_31 implements ShouldQueue
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
            chdir(__DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/rut/');
            shell_exec('./rut.sh 3');
            
            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [rut] update section flg to ' . ($deterioration->section_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->section_flg += 1;
            $deterioration->save();

            $rut_32 = (new \App\Jobs\rut_32($this->session_id))->onQueue('deterioration_rut');
            dispatch($rut_32);
        }
        catch (\Exception $e)
        {
            \Log::info('rut_section_31');
            \Log::info($e->getMessage());
        }
    }
}
