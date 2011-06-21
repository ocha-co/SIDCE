<?php
/**
 * Report bulletin js file.
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
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawCharts);

function drawCharts(){
    var data_i_c = new google.visualization.DataTable();
    var data_i_cat = new google.visualization.DataTable();
    var data_trend = new google.visualization.DataTable();

    // Per city
    data_i_c.addColumn('string', 'Fecha');
    data_i_c.addColumn('number', 'Eventos');
    <?php 
        foreach ($i_cities as $city => $data){
            echo "data_i_c.addRow(['$city', $data]);";
        } 
    ?>
    var chart = new google.visualization.ColumnChart(document.getElementById('div_i_c'));

    chart.draw(data_i_c, { width: 400, height: 200, title: '<?php echo Kohana::lang('ui_main.incidents_per_city'); ?>', fontSize: 11, legend: 'none', titleFontSize: 14 });
    
    // Per Category
    data_i_cat.addColumn('string', 'Categoria');
    data_i_cat.addColumn('number', 'Eventos');
    <?php 
        foreach ($i_cat as $cat => $data){
            echo "data_i_cat.addRow(['$cat', $data]);";
        } 
    ?>
    var chart = new google.visualization.PieChart(document.getElementById('div_cat'));

    chart.draw(data_i_cat, { width: 400, height: 150, title: '<?php echo Kohana::lang('ui_main.incidents_per_category'); ?>', fontSize: 11, titleFontSize: 14 });
    
    // Trend
    data_trend.addColumn('string', 'Fecha');
    data_trend.addColumn('number', 'Eventos');
    <?php 
        foreach ($trend as $w => $data){
            echo "data_trend.addRow(['$w', $data]);";
        } 
    ?>
    var chart = new google.visualization.LineChart(document.getElementById('div_trend'));
    chart.draw(data_trend, { width: 600, height: 200, title: '<?php echo Kohana::lang('ui_main.weekly_trend'); ?>', fontSize: 11, legend: 'none', titleFontSize: 14 });

}
    function init(){

        var extent = new OpenLayers.Bounds(-74.6, 7.5, -72, 11);
         map = new OpenLayers.Map('bulletin_map',
        {
            maxExtent: extent,
            restrictedExtent: extent,
            maxResolution: 'auto',
            units:"dd",
            projection: new OpenLayers.Projection("EPSG:4326"),
            numZoomLevels: 1,
            controls: [new OpenLayers.Control.Navigation({'zoomWheelEnabled': false}) ]
        }
        );

        layer_d = new OpenLayers.Layer.WMS( "Departamentos", "http://pdpcesar.co/cgi-bin/cesar",
        {	layers: 'depto', layername: 'depto', format: 'image/png', request: 'getmap', transparent: true});
                

        var m_style = new OpenLayers.StyleMap({
                "fillColor": "#F4F2CF",
                "strokeColor": "#424242"
                });
        
        layer_m = new OpenLayers.Layer.WMS( "Municipios", "http://pdpcesar.co/cgi-bin/cesar",
        {	layers: 'MPIO', format: 'image/png', request: 'getmap', 'map.layer[mpio].class[0].style[0]': 'COLOR 244 242 207'});
        

        map.addLayers([layer_d, layer_m]);

        var style  =new OpenLayers.Style({
            pointRadius: "${radius}", // sized according to type attribute
            fillColor: "${color}",
            fillOpacity: "0.35",
            strokeColor: "${color}",
            strokeWidth: 1,
            fontColor: "#FFFFFF",
            fontSize: "10px",
            fontWeight: "bold"
        });

        var Styles = new OpenLayers.StyleMap({
                "default": style,
                "select": style
                });

        var cat_map = [<?php echo $m_cat; ?>];
        var cat_map_id = [<?php echo $m_cat_id; ?>];
        var json_file;
        $.each(cat_map, function (i, cat_n){
            
            var TmpLayer = new OpenLayers.Layer.Vector( cat_n, {styleMap: Styles, format: OpenLayers.Format.GeoJSON}); 	
            map.addLayer(TmpLayer);

            selectControl = new OpenLayers.Control.SelectFeature(TmpLayer,
                { hover: true, onSelect: onFeatureSelect, onUnselect: onFeatureUnselect });

            map.addControl(selectControl);
            selectControl.activate();

            $.getJSON('<?php echo url::site() ?>json/bulletin/<?php echo "$start/$end" ?>/' + cat_map_id[i], function(json){
                var geojson = new OpenLayers.Format.GeoJSON( { 'internalProjection': map.baseLayer.projection, 'externalProjection':  map.baseLayer.projection});

                var features =  geojson.read(json);
                $('.olControlLoadingPanel:first').css({width: '0px', height: '0px', display: 'none'});
                TmpLayer.addFeatures(features);
            });

        });
        map.setCenter(extent.getCenterLonLat(), 3);
        var switcher =  new OpenLayers.Control.LayerSwitcher();
        map.addControl(switcher);
        //switcher.maximizeControl();
    }

    function onFeatureSelect(feature) {

        selectedFeature = feature;
alert('a');
        popup = new OpenLayers.Popup.FramedCloud("", feature.geometry.getBounds().getCenterLonLat(),

        new OpenLayers.Size(100,100), "<div class='map_popup'>"+feature.attributes.desc+"</div>", null, true, onPopupClose);

        feature.popup = popup;
        map.addPopup(popup);

    }

    function onPopupClose(evt) {
        selectControl.unselect(selectedFeature);
    }

    function onFeatureUnselect(feature) {

        map.removePopup(feature.popup);
        feature.popup.destroy();
        feature.popup = null;

    }
    $(function(){init()});
