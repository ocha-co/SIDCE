<?php
/**
 * Layers js file.
 * 
 * Handles javascript stuff related to layers controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Layers JS View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
// Layers JS
function fillFields(id, layer_name, layer_url, layer_color, layer_file_old, layer_type, layer_layer)
{
	$("#layer_id").attr("value", unescape(id));
	$("#layer_name").attr("value", unescape(layer_name));
	$("#layer_url").attr("value", unescape(layer_url));
	$("#layer_color").attr("value", unescape(layer_color));
	$("#layer_file_old").attr("value", unescape(layer_file_old));
	$("#layer_type").attr("value", unescape(layer_type));
	$("#layer_layer").attr("value", unescape(layer_layer));
}

function layerAction ( action, confirmAction, id )
{
	var statusMessage;
	var answer = confirm('Are You Sure You Want To ' 
		+ confirmAction)
	if (answer){
		// Set Category ID
		$("#layer_id_action").attr("value", id);
		// Set Submit Type
		$("#action").attr("value", action);		
		// Submit Form
		$("#layerListing").submit();
	}
}
