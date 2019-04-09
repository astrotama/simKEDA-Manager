<?php 

function import_edit_main($arg=NULL, $nama=NULL) {

	$output_form = drupal_get_form('import_edit_main_form');
	return drupal_render($output_form);// . $output;
		
} 

function import_edit_main_form($form, &$form_state) {
   

	$bulan = arg(1);
	
	if ($bulan=='') $bulan = date('n');
	if ($bulan==1)
		$bulan = 12;
	else
		$bulan--;
	
	//drupal_set_message($bulan);
	
	$opt_bulan['1'] = 'Januari';
	$opt_bulan['2'] = 'Februari';
	$opt_bulan['3'] = 'Maret';
	$opt_bulan['4'] = 'April';
	$opt_bulan['5'] = 'Mei';
	$opt_bulan['6'] = 'Juni';
	$opt_bulan['7'] = 'Juli';
	$opt_bulan['8'] = 'Agustus';
	$opt_bulan['9'] = 'September';
	$opt_bulan['10'] = 'Oktober';
	$opt_bulan['11'] = 'Nopember';
	$opt_bulan['12'] = 'Desember';	
	$form['bulan'] = array (
		'#type' => 'select',
		'#title' =>  t('Bulan'),
		'#options' => $opt_bulan,
		'#default_value' => $bulan,
	);
	$form['submit']= array(
		'#type' => 'submit',
		'#value' =>  '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"> XML LRA</span>',
		'#attributes' => array('class' => array('btn btn-info btn-sm')),
		//'#disabled' => TRUE,
		'#suffix' => "&nbsp;<a href='' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>Tutup</a>",
		
	);
	return $form;
}

function import_edit_main_form_submit($form, &$form_state) {
	$bulan = $form_state['values']['bulan'];
	
	drupal_set_message($bulan);
	
	//prepare_Rekap();
	//update_pendapatan($bulan);
	//update_belanja_01($bulan);
	//update_belanja_02($bulan);
	//update_belanja_03($bulan);
	update_belanja_04($bulan);
}

function prepare_Rekap() {

set_time_limit(0);
ini_set('memory_limit', '1024M');
	
/*
//RESET
db_delete('apbdrekap')
	->condition('kodeakun', '4')
	->execute();

	
drupal_set_message('Delete 4, OK');	
	
//PENDAPATAN 
$reskegmaster = db_query('select kodeuk,kodero,jumlah,jumlahp from {anggperuk}');
//$reskegmaster = db_query('select distinct kodeuk from jurnal where month(tanggal)<=:bulan and jurnalid in (select jurnalid from jurnalitem where left(kodero,1)=:empat)', array(':bulan' => $bulan, ':empat' => '4'));
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas,uk.kodeuk,uk.namauk from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu inner join {fungsi} f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => $datakegmaster->kodeuk));
	foreach ($reskeg as $datakeg) {
	 
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {
			
			db_insert('apbdrekap')
			->fields(array('kodefungsi', 'namafungsi', 'fungsisingkat', 'urusansingkat', 'kodekeg', 'kodeurusan', 'namaurusan', 'kodeprogram', 'namaprogram', 'kodeskpd', 'namaskpd', 'kegiatan', 'sumberdana', 'isppkd', 'kodeakun', 'namaakun', 'kodekelompok', 'namakelompok', 'kodejenis', 'namajenis', 'kodeobyek', 'namaobyek', 'koderincian', 'namarincian', 'anggaran1', 'anggaran2'))
			->values(array(
					
				'kodefungsi' => $datakeg->kodef,
				'namafungsi' => $datakeg->fungsi, 
				'fungsisingkat' => $datakeg->fungsi, 
				'urusansingkat' => $datakeg->urusan, 
				'kodekeg' => '000000000000', 
				'kodeurusan' => $datakeg->kodeu, 
				'namaurusan' => $datakeg->urusan, 
				'kodeprogram' => '000', 
				'namaprogram' => 'Non Program', 
				'kodeskpd' => $datakeg->kodeuk, 
				'namaskpd' => $datakeg->namauk, 
				'kegiatan' => 'Non Kegiatan', 
				'sumberdana' => 'Pendapatan', 
				'isppkd' => '0', 
				'kodeakun' => substr($datakegmaster->kodero, 0, 1), 
				'namaakun' => $datainforek->namaakunutama, 
				'kodekelompok' => substr($datakegmaster->kodero, 0, 2), 
				'namakelompok' => $datainforek->namaakunkelompok, 
				'kodejenis' => substr($datakegmaster->kodero, 0, 3), 
				'namajenis' => $datainforek->namaakunjenis, 
				'kodeobyek' => substr($datakegmaster->kodero, 0, 5), 
				'namaobyek' => $datainforek->namaakunobyek, 
				'koderincian' => $datakegmaster->kodero, 
				'namarincian' => $datainforek->namaakunrincian, 
				
				'anggaran1' => $datakegmaster->jumlah, 
				'anggaran2' => $datakegmaster->jumlahp, 
				))
			->execute();	
			
		}	


	}		
}
*/

/*
//BELANJA
db_delete('apbdrekap')
	->condition('kodeakun', '5')
	->execute();
$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and kodeuk<=:kodeuk', array(':kodeuk'=>'20'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}	
drupal_set_message('0. Selesai...');



$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and (kodeuk>=:kodeukbawah and kodeuk<=:kodeukatas)', array(':kodeukbawah'=>'21', ':kodeukatas'=>'40'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}	
drupal_set_message('1. Selesai...');


$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and (kodeuk>=:kodeukbawah and kodeuk<=:kodeukatas)', array(':kodeukbawah'=>'41', ':kodeukatas'=>'60'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}	
drupal_set_message('2. Selesai...');


$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and (kodeuk>=:kodeukbawah and kodeuk<=:kodeukatas)', array(':kodeukbawah'=>'61', ':kodeukatas'=>'80'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}	
drupal_set_message('3. Selesai...');



$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and (kodeuk>=:kodeukbawah and kodeuk<=:kodeukatas)', array(':kodeukbawah'=>'81', ':kodeukatas'=>'99'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}	
drupal_set_message('4. Selesai...');


$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and (kodeuk>=:kodeukbawah and kodeuk<=:kodeukatas)', array(':kodeukbawah'=>'A0', ':kodeukatas'=>'B9'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}	
drupal_set_message('5. Selesai...');



$reskegmaster = db_query('select kodeuk, kodekeg from {kegiatanskpd} where inaktif=0 and kodeuk>=:kodeuk', array(':kodeuk'=>'C0'));
foreach ($reskegmaster as $datakegmaster) {
	prepare_kegiatan($datakegmaster->kodeuk, $datakegmaster->kodekeg);
}
drupal_set_message('6. Selesai...');	
*/

/*
//PEMBIAYAAN
db_delete('apbdrekap')
	->condition('kodeakun', '6')
	->execute();
	
//PEMBIAYAAN 
$reskegmaster = db_query('select kodero,jumlah,jumlahp from {anggperda}');
foreach ($reskegmaster as $datakegmaster) {
	$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.kodeuk, uk.namauk from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu inner join {fungsi} f on u.kodef=f.kodef where uk.kodeuk=:kodeuk', array(':kodeuk' => '81'));
	foreach ($reskeg as $datakeg) {
	 
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datakegmaster->kodero));
		foreach ($resinforek as $datainforek) {
			
				
			db_insert('apbdrekap')
			->fields(array('kodefungsi', 'namafungsi', 'fungsisingkat', 'urusansingkat', 'kodekeg', 'kodeurusan', 'namaurusan', 'kodeprogram', 'namaprogram', 'kodeskpd', 'namaskpd', 'kegiatan', 'sumberdana', 'isppkd', 'kodeakun', 'namaakun', 'kodekelompok', 'namakelompok', 'kodejenis', 'namajenis', 'kodeobyek', 'namaobyek', 'koderincian', 'namarincian', 'anggaran1', 'anggaran2'))
			->values(array(
					
				'kodefungsi' => $datakeg->kodef,
				'namafungsi' => $datakeg->fungsi, 
				'fungsisingkat' => $datakeg->fungsi, 
				'urusansingkat' => $datakeg->urusan, 
				'kodekeg' => '999999999999', 
				'kodeurusan' => $datakeg->kodeu, 
				'namaurusan' => $datakeg->urusan, 
				'kodeprogram' => '000', 
				'namaprogram' => 'Non Program', 
				'kodeskpd' => $datakeg->kodeuk, 
				'namaskpd' => $datakeg->namauk, 
				'kegiatan' => 'Non Kegiatan', 
				'sumberdana' => 'Pembiayaan', 
				'isppkd' => '1', 
				'kodeakun' => substr($datakegmaster->kodero, 0, 1), 
				'namaakun' => $datainforek->namaakunutama, 
				'kodekelompok' => substr($datakegmaster->kodero, 0, 2), 
				'namakelompok' => $datainforek->namaakunkelompok, 
				'kodejenis' => substr($datakegmaster->kodero, 0, 3), 
				'namajenis' => $datainforek->namaakunjenis, 
				'kodeobyek' => substr($datakegmaster->kodero, 0, 5), 
				'namaobyek' => $datainforek->namaakunobyek, 
				'koderincian' => $datakegmaster->kodero, 
				'namarincian' => $datainforek->namaakunrincian, 
				
				'anggaran1' => $datakegmaster->jumlah, 
				'anggaran2' => $datakegmaster->jumlahp, 
				
				))
			->execute();		
			
		}	


	}		
}
*/

drupal_set_message('Selesai...');

}

function prepare_kegiatan($kodeuk, $kodekeg) {
	
$reskeg = db_query('select  f.kodef, f.fungsi, u.kodeu, u.urusan, uk.kodedinas, uk.urusan as urusandinas, uk.namauk, p.kodepro, p.program, k.kegiatan, k.sumberdana1, k.isppkd from kegiatanskpd k inner join unitkerja uk on k.kodeuk=uk.kodeuk inner join program p on k.kodepro=p.kodepro inner join urusan u on p.kodeu=u.kodeu inner join fungsi f on u.kodef=f.kodef where k.kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
foreach ($reskeg as $datakeg) {
 
	//REKENING
	$resrek = db_query('SELECT kodero,jumlah,jumlahpenetapan FROM {anggperkeg} WHERE kodekeg=:kodekeg', array(':kodekeg' => $kodekeg));
	
	foreach ($resrek as $datarek) {
			
		$resinforek = db_query('select namaakunutama, namaakunkelompok, namaakunjenis, namaakunobyek, namaakunrincian from rekeninglengkap where kodero=:kodero', array(':kodero' => $datarek->kodero));
		foreach ($resinforek as $datainforek) {
						
	
			db_insert('apbdrekap')
			->fields(array('kodefungsi', 'namafungsi', 'fungsisingkat', 'urusansingkat', 'kodekeg', 'kodeurusan', 'namaurusan', 'kodeprogram', 'namaprogram', 'kodeskpd', 'namaskpd', 'kegiatan', 'sumberdana', 'isppkd', 'kodeakun', 'namaakun', 'kodekelompok', 'namakelompok', 'kodejenis', 'namajenis', 'kodeobyek', 'namaobyek', 'koderincian', 'namarincian', 'anggaran1', 'anggaran2'))
			->values(array(
				'kodefungsi' => $datakeg->kodef,
				'namafungsi' => $datakeg->fungsi, 
				'fungsisingkat' => $datakeg->fungsi, 
				'urusansingkat' => $datakeg->urusan, 
				'kodekeg' => $kodekeg, 
				'kodeurusan' => $datakeg->kodeu, 
				'namaurusan' => $datakeg->urusan, 
				'kodeprogram' => $datakeg->kodepro, 
				'namaprogram' => $datakeg->program, 
				'kodeskpd' => $kodeuk, 
				'namaskpd' => $datakeg->namauk, 
				'kegiatan' => $datakeg->kegiatan, 
				'sumberdana' => $datakeg->sumberdana1, 
				'isppkd' => $datakeg->isppkd, 
				'kodeakun' => substr($datarek->kodero, 0, 1), 
				'namaakun' => $datainforek->namaakunutama, 
				'kodekelompok' => substr($datarek->kodero, 0, 2), 
				'namakelompok' => $datainforek->namaakunkelompok, 
				'kodejenis' => substr($datarek->kodero, 0, 3), 
				'namajenis' => $datainforek->namaakunjenis, 
				'kodeobyek' => substr($datarek->kodero, 0, 5), 
				'namaobyek' => $datainforek->namaakunobyek, 
				'koderincian' => $datarek->kodero, 
				'namarincian' => $datainforek->namaakunrincian, 
				
				'anggaran1' => $datarek->jumlahpenetapan, 
				'anggaran2' => $datarek->jumlah, 
				
				))
			->execute();	
		}	
			
	}

}	

	
}

function update_pendapatan($bulan) {
	$resmaster = db_query('select kodeskpd,	koderincian from {apbdrekap} where kodeakun=:kodeakun', array(':kodeakun'=>'4'));
	foreach ($resmaster as $data) {
		
		//drupal_set_message($data->kodeskpd . ' ' . $data->koderincian);
		
		$realisasi = read_realisasi_pendapatan($data->kodeskpd, $data->koderincian, $bulan);
		
		db_update('apbdrekap')
			->fields(array(
				'realisasi' . $bulan => $realisasi,
			))		
			->condition('kodeskpd', $data->kodeskpd)
			->condition('koderincian', $data->koderincian)
			->execute();		
		
	}
}

function update_belanja_01($bulan) {

	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$resmaster = db_query('select kodekeg,koderincian from {apbdrekap} where kodeakun=:kodeakun and kodeskpd<=:kodeskpd', array(':kodeakun'=>'5', ':kodeskpd'=>'05'));
	foreach ($resmaster as $data) {
		
		//drupal_set_message($data->kodekeg . ' ' . $data->koderincian);
		
		$realisasi = read_realisasi_belanja($data->kodekeg, $data->koderincian, $bulan);
		
		db_update('apbdrekap')
			->fields(array(
				'realisasi' . $bulan => $realisasi,
			))		
			->condition('kodekeg', $data->kodekeg)
			->condition('koderincian', $data->koderincian)
			->execute();		
		
	}
	drupal_set_message('Selesai...');
}

function update_belanja_02($bulan) {

	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$resmaster = db_query('select kodekeg,koderincian from {apbdrekap} where kodeakun=:kodeakun and (kodeskpd>=:kodebawah and kodeskpd<=:kodeatas)', array(':kodeakun'=>'5', ':kodebawah'=>'06', ':kodeatas'=>'10'));
	foreach ($resmaster as $data) {
		
		//drupal_set_message($data->kodekeg . ' ' . $data->koderincian);
		
		$realisasi = read_realisasi_belanja($data->kodekeg, $data->koderincian, $bulan);
		
		db_update('apbdrekap')
			->fields(array(
				'realisasi' . $bulan => $realisasi,
			))		
			->condition('kodekeg', $data->kodekeg)
			->condition('koderincian', $data->koderincian)
			->execute();		
		
	}
	drupal_set_message('Selesai...');
}

function update_belanja_03($bulan) {

	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$resmaster = db_query('select kodekeg,koderincian from {apbdrekap} where kodeakun=:kodeakun and (kodeskpd>=:kodebawah and kodeskpd<=:kodeatas)', array(':kodeakun'=>'5', ':kodebawah'=>'11', ':kodeatas'=>'20'));
	foreach ($resmaster as $data) {
		
		//drupal_set_message($data->kodekeg . ' ' . $data->koderincian);
		
		$realisasi = read_realisasi_belanja($data->kodekeg, $data->koderincian, $bulan);
		
		db_update('apbdrekap')
			->fields(array(
				'realisasi' . $bulan => $realisasi,
			))		
			->condition('kodekeg', $data->kodekeg)
			->condition('koderincian', $data->koderincian)
			->execute();		
		
	}
	drupal_set_message('Selesai...');
}

function update_belanja_04($bulan) {

	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	$resmaster = db_query('select kodekeg,koderincian from {apbdrekap} where kodeakun=:kodeakun and (kodeskpd>=:kodebawah and kodeskpd<=:kodeatas)', array(':kodeakun'=>'5', ':kodebawah'=>'21', ':kodeatas'=>'25'));
	foreach ($resmaster as $data) {
		
		//drupal_set_message($data->kodekeg . ' ' . $data->koderincian);
		
		$realisasi = read_realisasi_belanja($data->kodekeg, $data->koderincian, $bulan);
		
		db_update('apbdrekap')
			->fields(array(
				'realisasi' . $bulan => $realisasi,
			))		
			->condition('kodekeg', $data->kodekeg)
			->condition('koderincian', $data->koderincian)
			->execute();		
		
	}
	drupal_set_message('Selesai...');
}


function read_realisasi_pendapatan($kodeuk, $kodero, $bulan) {
	 db_set_active('akuntansi');
	 
	$realisasi = 0;	
	$resrek = db_query('SELECT ji.kodero, sum(ji.kredit-ji.debet) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.kodeuk=:kodeuk AND ji.kodero=:kodero AND month(j.tanggal)=:bulan', array(':kodeuk' => $kodeuk, ':kodero' => $kodero, ':bulan' => $bulan));	
	foreach ($resrek as $data) {
		$realisasi = $data->realisasi;
	}
	db_set_active();
	
	if ($realisasi=='null') $realisasi = 0;
	if ($realisasi=='') $realisasi = 0;
	
	return $realisasi;
}

function read_realisasi_belanja($kodekeg, $kodero, $bulan) {
	db_set_active('akuntansi');
	
	$realisasi = 0;	
	$resrek = db_query('SELECT ji.kodero, sum(ji.debet-ji.kredit) realisasi FROM {jurnalitem} ji INNER JOIN {jurnal} j ON ji.jurnalid=j.jurnalid WHERE j.kodekeg=:kodekeg AND ji.kodero=:kodero AND month(j.tanggal)=:bulan', array(':kodekeg' => $kodekeg, ':kodero' => $kodero, ':bulan' => $bulan));	
	foreach ($resrek as $data) {
		$realisasi = $data->realisasi;
	}
	db_set_active();
	
	if ($realisasi=='null') $realisasi = 0;
	if ($realisasi=='') $realisasi = 0;
	
	return $realisasi;
}


?>