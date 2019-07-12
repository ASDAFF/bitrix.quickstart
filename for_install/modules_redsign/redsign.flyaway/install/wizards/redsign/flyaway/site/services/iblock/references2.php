<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$COLOR_ID = $_SESSION["ESHOP_HBLOCK_COLOR_ID"];
unset($_SESSION["ESHOP_HBLOCK_COLOR_ID"]);

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
		"color1" => "references_files/uf/e47/e47a6cd28f0145616c141cec469cc5e4.jpg",
		"color2" => "references_files/uf/de9/de9c2b6a133f0322339b5b41977a65e2.jpg",
		"color3" => "references_files/uf/12f/12f9782618f4443e5bc4e5b59b2ad68d.jpg",
		"color4" => "references_files/uf/5b3/5b367f5dd7a3d1b902ca5ed984550817.jpg",
		"color5" => "references_files/uf/9f0/9f0512e7b04ae4add7a05b5eb15f5aa4.jpg",
		"color6" => "references_files/uf/111/11165ac68fd42dacee0acd62f5897a23.png",
		"color7" => "references_files/uf/305/3050e07e3cef1b3693818de6091c18c9.jpg",
		"color8" => "references_files/uf/10d/10d9f134c31ab0c2668570556f2c7e6d.png",
		"color9" => "references_files/uf/659/65907e935d6e60e019de62e2710c53b4.jpg",
		"color10" => "references_files/uf/b80/b80e2c454fc8cfcefa973f5a15d97700.jpg",
		"color11" => "references_files/uf/ce4/ce4c8cc03bfff1bc4a05c831118e72d0.jpg",
		"color12" => "references_files/uf/7c4/7c4c6104cabec5352abd8784b6eb4e5a.png",
		"color13" => "references_files/uf/bb1/bb1e3e28c6761c7dc4c8536d8052a61a.jpg",
		"color14" => "references_files/uf/008/00837aa90a4d27c0718e900d20599b65.jpg",
		"color15" => "references_files/uf/843/843f6a51d2e483995f3e34e9cf914663.jpg",
		"color16" => "references_files/uf/22e/22edb86c3514cd5e0ac5da00f6aa5356.png",
		"color17" => "references_files/uf/7a1/7a18ea9b9ff313c83acaba4cb9303f11.jpg",
		"color18" => "references_files/uf/666/666ef9871764d399914291190775426c.jpg",
		"color19" => "references_files/uf/ff3/ff3552f0770a74f4a709fb6216a55d1e.jpg",
		"color20" => "references_files/uf/78b/78b8e642ee9c9dd0a28c6b24603fa514.jpg",
		"color21" => "references_files/uf/a84/a8474c8bbc994ee1e866b5cfc19a85f7.jpg",
		"color22" => "references_files/uf/8b0/8b01cb208f36af6aa5fa0651fd563ac6.jpg",
		"color24" => "references_files/uf/f5a/f5a96e763d568045a2b77752bb92c725.jpg",
		"color25" => "references_files/uf/5a4/5a47973bfaa6f4e874a7953bb758cee1.jpg",
		"color26" => "references_files/uf/773/7730a10d6c93ff95145aec9a088fbe22.jpg",
		"color27" => "references_files/uf/e0a/e0afb4cf0cda9b1a9596b771fc61ba4d.jpg",
		"color28" => "references_files/uf/cee/cee068f41d53b73802d73e0403283001.jpg",
		"color29" => "references_files/uf/647/6479a9022d1895231233d65adc62fe84.jpg",
		"color30" => "references_files/uf/ab1/ab183bbacb740748f74b36a108f34011.jpg",
		"color31" => "references_files/uf/434/434c1190336b5776f550c054b6807c2b.jpg",
		"color32" => "references_files/uf/250/25093f3768a128e0d294096bdedce8c7.JPG",
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

/*****************************************************************************************************************/

if (WIZARD_THEME_ID == 'default' || !file_exists(WIZARD_SERVICE_RELATIVE_PATH.'/hl/'.WIZARD_THEME_ID.'.php'))
	return true;

// $entityName
// $tableName
// $arData
include_once(WIZARD_SERVICE_RELATIVE_PATH.'/hl/'.WIZARD_THEME_ID.'.php');

$THEME_ID = $_SESSION["ESHOP_HBLOCK_".$tableName."_ID"];
unset($_SESSION["ESHOP_HBLOCK_".$tableName."_ID"]);

WizardServices::IncludeServiceLang(WIZARD_THEME_ID.".php", LANGUAGE_ID);

if ($THEME_ID)
{
	$hldata = HL\HighloadBlockTable::getById($THEME_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	//$arData = array(
	//	"color1" => "references_files/uf/e47/e47a6cd28f0145616c141cec469cc5e4.jpg",
	//	"color2" => "references_files/uf/de9/de9c2b6a133f0322339b5b41977a65e2.jpg",
	//);
	$sort = 0;
	foreach($arData as $keyName => $file)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_THEME_".$keyName),
			'UF_FILE' =>
				array (
					'name' => ToLower($keyName).".jpg",
					'type' => 'image/jpeg',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$file
				),
			'UF_SORT' => $sort,
			'UF_DEF' => ($sort > 100) ? "0" : "1",
			'UF_XML_ID' => ToLower($keyName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$THEME_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$THEME_ID, null, $arData);

		$result = $entity_data_class::add($arData);
	}
}

