<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles requests for affected households and farming systems
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

class Aff_Controller extends Main_Controller {

	function __construct()
	{
		parent::__construct();
		$this->auth = new Auth();
		
        // If this is not a moderator
		if (!$this->auth->logged_in('moderator'))
        {
             //url::redirect('loginf');
		}
	}

	public function index()
	{
		// Create new session
		$this->session->create();
		
		$this->template->header->this_page = 'aff';
		$this->template->content = new View('aff');
    }

    public function submit_hh($edit=false)
    {

		$this->template->header->this_page = 'aff_hh';
		$this->template->content = new View('aff_hh');
		$this->template->header->js = new View('aff_hh_js');
        $rows = 11;  //Number of rows to print in one page

		// setup and initialize form field names
		$form = array
		(
            'aff_hh_date' => '',
            'aff_hh_location' => '');

        for ($c=0;$c<$rows;$c++){

            $form['aff_hh_city_id'][] = '';
            $form['aff_hh_township_id'][] = '';
            $form['aff_hh_head_name'][] = '';
            $form['aff_hh_head_doc'][] = '';
            $form['aff_hh_member_name'][] = '';
            $form['aff_hh_member_doc'][] = '';
            $form['aff_hh_sex_f'][] = '';
            $form['aff_hh_sex_m'][] = '';
            $form['aff_hh_owner'][] = 0;
            $form['aff_hh_tenant'][] = 0;
            $form['aff_hh_holder'][] = 0;
            $form['aff_hh_rural_destroyed'][] = 0;
            $form['aff_hh_rural_damaged'][] = 0;
            $form['aff_hh_urban_destroyed'][] = 0;
            $form['aff_hh_urban_damaged'][] = 0;
			
        }
            
        if (!empty($_POST)){
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory($_POST);

            if (!empty($_POST['aff_hh_head_name'])){
                $rows = count($post->type);
                $h = 0;
                for ($r=2;$r<$rows;$r++){
                    if ($post->type[$r] == 'h'){
                        $aff = new Aff_HH_Model();
                        
                        $aff->category_id = $post->category_id;
                        $aff->aff_hh_location = $post->aff_hh_location;
                        $aff->aff_hh_date = $post->aff_hh_date;

                        if (isset($post->city_id[$r])) $aff->city_id = $post->city_id[$r];
                        if (isset($post->township_id[$r])) $aff->township_id = $post->township_id[$r];

                        $aff->aff_hh_head_name = $post->aff_hh_head_name[$r];
                        $aff->aff_hh_head_doc = $post->aff_hh_head_doc[$r];
                        if (!empty($post->aff_hh_sex_f[$r])){
                            $aff->aff_hh_sex = 'f';
                            $aff->aff_hh_age = $post->aff_hh_sex_f[$r];
                        }   
                        if (!empty($post->aff_hh_sex_m[$r])){
                            $aff->aff_hh_sex = 'm';
                            $aff->aff_hh_age = $post->aff_hh_sex_m[$r];
                        }   
                        
                        $aff->aff_hh_owner = (isset($_POST["aff_hh_owner_$h"])) ? 1 : 0;
                        $aff->aff_hh_tenant = (isset($_POST["aff_hh_tenant_$h"])) ? 1 : 0;
                        $aff->aff_hh_holder = (isset($_POST["aff_hh_holder_$h"])) ? 1 : 0;
                        $aff->aff_hh_urban_destroyed = (isset($_POST["aff_hh_urban_destroyed_$h"])) ? 1 : 0;
                        $aff->aff_hh_urban_damaged = (isset($_POST["aff_hh_urban_damaged_$h"])) ? 1 : 0;
                        $aff->aff_hh_rural_destroyed = (isset($_POST["aff_hh_rural_destroyed_$h"])) ? 1 : 0;
                        $aff->aff_hh_rural_damaged = (isset($_POST["aff_hh_rural_damaged_$h"])) ? 1 : 0;

                        $aff->save();
                        $h++;
                    }
                    else{
                       $mem = new Aff_HH_Member_Model();
                       $mem->aff_hh_id = $aff->id;
                       if (!empty($post->aff_hh_member_name[$r]))   $mem->aff_hh_member_name = $post->aff_hh_member_name[$r];
                       if (!empty($post->aff_hh_member_doc[$r]))    $mem->aff_hh_member_doc = $post->aff_hh_member_doc[$r];

                        if (!empty($post->aff_hh_sex_f[$r])){
                            $mem->aff_hh_member_sex = 'f';
                            $mem->aff_hh_member_age = $post->aff_hh_sex_f[$r];
                        }   
                        if (!empty($post->aff_hh_sex_m[$r])){
                            $mem->aff_hh_member_sex = 'm';
                            $mem->aff_hh_member_age = $post->aff_hh_sex_m[$r];
                        }   

                       $mem->save();
                    }
                }
            }
        }

		$this->template->content->form = $form;
		$this->template->content->rows = $rows;
        $cities = array(0 => '');
        $cities += ORM::factory('city')->orderby('city')->select_list('id', 'city');
		$this->template->content->cities = $cities;
		//$this->template->content->townships = array_merge(array(0=>''), ORM::factory('township')->orderby('township')->select_list('id', 'township'));
		$this->template->content->townships = array(0 => '');
        $this->template->content->cats = array_merge(array('' => ''), Category_Model::dropdown_categories());

    }
    
}
