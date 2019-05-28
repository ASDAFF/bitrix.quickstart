<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}
if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
}

$ID = IntVal($arParams["ID"]);
$errorMessage = "";
$bInitVars = false;

$arParams["PATH_TO_LIST"] = Trim($arParams["PATH_TO_LIST"]);
if (strlen($arParams["PATH_TO_LIST"]) <= 0)
	$arParams["PATH_TO_LIST"] = htmlspecialcharsbx($APPLICATION->GetCurPage());
$arParams["PATH_TO_DETAIL"] = Trim($arParams["PATH_TO_DETAIL"]);
if (strlen($arParams["PATH_TO_DETAIL"]) <= 0)
	$arParams["PATH_TO_DETAIL"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?ID=#ID#");

	
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if($arParams["SET_TITLE"] == 'Y')
	$APPLICATION->SetTitle(GetMessage("SPPD_TITLE").$ID);

if ($ID <= 0 || strlen($_POST["reset"]) > 0)
	LocalRedirect($arParams["PATH_TO_LIST"]);

if ($_SERVER["REQUEST_METHOD"]=="POST" && (strlen($_POST["save"]) > 0 || strlen($_POST["apply"]) > 0) && check_bitrix_sessid())
{
	$dbUserProps = CSaleOrderUserProps::GetList(
			array("DATE_UPDATE" => "DESC"),
			array(
					"ID" => $ID,
					"USER_ID" => IntVal($USER->GetID())
				),
			false,
			false,
			array("ID", "PERSON_TYPE_ID", "DATE_UPDATE")
		);
	if (!($arUserProps = $dbUserProps->Fetch()))
		$errorMessage .= GetMessage("SALE_NO_PROFILE")."<br />";

	if (strlen($errorMessage) <= 0)
	{
		$NAME = Trim($_POST["NAME"]);
		if (strlen($NAME) <= 0)
			$errorMessage .= GetMessage("SALE_NO_NAME")."<br />";

		$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array(
						"PERSON_TYPE_ID" => $arUserProps["PERSON_TYPE_ID"],
						"USER_PROPS" => "Y"
					),
				false,
				false,
				array("ID", "PERSON_TYPE_ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "SORT", "USER_PROPS", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "SORT")
			);
		while ($arOrderProps = $dbOrderProps->GetNext())
		{
			$bErrorField = False;
			$curVal = $_POST["ORDER_PROP_".$arOrderProps["ID"]];
			if ($arOrderProps["TYPE"] == "LOCATION" && $arOrderProps["IS_LOCATION"] == "Y")
			{
				$DELIVERY_LOCATION = IntVal($curVal);
				if (IntVal($curVal) <= 0)
					$bErrorField = True;
			}
			elseif ($arOrderProps["IS_PROFILE_NAME"] == "Y" || $arOrderProps["IS_PAYER"] == "Y" || $arOrderProps["IS_EMAIL"] == "Y")
			{
				if ($arOrderProps["IS_PROFILE_NAME"] == "Y")
				{
					$PROFILE_NAME = Trim($curVal);
					if (strlen($PROFILE_NAME) <= 0)
						$bErrorField = True;
				}
				if ($arOrderProps["IS_PAYER"] == "Y")
				{
					$PAYER_NAME = Trim($curVal);
					if (strlen($PAYER_NAME) <= 0)
						$bErrorField = True;
				}
				if ($arOrderProps["IS_EMAIL"] == "Y")
				{
					$USER_EMAIL = Trim($curVal);
					if (strlen($USER_EMAIL) <= 0 || !check_email($USER_EMAIL))
						$bErrorField = True;
				}
			}
			elseif ($arOrderProps["REQUIED"] == "Y")
			{
				if ($arOrderProps["TYPE"] == "TEXT" || $arOrderProps["TYPE"] == "TEXTAREA" || $arOrderProps["TYPE"] == "RADIO" || $arOrderProps["TYPE"] == "SELECT")
				{
					if (strlen($curVal) <= 0)
						$bErrorField = True;
				}
				elseif ($arOrderProps["TYPE"] == "LOCATION")
				{
					if (IntVal($curVal) <= 0)
						$bErrorField = True;
				}
				elseif ($arOrderProps["TYPE"] == "MULTISELECT")
				{
					if (!is_array($curVal) || count($curVal) <= 0)
						$bErrorField = True;
				}
			}
			if ($bErrorField)
				$errorMessage .= GetMessage("SALE_NO_FIELD")." \"".$arOrderProps["NAME"]."\".<br />";
		}
	}

	if (strlen($errorMessage) <= 0)
	{
		$arFields = array("NAME" => $NAME);
		if (!CSaleOrderUserProps::Update($ID, $arFields))
			$errorMessage .= GetMessage("SALE_ERROR_EDIT_PROF")."<br />";
	}

	if (strlen($errorMessage) <= 0)
	{
		CSaleOrderUserPropsValue::DeleteAll($ID);

		$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array(
						"PERSON_TYPE_ID" => $arUserProps["PERSON_TYPE_ID"],
						"USER_PROPS" => "Y"
					),
				false,
				false,
				array("ID", "PERSON_TYPE_ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "SORT", "USER_PROPS", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "SORT")
			);
		while ($arOrderProps = $dbOrderProps->GetNext())
		{
			$curVal = $_POST["ORDER_PROP_".$arOrderProps["ID"]];
			if ($arOrderProps["TYPE"]=="MULTISELECT")
			{
				$curVal = "";
				for ($i = 0; $i < count($_POST["ORDER_PROP_".$arOrderProps["ID"]]); $i++)
				{
					if ($i > 0)
						$curVal .= ",";
					$curVal .= $_POST["ORDER_PROP_".$arOrderProps["ID"]][$i];
				}
			}

			if (isset($_POST["ORDER_PROP_".$arOrderProps["ID"]]))
			{
				$arFields = array(
						"USER_PROPS_ID" => $ID,
						"ORDER_PROPS_ID" => $arOrderProps["ID"],
						"NAME" => $arOrderProps["NAME"],
						"VALUE" => $curVal
					);
				CSaleOrderUserPropsValue::Add($arFields);
			}
		}
	}

	if (strlen($errorMessage) > 0)
		$bInitVars = True;
	
	if (strlen($_POST["save"]) > 0 && strlen($errorMessage) <= 0)
		LocalRedirect($arParams["PATH_TO_LIST"]);
	elseif(strlen($_POST["apply"]) > 0 &&  strlen($errorMessage) <= 0)
		LocalRedirect(CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DETAIL"], Array("ID" => $ID)));
}

$arResult["ORDER_PROPS"] = Array();
$dbUserProps = CSaleOrderUserProps::GetList(
		array("DATE_UPDATE" => "DESC"),
		array(
				"ID" => $ID,
				"USER_ID" => IntVal($GLOBALS["USER"]->GetID())
			),
		false,
		false,
		array("ID", "NAME", "USER_ID", "PERSON_TYPE_ID", "DATE_UPDATE")
	);
if ($arUserProps = $dbUserProps->GetNext())
{
	if(!$bInitVars)
		$arResult = $arUserProps;
	else
	{
		foreach($_POST as $k => $v)
		{
			$arResult[$k] = htmlspecialcharsbx($v);
			$arResult['~'.$k] = $v;
		}
	}

	$arResult["ERROR_MESSAGE"] = $errorMessage;

	$arResult["TITLE"] = str_replace("#ID#", $arUserProps["ID"], GetMessage("SPPD_PROFILE_NO"));
	$arResult["PERSON_TYPE"] = CSalePersonType::GetByID($arUserProps["PERSON_TYPE_ID"]);
	$arResult["PERSON_TYPE"]["NAME"] = htmlspecialcharsEx($arResult["PERSON_TYPE"]["NAME"]);
	$arPropValsTmp = Array();
	if (!$bInitVars)
	{
		$dbPropVals = CSaleOrderUserPropsValue::GetList(
				array("SORT" => "ASC"),
				array("USER_PROPS_ID" => $arUserProps["ID"]),
				false,
				false,
				array("ID", "ORDER_PROPS_ID", "VALUE", "SORT")
			);
		while ($arPropVals = $dbPropVals->GetNext())
		{
			$arPropValsTmp["ORDER_PROP_".$arPropVals["ORDER_PROPS_ID"]] = $arPropVals["VALUE"];
		}
	}
	else
	{
		foreach ($_REQUEST as $key => $value)
		{
			if (substr($key, 0, strlen("ORDER_PROP_"))=="ORDER_PROP_")
				$arPropValsTmp[$key] = htmlspecialcharsbx($value);
		}
	}
	$arResult["ORDER_PROPS_VALUES"] = $arPropValsTmp;
	$arrayTmp = Array();	
	$dbOrderPropsGroup = CSaleOrderPropsGroup::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array("PERSON_TYPE_ID" => $arUserProps["PERSON_TYPE_ID"]),
				false,
				false,
				array("ID", "PERSON_TYPE_ID", "NAME", "SORT")
			);
	while ($arOrderPropsGroup = $dbOrderPropsGroup->GetNext())
	{
		$arrayTmp[$arOrderPropsGroup["ID"]] = $arOrderPropsGroup;
		$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC", "NAME" => "ASC"),
				array(
						"PERSON_TYPE_ID" => $arUserProps["PERSON_TYPE_ID"],
						"PROPS_GROUP_ID" => $arOrderPropsGroup["ID"],
						"USER_PROPS" => "Y", "ACTIVE" => "Y", "UTIL" => "N"
					),
				false,
				false,
				array("ID", "PERSON_TYPE_ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "SORT", "USER_PROPS", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "SORT")
			);
		while($arOrderProps = $dbOrderProps->GetNext())
		{
			if ($arOrderProps["REQUIED"]=="Y" || $arOrderProps["IS_EMAIL"]=="Y" || $arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_PAYER"]=="Y")
				$arOrderProps["REQUIED"] = "Y";
			if (in_array($arOrderProps["TYPE"], Array("SELECT", "MULTISELECT", "RADIO")))
			{
				$dbVars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID"=>$arOrderProps["ID"]));
				while ($vars = $dbVars->GetNext())
					$arOrderProps["VALUES"][] = $vars;
			}
			elseif($arOrderProps["TYPE"]=="LOCATION")
			{
				$dbVars = CSaleLocation::GetList(Array("SORT"=>"ASC", "COUNTRY_NAME_LANG"=>"ASC", "CITY_NAME_LANG"=>"ASC"), array(), LANGUAGE_ID);
				while($vars = $dbVars->GetNext())
					$arOrderProps["VALUES"][] = $vars;
			}
			$arrayTmp[$arOrderPropsGroup["ID"]]["PROPS"][] = $arOrderProps;
		}
	}
	$arResult["ORDER_PROPS"] = $arrayTmp;
}
else
	$arResult["ERROR_MESSAGE"] = GetMessage("SALE_NO_PROFILE");

$this->IncludeComponentTemplate();
?>
