<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arPropertySocial = array(
		'VK'=>GetMessage("VK"),
		'FB'=>GetMessage("FB"),
		'OK'=>GetMessage("OK"),
		'TW'=>GetMessage("TW"),
		'GP'=>GetMessage("GP"),
		'MM'=>GetMessage("MM")
);
$arPropertyOther = array(
		'HH'=>GetMessage("HH"),
		'BX'=>GetMessage("BX"),
		'GH'=>GetMessage("GH"),
		'IG'=>GetMessage("IG"),
		'YT'=>GetMessage("YT")
);

$arComponentParameters = array(
	"GROUPS" => array(
		"SOCIAL" => array(
			"NAME" => GetMessage("SOCIAL"),
			"SORT" => 100,
		),
		"VK" => array(
			"NAME" => GetMessage("VK"),
			"SORT" => 310,
		),
		"FB" => array(
			"NAME" => GetMessage("FB"),
			"SORT" => 311,
		),
		"OK" => array(
			"NAME" => GetMessage("OK"),
			"SORT" => 312,
		),
		"TW" => array(
			"NAME" => GetMessage("TW"),
			"SORT" => 313,
		),
		"GP" => array(
			"NAME" => GetMessage("GP"),
			"SORT" => 314,
		),
		"MM" => array(
			"NAME" => GetMessage("MM"),
			"SORT" => 315,
		),
		"HH" => array(
			"NAME" => GetMessage("HH"),
			"SORT" => 350,
		),
		"BX" => array(
			"NAME" => GetMessage("BX"),
			"SORT" => 351,
		),
		"GH" => array(
			"NAME" => GetMessage("GH"),
			"SORT" => 352,
		),
		"IG" => array(
			"NAME" => GetMessage("IG"),
			"SORT" => 353,
		),
		"YT" => array(
			"NAME" => GetMessage("YT"),
			"SORT" => 354,
		),
	),
	"PARAMETERS" => array(
		"SOCIAL" => Array(
			"PARENT" => "SOCIAL",
			"NAME" => GetMessage("SOCIAL"),
			"TYPE" => "LIST",
			"VALUES" => $arPropertySocial,
			"MULTIPLE" => "Y",
			"REFRESH" => "Y",
		),
		"OTHER" => Array(
			"PARENT" => "SOCIAL",
			"NAME" => GetMessage("OTHER"),
			"TYPE" => "LIST",
			"VALUES" => $arPropertyOther,
			"MULTIPLE" => "Y",
			"REFRESH" => "Y",
		),
		"SIZE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SIZE"),
			"TYPE" => "STRING",
			"DEFAULT" => '55',
		),
		"POSITION" => array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("POSITION"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
		),
		"DARK" => array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("BACKGROUND"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
		),
		"JQUERY" => array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("JQUERY"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
		),
		"JQUERY_UI" => array(
				"PARENT" => "VISUAL",
				"NAME" => GetMessage("JQUERY_UI"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>36000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "D",
		),
		
	),
);

if($arCurrentValues['SOCIAL']){
	
if(in_array('VK', $arCurrentValues['SOCIAL'])){
	$arComponentParameters['PARAMETERS']['VK_GROUPS'] = array(
			"PARENT" => "VK",
			'NAME' => GetMessage("ID_GROUPS"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['VK_Y'] = array(
			"PARENT" => "VK",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['VK_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['VK_ICONS'] = array(
				"PARENT" => "VK",
				'NAME' => GetMessage("ICONS"),
				"TYPE" => "FILE",  "FD_TARGET" => "F", 
   				"FD_EXT" => "png,gif,jpg,jpeg", 
   				"FD_UPLOAD" => true, 
   				"FD_MEDIALIB_TYPES" => Array('image'), 
   				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['VK_ICONS_HOVER'] = array(
				"PARENT" => "VK",
				'NAME' => GetMessage("ICONS_HOVER"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}	
}
if(in_array('FB', $arCurrentValues['SOCIAL'])){
	$arComponentParameters['PARAMETERS']['FB_GROUPS'] = array(
			"PARENT" => "FB",
			'NAME' => GetMessage("ID_GROUPS"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['FB_Y'] = array(
			"PARENT" => "FB",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['FB_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['FB_ICONS'] = array(
				"PARENT" => "FB",
				'NAME' => GetMessage("ICONS"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['FB_ICONS_HOVER'] = array(
				"PARENT" => "FB",
				'NAME' => GetMessage("ICONS_HOVER"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
} 
if(in_array('OK', $arCurrentValues['SOCIAL'])){
	$arComponentParameters['PARAMETERS']['OK_GROUPS'] = array(
			"PARENT" => "OK",
			'NAME' => GetMessage("ID_GROUPS"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['OK_Y'] = array(
			"PARENT" => "OK",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['OK_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['OK_ICONS'] = array(
				"PARENT" => "OK",
				'NAME' => GetMessage("ICONS"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['OK_ICONS_HOVER'] = array(
				"PARENT" => "OK",
				'NAME' => GetMessage("ICONS_HOVER"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}
if(in_array('TW', $arCurrentValues['SOCIAL'])){
	$arComponentParameters['PARAMETERS']['TW_GROUPS'] = array(
			"PARENT" => "TW",
			'NAME' => GetMessage("ID_GROUPS"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['TW_Y'] = array(
			"PARENT" => "TW",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['TW_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['TW_ICONS'] = array(
				"PARENT" => "TW",
				'NAME' => GetMessage("ICONS"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['TW_ICONS_HOVER'] = array(
				"PARENT" => "TW",
				'NAME' => GetMessage("ICONS_HOVER"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}
if(in_array('GP', $arCurrentValues['SOCIAL'])){
	$arComponentParameters['PARAMETERS']['GP_GROUPS'] = array(
			"PARENT" => "GP",
			'NAME' => GetMessage("ID_GROUPS"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['GP_Y'] = array(
			"PARENT" => "GP",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['GP_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['GP_ICONS'] = array(
				"PARENT" => "GP",
				'NAME' => GetMessage("ICONS"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['GP_ICONS_HOVER'] = array(
				"PARENT" => "GP",
				'NAME' => GetMessage("ICONS_HOVER"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}
if(in_array('MM', $arCurrentValues['SOCIAL'])){
	$arComponentParameters['PARAMETERS']['MM_GROUPS'] = array(
			"PARENT" => "MM",
			'NAME' => GetMessage("ID_GROUPS"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['MM_Y'] = array(
			"PARENT" => "MM",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['MM_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['MM_ICONS'] = array(
				"PARENT" => "MM",
				'NAME' => GetMessage("ICONS"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['MM_ICONS_HOVER'] = array(
				"PARENT" => "MM",
				'NAME' => GetMessage("ICONS_HOVER"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}

 }
 
 if($arCurrentValues['OTHER']){
 	
 if(in_array('HH', $arCurrentValues['OTHER'])){
 	$arComponentParameters['PARAMETERS']['HH_GROUPS'] = array(
 			"PARENT" => "HH",
 			'NAME' => GetMessage("ID_BLOGS"),
 			'TYPE' => 'STRING',
 			"DEFAULT" => '',
 	);
 	$arComponentParameters['PARAMETERS']['HH_Y'] = array(
 			"PARENT" => "HH",
 			"NAME" => GetMessage("Y_ICONS"),
 			"TYPE" => "CHECKBOX",
 			"DEFAULT" => "N",
 			"REFRESH" => "Y",
 	);
 	if($arCurrentValues['HH_Y']=='Y'){
 		$arComponentParameters['PARAMETERS']['HH_ICONS'] = array(
 				"PARENT" => "HH",
 				'NAME' => GetMessage("ICONS"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 		$arComponentParameters['PARAMETERS']['HH_ICONS_HOVER'] = array(
 				"PARENT" => "HH",
 				'NAME' => GetMessage("ICONS_HOVER"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 	}
 }
 
 if(in_array('BX', $arCurrentValues['OTHER'])){
 	$arComponentParameters['PARAMETERS']['BX_GROUPS'] = array(
 			"PARENT" => "BX",
 			'NAME' => GetMessage("ID_PARTNER"),
 			'TYPE' => 'STRING',
 			"DEFAULT" => '',
 	);
 	$arComponentParameters['PARAMETERS']['BX_Y'] = array(
 			"PARENT" => "BX",
 			"NAME" => GetMessage("Y_ICONS"),
 			"TYPE" => "CHECKBOX",
 			"DEFAULT" => "N",
 			"REFRESH" => "Y",
 	);
 	if($arCurrentValues['BX_Y']=='Y'){
 		$arComponentParameters['PARAMETERS']['BX_ICONS'] = array(
 				"PARENT" => "BX",
 				'NAME' => GetMessage("ICONS"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 		$arComponentParameters['PARAMETERS']['BX_ICONS_HOVER'] = array(
 				"PARENT" => "BX",
 				'NAME' => GetMessage("ICONS_HOVER"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 	}
 }
 
 if(in_array('GH', $arCurrentValues['OTHER'])){
 	$arComponentParameters['PARAMETERS']['GH_GROUPS'] = array(
 			"PARENT" => "GH",
 			'NAME' => GetMessage("NICKNAME"),
 			'TYPE' => 'STRING',
 			"DEFAULT" => '',
 	);
 	$arComponentParameters['PARAMETERS']['GH_Y'] = array(
 			"PARENT" => "GH",
 			"NAME" => GetMessage("Y_ICONS"),
 			"TYPE" => "CHECKBOX",
 			"DEFAULT" => "N",
 			"REFRESH" => "Y",
 	);
 	if($arCurrentValues['GH_Y']=='Y'){
 		$arComponentParameters['PARAMETERS']['GH_ICONS'] = array(
 				"PARENT" => "GH",
 				'NAME' => GetMessage("ICONS"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 		$arComponentParameters['PARAMETERS']['GH_ICONS_HOVER'] = array(
 				"PARENT" => "GH",
 				'NAME' => GetMessage("ICONS_HOVER"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 	}
 }
 
 if(in_array('IG', $arCurrentValues['OTHER'])){
 	$arComponentParameters['PARAMETERS']['IG_GROUPS'] = array(
 			"PARENT" => "IG",
 			'NAME' => GetMessage("NICKNAME"),
 			'TYPE' => 'STRING',
 			"DEFAULT" => '',
 	);
 	$arComponentParameters['PARAMETERS']['IG_Y'] = array(
 			"PARENT" => "IG",
 			"NAME" => GetMessage("Y_ICONS"),
 			"TYPE" => "CHECKBOX",
 			"DEFAULT" => "N",
 			"REFRESH" => "Y",
 	);
 	if($arCurrentValues['IG_Y']=='Y'){
 		$arComponentParameters['PARAMETERS']['IG_ICONS'] = array(
 				"PARENT" => "IG",
 				'NAME' => GetMessage("ICONS"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 		$arComponentParameters['PARAMETERS']['IG_ICONS_HOVER'] = array(
 				"PARENT" => "IG",
 				'NAME' => GetMessage("ICONS_HOVER"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 	}
 }
 
 if(in_array('YT', $arCurrentValues['OTHER'])){
 	$arComponentParameters['PARAMETERS']['YT_GROUPS'] = array(
 			"PARENT" => "YT",
 			'NAME' => GetMessage("CHANEL"),
 			'TYPE' => 'STRING',
 			"DEFAULT" => '',
 	);
 	$arComponentParameters['PARAMETERS']['YT_Y'] = array(
 			"PARENT" => "YT",
 			"NAME" => GetMessage("Y_ICONS"),
 			"TYPE" => "CHECKBOX",
 			"DEFAULT" => "N",
 			"REFRESH" => "Y",
 	);
 	if($arCurrentValues['YT_Y']=='Y'){
 		$arComponentParameters['PARAMETERS']['YT_ICONS'] = array(
 				"PARENT" => "YT",
 				'NAME' => GetMessage("ICONS"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 		$arComponentParameters['PARAMETERS']['YT_ICONS_HOVER'] = array(
 				"PARENT" => "YT",
 				'NAME' => GetMessage("ICONS_HOVER"),
 				"TYPE" => "FILE",  "FD_TARGET" => "F",
 				"FD_EXT" => "png,gif,jpg,jpeg",
 				"FD_UPLOAD" => true,
 				"FD_MEDIALIB_TYPES" => Array('image'),
 				"DEFAULT" => '',
 		);
 	}
 }
 
 }
?>
