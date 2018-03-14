<?php

namespace App\Jobs;

use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class crack_22 implements ShouldQueue
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
            Excel::load(__DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/crack/condition/index.csv', function($reader) {
                $reader->sheet(0, function($sheet)  {
                    $sheet->cell('A1', function($cell) {
                        $cell->setValue(2);
                    });
                    $sheet->cell('A2', function($cell) {
                        $cell->setValue(2);
                    });
                });
            })->store('csv', __DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/crack/condition/');
            $crack_route_22 = (new \App\Jobs\crack_route_22($this->session_id))->onQueue('deterioration_crack');
            dispatch($crack_route_22);
        }
        catch (\Exception $e)
        {
            \Log::info('crack_22');
            \Log::info($e->getMessage());
        }
    }
}
