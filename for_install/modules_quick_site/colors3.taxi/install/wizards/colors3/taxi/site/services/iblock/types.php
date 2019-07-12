<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

WizardServices::IncludeServiceLang("types.php", $lang);	
$arTypes = Array(
	Array(
		"ID" => "jobs",
		"SECTIONS" => "N",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array('ru'=>Array(
						'NAME'=>GetMessage("COLORS3_TAXI_JOBS"),					
						'ELEMENT_NAME'=>GetMessage("COLORS3_TAXI_JOB")
						),
						'en'=>Array(
						'NAME'=>'jobs',					
						'ELEMENT_NAME'=>'job'
						)),
	),
	Array(
		"ID" => "orders",
		"SECTIONS" => "N",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array('ru'=>Array(
						'NAME'=>GetMessage("COLORS3_TAXI_ORDERS"),					
						'ELEMENT_NAME'=>GetMessage("COLORS3_TAXI_ORDER")
						),
						'en'=>Array(
						'NAME'=>'orders',					
						'ELEMENT_NAME'=>'order'
						)),
	),
	Array(
		"ID" => "services",
		"SECTIONS" => "N",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array('ru'=>Array(
						'NAME'=>GetMessage("COLORS3_TAXI_SERVICES"),					
						'ELEMENT_NAME'=>GetMessage("COLORS3_TAXI_SERVICE")
						),
						'en'=>Array(
						'NAME'=>'services',					
						'ELEMENT_NAME'=>'service'
						)),
	),
	Array(
		"ID" => "news",
		"SECTIONS" => "N",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array('ru'=>Array(
						'NAME'=>GetMessage("COLORS3_TAXI_NEWS"),					
						'ELEMENT_NAME'=>GetMessage("COLORS3_TAXI_NEW")
						),
						'en'=>Array(
						'NAME'=>'news',					
						'ELEMENT_NAME'=>'new'
						)),
	),
	Array(
		"ID" => "reviews",
		"SECTIONS" => "N",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array('ru'=>Array(
						'NAME'=>GetMessage("COLORS3_TAXI_OTZYVY"),						
						'ELEMENT_NAME'=>GetMessage("COLORS3_TAXI_OTZYV")
						),
						'en'=>Array(
						'NAME'=>'reviews',						
						'ELEMENT_NAME'=>'review'
						)),
	),
	Array(
		"ID" => "taxis",
		"SECTIONS" => "N",
		"IN_RSS" => "N",
		"SORT" => 300,
		"LANG" => Array('ru'=>Array(
						'NAME'=>GetMessage("COLORS3_TAXI_TAXIS"),					
						'ELEMENT_NAME'=>GetMessage("COLORS3_TAXI_TAXI")
						),
						'en'=>Array(
						'NAME'=>'taxis',					
						'ELEMENT_NAME'=>'taxi'
						)),
	),
);

$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

$iblockType = new CIBlockType;
foreach($arTypes as $arType)
{
	$dbType = CIBlockType::GetList(Array(),Array("ID" => $arType["ID"]));
	if($dbType->Fetch())
		continue;

	$iblockType->Add($arType);
}



CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/header.php', array("COLOR" => WIZARD_THEME_ID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/footer.php', array("COLOR" => WIZARD_THEME_ID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/css/style.css', array("COLOR" => WIZARD_THEME_ID));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/logo.php", array("COLOR" => WIZARD_THEME_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/logo_noindex.php", array("COLOR" => WIZARD_THEME_ID));

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/benefits.php", array("COLOR" => WIZARD_THEME_ID));
CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/components/bitrix/iblock.element.add.form/call/template.php', array("COLOR" => WIZARD_THEME_ID));

$dest = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php';
$init = $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'_'.WIZARD_THEME_ID.'/init/init.php';
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/init.php')) unlink($dest);
copy($init, $dest);

$dbconn_content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/dbconn.php');
$dbconn_content = str_replace('define("BX_CRONTAB_SUPPORT", true);', '', $dbconn_content);
file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/dbconn.php', $dbconn_content);



function InstallEvents()
{
	$arEventTypes = array();
	$langs = CLanguage::GetList(($b=""), ($o=""));
	while($language = $langs->Fetch())
	{
		$lid = $language["LID"];
		//$lid = WIZARD_SITE_ID;
		//IncludeModuleLangFile(__FILE__, $lid);

		$arEventTypes[] = array(
			"LID" => $lid,
			"EVENT_NAME" => "NEW_ORDER_TAXI",
			"NAME" => GetMessage("MAIN_ORDER_TAXI"),
			"DESCRIPTION" => '',
			"SORT" => 10
		);
		$arEventTypes[] = array(
			"LID" => $lid,
			"EVENT_NAME" => "ORDER_CALL",
			"NAME" => GetMessage("MAIN_ORDER_CALL"),
			"DESCRIPTION" => GetMessage("MAIN_ORDER_CALL_TYPE_DESC"),
			"SORT" => 20
		);
		$arEventTypes[] = array(
			"LID" => $lid,
			"EVENT_NAME" => "REVIEW_POSTED",
			"NAME" => GetMessage("MAIN_REVIEW_POSTED"),
			"DESCRIPTION" => '',
			"SORT" => 30
		);
		$arEventTypes[] = array(
			"LID" => $lid,
			"EVENT_NAME" => "NEW_DRIVER",
			"NAME" => GetMessage("MAIN_NEW_DRIVER"),
			"DESCRIPTION" => '',
			"SORT" => 30
		);	
	}

	$type = new CEventType;
	foreach ($arEventTypes as $arEventType)
		$type->Add($arEventType);

	//IncludeModuleLangFile(__FILE__);

	$arMessages = Array();
	$arMessages[] = Array(
			"EVENT_NAME" => "NEW_ORDER_TAXI",
			"LID" => WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",		
			"SUBJECT" => GetMessage("MAIN_ORDER_TAXI_SUBJECT"),
			"MESSAGE" => GetMessage("MAIN_ORDER_TAXI_MESSAGE")
	);
	$arMessages[] = Array(
			"EVENT_NAME" => "ORDER_CALL",
			"LID" => WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",			
			"SUBJECT" => GetMessage("MAIN_ORDER_CALL_SUBJECT"),
			"MESSAGE" => GetMessage("MAIN_ORDER_CALL_MESSAGE")
	);
	$arMessages[] = Array(
			"EVENT_NAME" => "REVIEW_POSTED",
			"LID" => WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",			
			"SUBJECT" => GetMessage("MAIN_REVIEW_POSTED_SUBJECT"),
			"MESSAGE" => GetMessage("MAIN_REVIEW_POSTED_MESSAGE")
	);
	$arMessages[] = Array(
			"EVENT_NAME" => "NEW_DRIVER",
			"LID" => WIZARD_SITE_ID,
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",			
			"SUBJECT" => GetMessage("MAIN_NEW_DRIVER_SUBJECT"),
			"MESSAGE" => GetMessage("MAIN_NEW_DRIVER_MESSAGE")
	);
		
	$message = new CEventMessage;
	foreach ($arMessages as $arMessage)
		$message->Add($arMessage);

	return true;
}
InstallEvents();

?>