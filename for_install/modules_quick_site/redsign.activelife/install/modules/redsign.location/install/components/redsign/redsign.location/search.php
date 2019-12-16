<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$arResult = array();
global $APPLICATION;
if(CModule::IncludeModule("sale"))
{
	if(!empty($_REQUEST["query"]) && is_string($_REQUEST["query"]))
	{	
		$query = $APPLICATION->ConvertCharset($_REQUEST["query"], "UTF-8",LANG_CHARSET);
		$search = $APPLICATION->UnJSEscape($query);

		$rsLocationsList = CSaleLocation::GetList(
			array(
				"CITY_NAME_LANG" => "ASC",
				"COUNTRY_NAME_LANG" => "ASC",
				"SORT" => "ASC",
			),
			array(
				"~CITY_NAME" => $search."%",
				"LID" => LANGUAGE_ID,
			),
			false,
			array("nTopCount" => 10),
			array(
				"ID", "CITY_ID", "CITY_NAME", "COUNTRY_NAME_LANG", "REGION_NAME_LANG"
			)
		);
		while ($arCity = $rsLocationsList->GetNext())
		{
			$arResult[] = array(
				"ID" => $arCity["ID"],
				"CITY_ID" => $arCity["CITY_ID"],
				"NAME" => $arCity["CITY_NAME"],
				"REGION_NAME" => $arCity["REGION_NAME_LANG"],
				"COUNTRY_NAME" => $arCity["COUNTRY_NAME_LANG"],
			);
		}
	}
}

if (SITE_CHARSET != "utf-8")
{
	$data = $APPLICATION->ConvertCharsetArray($arResult , SITE_CHARSET, "utf-8");
	$json_str_utf = json_encode($data);
	$json_str = $APPLICATION->ConvertCharset($json_str_utf, "utf-8", SITE_CHARSET);
	echo $json_str;
} else {
	echo json_encode( $arResult );
}

require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();

?>