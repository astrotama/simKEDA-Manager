<?php
function laporanapbd_periode_main($arg=NULL, $nama=NULL) {

drupal_set_title('Penganggaran APBD ' . apbd_tahun());

//$output_form = laporanapbd_periode_main_form();	
$output_form = laporanapbd_periode_belum_form();	
return drupal_render($output_form);
}

function laporanapbd_periode_belum_form() {
	$form['form']= array(
		'#type'         => 'item', 
		'#markup' => '<h4>Belum ada anggaran perubahan</h4>', 
			
	);
	return $form;
}	

function laporanapbd_periode_main_form() {

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

$aggpenetapan_pendapata_total = 0; 
$aggpenetapan_belanja_total = 0; 
$aggpenetapan_pembiayaan_netto = 0; 
	
	//ROW 11
	$form['menu1']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	//I	
	$form['menu1']['m11'] = array(
		//'#prefix' => '<div class="col-md-12">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' APBD Penatapan & Perubahan<em><small class="span4 text-info pull-right">' . $label_juta  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		//'#suffix' => '</div>',
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
			'#markup' => '<p align="right">Penetapan</p>', 
			'#suffix' => '</th>',
		);	
	$form['menu1']['m11']['tab11']['kumlatif11']= array(
			'#prefix' => '<th style="width:10%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Perubahan</p>', 
			'#suffix' => '</th>',
		);
	$form['menu1']['m11']['tab11']['naikturun11']= array(
			'#prefix' => '<th style="width:8%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">+/-</p>', 
			'#suffix' => '</th>',
		);
	$form['menu1']['m11']['tab11']['persen11']= array(
			'#prefix' => '<th style="width:5%; color:black;">',
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
$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
$query->addExpression('SUM(ag.jumlah)', 'perubahan');
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$arr_results = $results->fetchAllAssoc('kodea');
db_set_active();

foreach ($arr_results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$uraian = $datas->uraian ;
	$aggpenetapan = $datas->penetapan;
	$anggaran = $datas->perubahan;
	
	$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['nt_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_naikturun_bold(($anggaran-$aggpenetapan) / $sejuta), 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen),
		'#suffix' => '</td></tr>',
	);
	
	
	$agg_pendapata_total = $anggaran;
	$aggpenetapan_pendapata_total = $aggpenetapan;
	
	//KELOMPOK
	db_set_active('akuntansi');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperuk', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->fields('k', array('kodek', 'uraian'));
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
	$query->addExpression('SUM(ag.jumlah)', 'perubahan');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();	
	
	foreach ($arr_results_kel as $data_kel) {
	
		$uraian = $data_kel->uraian;
		$aggpenetapan = $data_kel->penetapan;
		$anggaran = $data_kel->perubahan;
		
		$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['nt_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_naikturun_bold(($anggaran-$aggpenetapan) / $sejuta),  
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
		$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
		$query->addExpression('SUM(ag.jumlah)', 'perubahan');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();
		
		foreach ($arr_results_jen as $data_jen) {

			$aggpenetapan = $data_jen->penetapan;
			$anggaran = $data_jen->perubahan;
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$anggaran_s = l(apbd_fn($anggaran / $sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));

			
			$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . $anggaran_s . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['nt_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => apbd_fn_naikturun(($anggaran-$aggpenetapan) / $sejuta), 
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
$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
$query->addExpression('SUM(ag.jumlah)', 'perubahan');
$query->condition('keg.inaktif', '0', '='); 
$query->condition('a.kodea', '5', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$arr_results = $results->fetchAllAssoc('kodea');
db_set_active();

foreach ($arr_results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	$aggpenetapan = $datas->penetapan;
	$anggaran = $datas->perubahan;
	$uraian = $datas->uraian;
	
	$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
	
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;>',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['nt_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_naikturun_bold(($anggaran-$aggpenetapan) / $sejuta), 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen),
		'#suffix' => '</td></tr>',
	);
	
	
	$agg_belanja_total = $anggaran;
	$aggpenetapan_belanja_total = $aggpenetapan;
	
	//KELOMPOK
	db_set_active('akuntansi');
	$query = db_select('kelompok', 'k');
	$query->innerJoin('anggperkeg', 'ag', 'k.kodek=left(ag.kodero,2)');
	$query->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=ag.kodekeg');
	$query->fields('k', array('kodek', 'uraian'));
	$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
	$query->addExpression('SUM(ag.jumlah)', 'perubahan');
	$query->condition('keg.inaktif', '0', '='); 
	$query->condition('k.kodea', $datas->kodea, '='); 
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	
	foreach ($arr_results_kel as $data_kel) {

		$aggpenetapan = $data_kel->penetapan;
		$anggaran = $data_kel->perubahan;
		
		$uraian = $data_kel->uraian;
		
		$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['nt_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_naikturun_bold(($anggaran-$aggpenetapan) / $sejuta),  
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
		$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
		$query->addExpression('SUM(ag.jumlah)', 'perubahan');
		$query->condition('keg.inaktif', '0', '='); 
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();
		
		foreach ($arr_results_jen as $data_jen) {
			
			$aggpenetapan = $data_jen->penetapan;
			$anggaran = $data_jen->perubahan;
			
			$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
			
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
				'#markup' => '<p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . $anggaran_s . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['nt_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => apbd_fn_naikturun(($anggaran-$aggpenetapan) / $sejuta), 
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
$aggpenetapan_surplus = $aggpenetapan_pendapata_total - $aggpenetapan_belanja_total;

$persen = apbd_hitungpersen_naikturun($aggpenetapan_surplus, $agg_surplus);	

$form['menu1']['m11']['tab11']['rowuraian_sd']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '<strong>SURPLUS / (DEFISIT)</strong>', 
	'#suffix' => '</td>',
	
);				
$form['menu1']['m11']['tab11']['rowangg_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);

$form['menu1']['m11']['tab11']['rowkmltf_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right">' . apbd_fn($agg_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);
$form['menu1']['m11']['tab11']['nt_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => apbd_fn_naikturun_bold(($agg_surplus-$aggpenetapan_surplus) / $sejuta),  
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
$aggpenetapan_pembiayaan_netto += 0;
	
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
	$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
	$query->addExpression('SUM(ag.jumlah)', 'perubahan');
	$query->groupBy('k.kodek');
	$query->orderBy('k.kodek');
	$results_kel = $query->execute();	
	$arr_results_kel = $results_kel->fetchAllAssoc('kodek');
	db_set_active();
	
	foreach ($arr_results_kel as $data_kel) {

		$aggpenetapan = $data_kel->penetapan;
		$anggaran = $data_kel->perubahan;
		
		$uraian = $data_kel->uraian;
		$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['nt_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_naikturun_bold(($anggaran-$aggpenetapan) / $sejuta),  
			'#suffix' => '</td>',
		);		
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen),
			'#suffix' => '</td></tr>',
		);
	
		if ($data_kel->kodek=='61') {
			$agg_pembiayaan_netto += $anggaran;
			$aggpenetapan_pembiayaan_netto += $aggpenetapan;

		} else	{	
			$agg_pembiayaan_netto -= $anggaran;
			$aggpenetapan_pembiayaan_netto -= $aggpenetapan;
		}
		
		//JENIS
		db_set_active('akuntansi');
		$query = db_select('jenis', 'j');
		$query->innerJoin('anggperda', 'ag', 'j.kodej=left(ag.kodero,3)');
		$query->fields('j', array('kodej', 'uraian'));
		$query->addExpression('SUM(ag.jumlahpenetapan)', 'penetapan');
		$query->addExpression('SUM(ag.jumlah)', 'perubahan');
		$query->condition('j.kodek', $data_kel->kodek, '='); 
		$query->groupBy('j.kodej');
		$query->orderBy('j.kodej');
		$results_jen = $query->execute();	
		$arr_results_jen = $results_jen->fetchAllAssoc('kodej');
		db_set_active();		
		foreach ($arr_results_jen as $data_jen) {
			
			$aggpenetapan = $data_jen->penetapan;
			$anggaran = $data_jen->perubahan;
			
			if (($anggaran+$aggpenetapan)>0) {
				//$uraian = ucwords(strtolower($data_jen->uraian));
				$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporanapbddetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
				
				$persen = apbd_hitungpersen_naikturun($aggpenetapan, $anggaran);	
				
				$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
					'#prefix' => '<tr><td valign="top">',
					'#type'   => 'item',  
					'#markup' => $uraian, 
					'#suffix' => '</td>',
					
				);				
				$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($aggpenetapan / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
		
				$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($anggaran / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
				$form['menu1']['m11']['tab11']['nt_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => apbd_fn_naikturun(($anggaran-$aggpenetapan) / $sejuta), 
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
		'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($agg_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['nt_7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_naikturun_bold(($agg_pembiayaan_netto-$aggpenetapan_pembiayaan_netto) / $sejuta),  
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
	$aggpenetapan_silpa = 0;
	
	$anggaran_silpa = $agg_surplus + $agg_pembiayaan_netto;
	$aggpenetapan_silpa = $aggpenetapan_surplus + $aggpenetapan_pembiayaan_netto;
	
	$persen = apbd_hitungpersen_naikturun($aggpenetapan_silpa, $anggaran_silpa);	
	
	$form['menu1']['m11']['tab11']['rowuraian8']= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($aggpenetapan_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right">' . apbd_fn($anggaran_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['nt_8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_naikturun_bold(($anggaran_silpa-$aggpenetapan_silpa) / $sejuta),  
		'#suffix' => '</td>',
	);				
	$form['menu1']['m11']['tab11']['rowpersen8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun_bold($persen),
		'#suffix' => '</td></tr>',
	);
		
	return $form;
}



?>