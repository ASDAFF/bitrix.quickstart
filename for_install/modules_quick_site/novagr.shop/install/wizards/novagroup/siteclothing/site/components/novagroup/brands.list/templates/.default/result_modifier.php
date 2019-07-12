<?
$lat = $arResult['LAT_ABC'];
$rus = $arResult['RUS_ABC'];

$arFilter = array(
    'ACTIVE' => "Y",
    'IBLOCK_CODE' => $arParams['BRANDS_IBLOCK_CODE']
);
$arSelect = array('NAME', 'CODE');
$rsElement = CIBlockElement::GetList(array('NAME' => 'ASC'), $arFilter, false, false, $arSelect);

$arResult['LAT'] = array();
$arResult['RUS'] = array();
while ($data = $rsElement -> Fetch())
{

    $let = mb_substr($data['NAME'], 0, 1);
    if( in_array($let, $lat) && !in_array($let, $arResult['LAT']) )
        $arResult['LAT'][] = $let;
    if( in_array($rus, $lat) && !in_array($let, $arResult['RUS']) )
        $arResult['RUS'][] = $let;
}

?>