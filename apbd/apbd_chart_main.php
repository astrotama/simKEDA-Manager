<?php

function apbd_chart_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';


	drupal_set_title('ANALISIS APBD');
	
	$output_form = drupal_get_form('apbd_chart_main_form');

	return drupal_render($output_form);
	
}

function apbd_chart_main_form($form, &$form_state) {

		
	$showchart = arg(2);
	$tahun = arg(3);
	$kodero = arg(4);
	$kodeuk = arg(5);
	$param4 = arg(6);
	
	$param5 = arg(7);


	if ($showchart == 'rekeningberjalan') {

		//REKENING
		$query = db_select('apbdrekap', 'a');
		$query->fields('a', array('namarincian'));
		$query->condition('a.koderincian', $kodero, '=');
		$results = $query->execute();
		foreach ($results as $datas) {	
			$rekening = $kodero . ' - ' . $datas->namarincian;
		}	

		if ($kodeuk=='##') 
			$skpd= 'KABUPATEN';
		else {
			$query = db_select('unitkerja', 'u');
			$query->fields('u', array('namasingkat'));
			$query->condition('u.kodeuk', $kodeuk, '=');
			$results = $query->execute();
			foreach ($results as $data) {
				$skpd= $data->namasingkat;
			}				
		}		

		$form['item1'] = array(
			'#type' => 'item',
			'#title' =>  t('Unit Kerja'),
			'#markup' => '<p>' . $skpd . '</p>',
		);	
		$form['item2'] = array(
			'#type' => 'item',
			'#title' =>  t('Rekening'),
			'#markup' => '<p>' .  $rekening . '</p>',
		);	
		$form['formdata']['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Tutup'),
		);
		
		//apbd_chart_rekening_berjalan($tahun, $koderincian, $kodeskpd, $inpersen)
		$form['chart1'] = array(
			'#type' => 'markup',
			'#markup' => apbd_chart_rekening_berjalan($tahun, $kodero, $kodeuk, false),		//$selected,

		);	
		
		$form['chart2'] = array(
			'#type' => 'markup',
			'#markup' => apbd_chart_rekening_berjalan($tahun, $kodero, $kodeuk, true),		//$selected,

		);	
	} 
	return $form;
}


function apbd_chart_rekening_berjalan($tahun, $koderekening, $kodeskpd, $inpersen) {

$tahunakhir = $tahun-4;
if ($tahunakhir<2008) $tahunakhir = 2008;

$arrbulan = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);

$query = db_select('apbdrekap', 'r');
$query->addExpression('SUM(r.anggaran2/1000)', 'jmlanggaran');

$query->addExpression('SUM(r.realisasi1/1000)', 'jmlrealisasi1');
$query->addExpression('SUM(r.realisasikum1/1000)', 'kumrealisasi1');

$query->addExpression('SUM(r.realisasi2/1000)', 'jmlrealisasi2');
$query->addExpression('SUM(r.realisasikum2/1000)', 'kumrealisasi2');
$query->addExpression('SUM(r.realisasi3/1000)', 'jmlrealisasi3');
$query->addExpression('SUM(r.realisasikum3/1000)', 'kumrealisasi3');
$query->addExpression('SUM(r.realisasi4/1000)', 'jmlrealisasi4');
$query->addExpression('SUM(r.realisasikum4/1000)', 'kumrealisasi4');
$query->addExpression('SUM(r.realisasi5/1000)', 'jmlrealisasi5');
$query->addExpression('SUM(r.realisasikum5/1000)', 'kumrealisasi5');
$query->addExpression('SUM(r.realisasi6/1000)', 'jmlrealisasi6');
$query->addExpression('SUM(r.realisasikum6/1000)', 'kumrealisasi6');
$query->addExpression('SUM(r.realisasi7/1000)', 'jmlrealisasi7');
$query->addExpression('SUM(r.realisasikum7/1000)', 'kumrealisasi7');
$query->addExpression('SUM(r.realisasi8/1000)', 'jmlrealisasi8');
$query->addExpression('SUM(r.realisasikum8/1000)', 'kumrealisasi8');
$query->addExpression('SUM(r.realisasi9/1000)', 'jmlrealisasi9');
$query->addExpression('SUM(r.realisasikum9/1000)', 'kumrealisasi9');
$query->addExpression('SUM(r.realisasi10/1000)', 'jmlrealisasi10');
$query->addExpression('SUM(r.realisasikum10/1000)', 'kumrealisasi10');
$query->addExpression('SUM(r.realisasi11/1000)', 'jmlrealisasi11');
$query->addExpression('SUM(r.realisasikum11/1000)', 'kumrealisasi11');
$query->addExpression('SUM(r.realisasi12/1000)', 'jmlrealisasi12');
$query->addExpression('SUM(r.realisasikum12/1000)', 'kumrealisasi12');
if ($kodeskpd!='##') $query->condition('r.kodeskpd', $kodeskpd, '=');

if (strlen($koderekening)==8)
	$query->condition('r.koderincian', $koderekening, '=');
else if (strlen($koderekening)==5)
	$query->condition('r.kodeobyek', $koderekening, '=');
else if (strlen($koderekening)==3)
	$query->condition('r.kodejenis', $koderekening, '=');
else
	$query->condition('r.kodekelompok', $koderekening, '=');
	

$results = $query->execute();
foreach ($results as $datas) {	

	$arr_anggaran = array(apbd_get_dbvalue($datas->jmlanggaran), c );
	$arr_realisasi = array();
	$arr_kum = array();
	
	$arr_anggaran[]= apbd_get_dbvalue($datas->jmlanggaran);
	$arr_realisasi[]= apbd_get_dbvalue($datas->jmlrealisasi);
	$arr_kum[]=apbd_get_dbvalue($datas->kumrealisasi);;
	
}	



if ($inpersen) {
	$chart = array(
		'#type' => 'chart',
		'#chart_type' => 'column',
		'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
		'#title' => t('Analisis Pencapaian Realisasi Anggaran (%)'),
		'#legend_position' => 'right',
		'#data_labels' => TRUE,
		'#tooltips' => TRUE,
		
	);

	$arr_persen = array();
	$x = count($arr_anggaran);
	for ($i=0; $i<$x; $i++) {
		$arr_persen[$i] = round(apbd_hitungpersen($arr_anggaran[$i], $arr_kum[$i]),2);
	}
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('% Anggaran'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_persen,
		'#suffix' => '%',
	);	
	
	
} else {
	$chart = array(
		'#type' => 'chart',
		'#chart_type' => 'column',
		'#chart_library' => 'highcharts', // Allowed values: 'google' or 'highcharts'
		'#title' => t('Analisis Anggaran/Realisasi'),
		'#legend_position' => 'right',
		'#data_labels' => TRUE,
		'#tooltips' => TRUE,
		
	);
	$chart['realisasi'] = array(
		'#type' => 'chart_data',
		'#title' => t('Bulanan'),
		//'#data' => array(12, 22, 32, 37, 44),
		'#data' => $arr_realisasi,
		'#suffix' => ' Rb',
	);
	
	$chart['anggaran'] = array(
		'#type' => 'chart_data',
		'#title' => t('Kumulatif'),
		//'#data' => array(10, 20, 30, 40, 50),
		'#data' => $arr_kum,
		'#suffix' => ' Rb',
	);
	
}

$chart['xaxis'] = array(
	'#type' => 'chart_xaxis',
	//'#labels' => array('2011', '2012', '2013', '2014', '2015'),
	'#labels' => $arrbulan,
);

$apbdbelanja['apbd_chart_rekening_berjalan'] = $chart;

return drupal_render($apbdbelanja);

}

?>
