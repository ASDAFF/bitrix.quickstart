<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;


$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/cupon.xml"; 

	$permissions = Array(
			"1" => "X",
			"2" => "R"
		);
	$iblockID = WizardServices::ImportIBlockFromXML(
		$iblockXMLFile,
		"discount_coupon",
		'catalog',
		WIZARD_SITE_ID,
		$permissions
	);

	if ($iblockID < 1)
		return;
	
	
$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "CODE"=>'last_name',"IBLOCK_ID"=>$iblockID));
if ($prop_fields = $properties->Fetch())
{
  $PROPERTY_LAST_NAME_ID =  $prop_fields["ID"];
}

$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "CODE"=>'name',"IBLOCK_ID"=>$iblockID));
if ($prop_fields = $properties->Fetch())
{
  $PROPERTY_NAME_ID =  $prop_fields["ID"];
}

$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "CODE"=>'patronymic',"IBLOCK_ID"=>$iblockID));
if ($prop_fields = $properties->Fetch())
{
  $PROPERTY_PATRONYMIC_ID =  $prop_fields["ID"];
}
/*
$NEW_IB = new CIBlock;
$NEW_IB->Update($iblockID, array('EDIT_FILE_BEFORE' => '/bitrix/php_interface/coupon_edit.php'));
*/

CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/discount_coupon/index.php", array("COUPON_IBLOCK_ID" => $iblockID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/discount_coupon/index.php", array("LAST_NAME_ID" => $PROPERTY_LAST_NAME_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/discount_coupon/index.php", array("NAME_ID" => $PROPERTY_NAME_ID));
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/discount_coupon/index.php", array("PATRONYMIC_ID" => $PROPERTY_PATRONYMIC_ID));


?>