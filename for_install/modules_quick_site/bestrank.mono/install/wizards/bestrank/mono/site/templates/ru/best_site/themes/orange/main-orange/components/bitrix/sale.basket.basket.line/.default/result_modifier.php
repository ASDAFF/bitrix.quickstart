<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$arResult["NUM_PRODUCTS"]=0;
$arResult["TOTAL"] = array();

CModule::IncludeModule('sale');

$dbBasketItems = CSaleBasket::GetList(
        array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
        array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL", "DELAY"=>"N"
            ),
        false,
        false,
        array("ID", "CALLBACK_FUNC", "MODULE", 
              "PRODUCT_ID", "QUANTITY", "DELAY", "CURRENCY",
              "CAN_BUY", "PRICE", "WEIGHT")
    );
while ($arItems = $dbBasketItems->Fetch())
{
    	if ($arItems["CAN_BUY"]=="N") continue;
	$arResult["NUM_PRODUCTS"] += $arItems["QUANTITY"];
	if(!is_array($arResult["TOTAL"][$arItems["CURRENCY"]])) {
		$arResult["TOTAL"][$arItems["CURRENCY"]] = array();
	}
	$arResult["TOTAL"][$arItems["CURRENCY"]]["NUMBER"] += $arItems["QUANTITY"];
	$arResult["TOTAL"][$arItems["CURRENCY"]]["SUM"] += $arItems["QUANTITY"]*$arItems["PRICE"];
}


$arResult["TOTAL_FORMATTED"] = array();


foreach ($arResult["TOTAL"] as $key=>$value) {
	$goods = GetMessage('GOOD');
	if(CModule::IncludeModule("bestrank.case")){
		$goods = CBestRankCase::GetForm($value["NUMBER"], GetMessage('GOOD'), GetMessage('GOOD2'), GetMessage('GOOD5'));
	}
	$arResult["TOTAL_FORMATTED"][$key] = "<b>".$value["NUMBER"]."</b> ".$goods."  <b>(".SaleFormatCurrency($value["SUM"], $key).")</B> ";
}


$arResult["TOTAL_FORMATTED_STRING"] = implode(",<br />", $arResult["TOTAL_FORMATTED"]);

?>