<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for reported Incidents
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Incident_Model extends ORM
{
	protected $has_many = array('category' => 'incident_category', 'media', 'verify', 'comment',
		'rating', 'alert' => 'alert_sent', 'incident_lang', 'form_response','cluster' => 'cluster_incident','victim');
	protected $has_one = array('location','incident_person','user','message','twitter','form');
	protected $belongs_to = array('sharing');

	// Database table name
	protected $table_name = 'incident';

	// Prevents cached items from being reloaded
	protected $reload_on_wakeup   = FALSE;

	static function get_active_categories()
	{
		// Get all active categories
		$categories = array();
		foreach (ORM::factory('category')
			->where('category_visible', '1')
			->find_all() as $category)
		{
			// Create a list of all categories
			$categories[$category->id] = array($category->category_title, $category->category_color);
		}
		return $categories;
	}

	/*
	* get the total number of reports
	* @param approved - Only count approved reports if true
	*/
	public static function get_total_reports($approved=false)
	{
		if($approved)
		{
			$count = ORM::factory('incident')->where('incident_active', '1')->count_all();
		}else{
			$count = ORM::factory('incident')->count_all();
		}

		return $count;
	}

	/*
	* get the total number of verified or unverified reports
	* @param verified - Only count verified reports if true, unverified if false
	*/
	public static function get_total_reports_by_verified($verified=false)
	{
		if($verified)
		{
			$count = ORM::factory('incident')->where('incident_verified', '1')->count_all();
		}else{
			$count = ORM::factory('incident')->where('incident_verified', '0')->count_all();
		}

		return $count;
	}

	/*
	* get the total number of verified or unverified reports
	* @param approved - Oldest approved report timestamp if true (oldest overall if false)
	*/
	public static function get_oldest_report_timestamp($approved=true)
	{
		if($approved)
		{
			$result = ORM::factory('incident')->where('incident_active', '1')->orderby(array('incident_date'=>'ASC'))->find_all(1,0);
		}else{
			$result = ORM::factory('incident')->where('incident_active', '0')->orderby(array('incident_date'=>'ASC'))->find_all(1,0);
		}

		foreach($result as $report)
		{
			return strtotime($report->incident_date);
		}
	}

	private static function category_graph_text($sql, $category)
	{
		$db = new Database();
		$query = $db->query($sql);
		$graph_data = array();
		$graph = ", \"".  $category[0] ."\": { label: '". str_replace("'","",$category[0]) ."', ";
		foreach ( $query as $month_count )
		{
			array_push($graph_data, "[" . $month_count->time * 1000 . ", " . $month_count->number . "]");
		}
		$graph .= "data: [". join($graph_data, ",") . "], ";
		$graph .= "color: '#". $category[1] ."' ";
		$graph .= " } ";
		return $graph;
	}

	static function get_incidents_by_interval($interval='month',$start_date=NULL,$end_date=NULL,$active='true',$media_type=NULL)
	{
		// Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');

		// get graph data
		// could not use DB query builder. It does not support parentheses yet
		$db = new Database();

		$select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-01')";
		$groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m')";
		if ($interval == 'day') {
			$select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-%d')";
			$groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m%d')";
		} elseif ($interval == 'hour') {
			$select_date_text = "DATE_FORMAT(incident_date, '%Y-%m-%d %H:%M')";
			$groupby_date_text = "DATE_FORMAT(incident_date, '%Y%m%d%H')";
		} elseif ($interval == 'week') {
			$select_date_text = "STR_TO_DATE(CONCAT(CAST(YEARWEEK(incident_date) AS CHAR), ' Sunday'), '%X%V %W')";
			$groupby_date_text = "YEARWEEK(incident_date)";
		}
        
		$date_filter = "";
		if ($start_date) {
			$date_filter .= ' AND incident_date >= "' . $start_date . '"';
		}
		if ($end_date) {
			$date_filter .= ' AND incident_date <= "' . $end_date . '"';
		}

		$active_filter = '1';
		if ($active == 'all' || $active == 'false') {
			$active_filter = '0,1';
		}

		$joins = '';
		$general_filter = '';
        $ope = ' COUNT(*) ';

        // SIDCE - L
		if (isset($media_type) && is_numeric($media_type) && $media_type != 99) {
			$joins = 'INNER JOIN '.$table_prefix.'media AS m ON m.incident_id = i.id';
			$general_filter = ' AND m.media_type IN ('. $media_type  .')';
		}

        if ($media_type == 99){
            $joins .= ' INNER JOIN form_response AS f ON i.id = f.incident_id ';
            $ope = ' SUM(form_response) ';
        }

		$graph_data = array();
		$all_graphs = array();

		$all_graphs['0'] = array();
		$all_graphs['0']['label'] = 'All Categories';
		$query_text = 'SELECT UNIX_TIMESTAMP(' . $select_date_text . ') AS time,
					    '.$ope.' AS number
					    FROM '.$table_prefix.'incident AS i ' . $joins . '
					    WHERE incident_active IN (' . $active_filter .')' .
		                $date_filter.$general_filter .'
					    GROUP BY ' . $groupby_date_text;




		$query = $db->query($query_text);
		$all_graphs['0']['data'] = array();
		foreach ( $query as $month_count )
		{
            // SIDCE - S
            $number = $month_count->number;
            if ($media_type == 99)  $number = $number;
			array_push($all_graphs['0']['data'],
				array($month_count->time * 1000, $number));
		}
		$all_graphs['0']['color'] = '#990000';

		$query_text = 'SELECT category_id, category_title, category_color, UNIX_TIMESTAMP(' . $select_date_text . ')
							AS time, '.$ope.' AS number
								FROM '.$table_prefix.'incident AS i
							INNER JOIN '.$table_prefix.'incident_category AS ic ON ic.incident_id = i.id
							INNER JOIN '.$table_prefix.'category AS c ON ic.category_id = c.id
							' . $joins . '
							WHERE incident_active IN (' . $active_filter . ')
								  ' . $date_filter.$general_filter . '
							GROUP BY ' . $groupby_date_text . ', category_id ';

		$query = $db->query($query_text);
		foreach ( $query as $month_count )
		{
			$category_id = $month_count->category_id;
			if (!isset($all_graphs[$category_id]))
			{
				$all_graphs[$category_id] = array();
				$all_graphs[$category_id]['label'] = $month_count->category_title;
				$all_graphs[$category_id]['color'] = '#'. $month_count->category_color;
				$all_graphs[$category_id]['data'] = array();
			}
			array_push($all_graphs[$category_id]['data'],
				array($month_count->time * 1000, $month_count->number));
		}
		$graphs = json_encode($all_graphs);
		return $graphs;
	}
	
    /**
	 * Retrieve number of persons, houses, etc for an incident
	 * @param int $incident_id Id of incident
	 * @param string $filters url filters defined in timeline.js, line 703
	 * @return int Impact
	 */
	static function get_incidents_to_category_chart($start_date=NULL,$end_date=NULL,$active='true', $media_type = null){
        
        $s = date("Y-m-d H:i:s", $start_date);
        $e = date("Y-m-d H:i:s", $end_date);
        
		foreach (ORM::factory('category')->where(array('category_visible' => '1', 'parent_id' => 0))->find_all() as $c => $category)
		{
            $c_id = $category->id;
            $categories = ORM::factory('category')->where('parent_id',$c_id)->find_all();
            $cats_id = array();
            $cats_id[] = $c_id;
            foreach ($categories as $cat){
                $cats_id[] = $cat->id;
            }

            $num = ORM::factory('incident')->join('incident_category', 'incident.id', 'incident_category.incident_id')
                    ->where("category_id IN (".implode(',', $cats_id).") AND incident_date BETWEEN '$s' AND '$e'")->count_all();

            $json['data'][] = array($category->category_title, $num);
            $json['colors'][] = "#".$category->category_color;
        }
        
        $json['num_cats'] = $c + 1; 
        return json_encode($json);

    }
    
    /**
	 * Retrieve number of persons, houses, etc for an incident, form_id = 2
	 * @param int $incident_id Id of incident
	 * @return array $impact
	 */
	public static function get_impact($id)
    {
		$db = new Database();
        $impact = array();

        $sql = "SELECT field_name, form_response FROM form_response f_r JOIN form_field f_f ON (f_r.form_field_id = f_f.id) 
                WHERE incident_id = $id AND form_id = 1 AND CHAR_LENGTH(form_response) > 0";
        
        foreach ($query = $db->query($sql) as $q){
            $impact[$q->field_name] = $q->form_response;
        }

        return $impact;

	}
    
    /**
	 * Retrieve aid for an incident, form_id = 2
	 * @param int $incident_id Id of incident
	 * @return array $aid
	 */
	public static function get_aid($id)
    {
		$db = new Database();
        $aid = array();

        $sql = "SELECT field_name, form_response FROM form_response f_r JOIN form_field f_f ON (f_r.form_field_id = f_f.id) 
                WHERE incident_id = $id AND form_id = 2 AND CHAR_LENGTH(form_response) > 0";
        
        foreach ($query = $db->query($sql) as $q){
            $aid[$q->field_name] = $q->form_response;
        }

        return $aid;

	}
    
    /**
	 * Retrieve number of persons, houses, etc for an incident
	 * @param int $incident_id Id of incident
	 * @param string $filters url filters defined in timeline.js, line 703
	 * @return int Impact
	 */
	public static function get_num_impact($id,$filters = null)
    {
		$db = new Database();
        
        $cond = "incident_id = $id";

        // Map Impact Filters
        if (!empty($filters)){
            $fls = explode('~',$filters);
            foreach ($fls as $f){
                $val = explode(':',$f);
                if (!empty($val[1])){
                    $cond .= ' AND form_field_id = '.$val[1];
                }
            }
        }
        
        $count = ORM::factory('form_response')->select("SUM(form_response) AS sum")->where($cond)->groupby('incident_id')->find();
        
        if ($count->loaded === true){
            return $count[0]->sum;
        }
        else    return 0;
	}
	
    /**
    * Read a file into array
    * @access public
    * @param $fp File pointer
    * @return array $array
    */
    public static function _LeerEnArreglo($fp){
		$array = array();
		$f = 0;
		while (!feof($fp)){
			$array[$f] = fgets($fp);
			$f++;
		}
		return $array;
	}

    /**
    * Load CSV file content to incidents
    * @access public
    * @param $path_to_file Real path of file to load
    * @param $go Load data into the db, 1,0
    * @return array $success
    */
	public static function process_upload_sigpad($path_to_file, $go, $date_format)
	{
        
        //ini_set('memory_limit', '128M');
        $conn = Database::instance();
        $locale = 'es_AR';
        $user_id = $f_w = 0;
        $mode = 1;
        $verified = 1;
        $active = 1;
        $source = 1;  // Source Reliability
        $information = 1; // Information Probability
        $rating = $alert = $insert = 0;
        $table_impact_type = 'form_field';
        $table_aid_item = 'form_field';
        $table_impact = 'form_response';
        $table_aid = 'form_response';
        $form_impact_id = 1;  // Already created
        $form_aid_id = 2;  // Already created
        $sep = '|';
        $sep_date = '-';
        $summary_t = '';
        $go = ($go == 1) ? true : false;
        $error_t = false;

        $fp = fopen($path_to_file,'r');
        $cont_archivo = Incident_Model::_LeerEnArreglo($fp);
        fclose($fp);

        // Delete labels rows
        $cont_archivo = array_slice($cont_archivo,2);
        
        $pattern_mun = '(-|,|\/)';
        $num_inc = 0;
        $num_dup = 0;
        $duplicities = '';
        foreach ($cont_archivo as $f=>$fila){

            $id_muns = array();
            $impact_id = array();
            $aid_id = array();
            $fila_s = explode($sep,$fila);
            $summary = '';
            $error = false;

            if (!empty($fila_s[0]) && !empty($fila_s[1]) && !empty($fila_s[2]) && !empty($fila_s[3])){

                $nom_mun = trim($fila_s[2]);
                $nom_mun_lower = strtolower($nom_mun);
                $cat_nom = trim($fila_s[3]);
                $fecha = str_replace('/',$sep_date,$fila_s[0]);

                // dd-mm-yyyy
                if ($date_format == 2 || $date_format == 3){
                    $f_t = explode('-', $fecha);
                    $fecha = $f_t[2].$sep_date.$f_t[1].$sep_date.$f_t[0];
                }
                // mm-dd-yyyy
                else if ($date_format == 4 || $date_format == 5){
                    $f_t = explode('-', $fecha);
                    $fecha = $f_t[2].$sep_date.$f_t[0].$sep_date.$f_t[1];
                }
                
                $depto_nom = trim($fila_s[1]);

                //Replacemets
                $nom_mun = str_replace(array('lopez de micay',
                                             'san juan nepomuceno',
                                             'san estanislao de kosta',
                                             'villa maria',
                                             'calima darien',
                                             'villarica',
                                             'herbeo',
                                             'piedecuesta',
                                             'guabata',
                                             'el hato',
                                             'san vicente de chucuri',
                                             'el carmen de chucuri',
                                             'villa garzon',
                                             'mayama',
                                             'magui payan',
                                             'vistahermosa',
                                             'dosquebradas',
                                             'san jose miranda',
                                             'sitio nuevo',
                                             'pueblo viejo',
                                             'cerro de san antonio',
                                             'eltambo',
                                             'biota',
                                             'san franciso',
                                             'san antonio del tequendama',
                                             'begachi',
                                             'entrerios',
                                             'polo nuevo',
                                             'rioviejo',
                                             'santa rosa sur',
                                             'arenal sur',
                                             'arroyo hondo',
                                             'itsmina',
                                             'san andres de sotavento',
                                             'san juan de rioseco',
                                             'caimalito',
                                             'guadalajara de buga',
                                             'circacia',
                                             'santafe de antioquia',
                                             'mesitas del colegio',
                                             'litoral del san juan',
                                             'san vicente de ferrer',
                                             '',

                                            ),
                                       array('López (Micay) ',
                                             'San Juan de Nepomuceno ',
                                             'San Estanislao ',
                                             'Villamaría ',
                                             'Calima (Darién) ',
                                             'Villarrica ',
                                             'Herveo ',
                                             'Pie de Cuesta',
                                             'Guavatá',
                                             'Hato',
                                             'San Vicente del Chucurí',
                                             'El Carmen',
                                             'Villagarzón',
                                             'Mallama (Piedrancha)',
                                             'Magüi (Payán)',
                                             'Vista Hermosa',
                                             'Dos Quebradas',
                                             'San José de Miranda',
                                             'Sitionuevo',
                                             'Puebloviejo',
                                             'Cerro San Antonio',
                                             'El Tambo',
                                             'Viotá',
                                             'San Francisco',
                                             'San Antonio de Tequendama',
                                             'Vegachí',
                                             'Entrerríos',
                                             'Polonuevo',
                                             'Río Viejo',
                                             'Santa Rosa del Sur',
                                             'Arenal',
                                             'Arroyohondo',
                                             'Istmina',
                                             'San Andrés Sotavento',
                                             'San Juan de Río Seco',
                                             'Caimito',
                                             'Buga',
                                             'Circasia',
                                             'Santa Fé de Antioquia',
                                             'El Colegio',
                                             'El Litoral de San Juan',
                                             'San Vicente',
                                             '',
                                             '',
                                             '',
                                            ),
                                            $nom_mun_lower);
                

                $no_one = preg_match("/$pattern_mun/",$nom_mun);

                if ($nom_mun_lower != 'departamento' && !$no_one){
                    $city_c = ORM::factory('city')->like('city',$nom_mun)->find();
                    if ($city_c->loaded !== true){
                        $summary .= "<br />No existe el municipio = ".$fila_s[1]." >> $nom_mun";
                        $error = true;
                    }
                }
                
                if ($nom_mun_lower == 'departamento'){
                    $state = ORM::factory('state')->like('state',$fila_s[1])->find();
                    if ($state->loaded === true){
                        $state_id = $state->id;

                        $muns = ORM::factory('city')->where('state_id',$state_id)->find_all();
                        foreach ($muns as $obj){
                            $id_muns[] = $obj->divipola; 
                        }

                    }
                }
                else if ($no_one){
                    $nom_muns = preg_split("/$pattern_mun/",$nom_mun);

                    foreach ($nom_muns as $n)  $nom_muns_sql[] = "'".trim($n)."'";
                    $muns = ORM::factory('city')->in('city',implode(',',$nom_muns_sql))->find_all();
                    foreach ($muns as $obj){
                        $id_muns[] = $obj->divipola; 
                    }
                }
                else    $id_muns[] = $city_c->divipola;

                // Check if category exists
                $object = ORM::factory('category')->like('category_title', $cat_nom)->find();

                if ($object->loaded === true){
                    $id_cat = $object->id;
                }
                else{
                    $summary .= '<br />No existe la categoria: '.$cat_nom.' Por favor cree primero la categoría';
                    $error = true;
                }
                
                $titulo = $cat_nom.' '.$nom_mun.' '.$fecha;
                $descripcion = $titulo;

                
                foreach ($id_muns as $id_mun){
                    
                    $city = ORM::factory('city')->where('divipola',$id_mun)->find();
                    if ($city->loaded !== true){
                        $summary .= "<br />No hay ciudad con divipola = $id_mun";
                        $error = true;
                    }
                    else{
                        
                        if ($go){
                            $location = new Location_Model();
                            $location->latitude = $city->city_lat;
                            $location->longitude = $city->city_lon;
                            $location->location_name = $city->city;
                            $location->country_id = $city->country_id;
                        
                            $location->save();
                        }
                    
                        // Find possible duplication
                        $dups = Incident_Model::find_duplicity(array('date' => $fecha,
                                                                'category' => array($id_cat),
                                                                'lat' => 1*$city->city_lat,
                                                                'lon' => 1*$city->city_lon
                                                          )
                                            );
                        if (count($dups) > 0){
                            $duplicities .= '<ul>';    
                            foreach ($dups as $dup){
                                $duplicities .= '<li><a href="'.url::site().'admin/reports/edit/'.$dup->id.'" target="_blank">'.$dup->incident_title.'</a></li>';
                            }
                            $duplicities .= '</ul>'; 

                            $num_dup++;
                        }
                        else{
                            
                            if ($go){
                                $incident = new Incident_Model();
                                $incident->incident_title = $titulo;
                                $incident->incident_description = $descripcion;
                                $incident->incident_date = $fecha;
                                $incident->location_id = $location->id;
                                $incident->form_id	  = '';
                                $incident->incident_mode = 5;  // Sigpad
                                $incident->locale	  =  $locale;
                                $incident->incident_active = 1;
                                $incident->incident_verified = 1;
                                $incident->save();
                                
                                if ($incident->id){
                                    $num_inc++;
                                
                                    $incident_category = new Incident_Category_Model();
                                    $incident_category->incident_id = $incident->id;
                                    $incident_category->category_id = $id_cat;
                                    $incident_category->save();

                                
                                    // Verified
                                    $sql = "INSERT INTO verified (incident_id,user_id,verified_date,verified_status) VALUES ($incident->id,$user_id,$fecha,1)";
                                    $conn->query($sql);

                                    //Impact & Aid, Column 4-28
                                    for ($c=4;$c<28;$c++){
                                        
                                        if (!empty($fila_s[$c])){
                                            $form_response = new Form_Response_Model();
                                            $form_response->form_field_id = $c - 3;  // Start id=1, table form_field, 
                                            $form_response->incident_id = $incident->id;
                                            $form_response->form_response = $fila_s[$c];
                                            $form_response->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($error){
                $summary_t .= "<br />ERROR Linea ".($f+3).": $summary";
                $f_w++;
                $error_t = true;
            }
        }
        
        if ($go)    $summary_t .= "<br /><br /><b>Se cargaron $num_inc Incidentes con éxito</b>, recuerde que este n&uacute;mero puede ser mayor al número de filas del archivo, dado que la localizaci&oacute;n puede ser 'Departamento', lo que indica que se debe cargar el mismo evento para todos los municipios";
        else{
            if ($num_dup > 0)   $summary_t .= "<br /><br />Se encontraron $num_dup duplicidades, a continuación encontrará un listado de los reportes que existen en el sistema
                                            y en el archivo a importar, $duplicities";
            $summary_t .= '<br /><br />Total filas archivo: <b>'.$f.'</b>, filas para importar: <b>'.($f - $f_w).'</b>, filas con inconsistencias: <b>'.$f_w.'</b>';

            if ($error_t) $summary_t .= '<br /><br /><b>EL ARCHIVO A IMPORTAR TIENE INCOSISTENCIAS, POR FAVOR REVISELO E ITENTE NUEVAMENTE LA IMPORTACION, DE LO CONTRARIO LOS REGISTROS CON INCONSISTENCIAS NO SERAN IMPORTADOS</b> ::: <a href="#" onclick="if (confirm(\'Está seguro que desea continuar?\')){ window.location.reload();}return false; ">Importar con inconsistencias</a>';

            if (!$error_t)
                    $summary_t .= '<div class="btns"><ul><li><a href="#" class="btn_save" onclick="if (confirm(\'Está seguro que desea continuar?\')){ location.reload();}return false; ">'.strtoupper(Kohana::lang('ui_main.continue')).'</a></li></ul></div>';
        }

        return $summary_t;

   } 
    
    /*
	* Find possible incident duplication with active incidents
    * access private
	* @param array $fields Fields to compare, keys are table column names
    */
	public static function find_duplicity($fields){
        
		$db = new Database();
        $cond = array('incident_active = 1');

        if (isset($fields['date'])){
            $date_comp = $fields['date'];
            $yyyy = date('Y',strtotime($date_comp));
            $mm = date('m',strtotime($date_comp))*1;
            $dd = date('d',strtotime($date_comp))*1;
            
            $cond[] = "YEAR(incident_date) = $yyyy AND MONTH(incident_date) = $mm AND DAY(incident_date) = $dd ";

        }

        if (isset($fields['category'])){
            $cond[] = 'category_id IN ('.implode(',',$fields['category']).')';
        }

        if (isset($fields['lat'])){
            // Field latitude in location table is double, 6 decimals
            $cond[] = ' latitude = '.number_format($fields['lat'],6);
        }
        
        if (isset($fields['lon'])){
            // Field latitude in location table is double, 6 decimals
            $cond[] = ' longitude = '.number_format($fields['lon'],6);
        }
        
        $sql = 'SELECT * FROM incident INNER JOIN incident_category ON incident.id = incident_category.incident_id INNER JOIN location ON incident.location_id = location.id WHERE '.implode(' AND ',$cond);
        $dups = $db->query($sql);

        return $dups;
    }
    
    /**
	 * Retrieve dropdown to start date
	 * @param string $s Timestamp of selected option
	 * @param string $end Timestamp of selected <option></option>
	 * @return string $dropdown
	 */
     static public function getDropdownsDate($active_startDate, $active_endDate){
        
        $db = new Database;
		// Set Table Prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
        $startDate = "";
        $endDate = "";
        $active_month_start = date('n',$active_startDate);
        $active_year_start = date('Y',$active_startDate);
        $active_day_end = date('d',$active_endDate);
        $active_month_end = date('n',$active_endDate);
        $active_year_end = date('Y',$active_endDate);

        // Next, Get the Range of Years
        /*$query = $db->query('SELECT DATE_FORMAT(incident_date, \'%Y\') AS incident_date FROM '.$table_prefix.'incident 
                             WHERE incident_active = 1 
                             GROUP BY DATE_FORMAT(incident_date, \'%Y\') ORDER BY incident_date');*/
        //foreach ($query as $slider_date)
        //{
			//$years = $slider_date->incident_date;
		for($years = $active_year_start; $years <= $active_year_end; $years++ )
		{
        	$startDate .= "<optgroup label=\"" . $years . "\">";
            for ( $i=1; $i <= 12; $i++ ) {
                if ( $i < 10 )
                {
                    $i = "0" . $i;
                }
                // SIHCE, fix selected option
                $startDate .= "<option value=\"" . strtotime($years . "-" . $i . "-01") . "\"";
				if ( ( (int) $i == ( $active_month_start )) && $years == $active_year_start )
				{
					$startDate .= " selected=\"selected\" ";
				}
				$m = substr(Kohana::lang('ui_main.'.date('F', mktime(0,0,0,$i,1))), 0,3);
				$startDate .= ">" .$m . " " . $years . "</option>";
			}
            $startDate .= "</optgroup>";

            $endDate .= "<optgroup label=\"" . $years . "\">";
            $months = ($years < $active_year_end ? 12 : $active_month_end);
            for ( $i=1; $i <= $months; $i++ )
            {
                if ( $i < 10 )
                {
                    $i = "0" . $i;
                }
                //$endDate .= "<option value=\"" . strtotime($years . "-" . $i . "-" . date('t', mktime(0,0,0,$i,1))." 23:59:59") . "\"";

                // SIDCE, end date day is today, not the last day of month
                $endDate .= "<option value=\"" . strtotime($years . "-" . $i . "-" . $active_day_end) ."\"";
                // Focus on the most active month or set December as month of endDate
				if ( ( ( (int) $i ==  $active_month_end ) && $years == $active_year_end )
						 	|| ($i == 12 && preg_match('/selected/', $endDate) == 0))
				{
					$endDate .= " selected=\"selected\" ";
                }
				$m = substr(Kohana::lang('ui_main.'.date('F', mktime(0,0,0,$i,1))), 0,3);
                $endDate .= ">" . $m . " " . $years . "</option>";
			}
            $endDate .= "</optgroup>";
        }

        return array('s' => $startDate, 'e' => $endDate);
    }

    /**
	 * Retrieve oldest incidente date
	 * @return string $date Y-M-d
	 */
     static public function getOldestDate(){
        $rs = ORM::factory('incident')->select('incident_date AS date')->find_all(1,0);
        return $rs[0]->date;
     }
    
    /**
	 * Return the last sigpad upload record
	 * @return string $date Y-M-d
	 */
     static public function getLastUploadSigpad(){
        $rs = ORM::factory('incident')->select('incident_title')->where('incident_mode = 5')->orderby('id','DESC')->find_all(1,0);
        return $rs[0];
     }

}
