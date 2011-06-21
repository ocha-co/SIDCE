<?php 
/**
 * Reports view page.
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


<table align="center">
    <tr>
        <td>
            <table>
                <tr>
                    <td valign="top">
                        <table style="height:70px">
                            <tr><td id="bulletin_title"><?php echo $title ?></td></tr>
                            <tr><td><?php echo $copy ?></td></tr>
                            <tr><td><?php echo html::image('themes/default/img/pdf.png') ?>&nbsp;<?php echo html::anchor($pdf_url, Kohana::lang('ui_main.download_pdf_version'))?></td></tr>
                        </table>
                    </td>
                    <td><div id="div_cat"></div></td>
                    <td><div id="div_i_c"></div></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellspacing="20">
                <tr>
                    <td><div id="bulletin_map"></div></td>
                    <td>
                        <table>
                            <tr><td id="bulletin_description"><?php echo $description ?></td></tr>
                            <tr><td><div id="div_trend"></div></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

