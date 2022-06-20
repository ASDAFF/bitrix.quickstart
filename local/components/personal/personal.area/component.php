<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

global $USER;
if (!$USER->IsAuthorized())
{
    LocalRedirect(SITE_DIR);
}


$arDefaultUrlTemplates404 = array(
   "list" => "/",
   "detail" => "/#ELEMENT_ID#/"
);

$navigation = 
	"<div class=\"personal_navigation\">
			<div class=\"tabs-menu\">".
			"<a".(($_REQUEST["page"] == "" || $_REQUEST["page"] == "personal") ? " class=\"active\"" : "")." href=\"".$APPLICATION->GetCurPageParam("page=personal", array("page", "ID", "PID"))."\">".GetMessage("PERSONAL_INFO_LINK_TEXT")."</a>".
			(($arParams["ORDERS"] == "Y") ? "<a".(($_REQUEST["page"] == "orders") ? " class=\"active\"" : "")." href=\"".$APPLICATION->GetCurPageParam("page=orders", array("page", "ID", "PID"))."\">".GetMessage("ORDERS_LINK_TEXT")."</a>" : "").  
			(($arParams["USER_ADDRESES"] == "Y") ? "<a".(($_REQUEST["page"] == "addreses") ? " class=\"active\"" : "")." href=\"".$APPLICATION->GetCurPageParam("page=addreses", array("page", "ID", "PID"))."\">".GetMessage("USER_ADDRESES_LINK_TEXT")."</a>" : ""). 
			(($arParams["LOGOUT"] == "Y") ? "<a class=\"logout\" href=\"".$APPLICATION->GetCurPageParam("logout=yes", array("page", "ID", "PID"))."\">".GetMessage("LOGOUT_LINK_TEXT")."</a>" : ""). 
		"</div>
	</div>";

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();

$arComponentVariables = array("IBLOCK_ID", "ELEMENT_ID");

$arVariables = array();

$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

$componentPage = "personal";
if($_REQUEST["page"]):
	if($_REQUEST["page"] == "orders" && $_REQUEST["ID"]):
		$componentPage = "order";
	elseif($_REQUEST["page"] == "addreses" && $_REQUEST["PID"]):
		$componentPage = "address";
	else:
		$componentPage = $_REQUEST["page"];
	endif;
endif;

$arResult = array(
	"FOLDER" => $SEF_FOLDER,
	"URL_TEMPLATES" => $arUrlTemplates,
	"VARIABLES" => $arVariables,
	"ALIASES" => $arVariableAliases,
	"NAVIGATION" => $navigation
);

$this->IncludeComponentTemplate($componentPage);

?>