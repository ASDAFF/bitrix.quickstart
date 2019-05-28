<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

$arParams["AJAX_CALL"] = $arParams["AJAX_CALL"] == "Y" ? "Y" : "N";
$arParams["STEP"] = intval($arParams["STEP"]);

$arParams['NO_AJAX'] = $arParams['NO_AJAX'] == 'Y' ? 'Y' : 'N';
if ($arParams['NO_AJAX'] == 'Y')
{
	$arParams['AJAX_CALL'] = 'Y';
	$arParams['STEP'] = 1;
}

if(!isset($arParams["EXTRA_PARAMS"]))
	$arParams["EXTRA_PARAMS"] = array();

$arParams["LOCATION_TO"] = intval($arParams["LOCATION_TO"]);
$arParams["LOCATION_FROM"] = intval($arParams["LOCATION_FROM"]);
if ($arParams["LOCATION_FROM"] <= 0)
{
	$arParams["LOCATION_FROM"] = COption::GetOptionString('sale', 'location');
}

$arParams["STEP"] = intval($arParams["STEP"]);
if ($arParams["STEP"] <= 0) $arParams["AJAX_CALL"] = 'N';

if (is_set($arParams["START_VALUE"])) $arParams["START_VALUE"] = doubleval($arParams["START_VALUE"]);

if ($arParams["AJAX_CALL"] == "Y")
{
	if ($arParams['NO_AJAX'] == 'Y')
	{
		$result = CSaleDeliveryHandler::CalculateFull(
			$arParams["DELIVERY"],
			$arParams["PROFILE"],
			array(
				"PRICE" => $arParams["ORDER_PRICE"],
				"WEIGHT" => $arParams["ORDER_WEIGHT"],
				"LOCATION_FROM" => $arParams["LOCATION_FROM"],
				"LOCATION_TO" => $arParams["LOCATION_TO"],
				"LOCATION_ZIP" => $arParams['LOCATION_ZIP'],
				"ITEMS" => $arParams["ITEMS"],
				"EXTRA_PARAMS" => $arParams["EXTRA_PARAMS"]
			),
			$arParams["CURRENCY"]
		);

		if (is_array($result))
		{
			if ($result["RESULT"] == "OK" && CModule::IncludeModule('currency'))
			{
				$result["VALUE_FORMATTED"] = CurrencyFormat($result["VALUE"], $arParams["CURRENCY"]);
			}
			elseif ($result["RESULT"] == "NEXT_STEP" && strlen($result["TEMP"]) > 0)
			{
				$result["TEMP"] = CUtil::JSEscape($result["TEMP"]);
			}

			$arResult["RESULT"] = $result;
		}
	}
	else
	{
		$dbHandler = CSaleDeliveryHandler::GetBySID($arParams["DELIVERY"]);

		if ($arHandler = $dbHandler->Fetch())
		{
			foreach ($arHandler["PROFILES"] as $profile_id => $arProfile)
			{
				if ($profile_id != $arParams["PROFILE"]) unset($arHandler["PROFILES"][$profile_id]);
			}

			$result = CSaleDeliveryHandler::Calculate(
				$arParams["STEP"],
				$arParams["DELIVERY"],
				$arParams["PROFILE"],
				array(
					"PRICE" => $arParams["ORDER_PRICE"],
					"WEIGHT" => $arParams["ORDER_WEIGHT"],
					"LOCATION_FROM" => $arParams["LOCATION_FROM"],
					"LOCATION_TO" => $arParams["LOCATION_TO"],
					"LOCATION_ZIP" => $arParams['LOCATION_ZIP'],
					"ITEMS" => $arParams["ITEMS"],
					"EXTRA_PARAMS" => $arParams["EXTRA_PARAMS"]
				),
				$arParams["CURRENCY"],
				$arParams["~TEMP"]
			);

			if (is_array($result))
			{
				if ($result["RESULT"] == "OK" && CModule::IncludeModule('currency'))
				{
					$result["VALUE_FORMATTED"] = CurrencyFormat($result["VALUE"], $arParams["CURRENCY"]);
				}
				elseif ($result["RESULT"] == "NEXT_STEP" && strlen($result["TEMP"]) > 0)
				{
					$result["TEMP"] = CUtil::JSEscape($result["TEMP"]);
				}

				$arResult["RESULT"] = $result;
			}
		}
		else
		{
			ShowError(GetMessage("SALE_DELIVERY_HANDLER_NOT_INSTALL"));
			return;
		}
	}
}
else
{
	$arParams["STEP"] = 0;

	$arResult["B_ADMIN"] = defined("ADMIN_SECTION") && ADMIN_SECTION===true ? "Y" : "N";

	if ($arResult["B_ADMIN"] != "Y")
	{
		$folderPath = "";
		IncludeAJAX();
		if($this->InitComponentTemplate())
		{
			$template = $this->GetTemplate();
			if($template)
			{
				$folderPath = $template->GetFolder();
			}
		}

		if(strlen($folderPath) <= 0)
		{
			$folderPath = $this->GetPath().'/templates/'.(strlen($componentTemplate) > 0 ? $componentTemplate : '.default');
		}

		$APPLICATION->AddHeadScript($folderPath.'/proceed.js');
	}
	elseif ($arParams["STEP"] == 0)
	{
		$arResult["PATH"] = $this->GetPath().'/templates/'.(strlen($componentTemplate) > 0 ? $componentTemplate : '.default').'/';
	}
}

$arTmpParams = array(
	"STEP" => intval($arParams["STEP"]) + 1,
	"DELIVERY" => $arParams["DELIVERY"],
	"PROFILE" => $arParams["PROFILE"],
	"WEIGHT" => doubleval($arParams["ORDER_WEIGHT"]),
	"PRICE" => doubleval($arParams["ORDER_PRICE"]),
	"LOCATION" => intval($arParams["LOCATION_TO"]),
	"LOCATION_ZIP" => $arParams['LOCATION_ZIP'],
	"CURRENCY" => $arParams["CURRENCY"],
	"INPUT_NAME" => $arParams["INPUT_NAME"],
	"TEMP" => $arParams["~TEMP"],
	"ITEMS" => $arParams["ITEMS"],
	"EXTRA_PARAMS_CALLBACK" => $arParams["EXTRA_PARAMS_CALLBACK"]
);

$arResult["JS_PARAMS"] = CUtil::PhpToJsObject($arTmpParams);

$this->IncludeComponentTemplate();
?>