<?php defined('SYSPATH') or die('No direct script access.');

/**
* Aid_Nationals Table Model
*/

class Aff_HH_Member_Model extends ORM
{
	protected $belongs_to = array('aff_hh');
	
    // Database table name
	protected $table_name = 'aff_hh_member';
}
