<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule('sale'))
	return;

if (WIZARD_IS_RERUN)
	return;

	$wizard =& $this->GetWizard();
	
	$dbLocation = CSaleLocation::GetList(Array("ID" => "ASC"), Array("LID" => $lang));
	
	$dbSite = WIZARD_SITE_ID;
	$lang = LANGUAGE_ID;
	$bRus = true;
	$defCurrency = "RUB";
	
if(COption::GetOptionString("store", "wizard_installed", "N", WIZARD_SITE_ID) != "Y")
{
	$dbLocation = CSaleLocation::GetList(Array("ID" => "ASC"), Array("LID" => $lang));
	if($arLocation = $dbLocation->Fetch())//if there are no data in module
	{
		$arLocation4Delivery = Array();
		$arLocationArr = Array();
		do
		{
			$arLocation4Delivery[] = Array("LOCATION_ID" => $arLocation["ID"], "LOCATION_TYPE"=>"L");
			$arLocationArr[] = $arLocation["ID"];
		}
		while($arLocation = $dbLocation->Fetch());

		$dbGroup = CSaleLocationGroup::GetList();
		if(!$dbGroup->Fetch())
		{
			$groupLang = array(
						  array("LID" => $lang, "NAME" => "Group 1")
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
			//Location group
			if(IntVal($locationGroupID) > 0)
				$arLocation4Delivery[] = Array("LOCATION_ID" => $locationGroupID, "LOCATION_TYPE"=>"G");

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
				
				CSaleDelivery::Add($arFields);
				
				$arFields = Array(
							"LID" => "",
							"ACTIVE" => "Y",
							"HID" => "russianpost",
							"NAME" => GetMessage("SALE_WIZARD_MAIL"),
							"SORT" => 200,
							"DESCRIPTION" => GetMessage("SALE_WIZARD_MAIL_DESCR"),
							"HANDLERS" => "/bitrix/modules/sale/delivery/delivery_russianpost.php",
							"SETTINGS" => "23",
							"PROFILES" => "",
							"TAX_RATE" => 0,
						);
				
				CSaleDeliveryHandler::Set("russianpost", $arFields);
			}			
			
		}
	}
}

?>