<?php
function laporanperencanaan_main($arg=NULL, $nama=NULL) {
	
	//http://akt.simkedajepara.net/laporandetil/9/81/421/10/20/kum
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kelompok = arg(2);
				$page = arg(3);
				if ($page=='') $page = '1';				
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$kelompok = '0';
		$page = '1';
	}
	if ($kelompok=='') $kelompok = '0';
	 
	drupal_set_title('Perencanaan APBD ' . apbd_tahun());
	
	if ($kelompok=='all')
		$output = gen_rekap_data();
	else
		$output = gen_report_data($kelompok, $page);
		
		
	//$output = drupal_render($form);
	return drupal_render($output);		
	//return $output;
}

function gen_report_data($kelompok, $page) {

$total_agg = 0; $total_ppa = 0;

if (apbd_client_type()=='m') {
	$sejuta = 1000000;
	$label_milyar = '(juta)';
	$is_mobile = true;	
	
} else {
	$sejuta = 1;
	$label_milyar = '';
	$is_mobile = false;
}
	
if ($kelompok=='0')
	$kelompok_s = 'DINAS/BADAN/KANTOR';
elseif ($kelompok=='1')	
	$kelompok_s = 'KECAMATAN';
elseif ($kelompok=='2')
	$kelompok_s = 'PUSKESMAS';
elseif ($kelompok=='3')
	$kelompok_s = 'SEKOLAH (SMP)';
elseif ($kelompok=='4')
	$kelompok_s = 'UPT DIKPORA';
	
//db_set_active('akuntansi');
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' ' . $kelompok_s . '<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
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
		'#markup' => '<p>OPD</p>', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">PPAS</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['realisasi11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">APBD</p>', 
		'#suffix' => '</th>',
	);	
	$form['m11']['tab11']['persen11']= array(
		'#prefix' => '<th style="width:12%; color:black;">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">%</p>', 
		'#suffix' => '</th>',
	);
	$form['m11']['tab11']['gbr1']= array(
		'#prefix' => '<tr><th style="width:3px">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th></tr>',
		
	);				



$i = 0;

if ($page == '1') {
	$i = 0;
	$start = 0;
} else {
	$i = apbd_row_limit()*($page-1);
	$start = $i;
}

db_set_active('akuntansi');

$jumlahpage = 1;

$query = db_query('SELECT count(kodeuk) jumlahuk from unitkerja where kelompok=:kelompok', array(':kelompok'=>$kelompok));	
foreach ($query as $data) {
	$jumlahpage = ceil($data->jumlahuk / apbd_row_limit());
	$jumlahuk = $data->jumlahuk;
} 
//drupal_set_message($start);
$results = db_query('select u.kodeuk, u.namauk, u.namasingkat, sum(k.total) as anggaran from unitkerja as u inner join kegiatanskpd as k on u.kodeuk=k.kodeuk where k.jenis=2 and u.kelompok=:kelompok group by u.kodeuk order by u.namasingkat limit ' . $start . ', '.apbd_row_limit(), array(':kelompok'=>$kelompok));

//$results = db_query('select u.kodeuk, u.namauk, u.namasingkat, sum(k.total) as anggaran from unitkerja as u inner join kegiatanskpd as k on u.kodeuk=k.kodeuk where k.jenis=2 and u.kelompok=:kelompok group by u.kodeuk order by u.namasingkat limit 0, 10', array(':kelompok'=>$kelompok));
  

$arr_result = $results->fetchAllAssoc('kodeuk');
db_set_active();

$uri = 'public://'; 
$path= file_create_url($uri);	

foreach ($arr_result as $data) {
	
	$anggaran = $data->anggaran;
	$perencanaan = read_perencanaan($data->kodeuk); 
	
	$_SESSION["perencanaan_ppas" . $data->kodeuk] = $perencanaan;
	$_SESSION["perencanaan_apbd" . $data->kodeuk] = $anggaran;
	
	$total_agg += $anggaran;
	$total_ppa += $perencanaan;

	//1
	$i++;
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td style="vertical-align: top;">',
		'#type'   => 'item', 
		'#markup' => $i . '.', 
		'#suffix' => '</td>',
		
	);			

	//$skpd = l($data->namasingkat, 'laporanapbddetilkeg/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	
	$skpd = l(($is_mobile? $data->namasingkat : $data->namauk), 'laporanperencanaanuk/filter/' . $data->kodeuk, array('attributes' => array('class' => null)));
	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'   => 'item', 
		'#markup' => $skpd, 
		'#suffix' => '</td>',
		
	);				
	$form['m11']['tab11']['row12' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($perencanaan/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	$rea_s = apbd_fn($anggaran/$sejuta);
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $rea_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($perencanaan, $anggaran)) . '</p>', 
		'#suffix' => '</td>',
	);
	$form['m11']['tab11']['gbr15' . $i]= array(
		'#prefix' => '<td style="vertical-align: top;">',
		'#type'   => 'item', 
		'#markup' =>  apbd_simbol_perencanaan($perencanaan, $anggaran),
		'#suffix' => '</td></tr>',
		
	);	
	

}

/*			
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
	'#markup' => '<p align="right"><strong>' . apbd_fn($total_ppa/$sejuta) . '</strong></p>', 
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
	'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($total_ppa, $total_agg)) . '</strong></p>', 
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['gbr15' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' =>  apbd_simbol_perencanaan($total_ppa, $total_agg), 
	'#suffix' => '</td></tr>',
	
);
*/

if ($jumlahpage>1) {
	
	$str_menu = '|';

	for ($p=1; $p<=$jumlahpage; $p++)  {
		
		if ($p == $page)
			$str_menu .= apbd_blank_space() . '<strong>' . $p . '</strong> |';
		else
			$str_menu .= apbd_blank_space() . '<a href="/laporanperencanaan/filter/' . $kelompok . '/' . $p . '">' . $p . '</a>' . ' |';
		
	}
	$form['menu']= array(
		'#type'  	=> 'markup', 
		'#markup' 	=>  '<p align="center">' . $str_menu . '</p>',
	);	

}
	
return $form;


}

function gen_rekap_data() {

$total_agg = 0; $total_ppa = 0;

if (apbd_client_type()=='m') {
	$sejuta = 1000000;
	$label_milyar = '(juta)';
	$is_mobile = true;	
	
} else {
	$sejuta = 1;
	$label_milyar = '';
	$is_mobile = false;
}
	

	
//db_set_active('akuntansi');
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">',
	'#type' => 'fieldset',
	'#title'=>  _bootstrap_icon('unchecked') . ' APBD ' . apbd_tahun() . '<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
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
		'#markup' => '', 
		'#suffix' => '</th>',
		
	);				
	$form['m11']['tab11']['anggaran11']= array(
		'#prefix' => '<th style="width:20%; color:black">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th></tr>',
	);	


$i = 0;


$anggaran = 0; $perencanaan = 0;

db_set_active('akuntansi');

$results = db_query('select sum(total) as anggaran from kegiatanskpd where inaktif=0');
foreach ($results as $data) {
	$anggaran = $data->anggaran;
}	
db_set_active();

$perencanaan = read_perencanaan_all();

//1
$i++;
$form['m11']['tab11']['no1' . $i]= array(
	'#prefix' => '<tr><td style="vertical-align: top;">',
	'#type'   => 'item', 
	'#markup' => $i . '.', 
	'#suffix' => '</td>',
	
);			
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td style="vertical-align: top;">',
	'#type'   => 'item', 
	'#markup' => 'Plafon PPAS', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row12' . $i]= array(
	'#prefix' => '<td style="vertical-align: top;">',
	'#type'         => 'item', 
	'#markup' => '<p align="right">' . apbd_fn($perencanaan/$sejuta) . '</p>', 
	'#suffix' => '</td></tr>',
	
);	
	
$i++;
$form['m11']['tab11']['no1' . $i]= array(
	'#prefix' => '<tr><td style="vertical-align: top;">',
	'#type'   => 'item', 
	'#markup' => $i . '.', 
	'#suffix' => '</td>',
	
);			
$form['m11']['tab11']['row11' . $i]= array(
	'#prefix' => '<td style="vertical-align: top;">',
	'#type'   => 'item', 
	'#markup' => 'Anggaran', 
	'#suffix' => '</td>',
	
);				
$form['m11']['tab11']['row12' . $i]= array(
	'#prefix' => '<td style="vertical-align: top;">',
	'#type'         => 'item', 
	'#markup' => '<p align="right">' . apbd_fn($anggaran/$sejuta) . '</p>', 
	'#suffix' => '</td></tr>',
	
);	

	
return $form;


}

function read_perencanaan_all() {

$ppa = '0';

$uri = 'http://service.simkedajepara.net/ppasopd.php?idrenja=ALL&token=2019';
$request = drupal_http_request($uri);

$ret_array = drupal_json_decode($request->data);
//drupal_set_message($ret_array["respon_code"]);
if ($ret_array["respon_code"]=='00') $ppa = $ret_array["anggaran"];

return 	$ppa;
}

function read_perencanaan($kodeuk) {

$idrenja = '';
db_set_active('akuntansi');

$results = db_query('select idrenja from kegiatanskpd where kodeuk=:kodeuk and jenis=2 and idrenja is not null limit 1', array(':kodeuk'=>$kodeuk));

foreach ($results as $data) {
	$idrenja = $data->idrenja;
}
db_set_active();

$ppa = '0';

$uri = 'http://service.simkedajepara.net/ppasopd.php?idrenja=' . $idrenja . '&token=2019';
$request = drupal_http_request($uri);

$ret_array = drupal_json_decode($request->data);
//drupal_set_message($ret_array["respon_code"]);
if ($ret_array["respon_code"]=='00') $ppa = $ret_array["anggaran"];

return 	$ppa;
}


?>

