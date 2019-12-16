<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();


if (intval($this->__component->SECTION_ID) < 1) {
    $FILTER_NAME = (string)$arParams["FILTER_NAME"];

    global ${$FILTER_NAME};
    if(!is_array(${$FILTER_NAME}))
        ${$FILTER_NAME} = array();

    $arFilter = $this->__component->makeFilter($FILTER_NAME);
    $arResult["ELEMENT_COUNT"] = CIBlockElement::GetList(array(), $arFilter, array(), false);
}

if ($arParams['INSTANT_RELOAD'] == 'Y' && $arParams['TEMPLATE_AJAXID']) {
	$arResult['FILTER_AJAX_URL'] .= '&amp;rs_ajax=Y&amp;&ajax_id='.$arParams['TEMPLATE_AJAXID'];
	$arResult['COMPONENT_CONTAINER_ID'] = $arParams['TEMPLATE_AJAXID'];
	$arResult['INSTANT_RELOAD'] = true;
}

$APPLICATION->RestartBuffer();
unset($arResult['COMBO']);
echo CUtil::PHPToJSObject($arResult, true);