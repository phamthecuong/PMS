<?php

namespace App\Jobs;

use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class budget_simulation_output implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
    	$source = __DIR__ . '/../../public/application/process/budget_simulation/' . $this->session_id;
    	chdir($source);
        try
        {
			$result = shell_exec('crack.sh 1');
			$queue = (new \App\Jobs\crack_benchmark($this->session_id))->onQueue('budget_simulation_output');
            dispatch($queue);
            
        }
        catch (\Exception $e)
        {
            \Log::info('budget_simulation_output');
            \Log::info($e->getMessage());
        }
    }
}
