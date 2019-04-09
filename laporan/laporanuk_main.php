<?php
function laporanuk_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    $cetakpdf = '';
	
	if (arg(2)!=null) {
		switch($arg) {
			case 'filter':
				$akun = arg(2);
				$kelompok = arg(3);			
				$bulan = arg(4);
				$cetakpdf = arg(5);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$akun = '5';	
		$bulan = date('n');		//variable_get('apbdtahun', 0);
		$kelompok = 'SEMUA';
	}

	if ($akun=='') $akun = '5';	
	if ($bulan=='') $bulan = date('n');		//variable_get('apbdtahun', 0);
	if ($kelompok=='') $kelompok = 'SEMUA';
	
	if ($cetakpdf=='pdf') {
		$output = gen_report_realisasi_print($akun, $kelompok, $bulan);
		print_pdf_p($output);
		
	} else {
		if ($akun=='4')
			drupal_set_title('Realisasi Pendapatan per SKPD');
		else
			drupal_set_title('Realisasi Belanja per SKPD');
		
		$output_form = drupal_get_form('laporanuk_main_form');
		$output = gen_report_realisasi($akun, $kelompok, $bulan);
		
		//$btn = '';
		$btn = l('Cetak', 'laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
		
		return drupal_render($output_form) . $btn . $output . $btn;
	}	
	
}

function laporanuk_main_form_submit($form, &$form_state) {
	$akun= $form_state['values']['akun'];
	$bulan= $form_state['values']['bulan'];
	$kelompok= $form_state['values']['kelompok'];
	
	$uri = 'laporanskpd/filter/' . $akun . '/' . $kelompok . '/' . $bulan;
	drupal_goto($uri);
	
}


function laporanuk_main_form($form, &$form_state) {
	
	$akun = '5';
	$bulan = date('n');
	$kelompok = 'SEMUA';
	
	if(arg(2)!=null){
		$akun = arg(2);
		$kelompok = arg(3);			
		$bulan = arg(4);
	}

	if ($akun=='') $akun = '5';	
	if ($bulan=='') $bulan = date('n');		//variable_get('apbdtahun', 0);
	if ($kelompok=='') $kelompok = 'SEMUA';
	
	if ($akun=='4')
		$akun_str ='|PENDAPATAN';
	else
		$akun_str ='|BELANJA';
		
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> $bulan . '|' . $kelompok . $akun_str . '<em><small class="text-info pull-right">klik disini utk  menampilkan/menyembunyikan pilihan data</small></em>',
		//'#attributes' => array('class' => array('container-inline')),
		'#collapsible' => TRUE, 
		'#collapsed' => TRUE,        
	);	

	$form['formdata']['akun']= array(
		'#type' => 'value',		//'radios', 
		'#value' => $akun,
	);
	
	$form['formdata']['kelompok']= array(
		'#type' => 'select',		//'radios', 
		'#title' => t('Kelompok'), 
		'#default_value' => $kelompok,
		
		'#options' => array('SEMUA'=>'SEMUA', 
							'0'=>'DINAS/BADAN/KANTOR',
							'1'=>'KECAMATAN',
							'2'=>'PUSKESMAS',
							'3'=>'SEKOLAH',
							'4'=>'UPT DIKPORA'),	
	);		
	
	$form['formdata']['bulan'] = array(
		'#type' => 'select',
		'#title' => 'Bulan',
		'#default_value' => $bulan,	
		'#options' => array(	
			 '0' => t('SETAHUN'), 	
			 '1' => t('JANUARI'), 	
			 '2' => t('FEBRUARI'),
			 '3' => t('MARET'),	
			 '4' => t('APRIL'),	
			 '5' => t('MEI'),	
			 '6' => t('JUNI'),	
			 '7' => t('JULI'),	
			 '8' => t('AGUSTUS'),	
			 '9' => t('SEPTEMBER'),	
			 '10' => t('OKTOBER'),	
			 '11' => t('NOVEMBER'),	
			 '12' => t('DESEMBER'),	
		   ),
	);
	
 
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => apbd_button_tampilkan(),
		'#attributes' => array('class' => array('btn btn-success')),
	);
	return $form;
}

function gen_report_realisasi($akun, $kelompok, $bulan) {

$total_anggaran = 0;
$total_realisasi = 0;
//TABEL
$header = array (
	array('data' => 'No','width' => '10px', 'valign'=>'top'),
	array('data' => '', 'width' => '5px','valign'=>'top'), 
	array('data' => 'SKPD','field'=> 'namaskpd' ,'valign'=>'top'),
	array('data' => 'Anggaran','field'=> 'anggaran', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Realiasi', 'field'=> 'realisasi', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'Persen','field'=> 'persen', 'width' => '15px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('apbdrekap', 'a')->extend('TableSort');
if ($kelompok!='SEMUA') { 
	$query->innerJoin('unitkerja', 'u', 'a.kodeskpd=u.kodeuk');
	$query->condition('u.kelompok', $kelompok, '=');
}
	
$query->fields('a', array('kodeskpd', 'namaskpd'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->addExpression('SUM(a.realisasikum' . $bulan . ')/SUM(a.anggaran2)', 'persen');
$query->condition('a.kodeakun', $akun, '=');
$query->groupBy('a.kodeskpd');
$query->orderByHeader($header);
$query->orderBy('persen');
$results = $query->execute();

$no=0;
$uri = 'public://';
$path= file_create_url($uri);	

foreach ($results as $datas) {
	
	$total_anggaran += $datas->anggaran;
	$total_realisasi += $datas->realisasi;
	
	$persen = $datas->persen * 100;
	if ($datas->realisasi <= $datas->anggaran) {
		
		if ($persen <= 20)
			if ($datas->anggaran==0)
				$imgstatus = "<img src='" . $path . "/icon/progress00.png'>";
			else
				$imgstatus = "<img src='" . $path . "/icon/progress20.png'>";
			
		else if ($persen <= 40)
			$imgstatus = "<img src='" . $path . "/icon/progress40.png'>";
		else if ($persen <= 60)
			$imgstatus = "<img src='" . $path . "/icon/progress60.png'>";
		else if ($persen <= 80)
			$imgstatus = "<img src='" . $path . "/icon/progress80.png'>";
		else
			$imgstatus = "<img src='" . $path . "/icon/progress100.png'>";
		
	} else {
		//$imgstatus = "<img src='/files/icon/progress-ex.png'>";
		$imgstatus = "<img src='" . $path . "/icon/progress-ex.png'>";
	}	
		
	$no++;
	$rows[] = array(
		array('data' => $no, 'align' => 'left', 'valign'=>'top'),
		array('data' => $imgstatus, 'align' => 'left', 'valign'=>'top'),
		array('data' => $datas->namaskpd, 'align' => 'left', 'valign'=>'top'),
		array('data' => apbd_fn($datas->anggaran), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn($datas->realisasi), 'align' => 'right', 'valign'=>'top'),
		array('data' => apbd_fn1($persen), 'align' => 'right', 'valign'=>'top'),
	);

}	//foreach ($results as $datas)

//TOTAL
$persen = apbd_hitungpersen($total_anggaran, $total_realisasi);
if ($total_realisasi <= $total_anggaran) {
	 
	if ($persen <= 20)
		if ($datas->anggaran==0)
			$imgstatus = "<img src='" . $path . "/icon/progress00.png'>";
		else
			$imgstatus = "<img src='" . $path . "/icon/progress20.png'>";
		
	else if ($persen <= 40)
		$imgstatus = "<img src='" . $path . "/icon/progress40.png'>";
	else if ($persen <= 60)
		$imgstatus = "<img src='" . $path . "/icon/progress60.png'>";
	else if ($persen <= 80)
		$imgstatus = "<img src='" . $path . "/icon/progress80.png'>";
	else
		$imgstatus = "<img src='" . $path . "/icon/progress100.png'>";
	
} else {
	//$imgstatus = "<img src='/files/icon/progress-ex.png'>";
	$imgstatus = "<img src='" . $path . "/icon/progress-ex.png'>";
}	
$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => $imgstatus, 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($total_anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($total_realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1($persen) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);



//RENDER	
$tabel_data = theme('table', array('header' => $header, 'rows' => $rows ));

//return drupal_render($apbdrupiah) . $chart_persen . $tabel_data;
return $tabel_data;

}

function gen_report_realisasi_print($akun, $kelompok, $bulan) {

$total_anggaran = 0;
$total_realisasi = 0;

//TABEL
$header = array (
	array('data' => 'NO','width' => '30px', 'valign'=>'top'),
	array('data' => 'SKPD','width' => '350px',  'valign'=>'top'),
	array('data' => 'ANGGARAN', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'REALISASI', 'width' => '90px', 'valign'=>'top'),
	array('data' => 'PERSEN', 'width' => '45px', 'valign'=>'top'),
);
$rows = array();

//AKUN
$query = db_select('apbdrekap', 'a');
if ($kelompok!='SEMUA') { 
	$query->innerJoin('unitkerja', 'u', 'a.kodeskpd=u.kodeuk');
	$query->condition('u.kelompok', $kelompok, '=');
}
	
$query->fields('a', array('kodeskpd', 'namaskpd'));
$query->addExpression('SUM(a.anggaran2/1000)', 'anggaran');
$query->addExpression('SUM(a.realisasikum' . $bulan . '/1000)', 'realisasi');
$query->addExpression('SUM(a.realisasikum' . $bulan . ')/SUM(a.anggaran2)', 'persen');
$query->condition('a.kodeakun', $akun, '=');
$query->groupBy('a.kodeskpd');
$query->orderBy('persen');
$results = $query->execute();

$no=0;
$uri = 'public://';
$path= file_create_url($uri);	

foreach ($results as $datas) {
	
	$persen = $datas->persen * 100;
	$total_anggaran += $datas->anggaran;
	$total_realisasi += $datas->realisasi;
		
	$no++;
	$rows[] = array(
		array('data' => $no . '.' , 'align' => 'right','width' => '30px', 'valign'=>'top'),
		array('data' => $datas->namaskpd, 'align' => 'left','width' => '350px', 'valign'=>'top'),
		array('data' => apbd_fn($datas->anggaran), 'align' => 'right','width' => '90px', 'valign'=>'top'),
		array('data' => apbd_fn($datas->realisasi), 'align' => 'right', 'width' => '90px','valign'=>'top'),
		array('data' => apbd_fn1($persen), 'align' => 'right','width' => '45px', 'valign'=>'top'),
	);
	


}	//foreach ($results as $datas)

//TOTAL
$persen = apbd_hitungpersen($total_anggaran, $total_realisasi);

$rows[] = array(
	array('data' => '', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>TOTAL</strong>', 'align' => 'left', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($total_anggaran) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn($total_realisasi) . '</strong>', 'align' => 'right', 'valign'=>'top'),
	array('data' => '<strong>' . apbd_fn1($persen) . '</strong>', 'align' => 'right', 'valign'=>'top'),
);

if ($akun=='4') 
	$top[] = array (
					array('data' => '<strong>LAPORAN REALISASI PENDAPATAN PER SKPD</strong>','width' => '575px', 'align'=>'center'),
					);
else
	$top[] = array (
					array('data' => '<strong>LAPORAN REALISASI BELANJA PER SKPD</strong>','width' => '575px', 'align'=>'center'),
					);

$top[] = array ( 
				array('data' => 'BULAN ' . $bulan . ' TAHUN 2017','width' => '575px', 'align'=>'center'),
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


