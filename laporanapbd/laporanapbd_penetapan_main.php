<?php
function laporanapbd_penetapan_main($arg=NULL, $nama=NULL) {

drupal_set_title('APBD Penetapan ' . apbd_tahun());
//$output_form = drupal_get_form('laporanapbd_main_form');	
$output_form = laporanapbd_penetapan_main_form();	
return drupal_render($output_form);
}


function laporanapbd_penetapan_main_form() {
if (apbd_client_type()=='m') {
	$semilyar = 1000000000; 
	$sejuta = 1000000;
	$label_milyar = '(juta)';	
	
} else {
	$semilyar = 1; 
	$sejuta = 1;
	$label_milyar = '';
}

$kodeuk = 'ZZ';	

$agg_pendapata_total = 0; 
$agg_belanja_total = 0; 
$agg_pembiayaan_netto = 0; 

$tsd_pendapata_total = 0; 
$tsd_belanja_total = 0; 
$tsd_pembiayaan_netto = 0; 
	
	//ROW 11
	$form['menu1']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	//I	
	$form['menu1']['m11'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' Penundaan APBD Penetapan<em><small class="span4 text-info pull-right">' . $label_milyar  .  '</small></em>',
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
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
	$form['menu1']['m11']['tab11']['kumlatif11']= array(
			'#prefix' => '<th style="width:10%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Penundaan</p>', 
			'#suffix' => '</th>',
		);
	$form['menu1']['m11']['tab11']['naikturun11']= array(
			'#prefix' => '<th style="width:10%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Tersedia</p>', 
			'#suffix' => '</th></tr>',
		);
		
db_set_active('anggaran');
// * PENDAPATAN * //
//AKUN
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperuk', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.jumlah)', 'tersedia');
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$arr_results = $results->fetchAllAssoc('kodea');
db_set_active();

foreach ($arr_results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$uraian = $datas->uraian ;
	$anggaran = $datas->anggaran;
	$tersedia = $datas->tersedia;
	
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($tersedia / $sejuta) . '</p></strong>', 
		'#suffix' => '</td></tr>',
	);
	
	
	$tsd_pendapata_total = $tersedia;
	$agg_pendapata_total = $anggaran;
	
	//KELOMPOK
	db_set_active('anggaran');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlah)', 'tersedia');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();	
	
	foreach ($arr_results_kel as $data_kel) {
	
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		$tersedia = $data_kel->tersedia;
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['nt_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($tersedia / $sejuta) . '</p></strong>', 
			'#suffix' => '</td></tr>',
		);
	
		//JENIS
		db_set_active('anggaran');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperuk', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'tersedia');
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();
		
		foreach ($arr_results_jen as $data_jen) {

			$anggaran = $data_jen->anggaran;
			$tersedia = $data_jen->tersedia;
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$tsd_s = l(apbd_fn($tersedia / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));

			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($anggaran / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
				
			$form['menu1']['m11']['tab11']['tsd_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . $tsd_s . '</p>', 
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
db_set_active('anggaran');
$query = db_select('anggaran', 'a');
$query->innerJoin('anggperkeg', 'ag', 'a.kodea=left(ag.kodero,1)');
$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
$query->fields('a', array('kodea', 'uraian'));
$query->addExpression('SUM(ag.anggaran)', 'tersedia');
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
	
	$anggaran = $datas->anggaran;
	$tersedia = $datas->tersedia;
	$uraian = $datas->uraian;
	
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;>',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['nt_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($tersedia / $sejuta) . '</p></strong>', 
		'#suffix' => '</td></tr>',
	);
	
	
	$tsd_belanja_total = $tersedia;
	$agg_belanja_total = $anggaran;
	
	//KELOMPOK
	db_set_active('anggaran');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.anggaran)', 'tersedia');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	
	foreach ($arr_results_kel as $data_kel) {

		$anggaran = $data_kel->anggaran;
		$tersedia = $data_kel->tersedia;
		
		$uraian = $data_kel->uraian;
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['nt_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($tersedia / $sejuta) . '</p></strong>', 
			'#suffix' => '</td></tr>',
		);		
	
	
		//JENIS
		db_set_active('anggaran');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperkeg', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.anggaran)', 'tersedia');
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('keg.inaktif', '0', '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();
		
		foreach ($arr_results_jen as $data_jen) {
			
			$anggaran = $data_jen->anggaran;
			$tersedia = $data_jen->tersedia;
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$tsd_s = l(apbd_fn($tersedia / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($anggaran / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['nt_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . $tsd_s . '</p>',  
				'#suffix' => '</td></tr>',
			);			
	
	
		}
	}
}

//SURPLUS/DEFISIT
$tsd_surplus = $tsd_pendapata_total - $tsd_belanja_total;
$agg_surplus = $agg_pendapata_total - $agg_belanja_total;

$form['menu1']['m11']['tab11']['rowuraian_sd']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '<strong>SURPLUS / (DEFISIT)</strong>', 
	'#suffix' => '</td>',
	
);				
$form['menu1']['m11']['tab11']['rowangg_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn($agg_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);

$form['menu1']['m11']['tab11']['rowkmltf_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn(($tsd_surplus-$tsd_surplus) / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);
$form['menu1']['m11']['tab11']['nt_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn($tsd_surplus / $sejuta) . '</p></strong>', 
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
$tsd_pembiayaan_netto += 0;
$agg_pembiayaan_netto += 0;
	
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
	db_set_active('anggaran');	
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperda', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlah)', 'tersedia');
	$query->addExpression('SUM(ag.jumlah)', 'anggaran');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	foreach ($arr_results_kel as $data_kel) {

		$anggaran = $data_kel->anggaran;
		$tersedia = $data_kel->tersedia;
		
		$uraian = $data_kel->uraian;
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['nt_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($tersedia / $sejuta) . '</p></strong>', 
			'#suffix' => '</td></tr>',
		);		
	
		if ($data_kel->kodek=='61') {
			$tsd_pembiayaan_netto += $tersedia;
			$agg_pembiayaan_netto += $anggaran;

		} else	{	
			$tsd_pembiayaan_netto -= $tersedia;
			$agg_pembiayaan_netto -= $anggaran;
		}
		
		//JENIS
		db_set_active('anggaran');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlah)', 'tersedia');
		$query->addExpression('SUM(ag.jumlah)', 'anggaran');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();		
		foreach ($arr_results_jen as $data_jen) {
			
			$anggaran = $data_jen->anggaran;
			$tersedia = $data_jen->tersedia;
			
			if (($tersedia+$anggaran)>0) {
				//$uraian = ucwords(strtolower($data_jen->uraian));
				$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
				
				$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
					'#prefix' => '<tr><td valign="top">',
					'#type'   => 'item',  
					'#markup' => $uraian, 
					'#suffix' => '</td>',
					
				);				
				$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($anggaran / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
		
				$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn(($anggaran-$tersedia) / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
				$form['menu1']['m11']['tab11']['nt_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($tersedia / $sejuta) . '</p>', 
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
		'#markup' => '<strong><p align="right">' . apbd_fn($agg_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn(($agg_pembiayaan_netto-$tsd_pembiayaan_netto) / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['nt_7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($tsd_pembiayaan_netto / $sejuta) . '</p></strong>', 
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
	$tsd_silpa = 0;
	$agg_silpa = 0;
	
	$tsd_silpa = $tsd_surplus + $tsd_pembiayaan_netto;
	$agg_silpa = $agg_surplus + $agg_pembiayaan_netto;
	
	$form['menu1']['m11']['tab11']['rowuraian8']= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agg_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn(($agg_silpa-$tsd_silpa) / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['nt_8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($tsd_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td></tr>',
	);				
		
	return $form;
}



?>