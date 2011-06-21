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
				<div id="main" class="clearingfix">
					<div id="mainmiddle" class="floatbox withright">
                        <div id="tabs">
                            <ul>
                                <li><a href="#tabs-1">General</a></li>
                                <li><a href="#tabs-2">Afectaci&oacute;n</a></li>
                                <li><a href="#tabs-3">Apoyo Fondo Nal. Calamidades</a></li>
                                <li><a href="#tabs-4"><?php echo kohana::lang('ui_main.incidents_nearby') ?></a></li>
                                <?php
                                if( count($incident_photos) > 0 ) {
                                    echo '<li><a href="#tabs-5">'.kohana::lang('ui_main.image').'</a></li>';
                                } 
                                if( count($incident_videos) > 0 ) {
                                    echo '<li><a href="#tabs-6">'.kohana::lang('ui_main.video').'</a></li>';
                                } 
                                ?>
                            </ul>
                        
                            <!-- Start general info -->
                            <div id="tabs-1" class="reports">
                                <div class="report-details report-details-modal">
                                    <div class="verified <?php
                                    if ($incident_verified == 1){
                                        echo " verified_yes";
                                    }
                                    ?>">
                                        <?php
                                        echo ($incident_verified == 1) ? kohana::lang('ui_main.verified') : kohana::lang('ui_main._no_verified');
                                        ?>
                                    </div>
                                    <h2><?php
                                    echo $incident_title;
                                    
                                    // If Admin is Logged In - Allow For Edit Link
                                    if ($logged_in){
                                        echo " [&nbsp;<a href=\"".url::base()."admin/reports/edit/".$incident_id."\">".kohana::lang('ui_main.edit') ."</a>&nbsp;]";
                                    }
                                    ?></h2>
                                    <ul class="details">
                                        <li>
                                            <small><?php echo kohana::lang('ui_main.description') ?></small>
                                            <?php echo $incident_description; ?>
                                            <br />
                                            <div class="credibility">
                                                <?php echo kohana::lang('ui_main.credibility'); ?>:
                                                <a href="javascript:rating('<?php echo $incident_id; ?>','add','original','oloader_<?php echo $incident_id; ?>')"><img id="oup_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-up.jpg" alt="UP" title="UP" border="0" /></a>&nbsp;
                                                <a href="javascript:rating('<?php echo $incident_id; ?>','subtract','original')"><img id="odown_<?php echo $incident_id; ?>" src="<?php echo url::base() . 'media/img/'; ?>thumb-down.jpg" alt="DOWN" title="DOWN" border="0" /></a>&nbsp;
                                                <a href="" class="rating_value" id="orating_<?php echo $incident_id; ?>"><?php echo $incident_rating; ?></a>
                                                <a href="" id="oloader_<?php echo $incident_id; ?>" class="rating_loading" ></a>
                                            </div>
                                        </li>
                                        <li>
                                            <small><?php echo kohana::lang('ui_main.location') ?></small>
                                            <?php echo $incident_location; ?>
                                        </li>
                                        <li>
                                            <small><?php echo kohana::lang('ui_main.date') ?></small>
                                            <?php echo $incident_date; ?>
                                        </li>
                                        <li>
                                            <small><?php echo kohana::lang('ui_main.time') ?></small>
                                            <?php echo $incident_time; ?>
                                        </li>
                                        <li>
                                            <small><?php echo kohana::lang('ui_main.category') ?></small>
                                            <?php
                                                foreach($incident_category as $category) 
                                                { 
                                                    echo "<a href=\"".url::base()."reports/?c=".$category->category->id."\">" .
                                                    $category->category->category_title . "</a>&nbsp;&nbsp;&nbsp;";
                                                }
                                            ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="location location-modal">
                                    <div class="incident-notation clearingfix">
                                        <ul>
                                            <li><img align="absmiddle" alt="Incident" src="<?php echo url::base(); ?>media/img/incident-pointer.jpg"/><?php echo kohana::lang('ui_main.incident') ?></li>
                                            <li><img align="absmiddle" alt="Nearby Incident" src="<?php echo url::base(); ?>media/img/nearby-incident-pointer.jpg"/> <?php echo kohana::lang('ui_main.nearby_incident') ?></li>
                                        </ul>
                                    </div>
                                    <div class="report-map report-map-modal">
                                        <div class="map-holder" id="map"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <!-- End General info -->

                            <!-- Start Impact -->
                            <div id="tabs-2">
                                <table class="tabs_table" align="center" cellspacing="1">
                                    <?php
                                    if (count($incident_impact) > 0){
                                        
                                        echo '<tr class="title">
                                            <th>Item</th>
                                            <th>'.kohana::lang('ui_main.description').'</th>
                                        </tr>';

                                        foreach ($incident_impact as $t => $i){
                                            if (!empty($i))   echo "<tr><td>$t</td><td>$i</td></tr>";
                                        }
                                    }
                                    else    echo "<tr><td align='center'>No hay Informaci&oacute;n</td></tr>";
                                    ?>
                                </table>
                            </div>
                            <!-- End Impact -->
                            
                            <!-- Start Aid -->
                            <div id="tabs-3">
                                <table class="tabs_table" align="center">
                                    <?php
                                    if (count($incident_aid) > 0){
                                        
                                        echo '<tr>
                                            <th>Item</th>
                                            <th>'.kohana::lang('ui_main.description').'</th>
                                        </tr>';

                                        foreach ($incident_aid as $t => $aid){
                                            echo "<tr><td>$t</td><td>$aid</td></tr>";
                                        }
                                    }
                                    else    echo "<tr><td align='center'>No hay Informaci&oacute;n</td></tr>";
                                    ?>
                                </table>
                            </div>
                            <!-- End Aid -->

                            <div id="tabs-4">
                                <table class="tabs_table" align="center">
                                    <tr>
                                        <th><?php echo kohana::lang('ui_main.title') ?></th>
                                        <th><?php echo kohana::lang('ui_main.incident_location') ?></th>
                                        <th><?php echo kohana::lang('ui_main.date') ?></th>
                                    </tr>
                                    <?php
                                        foreach($incident_neighbors as $neighbor)
                                        {
                                            echo "<tr>";
                                            echo "<td><a href=\"" . url::base(); 
                                            echo "reports/view/" . $neighbor->id . "\">" . $neighbor->incident_title . "</a></td>";
                                            echo "<td>" . $neighbor->location->location_name . "</td>";
                                            echo "<td>" . date('M j Y', strtotime($neighbor->incident_date)) . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                </table>
                            </div>
				<?php
                if( count($incident_photos) > 0 ) {
                    ?>
                    <!-- start images -->
                    <div id="tabs-5" class="photos">
                    <?php
                    foreach ($incident_photos as $photo) {
                        $thumb = str_replace(".","_t.",$photo);
                        $prefix = url::base()."media/uploads";
                        echo("<a class='photothumb' rel='lightbox-group1' href='$prefix/$photo'><img src='$prefix/$thumb'/></a> ");
                    }
                }
                ?>
					
                    <!-- start videos -->
                    <?php
                        if( count($incident_videos) > 0 ) 
                        {
                    ?>
                    <div id="tabs-6" class="report-description">
                        <div class="block-bg">
                            <div class="slider-wrap">
                                <div id="slider1" class="csw">
                                    <div class="panelContainer">

                                        <?php
                                            // embed the video codes
                                            foreach( $incident_videos as $incident_video) {
                                        ?>
                                        <div class="panel">
                                            <div class="wrapper">
                                                <p>
                                                    <?php
                                                        $videos_embed->embed($incident_video,'');
                                                    ?>	
                                                <p>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        </div><!-- .panelContainer -->
                                    </div><!-- #slider1 -->
                                </div><!-- .slider-wrap -->
                            </div>
                        </div>
                    <?php } ?>
                    <!-- end incident block <> start other report -->
                </div>
                <br />
                <!-- end incident block <> start other report -->
            </div>
			</div>
		</div>
	</div>
