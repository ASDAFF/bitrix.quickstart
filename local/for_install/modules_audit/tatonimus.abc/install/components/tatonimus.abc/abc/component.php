<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (strlen($arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
    $arParams["FILTER_NAME"] = "arrFilter";
} else {
    $arParams["FILTER_NAME"] = strval($arParams["FILTER_NAME"]);
}
if (strlen($arParams["REQUEST_KEY"]) <= 0) {
    $arParams["REQUEST_KEY"] = "ID";
} else {
    $arParams["REQUEST_KEY"] = strval($arParams["REQUEST_KEY"]);
}
if (!empty($arParams['PROPERTY'])) {
	$arParams['PROPERTY'] = strtoupper($arParams['PROPERTY']);
}

if (strlen($_REQUEST[$arParams['REQUEST_KEY']]) > 0) {
    $arResult['PROPERTY_VALUE'] = trim($_REQUEST[$arParams['REQUEST_KEY']]);
    global $$arParams['FILTER_NAME'];
    if (!is_array($$arParams['FILTER_NAME'])) {
        $$arParams['FILTER_NAME'] = array();
    }
    $keyProperty = 'PROPERTY_' . $arParams['PROPERTY'];
    if (CModule::IncludeModule('iblock')) {
        $rsProp = CIBlockProperty::GetList(
            array(),
            array(
                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                'CODE' => $arParams['PROPERTY'],
                'PROPERTY_TYPE' => 'L',
            )
        );
        if ($rsProp->SelectedRowsCount() == 1) {
            $keyProperty .= '_VALUE';
        }
    }
    ${$arParams['FILTER_NAME']}[$keyProperty] = $arResult['PROPERTY_VALUE'];
    $this->IncludeComponentTemplate('detail');
} else {
    if ($this->StartResultCache() && CModule::IncludeModule('iblock')) {
        $arAbc = array();
        if ($arParams['PROPERTY'] == 'IBLOCK_ELEMENT_NAME') {
            $rsElement = CIBlockElement::GetList(
                array(),
                array(
                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'INCLUDE_SUSBSECTIONS' => 'N',
                ),
                false,
                false,
                array('NAME', 'DETAIL_PAGE_URL')
            );
            while ($arItem = $rsElement->GetNext()) {
                if (!empty($arItem['NAME'])) {
                    $letter = strtoupper(substr($arItem['NAME'], 0, 1));
                    $arAbc[$letter][$arItem['NAME']] = array(
                        'NAME' => $arItem['NAME'],
                        'URL' => $arItem['DETAIL_PAGE_URL'],
                    );
                }
            }
        } elseif ($arParams['PROPERTY'] == 'IBLOCK_SECTION_ID') {
            $arSections = array();
            $rsElement = CIBlockElement::GetList(
                array(),
                array(
                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    'INCLUDE_SUSBSECTIONS' => 'N',
                ),
                array('IBLOCK_SECTION_ID')
            );
            while ($arElement = $rsElement->GetNext()) {
                if (!empty($arElement['IBLOCK_SECTION_ID'])) {
                    $arSections[$arElement['IBLOCK_SECTION_ID']] = $arElement['CNT'];
                }
            }

            $rsItems = CIBlockSection::GetList(
                array('NAME' => 'ASC'),
                array(
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                    'ID' => array_keys($arSections),
                ),
                false,
                array('ID', 'NAME', 'SECTION_PAGE_URL')
            );
            while ($arItem = $rsItems->GetNext()) {
                if (!empty($arItem['NAME'])) {
                    $letter = strtoupper(substr($arItem['NAME'], 0, 1));
                    $arAbc[$letter][$arItem['NAME']] = array(
                        'NAME' => $arItem['NAME'],
                        'URL' => $arItem['SECTION_PAGE_URL'],
                        'CNT' => $arSections[$arItem['ID']],
                    );
                }
            }
        } else {
            $rsElements = CIBlockElement::GetList(
                array(),
                array(
					'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                    'ACTIVE' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                    '!PROPERTY_' . $arParams['PROPERTY'] . '_VALUE' => false,
                ),
                array('PROPERTY_' . $arParams['PROPERTY'])
            );
            while ($arItem = $rsElements->Fetch()) {
                if (!empty($arItem['PROPERTY_' . $arParams['PROPERTY'] . '_VALUE'])) {
                    $letter = strtoupper(substr($arItem['PROPERTY_' . $arParams['PROPERTY'] . '_VALUE'], 0, 1));
                    $arAbc[$letter][$arItem['PROPERTY_' . $arParams['PROPERTY'] . '_VALUE']] = array(
                        'NAME' => $arItem['PROPERTY_' . $arParams['PROPERTY'] . '_VALUE'],
                        'URL' => $APPLICATION->GetCurPageParam($arParams['REQUEST_KEY'] . '=' . htmlspecialchars($arItem['PROPERTY_' . $arParams['PROPERTY'] . '_VALUE'], ENT_QUOTES), array($arParams['REQUEST_KEY'])),
                        'CNT' => $arItem['CNT'],
                    );
                }
            }
        }
        ksort($arAbc);
        foreach ($arAbc as $key => $value) {
            $valueKey = array_keys($value);
            natsort($valueKey);
            foreach ($valueKey as $val) {
                $arResult['ABC'][$key][$val] = $value[$val];
            }
        }
        $this->IncludeComponentTemplate();
    }
}
?>
