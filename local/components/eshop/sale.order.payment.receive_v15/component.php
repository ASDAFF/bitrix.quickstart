<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}


$dbPaySysAction = CSalePaySystemAction::GetList(
		array(),
		array(
				"PAY_SYSTEM_ID" => $arParams["PAY_SYSTEM_ID"],
				"PERSON_TYPE_ID" => $arParams["PERSON_TYPE_ID"],
			),
		false,
		false,
		array("ACTION_FILE", "PARAMS", "ENCODING")
	);

if ($arPaySysAction = $dbPaySysAction->Fetch())
{
	if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
	{
		$GLOBALS["SALE_CORRESPONDENCE"] = CSalePaySystemAction::UnSerializeParams($arPaySysAction["PARAMS"]);
		$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

		$pathToAction = str_replace("\\", "/", $pathToAction);
		while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
			$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

		if (file_exists($pathToAction))
		{
			if (is_dir($pathToAction))
			{
				if (file_exists($pathToAction."/result_rec.php"))
					include($pathToAction."/result_rec.php");
			}
		}
		if(strlen($arPaySysAction["ENCODING"]) > 0)
		{
			define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
			AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
			function ChangeEncoding($content)
			{
				global $APPLICATION;
				header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
				$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
				$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
			}
		}

	}
}
?>