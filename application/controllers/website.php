<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Website controller.
 *
 * @package    ERF
 * @module     Website Controller
 * @author     OCHA Colombia
 * @copyright  (c) 2010 OCHA Colombia
 */
class Website_Controller extends Template_Controller {

    public function __construct(){

        parent::__construct();

        // Makes database object available to all controllers
        $this->db = Database::instance();

        $this->logged_in = Auth::instance()->logged_in();
        
        // Load Header and Footer for all pager
        //$this->template->header = new View('header');
        //$this->template->header->site_name = 'SIHCE - Sistema de Información Humanitario del Cesar';
        //$this->template->footer = new View('footer');

    }

    /**
     * Logout
     * @access public
     */	
    public function logout(){
        Auth::instance()->logout();
        url::redirect('main');
    }

} 
