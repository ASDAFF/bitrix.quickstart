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

$ID = IntVal($arParams["ID"]);

$arParams["PATH_TO_LIST"] = Trim($arParams["PATH_TO_LIST"]);
if (strlen($arParams["PATH_TO_LIST"]) <= 0)
	$arParams["PATH_TO_LIST"] = htmlspecialchars($APPLICATION->GetCurPage());

$arParams["PATH_TO_DETAIL"] = Trim($arParams["PATH_TO_DETAIL"]);
if (strlen($arParams["PATH_TO_DETAIL"]) <= 0)
	$arParams["PATH_TO_DETAIL"] = htmlspecialchars($APPLICATION->GetCurPage()."?"."ID=#ID#");

if($arParams["SET_TITLE"] == 'Y')
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("SPOC_TITLE")));

if ($ID > 0 && $_REQUEST["CANCEL"] == "Y" && $_SERVER["REQUEST_METHOD"]=="POST" && strlen($_REQUEST["action"])>0 && check_bitrix_sessid())
{
	$dbOrder = CSaleOrder::GetList(
			array("ID" => "DESC"),
			array(
					"ID" => $ID,
					"USER_ID" => IntVal($USER->GetID())
				),
			false,
			false,
			array("ID")
		);
	if ($arOrder = $dbOrder->Fetch())
	{
// UnitellerPlugin change
		if (ps_uniteller::setUnitellerCancel($arOrder['ID'])) {
			CSaleOrder::CancelOrder($arOrder['ID'], 'Y', $_REQUEST['REASON_CANCELED']);
			LocalRedirect($arParams['PATH_TO_LIST']);
		} else {
			$arResult = array(
				'URL_TO_LIST' => $arParams['PATH_TO_LIST'],
				'ERROR_MESSAGE' => GetMessage('SPOC_UNITELLER_ERROR'),
			);

			$this->IncludeComponentTemplate();
			return true;
		}
// /UnitellerPlugin change
	}
}

if ($ID <= 0)
	LocalRedirect($arParams["PATH_TO_LIST"]);

$dbOrder = CSaleOrder::GetList(
		array("ID" => "DESC"),
		array(
				"ID" => $ID,
				"USER_ID" => IntVal($USER->GetID())
			),
		false,
		false,
		array("ID", "CANCELED", "STATUS_ID", "PAYED")
	);
if ($arOrder = $dbOrder->GetNext())
{
	if ($arOrder["CANCELED"]!="Y" && $arOrder["STATUS_ID"]!="F" && $arOrder["PAYED"]!="Y")
	{
		$arResult = Array(
				"ID" 		=> $ID,
				"URL_TO_DETAIL" => CComponentEngine::MakePathFromTemplate($arParams["PATH_TO_DETAIL"], Array("ID" => $arOrder["ID"])),
				"URL_TO_LIST" => $arParams["PATH_TO_LIST"],
			);
	}
	else
		$arResult["ERROR_MESSAGE"] = GetMessage("SPOC_CANCEL_ORDER");
}
else
	$arResult["ERROR_MESSAGE"] = str_replace("#ID#", $ID, GetMessage("SPOC_NO_ORDER"));

$this->IncludeComponentTemplate();
?>