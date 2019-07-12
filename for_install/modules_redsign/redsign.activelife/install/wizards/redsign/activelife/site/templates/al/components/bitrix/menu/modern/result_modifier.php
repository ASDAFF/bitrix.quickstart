<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;

use \Bitrix\Main\Loader;

if (
    Loader::includeModule('iblock') &&
    0 < intval($arParams['IBLOCK_ID'])
) {
   
    $arParams['COUNT_ELEMENTS'] = $arParams['COUNT_ELEMENTS'] == 'Y';
    $arItems = array();
    $arFilter = array(
        'TYPE' => $arParams['IBLOCK_TYPE'],
        'SITE_ID' => SITE_ID,
        'ACTIVE' => 'Y'
    );

    $obCache = \Bitrix\Main\Data\Cache::createInstance();
    
    if ($obCache->initCache($arParams['CACHE_TIME'], serialize($arFilter), '/iblock/menu')) {
        
        $arSections = $obCache->GetVars();
        
    } elseif ($obCache->startDataCache()) {

        if (Loader::includeModule('iblock')) {

            $arIblocks = array();
            $dbIBlock = CIBlock::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), $arFilter);
            $dbIBlock = new CIBlockResult($dbIBlock);

            if ($arIBlock = $dbIBlock->GetNext()) {
                $arIblocks[$arIBlock['ID']] = $arIBlock;
            }
            
            if (!empty($arIblocks)) {
                
                $arFilter = array(
                    'IBLOCK_ID' => array_keys($arIblocks),
                    'ACTIVE' => 'Y',
                    'GLOBAL_ACTIVE' => 'Y',
                    'CNT_ACTIVE' => 'Y',
                );
                $arSelect = array(
                    'ID',
                    'SECTION_PAGE_URL'
                );
                
                $dbSections = CIBlockSection::GetList(
                    array(),
                    $arFilter,
                    $arParams['COUNT_ELEMENTS'],
                    $arSelect
                );
                
                while ($arSection = $dbSections->GetNext()) {
                    $item_id = crc32($arSection['SECTION_PAGE_URL']);
                    $arSections[$item_id] = $arSection;
                }
            }
            
            if (!empty($arSections)) {
                $obCache->endDataCache($arSections);
            } else {
                $obCache->abortDataCache();
            }
        }
    }
    
	foreach ($arResult as $key => $arItem) {
        
        $item_id = crc32($arItem['LINK']);
        
		if (isset($arSections[$item_id])) {
			$arResult[$key]['PARAMS']['SECTION_ID'] = $arSections[$item_id]['ID'];
            
            if (isset($arSections[$item_id]['ELEMENT_CNT'])) {
                $arResult[$key]['ELEMENT_CNT'] = $arSections[$item_id]['ELEMENT_CNT'];
            }
		}

        if (
            $arItem['DEPTH_LEVEL'] == 1 &&
            intval($arResult[$key]['PARAMS']['SECTION_ID']) > 0 &&
            $arParams['PROPERTY_CODE_ELEMENT_IN_MENU'] != ''
        ) {
			$arResult[$key]['PARAMS']['ELEMENT'] = 'N';
			$arOrder = array('SORT'=>'ASC','ID'=>'ASC');
			$arFilter = array(
				'IBLOCK_ID'=> intval($arParams['IBLOCK_ID']),
				'ACTIVE' => 'Y',
				'INCLUDE_SUBSECTIONS' => 'Y',
				'SECTION_ID' => intval($arResult[$key]['PARAMS']['SECTION_ID']),
				'!PROPERTY_'.$arParams['PROPERTY_CODE_ELEMENT_IN_MENU'] => false,
			);
			$arNavStartParams = array(
                'nTopCount' => '1'
            );
			$arSelect = array(
                'ID',
                'IBLOCK_ID',
                'ACTIVE',
                'SECTION_ID',
                'PROPERTY_'.$arParams['PROPERTY_CODE_ELEMENT_IN_MENU']
            );
			
            $res = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
            
			if ($arObj = $res->GetNextElement()) {
				$arFields = $arObj->GetFields();
				$arResult[$key]['PARAMS']['ELEMENT'] = 'Y';
				$arResult[$key]['PARAMS']['ELEMENT_ID'] = $arFields['ID'];
			}
		}
	}
}

if(!function_exists('recursiveAlignItems')) {

    function recursiveAlignItems(&$arItems, $level = 1, &$i = 0) {

        $returnArray = array();

        if (!is_array($arItems)) {
            return $returnArray;
        }

        for (
            $currentItemKey = 0, $countItems = count($arItems);
            $i < $countItems;
            $i++
        ) {

            $arItem = $arItems[$i];

            if ($arItem['DEPTH_LEVEL'] == $level) {
                $returnArray[$currentItemKey++] = $arItem;
            } elseif ($arItem['DEPTH_LEVEL'] > $level) {

                $returnArray[$currentItemKey - 1]['SUB_ITEMS'] = recursiveAlignItems(
                    $arItems,
                    $level + 1,
                    $i
                );

            } elseif ($level > $arItem['DEPTH_LEVEL']) {
                --$i; break;
            }
        }
        return $returnArray;
    }
}

$arResult = recursiveAlignItems($arResult);
