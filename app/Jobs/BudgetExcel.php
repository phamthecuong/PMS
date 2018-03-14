<?php

namespace App\Jobs;

use App\Classes\Helper;

class BudgetExcel
{
	function _writeCrackSheet($xlsx, $path, $scenario, $crack_rank)
	{
		$sheet_data = $this->_getDataForCrackSheet($path, $scenario, $crack_rank);

		$sheet_index = $xlsx->findSheetByName('con_crack');
        $sheet = $xlsx->selectSheet($sheet_index);

        foreach ($sheet_data as $index => $d) 
        {
            foreach ($d as $column_index => $column_value) 
            {
            	$row_index = $index + 1;
            	if ($index == 0)
	            {
	            	$sheet->data($this->_getNameFromNumber($column_index) . $row_index, $column_value);
	            }
	            else
	            {
	            	$sheet->data($this->_getNameFromNumber($column_index) . $row_index, $column_value, 'n');
	            }
            }
        }
	}

	function _writeRutSheet($xlsx, $path, $scenario, $rut_rank)
	{
		$sheet_data = $this->_getDataForRutSheet($path, $scenario, $rut_rank);

		$sheet_index = $xlsx->findSheetByName('con_rut');
        $sheet = $xlsx->selectSheet($sheet_index);

        foreach ($sheet_data as $index => $d) 
        {
            foreach ($d as $column_index => $column_value) 
            {
            	$row_index = $index + 1;
            	if ($index == 0)
	            {
	            	$sheet->data($this->_getNameFromNumber($column_index) . $row_index, $column_value);
	            }
	            else
	            {
	            	$sheet->data($this->_getNameFromNumber($column_index) . $row_index, $column_value, 'n');
	            }
            }
        }
	}

	function _writeTableSheet($xlsx, $path, $scenario, $budget_simulation)
	{
		$dataset = $this->_getDataForTableSheet($path, $scenario);

		$sheet_index = $xlsx->findSheetByName('table');
        $sheet = $xlsx->selectSheet($sheet_index);
        $sheet->data('F5', $dataset[1], 'n');

        $repair_classification = \App\Models\tblRClassification::all()->pluck("name_en", 'id')->toArray();
		$sheet->data('C4', $repair_classification[1]);
		$sheet->data('D4', $repair_classification[2]);
		$sheet->data('E4', $repair_classification[3]);
		$sheet->data('H4', $repair_classification[1]);
		$sheet->data('I4', $repair_classification[2]);
		$sheet->data('J4', $repair_classification[3]);
        
        $sheet_data = $dataset[0];
        foreach ($sheet_data as $index => $d) 
        {
            $row_index = $index + 6;
            if ($index != 0)
            {
            	$sheet->cloneRow(6, $row_index);	
            }
            
            foreach ($d as $column_index => $column_value) 
            {
            	$sheet->data($this->_getNameFromNumber($column_index) . $row_index, $column_value, 'n');
            }
        }

        $this->_createHeader($xlsx, $budget_simulation, $scenario, $dataset[1]);
	}

	function _getDataForTableSheet($path, $scenario)
	{
		$sheet_data = [];
		$current_risk = 0;

		$fp = fopen($path . 'output' . $scenario . '/cost.csv', 'r');
		
		$index = 0;
		while ($line = fgetcsv($fp)) 
		{
			if ($index != 0 && $index != 1)
			{
				$sheet_data[$index - 2] = [
					intval($line[0]), 
					floatval($line[1]), 
					floatval($line[2]), 
					floatval($line[3]), 
					floatval($line[4])
				];
			}

			$index++;
		}
		fclose($fp);
		
		$fp = fopen($path . 'output' . $scenario . '/risk.csv', 'r');
		
		$index = 0;
		while ($line = fgetcsv($fp)) 
		{
			if ($index != 0) 
			{
				if ($index == 1)
				{
					$current_risk = floatval($line[1]);
				}
				else
				{
					$sheet_data[$index - 2][] = floatval($line[1]);
				}
			}

			$index++;
		}
		fclose($fp);

		$fp = fopen($path . 'output' . $scenario . '/length.csv', 'r');
		
		$index = 0;
		while ($line = fgetcsv($fp)) 
		{
			if ($index != 0 && $index != 1)
			{
				$sheet_data[$index - 2][] = floatval($line[1]);
				$sheet_data[$index - 2][] = floatval($line[2]);
				$sheet_data[$index - 2][] = floatval($line[3]);
				$sheet_data[$index - 2][] = floatval($line[4]);
			}

			$index++;
		}
		fclose($fp);
		return [$sheet_data, $current_risk];
	}

	function _getNameFromNumber($num) 
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

	function _getDataForCrackSheet($path, $scenario, $crack_rank)
	{
		$sheet_data = [];
		
		$fp = fopen($path . 'output' . $scenario . '/con_crack.csv', 'r');
		
		$sheet_data[0] = ['year'];
		foreach ($crack_rank as $c) 
		{
			$sheet_data[0][] = Helper::convertConditionInforToText($c->from, $c->to, 'C');
		}
		$index = 0;
		while ($line = fgetcsv($fp)) 
		{
			if ($index != 0)
			{
				$tmp = [intval($line[0])];
				for ($i = 1; $i <= $crack_rank->count(); $i++)
				{
					$tmp[] = floatval($line[$i]);
				}

				$sheet_data[$index] = $tmp;
			}
			$index++;
		}
		fclose($fp);
		return $sheet_data;	
	}

	function _getDataForRutSheet($path, $scenario, $rut_rank)
	{
		$sheet_data = [];
		
		$fp = fopen($path . 'output' . $scenario . '/con_rut.csv', 'r');
		
		$sheet_data[0] = ['year'];
		foreach ($rut_rank as $r) 
		{
			$sheet_data[0][] = Helper::convertConditionInforToText($r->from, $r->to, 'Rut');
		}
		$index = 0;
		while ($line = fgetcsv($fp)) 
		{
			if ($index != 0)
			{
				$tmp = [intval($line[0])];
				for ($i = 1; $i <= $rut_rank->count(); $i++)
				{
					$tmp[] = floatval($line[$i]);
				}

				$sheet_data[$index] = $tmp;
			}

			$index++;
		}
		fclose($fp);
		return $sheet_data;	
	}

	function _createHeader($xlsx, $budget_simulation, $scenario, $current_risk)
	{
		$sheet_index = $xlsx->findSheetByName('table');
        $sheet = $xlsx->selectSheet($sheet_index);
		$sheet->data('E1', $budget_simulation->getInfoOrganization());
		switch ($scenario) 
		{
			case 0:
				$sheet->data('E2', 'Non-constraint');
				break;
			case 1:
				$sheet->data('E2', 'Budget constraint');
				$sheet->data('G2', $budget_simulation->budget_constraint, 'n');
				$sheet->data('H2', 'billion VND');
				break;
			case 2:
				$sheet->data('E2', 'Current Risk Level');
				$sheet->data('G2', 100 * $current_risk, 'n');
				$sheet->data('H2', '%');
				break;
			case 3:
				$sheet->data('E2', 'Risk constraint');
				$sheet->data('G2', $budget_simulation->target_risk, 'n');
				$sheet->data('H2', '%');
				break;
			default:
				//
				break;
		}

		$sheet_index = $xlsx->findSheetByName('summary');
        $sheet = $xlsx->selectSheet($sheet_index);
		$sheet->data('G1', $budget_simulation->getInfoOrganization());
		switch ($scenario) 
		{
			case 0:
				$sheet->data('G2', 'Non-constraint');
				break;
			case 1:
				$sheet->data('G2', 'Budget constraint');
				$sheet->data('I2', $budget_simulation->budget_constraint, 'n');
				$sheet->data('J2', 'billion VND');
				break;
			case 2:
				$sheet->data('G2', 'Current Risk Level');
				$sheet->data('I2', 100 * $current_risk, 'n');
				$sheet->data('J2', '%');
				break;
			case 3:
				$sheet->data('G2', 'Target Risk Level');
				$sheet->data('I2', $budget_simulation->target_risk, 'n');
				$sheet->data('J2', '%');
				break;
			default:
				//
				break;
		}
	}

	function _drawChart($xlsx, $crack_rank, $rut_rank, $budget_simulation)
	{
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/chart1.xml']);
		
		for ($i = 0; $i < 3; $i++)
		{
			$xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->barChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->cat
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'table!$A$5:$A$' . ($budget_simulation->simulation_term + 5);
	        $xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->barChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->val
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'table!$' . $this->_getNameFromNumber($i+2) . '$5:$' . $this->_getNameFromNumber($i+2) . '$' . ($budget_simulation->simulation_term + 5);
		}
        $xml
            ->children('c', TRUE)->chart
            ->children('c', TRUE)->plotArea
            ->children('c', TRUE)->lineChart
            ->children('c', TRUE)->ser
            ->children('c', TRUE)->val
            ->children('c', TRUE)->numRef
            ->children('c', TRUE)->f[0] = 'table!$F$5:$F$' . ($budget_simulation->simulation_term + 5);
        $xlsx->arrXMLs['/xl/charts/chart1.xml'] = $xml->asXML();

        $xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/chart2.xml']);
		
		for ($i = 0; $i < 3; $i++)
		{
			$xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->barChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->cat
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'table!$A$5:$A$' . ($budget_simulation->simulation_term + 5);
	        $xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->barChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->val
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'table!$' . $this->_getNameFromNumber($i+7) . '$5:$' . $this->_getNameFromNumber($i+7) . '$' . ($budget_simulation->simulation_term + 5);
		}
        $xlsx->arrXMLs['/xl/charts/chart2.xml'] = $xml->asXML();

        $xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/chart3.xml']);
		$crack_length = $crack_rank->count();
		for ($i = 0; $i < $crack_length; $i++)
		{
			$xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->areaChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->cat
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'con_crack!$A$2:$A$' . ($budget_simulation->simulation_term + 2);
	        $xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->areaChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->val
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'con_crack!$' . $this->_getNameFromNumber($crack_length-$i) . '$2:$' . $this->_getNameFromNumber($crack_length-$i) . '$' . ($budget_simulation->simulation_term + 2);
		}
        $xlsx->arrXMLs['/xl/charts/chart3.xml'] = $xml->asXML();

        $xml = simplexml_load_string($xlsx->arrXMLs['/xl/charts/chart4.xml']);
		$rut_length = $rut_rank->count();
		for ($i = 0; $i < $rut_length; $i++)
		{
			$xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->areaChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->cat
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'con_rut!$A$2:$A$' . ($budget_simulation->simulation_term + 2);
	        $xml
	            ->children('c', TRUE)->chart
	            ->children('c', TRUE)->plotArea
	            ->children('c', TRUE)->areaChart
	            ->children('c', TRUE)->ser[$i]
	            ->children('c', TRUE)->val
	            ->children('c', TRUE)->numRef
	            ->children('c', TRUE)->f[0] = 'con_rut!$' . $this->_getNameFromNumber($rut_length-$i) . '$2:$' . $this->_getNameFromNumber($rut_length-$i) . '$' . ($budget_simulation->simulation_term + 2);
		}
        $xlsx->arrXMLs['/xl/charts/chart4.xml'] = $xml->asXML();
	}
}