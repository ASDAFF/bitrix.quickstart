<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @package bitrix
 * @subpackage altasib.geobase
 * @copyright (c) 2006-2015 ALTASIB
 */
IncludeModuleLangFile(__FILE__);

Class CAltasibGeoBaseTools extends CAltasibGeoBase
{
	function CompareArr($a, $b){
		$regName = $GLOBALS['RegionName'];

		if ($a['REGION'] == $regName
		&& $b['REGION'] != $regName){
			return -1;
		}
		elseif ($a['REGION'] != $regName
		&& $b['REGION'] == $regName){
			return 1;
		}
		else{
			return 0;
		}
	}

	function GetCodeKladrByAddr(){
		if(IsModuleInstalled("altasib.kladr") && CModule::IncludeModule("altasib.kladr")) {

			$arDataGeo = CAltasibGeoBase::GetAddres();
			if($arDataGeo)
			{
				$reg = $arDataGeo["REGION_NAME"];
				$findme = GetMessage("ALTASIB_GEOBASE_RESPUBLIC");
				$pos = strpos($reg, $findme);
				if ($pos !== FALSE)
					$reg = substr($reg, $pos+10).' '.$findme;

				$rsRegions = CAltasibKladrRegions::GetList(array("FULL_NAME" => $reg),array());
				if($arRegion = $rsRegions->Fetch())
				{
					$arInfo["REGION"] = array(
						"CODE" => $arRegion["CODE"],
						"NAME" => $arRegion["NAME"],
						"FULL_NAME" => $arRegion["FULL_NAME"],
						"SOCR" => $arRegion["SOCR"]
					);
					$rsRegions = CAltasibKladrCities::GetList(array(
						"FULL_NAME" => trim(htmlspecialcharsEx($reg))
					),array());
					if($arDistrict = $rsRegions->Fetch())
					{
						$arInfo["DISTRICT"] = array(
							"CODE" => $arDistrict["CODE"],
							"NAME" => $arDistrict["NAME"],
							"SOCR" => $arDistrict["SOCR"]
						);
						$rsCity = CAltasibKladrCities::GetList(array(
							"NAME" => trim(htmlspecialcharsEx($arDataGeo["CITY_NAME"]))
						),array());
						if($arCity = $rsCity->Fetch())
						{
							$arInfo["CITY"] = array(
								"ID" => $arCity["ID"],
								"NAME" => $arCity["NAME"],
								"SOCR" => $arCity["SOCR"],
								"POSTINDEX" => $arCity["POSTINDEX"],
								"ID_DISTRICT" => $arCity["ID_DISTRICT"]
							);
							$arInfo["CODE"] = $arCity["CODE"];
						}
					}
				}
			}
			return $arInfo;
		}
		else
			return false;
	}

	function AddScriptYourCityOnSite()
	{
		global $APPLICATION;
		$m_id = 'altasib.geobase';

		if (COption::GetOptionString($m_id, 'your_city_enable', 'Y') != 'Y')
			return false;

		if (!CAltasibGeoBase::GetTemplate(COption::GetOptionString($m_id, "template")))
			return false;
		if (!CAltasibGeoBase::CheckSite(COption::GetOptionString($m_id, "sites")))
			return false;

		if (isset($_SESSION["ALTASIB_GEOBASE_CODE"]))
			return false;

		if (COption::GetOptionString($m_id, "set_cookie", "Y") == "Y") {
			$strData = $APPLICATION->get_cookie("ALTASIB_GEOBASE_CODE");
			if(!empty($strData))
				return false;
		}

		if (ADMIN_SECTION !== true && COption::GetOptionString("altasib.geobase", "enable_jquery", "ON") == "ON")
			CJSCore::Init(array('jquery'));

		$TemplYC = explode(",", COption::GetOptionString($m_id, "your_city_templates"));
		$TemplSC = explode(",", COption::GetOptionString($m_id, "select_city_templates"));

		$strCName = 'altasib:geobase.your.city';

		if(isset($TemplYC))
			CAltasibGeoBase::GetTemplateProps($strCName, $TemplYC[0], $TemplYC[1]);

		$strCNameSC = 'altasib:geobase.select.city';

		if(isset($TemplSC))
			CAltasibGeoBase::GetTemplateProps($strCNameSC, $TemplSC[0], $TemplSC[1]);
	}
}
?>