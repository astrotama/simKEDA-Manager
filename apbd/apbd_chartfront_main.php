<?php

function apbd_chartfront_main($arg=NULL, $nama=NULL) {

	//drupal_set_title('ANALISIS APBD');
	
	$output_form = drupal_get_form('apbd_chartfront_main_form');

	return drupal_render($output_form);
	
}
 
function apbd_chartfront_main_form($form, &$form_state) {

	$jenischart = arg(2);
	$form['formdata']['submittotal'] = array(
		'#type' => 'submit',
		'#value' => t('Total'),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	$form['formdata']['submitpb'] = array(
		'#type' => 'submit',
		'#value' => t('Pend & Bel'),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	$form['formdata']['submitpembiayaan'] = array(
		'#type' => 'submit',
		'#value' => t('Pembiayaan'),
		'#attributes' => array('class' => array('btn btn-success')),
	);	
	$form['formdata']['submitkas'] = array(
		'#type' => 'submit',
		'#value' => t('Kas'),
		'#attributes' => array('class' => array('btn btn-success')),
	);

	
	switch ($jenischart) {
		case 'apbdutama_all':
			//apbd_chartfront_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
			$form['chart1'] = array(
				'#type' => 'markup',
				'#markup' => apbd_chart_utama_all(),		//$selected,
			);				
			break;

		case 'apbdutama_pb':
			//apbd_chartfront_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
			$form['chart1'] = array(
				'#type' => 'markup',
				'#markup' => apbd_chart_utama_pendapatan_belanja(),		//$selected,
			);				
			break;

		case 'apbdutama_pby':
			//apbd_chartfront_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
			$form['chart1'] = array(
				'#type' => 'markup',
				'#markup' => apbd_chart_utama_pembiayaan(),		//$selected,
			);				
			break;

		case 'apbdutama_kas':
			//apbd_chartfront_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
			$form['chart1'] = array(
				'#type' => 'markup',
				'#markup' => apbd_chart_utama_kas(),		//$selected,
			);				
			break;
			
		
		default:
			//apbd_chartfront_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
			
			$form['chart1'] = array(
				'#type' => 'markup',
				'#markup' => apbd_chart_utama_all(),		//$selected,
			);
				
			break;
			
	}	
	
	return $form;
}

function apbd_chartfront_main_form_submit($form, &$form_state) {

$select_button  = $form_state['clicked_button']['#value'];

if($select_button == 'Pend & Bel')
	$uri = 'apbdutama_pb';
else if($select_button == 'Pembiayaan')
	$uri = 'apbdutama_pby';
else if($select_button == 'Kas')
	$uri = 'apbdutama_kas';
else
	$uri = 'apbdutama_all';
	
$uri = 'apbd/chartfront/' . $uri;

drupal_goto($uri);	
}



?>
