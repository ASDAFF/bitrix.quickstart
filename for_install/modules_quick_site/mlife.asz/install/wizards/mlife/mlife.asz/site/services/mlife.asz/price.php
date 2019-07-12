<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
$wizard =& $this->GetWizard();
if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

if(COption::GetOptionString("mlife.asz", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA){
	return;
}

WizardServices::IncludeServiceLang("price.php", "ru");

$entity = \Mlife\Asz\PricetipTable::getEntity();
$connection = \Bitrix\Main\Application::getConnection();
$helper = $connection->getSqlHelper();
$tableName = $entity->getDBTableName();
$where = 'SITE_ID="'.WIZARD_SITE_ID.'"';
$sql = "DELETE FROM ".$tableName." WHERE ".$where;
$connection->queryExecute($sql);

$res = \Mlife\Asz\PricetipTable::add(array(
	"CODE" => "BASE",
	"NAME" => GetMessage("MLIFE_ASZ_WZ_PRICE_1"),
	"BASE" => "Y",
	"GROUP" => array(),
	"SITE_ID" => WIZARD_SITE_ID
));
$priceId = $res->getId();
if($priceId){
	$addAr = array("IDTIP"=>$priceId,"IDGROUP"=>null);
	\Mlife\Asz\PricetiprightTable::add($addAr);
}

if($priceId) {
	
	$IB = COption::GetOptionString("mlife.asz", "tempib", "", WIZARD_SITE_ID);
	
	$res = CIBlockElement::GetList(Array(), array("IBLOCK_ID"=>$IB), false, false, array("ID"));
	while($ar = $res->Fetch()){
		$arFields = array(
			"ID" => $ar["ID"],
			"IBLOCK_ID" => $IB,
			"RESULT" => 1
		);
		$str = "kol:::".rand(0,10).":::0+++cod".$priceId.":::".rand(5000,150000).":::".$wizard->GetVar("catalogCurency")."+++";
		CIBlockElement::SetPropertyValueCode($ar["ID"], "ASZ_SYSTEM", $str);
		\Mlife\Asz\Handlers::OnAfterIBlockElementAdd($arFields);
	}
	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", Array("PRICE" => $priceId));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/search/index.php", array("PRICE_CODE" => $priceId));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include_areas/search_block.php", array("PRICE_CODE" => $priceId));
}
