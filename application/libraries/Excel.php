<?php 
//App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
//App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

/**
 * Excel helper. Displays victims form on the front-end.
 * 
 * @package    Victim
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */

require Kohana::find_file('vendor', 'excel/PHPExcel', $required = TRUE);
require Kohana::find_file('vendor', 'excel/PHPExcel/Writer/Excel5', $required = TRUE);

class Excel_Core {
    
    var $xls;
    var $sheet;
    var $data;
    
    function __construct() {
        $this->xls = new PHPExcel();
        $this->sheet = $this->xls->getActiveSheet();
        $this->sheet->getDefaultStyle()->getFont()->setName('Helvetica');
    }
					 
	function generate(&$data, $title = 'Report') {
		 $this->data = $data;
		 
		 $this->headers();
		 $this->rows();
		 $this->output($title);
		 return true;
	}
	
	function headers($row, $r = 2) {
		$c = 0;
		foreach ($row as $label)
			$this->sheet->setCellValueByColumnAndRow($c++, $r, $label);

		$color = new PHPExcel_Style_Color;
		$color->setRGB('FFFFFF');	

		$this->sheet->getStyle('A'.$r)->getFont()->setSize(8);
		$this->sheet->getStyle('A'.$r)->getFont()->setBold(true);
		$this->sheet->getStyle('A'.$r)->getFont()->setColor($color);
		$this->sheet->getStyle('A'.$r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$this->sheet->getStyle('A'.$r)->getFill()->getStartColor()->setRGB('CCCCCC');
		$this->sheet->getStyle('A'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle('A'.$r)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->sheet->getStyle('A'.$r)->getAlignment()->setWrapText(true);
		
		$this->sheet->duplicateStyle( $this->sheet->getStyle('A'.$r), 'B'.$r.':'.$this->sheet->getHighestColumn().$r);
		
		for ($r=1; $r<$c; $r++) {
			$this->sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($r))->setWidth(10);
		}
		$this->sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(30);
		$this->sheet->getRowDimension($r)->setRowHeight(-1);
	}

	function cell($r, $c, $content, $properties = null){
		if(isset($properties['bold']) && $properties['bold'])
			$this->sheet->getStyleByColumnAndRow($c, $r)->getFont()->setBold(true);
		if(isset($properties['foot']) && $properties['foot'] == 'gray'){
			$color = new PHPExcel_Style_Color;
			$color->setRGB('FFFFFF');	
		
			$this->sheet->getStyleByColumnAndRow($c, $r)->getFont()->setColor($color);
			$this->sheet->getStyleByColumnAndRow($c, $r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->sheet->getStyleByColumnAndRow($c, $r)->getFill()->getStartColor()->setRGB('CCCCCC');
			$this->sheet->getStyleByColumnAndRow($c, $r)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$this->sheet->getStyleByColumnAndRow($c, $r)->getFill()->getStartColor()->setRGB('CCCCCC');
		}
			
		$this->sheet->getStyleByColumnAndRow($c, $r)->getFont()->setSize(8);
		$this->sheet->setCellValueByColumnAndRow($c, $r, $content);
	}
	
	function merge($string){
		$this->sheet->mergeCells($string);
	}
	
	function rows(&$data, $start = 3) {
		$i=$start;
		foreach ($data as $row) {
			$j=0;
			foreach ($row as $field => $value) {
					$this->sheet->getStyleByColumnAndRow($i, $j)->getFont()->setSize(8);
					$this->sheet->setCellValueByColumnAndRow($j++,$i, $value);
			}
			$i++;
		}
	}
			
	function output($title) {
		header("Content-type: application/vnd.ms-excel"); 
		header('Content-Disposition: attachment;filename="'.$title.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = new PHPExcel_Writer_Excel5($this->xls);
		$objWriter->setTempDir(APPPATH.'cache');
		$objWriter->save('php://output');
	}
}
?>
