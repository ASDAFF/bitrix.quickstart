<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true){
		die();
	}
	if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale"))
	return false;

?>
<?
	global $USER;	
	include(dirname(__FILE__)."/functions.php");

	if ($getUserCity = dwGetCity(BX_UTF)){
		$CITY_NAME = $getUserCity["CITY"][1];
	}

	$rsUser = CUser::GetByID($USER->GetID());
	$arUser = $rsUser->Fetch();
	
	foreach ($arUser as $code => $arProps) {
		$arResult["USER"][$code] = $arProps;
	} 

	if(!empty($CITY_NAME)){
		$arResult["USER"]["CITY_NAME"] = $CITY_NAME;
	}

	$arResult["BASKET_COUNT"] = getCountBasket();

	$this->IncludeComponentTemplate();

	function getCountBasket(){
	   
	   $arBasketItems = array();
	   $dbBasketItems = CSaleBasket::GetList(
          array("NAME" => "ASC","ID" => "ASC"),
          array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
          false,
          false,
          array("ID","MODULE","PRODUCT_ID","QUANTITY","CAN_BUY","PRICE")
	   );
	   
	   while ($arItems = $dbBasketItems->Fetch()){
	      $cart_num += $arItems['QUANTITY'];
	   }
	   
	   return  empty($cart_num) ? 0 : $cart_num;
    }


?>