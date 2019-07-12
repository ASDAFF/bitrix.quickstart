<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule('sale'))
	return;

if (WIZARD_IS_RERUN)
	return;
$pricesTypes=array(
		"BASE" =>array(
				"USER_LANG"=>array(
				      "ru" => GetMessage("Retail"),
				      "en" => "Retail price"
				      ),
				"BASE" => "Y",
				"SORT" => 1,
				"NAME" => "BASE",
				"USER_GROUP" => Array(1, 2),
				"USER_GROUP_BUY" => Array(1, 2),				
			),     
	);	
foreach($pricesTypes as $price)
{
	$dbResultList = CCatalogGroup::GetList(Array(), Array("NAME" =>$price['NAME']));
	if($arResultList=$dbResultList->GetNext())
	{		
		CCatalogGroup::Update($arResultList['ID'], $price);
	}
	else
		CCatalogGroup::add($price);
}

	//настройка валют
	CModule::IncludeModule("currency");	
	
	COption::SetOptionString('sale','default_currency','RUB');
	$dbCur = CCurrency::GetList();
	while($arCur = $dbCur->Fetch())
	{	
		CCurrencyLang::Update($arCur["CURRENCY"], LANGUAGE_ID, Array("DECIMALS" => 0));			
	}
		
	CModule::IncludeModule("catalog");
	//настройка ндс
	$dbVat = CCatalogVat::GetList(array(), Array("SITE_ID" => WIZARD_SITE_ID));
	if(!($dbVat->Fetch()))
	{
		$arF = Array ("ACTIVE" => "Y", "SORT" => "100", "NAME" => GetMessage("WIZ_VAT_1"), "RATE" => 0);
		CCatalogVat::Set($arF);
		$arF = Array ("ACTIVE" => "Y", "SORT" => "200", "NAME" => GetMessage("WIZ_VAT_2"), "RATE" => 18);
		CCatalogVat::Set($arF);
	}
?>