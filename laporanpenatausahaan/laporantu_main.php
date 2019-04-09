<?php
function laporantu_main($arg=NULL, $nama=NULL) {

drupal_set_title('Penatausahaan ' . apbd_tahun());
//$output_form = drupal_get_form('laporantu_main_form');	
$output_form = laporantu_main_form();	
return drupal_render($output_form);
}


function laporantu_main_form() {


	
$gaji_num = 0; $gaji_rp =0; 
$rutin_num = 0; $rutin_rp = 0; $uptu_num = 0; $uptu_rp = 0; 
$gu_num = 0; $gu_rp = 0; $ls_num = 0; $ls_rp = 0; $tamsil_num = 0; $tamsil_rp = 0;
$nihil_num = 0; $nihil_rp = 0; $tunihil_num = 0; $tunihil_rp = 0; $gunihil_num = 0; $gunihil_rp = 0;
$pfk_num = 0; $pfk_rp =0;
$restitusi_num = 0; $restitusi_rp = 0;

db_set_active('penatausahaan');

$results = db_query('select jenisdokumen, count(dokid) as jumlah, sum(jumlah) as nominal from {dokumen} where sp2dok=1 group by jenisdokumen');
foreach ($results as $data) {
	if ($data->jenisdokumen=='0') {				//UP
		$uptu_num += $data->jumlah;
		$uptu_rp += $data->nominal;
		
	} elseif ($data->jenisdokumen=='1') {		//GU
		$gu_num = $data->jumlah;
		$gu_rp = $data->nominal;
		
	} elseif ($data->jenisdokumen=='2') {		//TU
		$uptu_num += $data->jumlah;
		$uptu_rp += $data->nominal;

	} elseif ($data->jenisdokumen=='3') {		//GAJI
		$gaji_num = $data->jumlah;
		$gaji_rp = $data->nominal;
	} elseif ($data->jenisdokumen=='4') {		//LS
		$ls_num = $data->jumlah;
		$ls_rp = $data->nominal;
	} elseif ($data->jenisdokumen=='5') {		//GU NIHIL
		$gunihil_num = $data->jumlah;
		$gunihil_rp = $data->nominal;
	} elseif ($data->jenisdokumen=='6') {		//PAD
		$restitusi_num = $data->jumlah;
		$restitusi_rp = $data->nominal;
	} elseif ($data->jenisdokumen=='7') {		//TU NIHIL
		$tunihil_num = $data->jumlah;
		$tunihil_rp = $data->nominal;
	} elseif ($data->jenisdokumen=='8') {		//PFK
		$pfk_num = $data->jumlah;
		$pfk_rp = $data->nominal;
	}		
}	

$results = db_query('select count(dokid) as jumlah, sum(jumlah) as nominal from {dokumen} where jenisdokumen=3 and jenisgaji=4 and sp2dok=1');
foreach ($results as $data) {
	$tamsil_num += $data->jumlah;
	$tamsil_rp += $data->nominal;
}		
$gaji_num -= $tamsil_num;
$gaji_rp -= $tamsil_rp;

$rutin_num = $uptu_num + $gu_num + $ls_num + $tamsil_num;
$rutin_rp = $uptu_rp + $gu_rp + $ls_rp + $tamsil_rp;

$nihil_num = $tunihil_num + $gunihil_num;
$nihil_rp = $tunihil_rp + $gunihil_rp;

db_set_active();

//ROW 11
$form['menu1']= array(
	'#prefix' => '<div class="row">',
	'#suffix' => '</div>',
);	
//I	
$form['menu1']['m11'] = array(
	'#prefix' => '<div class="col-md-6">',
	'#type' => 'fieldset',
	//'#title'=>  _bootstrap_icon('unchecked') . ' REGISTER SP2D<em><small class="span4 text-info pull-right">(milyar)</small></em>',
	'#title'=>  _bootstrap_icon('unchecked') . ' REGISTER SP2D',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	'#suffix' => '</div>',
);	
$form['menu1']['m11']['tab11']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	$form['menu1']['m11']['tab11']['uraian11']= array(
		'#prefix' => '<tr><th>',
		'#type'         => 'item', 
		'#markup' => '<p>Uraian</p>',  
		'#suffix' => '</th>',
		
	);				
	$form['menu1']['m11']['tab11']['anggaran11']= array(
		'#prefix' => '<th>',
		'#type'         => 'item', 
		'#markup' => '<p align="right">Jumlah</p>', 
		'#suffix' => '</th>',
	);	
	$form['menu1']['m11']['tab11']['realisasi11']= array(
		'#prefix' => '<th style="width:40%; color:black">',
		'#type'   => 'item', 
		'#markup' => '<p align="right">Nominal</p>', 
		'#suffix' => '</th>',
	);	
	$form['menu1']['m11']['tab11']['link11']= array(
		'#prefix' => '<th style="width:3px">',
		'#type'   => 'item', 
		'#markup' => '', 
		'#suffix' => '</th></tr>',
	);	
	
	//1.1. REGISTER SP2D
	$form['menu1']['m11']['tab11']['row11']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>I. GAJI</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['row12']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . apbd_fn($gaji_num) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row13']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . apbd_fn($gaji_rp) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row15']= array(
		'#prefix' => '<td>',
		'#type'         => 'markup', 
		'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
		'#suffix' => '</td></tr>',
	);
	//2	RUTIN
	$form['menu1']['m11']['tab11']['row31']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>II. RUTIN</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['row32']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . apbd_fn($rutin_num) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row33']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . apbd_fn($rutin_rp) . '</strong></p>',  
		'#suffix' => '</td>',
	);	
	//'<span class="glyphicon glyphicon-ok-sign"></span>'
	$form['menu1']['m11']['tab11']['row35']= array(
		'#prefix' => '<td>',
		'#type'         => 'markup', 
		'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
		'#suffix' => '</td></tr>',
	);
	
		//II.1. UP/TU
		$form['menu1']['m11']['tab11']['row3511']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. UP/TU', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row3512']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($uptu_num) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3513']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($uptu_rp) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3514']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>', 
			'#suffix' => '</td></tr>',
		);
		//II.2. GU
		$form['menu1']['m11']['tab11']['row3521']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Ganti Uang', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row3522']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($gu_num) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3523']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($gu_rp) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3524']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>', 
			'#suffix' => '</td></tr>',
		);
		//II.3. LS Barang Jasa
		$form['menu1']['m11']['tab11']['row3531']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. LS Barang Jasa', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row3532']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($ls_num) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3533']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($ls_rp) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3534']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>', 
			'#suffix' => '</td></tr>',
		);
		//II.4. Tamsil
		$form['menu1']['m11']['tab11']['row3541']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. Tamsil', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row3542']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($tamsil_num) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3543']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($tamsil_rp) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row3544']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>', 
			'#suffix' => '</td></tr>',
		);
		
	//III. RESTITUSI
	$form['menu1']['m11']['tab21']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);	
	$form['menu1']['m11']['tab11']['row51']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>III. RESTITUSI</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['row52']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($restitusi_num) . '<strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row53']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($restitusi_rp) . '<strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row55']= array(
		'#prefix' => '<td>',
		'#type'         => 'markup', 
		'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
		'#suffix' => '</td></tr>',
	);
	//SUB TOTAL
	$form['menu1']['m11']['tab11']['row561']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SUB TOTAL</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['row562']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($gaji_num+$rutin_num) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row563']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($gaji_rp+$rutin_rp) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row564']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		'#suffix' => '</td></tr>',
	);		
	//batas
	$form['menu1']['m11']['tab11']['row564_batas']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '</br>', 
		'#suffix' => '</td><tr>',
		
	);
	
	//IV. NIHIL
	$form['menu1']['m11']['tab11']['row61']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>IV. NIHIL</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['row62']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($nihil_num) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row63']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($nihil_rp) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row64']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		'#suffix' => '</td></tr>',
	);	
		//1. TU Bihil
		$form['menu1']['m11']['tab11']['row611']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. TU Nihil', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row612']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($tunihil_num) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row613']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($tunihil_rp) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row614']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>', 
			'#suffix' => '</td></tr>',
		);		
		//2. GU Bihil
		$form['menu1']['m11']['tab11']['row621']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. GU Nihil', 
			'#suffix' => '</td>',
			
		);				
		$form['menu1']['m11']['tab11']['row622']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($gunihil_num) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row623']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p style="text-align: right;">' . apbd_fn($gunihil_rp) . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m11']['tab11']['row624']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			'#suffix' => '</td></tr>',
		);	
		
	//V. PFK
	$form['menu1']['m11']['tab11']['row71']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>V. PFK</strong>', 
		'#suffix' => '</td>',
		
	);				
	$form['menu1']['m11']['tab11']['row72']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($pfk_num) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row73']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p style="text-align: right;"><strong>' . apbd_fn($pfk_rp) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m11']['tab11']['row74']= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' =>  '<a href=""><span class="glyphicon glyphicon-menu-hamburger"></span></a>', 
		'#suffix' => '</td></tr>',
	);	

//II	
$form['menu1']['m12'] = array(
	'#prefix' => '<div class="col-md-6">',
	'#type' => 'fieldset',
	//'#title'=>  _bootstrap_icon('unchecked') . ' REALISASI SUMBER DANA<em><small class="span4 text-info pull-right">(milyar)</small></em>',
	'#title'=>  _bootstrap_icon('unchecked') . ' REALISASI SUMBER DANA',
	'#collapsible' => FALSE,
	'#collapsed' => FALSE,		
	'#suffix' => '</div>',
);	
$form['menu1']['m12']['tab12']= array(
	'#prefix' => '<table style="width:100%">',
	 '#suffix' => '</table>',
);
	//header
	$form['menu1']['m12']['tab12']['uraian12']= array(
		'#prefix' => '<tr><th>',
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

	$results = db_query('select sumberdana from {sumberdana} order by sumberdana');
	$arr_results = $results->fetchAllAssoc('sumberdana');
	//db_set_active();

	$tot_anggaran = 0;
	$tot_realiasasi = 0;
	
	$i = 0;
	foreach ($arr_results as $datas) {
		
		$i++;

		$anggaran = 0;
		$realiasasi = 0;
		read_sumberdana($datas->sumberdana, $anggaran, $realiasasi);

		$tot_anggaran += $anggaran;
		$tot_realiasasi += $realiasasi;

		$form['menu1']['m12']['tab12']['row11' . $i]= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => $datas->sumberdana, 
			'#suffix' => '</td>',
			
		);					
		$form['menu1']['m12']['tab12']['row12' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn($anggaran) . '</p>', 
			'#suffix' => '</td>',
		);	
		$rea_s = l(apbd_fn($realiasasi), 'laporandetiluk/filter/ZZ/41', array('attributes' => array('class' => null)));
		$form['menu1']['m12']['tab12']['row13' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . $rea_s . '</p>', 
			'#suffix' => '</td>',
		);	
		$form['menu1']['m12']['tab12']['row14' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($anggaran, $realiasasi)) . '</p>', 
			'#suffix' => '</td>',
		);
		$form['menu1']['m12']['tab12']['row15' . $i]= array(
			'#prefix' => '<td>',
			'#type'         => 'markup', 
			'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/41"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
			//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
			'#suffix' => '</td></tr>',
		);
	}

	//batas
	$form['menu1']['m12']['tab12']['row564_batas1']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '</br>', 
		'#suffix' => '</td><tr>',
		
	);
	$form['menu1']['m12']['tab12']['row564_batas2']= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '</br>', 
		'#suffix' => '</td><tr>',
		
	);	
	
	$i++;
	//TOTAL
	$form['menu1']['m12']['tab12']['row11' . $i]= array(
		'#prefix' => '<tr><td>',
		'#type'   => 'item', 
		'#markup' => '<strong>TOTAL</strong>', 
		'#suffix' => '</td>',
		
	);					
	$form['menu1']['m12']['tab12']['row12' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . apbd_fn($tot_anggaran) . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$rea_s = l(apbd_fn($tot_realiasasi), 'laporandetiluk/filter/ZZ/41', array('attributes' => array('class' => null)));
	$form['menu1']['m12']['tab12']['row13' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . $rea_s . '</strong></p>', 
		'#suffix' => '</td>',
	);	
	$form['menu1']['m12']['tab12']['row14' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'item', 
		'#markup' => '<p align="right"><strong>' . apbd_fn1(apbd_hitungpersen($tot_anggaran, $tot_realiasasi)) . '</strong></p>', 
		'#suffix' => '</td>',
	);
	$form['menu1']['m12']['tab12']['row15' . $i]= array(
		'#prefix' => '<td>',
		'#type'         => 'markup', 
		'#markup' =>  '<a href="/laporandetiluk/filter/%23%23/41"><span class="glyphicon glyphicon-menu-hamburger"></span></a>',
		//'#markup' =>  l('<span class="glyphicon glyphicon-menu-hamburger"></span>',
		'#suffix' => '</td></tr>',
	);	
	
	//II.1. TRIWULAN
	//$detect = mobile_detect_get_object();	
	//$is_mobile = $detect->isMobile();
	if (apbd_client_type()=='m') {
		$seribu = 1000; 
		$label = '(ribuan)';	
	} else {
		$seribu = 1; 
		$label = '';
	}
	
	
	$arr_agg_tw = array(0, 0, 0, 0); $arr_rea_tw = array(0, 0, 0, 0); 
	$arr_agg_tw_kum = array(0, 0, 0, 0); $arr_rea_tw_kum = array(0, 0, 0, 0); 
	$arr_tw_label = array('I', 'II', 'III', 'IV'); 
	$arr_tw_label_kum = array('I', 'I+II', 'I sd III', 'I sd IV');
	
	db_set_active('penatausahaan');
	//anggaran belanja
	$i = -1;
	$results = db_query('select sum(tw1) as tot_tw1,sum(tw2) as tot_tw2,sum(tw3) as tot_tw3,sum(total) as tot_total from {kegiatanskpd} where inaktif=0 and total>0');	
	foreach ($results as $data) {
			
		$arr_agg_tw[0] = $data->tot_tw1/$seribu;
		$arr_agg_tw[1] = $data->tot_tw2/$seribu;
		$arr_agg_tw[2] = $data->tot_tw3/$seribu;

		$arr_agg_tw_kum[0] = $data->tot_tw1/$seribu;
		$arr_agg_tw_kum[1] = $arr_agg_tw[0] + ($data->tot_tw2/$seribu);
		$arr_agg_tw_kum[2] = $arr_agg_tw[1] + ($data->tot_tw3/$seribu);
		$arr_agg_tw_kum[3] = $data->tot_total/$seribu;
		
		$arr_agg_tw[3] = ($data->tot_total - $arr_agg_tw_kum[2])/$seribu;
	}

	db_set_active('akuntansi');
	//1
	$results = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where ji.kodero like :belanja and month(j.tanggal)<=3', array(':belanja'=>'5%'));
	foreach ($results as $data) {
		$arr_rea_tw[0] = $data->realisasi/$seribu;
		$arr_rea_tw_kum[0] = $data->realisasi/$seribu;
	}
	//2
	$results = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where ji.kodero like :belanja and month(j.tanggal)<=6', array(':belanja'=>'5%'));
	foreach ($results as $data) {
		$arr_rea_tw_kum[1] = $data->realisasi/$seribu;
		$arr_rea_tw[1] = ($data->realisasi/$seribu) - $arr_rea_tw_kum[0];
	}
	//3
	$results = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where ji.kodero like :belanja and month(j.tanggal)<=9', array(':belanja'=>'5%'));
	foreach ($results as $data) {
		$arr_rea_tw_kum[2] = $data->realisasi/$seribu;
		$arr_rea_tw[2] = ($data->realisasi/$seribu) - $arr_rea_tw_kum[1];
	}
	//4
	$results = db_query('select sum(ji.debet-ji.kredit) as realisasi from {jurnalitem} as ji inner join {jurnal} as j on ji.jurnalid=j.jurnalid where ji.kodero like :belanja', array(':belanja'=>'5%'));
	foreach ($results as $data) {
		$arr_rea_tw_kum[3] = $data->realisasi/$seribu;
		$arr_rea_tw[3] = ($data->realisasi/$seribu) - $arr_rea_tw_kum[2];
	}
	
	db_set_active();

//ROW 21
$form['menu2']= array(
	'#prefix' => '<div class="row">',
	'#suffix' => '</div>',
);		
	$form['menu2']['tw'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		'#title'=>  _bootstrap_icon('unchecked') . ' REALISASI TRIWULAN<em><small class="span4 text-info pull-right">' . $label . '</small></em>',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	

	$form['menu2']['tw']['isi']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu2']['tw']['isi']['uraian11_h']= array(
			'#prefix' => '<tr><th>',
			'#type'   => 'item', 
			'#markup' => '<p>Triwulan</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['tw']['isi']['anggaran11_h']= array(
			'#prefix' => '<th style="width:35%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Anggaran</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['tw']['isi']['realisasi11_h']= array(
			'#prefix' => '<th style="width:35%; color:black">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">Realisasi</p>', 
			'#suffix' => '</th>',
		);	
		$form['menu2']['tw']['isi']['persen11_h']= array(
			'#prefix' => '<th style="width:12%; color:black;">',
			'#type'   => 'item', 
			'#markup' => '<p align="right">%</p>', 
			'#suffix' => '</th></tr>',
		);	
		
		//I
		for ($i=0; $i<=3; $i++) {
			
			
			
			$form['menu2']['tw']['isi']['row_u_tw' . $i]= array(
				'#prefix' => '<tr><td>',
				'#type'   => 'item', 
				'#markup' => $arr_tw_label[$i], 
				'#suffix' => '</td>',
				
			);				
			$form['menu2']['tw']['isi']['row_a_tw' . $i]= array(
				'#prefix' => '<td>',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($arr_agg_tw[$i]) . '</p>', 
				'#suffix' => '</td>',
			);	
			$form['menu2']['tw']['isi']['row_r_tw' . $i]= array(
				'#prefix' => '<td>',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($arr_rea_tw[$i]) . '</p>', 
				'#suffix' => '</td>',
			);	
			$form['menu2']['tw']['isi']['row_p_tw' . $i]= array(
				'#prefix' => '<td>',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($arr_agg_tw[$i], $arr_rea_tw[$i])) . '</p>', 
				'#suffix' => '</td></tr>',
			);
			
			if ($i>0) {
				$form['menu2']['tw']['isi']['row_u_tw_d' . $i]= array(
					'#prefix' => '<tr style="color:blue; font-style: italic;"><td >',
					'#type'   => 'item', 
					'#markup' =>  '<p align="center">' . $arr_tw_label_kum[$i] . '</p>',
					'#suffix' => '</td>',
					
				);				
				$form['menu2']['tw']['isi']['row_a_tw_d' . $i]= array(
					'#prefix' => '<td>',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($arr_agg_tw_kum[$i]) . '</p>', 
					'#suffix' => '</td>',
				);	
				$form['menu2']['tw']['isi']['row_r_tw_d' . $i]= array(
					'#prefix' => '<td>',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn($arr_rea_tw_kum[$i]) . '</p>', 
					'#suffix' => '</td>',
				);	
				$form['menu2']['tw']['isi']['row_p_tw_d' . $i]= array(
					'#prefix' => '<td>',
					'#type'         => 'item', 
					'#markup' => '<p align="right">' . apbd_fn1(apbd_hitungpersen($arr_agg_tw_kum[$i], $arr_rea_tw_kum[$i])) . '</p>', 
					'#suffix' => '</td></tr>',
				);				
			}
			
		}		
	
	//PAJAK
	$arr_pajak_uraian = array('PPh Pasal 21', 'PPh Pasal 22', 'PPh Pasal 23', 'PPh Pasal 4(2)', 'PPN', 'Pajak Daerah');$arr_pajak_value = array(0, 0, 0, 0, 0, 0); 
	
	/*
	db_set_active('bendahara');
	//anggaran belanja
	$total = 0;	
	$results = db_query('SELECT ltpajak.kodepajak, sum(bendaharapajak.jumlah) as total FROM ltpajak INNER JOIN bendaharapajak ON ltpajak.kodepajak=bendaharapajak.kodepajak GROUP BY ltpajak.kodepajak');	
	foreach ($results as $data) {
		
		$total += $data->total;
		
		if ($data->kodepajak=='01')
			$arr_pajak_value[0] = $data->total;
		elseif ($data->kodepajak=='02')	
			$arr_pajak_value[1] = $data->total;
		elseif ($data->kodepajak=='03')	
			$arr_pajak_value[2] = $data->total;
		elseif ($data->kodepajak=='04')	
			$arr_pajak_value[3] = $data->total;
		elseif ($data->kodepajak=='09')	
			$arr_pajak_value[4] = $data->total;
		elseif ($data->kodepajak=='11')	
			$arr_pajak_value[5] = $data->total;
	}
	db_set_active();
	*/
	
	$form['menu2']['pajak'] = array(
		'#prefix' => '<div class="col-md-6">',
		'#type' => 'fieldset',
		//'#title'=>  _bootstrap_icon('unchecked') . ' REKAP PAJAK<em><small class="span4 text-info pull-right">(milyar)</small></em>',
		'#title'=>  _bootstrap_icon('unchecked') . ' REKAP PAJAK',
		'#collapsible' => FALSE,
		'#collapsed' => FALSE,		
		'#suffix' => '</div>',
	);	

	$form['menu2']['pajak']['isi']= array(
		'#prefix' => '<table style="width:100%">',
		 '#suffix' => '</table>',
	);
		$form['menu2']['pajak']['isi']['no11_h']= array(
			'#prefix' => '<tr><th style="width:5px; >',
			'#type'   => 'item', 
			'#markup' => '<p>No.</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['pajak']['isi']['uraian11_h']= array(
			'#prefix' => '<th>',
			'#type'   => 'item', 
			'#markup' => '<p>Uraian</p>', 
			'#suffix' => '</th>',
			
		);				
		$form['menu2']['pajak']['isi']['jumlah11_h']= array(
			'#prefix' => '<th style="width:35%; color:black">',
			'#type'         => 'item', 
			'#markup' => '<p align="right">Jumlah</p>', 
			'#suffix' => '</th></tr>',
		);	
		
		//I
		for ($i=0; $i<=5; $i++) {
			
			$form['menu2']['pajak']['isi']['row_no_tw' . $i]= array(
				'#prefix' => '<tr><td>',
				'#type'   => 'item', 
				'#markup' => $i+1, 
				'#suffix' => '</td>',
				
			);				
			$form['menu2']['pajak']['isi']['row_u_tw' . $i]= array(
				'#prefix' => '<td>',
				'#type'   => 'item', 
				'#markup' => $arr_pajak_uraian[$i], 
				'#suffix' => '</td>',
				
			);				
			$form['menu2']['pajak']['isi']['row_r_tw' . $i]= array(
				'#prefix' => '<td>',
				'#type'         => 'item', 
				'#markup' => '<p align="right">' . apbd_fn($arr_pajak_value[$i]) . '</p>', 
				'#suffix' => '</td></tr>',
			);	
		}		
		//TOTAL
		$form['menu2']['pajak']['isi']['row_no_tw_t']= array(
			'#prefix' => '<tr><td>',
			'#type'   => 'item', 
			'#markup' => '', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['pajak']['isi']['row_u_tw_t']= array(
			'#prefix' => '<td>',
			'#type'   => 'item', 
			'#markup' => '<strong>TOTAL</strong>', 
			'#suffix' => '</td>',
			
		);				
		$form['menu2']['pajak']['isi']['row_r_tw_t']= array(
			'#prefix' => '<td>',
			'#type'         => 'item', 
			'#markup' => '<p align="right"><strong>' . apbd_fn($total) . '</strong></p>', 
			'#suffix' => '</td></tr>',
		);			
	return $form;

}

function read_sumberdana($sumberdana, &$anggaran, &$realiasasi) {

$agg = 0; $rea = 0;
db_set_active('penatausahaan');
$results = db_query('select sum(total) as anggaran from {kegiatanskpd} where inaktif=0 and sumberdana1=:sumberdana', array(':sumberdana'=>$sumberdana));
foreach ($results as $data) {
	$agg = $data->anggaran;
}

$results = db_query('select sum(d.jumlah) as realisasi from {dokumen} as d inner join {kegiatanskpd} as k on d.kodekeg=k.kodekeg where d.sp2dok=1 and k.sumberdana1=:sumberdana', array(':sumberdana'=>$sumberdana));
foreach ($results as $data) {
	$rea = $data->realisasi;
}
$anggaran = $agg;
$realiasasi = $rea;
db_set_active();

return true;
	
}

?>


