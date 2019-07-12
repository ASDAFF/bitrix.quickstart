<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule('sale'))
	return;

if (WIZARD_IS_RERUN)
	return;
$shopLocation =GetMessage("WIZ_CITY") ;
$dbLocation = CSaleLocation::GetList(Array("ID" => "ASC"), Array("LID" => "ru", "COUNTRY_NAME"=>GetMessage("WIZ_COUNTRY"), "CITY_NAME"=>$shopLocation));
if($arLocation = $dbLocation->Fetch())//if there are no data in module
{
	return;
}	

//импорт локаций
$lang = "ru";
	$wizard =& $this->GetWizard();	
	//импорт локаций//////////////////////////////////////////////////////////////////////////////////
	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");
	$csvFile = new CCSVData();
	$csvFile->LoadFile(dirname(__FILE__)."/data/ru/loc.csv");
	$csvFile->SetPos($_SESSION["LOC_POST"]);
	$csvFile->SetFieldsType("R");
	$csvFile->SetFirstHeader(false);
	$csvFile->SetDelimiter(",");
	$arLocation = Array();
	$arLocationMap = Array();
	
	$sopr=0;
	$tope=0;
	$def_loc_s=0;
	$def_loc_t=0;
	$def_loc=0;
	while ($arRes = $csvFile->Fetch())
	{
	 //@fwrite($handle,$arRes[0]." ".$arRes[2]." ".$arRes[4]."\n");
	 if ($arRes[0]=="S")
	 {
		/*$dbLocation=CSaleLocation::GetCountryList(
		 Array("NAME_LANG"=>"ASC"),
		 Array("NAME_LANG"=>$arRes[4]),
		 LANGUAGE_ID
		);
		if($arLocation = $dbLocation->Fetch())//if there are no data in module
		{*/
			$arCountry = array(
			"NAME" => $arRes[4],
			"SHORT_NAME" => $arRes[4],
				"ru" => array(
				"LID" => "ru",
				"NAME" => $arRes[4],
				"SHORT_NAME" => $arRes[4]
				),
				"en" => array(
				"LID" => "en",
				"NAME" => $arRes[2],
				"SHORT_NAME" => $arRes[2]
				)
			);
			$sopr=1;
			$CurCountryID = CSaleLocation::AddCountry($arCountry);
			if ($arRes[4]==GetMessage("WIZ_COUNTRY")) 
				$def_loc_s=$CurCountryID;
		}
	if ($arRes[0]=="T"){	
			$arCity = array(
			"NAME" => $arRes[4],
			"SHORT_NAME" => $arRes[4],
				"ru" => array(
				"LID" => "ru",
				"NAME" => $arRes[4],
				"SHORT_NAME" => $arRes[4]
				),
				"en" => array(
				"LID" => "en",
				"NAME" => $arRes[2],
				"SHORT_NAME" => $arRes[2]
				)
			);			
			$topr=1;
			$city_id = CSaleLocation::AddCity($arCity);
			if ($arRes[4]==GetMessage("WIZ_COUNTRY")) $def_loc_T=$CurCountryID;
	}
	
	if (($sopr==1) and ($topr==1))
	{
		$location = CSaleLocation::AddLocation(
				array(
					"COUNTRY_ID" => $CurCountryID,
					"CITY_ID" => $city_id
				));
	}
	
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	
	$location = 0;
		$dbLocation = CSaleLocation::GetList(Array("ID" => "ASC"), Array("LID" => "ru", "COUNTRY_NAME"=>GetMessage("WIZ_COUNTRY"), "CITY_NAME"=>$shopLocation));
		if($arLocation = $dbLocation->Fetch())//if there are no data in module
		{
			$location = $arLocation["ID"];
		}
		
		if(IntVal($location) <= 0)
		{
					
			$arCountry = array(
			"NAME" => GetMessage("WIZ_COUNTRY"),
			"SHORT_NAME" => GetMessage("WIZ_COUNTRY"),
				"ru" => array(
				"LID" => "ru",
				"NAME" => GetMessage("WIZ_COUNTRY"),
				"SHORT_NAME" => GetMessage("WIZ_COUNTRY")
				),
				"en" => array(
				"LID" => "en",
				"NAME" => GetMessage("WIZ_COUNTRY"),
				"SHORT_NAME" => GetMessage("WIZ_COUNTRY")
				)
			);
	
			$CurCountryID = CSaleLocation::AddCountry($arCountry);
					
			$arCity = array(
			"NAME" => $shopLocation,
			"SHORT_NAME" => $shopLocation,
				"ru" => array(
				"LID" => "ru",
				"NAME" => $shopLocation,
				"SHORT_NAME" => $shopLocation
				),
				"en" => array(
				"LID" => "en",
				"NAME" => $shopLocation,
				"SHORT_NAME" => $shopLocation
				)
			);			
			$city_id = CSaleLocation::AddCity($arCity);
			
			if ($def_loc_s>0) $CurCountryID=$def_loc_s;
			
			$location = CSaleLocation::AddLocation(
				array(
					"COUNTRY_ID" => $CurCountryID,
					"CITY_ID" => $city_id
				));
				
//			CSaleLocation::AddLocationZIP($location, "101000");
		}
COption::SetOptionString('sale', 'location', $location, false, WIZARD_SITE_ID);
?>