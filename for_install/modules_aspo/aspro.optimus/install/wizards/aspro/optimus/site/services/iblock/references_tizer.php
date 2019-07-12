<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";	

set_time_limit(0);

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$HL_ID = $_SESSION["OPTIMUS_HBLOCK_TIZER_ID"];
unset($_SESSION["OPTIMUS_HBLOCK_TIZER_ID"]);

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

if($HL_ID){
	$hldata = HL\HighloadBlockTable::getById($HL_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arTizers = array(
		"BONUS" => "references_files/iblock/d11/d1155780f7cb725a39446afb337697d2.png",
		"SERT" => "references_files/iblock/d29/d29a609f0d35a73bb78a4b133d044a8d.png",
		"DENY" => "references_files/iblock/195/195080058e597adc89dae60daab97727.png",
		"DELIVERY" => "references_files/iblock/abe/abedf713e6a85455b3932dc51d02067f.png",
	);
	$sort = 100;
	foreach($arTizers as $tizerName => $tizerFile){
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_TIZER_".$tizerName),
			'UF_FILE' =>
				array (
					'name' => ToLower($tizerName).".png",
					'type' => 'image/png',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$tizerFile
				),
			'UF_SORT' => $sort,
			'UF_XML_ID' => ToLower($tizerName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$HL_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$HL_ID, null, $arData);
		$result = $entity_data_class::add($arData);
		$sort += 100;
	}
}
?>