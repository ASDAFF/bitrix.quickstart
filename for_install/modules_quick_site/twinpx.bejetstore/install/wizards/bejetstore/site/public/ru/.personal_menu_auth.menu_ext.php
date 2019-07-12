<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $USER, $APPLICATION;
$aMenuLinksExt = array();
$arAddMenuLinks = array();
if($USER->IsAuthorized()){
	$aMenuLinksExt[] = array(
					"<b>".($USER->GetFullName() ? $USER->GetFullName() : $USER->GetLogin())."</b>",
					SITE_DIR."personal/",
					array(),
					array(),
					""
				);

	$arAddMenuLinks[] = array(
		"Выход",
		$APPLICATION->GetCurPageParam("logout=yes", array(
	     "login",
	     "logout",
	     "register",
	     "forgot_password",
	     "change_password")),
		array(),
		array(),
		""		
	);
}

$aMenuLinks = array_merge($aMenuLinksExt, $aMenuLinks);
$aMenuLinks = array_merge($aMenuLinks, $arAddMenuLinks);
?>