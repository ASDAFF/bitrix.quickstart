<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule('sale'))
	return;

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";
$bRus = false;
if($lang == "ru")
	$bRus = true;

$defCurrency = "EUR";
if($lang == "ru")
	$defCurrency = "RUB";
elseif($lang == "en")
	$defCurrency = "USD";

$delivery = $wizard->GetVar("delivery");
$shopLocalization = $wizard->GetVar("shopLocalization");

WizardServices::IncludeServiceLang("step2.php", $lang);
if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) != "Y")
{
	$locationGroupID = 0;
	$arLocation4Delivery = Array();
	$arLocationArr = Array();

	if(\Bitrix\Main\Config\Option::get('sale', 'sale_locationpro_migrated', '') == 'Y') // CSaleLocation::isLocationProMigrated()
	{
		$res = \Bitrix\Sale\Location\LocationTable::getList(array('filter' => array('=TYPE.CODE' => 'COUNTRY'), 'select' => array('ID')));
		while($item = $res->fetch())
		{
			$arLocation4Delivery[] = Array("LOCATION_ID" => $item["ID"], "LOCATION_TYPE"=>"L");
		}
	}
	else
	{
		$dbLocation = CSaleLocation::GetList(Array(), array("LID" => $lang));
		while($arLocation = $dbLocation->Fetch())
		{
			$arLocation4Delivery[] = Array("LOCATION_ID" => $arLocation["ID"], "LOCATION_TYPE"=>"L");
			$arLocationArr[] = $arLocation["ID"];
		}

		$dbGroup = CSaleLocationGroup::GetList();
		if($arGroup = $dbGroup->Fetch())
		{
			$arLocation4Delivery[] = Array("LOCATION_ID" => $arGroup["ID"], "LOCATION_TYPE"=>"G");
		}
		else
		{
			$groupLang = array(
				array("LID" => "en", "NAME" => "Group 1")
			);

			if($bRus)
				$groupLang[] = array("LID" => $lang, "NAME" => GetMessage("SALE_WIZARD_GROUP"));
				
			$locationGroupID = CSaleLocationGroup::Add(
					array(
						"SORT" => 150,
						"LOCATION_ID" => $arLocationArr,
						"LANG" => $groupLang
					)
				);
		}
		//Location group
		if(IntVal($locationGroupID) > 0)
			$arLocation4Delivery[] = Array("LOCATION_ID" => $locationGroupID, "LOCATION_TYPE"=>"G");
	}

	$dbDelivery = CSaleDelivery::GetList(array(), Array("LID" => WIZARD_SITE_ID));
	if(!$dbDelivery->Fetch())
	{
		//delivery handler
		$arFields = Array(
				"NAME" => GetMessage("SALE_WIZARD_COUR"),
				"LID" => WIZARD_SITE_ID,
				"PERIOD_FROM" => 0,
				"PERIOD_TO" => 0,
				"PERIOD_TYPE" => "D",
				"WEIGHT_FROM" => 0,
				"WEIGHT_TO" => 0,
				"ORDER_PRICE_FROM" => 0,
				"ORDER_PRICE_TO" => 0,
				"ORDER_CURRENCY" => $defCurrency,
				"ACTIVE" => "Y",
				"PRICE" => ($bRus ? "500" : "30"),
				"CURRENCY" => $defCurrency,
				"SORT" => 100,
				"DESCRIPTION" => GetMessage("SALE_WIZARD_COUR_DESCR"),
				"LOCATIONS" => $arLocation4Delivery,
			);
		if($delivery["courier"] != "Y")
			$arFields["ACTIVE"] = "N";

		CSaleDelivery::Add($arFields);
		
		$arFields = Array(
				"NAME" => GetMessage("SALE_WIZARD_COUR1"),
				"LID" => WIZARD_SITE_ID,
				"PERIOD_FROM" => 0,
				"PERIOD_TO" => 0,
				"PERIOD_TYPE" => "D",
				"WEIGHT_FROM" => 0,
				"WEIGHT_TO" => 0,
				"ORDER_PRICE_FROM" => 0,
				"ORDER_PRICE_TO" => 0,
				"ORDER_CURRENCY" => $defCurrency,
				"ACTIVE" => "Y",
				"PRICE" => 0,
				"CURRENCY" => $defCurrency,
				"SORT" => 200,
				"DESCRIPTION" => GetMessage("SALE_WIZARD_COUR1_DESCR"),
				"LOCATIONS" => $arLocation4Delivery,
			);
		if($delivery["self"] != "Y")
			$arFields["ACTIVE"] = "N";
		CSaleDelivery::Add($arFields);
	}

	$dbDelivery = CSaleDeliveryHandler::GetList();
	if(!$dbDelivery->Fetch())
	{
		if($bRus)
		{
			if ($shopLocalization == "ru")
			{
				$arFields = Array(
						"LID" => "",
						"ACTIVE" => "N",
						"HID" => "cpcr",
						"NAME" => GetMessage("SALE_WIZARD_SPSR"),
						"SORT" => 100,
						"DESCRIPTION" => GetMessage("SALE_WIZARD_SPSR_DESCR"),
						"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_cpcr.php",
						"SETTINGS" => "8",
						"PROFILES" => "",
						"TAX_RATE" => 0,
				);
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				CSaleDeliveryHandler::Set("cpcr", $arFields);

				$arFields = Array(
						"LID" => "",
						"ACTIVE" => "Y",
						"HID" => "russianpost",
						"NAME" => GetMessage("SALE_WIZARD_MAIL"),
						"SORT" => 200,
						"DESCRIPTION" => "",
						"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_russianpost.php",
						"SETTINGS" => "23",
						"PROFILES" => "",
						"TAX_RATE" => 0,
					);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				if($delivery["russianpost"] != "Y")
					$arFields["ACTIVE"] = "N";
				CSaleDeliveryHandler::Set("russianpost", $arFields);

			//new russian post
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "rus_post",
					"NAME" => GetMessage("SALE_WIZARD_MAIL2"),
					"SORT" => 400,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_rus_post.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				if($delivery["rus_post"] != "Y")
					$arFields["ACTIVE"] = "N";
				CSaleDeliveryHandler::Set("rus_post", $arFields);

				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "rus_post_first",
					"NAME" => GetMessage("SALE_WIZARD_MAIL_FIRST"),
					"SORT" => 500,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_rus_post_first.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				if($delivery["rus_post_first"] != "Y")
					$arFields["ACTIVE"] = "N";
				CSaleDeliveryHandler::Set("rus_post_first", $arFields);
			}
			elseif ($shopLocalization == "ua")
			{
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "ua_post",
					"NAME" => GetMessage("SALE_WIZARD_UA_POST"),
					"SORT" => 600,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_ua_post.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				if($delivery["ua_post"] != "Y")
					$arFields["ACTIVE"] = "N";
				CSaleDeliveryHandler::Set("ua_post", $arFields);
			}
			elseif ($shopLocalization == "kz")
			{
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "kaz_post",
					"NAME" => GetMessage("SALE_WIZARD_KAZ_POST"),
					"SORT" => 600,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_kaz_post.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				if($delivery["kaz_post"] != "Y")
					$arFields["ACTIVE"] = "N";
				CSaleDeliveryHandler::Set("kaz_post", $arFields);
			}
		}

		$arFields = Array(
				"LID" => "",
				"ACTIVE" => "Y",
				"HID" => "ups",
				"NAME" => "UPS",
				"SORT" => 300,
				"DESCRIPTION" => GetMessage("SALE_WIZARD_UPS"),
				"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_ups.php",
				"SETTINGS" => "/bitrix/modules/sale/delivery/ups/ru_csv_zones.csv;/bitrix/modules/sale/delivery/ups/ru_csv_export.csv",
				"PROFILES" => "",
				"TAX_RATE" => 0,
			);

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif"))
			$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif");

		if($delivery["ups"] != "Y")
			$arFields["ACTIVE"] = "N";
		CSaleDeliveryHandler::Set("ups", $arFields);
		
		$arFields = Array(
				"LID" => "",
				"ACTIVE" => "Y",
				"HID" => "dhlusa",
				"NAME" => "DHL (USA)",
				"SORT" => 300,
				"DESCRIPTION" => GetMessage("SALE_WIZARD_UPS"),
				"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_dhl_usa.php ",
				"SETTINGS" => "",
				"PROFILES" => "",
				"TAX_RATE" => 0,
			);

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif"))
			$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/delivery/".$arFields["HID"]."_logo.gif");
		
		if($delivery["dhl"] != "Y")
			$arFields["ACTIVE"] = "N";
		CSaleDeliveryHandler::Set("dhlusa", $arFields);
	}
}
else
{
	$arAutoDeliveries = array();
	$dbDelivery = CSaleDeliveryHandler::GetList();
	while($arDelivery = $dbDelivery->Fetch())
	{
		$arAutoDeliveries[$arDelivery["SID"]] = $arDelivery["ACTIVE"];
	}

	if($bRus)
	{
		if ($shopLocalization == "ru")
		{
			if($delivery["russianpost"] == "Y" && $arAutoDeliveries["russianpost"] != "Y")
			{
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "russianpost",
					"NAME" => GetMessage("SALE_WIZARD_MAIL"),
					"SORT" => 200,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_russianpost.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				CSaleDeliveryHandler::Set("russianpost", $arFields);
			}

			if($delivery["rus_post"] == "Y" && $arAutoDeliveries["rus_post"] != "Y")
			{
				//new russian post
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "rus_post",
					"NAME" => GetMessage("SALE_WIZARD_MAIL2"),
					"SORT" => 400,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_rus_post.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				CSaleDeliveryHandler::Set("rus_post", $arFields);
			}

			if($delivery["rus_post_first"] == "Y" && $arAutoDeliveries["rus_post_first"] != "Y")
			{
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "rus_post_first",
					"NAME" => GetMessage("SALE_WIZARD_MAIL_FIRST"),
					"SORT" => 500,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_rus_post_first.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				CSaleDeliveryHandler::Set("rus_post_first", $arFields);
			}
		}
		elseif ($shopLocalization == "ua")
		{
			if($delivery["ua_post"] == "Y" && $arAutoDeliveries["ua_post"] != "Y")
			{
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "ua_post",
					"NAME" => GetMessage("SALE_WIZARD_UA_POST"),
					"SORT" => 600,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_ua_post.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				CSaleDeliveryHandler::Set("ua_post", $arFields);
			}
		}
		elseif ($shopLocalization == "kz")
		{
			if($delivery["kaz_post"] == "Y" && $arAutoDeliveries["kaz_post"] != "Y")
			{
				$arFields = Array(
					"LID" => "",
					"ACTIVE" => "Y",
					"HID" => "kaz_post",
					"NAME" => GetMessage("SALE_WIZARD_KAZ_POST"),
					"SORT" => 600,
					"DESCRIPTION" => "",
					"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_kaz_post.php",
					"SETTINGS" => "23",
					"PROFILES" => "",
					"TAX_RATE" => 0,
				);

				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif"))
					$arFields["LOGOTIP"] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/ru/delivery/".$arFields["HID"]."_logo.gif");

				CSaleDeliveryHandler::Set("kaz_post", $arFields);
			}
		}
	}
}

if(CModule::IncludeModule('subscribe'))
{
	$templates_dir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/subscribe/templates";
	$template = $templates_dir."/store_news_".WIZARD_SITE_ID;
	//Copy template from module if where was no template
	if(!file_exists($template))
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/subscribe/install/php_interface/subscribe/templates/news", $template, false, true);
		$fname = $template."/template.php";
		if(file_exists($fname) && is_file($fname) && ($fh = fopen($fname, "rb")))
		{
			$php_source = fread($fh, filesize($fname));
			$php_source = preg_replace("#([\"'])(SITE_ID)(\\1)(\\s*=>\s*)([\"'])(.*?)(\\5)#", "\\1\\2\\3\\4\\5".WIZARD_SITE_ID."\\7", $php_source);
			$php_source = str_replace("Windows-1251", $arSite["CHARSET"], $php_source);
			$php_source = str_replace("Hello!", GetMessage("SUBSCR_1"), $php_source);
			$php_source = str_replace("<P>Best Regards!</P>", "", $php_source);
			fclose($fh);
			$fh = fopen($fname, "wb");
			if($fh)
			{
				fwrite($fh, $php_source);
				fclose($fh);
			}
		}
	}

	$rsRubric = CRubric::GetList(array(), array(
		"NAME" => GetMessage("SUBSCR_1"),
		"LID" => WIZARD_SITE_ID,
	));
	if(!$rsRubric->Fetch())
	{
		//Database actions
		$arFields = Array(
			"ACTIVE"	=> "Y",
			"NAME"		=> GetMessage("SUBSCR_1"),
			"SORT"		=> 100,
			"DESCRIPTION"	=> GetMessage("SUBSCR_2"),
			"LID"		=> WIZARD_SITE_ID,
			"AUTO"		=> "Y",
			"DAYS_OF_MONTH"	=> "",
			"DAYS_OF_WEEK"	=> "1,2,3,4,5,6,7",  
			"TIMES_OF_DAY"	=> "05:00",
			"TEMPLATE"	=> substr($template, strlen($_SERVER["DOCUMENT_ROOT"]."/")),
			"VISIBLE"	=> "Y",
			"FROM_FIELD"	=> COption::GetOptionString("main", "email_from", "info@ourtestsite.com"),
			"LAST_EXECUTED"	=> ConvertTimeStamp(false, "FULL"), 
		);
		$obRubric = new CRubric;
		$ID = $obRubric->Add($arFields);
	}
	COption::SetOptionString('subscribe', 'subscribe_section', '#SITE_DIR#personal/subscribe/');
}

$shopEmail = $wizard->GetVar("shopEmail");
$siteName = $wizard->GetVar("siteName");
COption::SetOptionString('main', 'email_from', $shopEmail);
COption::SetOptionString('main', 'new_user_registration', 'Y');
COption::SetOptionString('main', 'captcha_registration', 'Y');
COption::SetOptionString('main', 'site_name', $siteName);
COption::SetOptionInt("search", "suggest_save_days", 250);

if(strlen(COption::GetOptionString('main', 'CAPTCHA_presets', '')) <= 0)
{
	COption::SetOptionString('main', 'CAPTCHA_transparentTextPercent', '0');
	COption::SetOptionString('main', 'CAPTCHA_arBGColor_1', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_arBGColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_numEllipses', '0');
	COption::SetOptionString('main', 'CAPTCHA_arEllipseColor_1', '7F7F7F');
	COption::SetOptionString('main', 'CAPTCHA_arEllipseColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_bLinesOverText', 'Y');
	COption::SetOptionString('main', 'CAPTCHA_numLines', '0');
	COption::SetOptionString('main', 'CAPTCHA_arLineColor_1', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_arLineColor_2', 'FFFFFF');
	COption::SetOptionString('main', 'CAPTCHA_textStartX', '40');
	COption::SetOptionString('main', 'CAPTCHA_textFontSize', '26');
	COption::SetOptionString('main', 'CAPTCHA_arTextColor_1', '000000');
	COption::SetOptionString('main', 'CAPTCHA_arTextColor_2', '000000');
	COption::SetOptionString('main', 'CAPTCHA_textAngel_1', '-15');
	COption::SetOptionString('main', 'CAPTCHA_textAngel_2', '15');
	COption::SetOptionString('main', 'CAPTCHA_textDistance_1', '-2');
	COption::SetOptionString('main', 'CAPTCHA_textDistance_2', '-2');
	COption::SetOptionString('main', 'CAPTCHA_bWaveTransformation', 'Y');
	COption::SetOptionString('main', 'CAPTCHA_arBorderColor', '000000');
	COption::SetOptionString('main', 'CAPTCHA_arTTFFiles', 'bitrix_captcha.ttf');
	COption::SetOptionString('main', 'CAPTCHA_letters', 'ABCDEFGHJKLMNPQRSTWXYZ23456789');
	COption::SetOptionString('main', 'CAPTCHA_presets', '2');
}	
COption::SetOptionString('socialnetwork', 'allow_tooltip', 'N', false ,  WIZARD_SITE_ID);

//Edit profile task
$editProfileTask = false;
$dbResult = CTask::GetList(Array(), Array("NAME" => "main_change_profile"));
if ($arTask = $dbResult->Fetch())
	$editProfileTask = $arTask["ID"];
//Registered users group
$dbResult = CGroup::GetList($by, $order, Array("STRING_ID" => "REGISTERED_USERS"));
if (!$dbResult->Fetch())
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE" => "Y",
		"C_SORT" => 3,
		"NAME" => GetMessage("REGISTERED_USERS"),
		"STRING_ID" => "REGISTERED_USERS",
	);

	$groupID = $group->Add($arFields);
	if ($groupID > 0)
	{
		COption::SetOptionString("main", "new_user_registration_def_group", $groupID);
		if ($editProfileTask)
			CGroup::SetTasks($groupID, Array($editProfileTask), true);
	}
}

$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array("ACTIVE"=>"Y", "ADMIN"=>"N", "ANONYMOUS"=>"N")); 
if(!($rsGroups->Fetch()))
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE"       => "Y",
		"C_SORT"       => 100,
		"NAME"         => GetMessage("REGISTERED_USERS"),
		"DESCRIPTION"  => "",
		);
	$NEW_GROUP_ID = $group->Add($arFields);
	COption::SetOptionString('main', 'new_user_registration_def_group', $NEW_GROUP_ID);
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"main", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"P"));
	if($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($NEW_GROUP_ID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
}

$userGroupID = "";
$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "sale_administrator"));
if($arGroup = $dbGroup -> Fetch())
{
	$userGroupID = $arGroup["ID"];
}
else
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE"       => "Y",
		"C_SORT"       => 200,
		"NAME"         => GetMessage("SALE_WIZARD_ADMIN_SALE"),
		"DESCRIPTION"  => GetMessage("SALE_WIZARD_ADMIN_SALE_DESCR"),
		"USER_ID"      => array(),
		"STRING_ID"      => "sale_administrator",
		);
	$userGroupID = $group->Add($arFields);
}

if(IntVal($userGroupID) > 0)
{
	WizardServices::SetFilePermission(Array($siteID, "/bitrix/admin"), Array($userGroupID => "R"));
	WizardServices::SetFilePermission(Array($siteID, "/bitrix/admin"), Array($userGroupID => "R"));
	
	$new_task_id = CTask::Add(array(
			"NAME" => GetMessage("SALE_WIZARD_ADMIN_SALE"),
			"DESCRIPTION" => GetMessage("SALE_WIZARD_ADMIN_SALE_DESCR"),
			"LETTER" => "Q",
			"BINDING" => "module",
			"MODULE_ID" => "main",
	));
	if($new_task_id)
	{
		$arOps = array();
		$rsOp = COperation::GetList(array(), array("NAME"=>"cache_control|view_own_profile|edit_own_profile"));
		while($arOp = $rsOp->Fetch())
			$arOps[] = $arOp["ID"];
		CTask::SetOperations($new_task_id, $arOps);
	}
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"main", "SYS"=>"N", "BINDIG"=>"module","LETTER"=>"Q"));
	if($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	CMain::SetGroupRight("sale", $userGroupID, "U");
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"catalog", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"T"));
	while($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
}

$userGroupID = "";
$dbGroup = CGroup::GetList($by = "", $order = "", Array("STRING_ID" => "content_editor"));

if($arGroup = $dbGroup -> Fetch())
{
	$userGroupID = $arGroup["ID"];
}
else
{
	$group = new CGroup;
	$arFields = Array(
		"ACTIVE"       => "Y",
		"C_SORT"       => 300,
		"NAME"         => GetMessage("SALE_WIZARD_CONTENT_EDITOR"),
		"DESCRIPTION"  => GetMessage("SALE_WIZARD_CONTENT_EDITOR_DESCR"),
		"USER_ID"      => array(),
		"STRING_ID"      => "content_editor",
		);
	$userGroupID = $group->Add($arFields);
	$DB->Query("INSERT INTO b_sticker_group_task(GROUP_ID, TASK_ID)	SELECT ".intVal($userGroupID).", ID FROM b_task WHERE NAME='stickers_edit' AND MODULE_ID='fileman'", false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
}
if(IntVal($userGroupID) > 0)
{
	WizardServices::SetFilePermission(Array($siteID, "/bitrix/admin"), Array($userGroupID => "R"));
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"main", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"P"));
	if($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	$rsTasks = CTask::GetList(array(), array("MODULE_ID"=>"fileman", "SYS"=>"Y", "BINDIG"=>"module","LETTER"=>"F"));
	if($arTask = $rsTasks->Fetch())
	{
		CGroup::SetModulePermission($userGroupID, $arTask["MODULE_ID"], $arTask["ID"]);
	}
	
	$SiteDir = "";
	if(WIZARD_SITE_ID != "s1"){
		$SiteDir = "/site_" . WIZARD_SITE_ID;
	}
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/index.php"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/about/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/news/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/catalog/"), Array($userGroupID => "W"));
	WizardServices::SetFilePermission(Array($siteID, $SiteDir . "/personal/"), Array($userGroupID => "W"));
}
COption::SetOptionString("eshop", "wizard_installed", "Y", false, WIZARD_SITE_ID);
?>