<?
	IncludeModuleLangFile(__FILE__);
	IncludeModuleLangFile(dirname(__FILE__)."/admin/status.php");	
	$arOptionDefaults = Array(
		"FIO"=>Array(
			"TYPE"=>"USER",
			"VALUE"=>"USER_FIO"
		),
		"PHONE"=>Array(
			"TYPE"=>"USER",
			"VALUE"=>"PERSONAL_MOBILE"
		),
		"EMAIL"=>Array(
			"TYPE"=>"USER",
			"VALUE"=>"EMAIL"
		)
	);	
	
	$arCancelReason = Array(
		"client_decline"=>GetMessage("TCS_client_decline"),
		"shop_decline"=>GetMessage("TCS_shop_decline"), 
		"no_product"=>GetMessage("TCS_no_product"), 
		"error_offer"=>GetMessage("TCS_error_offer"), 
		"no_client"=>GetMessage("TCS_no_client"), 
		"document_error"=>GetMessage("TCS_document_error"), 
	);
	$arHosts = Array(
		"test"=>Array(
			"SRC"=>"https://kupivkredit-test-fe.tcsbank.ru:8100",
			"API"=>"https://kupivkredit-test-api.tcsbank.ru:8100",
			"NAME"=>GetMessage("TCS_TEST_SERVER_NAME")
		),	
		"main"=>Array(
			"SRC"=>"https://www.kupivkredit.ru",
			"API"=>"https://api.kupivkredit.ru",
			"NAME"=>GetMessage("TCS_MAIN_SERVER_NAME")
		),
		"another"=>Array(
			"NAME"=>GetMessage("TCS_ANOTHER_SERVER_NAME")
		)
	);
	
	$arCourierModes = Array(
		"both"=>Array(
			"NAME"=>GetMessage("TCS_BOTH"),
			"MODE"=>Array("partner","bank")
		),	
		"only_partner"=>Array(
			"NAME"=>GetMessage("TCS_ONLY_PARTNER"),
			"MODE"=>Array("partner")
		),
		"only_bank"=>Array(
			"NAME"=>GetMessage("TCS_ONLY_BANK"),
			"MODE"=>Array("bank")
		)
	);
	
	$arTCSBankStatuses = Array(
		1=>Array(
			"NAME"=>GetMessage("TCS_bank_status_new"),
			"STATUS"=>Array("new","hol","ver")
		),
		2=>Array(
			"NAME"=>GetMessage("TCS_bank_status_ovr"),
			"STATUS"=>Array("ovr","can","agr")
		),
		3=>Array(
			"NAME"=>GetMessage("TCS_bank_status_prr"),
			"STATUS"=>Array("prr","fap","pvr","app")
		),
		4=>Array(
			"NAME"=>GetMessage("TCS_bank_status_rej"),
			"STATUS"=>Array("rej")
		)
	);
	
	$arTCSOrderStatuses = Array(
		1=>Array(
			"NAME"=>GetMessage("TCS_order_status_new"),
			"STATUS"=>Array("new")
		),
		2=>Array(
			"NAME"=>GetMessage("TCS_order_status_rej"),
			"STATUS"=>Array("rej")
		),
		3=>Array(
			"NAME"=>GetMessage("TCS_order_status_hol"),
			"STATUS"=>Array("hol")
		),
		4=>Array(
			"NAME"=>GetMessage("TCS_order_status_ver"),
			"STATUS"=>Array("ver")
		),
		5=>Array(
			"NAME"=>GetMessage("TCS_order_status_prr"),
			"STATUS"=>Array("prr")
		),
		6=>Array(
			"NAME"=>GetMessage("TCS_order_status_fap"),
			"STATUS"=>Array("fap")
		),
		7=>Array(
			"NAME"=>GetMessage("TCS_order_status_pvr"),
			"STATUS"=>Array("pvr")
		),
		8=>Array(
			"NAME"=>GetMessage("TCS_order_status_app"),
			"STATUS"=>Array("app")
		),
		9=>Array(
			"NAME"=>GetMessage("TCS_order_status_ovr"),
			"STATUS"=>Array("ovr")
		),
		10=>Array(
			"NAME"=>GetMessage("TCS_order_status_can"),
			"STATUS"=>Array("can")
		),
		11=>Array(
			"NAME"=>GetMessage("TCS_order_status_agr"),
			"STATUS"=>Array("agr")
		),

	);
	
	$arButtonTypes = Array(
		1,2,3,4,5,6,7,8,9,10
	);
	$arInsButtonTypes = Array(
		1,2,3
	);
	$arInsButtonRounds = Array(
		1,2,3
	);
	
	$arRoundMethods = Array(
		"round"=>GetMessage("TCS_round_round"),
		"ceil"=>GetMessage("TCS_round_ceil"),
		"floor"=>GetMessage("TCS_round_floor")
	);
	
$arTCSMessage = array(
	'1010' => array(
		'ENG' => 'The order "(.*)" not found',
		'RUS' => GetMessage("TCS_ERROR_1010")
	),
	'1020' => array(
		'ENG' => 'The order "(.*)" already exists',
		'RUS' => GetMessage("TCS_ERROR_1020")
	),
	'1030' => array(
		'ENG' => '(.*) RUR is the minimum allowed order amount',
		'RUS' => GetMessage("TCS_ERROR_1030")
	),
	'1031' => array(
		'ENG' => 'The limit initial payment (.*) RUR has been exceeded',
		'RUS' => GetMessage("TCS_ERROR_1031")
	),
	'1032' => array(
		'ENG' => 'The desired credit period mast be greate than (.*) and lower than (.*)',
		'RUS' => GetMessage("TCS_ERROR_1032")
	),
	'1040' => array(
		'ENG' => 'Contract retrieval is not available for your store',
		'RUS' => GetMessage("TCS_ERROR_1040")
	),
	'1050' => array(
		'ENG' => 'Number of goods of the order must be less than (\d+)',
		'RUS' => GetMessage("TCS_ERROR_1050")
	),
	'1060' => array(
		'ENG' => 'Product Expiration Date must be greater than (.*)',
		'RUS' => GetMessage("TCS_ERROR_1060")
	),
	'1070' => array(
		'ENG' => 'Invalid form data',
		'RUS' => GetMessage("TCS_ERROR_1070")
	),
	'1080' => array(
		'ENG' => 'Malformed XML',
		'RUS' => GetMessage("TCS_ERROR_1080")
	),
	'2010' => array(
		'ENG' => 'Cannot cancel the order "(.*)" in status "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2010")
	),
	'2011' => array(
		'ENG' => 'Cannot create document for the order "(.*)" in status "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2011")
	),
	'2014' => array(
		'ENG' => 'The order "(.*)" return amount cannot be larger "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2014")
	),
	'2015' => array(
		'ENG' => 'Unavailable signing type "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2015")
	),
	'2016' => array(
		'ENG' => 'Cannot confirm the order "(.*)" with signing type "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2016")
	),
	'2017' => array(
		'ENG' => 'Your store is not allowed to perform signing',
		'RUS' => GetMessage("TCS_ERROR_2017")
	),
	'2018' => array(
		'ENG' => 'Cannot complete the order "(.*)" by seller with signing type confirmed "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2018")
	),
	'2020' => array(
		'ENG' => 'Cannot change the order "(.*)" since is already confirmed',
		'RUS' => GetMessage("TCS_ERROR_2020")
	),
	'2030' => array(
		'ENG' => 'Cannot change the order "(.*)" in status "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2030")
	),
	'2040' => array(
		'ENG' => 'Cannot complete an order "(.*)" that hasn\'t been confirmed yet',
		'RUS' => GetMessage("TCS_ERROR_2040")
	),
	'2050' => array(
		'ENG' => 'Cannot complete the order "(.*)" in status "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2050")
	),
	'2060' => array(
		'ENG' => 'Cannot confirm the order confirmed "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2060")
	),
	'2070' => array(
		'ENG' => 'Cannot confirm the order "(.*)" in status "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2070")
	),
	'2080' => array(
		'ENG' => 'Cannot return the order "(.*)" in status "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_2080")
	),
	'2090' => array(
		'ENG' => 'Cannot create document for the order "(.*)" not confirmed yet',
		'RUS' => GetMessage("TCS_ERROR_2090")
	),
	'3010' => array(
		'ENG' => 'The order "(.*)" is being processed',
		'RUS' => GetMessage("TCS_ERROR_3010")
	),
	'3020' => array(
		'ENG' => 'The order "(.*)" locked until "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_3020")
	),
	'3040' => array(
		'ENG' => "The operation \"(.*)\" temporally locked",
		'RUS' => GetMessage("TCS_ERROR_3040")
	),
	'3050' => array(
		'ENG' => 'The application "(.*)" temporally locked',
		'RUS' => GetMessage("TCS_ERROR_3050")
	),
	'3060' => array(
		'ENG' => 'Such login already exists "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_3060")
	),
	'4010' => array(
		'ENG' => 'Invalid Username or Password',
		'RUS' => GetMessage("TCS_ERROR_4010")
	),
	'4020' => array(
		'ENG' => 'Partner is not available',
		'RUS' => GetMessage("TCS_ERROR_4020")
	),
	'4030' => array(
		'ENG' => 'The user "(.*)" is blocked from "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_4030")
	),
	'4040' => array(
		'ENG' => 'Invalid authentication',
		'RUS' => GetMessage("TCS_ERROR_4040")
	),
	'4050' => array(
		'ENG' => 'Access denied',
		'RUS' => GetMessage("TCS_ERROR_4050")
	),
	'8010' => array(
		'ENG' => 'There was an error while processing the order "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_8010")
	),
	'9010' => array(
		'ENG' => 'This call is blocked',
		'RUS' => GetMessage("TCS_ERROR_9010")
	),
	'9020' => array(
		'ENG' => 'Access denied from your ip address "(.*)"',
		'RUS' => GetMessage("TCS_ERROR_9020")
	)
 );
	
?>