<?php
function laporan_detiluk_main($arg=NULL, $nama=NULL) {
	
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
		$output = gen_report_realisasi_pendapatan($kodeuk, $akun);
	else
		$output = gen_report_realisasi_belanja($kodeuk, $akun);
		
	//$output = drupal_render($form);
	return drupal_render($output);		
	//return $output;
}

function read_realiasai_pendapatan($kodeuk, $kodeakun) {

$realisasi = 0;

db_set_active('akuntansi');
$res_rea = db_query('select sum(ji.kredit-ji.debet) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where ji.kodero like :kodeakun and j.kodeuk=:kodeuk', array(':kodeakun'=> $kodeakun . '%', ':kodeuk'=>$kodeuk));

foreach ($res_rea as $data_rea) {
	$realisasi = $data_rea->realisasi;
}

db_set_active();

return 	$realisasi;
}

function read_realiasai_belanja($kodeuk, $kodeakun) {

$realisasi = 0;

db_set_active('akuntansi');
$res_rea = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid inner join {kegiatanskpd} k on j.kodekeg=k.kodekeg where k.inaktif=0 and ji.kodero like :kodeakun and j.kodeuk=:kodeuk', array(':kodeakun'=> $kodeakun . '%', ':kodeuk'=>$kodeuk));

foreach ($res_rea as $data_rea) {
	$realisasi = $data_rea->realisasi;
}

db_set_active();

return 	$realisasi;
}

function gen_report_realisasi_pendapatan($kodeuk, $kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000;
		$label_milyar = '(ribu)';	
		
	} else {
		$sejuta = 1;
		$label_milyar = '';
	}

$total_agg = 0; $total_rea = 0;

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
		'#markup' => '<p align="right">Anggaran</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">Realisasi</p>', 
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

foreach ($arr_result as $data) {
	 
	$realisasi = read_realiasai_pendapatan($data->kodeuk, $kodeakun); 
	
	$total_agg += $data->anggaran;
	$total_rea += $realisasi;
	
	//1
	$i++;
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => $i . '.', 
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
		'#markup' => '<p align="right">' . apbd_fn($data->anggaran/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	if (strlen($kodeakun)==8)
		$rea_s = apbd_fn($realisasi/$sejuta);
	else
		$rea_s = l(apbd_fn($realisasi/$sejuta), 'laporandetil/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	//$rea_s = apbd_fn1($realisasi/$sejuta);
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $rea_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn(apbd_hitungpersen($data->anggaran, $realisasi)) . '</p>', 
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
$form['m11']['tab11']['row12' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_agg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_rea/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_agg, $total_rea)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}

function gen_report_realisasi_belanja($kodeuk, $kodeakun) {

if (apbd_client_type()=='m') {
		$sejuta = 1000;
		$label_milyar = '(ribu)';	
		
	} else {
		$sejuta = 1;
		$label_milyar = '';
	}

$bulan = date('m');

$total_agg = 0; $total_rea = 0;

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
		'#markup' => '<p align="right">Anggaran</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">Realisasi</p>', 
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
	 
	$realisasi = read_realiasai_belanja($data->kodeuk, $kodeakun); 
	
	$total_agg += $data->anggaran;
	$total_rea += $realisasi;
	
	//1
	$i++;
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => $i . '.', 
		'#suffix' => '</td>',
		
	);	

	$skpd = l($data->namasingkat, 'laporandetilkeg/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	
	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $skpd, 
		'#suffix' => '</td>',
		 
	);				
	$form['m11']['tab11']['row12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->anggaran/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	if (strlen($kodeakun)==8)
		$rea_s = apbd_fn($realisasi/$sejuta);
	else
		$rea_s = l(apbd_fn($realisasi/$sejuta), 'laporandetil/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $rea_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($data->anggaran, $realisasi)) . '</p>', 
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
$form['m11']['tab11']['row12' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_agg/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_rea/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_agg, $total_rea)) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}


?>

