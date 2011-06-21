<?php
/**
 * Report js file.
 *
 * Handles javascript stuff related to report submit function.
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

    // Date Dropdowns actions
    var url_base = '<?php echo url::base() ?>reports/index/0/?';
    $('#startDate').change(function(){
        location.href = url_base + 's=' +$(this).val() + '&e=' + $('#endDate').val();
    });
    
    $('#endDate').change(function(){
        location.href = url_base + 's=' + $('#startDate').val() + '&e=' + $(this).val();
    });

    // Download XLS anchor
    $('#a_reports_xls').attr('href', '<?php echo url::base() ?>reports/xls/' + $('#startDate').val() + '/' + $('#endDate').val());

});
