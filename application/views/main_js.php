<?php
/**
 * Main cluster js file.
 * 
 * Server Side Map Clustering
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
		// Map JS
		
		// Map Object
		var map;
		// Selected Category
		var currentCat;
		// Selected Layer
		var thisLayer;
		// WGS84 Datum
		var proj_4326 = new OpenLayers.Projection('EPSG:4326');
		// Spherical Mercator
		var proj_900913 = new OpenLayers.Projection('EPSG:900913');
		// Change to 1 after map loads
		var mapLoad = 0;
		// /json or /json/cluster depending on if clustering is on
		var default_json_url = "<?php echo $json_url ?>";
		// Current json_url, if map is switched dynamically between json and json_cluster
		var json_url = default_json_url;
		
		var baseUrl = "<?php echo url::base(); ?>";
		var longitude = <?php echo $longitude; ?>;
		var latitude = <?php echo $latitude; ?>;
		var defaultZoom = <?php echo $default_zoom; ?>;
		var markerRadius = <?php echo $marker_radius; ?>;
		var markerOpacity = "<?php echo $marker_opacity; ?>";

		var gMarkerOptions = {baseUrl: baseUrl, longitude: longitude,
		                     latitude: latitude, defaultZoom: defaultZoom,
							 markerRadius: markerRadius,
							 markerOpacity: markerOpacity,
							 protocolFormat: OpenLayers.Format.GeoJSON};

		jQuery(function() {
			var map_layer;
			markers = null;
			var catID = '';
			OpenLayers.Strategy.Fixed.prototype.preload=true;
			
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			var options = {
				//units: "mi",
				numZoomLevels: 16,
				controls:[],
                projection: new OpenLayers.Projection("EPSG:900913"),
				displayProjection: proj_4326,
                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                                     20037508.34, 20037508.34),
				eventListeners: {
						"zoomend": mapMove
				    }
				};
			map = new OpenLayers.Map('map', options);
//			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
			/*
			- Select A Mapping API
			- Live/Yahoo/OSM/Google
			- Set Bounds					
			*/
			var default_map = <?php echo $default_map; ?>;
			
				
			if (default_map == 2)
			{
				map_layer = new OpenLayers.Layer.VirtualEarth("virtualearth", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			} 
			else if (default_map == 3)
			{
				map_layer = new OpenLayers.Layer.Yahoo("yahoo", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			else if (default_map == 4)
			{
				map_layer = new OpenLayers.Layer.OSM.Mapnik("openstreetmap", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			} 
			else if (default_map == 5)
			{
				map_layer = new OpenLayers.Layer.Google("Google Satellite", {
					type: G_SATELLITE_MAP,
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			} 
			else if (default_map == 6)
			{
				map_layer = new OpenLayers.Layer.OSM.Mapnik("Open Street Maps Satellite", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});

			} 
			else
			{  // default is 1
				map_layer = new OpenLayers.Layer.Google("Google Streets", {
					sphericalMercator: true,
					maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)
					});
			}
			
            map.addLayer(map_layer);
            
            var layer_tmp;
            <?php
            // SIDCE
            if ($layers)
            {
                foreach ($layers as $layer => $layer_info)
                {
                    $layer_name = $layer_info[0];
                    $layer_url = $layer_info[2];
                    $layer_type = $layer_info[4];
                    $layer_layer = $layer_info[5];
                    
                    echo "
                    //custom layer
                    layer_tmp = new OpenLayers.Layer.WMS('$layer_name', '$layer_url', 
                                {   layers: '$layer_layer', 
                                    format: 'image/png',
                                    request: 'getmap' , 
                                    transparent: true 
                                },
                                {
                                    isBaseLayer: false,
                                    opacity: 0.5
                                }
                                ); 
                    map.addLayer(layer_tmp);
                    ";

                }
            }
            ?>

			// Add Controls
			map.addControl(new OpenLayers.Control.Navigation());
			map.addControl(new OpenLayers.Control.PanZoomBar());
			map.addControl(new OpenLayers.Control.MousePosition(
					{ div: 	document.getElementById('mapMousePosition'), numdigits: 5 
				}));    
			map.addControl(new OpenLayers.Control.Scale('mapScale'));
            map.addControl(new OpenLayers.Control.ScaleLine());
			map.addControl(new OpenLayers.Control.LayerSwitcher());
			
			// display the map projection
			document.getElementById('mapProjection').innerHTML = map.projection;
				
			gMap = map;
			
			// Category Switch Action
			$("a[id^='cat_']").click(function()
			{
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[id^='cat_']").removeClass("active"); // Remove All active
				$("[id^='child_']").hide(); // Hide All Children DIV
				$("#cat_" + catID).addClass("active"); // Add Highlight
				$("#child_" + catID).show(); // Show children DIV
				$(this).parents("div").show();
				
				currentCat = catID;
				$("#currentCat").val(catID);
				// setUrl not supported with Cluster Strategy
				//markers.setUrl("<?php echo url::site(); ?>" json_url + '/?c=' + catID);
				
				// Destroy any open popups
				onPopupClose();
				
				// Get Current Zoom
				currZoom = map.getZoom();
				
				// Get Current Center
				currCenter = map.getCenter();
				
				graphData = dailyGraphData[0][catID];
				gCategoryId = catID;
				var startTime = new Date($("#startDate").val() * 1000);
				var endTime = new Date($("#endDate").val() * 1000);
				gTimeline = $.timeline({categoryId: catID, startTime: startTime, endTime: endTime,
					graphData: graphData,
					mediaType: gMediaType
				});
				gTimeline.plot();
				
				addMarkers(catID, $("#startDate").val(), $("#endDate").val(), currZoom, currCenter, gMediaType);
				
				return false;
			});
			
			// Sharing Layer[s] Switch Action
			$("a[id^='share_']").click(function()
			{
				var shareID = this.id.substring(6);
				
				if ( $("#share_" + shareID).hasClass("active") )
				{
					share_layer = map.getLayersByName("Share_"+shareID);
					if (share_layer)
					{
						for (var i = 0; i < share_layer.length; i++)
						{
							map.removeLayer(share_layer[i]);
						}
					}
					$("#share_" + shareID).removeClass("active");
					
				} 
				else
				{
					$("#share_" + shareID).addClass("active");
					
					// Get Current Zoom
					currZoom = map.getZoom();

					// Get Current Center
					currCenter = map.getCenter();
					
					// Add New Layer
					addMarkers('', '', '', currZoom, currCenter, '', shareID, 'shares');
				}
			});

			// Exit if we don't have any incidents
			if (!$("#startDate").val())
			{
				map.setCenter(new OpenLayers.LonLat(<?php echo $longitude ?>, <?php echo $latitude ?>), 5);
				return;
			}
			
			//Accessible Slider/Select Switch
			$("select#startDate, select#endDate").selectToUISlider({
				labels: 4,
				labelSrc: 'text',
				sliderOptions: {
					change: function(e, ui)
					{
						var startDate = $("#startDate").val();
						var endDate = $("#endDate").val();
						var currentCat = gCategoryId;
						
						// Get Current Category
						currCat = currentCat;
						
						// Get Current Zoom
						currZoom = map.getZoom();
						
						// Get Current Center
						currCenter = map.getCenter();
						
						// If we're in a month date range, switch to
						// non-clustered mode. Default interval is monthly
						var startTime = new Date(startDate * 1000);
						var endTime = new Date(endDate * 1000);
						if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 32)
						{
							json_url = "json";
						} 
						else
						{
							json_url = default_json_url;
						}
						
						// Refresh Map
						addMarkers(currCat, startDate, endDate, '', '', gMediaType);
						
						refreshGraph(startDate, endDate);

					}
				}
			});
		
			// Graph
			allGraphData = [<?php echo $all_graphs ?>];
			dailyGraphData = [<?php echo $daily_graphs ?>];
			weeklyGraphData = [<?php echo $weekly_graphs ?>];
			hourlyGraphData = [<?php echo $hourly_graphs ?>];

            // SIHCE
            /*
			var startTime = <?php echo $active_startDate ?>;	// Default to most active month
			var endTime = <?php echo $active_endDate ?>;		// Default to most active month
					
			// get the closest existing dates in the selection options
			var options = $('#startDate > optgroup > option').map(function()
			{
				return $(this).val(); 
			});
			startTime = $.grep(options, function(n,i)
			{
			  return n >= ('' + startTime) ;
			})[0];
			
			options = $('#endDate > optgroup > option').map(function()
			{
				return $(this).val(); 
			});
			endTime = $.grep(options, function(n,i)
			{
			  return n >= ('' + endTime) ;
			})[0];

            */

            // SIHCE
            var startTime = $("#startDate").val();
            var endTime = $("#endDate").val();
			
			gCategoryId = '0';
			gMediaType = 0;
			//$("#startDate").val(startTime);
			//$("#endDate").val(endTime);
			
			// Initialize Map
			addMarkers(gCategoryId, startTime, endTime, '', '', gMediaType);
			refreshGraph(startTime, endTime);
			
			// Media Filter Action
			$('.filters li a').click(function()
			{
				var startTimestamp = $("#startDate").val();
				var endTimestamp = $("#endDate").val();
				var startTime = new Date(startTimestamp * 1000);
				var endTime = new Date(endTimestamp * 1000);
				gMediaType = parseFloat(this.id.replace('media_', '')) || 0;
				// Get Current Zoom
				currZoom = map.getZoom();
					
				// Get Current Center
				currCenter = map.getCenter();
				
				// Refresh Map
				addMarkers(currentCat, startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);

                // SIDCE
                // Show impact filters
                if (gMediaType == 99)
                    $("#impact_filters").effect("highlight", {}, 800);
                else
				    $("#impact_filters").hide();

				$('.filters li a').attr('class', '');
				$(this).addClass('active');

				gTimeline = $.timeline({categoryId: gCategoryId, startTime: startTime, 
				    endTime: endTime, mediaType: gMediaType,
					//url: "'<?php echo url::site(); ?>'+json_url+'/timeline/'"
					url: '<?php echo url::site(); ?>json/timeline/'  // SIDCE
				});
				gTimeline.plot();

                // SIHCE
                //Add same code to impact filters
                $('#impact_filters li select').change(function(){
                    // Refresh Map
                    addMarkers(currentCat, startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);
                    
                    gTimeline = $.timeline({categoryId: gCategoryId, startTime: startTime, 
                        endTime: endTime, mediaType: gMediaType,
                        url: '<?php echo url::site(); ?>json/timeline/'  // SIHCE
                    });
                    gTimeline.plot();
                
                });
			});
			
			$('#playTimeline').click(function()
			{
			    gTimelineMarkers = gTimeline.addMarkers(gStartTime.getTime()/1000,
					$.dayEndDateTime(gEndTime.getTime()/1000), gMap.getZoom(),
					gMap.getCenter(),null,null,null,null,"json");
				gTimeline.playOrPause('raindrops');
			});
            
            //Bulletin more
            $('a.bulletin_more').click(function(){
                
                var w = 350;
                var h = 400;
                if (!$('#bulletin_more').size())
                    $(document.body).append("<div id='bulletin_more'></div>");

                var div_dialog = $('#bulletin_more');
                
                div_dialog.html('');
                div_dialog.append($('#bulletin_list').clone());
                div_dialog.dialog({
                                height: h + 50,
                                width: w, 
                                autoOpen: false,
                                zIndex: 5000,
                               title: '<?php echo Kohana::lang('ui_main.humanitarian_bulletin'); ?>', 
                               open : function (event, ui) { 
                                            div_dialog.find("li").each(function(){ $(this).show()});
                                            div_dialog.find('a.bulletin_more').hide();
                                        }
                              });

                div_dialog.dialog('open');

                return false;
            });

            // Category pie event
            $('#pie_chart_zoom').click(function(){
                
                var startTime = $("#startDate").val();
                var endTime = $("#endDate").val();
                var pie = $('#pie_chart_div');
                var w = 550;
                var h = 400;
                
                pie.dialog({    height: h + 50,
                                width: w, 
                                autoOpen: false,
                                resizable: false,
                                zIndex: 5000,
                               title: '<?php echo Kohana::lang('ui_main.chart_pie_title'); ?>', 
                                open : function (event, ui) { google.setOnLoadCallback(drawCharts(w, h, startTime, endTime))}
                              });

                pie.dialog('open');

            });

            // View more time line
            $("a.timeline_more").click(function(){

                var t_d = $('#timeline_div');
                var w = 700;
                var h = 400;
                var startDate = $("#startDate").val();
                var endDate = $("#endDate").val();
                var o_t_d = t_d.clone();
                
                // copy contents of original graph to put this in close modal method
				var originalContext = t_d.find("canvas")[0].getContext("2d");
				
                var imageData = originalContext.getImageData(0, 0, 370, 150);
				
                var cloneContext = o_t_d.find("canvas")[0].getContext("2d");
				cloneContext.putImageData(imageData, 0, 0); 
                
                
                t_d.children("div.slider-holder").removeClass('hide');
                
                //Set info
                t_d.prepend('<br /><p><?php echo Kohana::lang('ui_main.reports_timeline_instructions') ?></p><br />');
                
                t_d.find("div.graph-holder").css('width', (w - 50)+'px');
                refreshGraph(startDate, endDate);

                t_d.dialog({    height: h + 50,
                                width: w, 
                                autoOpen: false,
                                resizable: false,
                                zIndex: 5000,
                                title: '<?php echo Kohana::lang('ui_main.reports_timeline') ?>',
                                close: function(event, ui){ $('div.content-block-left').append(o_t_d); }
                              });

                t_d.dialog('open');

               return false; 
            });
		});
            
        google.load("visualization", "1", {packages:["corechart"]});
		
		
        function drawCharts(w, h, s, e) {

            var url = '<?php echo url::site(); ?>json/category_chart/?s='+s+'&e='+e;

            $.getJSON(url, function(json){
                    
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Category');
                    data.addColumn('number', 'Incidents');
                    data.addRows(json.data);
                        
                    var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
                    chart.draw(data, { width: w, height: h, 
                                       colors: json.colors,
                                     });
            });

        }
		
		/*
		Create the Markers Layer
		*/
		function addMarkers(catID,startDate,endDate, currZoom, currCenter,
			mediaType, thisLayerID, thisLayerType, thisLayerUrl, thisLayerColor)
		{
			return $.timeline({categoryId: catID,
			                   startTime: new Date(startDate * 1000),
			                   endTime: new Date(endDate * 1000),
							   mediaType: mediaType
							  }).addMarkers(startDate, endDate, gMap.getZoom(),
							                gMap.getCenter(), thisLayerID, thisLayerType,
							 				thisLayerUrl, thisLayerColor, json_url);
		}
		
		/*
		Display loader as Map Loads
		*/
		function onMapStartLoad(event)
		{
			if ($("#loader"))
			{
				$("#loader").show();
			}

			if ($("#OpenLayers\\.Control\\.LoadingPanel_4"))
			{
				$("#OpenLayers\\.Control\\.LoadingPanel_4").show();
			}
		}
		
		/*
		Hide Loader
		*/
		function onMapEndLoad(event)
		{
			if ($("#loader"))
			{
				$("#loader").hide();
			}
			
			if ($("#OpenLayers\\.Control\\.LoadingPanel_4"))
			{
				$("#OpenLayers\\.Control\\.LoadingPanel_4").hide();
			}
		}
		
		/*
		Close Popup
		*/
		function onPopupClose(evt)
		{
            // selectControl.unselect(selectedFeature);
			for (var i=0; i<map.popups.length; ++i)
			{
				map.removePopup(map.popups[i]);
			}
        }

		/*
		Display popup when feature selected
		*/
        function onFeatureSelect(event)
		{
            selectedFeature = event;
            // Since KML is user-generated, do naive protection against
            // Javascript.

			zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
			lon = zoom_point.lon;
			lat = zoom_point.lat;
			
			var content = "<div class=\"infowindow\"><div class=\"infowindow_list\"><ul><li>"+event.feature.attributes.name + "</li></ul><div style=\"clear:both;\"></div></div>";
			content = content + "\n<div class=\"infowindow_meta\"><a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", 1)'>Zoom&nbsp;In</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", -1)'>Zoom&nbsp;Out</a></div>";
			content = content + "</div>";			
			
			if (content.search("<script") != -1)
			{
                content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/</g, "&lt;");
            }
            popup = new OpenLayers.Popup.FramedCloud("chicken", 
				event.feature.geometry.getBounds().getCenterLonLat(),
				new OpenLayers.Size(100,100),
				content,
				null, true, onPopupClose);
            event.feature.popup = popup;
            map.addPopup(popup);
        }
		
		/*
		Destroy Popup Layer
		*/
        function onFeatureUnselect(event)
		{
            map.removePopup(event.feature.popup);
            event.feature.popup.destroy();
            event.feature.popup = null;
        }

		// Refactor Clusters On Zoom
		// *** Causes the map to load json twice on the first go
		// *** Need to fix this!
		function mapZoom(event)
		{
			// Prevent this event from running on the first load
			if (mapLoad > 0)
			{
				// Get Current Category
				currCat = $("#currentCat").val();

				// Get Current Start Date
				currStartDate = $("#startDate").val();

				// Get Current End Date
				currEndDate = $("#endDate").val();

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();

				// Refresh Map
				addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
			}
		}
		
		function mapMove(event)
		{
			// Prevent this event from running on the first load
			if (mapLoad > 0)
			{
				// Get Current Category
				currCat = $("#currentCat").val();

				// Get Current Start Date
				currStartDate = $("#startDate").val();

				// Get Current End Date
				currEndDate = $("#endDate").val();

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();

				// Refresh Map
				addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
			}
		}
		
		
		/*
		Refresh Graph on Slider Change
		*/
		function refreshGraph(startDate, endDate)
		{
			var currentCat = gCategoryId;
			
			// refresh graph
			if (!currentCat || currentCat == '0')
			{
				currentCat = '0';
			}
			
			var startTime = new Date(startDate * 1000);
			var endTime = new Date(endDate * 1000);
			// daily
			var graphData = dailyGraphData[0][currentCat];

			// plot hourly incidents when period is within 2 days
			if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 3)
			{
			    graphData = hourlyGraphData[0][currentCat];
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 124)
			{
			    // weekly if period > 2 months
			    graphData = dailyGraphData[0][currentCat];
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124)
			{
				// monthly if period > 4 months
			    graphData = allGraphData[0][currentCat];
			}
			gTimeline = $.timeline({categoryId: currentCat,
				startTime: new Date(startDate * 1000),
			    endTime: new Date(endDate * 1000), mediaType: gMediaType,
				markerOptions: gMarkerOptions,
				graphData: graphData
			});
			gTimeline.plot();
		}
		
		/*
		Zoom to Selected Feature from within Popup
		*/
		function zoomToSelectedFeature(lon, lat, zoomfactor)
		{
			var lonlat = new OpenLayers.LonLat(lon,lat);
			
			// Get Current Zoom
			currZoom = map.getZoom();
			// New Zoom
			newZoom = currZoom + zoomfactor;
			// Center and Zoom
			map.setCenter(lonlat, newZoom);
			// Remove Popups
			for (var i=0; i<map.popups.length; ++i)
			{
				map.removePopup(map.popups[i]);
			}
		}
		
		/*
		Add KML/KMZ Layers
		*/
		function switchLayer(layerID, layerURL, layerColor)
		{
			if ( $("#layer_" + layerID).hasClass("active") )
			{
				new_layer = map.getLayersByName("Layer_"+layerID);
				if (new_layer)
				{
					for (var i = 0; i < new_layer.length; i++)
					{
						map.removeLayer(new_layer[i]);
					}
				}
				$("#layer_" + layerID).removeClass("active");
				
			}
			else
			{
				$("#layer_" + layerID).addClass("active");
				
				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();
				
				// Add New Layer
				addMarkers('', '', '', currZoom, currCenter, '', layerID, 'layers', layerURL, layerColor);
			}
		}
		
		
		/*		
		d = $('#startDate > optgroup > option').map(function()
		{
			return $(this).val();
		});

		$.grep(d, function(n,i)
		{
			return n > '1183240800';
		})[0];
*/
