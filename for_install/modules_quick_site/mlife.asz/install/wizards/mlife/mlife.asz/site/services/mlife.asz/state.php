<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
$wizard =& $this->GetWizard();
if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

if(COption::GetOptionString("mlife.asz", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA){
	return;
}

WizardServices::IncludeServiceLang("state.php", "ru");

$baseCheck = \Mlife\Asz\CountryTable::getList(
		array(
			'select' => array('ID'),
			'filter' => array("SITEID"=>WIZARD_SITE_ID),
			'limit' => 1,
		)
	);
if(!$baseCheck->Fetch()){
	$arFields = Array(
		"CODE2" => "BY",
		"CODE3" => "BUR",
		"SITEID" => WIZARD_SITE_ID,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_1"),
		"ACTIVE" => "Y",
	);
	$res = \Mlife\Asz\CountryTable::add($arFields);
	$country = $res->getId();
	
	$arFields2 = Array(
		"CODE2" => "MI",
		"CODE3" => "MIN",
		"COUNTRY" => $country,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_2"),
		"ACTIVE" => "Y",
		"SORT" => "500",
	);
	\Mlife\Asz\StateTable::add($arFields2);
	
	$arFields2 = Array(
		"CODE2" => "GO",
		"CODE3" => "GOM",
		"COUNTRY" => $country,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_3"),
		"ACTIVE" => "Y",
		"SORT" => "500",
	);
	\Mlife\Asz\StateTable::add($arFields2);
	
	$arFields2 = Array(
		"CODE2" => "VI",
		"CODE3" => "VIT",
		"COUNTRY" => $country,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_4"),
		"ACTIVE" => "Y",
		"SORT" => "500",
	);
	\Mlife\Asz\StateTable::add($arFields2);
	
	$arFields2 = Array(
		"CODE2" => "MO",
		"CODE3" => "MOG",
		"COUNTRY" => $country,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_4"),
		"ACTIVE" => "Y",
		"SORT" => "500",
	);
	\Mlife\Asz\StateTable::add($arFields2);
	
	$arFields2 = Array(
		"CODE2" => "BR",
		"CODE3" => "BRE",
		"COUNTRY" => $country,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_5"),
		"ACTIVE" => "Y",
		"SORT" => "500",
	);
	\Mlife\Asz\StateTable::add($arFields2);
	
	$arFields2 = Array(
		"CODE2" => "GR",
		"CODE3" => "GRO",
		"COUNTRY" => $country,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATE_6"),
		"ACTIVE" => "Y",
		"SORT" => "500",
	);
	\Mlife\Asz\StateTable::add($arFields2);
}
