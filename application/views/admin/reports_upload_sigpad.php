<?php 
/**
 * Reports submit view page.
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
    <h2>
    <?php print $title; ?> <span></span>
    <a href="<?php print url::site() ?>admin/reports"><?php echo Kohana::lang('ui_main.view_reports');?></a>
    <a href="<?php print url::site() ?>admin/reports/download"><?php echo Kohana::lang('ui_main.download_reports');?></a>
    <a href="<?php print url::site() ?>admin/reports/upload"><?php echo Kohana::lang('ui_main.upload_reports');?></a></h2>
	<!-- report-form -->
	<div class="report-form">
		<!-- column -->
		<div class="upload_container">
            <?php
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
            
            print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'sigpadForm', 'name' => 'sigpadForm'));

            if ($_POST){
                echo "<h3>".Kohana::lang('ui_main.summary')."</h3>";
                echo "<p>$summary</p>";
            }
            else{
            ?>
            <div class="report_instructions">
            <?php
            if (!isset($success)){
            ?>
                <p>Esta opción permite cargar información de SIGPAD:</p>
                <p><b>El archivo de texto que va a importar debe estar separado por pipe '|' (barra vertical) y debe tener las siguientes
                    estructura:</b></p>
                <p>La primera fila debe tener los titulos de: DESCRIPCION , UBICACIÓN, 
                AFECTACIÓN y APOYO FONDO NACIONAL DE CALAMIDADES  
                </p>
                <p>La segunda fila debe tener las siguientes columnas</p>
                    <ul>
                    <li>FECHA: en formato <?php echo form::dropdown('date_format', array('YYYY/MM/DD','YYYY-MM-DD','DD/MM/YYYY','DD-MM-YYYY','MM/DD/YYYY','MM-DD-YYYY')) ?></li>
                    <li>DEPTO (Solo debe existir Cesar)</li>
                    <li>MUNICIPIO</li>
                    <li>EVENTO: Esta columna corresponde a las categorias en el sistema</li>
                    <li>MUERTOS</li>
                    <li>HERIDOS</li>
                    <li>DESAPARECIDOS</li>
                    <li>PERSONAS</li>
                    <li>FAMILIAS</li>
                    <li>VIVIENDAS DESTRUIDAS</li>
                    <li>VIVIENDAS AVERIADAS</li>
                    <li>VIAS</li>
                    <li>PUENTES VEHICULARES</li>
                    <li>PUENTES PEATONALES</li>
                    <li>ACUEDUCTO</li>
                    <li>ALCANTARILLADO</li>
                    <li>C. SALUD</li>
                    <li>C. EDUCATIVOS</li>
                    <li>C. COMUNITARIOS</li>
                    <li>HECTAREAS</li>
                    <li>OTROS</li>
                    <li>FECHA TRAMITE: en formato YYYY-MM-DD</li>
                    <li>MENAJES</li>
                    <li>AP. ALIMENTOS</li>
                    <li>MATERIALES DE CONSTRUCCION</li>
                    <li>SACOS</li>
                    <li>OTROS</li>
                    <li>GIRO DIRECTO</li>
                    <li>VALOR TOTAL</li>
                    <li>APOYOS EN TRAMITE</li>
                </ul>
                <p>Los valores puende ser texto para los descriptivos y números para las columnas de conteo y valores, sin simbolos de pesos, comas, etc</p>
                <p>Recuerde que los reportes importados con esta opción entran al sistema <b>APROBADOS</b> y <b>VERIFICADOS</b>.<br /><br /> 
                Los eventos que tengan la misma <b>categoria</b>, la misma <b>fecha</b> y <b>localización</b> no serán importados dado que se consideran como duplicidad
                </p>
                <?php if (isset($last_upload->incident_title)){ ?>
                    <p><b>*** Ultimo evento importado:</b>&nbsp;<?php echo $last_upload->incident_title ?></p>
                <?php } ?>
            <?php 
                print form::upload('sigpad_file', '', ' class="text long"');
            }
            ?>
            </div>
                                    
            <div class="btns">
                <ul>
                    <li><a href="#" class="btn_save" onclick="if($('#sigpad_file').val() != ''){if (confirm('Está seguro que desea continuar?')){$('#sigpadForm').submit()} else{return false;}} else{alert('Seleccione un archivo!');return false;} "><?php echo strtoupper(Kohana::lang('ui_main.continue'));?></a></li>
                    <li><a href="<?php echo url::site().'admin/reports/upload_sigpad';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                </ul>
            </div>						
        </div>
        <?php } ?>
        <?php
            print form::hidden('sigpad_submit',1);
            print form::hidden('go',0);
            print form::close(); 
        ?>
    </div>
	</div>
</div>



