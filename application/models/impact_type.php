<?php defined('SYSPATH') or die('No direct script access.');

/**
* Impact_Types Table Model
*/

class Impact_Type_Model extends ORM
{
	protected $has_many = array();
	protected $belongs_to = array('impact');
	
	// Database table name
	protected $table_name = 'impact_type';
}
