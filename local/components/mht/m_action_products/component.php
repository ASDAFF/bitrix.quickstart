<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$ids = $arParams['IDS'];
$products = array();

foreach($ids as $id) {
	$product = MobileCatalog::byID($id);
	$product->moreFields($id);
	$products[] = $product;
}

// создадим объект класса CDBResult
$rsDirContent = new CDBResult;

// инициализируем этот объект исходным массивом
$rsDirContent->InitFromArray($products);
$rsDirContent->NavStart(48);
$rsDirContent->NavPrint('',false,'',SITE_TEMPLATE_PATH.'/include/custom_nav_template.php');

$wblProd = array();
while($tmp = $rsDirContent->Fetch()){
	$wblProd[] = $tmp;
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
$arResult['PRODUCTS'] = $wblProd;


$this->IncludeComponentTemplate();
$rsDirContent->NavPrint('',false,'',SITE_TEMPLATE_PATH.'/include/custom_nav_template.php');
?>