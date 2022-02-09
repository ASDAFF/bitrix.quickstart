<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!$arResult["CurrentUserPerms"]["Operations"]["viewprofile"])
	$arResult["FatalError"] = GetMessage("SONET_P_USER_ACCESS_DENIED");
?>