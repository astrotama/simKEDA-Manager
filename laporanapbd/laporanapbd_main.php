<?php
function laporanapbd_main($arg=NULL, $nama=NULL) {
	drupal_set_title('Penganggaran APBD ' . apbd_tahun_lalu() . '/' . apbd_tahun());
	//$output_form = drupal_get_form('laporanapbd_main_form');	
	$output_form = laporanapbd_main_form();	
	return drupal_render($output_form);
}


function laporanapbd_main_form() {


	if (apbd_client_type()=='m') {
		$semilyar = 1000000; 
		$label_milyar = '(juta)';	
		
	} else {
		$semilyar = 1; 
		$label_milyar = '';
	}
	
	
	$agglalu_411 = 0; $agglalu_412 = 0; $agglalu_413 = 0; $agglalu_414 = 0; $agglalu_421 = 0; $agglalu_422 = 0; $agglalu_423 = 0; 
	$agglalu_431 = 0; $agglalu_432 = 0; $agglalu_433 = 0; $agglalu_434 = 0; $agglalu_435 = 0; 
	$agglalu_511 = 0; $agglalu_512 = 0; $agglalu_513 = 0; $agglalu_514 = 0; $agglalu_515 = 0; $agglalu_516 = 0; $agglalu_517 = 0; $agglalu_518 = 0; 
	$agglalu_521 = 0; $agglalu_522 = 0; $agglalu_523 = 0; 
	$agglalu_52301 = 0; $agglalu_52302 = 0; $agglalu_52303 = 0; $agglalu_52304 = 0; $agglalu_52305 = 0; $agglalu_52306 = 0; 
	$agglalu_611 = 0; $agglalu_612 = 0; $agglalu_613 = 0; $agglalu_614 = 0; $agglalu_615 = 0; $agglalu_616 = 0; 
	$agglalu_621 = 0; $agglalu_622 = 0; $agglalu_623 = 0; 

	$aggskrg_411 = 0; $aggskrg_412 = 0; $aggskrg_413 = 0; $aggskrg_414 = 0; $aggskrg_421 = 0; $aggskrg_422 = 0; $aggskrg_423 = 0; 
	$aggskrg_431 = 0; $aggskrg_432 = 0; $aggskrg_433 = 0; $aggskrg_434 = 0; $aggskrg_435 = 0; 
	$aggskrg_511 = 0; $aggskrg_512 = 0; $aggskrg_513 = 0; $aggskrg_514 = 0; $aggskrg_515 = 0; $aggskrg_516 = 0; $aggskrg_517 = 0; $aggskrg_518 = 0; 
	$aggskrg_521 = 0; $aggskrg_522 = 0; $aggskrg_523 = 0; 
	$aggskrg_52301 = 0; $aggskrg_52302 = 0; $aggskrg_52303 = 0; $aggskrg_52304 = 0; $aggskrg_52305 = 0; $aggskrg_52306 = 0; 
	$aggskrg_611 = 0; $aggskrg_612 = 0; $aggskrg_613 = 0; $aggskrg_614 = 0; $aggskrg_615 = 0; $aggskrg_616 = 0; 
	$aggskrg_621 = 0; $aggskrg_622 = 0; $aggskrg_623 = 0;
	
	//anggaran lalu, dr akutansilalu
	db_set_active('akuntansilalu');
	//anggaran pendapatan
	$results = db_query('select left(kodero,3) as kodej, sum(jumlah) as anggaran from {anggperuk} group by left(kodero,3)');	
	foreach ($results as $data) {
		if ($data->kodej=='411')
			$agglalu_411 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='412')
			$agglalu_412 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='413')
			$agglalu_413 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='414')
			$agglalu_414 = $data->anggaran/$semilyar;
		
		elseif ($data->kodej=='421')
			$agglalu_421 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='422')
			$agglalu_422 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='423')
			$agglalu_423 = $data->anggaran/$semilyar;
		
		elseif ($data->kodej=='431')
			$agglalu_431 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='432')
			$agglalu_432 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='433')
			$agglalu_433 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='434')
			$agglalu_434 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='435')
			$agglalu_435 = $data->anggaran/$semilyar;

		
	}

	//anggaran belanja
	$results = db_query('select left(a.kodero,3) as kodej, sum(a.jumlah) as anggaran from {anggperkeg} as a inner join {kegiatanskpd} as k on a.kodekeg=k.kodekeg where k.inaktif=0 group by left(a.kodero,3)');	
	foreach ($results as $data) {
		if ($data->kodej=='511')
			$agglalu_511 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='512')
			$agglalu_512 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='513')
			$agglalu_513 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='514')
			$agglalu_514 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='515')
			$agglalu_515 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='516')
			$agglalu_516 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='517')
			$agglalu_517 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='518')
			$agglalu_518 = $data->anggaran/$semilyar;

		elseif ($data->kodej=='521')
			$agglalu_521 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='522')
			$agglalu_522 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='523')
			$agglalu_523 = $data->anggaran/$semilyar;

	}	

	//anggaran belanja modal	
	$results = db_query('select left(a.kodero,5) as kodej, sum(a.jumlah) as anggaran from {anggperkeg} as a inner join {kegiatanskpd} as k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :modal group by left(a.kodero,5)', array(':modal'=>'523%'));	
	foreach ($results as $data) {
		if ($data->kodej=='52301')
			$agglalu_52301 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52302')
			$agglalu_52302 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52303')
			$agglalu_52303 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52304')
			$agglalu_52304 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52305')
			$agglalu_52305 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52306')
			$agglalu_52306 = $data->anggaran/$semilyar;

	}	
	
	
	//anggaran pembiayaan
	$results = db_query('select left(kodero,3) as kodej, sum(jumlah) as anggaran from {anggperda} group by left(kodero,3)');	
	foreach ($results as $data) {
		if ($data->kodej=='611')
			$agglalu_611 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='612')
			$agglalu_612 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='613')
			$agglalu_613 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='614')
			$agglalu_614 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='615')
			$agglalu_615 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='616')
			$agglalu_616 = $data->anggaran/$semilyar;

		elseif ($data->kodej=='621')
			$agglalu_621 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='622')
			$agglalu_622 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='623')
			$agglalu_623 = $data->anggaran/$semilyar;
		
	}
	db_set_active();
	
	//anggaran sekarang, dr akutansi skrg
	db_set_active('akuntansi');
	//anggaran pendapatan
	$results = db_query('select left(kodero,3) as kodej, sum(jumlah) as anggaran from {anggperuk} group by left(kodero,3)');	
	foreach ($results as $data) {
		if ($data->kodej=='411')
			$aggskrg_411 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='412')
			$aggskrg_412 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='413')
			$aggskrg_413 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='414')
			$aggskrg_414 = $data->anggaran/$semilyar;
		
		elseif ($data->kodej=='421')
			$aggskrg_421 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='422')
			$aggskrg_422 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='423')
			$aggskrg_423 = $data->anggaran/$semilyar;
		
		elseif ($data->kodej=='431')
			$aggskrg_431 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='432')
			$aggskrg_432 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='433')
			$aggskrg_433 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='434')
			$aggskrg_434 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='435')
			$aggskrg_435 = $data->anggaran/$semilyar;

		
	}

	//anggaran belanja
	$results = db_query('select left(a.kodero,3) as kodej, sum(a.jumlah) as anggaran from {anggperkeg} as a inner join {kegiatanskpd} as k on a.kodekeg=k.kodekeg where k.inaktif=0 group by left(a.kodero,3)');	
	foreach ($results as $data) {
		if ($data->kodej=='511')
			$aggskrg_511 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='512')
			$aggskrg_512 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='513')
			$aggskrg_513 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='514')
			$aggskrg_514 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='515')
			$aggskrg_515 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='516')
			$aggskrg_516 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='517')
			$aggskrg_517 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='518')
			$aggskrg_518 = $data->anggaran/$semilyar;

		elseif ($data->kodej=='521')
			$aggskrg_521 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='522')
			$aggskrg_522 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='523')
			$aggskrg_523 = $data->anggaran/$semilyar;

	}	

	//anggaran belanja modal	
	$results = db_query('select left(a.kodero,5) as kodej, sum(a.jumlah) as anggaran from {anggperkeg} as a inner join {kegiatanskpd} as k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :modal group by left(a.kodero,5)', array(':modal'=>'523%'));	
	foreach ($results as $data) {
		if ($data->kodej=='52301')
			$aggskrg_52301 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52302')
			$aggskrg_52302 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52303')
			$aggskrg_52303 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52304')
			$aggskrg_52304 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52305')
			$aggskrg_52305 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='52306')
			$aggskrg_52306 = $data->anggaran/$semilyar;

	}	
	
	
	//anggaran pembiayaan
	$results = db_query('select left(kodero,3) as kodej, sum(jumlah) as anggaran from {anggperda} group by left(kodero,3)');	
	foreach ($results as $data) {
		if ($data->kodej=='611')
			$aggskrg_611 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='612')
			$aggskrg_612 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='613')
			$aggskrg_613 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='614')
			$aggskrg_614 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='615')
			$aggskrg_615 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='616')
			$aggskrg_616 = $data->anggaran/$semilyar;

		elseif ($data->kodej=='621')
			$aggskrg_621 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='622')
			$aggskrg_622 = $data->anggaran/$semilyar;
		elseif ($data->kodej=='623')
			$aggskrg_623 = $data->anggaran/$semilyar;
		
	}
	db_set_active();
	
	//agg
	$agglalu_41 = $agglalu_411 + $agglalu_412 + $agglalu_413 + $agglalu_414;
	$agglalu_42 = $agglalu_421 + $agglalu_422 + $agglalu_423;
	$agglalu_43 = $agglalu_431 + $agglalu_432 + $agglalu_433 + $agglalu_434 + $agglalu_435;

	$agglalu_51 = $agglalu_511 + $agglalu_512 + $agglalu_513 + $agglalu_514 + $agglalu_515 + $agglalu_516 + $agglalu_517 + $agglalu_518;
	$agglalu_52 = $agglalu_521 + $agglalu_522 + $agglalu_523;
	
	$agglalu_4 = $agglalu_41 + $agglalu_42 + $agglalu_43; 
	$agglalu_5 = $agglalu_51 + $agglalu_52; 
	$agglalu_6 = ($agglalu_611+$agglalu_612+$agglalu_613+$agglalu_614+$agglalu_615+$agglalu_616) - ($agglalu_621+$agglalu_622+$agglalu_623); 

	//rea
	$aggskrg_41 = $aggskrg_411 + $aggskrg_412 + $aggskrg_413 + $aggskrg_414;
	$aggskrg_42 = $aggskrg_421 + $aggskrg_422 + $aggskrg_423;
	$aggskrg_43 = $aggskrg_431 + $aggskrg_432 + $aggskrg_433 + $aggskrg_434 + $aggskrg_435;

	$aggskrg_51 = $aggskrg_511 + $aggskrg_512 + $aggskrg_513 + $aggskrg_514 + $aggskrg_515 + $aggskrg_516 + $aggskrg_517 + $aggskrg_518;
	$aggskrg_52 = $aggskrg_521 + $aggskrg_522 + $aggskrg_523;

	$aggskrg_4 = $aggskrg_41 + $aggskrg_42 + $aggskrg_43; 
	$aggskrg_5 = $aggskrg_51 + $aggskrg_52; 
	$aggskrg_6 = ($aggskrg_611+$aggskrg_612+$aggskrg_613+$aggskrg_614+$aggskrg_615+$aggskrg_616) - ($aggskrg_621+$aggskrg_622+$aggskrg_623);

	//ROW 11
	$form['menu1']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	//I	
	$form['menu1']['m11'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' RINGKASAN<em><small class="span4 text-info pull-right">' . $label_milyar  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu1']['m11']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu1']['m11']['tab11']['no11']= array(
			'#prefix' => '<tr><th style="width:3px">',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</th>',
			
		);		
		$form['menu1']['m11']['tab11']['uraian11']= array(
			'#prefix' => '<th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu1']['m11']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m11']['tab11']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m11']['tab11']['persen11']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m11']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
		);	
		
		
		//1
		$persen = apbd_hitungpersen_naikturun($agglalu_4, $aggskrg_4);
		$form['menu1']['m11']['tab11']['row10X']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('PENDAPATAN', 'laporanapbddetil/filter/ZZ/4', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row11']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',			
		);				
		$form['menu1']['m11']['tab11']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_4) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_4), 'laporanapbddetiluk/filter/ZZ/4', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['row15']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/4"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		//2	
		$persen = apbd_hitungpersen_naikturun($agglalu_5, $aggskrg_5);
		$form['menu1']['m11']['tab11']['row30']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);		
		$aggskrg_u = l('BELANJA', 'laporanapbddetil/filter/ZZ/5', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row31']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_5) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_5), 'laporanapbddetiluk/filter/ZZ/5', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		//'<span class="glyphicon glyphicon-ok-sign"></span>'
		$form['menu1']['m11']['tab11']['row35']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/5"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		//3
		$persen = apbd_hitungpersen_naikturun($agglalu_4 - $agglalu_5, $aggskrg_4 - $aggskrg_5);
		$form['menu1']['m11']['tab11']['row40']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);				
		$form['menu1']['m11']['tab11']['row41']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<p style="text-align: center;">DEFISIT</p>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;color:red;">' . apbd_fn($agglalu_4 - $agglalu_5) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;color:red;">' . apbd_fn($aggskrg_4 - $aggskrg_5) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td></tr>',
		);
		
		//4
		$persen = apbd_hitungpersen_naikturun($agglalu_6, $aggskrg_6);
		$form['menu1']['m11']['tab11']['row50']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);
		$aggskrg_u = l('PEMBIAYAAN', 'laporanapbddetil/filter/ZZ/6', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row51']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_6) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($aggskrg_6) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu1']['m11']['tab11']['row55']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/6"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		//5
		$silpa_lalu = ($agglalu_4 - $agglalu_5 + $agglalu_6);
		if (($silpa_lalu<1) and ($silpa_lalu>-1)) $silpa_lalu = 0;
		$silpa_skrg = ($aggskrg_4 - $aggskrg_5 + $aggskrg_6);
		if (($silpa_skrg<1) and ($silpa_skrg>-1)) $silpa_skrg = 0;
		
		//drupal_set_message(number_format((float)$silpa_skrg, 20, ',', '.'));
		
		$persen = apbd_hitungpersen_naikturun($silpa_lalu, $silpa_skrg);
		//drupal_set_message(($aggskrg_4 - $aggskrg_5 + $aggskrg_6));
		$form['menu1']['m11']['tab11']['row60']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);		
		$form['menu1']['m11']['tab11']['row61']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>SILPA</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($silpa_lalu) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($silpa_skrg) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen), 
			'#suffix' => '</td>',
		);		
		
		
	//II	
	$form['menu1']['m12'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' PENDAPATAN<em><small class="span4 text-info pull-right">' . $label_milyar  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu1']['m12']['tab12']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu1']['m12']['tab12']['no11']= array(
			'#prefix' => '<tr><th style="width:3px">',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</th>',
			
		);		
		$form['menu1']['m12']['tab12']['uraian12']= array(
			'#prefix' => '<th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu1']['m12']['tab12']['anggaran12']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m12']['tab12']['realisasi12']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item',  
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m12']['tab12']['persen12']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th>',
		);
		$form['menu1']['m12']['tab12']['link12']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
		);
		
		//1
		$persen = apbd_hitungpersen_naikturun($agglalu_41, $aggskrg_41);
		$form['menu1']['m12']['tab12']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('PAD', 'laporanapbddetil/filter/ZZ/41', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row11']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);					
		$form['menu1']['m12']['tab12']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_41) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_41), 'laporanapbddetiluk/filter/ZZ/41', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn(apbd_hitungpersen_naikturun($agglalu_41, $aggskrg_41)) . '</p>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m12']['tab12']['row15']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/41"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		//2	
		$persen = apbd_hitungpersen_naikturun($agglalu_42, $aggskrg_42);
		$form['menu1']['m12']['tab12']['row30']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);					
		$aggskrg_u = l('PERIMBANGAN', 'laporanapbddetil/filter/ZZ/42', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row31']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_42) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_42), 'laporanapbddetiluk/filter/ZZ/42', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu1']['m12']['tab12']['row35']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/42"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		//3
		$persen = apbd_hitungpersen_naikturun($agglalu_43, $aggskrg_43);
		$form['menu1']['m12']['tab12']['row40']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('LAIN-LAIN', 'laporanapbddetil/filter/ZZ/43', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row41']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_43) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_43), 'laporanapbddetiluk/filter/ZZ/43', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu1']['m12']['tab12']['row45']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/43"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//4
		$form['menu1']['m11']['tab11']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',			
		);			
		$form['menu1']['m12']['tab12']['row51']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<p style="color:white";>.</p>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="color:white";>.</p>', 
			'#suffix' => '</td></tr>',
		);	
		//5
		$persen = apbd_hitungpersen_naikturun($agglalu_4, $aggskrg_4);
		$form['menu1']['m12']['tab12']['row60']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$form['menu1']['m12']['tab12']['row61']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agglalu_4) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		//totalll
		$form['menu1']['m12']['tab12']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($aggskrg_4) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen),  
			'#suffix' => '</td></tr>',
		);
		

	//II	
	//1
	$form['menu2']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	$form['menu2']['m12'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA<em><small class="span4 text-info pull-right">' . $label_milyar  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu2']['m12']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu2']['m12']['tab11']['no11']= array(
			'#prefix' => '<tr><th style="width:3px">',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</th>',
			
		);
		$form['menu2']['m12']['tab11']['uraian11']= array(
			'#prefix' => '<th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['m12']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['m12']['tab11']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['m12']['tab11']['persen11']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th>',
		);
		$form['menu2']['m12']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
		);
		
		//1
		$persen = apbd_hitungpersen_naikturun($agglalu_51, $aggskrg_51);
		$form['menu2']['m12']['tab11']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('TDK LANGSUNG', 'laporanapbddetil/filter/ZZ/51', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row11']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_51) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_51), 'laporanapbddetiluk/filter/ZZ/51', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu2']['m12']['tab11']['row15']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/51"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//2	
		$persen = apbd_hitungpersen_naikturun($agglalu_52, $aggskrg_52);
		$form['menu2']['m12']['tab11']['row30']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('LANGSUNG', 'laporanapbddetil/filter/ZZ/52', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row31']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_52) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52), 'laporanapbddetiluk/filter/ZZ/52', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu2']['m12']['tab11']['row35']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//4
		//$persen = apbd_hitungpersen_naikturun($agglalu_4, $aggskrg_4);
		$form['menu2']['m12']['tab11']['row40']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',			
		);			
		$form['menu2']['m12']['tab11']['row41']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<p style="color:white">.</p>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="color:white;">.</p>', 
			'#suffix' => '</td></tr>',
		);			
		//4
		$persen = apbd_hitungpersen_naikturun($agglalu_51+$agglalu_52, $aggskrg_51+$aggskrg_52);
		$form['menu2']['m12']['tab11']['row50']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$form['menu2']['m12']['tab11']['row51']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agglalu_51+$agglalu_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($aggskrg_51+$aggskrg_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen), 
			'#suffix' => '</td></tr>',
		);	
	
	//2
	$form['menu2']['m22'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA LANGSUNG<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	

	$form['menu2']['m22']['tab21']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu2']['m22']['tab21']['no11']= array(
			'#prefix' => '<tr><th style="width:3px">',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</th>',	
			
		);
		$form['menu2']['m22']['tab21']['uraian11']= array(
			'#prefix' => '<th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => 'Uraian', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['m22']['tab21']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['m22']['tab21']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['m22']['tab21']['persen11']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th>',
		);
		$form['menu2']['m22']['tab21']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
		);
		
		//1
		$persen = apbd_hitungpersen_naikturun($agglalu_521, $aggskrg_521);
		$form['menu2']['m22']['tab21']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('PEGAWAI', 'laporanapbddetil/filter/ZZ/521', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row11']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m22']['tab21']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_521) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_521), 'laporanapbddetiluk/filter/ZZ/521', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu2']['m22']['tab21']['row15']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/521"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//2	
		$persen = apbd_hitungpersen_naikturun($agglalu_522, $aggskrg_522);
		$form['menu2']['m22']['tab21']['row30']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('BARANG JASA', 'laporanapbddetil/filter/ZZ/522', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row31']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);			
		//$detil = l('Detil', 'laporanapbddetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
		
		$aggskrg_s = l(apbd_fn($aggskrg_522), 'laporanapbddetiluk/filter/ZZ/522', array('attributes' => array('class' => null)));
		
		$form['menu2']['m22']['tab21']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_522) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu2']['m22']['tab21']['row35']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/522"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//3
		$persen = apbd_hitungpersen_naikturun($agglalu_523, $aggskrg_523);
		$form['menu2']['m22']['tab21']['row40']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('MODAL', 'laporanapbddetil/filter/ZZ/523', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row41']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m22']['tab21']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_523) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_523), 'laporanapbddetiluk/filter/ZZ/523', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu2']['m22']['tab21']['row45']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/523"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//4
		$persen = apbd_hitungpersen_naikturun($agglalu_52, $aggskrg_52);
		$form['menu2']['m22']['tab21']['row50']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$form['menu2']['m22']['tab21']['row51']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m22']['tab21']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agglalu_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($aggskrg_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen), 
			'#suffix' => '</td></tr>',
		);
	
	//iii
	$form['menu3']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	$form['menu3']['m31'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA MODAL<em><small class="span4 text-info pull-right">' . $label_milyar  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu3']['m31']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu3']['m31']['tab11']['no11']= array(
			'#prefix' => '<tr><th style="width:3px">',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</th>',
			
		);
		$form['menu3']['m31']['tab11']['uraian11']= array(
			'#prefix' => '<th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu3']['m31']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu3']['m31']['tab11']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu3']['m31']['tab11']['persen11']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th>',
		);
		$form['menu3']['m31']['tab11']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
		);
		
		//1
		$persen = apbd_hitungpersen_naikturun($agglalu_52301, $aggskrg_52301);
		$form['menu3']['m31']['tab11']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('TANAH', 'laporanapbddetil/filter/ZZ/52301', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row11']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_52301) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52301), 'laporanapbddetiluk/filter/ZZ/52301', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row15']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52301"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//2	
		$persen = apbd_hitungpersen_naikturun($agglalu_52302, $aggskrg_52302);
		$form['menu3']['m31']['tab11']['row30']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('MESIN', 'laporanapbddetil/filter/ZZ/52302', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row31']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_52302) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52302), 'laporanapbddetiluk/filter/ZZ/52302', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row35']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52302"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//3
		$persen = apbd_hitungpersen_naikturun($agglalu_52303, $aggskrg_52303);
		$form['menu3']['m31']['tab11']['row40']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('GEDUNG', 'laporanapbddetil/filter/ZZ/52303', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row41']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_52303) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52303), 'laporanapbddetiluk/filter/ZZ/52303', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen),  
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row45']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52303"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);		
		//4
		$persen = apbd_hitungpersen_naikturun($agglalu_52304, $aggskrg_52304);
		$form['menu3']['m31']['tab11']['row50']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('JARINGAN', 'laporanapbddetil/filter/ZZ/52304', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row51']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_52304) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52304), 'laporanapbddetiluk/filter/ZZ/52304', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen),  
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row55']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52304"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//5
		$persen = apbd_hitungpersen_naikturun($agglalu_52305, $aggskrg_52305);
		$form['menu3']['m31']['tab11']['row60']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('ATL', 'laporanapbddetil/filter/ZZ/52305', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row61']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_52305) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52305), 'laporanapbddetiluk/filter/ZZ/52305', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen),  
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row65']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52305"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//6
		$persen = apbd_hitungpersen_naikturun($agglalu_52306, $aggskrg_52306);
		$form['menu3']['m31']['tab11']['row70']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('BOS', 'laporanapbddetil/filter/ZZ/52306', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row71']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row72']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_52306) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_52306), 'laporanapbddetiluk/filter/ZZ/52306', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row73']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row74']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row75']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52306"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		$form['menu1']['m11']['tab11']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',			
		);			
		$form['menu3']['m31']['tab11']['rowx1']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<p style="color:white";>.</p>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['rowx2']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['rowx3']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['rowx4']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="color:white";>.</p>', 
			'#suffix' => '</td></tr>',
		);	
	
		//6
		$persen = apbd_hitungpersen_naikturun($agglalu_523, $aggskrg_523);
		$form['menu3']['m31']['tab11']['rowt0']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$form['menu3']['m31']['tab11']['rowt1']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['rowt2']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agglalu_523) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['rowt3']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($aggskrg_523) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['rowt4']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen),  
			'#suffix' => '</td></tr>',
		);

	//2
	$form['menu3']['m22'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA TIDAK LANGSUNG<em><small class="span4 text-info pull-right">' . $label_milyar  .  '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu3']['m22']['tab21']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu3']['m22']['tab21']['no11']= array(
			'#prefix' => '<tr><th style="width:3px">',
			'#type'         => 'item', 
			'#markup' => '', 
			'#suffix' => '</th>',
			
		);
		$form['menu3']['m22']['tab21']['uraian11']= array(
			'#prefix' => '<th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu3']['m22']['tab21']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun_lalu() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu3']['m22']['tab21']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">' . apbd_tahun() . '</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu3']['m22']['tab21']['persen11']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th>',
		);
		$form['menu3']['m22']['tab21']['link11']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</th></tr>',
		);		
		
		//0
		$persen = apbd_hitungpersen_naikturun($agglalu_511, $aggskrg_511);
		$form['menu3']['m22']['tab21']['row00']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('GAJI', 'laporanapbddetil/filter/ZZ/511', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row01']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row02']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_511) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_511), 'laporanapbddetiluk/filter/ZZ/511', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row03']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row04']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row05']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/511"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//1
		$persen = apbd_hitungpersen_naikturun($agglalu_513, $aggskrg_513);
		$form['menu3']['m22']['tab21']['row10']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('SUBSIDI', 'laporanapbddetil/filter/ZZ/513', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row11']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_513) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_513), 'laporanapbddetiluk/filter/ZZ/513', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row15']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/513"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//2	
		$persen = apbd_hitungpersen_naikturun($agglalu_514, $aggskrg_514);
		$form['menu3']['m22']['tab21']['row30']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('HIBAH', 'laporanapbddetil/filter/ZZ/514', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row31']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agglalu_514) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_514), 'laporanapbddetiluk/filter/ZZ/514', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row35']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/514"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//3
		$persen = apbd_hitungpersen_naikturun($agglalu_515, $aggskrg_515);
		$form['menu3']['m22']['tab21']['row40']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('BANSOS', 'laporanapbddetil/filter/ZZ/515', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row41']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_515) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_515), 'laporanapbddetiluk/filter/ZZ/515', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen),  
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row45']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/515"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//4
		$persen = apbd_hitungpersen_naikturun($agglalu_516, $aggskrg_516);
		$form['menu3']['m22']['tab21']['row50']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('BAGI HASIL', 'laporanapbddetil/filter/ZZ/516', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row51']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_516) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_516), 'laporanapbddetiluk/filter/ZZ/516', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen),  
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row55']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/516"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		//5
		$persen = apbd_hitungpersen_naikturun($agglalu_517, $aggskrg_517);
		$form['menu3']['m22']['tab21']['row60']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('BANKEU', 'laporanapbddetil/filter/ZZ/517', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row61']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_517) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_517), 'laporanapbddetiluk/filter/ZZ/517', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row65']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/517"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);		
		//6
		$persen = apbd_hitungpersen_naikturun($agglalu_518, $aggskrg_518);
		$form['menu3']['m22']['tab21']['row70']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$aggskrg_u = l('TDK TERDUGA', 'laporanapbddetil/filter/ZZ/518', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row71']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => $aggskrg_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row72']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agglalu_518) . '</p>', 
			'#suffix' => '</td>',
		);	
		$aggskrg_s = l(apbd_fn($aggskrg_518), 'laporanapbddetiluk/filter/ZZ/518', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row73']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $aggskrg_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row74']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun($persen), 
			'#suffix' => '</td>',
		);
		$form['menu3']['m22']['tab21']['row75']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/518"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);		
		//7
		$persen = apbd_hitungpersen_naikturun($agglalu_51, $aggskrg_51);
		$form['menu3']['m22']['tab21']['row80']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => apbd_simbol_naikturun($persen), 
			'#suffix' => '</td>',			
		);			
		$form['menu3']['m22']['tab21']['row81']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row82']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agglalu_51) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row83']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($aggskrg_51) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row84']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => apbd_fn_persen_naikturun_bold($persen), 
			'#suffix' => '</td></tr>',
		);
	return $form;

}
?>


