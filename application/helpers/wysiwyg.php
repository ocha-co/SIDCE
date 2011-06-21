<?php

class wysiwyg {
	
	public static function render($elements) {
        
		echo html::script(array('media/js/tinymce/tiny_mce'), false);
        echo '<script type="text/javascript">

		$(function() {
		tinyMCE.init({
					// General options
					mode : "exact", elements: "'.implode(',', $elements).'", theme : "advanced",
					plugins : "paste",
					
					// Theme options
					theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,undo,redo,code,|,formatselect,",
					theme_advanced_buttons2 : "", 
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
			});
		});
	    </script>';
	}

}


?>
