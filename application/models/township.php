<?php defined('SYSPATH') or die('No direct script access.');

/**
* Bulletin Table Model
*/

class Township_Model extends ORM
{
	// Database table name
	protected $table_name = 'township';

    /**
    *
    * Return array to populate dropdown givenan city_id
    * @access public
    * @param int $city_id
    */
    public static function dropdown($city_id){

        $towns = array();
        $ts = ORM::factory('township')->where("city_id = $city_id")->orderby('township')->select_list('id','township');
        foreach ($ts as $id => $name){

            $towns[] = compact('id', 'name');
        }

        return $towns;
    }
	
}
