<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// *****************************************************************************************
//Входные параметры
// $bVarsFromForm -
// arUserField USER_TYPE - тип свойства
// arUserField VALUE- значение пользовательского свойства
// *****************************************************************************************
$arParams["bVarsFromForm"] = $arParams["bVarsFromForm"] ? true:false;
$arResult["VALUE"] = false;
$arUserField = &$arParams["arUserField"];
// *****************************************************************************************

if($arUserField["USER_TYPE"])
{
	if(!$arParams["bVarsFromForm"])
	{
		if(
			$arUserField["ENTITY_VALUE_ID"] <= 0
			&& !is_array($arUserField["SETTINGS"]["DEFAULT_VALUE"])
			&& strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"]) > 0
		)
			$arResult["VALUE"] = $arParams["~arUserField"]["SETTINGS"]["DEFAULT_VALUE"];
		else
			$arResult["VALUE"] = $arParams["~arUserField"]["VALUE"];
	}
	else
	{
		if($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
			$arResult["VALUE"] = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"];
		else
			$arResult["VALUE"] = $_REQUEST[$arUserField["FIELD_NAME"]];
	}
	if (!is_array($arResult["VALUE"]))
		$arResult["VALUE"] = array($arResult["VALUE"]);
	if (empty($arResult["VALUE"]))
		$arResult["VALUE"] = array(null);

	foreach ($arResult["VALUE"] as $key => $res)
	{
		switch ($arUserField["USER_TYPE"]["BASE_TYPE"])
		{
			case "double":
				if (strlen($res)>0)
					$res = round(doubleval($res), $arUserField["SETTINGS"]["PRECISION"]);
				break;
			case "int":
				$res = intVal($res);
				break;
			default:
				$res = htmlspecialcharsbx($res);
				break;
		}
		$arResult["VALUE"][$key] = $res;
	}

	$arUserField["~FIELD_NAME"] = $arUserField["FIELD_NAME"];
	if ($arUserField["MULTIPLE"]=="Y")
	{
		$arUserField["~FIELD_NAME"] = $arUserField["FIELD_NAME"];
		$arUserField["FIELD_NAME"] .= "[]";

		if (!empty($arResult["VALUE"]) && (!empty($arResult["VALUE"][count($arResult["VALUE"])-1])))
		{
			$arResult["VALUE"][] = null;
		}
	}

	if (is_callable(array($arUserField["USER_TYPE"]['CLASS_NAME'], 'getlist')))
	{
		$enum = array();

		if(
			($arUserField["MANDATORY"] != "Y")
			&& ($arUserField["SETTINGS"]["DISPLAY"] != "CHECKBOX")
		):
			$enum = array(null=>GetMessage("MAIN_NO"));
		endif;

		$rsEnum = call_user_func_array(
			array($arParams['arUserField']["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arParams['arUserField'],
			)
		);

		if(!$arParams["bVarsFromForm"] && ($arUserField["ENTITY_VALUE_ID"] <= 0))
			$arResult["VALUE"] = array();

		while($arEnum = $rsEnum->GetNext())
		{
			$enum[$arEnum["ID"]] = $arEnum["VALUE"];
			if(!$arParams["bVarsFromForm"] && ($arUserField["ENTITY_VALUE_ID"] <= 0))
			{
				if($arEnum["DEF"] == "Y")
					$arResult["VALUE"][] = $arEnum["ID"];
			}
		}
		$arUserField["USER_TYPE"]["FIELDS"] = $enum;
	}

	$arParams["form_name"] = !empty($arParams["form_name"]) ? $arParams["form_name"] : "form1";
	$this->IncludeComponentTemplate();
}?>