<?php
/**
 * Main view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */
?>

				<!-- main body -->
				<div id="main" class="clearingfix">
					<div id="mainmiddle" class="floatbox withright">
						<!-- right column -->
						<div id="right">
							<?php
							if ($layers)
							{
								?>
								<!-- Layers (KML/KMZ) -->
                                <!--
								<div class="cat-filters clearingfix" style="margin-top:20px;">
									<strong><?php echo Kohana::lang('ui_main.layers_filter');?></strong>
								</div>
								<ul class="category-filters">
									<?php
									foreach ($layers as $layer => $layer_info)
									{
										$layer_name = $layer_info[0];
										$layer_color = $layer_info[1];
										$layer_url = $layer_info[2];
										$layer_file = $layer_info[3];
										$layer_link = (!$layer_url) ?
											url::base().'media/uploads/'.$layer_file :
											$layer_url;
										echo '<li><a href="#" id="layer_'. $layer .'"
										onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
										<div>'.$layer_name.'</div></a></li>';
									}
									?>
								</ul>
                                -->
								<!-- /Layers -->
								<?php
							}
							?>
                            <div id="right-box">
                                <div class="inner">
                                    <div class="content-block-left">
                                        <div class="img_title left"><?php echo html::image('themes/default/img/events_list.png'); ?></div>
                                        <div>
                                            <h5><?php  echo Kohana::lang('ui_main.incidents_listed'); ?></h5>
                                        </div>
                                        <div>
                                            <?php  echo Kohana::lang('ui_main.incidents_listed_info'); ?>
                                            <div class="right">
                                                <a href="<?php echo url::base() . 'reports/' ?>"><?php echo Kohana::lang('ui_main.view_more'); ?></a>
                                            </div>
                                            <div class="right">
                                                <?php echo html::image('themes/default/img/more.png'); ?>&nbsp;
                                            </div>
                                        </div>
                                        <br /> 
                                        <table class="table-list">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="title"><?php echo Kohana::lang('ui_main.title'); ?></th>
                                                    <th scope="col" class="location"><?php echo Kohana::lang('ui_main.location'); ?></th>
                                                    <th scope="col" class="date"><?php echo Kohana::lang('ui_main.date'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if ($total_items == 0)
                                                {
                                                ?>
                                                <tr><td colspan="3">No Reports In The System</td></tr>

                                                <?php
                                                }
                                                foreach ($incidents as $incident)
                                                {
                                                    $m = substr(Kohana::lang('ui_main.'.date('F', strtotime($incident->incident_date))), 0,3);	
                                                	$incident_id = $incident->id;
                                                    $incident_title = text::limit_chars($incident->incident_title, 40, '...', True);
                                                    $incident_date = $m.' '.date('j Y', strtotime($incident->incident_date));
                                                    $incident_location = $incident->location->location_name;
                                                ?>
                                                <tr>
                                                    <td><a href="#" onclick="showIncidentDetail('<?php echo url::base().'reports/view_modal/'.$incident_id; ?>')"> <?php echo ucfirst(strtolower($incident_title)); ?></a></td>
                                                    <td><?php echo $incident_location ?></td>
                                                    <td><?php echo $incident_date; ?></td>
                                                </tr>
                                                <?php
                                                }
                                                ?>


                                            </tbody>
                                        </table>
                                        <br /><br />
                                        <div class="img_title left" style="margin-left: 13px;"><?php echo html::image('themes/default/img/timeline_icon.png'); ?></div>
                                        <div>
                                            <h5><?php  echo Kohana::lang('ui_main.reports_timeline'); ?></h5>
                                        </div>
                                        <div>
                                            <?php  echo Kohana::lang('ui_main.reports_timeline_info'); ?>
                                            <div class="right">
                                                <a href="#" class="timeline_more"><?php echo Kohana::lang('ui_main.view_more'); ?></a>
                                            </div>
                                            <div class="right">
                                                <?php echo html::image('themes/default/img/more.png'); ?>&nbsp;
                                            </div>
                                        </div>
                                        <div class="clear"></div><br />
                                        <div id="timeline_div">
                                            <?php echo $div_timeline; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                                
							<!-- additional content -->
							<!--
							<?php
							if (Kohana::config('settings.allow_reports'))
							{
								?>
								<div class="additional-content">
									<h5><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5>
									<ol>
										<?php if (!empty($phone_array)) 
										{ ?><li><?php echo Kohana::lang('ui_main.report_option_1')." "; ?> <?php foreach ($phone_array as $phone) {
											echo "<strong>". $phone ."</strong>";
											if ($phone != end($phone_array)) {
												echo " or ";
											}
										} ?></li><?php } ?>
										<?php if (!empty($report_email)) 
										{ ?><li><?php echo Kohana::lang('ui_main.report_option_2')." "; ?> <a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></li><?php } ?>
										<?php if (!empty($twitter_hashtag_array)) 
													{ ?><li><?php echo Kohana::lang('ui_main.report_option_3')." "; ?> <?php foreach ($twitter_hashtag_array as $twitter_hashtag) {
										echo "<strong>". $twitter_hashtag ."</strong>";
										if ($twitter_hashtag != end($twitter_hashtag_array)) {
											echo " or ";
										}
										} ?></li><?php
										} ?><li><a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a></li>
									</ol>
		
								</div>
							<?php } ?>-->
							<!-- / additional content -->
							
							<?php
							// Action::main_sidebar - Add Items to the Entry Page Sidebar
							Event::run('ushahidi_action.main_sidebar');
							?>
					
						</div>
						<!-- / right column -->
							
                        <div id="div-category-filters" class="left">
							<!-- category filters -->
							<div class="inner">
							<!--
							<div class="cat-filters clearingfix">
								<strong><?php echo Kohana::lang('ui_main.category_filter');?></strong>
							</div>-->
						
							<ul class="category-filters">
								<li><a class="active" id="cat_0" href="#"><div class="swatch" style="background-color:#<?php echo $default_map_all;?>"></div><div class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></div></a></li>
								<?php
									foreach ($categories as $category => $category_info)
									{
										$category_title = $category_info[0];
										$category_color = $category_info[1];
										$category_image = '';
										$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
										if($category_info[2] != NULL && file_exists('media/uploads/'.$category_info[2])) {
											$category_image = html::image(array(
												'src'=>'media/uploads/'.$category_info[2],
												'style'=>'float:left;padding-right:5px;'
												));
											$color_css = '';
										}
										echo '<li><div '.$color_css.'>'.$category_image.'</div><a href="#" id="cat_'. $category .'"><div class="category-title">'.$category_title.'</div></a>';
										// Get Children
										echo '<div class="hide" id="child_'. $category .'"><ul>';
										foreach ($category_info[3] as $child => $child_info)
										{
											$child_title = $child_info[0];
											$child_color = $child_info[1];
											$child_image = '';
											$color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
											if($child_info[2] != NULL && file_exists('media/uploads/'.$child_info[2])) {
												$child_image = html::image(array(
													'src'=>'media/uploads/'.$child_info[2],
													'style'=>'float:left;padding-right:5px;'
													));
												$color_css = '';
											}
											echo '<li><div '.$color_css.'>'.$child_image.'</div><a href="#" id="cat_'. $child .'"><div class="category-title">'.$child_title.'</div></a></li>';
										}
										echo '</ul></div></li>';
									}
								?>
                                </ul>
                            </div>
                            <!-- # last weeks humanitarian bulletin -->
                            <div id="left_block">
                               <div id="left_block_title"><?php echo Kohana::lang('ui_main.humanitarian_bulletin'); ?></div> 
                               <div id="bulletin_list">
                                <ul class="ul_style_none">
                                    <?php 
                                    foreach ($bulletin_weeks as $w => $week){
                                        $s = $week['s'];
                                        $e = $week['e'];
                                        $t = $week['t'];
                                        $class = ($w > 2) ? 'hide' : '';
                                        $t = Kohana::lang('ui_main.'.$t['s'][0]).' '.$t['s'][1].' - '.Kohana::lang('ui_main.'.$t['f'][0]).' '.$t['f'][1];
                                        echo "<li class='$class'>&raquo;&nbsp;<a href='".url::base()."reports/bulletin/$s/$e'>$t</a></li>";
                                    }
                                    ?>
                                    <li>&nbsp;</li>
                                    <li>
                                        <div class="right">
                                            <a href="#" class="bulletin_more"><?php echo Kohana::lang('ui_main.view_more'); ?>...</a>
                                        </div>
                                    </li>
                                </ul>
                                </div>
                            </div>
                            <div id="left_block">
                                <div id="left_block_title"><?php echo Kohana::lang('ui_main.chart_pie_title'); ?></div>
                                <div id="pie_chart_zoom"><?php echo html::image('themes/default/img/pie.png'); ?></div>
                            </div>
                            <div id="pie_chart_div"><div id="pie_chart"></div></div>
						</div>
					
						<!-- content column -->
						<div id="content" class="clearingfix">
							<div class="floatbox">
								<!-- filters -->
								<div class="filters clearingfix">
                                    <div>
                                        <ul>
                                            <!--<li class="note"><?php echo Kohana::lang('ui_main.filters_note'); ?></li>-->
                                            <li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.number').' '.Kohana::lang('ui_main.reports'); ?></span></a></li>
                                            <li><a id="media_99" href="#"><span><?php echo Kohana::lang('ui_main.impact_filter'); ?></span></a></li>
                                            <!--
                                            <li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
                                            <li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
                                            <li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
                                            <li><a id="media_0" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
                                            -->
                                        </ul>
                                    </div>
                                </div>
                                <div id="impact_filters">
                                    <ul>
                                        <li><?php echo Kohana::lang('ui_main.filters_impact'); ?></li>
                                        <li>
                                        <?php
                                        echo form::dropdown(array('id' => 'impact_field_id'),$impact_fields, array('3'));
                                        ?>
                                        </li>
                                    </ul>
								</div>
								<!-- / filters -->
								
								<?php								
								// Map and Timeline Blocks
								echo $div_map;
								?>
							</div>
						</div>
						<!-- / content column -->
				
					</div>
				<!-- / main body -->
			
				<!-- content -->
				<!--<div class="content-container">-->
			
					
					
<?php
/*
 *					<!-- site footer -->
 *					<div class="site-footer">
 *
 *						<h5>Site Footer</h5>
 *						Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Mauris porta. Sed eget nisi. Fusce rhoncus lorem ac erat. Maecenas turpis tellus, volutpat quis, sodales et, consectetuer ac, est. Nullam sed est sed augue vestibulum condimentum. In tellus. Integer luctus odio eu arcu. Pellentesque imperdiet felis eu tortor. Morbi ante dui, iaculis id, vulputate sit amet, venenatis in, turpis. Fusce in risus.
 *
 *					</div>
 *					<!-- / site footer -->
*/
?>
			
				</div>
				<!-- content -->
		
			</div>
		</div>
		<!-- / main body -->

	</div>
	<!-- / wrapper -->
