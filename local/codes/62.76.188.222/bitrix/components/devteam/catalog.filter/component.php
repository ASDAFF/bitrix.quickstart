<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!$arParams['CACHE_TIME'])
    $arParams['CACHE_TIME'] = 3600000;

$cache_id = md5(serialize($arParams)); 
$cache_dir = '/filter';
 
$obCache = new CPHPCache; 
if ($obCache->InitCache($arParams['CACHE_TIME'], $cache_id, $cache_dir)) { 
   $arResult['PROPS'] = $obCache->GetVars(); 
}
elseif ($obCache->StartDataCache()){
    $slider_prop_id = 4;
    global $CACHE_MANAGER;
    $CACHE_MANAGER->StartTagCache($cache_dir);
    $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"] );     
    $CACHE_MANAGER->EndTagCache();

    $property_codes = FilterPropsType::getBySectionID($arParams['SECTION_ID']);
 
    // $property_codes - коды свойств для вывода в фильтре 

    $arResult['PROPS']['PRICE'] = array('NAME' => 'Розничная цена',
                                        'PROPERTY_TYPE' => 'PRICE',
                                        'VALUES' => array('MIN' => NULL, 
                                                          'MAX' => NULL));

    CModule::IncludeModule('iblock');

    $properties = CIBlockProperty::GetList(Array("sort"=>"asc",
                                                 "name"=>"asc"), 
                                           Array("ACTIVE" => "Y", 
                                                 "IBLOCK_ID" => $arParams['IBLOCK_ID'])
                                           ); 

    while ($prop_fields = $properties->GetNext()) {
        if (in_array($prop_fields['ID'], $property_codes)) {
            $prop_fields['CONFIG'] = $property_codes['CONFIG'][$prop_fields['ID']];
            if ($prop_fields["PROPERTY_TYPE"] == 'L') {
                $property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC",
                                                                     "NAME"=>"ASC"),
                                                               Array("IBLOCK_ID" => $arParams['IBLOCK_ID'], 
                                                                     "CODE" => $prop_fields['CODE']));
                while ($enum_fields = $property_enums->GetNext()) {
                    $prop_fields['VALUES'][] = $enum_fields;
                }
            } elseif ($prop_fields["PROPERTY_TYPE"] == 'S' ||
                      $prop_fields["PROPERTY_TYPE"] == 'N') { 
                $res = CIBlockElement::GetList(Array(), 
                                               Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"],
                                                     "!PROPERTY_" . $prop_fields["CODE"] => false), 
                                               Array("ID", 
                                                     "IBLOCK_ID",  
                                                     "PROPERTY_" . $prop_fields["CODE"]));
                while($ar_fields = $res->GetNext()){  
                    $prop_fields['VALUES'][] = $ar_fields["PROPERTY_" . strtoupper($prop_fields["CODE"]) . "_VALUE"];
                } 
                $prop_fields['VALUES'] = array_unique($prop_fields['VALUES']);
            }  
            
            if($prop_fields['CONFIG'] == $slider_prop_id){ 
                $prop_fields['VALUES']['MAX'] = ceil(max($prop_fields['VALUES']));
                $prop_fields['VALUES']['MIN'] = floor(min($prop_fields['VALUES']));
            } else {
                natsort($prop_fields['VALUES']);
            } 
            
            $arResult['PROPS'][$prop_fields['CODE']] = $prop_fields;
        }
   } 
   $res = CIBlockElement::GetList(array('CATALOG_PRICE_2'=>'DESC'), 
                                   array("IBLOCK_ID"=>$arParams["IBLOCK_ID"],
                                         'SECTION_ID'=>$arParams["SECTION_ID"]),
                                   false, 
                                   array("nPageSize"=>1),
                                   array("ID", "IBLOCK_ID", "CATALOG_GROUP_2"));
   if($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arResult['PROPS']['PRICE']['VALUES_']['MAX_'] = $arFields['CATALOG_PRICE_2'];
        //поскольку там шаг слайдера стоит 100, то сделай плиз чтоб максимальное число было ровным 100, 1000 и т. д.
        while($arResult['PROPS']['PRICE']['VALUES_']['MAX_'] % 1000 !== 0)
            $arResult['PROPS']['PRICE']['VALUES_']['MAX_']++;
   } 
   $obCache->EndDataCache($arResult['PROPS']); 
} 

$fpr = 'filter_';
foreach ($_REQUEST as $key => $value)
    if(substr($key, 0, strlen($fpr)) == $fpr){
           $k_ = substr($key, strlen($fpr)); 
           $_REQUEST['filter'][$k_] = $value;
    }

foreach($_REQUEST['filter'] as $filterName => $val){
    if($val != 'false') {
        switch ($filterName) {
            case 'min_price': 
                $val = intval($val);
                $arrFilter['>=CATALOG_PRICE_2'] = $val;
                $arResult['PROPS']['PRICE']['VALUES_']['MIN'] = $val; 
                break;
            case 'max_price':
                $val = intval($val);
                $arrFilter['<=CATALOG_PRICE_2'] = $val;
                $arResult['PROPS']['PRICE']['VALUES_']['MAX'] = $val;
                break;
            default:
                if(is_array($arResult['PROPS'][$filterName])){
                    if(is_array($val)){ 
                        $tmArr = array("LOGIC" => "OR");
                        foreach($val as $el){
                            $tmArr[] = array('PROPERTY_' . $filterName => $el);
                            $arResult['PROPS'][$filterName]['VALUES_'][] = $el;
                        }
                        $logicFilterArrays[] = $tmArr;
                    } else { 
                        $arrFilter['PROPERTY_' . $filterName] = $val; 
                        $val = str_replace('"', '&quot;', $val); // для селектов 
                        $arResult['PROPS'][$filterName]['VALUES_'] = $val;
                    }
                } else {
                    $arrZnaki = array('min_' => '>=',
                                      'max_' => '<='); 
                    $k_ = substr($filterName, 4);
                    if(in_array($firstSymbols = substr($filterName, 0, 4), array_keys($arrZnaki)) &&
                       is_array($arResult['PROPS'][$k_])){  
                           $arrFilter[$arrZnaki[$firstSymbols] . 'PROPERTY_' . $k_] = $val;
                           $arResult['PROPS'][$k_]['VALUES_'][strtoupper(substr($firstSymbols, 0, 3))] = $val; 
                       }
                } 
                break;
         }
    } 
}  

if(is_array($logicFilterArrays)){ 
    if(count($logicFilterArrays) > 1){
        $logicArr = array('LOGIC'=>'AND');
        foreach($logicFilterArrays as $arr)
            $logicArr[] = $arr;
        }
    else{
        $logicArr = $logicFilterArrays[0];
        } 
    $arrFilter[] = $logicArr; 
}
  
$this->IncludeComponentTemplate();
 
return $arrFilter;