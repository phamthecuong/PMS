<?php

namespace App\Jobs;

use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class cri_flg1 implements ShouldQueue
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
            Excel::load(__DIR__ . '/../../public/application/process/budget_simulation/' . $this->session_id . '/input/conditions/para2.csv', function($reader) {
                $reader->sheet(0, function($sheet)  {
                    $sheet->cell('A1', function($cell) {
                        $cell->setValue(1);
                    });
                });
            })->store('csv', __DIR__ . '/../../public/application/process/budget_simulation/' . $this->session_id . '/input/conditions/');
            $budget_simulation_output = (new \App\Jobs\budget_simulation_output1($this->session_id))->onQueue('budget_simulation_output');
            dispatch($budget_simulation_output);
        }
        catch (\Exception $e)
        {
            \Log::info('cri_flg1');
            \Log::info($e->getMessage());
        }
    }
}
