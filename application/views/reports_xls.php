<?php
$array = array(
            strtoupper(Kohana::lang('ui_main.report_title')),
            strtoupper(Kohana::lang('ui_main.report_details')),
            strtoupper(Kohana::lang('ui_main.date')),
            strtoupper(Kohana::lang('ui_main.location')),
            strtoupper(Kohana::lang('ui_main.verified')));

$excel->headers($array, 1);

$r = 2;
foreach ($incidents as $incident){
    $incident_id = $incident->id;
    $incident_title = $incident->incident_title;
    $incident_description = $incident->incident_description;
    $incident_description = $incident_description;
    $incident_date = date('Y-m-d', strtotime($incident->incident_date));
    $incident_location = $incident->location_name;
    $incident_verified = ($incident->incident_verified) ? 'x' : '';
    
    $c = 0;
    $excel->cell($r, $c++, $incident_title, array('type' => 'text'));
    $excel->cell($r, $c++, $incident_description, array('type' => 'text'));
    $excel->cell($r, $c++, $incident_date, array('type' => 'text'));
    $excel->cell($r, $c++, $incident_location, array('type' => 'text'));
    $excel->cell($r, $c++, $incident_verified, array('type' => 'text'));
    $r++;
}
$excel->output($filename);

?>
