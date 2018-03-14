<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblDeterioration;

class rut_benchmark implements ShouldQueue
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
            shell_exec('./rut.sh 1');

            include_once __DIR__ . '/../../lib/eiseXLSX/eiseXLSX.php';
            $link = 'rut';
            $id = $this->session_id;
            $deterioration = tblDeterioration::find($id);
            $condition_rank = $deterioration->condition_rank;
            // $condition_rank_array = json_decode($condition_rank);
            //
            $option = Helper::convertJsonConditionRank($condition_rank);
            $case = count($option[strtolower($link)]);
            // load template case
            
            $template = '../../../public/excel_templates/TemplateDeterioration/Deterioration' . $case . '/DeteriorationBenchMarkingCase'.$case.'.xlsx';
            
            $xlsx = new \eiseXLSX($template);
            
            // active sheet 1
            $sheet1 = $xlsx->selectSheet(1);
            $sheet1->data('C3', '01_deterioration / ' . ucwords($link));
            $sheet1->data('F5', ucwords($link) . '(mm)');
            // load data hazard - tvalue
            $load = Helper::loadExcel($id, $link, 'output1', 'output.csv', true);
            $j = 6;
            for ($i = 6; $i < 6 + $case - 1; $i++)
            {   
                $key = $i - $j;
                if (isset($load[0][$key]))
                {
                    $dataHazart = $load[0][$key];    
                    $sheet1->data('C'.$i, $dataHazart, 'n'); 
                }
                else
                {
                    $sheet1->data('C'.$i, '-');    
                }
                if (isset($load[1][$key]))
                {
                    $dataValue = $load[1][$key];
                    $sheet1->data('D'.$i, $dataValue, 'n'); 
                }
                else
                {
                    $sheet1->data('D'.$i, '-');    
                }
            }

            // load data rank-crack
            foreach ($option[strtolower($link)]  as $key => $row)
            {
                $rankCrack[] = array("$key" , $row['from'] );
                $start[] =  $row['from'];
            }
            $j = 6;
            for ($i= 6; $i < 6+$case; $i++)
            {   
                $key = $i-$j;
                $crack = $rankCrack[$key][1];
                $sheet1->data('F'.$i, $crack,'n'); 
            }
            // load data Expected life length(year)
            $Expected =  Helper::total($load,$case);
            $maximum_x_axis = 0;
            for ($i = 6; $i < 6 + $case; $i++)
            {   
                $key = $i-$j;
                $sheet1->data('G'.$i, $Expected[$key], 'n'); 
                if ($maximum_x_axis < $Expected[$key])
                {
                    $maximum_x_axis = $Expected[$key];
                }
            }
            // active shhet 2
            $sheet2 = $xlsx->selectSheet(2);
            $sheet2->data('B3', '01_deterioration / ' . ucwords($link));
            //$sheet2->data('D3',$link);
            // load data : Markov Transition Probabilities
            $matrixFile = file(__DIR__ . '/../../public/application/process/deterioration/' . $id . '/' . $link . '/output1/matrix.csv');
           
            foreach ($matrixFile as $row)
            {
                $matrix[] = explode(',', $row);
            }
            
            $i = 7;
            for ($j = 0; $j < count($matrix); $j++)
            {   
                for ($col = ord('d'); $col < ord('d')+ $case; $col++)
                {
                    $key = $col - ord('d');
                    $sheet2->data(chr($col).$i, $matrix[$j][$key], 'n');
                }
                $i++;
            }
            //load data: Deterioration Transition Probabilities
            //+ load year
            foreach ($option[strtolower($link)] as $row)
            {
                $to =  $row['to'];
                $from =  $row['from'];
                $array = Helper::convertConditionInforToText($from, $to, 'R');
                $year[] = $array;
            }

            for ($col = ord('d'); $col <= ord('d')+$case-1; $col++)
            {
                $key = $col - ord('d');
                $sheet2->data(chr($col) . (7 + $case + 2), $year[$key]);
            }
          
            // end load year
            $transitionFile = file(__DIR__ . '/../../public/application/process/deterioration/' . $id . '/' . $link.'/output1/transition.csv');
            foreach ($transitionFile as $row)
            {
                $transition[] = explode(',', $row);
            }

            $i = 7 + $case + 3;
            for ($j = 0; $j < count($transition); $j++)
            {   
                for ($col = ord('c'); $col <= ord('c')+$case; $col++)
                {
                    $key = $col - ord('c');
                    $sheet2->data(chr($col).$i, $transition[$j][$key], 'n');
                }
                $i++;
            }
            libxml_use_internal_errors(true);

            $xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/chart1.xml']);
            
            $xml = $this->setMaxXAxis($xml, $maximum_x_axis);
            $xml = $this->setMinXAxis($xml, 0);
            $xml = $this->setMaxYAxis($xml, end($start));

            $xlsx->arrXMLs['/xl/charts/chart1.xml'] = $xml->asXML();

            $xlsx->Output("bench-mark.xlsx", "F");
          
            $xlsx->Output("bench-mark.xlsx", "F");
    
            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [rut] update benchmark flg to ' . ($deterioration->benchmark_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->benchmark_flg += 1;
            $deterioration->save();
            
            $rut_pavement_type_phi = (new \App\Jobs\rut_phi($this->session_id))->onQueue('deterioration_rut');
            dispatch($rut_pavement_type_phi);

        }
        catch (\Exception $e)
        {
            \Log::info('rut_benchmark');
            \Log::info($e->getMessage());
        }
    }
    private function _sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from) 
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
    private function setMaxXAxis($xml, $maximum_x_axis)
    {
        $scaling = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->valAx[0]->children('c', TRUE)->scaling;
        $data = Helper::round20($maximum_x_axis);
        $this->_sxml_append($scaling, simplexml_load_string('<c:max val="' . $data . '"/>'));
        return $xml;
    }
    private function setMinXAxis($xml, $minimum_x_axis)
    {
        $scaling = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->valAx[0]->children('c', TRUE)->scaling;
        $this->_sxml_append($scaling, simplexml_load_string('<c:min val="' . $minimum_x_axis . '"/>'));
        return $xml;
    }
    
    private function setMaxYAxis($xml, $maximum_y_axis)
    {
        $scaling = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->valAx[1]->children('c', TRUE)->scaling;
        $this->_sxml_append($scaling, simplexml_load_string('<c:max val="' . $maximum_y_axis . '"/>'));
        return $xml;
    }
}
