<?php defined('SYSPATH') or die('No direct script access.');

/**
* Impacts Table Model
*/

class Impact_Model extends ORM
{
	protected $has_many = array('impact_type');
	protected $belongs_to = array('incident');
	
	// Database table name
	protected $table_name = 'impact';
}
