<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$count=0;
$sum=0;


foreach ($arResult["ITEMS"] as $key => $value)
	{
		if ($value["DELAY"]=="N" && $value["CAN_BUY"]=="Y")
		{
		 $count++; 
		 $sum+=	$value["PRICE"]*$value["QUANTITY"];
		}
	}
$arResult["COUNT"]=$count;
$arResult["SUM"]=SaleFormatCurrency($sum, "RUB").".";

?>
