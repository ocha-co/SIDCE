<?php
/**
 * Bulletin js file.
 *
 * Handles javascript stuff related to edit report function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
$(function(){
    // Edit link
    $('#a_edit').click(function(){
        window.location.href =  $('#a_edit').attr('href') + $('#week_date').val();
        return false; //Avoid href=#
    });

    //Save
    $('a.btn_save').click(function(){ $('#bulletinForm').submit() });
});
