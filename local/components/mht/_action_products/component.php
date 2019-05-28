<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$ids = $arParams['IDS'];
$products = array();

foreach($ids as $id) {
	$product = MHT\Product::byID($id);
	$product->moreFields($id);
	$products[] = $product;
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

if ($_REQUEST['huy']) {
	print_r ($products);
}

$this->IncludeComponentTemplate();
?>