<?php

namespace App\Jobs;

use App;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblPMSDataset;
use App\Models\tblPMSSectioning;
use App\Models\tblPMSSectioningInfo;
use DB;

class merge_single_lane implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use DispatchesJobs;

    protected $pms_dataset_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pms_dataset_id)
    {
        $this->pms_dataset_id = $pms_dataset_id;
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
            $pms_dataset = tblPMSDataset::findOrFail($this->pms_dataset_id);            
            $this->_mergeSingleLane();

            $total_segment = 0;
            \DB::transaction(function() use($pms_dataset, &$total_segment) {
                tblPMSSectioning::chunk(500, function($r) use($pms_dataset, &$total_segment) {
                    $job = (new \App\Jobs\formulate_dataset($r->pluck('id')->toArray(), $pms_dataset->id))->onQueue('formulate_dataset');
                    dispatch($job);
                    $total_segment++;
                });
            }, 5);

            $pms_dataset->total_segment = $total_segment;
            $pms_dataset->save();
        }
        catch (\Exception $e)
        {
            \Log::info('merge_single_lane');
            \Log::info($e->getMessage());
            echo $e->getMessage(), ':line:', $e->getLine();
        }
    }

    private function _mergeSingleLane()
    {
        // get single section
        \DB::table('tblPMS_sectioning')
            ->select(DB::raw('*, max(direction) as max_direction, count(direction) as total_direction'))
            ->groupBy('branch_id')
            ->groupBy('km_from')
            ->groupBy('m_from')
            ->groupBy('km_to')
            ->groupBy('m_to')
            ->having('max_direction', '=', 3)
            ->having('total_direction', '>', 1)
            ->chunk(100, function($records) {
            foreach ($records as $r) 
            {
                \DB::transaction(function() use($r) {
                    $matching = tblPMSSectioning::where('direction', 1)
                        ->where('lane_pos_no', 1)
                        ->where('km_from', $r->km_from)
                        ->where('m_from', $r->m_from)
                        ->where('km_to', $r->km_to)
                        ->where('m_to', $r->m_to)
                        ->where('branch_id', $r->branch_id)
                        ->first();

                    if ($matching)
                    {
                        tblPMSSectioningInfo::where('type_id', 3)
                            ->where('PMS_section_id', $matching->id)
                            ->update(['PMS_section_id' => $r->id]);
                    }
                    else
                    {
                        $matching = tblPMSSectioning::where('direction', 2)
                            ->where('lane_pos_no', 1)
                            ->where('km_from', $r->km_from)
                            ->where('m_from', $r->m_from)
                            ->where('km_to', $r->km_to)
                            ->where('m_to', $r->m_to)
                            ->where('branch_id', $r->branch_id)
                            ->first();
                        if ($matching)
                        {
                            tblPMSSectioningInfo::where('type_id', 3)
                                ->where('PMS_section_id', $matching->id)
                                ->update(['PMS_section_id' => $r->id]);
                        }
                    }
                    tblPMSSectioning::where('direction', '<>', 3)
                        ->where('km_from', $r->km_from)
                        ->where('m_from', $r->m_from)
                        ->where('km_to', $r->km_to)
                        ->where('m_to', $r->m_to)
                        ->where('branch_id', $r->branch_id)
                        ->delete();
                }, 5);
            }
        });
    }
}
