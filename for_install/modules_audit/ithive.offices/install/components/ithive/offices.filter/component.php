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

foreach($arParams["FIELD_CODE"] as $field_code)
{
	$field_res = "";
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
			$field_res = '<input type="text" name="'.$name.'" size="'.$arParams["TEXT_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["?".$field_code] = $value;

			break;
		case "ID":
		case "SORT":
		case "SHOW_COUNTER":
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
		);
}

foreach($arResult["arrProp"] as $prop_id=>$arProp)
{
	$res = "";
	$arResult["arrInputNames"][$FILTER_NAME."_pf"]=true;
	switch ($arProp["PROPERTY_TYPE"])
	{
		case "L":

			$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";
			$value = $arrPFV[$arProp["CODE"]];
if($arProp["CODE"]!='PRODUCT_TYPE'):
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
else:

            foreach($arProp["VALUE_LIST"] as $key=>$val)
            {
                $res .= '<label><input type="checkbox" 
name="'.$name.'[]" ';

                if (($arProp["MULTIPLE"] == "Y") && is_array($value))
                {
                    if(in_array($key, $value))
                        $res .= ' checked';
                }
                else
                {
                    if($key == $value)
                        $res .= ' checked';
                }

                $res .= ' 
value="'.htmlspecialchars($key).'">'.htmlspecialchars($val).'</label>';
            }

endif;

			if ($arProp["MULTIPLE"]=="Y")
			{
				if (is_array($value) && count($value)>0)
					${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $value;
			}
			else
			{
				if (strlen($value)>0)
					${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $value;
			}
			break;

		case "N":

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

			$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";
			$value = $arrPFV[$arProp["CODE"]];
			$res .= '<input type="text" name="'.$name.'" size="'.$arParams["TEXT_WIDTH"].'" value="'.htmlspecialchars($value).'" />';

			if (strlen($value)>0)
				${$FILTER_NAME}["PROPERTY"]["?".$arProp["CODE"]] = $value;

			break;
	}
	if($res)
		$arResult["ITEMS"][] = array(
			"NAME" => htmlspecialchars($arProp["NAME"]),
			"INPUT" => $res,
			"INPUT_NAME" => $name,
			"INPUT_VALUE" => htmlspecialchars($value),
			"~INPUT_VALUE" => $value,
 		);
}

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

	$arResult["ITEMS"][] = array("NAME" => htmlspecialchars($arPrice["TITLE"]), "INPUT" => $res_price);

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

//echo "<pre>",htmlspecialchars(print_r($arResult["ITEMS"],true)),"</pre>";
$this->IncludeComponentTemplate();

?>
