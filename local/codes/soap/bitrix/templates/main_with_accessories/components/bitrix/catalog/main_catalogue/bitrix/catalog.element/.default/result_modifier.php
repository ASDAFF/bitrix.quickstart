<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); 
$max_cnt = 5; // число градаций 
$stores_iblock_id = 8; // инфобок с складами
 
if (!CModule::IncludeModule('remains')) return;
if (!CModule::IncludeModule('iblock'))  return;
if (!$arResult['ID']) return;
for($a = 1; $a <= $max_cnt - 1; $a++)   
    $gr[$a] = $arParams['N' . $a];
$availability = new availability();
$r = $availability->GetList(array(), array('ITEM_ID' => $arResult['ID']));
while ($res = $r->Fetch()) {
    if(!$res['AVIABLE']) 
        continue;
    $resStore = CIBlockElement::GetList(Array(),
                                   Array("IBLOCK_ID" => $stores_iblock_id, 
                                         "ID" => $res['STORE_ID'],
                                         "!PROPERTY_SAMOVIVOZ" => false),
                                   false,
                                   false, 
                                   Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_*"));
    while($ob = $resStore->GetNextElement()){
        $arFields = $ob->GetFields(); 
        if(in_array($arFields['ID'], $storesIds))
             continue; 
        $storesIds[] = $arFields['ID'];
        $arProps = $ob->GetProperties();   
        for($a = 0; $a <= $max_cnt; $a++){ 
            $amount = $a; 
            if($res['AVIABLE'] < $gr[$a])
                break;  
            }
        $arResult['STORE'][] = array('STORE_NAME' => $arFields['NAME'], 
                                     'STORE_ADDR' => $arProps['ADDR']['VALUE'],
                                     'SCHEDULE'   => $arProps['TIME']['VALUE'],
                                     'AMOUNT'     => $res['AVIABLE'],
                                     'AMOUNT_%'   => $amount );
    }
}