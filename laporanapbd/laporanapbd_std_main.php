<?php
function laporanapbd_std_main($arg=NULL, $nama=NULL) {

	$exportpdf = arg(1);

	if ($exportpdf=='excel')  {	
		$output = laporanapbd_std_main_excel();
		header( "Content-Type: application/vnd.ms-excel" );
		header( "Content-disposition: attachment; filename=Penganggaran APBD 2018/2017.xls" );
		header("Pragma: no-cache"); 
		header("Expires: 0");
		echo $output;	
	} else {
		drupal_set_title('Penganggaran APBD ' . apbd_tahun() . '/' . apbd_tahun_lalu());
		//$output_form = drupal_get_form('laporanapbd_main_form');	
		$output_form = laporanapbd_std_main_form();	
		return drupal_render($output_form);
	}
 
}

 
function laporanapbd_std_main_form() {

if (apbd_client_type()=='m') {
	$sejuta = 1000000;
	$label_juta = '(juta)';	
	
} else {
	$sejuta = 1;
	$label_juta = '';
}
	
$kodeuk = 'ZZ';	

$agg_pendapata_total = 0; 
$agg_belanja_total = 0; 
$agg_pembiayaan_netto = 0; 

$agglalu_pendapata_total = 0; 
$agglalu_belanja_total = 0; 
$agglalu_pembiayaan_netto = 0; 
	
	//ROW 11
	$form['menu1']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	//I	
	$form['menu1']['m11'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' Ringkasan APBD<em><small class="span4 text-info pull-right">' . $label_juta  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu1']['m11']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		'#suffix' => '</table>',
	);
	$form['menu1']['m11']['tab11']['uraian11']= array(
		'#prefix' => '<tr><th>',
		'#type'         => 'item', 
		'#markup' => '<p>Uraian</p>', 
		'#suffix' => '</th>',
			
	);
	
	$form['menu1']['m11']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:10%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
	$form['menu1']['m11']['tab11']['kumlatif11']= array(
			'#prefix' => '<th style="width:10%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);
	$form['menu1']['m11']['tab11']['persen11']= array(
			'#prefix' => '<th style="color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th></tr>',
		);
		
db_set_active('akuntansi');
// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$arr_results = $results->fetchAllAssoc('kodea');
db_set_active();

foreach ($arr_results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agglalu = read_pendapatan_lalu($kodeuk, $datas->kodea);
	
	$uraian = $datas->uraian ;
	$anggaran = $datas->anggaran;
	
	$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agglalu / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen),
		'#suffix' => '</td></tr>',
	);
	
	
	$agg_pendapata_total = $datas->anggaran;
	$agglalu_pendapata_total = $agglalu;
	
	//KELOMPOK
	db_set_active('akuntansi');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();	
	
	foreach ($arr_results_kel as $data_kel) {
	
		$agglalu = read_pendapatan_lalu($kodeuk, $data_kel->kodek);
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($agglalu / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen),
			'#suffix' => '</td></tr>',
		);
	
		//JENIS
		db_set_active('akuntansi');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();
		
		foreach ($arr_results_jen as $data_jen) {

			$agglalu = read_pendapatan_lalu($kodeuk, $data_jen->kodej);
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$anggaran_s = l(apbd_fn($anggaran / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));

			$anggaran = $data_jen->anggaran;
			
			$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($agglalu / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . $anggaran_s . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['rowpersen_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => apbd_fn_persen_naikturun($persen),
				'#suffix' => '</td></tr>',
			);
		}
	}
}

//batas belanjan
$form['menu1']['m11']['tab11']['rowuraian_bb']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '</br>', 
	'#suffix' => '</td><tr>',
	
);				
			
// * BELANJA * //
//AKUN
db_set_active('akuntansi');
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('keg.inaktif', '0', '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$arr_results = $results->fetchAllAssoc('kodea');
db_set_active();

foreach ($arr_results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$agglalu = read_belanja_lalu($kodeuk, $datas->kodea);
	
	$anggaran = $datas->anggaran;
	$uraian = $datas->uraian;
	
	$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;>',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agglalu / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen),
		'#suffix' => '</td></tr>',
	);
	
	
	$agg_belanja_total = $datas->anggaran;
	$agglalu_belanja_total = $agglalu;
	
	//KELOMPOK
	db_set_active('akuntansi');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	
	foreach ($arr_results_kel as $data_kel) {

		$agglalu = read_belanja_lalu($kodeuk, $data_kel->kodek);
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($agglalu / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen), 
			'#suffix' => '</td></tr>',
		);
	
	
		//JENIS
		db_set_active('akuntansi');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('keg.inaktif', '0', '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();
		
		foreach ($arr_results_jen as $data_jen) {
			
			$agglalu = read_belanja_lalu($kodeuk, $data_jen->kodej);
			
			$anggaran = $data_jen->anggaran;
			
			$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$anggaran_s = l(apbd_fn($anggaran / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($agglalu / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . $anggaran_s . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['rowpersen_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => apbd_fn_persen_naikturun($persen), 
				'#suffix' => '</td></tr>',
			);
	
	
		}
	}
}

//SURPLUS/DEFISIT
$agg_surplus = $agg_pendapata_total - $agg_belanja_total;
$agglalu_surplus = $agglalu_pendapata_total - $agglalu_belanja_total;

$persen = apbd_hitungpersen_naikturun($agglalu_surplus, $agg_surplus);	

$form['menu1']['m11']['tab11']['rowuraian_sd']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '<strong>SURPLUS / (DEFISIT)</strong>', 
	'#suffix' => '</td>',
	
);				
$form['menu1']['m11']['tab11']['rowangg_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn($agglalu_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);

$form['menu1']['m11']['tab11']['rowkmltf_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn($agg_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);
$form['menu1']['m11']['tab11']['rowpersen_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => apbd_fn_persen_naikturun_bold($persen),
	'#suffix' => '</td></tr>',
);	

//batas belanjan
$form['menu1']['m11']['tab11']['rowuraian_bp']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '</br>', 
	'#suffix' => '</td><tr>',
	
);

//PEMBIAYAAN
$agg_pembiayaan_netto += 0;
$agglalu_pembiayaan_netto += 0;
	
$form['menu1']['m11']['tab11']['rowuraian_P']= array(
	'#prefix' => '<tr><td valign="top" style="font-size:20px;>',
	'#type'   => 'item',  
	'#markup' => '<strong>PEMBIAYAAN</strong>', 
	'#suffix' => '</td>',
	
);				
$form['menu1']['m11']['tab11']['rowangg_P']= array(
	'#prefix' => '<td valign="top">',
	'#type'         => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
);

$form['menu1']['m11']['tab11']['rowkmltf_P']= array(
	'#prefix' => '<td valign="top">',
	'#type'         => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
);
$form['menu1']['m11']['tab11']['rowpersen_P']= array(
	'#prefix' => '<td valign="top">',
	'#type'         => 'item', 
	'#markup' => '', 
	'#suffix' => '</td></tr>',
);
	
	//KELOMPOK
	db_set_active('akuntansi');	
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	foreach ($arr_results_kel as $data_kel) {

		$agglalu = read_pembiayaan_lalu($data_kel->kodek);
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($agglalu / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen),
			'#suffix' => '</td></tr>',
		);
	
		if ($data_kel->kodek=='61') {
			$agg_pembiayaan_netto += $data_kel->anggaran;
			$agglalu_pembiayaan_netto += $agglalu;

		} else	{	
			$agg_pembiayaan_netto -= $data_kel->anggaran;
			$agglalu_pembiayaan_netto -= $agglalu;
		}
		
		//JENIS
		db_set_active('akuntansi');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();		
		foreach ($arr_results_jen as $data_jen) {
			
			$agglalu = read_pembiayaan_lalu($data_jen->kodej);
			
			if (($data_jen->anggaran+$agglalu)>0) {
				//$uraian = ucwords(strtolower($data_jen->uraian));
				$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
				
				$anggaran = $data_jen->anggaran;
				$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
				
				$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
					'#prefix' => '<tr><td valign="top">',
					'#type'   => 'item',  
					'#markup' => $uraian, 
					'#suffix' => '</td>',
					
				);				
				$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($agglalu / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
		
				$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($anggaran / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
				$form['menu1']['m11']['tab11']['rowpersen_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => apbd_fn_persen_naikturun($persen),
					'#suffix' => '</td></tr>',
				);
			}
		}
	}

	//NETTO
	$form['menu1']['m11']['tab11']['rowuraian7']= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>PEMBIAYAAN NETTO</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agglalu_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agg_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen), 
		'#suffix' => '</td></tr>',
	);	

//batas belanjan
$form['menu1']['m11']['tab11']['rowuraian_bD']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '</br>', 
	'#suffix' => '</td><tr>',
	
);	
	//SILPA
	$anggaran_silpa = 0;
	$agglalu_silpa = 0;
	
	$anggaran_silpa = $agg_surplus + $agg_pembiayaan_netto;
	$agglalu_silpa = $agglalu_surplus + $agglalu_pembiayaan_netto;
	
	$persen = apbd_hitungpersen_naikturun($agglalu_silpa, $anggaran_silpa);	
	
	$form['menu1']['m11']['tab11']['rowuraian8']= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agglalu_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen),
		'#suffix' => '</td></tr>',
	);
	$btn .= "&nbsp;" . apbd_button_print('');
	$form['menu1']['m11']['tab11']['download']= array(
		'#prefix' => '<td></td><td></td><td></td><td valign="center" style="">',
		'#type'         => 'item', 
		'#attributes' => array('class' => array('btn btn-primary')),
		'#suffix' => "&nbsp;<a href='/laporanapbdstd/excel' class='btn btn-xs btn-success pull-right'><span class='glyphicon glyphicon-download-alt' aria-hidden='true'></span>Download</a></td>",
	);
	return $form;
}


function laporanapbd_std_main_excel() {
	
	$kodeuk = 'ZZ';	

	$agg_pendapata_total = 0; 
	$agg_belanja_total = 0; 
	$agg_pembiayaan_netto = 0; 

	$agglalu_pendapata_total = 0; 
	$agglalu_belanja_total = 0; 
	$agglalu_pembiayaan_netto = 0;
	
	$rows[]=array(
		array('data' => 'Uraian','width' => '600px','align'=>'left','style'=>'font-size:35px;'),
		array('data' => '2017','width' => '120px','align'=>'center;','style'=>'font-size:35px;'),
		array('data' => '2018','width' => '120px','align'=>'right','style'=>'font-size:35px;'),
		array('data' => '%','width' => '120px','align'=>'right','style'=>'font-size:35px;'),
	);
	db_set_active('akuntansi');
	// * PENDAPATAN * //
	//AKUN
	$query = db_select('anggaran', 'a');
	$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
	$query->fields('a', array('kodea', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('a.kodea', '4', '='); 
	$query->groupBy('a.kodea');
	$query->orderBy('a.kodea');
	$results = $query->execute();
	$arr_results = $results->fetchAllAssoc('kodea');
	db_set_active();

	foreach ($arr_results as $datas) {
		//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		
		$agglalu = read_pendapatan_lalu($kodeuk, $datas->kodea);
		
		$uraian = $datas->uraian ;
		$anggaran = $datas->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	
	
	$agg_pendapata_total = $datas->anggaran;
	$agglalu_pendapata_total = $agglalu;
	
	//KELOMPOK
	db_set_active('akuntansi');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();	
	
	foreach ($arr_results_kel as $data_kel) {
	
		$agglalu = read_pendapatan_lalu($kodeuk, $data_kel->kodek);
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
		
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	
	//JENIS
	db_set_active('akuntansi');
	$query = db_select('jenis', 'j');
	$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
	$query->fields('j', array('kodej', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('j.kodek', $data_kel->kodek, '='); 
	$query->groupBy('j.kodej');
	$query->orderBy('j.kodej');
	$results_jen = $query->execute();	
	$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
	db_set_active();
	
	foreach ($arr_results_jen as $data_jen) {

		$agglalu = read_pendapatan_lalu($kodeuk, $data_jen->kodej);
		
		//$uraian = ucwords(strtolower($data_jen->uraian));
		$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
		$anggaran_s = l(apbd_fn($anggaran / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));

		$anggaran = $data_jen->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran_s),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	}
	}
	}
	$rows[]=array(
		array('data' => '' ,'width' => '600px','align'=>'left','style'=>'font-size:20px;'),
		array('data' => '','width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => '','width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => '','width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
				
	// * BELANJA * //
	//AKUN
	db_set_active('akuntansi');
	$query = db_select('anggaran', 'a');
	$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('a', array('kodea', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('a.kodea', '5', '='); 
	$query->groupBy('a.kodea');
	$query->orderBy('a.kodea');
	$results = $query->execute();
	$arr_results = $results->fetchAllAssoc('kodea');
	db_set_active();

	foreach ($arr_results as $datas) {
		//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
		
		$agglalu = read_belanja_lalu($kodeuk, $datas->kodea);
		
		$anggaran = $datas->anggaran;
		$uraian = $datas->uraian;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	}
	$agg_belanja_total = $datas->anggaran;
	$agglalu_belanja_total = $agglalu;
	
	//KELOMPOK
	db_set_active('akuntansi');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	
	foreach ($arr_results_kel as $data_kel) {

		$agglalu = read_belanja_lalu($kodeuk, $data_kel->kodek);
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	
	
	//JENIS
	db_set_active('akuntansi');
	$query = db_select('jenis', 'j');
	$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('j', array('kodej', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('j.kodek', $data_kel->kodek, '='); 
	$query->groupBy('j.kodej');
	$query->orderBy('j.kodej');
	$results_jen = $query->execute();	
	$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
	db_set_active();
	
	foreach ($arr_results_jen as $data_jen) {
		
		$agglalu = read_belanja_lalu($kodeuk, $data_jen->kodej);
		
		$anggaran = $data_jen->anggaran;
		
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
		
		//$uraian = ucwords(strtolower($data_jen->uraian));
		$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
		$anggaran_s = l(apbd_fn($anggaran / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	}
	}
	
	//SURPLUS/DEFISIT
	$agg_surplus = $agg_pendapata_total - $agg_belanja_total;
	$agglalu_surplus = $agglalu_pendapata_total - $agglalu_belanja_total;

	$persen = apbd_hitungpersen_naikturun($agglalu_surplus, $agg_surplus);	
	
	$rows[]=array(
		array('data' => 'SURPLUS/ (DEFISIT)' ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu_surplus),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($agg_surplus / $sejuta),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	$rows[]=array(
		array('data' => '' ,'width' => '600px','align'=>'left','style'=>'font-size:20px;'),
		array('data' => '','width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => '','width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => '','width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	$agg_pembiayaan_netto += 0;
	$agglalu_pembiayaan_netto += 0;
	$rows[]=array(
		array('data' => 'PEMBIAYAAN' ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => '','width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => '','width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => '','width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	
	//KELOMPOK
	db_set_active('akuntansi');	
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	foreach ($arr_results_kel as $data_kel) {

		$agglalu = read_pembiayaan_lalu($data_kel->kodek);
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold(apbd_fn($persen)),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	
	if ($data_kel->kodek=='61') {
		$agg_pembiayaan_netto += $data_kel->anggaran;
		$agglalu_pembiayaan_netto += $agglalu;

	} else	{	
		$agg_pembiayaan_netto -= $data_kel->anggaran;
		$agglalu_pembiayaan_netto -= $agglalu;
	}
	
	//JENIS
	db_set_active('akuntansi');
	$query = db_select('jenis', 'j');
	$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
	$query->fields('j', array('kodej', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('j.kodek', $data_kel->kodek, '='); 
	$query->groupBy('j.kodej');
	$query->orderBy('j.kodej');
	$results_jen = $query->execute();	
	$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
	db_set_active();		
	foreach ($arr_results_jen as $data_jen) {
		
		$agglalu = read_pembiayaan_lalu($data_jen->kodej);
		
		if (($data_jen->anggaran+$agglalu)>0) {
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			
			$anggaran = $data_jen->anggaran;
			$persen = apbd_hitungpersen_naikturun($agglalu, $anggaran);	
	$rows[]=array(
		array('data' => $uraian ,'width' => '600px','align'=>'left','style'=>'font-size:12px;'),
		array('data' => apbd_fn($agglalu),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	}
	}
	}
	$rows[]=array(
		array('data' => 'PEMBIAYAAN NETTO' ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu_pembiayaan_netto),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($agg_pembiayaan_netto),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	//SILPA
	$anggaran_silpa = 0;
	$agglalu_silpa = 0;
	
	$anggaran_silpa = $agg_surplus + $agg_pembiayaan_netto;
	$agglalu_silpa = $agglalu_surplus + $agglalu_pembiayaan_netto;
	
	$persen = apbd_hitungpersen_naikturun($agglalu_silpa, $anggaran_silpa);	
	$rows[]=array(
		array('data' => 'SISA LEBIH ANGGARAN TAHUN BERJALAN' ,'width' => '600px','align'=>'left','style'=>'font-size:12px;font-weight:bold;'),
		array('data' => apbd_fn($agglalu_silpa),'width' => '120px','align'=>'left;','style'=>'font-size:12px;'),
		array('data' => apbd_fn($anggaran_silpa),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
		array('data' => apbd_fn_persen_naikturun_bold($persen),'width' => '120px','align'=>'right','style'=>'font-size:12px;'),
	);
	
	
	//$output = theme('table', array('header' => $header, 'rows' => $rows ));
	
	//$rows=null;
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;
	
}


?>