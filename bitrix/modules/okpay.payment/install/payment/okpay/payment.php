<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));
$OrderID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
if (!($arOrder = CSaleOrder::GetByID($OrderID))):
	echo GetMessage("PYM_ORDER").$OrderID.GetMessage("PYM_NOT_FOUND");
else:
	#---Get basket items for detail payment--
	$dbBasketItems = CSaleBasket::GetList(
		array( "NAME" => "ASC", "ID" => "ASC" ),
		array( "LID" => SITE_ID, "ORDER_ID" => $ORDER_ID ),
		false, false,
		array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT")
	);
	while ($arItems = $dbBasketItems->Fetch()) {
		if (strlen($arItems["CALLBACK_FUNC"]) > 0) {
			CSaleBasket::UpdatePrice($arItems["ID"], $arItems["CALLBACK_FUNC"], $arItems["MODULE"], $arItems["PRODUCT_ID"], $arItems["QUANTITY"]);
			$arItems = CSaleBasket::GetByID($arItems["ID"]);
		}
		$arBasketItems[] = $arItems;
	}
	#-----------Basket items getted----------
	/* Example of detailed options
		<?for($i=0;$i<count($arBasketItems);$i++):?>
			<input type="hidden" name="ok_item_<?=($i+1);?>_name" value="<?=$arBasketItems[$i]['NAME'];?>"/><br/>
			<input type="hidden" name="ok_item_<?=($i+1);?>_price" value="<?=$arBasketItems[$i]['PRICE'];?>"/><br/>
			<input type="hidden" name="ok_item_<?=($i+1);?>_quantity" value="<?=$arBasketItems[$i]['QUANTITY'];?>"/><br/>
		<?endfor;?>
	*/
	$WalletID = CSalePaySystemAction::GetParamValue("WalletID");
	$OrderSUM = number_format(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), 2, ".", ""); 
	$OrderCurrency = CSalePaySystemAction::GetParamValue("CURRENCY");
	?>
	<form method="post" action="https://www.okpay.com/process.html" accept-charset="UTF-8">
		<font class="tablebodytext">
		<?=GetMessage("PYM_TITLE")?><br>
		<?=GetMessage("PYM_ORDER")?> <?echo $OrderID."  ".CSalePaySystemAction::GetParamValue("DATE_INSERT")?><br>
		<?=GetMessage("PYM_TO_PAY")?> <b><?echo SaleFormatCurrency(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), CSalePaySystemAction::GetParamValue("CURRENCY"))?></b>
		<input type="hidden" name="ok_receiver" value="<?=$WalletID?>"/>
		<input type="hidden" name="ok_item_1_name" value="<?=GetMessage("PYM_ORDER")?><?=$OrderID?>"/>
		<input type="hidden" name="ok_item_1_price" value="<?=$OrderSUM?>"/>
		<input type="hidden" name="ok_currency" value="<?=$OrderCurrency?>"/>
		<input type="hidden" name="ok_invoice" value="<?=$OrderID?>"/><br/><br/>
		<input type="image" name="submit" alt="OKPAY Payment" src="https://www.okpay.com/img/buttons/en/buy/b20g145x42en.png"/>
	</form>
	<p align=\"justify\"><font class=\"tablebodytext\"><?=GetMessage("PYM_WARN")?></p>
<?endif;?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>