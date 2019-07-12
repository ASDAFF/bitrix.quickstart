<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    $cp = $this->__component; // объект компонента

    if (is_object($cp) and is_array($arResult['ITEMS']))
    {
        $cp->arResult['ITEMS'] = $arResult['ITEMS'];   
        //кешируем
        $cp->SetResultCacheKeys(array('ITEMS'));
        //заносим в массив для доступа в шаблоне и эпилоге
        $arResult['ITEMS'] = $cp->arResult['ITEMS'];
    }
?>