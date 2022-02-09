<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (
	(
		$GLOBALS["USER"]->IsAuthorized() 
		|| $arParams["AUTH"] == "Y" 
		|| $arParams["SUBSCRIBE_ONLY"] != "Y"
	)
	&& $arParams["SHOW_EVENT_ID_FILTER"] == "Y"
)
{
	__IncludeLang(dirname(__FILE__)."/lang/".LANGUAGE_ID."/result_modifier.php");	

	$arResult["DATE_FILTER"] = array(
		"" => GetMessage("SONET_C30_DATE_FILTER_NO_NO_NO_1"),
		"today" => GetMessage("SONET_C30_DATE_FILTER_TODAY"),
		"yesterday" => GetMessage("SONET_C30_DATE_FILTER_YESTERDAY"),
		"week" => GetMessage("SONET_C30_DATE_FILTER_WEEK"),
		"week_ago" => GetMessage("SONET_C30_DATE_FILTER_WEEK_AGO"),
		"month" => GetMessage("SONET_C30_DATE_FILTER_MONTH"),
		"month_ago" => GetMessage("SONET_C30_DATE_FILTER_MONTH_AGO"),
		"days" => GetMessage("SONET_C30_DATE_FILTER_LAST"),
		"exact" => GetMessage("SONET_C30_DATE_FILTER_EXACT"),
		"after" => GetMessage("SONET_C30_DATE_FILTER_LATER"),
		"before" => GetMessage("SONET_C30_DATE_FILTER_EARLIER"),
		"interval" => GetMessage("SONET_C30_DATE_FILTER_INTERVAL"),
	);
}

$arResult["flt_created_by_string"] = "";

if (strlen($_REQUEST["flt_created_by_string"]) > 0)
	$arResult["flt_created_by_string"] = $_REQUEST["flt_created_by_string"];
else
{
	if (is_array($_REQUEST["flt_created_by_id"]) && intval($_REQUEST["flt_created_by_id"][0]) > 0)
		$user_id_tmp = $_REQUEST["flt_created_by_id"][0];
	elseif(intval($_REQUEST["flt_created_by_id"]) > 0)
		$user_id_tmp = $_REQUEST["flt_created_by_id"];

	if (intval($user_id_tmp) > 0)
	{
		$rsUser = CUser::GetByID($user_id_tmp);
		if ($arUser = $rsUser->GetNext())
			$arResult["flt_created_by_string"] = CUser::FormatName($arParams["NAME_TEMPLATE"]." <#EMAIL#> [#ID#]", $arUser, ($arParams["SHOW_LOGIN"] != "N"), false);
	}
}



?>