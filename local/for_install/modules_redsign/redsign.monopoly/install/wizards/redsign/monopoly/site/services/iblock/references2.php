<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$COLOR_ID = $_SESSION["ESHOP_HBLOCK_COLOR_ID"];
unset($_SESSION["ESHOP_HBLOCK_COLOR_ID"]);

$BRAND_ID = $_SESSION["ESHOP_HBLOCK_BRAND_ID"];
unset($_SESSION["ESHOP_HBLOCK_BRAND_ID"]);

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

if ($COLOR_ID)
{
	$hldata = HL\HighloadBlockTable::getById($COLOR_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arColors = array(
		"WHITE" => "references_files/uf/74e/74e65087a31e3e1220028214423908a7.jpg",
		"RED" => "references_files/uf/544/544d8c4ea617102808554491d01c5e39.jpg",
		"ORANGE" => "references_files/uf/d90/d9060844c6260ed9784d037f49c5063d.jpg",
		"BROWN" => "references_files/uf/768/7682fa9cf794fbef90043a55bfc8b96a.jpg",
	);
	$sort = 0;
	
	$rsData = $entity_data_class::getList(array(
		"filter" => array('UF_XML_ID' => array_map('strtolower', array_keys($arColors)))
	));
	
	while ($arData = $rsData->fetch()) {
		unset($arColors[strtoupper($arData['UF_XML_ID'])]);
	}
	
	foreach($arColors as $colorName=>$colorFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_COLOR_".$colorName),
			'UF_FILE' =>
				array (
					'name' => ToLower($colorName).".jpg",
					'type' => 'image/jpeg',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$colorFile
				),
			'UF_SORT' => $sort,
			'UF_DEF' => ($sort > 100) ? "0" : "1",
			'UF_XML_ID' => ToLower($colorName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$COLOR_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$COLOR_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}

if ($BRAND_ID)
{
	$hldata = HL\HighloadBlockTable::getById($BRAND_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arBrands = array(
		"BRAND1" => "",
		"BRAND2" => "",
		"BRAND3" => "",
		"BRAND4" => "",
		"BRAND5" => "",
		"BRAND6" => "",
		"BRAND7" => "",
		"BRAND8" => "",
		"BRAND9" => "",
		"BRAND10" => "",
		"BRAND11" => "",
		"BRAND12" => "",
		"BRAND13" => "",
		"BRAND14" => "",
		"BRAND15" => "",
		"BRAND16" => "",
		"BRAND17" => "",
		"BRAND18" => "",
		"BRAND19" => "",
		"BRAND20" => "",
		"BRAND21" => "",
		"BRAND22" => "",
		"BRAND23" => "",
		"BRAND24" => "",
		"BRAND25" => "",
		"BRAND26" => "",
		"BRAND27" => "",
		"BRAND28" => "",
		"BRAND29" => "",
		"BRAND30" => "",
	);
	
	$rsData = $entity_data_class::getList(array(
		"filter" => array('UF_XML_ID' => array_map('strtolower', array_keys($arBrands)))
	));
	
	while ($arData = $rsData->fetch()) {
		unset($arBrands[strtoupper($arData['UF_XML_ID'])]);
	}

	$sort = 0;
	foreach($arBrands as $brandName=>$brandFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_BRAND_".$brandName),
			'UF_FILE' =>
				array (
					/*
					'name' => ToLower($brandName).".png",
					'type' => 'image/png',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$brandFile
					*/
				),
			'UF_SORT' => $sort,
			//'UF_DESCRIPTION' => GetMessage("WZD_REF_BRAND_DESCR_".$brandName),
			//'UF_FULL_DESCRIPTION' => GetMessage("WZD_REF_BRAND_FULL_DESCR_".$brandName),
			'UF_XML_ID' => ToLower($brandName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$BRAND_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$BRAND_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}
