<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("highloadblock"))
	return;

$COLOR_ID = $_SESSION["GORPO_HBLOCK_COLOR_ID"];
unset($_SESSION["GORPO_HBLOCK_COLOR_ID"]);

$BRAND_ID = $_SESSION["GORPO_HBLOCK_BRAND_ID"];
unset($_SESSION["GORPO_HBLOCK_BRAND_ID"]);

//adding rows
WizardServices::IncludeServiceLang("index.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

if ($COLOR_ID)
{
	$hldata = HL\HighloadBlockTable::getById($COLOR_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arColors = array(
		"color1" => "files/uf/6ad/6ad58e70d3c34491cc6cc01b90f26aac.jpg",
		"color2" => "files/uf/176/176d7a2e4981f7769adb45add9485bf0.jpg",
		"color3" => "files/uf/a17/a17c377db16b209b5438cc2211217e46.jpg",
		"color4" => "files/uf/2bc/2bc0b86a5d81fcaa45d78491536a4551.jpg",
		"color5" => "files/uf/b99/b9937b59024d5b749004e1bb5253090a.jpg",
		"color6" => "files/uf/aeb/aeb96f0f3307145f633cb84bb25fbd40.jpg",
		"color7" => "files/uf/6bf/6bf2b13ba0092377636391a3c8ed5a7a.jpg",
		"color8" => "files/uf/765/765b4a077951e03e8d343f63ae02cc66.jpg",
		"color9" => "files/uf/7b6/7b6cc65cd7f444556c2a3e12129df596.jpg",
		"color10" => "files/uf/b4e/b4e127422f266f50f300c521a55b4cb0.jpg",
		"color11" => "files/uf/0a5/0a574a46a34956033d4836ce89f2c216.jpg",
		"color12" => "files/uf/426/426897b7eba04d3386a5b74e04c80616.jpg",
		"color13" => "files/uf/d75/d759f7f06fff904984bfec055a4a0b91.jpg",
		"color14" => "files/uf/002/0025323696870e4c65920fb86aac9709.jpg",
		"color15" => "files/uf/551/551d879faa6c01d14bd1f64d1f622d70.jpg",
		"color16" => "files/uf/534/534d474004c186917d173cde0cf7a8be.jpg",
		"color17" => "files/uf/5bb/5bbe596cd3ab5eb396d5bfdd830b9f77.jpg",
		"color18" => "files/uf/101/101fd89fc8d026e5e781fb2e5e49dadd.jpg",
		"color19" => "files/uf/283/283325651a12bfbca40b5e86502ce49c.jpg",
		"color20" => "files/uf/050/050a4f9a77734f319137b5f31258c43a.jpg",
		"color21" => "files/uf/344/344acbe0c65a8e6958fcc20b3a44162a.jpg",
		"color22" => "files/uf/415/4156730690fe4ebdd4cd1d442af9e700.jpg",
		"color24" => "files/uf/5f0/5f0b09eed502696b64688b6f1e90b534.jpg",
		"color25" => "files/uf/073/073f7e569ec34eb8ce9ebcd8cc8bfa8b.jpg",
		"color26" => "files/uf/4f7/4f79f43a7ec06ab724a6a72c40ee23d9.jpg",
		"color27" => "files/uf/a4c/a4c272b5106c89dfb57475184c715c1d.jpg",
		"color28" => "files/uf/feb/feb18e1ad5df0fd7a3e24ca672c9c3b6.jpg",
		"color29" => "files/uf/35f/35f505df41a196e8779d6c39d6b7b3eb.jpg",
		"color30" => "files/uf/e7a/e7a99b263276a50e5ff11605910265e7.jpg",
		"color31" => "files/uf/613/6139715adf5e6bcbf3b85939e0465cc7.jpg",
		"color32" => "files/uf/f5f/f5f11a77701e82e44cbfc0c20a2fefb0.jpg",
	);
	$sort = 0;
	foreach($arColors as $colorName=>$colorFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage("GOPRO_HL_COLORS_".$colorName),
			'UF_FILE' =>
				array (
					'name' => $colorName.".jpg",
					'type' => 'image/jpeg',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/hl/".$colorFile
				),
			'UF_SORT' => $sort,
			'UF_DEF' => ($sort > 100) ? "0" : "1",
			'UF_XML_ID' => $colorName
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
		"brand1" => "",
		"brand2" => "",
		"brand3" => "",
		"brand4" => "",
		"brand5" => "",
		"brand6" => "",
		"brand7" => "",
		"brand8" => "",
		"brand9" => "",
		"brand10" => "",
		"brand11" => "",
		"brand12" => "",
		"brand13" => "",
		"brand14" => "",
	);
	$sort = 0;
	foreach($arBrands as $brandName=>$brandFile)
	{
		$sort+= 100;
		$arData = array(
			'UF_NAME' => GetMessage('GOPRO_HL_BRANDS_'.$brandName),
			'UF_FILE' => array(),
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