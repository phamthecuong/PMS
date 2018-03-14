<?php

namespace App\Jobs;

use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class rut_11 implements ShouldQueue
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
            Excel::load(__DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/rut/condition/index.csv', function($reader) {
                $reader->sheet(0, function($sheet) {
                    $sheet->cell('A1', function($cell) {
                        $cell->setValue(1);
                    });
                    $sheet->cell('A2', function($cell) {
                        $cell->setValue(1);
                    });
                });
            })->store('csv', __DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/rut/condition/');
            $rut_benchmark = (new \App\Jobs\rut_benchmark($this->session_id))->onQueue('deterioration_rut');
            dispatch($rut_benchmark);    
        }
        catch (\Exception $e)
        {
            \Log::info('rut_11');
            \Log::info($e->getMessage());
        }
    }
}