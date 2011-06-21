<?php defined('SYSPATH') or die('No direct script access.');

/**
* Aid_Nationals Table Model
*/

class Aff_HH_Model extends ORM
{
	protected $has_many = array('aff_hh_member');
	
    // Database table name
	protected $table_name = 'aff_hh';
}
