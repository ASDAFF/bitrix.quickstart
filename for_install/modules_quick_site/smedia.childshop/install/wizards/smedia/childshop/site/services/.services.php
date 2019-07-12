<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arServices=
Array(
	'main' => array(
		'NAME' => GetMessage('SERVICES_main_NAME'),
		'STAGES' => array(
			'0' => 'files.php',
			'1' => 'options.php',
			'2' => 'theme.php',
			'3' => 'template.php',
			'4' => 'userOptions.php',
			'5' => 'groups.php',
			'6' =>"geocode.php",//google map
		),
	),
	'iblock_collection' => array(
		'NAME' => GetMessage('SERVICES_iblock_collection_NAME'),
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			'types.php',
			'info.php',
			'igrushka.php',
			'igrushka_2.php',
			'igrushka_3.php',
			'igrushka_4.php',
			'igrushka_5.php',
			'igrushka_price.php',
			'igrushka_price_2.php',
			'igrushka_price_3.php',
			'igrushka_price_4.php',
			'igrushka_price_5.php',
			'main_banners.php',
		),
	),
	'fileman' => array(
		'NAME' => GetMessage('SERVICES_fileman_NAME'),
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			'0' => 'options.php',
		),
	),
	'sale' => array(
		'NAME' => '',
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			"prices.php",
			"locations.php",
			"options.php",  
			"personTypes.php",
			"delivery.php",
		),
	),
	'sitestore' => array(
		'NAME' => '',
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			'0' => 'options.php',
		),
	),
	'socialnetwork' => array(
		'NAME' => GetMessage('SERVICES_socialnetwork_NAME'),
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			'0' => 'options.php',
		),
	),
	'store' => array(
		'NAME' => '',
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			"options.php",
		),
	),
	'iblock' => array(
		'NAME' => GetMessage('SERVICES_iblock_NAME'),
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			'0' => 'options.php',
		),
	),
	'event_types' => array(
		'NAME' => GetMessage('SERVICES_event_types_NAME'),
		'INSTALL_ONLY' => 'Y',
	),
	'subscribe' => array(
		'NAME' => GetMessage('SERVICES_subscribe_NAME'),
		'INSTALL_ONLY' => 'Y',
		'STAGES' => array(
			'0' => 'index.php',
		),	
	),
	'forum' => array(
		'NAME' => GetMessage('SERVICES_forum_NAME'),
		'INSTALL_ONLY' => 'Y',
	),
);

?>