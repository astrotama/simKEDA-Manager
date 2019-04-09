<?php

function cari_kegiatan_main($arg=NULL, $nama=NULL) {
	
	if ($arg) {
		
		switch($arg) {
			case 'filter':
				$kata_kunci  = arg(2);

				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		 $kata_kunci=''; ;
	} 


	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'Kegiatan', 'valign'=>'top'), 
		array('data' => 'total', 'valign'=>'top'),
	);
	db_set_active('akuntansi');
	
	$results = db_query("select kodekeg, kegiatan, lokasi, total,kodeuk from kegiatanskpd where kegiatan like '%". $kata_kunci ."%' limit 10");
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		
		$editlink = '<a href="/laporanapbddetilkeg/filterkeginfo/'. $data->kodeuk .'/' . $data->kodekeg.'">'. $data->kegiatan .'</a>';
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
						array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),	
					); 
	}
	db_set_active();
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');

	//return drupal_render($output_form) . $btn . $output . $btn;
	return $output ;
	
}

?>
