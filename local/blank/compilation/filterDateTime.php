<?php 
\Bitrix\Main\Loader::includeModule("itua.dealexchange");
$days = (int) \Bitrix\Main\Config\Option::get("itua.dealexchange", "DAYS_MODIFY");
$date = new \Bitrix\Main\Type\DateTime;
$strDate = '-'.$days.' day';
$arFilter = array( array('>=DATE_MODIFY'=>$date->add($strDate) ) );
