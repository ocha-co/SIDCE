<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles requests for townships
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Alerts Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Township_Controller extends Main_Controller {

	function __construct()
	{
		parent::__construct();
		$this->session = Session::instance();
	}

	public function index()
	{
	}

    /**
    *
    * Return array to populate dropdown givenan city_id
    * @access public
    * @param int $city_id
    */
    public function dropdown($city_id){

		$this->auto_render = false;
        //$this->template = new View('json');
        $ops = array();

        if (!empty($city_id) && is_numeric($city_id)){
            $ops = Township_Model::dropdown($city_id);
        }

		header('Content-type: application/json');
        echo json_encode($ops);
    }
}
