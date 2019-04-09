<?php
function laporan_main($arg=NULL, $nama=NULL) {

drupal_set_title('Realisasi APBD ' . apbd_tahun());
//$output_form = drupal_get_form('laporan_main_form');	
$output_form = laporan_main_form();	
return drupal_render($output_form);
}


function laporan_main_form() {


	if (apbd_client_type()=='m') {
		$sejuta = 1000000;
		$label_milyar = '(juta)';	
		
	} else {
		$sejuta = 1;
		$label_milyar = '';
	}
	
	$agg_411 = 0; $agg_412 = 0; $agg_413 = 0; $agg_414 = 0; $agg_421 = 0; $agg_422 = 0; $agg_423 = 0; 
	$agg_431 = 0; $agg_432 = 0; $agg_433 = 0; $agg_434 = 0; $agg_435 = 0; 
	$agg_511 = 0; $agg_512 = 0; $agg_513 = 0; $agg_514 = 0; $agg_515 = 0; $agg_516 = 0; $agg_517 = 0; $agg_518 = 0; 
	$agg_521 = 0; $agg_522 = 0; $agg_523 = 0; 
	$agg_52301 = 0; $agg_52302 = 0; $agg_52303 = 0; $agg_52304 = 0; $agg_52305 = 0; $agg_52306 = 0; 
	$agg_611 = 0; $agg_612 = 0; $agg_613 = 0; $agg_614 = 0; $agg_615 = 0; $agg_616 = 0; 
	$agg_621 = 0; $agg_622 = 0; $agg_623 = 0; 

	$rea_411 = 0; $rea_412 = 0; $rea_413 = 0; $rea_414 = 0; $rea_421 = 0; $rea_422 = 0; $rea_423 = 0; 
	$rea_431 = 0; $rea_432 = 0; $rea_433 = 0; $rea_434 = 0; $rea_435 = 0; 
	$rea_511 = 0; $rea_512 = 0; $rea_513 = 0; $rea_514 = 0; $rea_515 = 0; $rea_516 = 0; $rea_517 = 0; $rea_518 = 0; 
	$rea_521 = 0; $rea_522 = 0; $rea_523 = 0; 
	$rea_52301 = 0; $rea_52302 = 0; $rea_52303 = 0; $rea_52304 = 0; $rea_52305 = 0; $rea_52306 = 0; 
	$rea_611 = 0; $rea_612 = 0; $rea_613 = 0; $rea_614 = 0; $rea_615 = 0; $rea_616 = 0; 
	$rea_621 = 0; $rea_622 = 0; $rea_623 = 0;
	
	try {
		db_set_active('akuntansi');
		//anggaran pendapatan
		$results = db_query('select left(kodero,3) as kodej, sum(jumlah) as anggaran from {anggperuk} group by left(kodero,3)');	
		foreach ($results as $data) {
			if ($data->kodej=='411')
				$agg_411 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='412')
				$agg_412 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='413')
				$agg_413 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='414')
				$agg_414 = $data->anggaran/$sejuta;
			
			elseif ($data->kodej=='421')
				$agg_421 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='422')
				$agg_422 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='423')
				$agg_423 = $data->anggaran/$sejuta;
			
			elseif ($data->kodej=='431')
				$agg_431 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='432')
				$agg_432 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='433')
				$agg_433 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='434')
				$agg_434 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='435')
				$agg_435 = $data->anggaran/$sejuta;

			
		}
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}		
	
	try {
		//anggaran belanja
		$results = db_query('select left(a.kodero,3) as kodej, sum(a.jumlah) as anggaran from {anggperkeg} as a inner join {kegiatanskpd} as k on a.kodekeg=k.kodekeg where k.inaktif=0 group by left(a.kodero,3)');	
		foreach ($results as $data) {
			if ($data->kodej=='511')
				$agg_511 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='512')
				$agg_512 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='513')
				$agg_513 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='514')
				$agg_514 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='515')
				$agg_515 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='516')
				$agg_516 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='517')
				$agg_517 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='518')
				$agg_518 = $data->anggaran/$sejuta;

			elseif ($data->kodej=='521')
				$agg_521 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='522')
				$agg_522 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='523')
				$agg_523 = $data->anggaran/$sejuta;

		}	
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}		

		//anggaran belanja modal
	try {	
		$results = db_query('select left(a.kodero,5) as kodej, sum(a.jumlah) as anggaran from {anggperkeg} as a inner join {kegiatanskpd} as k on a.kodekeg=k.kodekeg where k.inaktif=0 and a.kodero like :modal group by left(a.kodero,5)', array(':modal'=>'523%'));	
		foreach ($results as $data) {
			if ($data->kodej=='52301')
				$agg_52301 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='52302')
				$agg_52302 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='52303')
				$agg_52303 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='52304')
				$agg_52304 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='52305')
				$agg_52305 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='52306')
				$agg_52306 = $data->anggaran/$sejuta;

		}	
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}		
		
	try {	
		//anggaran pembiayaan
		$results = db_query('select left(kodero,3) as kodej, sum(jumlah) as anggaran from {anggperda} group by left(kodero,3)');	
		foreach ($results as $data) {
			if ($data->kodej=='611')
				$agg_611 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='612')
				$agg_612 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='613')
				$agg_613 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='614')
				$agg_614 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='615')
				$agg_615 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='616')
				$agg_616 = $data->anggaran/$sejuta;

			elseif ($data->kodej=='621')
				$agg_621 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='622')
				$agg_622 = $data->anggaran/$sejuta;
			elseif ($data->kodej=='623')
				$agg_623 = $data->anggaran/$sejuta;
			
		}
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}		
	
	try {
		//rea pendapatan dan pembiayaan		
		$results = db_query('select left(ji.kodero,3) as kodej, sum(ji.debet-ji.kredit) as debetkredit, sum(ji.kredit-ji.debet) as kreditdebet from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where (ji.kodero like :pendapatan or ji.kodero like :penerimaan) group by left(kodero,3)', array(':pendapatan'=>'4%', ':penerimaan'=>'6%'));

		foreach ($results as $data) {
			if ($data->kodej=='411')
				$rea_411 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='412')
				$rea_412 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='413')
				$rea_413 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='414')
				$rea_414 = $data->kreditdebet/$sejuta;
			
			elseif ($data->kodej=='421')
				$rea_421 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='422')
				$rea_422 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='423')
				$rea_423 = $data->kreditdebet/$sejuta;
			
			elseif ($data->kodej=='431')
				$rea_431 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='432')
				$rea_432 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='433')
				$rea_433 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='434')
				$rea_434 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='435')
				$rea_435 = $data->kreditdebet/$sejuta;

			elseif ($data->kodej=='611')
				$rea_611 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='612')
				$rea_612 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='613')
				$rea_613 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='614')
				$rea_614 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='615')
				$rea_615 = $data->kreditdebet/$sejuta;
			elseif ($data->kodej=='616')
				$rea_616 = $data->kreditdebet/$sejuta;

			elseif ($data->kodej=='621')
				$rea_621 = $data->debetkredit/$sejuta;
			elseif ($data->kodej=='622')
				$rea_622 = $data->debetkredit/$sejuta;
			elseif ($data->kodej=='623')
				$rea_623 = $data->debetkredit/$sejuta;
			
		}
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}		
	
	try {
		//rea belanja
		$results = db_query('select left(ji.kodero,3) as kodej, sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid inner join {kegiatanskpd} as k on j.kodekeg=k.kodekeg where k.inaktif=0 and ji.kodero like :belanja group by left(ji.kodero,3)', array(':belanja'=>'5%'));
		
		foreach ($results as $data) {
			if ($data->kodej=='511')
				$rea_511 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='512')
				$rea_512 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='513')
				$rea_513 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='514')
				$rea_514 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='515')
				$rea_515 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='516')
				$rea_516 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='517')
				$rea_517 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='518')
				$rea_518 = $data->realisasi/$sejuta;

			elseif ($data->kodej=='521')
				$rea_521 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='522')
				$rea_522 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='523')
				$rea_523 = $data->realisasi/$sejuta;
			
		}
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}		
	
	try {	
		//rea belanja modal	
		$results = db_query('select left(ji.kodero,5) as kodej, sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid inner join {kegiatanskpd} as k on j.kodekeg=k.kodekeg inner join {anggperkeg} as ag on k.kodekeg=ag.kodekeg and ji.kodero=ag.kodero where k.inaktif=0 and ji.kodero like :modal group by left(kodero,5)', array(':modal'=>'523%'));

		foreach ($results as $data) {
			if ($data->kodej=='52301')
				$rea_52301 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='52302')
				$rea_52302 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='52303')
				$rea_52303 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='52304')
				$rea_52304 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='52305')
				$rea_52305 = $data->realisasi/$sejuta;
			elseif ($data->kodej=='52306')
				$rea_52306 = $data->realisasi/$sejuta;
			
		}	
		
		db_set_active();
	
	} catch (Exception $e) {
		// Something went wrong somewhere, so roll back now.
		//$txn->rollback();
		// Log the exception to watchdog.
		watchdog_exception('type', $e);
	}
	
	//agg
	$agg_41 = $agg_411 + $agg_412 + $agg_413 + $agg_414;
	$agg_42 = $agg_421 + $agg_422 + $agg_423;
	$agg_43 = $agg_431 + $agg_432 + $agg_433 + $agg_434 + $agg_435;

	$agg_51 = $agg_511 + $agg_512 + $agg_513 + $agg_514 + $agg_515 + $agg_516 + $agg_517 + $agg_518;
	$agg_52 = $agg_521 + $agg_522 + $agg_523;
	
	$agg_4 = $agg_41 + $agg_42 + $agg_43; 
	$agg_5 = $agg_51 + $agg_52; 
	$agg_6 = ($agg_611+$agg_612+$agg_613+$agg_614+$agg_615+$agg_616) - ($agg_621+$agg_622+$agg_623); 

	//rea
	$rea_41 = $rea_411 + $rea_412 + $rea_413 + $rea_414;
	$rea_42 = $rea_421 + $rea_422 + $rea_423;
	$rea_43 = $rea_431 + $rea_432 + $rea_433 + $rea_434 + $rea_435;

	$rea_51 = $rea_511 + $rea_512 + $rea_513 + $rea_514 + $rea_515 + $rea_516 + $rea_517 + $rea_518;
	$rea_52 = $rea_521 + $rea_522 + $rea_523;

	$rea_4 = $rea_41 + $rea_42 + $rea_43; 
	$rea_5 = $rea_51 + $rea_52; 
	$rea_6 = ($rea_611+$rea_612+$rea_613+$rea_614+$rea_615+$rea_616) - ($rea_621+$rea_622+$rea_623);
	
	//ROW 11
	$form['menu1']= array(
		'#prefix' => '<div class="row">',
		'#suffix' => '</div>',
	);	
	//I	
	$form['menu1']['m11'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' RINGKASAN<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu1']['m11']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu1']['m11']['tab11']['uraian11']= array(
			'#prefix' => '<tr><th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu1']['m11']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m11']['tab11']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
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
		$rea_u = l('PENDAPATAN', 'laporandetil/filter/ZZ/4', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row11']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_4) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_4), 'laporandetiluk/filter/ZZ/4', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_4, $rea_4)) . '</p>', 
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
		$rea_u = l('BELANJA', 'laporandetil/filter/ZZ/5', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row31']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_5) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_5), 'laporandetiluk/filter/ZZ/5', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_5, $rea_5)) . '</p>', 
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
		$form['menu1']['m11']['tab11']['row41']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<p style="text-align: center;">DEFISIT</p>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;color:red;">' . apbd_fn($agg_4 - $agg_5) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;color:red;">' . apbd_fn($rea_4 - $rea_5) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;color:red;">' . apbd_fn1(apbd_hitungpersen($agg_4 - $agg_5, $rea_4 - $rea_5)) . '</p>', 
			'#suffix' => '</td></tr>',
		);
		//4
		$rea_u = l('PEMBIAYAAN', 'laporandetil/filter/ZZ/6', array('attributes' => array('class' => null)));
		$form['menu1']['m11']['tab11']['row51']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_6) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($rea_6) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_6, $rea_6)) . '</p>', 
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
		$form['menu1']['m11']['tab11']['row61']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<strong>SILPA</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agg_4 - $agg_5 + $agg_6) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($rea_4 - $rea_5 + $rea_6) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn1(apbd_hitungpersen($agg_4 - $agg_5 + $agg_6, $rea_4 - $rea_5 + $rea_6)) . '</strong></p>', 
			'#suffix' => '</td>',
		);		
		
		
	//II	
	$form['menu1']['m12'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' PENDAPATAN<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu1']['m12']['tab12']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu1']['m12']['tab12']['uraian12']= array(
			'#prefix' => '<tr><th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu1']['m12']['tab12']['anggaran12']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu1']['m12']['tab12']['realisasi12']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
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
		$rea_u = l('PAD', 'laporandetil/filter/ZZ/41', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row11']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);					
		$form['menu1']['m12']['tab12']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_41) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_41), 'laporandetiluk/filter/ZZ/41', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_41, $rea_41)) . '</p>', 
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
		$rea_u = l('PERIMBANGAN', 'laporandetil/filter/ZZ/42', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row31']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_42) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_42), 'laporandetiluk/filter/ZZ/42', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_42, $rea_42)) . '</p>', 
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
		$rea_u = l('LAIN-LAIN', 'laporandetil/filter/ZZ/43', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row41']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_43) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_43), 'laporandetiluk/filter/ZZ/43', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_43, $rea_43)) . '</p>', 
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
		$form['menu1']['m12']['tab12']['row51']= array(
			'#prefix' => '<tr><td>',
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
		$form['menu1']['m12']['tab12']['row61']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m12']['tab12']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agg_4) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		//totalll
		$form['menu1']['m12']['tab12']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($rea_4) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn1(apbd_hitungpersen($agg_4, $rea_4)) . '</strong></p>', 
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
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu2']['m12']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu2']['m12']['tab11']['uraian11']= array(
			'#prefix' => '<tr><th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['m12']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['m12']['tab11']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
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
		$rea_u = l('TDK LANGSUNG', 'laporandetil/filter/ZZ/51', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row11']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_51) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_51), 'laporandetiluk/filter/ZZ/51', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_51, $rea_51)) . '</p>', 
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
		$rea_u = l('LANGSUNG', 'laporandetil/filter/ZZ/52', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row31']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_52) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52), 'laporandetiluk/filter/ZZ/52', array('attributes' => array('class' => null)));
		$form['menu2']['m12']['tab11']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_52, $rea_52)) . '</p>', 
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
		$form['menu2']['m12']['tab11']['row41']= array(
			'#prefix' => '<tr><td>',
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
		$form['menu2']['m12']['tab11']['row51']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m12']['tab11']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agg_51+$agg_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($rea_51+$rea_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m12']['tab11']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn1(apbd_hitungpersen($agg_51 + $agg_52, $rea_51 + $rea_52)) . '</strong></p>', 
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
	
		$form['menu2']['m22']['tab21']['uraian11']= array(
			'#prefix' => '<tr><th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['m22']['tab21']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['m22']['tab21']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
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
		$rea_u = l('PEGAWAI', 'laporandetil/filter/ZZ/521', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row11']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m22']['tab21']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_521) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_521), 'laporandetiluk/filter/ZZ/521', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_521, $rea_521)) . '</p>', 
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
		$rea_u = l('BARANG JASA', 'laporandetil/filter/ZZ/522', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row31']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);			
		//$detil = l('Detil', 'laporandetil/filter/' . $bulan . '/' . $kodeuk . '/' . $data_jen->kodej . '/' . $margin . '/' . $marginkiri . '/view', array('attributes' => array('class' => null)));
		
		$rea_s = l(apbd_fn($rea_522), 'laporandetiluk/filter/ZZ/522', array('attributes' => array('class' => null)));
		
		$form['menu2']['m22']['tab21']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_522) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_522, $rea_522)) . '</p>', 
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
		$rea_u = l('MODAL', 'laporandetil/filter/ZZ/523', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row41']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m22']['tab21']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_523) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_523), 'laporandetiluk/filter/ZZ/523', array('attributes' => array('class' => null)));
		$form['menu2']['m22']['tab21']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_523, $rea_523)) . '</p>', 
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
		$form['menu2']['m22']['tab21']['row51']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['m22']['tab21']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agg_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($rea_52) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu2']['m22']['tab21']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn1(apbd_hitungpersen($agg_52, $rea_52)) . '</strong></p>', 
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
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA MODAL<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu3']['m31']['tab11']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu3']['m31']['tab11']['uraian11']= array(
			'#prefix' => '<tr><th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu3']['m31']['tab11']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu3']['m31']['tab11']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
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
		$rea_u = l('TANAH', 'laporandetil/filter/ZZ/52301', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row11']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_52301) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52301), 'laporandetiluk/filter/ZZ/52301', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_52301, $rea_52301)) . '</p>', 
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
		$rea_u = l('MESIN', 'laporandetil/filter/ZZ/52302', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row31']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_52302) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52302), 'laporandetiluk/filter/ZZ/52302', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_52302, $rea_52302)) . '</p>', 
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
		$rea_u = l('GEDUNG', 'laporandetil/filter/ZZ/52303', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row41']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_52303) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52303), 'laporandetiluk/filter/ZZ/52303', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_52303, $rea_52303)) . '</p>', 
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
		$rea_u = l('JARINGAN', 'laporandetil/filter/ZZ/52304', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row51']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_52304) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52304), 'laporandetiluk/filter/ZZ/52304', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_52304, $rea_52304)) . '</p>', 
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
		$rea_u = l('ATL', 'laporandetil/filter/ZZ/52305', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row61']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_52305) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52305), 'laporandetiluk/filter/ZZ/52305', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_52305, $rea_52305)) . '</p>', 
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
		$rea_u = l('BOS', 'laporandetil/filter/ZZ/52306', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row71']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['row72']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_52306) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_52306), 'laporandetiluk/filter/ZZ/52306', array('attributes' => array('class' => null)));
		$form['menu3']['m31']['tab11']['row73']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['row74']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_52306, $rea_52306)) . '</p>', 
			'#suffix' => '</td>',
		);
		$form['menu3']['m31']['tab11']['row75']= array(
			'#prefix' => '<td>',
			'#type'         => 'markup',
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/52306"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
		
		$form['menu3']['m31']['tab11']['rowx1']= array(
			'#prefix' => '<tr><td>',
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
		$form['menu3']['m31']['tab11']['rowt1']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m31']['tab11']['rowt2']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agg_523) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['rowt3']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($rea_523) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m31']['tab11']['rowt4']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn1(apbd_hitungpersen($agg_523, $rea_523)) . '</strong></p>', 
			'#suffix' => '</td></tr>',
		);

	//2
	$form['menu3']['m22'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' BELANJA TIDAK LANGSUNG<em><small class="span4 text-info pull-right">' . $label_milyar . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	
	$form['menu3']['m22']['tab21']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu3']['m22']['tab21']['uraian11']= array(
			'#prefix' => '<tr><th style="width:48%">',
			'#type'         => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu3']['m22']['tab21']['anggaran11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu3']['m22']['tab21']['realisasi11']= array(
			'#prefix' => '<th style="width:20%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
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
		$rea_u = l('GAJI', 'laporandetil/filter/ZZ/511', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row01']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row02']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_511) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_511), 'laporandetiluk/filter/ZZ/511', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row03']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row04']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_511, $rea_511)) . '</p>', 
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
		$rea_u = l('SUBSIDI', 'laporandetil/filter/ZZ/513', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row11']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row12']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_513) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_513), 'laporandetiluk/filter/ZZ/513', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row13']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row14']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_513, $rea_513)) . '</p>', 
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
		$rea_u = l('HIBAH', 'laporandetil/filter/ZZ/514', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row31']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row32']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($agg_514) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_514), 'laporandetiluk/filter/ZZ/514', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row33']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row34']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($agg_514, $rea_514)) . '</p>', 
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
		$rea_u = l('BANSOS', 'laporandetil/filter/ZZ/515', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row41']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row42']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_515) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_515), 'laporandetiluk/filter/ZZ/515', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row43']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row44']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_515, $rea_515)) . '</p>', 
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
		$rea_u = l('BAGI HASIL', 'laporandetil/filter/ZZ/516', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row51']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row52']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_516) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_516), 'laporandetiluk/filter/ZZ/516', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row53']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row54']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_516, $rea_516)) . '</p>', 
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
		$rea_u = l('BANKEU', 'laporandetil/filter/ZZ/517', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row61']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row62']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_517) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_517), 'laporandetiluk/filter/ZZ/517', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row63']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row64']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_517, $rea_517)) . '</p>', 
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
		$rea_u = l('TDK TERDUGA', 'laporandetil/filter/ZZ/518', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row71']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $rea_u, 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row72']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($agg_518) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($rea_518), 'laporandetiluk/filter/ZZ/518', array('attributes' => array('class' => null)));
		$form['menu3']['m22']['tab21']['row73']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row74']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn1(apbd_hitungpersen($agg_518, $rea_518)) . '</p>', 
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
		$form['menu3']['m22']['tab21']['row81']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu3']['m22']['tab21']['row82']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($agg_51) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row83']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($rea_51) . '</strong></p>', 
			'#suffix' => '</td>',
		);	
		$form['menu3']['m22']['tab21']['row84']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn1(apbd_hitungpersen($agg_51, $rea_51)) . '</strong></p>', 
			'#suffix' => '</td></tr>',
		);
	return $form;

}


?>


