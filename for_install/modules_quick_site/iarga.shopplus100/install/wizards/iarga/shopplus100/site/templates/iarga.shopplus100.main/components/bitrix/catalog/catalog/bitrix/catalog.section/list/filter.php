 <?
global $USER;
$tf = $templateFolder;
global $templateFolder;

// For filter Price
$item = CIBlockElement::GetList(Array(),Array("IBLOCK_ID"=>$arParams['IBLOCK_ID']))->GetNext();
$price = CCatalogProduct::GetOptimalPrice($item['ID'],1,$USER->GetUserGroupArray());
$price_id = $price['PRICE']['CATALOG_GROUP_ID'];
$cond = Array("IBLOCK_ID"=>$arParams['IBLOCK_ID']);
if($arResult['ID']){
	$cond["SECTION_ID"] = $arResult['ID'];
	$cond["INCLUDE_SUBSECTIONS"] = "Y";
}
$min = CIBlockElement::GetList(Array("CATALOG_PRICE_".$price_id=>"ASC"),$cond)->GetNext();
$max = CIBlockElement::GetList(Array("CATALOG_PRICE_".$price_id=>"DESC"),$cond)->GetNext();

$arResult['MIN_PRICE'] = floor(iarga::getprice($min['ID'])/100)*100;
$arResult['MAX_PRICE'] = ceil(iarga::getprice($max['ID'])/100)*100;
$price_type = CCatalogProduct::GetOptimalPrice($max['ID'],1,$USER->GetUserGroupArray());

$step = pround(($arResult['MAX_PRICE'] - $arResult['MIN_PRICE'])/4);
for($i=0;$i<3;$i++) $arResult['STEPS_PRICE'][$i] = $arResult['MIN_PRICE'] + ($i+1)*$step;



?>
<aside>
    <?$APPLICATION->IncludeComponent (
    "bitrix:catalog.smart.filter",
        "",
        Array(
            "IBLOCK_TYPE" => "",
            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
            "SECTION_ID" => $arResult["ID"],
            "FILTER_NAME" => "arrFilter",
            "PRICE_CODE" => array("BASE"),
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "Y",
            "SAVE_IN_SESSION" => "Y",
            "INSTANT_RELOAD" => "Y"
        ),$component
    );?>
</aside>
