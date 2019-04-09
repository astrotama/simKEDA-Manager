<?php
function laporanperencanaan_detiluk_main($arg=NULL, $nama=NULL) {
	
	//laporanperencanaan_detiluk_main
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kodekeg = arg(2);
				$idrenja = arg(3);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} 
	
	$results = db_query('select kegiatan from kegiatanskpd where kodekeg=:kodekeg', array(':kodekeg'=>$kodekeg));
	foreach ($results as $data) {	
		$kegiatan = $data->kegiatan;
	}
	
	drupal_set_title($kegiatan);
	
	
	$output = gen_anggaran_belanja($kodekeg, $idrenja);
		
	return drupal_render($output);		
	//return $output;
}

function gen_anggaran_belanja($kodekeg, $idrenja) {

if (apbd_client_type()=='m') {
		$sejuta = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1; 
		$label_milyar = '';
	}

$total_ppa = 0; $total_agg = 0;

//db_set_active('akuntansi');
$form['m11'] = array(
	//'#prefix' => '<div class="col-md-12">', 
	'#type' => 'fieldset', 
	'#title'=>  _bootstrap_icon('unchecked') . ' KEGIATAN<em><small class="span4 text-info pull-right">'.$label_milyar.'</small></em>',
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
	$form['m11']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
	);	

//$sejuta = 1000000;	
if ($page == '1') {
	$i = 0;
	$start = 1;
} else {
	$i = 15*($page-1);
	$start = $i+1;
}

db_set_active('akuntansi');

$jumlahpage = 1;

$query = db_query('SELECT count(kodekeg) jumlahkeg from kegiatanskpd where jenis=2 and total>0 and kodeuk=:kodeuk', array(':kodeuk'=>$kodeuk));	
foreach ($query as $data) {
	$jumlahpage = ceil($data->jumlahkeg / 15);
	$jumlahkeg = $data->jumlahkeg;
}

$results = db_query('select kodekeg, idrenja, kegiatan, total from {kegiatanskpd} where jenis=2 and total>0 and kodeuk=:kodeuk order by kegiatan limit ' . $start . ', 15', array(':kodeuk'=>$kodeuk));	
$arr_result = $results->fetchAllAssoc('kodekeg');
db_set_active();

foreach ($arr_result as $data) {
	 
	$ppa = read_perencanaan($data->idrenja); 
	
	$total_ppa += $ppa;
	$total_agg += $data->total;
	
	//1
	$i++;
	$form['m11']['tab11']['no1' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => $i . '.', 
		'#suffix' => '</td>',
		
	);	

	//$skpd = l($data->namasingkat, 'laporanapbddetilkeg/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	
	$kegiatan = l($data->kegiatan, 'laporanapbddetilkeg/filterkegppa/' . $data->kodeuk . '/' . $data->kodekeg, array('attributes' => array('class' => null)));
	
	$form['m11']['tab11']['row11' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $kegiatan, 
		'#suffix' => '</td>',
		 
	);				
	$form['m11']['tab11']['row12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($ppa/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn($data->total/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	$persen = apbd_hitungpersen($ppa, $data->total);
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => apbd_fn_persen_naikturun($persen),
		'#suffix' => '</td>',
	);
	$form['m11']['tab11']['row15' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'markup', 
		//'#markup' =>  '<a href="/laporandetil/filter/' . $data->kodeuk . '/' . $kodeakun . '"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		'#markup' =>  apbd_simbol_perencanaan($data->total, $ppa),
		'#suffix' => '</td></tr>',
	);	
	
	//$skpd = ($kodeuk=='ZZ'? l('SKPD', 'laporandetiluk/filter/'  . $data_oby->kodeo . '/' . $bulan, array('attributes' => array('class' => null))) : '');
	

}

if ($jumlahpage>1) {
	
	$str_menu = '|';

	for ($p=1; $p<=$jumlahpage; $p++)  {
		
		if ($p == $page)
			$str_menu .= apbd_blank_space() . '<strong>' . $p . '</strong> |';
		else
			$str_menu .= apbd_blank_space() . '<a href="/laporanperencanaanuk/filter/' . $kodeuk . '/' . $p . '">' . $p . '</a>' . ' |';
		
	}
	$form['menu']= array(
		'#type'  	=> 'markup', 
		'#markup' 	=>  '<p align="center">' . $str_menu . '</p>',
	);	

}

/*			
$i++;
$persen = apbd_hitungpersen($total_ppa, $total_agg);
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
	'#markup' => apbd_fn_persen_naikturun($persen),
	'#suffix' => '</td>',
);

$form['m11']['tab11']['row15' . $i]= array(
	'#prefix' => '<td>',
	'#type'         => 'item', 
	'#markup' => apbd_simbol_perencanaan($total_agg, $total_ppa),
	'#suffix' => '</td></tr>',
);	
*/	

return $form;


}


function read_perencanaan($idrenja) {

//$uri = urlencode('http://service.simkedajepara.net/ppaskeg.php?idrenja=418&token=2019');
//$uri = 'https://www.myapifilms.com/imdb/top';

$ppa = '0';

$uri = 'http://service.simkedajepara.net/ppaskeg.php?idrenja=' . $idrenja . '&token=2019';
$request = drupal_http_request($uri);

$ret_array = drupal_json_decode($request->data);
//drupal_set_message($ret_array["respon_code"]);
if ($ret_array["respon_code"]=='00') $ppa = $ret_array["anggaran"];

return 	$ppa;
}

?>

