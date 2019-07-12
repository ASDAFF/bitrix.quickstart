<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
foreach($arResult['SEARCH_RESULT']['CATALOG'] as &$items){
    if($items['IBLOCK_CODE']=='tyres'){
        $rsProp = CIBlockProperty::GetByID('model', false, 'tyres');
        if($arProp = $rsProp->GetNext()){
            $rsElement = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$arProp['LINK_IBLOCK_ID'], 'ID'=>$ar_res['PROPERTIES']['model']['VALUE'], 'ACTIVE'=>'Y'), false, array('nTopCount'=>1), array('ID', 'IBLOCK_ID', 'PREVIEW_PICTURE'));
            if ($obElement = $rsElement->GetNextElement()) {
                $arFields = $obElement->GetFields();
                $arProps = $obElement->GetProperties(false, array('CODE'=>array('model_season', 'model_type', 'model_pin')));
                
                $items['PREVIEW_PICTURE'] = CFile::GetFileArray($items['PROPERTY_MODEL_PREVIEW_PICTURE']);
                $items['PROPERTIES'] = $arProps;
            }
        }
    }elseif($items['IBLOCK_CODE']=='wheels'){
        $rsProp = CIBlockProperty::GetByID('model', false, 'wheels');
        if($arProp = $rsProp->GetNext()){
            $rsElement = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$arProp['LINK_IBLOCK_ID'], 'ID'=>$ar_res['PROPERTIES']['model']['VALUE'], 'ACTIVE'=>'Y'), false, array('nTopCount'=>1), array('ID', 'IBLOCK_ID', 'PREVIEW_PICTURE'));
            if ($obElement = $rsElement->GetNextElement()) {
                $arFields = $obElement->GetFields();

                $items['PREVIEW_PICTURE'] = CFile::GetFileArray($items['PROPERTY_MODEL_PREVIEW_PICTURE']);
            }
        }
    }

    $index_tmp = count($arTempResult); // сколько строк; 0 если первый
    if($index_tmp!=0){
        if(count($arTempResult[($index_tmp-1)])==$arParams['LINE_ELEMENT_COUNT']){
            $arTempResult[$index_tmp][] = $items;
        }else{
            $arTempResult[$index_tmp-1][] = $items;
        }
    }else{
        $arTempResult[0][0] = $items;
    }
}

$arResult['ITEMS'] = $arTempResult;

if(isset($arResult['TOTAL_FOUND']['CATALOG'])){
    $arResult['CATALOG_URL'] = $APPLICATION->GetCurPageParam("area=catalog",
        array("login", "logout", "register", "forgot_password", "change_password", "area")
    );
}

if(isset($arResult['TOTAL_FOUND']['CONTENT'])){
    $arResult['CONTENT_URL'] = $APPLICATION->GetCurPageParam("area=content",
        array("login", "logout", "register", "forgot_password", "change_password", "area")
    );
}
$arResult['PURE_URL'] = $APPLICATION->GetCurPageParam("",
        array("login", "logout", "register", "forgot_password", "change_password", "area")
    );
?>
