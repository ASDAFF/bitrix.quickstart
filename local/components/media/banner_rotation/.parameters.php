<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (IsModuleInstalled("advertising")) {

	if (!CModule::IncludeModule("advertising"))
		return;

	$res = CAdvType::GetList(($by='s_sid'), ($order='asc'), Array("ACTIVE" => "Y"), $is_filtered, "Y");
	while (is_object($res) && $ar = $res->GetNext())
	{
		$arTypeFields[$ar["SID"]] = "[".$ar["SID"]."] ".$ar["NAME"];
	}
	$arDataSources['advert'] = GetMessage("BEONO_BANNER_SOURCE_MODULE");
} else {
	$arCurrentValues["SOURCE"] = 'medialib';
}

$arDataSources['medialib'] = GetMessage("BEONO_BANNER_SOURCE_MEDIALIB");

$arComponentParameters = array(
	"PARAMETERS" => array(
		"SOURCE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("BEONO_BANNER_SOURCE"),
			"TYPE" => "LIST",
			"VALUES" => $arDataSources,
			"REFRESH" => "Y",
		)
	)
);

if ($arCurrentValues["SOURCE"] != 'medialib' && IsModuleInstalled("advertising")) {
	$arComponentParameters['PARAMETERS'] = array_merge($arComponentParameters['PARAMETERS'], array(
			"TYPE" => Array(
				"NAME"=>GetMessage("BEONO_BANNER_TYPE"), 
				"PARENT" => "BASE",
				"TYPE"=>"LIST", 
				"DEFAULT" => "", 
				"VALUES"=>$arTypeFields, 
				"ADDITIONAL_VALUES"=>"N"
			),
			"NOINDEX" => array(
				"NAME" => GetMessage("BEONO_BANNER_NOINDEX"),
				"PARENT" => "BASE",
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",	
			),
			"CACHE_TIME" => Array("DEFAULT"=>"0"),
		)
	);
}


$arComponentParameters['PARAMETERS']["LIMIT"] = array(
	"NAME" => GetMessage("BEONO_BANNER_LIMIT"),
	"PARENT" => "BASE",
	"TYPE" => "STRING",
	"DEFAULT" => "5",	
	"REFRESH" => "Y",
);

if ($arCurrentValues["SOURCE"] == 'medialib') {

	if ($arCurrentValues['LIMIT'] < 1) {
		$arCurrentValues['LIMIT'] = 5;
	}
	
	for ($i=1; $i <= $arCurrentValues['LIMIT']; $i++) {
		$arComponentParameters['PARAMETERS']['BANNER_IMAGE_'.$i] = array(
				"NAME"=> GetMessage("BEONO_BANNER_IMAGE").' '.$i, 
				"PARENT" => "BASE",
				"TYPE" => "FILE",
				"FD_TARGET" => "F",
				"FD_EXT" => 'png,jpg,jpeg,gif,swf',
				"FD_UPLOAD" => true,
				"FD_USE_MEDIALIB" => true,
				"FD_MEDIALIB_TYPES" => Array(),		
				"DEFAULT" => "", 
				"VALUES"=> "",
		);
		$arComponentParameters['PARAMETERS']['BANNER_NAME_'.$i] = array(
				"NAME"=> GetMessage("BEONO_BANNER_NAME"), 
				"PARENT" => "BASE",
				"TYPE"=> "STRING", 
				"DEFAULT" => '',				
		);
		$arComponentParameters['PARAMETERS']['BANNER_HREF_'.$i] = array(
				"NAME"=> GetMessage("BEONO_BANNER_HREF"),  
				"PARENT" => "BASE",
				"TYPE"=>"STRING", 
				"DEFAULT" => "", 
				"VALUES"=> "",
		);
	}
}

?>