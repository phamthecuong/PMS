<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblDeterioration;

class iri_epsilon implements ShouldQueue
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

            $this->_writeExcel();
            
            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [iri] update pav type flg to ' . ($deterioration->pav_type_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->pav_type_flg += 1;
            $deterioration->save();

            $iri_21 = (new \App\Jobs\iri_21($this->session_id))->onQueue('deterioration_iri');
            dispatch($iri_21);
        }
        catch (\Exception $e)
        {
            \Log::info('iri_epsilon');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }

    private function _writeExcel()
    {
        include_once __DIR__ . "/../../lib/eiseXLSX/eiseXLSX.php";
        $link = 'IRI';
        $id = $this->session_id;
        //count rank-> case
        $deterioration = tblDeterioration::find($id);
        $condition_rank =  $deterioration->condition_rank;
        $condition_rank_array = json_decode($condition_rank);
        $option =  Helper::convertJsonConditionRank($condition_rank);
        $case = count($option[strtolower($link)]);
        
        $template = '../../../public/excel_templates/TemplateDeterioration/Deterioration'.$case.'/DeteriorationPavementType'.$case.'.xlsx';
        $xlsx = new \eiseXLSX($template);
        
        ///////////////////// active sheet 1//////////////////////////////////////////
        $sheet1 = $xlsx->selectSheet(1);
        $sheet1->data('C3','01_deterioration /'.ucwords($link));
        $sheet1->data('F5',ucwords($link).'(mm/m)');
        // load data hazard - tvalue
        $load = Helper::loadExcel($id, $link,'output1','output.csv', true);

        $j = 6;
        for ($i = 6; $i < 6 + $case - 1; $i++)
        {   
            $key = $i-$j;
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
        foreach( $option[strtolower($link)]  as $key => $row)
        {
            $rankCrack[] = array("$key" , $row['from'] );
            $start[] =  $row['from'];
        }
        $j=6;
        for($i= 6; $i< 6+$case; $i++)
        {   
            $key = $i-$j;
            $crack = $rankCrack[$key][1];
            $sheet1->data('F'.$i, $crack,'n'); 
        }
        //load data Expected life length(year)
        $Expected =  Helper::total($load,$case);
        $maximum_x_axis = $Expected[$key];
        for($i= 6; $i < 6 + $case; $i++)
        {   
            $key = $i-$j;
            $sheet1->data('G'.$i, $Expected[$key], 'n'); 
            if ($maximum_x_axis < $Expected[$key])
            {
                $maximum_x_axis = $Expected[$key];
            }
        }

        ///////////////////////////active shhet 2///////////////////////////////////////////////////
        $sheet2 =  $xlsx->selectSheet(2);
        $sheet2->data('B3', '01_deterioration /'.ucwords($link));
        //load data : Markov Transition Probabilities
        $matrixFile = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output1/matrix.csv');

        foreach($matrixFile as $row)
        {
            $matrix[]=explode(',',$row);
        }
        $i = 7;
        for($j= 0; $j< count($matrix); $j++)
        {   
            for ($col = ord('d'); $col< ord('d')+$case; $col++)
            {
                $key = $col - ord('d');
                $sheet2->data(chr($col).$i, $matrix[$j][$key],'n');
            }
        $i++;
        }
        //load data: Deterioration Transition Probabilities
        //+ load year
        foreach($option[strtolower($link)]  as $row)
        {
            $to =  $row['to'];
            $from =  $row['from'];
            $array = Helper::convertConditionInforToText($from, $to, 'I');
            $year[] = $array;
        }
        for ($col = ord('d'); $col <= ord('d')+$case-1; $col++)
        {
            $key = $col - ord('d');
            $sheet2->data(chr($col) . (7 + $case + 2),$year[$key]);
        }
      
        // end load year
        $transitionFile = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output1/transition.csv');
        foreach($transitionFile as $row)
        {
            $transition[]=explode(',',$row);
        }
        // $i = 17;
        $i = 7 + $case + 3;
        for($j= 0; $j< count($transition); $j++)
        {   
            for ($col = ord('c'); $col <= ord('c')+$case; $col++)
            {
                $key = $col - ord('c');
                $sheet2->data(chr($col).$i,$transition[$j][$key],'n');
            }
            $i++;
        }

        ////////////////////////////////active sheet 3////////////////////////
        $sheet3 =  $xlsx->selectSheet(3);
        $sheet3->data('C3','01_deterioration /'.ucwords($link));
        $sheet3->data('G5',ucwords($link).'(mm/m)');
        //load data Hazard rate
        $fileHazartRate = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/para1.csv');
        $i = 6;
        for($j= 0; $j< count($fileHazartRate)-1; $j++)
        {
            $sheet3->data('c'.$i, $fileHazartRate[$j],'n');
            $i++;
        }
       
        //load  data Fai-Epsilon
            // + load fai
        $fai = Helper::fai($id, $link, 'output2', true);
        $sheet3->data('D6',$fai[0],'n');
            // + load epsilon
        $epsilon = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon10.csv');
        foreach ($epsilon as  $value) {
             $data[] = explode(',',$value);
        }
        $sheet3->data('E6', $data[1][2],'n');
            // +load data crack
        $j=6;
        for($i= 6; $i< 6+ $case; $i++)
        {   
            $key = $i-$j;
            $crack = $rankCrack[$key][1];
            $sheet3->data('G'.$i, $crack, 'n'); 
        }
        //load data Expected life length(year)
        $HazartRate3[] = $fileHazartRate;
        $Expected =  Helper::total($HazartRate3,$case);
        $maximum_x_axis2 = 0;
        for($i= 6; $i < 6 + $case; $i++)
        {   
            $key = $i-$j;
            $sheet3->data('H'.$i, $Expected[$key], 'n'); 
            if ($maximum_x_axis2 < $Expected[$key])
            {
                $maximum_x_axis2 = $Expected[$key];
            }
        }

        /////////////////////////////// active sheet 4 ///////////////////////
        $sheet4 =  $xlsx->selectSheet(4);
        $sheet4->data('B3','01_deterioration / '.ucwords($link));
        //load data : Markov Transition Probabilities
        $matrix_1 = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/matrix_1.csv');

        foreach($matrix_1 as $row)
        {
            $matrixType4[]=explode(',',$row);
        }
       
        $i = 7;
        for($j= 0; $j< count($matrixType4); $j++)
        {   
            for ($col = ord('d'); $col< ord('d')+$case; $col++)
            {
                $key = $col - ord('d');
                $sheet4->data(chr($col).$i, $matrixType4[$j][$key],'n');
            }
        $i++;
        }
      
        //load data: Deterioration Transition Probabilities
        //+ load year
        foreach($option[strtolower($link)]  as $row)
        {
            $to =  $row['to'];
            $from =  $row['from'];
            $array = Helper::convertConditionInforToText($from, $to, 'I');
            $year[] = $array;
        }
        for ($col = ord('d'); $col < ord('d')+ $case; $col++)
        {
            $key = $col - ord('d');
            $sheet4->data(chr($col) . (7 + $case + 2), $year[$key]);
        }
      
        // end load year
        $transition_1 = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/transition_1.csv');
        foreach($transition_1 as $row)
        {
            $transitionType4[]= explode(',',$row);
        }

        $i = 7 + $case + 3;
        for($j= 0; $j< count($transitionType4); $j++)
        {   
            for ($col = ord('c'); $col<= ord('c')+$case; $col++)
            {
                $key = $col - ord('c');
                $sheet4->data(chr($col).$i,$transitionType4[$j][$key],'n');
            }
            $i++;
        }

        ///////////////////active sheet 5 //////////////////////////
        $sheet5 = $xlsx->selectSheet(5);
        $sheet5->data('C3','01_deterioration /'.ucwords($link));
        $sheet5->data('G5',ucwords($link).'(mm/m)');
        //load data Hazard rate
        $fileHazartRate = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/para2.csv');
      
        $i = 6;
        for($j= 0; $j< count($fileHazartRate)-1; $j++)
        {
            $sheet3->data('c'.$i, $fileHazartRate[$j],'n');
            $i++;
        }
       
        //load  data Fai-Epsilon
            // + load fai
        $fai = Helper::fai($id, $link, 'output2', true);
        $sheet5->data('D6', $fai[0], 'n');
            // + load epsilon
        $epsilon = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon10.csv');
        foreach ($epsilon as  $value) {
             $data[] = explode(',',$value);
        }
        $sheet5->data('E6', $data[3][2],'n');
            // +load data crack
        $j= 6;
        for($i= 6; $i< 6+ $case; $i++)
        {   
            $key = $i-$j;
            $crack = $rankCrack[$key][1];
            $sheet5->data('G'.$i, $crack,'n'); 
        }
        //load data Expected life length(year)
        $HazartRate5[] = $fileHazartRate;
        $Expected = Helper::total($HazartRate5,$case);
        $maximum_x_axis3 = 0;
        for($i= 6; $i< 6+ $case; $i++)
        {   
            $key = $i-$j;
            $sheet5->data('H'.$i, $Expected[$key], 'n'); 
            if ($maximum_x_axis3 < $Expected[$key])
            {
                $maximum_x_axis3 = $Expected[$key];
            }
        }
        ////////////////////// active sheet 6 ////////////////////////////
        $sheet6 = $xlsx->selectSheet(6);
        $sheet6->data('B3','01_deterioration / '.ucwords($link));
        //load data : Markov Transition Probabilities
        $matrix_2 = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/matrix_2.csv');

        $matrixType6 = [];
        foreach($matrix_2 as $row)
        {
            $matrixType6[] = explode(',',$row);
        }
       
        $i = 7;
        for ($j= 0; $j< count($matrixType6); $j++)
        {   
            for ($col = ord('d'); $col< ord('d')+$case; $col++)
            {
                $key = $col - ord('d');
                $sheet6->data(chr($col).$i, $matrixType6[$j][$key],'n');
            }
            $i++;
        }
      
        //load data: Deterioration Transition Probabilities
        //+ load year
        foreach ($option[strtolower($link)] as $row)
        {
            $to =  $row['to'];
            $from = $row['from'];
            $array = Helper::convertConditionInforToText($from, $to, 'I');
            $year[] = $array;
        }
        for ($col = ord('d'); $col < ord('d')+ $case; $col++)
        {
            $key = $col - ord('d');
            $sheet6->data(chr($col) . (7 + $case + 2), $year[$key]);
        }
      
        // end load year
        $transition_2 = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/transition_2.csv');
        $transitionType6 = [];
        foreach($transition_2 as $row)
        {
            $transitionType6[]=explode(',',$row);
        }

        $i = 7 + $case + 3;
        for ($j= 0; $j< count($transitionType6); $j++)
        {   
            for ($col = ord('c'); $col <= ord('c')+$case; $col++)
            {
                $key = $col - ord('c');
                $sheet6->data(chr($col).$i,$transitionType6[$j][$key], 'n');
            }
            $i++;
        }

         //////////active sheet 7 ///////////////////////////////
        $sheet7 = $xlsx->selectSheet(7);
        $sheet7->data('C3','01_deterioration / '.$link);
        $sheet7->data('G5',ucwords($link).'(mm/m)');
        //load data Hazard rate
        $fileHazartRate = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/para3.csv');
        $i = 6;
        for($j= 0; $j< count($fileHazartRate)-1; $j++)
        {
            $sheet7->data('c'.$i, $fileHazartRate[$j],'n');
            $i++;
        }
       
        //load  data Fai-Epsilon
            // + load fai
        $fai = Helper::fai($id,$link,'output2', true);
        $sheet7->data('D6',$fai[0],'n');
            // + load epsilon
        $epsilon = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon10.csv');
        foreach ($epsilon as  $value) {
             $data[] = explode(',',$value);
        }
        $sheet7->data('E6', $data[2][2],'n');
            // +load data crack
        $j=6;
        for($i= 6; $i< 6+$case; $i++)
        {   
            $key = $i-$j;
            $crack = $rankCrack[$key][1];
            $sheet7->data('G'.$i, $crack,'n'); 
        }
        //load data Expected life length(year)
        $HazartRate7[] = $fileHazartRate;
        $Expected = Helper::total($HazartRate7,$case);
        $maximum_x_axis4 = 0;
        for($i= 6; $i < 6 + $case; $i++)
        {   
            $key = $i-$j;
            $sheet7->data('H'.$i, $Expected[$key], 'n'); 
            if ($maximum_x_axis4 < $Expected[$key])
            {
                $maximum_x_axis4 = $Expected[$key];
            }
        } 
        ///////active sheet 8 ///////////////////// 

        $sheet8 =  $xlsx->selectSheet(8);
        $sheet8->data('B3','01_deterioration /'.ucwords($link));
        //load data : Markov Transition Probabilities
        $matrix_3 = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/matrix_3.csv');
        foreach($matrix_3 as $row)
        {
            $matrixType8[] = explode(',', $row);
        }
        $i = 7;
        for($j= 0; $j< count($matrixType8); $j++)
        {   
            for ($col = ord('d'); $col< ord('d')+$case; $col++)
            {
                $key = $col - ord('d');
                $sheet8->data(chr($col).$i, $matrixType8[$j][$key],'n');
            }
            $i++;
        }
      
        //load data: Deterioration Transition Probabilities
        //+ load year
        foreach($option[strtolower($link)] as $row)
        {
            $to =  $row['to'];
            $from =  $row['from'];
            $array = Helper::convertConditionInforToText($from, $to, 'I');
            $year[] = $array;
        }
        for ($col = ord('d'); $col < ord('d')+ $case; $col++)
        {
            $key = $col - ord('d');
            $sheet8->data(chr($col) . (7 + $case + 2), $year[$key]);
        }
      
        // end load year
        $transition_3 = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/output3/transition_3.csv');
        foreach($transition_3 as $row)
        {
            $transitionType8[] = explode(',',$row);
        }

        $i = 7 + $case + 3;
        for ($j= 0; $j< count($transitionType8); $j++)
        {   
            for ($col = ord('c'); $col <= ord('c')+ $case; $col++)
            {
                $key = $col - ord('c');
                $sheet8->data(chr($col).$i,$transitionType8[$j][$key], 'n');
            }
            $i++;
        } 
        //custom xml
        libxml_use_internal_errors(true);

        
        $xml = $this->createChartDataPerType($xlsx, $name = 'chart1', $case, $start, $maximum_x_axis);

        $xlsx->arrXMLs['/xl/charts/chart1.xml'] = $xml->asXML();

        $xml = $this->createChartDataPerType($xlsx, $name = 'chart3', $case, $start, $maximum_x_axis2);

        $xlsx->arrXMLs['/xl/charts/chart3.xml'] = $xml->asXML();

        $xml = $this->createChartDataPerType($xlsx, $name = 'chart5', $case, $start, $maximum_x_axis3);

        $xlsx->arrXMLs['/xl/charts/chart5.xml'] = $xml->asXML();

        $xml = $this->createChartDataPerType($xlsx, $name = 'chart7', $case, $start, $maximum_x_axis4);

        $xlsx->arrXMLs['/xl/charts/chart7.xml'] = $xml->asXML();

        $xlsx->Output("pavement-type.xlsx", "F");
    }
    private function createChartDataPerType($xlsx, $name, $case, $from, $max)
    {
        $chart_name = $name;
        $xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/' . $chart_name . '.xml']);
        
        $xml = $this->setMaxXAxis($xml, $max);
        //$xml = $this->setMinXAxis($xml, 0);
        $xml = $this->setMaxYAxis($xml, end($from));

        return $xml;
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
