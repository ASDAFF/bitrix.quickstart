<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!function_exists("getNameCount"))
{
	function getNameCount($propName, $propCode, $arProps)
	{
		$count = 1;
		foreach ($arProps as $id => $arData)
		{
			if (isset($arData["NAME"]) && $arData["NAME"] == $propName && $propCode != $arData["CODE"])
				$count++;
		}
		return $count;
	}
}

if (!function_exists("getIblockNames"))
{
	function getIblockNames($arIblockIDs)
	{
		$str = "";
		if (CModule::IncludeModule("iblock"))
		{
			$res = CIBlock::GetList(
				Array(),
				Array(
					"ID" => $arIblockIDs,
					"ACTIVE" => "Y",
				),
				true
			);
			while($ar_res = $res->Fetch())
				$str .= "\"".$ar_res["NAME"]."\", ";

			$str .= "#";
		}
		return str_replace(", #", "", $str);
	}
}

$arColumns = array(
	"PREVIEW_PICTURE" => GetMessage("SOA_PREVIEW_PICTURE"),
	"DETAIL_PICTURE" => GetMessage("SOA_DETAIL_PICTURE"),
	"PREVIEW_TEXT" => GetMessage("SOA_PREVIEW_TEXT"),
	"PROPS" => GetMessage("SOA_PROPS"),
	"NOTES" => GetMessage("SOA_PRICE_TYPE"),
	"DISCOUNT_PRICE_PERCENT_FORMATED" => GetMessage("SOA_DISCOUNT"),
	"WEIGHT_FORMATED" => GetMessage("SOA_WEIGHT"),
);

if (CModule::IncludeModule("catalog"))
{
	// get iblock props from all catalog iblocks including sku iblocks
	$arIblockIDs = array();
	$dbCatalog = CCatalog::GetList(array(), array());
	while ($arCatalog = $dbCatalog->GetNext())
		$arIblockIDs[] = $arCatalog["IBLOCK_ID"];

	// iblock props
	$arProps = array();
	foreach ($arIblockIDs as $iblockID)
	{
		$dbProps = CIBlockProperty::GetList(
			array(
				"NAME"=>"ASC",
				"SORT"=>"ASC"
			),
			array(
				"IBLOCK_ID" => $iblockID,
				"ACTIVE" => "Y",
				"CHECK_PERMISSIONS" => "N",
			)
		);

		while ($arProp = $dbProps->GetNext())
		{
			if ('CML2_LINK' == $arProp['XML_ID'])
				continue;
			$arProps[] = $arProp;
		}
	}

	// create properties array where properties with the same codes are considered the same
	$arTmpProperty2Iblock = array();
	foreach ($arProps as $id => $arProperty)
	{
		$arTmpProperty2Iblock["PROPERTY_".$arProperty["CODE"]][] = $arProperty["IBLOCK_ID"];

		if (getNameCount($arProperty["NAME"], $arProperty["CODE"], $arProps) > 1)
			$name = $arProperty["NAME"]." [".$arProperty["CODE"]."] ";
		else
			$name = $arProperty["NAME"];

		$name = htmlspecialcharsback($name);

		if (array_key_exists("PROPERTY_".$arProperty["CODE"], $arColumns))
			$arColumns["PROPERTY_".$arProperty["CODE"]]	= $name." (".getIblockNames($arTmpProperty2Iblock["PROPERTY_".$arProperty["CODE"]]).")";
		else
			$arColumns["PROPERTY_".$arProperty["CODE"]] = $name;
	}
}

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_BASKET" => Array(
			"NAME" => GetMessage("SOA_PATH_TO_BASKET"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "basket.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_PERSONAL" => Array(
			"NAME" => GetMessage("SOA_PATH_TO_PERSONAL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "index.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_PAYMENT" => Array(
			"NAME" => GetMessage("SOA_PATH_TO_PAYMENT"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "payment.php",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PATH_TO_AUTH" => Array(
			"NAME" => GetMessage("SOA_PATH_TO_AUTH"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/auth/",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
		"PAY_FROM_ACCOUNT" => Array(
			"NAME"=>GetMessage("SOA_ALLOW_PAY_FROM_ACCOUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"Y",
			"PARENT" => "BASE",
		),
		"ONLY_FULL_PAY_FROM_ACCOUNT" => Array(
			"NAME"=>GetMessage("SOA_ONLY_FULL_PAY_FROM_ACCOUNT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"COUNT_DELIVERY_TAX" => Array(
			"NAME"=>GetMessage("SOA_COUNT_DELIVERY_TAX"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"ALLOW_AUTO_REGISTER" => Array(
			"NAME"=>GetMessage("SOA_ALLOW_AUTO_REGISTER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"N",
			"PARENT" => "BASE",
		),
		"SEND_NEW_USER_NOTIFY" => Array(
			"NAME"=>GetMessage("SOA_SEND_NEW_USER_NOTIFY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT"=>"Y",
			"PARENT" => "BASE",
		),
		"DELIVERY_NO_AJAX" => Array(
			"NAME" => GetMessage("SOA_DELIVERY_NO_AJAX"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
		),
		"DELIVERY_NO_SESSION" => Array(
			"NAME" => GetMessage("SOA_DELIVERY_NO_SESSION"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"PARENT" => "BASE",
		),
		"TEMPLATE_LOCATION" => Array(
			"NAME"=>GetMessage("SBB_TEMPLATE_LOCATION"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"N",
			"VALUES"=>array(
					".default" => GetMessage("SBB_TMP_DEFAULT"),
					"popup" => GetMessage("SBB_TMP_POPUP")
				),
			"DEFAULT"=>".default",
			"COLS"=>25,
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		),
		"DELIVERY_TO_PAYSYSTEM" => Array(
			"NAME" => GetMessage("SBB_DELIVERY_PAYSYSTEM"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES"=>array(
					"d2p" => GetMessage("SBB_TITLE_PD"),
					"p2d" => GetMessage("SBB_TITLE_DP")
				),
			"PARENT" => "BASE",
		),
		"SET_TITLE" => Array(),
		"USE_PREPAYMENT" => array(
			"NAME" => GetMessage('SBB_USE_PREPAYMENT'),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"DEFAULT" => "N",
			"ADDITIONAL_VALUES"=>"N",
			"PARENT" => "BASE",
		),
		"DISABLE_BASKET_REDIRECT" => array(
			"NAME" => GetMessage('SOA_DISABLE_BASKET_REDIRECT'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		"PRODUCT_COLUMNS" => Array(
			"NAME" => GetMessage("SOA_PRODUCT_COLUMNS"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"COLS" => 25,
			"SIZE" => 7,
			"VALUES" => $arColumns,
			"DEFAULT" => array(),
			"ADDITIONAL_VALUES" => "N",
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	)
);

if(CModule::IncludeModule("sale"))
{
	$dbPerson = CSalePersonType::GetList(Array("SORT" => "ASC", "NAME" => "ASC"));
	while($arPerson = $dbPerson->GetNext())
	{
		$arPers2Prop = Array("" => GetMessage("SOA_SHOW_ALL"));
		$bProp = false;
		$dbProp = CSaleOrderProps::GetList(Array("SORT" => "ASC", "NAME" => "ASC"), Array("PERSON_TYPE_ID" => $arPerson["ID"]));
		while($arProp = $dbProp -> Fetch())
		{
			$arPers2Prop[$arProp["ID"]] = $arProp["NAME"];
			$bProp = true;
		}

		if($bProp)
		{
			$arComponentParameters["PARAMETERS"]["PROP_".$arPerson["ID"]] =  Array(
				"NAME" => GetMessage("SOA_PROPS_NOT_SHOW")." \"".$arPerson["NAME"]."\" (".$arPerson["LID"].")",
				"TYPE"=>"LIST", "MULTIPLE"=>"Y",
				"VALUES" => $arPers2Prop,
				"DEFAULT"=>"",
				"COLS"=>25,
				"ADDITIONAL_VALUES"=>"N",
				"PARENT" => "BASE",
			);
		}
	}
}
?>