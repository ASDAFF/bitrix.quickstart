<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BCF_MODULE_NOT_INSTALLED"));
	return;
}

/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

unset($arParams["IBLOCK_TYPE"]); //was used only for IBLOCK_ID setup with Editor
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);
if(!is_array($arParams["PRICE_CODE"]))
	$arParams["PRICE_CODE"] = array();

if(!is_array($arParams["OFFERS_FIELD_CODE"]))
	$arParams["OFFERS_FIELD_CODE"] = array();
foreach($arParams["OFFERS_FIELD_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["OFFERS_FIELD_CODE"][$k]);

if(!is_array($arParams["OFFERS_PROPERTY_CODE"]))
	$arParams["OFFERS_PROPERTY_CODE"] = array();
foreach($arParams["OFFERS_PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["OFFERS_PROPERTY_CODE"][$k]);
	
$arParams["SAVE_IN_SESSION"] = $arParams["SAVE_IN_SESSION"]=="Y";

if(strlen($arParams["FILTER_NAME"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilter";
$FILTER_NAME = $arParams["FILTER_NAME"];

global $$FILTER_NAME;
$$FILTER_NAME = array();

$arParams["NUMBER_WIDTH"] = intval($arParams["NUMBER_WIDTH"]);
if($arParams["NUMBER_WIDTH"]<=0)
	$arParams["NUMBER_WIDTH"]=5;
$arParams["TEXT_WIDTH"] = intval($arParams["TEXT_WIDTH"]);
if($arParams["TEXT_WIDTH"]<=0)
	$arParams["TEXT_WIDTH"]=20;
$arParams["LIST_HEIGHT"] = intval($arParams["LIST_HEIGHT"]);
if($arParams["LIST_HEIGHT"]<=0)
	$arParams["LIST_HEIGHT"]=5;

	/*************************************************************************
				Processing the  "Filter" and "Reset" button actions
	*************************************************************************/

	if (in_array("ACTIVE_DATE", $arParams["FIELD_CODE"]))
	{
		$active_date_1 = $FILTER_NAME."_ACTIVE_DATE_1";
		$active_date_2 = $FILTER_NAME."_ACTIVE_DATE_2";
		$active_date_days_to_back = $active_date_1."_DAYS_TO_BACK";
		global $$active_date_days_to_back;
	}
	if (in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))
	{
		$date_active_from_1 = $FILTER_NAME."_DATE_ACTIVE_FROM_1";
		$date_active_from_2 = $FILTER_NAME."_DATE_ACTIVE_FROM_2";
		$date_active_from_days_to_back = $date_active_from_1."_DAYS_TO_BACK";
		global $$date_active_from_days_to_back;
	}
	if (in_array("DATE_ACTIVE_TO", $arParams["FIELD_CODE"]))
	{
		$date_active_to_1 = $FILTER_NAME."_DATE_ACTIVE_TO_1";
		$date_active_to_2 = $FILTER_NAME."_DATE_ACTIVE_TO_2";
		$date_active_to_days_to_back = $date_active_to_1."_DAYS_TO_BACK";
		global $$date_active_to_days_to_back;
	}
	if (in_array("DATE_CREATE", $arParams["FIELD_CODE"]))
	{
		$date_create_1 = $FILTER_NAME."_DATE_CREATE_1";
		$date_create_2 = $FILTER_NAME."_DATE_CREATE_2";
		$date_create_days_to_back = $date_create_1."_DAYS_TO_BACK";
		global $$date_create_days_to_back;
	}

	if (strlen($_REQUEST["set_filter"])>0)
	{
		$arrPFV = $_REQUEST[$FILTER_NAME."_pf"];
		$arrCFV = $_REQUEST[$FILTER_NAME."_cf"];
		$arrFFV = $_REQUEST[$FILTER_NAME."_ff"];
		if(isset($_REQUEST[$FILTER_NAME."_of"]))
			$arrOFV = $_REQUEST[$FILTER_NAME."_of"];
		if(isset($_REQUEST[$FILTER_NAME."_op"]))
			$arrOPFV = $_REQUEST[$FILTER_NAME."_op"];
		if (in_array("ACTIVE_DATE", $arParams["FIELD_CODE"]))
		{
			${$active_date_1} = $_REQUEST[$active_date_1];
			${$active_date_2} = $_REQUEST[$active_date_2];
			${$active_date_days_to_back} = $_REQUEST[$active_date_days_to_back];
			if (strlen(${$active_date_days_to_back})>0)
				${$active_date_1} = GetTime(time()-86400*intval(${$active_date_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))
		{
			${$date_active_from_1} = $_REQUEST[$date_active_from_1];
			${$date_active_from_2} = $_REQUEST[$date_active_from_2];
			${$date_active_from_days_to_back} = $_REQUEST[$date_active_from_days_to_back];
			if (strlen(${$date_active_from_days_to_back})>0)
				${$date_active_from_1} = GetTime(time()-86400*intval(${$date_active_from_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_TO", $arParams["FIELD_CODE"]))
		{
			${$date_active_to_1} = $_REQUEST[$date_active_to_1];
			${$date_active_to_2} = $_REQUEST[$date_active_to_2];
			${$date_active_to_days_to_back} = $_REQUEST[$date_active_to_days_to_back];
			if (strlen(${$date_active_to_days_to_back})>0)
				${$date_active_to_1} = GetTime(time()-86400*intval(${$date_active_to_days_to_back}));
		}
		if (in_array("DATE_CREATE", $arParams["FIELD_CODE"]))
		{
			${$date_create_1} = $_REQUEST[$date_create_1];
			${$date_create_2} = $_REQUEST[$date_create_2];
			${$date_create_days_to_back} = $_REQUEST[$date_create_days_to_back];
			if (strlen(${$date_create_days_to_back})>0)
				${$date_create_1} = GetTime(time()-86400*intval(${$date_create_days_to_back}));
		}

		if ($arParams["SAVE_IN_SESSION"])
		{
			$_SESSION[$FILTER_NAME."arrPFV"] = $arrPFV;
			$_SESSION[$FILTER_NAME."arrCFV"] = $arrCFV;
			$_SESSION[$FILTER_NAME."arrFFV"] = $arrFFV;
			if(isset($_SESSION[$FILTER_NAME."arrOFV"]))
				$arrOFV = $_SESSION[$FILTER_NAME."arrOFV"];
			if(isset($_SESSION[$FILTER_NAME."arrOPFV"]))
				$arrOPFV = $_SESSION[$FILTER_NAME."arrOPFV"];
			if (in_array("ACTIVE_DATE", $arParams["FIELD_CODE"]))
			{
				$_SESSION[$active_date_1] = $_REQUEST[$active_date_1];
				$_SESSION[$active_date_2] = $_REQUEST[$active_date_2];
				$_SESSION[$active_date_days_to_back] = $_REQUEST[$active_date_days_to_back];
			}
			if (in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))
			{
				$_SESSION[$date_active_from_1] = $_REQUEST[$date_active_from_1];
				$_SESSION[$date_active_from_2] = $_REQUEST[$date_active_from_2];
				$_SESSION[$date_active_from_days_to_back] = $_REQUEST[$date_active_from_days_to_back];
			}
			if (in_array("DATE_ACTIVE_TO", $arParams["FIELD_CODE"]))
			{
				$_SESSION[$date_active_to_1] = $_REQUEST[$date_active_to_1];
				$_SESSION[$date_active_to_2] = $_REQUEST[$date_active_to_2];
				$_SESSION[$date_active_to_days_to_back] = $_REQUEST[$date_active_to_days_to_back];
			}
			if (in_array("DATE_CREATE", $arParams["FIELD_CODE"]))
			{
				$_SESSION[$date_create_1] = $_REQUEST[$date_create_1];
				$_SESSION[$date_create_2] = $_REQUEST[$date_create_2];
				$_SESSION[$date_create_days_to_back] = $_REQUEST[$date_create_days_to_back];
			}
		}
	}
	elseif ($arParams["SAVE_IN_SESSION"])
	{
		$arrPFV = $_SESSION[$FILTER_NAME."arrPFV"];
		$arrCFV = $_SESSION[$FILTER_NAME."arrCFV"];
		$arrFFV = $_SESSION[$FILTER_NAME."arrFFV"];
		if(isset($_SESSION[$FILTER_NAME."arrOFV"]))
			$arrOFV = $_SESSION[$FILTER_NAME."arrOFV"];
		if(isset($_SESSION[$FILTER_NAME."arrOPFV"]))
			$arrOPFV = $_SESSION[$FILTER_NAME."arrOPFV"];
		if (in_array("ACTIVE_DATE", $arParams["FIELD_CODE"]))
		{
			${$active_date_1} = $_SESSION[$active_date_1];
			${$active_date_2} = $_SESSION[$active_date_2];
			${$active_date_days_to_back} = $_SESSION[$active_date_days_to_back];
			if (strlen(${$active_date_days_to_back})>0)
				${$active_date_1} = GetTime(time()-86400*intval(${$active_date_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))
		{
			${$date_active_from_1} = $_SESSION[$date_active_from_1];
			${$date_active_from_2} = $_SESSION[$date_active_from_2];
			${$date_active_from_days_to_back} = $_SESSION[$date_active_from_days_to_back];
			if (strlen(${$date_active_from_days_to_back})>0)
				${$date_active_from_1} = GetTime(time()-86400*intval(${$date_active_from_days_to_back}));
		}
		if (in_array("DATE_ACTIVE_TO", $arParams["FIELD_CODE"]))
		{
			${$date_active_to_1} = $_SESSION[$date_active_to_1];
			${$date_active_to_2} = $_SESSION[$date_active_to_2];
			${$date_active_to_days_to_back} = $_SESSION[$date_active_to_days_to_back];
			if (strlen(${$date_active_to_days_to_back})>0)
				${$date_active_to_1} = GetTime(time()-86400*intval(${$date_active_to_days_to_back}));
		}
		if (in_array("DATE_CREATE", $arParams["FIELD_CODE"]))
		{
			${$date_create_1} = $_SESSION[$date_create_1];
			${$date_create_2} = $_SESSION[$date_create_2];
			${$date_create_days_to_back} = $_SESSION[$date_create_days_to_back];
			if (strlen(${$date_create_days_to_back})>0)
				${$date_create_1} = GetTime(time()-86400*intval(${$date_create_days_to_back}));
		}
	}
	if (strlen($_REQUEST["del_filter"])>0)
	{
		$arrPFV = array();
		$arrCFV = array();
		$arrFFV = array();
		$arrODFV = array();
		$arrOPFV = array();
		if (in_array("ACTIVE_DATE", $arParams["FIELD_CODE"]))
		{
			${$active_date_1} = "";
			${$active_date_2} = "";
			${$active_date_days_to_back} = "";
		}
		if (in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))
		{
			${$date_active_from_1} = "";
			${$date_active_from_2} = "";
			${$date_active_from_days_to_back} = "";
		}
		if (in_array("DATE_ACTIVE_TO", $arParams["FIELD_CODE"]))
		{
			${$date_active_to_1} = "";
			${$date_active_to_2} = "";
			${$date_active_to_days_to_back} = "";
		}
		if (in_array("DATE_CREATE", $arParams["FIELD_CODE"]))
		{
			${$date_create_1} = "";
			${$date_create_2} = "";
			${$date_create_days_to_back} = "";
		}
		if ($arParams["SAVE_IN_SESSION"])
		{
			unset($_SESSION[$FILTER_NAME."arrPFV"]);
			unset($_SESSION[$FILTER_NAME."arrCFV"]);
			unset($_SESSION[$FILTER_NAME."arrFFV"]);
			if (in_array("ACTIVE_DATE", $arParams["FIELD_CODE"]))
			{
				unset($_SESSION[$active_date_1]);
				unset($_SESSION[$active_date_2]);
				unset($_SESSION[$active_date_days_to_back]);
			}
			if (in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))
			{
				unset($_SESSION[$date_active_from_1]);
				unset($_SESSION[$date_active_from_2]);
				unset($_SESSION[$date_active_from_days_to_back]);
			}
			if (in_array("DATE_ACTIVE_TO", $arParams["FIELD_CODE"]))
			{
				unset($_SESSION[$date_active_to_1]);
				unset($_SESSION[$date_active_to_2]);
				unset($_SESSION[$date_active_to_days_to_back]);
			}
			if (in_array("DATE_CREATE", $arParams["FIELD_CODE"]))
			{
				unset($_SESSION[$date_create_1]);
				unset($_SESSION[$date_create_2]);
				unset($_SESSION[$date_create_days_to_back]);
			}
		}
	}

/*************************************************************************
			Work with cache
*************************************************************************/
$obCache = new CPHPCache;

if(
	$arParams["CACHE_TYPE"] == "N"
	|| (
		$arParams["CACHE_TYPE"] == "A"
		&& COption::GetOptionString("main", "component_cache_on", "Y") == "N"
	)
)
	$arParams["CACHE_TIME"] = 0;

if($obCache->StartDataCache($arParams["CACHE_TIME"], $this->GetCacheID(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())), "/".SITE_ID.$this->GetRelativePath()))
{
	$arResult["arrProp"] = array();
	$arResult["arrPrice"] = array();
	$arResult["arrSection"] = array();
	$arResult["arrOfferProp"] = array();

	// simple fields
	if (in_array("SECTION_ID", $arParams["FIELD_CODE"]))
	{
		$arResult["arrSection"][0] = GetMessage("CC_BCF_TOP_LEVEL");
		$rsSection = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y"));
		while($arSection = $rsSection->Fetch())
		{
			$arResult["arrSection"][$arSection["ID"]] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]).$arSection["NAME"];
		}
	}

	// prices
	if(CModule::IncludeModule("catalog"))
	{
		$rsPrice = CCatalogGroup::GetList($v1, $v2);
		while($arPrice = $rsPrice->Fetch())
		{
			if(($arPrice["CAN_ACCESS"] == "Y" || $arPrice["CAN_BUY"] == "Y") && in_array($arPrice["NAME"],$arParams["PRICE_CODE"]))
				$arResult["arrPrice"][$arPrice["NAME"]] = array("ID"=>$arPrice["ID"], "TITLE"=>$arPrice["NAME_LANG"]);
		}
	}
	else
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
		while($arProp = $rsProp->Fetch())
		{
			if(in_array($arProp["CODE"],$arParams["PRICE_CODE"]) && in_array($arProp["PROPERTY_TYPE"], array("N")))
				$arResult["arrPrice"][$arProp["CODE"]] = array("ID"=>$arProp["ID"], "TITLE"=>$arProp["NAME"]);
		}
	}
	// properties
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
	while ($arProp = $rsProp->Fetch())
	{
		if(in_array($arProp["CODE"],$arParams["PROPERTY_CODE"]) && $arProp["PROPERTY_TYPE"] != "F")
		{
			$arResult["arrProp"][$arProp["ID"]]["CODE"] = $arProp["CODE"];
			$arResult["arrProp"][$arProp["ID"]]["NAME"] = $arProp["NAME"];
			$arResult["arrProp"][$arProp["ID"]]["PROPERTY_TYPE"] = $arProp["PROPERTY_TYPE"];
			if ($arProp["MULTIPLE"]=="Y") $arResult["arrProp"][$arProp["ID"]]["MULTIPLE"] = $arProp["MULTIPLE"];
			if ($arProp["PROPERTY_TYPE"]=="L")
			{
				$arrEnum = array();
				$rsEnum = CIBlockProperty::GetPropertyEnum($arProp["ID"]);
				while($arEnum = $rsEnum->Fetch())
				{
					$arrEnum[$arEnum["ID"]] = $arEnum["VALUE"];
				}
				$arResult["arrProp"][$arProp["ID"]]["VALUE_LIST"] = $arrEnum;
			}
		}
	}
	// offer properties
	$arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
	if(is_array($arOffersIBlock))
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arOffersIBlock["OFFERS_IBLOCK_ID"]));
		while ($arProp = $rsProp->Fetch())
		{
			if(in_array($arProp["CODE"], $arParams["OFFERS_PROPERTY_CODE"]) && $arProp["PROPERTY_TYPE"] != "F")
			{
				$arTemp = array(
					"CODE" => $arProp["CODE"],
					"NAME" => $arProp["NAME"],
					"PROPERTY_TYPE" => $arProp["PROPERTY_TYPE"],
					"MULTIPLE" => $arProp["MULTIPLE"],
				);
				if ($arProp["PROPERTY_TYPE"]=="L")
				{
					$arrEnum = array();
					$rsEnum = CIBlockProperty::GetPropertyEnum($arProp["ID"]);
					while($arEnum = $rsEnum->Fetch())
					{
						$arrEnum[$arEnum["ID"]] = $arEnum["VALUE"];
					}
					$arTemp["VALUE_LIST"] = $arrEnum;
				}
				$arResult["arrOfferProp"][$arProp["ID"]] = $arTemp;
			}
		}
	}
	$obCache->EndDataCache($arResult);
}
else
{
	$arResult = $obCache->GetVars();
}

$arResult["FORM_ACTION"] = isset($_SERVER['REQUEST_URI'])? htmlspecialchars($_SERVER['REQUEST_URI']): "";
$arResult["FILTER_NAME"] = $FILTER_NAME;

/*************************************************************************
		Adding the titles and input fields
*************************************************************************/

$arResult["arrInputNames"] = array(); // array of the input field names; is being used in the function $APPLICATION->GetCurPageParam

// simple fields
$arResult["ITEMS"] = array();


foreach($arResult["arrPrice"] as $price_code => $arPrice)
{
	$res_price = "";
	$arResult["arrInputNames"][$FILTER_NAME."_cf"]=true;

	$name = $FILTER_NAME."_cf[".$arPrice["ID"]."][LEFT]";
	$value = $arrCFV[$arPrice["ID"]]["LEFT"];

	if (strlen($value)>0)
	{
		if(CModule::IncludeModule("catalog"))
			${$FILTER_NAME}[">=CATALOG_PRICE_".$arPrice["ID"]] = $value;
		else
			${$FILTER_NAME}[">=PROPERTY_".$arPrice["ID"]] = $value;
	}

	$res_price .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />&nbsp;'.GetMessage("CC_BCF_TILL").'&nbsp;';

	$name = $FILTER_NAME."_cf[".$arPrice["ID"]."][RIGHT]";
	$value = $arrCFV[$arPrice["ID"]]["RIGHT"];

	if (strlen($value)>0)
	{
		if(CModule::IncludeModule("catalog"))
			${$FILTER_NAME}["<=CATALOG_PRICE_".$arPrice["ID"]] = $value;
		else
			${$FILTER_NAME}["<=PROPERTY_".$arPrice["ID"]] = $value;
	}

	$res_price .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

	$arResult["ITEMS"][] = array(
		"NAME" => htmlspecialchars(count($arResult["arrPrice"]) > 1 ? $arPrice["TITLE"] : GetMessage('CC_BCF_PRICE')),
		"INPUT" => $res_price,
		"TYPE" => "interval"
	);
}

foreach($arParams["FIELD_CODE"] as $field_code)
{
	$field_res = "";
	$controlType = "";
	$arResult["arrInputNames"][$FILTER_NAME."_ff"]=true;
	$name = $FILTER_NAME."_ff[".$field_code."]";
	$value = $arrFFV[$field_code];
	switch ($field_code)
	{
		case "CODE":
		case "XML_ID":
		case "NAME":
		case "PREVIEW_TEXT":
		case "DETAIL_TEXT":
		case "IBLOCK_TYPE_ID":
		case "IBLOCK_ID":
		case "IBLOCK_CODE":
		case "IBLOCK_NAME":
		case "IBLOCK_EXTERNAL_ID":
		case "SEARCHABLE_CONTENT":
			$controlType = "textbox";
			$field_res = '<input type="text" name="'.$name.'" size="'.$arParams["TEXT_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["?".$field_code] = $value;

			break;
		case "ID":
		case "SORT":
		case "SHOW_COUNTER":
			
			$controlType = "interval";
			$name = $FILTER_NAME."_ff[".$field_code."][LEFT]";
			$value = $arrFFV[$field_code]["LEFT"];
			$field_res = '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />&nbsp;'.GetMessage("CC_BCF_TILL").'&nbsp;';

			if(strlen($value)>0)
				${$FILTER_NAME}[">=".$field_code] = intval($value);

			$name = $FILTER_NAME."_ff[".$field_code."][RIGHT]";
			$value = $arrFFV[$field_code]["RIGHT"];
			$field_res .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if(strlen($value)>0)
				${$FILTER_NAME}["<=".$field_code] = intval($value);

			break;
		case "SECTION_ID":
			
			$controlType = "dropdown";
			$arrRef = array("reference" => array_values($arResult["arrSection"]), "reference_id" => array_keys($arResult["arrSection"]));
			$field_res = SelectBoxFromArray($name, $arrRef, $value, " ", "");

			if ($value!="NOT_REF" && strlen($value)>0)
				${$FILTER_NAME}[$field_code] = intval($value);

			$_name = $FILTER_NAME."_ff[INCLUDE_SUBSECTIONS]";
			$_value = $arrFFV["INCLUDE_SUBSECTIONS"];
			$field_res .= "<br>".InputType("checkbox", $_name, "Y", $_value, false, "", "")."&nbsp;".GetMessage("CC_BCF_INCLUDE_SUBSECTIONS");

			if (strlen($value)>0 && $_value=="Y") ${$FILTER_NAME}["INCLUDE_SUBSECTIONS"] = "Y";

			break;

		case "ACTIVE_DATE":

			$controlType = "interval";
			$arResult["arrInputNames"][$FILTER_NAME."_ACTIVE_DATE_1"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_ACTIVE_DATE_2"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_ACTIVE_DATE_1_DAYS_TO_BACK"]=true;

			$field_res = CalendarPeriod($active_date_1, ${$active_date_1}, $active_date_2, ${$active_date_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

			if (strlen(${$active_date_1})>0)
				${$FILTER_NAME}[">=DATE_ACTIVE_FROM"] = ${$active_date_1};

			if (strlen(${$active_date_2})>0)
				${$FILTER_NAME}["<=DATE_ACTIVE_TO"] = ${$active_date_2};

			break;

		case "DATE_ACTIVE_FROM":

			$controlType = "interval";
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_ACTIVE_FROM_1"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_ACTIVE_FROM_2"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_ACTIVE_FROM_1_DAYS_TO_BACK"]=true;

			$field_res = CalendarPeriod($date_active_from_1, ${$date_active_from_1}, $date_active_from_2, ${$date_active_from_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

			if (strlen(${$date_active_from_1})>0)
				${$FILTER_NAME}[">=".$field_code] = ${$date_active_from_1};

			if (strlen(${$date_active_from_2})>0)
				${$FILTER_NAME}["<=".$field_code] = ${$date_active_from_2};

			break;

		case "DATE_ACTIVE_TO":

			$controlType = "interval";
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_ACTIVE_TO_1"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_ACTIVE_TO_2"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_ACTIVE_TO_1_DAYS_TO_BACK"]=true;

			$field_res = CalendarPeriod($date_active_to_1, ${$date_active_to_1}, $date_active_to_2, ${$date_active_to_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

			if (strlen(${$date_active_to_1})>0)
				${$FILTER_NAME}[">=".$field_code] = ${$date_active_to_1};

			if (strlen(${$date_active_to_2})>0)
				${$FILTER_NAME}["<=".$field_code] = ${$date_active_to_2};

			break;

		case "DATE_CREATE":

			$controlType = "interval";
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_CREATE_1"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_CREATE_2"]=true;
			$arResult["arrInputNames"][$FILTER_NAME."_DATE_CREATE_1_DAYS_TO_BACK"]=true;

			$field_res = CalendarPeriod($date_create_1, ${$date_create_1}, $date_create_2, ${$date_create_2}, $FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\"");

			if (strlen(${$date_create_1})>0)
				${$FILTER_NAME}[">=".$field_code] = ${$date_create_1};

			if (strlen(${$date_create_2})>0)
				${$FILTER_NAME}["<=".$field_code] = ${$date_create_2};

			break;
	}
	if($field_res)
		$arResult["ITEMS"][] = array(
			"NAME" => htmlspecialchars(GetMessage("IBLOCK_FIELD_".$field_code)),
			"INPUT" => $field_res,
			"INPUT_NAME" => $name,
			"INPUT_VALUE" => htmlspecialchars($value),
			"~INPUT_VALUE" => $value,
			"TYPE" => $controlType
		);
}

foreach($arResult["arrProp"] as $prop_id=>$arProp)
{
	$value = $arrPFV[$arProp["CODE"]];
	if(is_array($value))
	{
		$arValue = $value;
		list(,$strValue) = each($value);
	}
	else
	{
		if(strlen($value))
			$arValue = array($value);
		else
			$arValue = array();
		$strValue = $value;
	}

	$res = "";
	$controlType = "";
	$arResult["arrInputNames"][$FILTER_NAME."_pf"]=true;
	switch ($arProp["PROPERTY_TYPE"])
	{
		case "L":

			$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";


			if ($arProp['MULTIPLE'] == 'Y' || count($arProp["VALUE_LIST"]) == 1)
			{
				foreach($arProp["VALUE_LIST"] as $key=>$val)
				{
					$controlType = "checkbox";
					$prop_input_id = htmlspecialchars($FILTER_NAME."_pf_".$arProp["CODE"]."_".$key);
					$res .= '<input type="checkbox" name="'.$name.'[]" value="'.htmlspecialchars($key).'" id="'.$prop_input_id.'"'.(in_array($key, $arValue) ? ' checked="checked"' : '').' />';
					$res .= '<label for="'.$prop_input_id.'">'.htmlspecialchars($val).'</label><br />';
				}
			}
			else
			{
				$controlType = "dropdown";
				if ($arProp["MULTIPLE"]=="Y")
					$res .= '<select multiple name="'.$name.'[]" size="'.$arParams["LIST_HEIGHT"].'">';
				else
					$res .= '<select name="'.$name.'">';
				$res .= '<option value="">'.GetMessage("CC_BCF_ALL").'</option>';
				foreach($arProp["VALUE_LIST"] as $key=>$val)
				{
					$res .= '<option';

					if ($arProp["MULTIPLE"] == "Y")
					{
						if(in_array($key, $arValue))
							$res .= ' selected';
					}
					else
					{
						if($key == $strValue)
							$res .= ' selected';
					}

					$res .= ' value="'.htmlspecialchars($key).'">'.htmlspecialchars($val).'</option>';
				}
				$res .= '</select>';
			}

			if ($arProp["MULTIPLE"]=="Y")
			{
				if (count($arValue))
					${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $arValue;
			}
			else
			{
				if (strlen($strValue))
					${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $strValue;
			}
			break;

		case "N":

			$controlType = "interval";
			$name = $FILTER_NAME."_pf[".$arProp["CODE"]."][LEFT]";
			$value = $arrPFV[$arProp["CODE"]]["LEFT"];
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />&nbsp;'.GetMessage("CC_BCF_TILL").'&nbsp;';

			if (strlen($value)>0)
				${$FILTER_NAME}["PROPERTY"][">=".$arProp["CODE"]] = intval($value);

			$name = $FILTER_NAME."_pf[".$arProp["CODE"]."][RIGHT]";
			$value = $arrPFV[$arProp["CODE"]]["RIGHT"];
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["PROPERTY"]["<=".$arProp["CODE"]] = doubleval($value);

			break;

		case "S":
		case "E":
		case "G":

			$controlType = "textbox";
			$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["TEXT_WIDTH"].'" value="'.htmlspecialchars($strValue).'" />';

			if (strlen($strValue))
				${$FILTER_NAME}["PROPERTY"]["?".$arProp["CODE"]] = $strValue;

			break;
	}
	if($res)
		$arResult["ITEMS"][] = array(
			"NAME" => htmlspecialcharsEx($arProp["NAME"]),
			"INPUT" => $res,
			"INPUT_NAME" => $name,
			"INPUT_VALUE" => htmlspecialcharsEx($value),
			"~INPUT_VALUE" => $value,
			"TYPE" => $controlType
 		);
}

$bHasOffersFilter = false;
foreach($arParams["OFFERS_FIELD_CODE"] as $field_code)
{
	$field_res = "";
	$arResult["arrInputNames"][$FILTER_NAME."_of"]=true;
	$name = $FILTER_NAME."_of[".$field_code."]";
	$value = $arrOFV[$field_code];
	switch ($field_code)
	{
		case "CODE":
		case "XML_ID":
		case "NAME":
		case "PREVIEW_TEXT":
		case "DETAIL_TEXT":
		case "IBLOCK_TYPE_ID":
		case "IBLOCK_ID":
		case "IBLOCK_CODE":
		case "IBLOCK_NAME":
		case "IBLOCK_EXTERNAL_ID":
		case "SEARCHABLE_CONTENT":
			$field_res = '<input type="text" name="'.$name.'" size="'.$arParams["TEXT_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["OFFERS"]["?".$field_code] = $value;

			break;
		case "ID":
		case "SORT":
		case "SHOW_COUNTER":
			$name = $FILTER_NAME."_of[".$field_code."][LEFT]";
			$value = $arrOFV[$field_code]["LEFT"];
			$field_res = '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />&nbsp;'.GetMessage("CC_BCF_TILL").'&nbsp;';

			if(strlen($value)>0)
				${$FILTER_NAME}["OFFERS"][">=".$field_code] = intval($value);

			$name = $FILTER_NAME."_of[".$field_code."][RIGHT]";
			$value = $arrOFV[$field_code]["RIGHT"];
			$field_res .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if(strlen($value)>0)
				${$FILTER_NAME}["OFFERS"]["<=".$field_code] = intval($value);

			break;

		case "ACTIVE_DATE":
		case "DATE_ACTIVE_FROM":
		case "DATE_ACTIVE_TO":
		case "DATE_CREATE":
			$arDateField = $arrODFV[$field_code];
			$arResult["arrInputNames"][$arDateField["from"]["name"]]=true;
			$arResult["arrInputNames"][$arDateField["to"]["name"]]=true;
			$arResult["arrInputNames"][$arDateField["days_to_back"]["name"]]=true;

			$field_res = CalendarPeriod(
				$arDateField["from"]["name"], $arDateField["from"]["value"],
				$arDateField["to"]["name"], $arDateField["to"]["value"],
				$FILTER_NAME."_form", "Y", "class=\"inputselect\"", "class=\"inputfield\""
			);

			if(strlen($arDateField["from"]["value"]) > 0)
				${$FILTER_NAME}["OFFERS"][$arDateField["filter_from"]] = $arDateField["from"]["value"];

			if(strlen($arDateField["to"]["value"]) > 0)
				${$FILTER_NAME}["OFFERS"][$arDateField["filter_to"]] = $arDateField["to"]["value"];
			break;
	}
	if($field_res)
		$bHasOffersFilter = true;
		$arResult["ITEMS"][] = array(
			"NAME" => htmlspecialchars(GetMessage("IBLOCK_FIELD_".$field_code)),
			"INPUT" => $field_res,
			"INPUT_NAME" => $name,
			"INPUT_VALUE" => htmlspecialchars($value),
			"~INPUT_VALUE" => $value,
		);
}

foreach($arResult["arrOfferProp"] as $prop_id => $arProp)
{
	$res = "";
	$arResult["arrInputNames"][$FILTER_NAME."_op"]=true;
	switch ($arProp["PROPERTY_TYPE"])
	{
		case "L":

			$name = $FILTER_NAME."_op[".$arProp["CODE"]."]";
			$value = $arrOPFV[$arProp["CODE"]];
			if ($arProp["MULTIPLE"]=="Y")
				$res .= '<select multiple name="'.$name.'[]" size="'.$arParams["LIST_HEIGHT"].'">';
			else
				$res .= '<select name="'.$name.'">';
			$res .= '<option value="">'.GetMessage("CC_BCF_ALL").'</option>';
			foreach($arProp["VALUE_LIST"] as $key=>$val)
			{
				$res .= '<option';

				if (($arProp["MULTIPLE"] == "Y") && is_array($value))
				{
					if(in_array($key, $value))
						$res .= ' selected';
				}
				else
				{
					if($key == $value)
						$res .= ' selected';
				}

				$res .= ' value="'.htmlspecialchars($key).'">'.htmlspecialchars($val).'</option>';
			}
			$res .= '</select>';

			if ($arProp["MULTIPLE"]=="Y")
			{
				if (is_array($value) && count($value)>0)
					${$FILTER_NAME}["OFFERS"]["PROPERTY"][$arProp["CODE"]] = $value;
			}
			else
			{
				if (strlen($value)>0)
					${$FILTER_NAME}["OFFERS"]["PROPERTY"][$arProp["CODE"]] = $value;
			}
			break;

		case "N":

			$name = $FILTER_NAME."_op[".$arProp["CODE"]."][LEFT]";
			$value = $arrOPFV[$arProp["CODE"]]["LEFT"];
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />&nbsp;'.GetMessage("CC_BCF_TILL").'&nbsp;';

			if (strlen($value)>0)
				${$FILTER_NAME}["OFFERS"]["PROPERTY"][">=".$arProp["CODE"]] = intval($value);

			$name = $FILTER_NAME."_op[".$arProp["CODE"]."][RIGHT]";
			$value = $arrOPFV[$arProp["CODE"]]["RIGHT"];
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["OFFERS"]["PROPERTY"]["<=".$arProp["CODE"]] = doubleval($value);

			break;

		case "S":
		case "E":
		case "G":

			$name = $FILTER_NAME."_op[".$arProp["CODE"]."]";
			$value = $arrOPFV[$arProp["CODE"]];
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["TEXT_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["OFFERS"]["PROPERTY"]["?".$arProp["CODE"]] = $value;

			break;
	}
	if($res)
	{
		$bHasOffersFilter = true;
		$arResult["ITEMS"][] = array(
			"NAME" => htmlspecialchars($arProp["NAME"]),
			"INPUT" => $res,
			"INPUT_NAME" => $name,
			"INPUT_VALUE" => htmlspecialchars($value),
			"~INPUT_VALUE" => $value,
 		);
	}
}

if($bHasOffersFilter)
{
	//This will force to use catalog.section offers price filter
	if(!isset(${$FILTER_NAME}["OFFERS"]))
		${$FILTER_NAME}["OFFERS"] = array();
}

$arResult["arrInputNames"]["set_filter"]=true;
$arResult["arrInputNames"]["del_filter"]=true;

$arSkip = array(
	"AUTH_FORM" => true,
	"TYPE" => true,
	"USER_LOGIN" => true,
	"USER_CHECKWORD" => true,
	"USER_PASSWORD" => true,
	"USER_CONFIRM_PASSWORD" => true,
	"USER_EMAIL" => true,
	"captcha_word" => true,
	"captcha_sid" => true,
	"login" => true,
	"Login" => true,
	"backurl" => true,
);

foreach(array_merge($_GET, $_POST) as $key=>$value)
{
	if(
		!array_key_exists($key, $arResult["arrInputNames"])
		&& !array_key_exists($key, $arSkip)
	)
	{
		$arResult["ITEMS"][] = array(
			"HIDDEN" => true,
			"INPUT" => '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'" />',
		);
	}
}

$arResult['IS_FILTERED'] = count($arrPFV) > 0 || count($arrCFV) > 0 || count($arrFFV) > 0;

//echo "<pre>",htmlspecialchars(print_r($arResult["ITEMS"],true)),"</pre>";
$this->IncludeComponentTemplate();

?>
