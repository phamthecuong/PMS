<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblDeterioration;

class iri_route_21 implements ShouldQueue
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
            chdir(__DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/IRI/');
            $re = shell_exec('./iri.sh 3');
            
            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [iri] update route flg to ' . ($deterioration->route_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->route_flg += 1;
            $deterioration->save();

            $iri_22 = (new \App\Jobs\iri_22($this->session_id))->onQueue('deterioration_iri');
            dispatch($iri_22);
        }
        catch (\Exception $e)
        {
            \Log::info('iri_route_21');
            \Log::info($e->getMessage());
        }
    }
}
