<?php

$lang = array
(
	'layer_name' => array
	(
		'required'		=> 'Se requiere un nombre',
		'length'		=> 'El nombre debe tener entre tres y 80 caracteres',
	),
	
	'layer_url' => array
	(
		'url' => 'Por favor ingrese un URL v�lido, por ejemplo http://www.ushahidi.com/layerl.kml',
		'atleast' => 'Se requier un archivo o un URL de un KML',
		'both' => 'No se puede especificar un URL y un archivo KML. Utilize solamente uno'
	),
	
	'layer_color' => array
	(
		'required'		=> 'Se requiere un color',
		'length'		=> 'El color seleccionado debe tenere seis caracteres',
	),
	
	'layer_file' => array
	(
		'valid'		=> 'El archivo especificado no es v�lido',
		'type'		=> 'El archivo especificado no es v�lido. Los formatos aceptados son .KMZ y .KML.'
	),	
	'layer_type' => array
	(
		'required'		=> 'Seleccione el tipo de layer',
	),	
	'layer_layer' => array
	(
		'required'		=> 'El nombre del layer del archivo .map no puede estar vacio'
	),	
);
