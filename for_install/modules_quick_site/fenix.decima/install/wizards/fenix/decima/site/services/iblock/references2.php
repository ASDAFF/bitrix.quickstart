<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$COLOR_ID = $_SESSION["DECIMA_HBLOCK_COLOR_ID"];
unset($_SESSION["DECIMA_HBLOCK_COLOR_ID"]);

$BRAND_ID = $_SESSION["DECIMA_HBLOCK_BRAND_ID"];
unset($_SESSION["DECIMA_HBLOCK_BRAND_ID"]);

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
		"PURPLE" => "references_files/iblock/0d3/0d3ef035d0cf3b821449b0174980a712.jpg",
		"BROWN" => "references_files/iblock/f5a/f5a37106cb59ba069cc511647988eb89.jpg",
		"SEE" => "references_files/iblock/f01/f01f801e9da96ae5a7f26aae01255f38.jpg",
		"BLUE" => "references_files/iblock/c1b/c1ba082577379bdc75246974a9f08c8b.jpg",
		"ORANGERED" => "references_files/iblock/0ba/0ba3b7ecdef03a44b145e43aed0cca57.jpg",
		"REDBLUE" => "references_files/iblock/1ac/1ac0a26c5f47bd865a73da765484a2fa.jpg",
		"RED" => "references_files/iblock/0a7/0a7513671518b0f2ce5f7cf44a239a83.jpg",
		"GREEN" => "references_files/iblock/b1c/b1ced825c9803084eb4ea0a742b2342c.jpg",
		"WHITE" => "references_files/iblock/b0e/b0eeeaa3e7519e272b7b382e700cbbc3.jpg",
		"BLACK" => "references_files/iblock/d7b/d7bdba8aca8422e808fb3ad571a74c09.jpg",
		"PINK" => "references_files/iblock/1b6/1b61761da0adce93518a3d613292043a.jpg",
		"AZURE" => "references_files/iblock/c2b/c2b274ad2820451d780ee7cf08d74bb3.jpg",
		"JEANS" => "references_files/iblock/24b/24b082dc5e647a3a945bc9a5c0a200f0.jpg",
        "FLOWERS" => "references_files/iblock/64f/64f32941a654a1cbe2105febe7e77f33.jpg",
        "CORAL" => "references_files/coral.jpg",
        "BORDO" => "references_files/bordo.jpg",
        "GREY" => "references_files/grey.jpg",
        "LIGHT_PINK" => "references_files/light_pink.jpg",
        "YELLOW" => "references_files/yellow.jpg",
		"MINT" => "references_files/yellow.jpg",
	);
	$sort = 0;
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
        "Broadway" => "brands_files/Broadway.jpg",
        "F5" => "brands_files/F5.jpg",
        "Fox" => "brands_files/Fox.jpg",
        "Glamorise" => "brands_files/Glamorise.jpg",
        "INCITY" => "brands_files/INCITY.jpg",
        "Terranova" => "brands_files/Terranova.jpg",
        "Viania" => "brands_files/Viania.jpg",
        "ZARGA" => "brands_files/ZARGA.jpg",
        
        
    );
	$sort = 0;
	foreach($arBrands as $brandName=>$brandFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => $brandName,
			'UF_FILE' =>
				array (
					'name' => ($brandName).".jpg",
					'type' => 'image/jpg',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$brandFile
				),
			'UF_SORT' => $sort,
			'UF_DESCRIPTION' => '',
			'UF_FULL_DESCRIPTION' => '',
			'UF_XML_ID' => ToLower($brandName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$BRAND_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$BRAND_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}
?>