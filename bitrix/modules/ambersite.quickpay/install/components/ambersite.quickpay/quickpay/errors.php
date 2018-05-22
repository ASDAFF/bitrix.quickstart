<?
if(CModule::IncludeModuleEx('ambersite.quickpay')=='0') {echo GetMessage("NOTIFY_NOT_FOUND"); $error = true;}
if(CModule::IncludeModuleEx('ambersite.quickpay')=='3') {echo GetMessage("NOTIFY_DEMO_EXPIRED"); $error = true;}
if(!$arParams['YAMONEY']) {ShowError(GetMessage('YKAGITE_NOMER_YANDEX_KOSHELKA_V_PARAMETRAH')); $error = true;}
//if(!$arParams['SECRETKEY']) {ShowError(GetMessage('YKAGITE_SEKRETNOE_SLOVO_V_PARAMETRAH')); $error = true;}
if(!$arParams['PAYTYPE']) {ShowError(GetMessage('YKAGITE_HOTYA_BU_ODIN_SPOSOB_OPLATU_V_PARAMETRAH')); $error = true;}
if(!$_REQUEST['qp_order']) {
	if(!$arParams['ELEMENT_ID_CATALOG'] && !$arParams['ALT_NAME'] && !$arParams['ALT_PRICE']) {ShowError(GetMessage('OTSUTSTVUYUT_VHODNUE_DANNUE')); $error = true;}
	if($arParams['ELEMENT_ID_CATALOG'] && !intval($arParams['ELEMENT_ID_CATALOG'])) {ShowError(GetMessage('NEVERNOE_ZNACHENIE_IDENTIFIKATORA_ELEMENTA')); $error = true;}
	if(intval($arParams['ELEMENT_ID_CATALOG'])>0 && !$arParams['IBLOCK_ID_CATALOG']) {ShowError(GetMessage('YKAGITE_INFOBLOK_V_PARAMETRAH')); $error = true;}
	if(intval($arParams['ELEMENT_ID_CATALOG'])>0 && $arParams['IBLOCK_ID_CATALOG'] && !$arParams['IBLOCK_PAYPROP_ID']) {ShowError(GetMessage('YKAGITE_SVOJSTVO_S_CENOJ_V_PARAMETRAH')); $error = true;}
	if(intval($arParams['ELEMENT_ID_CATALOG'])>0 && $arParams['IBLOCK_ID_CATALOG'] && $arParams['IBLOCK_PAYPROP_ID']) {
		CModule::IncludeModule('iblock');
		$dbElem = CIBlockElement::GetByID($arParams['ELEMENT_ID_CATALOG']); if($arElem = $dbElem->GetNext()) {$elemexist=true;}
		if(!$elemexist) {ShowError(GetMessage('NEVERNOE_ZNACHENIE_IDENTIFIKATORA_ELEMENTA')); $error = true;}
		if($elemexist) {
			$dbProp = CIBlockElement::GetProperty($arParams['IBLOCK_ID_CATALOG'], $arParams['ELEMENT_ID_CATALOG'], array(), Array("CODE"=>$arParams['IBLOCK_PAYPROP_ID'])); if ($arProp = $dbProp->GetNext()) {$propexist=true; if(!intval($arProp['VALUE'])) {ShowError(GetMessage('NEVERNOE_ZNACHENIE_SVOJSTVA_CENU_U_ELEMENTA')); $error = true;}}
		if(!$propexist) {ShowError(GetMessage('NEVERNOE_SVOJSTVO_ELEMENTA')); $error = true;}	
		}
	}
	if(!$arParams['ELEMENT_ID_CATALOG'] && $arParams['ENABLE_ALT']=='Y' && (!$arParams['ALT_NAME'] || !$arParams['ALT_PRICE'])) {ShowError(GetMessage('OTSUTSTVUET_ODIN_IZ_PARAMETROV_ALTERNATIVNUH_ZNACHENIJ')); $error = true;}
	if(!$arParams['ELEMENT_ID_CATALOG'] && $arParams['ENABLE_ALT']=='N' && ($arParams['ALT_NAME'] || $arParams['ALT_PRICE'])) {ShowError(GetMessage('ALTERNATIVNUE_POLYA_ZAPRESHENU_V_PARAMETRAH')); $error = true;}
	if($arParams['ALT_PRICE'] && !intval($arParams['ALT_PRICE'])) {ShowError(GetMessage('NEVERNOE_ZNACHENIE_ALTERNATIVNOGO_SVOJSTVA_CENU')); $error = true;}
}
?>