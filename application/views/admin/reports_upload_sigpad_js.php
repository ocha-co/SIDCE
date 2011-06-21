<?php
/**
 * Report submit js file.
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
		$().ready(function() {
			// validate signup form on keyup and submit
			$("#loadCsvForm").validate({
				rules: {
					csv_file: {
						required: true,
                        accept : 'txt|csv'
					},
				},
				messages: {
					csv_file: {
						required: "Seleccione un archivo",
                        accept: 'Seleccione un archivo de extensión válida, txt o csv'
					},
				},
				errorPlacement: function(error, element) {
                    error.insertAfter(element);
				}
			});
		});
		
