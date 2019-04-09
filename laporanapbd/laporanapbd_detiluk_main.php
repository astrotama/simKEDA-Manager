<?php
function laporanapbd_detiluk_main($arg=NULL, $nama=NULL) {
	
	//http://akt.simkedajepara.net/laporandetil/9/81/421/10/20/kum
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodeuk = arg(2);
				$akun = arg(3);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$kodeuk = 'ZZ';
		$akun = '521';
		
	}
	
	if (strlen($akun)==1) {
		
		$rekening = $akun . ' - ' . ($akun=='4' ? 'PENDAPATAN' : 'BELANJA');
		
	} else {
		
		db_set_active('akuntansi');
		if (strlen($akun)==2)
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
	}
	
	drupal_set_title($rekening);
	
	if (substr($akun,0,1)=='4')
		$output = gen_anggaran_pendapatan($kodeuk, $akun);
	else
		$output = gen_anggaran_belanja($kodeuk, $akun);
		
	//$output = drupal_render($form);
	return drupal_render($output);		
	//return $output;
}

function gen_anggaran_pendapatan($kodeuk, $kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1; 
		$label_milyar = '';
	}

//db_set_active('akuntansi');
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' RINCIAN<em><small class="span4 text-info pull-right">'.$label_milyar.'</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
$form['m11']['tab11']= array(
	//'#prefix' => '<table class="table table-striped" style="width:100%">',
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
		'#markup' => '<p>Uraian</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen11']= array(
		'#prefix' => '<th style="width:12%; color:black;">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th>',
	);
	$form['m11']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
	);

//$sejuta = 1000000;	
$i = 0;

db_set_active('akuntansi');
$results = db_query('select u.kodeuk, u.namasingkat, sum(a.jumlah) as anggaran from {anggperuk} a inner join {unitkerja} u on a.kodeuk=u.kodeuk where a.kodero like :kodeakun group by u.kodeuk, u.namasingkat order by u.kodedinas', array(':kodeakun'=>$kodeakun . '%'));	
$arr_result = $results->fetchAllAssoc('kodeuk');
db_set_active();

$total_lalu = 0; $total_skrg = 0;

foreach ($arr_result as $data) {
	 
	$agglalu = read_pendapatan_lalu($data->kodeuk, $kodeakun); 
	
	$total_lalu += $agglalu;
	$total_skrg += $data->anggaran;
	
	//1
	$i++;
	$persen = apbd_hitungpersen_naikturun($agglalu, $data->anggaran);
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => apbd_simbol_naikturun($persen), 
		'#suffix' => '</td>',
		
	);			

	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $data->namasingkat, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['row12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($agglalu/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	if (strlen($kodeakun)==8)
		$anggaran_s = apbd_fn($data->anggaran/$sejuta);
	else
		$anggaran_s = l(apbd_fn($data->anggaran/$sejuta), 'laporanapbddetil/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	//$rea_s = apbd_fn($realisasi/$sejuta);
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $anggaran_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun($persen),
		'#suffix' => '</td>',
	);
	$form['m11']['tab11']['row15' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporanapbddetil/filter/' . $data->kodeuk . '/' . $kodeakun . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
	);
	
	//$skpd = ($kodeuk=='ZZ'? l('SKPD', 'laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan, array('attributes' => array('class' => null))) : '');
	

}
			
$i++;
$persen = apbd_hitungpersen_naikturun($total_lalu, $total_skrg);
$form['m11']['tab11']['no11' . $i]= array(
	'#prefix' => '<tr><td>',
	'#type'   => 'item', 
	'#markup' => apbd_simbol_naikturun($persen), 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '<strong>TOTAL</strong>', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row12' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_lalu/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_skrg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => apbd_fn_persen_naikturun_bold($persen),
	'#suffix' => '</td></tr>',
);	

return $form;


}

function gen_anggaran_belanja($kodeuk, $kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1; 
		$label_milyar = '';
	}

$total_lalu = 0; $total_skrg = 0;

//db_set_active('akuntansi');
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">', 
	'#type' => 'fieldset', 
	'#title'=>  _bootstrap_icon('unchecked') . ' RINCIAN<em><small class="span4 text-info pull-right">'.$label_milyar.'</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
$form['m11']['tab11']= array(
	//'#prefix' => '<table class="table table-striped" style="width:100%">',
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
		'#markup' => '<p>Uraian</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen11']= array(
		'#prefix' => '<th style="width:12%; color:black;">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th>',
	);
	$form['m11']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
	);	

//$sejuta = 1000000;	
$i = 0;

db_set_active('akuntansi');
$results = db_query('select u.kodeuk, u.namasingkat, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.inaktif=0 and a.kodero like :kodeakun group by u.kodeuk, u.namasingkat order by u.kodedinas', array(':kodeakun'=>$kodeakun . '%'));	
$arr_result = $results->fetchAllAssoc('kodeuk');
db_set_active();

foreach ($arr_result as $data) {
	 
	$agglalu = read_belanja_lalu($data->kodeuk, $kodeakun); 
	
	$total_lalu += $agglalu;
	$total_skrg += $data->anggaran;
	
	//1
	$i++;
	$persen = apbd_hitungpersen_naikturun($agglalu, $data->anggaran);
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => apbd_simbol_naikturun($persen), 
		'#suffix' => '</td>',
		
	);	

	$skpd = l($data->namasingkat, 'laporanapbddetilkeg/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	
	
	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $skpd, 
		'#suffix' => '</td>',
		 
	);				
	$form['m11']['tab11']['row12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($agglalu/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	if (strlen($kodeakun)==8)
		$anggaran_s = apbd_fn($data->anggaran/$sejuta);
	else
		$anggaran_s = l(apbd_fn($data->anggaran/$sejuta), 'laporandetil/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $anggaran_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun($persen),
		'#suffix' => '</td>',
	);
	$form['m11']['tab11']['row15' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'markup', 
		'#markup' =>  '<a href="/laporandetil/filter/' . $data->kodeuk . '/' . $kodeakun . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
		'#suffix' => '</td></tr>',
	);	
	
	//$skpd = ($kodeuk=='ZZ'? l('SKPD', 'laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan, array('attributes' => array('class' => null))) : '');
	

}
			
$i++;
$persen = apbd_hitungpersen_naikturun($total_lalu, $total_skrg);
$form['m11']['tab11']['no11' . $i]= array(
	'#prefix' => '<tr><td>',
	'#type'   => 'item', 
	'#markup' => apbd_simbol_naikturun($persen),   
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => '<strong>TOTAL</strong>', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row12' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_lalu/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_skrg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => apbd_fn_persen_naikturun_bold($persen),
	'#suffix' => '</td></tr>',
);	

return $form;


}

/*
function read_pendapatan_lalu($kodeuk, $kodeakun) {

$anggaran = 0;

db_set_active('akuntansilalu');
if ($kodeuk=='ZZ') {
	if (strlen($kodeakun)==1)
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun', array(':kodeakun'=>$kodeakun . '%'));	
	elseif (strlen($kodeakun)==2)
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun', array(':kodeakun'=>$kodeakun . '%'));	
	elseif (strlen($kodeakun)==3)
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun', array(':kodeakun'=>$kodeakun . '%'));	
	else
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun', array(':kodeakun'=>$kodeakun . '%'));	

} else {
	if (strlen($kodeakun)==1)
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun and kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	elseif (strlen($kodeakun)==2)
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun and kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	elseif (strlen($kodeakun)==3)
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun and kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	else
		$results = db_query('select sum(jumlah) as anggaran from {anggperuk} where kodero like :kodeakun and kodeuk=:kodeuk', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	
}

foreach ($results as $data_rea) {
	$anggaran = $data_rea->anggaran;
}

db_set_active();

return 	$anggaran;
}

function read_belanja_lalu($kodeuk, $kodeakun) {

$agglalu = 0;

db_set_active('akuntansilalu');

if ($kodeuk=='ZZ') {
	$results = db_query('select sum(a.jumlah) as anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun', array(':kodeakun'=>$kodeakun . '%'));	
} else {
	$results = db_query('select sum(a.jumlah) as anggaran from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and k.kodeuk=:kodeuk and a.kodero like :kodeakun', array(':kodeuk'=>$kodeuk,':kodeakun'=>$kodeakun . '%'));	
}
foreach ($results as $data_rea) {
	$agglalu = $data_rea->anggaran;
}

db_set_active();

return 	$agglalu;
}


*/

?>

