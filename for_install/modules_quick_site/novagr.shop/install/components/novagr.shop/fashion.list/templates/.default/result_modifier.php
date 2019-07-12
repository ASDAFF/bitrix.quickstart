<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    $cp = $this->__component; // объект компонента

    if (is_object($cp) and is_array($arResult['ITEMS']))
    {
        $cp->arResult['ITEMS'] = $arResult['ITEMS'];
        $cp->arResult['ORIGINAL'] = $arResult['ORIGINAL'];
        //кешируем
        $cp->SetResultCacheKeys(array('ITEMS','ORIGINAL'));
        //заносим в массив для доступа в шаблоне и эпилоге
        $arResult['ITEMS'] = $cp->arResult['ITEMS'];
        $arResult['ORIGINAL'] = $cp->arResult['ORIGINAL'];
    }
?>