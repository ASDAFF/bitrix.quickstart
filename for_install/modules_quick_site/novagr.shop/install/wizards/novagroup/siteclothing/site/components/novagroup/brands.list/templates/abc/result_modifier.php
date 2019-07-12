<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 01.04.14
 * Time: 20:17
 */

$ALFABET = $arResult['LAT_ABC'] + $arResult['RUS_ABC'] ;
$items = array();
foreach ($ALFABET as $ABC) {
    foreach ($arResult['BRANDS'] as $item) {
        if(mb_substr($item['NAME'],0,1)==$ABC){$items[$ABC][]=$item;
        }
    }
}
$arResult['BRANDS'] = $items;