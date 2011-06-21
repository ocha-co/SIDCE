<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This is the controller for the main site.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Main_Controller extends Template_Controller {

    public $auto_render = TRUE;

    // Main template
    public $template = 'layout';

    // Cache instance
    protected $cache;

	// Session instance
	protected $session;

	// Table Prefix
	protected $table_prefix;

    public function __construct()
    {
        parent::__construct();

        // Load cache
        $this->cache = new Cache;

		// Load Session
		$this->session = Session::instance();

        // Load Header & Footer
        $this->template->header  = new View('header');
        $this->template->footer  = new View('footer');

        // In case js doesn't get set in the construct, initialize it here

        $this->template->header->js = '';

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Retrieve Default Settings
		$site_name = Kohana::config('settings.site_name');
			// Prevent Site Name From Breaking up if its too long
			// by reducing the size of the font
			if (strlen($site_name) > 20)
			{
				$site_name_style = " style=\"font-size:21px;\"";
			}
			else
			{
				$site_name_style = "";
			}
        $this->template->header->site_name = $site_name;
		$this->template->header->site_name_style = $site_name_style;
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');
        $this->template->header->api_url = Kohana::config('settings.api_url');

		// Display Contact Tab?
		$this->template->header->site_contact_page = Kohana::config('settings.site_contact_page');

		// Display Help Tab?
		$this->template->header->site_help_page = Kohana::config('settings.site_help_page');

		// Get Custom Pages
		$this->template->header->pages = ORM::factory('page')->where('page_active', '1')->find_all();

        // Get custom CSS file from settings
        $this->template->header->site_style = Kohana::config('settings.site_style');

		// Javascript Header
		$this->template->header->map_enabled = FALSE;
		$this->template->header->validator_enabled = TRUE;
		$this->template->header->treeview_enabled = FALSE;
		$this->template->header->datepicker_enabled = FALSE;
		$this->template->header->photoslider_enabled = FALSE;
		$this->template->header->videoslider_enabled = FALSE;
		$this->template->header->protochart_enabled = FALSE;
		$this->template->header->main_page = FALSE;

		$this->template->header->this_page = "";

		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->_google_analytics($google_analytics);

		// *** Locales/Languages ***
		// First Get Available Locales
		$this->template->header->locales_array = $this->cache->get('locales');

		// Locale form submitted?
		if (isset($_GET['l']) && !empty($_GET['l']))
		{
			$this->session->set('locale', $_GET['l']);
		}
		// Has a locale session been set?
		if ($this->session->get('locale',FALSE))
		{
			// Change current locale
			Kohana::config_set('locale.language', $_SESSION['locale']);
		}
		$this->template->header->l = Kohana::config('locale.language');

		//Set up tracking gif
		if($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_NAME'] != '127.0.0.1'){
			$track_url = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		}else{
			$track_url = 'null';
		}
		$this->template->footer->tracker_url = 'http://tracker.ushahidi.com/track.php?url='.urlencode($track_url).'&lang='.$this->template->header->l.'&version='.Kohana::config('version.ushahidi_version');
        // Load profiler
        // $profiler = new Profiler;

        // Get tracking javascript for stats
        $this->template->footer->ushahidi_stats = $this->_ushahidi_stats();
    }

    public function index()
    {
        $this->template->header->this_page = 'home';
        $this->template->content = new View('main');

		// Map and Slider Blocks
		$div_map = new View('main_map');
		$div_timeline = new View('main_timeline');
			// Filter::map_main - Modify Main Map Block
			Event::run('ushahidi_filter.map_main', $div_map);
			// Filter::map_timeline - Modify Main Map Block
			Event::run('ushahidi_filter.map_timeline', $div_timeline);
		$this->template->content->div_map = $div_map;
		$this->template->content->div_timeline = $div_timeline;

        // Get all active top level categories
        $parent_categories = array();
        foreach (ORM::factory('category')
				->where('category_visible', '1')
				->where('parent_id', '0')
				->find_all() as $category)
        {
            // Get The Children
			$children = array();
			foreach ($category->children as $child)
			{
				$children[$child->id] = array(
					$child->category_title,
					$child->category_color,
					$child->category_image
				);
			}

			// Put it all together
            $parent_categories[$category->id] = array(
				$category->category_title,
				$category->category_color,
				$category->category_image,
				$children
			);
        }
        $this->template->content->categories = $parent_categories;

		// Get all active Layers (KMZ/KML)
		$layers = array();
		$config_layers = Kohana::config('map.layers'); // use config/map layers if set
		if ($config_layers == $layers) {
			foreach (ORM::factory('layer')
					  ->where('layer_visible', 1)
					  ->find_all() as $layer)
			{
				$layers[$layer->id] = array($layer->layer_name, $layer->layer_color,
					$layer->layer_url, $layer->layer_file, $layer->layer_type, $layer->layer_layer);
			}
		} else {
			$layers = $config_layers;
		}
		$this->template->content->layers = $layers;

		// Get all active Shares
		$shares = array();
		foreach (ORM::factory('sharing')
				  ->where('sharing_active', 1)
				  ->where('sharing_type', 1)
				  ->find_all() as $share)
		{
			$shares[$share->id] = array($share->sharing_site_name, $share->sharing_color);
		}
		$this->template->content->shares = $shares;

        // Get Reports
        // XXX: Might need to replace magic no. 8 with a constant
        $this->template->content->total_items = ORM::factory('incident')
            ->where('incident_active', '1')
            ->limit('8')->count_all();
        $this->template->content->incidents = ORM::factory('incident')
            ->where('incident_active', '1')
			->limit('10')
            ->orderby('incident_date', 'desc')
			->with('location')
            ->find_all();

		// Get Default Color
		$this->template->content->default_map_all = Kohana::config('settings.default_map_all');

		// Get Twitter Hashtags
		$this->template->content->twitter_hashtag_array = array_filter(array_map('trim',
			explode(',', Kohana::config('settings.twitter_hashtags'))));

		// Get Report-To-Email
		$this->template->content->report_email = Kohana::config('settings.site_email');

		// Get SMS Numbers
		$phone_array = array();
		$sms_no1 = Kohana::config('settings.sms_no1');
		$sms_no2 = Kohana::config('settings.sms_no2');
		$sms_no3 = Kohana::config('settings.sms_no3');
		if (!empty($sms_no1)) {
			$phone_array[] = $sms_no1;
		}
		if (!empty($sms_no2)) {
			$phone_array[] = $sms_no2;
		}
		if (!empty($sms_no3)) {
			$phone_array[] = $sms_no3;
		}
		$this->template->content->phone_array = $phone_array;


		// Get RSS News Feeds
		$this->template->content->feeds = ORM::factory('feed_item')
			->limit('10')
            ->orderby('item_date', 'desc')
            ->find_all();



        // Get The START, END and most ACTIVE Incident Dates
        $startDate = "";
        $endDate = "";
		$active_month = 0;
		$active_startDate = 0;
		$active_endDate = 0;

		$db = new Database();

        /*
		// First Get The Most Active Month
		$query = $db->query('SELECT incident_date, count(*) AS incident_count FROM '.$this->table_prefix.'incident WHERE incident_active = 1 GROUP BY DATE_FORMAT(incident_date, \'%Y-%m\') ORDER BY incident_count DESC LIMIT 1');
		foreach ($query as $query_active)
		{
			$active_month = date('n', strtotime($query_active->incident_date));
			$active_year = date('Y', strtotime($query_active->incident_date));
			$active_startDate = strtotime($active_year . "-" . $active_month . "-01");
			$active_endDate = strtotime($active_year . "-" . $active_month .
				"-" . date('t', mktime(0,0,0,$active_month,1))." 23:59:59");

                echo $active_startDate.'<br />';
                echo $active_month.'<br />';
		}
        */

        // SIDCE - Time range = last three months
        $active_startDate = strtotime('-1 month');
        $active_endDate = strtotime('now'); //It was modified to included events reported today
        $dropdowns = Incident_Model::getDropdownsDate($active_startDate, $active_endDate);
        $startDate = $dropdowns['s'];
        $endDate = $dropdowns['e']; 

        $this->template->content->div_timeline->startDate = $startDate;
        $this->template->content->div_timeline->endDate = $endDate;


		// get graph data
		// could not use DB query builder. It does not support parentheses yet
		$graph_data = array();
		$all_graphs = Incident_Model::get_incidents_by_interval('month');
		$daily_graphs = Incident_Model::get_incidents_by_interval('day');
		$weekly_graphs = Incident_Model::get_incidents_by_interval('week');
		$hourly_graphs = Incident_Model::get_incidents_by_interval('hour');
		$this->template->content->all_graphs = $all_graphs;
		$this->template->content->daily_graphs = $daily_graphs;

        /****** SIDCE :: START ********/
		//GET impact forms fields, ID = 1
        $impact_fields = ORM::factory('form_field')->where('form_id = 1')->orderby('field_name')->select_list('id','field_name');
		$this->template->content->impact_fields = $impact_fields;

        //Bulletin 
        $bulletin_weeks = array();
        $bulletins = ORM::factory('bulletin')->select('bulletin_start AS s, bulletin_end AS e')->orderby('bulletin_end', 'DESC')->find_all();
        foreach ($bulletins as $bulletin){
            $s = strtotime($bulletin->s);
            $e = strtotime($bulletin->e);

            $t = array('s' => array(date('F', $s), date('d', $s)), 'f' => array(date('F', $e), date('d', $e)));
            $s = date('Y-m-d', $s);
            $e = date('Y-m-d', $e);
            
            $bulletin_weeks[] = compact('s','e','t');
        }

        $this->template->content->bulletin_weeks = $bulletin_weeks;
        /****** SIDCE :: END ********/

		// Javascript Header
		$this->template->header->map_enabled = TRUE;
		$this->template->header->main_page = TRUE;
		$this->template->header->google_api = TRUE;
		$this->template->header->validator_enabled = TRUE;

		// Map Settings
		$clustering = Kohana::config('settings.allow_clustering');
		$marker_radius = Kohana::config('map.marker_radius');
		$marker_opacity = Kohana::config('map.marker_opacity');
		$marker_stroke_width = Kohana::config('map.marker_stroke_width');
		$marker_stroke_opacity = Kohana::config('map.marker_stroke_opacity');

		// pdestefanis - allows to restrict the number of zoomlevels available
		$numZoomLevels = Kohana::config('map.numZoomLevels');
	    $minZoomLevel = Kohana::config('map.minZoomLevel');
       	$maxZoomLevel = Kohana::config('map.maxZoomLevel');

		// pdestefanis - allows to limit the extents of the map
		$lonFrom = Kohana::config('map.lonFrom');
		$latFrom = Kohana::config('map.latFrom');
		$lonTo = Kohana::config('map.lonTo');
		$latTo = Kohana::config('map.latTo');

		$this->template->header->js = new View('main_js');
		$this->template->header->js->json_url = ($clustering == 1) ?
			"json/cluster" : "json";
		$this->template->header->js->marker_radius =
			($marker_radius >=1 && $marker_radius <= 10 ) ? $marker_radius : 5;
		$this->template->header->js->marker_opacity =
			($marker_opacity >=1 && $marker_opacity <= 10 )
			? $marker_opacity * 0.1  : 0.9;
		$this->template->header->js->marker_stroke_width =
			($marker_stroke_width >=1 && $marker_stroke_width <= 5 ) ? $marker_stroke_width : 2;
		$this->template->header->js->marker_stroke_opacity =
			($marker_stroke_opacity >=1 && $marker_stroke_opacity <= 10 )
			? $marker_stroke_opacity * 0.1  : 0.9;

           // pdestefanis - allows to restrict the number of zoomlevels available
		$this->template->header->js->numZoomLevels = $numZoomLevels;
	    $this->template->header->js->minZoomLevel = $minZoomLevel;
	    $this->template->header->js->maxZoomLevel = $maxZoomLevel;

           // pdestefanis - allows to limit the extents of the map
           $this->template->header->js->lonFrom = $lonFrom;
           $this->template->header->js->latFrom = $latFrom;
           $this->template->header->js->lonTo = $lonTo;
           $this->template->header->js->latTo = $latTo;

		$this->template->header->js->default_map = Kohana::config('settings.default_map');
		$this->template->header->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->header->js->latitude = Kohana::config('settings.default_lat');
		$this->template->header->js->longitude = Kohana::config('settings.default_lon');
		$this->template->header->js->graph_data = $graph_data;
		$this->template->header->js->all_graphs = $all_graphs;
		$this->template->header->js->daily_graphs = $daily_graphs;
		$this->template->header->js->hourly_graphs = $hourly_graphs;
		$this->template->header->js->weekly_graphs = $weekly_graphs;
		$this->template->header->js->default_map_all = Kohana::config('settings.default_map_all');
		$this->template->header->js->layers = $layers;

		//
		$this->template->header->js->active_startDate = $active_startDate;
		$this->template->header->js->active_endDate = $active_endDate;

		$myPacker = new javascriptpacker($this->template->header->js , 'Normal', false, false);
		$this->template->header->js = $myPacker->pack();
	}

	/*
	* Ushahidi Stats HTML/JavaScript
    * @return mixed  Return ushahidi stats HTML code.
	*/
	private function _ushahidi_stats( )
	{
		// Make sure cURL is installed
		if (!function_exists('curl_exec')) {
			throw new Kohana_Exception('footer.cURL_not_installed');
			return false;
		}

		$settings = ORM::factory('settings', 1);
		$stat_id = $settings->stat_id;

		if($stat_id == 0) return '';
		$url = 'http://tracker.ushahidi.com/px.php?task=tc&siteid='.$stat_id;

		$curl_handle = curl_init();
		curl_setopt($curl_handle,CURLOPT_URL,$url);
		curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,15); // Timeout set to 15 seconds. This is somewhat arbitrary and can be changed.
		curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1); // Set cURL to store data in variable instead of print
		$buffer = curl_exec($curl_handle);
		curl_close($curl_handle);

		try {
			$tag = @simplexml_load_string($buffer); // This works because the tracking code is only wrapped in one tag
		} catch (Exception $e) {
			// In case the xml was malformed for whatever reason, we will just guess what the tag should be here
			$tag = '<!-- Piwik -->
					<script type="text/javascript">
					var pkBaseURL = (("https:" == document.location.protocol) ? "https://tracker.ushahidi.com/piwik/" : "http://tracker.ushahidi.com/piwik/");
					document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
					</script><script type="text/javascript">
					try {
					  var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$stat_id.');
					  piwikTracker.trackPageView();
					  piwikTracker.enableLinkTracking();
					} catch( err ) {}
					</script><noscript><p><img src="http://tracker.ushahidi.com/piwik/piwik.php?idsite='.$stat_id.'" style="border:0" alt=""/></p></noscript>
					<!-- End Piwik Tag -->
					';
		}

		return $tag;

	}


	/*
	* Google Analytics
	* @param text mixed  Input google analytics web property ID.
    * @return mixed  Return google analytics HTML code.
	*/
	private function _google_analytics($google_analytics = false)
	{
		$html = "";
		if (!empty($google_analytics)) {
			$html = "<script type=\"text/javascript\">
				var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
				document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
				</script>
				<script type=\"text/javascript\">
				var pageTracker = _gat._getTracker(\"" . $google_analytics . "\");
				pageTracker._trackPageview();
				</script>";
		}
		return $html;
	}

} // End Main
