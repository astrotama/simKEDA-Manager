<?php

function laporan_keg_main($arg=NULL, $nama=NULL) {
	
	//http://akt.simkedajepara.net/laporandetil/9/81/421/10/20/kum
	if ($arg) {
		$filter = arg(1);
		$kodeuk = arg(2);
		$akun = arg(3);
	
	} else {
		$filter = 'filter';
		$kodeuk = '81';
		$akun = '52101001';
		
		
	}
	
	if ($filter=='filter') {
	
		db_set_active('akuntansi');
		if (strlen($akun)==1)
			$results = db_query('select uraian from {anggaran} where kodea=:kodea', array(':kodea' => $akun));
		elseif (strlen($akun)==2)
			$results = db_query('select uraian from {kelompok} where kodek=:kodek', array(':kodek' => $akun));
		elseif (strlen($akun)==3)
			$results = db_query('select uraian from {jenis} where kodej=:kodej', array(':kodej' => $akun));
		elseif (strlen($akun)==5)
			$results = db_query('select uraian from {obyek} where kodeo=:kodeo', array(':kodeo' => $akun));
		else
			$results = db_query('select uraian from {rincianobyek} where kodero=:kodero', array(':kodero' => $akun));
		
		foreach ($results as $datas) {
			$rekening = $akun . ' - ' . $datas->uraian;
		};
		db_set_active();
		
		drupal_set_title($rekening);
		
		$output = view_belanja_rincian($kodeuk, $akun);
	
	} elseif ($filter=='filtersd') {
	
		drupal_set_title('KEGIATAN ' . $akun);
		
		$output = view_belanja_sumberdana($kodeuk, $akun);

	} elseif ($filter=='filterkegsp2d') {
		db_set_active('penatausahaan');
		$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg' => $akun));

		foreach ($results as $datas) {
			$kegiatan = $datas->kegiatan;
		};
		db_set_active();	
		
		drupal_set_title($kegiatan);
		
		$output = view_kegiatan_anggaran_sp2d($kodeuk, $akun);

	} else {
		db_set_active('akuntansi');
		$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg' => $akun));

		foreach ($results as $datas) {
			$kegiatan = $datas->kegiatan;
		};
		db_set_active();	
		
		drupal_set_title($kegiatan);
		
		$output = view_kegiatan_anggaran($kodeuk, $akun);
	}
	
	//$output = drupal_render($form);
	return drupal_render($output);		
	//return $output;
}

function view_belanja_rincian($kodeuk, $kodeakun) {



$total_agg = 0; $total_rea = 0;


if ($kodeuk=='ZZ') {
	$caption = 'SELURUH OPD';
} else {
	db_set_active('akuntansi');
	$results = db_query('select namasingkat from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));

	foreach ($results as $datas) {
		$caption = $datas->namasingkat;
	};
	db_set_active();
}

$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption . '<em><small class="span4 text-info pull-right">(ribu)</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
$form['m11']['tab11']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	$form['m11']['tab11']['no1']= array(
		'#prefix' => '<tr><th style="width:3px">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th>',
		
	);	
	$form['m11']['tab11']['uraian11']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Kegiatan</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran12']= array(
		'#prefix' => '<th style="width:20%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Anggaran</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi12']= array(
		'#prefix' => '<th style="width:20%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Realisasi</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen12']= array(
		'#prefix' => '<th style="width:12%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th></tr>',
	);	

$sejuta = 1000;	
$i = 0;

db_set_active('akuntansi');

if ($kodeuk=='ZZ') {
	$results = db_query('select distinct k.kodekeg,k.kegiatan,k.total as agg from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and a.kodero like :kodeakun order by k.kegiatan', array(':kodeakun'=>$kodeakun . '%'));	
	
} else {
	$results = db_query('select distinct k.kodekeg,k.kegiatan,k.total as agg from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and k.kodeuk=:kodeuk and a.kodero like :kodeakun order by k.kegiatan', array(':kodeuk'=>$kodeuk, ':kodeakun'=>$kodeakun . '%'));	
}
$arr_result = $results->fetchAll();
db_set_active();

foreach ($arr_result as $data) {
	
	$realisasi = read_realiasai_kegiatan($data->kodekeg);
	
	$total_agg += $data->agg;
	$total_rea += $realisasi;
	
	//1
	$i++;
	
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $i, 
		'#suffix' => '</td>',
		
	);	
	  
	
	//http://manager.simkedajepara.net/laporanapbdkeg/filter/58/52101001
	$kegiatan = l($data->kegiatan, 'laporandetilkeg/filterkeg/' . $kodeuk . '/' . $data->kodekeg, array('attributes' => array('class' => null)));
	//$kegiatan = $data->kegiatan;
	$form['m11']['tab11']['keg11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $kegiatan, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['agg12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->agg/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($realisasi/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['persen12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' .  apbd_fn1(apbd_hitungpersen($data->agg, $realisasi)) . '</p>', 
		'#suffix' => '</td></tr>',
	);	

}
			
$i++;

$form['m11']['tab11']['no11' . $i]= array(
	'#prefix' => '<tr><td>',
	'#type'   => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '<strong>TOTAL</strong>', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['agg13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_agg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['rea13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_rea/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['persen13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_agg, $total_rea)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}

function view_belanja_sumberdana($kodeuk, $sumberdana) {



$total_agg = 0; $total_rea = 0;


db_set_active('penatausahaan');
$results = db_query('select namasingkat from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));

foreach ($results as $datas) {
	$caption = $datas->namasingkat;
};
db_set_active();

$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption . '<em><small class="span4 text-info pull-right">(ribu)</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
$form['m11']['tab11']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	$form['m11']['tab11']['no1']= array(
		'#prefix' => '<tr><th style="width:3px">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th>',
		
	);	
	$form['m11']['tab11']['uraian11']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Kegiatan</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran12']= array(
		'#prefix' => '<th style="width:20%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Anggaran</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi12']= array(
		'#prefix' => '<th style="width:20%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Realisasi</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen12']= array(
		'#prefix' => '<th style="width:12%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th></tr>',
	);	

$sejuta = 1000;	
$i = 0;

db_set_active('penatausahaan');
 
$results = db_query('select kodekeg,kegiatan,total as agg from {kegiatanskpd} where inaktif=0 and kodeuk=:kodeuk and sumberdana1=:sumberdana order by kegiatan', array(':kodeuk'=>$kodeuk, ':sumberdana'=>$sumberdana));	
$arr_result = $results->fetchAll();
db_set_active();

foreach ($arr_result as $data) {
	
	$realisasi = read_realiasai_kegiatan_sp2d($data->kodekeg);
	
	$total_agg += $data->agg;
	$total_rea += $realisasi;
	
	//1
	$i++;
	
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $i, 
		'#suffix' => '</td>',
		
	);	
	  
	
	//http://manager.simkedajepara.net/laporanapbdkeg/filter/58/52101001
	$kegiatan = l($data->kegiatan, 'laporandetilkeg/filterkegsp2d/' . $kodeuk . '/' . $data->kodekeg, array('attributes' => array('class' => null)));
	//$kegiatan = $data->kegiatan;
	$form['m11']['tab11']['keg11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $kegiatan, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['agg12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->agg/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($realisasi/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['persen12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' .  apbd_fn1(apbd_hitungpersen($data->agg, $realisasi)) . '</p>', 
		'#suffix' => '</td></tr>',
	);	

}
			
$i++;

$form['m11']['tab11']['no11' . $i]= array(
	'#prefix' => '<tr><td>',
	'#type'   => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '<strong>TOTAL</strong>', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['agg13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_agg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['rea13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_rea/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['persen13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_agg, $total_rea)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}


function view_kegiatan_anggaran($kodeuk, $kodekeg) {



$total_agg = 0; $total_rea = 0; 


$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' REKENING<em><small class="span4 text-info pull-right">(ribu)</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
$form['m11']['tab11']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	$form['m11']['tab11']['no1']= array(
		'#prefix' => '<tr><th style="width:3px">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th>',
		
	);
	/*	
	$form['m11']['tab11']['kodej']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Kode</p>', 
		'#suffix' => '</th>',
		
	);
	*/	
	$form['m11']['tab11']['uraianj']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Uraian</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Anggaran</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Realisasi</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen1']= array(
		'#prefix' => '<th style="width:10%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th></tr>',
	);		
$sejuta = 1000;	
$i = 0;
 
db_set_active('akuntansi');
 
$results = db_query('select r.kodero,r.uraian,a.jumlah anggaran  from {rincianobyek} r inner join {anggperkeg} a on r.kodero=a.kodero where a.kodekeg=:kodekeg order by r.kodero', array(':kodekeg'=>$kodekeg));	
$arr_result = $results->fetchAll();
db_set_active();

foreach ($arr_result as $data) {
	
	$realisasi = read_realiasai_rekekening($kodekeg, $data->kodero);
	
	$total_rea += $realisasi;
	$total_agg += $data->anggaran;
	
	//1
	$i++;
	
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $i, 
		'#suffix' => '</td>',
		
	);	
	
	/*
	$form['m11']['tab11']['kode11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $data->kodero, 
		'#suffix' => '</td>',
		
	);
	*/	
	
	$rekening = $data->uraian;
	$form['m11']['tab11']['uraian11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $rekening, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['agg12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->anggaran/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($realisasi/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['per12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($data->anggaran, $realisasi)) . '</p>', 
		'#suffix' => '</td></tr>',
	);	

}
			
$i++;

$form['m11']['tab11']['no11' . $i]= array(
	'#prefix' => '<tr><td>',
	'#type'   => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
	
);	
/*			
$form['m11']['tab11']['kode11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
	
);
*/				
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '<strong>TOTAL</strong>', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_agg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_rea/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row15' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_agg, $total_rea)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}

function view_kegiatan_anggaran_sp2d($kodeuk, $kodekeg) {



$total_agg = 0; $total_rea = 0; 


$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' REKENING<em><small class="span4 text-info pull-right">(ribu)</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
$form['m11']['tab11']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	$form['m11']['tab11']['no1']= array(
		'#prefix' => '<tr><th style="width:3px">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th>',
		
	);	
	$form['m11']['tab11']['kodej']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Kode</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['uraianj']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Uraian</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Anggaran</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Realisasi</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen1']= array(
		'#prefix' => '<th style="width:10%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th></tr>',
	);		
$sejuta = 1000;	
$i = 0;
 
db_set_active('penatausahaan');
 
$results = db_query('select r.kodero,r.uraian,a.jumlah anggaran  from {rincianobyek} r inner join {anggperkeg} a on r.kodero=a.kodero where a.kodekeg=:kodekeg order by r.kodero', array(':kodekeg'=>$kodekeg));	
$arr_result = $results->fetchAll();
db_set_active();

foreach ($arr_result as $data) {
	
	$realisasi = read_realiasai_rekekening_sp2d($kodekeg, $data->kodero);
	
	$total_rea += $realisasi;
	$total_agg += $data->anggaran;
	
	//1
	$i++;
	
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $i, 
		'#suffix' => '</td>',
		
	);	

	$form['m11']['tab11']['kode11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $data->kodero, 
		'#suffix' => '</td>',
		
	);				
	
	$rekening = $data->uraian;
	$form['m11']['tab11']['uraian11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $rekening, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['agg12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->anggaran/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($realisasi/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['per12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($data->anggaran, $realisasi)) . '</p>', 
		'#suffix' => '</td></tr>',
	);	

}
			
$i++;

$form['m11']['tab11']['no11' . $i]= array(
	'#prefix' => '<tr><td>',
	'#type'   => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['kode11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '<strong>TOTAL</strong>', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_agg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_rea/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row15' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_agg, $total_rea)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}



function read_realiasai_kegiatan($kodekeg) {

$realisasi = 0;

db_set_active('akuntansi');
$res_rea = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where j.kodekeg=:kodekeg and ji.kodero like :akun', array(':kodekeg'=> $kodekeg, ':akun'=>'5%'));
foreach ($res_rea as $data_rea) {
	$realisasi = $data_rea->realisasi;
}
db_set_active();

return 	$realisasi;
}

function read_realiasai_rekekening($kodekeg, $kodero) {

$realisasi = 0;

db_set_active('akuntansi');
$res_rea = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where j.kodekeg=:kodekeg and ji.kodero=:kodero', array(':kodekeg'=> $kodekeg, ':kodero'=>$kodero));
foreach ($res_rea as $data_rea) {
	$realisasi = $data_rea->realisasi;
}
db_set_active();

return 	$realisasi;
}

function read_realiasai_rekekening_sp2d($kodekeg, $kodero) {

$realisasi = 0;

db_set_active('penatausahaan');
$res_rea = db_query('select sum(di.jumlah) as realisasi from {dokumenrekening} as di inner join {dokumen} as d on di.dokid=d.dokid where d.kodekeg=:kodekeg and di.kodero=:kodero', array(':kodekeg'=> $kodekeg, ':kodero'=>$kodero));
foreach ($res_rea as $data_rea) {
	$realisasi = $data_rea->realisasi;
}
db_set_active();

return 	$realisasi;
}


function read_realiasai_kegiatan_sp2d($kodekeg) {

$realisasi = 0;

db_set_active('penatausahaan');
$res_rea = db_query('select sum(jumlah) as realisasi from {dokumen} where kodekeg=:kodekeg', array(':kodekeg'=> $kodekeg));
foreach ($res_rea as $data_rea) {
	$realisasi = $data_rea->realisasi;
}
db_set_active();

return 	$realisasi;
}


?>

