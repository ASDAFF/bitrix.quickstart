<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}
if (!$USER->IsAuthorized())
{
	$APPLICATION->AuthForm(GetMessage("SALE_ACCESS_DENIED"));
}

$arParams["PATH_TO_DETAIL"] = Trim($arParams["PATH_TO_DETAIL"]);
if (strlen($arParams["PATH_TO_DETAIL"]) <= 0)
	$arParams["PATH_TO_DETAIL"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?ID=#ID#");

$arParams["PER_PAGE"] = (intval($arParams["PER_PAGE"]) <= 0 ? 20 : intval($arParams["PER_PAGE"]));
	
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if($arParams["SET_TITLE"] == 'Y')
	$APPLICATION->SetTitle(GetMessage("SPPL_DEFAULT_TITLE"));

//Delete profile
$errorMessage = "";
$del_id = IntVal($_REQUEST["del_id"]);
if ($del_id > 0 && check_bitrix_sessid())
{
	$dbUserProps = CSaleOrderUserProps::GetList(
			array(),
			array(
					"ID" => $del_id,
					"USER_ID" => IntVal($USER->GetID())
				)
		);
	if ($arUserProps = $dbUserProps->Fetch())
	{
		if (!CSaleOrderUserProps::Delete($arUserProps["ID"]))
		{
			$errorMessage = GetMessage("SALE_DEL_PROFILE");
		}
	}
	else
	{
		$errorMessage = GetMessage("SALE_NO_PROFILE");
	}
	if(strlen($errorMessage) > 0)
		LocalRedirect($APPLICATION->GetCurPageParam("del_id=".$del_id, Array("del_id", "sessid")));
	else
		LocalRedirect($APPLICATION->GetCurPageParam("success_del_id=".$del_id, Array("del_id", "sessid")));
}

if(IntVal($_REQUEST["del_id"]) > 0)
	$errorMessage = GetMessage("SALE_DEL_PROFILE", array("#ID#" => IntVal($_REQUEST["del_id"])));
elseif(IntVal($_REQUEST["success_del_id"]) > 0)
	$errorMessage = GetMessage("SALE_DEL_PROFILE_SUC", array("#ID#" => IntVal($_REQUEST["success_del_id"])));
	
if(strLen($errorMessage)>=0)
	$arResult["ERROR_MESSAGE"] = $errorMessage;
	
$by = (strlen($_REQUEST["by"])>0 ? $_REQUEST["by"]: "DATE_UPDATE");
$order = (strlen($_REQUEST["order"])>0 ? $_REQUEST["order"]: "DESC");

$dbUserProps = CSaleOrderUserProps::GetList(
		array($by => $order),
		array("USER_ID" => IntVal($GLOBALS["USER"]->GetID()))
	);
$dbUserProps->NavStart($arParams["PER_PAGE"]);
$arResult["NAV_STRING"] = $dbUserProps->GetPageNavString(GetMessage("SPPL_PAGES"));
$arResult["PROFILES"] = Array();
while($arUserProps = $dbUserProps->GetNext())
{
	$arResultTmp = Array();
	$arResultTmp = $arUserProps;
	$arResultTmp["PERSON_TYPE"] = CSalePersonType::GetByID($arUserProps["PERSON_TYPE_ID"]);
	$arResultTmp["PERSON_TYPE"]["NAME"] = htmlspecialcharsEx($arResultTmp["PERSON_TYPE"]["NAME"]);
	$arResultTmp["URL_TO_DETAIL"] = CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DETAIL"], Array("ID" => $arUserProps["ID"]));
	$arResultTmp["URL_TO_DETELE"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?del_id=".$arUserProps["ID"]."&".bitrix_sessid_get());
	$arResult["PROFILES"][] = $arResultTmp;
}

$this->IncludeComponentTemplate();
?>