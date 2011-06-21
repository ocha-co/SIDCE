<?php defined('SYSPATH') or die('No direct script access.');

/**
* Aid_Nationals Table Model
*/

class Aid_National_Model extends ORM
{
	protected $has_many = array('aid_national_type');
	protected $belongs_to = array('incident');
	
	// Database table name
	protected $table_name = 'aid_national';
}
