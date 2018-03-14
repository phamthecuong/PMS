<?php

namespace App\Jobs;
use App ,Auth ,Excel, Hash, Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\tblDeterioration;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use App\Models\tblRoad;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App\Models\mstRoadCategory;

class crack_section_32 implements ShouldQueue
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
        try
        {
            chdir(__DIR__ . '/../../public/application/process/deterioration/' . $this->session_id . '/crack/');
            $re = shell_exec('./crack.sh 3');
            
            $this->_writeExcel();
            $this->_formatExcel();

            $deterioration = tblDeterioration::findOrFail($this->session_id);
            $log = [$deterioration->log_text, date('Y-m-d H:i:s') . ': [crack] update section flg to ' . ($deterioration->section_flg + 1)];
            $deterioration->log_text = implode(PHP_EOL , $log);
            $deterioration->section_flg += 1;
            $deterioration->save();
        }
        catch (\Exception $e)
        {
            \Log::info('crack_section_32');
            \Log::info($e->getMessage());
            echo $e->getMessage();
        }
    }

    private function _writeExcel()
    {
        // data to excel
        $id = $this->session_id;
        $link = 'crack';
        $deterioration = tblDeterioration::find($id);
        $condition_rank =  $deterioration->condition_rank;
        $condition_rank_array = json_decode($condition_rank);
        $option =  Helper::convertJsonConditionRank($condition_rank);
        // check case for template
        $case = count($option[strtolower($link)]);
       
        $template = __DIR__ . '/../../public/excel_templates/TemplateDeterioration/Deterioration'.$case.'/DeteriorationSection'.$case.'.xlsx';
        // $inputFile = file(__DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/data/input.csv');
        // $input = [];
        // foreach ($inputFile as $row1)
        // {
        //     $input[] = explode(',', $row1);
        // }
        // unset($input[0]);

        $existingFilePath = $template;
        $newFilePath = __DIR__ . "/../../public/application/process/deterioration/{$id}/{$link}/section.xlsx";

        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($existingFilePath);

        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($newFilePath);
        //data to sheet 1
        $epsilonFile31 = file(__DIR__ .'/../../public/application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon31.csv');

        foreach ($epsilonFile31 as $row)
        {
            $tmp = explode(',', $row);

            $tmp[1] = intval($tmp[1]);
            for ($i = 2; $i < (count($tmp) - 1); $i++)
            {
                $tmp[$i] = floatval($tmp[$i]);
            }
            $epsilon31[] = $tmp;
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
        $tblBranch = tblBranch::where('branch_number', '00')->get();
        foreach($tblBranch as $row)
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
        $epsilonFile32 = file(__DIR__ .'/../../public/application/process/deterioration/'.$id.'/'.$link.'/output4/epsilon32.csv');

        foreach ($epsilonFile32 as $row)
        {
            $tmp = explode(',', $row);

            $tmp[1] = intval($tmp[1]);
            for ($i = 2; $i < (count($tmp) - 1); $i++)
            {
                $tmp[$i] = floatval($tmp[$i]);
            }
            $epsilon32[] = $tmp;
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
                $sub1[] = array('-','-','-','-','-');
            }
            else if (Helper::subString($row[0],$target, $route_category, $route))
            {
                $sub1[] = Helper::subString($row[0],$target, $route_category, $route);
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
        //
        $style = (new StyleBuilder())->setShouldWrapText(false)->build();
        // let's read the entire spreadsheet
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) 
        {
            if ($sheetIndex !== 1) 
            {
                $writer->addNewSheetAndMakeItCurrent();
            }
            if ($sheet->getIndex() === 0) 
            { 
                foreach ($sheet->getRowIterator() as $row) 
                {
                    $writer->addRow($row);
                    break;
                }
                foreach ($epsilon31 as $key => $v) 
                {
                    $writer->addRowWithStyle($v, $style);    
                }
            }
            else if ($sheet->getIndex() === 1) 
            { 
                foreach ($sheet->getRowIterator() as $row) 
                {
                    $writer->addRow($row);
                    break;
                }
                foreach ($epsilon32 as $key => $v1) 
                {
                    $writer->addRowWithStyle($v1, $style);    
                }
            }
            // else if ($sheet->getIndex() === 2) 
            // { 
            //     foreach ($sheet->getRowIterator() as $row) 
            //     {
            //         $writer->addRow($row);
            //         break;
            //     }
            //     foreach ($input as $key => $v) 
            //     {
            //         $writer->addRowWithStyle([intval($v[0]), (int)$v[1], (int)$v[2], (int)$v[3], (int)$v[4], (string)$v[5], $v[6]], $style);    
            //     }
            // }
            else
            {
                foreach ($sheet->getRowIterator() as $row) 
                {
                    $writer->addRow($row);
                }
                
            }
            $cur_sheet = $writer->getCurrentSheet();
            $cur_sheet->setName($sheet->getName());
        }

        $reader->close();
        $writer->close();
    }

    private function _formatExcel()
    {
        include_once __DIR__ . "/../../lib/eiseXLSX/eiseXLSX.php";
        $id = $this->session_id;
        $link = 'crack';
        
        $deterioration = tblDeterioration::find($id);
        $condition_rank =  $deterioration->condition_rank;
        $condition_rank_array = json_decode($condition_rank);
        $option = Helper::convertJsonConditionRank($condition_rank);
        //check case for template
        $case = count($option[strtolower($link)]);

        $template = __DIR__ . '/../../public/application/process/deterioration/'.$id.'/'.$link.'/section.xlsx';
        $xlsx = new \eiseXLSX($template);
    
        //edit mergercell
        $sheet1 = $xlsx->arrXMLs['/xl/worksheets/sheet1.xml'];
        $merge_cell = simplexml_load_string('<mergeCells count="2"><mergeCell ref="I1:'.chr(ord('I')+ $case-2).'1"/><mergeCell ref="'.chr(ord('I')+ $case-1).'1:'.chr(ord('I')+ 2*$case-2).'1"/></mergeCells>');
        $this->_sxml_append($sheet1, $merge_cell);

        $sheet2 = $xlsx->arrXMLs['/xl/worksheets/sheet2.xml'];
        $merge_cell = simplexml_load_string('<mergeCells count="2"><mergeCell ref="I1:'.chr(ord('I')+ $case-2).'1"/><mergeCell ref="'.chr(ord('I')+ $case-1).'1:'.chr(ord('I')+ 2*$case-2).'1"/></mergeCells>');
        $this->_sxml_append($sheet2, $merge_cell);

        $xlsx->Output("section.xlsx", "F");
    }

    private function _sxml_append(\SimpleXMLElement $to, \SimpleXMLElement $from) 
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
}
