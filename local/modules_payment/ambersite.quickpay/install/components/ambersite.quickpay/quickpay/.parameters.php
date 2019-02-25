<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;
	
$dbIblockType = CIBlockType::GetList();
$iblocktype = array();
while ($arIblockType = $dbIblockType->GetNext(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'))) {
	if ($arIblockTypeLang = CIBlockType::GetByIDLang($arIblockType['ID'], LANGUAGE_ID))
	$iblocktype[$arIblockType['ID']] = $arIblockTypeLang['NAME'];
}

$dbIblockId = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y')); $iblockid = array();
while ($arIblockId = $dbIblockId->GetNext()) {
	$iblockid[$arIblockId['ID']] = $arIblockId['NAME'];
}

if (0 < intval($arCurrentValues['IBLOCK_ID_CATALOG'])) {
$dbProps = CIBlockProperty::GetList(array(),array('ACTIVE' => 'Y', 'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID_CATALOG']));
$payprop = array(); 
while ($arProps = $dbProps->GetNext()) {
	if (in_array($arProps['PROPERTY_TYPE'], array('N')) && $arProps['CODE']) {
		$payprop[$arProps['CODE']] = '['.$arProps['CODE'].'] '.$arProps['NAME'];
	}
}}
	
$arComponentParameters = array(
   "GROUPS" => array(
	  "GROUP1" => array(
		 "NAME" => GetMessage("OPLATA")
	  ),
	  "GROUP2" => array(
		 "NAME" => GetMessage("ISTOCHNIK_DANNUH")
	  ),
	  "GROUP3" => array(
		 "NAME" => GetMessage("OFORMLENIE")
	  ),
	  "GROUP4" => array(
		 "NAME" => "JQuery"
	  ),
   ),
   "PARAMETERS" => array(
	  "YAMONEY" => array(
		 "PARENT" => "GROUP1",
		 "NAME" => GetMessage("NOMER_YANDEX_KOSHELKA"),
		 "TYPE" => "STRING",
	  ),
	  "SECRETKEY" => array(
		 "PARENT" => "GROUP1",
		 "NAME" => GetMessage("SEKRETNOE_SLOVO"),
		 "TYPE" => "STRING",
	  ),
	  "PAYTYPE" => array(
		 "PARENT" => "GROUP1",
		 "NAME" => GetMessage("SPOSOBU_OPLATU"),
		 "TYPE" => "LIST",
		 "VALUES" => array("AC"=>GetMessage("S_BANKOVSKOJ_KARTU"), "PC"=>GetMessage("OPLATA_IZ_KOSHELKA_V_YANDEX_DENGAH"), "MC"=>GetMessage("S_BALANSA_MOBILNOGO")),
		 "MULTIPLE" => "Y",
		 "DEFAULT" => array("AC","PC","MC"),
	  ),
	  "COMMISSION" => array(
		 "PARENT" => "GROUP1",
		 "NAME" => GetMessage("VKLYUCHAT_KOMISSIYU"),
		 "TYPE" => "CHECKBOX",
		 "DEFAULT" => "Y"
	  ),
	  "IBLOCK_ID_CATALOG" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("INFOBLOK"),
		 "TYPE" => "LIST",
		 "VALUES" => $iblockid,
		 "ADDITIONAL_VALUES" => "N",
		 "REFRESH" => "Y"
	  ),
	  "IBLOCK_PAYPROP_ID" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("SVOJSTVO_S_CENOJ"),
		 "TYPE" => "LIST",
		 "VALUES" => $payprop,
		 "ADDITIONAL_VALUES" => "N"
	  ),
	  "ELEMENT_ID_CATALOG" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("ID_ELEMENTA"),
		 "TYPE" => "STRING",
		 "DEFAULT" => '={$_REQUEST["qp_id"]}'
	  ),
	  "ENABLE_ALT" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("RAZRESHIT_ALT_POLYA"),
		 "TYPE" => "CHECKBOX",
		 "DEFAULT" => "Y"
	  ),
	  "ALT_NAME" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("ALTERNATIVNOE_ZNACHENIE_IMENI"),
		 "TYPE" => "STRING",
		 "DEFAULT" => '={$_REQUEST["qp_name"]}'
	  ),
	  "ALT_PRICE" => array(
		 "PARENT" => "GROUP2",
		 "NAME" => GetMessage("ALTERNATIVNOE_ZNACHENIE_CENU"),
		 "TYPE" => "STRING",
		 "DEFAULT" => '={$_REQUEST["qp_price"]}'
	  ),
	  "FONT_RC" => array(
		 "PARENT" => "GROUP3",
		 "NAME" => GetMessage("SHRIFT_ROBOTO_CONDENSED"),
		 "TYPE" => "CHECKBOX",
		 "DEFAULT" => "Y"
	  ),
	  "COLOR_PAYBTN" => array(
		 "PARENT" => "GROUP3",
		 "NAME" => GetMessage("CVET_KNOPKI_OPLATIT"),
		 "TYPE" => "COLORPICKER",
		 "DEFAULT" => "#6BBA50"
	  ),
	  "COLOR_TEXTPAYBTN" => array(
		 "PARENT" => "GROUP3",
		 "NAME" => GetMessage("CVET_TEXTA_KNOPKI_OPLATIT"),
		 "TYPE" => "COLORPICKER",
		 "DEFAULT" => "#FFF"
	  ),
	  "USETITLE" => array(
		 "PARENT" => "GROUP3",
		 "NAME" => GetMessage("YSTANAVLIVAT_ZAGOLOVOK_STRANICU"),
		 "TYPE" => "CHECKBOX",
		 "DEFAULT" => "Y"
	  ),
	  "USEH1" => array(
		 "PARENT" => "GROUP3",
		 "NAME" => GetMessage("DOBAVIT_ZAGOLOVOK_H1"),
		 "TYPE" => "CHECKBOX",
		 "DEFAULT" => "N"
	  ),
	  "JQUERY" => array(
		 "PARENT" => "GROUP4",
		 "NAME" => GetMessage("PODKLYUCHYAT_JQUERY"),
		 "TYPE" => "CHECKBOX",
		 "DEFAULT" => "Y"
	  )
   )
);
	
?>