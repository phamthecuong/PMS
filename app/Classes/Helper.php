<?php
namespace App\Classes;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use Carbon\Carbon;
use Mail, DB, Config, Excel, App, Validator;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataTV;
use App\Models\mstRepairMethod;
use App\Models\tblDeterioration;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App\Models\tblBudgetSimulation;
use App\Models\tblWorkPlanning;
use App\Models\tblSegment;
use App\Models\mstPavementType;

class Helper 
{
    static function checkSurveyTime($check, $data, $url)
    {
        $count = count($check);
        $new = [];
        $err = [];
        $update = [];
        $ignore = [];
        for ($i = 0; $i < $count; $i++)
        {
            if ($check[$i]['section_id'] == '' && $check[$i]['error'] == 0)
            {
            	$check[$i]['ignore'] > 0 ? $ignore[] = $check[$i] : $new[] = $check[$i];
            }
            else
            {
                // if ($check[$i]['section_id'] == $section_data)
                // {
                //     $check[$i]['ignore'] = 1;
                //     $ignore[] = $check[$i];
                // }
                // else
                // {
                	//Query
                    $model = DB::table($data)->where('id', $check[$i]['section_id'])->first();
                    if (!$model)
                    {
                        if ($check[$i]['error'] > 0 && $check[$i]['ignore'] == 0)
                        {
                            $err[] = $check[$i];
                        }
                        else
                        {
                            $check[$i]['ignore'] == 0 ? $new[] = $check[$i] : $ignore[] = $check[$i];
                        }
                    }
                    else
                    {
                        if ($url == 'traffic_volume')
                        {
                            $check[$i]['survey_time'] = $check[$i]['survey_time'].'/01';
                        }
                        if ($check[$i]['ignore'] == 0)
                        {
                            if ($check[$i]['error'] > 0)
                            {
                                $err[] = $check[$i];
                            }
                            if ($check[$i]['section_id'] == $model->id)
                            {
                                $update[] = $check[$i];
                            }
                        }
                        if ($check[$i]['ignore'] > 0)
                        {
                            $ignore[] = $check[$i];
                        }
                    }
                // }
            }
            $section_data = $check[$i]['section_id'];
        }
        return array(
            'update' => $update,
            'ignore' => $ignore,
            'new' => $new,
            'err' => $err
        );
    }
    
	/**
	 * Create a Random String
	 *
	 * Useful for generating passwords or hashes.
	 *
	 * @access	public
	 * @param	string	type of random string.  basic, alpha, alunum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	integer	number of characters
	 * @return	string
	 */
	static function random_string($type = 'alnum', $len = 8)
	{
		switch($type)
		{
			case 'basic'	: return mt_rand();
				break;
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			case 'alpha'	:

					switch ($type)
					{
						case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'numeric'	:	$pool = '0123456789';
							break;
						case 'nozero'	:	$pool = '123456789';
							break;
					}

					$str = '';
					for ($i=0; $i < $len; $i++)
					{
						$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
					}
					return $str;
				break;
			case 'unique'	:
			case 'md5'		:

						return md5(uniqid(mt_rand()));
				break;
			case 'encrypt'	:
			case 'sha1'	:

						$CI =& get_instance();
						$CI->load->helper('security');

						return do_hash(uniqid(mt_rand(), TRUE), 'sha1');
				break;
		}
	}

    /**
     * Returns a positive value if point1 > point2, zero if point1 = point2 and a negative value if point1 < point2.
     * @return int
     */
    static function compareTwoPoint($km1, $m1, $km2, $m2)
    {
        $pos_point1 = $km1 * 10000 + $m1;
        $pos_point2 = $km2 * 10000 + $m2;
        if ($pos_point1 > $pos_point2)
        {
            return 1;
        }
        else if ($pos_point1 == $pos_point2)
        {
            return 0;
        }
        else
        {
            return -1;
        }
    }

	/**
     * Returns a survaytime first
     * @return int
     */
	static function getSurveyTime($has_min = FALSE)
	{
		$custom = (!$has_min) ? 'asc' : 'desc';

		$survey_time_rmd = @tblSectiondataRMD::orderBy('survey_time', $custom)->first()->survey_time;
		$survey_time_mh = @tblSectiondataMH::orderBy('survey_time', $custom)->first()->survey_time;
		$survey_time_tv = @tblSectiondataTV::orderBy('survey_time', $custom)->first()->survey_time;
		$array = array($survey_time_rmd, $survey_time_mh, $survey_time_tv);

		$result = $survey_time_rmd;
		foreach ($array as $key => $value)
		{
			if (strtotime($value) - strtotime($result) >= 0)
			{
				$result = $value;
			}
		}
		return $result;
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	public static function copyr($source, $dest)
	{
	    if (is_link($source))
	    {
	        return symlink(readlink($source), $dest);
	    }

	    if (is_file($source))
	    {
	        return copy($source, $dest);
	    }

	    if (!is_dir($dest))
	    {
	        mkdir($dest);
	    }

	    $dir = dir($source);
	    while (false !== $entry = $dir->read())
	    {
	        if ($entry == '.' || $entry == '..')
	        {
	            continue;
	        }

	        Helper::copyr("$source/$entry", "$dest/$entry");
	    }

	    $dir->close();
	    return true;
	}

	/**
	 * Get data colNames for jqfrid
	 * @param       string   $source    Source path
	 * @return      array    Returns array header table
	 */
	public static function getHeaderJqfrid($source)
	{
		$file = fopen("$source", "r");
		$header = fgetcsv($file);
		return $header;
	}

	/**
	 * Get data colModel for jqfrid
	 * @param       string   $source    Source path
	 * @param       string   $custom    custom type for colum
	 * @param       string   $value_custom    custom value for colum
	 * @return      array    Returns array customcolum table
	 */
	public static function getColModelJqgird($source, $custom = '', $value_custom = '')
	{
		$header = Helper::getHeaderJqfrid($source);

		$col_model = array();
		foreach ($header as $key => $value)
		{
			$array_core[] = $value;
			$col_model[] = array(
				'name' => $value,
				'index' => $value,
				// 'editoptions' => array('size' => '10')
			);

			if ($custom != '' && $value_custom != '')
			{
				$col_model[][] += array($custom => $value_custom);
			}
		}

		return $col_model;
	}

	/**
	 * Get data content for jqfrid
	 * @param       string   $source    Source path
	 * @return      array    Returns array data content for jqfrid
	 */
	public static function getDataJqgird($source)
	{
        $file = fopen("$source", "r");
        $header = Helper::getHeaderJqfrid($source);
		$array_core = array();
		foreach ($header as $key => $value)
		{
			$array_core[] = $value;
		}

		$json_data = array();
		$herder_flg = TRUE;

		$i = 0;
		while(!feof($file))
		{
			$data = fgetcsv($file);
			$tmp = array();
			if ($herder_flg)
			{
				$herder_flg = FALSE;
				continue;
			}
			else
			{
				if (is_array($data) && count($data) > 0)
				{
					foreach ($data as $key => $value)
					{
						foreach ($array_core as $key_core => $value_core)
						{
							if ($key == $key_core)
							{
								$tmp[$value_core] = $value;
								break;
							}
							else
							{
								continue;
							}
						}
					}
					$json_data[] = $tmp;
				}
				else
				{
					continue;
				}
			}
		}

		fclose($file);

		return $json_data;
	}

	/**
	 * convert json from condition_rank in tblDeterioration to array
	 * @param       json   $json   json from condition_rank
	 * @return      array    Returns array data C, R, I
	 */
	public static function convertJsonConditionRank($json)
	{
		$data = json_decode($json);
		// usort($data, array($this, 'sortByOrder'));
		// if (isset($data) && is_array($data) && count($data) > 0)
		// {
		// 	usort($data, 'Helper::sortByOrder');
		// }
  		//       else
  		//       {
  		//           throw new \Exception("Server Error", 1);
  		//       }
		$array_rut = array();
		$array_crack = array();
		$array_iri = array();
		foreach ($data as $key => $object)
		{
			if ($object->target_type == 1)
			{
				$array_crack[] = array(
					'from' => (float)$object->from,
					'to' => (float)$object->to,
					// 'color' => (isset($object->cell_id)) ? tblRepairMatrixCell::find($object->cell_id)->repair_method->color : NULL
				);
			}
			else if ($object->target_type == 2)
			{
				$array_rut[] = array(
					'from' => (float)$object->from,
					'to' => (float)$object->to,
					// 'color' => (isset($object->cell_id)) ? tblRepairMatrixCell::find($object->cell_id)->repair_method->color : NULL
				);
			}
			else if ($object->target_type == 3)
			{
				$array_iri[] = array(
					'from' => (float)$object->from,
					'to' => (float)$object->to,
				);
			}
		}
		return array(
			'rut' => $array_rut,
			'crack' => $array_crack,
			'iri' => $array_iri
		);
	}

	public static function sortByOrder($a, $b) {
		if ($a->from == $b->from)
		{
			return ($a->to > $b->to) ? 1 : -1;
		}
	    return $a->from - $b->from;
	}

	static function getInfoRepairMethod($source)
	{
		$file = fopen("$source","r");
		$array_repair_method = array();
		while (!feof($file))
		{
			$data = fgetcsv($file);
			if (is_array($data))
			{
				$repair_method = mstRepairMethod::find($data[0]);
				$array_repair_method[] = array(
					'name' => (Config::get('app.locale') == 'en') ? $repair_method->name_en : $repair_method->name_vn,
					'color' => $repair_method->color,
					'id' => $repair_method->id,
				);
			}
		}

		return $array_repair_method;
	}

    //  duc.dn  .21.12.2016 
    // function covert information form and to to text
    public static function convertConditionInforToText($from, $to, $c)
    {
        $from = floatval($from);
        $to = floatval($to);
        if (fmod($to, 1) !== 0.00)
        {
            $to = intval($to);
        }
        if ($from == $to )
        {
            return $c . ' = ' . intval($to);
        }
        else if ($to == NULL)
        {
            return $c . ' ≥ ' . intval($from);
        }
        else
        {
            if (fmod($from, 1) !== 0.00)
            {
                return intval($from) . ' < ' . $c. ' < ' . intval($to) ;
            }
            return intval($from) . ' ≤ ' . $c . ' < ' . intval($to) ;
        }
    }

	// public static function getLabelChartJs($condition_rank, $type)
	// {
	// 	$data_set = Helper::convertJsonConditionRank($condition_rank);
	// 	$data = tblDeterioration::getDataRepairMatrix($condition_rank);
	// 	$data_label;
	// 	$dataset_detail;

	// 	switch ($type)
	// 	{
	// 		case 'crack':
	// 			$data_label = $data_set['crack'];
	// 			$dataset_detail = $data['crack'];
	// 			break;

	// 		case 'rut':
	// 			$data_label = $data_set['rut'];
	// 			$dataset_detail = $data['rut'];
	// 			break;

	// 		case 'iri':
	// 			$data_label = $data_set['iri'];
	// 			$dataset_detail = $data['iri'];
	// 			break;

	// 		case 'year':
	// 			$data_label = $data_set['crack'];
	// 			$dataset_detail = $data['crack'];
	// 			break;

	// 		default:
	// 			break;
	// 	}

	// 	$labels = array();
	// 	foreach ($data_label as $key => $value)
	// 	{
	// 		array_unshift($labels, $value['from'], $value['to']);
	// 	}
	// 	$labels = array_unique($labels);
	// 	array_multisort($labels);

	// 	return array(
	// 		'labels' => $labels,
	// 		'dataset_detail' => $dataset_detail,
	// 	);
	// }

	// public static function getDataChartJs($source, $type)
	// {
	// 	$col = 0;
	// 	$flg_1 = TRUE;
	// 	$array_data_col = array();
	// 	$file = fopen("$source","r");

	// 	$data_detail = array();
	// 	while(!feof($file))
	// 	{
	// 		$data = fgetcsv($file);
	// 		if ($flg_1)
	// 		{
	// 			$flg_1 = FALSE;
	// 			continue;
	// 		}
	// 		else
	// 		{
	// 			if (is_array($data))
	// 			{
	// 				$z = 0;
	// 				$flg_2 = TRUE;
	// 				$sum = array_sum($data);
	// 				foreach ($data as $key => $value)
	// 				{
	// 					if ($flg_2)
	// 					{
	// 						$flg_2 = FALSE;
	// 						continue;
	// 					}
	// 					else if (!$flg_2 && $z <= 6 && $type != 'bar_line')
	// 					{
	// 						$tmp = trim($value);
	// 						$data_detail[$z][] = round($tmp / $sum * 100, 2);
	// 						$z++;
	// 					}
	// 					else if (!$flg_2 && $type == 'bar_line')
	// 					{
	// 						$data_detail[] = $value;
	// 					}
	// 					else if ($z > 6 && $type != 'bar_line')
	// 					{
	// 						break;
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}

	// 	if ($type != 'bar_line')
	// 	{
	// 		$data_detail = array_reverse($data_detail);
	// 	}

	// 	return $data_detail;
	// }

	// public static function getOptionChartJs($type, $title, $title_x, $title_y)
	// {
	// 	$datasets += array(
	// 		'type' => 'line',
	// 		'data' => array(
	// 			'labels' => $label,
	// 		),
	// 	);

	// 	$i = 0;
	// 	foreach ($dataset_detail as $key => $value)
	// 	{
	// 		if ($i < 7)
	// 		{
	// 			$datasets['data']['datasets'][] = array(
	// 				'label' => $value,
	// 				'borderColor' => $colors[$i],
	// 				'backgroundColor' => $colors[$i],
	// 				'data' => $data_detail[$i],
	// 			);
	// 		}
	// 		else
	// 		{
	// 			break;
	// 		}
	// 		$i++;
	// 	}

	// 	$datasets += array(
	// 		'options' => array(
	// 			'responsive' => TRUE,
	// 			'title' => array(
	// 				'display' => TRUE,
	// 				'text' => $title
	// 			),
	// 			'tooltips' => array('mode' => 'index'),
	// 			'hover' => array('mode' => 'index'),
	// 			'scales' => array(
	// 				'xAxes' => array(
	// 					0 => array(
	// 						'stacked' => TRUE,
	// 						'scaleLabel' => array(
	// 							'display' => TRUE,
	// 							'labelString' => $title_x
	// 						),
	// 						'ticks' => array(
	// 							'min' => 0,
	// 							'max' => 1000
	// 						),
	// 					),
	// 				),
	// 				'yAxes' => array(
	// 					0 => array(
	// 						'id' => "y-axis-0",
	// 						'stacked' => TRUE,
	// 						'scaleLabel' => array(
	// 							'display' => TRUE,
	// 							'labelString' => $title_y
	// 						),
	// 						'ticks' => array(
	// 							'min' => 0,
	// 							'max' => 100
	// 						),
	// 					),
	// 				)
	// 			)
	// 		),
	// 	);

	// 	if ($type == 'bar_line')
	// 	{
	// 		$datasets['scales']['yAxes'][1] = array(
	// 			'id' => "y-axis-1",
	// 			'stacked' => TRUE,
	// 			'scaleLabel' => array(
	// 				'display' => TRUE,
	// 				'labelString' => 'risk'
	// 			),
	// 			'ticks' => array(
	// 				'min' => 0,
	// 				'max' => 100
	// 			),
	// 		);
	// 	}

	// 	return $datasets;
	// }

	// public static function getJsonChartJs($source, $condition_rank, $title, $title_x, $title_y, $type = 'crack', $mode = 'bar', $source_risk = '')
	// {
	// 	$info = Helper::getLabelChartJs($condition_rank, $type);
	// 	$dataset_detail = $info['dataset_detail'];
	// 	$labels = $info['labels'];
	// 	$data_detail = Helper::getDataChartJs($source, $type);

	// 	$i = 0;
	// 	$datasets = array();
	// 	$colors = Helper::randomColor(count($dataset_detail));

	// 	if ($mode == 'line' || $mode == 'bar')
	// 	{
	// 		$datasets += array(
	// 			'type' => $mode,
	// 		);
	// 	}

	// 	$datasets += array(
	// 		'data' => array(
	// 			'labels' => $labels,
	// 		),
	// 	);

	// 	$i = 0;
	// 	foreach ($dataset_detail as $key => $value)
	// 	{
	// 		if ($i < 7)
	// 		{
	// 			$datasets['data']['datasets'][] = array(
	// 				'label' => $value,
	// 				'borderColor' => $colors[$i],
	// 				'backgroundColor' => $colors[$i],
	// 				'yAxisID' => 'y-axis-0',
	// 				'data' => $data_detail[$i],
	// 			);
	// 		}
	// 		else
	// 		{
	// 			break;
	// 		}
	// 		$i++;
	// 	}

	// 	if ($mode == 'bar_line')
	// 	{
	// 		$data_bar_line = Helper::getDataChartJs($source_risk, $type);
	// 		$datasets['data']['datasets'][] = array(
	// 			'type' => 'line',
	// 			'label' => 'risk',
	// 			'borderColor' => 'rgb(255, 99, 132)',
 //                'borderWidth' => 2,
 //                'fill' => false,
 //                'yAxisID' => 'y-axis-1',
	// 			'data' => $data_bar_line,
	// 		);
	// 	}

	// 	$option_chart_js = Helper::getOptionChartJs($type, $title, $title_x, $title_y);
	// 	$datasets += $option_chart_js;

	// 	return $datasets;
	// }

	public static function randomColor($number)
	{
	    $colors = array();
	 	$jump = floor(300 / $number);
	    for ($i = 0; $i < 300; $i += $jump)
	    {
			$colors[] = 'hsl('.$i.', 100%, 60%)';
	    }
	    return $colors;
        // $colors = array();
        // while (true) {
        //     $color = substr(str_shuffle('ABCDEF0123456789'), 0, 6);
        //     $colors[] = '#' . $color;
        //     if (count($colors) == $number) 
        //     {
        //         // echo implode(PHP_EOL, $colors);
        //         break;
        //     }
        // }
        // return $colors;
	}

    public static function hslToHex($hsl)
    {
        $hsl = str_replace('hsl(', '', $hsl);
        $hsl = str_replace(')', '', $hsl);
        $hsl = explode(',', $hsl);
        $hsl[0] = (float) $hsl[0];
        $hsl[1] = (float) $hsl[1]/100;
        $hsl[2] = (float) $hsl[2]/100;

        list($h, $s, $l) = $hsl;

        if ($s == 0) {
            $r = $g = $b = 1;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;

            $r = self::hue2rgb($p, $q, $h + 1/3);
            $g = self::hue2rgb($p, $q, $h);
            $b = self::hue2rgb($p, $q, $h - 1/3);
        }

        return self::rgb2hex($r) . self::rgb2hex($g) . self::rgb2hex($b);
    }

    public static function hue2rgb($p, $q, $t)
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;

        return $p;
    }

    public static function rgb2hex($rgb)
    {
        return str_pad(dechex($rgb * 255), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Copy recursively from source to destination
     */
    static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ( $file = readdir($dir)))
        {
            if (( $file != '.' ) && ( $file != '..' ))
            {
                if (is_dir($src . '/' . $file))
                {
                    Helper::recurseCopy($src . '/' . $file,$dst . '/' . $file);
                }
                else
                {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    static function straightCopy($s1, $s2)
    {
        $path = pathinfo($s2);
        if (!file_exists($path['dirname']))
        {
            mkdir($path['dirname'], 0777, true);
        }
        if (!copy($s1, $s2))
        {
            throw new Exception("copy failed", 1);
        }
    }

    /**
     * Change mode recursively
     */
    static function chmodr($dir, $dirPermissions, $filePermissions)
    {
        $dp = opendir($dir);
            while($file = readdir($dp))
            {
                if (($file == ".") || ($file == ".."))
                continue;

                $fullPath = $dir."/".$file;

                if (is_dir($fullPath))
                {
                    chmod($fullPath, $dirPermissions);
                    \Helper::chmodr($fullPath, $dirPermissions, $filePermissions);
                } else {
                    chmod($fullPath, $filePermissions);
                }
        }
        closedir($dp);
    }

    static function loadExcel($id, $link, $ouput, $filename, $queue_flg = false)
    {
        $input = '../public/application/process/deterioration/'.$id.'/'.$link.'/'.$ouput.'/'.$filename;
        if ($queue_flg)
        {
            $input = 'public/application/process/deterioration/'.$id.'/'.$link.'/'.$ouput.'/'.$filename;
        }
        $load = Excel::load($input)->get();
        $hazard_parameter = [];
        $t_value = [];
        $cache = '';
        for ($i = 0; $i < count($load); $i++)
        {
            if (trim($load[$i]['unknown_parameter_beta']) != 't-value')
            {
                $hazard_parameter[] = $load[$i]['unknown_parameter_beta'];
            }
            else
            {
                $cache = $i;
                break;
            }
        }
        for ($i = $cache+1; $i < count($load); $i++)
        {
            $t_value[] = $load[$i]['unknown_parameter_beta'];
        }

        $data = [];
        $data[] = $hazard_parameter;
        $data[] = $t_value;
        return $data;
    }

    static function total($HazardRate,$case)
    {

        $G = array();
        $G[0] = 0;
        $G[1] = 1/$HazardRate[0][0] + $G[0];
        for($i= 1; $i < $case-1; $i++)
        {
            $G[$i+1] = 1/$HazardRate[0][$i] +  $G[$i];
        }

        return $G;

    }

    static function fai($id, $link, $ouput, $queue_flg = false)
    {
        $input = '../public/application/process/deterioration/'.$id.'/'.$link.'/'.$ouput.'/output0.csv';
        if ($queue_flg)
        {
            $input = 'public/application/process/deterioration/'.$id.'/'.$link.'/'.$ouput.'/output0.csv';
        }

        $load = Excel::load($input)->get();
        $hazard_parameter = [];
        $t_value = [];
        $cache = '';
        for ($i = 0; $i < count($load); $i++)
        {
            if (trim($load[$i]['hazard_parameters']) !='fai')
            {
                $hazard_parameter[] = $load[$i]['hazard_parameters'];
            }
            else
            {
                $cache = $i;
                break;
            }
        }

            $fai = $load[$cache+1]['hazard_parameters'];


        $data[] = $fai;
        return $data;
    }

    static public function subString($string , &$target = null, &$route_category = null, &$route = null )
    {
        $language_cd = (App::getLocale() == 'en') ? 'en' : 'vn';

        // if (strlen($string) == 17)
        // {
            if (substr($string, 11, 1) == 1)
            {
                $right_left = trans('deterioration.left');
            }
            else if (substr($string, 11, 1) == 2)
            {
                $right_left = trans('deterioration.right');
            }
            else if (substr($string, 11, 1) == 3)
            {
                $right_left = trans('deterioration.single');
            }
            else
            {
                $right_left = substr($string, 11, 1);
            }
        // }
        // else if (substr($string, 11, 1) == 1)
        // {
        //     $right_left = trans('deterioration.right');
        // }
        // else
        // {
        //     $right_left = trans('deterioration.single');
        // }

        if (isset($target) && isset($route_category) && isset($route))
        {
            if (isset($target[(int)substr($string, 0, 2)]["name_$language_cd"]))
            {
                $target_name = $target[(int)substr($string, 0, 2)]["name_$language_cd"];
            }
            else
            {
                $target_name = '';
            }
            if (isset($route_category[(int)substr($string, 2, 1)]['code_name']))
            {
                 $route_category_name =$route_category[(int)substr($string, 2, 1)]['code_name'] ;
            }
            else
            {
                $route_category_name = '';
            }
            if (isset($route[substr($string, 3, 3) .substr($string, 6, 3) . substr($string, 2, 1)]["name_$language_cd"]))
            {
                $route_name = $route[substr($string, 3, 3) . substr($string, 6, 3) .substr($string, 2, 1)]["name_$language_cd"];
            }
            else
            {
                $route_name = '';
            }

            return [$target_name, $route_category_name, $route_name, $right_left, (int)substr($string, 13, 4)];
        }
        else
        {
            $target = @tblOrganization::where('code_id', substr($string, 0, 2))
                ->first()
                ->{"name_$language_cd"};
            $route_category = @DB::table('mstRoad_category')
                ->where('code_id', substr($string, 2, 1))
                ->first()
                ->code_name;
            $route = @tblBranch::where('road_number', substr($string, 3, 3))
                ->where('road_number_supplement', substr($string, 6, 3))
                ->where('road_category', substr($string, 2, 1))
                ->where('branch_number', '00')
                ->first()
                ->{"name_$language_cd"};

            return [$target, $route_category, $route, $right_left, (int)substr($string, 13, 4)];
        }
    }

    static public function getRowDataChunk($file, $chunk_size, $callback, $num_rows = 10)
    {
        try
        {
            $handle = fopen($file, "r");
            $i = 0;
            while (!feof($handle) && $i < $num_rows)
            {
                call_user_func_array($callback, array(fgets($handle, $chunk_size), &$handle, $i));
                $i++;
            }

            fclose($handle);

        }
        catch(\Exception $e)
        {
            dd($e);
            trigger_error("file_get_contents_chunked::" . $e->getMessage(), E_USER_NOTICE);
            return false;
        }

        return true;
    }

    static public function strToFloat($val)
    {
        return floatval(preg_replace("[^-0-9\.]","", $val));
    }

	static function createMatrixDefaultFromConditionRank($session_id)
	{
		$condition_rank = tblBudgetSimulation::find($session_id)->repairMatrix->condition_rank;
		$data_default = Helper::convertJsonConditionRank($condition_rank);
		$i = count($data_default['crack']);
		$j = count($data_default['rut']);

		$matrix_as = $matrix_bst = $matrix_cc = array();

		for ($i = 0; $i < count($data_default['crack']); $i++)
		{
			$matrix_cc[$i] = 0;
			for ($j = 0; $j < count($data_default['rut']); $j++)
			{
				$matrix_as[$i][$j] = 0;
				$matrix_bst[$i][$j] = 0;
			}
		}

		return array(
			'matrix_as' => $matrix_as,
			'matrix_bst' => $matrix_bst,
			'matrix_cc' => $matrix_cc,
		);
	}

    /**
     * author :cuong.pt 09032017
     * function to trim array
     * @param $array: array 2 shifts
     */
    static public function trim($array)
    {
        if (is_array($array))
        {
            $input = array();
            foreach ($array as $key => $row)
            {
                foreach ($row as $subRow)
                {
                    $subRow = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $subRow);
                    $value = trim($subRow,'"');

                    $input[$key][] = $value;
                }
            }
        }
        return $input;
    }

    /**
     * @param:$x -> $year
     * @param:$array -> [[x1,y1],[x2,y2],...] with (...>x2>x1)
     *  function return $y ->($x,$y)
     */
    static public function  predictionYear($array, $x)
    {
        $i = 0;
        if (is_array($array))
        {
            foreach ($array as $key => $subArray)
            {
                if ($subArray[0] >= $x)
                {
                   $i = $key;
                   break;
                }
            }
        }
        if ($i != 0)
        {
            return $y = ((($array[$i][1] - $array[$i- 1][1]) * ($x - $array[$i- 1][0])) / ($array[$i][0] - $array[$i-1][0])) + $array[$i-1][1];
        }
        else
        {
            return 0;
        }
        return false;
    }

    static public function _getXAxisData($data, $condition_rank)
    {
        $crack_rank = count($condition_rank['crack']);
        $rut_rank = count($condition_rank['rut']);
        $iri_rank = count($condition_rank['iri']);
        $data_from = [];
        foreach ($data as $link => $offset)
        {
            if ($link == 'crack')
            {
                $start_index = 0;
                for ($i = 0; $i <= $offset-1 ; $i++)
                {
                    $data_from['crack'][] = $condition_rank['crack'][$i]['from'];
                }
            }
            else if ($link == 'rut')
            {
                $start_index =  $crack_rank + 1;
                for($i = 0; $i <= $offset-1 ; $i++)
                {
                    $data_from['rut'][] = $condition_rank['rut'][$i]['from'];
                }
            }
            else
            {
                $start_index = $crack_rank + $rut_rank + 2;
                for ($i = 0; $i <= $offset-1 ; $i++)
                {
                    $data_from['IRI'][] = $condition_rank['iri'][$i]['from'];
                }
            }
        }
        return $data_from;
    }

    /**
     * @param $data = [[a1,b1], [a2,b2], [a3,b3], [a4,b4]]
     * return array
     */
    static public  function predict($data, $y, $base_planning_gap = 0)
    {
        if (is_array($data))
        {
            $i = count($data) - 1;
            foreach ($data as $k => $v)
            {
                if ($k != 0 && floatval($v[0]) == 0)
                {
                    $i = $k - 1;
                    break;
                }
                else if (floatval($v[1]) >= floatval($y))
                {
                    $i = $k;
                    break;
                }
            }

            if ($i == 0)
            {
                $i = 1;
            }

            if (intval($data[$i][1]) - intval($data[$i-1][1]) != 0)
            {
                $tmp_value = floatval($data[$i][0]) - floatval($data[$i-1][0]);
                $tmp_value*= floatval($y);
                $tmp_value+= floatval($data[$i-1][0]) * floatval($data[$i][1]);
                $tmp_value-= floatval($data[$i-1][1]) * floatval($data[$i][0]);

                $division = floatval($data[$i][1]) - floatval($data[$i-1][1]);

                $tmp_value*= 1/$division;
                $x = round($tmp_value, 2);

                // $x = ((((int)$data[$i][0] - (int)$data[$i-1][0]) * ((int)$y - (int)$data[$i-1][1])) / ((int)$data[$i][1] - (int)$data[$i-1][1])) + (int)$data[$i-1][0];
            }
            else
            {
                $x = 0;
            }

            // add base planning year
            $x+= $base_planning_gap;

            $xn = [];
            for ($n = 0; $n <= 4; $n++)
            {
                $i = count($data) - 1;

                foreach ($data as $k => $v)
                {
                    if ($k != 0 && floatval($v[0]) == 0)
                    {
                        $i = $k - 1;
                        break;
                    }
                    else if (floatval($v[0]) >= ($x + $n))
                    {
                        $i = $k;
                        break;
                    }
                }
                $xn[] = [$i => ($x + $n)];
            }

            if (count($xn) > 0)
            {
                $yn = [];

                foreach ($xn as $key => $value)
                {
                    foreach ($value as $k =>$v)
                    {
                        if ($k == 0)
                        {
                            $yn[] = 0;
                        }
                        else if ((floatval($data[$k][0]) - floatval($data[$k-1][0])) == 0)
                        {
                            $yn[] = 0;
                        }
                        else
                        {
                            $tmp_value = floatval($data[$k][1]) - floatval($data[$k-1][1]);
                            $tmp_value*= floatval($v) - floatval($data[$k-1][0]);

                            $division = floatval($data[$k][0]) - floatval($data[$k-1][0]);
                            if ($division == 0)
                            {
                                $yn[] = -1;
                            }
                            else
                            {
                                $tmp_value*= 1/$division;
                                $tmp_value+= floatval($data[$k-1][1]);
                                $yn[] = round($tmp_value, 2);
                            }
                        }
                    }
                }
                return $yn;
            }
            // }
            // else
            // {
            //     return 0;
            // }
        }
        return FALSE;
    }

    static public function getRowColoum($session_id, $forecast_crack, $forecast_rut)
    {
        $wp = tblWorkPlanning::find($session_id);
        $condition_rank = \Helper::convertJsonConditionRank($wp->repairMatrix->condition_rank);
        foreach ($condition_rank as $key => $value)
        {
            if ($key == 'rut')
            {
                foreach ($value as $k => $v)
                {
                    if ($v['from'] <= $forecast_rut  && $forecast_rut <= $v['to'])
                    {
                        $coloum_rut = $k;
                        break;
                    }
                    else
                    {
                        $coloum_rut = 0;
                    }
                }
            }

            if ($key == 'crack')
            {
                foreach ($value as $k => $v)
                {
                    if ($v['from'] <= $forecast_crack && $forecast_crack <= $v['to'])
                    {
                        $coloum_crack = $k;
                        break;
                    }
                    else
                    {
                        $coloum_crack = 0;
                    }
                }
            }
        }
        return ['coloum' => $coloum_rut, 'row' => $coloum_crack];
    }

    static function readCsvToArray($file)
    {
        $data = [];
        $csv_rows = file($file);
        foreach ($csv_rows as $row)
        {
            $tmp = [];
            $tmp_data = explode(',', $row);
            if (count($tmp_data) > 1)
            {
                array_pop($tmp_data);
            }
            foreach ($tmp_data as $value)
            {
                $tmp[] = trim($value);
            }
            $data[] = $tmp;
        }
        return $data;
    }
    /**
     * $crack,$rut, $iri : forecast year
     */
    static function getMCI($crack, $rut, $iri, $pavment_type)
    {
        if ($pavment_type == 'CC')
        {
            $crack = $crack/ 3.33;
        }
        $sv = ($iri - 0.75)/1.47 ;
        $MCI0 = 10 - 1.48*pow($crack, 0.3) - 0.29*pow($rut, 0.7) - 0.47*pow($sv, 0.2);
        $MCI1 = 10 - 1.51*pow($crack, 0.3) - 0.30*pow($rut, 0.7);
        $MCI2 = 10 - 2.23*pow($crack, 0.3);
        $MCI3 = 10 - 0.54*pow($rut, 0.7);

        // echo 'crack: ', $crack , '<br>';
        // echo 'rut: ', $rut , '<br>';
        // echo 'iri: ', $iri , '<br>';
        // echo 'MCI0: ', $MCI0 , '<br>';
        // echo 'MCI1: ', $MCI1 , '<br>';
        // echo 'MCI2: ', $MCI2 , '<br>';
        // echo 'MCI3: ', $MCI3 , '<br>';
        // echo 'min: ', MIN($MCI0, $MCI1, $MCI2, $MCI3), '<br>';die;
        $min = MIN($MCI0, $MCI1, $MCI2, $MCI3);
        $mci = round($min, 2);
        return ($mci < 0) ? 0 : $mci;
    }

    static function getNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0)
        {
            return Helper::getNameFromNumber($num2 - 1) . $letter;
        }
        else
        {
            return $letter;
        }
    }

    static function generateMatrixCell($rc, $rclass, $pt, $cindex, $rindex, $zones, $matrix, $saved_zone = null, $history_flg = false, $status = 0)
    {
        if (!isset($matrix[$rc->code_id][$rclass->code_id][$pt->code_id][$cindex][$rindex]))
        {
            return '';
        }
        if ($status == 1)
        {
            $disabled = "disabled";
        }
        $html = '';
        $html.= '<div class="btn-group full-width">';
        $html.= '<button class="btn dropdown-toggle btn-xs full-width" data-color="method-' . $matrix[$rc->code_id][$rclass->code_id][$pt->code_id][$cindex][$rindex] . '" data-toggle="dropdown" aria-expanded="true" '. @$disabled .'>';
        $html.= '<span class="caret"></span>';
        $html.= '</button>';
        if (!$history_flg)
        {
            $html.= '<ul class="dropdown-menu pull-right js-status-update list-select-method">';
            $html.= '<li>';
            $html.= '<a href="javascript:void(0);" onclick="selectItem(this, 0, ' . $rc->code_id . ', ' . $rclass->code_id . ', ' . $pt->code_id . ', ' . $cindex . ', ' . $rindex . ');"><i class="fa fa-circle txt-color-white"></i> '. trans('back_end.no_apply') . '</a>';
            $html.= '</li>';
            foreach ($zones as $z)
            {
                if ($z['pavement_type'] == $pt->code_id)
                {
                    if (isset($saved_zone))
                    {
                        if (isset($saved_zone[$rc->code_id][$rclass->code_id][$pt->code_id][$cindex][$rindex]))
                        {
                            if ($z['zone_id'] != $saved_zone[$rc->code_id][$rclass->code_id][$pt->code_id][$cindex][$rindex])
                            {
                                continue;
                            }
                        }
                        else
                        {
                            continue;
                        }

                    }
                    $html.= '<li>';
                    $html.= '<a href="javascript:void(0);" onclick="selectItem(this, ' . $z['id'] . ', ' . $rc->code_id . ', ' . $rclass->code_id . ', ' . $pt->code_id . ', ' . $cindex . ', ' . $rindex . ');"><i class="fa fa-circle" style="color: ' . $z['color'] . '"></i> '. $z['name'] . '</a>';
                    $html.= '</li>';

                }
            }
            $html.= '</ul>';
        }
        $html.= '</div>';
        return $html;
    }

    static function writeCSV($data, $file)
    {
        $out = fopen($file, 'w+');
        foreach($data as $row)
        {
            fputcsv($out, $row);
        }
        fclose($out);
    }

    static function vlookup($lookupValue, $array, $equal = false)
    {
        $result = null;

        foreach ($array as $key => $value)
        {
            if ($equal)
            {
                if ($lookupValue == floatval($key))
                {
                    $result = $value;
                }
            }
            else
            {
                if ($lookupValue >= floatval($key))
                {
                    $result = $value;
                }
            }
        }
        return $result;
    }

    static function getMonthDiff($d1, $d2)
    {
        $d1 = strtotime($d1);
        $d2 = strtotime($d2);
        $min_date = min($d1, $d2);
        $max_date = max($d1, $d2);
        $i = 0;

        while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date)
        {
            $i++;
        }
        return $i;
    }
    static function parseSuperInput($str)
    {
        $operator1 = substr($str, 0, 2);
        $operator2 = substr($str, 0, 1);
        if ($operator1 == '>=' || $operator1 == '<=')
        {
            $data = substr($str, 2);
            return [$operator1, $data];
        }
        if ($operator2 == '>' || $operator2 == '<')
        {
            $data = substr($str, 1);
            return [$operator2, $data];
        }
        return ['=', $str];
    }

    static function getListDirection()
    {
        return [
            [
                'name' => trans('back_end.left_direction'),
                'value' => 1
            ],
            [
                'name' => trans('back_end.right_direction'),
                'value' => 2
            ],
            [
                'name' => trans('back_end.single_direction'),
                'value' => 3
            ]
        ];
    }

    static function writeDataToCSV($source, $data)
    {
        $dirname = dirname($source);
        if (!is_dir($dirname))
        {
            mkdir($dirname, 0755, true);
        }
        $open = fopen($source, 'w+');
        foreach ($data as $row)
        {
            fputcsv($open, $row);
        }
        fclose($open);
    }

    static function round20($x)
    {
        $round_number = ceil(floatval($x));
        // if ($round_number % 4 != 0)
        // {
            // $round_number+= 5;
        // }
        return intval($round_number);
    }

    static function preProcessing ($path, $query = array(), $start_row, $type = 0)
    {
    	//$type = 0 -> import inputting,
    	//$type = 1 -> import planned_section
        $array = [];
        $src = public_path('../storage/app/'.$path);
        $workbook = SpreadsheetParser::open($src);
        $myWorksheetIndex = $workbook->getWorksheetIndex(0);
        
        foreach ($workbook->createRowIterator($myWorksheetIndex) as $rowIndex => $values)
        {
            if ($rowIndex < $start_row+1) continue;
            $object = [];
            if ($type == 0)
            {
            	array_splice($values, 0,1);	
            }
            foreach ($query as $model)
            {
                foreach ($model as $m_key => $value)
                {
                    isset($values[$value['index']]) ? $valueRow = $values[$value['index']] : $valueRow = '';
                    $object[$m_key] = $valueRow ;
                    if (isset($object['survey_time']))
                    {
                        if ($object['survey_time'] != '') {
                        	if (is_string($object['survey_time'])) {
                        		$object['survey_time'] = str_replace("/", "-", $object['survey_time']);
                        	}
                        	elseif(!is_int($object['survey_time'])){
                        		$object['survey_time'] = date_format($object['survey_time'],"Y-m-d");	
                        	}
                        }
                    }
                    if (isset($object['completion_date']))
                    {
                        if ($object['completion_date'] != '') {
                        	if (is_string($object['completion_date'])) {
                        		$object['completion_date'] = str_replace("/", "-", $object['completion_date']);
                        	}
                        	elseif(!is_int($object['completion_date'])){
                        		$object['completion_date'] = date_format($object['completion_date'],"Y-m-d");
                        	}
                        }
                    }
                    if (isset($object['kilometpost']))
                    {
                        if ($object['kilometpost'] != '') $object['kilometpost'] = str_replace("/", "-", $object['kilometpost']);
                    }
                }
            }
            $array[] = $object;
        }
        return $array;
    }
    
    static function validate($record, $query = [], $dataQuery = NULL, $models, $data_success = NULL, $id = NULL, $data_pavement = null)
    {
       	$time = Carbon::now()->toDateString();
        $now = str_replace('-', '/', $time);
        $year = substr($now, 0,4);
        $month = substr($now, 5,2);
        $config = $query;
        $validate = [];
        $check_csv = true;
        $check_segment = false;

        foreach ($config as $model1)
        {
            foreach ($model1 as $key1 => $value1)
            {
                if (isset($record[$key1])) $validate[$key1] = $value1['validate'];
            }
        }

        $messages = [
            'regex' => trans('validator.inputdata_TV.invalid'),
        ];
        $validator = Validator::make($record, $validate, $messages);
        $record['err'] = $validator->errors();

        if ($models != '\App\Models\tblSectiondataTV')
        {
            if (isset($record['segment_id']) && $record['segment_id'] != null)
            {
                $check_segment = true;
            }
            else
            {
                $validator->errors()->add('segment_id', trans('validator.segment_id_errors'));
                $record['err'] = $validator->errors();
            }
        }

        if (isset($record['construct_year_y']))
        {
            if ($record['construct_year_y'] == '' || $record['construct_year_y'] > $year || $record['construct_year_m'] == '')
            {
                $validator->errors()->add('construct_year', trans('validator.construct_year_errors'));
                $record['err'] = $validator->errors();
            }
            if ($record['construct_year_y'] == $year)
            {
                if ($record['construct_year_m'] > $month)
                {
                    $validator->errors()->add('construct_year', trans('validator.service_start_year_errors'));
                    $record['err'] = $validator->errors();
                }
            }
        }
        if (isset($record['actual_length']) &&  empty($record['actual_length']))
        {
            $record['actual_length'] = 1000*$record['km_to'] + $record['m_to'] - 1000*$record['km_from'] - $record['m_from'];
        }

        if (isset($record['service_start_year_y']))
        {
            if ($record['service_start_year_y'] > $year)
            {
                $validator->errors()->add('service_start_year', trans('validator.service_start_year_errors'));
                $record['err'] = $validator->errors();
            }
            if ($record['service_start_year_y'] == $year)
            {
                if ($record['service_start_year_m'] > $month)
                {
                    $validator->errors()->add('service_start_year', trans('validator.service_start_year_errors'));
                    $record['err'] = $validator->errors();
                }
            }
        }

        $listError = [];
        $dataRelation = [];
        foreach ($config as $model)
        {
            foreach ($model as $key => $value)
            {
                if ($value['type'] == 'checkdata')
                {
                    $checked = false;
                    if ($record[$key] != null)
                    {
                    	if ((int)$record[$key]) {
                    		if (isset($data_pavement[$record[$key]])) 
                    		{
                    			$record[$key] = $data_pavement[$record[$key]];
                    		}
                    		else
                    		{
                    			$checked = true;
                    		}
                    	}
                        else
                        {
                        	$checkdata = mb_strtolower($record[$key]);
	                        $checkdata = trim($record[$key]);
                        	if (isset($data_pavement[$checkdata])) {
                        		$record[$key] = $data_pavement[$checkdata];
                        	}
                        	else
                        	{
                        		$checked = true;
                        	}
                        }
                        if ($checked)
                        {
                            $validator->errors()->add($key, $record[$key].' '.trans('validator.not_exist'));
                            $record['err'] = $validator->errors();
                        }
                    }
                }
                else if ($value['type'] == 'check_select')
                {
                    $checked = false;
                    if ($record[$key] != null)
                    {
                        $checkbox_value = mb_strtolower($record[$key]);
                        $checkbox_value = trim($checkbox_value);
                        if ($checkbox_value == "trái" || $checkbox_value == "left" || $checkbox_value == 1)
                        {
                            $record[$key] = '1';
                        }
                        else if ($checkbox_value == "phải" || $checkbox_value == "right" || $checkbox_value == 2)
                        {
                            $record[$key] = '2';
                        }
                        else if ($checkbox_value == "làn đơn chung" || $checkbox_value == "single" || $checkbox_value == 3)
                        {
                            $record[$key] = '3';
                        }
                        else
                        {
                            $checked = true;
                        }
                        if ($checked)
                        {
                            $validator->errors()->add($key, $record[$key].' '.trans('validator.import_master_not_exist'));
                            $record['err'] = $validator->errors();
                        }
                        else
                        {
                            if (isset($record['direction']))
                            {
                            	if ($models == '\App\Models\tblSectiondataMH') {
                            		if (isset($record['lane_pos_number'])) {
	                            		if ($record['direction'] == 3) {
	                                        if ($record['lane_pos_number'] > 1) {
	                                            $validator->errors()->add('lane_pos_number', trans('validator.import_lane_invalid'));
	                                            $record['err'] = $validator->errors();
	                                        }
	                                    }
	                            	}
                            	}
                                if (isset($record['no_lane']) && isset($record['lane_pos_number']))
                                {
                                    if ($record['no_lane'] == null) $validator->errors()->add('no_lane', trans('validator.nolane_required'));
                                    $record['err'] = $validator->errors();
                                    // if (($record['lane_pos_number'])) 
                                    // {
                                    // 	$validator->errors()->add('lane_pos_number', trans('validator.lane_pos_number_required'));
                                    // }
                                    $record['err'] = $validator->errors();

                                    if(!$validator->errors()->has('no_lane') && !$validator->errors()->has('lane_pos_number')) {
                                        if ($record['no_lane'] < $record['lane_pos_number']) {
                                            $validator->errors()->add('no_lane', trans('validator.import_lane_invalid'));
                                            $record['err'] = $validator->errors();
                                        }
                                        if ($record['direction'] == 3) {
                                            if ($record['no_lane'] > 1) {
                                                $validator->errors()->add('no_lane', trans('validator.import_lane_invalid'));
                                                $record['err'] = $validator->errors();
                                            }
                                            if ($record['lane_pos_number'] > 1) {
                                                $validator->errors()->add('lane_pos_number', trans('validator.import_lane_invalid'));
                                                $record['err'] = $validator->errors();
                                            }
                                        }
                                        if ($record['direction'] == 2 || $record['direction'] == 1) {
                                            if ($record['no_lane'] <= 1) {
                                                $validator->errors()->add('no_lane', trans('validator.import_lane_invalid'));
                                                $record['err'] = $validator->errors();
                                            }
                                            if ($record['lane_pos_number'] == 0) {
                                                $validator->errors()->add('lane_pos_number', trans('validator.import_lane_invalid'));
                                                $record['err'] = $validator->errors();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else if ($value['type'] == 'radio_check')
                {
                    $checked = false;
                    if ($record[$key])
                    {
                        $checkbox_value = mb_strtolower($record[$key]);
                        $checkbox_value = trim($checkbox_value);
                        if ($checkbox_value == "trái" || $checkbox_value == "left" || $checkbox_value == 1)
                        {
                            $record[$key] = '0';
                        }
                        else if ($checkbox_value == "phải" || $checkbox_value == "right" || $checkbox_value == 2)
                        {
                            $record[$key] = '1';
                        }
                        else
                        {
                            $checked = true;
                        }
                        if ($checked)
                        {
                            $validator->errors()->add($key, $record[$key].' '.trans('validator.radio_not_exist'));
                            $record['err'] = $validator->errors();
                        }
                    }
                }
                else if ($value['type'] == 'select')
                {
                    $found = false;
                    $valueKey = isset($record[$key]) ? trim($record[$key]) : '';

                    if (!empty($value['modelCheck']))
                    {

                        foreach ($value['modelCheck'] as $master_item)
                        {
                        	
                            if ($master_item['value'] != '' && isset($record[$key]) && $valueKey != '')
                            {
                                $master_name = mb_strtolower($master_item['name']);
                                $record_lower = mb_strtolower($valueKey);
                                // if (strpos($master_name, $record_lower) !== false || $master_item['value'] == $valueKey)
                                // if ($master_item['value'] == $valueKey)
                                if ($master_name == $record_lower || $master_item['value'] == $valueKey)
                                // if ($master_item['value'] == $valueKey)
                                // if ($master_name == $record_lower || $master_item['value'] == $valueKey)
                                {
                                	// if (isset($value['item_model']))
                                 //    {
                                 //        if (isset($value['special_name']))
                                 //        {
                                 //            App::getLocale() == 'en' ? $record[$key] = $value['item_model']::where('id', $master_item['value'])->first()->segname_en
                                 //                : $record[$key] = $value['item_model']::where('id', $master_item['value'])->first()->segname_vn;
                                 //        }
                                 //        else
                                 //        {
                                 //            App::getLocale() == 'en' ? $record[$key] = $value['item_model']::where('id', $master_item['value'])->first()->name_en
                                 //                : $record[$key] = $value['item_model']::where('id', $master_item['value'])->first()->name_vn;
                                 //        }
                                 //    }
                                    $dataRelation[$key] = $master_item['value'];
                                    if (isset($value['relation']))
                                    {
                                    	$found = true;
                                        $modelParent = $value['relation']['model'];
                                        $funcCheck = $value['relation']['func'];
                                        $parentRelation = $value['relation']['parent'];
                                        $valueParent = [];
                                        $keySave = '';
                                        if (is_array($parentRelation))
                                        {
                                            foreach ($parentRelation as $keyParent)
                                            {
                                                if (in_array($keyParent, $listError))
                                                {
                                                    break 2;
                                                }
                                                $valueParent[] = $dataRelation[$keyParent];
                                                $keySave = $keySave.'/'.$dataRelation[$keyParent];
                                            }
                                        }
                                        else
                                        {
                                            if (!$validator->errors()->has($parentRelation))
                                            {
                                                $valueParent = @$dataRelation[$parentRelation];
                                                $keySave = @$valueParent;
                                            }
                                        }

                                        if (!empty($valueParent))
                                        {
                                            if (!empty($dataQuery) && isset($dataQuery[$key][$keySave]))
                                            {
                                                $childData = $dataQuery[$key][$keySave];
                                            }
                                            else
                                            {
                                                $childData = $modelParent::$funcCheck($valueParent);
                                                $dataQuery[$key][$keySave] = @$childData;
                                            }

                                            if (!empty($childData))
                                            {
                                            	if (in_array($master_item['value'], $childData))
                                                {
                                                    $found = true;
                                                }
                                            }
                                        }

                                        if ($found)
                                        {
                                            if (isset($dataRelation['sb']) && isset($dataRelation['road']))
                                            {
                                                $segment_result = App\Models\tblSegment::where('SB_id', $dataRelation['sb'])
                                                    ->where('branch_id', $dataRelation['road']);
                                                if ($models != '\App\Models\tblSectiondataTV') 
                                                {
                                                	// add condition to find out correct segment
                                                	$segment_result = $segment_result->whereRaw('10000 * km_from + m_from <= ?', [10000 * $record['km_from'] + $record['m_from']])
                                                		->whereRaw('10000 * km_to + m_to >= ?', [10000 * $record['km_to'] + $record['m_to']]);
                                                }
                                                else
                                                {
                                                	$segment_result = $segment_result->whereRaw('10000 * km_from + m_from <= ?', [10000 * $record['km_station'] + $record['m_station']])
                                                		->whereRaw('10000 * km_to + m_to >= ?', [10000 * $record['km_station'] + $record['m_station']]);
                                                }
                                                $segment_array = $segment_result->first();

                                                if ($segment_array)
                                            	{
                                            		$record['segment_id'] = $segment_array->id;
                                            		$check_segment = true;
                                            	}
                                            	else
                                            	{
                                            		// $validator->errors()->add('km_station', trans('validator.chainage_neutral'));
                                              //   	$record['err'] = $validator->errors();
                                              //   	$record['segment_id'] = '';
                                            		if ($models == '\App\Models\tblSectiondataTV')
	                                                {
		                                                $validator->errors()->add('km_station', trans('validator.chainage_neutral'));
	                                                	$record['err'] = $validator->errors();
	                                                	// $record['segment_id'] = '';
	                                                }
	                                                else
	                                                {
		                                                $validator->errors()->add('km_from', trans('validator.chainage_neutral'));
	                                                	$record['err'] = $validator->errors();
	                                                	// $record['segment_id'] = '';
	                                                }
                                            	}


                                                // if ($models == '\App\Models\tblSectiondataTV' && $record['segment_id'] == '')
                                                // {
                                                //     if (isset($record['km_station']) && isset($record['m_station']))
                                                //     {
                                                //         $station = $record['km_station'] * 1000000 + $record['m_station'];
                                                //         $segment_result = $segment_result->get();
                                                //         $id_success = null;
                                                //         foreach ($segment_result as $result)
                                                //         {
                                                //             $result_from = $result->km_from * 1000000 + $result->m_from;
                                                //             $result_to = $result->km_to * 1000000 + $result->to;
                                                //             $result_id = $result->id;
                                                //             if($station >= $result_from && $station <= $result_to)
                                                //             {
                                                //                 $record['segment_id'] = (string)$result_id;
                                                //                 $check_segment = true;
                                                //             }
                                                //             elseif ($record['segment_id'] == '')
                                                //             {
                                                //                 $record['segment_id'] = (string)$segment_result[0]->id;
                                                //                 $validator->errors()->add('km_station', 'validator.km_station_not_exist');
                                                //                 $record['err'] = $validator->errors();
                                                //             }
                                                //         }
                                                //     }
                                                //     if (!isset($record['km_station'])) $validator->errors()->add('km_station', 'validator.km_station_invalid');
                                                //     $record['err'] = $validator->errors();
                                                //     if (!isset($record['m_station'])) $validator->errors()->add('m_station', 'validator.m_station_invalid');
                                                //     $record['err'] = $validator->errors();
                                                // }
                                                // else
                                                // {
                                                    
                                                //     if ($models == '\App\Models\tblSectiondataTV')
                                                //     {
                                                //     	$check_segment = true;
                                                //         if (isset($record['km_station']) && isset($record['m_station']))
                                                //         {
                                                //             $station = $record['km_station'] * 1000000 + $record['m_station'];
                                                //             if (!in_array($record['segment_id'], $segment_array))
                                                //             {
                                                //                 $validator->errors()->add('segment_id', 'validator.segment_not_exist');
                                                //                 $record['err'] = $validator->errors();
                                                //             }
                                                //             else
                                                //             {
                                                //                 $segment_station = App\Models\tblSegment::find($record['segment_id']);
                                                //                 $result_from = $segment_station->km_from * 1000000 + $segment_station->m_from;
                                                //                 $result_to = $segment_station->km_to * 1000000 + $segment_station->to;
                                                //                 if ($station < $result_from || $station > $result_to)
                                                //                 {
                                                //                     $validator->errors()->add('km_station', trans('validator.km_station_not_exist'));
                                                //                     $record['err'] = $validator->errors();
                                                //                 }
                                                //             }
                                                //         }
                                                //         if (!isset($record['km_station'])) $validator->errors()->add('km_station', 'validator.km_station_invalid');
                                                //         $record['err'] = $validator->errors();
                                                //         if (!isset($record['m_station'])) $validator->errors()->add('m_station', 'validator.m_station_invalid');
                                                //         $record['err'] = $validator->errors();
                                                //     }
                                                //     else
                                                //     {
                                                //         // if ($check_segment)
                                                //         // {
                                                //         //     $segment_id = Helper::getMasterIDByKey('segment_id', $record['segment_id'], $model);
                                                //         // }
                                                //         // else
                                                //         // {
                                                //         //     $segment_id = null;
                                                //         // }

                                                //         // if (!in_array($segment_id, $segment_array))
                                                //         // {
                                                        	
                                                //         // }
                                                //         // else
                                                //         // {
                                                //         //     $record['segment_id'] = $segment_id;
                                                //         // }
                                                //     }
                                                // }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $found = true;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    if (isset($record[$key]) && $valueKey != '')
                    {
                        if (!$found && $value['validate'] == 'required')
                        {
                            $listError[] = $key;
                            $validator->errors()->add($key, trans('validator.import_master_not_exist'));
                            $record['err'] = $validator->errors();
                        }
                    }
                }
            }
            $record['err'] = $validator->errors();
            $record['query'] = $dataQuery;
        }

        if ($check_segment)
        {
            $flag = Helper::validationOverlapping($record, $config, $record['section_id'], $models, $validator);

        //     if (isset($data_success) && !empty($data_success))
        //     {
        //         foreach ($data_success as $item_success)
        //         {
        //             $check_csv = Helper::checkOverlapCsv($record, $item_success);
        //             if (!$check_csv)
        //             {
        //                 break;
        //             }
        //         }
        //     }
            if (!$flag)
            {
                if ($models == '\App\Models\tblSectiondataMH')
                {
                    if (!$validator->errors()->has('km_from'))
                    {
                        $validator->errors()->add('km_from', trans('validator.asset_exists'));
                        $record['err'] = $validator->errors();
                    }
                    $record['overlap'] = $validator->errors();
                }
                else if($models == '\App\Models\tblSectiondataTV')
                {
                    $validator->errors()->add('km_station', trans('validator.asset_exists'));
                    $record['err'] = $validator->errors();
                    $record['overlap'] = $validator->errors();
                }
                else
                {
                    if (!$validator->errors()->has('km_from'))
                    {
                        $validator->errors()->add('km_from', trans('validator.asset_exists'));
                        $record['err'] = $validator->errors();
                    }
                    $record['overlap'] = $validator->errors();
                }
            }
        }
        return $record;
    }

    static function validationOverlapping($record, $config = [], $id = NULL, $model, $validator = NULL)
    {
        $flag = true;
        $arr = [];
        $sub_record = [];
        foreach ($config as $item)
        {
            $arr = array_merge($arr, $item);
        }
        // Convert string into ID of table in DB
        foreach ($record as $key => $v)
        {
        	if ($key != 'segment_id')
        	{
        		// segment ID will be handled in different way
        		$value = Helper::getID($key, $v, $arr,$sub_record);
        	}
        	if (!$value) 
            {
                $value = $v;
            }
            $sub_record[$key] = $value;
        }
        // dd($sub_record);
        if ($sub_record['segment_id'] != '')
        {
            $check_data = null;
            if ($validator == NULL)
            {
                $check_data = 1;
            }
            else
            {
                if (!$validator->errors()->has('segment_id'))
                {
                    $check_data = 1;
                }
            }
            if ($check_data == 1)
            {
                // $segment_check = App\Models\tblSegment::find(@$sub_record['segment_id']);
                // $segment_m_from_convert = @$segment_check->km_from * 1000000 + @$segment_check->m_from;
                // $segment_m_to_convert = @$segment_check->km_to * 1000000 + @$segment_check->m_to;

                if ($model == '\App\Models\tblSectiondataTV')
                {
                    if (isset($record['km_station']) && isset($record['m_station']))
                    {
                        // OVERLAPPING CHECKING for COUNTING STATION
                        // $sub_record_station = $sub_record['km_station'] * 1000000 + $sub_record['m_station'];
                        // if ($sub_record_station < $segment_m_from_convert || $sub_record_station > $segment_m_to_convert) {
                        //     $flag = false;
                        // } else {
                            $tv_check = $model::where('id', '!=', $id)
                                ->where('segment_id', $record['segment_id'])
                                ->where('km_station', $sub_record['km_station'])
                                ->where('m_station', $sub_record['m_station'])
                                ->whereRaw("YEAR(survey_time) = '" . substr($sub_record['survey_time'], 0, 4) . "'")
                                ->count();
                            if ($tv_check > 0) 
                            {
                                $flag = false;
                            }
                        // }
                    }
                    if (!isset($record['km_station']))
                    {
                        $validator->errors()->add('km_station', 'validator.km_station_invalid');
                    }
                    $record['err'] = $validator->errors();
                    if (!isset($record['m_station'])) 
                    {
                        $validator->errors()->add('m_station', 'validator.m_station_invalid');
                    }
                    $record['err'] = $validator->errors();
                }
                else if (isset($sub_record['km_from']) && isset($sub_record['m_to']))
                {
                    $m_from_convert = $sub_record['km_from'] * 1000000 + $sub_record['m_from'];
                    $m_to_convert = $sub_record['km_to'] * 1000000 + $sub_record['m_to'];
                    if ($m_from_convert >= $m_to_convert)
                    {
                        $flag = false;
                        $validator->errors()->add('km_from', trans('validator.chainage_neutral'));
                        $record['err'] = $validator->errors();
                    }
                    // else if ($m_to_convert < $segment_m_from_convert || $m_from_convert > $segment_m_to_convert || $m_to_convert > $segment_m_to_convert)
                    // {
                    //     $flag = false;
                    //     $validator->errors()->add('km_from', trans('validator.out_segment'));
                    //     $record['err'] = $validator->errors();
                    // }
                    // else 
                    else if ($model == '\App\Models\tblSectiondataMH')
                    {
                        $distance_start_db = 0;
                        $distance_end_db = 0;
                        if ($sub_record['direction_running'] == "0")
                        {
                            $distance_start_db = " round(-(distance) - ((total_width_repair_lane)/2), 2)";
                            $distance_end_db = "round(-(distance) + ((total_width_repair_lane)/2), 2)";
                        }
                        elseif ($sub_record['direction_running'] == "1") 
                        {
                            $distance_start_db = " round((distance) - ((total_width_repair_lane)/2), 2)";
                            $distance_end_db = "round((distance) + ((total_width_repair_lane)/2), 2)";
                        }

                        $distance_start = 0;
                        $distance_end = 0;
                        if ($sub_record['direction_running'] == '0')
                        {
                            $distance_start -= ((int)$sub_record['distance'] + (int)$sub_record['total_width_repair_lane']/2);
                            $distance_end -= ((int)$sub_record['distance'] - (int)$sub_record['total_width_repair_lane']/2);
                        }
                        else
                        {
                            $distance_start += ((int)$sub_record['distance'] - (int)$sub_record['total_width_repair_lane']/2);
                            $distance_end += ((int)$sub_record['distance'] + (int)$sub_record['total_width_repair_lane']/2);
                        }

                        if (is_numeric($sub_record['repair_duration']))
                        {
                        	$mh_check = $model::where('id', '!=', $id)
	                            ->where('segment_id', $record['segment_id'])
	                            ->where('direction', $sub_record['direction'])
	                            ->where('lane_pos_number', $sub_record['lane_pos_number'])
	                            ->whereRaw("$m_from_convert < (km_to*1000000+m_to) AND (km_from*1000000+m_from) < $m_to_convert")
	                            ->whereRaw("(
									(date_format(DATE_SUB(completion_date, INTERVAL repair_duration MONTH), '%Y/%m/%d') BETWEEN date_format(DATE_SUB('" . $sub_record['completion_date'] . "', INTERVAL " . intval($sub_record['repair_duration']) . " MONTH), '%Y/%m/%d') AND '" . $sub_record['completion_date'] . "')
									OR (date_format(completion_date, '%Y/%m/%d') BETWEEN date_format(DATE_SUB('" . $sub_record['completion_date'] . "', INTERVAL " . intval($sub_record['repair_duration']) . " MONTH), '%Y/%m/%d') AND '" . $sub_record['completion_date'] . "')
									OR (date_format(DATE_SUB('" . $sub_record['completion_date'] . "', INTERVAL " . intval($sub_record['repair_duration']) . " MONTH), '%Y/%m/%d') BETWEEN date_format(DATE_SUB(completion_date, INTERVAL repair_duration MONTH), '%Y/%m/%d') AND date_format(completion_date, '%Y/%m/%d'))
								)")
								->whereRaw("(
									( {$distance_start_db} BETWEEN $distance_start AND $distance_end )
									OR ( {$distance_end_db} BETWEEN $distance_start AND $distance_end )
									OR ( $distance_start BETWEEN {$distance_start_db} AND {$distance_end_db} ) 
	                            )")
	                            ->count();
                            if ($mh_check > 0) 
                            {
                                $flag = false;
                            }
                        }
                        else
                    	{
                            $flag = true;
                        }
                    }
                    else if ($model == '\App\Models\tblSectiondataRMD')
                    {
                        $ri_check = $model::where('id', '!=', $id)
                            ->where('segment_id', $record['segment_id'])
                            ->where('direction', $sub_record['direction'])
                            ->where('lane_pos_number', $sub_record['lane_pos_number'])
                            ->whereRaw("$m_from_convert < (km_to*1000000+m_to) AND (km_from*1000000+m_from) < $m_to_convert")
                            ->whereRaw("YEAR(survey_time) = '" . substr($sub_record['survey_time'], 0, 4) . "'")
                            ->count();
                            if ($ri_check > 0) 
                            {
                                $flag = false;
                            }
                    }
                }
            }
        }
        if (isset($record['excel_overlap']) && ($record['excel_overlap'] == true))
        {
            $flag = false;
        }

        return $flag;
    }

    // static function dataTransform($data)
    // {
    //     $test_success = [];
    //     $test_error = [];
    //     $arr = [];
    //     foreach ($data as $item_data)
    //     {
    //         $flag_overlap = true;
    //         if(empty($test_success))
    //         {
    //             $test_success[] = $item_data;
    //         }
    //         else
    //         {
    //             foreach ($test_success as $row)
    //             {
    //                 $flag_overlap = Helper::checkOverlapCsv($item_data, $row);
    //                 if (!$flag_overlap)
    //                 {
    //                     break;
    //                 }
    //             }
    //             if ($flag_overlap)
    //             {
    //                 $test_success[] = $item_data;
    //             }
    //             else
    //             {
    //                 $item_data['excel_overlap'] = true;
    //                 $test_error[] = $item_data;
    //             }
    //         }
    //     }
    //     $arr = array_merge($arr, $test_success, $test_error);
    //     return $arr;
    // }

    // static function checkOverlapCsv($record, $check_item)
    // {
    //     $check_csv = true;
    //     $chainage = false;
    //     if (isset($record['m_from']) && $record['m_from'] != null)
    //     {
    //         if (isset($record['km_to']) && $record['km_to'] != null)
    //         {
    //             $item_m_from_convert = $check_item['km_from'] * 1000000 + $check_item['m_from'];
    //             $item_m_to_convert = $check_item['km_to'] * 1000000 + $check_item['m_to'];
    //             $chainage = true;
    //         }

    //         if ($chainage)
    //         {
    //             $m_from_convert = $record['km_from'] * 1000000 + $record['m_from'];
    //             $m_to_convert = $record['km_to'] * 1000000 + $record['m_to'];
    //             if ($m_from_convert >= $m_to_convert)
    //             {
    //                 $check_csv = false;
    //             }
    //             elseif (isset($record['segment_id']) && $record['segment_id'] != '')
    //             {
    //                 $segment_check = App\Models\tblSegment::find($record['segment_id']);
    //                 if (isset($segment_check))
    //                 {
    //                     $segment_m_from_convert = $segment_check->km_from * 1000000 + $segment_check->m_from;
    //                     $segment_m_to_convert = $segment_check->km_to * 1000000 + $segment_check->m_to;
    //                     if ($m_to_convert < $segment_m_from_convert || $m_from_convert > $segment_m_to_convert || $m_to_convert > $segment_m_to_convert)
    //                     {
    //                         $check_csv = false;
    //                     }
    //                     else
    //                     {
    //                         if (isset($check_item['segment_id']) && $check_item['segment_id'] == $record['segment_id'])
    //                         {
    //                             $check_csv = false;
    //                             if (isset($record['direction']) && $record['direction'] != null)
    //                             {
    //                                 if ($check_item['direction'] != $record['direction'])
    //                                 {
    //                                     $check_csv = true;
    //                                 }
    //                                 else
    //                                 {
    //                                     if (isset($check_item['lane_pos_number']) && $check_item['lane_pos_number'] != $record['lane_pos_number'])
    //                                     {
    //                                         $check_csv = true;
    //                                     }
    //                                     else
    //                                     {
    //                                         if (isset($check_item['no_lane']) && $check_item['no_lane'] != $record['no_lane'])
    //                                         {
    //                                             $check_csv = true;
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                         if (!$check_csv)
    //                         {
    //                             if ($m_to_convert <= $item_m_from_convert || $m_from_convert >= $item_m_to_convert)
    //                             {
    //                                 $check_csv = true;
    //                             }
    //                             else
    //                             {
    //                                 $check_csv = false;
    //                                 $record_completion_date = Carbon::parse($record['completion_date']);
    //                                 $item_completion_date = Carbon::parse($check_item['completion_date']);
    //                                 $record_repair_width = $record['total_width_repair_lane'] / 2;
    //                                 $item_repair_width = $check_item['total_width_repair_lane'] / 2;
    //                                 if (isset($sub_record['direction_running']))
    //                                 {
    //                                     if($sub_record['direction_running'] == 0)
    //                                     {
    //                                         $record['distance'];
    //                                     }
    //                                     else
    //                                     {
    //                                         $record['distance'] = - $record['distance'];
    //                                     }
    //                                     if (( $record['distance'] - $record_repair_width) >= ($check_item['distance'] + $item_repair_width)
    //                                         || ( $record['distance'] + $record_repair_width) <= ($check_item['distance'] - $item_repair_width))
    //                                     {
    //                                         $check_csv = true;
    //                                     }
    //                                     elseif($record_completion_date->copy()->subMonths($sub_record['repair_duration']) > $item_completion_date
    //                                         || $record_completion_date < $item_completion_date->copy()->subMonths($check_item['repair_duration']))
    //                                     {
    //                                         $check_csv = true;
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     if (isset($record['km_station']) && isset($record['m_station']))
    //     {
    //         if (isset($record['segment_id']) && $record['segment_id'] != '' )
    //         {
    //             if ($check_item['segment_id'] == $record['segment_id'])
    //             {
    //                 if ($record['km_station'] == $check_item['km_station'] && $record['m_station'] == $check_item['m_station'])
    //                 {
    //                     $check_csv = false;
    //                 }
    //             }
    //         }
    //     }
    //     return $check_csv;
    // }

    static function getMasterIDByKey($key_check, $item, $config = [], $data= null)
    {
        $id = null;
        $all_config = $config;
        foreach ($all_config as $key => $value)
        {
            if ($value['type'] == 'select' && $key == $key_check)
            {
                if (isset($value['modelCheck']))
                {
                    foreach ($value['modelCheck'] as $master_item)
                    {
                        if ($master_item['value'] != '')
                        {
                            $master_name = mb_strtolower($master_item['name']);
                            $item_lower = mb_strtolower($item);
                            $item_name = trim($item_lower);
                            if ($master_name == $item_name || $master_item['value'] == $item)
                            {
                                $id = $master_item['value'];
                                break;
                            }
                        }
                    }
                }
                break;
            }
        }
        return $id;
    }

	static function getID($key_check, $item, $config = [], $record = null)
    {
        $id = null;
        $all_config = $config;
        foreach ($all_config as $key => $value)
        {
            if ($value['type'] == 'select' && $key == $key_check)
            {
            	if (isset($value['checkitem']))
            	{
            		$check = null;
            		$modelParent = $value['checkitem']['model'];
                    $funcCheck = $value['checkitem']['func'];
                    if(isset($value['checkitem']['data']))
                    {
                    	@$childData = @$modelParent::$funcCheck($check = $record[@$value['checkitem']['data']]);
                    }
                    else
                    {
                    	$childData = $modelParent::$funcCheck();
                    }
                    foreach ($childData as $master_item)
                    {
                        if ($master_item['value'] != '')
                        {
                            $master_name = mb_strtolower($master_item['name']);
                            $item_lower = mb_strtolower($item);
                            $item_name = trim($item_lower);
                            if ($master_name == $item_name || $master_item['value'] == $item)
                            {
                                $id = $master_item['value'];
                                break;
                            }
                        }
                    }
            	}
            	else
	            {
	            	if (isset($value['modelCheck']))
	                {
		            	foreach ($value['modelCheck'] as $master_item)
		                {
		                    if (isset($value['modelCheck']))
					        {
		                        if ($master_item['value'] != '')
		                        {
		                            $master_name = mb_strtolower($master_item['name']);
		                            $item_lower = mb_strtolower($item);
		                            $item_name = trim($item_lower);
		                            if ($master_name == $item_name || $master_item['value'] == $item)
		                            {
		                                $id = $master_item['value'];
		                                break;
		                            }
		                        }
		                    }
		                }
		            }
	            }
            }
        }
        return $id;
    }

    static function super_unique($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
        foreach ($result as $key => $value)
        {
            if ( is_array($value) )
            {
                $result[$key] = super_unique($value);
            }
        }

        return $result;
    }

    static function covertChr($case)
    {
    	if($case < 10) 
        {
           $data = chr(ord('I')+ $case-1).'1:'.chr(ord('I')+ 2*$case-2);
        }
        else 
        {
            $data = chr(ord('I')+ $case-1).'1:A'.chr(56 + $case);
        }
        return $data;
    }

    /**
     * Calculate integrated RI length
     * @param rmb: number, optional
     * @param sb: number, optional
     * @param route: number, optional
     * @param year: number, required
     * @param pavement_type: number, optional
     * @return a number
     */
    static function calculateIntegratedRILength($rmb = null, $sb = null, $route = null, $year, $pavement_type = null)
    {
    	$sb_ids = [];
    	if (!empty($sb))
        {
            $sb_ids = [$sb];
        }
        else if (!empty($rmb))
        {
            $sb_ids = tblOrganization::where('parent_id', $rmb)->pluck('id')->toArray();
        }

        $segment = new tblSegment();
        if (count($sb_ids) > 0)
        {
        	$segment = $segment->whereIn('SB_id', $sb_ids);
        }
        if (!empty($route))
        {
        	$segment = $segment->where('branch_id', $route);
        }
        $segment = $segment->pluck('id')->toArray();

        $sql = "select (count(id) * 100) as total_length from (SELECT * FROM `tblPMS_RI_info` WHERE segment_id IN (" . implode(',', $segment) . ') ';
        
        if ($pavement_type == 'all')
        {
        	$sql.= "and pavement_type_code IN (1, 2, 3)";	
        }
        else if (!empty($pavement_type))
        {
            $sql.= "and pavement_type_code = " . $pavement_type ;
        }
        $sql.= " and PMS_info_id IN (SELECT id FROM `tblPMS_sectioning_info` i where type_id = 1 and id = (SELECT id from tblPMS_sectioning_info where PMS_section_id = i.`PMS_section_id` and type_id = 1 and condition_year <= ? order by condition_year desc limit 1))) a";
    
        $rsl = DB::select($sql, [$year]);
        return $rsl[0]->total_length;
    }

    /**
     * Calculate average indeces
     * @param index_type: string, required
     * @param rmb: number, required
     * @param integrated_mode: boolean, optional
     * @param year: number, optional. -1 = latest
     * @param groupby_route: boolean, optional
     * @return a number or array of number
     */
    static function calculateAVGPCIndex($index_type, $rmb, $year = -1, $integrated_mode = false, $groupby_route = false)
    {
    	$sb_ids = tblOrganization::where('parent_id', $rmb)->pluck('id')->toArray();
    	$data = DB::table('tblSection_PC AS p')
			->join('tblSection_PC_history AS h', 'p.id', '=', 'h.section_id')
            ->join('tblBranch', 'tblBranch.id', '=', 'p.branch_id')
            ->select(DB::raw('sum(h.' . $index_type  . ' * h.section_length)/ sum(h.section_length) as total, tblBranch.name_en as name_en, tblBranch.name_vn as name_vi, tblBranch.id as branch_id'))
            ->whereIn('p.SB_id', $sb_ids)
			->where('p.branch_id', '!=', 0)
			->where('h.' . $index_type, '>=', 0);

		if ($year == -1)
		{	
			$data = $data->whereRaw("h.id = (SELECT id FROM tblSection_PC_history WHERE section_id = p.id ORDER BY date_y DESC LIMIT 0, 1)");
		}
		else
		{
			if ($integrated_mode)
			{
				$data = $data->whereRaw("h.id = (SELECT id FROM tblSection_PC_history WHERE section_id = p.id and date_y <= $year ORDER BY date_y DESC LIMIT 0, 1)");
			}
			else
			{
				$data = $data->where('h.date_y', $year);
			}
			
		}

        if ($groupby_route)
        {
        	$data = $data->groupBy('p.branch_id');
        }
        $data = $data->get();
        return $data;
    }

    static function calculateAVGPCIndexNotIntegrated($index_type, $rmb)
    {
        $parent_id = $rmb == -1 ? DB::table('tblOrganization')->where('level', 2)->pluck('id')->toArray() : [$rmb];
        $SB_id = DB::table('tblOrganization')->whereIn('parent_id', $parent_id)->pluck('id')->toArray();
        $data = DB::table('tblSection_PC_history AS h')
            ->join('tblOrganization AS o', 'o.id', '=', 'h.SB_id')
            ->select(DB::raw('o.parent_id as parent, sum(h.' . $index_type  . ' * h.section_length)/ sum(h.section_length) as total, h.date_y as year'))
            ->whereIn('h.SB_id', $SB_id)
            ->where('h.branch_id', '!=', 0)
            ->where('h.' . $index_type, '>=', 0);
        if ($rmb == -1)
        {
            $data = $data->groupBy('parent')->groupBy('date_y')->get();
        }
        else
        {
            $data = $data->groupBy('date_y')->get();
        }
        return $data;
    }
}