<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?if($_POST["ID"])
{
	\Bitrix\Main\Loader::includeModule('sale');
	$_POST["ID"] = urldecode($_POST["ID"]);
	$id = 0;
	$arSelect = array("ID");
	$arFilter = array("ACCOUNT_NUMBER" => $_POST["ID"]);
	$rsOrder = \Bitrix\Sale\OrderTable::getList(
		array(
			"filter" => $arFilter,
			"select" => $arSelect,
		)
	);
	if($arOrder = $rsOrder->Fetch())
	{
		if(!$_SESSION["ORDER"][$arOrder["ID"]])
		{
			$id = $arOrder["ID"];
			$_SESSION["ORDER"][$arOrder["ID"]] = $arOrder["ID"];
		}
	}
	echo json_encode($id);
}?>