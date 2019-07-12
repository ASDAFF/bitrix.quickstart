<?
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("catalog"))
{
	return;
}

if ($_SERVER["REQUEST_METHOD"]=="POST" && strlen($_POST["action"])>0 && check_bitrix_sessid())
{
	global $APPLICATION,$JSON;
	
	$APPLICATION->RestartBuffer();

	switch ($_POST["action"])
	{
		case "catalogSetAdd2Basket":
			if (is_array($_POST["set_ids"]))
			{
				foreach($_POST["set_ids"] as $itemID)
				{
					$product_properties = true;
					if (!empty($_POST["setOffersCartProps"]))
					{
						$product_properties = CIBlockPriceTools::GetOfferProperties(
							$itemID,
							$_POST["iblockId"],
							$_POST["setOffersCartProps"]
						);
					}
					$ratio = 1;
					if ($_POST["itemsRatio"][$itemID])
						$ratio = $_POST["itemsRatio"][$itemID];

					if (intval($itemID))
						Add2BasketByProductID(intval($itemID), $ratio, array("LID" => $_POST["lid"]), $product_properties);
				}
			}
			$APPLICATION->RestartBuffer();
			$APPLICATION->IncludeComponent('bitrix:sale.basket.basket.small','json',array());
			$APPLICATION->RestartBuffer();
			if(SITE_CHARSET != 'utf-8')
			{
				$data = $APPLICATION->ConvertCharsetArray($JSON, SITE_CHARSET, 'utf-8');
				$json_str_utf = json_encode($data);
				$json_str = $APPLICATION->ConvertCharset($json_str_utf, 'utf-8', SITE_CHARSET);
				echo $json_str;
			} else {
				echo json_encode( $arResult['JSON_EXT'] );
			}
			break;
		case "ajax_recount_prices":
			if (strlen($_POST["currency"])>0)
			{
				$arPices = array("formatSum" => "", "formatOldSum" => "", "formatDiscDiffSum" => "");
				if ($_POST["sumPrice"])
					$arPices["formatSum"] = FormatCurrency($_POST["sumPrice"], $_POST["currency"]);
				if ($_POST["sumOldPrice"] && $_POST["sumOldPrice"] != $_POST["sumPrice"])
					$arPices["formatOldSum"] = FormatCurrency($_POST["sumOldPrice"], $_POST["currency"]);
				if ($_POST["sumDiffDiscountPrice"])
					$arPices["formatDiscDiffSum"] = FormatCurrency($_POST["sumDiffDiscountPrice"], $_POST["currency"]);

				if (SITE_CHARSET != "utf-8")
					$arPices = $APPLICATION->ConvertCharsetArray($arPices, SITE_CHARSET, "utf-8");
				echo json_encode($arPices);
			}
			break;
	}
	
	die();
}