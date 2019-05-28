<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();



$ids = array();
$products = array();

global $DB;
$user_id = MHT\Product::fav_user();
$result = $DB->Query("SELECT `GOOD_ID` FROM `mht_favorites` WHERE `USER_ID` = '".$user_id."';");

while($el = $result->Fetch()) {
	$ids[] = $el['GOOD_ID'];
}
$ids = array_unique($ids);

foreach($ids as $id) {	
    if ( $id && $product = MHT\Product::byIDActive($id) ) {
		$product->moreFields($id);
        $products[] = $product;
    } else {
        //чистим Избранное
        $result = $DB->Query("DELETE FROM `mht_favorites` WHERE `GOOD_ID` = '".$id."' AND `USER_ID` = '".$user_id."';");
    }
 }

/*CModule::IncludeModule('sale');
$list = CSaleBasket::GetList(array(
	'NAME' => 'ASC'
), array(
	'LID' => SITE_ID,
	'FUSER_ID' => CSaleBasket::GetBasketUserID(),
	'DELAY' => 'Y'
));
while(($e = $list->GetNext()) !== false){
	$product = MHT\Product::byID($e['PRODUCT_ID']);
	$product->moreFields($e);
	$products[] = $product;
}*/
$arResult['PRODUCTS'] = $products;


$this->IncludeComponentTemplate();
?>