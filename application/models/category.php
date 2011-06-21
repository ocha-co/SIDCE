<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Categories of reported Incidents
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Category Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Category_Model extends ORM_Tree
{	
	protected $has_many = array('incident' => 'incident_category', 'category_lang');
	
	// Database table name
	protected $table_name = 'category';
	protected $children = "category";
	
	static function categories($id=NULL,$locale='en_US')
	{
		if($id == NULL){
			$categories = ORM::factory('category')->where('locale',$locale)->find_all();
		}else{
			$categories = ORM::factory('category')->where('id',$id)->find_all(); // Don't need locale if we specify an id
		}
		
		$cats = array();
		foreach($categories as $category) {
			$cats[$category->id]['category_id'] = $category->id;
			$cats[$category->id]['category_title'] = $category->category_title;
			$cats[$category->id]['category_color'] = $category->category_color;
		}
		
		return $cats;
	}
	
    /**
    *
    * Get options array to populate a dropdown
    */
    static function dropdown_categories()
	{
        $cats_p = ORM::factory('category')->select('id, category_title')
                                          ->where('parent_id = 0')
                                          ->orderby('category_title')
                                          ->find_all();
		$cats = array();
		foreach($cats_p as $cat_p) {
			$cats[$cat_p->id] = $cat_p->category_title;

            $categories = ORM::factory('category')->select('id, category_title')->where('parent_id = '.$cat_p->id)->find_all();
            
            foreach($categories as $cat) {
                $cats[$cat->id] = '&nbsp;&nbsp;&nbsp;&nbsp;'.$cat->category_title;
            }
            
		}
		
		return $cats;
	}

    /** 
    * Get categories of incidents in a period
    * @param string $s Start
    * @param string $e End
    * @return array $cats  Array of Categories
    */
    static function getCatsIncidentPeriod($s, $e){

        $cats = array();
        $rs = ORM::factory('incident')->select("DISTINCT category_title AS title, category_color AS color")
            ->join('incident_category AS i_c', 'i_c.incident_id', 'incident.id')
            ->join('category', 'i_c.category_id', 'category.id')
            ->where("incident_date BETWEEN '$s' AND '$e' AND parent_id = 0")
            ->find_all();
        
        foreach ($rs as $r){
            $cats[] = array('title' => $r->title, 'color' => $r->color);
        }

        return $cats;

    }
	
}
