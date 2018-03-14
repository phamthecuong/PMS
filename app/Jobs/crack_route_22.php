<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\tblDeterioration;

class crack_route_22 implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use DispatchesJobs;

    protected $link = 'crack';
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
            $re = shell_exec('./crack.sh 3');

            $this->_writeExcel();
            
            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [crack] update route flg to ' . ($deterioration->route_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->route_flg += 1;
            $deterioration->save();

            $crack_31 = (new \App\Jobs\crack_31($this->session_id))->onQueue('deterioration_crack');
            dispatch($crack_31);
        }
        catch (\Exception $e)
        {
            \Log::info('crack_route_22');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }

    private function getSheetDataFromEpsilon($file)
    {
        $sheet_data = [];
        $route = [];
        $epsilonFile = file($file);
        foreach ($epsilonFile as $row)
        {
            $sheet_data[] = explode(',', $row);
        }
        // unset key first
        foreach($sheet_data as $key => $row)
        {
            $route[] = $sheet_data[$key][0];   
            unset($sheet_data[$key][0]);
        }

        foreach ($route as $key => $row)
        {
            $sub_route[] = Helper::subString($row);
        }
        unset($sub_route[0]);
        
        $name_route = [];
        $name_route[] = 'BM';
        foreach ($sub_route as $road)
        {
            if ($road[2] != null)
            {
                $name_route[] = $road[2];
            }
            else
            {
                 $name_route[] = '';
            }
        }
        
        foreach ($sheet_data as $index => $row)
        {
            $sheet_data[$index][0] = $name_route[$index];
        }
        return [$sheet_data, $name_route];
    }

    private function createChartDataPerType($xlsx, $type, $case, $from)
    {
        $sheet_route = '003_route_1';
        $chart_name = 'chart1';
        $csv_file = public_path('application/process/deterioration/'.$this->session_id.'/'.$this->link.'/output4/');
        if ($type == 2)
        {
            $sheet_route = '003_route_2';
            $chart_name = 'chart2';
            $csv_file.= 'epsilon22.csv';
        }
        else
        {
            $csv_file.= 'epsilon21.csv';   
        }

        $dataset = $this->getSheetDataFromEpsilon($csv_file);
        $sheet_data = $dataset[0];
        $name_route = $dataset[1];
        // Add data to sheet 003_route_1
        $sheet1 = $xlsx->findSheetByName($sheet_route);
        $sheet1 = $xlsx->selectSheet($sheet1);
        $sheet1->data('D3', $this->link);

        foreach ($sheet_data as $index => $d) 
        {
            $row_index = $index + 6;
            if ($index != 0 && ($index + 1 != count($sheet_data)))
            {
                $sheet1->cloneRow(6, $row_index);
            }
            $sheet1->data("C{$row_index}", $d[0]);
            $sheet1->data("D{$row_index}", $d[1], 'n');
            $sheet1->data("E{$row_index}", $d[2], 'n');        
        }

        // add missing row to make export look better
        for ($i = 18; $i > count($sheet_data); $i--)
        {
            $sheet1->cloneRow(count($sheet_data) + 6 + 1, count($sheet_data) + 6 + 2);
        }

        // Add data to sheet 003_route_1_data
        $sheet2 = $xlsx->findSheetByName($sheet_route . '_data');
        $sheet2 = $xlsx->selectSheet($sheet2);
        $sheet2->data('C3', $this->link);
        
        $maximum_x_axis = 0;
        foreach ($sheet_data as $index => $d) 
        {
            $row_index = $index + 6;
            if ($index != 0 && $index != 1 && ($index + 1 != count($sheet_data)))
            {
                $sheet2->cloneRow(7, $row_index); 
            }
            $sheet2->data('B'.$row_index, $name_route[$index]);
            for ($col = ord('c'); $col < ord('c')+ count($d) - 2; $col++)
            {   
                $key = $col - ord('c') + 1;
                $sheet2->data(chr($col).$row_index, floatval($sheet_data[$index][$key]), 'n');
                if ($maximum_x_axis < $sheet_data[$index][$key] && $key != 0 && $key != 1)
                {
                    $maximum_x_axis = $sheet_data[$index][$key];
                }
            }
        }
        
        for ($col= ord('E') + $case-1; $col <= (ord('E')+ ($case-1)*2); $col++)
        {   
            $key = $col - (ord('E')+ $case-1);
            $sheet2->data(chr($col).(6 + count($sheet_data)), $from[$key], 'n');
        }
        // now modify chart
        libxml_use_internal_errors(true);
        //config print
        $print = $xlsx->arrXMLs['/xl/workbook.xml'];
        $print->definedNames->definedName[0] ="'{$sheet_route}_data'!$".'A1'.":$".(chr(ord('E')+ ($case-1)*2))."$".(6+count($sheet_data)-1);
        //$xlsx->arrXMLs['/xl/workbook.xml'] = $print;
        $xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/' . $chart_name . '.xml']);
        $scatter_chart = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->scatterChart;
        $bm_chart = $scatter_chart->children('c', TRUE)->ser;

        // apply maximum for x-Axis
        $xml = $this->setMaxXAxis($xml, $maximum_x_axis);
        $xml = $this->setMinXAxis($xml, 0);
        $xml = $this->setMaxYAxis($xml, end($from));
        // remove BM chart
        $dom = dom_import_simplexml($bm_chart);
        $dom->parentNode->removeChild($dom);

        // remove chart title
        $dom_title = $xml->children('c', TRUE)->chart->children('c', TRUE)->title;
        $dom = dom_import_simplexml($dom_title);
        $dom->parentNode->removeChild($dom);
        // Add chart $y_axis
        $xml = $this->setYAxisLabel($xml, trans('wp.cracking_ratio_%'));

        $y_axis = "'{$sheet_route}_data'!$".(chr(ord('E')+ $case-1))."$" . (count($sheet_data) + 6) . ":$".(chr(ord('E')+ ($case-1)*2))."$" . (count($sheet_data) + 6);
        
        foreach ($sheet_data as $index => $d) 
        {
            $x_axis = "'{$sheet_route}_data'!$".(chr(ord('E')+ $case-1))."$" . ($index+6) . ":$".(chr(ord('E')+ ($case-1)*2))."$" . ($index + 6);
            
            $label = '\'' . $sheet_route . '_data\'!$B$' . ($index + 6);
            $chart1 = $this->createChartExcelItem($index, $label, $x_axis, $y_axis);
            $this->_sxml_append($scatter_chart, $chart1);
        }
        return $xml;
    }

    private function _writeExcel()
    { 
        $id = $this->session_id;
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
        $deterioration = tblDeterioration::find($id);
        $option = Helper::convertJsonConditionRank($deterioration->condition_rank);

        foreach ($option[strtolower($this->link)] as $row)
        {
            $from[] =  $row['from'];
        }
        // check case for template
        $case = count($option[strtolower($this->link)]);
        
        $template = public_path('excel_templates/TemplateDeterioration/Deterioration'.$case.'/DeteriorationRoute'.$case.'.xlsx');
       
        $xlsx = new \eiseXLSX($template);
        
        // load data to
        $xml = $this->createChartDataPerType($xlsx, 1, $case, $from);
        
        $xlsx->arrXMLs['/xl/charts/chart1.xml'] = $xml->asXML();
        // Add data to sheet 003_route_2
        $xml = $this->createChartDataPerType($xlsx, 2, $case, $from);
          
        $xlsx->arrXMLs['/xl/charts/chart2.xml'] = $xml->asXML();

        $xlsx->Output("route.xlsx", "F");
    }

    private function _sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from) 
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    private function createChartExcelItem($order = 0, $label, $x_axis, $y_axis)
    {
        return simplexml_load_string('<c:ser><c:idx val="' . $order . '"/><c:order val="' . $order . '"/><c:tx><c:strRef><c:f>' . $label . '</c:f></c:strRef></c:tx><c:spPr><a:ln w="12700"/></c:spPr><c:xVal><c:numRef><c:f>' . $x_axis . '</c:f></c:numRef></c:xVal><c:yVal><c:numRef><c:f>' . $y_axis . '</c:f></c:numRef></c:yVal><c:smooth val="1"/></c:ser>');
    }

    private function setMaxXAxis($xml, $maximum_x_axis)
    {
        $max_x = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->valAx->children('c', TRUE)->scaling->children('c', TRUE)->max;
        $max_x = dom_import_simplexml($max_x);
        $max_x->setAttribute('val', Helper::round20($maximum_x_axis));
        return $xml;
    }

    private function setMinXAxis($xml, $minimum_x_axis)
    {
        $scaling = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->valAx->children('c', TRUE)->scaling;
        $this->_sxml_append($scaling, simplexml_load_string('<c:min val="' . $minimum_x_axis . '"/>'));
        return $xml;
    }

    private function setMaxYAxis($xml, $maximum_y_axis)
    {
        $scaling = $xml->children('c', TRUE)->chart->children('c', TRUE)->plotArea->children('c', TRUE)->valAx[1]->children('c', TRUE)->scaling;
        $this->_sxml_append($scaling, simplexml_load_string('<c:max val="' . $maximum_y_axis . '"/>'));
        return $xml;
    }

    private function setYAxisLabel($xml, $label)
    {
        $title_el = $xml
            ->children('c', TRUE)->chart
            ->children('c', TRUE)->plotArea
            ->children('c', TRUE)->valAx[1]
            ->children('c', TRUE)->title
            ->children('c', TRUE)->tx
            ->children('c', TRUE)->rich
            ->children('a', TRUE)->p
            ->children('a', TRUE)->r
            ->children('a', TRUE)->t;
        
        $title_el[0] = $label;
        return $xml;
    }
}
