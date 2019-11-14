<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(empty($arParams['COLLECTION_VARIABLE']))
    $arParams['COLLECTION_VARIABLE'] = 'ID_COL';

if($this->StartResultCache(false, $_REQUEST[$arParams['COLLECTION_VARIABLE']])){

    CModule::IncludeModule("fileman");
    CMedialib::Init();

    $arCollections = array();
    if(!isset($_REQUEST[$arParams['COLLECTION_VARIABLE']]) || $_REQUEST[$arParams['COLLECTION_VARIABLE']] == 0){
        
        $Collections = $arParams['ROOT_COLLECTIONS'];

        if(sizeof($Collections) == 1){

            $ar = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', 'PARENT_ID' => $arParams['ROOT_COLLECTIONS'][0])));
            foreach($ar as $col){
                 $arCollections[] = $col;
            }
            $arCollectionItems = CMedialibItem::GetList(array('arCollections'=>array(intval($arParams['ROOT_COLLECTIONS'][0]))));

        }elseif(sizeof($Collections) > 1){

            foreach($Collections as $Collection){
                $ar = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', 'ID' => $Collection)));
                $arCollections[] = $ar[0];
            }
        }else
            $arCollections = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', 'PARENT_ID' => 0)));

    }else{

        $arCollections = CMedialibCollection::GetList(
            array(
                 'arFilter' => array(
                     'ACTIVE' => 'Y',
                     'PARENT_ID' => intval($_REQUEST[$arParams['COLLECTION_VARIABLE']]
                     )
                 )
            )
        );
        $arNowCollection = CMedialibCollection::GetList(array('arFilter' => array('ACTIVE' => 'Y', 'ID' => intval($_REQUEST[$arParams['COLLECTION_VARIABLE']]))));

        $arCollectionItems = CMedialibItem::GetList(array('arCollections'=>array(intval($_REQUEST[$arParams['COLLECTION_VARIABLE']]))));
    }


    // Сортируем разделы
    if($arParams['SECTION_SORT'] == 'Y'){
        $tmp_sec = array();
        foreach($arCollections as $Section){

            $tmp_sec[intval($Section['DESCRIPTION'])] = $Section;
        }
        ksort($tmp_sec);
        $arCollections = $tmp_sec;
    }

    foreach($arCollections as &$arCollection){

        $arCollection['URL'] = $APPLICATION->GetCurPage().'?'.$arParams['COLLECTION_VARIABLE']."=".$arCollection['ID'];
    }

    $arResult['COLLECTIONS'] = $arCollections;
    $arResult['COLLECTION'] = $arNowCollection[0];

    if(sizeof($arParams['ROOT_COLLECTIONS']) == 1 && $arNowCollection[0]['ID'] == $arParams['ROOT_COLLECTIONS'][0]){

        $arResult['COLLECTION']['BACK_URL'] = '';
        
    }elseif($arNowCollection[0]['ID']){

        if($arNowCollection[0]['PARENT_ID'] == 0)
            $arResult['COLLECTION']['BACK_URL'] = $APPLICATION->GetCurPage(false);

        elseif((sizeof($arParams['ROOT_COLLECTIONS']) == 1 && $arParams['ROOT_COLLECTIONS'][0] == $arNowCollection[0]['PARENT_ID']) || in_array($_REQUEST[$arParams['COLLECTION_VARIABLE']], $arParams['ROOT_COLLECTIONS']))
            $arResult['COLLECTION']['BACK_URL'] = $APPLICATION->GetCurPage(false);
        
        else
           $arResult['COLLECTION']['BACK_URL'] = $APPLICATION->GetCurPage().'?'.$arParams['COLLECTION_VARIABLE']."=".$arNowCollection[0]['PARENT_ID'];
    }
//    echo '<pre>'; print_r($arCollectionItems); echo '</pre>';

    // Сортировка
    if($arParams['SORT_BY']){
        $ar = array();
        foreach($arCollectionItems as &$ArItem){
            $ar = array_merge(array('SORT_FIELD' => $ArItem[$arParams['SORT_BY']]), $ArItem);
            $ArItem = $ar;
        }
        if($arParams['SORT_ORDER'] == 'ASC')
            sort($arCollectionItems);
        else
            rsort($arCollectionItems);
    }

    $arResult['COLLECTION']['ITEMS'] = $arCollectionItems;

    $this->IncludeComponentTemplate();


}

if($arParams["SET_HEADER"] && $arResult['COLLECTION']['NAME'])
    $APPLICATION->SetTitle($arResult['COLLECTION']['NAME']);

