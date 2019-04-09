<?php
function laporanapbd_detil_main($arg=NULL, $nama=NULL) {
	
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
		if ($akun=='6')
			$rekening = $akun . ' - PEMBIAYAAN';
		else
			$rekening = $akun . ' - ' . ($akun=='4'? 'PENDAPATAN' : 'BELANJA');
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
		$output = gen_report_anggaran_pendapatan($kodeuk, $akun);
	elseif (substr($akun,0,1)=='5')
		$output = gen_report_anggaran_belanja($kodeuk, $akun);
	else
		$output = gen_report_anggaran_pembiayaan($akun);
	
	
	//$output = drupal_render($form);
	return drupal_render($output);		
	//return $output;
}

function gen_report_anggaran_pendapatan($kodeuk, $kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1; 
		$label_milyar = '';
	}

if ($kodeuk=='ZZ') {
	$caption = 'RINCIAN';
} else {
	db_set_active('akuntansi');
	$results = db_query('select namasingkat from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));
	
	foreach ($results as $datas) {
		$caption = $datas->namasingkat;
	};
	db_set_active();
}	

$total_agg = 0; $total_rea = 0;

$form['m11'] = array( 
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption . '<em><small class="span4 text-info pull-right">'. $label_milyar .'</small></em>',
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
	$form['menu1']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
	);

//$sejuta = 1000000;	
$i = 0;

db_set_active('akuntansi');

if ($kodeuk=='ZZ') {
	if (strlen($kodeakun)==1)
		$results = db_query('select r.kodek as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {kelompok}} r on left(a.kodero,2)=r.kodek where a.kodero like :kodeakun group by r.kodek order by r.kodek', array(':kodeakun'=>$kodeakun . '%'));	
	elseif (strlen($kodeakun)==2)
		$results = db_query('select r.kodej as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {jenis}} r on left(a.kodero,3)=r.kodej where a.kodero like :kodeakun group by r.kodej order by r.kodej', array(':kodeakun'=>$kodeakun . '%'));	
	elseif (strlen($kodeakun)==3)
		$results = db_query('select r.kodeo as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {obyek}} r on left(a.kodero,5)=r.kodeo where a.kodero like :kodeakun group by r.kodeo order by r.kodeo', array(':kodeakun'=>$kodeakun . '%'));	
	else
		$results = db_query('select r.kodero as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {rincianobyek}} r on a.kodero=r.kodero where a.kodero like :kodeakun group by r.kodero order by r.kodero', array(':kodeakun'=>$kodeakun . '%'));	

} else {
	if (strlen($kodeakun)==1)
		$results = db_query('select r.kodek as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {kelompok}} r on left(a.kodero,2)=r.kodek where a.kodero like :kodeakun and a.kodeuk=:kodeuk group by r.kodek order by r.kodek', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	elseif (strlen($kodeakun)==2)
		$results = db_query('select r.kodej as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {jenis}} r on left(a.kodero,3)=r.kodej where a.kodero like :kodeakun and a.kodeuk=:kodeuk group by r.kodej order by r.kodej', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	elseif (strlen($kodeakun)==3)
		$results = db_query('select r.kodeo as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {obyek}} r on left(a.kodero,5)=r.kodeo where a.kodero like :kodeakun and a.kodeuk=:kodeuk group by r.kodeo order by r.kodeo', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	else
		$results = db_query('select r.kodero as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperuk} a inner join {rincianobyek}} r on a.kodero=r.kodero where a.kodero like :kodeakun and a.kodeuk=:kodeuk group by r.kodero order by r.kodero', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	
}	

$arr_result = $results->fetchAllAssoc('kode');
db_set_active();

$total_skrg = 0; $total_lalu = 0;

foreach ($arr_result as $data) {

	$agglalu = read_pendapatan_lalu($kodeuk, $data->kode); 
	
	$total_skrg += $data->anggaran;
	$total_lalu += $agglalu;
	
	
	//1
	$i++;
	$persen = apbd_hitungpersen_naikturun($agglalu, $data->anggaran);
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top;">',
		'#type'   => 'item', 
		'#markup' => apbd_simbol_naikturun($persen), 
		'#suffix' => '</td>',
		
	);	
	
	if (strlen($data->kode)==8)
		$rea_u = $data->uraian;
	else
		$rea_u = l($data->uraian, 'laporanapbddetil/filter/' . $kodeuk . '/' . $data->kode, array('attributes' => array('class' => null)));
	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'   => 'item', 
		'#markup' => $rea_u, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['row12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($agglalu/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	if ($kodeuk=='ZZ')
		$agg_baru_s = l(apbd_fn($data->anggaran/$sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data->kode, array('attributes' => array('class' => null)));
	else
		$agg_baru_s = apbd_fn($data->anggaran/$sejuta);
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $agg_baru_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun($persen),
		'#suffix' => '</td>',
	);
	$form['m11']['tab11']['row15' . $i]= array(
			'#prefix' => '<td style="vertical-align: top;">',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporanapbddetiluk/filter/%23%23/' . $data->kode . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
	
	//$skpd = ($kodeuk=='ZZ'? l('SKPD', 'laporanapbddetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan, array('attributes' => array('class' => null))) : '');
	

}
$i++;			
$persen = apbd_hitungpersen_naikturun($total_lalu, $total_skrg);
$form['m11']['tab11']['row10' . $i]= array(
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

function gen_report_anggaran_belanja($kodeuk, $kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1; 
		$label_milyar = '';
	}

$total_skrg = 0; $total_lalu = 0;

if ($kodeuk=='ZZ') {
	$caption = 'RINCIAN';
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
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption .  '<em><small class="span4 text-info pull-right">'.$label_milyar.'</small></em>',
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
	$form['menu1']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
	);

//$sejuta = 1000000;	
$i = 0;

db_set_active('akuntansi');

if ($kodeuk=='ZZ') {
	if (strlen($kodeakun)==1)
		$results = db_query('select r.kodek as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {kelompok} r on left(a.kodero,2)=r.kodek inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun group by r.kodek order by r.kodek', array(':kodeakun'=>$kodeakun . '%'));	

	elseif (strlen($kodeakun)==2)
		$results = db_query('select r.kodej as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {jenis} r on left(a.kodero,3)=r.kodej inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun group by r.kodej order by r.kodej', array(':kodeakun'=>$kodeakun . '%'));	
	elseif (strlen($kodeakun)==3)
		$results = db_query('select r.kodeo as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {obyek} r on left(a.kodero,5)=r.kodeo inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun group by r.kodeo order by r.kodeo', array(':kodeakun'=>$kodeakun . '%'));	
	else
		$results = db_query('select r.kodero as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {rincianobyek} r on a.kodero=r.kodero inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun group by r.kodero order by r.kodero', array(':kodeakun'=>$kodeakun . '%'));	

} else {
	if (strlen($kodeakun)==1)
		$results = db_query('select r.kodek as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {kelompok} r on left(a.kodero,2)=r.kodek inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun and k.kodeuk=:kodeuk group by r.kodek order by r.kodek', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	

	elseif (strlen($kodeakun)==2)
		$results = db_query('select r.kodej as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {jenis} r on left(a.kodero,3)=r.kodej inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun and k.kodeuk=:kodeuk group by r.kodej order by r.kodej', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	elseif (strlen($kodeakun)==3)
		$results = db_query('select r.kodeo as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {obyek} r on left(a.kodero,5)=r.kodeo inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun and k.kodeuk=:kodeuk group by r.kodeo order by r.kodeo', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	else
		$results = db_query('select r.kodero as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperkeg} a inner join {rincianobyek} r on a.kodero=r.kodero inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :kodeakun and k.kodeuk=:kodeuk group by r.kodero order by r.kodero', array(':kodeakun'=>$kodeakun . '%', ':kodeuk'=>$kodeuk));	
	
}
$arr_result = $results->fetchAllAssoc('kode');
db_set_active();

$total_skrg = 0; $total_lalu = 0;

foreach ($arr_result as $data) {

	$agglalu = read_belanja_lalu($kodeuk, $data->kode); 
	
	$total_skrg += $data->anggaran;
	$total_lalu += $agglalu;
	
	
	//1
	$i++;
	$persen = apbd_hitungpersen_naikturun($agglalu, $data->anggaran);
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => apbd_simbol_naikturun($persen), 
		'#suffix' => '</td>',
		
	);
	
	if (strlen($data->kode)==8)
		//$rea_u = $data->uraian;
		$rea_u = l($data->uraian, 'laporanapbddetilkeg/filter/' . $kodeuk . '/' . $data->kode, array('attributes' => array('class' => null)));

	else
		$rea_u = l($data->uraian, 'laporanapbddetil/filter/' . $kodeuk . '/' . $data->kode, array('attributes' => array('class' => null)));
	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $rea_u, 
		'#suffix' => '</td>',
		
	);				

	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($agglalu/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	

	if ($kodeuk=='ZZ')
		$anggaran_s = l(apbd_fn($data->anggaran/$sejuta), 'laporanapbddetiluk/filter/ZZ/' . $data->kode, array('attributes' => array('class' => null)));
	else
		$anggaran_s = apbd_fn($data->anggaran/$sejuta);
	
	
	$form['m11']['tab11']['row12' . $i]= array(
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
		'#markup' =>  '<a href="/laporanapbddetiluk/filter/%23%23/' . $data->kode . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
		'#suffix' => '</td></tr>',
	);
	
	//$skpd = ($kodeuk=='ZZ'? l('SKPD', 'laporanapbddetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan, array('attributes' => array('class' => null))) : '');
	

}
			
$i++;
$persen = apbd_hitungpersen_naikturun($total_lalu, $total_skrg);
$form['m11']['tab11']['row10' . $i]= array(
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

function gen_report_anggaran_pembiayaan($kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1; 
		$label_milyar = '';
	}

$caption = 'PEMBIAYAAN';

$total_skrg = 0; $total_lalu = 0;

$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption . '<em><small class="span4 text-info pull-right">'.$label_milyar.'</small></em>',
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
	$form['menu1']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
	);	

//$sejuta = 1000000;	
$i = 0;

db_set_active('akuntansi');

if (strlen($kodeakun)==1)
	$results = db_query('select r.kodek as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperda} a inner join {kelompok}} r on left(a.kodero,2)=r.kodek where a.kodero like :kodeakun group by r.kodek order by r.kodek', array(':kodeakun'=>$kodeakun . '%'));	
elseif (strlen($kodeakun)==2)
	$results = db_query('select r.kodej as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperda} a inner join {jenis}} r on left(a.kodero,3)=r.kodej where a.kodero like :kodeakun group by r.kodej order by r.kodej', array(':kodeakun'=>$kodeakun . '%'));	
elseif (strlen($kodeakun)==3)
	$results = db_query('select r.kodeo as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperda} a inner join {obyek}} r on left(a.kodero,5)=r.kodeo where a.kodero like :kodeakun group by r.kodeo order by r.kodeo', array(':kodeakun'=>$kodeakun . '%'));	
else
	$results = db_query('select r.kodero as kode, r.uraian, sum(a.jumlah) as anggaran from {anggperda} a inner join {rincianobyek}} r on a.kodero=r.kodero where a.kodero like :kodeakun group by r.kodero order by r.kodero', array(':kodeakun'=>$kodeakun . '%'));	


$arr_result = $results->fetchAllAssoc('kode');
db_set_active();

foreach ($arr_result as $data) {

	$agglalu = read_pembiayaan_lalu($data->kode); 
	
	$total_skrg += $data->anggaran;
	$total_lalu += $agglalu;
	
	if (($data->anggaran+$agglalu)>0) {
		//1
		$i++;
		$persen = apbd_hitungpersen_naikturun($agglalu, $data->anggaran);
		$form['m11']['tab11']['no1' . $i]= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',
			
		);
		
		if (strlen($data->kode)==8)
			$rea_u = $data->uraian;
		else
			$rea_u = l($data->uraian, 'laporanapbddetil/filter/00/' . $data->kode, array('attributes' => array('class' => null)));
		$form['m11']['tab11']['row11' . $i]= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['m11']['tab11']['row12' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu/$sejuta) . '</p>', 
			'#suffix' => '</td>',
		);	
		
		$anggaran_s = apbd_fn($data->anggaran/$sejuta);
		
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
			'#markup' =>  '<a href="/laporanapbddetiluk/filter/%23%23/4' . $data->kode . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		//$skpd = ($kodeuk=='ZZ'? l('SKPD', 'laporanapbddetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan, array('attributes' => array('class' => null))) : '');
	}

}
			
$i++;
$persen = apbd_hitungpersen_naikturun($total_lalu, $total_skrg);
$form['m11']['tab11']['row10' . $i]= array(
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
	'#markup' => '<p align="right"><strong>' . apbd_fn(apbd_hitungpersen_naikturun($total_lalu, $total_skrg)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);

return $form;


}


?>

