<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblDeterioration;

class crack_phi implements ShouldQueue
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
            chdir(__DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/crack/');
            shell_exec('./crack.sh 2');

            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [crack] update pav type flg to ' . ($deterioration->pav_type_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->pav_type_flg += 1;
            $deterioration->save();

            $crack_pavement_type_epsilon = (new \App\Jobs\crack_epsilon($this->session_id))->onQueue('deterioration_crack');
            dispatch($crack_pavement_type_epsilon);
        }
        catch (\Exception $e)
        {
            \Log::info('crack_phi');
            \Log::info($e->getMessage());
        }
    }
}
