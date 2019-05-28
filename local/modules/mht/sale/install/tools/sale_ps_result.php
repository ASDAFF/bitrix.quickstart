<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define("DisableEventsCheck", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_REQUEST["orderNumber"]) && intval($_REQUEST["orderNumber"]) > 0)
{
	if (CModule::IncludeModule("sale"))
	{
		$arOrder = CSaleOrder::GetByID(intval($_REQUEST["orderNumber"]));

		if ($arOrder)
		{
			$personTypeId = $arOrder["PERSON_TYPE_ID"];
			$paySystemId = $arOrder["PAY_SYSTEM_ID"];

			$APPLICATION->IncludeComponent(
				"bitrix:sale.order.payment.receive",
				"",
				array(
					"PAY_SYSTEM_ID" => $paySystemId,
					"PERSON_TYPE_ID" => $personTypeId
				),
			false
			);
		}
	}
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
