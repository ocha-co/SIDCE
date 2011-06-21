<?php defined('SYSPATH') or die('No direct script access.');

/**
* Bulletin Table Model
*/

class Bulletin_Model extends ORM
{
	// Database table name
	protected $table_name = 'bulletin';

    /**
    * Title for bulletin
    * @param string $start Start date of period
    * @param string $end End date of period
    * @return string $title
    */
    public static function title($start, $end){

        list($y_s,$m_s,$d_s) = explode('-',$start);
        list($y_e,$m_e,$d_e) = explode('-',$end);

        $title_e = "$d_e ".Kohana::lang('ui_main.'.strtolower(date('F', strtotime($end))))." $y_e";
        $title_s = $d_s;
        if ($m_s != $m_e)   $title_s .= " ".Kohana::lang('ui_main.'.strtolower(date('F', strtotime($start))));
        if ($y_s != $y_e)   $title_s .= " $y_s"; 
        
        return strtoupper(Kohana::lang('ui_main.humanitarian_bulletin'))." <br />".$title_s." - ".$title_e;
    }
    
    /**
    * Copy for bulletin
    * @return string $copy
    */
    public static function copyright(){

        list($y,$m,$d) = explode('-',date('Y-F-d'));
        $c = "Generado el: $d ".Kohana::lang('ui_main.'.strtolower($m))." $y";

        return $c;
    }
    
    /**
    * Description for bulletin
    * @param string $start Start date of period
    * @param string $end End date of period
    * @return string $description
    */
    public static function description($start, $end){

        $bulletin = ORM::factory('bulletin')->where("bulletin_start = '$start' AND bulletin_end = '$end'")->find();

        //return utf8_encode($bulletin->bulletin_description);
        return $bulletin->bulletin_description;
    }

    /**
    * Get trend to bulletin, 2 weeks period
    * @return array $trend
    */
    public static function trend(){

        $trend = array();
        $num_ch = 3;  // Number of character of month name
        $weeks = 12; // Weeks of trend
        
        //Last 3 months, 2 weeks trend
        for ($i=12;$i>0;$i--){

            $s = date('Y-m-d', strtotime("-".(2*$i+1)." monday"));
            $e = date('Y-m-d', strtotime("-".(2*$i)." sunday"));

            list($y_s,$m_s,$d_s) = explode('-',$s);
            list($y_e,$m_e,$d_e) = explode('-',$e);

            $t = substr(Kohana::lang('ui_main.'.strtolower(date('F', strtotime($s)))),0,$num_ch)." $d_s - ".
                 substr(Kohana::lang('ui_main.'.strtolower(date('F', strtotime($e)))),0,$num_ch)." $d_e";

            $trend[$t] = ORM::factory('incident')->where("incident_date BETWEEN '$s' AND '$e'")->count_all();
        }

        return $trend;
    }
    
    /**
    * Get img trend to bulletin, 2 weeks period
    * @return array $i_cities Number of incident in city
    */
    public static function img_trend($end){
        
        $trend = Bulletin_Model::trend($end);
        $labels = Bulletin_Model::img_labels($trend);
        $max_y = 1.5*max($trend);
        $grid_step_x = 8;
        $grid_step_y = 10;
        $lineChart = new gLineChart(450,150);
        $lineChart->setTitle(Kohana::lang('ui_main.weekly_trend'));
        $lineChart->setTitleOptions('000000',20);
        $lineChart->addDataSet($trend);
        $lineChart->setGridLines($grid_step_x,$grid_step_y,1,5,2,5);
        $lineChart->addAxisRange(1,0,$max_y);
        $lineChart->setDataRange(0,$max_y);
        $lineChart->setVisibleAxes(array('x','y','x'));
        $lineChart->addAxisLabel(0, $labels['labels_0']);
        $lineChart->addAxisLabel(2, $labels['labels_2']);
        $lineChart->addValueMarkers('N','000000',0,-1,11);
        $lineChart->setAxisLabelStyle(0,'000000',9);
        $lineChart->setAxisLabelStyle(1,'000000',11);
        $lineChart->setAxisLabelStyle(2,'000000',9);
        ob_start();
        $lineChart->getImgCode();
        return ob_get_clean();
    }
   
    public static function img_labels($array){
        $labels = array_keys($array);
        foreach ($labels as $l=>$label){
            if (fmod($l,2) == 0){
                $labels_0[] = str_replace(" ","",$label);
                $labels_2[] = '';
            }
            else{
                $labels_0[] = '';
                $labels_2[] = str_replace(" ","",$label);
            }
        }

        return compact('labels_0', 'labels_2');

    }

    /**
    * Get incidentes per cities in a period
    * @param string $s Start date of period
    * @param string $e End date of period
    * @return array $i_cities Number of incident in city
    */
    public static function i_cities($s, $e){
        
        $i_cities = array();
        $rs = ORM::factory('incident')->select("COUNT(incident.id) AS num, location_name AS name")
                                      ->join('location', 'incident.location_id', 'location.id')
                                      ->where("incident_date BETWEEN '$s' AND '$e'")
                                      ->groupby("name")
                                      ->find_all();
        foreach ($rs as $r){
            $i_cities[$r->name] = $r->num; 
        }
        return $i_cities;
    }
    
    /**
    * Get image of incidentes per cities in a period
    * @param string $s Start date of period
    * @param string $e End date of period
    * @return string $img_code
    */
    public static function img_cities($s, $e){
    
        $i_cities = Bulletin_Model::i_cities($s,$e);
        $labels = Bulletin_Model::img_labels($i_cities);
        $max_y = 1.5*max($i_cities);
        $barChart = new gBarChart(300,200);
        $barChart->setTitle(Kohana::lang('ui_main.incidents_per_city'));
        $barChart->setTitleOptions('000000',20);
        $barChart->addDataSet($i_cities);
        $barChart->setVisibleAxes(array('x','y','x'));
        $barChart->addAxisLabel(0, $labels['labels_0']);
        $barChart->addAxisLabel(2, $labels['labels_2']);
        $barChart->setAxisLabelStyle(0,'000000',9);
        $barChart->setAxisLabelStyle(2,'000000',9);
        $barChart->addAxisRange(1,0,$max_y);
        $barChart->setDataRange(0,$max_y);
        $barChart->setGridLines(0,20,1,5);
        $barChart->addValueMarkers('N','000000',0,-1,11);
        ob_start();
        $barChart->getImgCode();
        return ob_get_clean();
    }

    /**
    * Get incidentes per category in a period
    * @param string $s Start date of period
    * @param string $e End date of period
    * @param string $o Option
    * @return array $array Number of incident in category
    */
    public static function i_cat($s, $e, $o='i_cat'){
        
        $array = array();

        switch($o){
            case 'i_cat':
                foreach (ORM::factory('category')->where(array('category_visible' => '1', 'parent_id' => 0))->find_all() as $c => $category)
                {
                    $c_id = $category->id;
                    $categories = ORM::factory('category')->where('parent_id',$c_id)->find_all();
                    $cats_id = array();
                    $cats_id[] = $c_id;
                    foreach ($categories as $cat){
                        $cats_id[] = $cat->id;
                    }

                    $rs = ORM::factory('incident')->select("COUNT(incident.id) AS num")
                        ->join('incident_category AS i_c', 'i_c.incident_id', 'incident.id')
                        ->where("incident_date BETWEEN '$s' AND '$e' AND category_id IN (".implode(',', $cats_id).")")
                        ->find_all();
                    
                    $num = 0;
                    foreach ($rs as $r) $num += $r->num; 
                    
                    $array[$category->category_title] = $num; 
                }
            break;

            case 'map':
                $rs = ORM::factory('category')->select("category_title AS title, id")
                    ->where('parent_id = 0')
                    ->find_all();
                foreach ($rs as $r){
                    $array[$r->id] = "'".$r->title."'";
                }

            break;
        }

        return $array;
    }
    
    /**
    * Get image of incidentes per category in a period
    * @param string $s Start date of period
    * @param string $e End date of period
    * @return string $img_code
    */
    public static function img_cat($s, $e){

        $i_cat = Bulletin_Model::i_cat($s,$e);
        $pieChart = new gPieChart(400,150);
        $pieChart->setTitle(Kohana::lang('ui_main.incidents_per_category'));
        $pieChart->setTitleOptions('000000',20);
        $pieChart->addDataSet($i_cat);
        $pieChart->setLabels(array_keys($i_cat));
        $pieChart->setColors(array('ffad00','133cac','bf9130','2b4281','a67000','062270','ffc140','476dd5'));
        ob_start();
        $pieChart->getImgCode();
        return ob_get_clean();
    }

    /**
    * Generate Map - Mapserver
    * @param string $s Start date of period
    * @param string $e End date of period
    */
    public static function map($s, $e){
        
        //require Kohana::find_file('vendor', 'mapserver', $required = TRUE);
        require Kohana::find_file('vendor', 'color_converter', $required = TRUE);
        
        $converter = new colorConverter();
        $mapObj =  ms_newMapObj(Kohana::find_file('wms', 'cesar', true, 'map'));
        $mapObj->web->set('imagepath', DOCROOT.'media/img/tmp');
        $mapObj->web->set('imageurl', url::site()."media/img/tmp");
        $mapObj->setFontSet(APPPATH.'wms/fonts/fontset.txt');
		$mapObj->set('shapepath', APPPATH.'wms/');
        //SCALEBAR
        $mapObj->scalebar->set("status",MS_EMBED);
        $mapObj->scalebar->color->setRGB(0,0,0);
        $mapObj->scalebar->set("position",MS_UL);
        $mapObj->scalebar->set("intervals",3);
        $mapObj->scalebar->set("width",100);
        $mapObj->scalebar->set("height",5);
        $mapObj->scalebar->set("units",MS_KILOMETERS);
        $mapObj->scalebar->label->set("type",MS_TRUETYPE);
        $mapObj->scalebar->label->set("font", 'label');
        $mapObj->scalebar->label->set("size", 6);
        $mapObj->scalebar->label->set("position",MS_UC);
        $mapObj->scalebar->label->color->setRGB(0,0,0);

        $mapObj->legend->set('status', MS_EMBED);

        $cats = Category_Model::getCatsIncidentPeriod($s, $e);
        $layerObj = $mapObj->getLayerByName('INLINE');
        foreach ($cats as $cat){
            
            $rgb = $converter->html2rgb($cat['color']);
            
            $classObj = ms_newClassObj($layerObj);
            $classObj->set('name', $cat['title']);
            $styleObj =  ms_newStyleObj($classObj);
            $styleObj->color->setRGB($rgb[0], $rgb[1], $rgb[2]);
            $styleObj->set("symbolname", 'circle');
            $styleObj->set("size", '10');
            
        }

        $image = $mapObj->draw();
        
        // Incidents
        $incidents = json_decode(Bulletin_Model::json($s, $e));

        //Draw the map and add the point
        foreach($incidents->features as $inc){
            $pt = ms_newPointObj();
            $pt-> setXY($inc->geometry->coordinates[0], $inc->geometry->coordinates[1]);

            $classObj = $layerObj->getClass(0);
            $styleObj = $classObj->getStyle(0);
            $rgb = $converter->html2rgb($inc->properties->color);
            $styleObj->color->setRGB($rgb[0], $rgb[1], $rgb[2]);

            $pt->draw($mapObj, $layerObj, $image, 0 ,'');
        }

         return $image->saveWebImage();

    }
    
    /**
    * Retrive json to bulletin
    * @param $s string Start Date
    * @param $e string End Date
    * @param int $cat_id Category ID
    * @return
    */
    public function json($s,$e, $cat_id=null){
            
        $array = array();

        $array['type'] = 'FeatureCollection';

        $cond = "incident_date BETWEEN '$s' AND '$e'";
        if (!empty($cat_id))    $cond .= " AND category_id = $cat_id";

        $rs = ORM::factory('incident')->select("incident.id AS id, incident.incident_title AS title, category_color AS color, loc.longitude, loc.latitude")
            ->join('incident_category AS i_c', 'i_c.incident_id', 'incident.id')
            ->join('category', 'i_c.category_id', 'category.id')
            ->join('location AS loc', 'incident.location_id', 'loc.id')
            ->where("$cond")
            ->find_all();
        
        $array['features'] = array();
        foreach ($rs as $r){
            $x = $r->longitude;
            $y = $r->latitude;

            $array['features'][] = array('type' => 'Feature',
                    'properties' => array('color' => '#'.$r->color, 'total' => '', 'radius' => 5, 'desc' => ucfirst(strtolower($r->title))),
                    'geometry' => array('type' => 'point', 'coordinates' => array($x,$y)));

        } 
            
        return json_encode($array);

    }
	
}
