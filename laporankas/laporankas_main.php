<?php
function laporankas_main($arg=NULL, $nama=NULL) {
	
	//http://akt.simkedajepara.net/laporandetil/9/81/421/10/20/kum
	if ($arg) {
		switch($arg) {
			case 'filter':
				$kelompok = arg(2);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$kelompok = '0';
		
	}
	if ($kelompok=='') $kelompok = '0';
	
	drupal_set_title('Anggaran Kas ' . apbd_tahun());
	
	$output = gen_report_anggaran_kas($kelompok);
		
	//$output = drupal_render($form);
	return drupal_render($output);		
	//return $output;
}

function gen_report_anggaran_kas($kelompok) {

$bulan = date('m');
$bulan--;
$total_agg = 0; $total_rea = 0;

if (apbd_client_type()=='m') {
	$sejuta = 1000000;
	$label_milyar = '(juta)';	
	
} else {
	$sejuta = 1;
	$label_milyar = '';
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
		'#markup' => '<p align="right">Rencana</p>', 
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
	$form['m11']['tab11']['gbr1']= array(
		'#prefix' => '<tr><th style="width:3px">',
		'#type'         => 'item', 
		'#markup' => '', 
		'#suffix' => '</th></tr>',
		
	);				



$i = 0;

//db_set_active('akuntansi');

$query = db_select('progressrea', 'p');
$query->innerJoin('unitkerja', 'u', 'p.kodeuk=u.kodeuk');

# get the desired fields from the database
$query->fields('u', array('kodeuk','kodedinas','namasingkat'));
$query->fields('p', array('totalrea','totalagg','totalpersen'));

/*
$query->addExpression('p.totalrea/1000', 'realisasi');
$query->addExpression('p.totalagg/1000', 'anggaran');
$query->addExpression('p.totalpersen', 'persen');
*/

$query->condition('p.bulan', $bulan, '=');
$query->condition('u.kelompok', $kelompok, '=');
$query->orderBy('totalpersen', 'ASC');
$query->orderBy('totalrea', 'ASC');
# execute the query
$results = $query->execute();

$arr_result = $results->fetchAllAssoc('kodeuk');
//db_set_active();

$uri = 'public://';
$path= file_create_url($uri);	

foreach ($arr_result as $data) {
	
	$anggaran = $data->totalagg;
	$realisasi = $data->totalrea; 
	
	$total_agg += $anggaran;
	$total_rea += $realisasi;

	if ($realisasi <= $anggaran) {
		
		if ($data->totalpersen <= 20)
			if ($anggaran==0)
				$imgstatus = "<img src='" . $path . "progress00.png'>";
			else
				$imgstatus = "<img src='" . $path . "progress20.png'>";
			
		else if ($data->totalpersen <= 40)
			$imgstatus = "<img src='" . $path . "progress40.png'>";
		else if ($data->totalpersen <= 60)
			$imgstatus = "<img src='" . $path . "progress60.png'>";
		else if ($data->totalpersen <= 80)
			$imgstatus = "<img src='" . $path . "progress80.png'>";
		else
			$imgstatus = "<img src='" . $path . "progress100.png'>";
		
	} else {
		//$imgstatus = "<img src='/filesprogress-ex.png'>";
		$imgstatus = "<img src='" . $path . "progress-ex.png'>";
	}
	
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
		'#markup' => '<p align="right">' . apbd_fn($anggaran/$sejuta) . '</p>', 
		'#suffix' => '</td>',
	);	
	
	/*
	if (strlen($kodeakun)==8)
		$rea_s = apbd_fn($realisasi/$sejuta);
	else
		$rea_s = l(apbd_fn($realisasi/$sejuta), 'laporandetil/filter/' . $data->kodeuk . '/' . $kodeakun, array('attributes' => array('class' => null)));
	*/
	$rea_s = apbd_fn($realisasi/$sejuta);
	
	$form['m11']['tab11']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . $rea_s . '</p>', 
		'#suffix' => '</td>',
	);	
	$form['m11']['tab11']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($anggaran, $realisasi)) . '</p>', 
		'#suffix' => '</td>',
	);
	$form['m11']['tab11']['gbr15' . $i]= array(
		'#prefix' => '<td>',
		'#type'   => 'item', 
		'#markup' => $imgstatus, 
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
	'#suffix' => '</td>',
);	
$form['m11']['tab11']['gbr15' . $i]= array(
	'#prefix' => '<td>',
	'#type'   => 'item', 
	'#markup' => $imgstatus, 
	'#suffix' => '</td></tr>',
	
);
	
return $form;


}

?>

