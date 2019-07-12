<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BCF_MODULE_NOT_INSTALLED"));
	return;
}

$arUrlTemplates = array(
"section" => $arParams["~SEF_URL_TEMPLATES"]["section"],
"element" => $arParams["~SEF_URL_TEMPLATES"]["element"]
); 
$arVariables = array();
$page = CComponentEngine::ParseComponentPath($arParams["~SEF_FOLDER"], $arUrlTemplates, $arVariables);


/*************************************************************************
	Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

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
function sortPropEnum($a, $b)
{
	if(is_numeric($a['VALUE']) && is_numeric($b['VALUE']))
	{
		if ($a['VALUE']*1 == $b['VALUE']*1) {
		        return 0;
		    }
		    return ($a['VALUE']*1 < $b['VALUE']*1) ? -1 : 1;
	}
	else
		return strcmp($a['VALUE'], $b["VALUE"]);
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
		$rsSection = CIBlockSection::GetList(
			Array("left_margin"=>"asc"),
			Array(
				"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
				"ACTIVE"=>"Y",
			),
			false,
			Array("ID", "DEPTH_LEVEL", "NAME")
		);
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
			$arResult["arrProp"][$arProp["ID"]]['LINK_IBLOCK_ID']=$arProp['LINK_IBLOCK_ID'];
			if ($arProp["MULTIPLE"]=="Y") $arResult["arrProp"][$arProp["ID"]]["MULTIPLE"] = $arProp["MULTIPLE"];
			if ($arProp["PROPERTY_TYPE"]=="L")
			{
				$arrEnumTmp = array();
				$rsEnum = CIBlockProperty::GetPropertyEnum($arProp["ID"], array("sort"=>"asc","value"=>"asc"));
				while($arEnum = $rsEnum->Fetch())
				{
					$arrEnumTmp[]=array('VALUE'=> $arEnum["VALUE"], "ID"=>$arEnum["ID"]);					
				}	
				usort($arrEnumTmp, "sortPropEnum");
				$arrEnum=array();	
				foreach($arrEnumTmp as $enumVals)
				{
					$arrEnum[$enumVals['ID']]=$enumVals["VALUE"];
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

foreach($arResult["arrProp"] as $prop_id=>$arProp):
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
	$brand=array();			
										
	if ($arVariables['SECTION_CODE']!="") 
		$arFilter = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'],"SECTION_CODE"=>$arVariables['SECTION_CODE'], "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE"=>"Y");
	else
		$arFilter = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID'], "ACTIVE"=>"Y");
	$findBrands=array();
	$res2 = CIBlockElement::GetList(Array("PROPERTY_".strtoupper($arProp["CODE"])=>"asc"), $arFilter, array("PROPERTY_".strtoupper($arProp["CODE"])), false);
	while($ob2 = $res2->GetNext())
	{								
		if($ob2['CNT']>0 && $ob2['PROPERTY_'.strtoupper($arProp["CODE"]).'_VALUE'])
			$findBrands[]=$ob2['PROPERTY_'.strtoupper($arProp["CODE"]).'_VALUE'];								
	}
	if($findBrands)
	{		
		$valueList=array();
		$name = $FILTER_NAME."_pf[".$arProp["CODE"]."]";
		$value = $arrPFV[$arProp["CODE"]];
		switch ($arProp["PROPERTY_TYPE"])
		{
				case "L":					
					foreach($arProp["VALUE_LIST"] as $key=>$val)
					{
						if(!in_array($val, $findBrands))
							continue;
						$valueList[htmlspecialchars($key)]=htmlspecialchars($val);						
					}					
					break;
				case "E":				
					$res2 = CIBlockElement::GetList(Array("NAME"=>"asc"), $arFilter=array("ID"=>$findBrands), false, false, array("ID","NAME"));							
					while($arRes2=$res2->GetNext())
					{
						$valueList[htmlspecialchars($arRes2['ID'])]=htmlspecialchars($arRes2['NAME']);								
					}												
					break;					
				case "G":
					$res2 = CIBlockSection::GetList(Array("NAME"=>"asc"), $arFilter=array("ID"=>$findBrands), false, array("ID","NAME"));							
					while($arRes2=$res2->GetNext())
					{
						$valueList[htmlspecialchars($arRes2['ID'])]=htmlspecialchars($arRes2['NAME']);								
					}												
					break;	
				case "S":						
				case "N":	
					foreach($findBrands as $text)
					{
						$valueList[htmlspecialchars($text)]=htmlspecialchars($text);
					}					
					break;
		}
		if($valueList)
		{
				if ($arProp["CODE"]=="PRODUSER")
				{										
					$key=0;
					foreach($valueList as $val=>$text)
					{
						if ($key+1==1 || $key+1==ceil(count($brand)/2)+1)
							$res .= '<div class="item"><ul>';
						$prop_input_id = htmlspecialchars($FILTER_NAME."_pf_".$arProp["CODE"]."_".$key);
						$res .= '<li><input type="checkbox" name="'.$name.'[]" value="'.htmlspecialchars($val).'" id="'.$prop_input_id.'"'.(in_array($val, $arValue) ? ' checked="checked"' : '').' />';
						$res .= '<label for="'.$prop_input_id.'">'.htmlspecialchars($text).'</label><div class="clear"></div></li>';
						if ($key+1==ceil(count($brand)/2) || $key+1==count($brand))
							$res .= '</ul></div>';	
						$key++;
					}							
					if (is_array($value) && count($value)>0)
						${$FILTER_NAME}["PROPERTY"][$arProp["CODE"]] = $value;		
				}
				else
				{
					if ($arProp["MULTIPLE"]=="Y")
						$res .= '<select multiple name="'.$name.'[]" size="'.$arParams["LIST_HEIGHT"].'">';
					else
						$res .= '<select name="'.$name.'">';
					$res .= '<option value="">'.GetMessage("CC_BCF_ALL").'</option>';
					foreach($valueList as $val=>$text)
					{
						$res .= '<option';
						if (($arProp["MULTIPLE"] == "Y") && is_array($value))
						{
							if(in_array($val, $value))
								$res .= ' selected';
						}
						else
						{
							if($val == $value)
								$res .= ' selected';
						}
						$res .= ' value="'.htmlspecialchars($val).'">'.htmlspecialchars($text).'</option>';
					}
					$res .= '</select>';
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
				}
		}
	}
	if($res)
		$arResult["ITEMS"][] = array(
				"NAME" => htmlspecialchars($arProp["NAME"]),
				"INPUT" => $res,
				"INPUT_NAME" => $name,
				"INPUT_VALUE" => htmlspecialchars($value),
				"~INPUT_VALUE" => $value,
		);		
endforeach;

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

	$res_price .= '<div class="price_start"><label for="'.$name.'">'.GetMessage("CC_OT_TILL").'</label><div class="input"><input type="text" id="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" /></div><span>'.GetMessage("CC_RUB_TILL").'</span><div class="clear"></div></div>';

	$name = $FILTER_NAME."_cf[".$arPrice["ID"]."][RIGHT]";
	$value = $arrCFV[$arPrice["ID"]]["RIGHT"];

	if (strlen($value)>0)
	{
		if(CModule::IncludeModule("catalog"))
			${$FILTER_NAME}["<=CATALOG_PRICE_".$arPrice["ID"]] = $value;
		else
			${$FILTER_NAME}["<=PROPERTY_".$arPrice["ID"]] = $value;
	}

	$res_price .= '<div class="price_finish"><label for="'.$name.'">'.GetMessage("CC_DO_TILL").'</label><div class="input"><input id="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" type="text" name="'.$name.'" size="'.$arParams["NUMBER_WIDTH"].'" value="'.htmlspecialchars($value).'" /></div><span>'.GetMessage("CC_RUB_TILL").'</span><div class="clear"></div></div><div class="clear"></div>';								
	
	$price_id=$arPrice['ID'];
	$priceName=$arPrice['TITLE'];
	
	$arFilter=array();
	$arFilter=array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y");
        if ($arVariables['SECTION_CODE']!="") 
	{
	    $arFilter["SECTION_CODE"]=$arVariables['SECTION_CODE'];
	    $arFilter["INCLUDE_SUBSECTIONS"]="Y";
	}
	else 
	{ 
		$arFilter["INCLUDE_SUBSECTIONS"]="Y";
	}
			
	/*if ($_REQUEST[arrFilter_pf]!="")
		foreach ($_REQUEST[arrFilter_pf] as $keys=> $prop)
		{
		     if (array_key_exists("LEFT", $prop))
		         {
		             if ($prop['LEFT']==0) 
		                 {
		                     $arFilter[]=array("LOGIC" => "OR", '<=PROPERTY_'.$keys=>$prop['RIGHT'], '=PROPERTY_'.$keys=>false);
		                 }
		             else
		                {
		                     $arFilter['>=PROPERTY_'.$keys]=$prop['LEFT'];
		                     $arFilter['<=PROPERTY_'.$keys]=$prop['RIGHT'];
		                }
		          }
		     else $arFilter['PROPERTY_'.$keys]=$prop;
		 }*/		
			
	$rs=CIBlockElement::GetList(array("CATALOG_PRICE_".$price_id=>"desc"), array("ACTIVE"=>"Y", $arFilter), false, array("nTopCount"=>1), array("ID"));
	if($ar=$rs->GetNext())
	{
	   $max=(integer)($ar["CATALOG_PRICE_".$price_id]);	            
	}	
	 
	$arResult["ITEMS"][] = array(
											"NAME" => htmlspecialchars($arPrice["TITLE"]), 
											"INPUT" => $res_price, 
											"TYPE"=>"price",
											"MAX"=>$max,
											'PRICE_ID'=>$price_id,
											'PRICE_NAME'=>$priceName,
										);	

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
