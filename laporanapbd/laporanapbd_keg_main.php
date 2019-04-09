<?php

function laporanapbd_keg_main($arg=NULL, $nama=NULL) {
	
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
	
		db_set_active('penatausahaan');
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

	} elseif ($filter=='filterkeginfo') {
		db_set_active('penatausahaan');
		$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg' => $akun));

		foreach ($results as $datas) {
			$kegiatan = $datas->kegiatan;
		};
		db_set_active();	
		
		drupal_set_title('APBD | ' . $kegiatan);
		
		$output = view_kegiatan_info($kodeuk, $akun, $kegiatan);

	} elseif ($filter=='filterkegppa') {
		db_set_active('penatausahaan');
		$results = db_query('select kegiatan from {kegiatanskpd} where kodekeg=:kodekeg', array(':kodekeg' => $akun));

		foreach ($results as $datas) {
			$kegiatan = $datas->kegiatan;
		};
		db_set_active();	
		
		drupal_set_title('Perencanaan | ' . $kegiatan);
		
		$output = view_kegiatan_ppa($kodeuk, $akun, $kegiatan);
		
	} else {
		db_set_active('penatausahaan');
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

if (apbd_client_type()=='m') {
	$sejuta = 1000;	 
	$label_milyar = '(ribu)';	
	
} else {
	$sejuta = 1; 
	$label_milyar = '';
}

$total_agg = 0; $total_rea = 0;


if ($kodeuk=='ZZ') {
	$caption = 'SELURUH OPD';
} else {
	db_set_active('penatausahaan');
	$results = db_query('select namasingkat from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));

	foreach ($results as $datas) {
		$caption = $datas->namasingkat;
	};
	db_set_active();
}

$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption . '<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
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
		'#markup' => '<p align="right">Tunda</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen12']= array(
		'#prefix' => '<th style="width:20%;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Tersedia</p>', 
		'#suffix' => '</th></tr>',
	);	


$i = 0;

db_set_active('penatausahaan');

if ($kodeuk=='ZZ') {
	$results = db_query('select distinct k.kodekeg,k.kegiatan,k.totalpenetapan,k.total,k.anggaran from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and a.kodero like :kodeakun order by k.kegiatan', array(':kodeakun'=>$kodeakun . '%'));	
	
} else {
	$results = db_query('select distinct k.kodekeg,k.kegiatan,k.totalpenetapan,k.total,k.anggaran from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and k.kodeuk=:kodeuk and a.kodero like :kodeakun order by k.kegiatan', array(':kodeuk'=>$kodeuk, ':kodeakun'=>$kodeakun . '%'));	
}
$arr_result = $results->fetchAll();
db_set_active();

$total_penetapan = 0; $total_perubahan = 0; $total_anggaran = 0;; $total_tunda = 0;
foreach ($arr_result as $data) {
	
	
	$total_penetapan += $data->totalpenetapan;
	$total_perubahan += $data->total;
	$total_anggaran += $data->anggaran;
	$total_tunda += $data->total - $data->anggaran;
	
	//1
	$i++;
	
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top">',
		'#type'   => 'item', 
		'#markup' => $i, 
		'#suffix' => '</td>',
		
	);	
	  
	
	//http://manager.simkedajepara.net/laporanapbdkeg/filter/58/52101001
	$kegiatan = l($data->kegiatan, 'laporanapbddetilkeg/filterkeg/' . $kodeuk . '/' . $data->kodekeg, array('attributes' => array('class' => null)));
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
		'#markup' => '<p align="right">' . apbd_fn($data->total/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn(($data->total - $data->anggaran)/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['persen12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' .  apbd_fn($data->anggaran/$sejuta) . '</p>', 
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
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_perubahan/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['rea13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_tunda/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['persen13' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_anggaran/$sejuta) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

return $form;


}

function view_belanja_sumberdana($kodeuk, $sumberdana) {

if (apbd_client_type()=='m') {
	$sejuta = 1000;	 
	$label_milyar = '(ribu)';	
	
} else {
	$sejuta = 1; 
	$label_milyar = '';
}

$total_agg = 0; $total_rea = 0;


db_set_active('penatausahaan');
$results = db_query('select namasingkat from {unitkerja} where kodeuk=:kodeuk', array(':kodeuk' => $kodeuk));

foreach ($results as $datas) {
	$caption = $datas->namasingkat;
};
db_set_active();

$form['m11'] = array(
	'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $caption . '<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	'#suffix' => '</div>',
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


if (apbd_client_type()=='m') {
	$sejuta = 1000;	 
	$label_milyar = '(ribu)';	
	
} else {
	$sejuta = 1; 
	$label_milyar = '';
}

$total_agg = 0; $total_rea = 0; 


$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' REKENING<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
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
		'#markup' => '<p align="right">Tunda</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Tersedia</p>', 
		'#suffix' => '</th></tr>',
	);		

$i = 0;
 
db_set_active('penatausahaan');
 
$results = db_query('select r.kodero,r.uraian,a.jumlah,a.jumlahpenetapan,a.anggaran from {rincianobyek} r inner join {anggperkeg} a on r.kodero=a.kodero where a.kodekeg=:kodekeg order by r.kodero', array(':kodekeg'=>$kodekeg));	
$arr_result = $results->fetchAll();
db_set_active();

$total_penetapan = 0; $total_perubahan = 0; $total_anggaran = 0; $total_tunda = 0; 
foreach ($arr_result as $data) {
	
	$total_penetapan += $data->jumlahpenetapan;
	$total_perubahan += $data->jumlah;
	$total_anggaran += $data->anggaran;
	$total_tunda += ($data->jumlah-$data->anggaran);
	
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
		'#markup' => '<p align="right">' . apbd_fn($data->jumlah/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn(($data->jumlah-$data->anggaran)/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['per12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->anggaran/$sejuta) . '</p>', 
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
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_perubahan/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row14' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_tunda/$sejuta) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['row15' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_anggaran/$sejuta) . '</strong></p>', 
	'#suffix' => '</td></tr>',
);	

$str_menu =  '|<a href="/laporanapbddetilkeg/filterkegppa/' . $kodeuk . '/' . $kodekeg . '">Perencanaan</a>' . '|';
$str_menu .=  '<a href="/laporanapbddetilkeg/filterkeginfo/' . $kodeuk . '/' . $kodekeg . '">Penganggaran</a>' . '|';
$str_menu .=  'Rekening|';


$form['menu']= array(
	'#type'  	=> 'markup', 
	'#markup' 	=>  '<p align="center">' . $str_menu . '</p>',
);	
return $form;


}

function view_kegiatan_anggaran_sp2d($kodeuk, $kodekeg) {


if (apbd_client_type()=='m') {
	$sejuta = 1000;	 
	$label_milyar = '(ribu)';	
	
} else {
	$sejuta = 1; 
	$label_milyar = '';
}

$total_agg = 0; $total_rea = 0; 


$form['m11'] = array(
	'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' REKENING<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	'#suffix' => '</div>',
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

function view_kegiatan_info($kodeuk, $kodekeg, $kegiatan) {


/*
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' TOLOK UKUR<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
*/
 
db_set_active('penatausahaan');
 
$results = db_query('select k.programsasaran, k.programtarget, k.keluaransasaran, k.keluarantarget, k.hasilsasaran, k.hasiltarget, k.waktupelaksanaan, k.latarbelakang, k.kelompoksasaran, k.lokasi, k.tw1, k.tw2, k.tw3, k.tw4, k.total, p.program from {kegiatanskpd} k inner join {program} p on k.kodepro=p.kodepro where k.kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));	
$arr_result = $results->fetchAll();
db_set_active();

foreach ($arr_result as $data) {
	
	$form['program'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#title'   => 'Program:', 
		'#type'   => 'item', 
		'#markup' => '<p><strong>' . $data->program . '</strong><p>',
		'#suffix' => '</div>',	
	);	

	$form['tab11']= array(
		'#prefix' => '<div class="col-md-12"><table style="width:100%">',
		 '#suffix' => '</table></div>',
	);
		$form['tab11']['null']= array(
			'#prefix' => '<tr><th style="width:13%">',
			'#type'   => 'item', 
			'#markup' => 'Tolok Ukur', 
			'#suffix' => '</th>',
			
		);	
		
		$form['tab11']['program_label']= array(
			'#prefix' => '<th style="width:45%;">',
			'#type'         => 'item', 
			'#markup' => 'Sasaran', 
			'#suffix' => '</th>',
		);	
		$form['tab11']['target_label']= array(
			'#prefix' => '<th>',
			'#type'         => 'item', 
			'#markup' => '<p align="left">Target</p>', 
			'#suffix' => '</th></tr>',
		);		
		
		$form['tab11']['programsasaran_t']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<em>Program:</em>', 
			'#suffix' => '</td>',
			
		);				
		$form['tab11']['programsasaran']= array(
			'#prefix' => '<td style="width:45%;">',
			'#type'         => 'item', 
			'#markup' => $data->programsasaran, 
			'#suffix' => '</td>',
		);	
		$form['tab11']['programtarget']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => $data->programtarget, 
			'#suffix' => '</td></tr>',
		);	
		
		$form['tab11']['keluaransasaran_t']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<em>Keluaran:</em>', 
			'#suffix' => '</td>',
			
		);				
		$form['tab11']['keluaransasaran']= array(
			'#prefix' => '<td style="width:45%;">',
			'#type'         => 'item', 
			'#markup' => $data->keluaransasaran, 
			'#suffix' => '</td>',
		);	
		$form['tab11']['keluarantarget']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => $data->keluarantarget, 
			'#suffix' => '</td></tr>',
		);		

		$form['tab11']['hasilsasaran_t']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<em>Hasil:</em>', 
			'#suffix' => '</td>',
			
		);				
		$form['tab11']['hasilsasaran']= array(
			'#prefix' => '<td style="width:45%;">',
			'#type'         => 'item', 
			'#markup' => $data->hasilsasaran, 
			'#suffix' => '</td>',
		);	
		$form['tab11']['hasiltarget']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => $data->hasiltarget, 
			'#suffix' => '</td></tr>',
		);				
	
	$form['kelompoksasaran_x'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#type'   => 'item', 
		'#markup' => '<p><p>',
		'#suffix' => '</div>',	
	);	
	
	$form['kelompoksasaran'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#title'   => 'Kelompok Sasaran:', 
		'#type'   => 'item', 
		'#markup' => '<p><strong>' . $data->kelompoksasaran . '</strong><p>',
		'#suffix' => '</div>',	
	);	

	$form['waktupelaksanaan'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#title'   => 'Waktu Pelaksanaan:', 
		'#type'   => 'item', 
		'#markup' => '<p><strong>' . $data->waktupelaksanaan . '</strong><p>',
		'#suffix' => '</div>',	
	);	
	$form['lokasi'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#title'   => 'Lokasi:', 
		'#type'   => 'item', 
		'#markup' => '<p><strong>' . $data->lokasi . '</strong><p>',
		'#suffix' => '</div>',	
	);		
	$form['anggaran'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#title'   => 'Anggaran:', 
		'#type'   => 'item', 
		'#markup' => '<p><strong>' . apbd_fn($data->total) . '</strong><p>',
		'#suffix' => '</div>',	
	);		


	$form['triwulan'] = array(
		'#prefix' => '<div class="col-md-12">',
		'#type'   => 'item', 
		'#title' => 'Triwulan:',
		'#suffix' => '</div>',	
	);		
		$form['twsatu']= array(
			'#prefix' => '<div class="col-md-3">',
			'#type'   => 'item', 
			'#markup' => '<p>1 : <strong>' . apbd_fn($data->tw1) . '</strong></p>', 
			'#suffix' => '</div>',
			
		);				
		$form['twdua']= array(
			'#prefix' => '<div class="col-md-3">',
			'#type'   => 'item', 
			'#markup' => '<p>2 : <strong>' . apbd_fn($data->tw2) . '</strong></p>',
			'#suffix' => '</div>',
		);	
		$form['twtiga']= array(
			'#prefix' => '<div class="col-md-3">',
			'#type'   => 'item', 
			'#markup' => '<p>3 : <strong>' . apbd_fn($data->tw3) . '</strong></p>',
			'#suffix' => '</div>',
		);	
		$form['twempat']= array(
			'#prefix' => '<div class="col-md-3">',
			'#type'   => 'item', 
			'#markup' => '<p>4 : <strong>' . apbd_fn($data->tw4) . '</strong></p>',
			'#suffix' => '</div></tr>',
		);		
 
}

$form['blank']= array(
	'#type'   => 'item', 
	'#markup' => '<br/> &nbsp; <br/>',
);	

$form['penganggaran'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' HISTORI ANGGARAN',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
	$form['penganggaran']['tablepenganggaran']= array(
		'#prefix' => '<div class="table-responsive"><table class="table"><tr><th width="10px">No</th><th width="50px">Tahun</th><th>Kegiatan</th><th width="100px">Anggaran</th><th width="100px">Realisasi</th></tr>',
		 '#suffix' => '</table></div>',
	);	

$uri = 'http://service.simkedajepara.net/apbdhist.php?token=' . apbd_tahun() . '&kodeuk=' . $kodeuk . '&kegiatan=' . $kegiatan;
$request = drupal_http_request($uri);
//drupal_set_message($idrenja);
$ret_array = drupal_json_decode($request->data);
//drupal_set_message($ret_array["respon_code"]);
if ($ret_array["respon_code"]=='S3') {

		$form['penganggaran']['tablepenganggaran']['no']= array(
				'#prefix' => '<tr><td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['penganggaran']['tablepenganggaran']['tahun' . $no]= array(
				'#prefix' => '<td>',
				'#markup' => '',
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		
		$form['penganggaran']['tablepenganggaran']['kegiatan' . $no]= array(
				'#prefix' => '<td>',
				'#markup' => '',
				'#markup' => '<p style="color:red">' . $ret_array["resp_desc"] . '</p>', 
				'#suffix' => '</td>',
		); 
		$form['penganggaran']['tablepenganggaran']['anggaran' . $no]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '', 
			'#suffix' => '</td>',
		); 
		$form['penganggaran']['tablepenganggaran']['realisasi' . $no]= array(
			//'#type'         => 'checkbox', 
			'#markup'=> '', 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
 } else {
	//drupal_set_message($ret_array["apbdhist"][0]["id"]);
	
	$total_agg = 0;	
	$total_rea = 0;	
	$no = 0;
	foreach ($ret_array["apbdhist"] as $data) {
		$no++;
		//drupal_set_message($data["anggaran"]);
		$form['penganggaran']['tablepenganggaran']['no' . $no]= array(
				'#prefix' => '<tr><td>',
				'#markup' => $no,
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		$form['penganggaran']['tablepenganggaran']['tahun' . $no]= array(
				'#prefix' => '<td>',
				'#markup' => $data["tahun"],
				//'#size' => 10,
				'#suffix' => '</td>',
		); 
		
		$form['penganggaran']['tablepenganggaran']['kegiatan' . $no]= array(
				'#prefix' => '<td>',
				'#markup' => $data["kegiatan"],
				//'#size' => 10,
				'#suffix' => '</td>',
		); 

		$total_agg += $data["anggaran"];	
		$total_rea += $data["realisasi"];	
		
		$form['penganggaran']['tablepenganggaran']['anggaran' . $no]= array(
			//'#type'         => 'textfield', 
			'#prefix' => '<td>',
			'#markup'=> '<p class="text-right">' . apbd_fn($data["anggaran"]) . '</p>', 
			'#suffix' => '</td>',
		); 
		$form['penganggaran']['tablepenganggaran']['realisasi' . $no]= array(
			//'#type'         => 'checkbox', 
			'#markup'=> '<p class="text-right">' . apbd_fn($data["realisasi"]) . '</p>', 
			'#prefix' => '<td>',
			'#suffix' => '</td></tr>',
		);
		
	}
	$no++;
	//drupal_set_message($data["anggaran"]);
	$form['penganggaran']['tablepenganggaran']['no' . $no]= array(
			'#prefix' => '<tr><td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	$form['penganggaran']['tablepenganggaran']['tahun' . $no]= array(
			'#prefix' => '<td>',
			'#markup' => '',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 
	
	$form['penganggaran']['tablepenganggaran']['kegiatan' . $no]= array(
			'#prefix' => '<td>',
			'#markup' => '<strong>TOTAL</strong>',
			//'#size' => 10,
			'#suffix' => '</td>',
	); 

	$form['penganggaran']['tablepenganggaran']['anggaran' . $no]= array(
		//'#type'         => 'textfield', 
		'#prefix' => '<td>',
		'#markup'=> '<p class="text-right"><strong>' . apbd_fn($total_agg) . '</strong></p>', 
		'#suffix' => '</td>',
	); 
	$form['penganggaran']['tablepenganggaran']['realisasi' . $no]= array(
		//'#type'         => 'checkbox', 
		'#markup'=> '<p class="text-right"><strong>' . apbd_fn($total_rea) . '</strong></p>', 
		'#prefix' => '<td>',
		'#suffix' => '</td></tr>',
	);		
}


$str_menu =  '|<a href="/laporanapbddetilkeg/filterkegppa/' . $kodeuk . '/' . $kodekeg . '">Perencanaan</a>' . '|';
$str_menu .=  'Penganggaran|';
$str_menu .=  '<a href="/laporanapbddetilkeg/filterkeg/' . $kodeuk . '/' . $kodekeg . '">Rekening</a>' . '|';

$form['menu']= array(
	'#type'  	=> 'markup', 
	'#markup' 	=>  '<p align="center">...</p><p align="center">' . $str_menu . '</p>',
);	

return $form;


}

function view_kegiatan_ppa($kodeuk, $kodekeg, $kegiatan) {


/*
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' TOLOK UKUR<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);	
*/

$ppa = '0';
$tolokukur = '';
$program = '';
 
db_set_active('penatausahaan');
 
$results = db_query('select k.keluaransasaran, k.keluarantarget, k.lokasi, k.total, p.program, k.idrenja from {kegiatanskpd} k inner join {program} p on k.kodepro=p.kodepro where k.kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));	
$arr_result = $results->fetchAll();
db_set_active();

foreach ($arr_result as $data) {
	$idrenja = $data->idrenja;
	$anggaran = $data->total;	
}
read_perencanaan($idrenja, $program, $tolokukur, $lokasi, $ppa);
$form['program'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#title'   => 'Program:', 
	'#type'   => 'item', 
	'#markup' => '<p><strong>' . $program . '</strong><p>',
	//'#suffix' => '</div>',	
);					

$form['tolokukur'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#title'   => 'Sasaran:', 
	'#type'   => 'item', 
	'#markup' => '<p><strong>' . $tolokukur . '</strong><p>',
	//'#suffix' => '</div>',	
);		
$form['lokasi'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#title'   => 'Lokasi:', 
	'#type'   => 'item', 
	'#markup' => '<p><strong>' . $lokasi . '</strong><p>',
	//'#suffix' => '</div>',	
);		
$form['ppa'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#title'   => 'Plafon PPAS:', 
	'#type'   => 'item', 
	'#markup' => '<p><strong>' . apbd_fn($ppa) . '</strong><p>',
	//'#suffix' => '</div>',	
);	
$form['anggaran'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#title'   => 'Anggaran APBD:', 
	'#type'   => 'item', 
	'#markup' => '<p><strong>' . apbd_fn($anggaran) . '</strong> ' . apbd_simbol_perencanaan($ppa, $anggaran) . '<p>',
	//'#suffix' => '</div>',	
);	

$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' HISTORI PERENCANAAN',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	//'#suffix' => '</div>',
);
//HISTORI
$form['m11']['tab11']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	$form['m11']['tab11']['no1']= array(
		'#prefix' => '<tr><th style="width:5px">',
		'#type'         => 'item', 
		'#markup' => 'No.', 
		'#suffix' => '</th>',
		
	);	
	$form['m11']['tab11']['kodej']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p>Uraian</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Jumlah</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi1']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Awal</p>', 
		'#suffix' => '</th></tr>',
	);		
$i = 0;
 
$uri = 'http://service.simkedajepara.net/ppashist.php?idrenja=' . $idrenja . '&token=2019';
$request = drupal_http_request($uri);

$ret_array = drupal_json_decode($request->data);
//drupal_set_message($ret_array["respon_code"]);
if ($ret_array["respon_code"]=='S3') {

	$i++;
	
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => ''	, 
		'#suffix' => '</td>',
		
	);	

	$form['m11']['tab11']['uraian11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => '<p style="color:red">' . $ret_array["resp_desc"] . '</p>', 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['agg12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">0</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['rea12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">0</p>', 
		'#suffix' => '</td></tr>',
	);	
} else {
	//drupal_set_message($ret_array["ppashist"][0]["id"]);
	//drupal_set_message($ret_array["ppashist"][0]["anggaran"]);
	//$arr_hist = drupal_json_decode($ret_array["ppashist"]);
	
	foreach ($ret_array["ppashist"] as $data) {
		//drupal_set_message($data["id"]);
		//drupal_set_message($data["anggaran"]);
		
		$i++;
	
		$form['m11']['tab11']['no1' . $i]= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $i	, 
			'#suffix' => '</td>',
			
		);	

		$form['m11']['tab11']['uraian11' . $i]= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<p style="color:red">' . $kegiatan . '</p>', 
			'#suffix' => '</td>',
			
		);				
		$form['m11']['tab11']['agg12' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($data["anggaran"]) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['m11']['tab11']['rea12' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($data["awal"]) . '</p>', 
			'#suffix' => '</td></tr>',
		);	
	}
}	
	
$str_menu =  '|Perencanaan|';
$str_menu .=  '<a href="/laporanapbddetilkeg/filterkeginfo/' . $kodeuk . '/' . $kodekeg . '">Penganggaran</a>' . '|';
$str_menu .=  '<a href="/laporanapbddetilkeg/filterkeg/' . $kodeuk . '/' . $kodekeg . '">Rekening</a>' . '|';


$form['menu']= array(
	'#type'  	=> 'markup', 
	'#markup' 	=>  '<p align="center">...</p><p align="center">' . $str_menu . '</p>',
);	
	
return $form;


}

function read_perencanaan($idrenja, &$program, &$tolokukur, &$lokasi, &$ppa) {

//$uri = urlencode('http://service.simkedajepara.net/ppaskeg.php?idrenja=418&token=2019');
//$uri = 'https://www.myapifilms.com/imdb/top';

$ppa = '0';
$tolokukur = 'Tidak ada di PPAS';
$program = 'Tidak ada di PPAS';
$lokasi = 'Tidak ada di PPAS';

$uri = 'http://service.simkedajepara.net/ppaskeg.php?idrenja=' . $idrenja . '&token=2019';
$request = drupal_http_request($uri);

$ret_array = drupal_json_decode($request->data);
//drupal_set_message($ret_array["respon_code"]);
if ($ret_array["respon_code"]=='00') {
	$tolokukur = $ret_array["tolokukur"];
	$program = $ret_array["program"];
	$lokasi = $ret_array["lokasi"];
	$ppa = $ret_array["anggaran"];
}
return 	true;
}
?>

