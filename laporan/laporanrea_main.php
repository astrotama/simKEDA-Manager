<?php
function laporanrea_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	if ($arg) {
		switch($arg) {
			case 'filter':
				$tahun = arg(2);
				$kodeuk = arg(3);
				$cetakpdf = arg(4);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$tahun = date('n');		//variable_get('apbdtahun', 0);
		$kodeuk = 'ZZ';
	}
	
	//drupal_set_title('BELANJA');
	
	
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($tahun, $kodeuk, true, true);
		print_pdf_p($output);
	} else {
		$output = gen_report_realisasi($tahun, $kodeuk, true, true);
		$output_form = drupal_get_form('laporanrea_main_form');	
		
		$btn = l('Cetak', 'laporanrea/filter/' . $tahun . '/'. $kodeuk . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));

		//$btn = '';
		
		return drupal_render($output_form) . $btn . $output . $btn;
		
	}	
	
}

function laporanrea_main_form_submit($form, &$form_state) {
	$tahun= $form_state['values']['tahun'];
	$kodeuk = $form_state['values']['kodeuk'];

	$uri = 'laporanrea/filter/' . $tahun . '/'.$kodeuk ;
	drupal_goto($uri);
	
}


function laporanrea_main_form($form, &$form_state) {
	
	$kodeuk = 'ZZ';
	$namasingkat = '|SELURUH SKPD';
	$tahun = '2015';
	
	if(arg(2)!=null){
		
		$tahun = arg(2);
		$kodeuk = arg(3);
	}
	if ($kodeuk!='ZZ') {
		$query = db_select('unitkerja', 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',$kodeuk,'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat= '|' . $data->namasingkat;
			}
		}	
	}
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $tahun . $namasingkat . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	
	$form['formdata']['tahun'] = array(
		'#type' => 'select',
		'#title' => 'Tahun',
		'#default_value' => $tahun,	
		'#options' => array(	
			 '2011' => t('2011'), 	
			 '2012' => t('2012'), 	
			 '2013' => t('2013'),
			 '2014' => t('2014'),	
			 '2015' => t('2015'),	
		   ),
	);

	
	//SKPD
	$query = db_select('unitkerja', 'p');
	$query->fields('p', array('namasingkat','kodeuk'));
	$query->orderBy('kodedinas', 'ASC');
	$results = $query->execute();
	$optskpd = array();
	$optskpd['ZZ'] = 'SELURUH SKPD'; 
	if($results){
		foreach($results as $data) {
		  $optskpd[$data->kodeuk] = $data->namasingkat; 
		}
	}
	
	$form['formdata']['kodeuk'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#options' => $optskpd,
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $kodeuk,
	);

	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($tahun, $kodeuk, $showpersen, $shownumber) {

$arr_jenis = array();
$arr_anggaran = array();
$arr_realisasi = array();

//TABEL
$header = array (
	array('data' => 'Kode','width' => '10px', 'valign'=>'top'),
	array('data' => 'Uraian', 'valign'=>'top'),
	array('data' => 'Anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => '%', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('apbdrekap' . $tahun, 'a');
$query->fields('a', array('kodeakun', 'namaakun'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
$query->condition('a.kodeakun', '6', '<');
if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
$query->groupBy('a.kodeakun');
$query->orderBy('a.kodeakun');
$results = $query->execute();

$anggaran_netto = 0;
$realisasi_netto = 0;
foreach ($results as $datas) {

	if ($datas->kodeakun=='4') {
		$anggaran_netto = $datas->anggaran;
		$realisasi_netto = $datas->realisasi;
	} else {
		$anggaran_netto -= $datas->anggaran;
		$realisasi_netto -= $datas->realisasi;
	}
		

	$rows[] = array(
		array('data' => $datas->kodeakun, 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->namaakun . '</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $datas->realisasi)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('apbdrekap' . $tahun, 'a');
	$query->fields('a', array('kodekelompok', 'namakelompok'));
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
	$query->condition('a.kodeakun', $datas->kodeakun, '=');
	if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
	$query->groupBy('a.kodekelompok');
	$query->orderBy('a.kodekelompok');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$rows[] = array(
			array('data' => $data_kel->kodekelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_kel->namakelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)), 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('apbdrekap' . $tahun, 'a');
		$query->fields('a', array('kodejenis', 'namajenis'));
		$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
		$query->condition('a.kodekelompok', $data_kel->kodekelompok, '=');
		if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
		$query->groupBy('a.kodejenis');
		$query->orderBy('a.kodejenis');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {

			$rows[] = array(
				array('data' => $data_jen->kodejenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. ucfirst(strtolower($data_jen->namajenis)) . '</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//foreach ($results as $datas)			

		
	}

}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);

if ($kodeuk=='ZZ') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '6', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('apbdrekap' . $tahun, 'a');
	$query->fields('a', array('kodekelompok', 'namakelompok'));
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
	$query->condition('a.kodeakun', '6', '=');
	$query->groupBy('a.kodekelompok');
	$query->orderBy('a.kodekelompok');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodekelompok=='61') {
			$anggaran_netto_p = $data_kel->anggaran;
			$realisasi_netto_p = $data_kel->realisasi;
		} else {
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $data_kel->realisasi;
		}	

		$rows[] = array(
			array('data' => $data_kel->kodekelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_kel->namakelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)), 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('apbdrekap' . $tahun, 'a');
		$query->fields('a', array('kodejenis', 'namajenis'));
		$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
		$query->condition('a.kodekelompok', $data_kel->kodekelompok, '=');
		if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
		$query->groupBy('a.kodejenis');
		$query->orderBy('a.kodejenis');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {

			$rows[] = array(
				array('data' => $data_jen->kodejenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. ucfirst(strtolower($data_jen->namajenis)) . '</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//foreach ($results as $datas)			

		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
}

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($tahun, $kodeuk, $showpersen, $shownumber) {

$arr_jenis = array();
$arr_anggaran = array();
$arr_realisasi = array();

//TABEL
$header = array (
	array('data' => '<strong>KODE</strong>','width' => '50px', 'valign'=>'top'),
	array('data' => '<strong>URAIAN</strong>', 'width' => '330px','valign'=>'top'),
	array('data' => '<strong>ANGGARAN</strong>', 'width' => '90px', 'valign'=>'top'),
	array('data' => '<strong>REALISASI</strong>', 'width' => '90px', 'valign'=>'top'),
	array('data' => '<strong>PERSEN</strong>', 'width' => '45px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('apbdrekap' . $tahun, 'a');
$query->fields('a', array('kodeakun', 'namaakun'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
$query->condition('a.kodeakun', '6', '<');
if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
$query->groupBy('a.kodeakun');
$query->orderBy('a.kodeakun');
$results = $query->execute();

$anggaran_netto = 0;
$realisasi_netto = 0;
foreach ($results as $datas) {

	if ($datas->kodeakun=='4') {
		$anggaran_netto = $datas->anggaran;
		$realisasi_netto = $datas->realisasi;
	} else {
		$anggaran_netto -= $datas->anggaran;
		$realisasi_netto -= $datas->realisasi;
	}
		

	$rows[] = array(
		array('data' => $datas->kodeakun, 'width' => '50px', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . $datas->namaakun . '</strong>','width' => '330px',  'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->anggaran) . '</strong>', 'width' => '90px', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($datas->realisasi) . '</strong>','width' => '90px',  'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($datas->anggaran, $datas->realisasi)) . '</strong>', 'width' => '45px', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('apbdrekap' . $tahun, 'a');
	$query->fields('a', array('kodekelompok', 'namakelompok'));
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
	$query->condition('a.kodeakun', $datas->kodeakun, '=');
	if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
	$query->groupBy('a.kodekelompok');
	$query->orderBy('a.kodekelompok');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {
		$rows[] = array(
			array('data' => $data_kel->kodekelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_kel->namakelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)), 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('apbdrekap' . $tahun, 'a');
		$query->fields('a', array('kodejenis', 'namajenis'));
		$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
		$query->condition('a.kodekelompok', $data_kel->kodekelompok, '=');
		if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
		$query->groupBy('a.kodejenis');
		$query->orderBy('a.kodejenis');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {

			$rows[] = array(
				array('data' => $data_jen->kodejenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. ucfirst(strtolower($data_jen->namajenis)) . '</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//foreach ($results as $datas)			

		
	}

}	//foreach ($results as $datas)

//SURPLUS DEFIIT
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);

if ($kodeuk=='ZZ') {
	//PEMBIAYAAN
	$anggaran_netto_p = 0;
	$realisasi_netto_p = 0;

	$rows[] = array(
		array('data' => '6', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
		array('data' => '', 'align' => 'right', 'valign'=>'top'),
	);
	
	//KELOMPOK
	$query = db_select('apbdrekap' . $tahun, 'a');
	$query->fields('a', array('kodekelompok', 'namakelompok'));
	$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
	$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
	$query->condition('a.kodeakun', '6', '=');
	$query->groupBy('a.kodekelompok');
	$query->orderBy('a.kodekelompok');
	$results_kel = $query->execute();	
	
	foreach ($results_kel as $data_kel) {

		if ($data_kel->kodekelompok=='61') {
			$anggaran_netto_p = $data_kel->anggaran;
			$realisasi_netto_p = $data_kel->realisasi;
		} else {
			$anggaran_netto_p -= $data_kel->anggaran;
			$realisasi_netto_p -= $data_kel->realisasi;
		}	

		$rows[] = array(
			array('data' => $data_kel->kodekelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => $data_kel->namakelompok, 'align' => 'left', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->anggaran), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn($data_kel->realisasi), 'align' => 'right', 'valign'=>'top'),
			array('data' => apbd_fn1(apbd_hitungpersen($data_kel->anggaran, $data_kel->realisasi)), 'align' => 'right', 'valign'=>'top'),
		);		
		
		//JENIS
		$query = db_select('apbdrekap' . $tahun, 'a');
		$query->fields('a', array('kodejenis', 'namajenis'));
		$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
		$query->addExpression('SUM(a.realisasi/1000)', 'realisasi');
		$query->condition('a.kodekelompok', $data_kel->kodekelompok, '=');
		if ($kodeuk!='ZZ') $query->condition('a.kodeskpd', $kodeuk, '='); 
		$query->groupBy('a.kodejenis');
		$query->orderBy('a.kodejenis');
		$results_jen = $query->execute();	
		foreach ($results_jen as $data_jen) {

			$rows[] = array(
				array('data' => $data_jen->kodejenis, 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. ucfirst(strtolower($data_jen->namajenis)) . '</em>', 'align' => 'left', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->anggaran) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn($data_jen->realisasi) . '</em>', 'align' => 'right', 'valign'=>'top'),
				array('data' => '<em>'. apbd_fn1(apbd_hitungpersen($data_jen->anggaran, $data_jen->realisasi)) . '</em>', 'align' => 'right', 'valign'=>'top'),
			);
		
		}	//foreach ($results as $datas)			

		
	}
	
	//SURPLUS DEFIIT
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto_p) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto_p, $realisasi_netto_p)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);	

	//SILPA
	
	$anggaran_netto += $anggaran_netto_p;
	$realisasi_netto += $realisasi_netto_p;
	$rows[] = array(
		array('data' => '', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'align' => 'left', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($anggaran_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn($realisasi_netto) . '</strong>', 'align' => 'right', 'valign'=>'top'),
		array('data' => '<strong>' . apbd_fn1(apbd_hitungpersen($anggaran_netto, $realisasi_netto)) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	);
	
} 

//HEADER
if ($kodeuk=='ZZ')
	$skpd= 'KABUPATEN JEPARA';
else {
	$query = db_select('unitkerja', 'p');
	$query->fields('p', array('namauk','kodeuk'))
		  ->condition('kodeuk',$kodeuk,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$skpd= $data->namauk;
		}
	}	
}

$top[] = array (
		array('data' => '<strong>LAPORAN REALISASI ANGGARAN</strong>','width' => '575px', 'align'=>'center'),
	);
$top[] = array (
		array('data' => $skpd,'width' => '575px', 'align'=>'center'),
	);
$top[] = array (
		array('data' => 'BULAN ' . $tahun . ' TAHUN 2017','width' => '575px', 'align'=>'center'),
	);
$top[] = array (
		array('data' => '','width' => '575px', 'align'=>'center'),
	);

$headertop = array ();
$output_top = theme('table', array('header' => $headertop, 'rows' => $top ));

//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $output_top . $tabel_data;

}


?>


