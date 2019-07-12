<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

// подключаем скрипт
$APPLICATION->AddHeadScript($this->GetPath().'/script.js');

$arResult=array('ERRORS'=>array());

foreach ($arParams as $key=>$value) {
	$arParams[$key] = trim($value);
}

if (strlen($arParams['EMAIL'])>0) {
	// разбиваем email чтобы не светить его в html документе
	$mail = explode('@', $arParams['EMAIL']);
	$mail_ext = explode('.', $mail[1]);
	$arResult['NAME'] = $mail[0];
	$arResult['DOMEN'] = $mail_ext[0];
	$arResult['ZONE'] = $mail_ext[1];
	
	if (strlen($arParams['ELEMENT_CLASS'])>0) {
		$arResult['ELEMENT_CLASS'] = $arParams['ELEMENT_CLASS'];
	}
	
	$arResult['LINK'] = ($arParams['LINK']=='Y') ? TRUE : FALSE;
} else {
	$arResult['ERRORS'][]='Не заполнено поле Email';
}

//echo "<pre>"; print_r($arParams); echo "</pre>";
//echo "<pre>"; print_r($arResult); echo "</pre>";

$this->IncludeComponentTemplate();
?>