<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arPropertyMessenger = array(
		'ICQ'=>GetMessage("ICQ"),
		'JABBER'=>GetMessage("JABBER"),
		'MRA'=>GetMessage("MRA"),
		'SKYPE'=>GetMessage("SKYPE"),
		'VK'=>GetMessage("VK")
);

$arComponentParameters = array(
	"GROUPS" => array(
		"MESSENGER" => array(
			"NAME" => GetMessage("MESSENGERS"),
			"SORT" => 100,
		),
		"ICQ" => array(
			"NAME" => GetMessage("ICQ"),
			"SORT" => 310,
		),
		"JABBER" => array(
			"NAME" => GetMessage("JABBER"),
			"SORT" => 311,
		),
		"MRA" => array(
			"NAME" => GetMessage("MRA"),
			"SORT" => 312,
		),
		"SKYPE" => array(
			"NAME" => GetMessage("SKYPE"),
			"SORT" => 313,
		),
		"VK" => array(
			"NAME" => GetMessage("VK"),
			"SORT" => 314,
		),
	),
	"PARAMETERS" => array(
		"MESSENGER" => Array(
			"PARENT" => "MESSENGER",
			"NAME" => GetMessage("MESSENGERS"),
			"TYPE" => "LIST",
			"VALUES" => $arPropertyMessenger,
			"MULTIPLE" => "Y",
			"REFRESH" => "Y",
		),
		"SIZE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SIZE"),
			"TYPE" => "STRING",
			"DEFAULT" => '45',
		),
		"POSITION" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("POSITION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"MYAJAX" => array(
			"PARENT" => "VISUAL",
			"NAME" => "AJAX",
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	),
);

if($arCurrentValues['MESSENGER']){
	
if(in_array('ICQ', $arCurrentValues['MESSENGER'])){
	$arComponentParameters['PARAMETERS']['UIN'] = array(
			"PARENT" => "ICQ",
			'NAME' => GetMessage("UIN"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['ICQ_Y'] = array(
			"PARENT" => "ICQ",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['ICQ_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['ICQ_ONLINE'] = array(
				"PARENT" => "ICQ",
				'NAME' => GetMessage("ONLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F", 
   				"FD_EXT" => "png,gif,jpg,jpeg", 
   				"FD_UPLOAD" => true, 
   				"FD_MEDIALIB_TYPES" => Array('image'), 
   				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['ICQ_OFFLINE'] = array(
				"PARENT" => "ICQ",
				'NAME' => GetMessage("OFFLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}	
}

if(in_array('JABBER', $arCurrentValues['MESSENGER'])){
	$arComponentParameters['PARAMETERS']['JID'] = array(
			"PARENT" => "JABBER",
			'NAME' => GetMessage("JID"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['JID_HASH'] = array(
			"PARENT" => "JABBER",
			'NAME' => GetMessage("JID_HASH"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['JABBER_Y'] = array(
			"PARENT" => "JABBER",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['JABBER_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['JABBER_ONLINE'] = array(
				"PARENT" => "JABBER",
				'NAME' => GetMessage("ONLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['JABBER_OFFLINE'] = array(
				"PARENT" => "JABBER",
				'NAME' => GetMessage("OFFLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}

if(in_array('MRA', $arCurrentValues['MESSENGER'])){
	$arComponentParameters['PARAMETERS']['MAIL'] = array(
			"PARENT" => "MRA",
			'NAME' => GetMessage("MAIL"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['MRA_Y'] = array(
			"PARENT" => "MRA",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['MRA_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['MRA_ONLINE'] = array(
				"PARENT" => "MRA",
				'NAME' => GetMessage("ONLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['MRA_OFFLINE'] = array(
				"PARENT" => "MRA",
				'NAME' => GetMessage("OFFLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}

if(in_array('SKYPE', $arCurrentValues['MESSENGER'])){
	$arComponentParameters['PARAMETERS']['NICK'] = array(
			"PARENT" => "SKYPE",
			'NAME' => GetMessage("NICK"),
			'TYPE' => 'STRING',
			"DEFAULT" => '',
	);
	$arComponentParameters['PARAMETERS']['SKYPE_Y'] = array(
			"PARENT" => "SKYPE",
			"NAME" => GetMessage("Y_ICONS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
	);
	if($arCurrentValues['SKYPE_Y']=='Y'){
		$arComponentParameters['PARAMETERS']['SKYPE_ONLINE'] = array(
				"PARENT" => "SKYPE",
				'NAME' => GetMessage("ONLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['SKYPE_OFFLINE'] = array(
				"PARENT" => "SKYPE",
				'NAME' => GetMessage("OFFLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
	}
}

if(in_array('VK', $arCurrentValues['MESSENGER'])){
	$arComponentParameters['PARAMETERS']['VK_ID'] = array(
			"PARENT" => "VK",
			'NAME' => GetMessage("VK_ID"),
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
		$arComponentParameters['PARAMETERS']['VK_ONLINE'] = array(
				"PARENT" => "VK",
				'NAME' => GetMessage("ONLINE"),
				"TYPE" => "FILE",  "FD_TARGET" => "F",
				"FD_EXT" => "png,gif,jpg,jpeg",
				"FD_UPLOAD" => true,
				"FD_MEDIALIB_TYPES" => Array('image'),
				"DEFAULT" => '',
		);
		$arComponentParameters['PARAMETERS']['VK_OFFLINE'] = array(
				"PARENT" => "VK",
				'NAME' => GetMessage("OFFLINE"),
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
