<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/*
 * Code is distributed as-is
 * the Developer may change the code at its discretion without prior notice
 * Developers: Djo 
 * Website: http://zixn.ru
 * Twitter: https://twitter.com/Zixnru
 * Email: izm@zixn.ru
 */

$arResult = array();
$time_cashe = 604800; //7 суток
$uniq_key_cashe = md5(serialize($arParams));
$cashe_dir = '/catalog/zixnru_getproperties_products';

//Кэш
if ($this->StartResultCache($time_cashe, $uniq_key_cashe, $cashe_dir)) {
    $idElem = $arParams['ID'];
    $arResult = $this->getProps($arParams, $idElem);
    if (empty($arResult)) {//Прерываем кэширование
        $this->AbortResultCache();
    }
    $this->includeComponentTemplate();
}




