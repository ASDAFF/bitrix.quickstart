<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!CModule::IncludeModule("catalog"))
    return;

$discount = array();

$discount[] = Array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_1'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'15.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:101;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_2') , "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'8.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:144;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=>GetMessage('DISCOUNT_3'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'10.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:103;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_4'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'18.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:106;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_5'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'30.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:124;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_6'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'10.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:123;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_7'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'5.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:125;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=>GetMessage('DISCOUNT_8'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'15.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:185;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_9'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'20.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:184;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_10'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'15.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:193;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=>GetMessage('DISCOUNT_11'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'10.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:202;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=>GetMessage('DISCOUNT_12'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'10.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:140;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_13'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'30.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:190;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_14'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'20.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:156;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_15'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'30.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:161;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_16') , "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'15.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:200;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_17') , "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'15.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:139;}}}}'
);
$discount[] = array(
    "SITE_ID" => WIZARD_SITE_ID,
    "ACTIVE"=>'Y', "NAME"=> GetMessage('DISCOUNT_18'), "MAX_USES"=>'0', "COUNT_USES"=>'0', "COUPON"=>'', "SORT"=>'100', "MAX_DISCOUNT"=>'0.0000', "VALUE_TYPE"=>'P', "VALUE"=>'5.0000', "CURRENCY"=>'RUB', "MIN_ORDER_SUM"=>'0.0000', "NOTES"=>'', "RENEWAL"=>'N', "ACTIVE_FROM"=>'', "ACTIVE_TO"=>'', "PRIORITY"=>'1', "LAST_DISCOUNT"=>'Y', "VERSION"=>'2', "CONDITIONS"=>'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:3;a:2:{s:8:"CLASS_ID";s:13:"CondIBSection";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:203;}}}}'
);

//в этом массиве мы храним связку ID секции => XML_ID секции
$XML_ID = array(
    101=>18,
    144=>78,
    103=>20,
    106=>23,
    124=>59,
    123=>58,
    125=>60,
    185=>118,
    184=>117,
    193=>126,
    202=>138,
    140=>75,
    190=>123,
    156=>90,
    161=>95,
    200=>135,
    139=>74,
    203=>16
);

$ID_BY_XML = array();

//тут мы находим вновь созданные ID через XML_ID
$arFilter = Array('XML_ID'=>$XML_ID,"SITE_ID"=>WIZARD_SITE_ID);
$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true);
while($ar_result = $db_list->GetNext())
{
    $ID_BY_XML[$ar_result['XML_ID']] = $ar_result['ID'];
}

//тут мы пересоздаем массив $discount с новыми ID
foreach($discount as &$arFields)
{
    $COND = unserialize($arFields['CONDITIONS']);
    if(is_array($COND['CHILDREN']))
    {
        foreach($COND['CHILDREN'] as &$v)
        {
            $CHILDREN_ID = $v['DATA']['value'];
            $GET_XML_ID = $XML_ID[$CHILDREN_ID];
            $v['DATA']['value'] = $ID_BY_XML[$GET_XML_ID];
        }
    }
    $arFields['CONDITIONS'] = serialize($COND);
}

//тут добавляем скидки
foreach($discount as $arFields){
    CCatalogDiscount::Add($arFields);
}
?>