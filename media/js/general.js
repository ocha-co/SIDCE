/**
 * General functions to use in all around site
 * 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

/*
* Show Incident detail in home right list
*
*/
function showIncidentDetail(url){
    dialog(url,900,650,true);
}

/*
* Create a jquery dialog
*/
function dialog(url,w,h,load){
	if(!$('#dialog').size())
	    $('body').append('<div id="dialog"></div>')

    $("#dialog").dialog({
        height: h,
        width: w,
        zIndex : 5000,
        autoOpen: false,
    }).load(url,function(){ if(load)    onLoad();});

    $("#dialog").dialog('open');
}

function onLoad(){
    if($('#tabs').size())   $('#tabs').tabs(); 
    //start(); 
}
function casa(){
    alert('a');
}
/*
* Show humanitarian bulletin in home right list
*/
function showBulletin(url){
    dialog(url,900,650,true);
}
