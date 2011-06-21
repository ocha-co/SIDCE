<?php 
/**
 * Reports upload view page.
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

<div class="bg">
	<h2><?php print $title; ?> <span></span><a href="<?php print url::site() ?>admin/reports/download"><?php echo Kohana::lang('ui_main.download_reports');?></a>
        <a href="<?php print url::site() ?>admin/reports"><?php echo Kohana::lang('ui_main.view_reports');?></a>
        <a href="<?php print url::site() ?>admin/reports/edit"><?php echo Kohana::lang('ui_main.create_report');?></a>
        <a href="<?php print url::site() ?>admin/reports/upload_sigpad"><?php echo Kohana::lang('ui_main.upload_reports_sigpad');?></a>
        </h2>
	<!-- report-form -->
	<div class="bulletin-form">
		<?php
		echo form::open(NULL, array('id' => 'bulletinForm', 'name' => 'bulletinForm'));

		if ($form_error) {
		?>
			<!-- red-box -->
			<div class="red-box">
				<h3><?php echo Kohana::lang('ui_main.error');?></h3>
				<ul>
                <?php
				foreach ($errors as $error_item => $error_description)
				{
					print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
				}
				?>
				</ul>
			</div>
		<?php
		}
		?>
		<!-- column -->
		<div class="upload_container">
            <p><?php echo Kohana::lang('ui_admin.bulletin_detail_1');?>.</p>
            <h3><?php echo Kohana::lang('ui_main.please_note');?></h3>
            <ul>
                <li><?php echo Kohana::lang('ui_admin.bulletin_detail_2');?>.</li>
            </ul>
            <?php
            wysiwyg::render(array('description'));
            ?>
			<div class="row">
                <h4><?php echo Kohana::lang('ui_main.period')?> </h4>
                <?php echo form::dropdown('week_date', $week_date_op, $form['week_date']);?>
                &nbsp;
                <?php echo html::anchor(url::site().'admin/reports/bulletin/', Kohana::lang('ui_admin.edit_action'), array('id' => 'a_edit')) ?>
            </div>
			<div class="row">
                <h4><?php echo Kohana::lang('ui_admin.analysis')?> </h4>
                <?php echo form::textarea('description', $form['description']); ?>
            </div>
            <div class="btns">
                <ul>
                    <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_main.save'));?></a></li>
                </ul>
            </div>						
		</div>
	</div>
	<?php 
    echo form::hidden('sigpad_submit',1);
    echo form::close(); 
    ?>
</div>
