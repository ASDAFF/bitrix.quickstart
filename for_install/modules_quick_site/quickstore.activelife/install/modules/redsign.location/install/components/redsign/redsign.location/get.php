<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModule('sale'))
{
	if(!empty($_REQUEST['city_name']))
	{
		$arRes = $_REQUEST['city_name'];
	}
	
	if(!empty($_REQUEST['city']))
	{
		$city = $APPLICATION->ConvertCharset($_REQUEST['city'], "utf-8", SITE_CHARSET);
		$dbVariants = CSaleLocation::GetList(
						array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
						array("LID" => LANGUAGE_ID),
						false,
						false,
						array("ID", "COUNTRY_ID" ,"COUNTRY_NAME", "REGION_ID", "REGION_NAME", "CITY_ID", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
		);
		
		$arRes = array();
		while ($arVariants = $dbVariants->GetNext())
		{
			if($arVariants["CITY_NAME"] == $city)
			{
				$arRes["city_id"] = $arVariants["ID"];
				$arRes["country_id"] = $arVariants["COUNTRY_ID"];
				$arRes["region_id"] = $arVariants["REGION_ID"];
			}
		}
	}
	
	if(!empty($_REQUEST['all_name']))
	{
		$city = $APPLICATION->ConvertCharset($_REQUEST['all_name'], "utf-8", SITE_CHARSET);
		$dbVariants = CSaleLocation::GetList(
						array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
						array("LID" => LANGUAGE_ID),
						false,
						false,
						array("ID", "COUNTRY_ID" ,"COUNTRY_NAME", "REGION_ID", "REGION_NAME", "CITY_ID", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
		);
		
		$arRes = array();
		while ($arVariants = $dbVariants->GetNext())
		{

			if($arVariants["CITY_NAME"] == $city)
			{
				$arRes["city_name"] = $arVariants["CITY_NAME"];
				$arRes["country_name"] = $arVariants["COUNTRY_NAME"];
				$arRes["region_name"] = $arVariants["REGION_NAME"];
			}
		}
	}
	
	if(!empty($_REQUEST['props']))
	{
		$dbProperties = CSaleOrderProps::GetList(
			array(
					"GROUP_SORT" => "ASC",
					"PROPS_GROUP_ID" => "ASC",
					"USER_PROPS" => "ASC",
					"SORT" => "ASC",
					"NAME" => "ASC"
				),
			$arFilter,
			false,
			false,
			array("ID","PERSON_TYPE_ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "GROUP_NAME", "GROUP_SORT", "SORT", "USER_PROPS", "IS_ZIP", "INPUT_FIELD_LOCATION")
		);
		$arRes = array();
		while ($arProperties = $dbProperties->GetNext())
		{
			if ($arProperties["TYPE"] == "LOCATION" && $arProperties["PERSON_TYPE_ID"] == 1)
			{
				$arRes['type1'] = $arProperties["ID"];
					
			}elseif ($arProperties["TYPE"] == "LOCATION" && $arProperties["PERSON_TYPE_ID"] == 2){
			
				$arRes['type2'] = $arProperties["ID"];
			}
		}
	}
	
	if (SITE_CHARSET != "utf-8")
	{
		$data = $APPLICATION->ConvertCharsetArray($arRes , SITE_CHARSET, "utf-8");
		$json_str_utf = json_encode($data);
		$json_str = $APPLICATION->ConvertCharset($json_str_utf, "utf-8", SITE_CHARSET);
		echo $json_str;
	} else {
		echo json_encode( $arRes );
	}
}
?>