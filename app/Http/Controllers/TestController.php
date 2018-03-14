<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use Yajra\Datatables\Datatables;
use App\Models\User;
use App\Models\tblDeterioration;
use DB, Helper;
use Config;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use App\Models\tblRoad;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App\Models\mstRoadCategory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('front-end.layouts.templates.load_ajax_notification');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // return view('admin.templates.delete')->with(array('id' => 1, 'action' => 'admin_manager.destroy'));
        return view('test')->with(['admin' => 'admin']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function jqtest()
    {
        return view('test');
    }

    // function export_deterioration_output()
    // {
    //     try
    //     {
    //         $ea = new \PHPExcel();
    //         // first sheet: 001_average
    //         $ews = $ea->getSheet(0);
    //         $ews->setTitle('001_average');
    //         $ews->setCellValue('c2', 'Estimation Results');
    //         $ews->setCellValue('c3', '01_deterioration / Crack');
    //         $ews->setCellValue('o2', '=TODAY()');
    //         $ews->setCellValue('c4', '001_Average');

            

    //     }
    //     catch (\Exception $e)
    //     {
    //         dd($e);
    //     }
    // }

    function export_deterioration_output()
    {
        try
        {
            $ea = new \PHPExcel();
            $ea->getProperties()
               ->setCreator('Taylor Ren')
               ->setTitle('PHPExcel Demo')
               ->setLastModifiedBy('Taylor Ren')
               ->setDescription('A demo to show how to use PHPExcel to manipulate an Excel file')
               ->setSubject('PHP Excel manipulation')
               ->setKeywords('excel php office phpexcel lakers')
               ->setCategory('programming');
            $ews = $ea->getSheet(0);
            $ews->setTitle('Data');
            $ews->setCellValue('a1', 'ID'); 
            $ews->setCellValue('b1', 'Season');
            $ews->setCellValue('c1', 'Teams');
            $ews->setCellValue('d1', 'Self Score');
            $ews->setCellValue('e1', 'Opponent Score');
            $ews->setCellValue('f1', 'Date Played');
            $ews->setCellValue('g1', 'Win/Lose');

            $data = array(
                array(-8, 2013, 'LAL vs GSW', 104, 95, '2013-10-05', 'W'),
                array(-7, 2013, 'LAL vs DEN', 88, 97, '2013-10-06', 'L'),
                array(-6, 2013, 'LAL vs DEN', 90, 88, '2013-10-08', 'W'),
                array(-5, 2013, 'LAL vs SAC', 86, 104, '2013-10-10', 'L'),
                array(-4, 2013, 'LAL vs GSW', 95, 100, '2013-10-15', 'L')
            );
            $ews->fromArray($data, ' ', 'A2');
            $header = 'a1:h1';
            $ews->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
            $style = array(
                'font' => array('bold' => true,),
                'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            );
            $ews->getStyle($header)->applyFromArray($style);

            for ($col = ord('a'); $col <= ord('h'); $col++)
            {
                $ews->getColumnDimension(chr($col))->setAutoSize(true);
            }

            $ews2 = new \PHPExcel_Sheet1($ea, 'Summary');
            $ea->addSheet($ews2, 0);
            $ews2->setTitle('Summary');
            $ews2->setCellValue('b2', '=COUNTIF(Data!G2:G6, "W")-COUNTIF(Data!G2:G4, "W")');
            $ews2->setCellValue('b3', '=COUNTIF(Data!G2:G6, "L")-COUNTIF(Data!G2:G4, "L")');
            $ews2->setCellValue('b4', '=b2/(b2+b3)');
            $ews2->getStyle('b4')->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);

            $ews2->setCellValue('a1', 'Lakers 2013-2014 Season');
            $style = array(
                'font' => array('bold' => true, 'size' => 20,),
                'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,),
            );
            $ews2->mergeCells('a1:b1');
            $ews2->getStyle('a1')->applyFromArray($style);
            $ews2->getColumnDimension('a')->setAutoSize(true);
            //label
            $dsl = array(
                new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$B$1', NULL, 1),
                new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$C$1', NULL, 1)      
            );
            //truc hoanh
            $xal = array(
                new \PHPExcel_Chart_DataSeriesValues('String', 'Data!$F$2:$F$6', NULL, 5),
            );
            //truc tung
            $dsv = array(
                new \PHPExcel_Chart_DataSeriesValues('Number', 'Data!$D$2:$D$6', NULL, 5),
                new \PHPExcel_Chart_DataSeriesValues('Number', 'Data!$E$2:$E$6', NULL, 5),
            );

            $ds = new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_LINECHART,
                \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
                range(0, count($dsv)-1),
                $dsl,
                $xal,
                $dsv
            );

            $pa = new \PHPExcel_Chart_PlotArea(NULL, array($ds));
            $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_LEFT, NULL, false);
            $title = new \PHPExcel_Chart_Title('Any literal string');
            $chart = new \PHPExcel_Chart(
                'chart1',
                $title,
                $legend,
                $pa,
                true,
                0,
                NULL,
                NULL
            );

            $chart->setTopLeftPosition('K1');
            $chart->setBottomRightPosition('S17');
            $ews->addChart($chart);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="output.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = \PHPExcel_IOFactory::createWriter($ea, 'Excel2007');
            $writer->setIncludeCharts(true);
            $writer->save('php://output');
            exit();
            // ::sConfiget('excel::export.includeCharts', true);
            // return Excel::create('Output_deterioration', function($excel) {
            //     $excel->sheet('Sheet', function($sheet) {
            //         $sheet->setTitle('Grafico');
            //         $sheet->fromArray(
            //             array(
            //                 array('', 'Rainfall (mm)', 'Temperature (°F)', 'Humidity (%)'),
            //                 array('Jan', 78, 52, 61),
            //                 array('Feb', 64, 54, 62),
            //                 array('Mar', 62, 57, 63),
            //                 array('Apr', 21, 62, 59),
            //                 array('May', 11, 75, 60),
            //                 array('Jun', 1, 75, 57),
            //                 array('Jul', 1, 79, 56),
            //                 array('Aug', 1, 79, 59),
            //                 array('Sep', 10, 75, 60),
            //                 array('Oct', 40, 68, 63),
            //                 array('Nov', 69, 62, 64),
            //                 array('Dec', 89, 57, 66),
            //             )
            //         );
            //         $dataseriesLabels1 = array(
            //             new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$B$2', NULL, 1), //  Temperature
            //         );
            //         $dataseriesLabels2 = array(
            //             new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$C$2', NULL, 1), //  Rainfall
            //         );
            //         $dataseriesLabels3 = array(
            //             new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$D$2', NULL, 1), //  Humidity
            //         );
            //         $xAxisTickValues = array(
            //             new \PHPExcel_Chart_DataSeriesValues('String', 'Grafico!$A$3:$A$14', NULL, 12), //  Jan to Dec
            //         );
            //         $dataSeriesValues1 = array(
            //             new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico!$B$3:$B$14', NULL, 12),
            //         );
            //         $series1 = new \PHPExcel_Chart_DataSeries(
            //             \PHPExcel_Chart_DataSeries::TYPE_BARCHART, // plotType
            //             \PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, // plotGrouping
            //             range(0, count($dataSeriesValues1) - 1), // plotOrder
            //             $dataseriesLabels1, // plotLabel
            //             $xAxisTickValues, // plotCategory
            //             $dataSeriesValues1                              // plotValues
            //         );
            //         $series1->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_COL);
            //         $dataSeriesValues2 = array(
            //             new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico!$C$3:$C$14', NULL, 12),
            //         );
            //         $series2 = new \PHPExcel_Chart_DataSeries(
            //             \PHPExcel_Chart_DataSeries::TYPE_LINECHART, // plotType
            //             \PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // plotGrouping
            //             range(0, count($dataSeriesValues2) - 1), // plotOrder
            //             $dataseriesLabels2, // plotLabel
            //             NULL, // plotCategory
            //             $dataSeriesValues2                              // plotValues
            //         );
            //         $dataSeriesValues3 = array(
            //             new \PHPExcel_Chart_DataSeriesValues('Number', 'Grafico!$D$3:$D$14', NULL, 12),
            //         );
            //         $series3 = new \PHPExcel_Chart_DataSeries(
            //             \PHPExcel_Chart_DataSeries::TYPE_AREACHART, // plotType
            //             \PHPExcel_Chart_DataSeries::GROUPING_STANDARD, // plotGrouping
            //             range(0, count($dataSeriesValues2) - 1), // plotOrder
            //             $dataseriesLabels3, // plotLabel
            //             NULL, // plotCategory
            //             $dataSeriesValues3                              // plotValues
            //         );
            //         $plotarea = new \PHPExcel_Chart_PlotArea(NULL, array($series1, $series2, $series3));
            //         //  Set the chart legend
            //         $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

            //         $title = new \PHPExcel_Chart_Title('Chart awesome');

            //         //  Create the chart
            //         $chart = new \PHPExcel_Chart(
            //                 'chart1', // name
            //                 $title, // title
            //                 $legend, // legend
            //                 $plotarea, // plotArea
            //                 true, // plotVisibleOnly
            //                 0, // displayBlanksAs
            //                 NULL, // xAxisLabel
            //                 NULL            // yAxisLabel
            //         );

            //         //  Set the position where the chart should appear in the Sheet1
            //         $chart->setTopLeftPosition('F2');
            //         $chart->setBottomRightPosition('O16');

            //         //  Add the chart to the Sheet1
            //         $sheet->addChart($chart);
            //     });
            // })
            // ->download('xlsx');    
        }
        catch (\Exception $e)
        {
            dd($e);
        }
        
    }

    function apprun()
    {
        // chdir('../public/application/process/deterioration/ducdn/IRI/');
        // $return = shell_exec('./crack.sh');
        // exec(sprintf("%s > %s 2>&1 &", './crack.sh', 'log.txt'));
        // exec('nohup ./001_Exponential.out &', $output, $return);
        // exec('bash -c "exec nohup setsid ./crack.sh > /dev/null 2>&1 &"');
        // dd($return);
    }

    function updatecsv()
    {
            $target = [];
            $route_category = [];
            $route = [];
            $id = '134e5719-9e99-4051-a1f6-1babaef39b09';
            $link = 'IRI';
            $deterioration = tblDeterioration::find($id);
            $condition_rank =  $deterioration->condition_rank;
            $condition_rank_array = json_decode($condition_rank);
            $option =  Helper::convertJsonConditionRank($condition_rank);
            //check case for template
            foreach($condition_rank_array as $row)
            {
                
                if( $row->target_type == 3)
                {
                    $rank[] = $row->rank;
                }
               
            }
           
            if ($link == 'IRI')
            {
                $case = count($rank);
            }
           
            $template ='excel_templates/TemplateDeterioration/Deterioration'.$case.'/DeteriorationSection'.$case.'.xlsx';
            $inputFile = file('application/process/deterioration/'.$id.'/'.$link.'/data/input.csv');
            $input = [];
            foreach ($inputFile as $row1)
            {
                $input[] = explode(',', $row1);
            }
            unset($input[0]);

           /* $existingFilePath = $template;
            $newFilePath = "application/process/deterioration/{$id}/{$link}/route.xlsx";

            $reader = ReaderFactory::create(Type::XLSX);
            $reader->open($existingFilePath);

            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToFile($newFilePath);*/
            //data to sheet 1
            $epsilonFile31 = file('application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon31.csv');

            foreach ($epsilonFile31 as $row)
            {
                $epsilon31[] = explode(',', $row);
            }

            foreach ($epsilon31 as $key => $value)
            {   
                $region[] = array($value[0]);
                unset($epsilon31[$key][0]);
            }
           
            $tblOrganization = tblOrganization::get();
            foreach($tblOrganization as $row)
            {
                $target[$row->code_id]  = ['name_en' => $row->name_en,'name_vn'=> $row->name_vn ]; 
            }
            //
            $mstRoad_category = mstRoadCategory::get();
            foreach($mstRoad_category as $row)
            {
                $route_category[$row->code_id] = ['code_name' => $row->code_name]; 
            }
            //
            $tblBranch = tblBranch::get();
            foreach ($tblBranch as $row)
            {   
                $route[(string)$row->road_number . (string)$row->road_number_supplement . (string)$row->road_category] = ['name_en' => $row->name_en, 'name_vn' => $row ->name_vn]; 
            }
         
            foreach ($region as $row)
            {   
                if ($row[0] == 'BM')
                {
                    $sub[] =array('-','-','-','-','-');
                }
                else if (Helper::subString($row[0],$target, $route_category, $route))
                {
                   
                    $sub[] = Helper::subString($row[0],$target, $route_category, $route);
                }
                else
                {
                    $sub[] = array('', '', '', '', '');
                }
            }
           
            for ($key = 0; $key < count($epsilon31); $key++)
            {
                array_unshift($epsilon31[$key], $sub[$key][4]); //Route
                array_unshift($epsilon31[$key], $sub[$key][3]);//KM
                array_unshift($epsilon31[$key], $sub[$key][2]);//Right/Left
                array_unshift($epsilon31[$key], $sub[$key][1]);//Road_Category
                array_unshift($epsilon31[$key], $sub[$key][0]);//region
                array_unshift($epsilon31[$key], $region[$key][0]);//Section_ID2
            }
            //
            //data to sheet 2
            $epsilonFile32 = file('application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon32.csv');

            foreach ($epsilonFile32 as $row)
            {
                $epsilon32[] = explode(',', $row);
            }

            foreach ($epsilon32 as $key => $value)
            {   
                $region1[] = array($value[0]);
                unset($epsilon32[$key][0]);
            }
           
            foreach ($region1 as $row)
            {   
                if ($row[0] == 'BM')
                {
                    $sub1[] =array('-','-','-','-','-');
                }
                else if (Helper::subString($row[0],$target, $route_category, $route))
                {
                    $sub1[] = Helper::subString($row[0], $target, $route_category, $route);
                }
                else
                {
                    $sub1[] = array('', '', '', '', '');
                }
            }

            for ($key = 0; $key < count($epsilon32); $key++)
            {
                array_unshift($epsilon32[$key], $sub1[$key][4]); //Route
                array_unshift($epsilon32[$key], $sub1[$key][3]);//KM
                array_unshift($epsilon32[$key], $sub1[$key][2]);//Right/Left
                array_unshift($epsilon32[$key], $sub1[$key][1]);//Road_Category
                array_unshift($epsilon32[$key], $sub1[$key][0]);//region
                array_unshift($epsilon32[$key], $region1[$key][0]);//Section_ID2
            }
           
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

    private function _getNameFromNumber($num) 
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) 
        {
            return $this->_getNameFromNumber($num2 - 1) . $letter;
        } 
        else 
        {
            return $letter;
        }
    }

    function exportWP()
    {
        $template = public_path('excel_templates/wp_template.xlsx');
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");

        $xlsx = new \eiseXLSX($template);

        // $sheet_index = $xlsx->findSheetByName('Loaded sections');
        // $sheet = $xlsx->selectSheet($sheet_index);

        // 

        // 
        $start = microtime(true);
        $xml = $xlsx->arrXMLs['/xl/worksheets/sheet6.xml'];
        // dd($xml);

        for ($i = 6; $i < 5006; $i++)
        {
            $new_row = $xml->sheetData->addChild('row');

            for ($j = 0; $j <= 34; $j++) 
            {
                $new_cell = $new_row->addChild('c'); 

                $new_cell->addAttribute('t', "inlineStr");
                $new_is = $new_cell->addChild('is');
                // text has to be saved as utf-8 (otherwise the spreadsheet file become corrupted)
                $cbd = 'test';
                if (!mb_check_encoding($cbd, 'utf-8')) $cbd = iconv("cp1250", "utf-8", $cbd); 
                $new_T = $new_is->addChild('t', $cbd);  
            }
        }
        // dd(microtime(true) - $start);
        
        $xlsx->arrXMLs['/xl/worksheets/sheet6.xml'] = $xml->asXML();

        $xlsx->Output("wp.xlsx", "D");

        // $start = microtime(true);

        // $tmp_file = public_path('excel_templates/wp_template.xlsx');
        // $obj = \PHPExcel_IOFactory::load($tmp_file);

        // $filename = mt_rand(1, 100000).'.xlsx';
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="'.$filename.'"');
        // header('Cache-Control: max-age=0');

        // dd(microtime(true) - $start);

        // $obj_writer = \PHPExcel_IOFactory::createWriter($obj, 'Excel2007');  //downloadable file is in Excel 2003 format (.xls)
        // $obj_Writer->save('php://output');  //send it to user, of course you can save it to disk also!

        // exit; //done.. exiting!
    }
}
