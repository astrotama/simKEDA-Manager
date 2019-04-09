<?php
function Penatausahaan_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    //drupal_set_html_head($h);
	//drupal_add_css('apbd.css');
	//drupal_add_css('files/css/tablenew.css');
	//drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 10;
    
	if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
				$ntitle = 'Kegiatan';
				$nntitle ='';
				$tahun = arg(2);
				
				$kodeuk = arg(3);
				drupal_set_title($ntitle);
				break;
				
			case 'excel':
				break;

			default:
				//drupal_access_denied();
				break;
		}
		
	} else {
		$tahun = 2015;		//variable_get('apbdtahun', 0);
		$kodeuk = '##';
	}
	
	//drupal_set_message($tahun);
	//drupal_set_message($kodeuk);
	
	$output_form = drupal_get_form('Penatausahaan_main_form');
	$header = array (
		array('data' => 'No','width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD','field'=> 'namasingkat', 'valign'=>'top'),
		array('data' => 'Kegiatan','field'=> 'kegiatan', 'valign'=>'top'),
		array('data' => 'Sumberdana', 'valign'=>'top'),
		array('data' => 'Sasaran/Target', 'field'=> 'sasaran', 'valign'=>'top'),
		array('data' => 'Anggaran', 'width' => '80px', 'field'=> 'anggaran2', 'valign'=>'top'),
		array('data' => 'Realisasi', 'width' => '80px', 'field'=> 'realisasi', 'valign'=>'top'),
		array('data' => 'Prsn', 'width' => '10px', 'valign'=>'top'),
		array('data' => '', 'width' => '20px', 'valign'=>'top'),
	);
	
	/*
	$query = db_select('kegiatan' . $tahun, 'p')->extend('PagerDefault')->extend('TableSort');
	if ($kodeuk !='##'){
		$field='kodeuk';
		$value= $kodeuk;
	}
	else {
		$field='1';
		$value='1';
	};

	# get the desired fields from the database
	  
	$query->fields('p', array('kegiatan', 'kodeuk','sumberdana', 'sasaran', 'target', 'anggaran1', 'anggaran2','realisasi'))
		//
		->condition($field, $value, '=')
		->orderByHeader($header)
		->orderBy('kegiatan', 'ASC')
		->limit($limit);
	*/
	
	$query = db_select('kegiatan' . $tahun, 'k')->extend('PagerDefault')->extend('TableSort');
	$query->innerJoin('unitkerja' . $tahun, 'u', 'k.kodeuk=u.kodeuk');
	if ($kodeuk !='##'){
		$field='k.kodeuk';
		$value= $kodeuk;
	}
	else {
		$field='1';
		$value='1';
	};

	# get the desired fields from the database
	  
	$query->fields('k', array('kegiatan', 'kodeuk','kodekeg','sumberdana', 'sasaran', 'target', 'anggaran1', 'anggaran2','realisasi'));
	$query->fields('u', array('namasingkat'));
	$query->condition($field, $value, '=');
	$query->orderByHeader($header);
	$query->orderBy('k.kegiatan', 'ASC');
	$query->limit($limit);
		
	# execute the query
	$results = $query->execute();
		
	# build the table fields
	$no=0;

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$no = $page * $limit;
	} else {
		$no = 0;
	} 

	
		
	$rows = array();
	foreach ($results as $data) {
		$no++;  
		$editlink = l('Rekening', 'Penatausahaan/rekening/'.$tahun.'/'.$data->kodekeg, array ('html' => true, 'attributes'=> array ('class'=>'btn btn-success btn-xs btn-block')));
		$editlink .= l('Register', '', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-warning btn-xs btn-block')));
		$editlink .= l('Gambar', '', array ('html' => true, 'attributes'=> array ('class'=>'btn btn-danger btn-xs btn-block')));
		//<font color="red">This is some text!</font>
		$anggaran = apbd_fn($data->anggaran2);
		
		if ($data->anggaran1 > $data->anggaran2)
			$anggaran .= '<p><font color="red">' . apbd_fn($data->anggaran1) . '</font></p>';
		else if ($data->anggaran1 < $data->anggaran2)
			$anggaran .= '<p><font color="green">' . apbd_fn($data->anggaran1) . '</font></p>';
		
		$rows[] = array(
						array('data' => $no, 'width' => '10px', 'align' => 'right', 'valign'=>'top'),
						array('data' => $data->namasingkat, 'width' => '20px', 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->kegiatan, 'align' => 'left', 'valign'=>'top'),
						array('data' => $data->sumberdana, 'width' => '120px', 'align' => 'center', 'valign'=>'top'),
						array('data' => $data->target . '<p><i><font color="orange">' . $data->sasaran . '</font></i></p>', 'width' => '230px', 'align' => 'left', 'valign'=>'top'),
						array('data' => $anggaran, 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn($data->realisasi), 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						array('data' => apbd_fn1(apbd_hitungpersen($data->anggaran2, $data->realisasi)), 'width' => '120px', 'align' => 'right', 'valign'=>'top'),
						//"<a href=\'?q=jurnal/edit/'>" . 'Register' . '</a>',
						$editlink,
					);
	}

	//$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;')));
    //$btn .= "&nbsp;" . l("Cari", '' , array ('html' => true, 'attributes'=> array ('class'=>'btn', 'style'=>'color:white;'))) ;
	$btn = l('Cetak', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	$btn .= "&nbsp;" . l('Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn btn-primary')));
	
	$output = theme('table', array('header' => $header, 'rows' => $rows ));
	$output .= theme('pager');
	return drupal_render($output_form) . $btn . $output . $btn;
}


function Penatausahaan_main_form_submit($form, &$form_state) {
	$tahun= $form_state['values']['tahun'];
	$skpd = $form_state['values']['skpd'];
	
	//drupal_set_message($row[2014][1]); 
	$kodeuk = '##';
	$query = db_select('unitkerja'.$tahun, 'p');
	$query->fields('p', array('namasingkat','kodeuk'))
		  ->condition('namasingkat',$skpd,'=');
	$results = $query->execute();
	if($results){
		foreach($results as $data) {
			$kodeuk = $data->kodeuk;
		}
	}
	$uri = 'Penatausahaan/filter/' . $tahun.'/'.$kodeuk;
	drupal_goto($uri);
	
}


function Penatausahaan_main_form($form, &$form_state) {
	
	$kodeuk = '##';
	$namasingkat = 'SELURUH SKPD';
	if(arg(2)!=null){
		
		$tahun = arg(2);
		
		$kodeuk = arg(3);
		$query = db_select('unitkerja'.arg(2), 'p');
		$query->fields('p', array('namasingkat','kodeuk'))
			  ->condition('kodeuk',arg(3),'=');
		$results = $query->execute();
		if($results){
			foreach($results as $data) {
				$namasingkat=$data->namasingkat;
			}
		}
			 
	}
	else{
		$tahun = 2015;
		$namasingkat='';
		
	}
	
	
	// Get the list of options to populate the first dropdown.
	$option_tahun = _ajax_get_tahun_dropdown();
	// If we have a value for the first dropdown from $form_state['values'] we use
	// this both as the default value for the first dropdown and also as a
	// parameter to pass to the function that retrieves the options for the
	// second dropdown.
  
	$selected_tahun = isset($form_state['values']['tahun']) ? $form_state['values']['tahun'] : $tahun;
	
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);	
	$form['formdata']['tahun'] = array(
		'#type' => 'select',
		'#title' => 'Tahun',
		'#options' => $option_tahun,
		'#default_value' => $tahun,		//$selected,
		// Bind an ajax callback to the change event (which is the default for the
		// select form type) of the first dropdown. It will replace the second
		// dropdown when rebuilt.
		'#ajax' => array(
		  // When 'event' occurs, Drupal will perform an ajax request in the
		  // background. Usually the default value is sufficient (eg. change for
		  // select elements), but valid values include any jQuery event,
		  // most notably 'mousedown', 'blur', and 'submit'.
		  // 'event' => 'change',
			'callback' => 'Penatausahaan_main_form_callback',
			'wrapper' => 'skpd-replace',
		),
	);

	$form['formdata']['skpd'] = array(
		'#type' => 'select',
		'#title' =>  t('SKPD'),
		// The entire enclosing div created here gets replaced when dropdown_first
		// is changed.
		'#prefix' => '<div id="skpd-replace">',
		'#suffix' => '</div>',
		// When the form is rebuilt during ajax processing, the $selected variable
		// will now have the new value and so the options will change.
		'#options' => _ajax_get_skpd_dropdown($selected_tahun),
		//'#default_value' => isset($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $kodeuk,
		'#default_value' => $namasingkat,
	);
	
	$form['formdata']['submit'] = array(
		'#type' => 'submit',
		'#value' => t('Tampilkan'),
	);
	return $form;
}

/**
 * Selects just the second dropdown to be returned for re-rendering.
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return array
 *   Renderable array (the second dropdown)
 */
function Penatausahaan_main_form_callback($form, $form_state) {
  return $form['formdata']['skpd'];
}

/**
 * Helper function to populate the first dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @return array
 *   Dropdown options.
 */
function _ajax_get_tahun_dropdown() {
  // drupal_map_assoc() just makes an array('String' => 'String'...).
  return drupal_map_assoc(
    array(
	  t('2015'),
	  t('2014'),
	  t('2013'),
	  t('2012'),
      t('2011'),
      t('2010'),
      t('2009'),
      t('2008'),
    )
  );
}

/**
 * Helper function to populate the second dropdown.
 *
 * This would normally be pulling data from the database.
 *
 * @param string $key
 *   This will determine which set of options is returned.
 *
 * @return array
 *   Dropdown options
 */
function _ajax_get_skpd_dropdown($key = '') {
	$row = array();
	for($n=2015;$n>=2008;$n--){
		$query = db_select('unitkerja'.$n, 'p');

		# get the desired fields from the database
		$query->fields('p', array('namasingkat','kodeuk','kodedinas'))
				->orderBy('kodedinas', 'ASC');

		# execute the query
		$results = $query->execute();
		
			
		# build the table fields
		$row[$n]['##'] = 'SELURUH SKPD'; 
		if($results){
			foreach($results as $data) {
			  $row[$n][$data->kodeuk] = $data->namasingkat; 
			}
		}
	}
	
	$options = array(
		t('2008') => drupal_map_assoc(
			$row[2008]
		),
		t('2009') => drupal_map_assoc(
			$row[2009]
		),
		t('2010') => drupal_map_assoc(
			$row[2010]
		),
		t('2011') => drupal_map_assoc(
			$row[2011]
		),
		t('2012') => drupal_map_assoc(
			$row[2012]
		),
		t('2013') => drupal_map_assoc(
			$row[2013]
		),
		t('2014') => drupal_map_assoc(
			$row[2014]
		),
		t('2015') => drupal_map_assoc(
			$row[2015]
		),
	);
	
	if (isset($options[$key])) {
		return $options[$key];
	} else {
		return array();
	}
}


?>
