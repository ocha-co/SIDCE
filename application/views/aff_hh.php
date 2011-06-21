<?php 
/**
 * Affected Households submit view page.
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

    <div id="content">
        <p class="t_center">
            Ministerio de ambiente, vivienda y desarrollo territorial<br />
            Viceministerio de vivienda y desarrollo territorial - direccion del sistema habitacional<br />
            Ministerio de agricultura y desarrollo rural<br />
            Viceministerio de agricultura - direccion de desarrollo rural<br />
            Ministerio del interior y de justicia - direccion de prevencion y atencion de desastres<br />
            <b>Censo formato único de registro hogares afectados por situacion de desastre, calamidad o emergencias<br />
            Vivienda urbana y rural afectada a nivel municipal y departamental</b> 
        </p>
        <br /><br />
        <?php print form::open(NULL); ?>
        <table class="aff_table">
            <tr>
                <td colspan="3"><?php echo Kohana::lang('ui_main.event_type')?> *<br />
                    <?php echo form::dropdown('category_id', $cats); ?>
                </td>
                <td colspan="3">
                    <?php echo Kohana::lang('ui_main.aff_hh_label_1')?> *<br />
                    <?php echo form::input('aff_hh_location', $form['aff_hh_location'], ' class="text long"'); ?></td> 
                <td colspan="6">
                    <?php echo Kohana::lang('ui_main.date')?> *<br />
                    <?php echo form::input('aff_hh_date', $form['aff_hh_date'], ' class="text short"'); ?>								
                </td>
            <tr>
               <th rowspan="2"><?php echo Kohana::lang('ui_main.city')?></th> 
               <th rowspan="2"><?php echo Kohana::lang('ui_main.township')?></th>
               <th rowspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_1')?></th>
               <th rowspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_2')?></th>
               <th rowspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_3')?></th>
               <th rowspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_4')?></th>
               <th colspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_5')?></th>
               <th colspan="3"><?php echo Kohana::lang('ui_main.aff_hh_th_6')?></th>
               <th colspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_7')?></th>
               <th colspan="2"><?php echo Kohana::lang('ui_main.aff_hh_th_8')?></th>
            </tr>
            <tr>
               <th>F</th>
               <th>M</th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_9')?></th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_10')?></th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_11')?></th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_12')?></th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_13')?></th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_12')?></th>
               <th><?php echo Kohana::lang('ui_main.aff_hh_th_13')?></th>
            </tr>
            <tr>
                <td>
                    <a href="#" id="new_h">Nuevo Jefe</a> - <a href="#" id="new_m">Nuevo Miembro</a>
                </td>
            </tr>
            <?php
            $c = 0;
            //for ($c=0;$c<$rows;$c++){
                ?>
                <tr class="aff_tr aff_tr_head hide">
                    <td><?php echo form::dropdown(array('name' => 'city_id[]', 'id' => 'city_id'), $cities); ?></td>
                    <td><?php echo form::dropdown(array('name' => 'township_id[]', 'class' => 'township'), $townships); ?></td>
                    <td><?php echo form::input('aff_hh_head_name[]', $form['aff_hh_head_name'][$c], 'class="text"'); ?></td>
                    <td><?php echo form::input('aff_hh_head_doc[]', $form['aff_hh_head_doc'][$c], 'class="text short"'); ?></td>
                    <td class="short" align="center">-----<input type="hidden" name="aff_hh_member_name[]" /></td>
                    <td class="short" align="center">-----<input type="hidden" name="aff_hh_member_doc[]" /></td>
                    <td><?php echo form::input('aff_hh_sex_f[]', $form['aff_hh_sex_f'][$c], 'class="text short sex_age"'); ?></td>
                    <td><?php echo form::input('aff_hh_sex_m[]', $form['aff_hh_sex_m'][$c], 'class="text short sex_age"'); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_owner', $form['aff_hh_owner'][$c]); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_tenant', $form['aff_hh_tenant'][$c]); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_holder', $form['aff_hh_holder'][$c]); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_urban_destroyed', $form['aff_hh_urban_destroyed'][$c]); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_urban_damaged', $form['aff_hh_urban_damaged'][$c]); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_rural_destroyed', $form['aff_hh_rural_destroyed'][$c]); ?></td>
                    <td align="center"><?php echo form::checkbox('aff_hh_rural_damaged', $form['aff_hh_rural_damaged'][$c]); ?>
                    <input type="hidden" name="type[]" value="h" />
                    </td>
                </tr> 
                <tr class="aff_tr aff_tr_member hide">
                    <td class="td_city"><input type="hidden" name="city_id[]"><a href="#" id="rem_m">Borrar Miembro</a></td>
                    <td class="td_township"><input type="hidden" name="township_id[]"></td>
                    <td><?php echo form::input('aff_hh_head_name[]', $form['aff_hh_head_name'][$c], 'class="text no_border"'); ?></td>
                    <td><?php echo form::input('aff_hh_head_doc[]', $form['aff_hh_head_doc'][$c], 'class="text short no_border"'); ?></td>
                    <td><?php echo form::input('aff_hh_member_name[]', $form['aff_hh_member_name'][$c], 'class="text short"'); ?></td>
                    <td><?php echo form::input('aff_hh_member_doc[]', $form['aff_hh_member_doc'][$c], 'class="text short"'); ?></td>
                    <td><?php echo form::input('aff_hh_sex_f[]', $form['aff_hh_sex_f'][$c], 'class="text short sex_age"'); ?></td>
                    <td><?php echo form::input('aff_hh_sex_m[]', $form['aff_hh_sex_m'][$c], 'class="text short sex_age"'); ?>
                    <input type="hidden" name="type[]" value="m" /></td>
                </tr> 
        <?php //} ?>
        <tr class="tr_submit">
            <td colspan="15" align="center">
                <?php echo form::submit(array('name' => 'submit', 'class' => 'btn_submit'), Kohana::lang('ui_main.reports_btn_submit')); ?>
            </td>
        </tr>
        <?php print form::close(); ?>
        </table>
    </div>
