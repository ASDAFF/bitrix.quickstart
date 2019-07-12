<?
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["basketChange"])>0 && check_bitrix_sessid())
{
	if (isset($_POST["site_id"]))
		$site_id = $_POST["site_id"];

	if(isset($_SESSION["SALE_BASKET_NUM_PRODUCTS"][$site_id]))
	{
		$num_products = $_SESSION["SALE_BASKET_NUM_PRODUCTS"][$site_id];
	}
	else
	{
		if(!CModule::IncludeModule("sale"))
		{
			return;
		}
		$fUserID = CSaleBasket::GetBasketUserID(True);
		$fUserID = IntVal($fUserID);
		$num_products = 0;
		if ($fUserID > 0)
		{
			$dbRes = CSaleBasket::GetList(
				array(),
				array(
					"FUSER_ID" => $fUserID,
					"LID" => $site_id,
					"ORDER_ID" => "NULL",
					"CAN_BUY" => "Y",
					"DELAY" => "N",
					"SUBSCRIBE" => "N"
				)
			);
			while ($arItem = $dbRes->GetNext())
			{
				if (!CSaleBasketHelper::isSetItem($arItem))
					$num_products++;
			}
		}
		$_SESSION["SALE_BASKET_NUM_PRODUCTS"][$site_id] = intval($num_products);
	}

	$APPLICATION->RestartBuffer();
	echo $num_products;
	die();
}
?>