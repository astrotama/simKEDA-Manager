<?php
function laporan_std_main($arg=NULL, $nama=NULL) {

if(arg(1) == 'excel'){
	$output = getLaporan();
	header( "Content-Type: application/vnd.ms-excel" );
	header( "Content-disposition: attachment; filename= Laporan_STD.xls" );
	header("Pragma: no-cache"); 
	header("Expires: 0");
	echo $output;
}else {
	drupal_set_title('Realisasi APBD ' . apbd_tahun());
	//$output_form = drupal_get_form('laporan_main_form');	
	$output_form = laporan_std_main_form();	
	return drupal_render($output_form);
}
} 


function laporan_std_main_form() {
	
if (apbd_client_type()=='m') {
	$sejuta = 1000000;
	$label_milyar = '(juta)';	
	
} else {
	$sejuta = 1;
	$label_milyar = '';
}

$agg_pendapata_total = 0; 
$agg_belanja_total = 0; 
$agg_pembiayaan_netto = 0; 

$rea_pendapata_total = 0; 
$rea_belanja_total = 0; 
$rea_pembiayaan_netto = 0; 
	
	//ROW 11
	$form['menu1']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	//I	
	$form['menu1']['m11'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' Realisasi Per Rekening<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
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
			'#markup' => '<p align="right">Realisasi</p>', 
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
$query->addExpression('SUM(ag.jumlah)', 'anggaran');
$query->condition('a.kodea', '4', '='); 
$query->groupBy('a.kodea');
$query->orderBy('a.kodea');
$results = $query->execute();
$arr_results = $results->fetchAllAssoc('kodea');
db_set_active();

foreach ($arr_results as $datas) {
	//$query->where('EXTRACT(MONTH FROM j.tanggal) <= :month', array('month' => $bulan));
	
	db_set_active('akuntansi');
	$realisasi = 0; 
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	db_set_active();
	
	$uraian = $datas->uraian ;
	$anggaran = $datas->anggaran;
		
	$form['menu1']['m11']['tab11']['rowuraian_' . $datas->kodea]= array(
		'#prefix' => '<tr><td valign="top" style="font-size:20px;" >',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 
		'#suffix' => '</td></tr>',
	);
	
	
	$agg_pendapata_total = $datas->anggaran;
	$rea_pendapata_total = $realisasi;
	
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
		$realisasi = 0; 
		db_set_active('akuntansi');
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		db_set_active();	
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 
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
			
			$realisasi = 0; 
			db_set_active('akuntansi');
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			db_set_active();
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporandetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$rea_s = l(apbd_fn($realisasi / $sejuta), 'laporandetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));

			$anggaran = $data_jen->anggaran;
			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right" style="vertical-align: top">' . $rea_s . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['rowpersen_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 
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
	
	$realisasi = 0;
	db_set_active('akuntansi');
	$sql = db_select('jurnal', 'j');
	$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
	$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
	$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
	$sql->condition('keg.inaktif', '0', '='); 
	$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
	$res = $sql->execute();
	foreach ($res as $data) {
		$realisasi = $data->realisasi;
	}
	db_set_active();
	
	$anggaran = $datas->anggaran;
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
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen_' . $datas->kodea]= array(
		'#prefix' => '<td valign="center" style="border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 
		'#suffix' => '</td></tr>',
	);
	
	
	$agg_belanja_total = $datas->anggaran;
	$rea_belanja_total = $realisasi;
	
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
		$realisasi = 0; 
		db_set_active('akuntansi');
		$sql = db_select('jurnal' , 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('keg.inaktif', '0', '='); 
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		db_set_active();
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>' . $uraian . '</strong>', 
		'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 
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
			
			$realisasi = 0; 
			db_set_active('akuntansi');
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}	
			db_set_active();
			
			$anggaran = $data_jen->anggaran;
			
			//$uraian = ucwords(strtolower($data_jen->uraian));
			$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporandetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			$rea_s = l(apbd_fn($realisasi / $sejuta), 'laporandetiluk/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
			
			$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
				'#prefix' => '<tr><td valign="top">',
				'#type'   => 'item',  
				'#markup' => $uraian, 
				'#suffix' => '</td>',
				
			);				
			$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p>', 
				'#suffix' => '</td>',
			);
			
			$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right" style="vertical-align: top">' . $rea_s . '</p>', 
				'#suffix' => '</td>',
			);
			$form['menu1']['m11']['tab11']['rowpersen_' . $data_jen->kodej]= array(
				'#prefix' => '<td valign="top">',
				'#type'         => 'item', 
				'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 
				'#suffix' => '</td></tr>',
			);
	
	
		}
	}
}

//SURPLUS/DEFISIT
$agg_surplus = $agg_pendapata_total - $agg_belanja_total;
$rea_surplus = $rea_pendapata_total - $rea_belanja_total;
$form['menu1']['m11']['tab11']['rowuraian_sd']= array(
	'#prefix' => '<tr><td valign="top">',
	'#type'   => 'item',  
	'#markup' => '<strong>SURPLUS / (DEFISIT)</strong>', 
	'#suffix' => '</td>',
	
);				
$form['menu1']['m11']['tab11']['rowangg_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($agg_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);

$form['menu1']['m11']['tab11']['rowkmltf_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($rea_surplus / $sejuta) . '</p></strong>', 
	'#suffix' => '</td>',
);
$form['menu1']['m11']['tab11']['rowpersen_sd']= array(
	'#prefix' => '<td valign="center" style="border-top:1px solid black">',
	'#type'         => 'item', 
	'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($agg_surplus, $rea_surplus)) . '</p></strong>', 
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
		$realisasi = 0; 
		db_set_active('akuntansi');	
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
		$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
		}
		db_set_active();
		
		$uraian = $data_kel->uraian;
		$anggaran = $data_kel->anggaran;
		$form['menu1']['m11']['tab11']['rowuraian1_' . $data_kel->kodek]= array(
			'#prefix' => '<tr><td valign="top">',
			'#type'   => 'item',  
			'#markup' => '<strong>' . $uraian . '</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['rowangg1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
	
		$form['menu1']['m11']['tab11']['rowkmltf1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['rowpersen1_' . $data_kel->kodek]= array(
			'#prefix' => '<td valign="top">',
			'#type'         => 'item', 
			'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 
			'#suffix' => '</td></tr>',
		);
	
		if ($data_kel->kodek=='61') {
			$agg_pembiayaan_netto += $anggaran;
			$rea_pembiayaan_netto += $realisasi;

		} else	{	
			$agg_pembiayaan_netto -= $anggaran;
			$rea_pembiayaan_netto -= $realisasi;
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
			
			$realisasi = 0; 
			db_set_active('akuntansi');
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
			}		
			db_set_active();	
			
			if (($data_jen->anggaran+$realisasi)>0) {
				//$uraian = ucwords(strtolower($data_jen->uraian));
				$uraian = l(ucwords(strtolower($data_jen->uraian)), 'laporandetil/filter/ZZ/' . $data_jen->kodej, array('attributes' => array('class' => null)));
				
				$anggaran = $data_jen->anggaran;
				
				$form['menu1']['m11']['tab11']['rowuraian_' . $data_jen->kodej]= array(
					'#prefix' => '<tr><td valign="top">',
					'#type'   => 'item',  
					'#markup' => $uraian, 
					'#suffix' => '</td>',
					
				);				
				$form['menu1']['m11']['tab11']['rowangg_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
		
				$form['menu1']['m11']['tab11']['rowkmltf_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p>', 
					'#suffix' => '</td>',
				);
				$form['menu1']['m11']['tab11']['rowpersen_' . $data_jen->kodej]= array(
					'#prefix' => '<td valign="top">',
					'#type'         => 'item', 
					'#markup' => '<p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 
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
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($agg_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($rea_pembiayaan_netto / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen7']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($agg_pembiayaan_netto, $rea_pembiayaan_netto)) . '</p></strong>', 
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
	$realisasi_silpa = 0;
	
	$anggaran_silpa = $agg_surplus + $agg_pembiayaan_netto;
	$realisasi_silpa = $rea_surplus + $rea_pembiayaan_netto;
	
	$form['menu1']['m11']['tab11']['rowuraian8']= array(
		'#prefix' => '<tr><td valign="top">',
		'#type'   => 'item',  
		'#markup' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['rowangg8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	
	$form['menu1']['m11']['tab11']['rowkmltf8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi_silpa / $sejuta) . '</p></strong>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m11']['tab11']['rowpersen8']= array(
		'#prefix' => '<td valign="center" style="border-top:1px solid black;border-bottom:1px solid black">',
		'#type'         => 'item', 
		'#markup' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran_silpa, $realisasi_silpa)) . '</p></strong>', 
		'#suffix' => '</td></tr>',
	);
	
	$form['menu1']['m11']['tab11']['excel']= array(
		'#prefix' => '<tr><td colspan=4 align="right" style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<br /><a href="/laporanstd/excel" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-file" />Excel</a>', 
		'#suffix' => '</td></tr>',
	);
		
	return $form;
}

function getlaporan() {
		
if (apbd_client_type()=='m') {
		$sejuta = 1000000;
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1;
		$label_milyar = '';
	}

	$agg_pendapata_total = 0; 
	$agg_belanja_total = 0; 
	$agg_pembiayaan_netto = 0; 

	$rea_pendapata_total = 0; 
	$rea_belanja_total = 0; 
	$rea_pembiayaan_netto = 0; 
		
		$header=array();
		$rows[]=array(
			array('data' => '<b>REALISASI PER REKENING</b>', 'width' => '750px','align'=>'center','style'=>'userskpd'),
		);
		
		$rows[]=array(
			array('data' => 'Uraian', 'width' => '400px','align'=>'center','style'=>'userskpd'),
			array('data' => 'Anggaran',  'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => 'Realisasi', 'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '%', 'width' => '30px','align'=>'center','style'=>'userskpd'),
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
		
		db_set_active('akuntansi');
		$realisasi = 0; 
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
		$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		db_set_active();
		
		$uraian = $datas->uraian ;
		$anggaran = $datas->anggaran;
		
		$rows[]=array(
			array('data' => '<strong>' . $uraian . '</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
		);
		
		$agg_pendapata_total = $datas->anggaran;
		$rea_pendapata_total = $realisasi;
		
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
			$realisasi = 0; 
			db_set_active('akuntansi');
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
			$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			db_set_active();	
			
			$uraian = $data_kel->uraian;
			$anggaran = $data_kel->anggaran;
			
			$rows[]=array(
				array('data' => '<strong>' . $uraian . '</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
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
				
				$realisasi = 0; 
				db_set_active('akuntansi');
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.kredit-ji.debet)', 'realisasi');
				$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				}
				db_set_active();
				
				//$uraian = ucwords(strtolower($data_jen->uraian));
				$uraian = ucwords(strtolower($data_jen->uraian));
				$rea_s = apbd_fn($realisasi / $sejuta);

				$anggaran = $data_jen->anggaran;
				
				$rows[]=array(
					array('data' => $uraian, 'width' => '400px','align'=>'left','style'=>'userskpd'),
					array('data' => '<p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
					array('data' => '<p align="right" style="vertical-align: top">' . $rea_s . '</p>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
					array('data' => '<p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
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
		
		$realisasi = 0;
		db_set_active('akuntansi');
		$sql = db_select('jurnal', 'j');
		$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
		$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
		$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
		$sql->condition('keg.inaktif', '0', '='); 
		$sql->condition('ji.kodero', db_like($datas->kodea) . '%', 'LIKE'); 
		$res = $sql->execute();
		foreach ($res as $data) {
			$realisasi = $data->realisasi;
		}
		db_set_active();
		
		$anggaran = $datas->anggaran;
		$uraian = $datas->uraian;
		
		$rows[]=array(
			array('data' => '<strong>' . $uraian . '</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
		);		
		
		$agg_belanja_total = $datas->anggaran;
		$rea_belanja_total = $realisasi;
		
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
			$realisasi = 0; 
			db_set_active('akuntansi');
			$sql = db_select('jurnal' , 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
			$sql->condition('keg.inaktif', '0', '='); 
			$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = $data->realisasi;
			}
			db_set_active();
			
			$uraian = $data_kel->uraian;
			$anggaran = $data_kel->anggaran;
			
			$rows[]=array(
				array('data' => '<strong>' . $uraian . '</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
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
				
				$realisasi = 0; 
				db_set_active('akuntansi');
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->innerJoin('kegiatanskpd', 'keg', 'keg.kodekeg=j.kodekeg');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'realisasi');
				$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasi = $data->realisasi;
				}	
				db_set_active();
				
				$anggaran = $data_jen->anggaran;
				
				//$uraian = ucwords(strtolower($data_jen->uraian));
				$uraian = ucwords(strtolower($data_jen->uraian));
				$rea_s = apbd_fn($realisasi / $sejuta);
				
				$rows[]=array(
					array('data' => $uraian, 'width' => '400px','align'=>'left','style'=>'userskpd'),
					array('data' => '<p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
					array('data' => '<p align="right" style="vertical-align: top">' . $rea_s . '</p>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
					array('data' => '<p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
				);
			}
		}
	}

	//SURPLUS/DEFISIT
	$agg_surplus = $agg_pendapata_total - $agg_belanja_total;
	$rea_surplus = $rea_pendapata_total - $rea_belanja_total;
	$rows[]=array(
		array('data' => '<strong>SURPLUS / (DEFISIT)</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
		array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($agg_surplus / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
		array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($rea_surplus / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
		array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($agg_surplus, $rea_surplus)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
	);

	//batas belanjan

	//PEMBIAYAAN
	
	$rows[]=array(
		array('data' => '<strong>PEMBIAYAAN</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
		array('data' => '',  'width' => '100px','align'=>'center','style'=>'userskpd'),
		array('data' => '', 'width' => '100px','align'=>'center','style'=>'userskpd'),
		array('data' => '', 'width' => '30px','align'=>'center','style'=>'userskpd'),
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
			$realisasi = 0; 
			db_set_active('akuntansi');	
			$sql = db_select('jurnal', 'j');
			$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
			$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
			$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
			$sql->condition('ji.kodero', db_like($data_kel->kodek) . '%', 'LIKE'); 
			$res = $sql->execute();
			foreach ($res as $data) {
				$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
			}
			db_set_active();
			
			$uraian = $data_kel->uraian;
			$anggaran = $data_kel->anggaran;
			
			$rows[]=array(
				array('data' => '<strong>' . $uraian . '</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
				array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
			);
		
			if ($data_kel->kodek=='61') {
				$agg_pembiayaan_netto += $anggaran;
				$rea_pembiayaan_netto += $realisasi;

			} else	{	
				$agg_pembiayaan_netto -= $anggaran;
				$rea_pembiayaan_netto -= $realisasi;
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
				
				$realisasi = 0; 
				db_set_active('akuntansi');
				$sql = db_select('jurnal', 'j');
				$sql->innerJoin('jurnalitem', 'ji', 'j.jurnalid=ji.jurnalid');
				$sql->addExpression('SUM(ji.kredit-ji.debet)', 'kreditdebet');
				$sql->addExpression('SUM(ji.debet-ji.kredit)', 'debetkredit');
				$sql->condition('ji.kodero', db_like($data_jen->kodej) . '%', 'LIKE'); 
				$res = $sql->execute();
				foreach ($res as $data) {
					$realisasi = (($data_kel->kodek=='61') ? $data->kreditdebet : $data->debetkredit);
				}		
				db_set_active();	
				
				if (($data_jen->anggaran+$realisasi)>0) {
					//$uraian = ucwords(strtolower($data_jen->uraian));
					$uraian = ucwords(strtolower($data_jen->uraian));
					
					$anggaran = $data_jen->anggaran;
					
					$rows[]=array(
						array('data' => $uraian, 'width' => '400px','align'=>'left','style'=>'userskpd'),
						array('data' => '<p align="right" style="vertical-align: top">' . apbd_fn($anggaran / $sejuta) . '</p>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
						array('data' => '<p align="right" style="vertical-align: top">' . $rea_s . '</p>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
						array('data' => '<p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
					);
				}
			}
		}

		//NETTO
		$rows[]=array(
			array('data' => '<strong>PEMBIAYAAN NETTO</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($agg_pembiayaan_netto / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi_silpa / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($agg_pembiayaan_netto, $rea_pembiayaan_netto)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
		);

	//batas belanjan
		//SILPA
		$anggaran_silpa = 0;
		$realisasi_silpa = 0;
		
		$anggaran_silpa = $agg_surplus + $agg_pembiayaan_netto;
		$realisasi_silpa = $rea_surplus + $rea_pembiayaan_netto;
		
		$rows[]=array(
			array('data' => '<strong>SISA LEBIH ANGGARAN TAHUN BERJALAN</strong>', 'width' => '400px','align'=>'left','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($anggaran_silpa / $sejuta) . '</p></strong>',  'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn($realisasi / $sejuta) . '</p></strong>', 'width' => '100px','align'=>'center','style'=>'userskpd'),
			array('data' => '<strong><p align="right" style="vertical-align: top">' . apbd_fn1(apbd_hitungpersen($anggaran_silpa, $realisasi_silpa)) . '</p></strong>', 'width' => '30px','align'=>'center','style'=>'userskpd'),
		);
		
	$output .= theme('table', array('header' => $header, 'rows' => $rows ));
	return $output;

}

?>