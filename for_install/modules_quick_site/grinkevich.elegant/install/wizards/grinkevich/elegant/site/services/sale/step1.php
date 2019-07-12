<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule('sale'))
	return;

if (COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS") == "")
	COption::SetOptionString("catalog", "1C_GROUP_PERMISSIONS", "1", GetMessage('SALE_1C_GROUP_PERMISSIONS'));

$arGeneralInfo = Array();

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite -> Fetch())
	$lang = $arSite["LANGUAGE_ID"];
if(strlen($lang) <= 0)
	$lang = "ru";
$bRus = false;
if($lang == "ru")
	$bRus = true;

$defCurrency = "EUR";
if($lang == "ru")
	$defCurrency = "RUB";
elseif($lang == "en")
	$defCurrency = "USD";
	
$arLanguages = Array();
$rsLanguage = CLanguage::GetList($by, $order, array());
while($arLanguage = $rsLanguage->Fetch())
	$arLanguages[] = $arLanguage["LID"];

WizardServices::IncludeServiceLang("step1.php", $lang);

/*
siteTelephone
"shopEmail" => "sale@".$_SERVER["SERVER_NAME"],
"shopLocation" => GetMessage("WIZ_SHOP_LOCATION_DEF"),
"shopAdr" => GetMessage("WIZ_SHOP_ADR_DEF"),
//"shopZip" => 101000,
personType
delivery
paysystem
*/
if(COption::GetOptionString("eshop", "wizard_installed", "N", WIZARD_SITE_ID) != "Y" || WIZARD_INSTALL_DEMO_DATA)
{
	$loc_file = $wizard->GetVar("locations_csv");
	if(strlen($loc_file) > 0)
	{
		define('LOC_STEP_LENGTH', 20);

		$time_limit = ini_get('max_execution_time');
		if ($time_limit < LOC_STEP_LENGTH) set_time_limit(LOC_STEP_LENGTH + 5);

		$start_time = time();
		$finish_time = $start_time + LOC_STEP_LENGTH;

		if($loc_file == "loc_ussr.csv")
			$file_url = $_SERVER['DOCUMENT_ROOT'].WIZARD_SERVICE_RELATIVE_PATH."/locations/ru/".$loc_file;
		else
			$file_url = $_SERVER['DOCUMENT_ROOT'].WIZARD_SERVICE_RELATIVE_PATH."/locations/".$loc_file;

		if (file_exists($file_url))
		{
			$bFinish = true;

			$arSysLangs = Array();
			$db_lang = CLangAdmin::GetList(($b="sort"), ($o="asc"), array("ACTIVE" => "Y"));
			while ($arLang = $db_lang->Fetch())
			{
				$arSysLangs[$arLang["LID"]] = $arLang["LID"];
			}

			$arLocations = array();
			$bSync = true;

			$dbLocations = CSaleLocation::GetList(array(), array(), false, false, array("ID", "COUNTRY_ID", "REGION_ID", "CITY_ID"));
			while ($arLoc = $dbLocations->Fetch())
			{
				$arLocations[$arLoc["ID"]] = $arLoc;
			}

			if (count($arLocations) <= 0)
				$bSync = false;

			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");

			$csvFile = new CCSVData();
			$csvFile->LoadFile($file_url);
			$csvFile->SetFieldsType("R");
			$csvFile->SetFirstHeader(false);
			$csvFile->SetDelimiter(",");

			$arRes = $csvFile->Fetch();
			if (is_array($arRes) && count($arRes) > 0 && strlen($arRes[0]) == 2)
			{
				$DefLang = $arRes[0];
				if (in_array($DefLang, $arSysLangs))
				{

					if (is_set($_SESSION["LOC_POS"]))
					{
						$csvFile->SetPos($_SESSION["LOC_POS"]);

						$CurCountryID = $_SESSION["CUR_COUNTRY_ID"];
						$CurRegionID = $_SESSION["CUR_REGION_ID"];
						$numCountries = $_SESSION["NUM_COUNTRIES"];
						$numRegiones = $_SESSION["NUM_REGIONES"];
						$numCities = $_SESSION["NUM_CITIES"];
						$numLocations = $_SESSION["NUM_LOCATIONS"];
					}
					else
					{
						$CurCountryID = 0;
						$CurRegionID = 0;
						$numCountries = 0;
						$numRegiones = 0;
						$numCities = 0;
						$numLocations = 0;
					}

					$tt = 0;
					while ($arRes = $csvFile->Fetch())
					{
						$type = strtoupper($arRes[0]);
						$tt++;
						$arArrayTmp = array();
						foreach($arRes as $ind => $value)
						{
							if ($ind%2 && isset($arSysLangs[$value]))
							{
								$arArrayTmp[$value] = array(
										"LID" => $value,
										"NAME" => $arRes[$ind + 1]
									);

								if ($value == $DefLang)
								{
									$arArrayTmp["NAME"] = $arRes[$ind + 1];
								}
							}
						}

						//country
						if (is_array($arArrayTmp) && strlen($arArrayTmp["NAME"])>0)
						{
							if ($type == "S")
							{
								$CurCountryID = null;
								$arContList = array();
								$LLL = 0;
								if ($bSync)
								{
									$db_contList = CSaleLocation::GetList(
										Array(),
										Array(
											"COUNTRY_NAME" => $arArrayTmp["NAME"],
											"LID" => $DefLang
										)
									);
									if ($arContList = $db_contList->Fetch())
									{
										$LLL = IntVal($arContList["ID"]);
										$CurCountryID = IntVal($arContList["COUNTRY_ID"]);
									}
								}

								if (IntVal($CurCountryID) <= 0)
								{
									$CurCountryID = CSaleLocation::AddCountry($arArrayTmp);
									$CurCountryID = IntVal($CurCountryID);
									if ($CurCountryID>0)
									{
										$numCountries++;
										if(IntVal($LLL) <= 0)
										{
											$LLL = CSaleLocation::AddLocation(array("COUNTRY_ID" => $CurCountryID));
											if (IntVal($LLL)>0) $numLocations++;
										}
									}
								}
							}
							elseif ($type == "R") //region
							{
								$CurRegionID = null;
								$arRegionList = Array();
								$LLL = 0;
								if ($bSync)
								{
									$db_rengList = CSaleLocation::GetList(
										Array(),
										Array(
											"COUNTRY_ID" => $CurCountryID,
											"REGION_NAME"=>$arArrayTmp["NAME"],
											"LID" => $DefLang
										)
									);
									if ($arRegionList = $db_rengList->Fetch())
									{
										$LLL = $arRegionList["ID"];
										$CurRegionID = IntVal($arRegionList["REGION_ID"]);
									}
								}

								if (IntVal($CurRegionID) <= 0)
								{
									$CurRegionID = CSaleLocation::AddRegion($arArrayTmp);
									$CurRegionID = IntVal($CurRegionID);
									if ($CurRegionID > 0)
									{
										$numRegiones++;
										if (IntVal($LLL) <= 0)
										{
											$LLL = CSaleLocation::AddLocation(array("COUNTRY_ID" => $CurCountryID, "REGION_ID" => $CurRegionID));
											if (IntVal($LLL)>0) $numLocations++;
										}
									}
								}
							}
							elseif ($type == "T" && IntVal($CurCountryID)>0) //city
							{
								$city_id = 0;
								$LLL = 0;
								$arCityList = Array();

								if ($bSync)
								{
									$arFilter = Array(
											"COUNTRY_ID" => $CurCountryID,
											"CITY_NAME" => $arArrayTmp["NAME"],
											"LID" => $DefLang
										);
									if(IntVal($CurRegionID) > 0)
										$arFilter["REGION_ID"] = $CurRegionID;
									
									$db_cityList = CSaleLocation::GetList(
										Array(),
										$arFilter
									);
									if ($arCityList = $db_cityList->Fetch())
									{
										$LLL = $arCityList["ID"];
										$city_id = IntVal($arCityList["CITY_ID"]);
									}
								}

								if ($city_id <= 0)
								{
									$city_id = CSaleLocation::AddCity($arArrayTmp);
									$city_id = IntVal($city_id);
									if ($city_id > 0)
										$numCities++;
								}

								if ($city_id > 0)
								{
									if (IntVal($LLL) <= 0)
									{
										$LLL = CSaleLocation::AddLocation(
											array(
												"COUNTRY_ID" => $CurCountryID,
												"REGION_ID" => $CurRegionID,
												"CITY_ID" => $city_id
											));

										if (intval($LLL) > 0) $numLocations++;
									}
								}
							}
						}

						if($tt == 10)
						{
							$tt = 0;
							$cur_time = time();

							if ($cur_time >= $finish_time)
							{
								$cur_step = $csvFile->GetPos();
								$amount = $csvFile->iFileLength;

								$_SESSION["LOC_POS"] = $cur_step;
								$_SESSION["CUR_COUNTRY_ID"] = $CurCountryID;
								$_SESSION["CUR_REGION_ID"] = $CurRegionID;
								$_SESSION["NUM_COUNTRIES"] = $numCountries;
								$_SESSION["NUM_REGIONES"] = $numRegiones;
								$_SESSION["NUM_CITIES"] = $numCities;
								$_SESSION["NUM_LOCATIONS"] = $numLocations;
								
								$this->repeatCurrentService = true;

								$bFinish = false;
							}
						}
					}
				}
			}

			if ($bFinish)
				unset($_SESSION["LOC_POS"]);
			else
				return true;

			$time_limit = ini_get('max_execution_time');
			if ($time_limit < LOC_STEP_LENGTH) set_time_limit(LOC_STEP_LENGTH + 5);

			$start_time = time();
			$finish_time = $start_time + LOC_STEP_LENGTH;

			if ($loc_file == "loc_ussr.csv" && file_exists($_SERVER['DOCUMENT_ROOT'].WIZARD_SERVICE_RELATIVE_PATH."/locations/ru/zip_ussr.csv"))
			{
				$rsLocations = CSaleLocation::GetList(array(), array("LID" => 'ru'), false, false, array("ID", "CITY_NAME_LANG", "REGION_NAME_LANG"));
				$arLocationMap = array();
				while ($arLocation = $rsLocations->Fetch())
				{
					if(strlen($arLocation["REGION_NAME_LANG"]) > 0)
						$arLocationMap[$arLocation["CITY_NAME_LANG"]][$arLocation["REGION_NAME_LANG"]] = $arLocation["ID"];
					else
						$arLocationMap[$arLocation["CITY_NAME_LANG"]] = $arLocation["ID"];
				}

				$DB->StartTransaction();

				include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");

				$csvFile = new CCSVData();
				$csvFile->LoadFile($_SERVER['DOCUMENT_ROOT'].WIZARD_SERVICE_RELATIVE_PATH."/locations/ru/zip_ussr.csv");
				$csvFile->SetFieldsType("R");
				$csvFile->SetFirstHeader(false);
				$csvFile->SetDelimiter(";");

				if (is_set($_SESSION, 'ZIP_POS'))
				{
					$numZIP = $_SESSION["NUM_ZIP"];
					$csvFile->SetPos($_SESSION["ZIP_POS"]);
				}
				else
				{
					CSaleLocation::ClearAllLocationZIP();

					unset($_SESSION["NUM_ZIP"]);
					$numZIP = 0;
				}

				$bFinish = true;
				$arLocationsZIP = array();
				$tt = 0;
				$REGION = "";
				while ($arRes = $csvFile->Fetch())
				{
					$tt++;
					$CITY = $arRes[1];
					if(strlen($arRes[3]) > 0)
						$REGION = $arRes[3];

					if (array_key_exists($CITY, $arLocationMap))
					{
						if(strlen($REGION) > 0)
							$ID = $arLocationMap[$CITY][$REGION];
						else
							$ID = $arLocationMap[$CITY];
					}
					else
					{
						$ID = 0;
					}

					if ($ID)
					{
						CSaleLocation::AddLocationZIP($ID, $arRes[2]);

						$numZIP++;
					}

					if($tt == 10)
					{
						$tt = 0;

						$cur_time = time();
						if ($cur_time >= $finish_time)
						{
							$cur_step = $csvFile->GetPos();
							$amount = $csvFile->iFileLength;

							$_SESSION["ZIP_POS"] = $cur_step;
							$_SESSION["NUM_ZIP"] = $numZIP;

							$bFinish = false;

							$this->repeatCurrentService = true;

						}
					}
				}

				$DB->Commit();

				if ($bFinish)
					unset($_SESSION["ZIP_POS"]);
				else
					return true;
			}
		}
	}

	$shopLocalization = $wizard->GetVar("shopLocalization");
	COption::SetOptionString("eshop", "shopLocalization", $shopLocalization, "ru", WIZARD_SITE_ID);
	$shopLocation = $wizard->GetVar("shopLocation");
	COption::SetOptionString("eshop", "shopLocation", $shopLocation, false, WIZARD_SITE_ID);
	$shopOfName = $wizard->GetVar("shopOfName");
	COption::SetOptionString("eshop", "shopOfName", $shopOfName, false, WIZARD_SITE_ID);
	$personType = $wizard->GetVar("personType");
	$paysystem = $wizard->GetVar("paysystem");

//	if ($shopLocalization == "ru")
	//{
		$shopINN = $wizard->GetVar("shopINN");
		COption::SetOptionString("eshop", "shopINN", $shopINN, false, WIZARD_SITE_ID);
		$shopKPP = $wizard->GetVar("shopKPP");
		COption::SetOptionString("eshop", "shopKPP", $shopKPP, false, WIZARD_SITE_ID);
		$shopNS = $wizard->GetVar("shopNS");
		COption::SetOptionString("eshop", "shopNS", $shopNS, false, WIZARD_SITE_ID);
		$shopBANK = $wizard->GetVar("shopBANK");
		COption::SetOptionString("eshop", "shopBANK", $shopBANK, false, WIZARD_SITE_ID);
		$shopBANKREKV = $wizard->GetVar("shopBANKREKV");
		COption::SetOptionString("eshop", "shopBANKREKV", $shopBANKREKV, false, WIZARD_SITE_ID);
		$shopKS = $wizard->GetVar("shopKS");
		COption::SetOptionString("eshop", "shopKS", $shopKS, false, WIZARD_SITE_ID);
		$siteStamp = $wizard->GetVar("siteStamp");
		COption::SetOptionString("eshop", "siteStamp", $siteStamp, false, WIZARD_SITE_ID);
	/*}
	elseif ($shopLocalization == "ua")
	{
		$shopCompany_ua = $wizard->GetVar("shopCompany_ua");
		COption::SetOptionString("eshop", "shopCompany_ua", $shopCompany_ua, false, WIZARD_SITE_ID);
		$shopEGRPU_ua = $wizard->GetVar("shopEGRPU_ua");
		COption::SetOptionString("eshop", "shopCompany_ua", $shopEGRPU_ua, false, WIZARD_SITE_ID);
		$shopINN_ua = $wizard->GetVar("shopINN_ua");
		COption::SetOptionString("eshop", "shopINN_ua", $shopINN_ua, false, WIZARD_SITE_ID);
		$shopNDS_ua = $wizard->GetVar("shopNDS_ua");
		COption::SetOptionString("eshop", "shopNDS_ua", $shopNDS_ua, false, WIZARD_SITE_ID);
		$shopNS_ua = $wizard->GetVar("shopNS_ua");
		COption::SetOptionString("eshop", "shopNS_ua", $shopNS_ua, false, WIZARD_SITE_ID);
		$shopBank_ua = $wizard->GetVar("shopBank_ua");
		COption::SetOptionString("eshop", "shopBank_ua", $shopBank_ua, false, WIZARD_SITE_ID);
		$shopMFO_ua = $wizard->GetVar("shopMFO_ua");
		COption::SetOptionString("eshop", "shopMFO_ua", $shopMFO_ua, false, WIZARD_SITE_ID);
		$shopPlace_ua = $wizard->GetVar("shopPlace_ua");
		COption::SetOptionString("eshop", "shopPlace_ua", $shopPlace_ua, false, WIZARD_SITE_ID);
		$shopFIO_ua = $wizard->GetVar("shopFIO_ua");
		COption::SetOptionString("eshop", "shopFIO_ua", $shopFIO_ua, false, WIZARD_SITE_ID);
		$shopTax_ua = $wizard->GetVar("shopTax_ua");
		COption::SetOptionString("eshop", "shopTax_ua", $shopTax_ua, false, WIZARD_SITE_ID);
	}    */

	$shopAdr = $wizard->GetVar("shopAdr");
	COption::SetOptionString("eshop", "shopAdr", $shopAdr, false, WIZARD_SITE_ID);
	$siteTelephone = $wizard->GetVar("siteTelephone");
	COption::SetOptionString("eshop", "siteTelephone", $siteTelephone, false, WIZARD_SITE_ID);
	$shopEmail = $wizard->GetVar("shopEmail");
	COption::SetOptionString("eshop", "shopEmail", $shopEmail, false, WIZARD_SITE_ID);
	$siteName = $wizard->GetVar("siteName");
	COption::SetOptionString("eshop", "siteName", $siteName, false, WIZARD_SITE_ID);
//SocNets
	$shopFacebook = $wizard->GetVar("shopFacebook");
	COption::SetOptionString("eshop", "shopFacebook", $shopFacebook, false, WIZARD_SITE_ID);
	$shopTwitter = $wizard->GetVar("shopTwitter");
	COption::SetOptionString("eshop", "shopTwitter", $shopTwitter, false, WIZARD_SITE_ID);
	$shopGooglePlus= $wizard->GetVar("shopGooglePlus");
	COption::SetOptionString("eshop", "shopGooglePlus", $shopGooglePlus, false, WIZARD_SITE_ID);
	if (LANGUAGE_ID == "ru")
	{
		$shopVk = $wizard->GetVar("shopVk");	
		COption::SetOptionString("eshop", "shopVk", $shopVk, false, WIZARD_SITE_ID);
	}
		
	$obSite = new CSite;
	$obSite->Update(WIZARD_SITE_ID, Array(
			"EMAIL" => $shopEmail,
			"SITE_NAME" => $siteName,
			"SERVER_NAME" => $_SERVER["SERVER_NAME"],
		));
	
	if(strlen($siteStamp)>0)
	{
		if(IntVal($siteStamp) > 0)
		{
			$ff = CFile::GetByID($siteStamp);
			if($zr = $ff->Fetch())
			{
				$strOldFile = str_replace("//", "/", WIZARD_SITE_ROOT_PATH."/".(COption::GetOptionString("main", "upload_dir", "upload"))."/".$zr["SUBDIR"]."/".$zr["FILE_NAME"]);
				@copy($strOldFile, WIZARD_SITE_PATH."include/stamp.gif");
				CFile::Delete($zr["ID"]);
				$siteStamp = WIZARD_SITE_DIR."include/stamp.gif";
				COption::SetOptionString("eshop", "siteStamp", $siteStamp, false, WIZARD_SITE_ID);
			}
		}
	}
	else
	{
		$siteStamp = "/bitrix/templates/".WIZARD_TEMPLATE_ID."_".WIZARD_THEME_ID."/images/pechat.gif";
	}
	
	$dbPerson = CSalePersonType::GetList(array(), Array("LID" => WIZARD_SITE_ID));
	if(!$dbPerson->Fetch())//if there are no data in module
	{
		//Person Types
		if(!$bRus)
		{
			$personType["fiz"] = "Y";
			$personType["ur"] = "N";
		}
		if($personType["fiz"] == "Y")
		{
			$arGeneralInfo["personType"]["fiz"] = CSalePersonType::Add(Array(
						"LID" => WIZARD_SITE_ID,
						"NAME" => GetMessage("SALE_WIZARD_PERSON_1"),
						"SORT" => "100"
						)
					);
		}
		
		if($personType["ur"] == "Y")
		{
			$arGeneralInfo["personType"]["ur"] = CSalePersonType::Add(Array(
						"LID" => WIZARD_SITE_ID,
						"NAME" => GetMessage("SALE_WIZARD_PERSON_2"),
						"SORT" => "150"
						)
					);
		}

		if($shopLocalization == "ua" && $personType["fiz_ua"] == "Y")
		{
			$arGeneralInfo["personType"]["fiz_ua"] = CSalePersonType::Add(Array(
					"LID" => WIZARD_SITE_ID,
					"NAME" => GetMessage("SALE_WIZARD_PERSON_3"),
					"SORT" => "100"
				)
			);
		}
		//Set options 
		COption::SetOptionString('sale','default_currency',$defCurrency);
		COption::SetOptionString('sale','delete_after','30');
		COption::SetOptionString('sale','order_list_date','30');
		COption::SetOptionString('sale','MAX_LOCK_TIME','30');
		COption::SetOptionString('sale','GRAPH_WEIGHT','600');
		COption::SetOptionString('sale','GRAPH_HEIGHT','600');
		COption::SetOptionString('sale','path2user_ps_files','/bitrix/php_interface/include/sale_payment/');
		COption::SetOptionString('sale','lock_catalog','Y');
		COption::SetOptionString('sale','order_list_fields','ID,USER,PAY_SYSTEM,PRICE,STATUS,PAYED,PS_STATUS,CANCELED,BASKET');
		COption::SetOptionString('sale','GROUP_DEFAULT_RIGHT','D');
		COption::SetOptionString('sale','affiliate_param_name','partner');
		COption::SetOptionString('sale','show_order_sum','N');
		COption::SetOptionString('sale','show_order_product_xml_id','N');
		COption::SetOptionString('sale','show_paysystem_action_id','N');
		COption::SetOptionString('sale','affiliate_plan_type','N');
		if($bRus)
		{
			COption::SetOptionString('sale','1C_SALE_SITE_LIST',WIZARD_SITE_ID);
			COption::SetOptionString('sale','1C_EXPORT_PAYED_ORDERS','N');
			COption::SetOptionString('sale','1C_EXPORT_ALLOW_DELIVERY_ORDERS','N');
			COption::SetOptionString('sale','1C_EXPORT_FINAL_ORDERS','');
			COption::SetOptionString('sale','1C_FINAL_STATUS_ON_DELIVERY','F');
			COption::SetOptionString('sale','1C_REPLACE_CURRENCY',GetMessage("SALE_WIZARD_PS_BILL_RUB"));
			COption::SetOptionString('sale','1C_SALE_USE_ZIP','Y');
		}
		COption::SetOptionString('sale','weight_unit', GetMessage("SALE_WIZARD_WEIGHT_UNIT"), false, WIZARD_SITE_ID);
		COption::SetOptionString('sale','WEIGHT_different_set','N', false, WIZARD_SITE_ID);
		COption::SetOptionString('sale','ADDRESS_different_set','N');
		COption::SetOptionString('sale','measurement_path','/bitrix/modules/sale/measurements.php');
		COption::SetOptionString('sale','delivery_handles_custom_path','/bitrix/php_interface/include/sale_delivery/');
		if($bRus)
			COption::SetOptionString('sale','location_zip','101000');
		COption::SetOptionString('sale','weight_koef','1000', false, WIZARD_SITE_ID);
		
		COption::SetOptionString('sale','recalc_product_list','Y');
		COption::SetOptionString('sale','recalc_product_list_period','4');
		COption::SetOptionString('sale', 'order_email', $shopEmail);
		
		if(!$bRus)
			$shopLocation = GetMessage("WIZ_CITY");
		$location = 0;
		$dbLocation = CSaleLocation::GetList(Array("ID" => "ASC"), Array("LID" => $lang, "CITY_NAME" => $shopLocation));
		if($arLocation = $dbLocation->Fetch())//if there are no data in module
		{
			$location = $arLocation["ID"];
		}
		if(IntVal($location) <= 0)
		{
			$CurCountryID = 0;
			$db_contList = CSaleLocation::GetList(
				Array(),
				Array(
					"COUNTRY_NAME" => GetMessage("WIZ_COUNTRY"),
					"LID" => $langID
				)
			);
			if ($arContList = $db_contList->Fetch())
			{
				$LLL = IntVal($arContList["ID"]);
				$CurCountryID = IntVal($arContList["COUNTRY_ID"]);
			}
			if(IntVal($CurCountryID) <= 0)
			{
				$arArrayTmp = Array();
				$arArrayTmp["NAME"] = GetMessage("WIZ_COUNTRY");
				foreach($arLanguages as $langID)
				{
					WizardServices::IncludeServiceLang("step1.php", $langID);
					$arArrayTmp[$langID] = array(
							"LID" => $langID,
							"NAME" => GetMessage("WIZ_COUNTRY")
						);
				}
				$CurCountryID = CSaleLocation::AddCountry($arArrayTmp);
			}

			$arArrayTmp = Array();
			$arArrayTmp["NAME"] = $shopLocation;
			foreach($arLanguages as $langID)
			{
				$arArrayTmp[$langID] = array(
						"LID" => $langID,
						"NAME" => $shopLocation
					);
			}
			$city_id = CSaleLocation::AddCity($arArrayTmp);
			
			$location = CSaleLocation::AddLocation(
				array(
					"COUNTRY_ID" => $CurCountryID,
					"CITY_ID" => $city_id
				));
			if($bRus)
				CSaleLocation::AddLocationZIP($location, "101000");
			
			WizardServices::IncludeServiceLang("step1.php", $lang);
		}
		COption::SetOptionString('sale', 'location', $location);
		//Order Prop Group
		if($personType["fiz"] == "Y")
		{
			$arGeneralInfo["propGroup"]["user_fiz"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"), "SORT" => 100));
			$arGeneralInfo["propGroup"]["adres_fiz"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"), "SORT" => 200));
		}
		if($personType["ur"] == "Y")
		{
			$arGeneralInfo["propGroup"]["user_ur"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR1"), "SORT" => 300));
			$arGeneralInfo["propGroup"]["adres_ur"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR2"), "SORT" => 400));
		}

		if($shopLocalization == "ua" && $personType["fiz_ua"] == "Y")
		{
			$arGeneralInfo["propGroup"]["user_fiz_ua"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"), "SORT" => 100));
			$arGeneralInfo["propGroup"]["adres_fiz_ua"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"), "SORT" => 200));
		}
		$arProps = Array();
		if($personType["fiz"] == "Y")
		{
			$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_6"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 100,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "Y",
					"IS_PAYER" => "Y",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "FIO",
					"IS_FILTERED" => "Y",
				);
			$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
					"NAME" => "E-Mail",
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 110,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "Y",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "EMAIL",
					"IS_FILTERED" => "Y",
				);
			$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_9"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 120,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz"],
					"SIZE1" => 0,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "PHONE",
					"IS_FILTERED" => "N",
				);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_4"),
				"TYPE" => "TEXT",
				"REQUIED" => "N",
				"DEFAULT_VALUE" => "101000",
				"SORT" => 130,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
				"SIZE1" => 8,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "ZIP",
				"IS_FILTERED" => "N",
				"IS_ZIP" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_21"),
				"TYPE" => "TEXT",
				"REQUIED" => "N",
				"DEFAULT_VALUE" => $shopLocation,
				"SORT" => 145,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "CITY",
				"IS_FILTERED" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_2"),
				"TYPE" => "LOCATION",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => $location,
				"SORT" => 140,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "Y",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "LOCATION",
				"IS_FILTERED" => "N",
				"INPUT_FIELD_LOCATION" => ""
			);			
			$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_5"),
					"TYPE" => "TEXTAREA",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 150,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
					"SIZE1" => 30,
					"SIZE2" => 3,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "ADDRESS",
					"IS_FILTERED" => "N",
				);
		}

		if($personType["ur"] == "Y")
		{
			//if ($shopLocalization != "ua")
			//{
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_8"),
						"TYPE" => "TEXT",
						"REQUIED" => "Y",
						"DEFAULT_VALUE" => "",
						"SORT" => 200,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
						"SIZE1" => 40,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "Y",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "COMPANY",
						"IS_FILTERED" => "Y",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_7"),
						"TYPE" => "TEXTAREA",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" => 210,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
						"SIZE1" => 40,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "COMPANY_ADR",
						"IS_FILTERED" => "N",
					);
				$arProps[] =
					Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_13"),
						"TYPE" => "TEXT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" => 220,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
						"SIZE1" => 0,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "INN",
						"IS_FILTERED" => "N",
					);
				$arProps[] =
					Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_14"),
						"TYPE" => "TEXT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" => 230,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
						"SIZE1" => 0,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "KPP",
						"IS_FILTERED" => "N",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_10"),
						"TYPE" => "TEXT",
						"REQUIED" => "Y",
						"DEFAULT_VALUE" => "",
						"SORT" => 240,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 0,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "Y",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "CONTACT_PERSON",
						"IS_FILTERED" => "N",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => "E-Mail",
						"TYPE" => "TEXT",
						"REQUIED" => "Y",
						"DEFAULT_VALUE" => "",
						"SORT" => 250,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 40,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "Y",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "EMAIL",
						"IS_FILTERED" => "N",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_9"),
						"TYPE" => "TEXT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" =>260,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 0,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "PHONE",
						"IS_FILTERED" => "N",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_11"),
						"TYPE" => "TEXT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" => 270,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 0,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "FAX",
						"IS_FILTERED" => "N",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_4"),
						"TYPE" => "TEXT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "101000",
						"SORT" => 280,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 8,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "ZIP",
						"IS_FILTERED" => "N",
						"IS_ZIP" => "Y",
					);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_21"),
					"TYPE" => "TEXT",
					"REQUIED" => "N",
					"DEFAULT_VALUE" => $shopLocation,
					"SORT" => 285,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "CITY",
					"IS_FILTERED" => "Y",
				);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_2"),
						"TYPE" => "LOCATION",
						"REQUIED" => "Y",
						"DEFAULT_VALUE" => "",
						"SORT" => 290,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "Y",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 40,
						"SIZE2" => 0,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "Y",
						"CODE" => "LOCATION",
						"IS_FILTERED" => "N",
					);
				$arProps[] = Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PROP_12"),
						"TYPE" => "TEXTAREA",
						"REQUIED" => "Y",
						"DEFAULT_VALUE" => "",
						"SORT" => 300,
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
						"SIZE1" => 30,
						"SIZE2" => 40,
						"DESCRIPTION" => "",
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_LOCATION4TAX" => "N",
						"CODE" => "ADDRESS",
						"IS_FILTERED" => "N",
					);
			/*}
			else
			{
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_41"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 100,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "Y",
					"IS_PAYER" => "Y",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "FIO",
					"IS_FILTERED" => "Y",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => "E-Mail",
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 110,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "Y",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "EMAIL",
					"IS_FILTERED" => "Y",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_40"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 130,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_ur"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "Y",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "COMPANY",
					"IS_FILTERED" => "Y",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_47"),
					"TYPE" => "TEXTAREA",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 140,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 40,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "COMPANY_ADR",
					"IS_FILTERED" => "N",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_48"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 150,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 30,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "EGRPU",
					"IS_FILTERED" => "N",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_49"),
					"TYPE" => "TEXT",
					"REQUIED" => "N",
					"DEFAULT_VALUE" => "",
					"SORT" => 160,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 30,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "INN",
					"IS_FILTERED" => "N",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_46"),
					"TYPE" => "TEXT",
					"REQUIED" => "N",
					"DEFAULT_VALUE" => "",
					"SORT" => 170,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 30,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "NDS",
					"IS_FILTERED" => "N",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_44"),
					"TYPE" => "TEXT",
					"REQUIED" => "N",
					"DEFAULT_VALUE" => "",
					"SORT" => 180,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 8,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "ZIP",
					"IS_FILTERED" => "N",
					"IS_ZIP" => "Y",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_43"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => $shopLocation,
					"SORT" => 190,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 30,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "CITY",
					"IS_FILTERED" => "Y",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_42"),
					"TYPE" => "TEXTAREA",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 200,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 30,
					"SIZE2" => 3,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "ADDRESS",
					"IS_FILTERED" => "N",
				);
				$arProps[] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PROP_45"),
					"TYPE" => "TEXT",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 210,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "N",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 30,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "PHONE",
					"IS_FILTERED" => "N",
				);
			}  */
		}

	/*	if ($shopLocalization == "ua" && $personType["fiz_ua"] == "Y")
		{
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_31"),
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 100,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "Y",
				"IS_PAYER" => "Y",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "FIO",
				"IS_FILTERED" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => "E-Mail",
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 110,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "Y",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "EMAIL",
				"IS_FILTERED" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_30"),
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 130,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "Y",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "COMPANY",
				"IS_FILTERED" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_37"),
				"TYPE" => "TEXTAREA",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 140,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["user_fiz_ua"],
				"SIZE1" => 40,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "COMPANY_ADR",
				"IS_FILTERED" => "N",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_38"),
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 150,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 30,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "EGRPU",
				"IS_FILTERED" => "N",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_39"),
				"TYPE" => "TEXT",
				"REQUIED" => "N",
				"DEFAULT_VALUE" => "",
				"SORT" => 160,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 30,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "INN",
				"IS_FILTERED" => "N",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_36"),
				"TYPE" => "TEXT",
				"REQUIED" => "N",
				"DEFAULT_VALUE" => "",
				"SORT" => 170,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 30,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "NDS",
				"IS_FILTERED" => "N",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_34"),
				"TYPE" => "TEXT",
				"REQUIED" => "N",
				"DEFAULT_VALUE" => "",
				"SORT" => 180,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 8,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "ZIP",
				"IS_FILTERED" => "N",
				"IS_ZIP" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_33"),
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => $shopLocation,
				"SORT" => 190,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 30,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "CITY",
				"IS_FILTERED" => "Y",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_32"),
				"TYPE" => "TEXTAREA",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 200,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 30,
				"SIZE2" => 3,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "ADDRESS",
				"IS_FILTERED" => "N",
			);
			$arProps[] = Array(
				"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
				"NAME" => GetMessage("SALE_WIZARD_PROP_35"),
				"TYPE" => "TEXT",
				"REQUIED" => "Y",
				"DEFAULT_VALUE" => "",
				"SORT" => 210,
				"USER_PROPS" => "Y",
				"IS_LOCATION" => "N",
				"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz_ua"],
				"SIZE1" => 30,
				"SIZE2" => 0,
				"DESCRIPTION" => "",
				"IS_EMAIL" => "N",
				"IS_PROFILE_NAME" => "N",
				"IS_PAYER" => "N",
				"IS_LOCATION4TAX" => "N",
				"CODE" => "PHONE",
				"IS_FILTERED" => "N",
			);
		}  */
		$propCityId = 0;
		foreach($arProps as $prop)
		{
			$variants = Array();
			if(!empty($prop["VARIANTS"]))
			{
				$variants = $prop["VARIANTS"];
				unset($prop["VARIANTS"]);
			}
			
			if ($prop["CODE"] == "LOCATION" && $propCityId > 0)
			{
				$prop["INPUT_FIELD_LOCATION"] = $propCityId;
				$propCityId = 0;
			}
			$id = CSaleOrderProps::Add($prop);
			if ($prop["CODE"] == "CITY")
			{
				$propCityId = $id;
			}
			
			if(strlen($prop["CODE"]) > 0)
			{
				//$arGeneralInfo["propCode"][$prop["CODE"]] = $prop["CODE"];
				$arGeneralInfo["propCodeID"][$prop["CODE"]] = $id;
				$arGeneralInfo["properies"][$prop["PERSON_TYPE_ID"]][$prop["CODE"]] = $prop;
				$arGeneralInfo["properies"][$prop["PERSON_TYPE_ID"]][$prop["CODE"]]["ID"] = $id;
			}
			
			if(!empty($variants))
			{	
				foreach($variants as $val)
				{
					$val["ORDER_PROPS_ID"] = $id;
					CSaleOrderPropsVariant::Add($val);
				}
			}
		}
		$propReplace = "";
		foreach($arGeneralInfo["properies"] as $key => $val)
		{
			if(IntVal($val["LOCATION"]["ID"]) > 0)
				$propReplace .= '"PROP_'.$key.'" => Array(0 => "'.$val["LOCATION"]["ID"].'"), ';
		}
		WizardServices::ReplaceMacrosRecursive(WIZARD_SITE_PATH."personal/order/", Array("PROPS" => $propReplace));
		
		//1C export
		if($personType["fiz"] == "Y")
		{
			$val = serialize(Array(
					"AGENT_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["FIO"]["ID"]),
					"FULL_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["FIO"]["ID"]),
					"SURNAME" => Array("TYPE" => "USER", "VALUE" => "LAST_NAME"),
					"NAME" => Array("TYPE" => "USER", "VALUE" => "NAME"),
					"ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ADDRESS"]["ID"]),
					"INDEX" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ZIP"]["ID"]),
					"COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["LOCATION"]["ID"]."_COUNTRY"),
					"CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["LOCATION"]["ID"]."_CITY"),
					"STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["ADDRESS"]["ID"]),
					"EMAIL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["EMAIL"]["ID"]),
					"CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz"]]["CONTACT_PERSON"]["ID"]),
					"IS_FIZ" => "Y",
				));
			CSaleExport::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "VARS" => $val));
		}
		if($personType["ur"] == "Y")
		{
			$val = serialize(Array(
					"AGENT_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY"]["ID"]),
					"FULL_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY"]["ID"]),
					"ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY_ADR"]["ID"]),
					"COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_COUNTRY"),
					"CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_CITY"),
					"STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["COMPANY_ADR"]["ID"]),
					"INN" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["INN"]["ID"]),
					"KPP" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["KPP"]["ID"]),
					"PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["PHONE"]["ID"]),
					"EMAIL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["EMAIL"]["ID"]),
					"CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["NAME"]["ID"]),
					"F_ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ADDRESS"]["ID"]),
					"F_COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_COUNTRY"),
					"F_CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["LOCATION"]["ID"]."_CITY"),
					"F_INDEX" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ZIP"]["ID"]),
					"F_STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["ur"]]["ADDRESS"]["ID"]),
					"IS_FIZ" =>  "N",
				));
			CSaleExport::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"], "VARS" => $val));
		}
		if ($shopLocalization == "ua" && $personType["fiz_ua"] == "Y")
		{
			$val = serialize(Array(
				"AGENT_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["FIO"]["ID"]),
				"FULL_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["FIO"]["ID"]),
				"SURNAME" => Array("TYPE" => "USER", "VALUE" => "LAST_NAME"),
				"NAME" => Array("TYPE" => "USER", "VALUE" => "NAME"),
				"ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["ADDRESS"]["ID"]),
				"INDEX" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["ZIP"]["ID"]),
				"COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["LOCATION"]["ID"]."_COUNTRY"),
				"CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["LOCATION"]["ID"]."_CITY"),
				"STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["ADDRESS"]["ID"]),
				"EMAIL" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["EMAIL"]["ID"]),
				"CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => $arGeneralInfo["properies"][$arGeneralInfo["personType"]["fiz_ua"]]["CONTACT_PERSON"]["ID"]),
				"IS_FIZ" => "Y",
			));
			CSaleExport::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "VARS" => $val));
		}
		//PaySystem
		$arPaySystems = Array();
		if($paysystem["cash"] == "Y")
		{
			$arPaySystemTmp = Array(
				"NAME" => GetMessage("SALE_WIZARD_PS_CASH"),
				"SORT" => 50,
				"ACTIVE" => "Y",
				"DESCRIPTION" => GetMessage("SALE_WIZARD_PS_CASH_DESCR"),
				"CODE_TEMP" => "cash");
			if($personType["fiz"] == "Y")
			{
				$arPaySystemTmp["ACTION"][] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
					"NAME" => GetMessage("SALE_WIZARD_PS_CASH"),
					"ACTION_FILE" => "/bitrix/modules/sale/payment/cash",
					"RESULT_FILE" => "",
					"NEW_WINDOW" => "N",
					"PARAMS" => "",
					"HAVE_PAYMENT" => "Y",
					"HAVE_ACTION" => "N",
					"HAVE_RESULT" => "N",
					"HAVE_PREPAY" => "N",
					"HAVE_RESULT_RECEIVE" => "N",
				);
			}
			/*if($personType["ur"] == "Y")
			{
				$arPaySystemTmp["ACTION"][] = Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PS_CASH"),
					"ACTION_FILE" => "/bitrix/modules/sale/payment/cash",
					"RESULT_FILE" => "",
					"NEW_WINDOW" => "N",
					"PARAMS" => "",
					"HAVE_PAYMENT" => "Y",
					"HAVE_ACTION" => "N",
					"HAVE_RESULT" => "N",
					"HAVE_PREPAY" => "N",
					"HAVE_RESULT_RECEIVE" => "N",
				);
			}   */
			$arPaySystems[] = $arPaySystemTmp;
		}
		if($personType["fiz"] == "Y")
		{
			if($bRus)
			{
				$arPaySystems[] = Array(
					"NAME" => GetMessage("SALE_WIZARD_PS_CC"),
					"SORT" => 60,
					"ACTIVE" => "N",
					"DESCRIPTION" => GetMessage("SALE_WIZARD_PS_CC"),
					"CODE_TEMP" => "card",
					"ACTION" => Array(Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
						"NAME" => GetMessage("SALE_WIZARD_PS_CC"),
						"ACTION_FILE" => "/bitrix/modules/sale/payment/assist",
						"RESULT_FILE" => "/bitrix/modules/sale/payment/assist_res.php",
						"NEW_WINDOW" => "N",
						"PARAMS" => serialize(Array(
							"FIRST_NAME" => Array("TYPE" => "USER", "VALUE" => "NAME"),
							"LAST_NAME" => Array("TYPE" => "USER", "VALUE" => "LAST_NAME"),
							"EMAIL" => Array("TYPE" => "PROPERTY", "VALUE" => "EMAIL"),
							"ADDRESS" => Array("TYPE" => "PROPERTY", "VALUE" => "ADDRESS"),
						)),
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "Y",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "N",
					))

				);
				$arPaySystems[] = Array(
					"NAME" => GetMessage("SALE_WIZARD_PS_WM"),
					"SORT" => 70,
					"ACTIVE" => "N",
					"DESCRIPTION" => GetMessage("SALE_WIZARD_PS_WM_DESCR"),
					"CODE_TEMP" => "webmoney",
					"ACTION" => Array(Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
						"NAME" => GetMessage("SALE_WIZARD_PS_WM"),
						"ACTION_FILE" => "/bitrix/modules/sale/payment/webmoney_web",
						"RESULT_FILE" => "",
						"NEW_WINDOW" => "Y",
						"PARAMS" => "",
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "Y",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "N",
					))
				);
				$arPaySystems[] = Array(
					"NAME" => GetMessage("SALE_WIZARD_PS_PC"),
					"SORT" => 80,
					"ACTIVE" => "N",
					"DESCRIPTION" => "",
					"CODE_TEMP" => "paycash",
					"ACTION" => Array(Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
						"NAME" => GetMessage("SALE_WIZARD_PS_PC"),
						"ACTION_FILE" => "/bitrix/modules/sale/payment/yandex",
						"RESULT_FILE" => "",
						"NEW_WINDOW" => "N",
						"PARAMS" => serialize(Array(
							"ORDER_ID" => Array("TYPE" => "ORDER", "VALUE" => "ID"),
							"USER_ID" => Array("TYPE" => "PROPERTY", "VALUE" => "FIO"),
							"ORDER_DATE" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT"),
							"SHOULD_PAY" => Array("TYPE" => "ORDER", "VALUE" => "PRICE"),
						)),
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "N",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "Y",
					))
				);
				if($paysystem["sber"] == "Y")
				{
					$arPaySystems[] = Array(
						"NAME" => GetMessage("SALE_WIZARD_PS_SB"),
						"SORT" => 90,
						"DESCRIPTION" => GetMessage("SALE_WIZARD_PS_SB_DESCR"),
						"CODE_TEMP" => "sberbank",
						"ACTION" => Array(Array(
							"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
							"NAME" => GetMessage("SALE_WIZARD_PS_SB"),
							"ACTION_FILE" => "/bitrix/modules/sale/payment/sberbank_new",
							"RESULT_FILE" => "",
							"NEW_WINDOW" => "Y",
							"PARAMS" => serialize(Array(
								"COMPANY_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
								"INN" => Array("TYPE" => "", "VALUE" => $shopINN),
								"KPP" => Array("TYPE" => "", "VALUE" => $shopKPP),
								"SETTLEMENT_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopNS),
								"BANK_NAME" => Array("TYPE" => "", "VALUE" => $shopBANK),
								"BANK_BIC" => Array("TYPE" => "", "VALUE" => $shopBANKREKV),
								"BANK_COR_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopKS),
								"ORDER_ID" => Array("TYPE" => "ORDER", "VALUE" => "ID"),
								"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
								"PAYER_CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => "FIO"),
								"PAYER_ZIP_CODE" => Array("TYPE" => "PROPERTY", "VALUE" => "ZIP"),
								"PAYER_COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => "LOCATION_COUNTRY"),
								"PAYER_CITY" => Array("TYPE" => "PROPERTY", "VALUE" => "LOCATION_CITY"),
								"PAYER_ADDRESS_FACT" => Array("TYPE" => "PROPERTY", "VALUE" => "ADDRESS"),
								"SHOULD_PAY" => Array("TYPE" => "ORDER", "VALUE" => "PRICE"),
							)),
							"HAVE_PAYMENT" => "Y",
							"HAVE_ACTION" => "N",
							"HAVE_RESULT" => "N",
							"HAVE_PREPAY" => "N",
							"HAVE_RESULT_RECEIVE" => "N",
						))

					);
				}
			}
			else
			{
				$arPaySystems[] = Array(
					"NAME" => "PayPal",
					"SORT" => 90,
					"DESCRIPTION" => "",
					"CODE_TEMP" => "paypal",
					"ACTION" => Array(Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
						"NAME" => "PayPal",
						"ACTION_FILE" => "/bitrix/modules/sale/payment/paypal",
						"RESULT_FILE" => "",
						"NEW_WINDOW" => "N",
						"PARAMS" => serialize(Array(
							"ORDER_ID" => Array("TYPE" => "ORDER", "VALUE" => "ID"),
							"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
							"SHOULD_PAY" => Array("TYPE" => "ORDER", "VALUE" => "SHOULD_PAY"),
							"CURRENCY" => Array("TYPE" => "ORDER", "VALUE" => "CURRENCY"),
						)),
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "N",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "Y",
					))

				);
			}
		}
		if($personType["ur"] == "Y" && $paysystem["bill"] == "Y")
		{
			$arPaySystems[] = Array(
				"NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
				"SORT" => 100,
				"DESCRIPTION" => "",
				"CODE_TEMP" => "bill",
				"ACTION" => Array(Array(
					"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
					"NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
					"ACTION_FILE" => "/bitrix/modules/sale/payment/bill",
					"RESULT_FILE" => "",
					"NEW_WINDOW" => "Y",
					"PARAMS" => serialize(Array(
						"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
						"SELLER_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
						"SELLER_ADDRESS" => Array("TYPE" => "", "VALUE" => $shopAdr),
						"SELLER_PHONE" => Array("TYPE" => "", "VALUE" => $siteTelephone),
						"SELLER_INN" => Array("TYPE" => "", "VALUE" => $shopINN),
						"SELLER_KPP" => Array("TYPE" => "", "VALUE" => $shopKPP),
						"SELLER_RS" => Array("TYPE" => "", "VALUE" => $shopNS),
						"SELLER_KS" => Array("TYPE" => "", "VALUE" => $shopKS),
						"SELLER_BIK" => Array("TYPE" => "", "VALUE" => $shopBANKREKV),
						"BUYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_NAME"),
						"BUYER_INN" => Array("TYPE" => "PROPERTY", "VALUE" => "INN"),
						"BUYER_ADDRESS" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"),
						"BUYER_PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => "PHONE"),
						"BUYER_FAX" => Array("TYPE" => "PROPERTY", "VALUE" => "FAX"),
						"BUYER_PAYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "CONTACT_PERSON"),
						"PATH_TO_STAMP" => Array("TYPE" => "", "VALUE" => $siteStamp),
					)),
					"HAVE_PAYMENT" => "Y",
					"HAVE_ACTION" => "N",
					"HAVE_RESULT" => "N",
					"HAVE_PREPAY" => "N",
					"HAVE_RESULT_RECEIVE" => "N",
				))

			);
		}
//Ukraine
		if($shopLocalization == "ua")
		{
			//oshadbank
			if (($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y") && $paysystem["oshad"] == "Y")
			{
				$arPaySystems[] = Array(
					"NAME" => GetMessage("SALE_WIZARD_PS_OS"),
					"SORT" => 90,
					"DESCRIPTION" => GetMessage("SALE_WIZARD_PS_OS_DESCR"),
					"CODE_TEMP" => "oshadbank",
					"ACTION" => Array(
						Array(
							"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
							"NAME" => GetMessage("SALE_WIZARD_PS_OS"),
							"ACTION_FILE" => "/bitrix/modules/sale/payment/oshadbank",
							"RESULT_FILE" => "",
							"NEW_WINDOW" => "Y",
							"PARAMS" => serialize(Array(
								"COMPANY_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
								"INN" => Array("TYPE" => "", "VALUE" => $shopINN_ua),
								"EGRPU" => Array("TYPE" => "", "VALUE" => $shopEGRPU_ua),
								"SETTLEMENT_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopNS_ua),
								"BANK_NAME" => Array("TYPE" => "", "VALUE" => $shopBANK),
								"BANK_BIC" => Array("TYPE" => "", "VALUE" => $shopBank_ua),
								"MFO" => Array("TYPE" => "", "VALUE" => $shopMFO_ua),
								"NDS" => Array("TYPE" => "", "VALUE" => $shopNDS_ua),
								"Place" => Array("TYPE" => "", "VALUE" => $shopPlace_ua),
								"FIO" => Array("TYPE" => "", "VALUE" => $shopFIO_ua),
								"Tax" => Array("TYPE" => "", "VALUE" => $shopTax_ua),
								"ORDER_ID" => Array("TYPE" => "ORDER", "VALUE" => "ID"),
								"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
								"PAYER_CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => "FIO"),
								"PAYER_ZIP_CODE" => Array("TYPE" => "PROPERTY", "VALUE" => "ZIP"),
								"PAYER_COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => "LOCATION_COUNTRY"),
								"PAYER_CITY" => Array("TYPE" => "PROPERTY", "VALUE" => "LOCATION_CITY"),
								"PAYER_ADDRESS_FACT" => Array("TYPE" => "PROPERTY", "VALUE" => "ADDRESS"),
								"SHOULD_PAY" => Array("TYPE" => "ORDER", "VALUE" => "PRICE"),
							)),
							"HAVE_PAYMENT" => "Y",
							"HAVE_ACTION" => "N",
							"HAVE_RESULT" => "N",
							"HAVE_PREPAY" => "N",
							"HAVE_RESULT_RECEIVE" => "N",
						),
						Array(
							"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
							"NAME" => GetMessage("SALE_WIZARD_PS_OS"),
							"ACTION_FILE" => "/bitrix/modules/sale/payment/oshadbank",
							"RESULT_FILE" => "",
							"NEW_WINDOW" => "Y",
							"PARAMS" => serialize(Array(
								"COMPANY_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
								"INN" => Array("TYPE" => "", "VALUE" => $shopINN_ua),
								"EGRPU" => Array("TYPE" => "", "VALUE" => $shopEGRPU_ua),
								"SETTLEMENT_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopNS_ua),
								"BANK_NAME" => Array("TYPE" => "", "VALUE" => $shopBANK),
								"BANK_BIC" => Array("TYPE" => "", "VALUE" => $shopBank_ua),
								"MFO" => Array("TYPE" => "", "VALUE" => $shopMFO_ua),
								"NDS" => Array("TYPE" => "", "VALUE" => $shopNDS_ua),
								"Place" => Array("TYPE" => "", "VALUE" => $shopPlace_ua),
								"FIO" => Array("TYPE" => "", "VALUE" => $shopFIO_ua),
								"Tax" => Array("TYPE" => "", "VALUE" => $shopTax_ua),
								"ORDER_ID" => Array("TYPE" => "ORDER", "VALUE" => "ID"),
								"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
								"PAYER_CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => "FIO"),
								"PAYER_ZIP_CODE" => Array("TYPE" => "PROPERTY", "VALUE" => "ZIP"),
								"PAYER_COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => "LOCATION_COUNTRY"),
								"PAYER_CITY" => Array("TYPE" => "PROPERTY", "VALUE" => "LOCATION_CITY"),
								"PAYER_ADDRESS_FACT" => Array("TYPE" => "PROPERTY", "VALUE" => "ADDRESS"),
								"SHOULD_PAY" => Array("TYPE" => "ORDER", "VALUE" => "PRICE"),
							)),
							"HAVE_PAYMENT" => "Y",
							"HAVE_ACTION" => "N",
							"HAVE_RESULT" => "N",
							"HAVE_PREPAY" => "N",
							"HAVE_RESULT_RECEIVE" => "N",
						)
					)
				);
			}

			//bill
			if (($personType["fiz"] == "Y" || $personType["fiz_ua"] == "Y") && $paysystem["oshad"] == "Y")
			{
				$arPaySystemTmp = Array(
					"NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
					"SORT" => 100,
					"DESCRIPTION" => "",
					"CODE_TEMP" => "bill"
				);

				if ($personType["ur"] == "Y")
					$arPaySystemTmp["ACTION"][] =  Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"],
						"NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
						"ACTION_FILE" => "/bitrix/modules/sale/payment/bill_ua",
						"RESULT_FILE" => "",
						"NEW_WINDOW" => "Y",
						"PARAMS" => serialize(Array(
							"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
							"SELLER_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
							"SELLER_ADDRESS" => Array("TYPE" => "", "VALUE" => $shopAdr),
							"SELLER_PHONE" => Array("TYPE" => "", "VALUE" => $siteTelephone),
							"INN" => Array("TYPE" => "", "VALUE" => $shopINN_ua),
							"EGRPU" => Array("TYPE" => "", "VALUE" => $shopEGRPU_ua),
							"SETTLEMENT_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopNS_ua),
							"BANK_NAME" => Array("TYPE" => "", "VALUE" => $shopBANK),
							"BANK_BIC" => Array("TYPE" => "", "VALUE" => $shopBank_ua),
							"MFO" => Array("TYPE" => "", "VALUE" => $shopMFO_ua),
							"NDS" => Array("TYPE" => "", "VALUE" => $shopNDS_ua),
							"Place" => Array("TYPE" => "", "VALUE" => $shopPlace_ua),
							"FIO" => Array("TYPE" => "", "VALUE" => $shopFIO_ua),
							"Tax" => Array("TYPE" => "", "VALUE" => $shopTax_ua),
							"BUYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_NAME"),
							"BUYER_INN" => Array("TYPE" => "PROPERTY", "VALUE" => "INN"),
							"BUYER_ADDRESS" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"),
							"BUYER_PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => "PHONE"),
							"BUYER_FAX" => Array("TYPE" => "PROPERTY", "VALUE" => "FAX"),
							"BUYER_PAYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "CONTACT_PERSON"),
							"PATH_TO_STAMP" => Array("TYPE" => "", "VALUE" => $siteStamp),
						)),
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "N",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "N",
					);
				if ($personType["fiz"] == "Y")
					$arPaySystemTmp["ACTION"][] =  Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"],
						"NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
						"ACTION_FILE" => "/bitrix/modules/sale/payment/bill_ua",
						"RESULT_FILE" => "",
						"NEW_WINDOW" => "Y",
						"PARAMS" => serialize(Array(
							"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
							"SELLER_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
							"SELLER_ADDRESS" => Array("TYPE" => "", "VALUE" => $shopAdr),
							"SELLER_PHONE" => Array("TYPE" => "", "VALUE" => $siteTelephone),
							"INN" => Array("TYPE" => "", "VALUE" => $shopINN_ua),
							"EGRPU" => Array("TYPE" => "", "VALUE" => $shopEGRPU_ua),
							"SETTLEMENT_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopNS_ua),
							"BANK_NAME" => Array("TYPE" => "", "VALUE" => $shopBANK),
							"BANK_BIC" => Array("TYPE" => "", "VALUE" => $shopBank_ua),
							"MFO" => Array("TYPE" => "", "VALUE" => $shopMFO_ua),
							"NDS" => Array("TYPE" => "", "VALUE" => $shopNDS_ua),
							"Place" => Array("TYPE" => "", "VALUE" => $shopPlace_ua),
							"FIO" => Array("TYPE" => "", "VALUE" => $shopFIO_ua),
							"Tax" => Array("TYPE" => "", "VALUE" => $shopTax_ua),
							"BUYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_NAME"),
							"BUYER_INN" => Array("TYPE" => "PROPERTY", "VALUE" => "INN"),
							"BUYER_ADDRESS" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"),
							"BUYER_PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => "PHONE"),
							"BUYER_FAX" => Array("TYPE" => "PROPERTY", "VALUE" => "FAX"),
							"BUYER_PAYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "CONTACT_PERSON"),
							"PATH_TO_STAMP" => Array("TYPE" => "", "VALUE" => $siteStamp),
						)),
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "N",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "N",
					);
				if ($personType["fiz_ua"] == "Y")
					$arPaySystemTmp["ACTION"][] =  Array(
						"PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz_ua"],
						"NAME" => GetMessage("SALE_WIZARD_PS_BILL"),
						"ACTION_FILE" => "/bitrix/modules/sale/payment/bill_ua",
						"RESULT_FILE" => "",
						"NEW_WINDOW" => "Y",
						"PARAMS" => serialize(Array(
							"DATE_INSERT" => Array("TYPE" => "ORDER", "VALUE" => "DATE_INSERT_DATE"),
							"SELLER_NAME" => Array("TYPE" => "", "VALUE" => $shopOfName),
							"SELLER_ADDRESS" => Array("TYPE" => "", "VALUE" => $shopAdr),
							"SELLER_PHONE" => Array("TYPE" => "", "VALUE" => $siteTelephone),
							"INN" => Array("TYPE" => "", "VALUE" => $shopINN_ua),
							"EGRPU" => Array("TYPE" => "", "VALUE" => $shopEGRPU_ua),
							"SETTLEMENT_ACCOUNT" => Array("TYPE" => "", "VALUE" => $shopNS_ua),
							"BANK_NAME" => Array("TYPE" => "", "VALUE" => $shopBANK),
							"BANK_BIC" => Array("TYPE" => "", "VALUE" => $shopBank_ua),
							"MFO" => Array("TYPE" => "", "VALUE" => $shopMFO_ua),
							"NDS" => Array("TYPE" => "", "VALUE" => $shopNDS_ua),
							"Place" => Array("TYPE" => "", "VALUE" => $shopPlace_ua),
							"FIO" => Array("TYPE" => "", "VALUE" => $shopFIO_ua),
							"Tax" => Array("TYPE" => "", "VALUE" => $shopTax_ua),
							"BUYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_NAME"),
							"BUYER_INN" => Array("TYPE" => "PROPERTY", "VALUE" => "INN"),
							"BUYER_ADDRESS" => Array("TYPE" => "PROPERTY", "VALUE" => "COMPANY_ADR"),
							"BUYER_PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => "PHONE"),
							"BUYER_FAX" => Array("TYPE" => "PROPERTY", "VALUE" => "FAX"),
							"BUYER_PAYER_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => "CONTACT_PERSON"),
							"PATH_TO_STAMP" => Array("TYPE" => "", "VALUE" => $siteStamp),
						)),
						"HAVE_PAYMENT" => "Y",
						"HAVE_ACTION" => "N",
						"HAVE_RESULT" => "N",
						"HAVE_PREPAY" => "N",
						"HAVE_RESULT_RECEIVE" => "N",
					);
				$arPaySystems[] = $arPaySystemTmp;
			}
		}
	}
		
	foreach($arPaySystems as $val)
	{
		$id = CSalePaySystem::Add(
			Array(
				"LID" => WIZARD_SITE_ID,
				"CURRENCY" => $defCurrency,
				"NAME" => $val["NAME"],
				"ACTIVE" => ($val["ACTIVE"] == "N") ? "N" : "Y",
				"SORT" => $val["SORT"],
				"DESCRIPTION" => $val["DESCRIPTION"]
			)
		);
		foreach($val["ACTION"] as $action)
		{
			$arGeneralInfo["paySystem"][$val["CODE_TEMP"]][$action["PERSON_TYPE_ID"]] = $id;
			$action["PAY_SYSTEM_ID"] = $id;
			CSalePaySystemAction::Add($action);
		}
	}
	

	$bStatusP = false;
	$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"));
	while($arStatus = $dbStatus->Fetch())
	{
		$arFields = Array();
		foreach($arLanguages as $langID)
		{
			WizardServices::IncludeServiceLang("step1.php", $langID);
			$arFields["LANG"][] = Array("LID" => $langID, "NAME" => GetMessage("WIZ_SALE_STATUS_".$arStatus["ID"]), "DESCRIPTION" => GetMessage("WIZ_SALE_STATUS_DESCRIPTION_".$arStatus["ID"]));
		}
		$arFields["ID"] = $arStatus["ID"];
		CSaleStatus::Update($arStatus["ID"], $arFields);
		if($arStatus["ID"] == "P")
			$bStatusP = true;
	}
	if(!$bStatusP)
	{
		$arFields = Array("ID" => "P", "SORT" => 150);
		foreach($arLanguages as $langID)
		{
			WizardServices::IncludeServiceLang("step1.php", $langID);
			$arFields["LANG"][] = Array("LID" => $langID, "NAME" => GetMessage("WIZ_SALE_STATUS_P"), "DESCRIPTION" => GetMessage("WIZ_SALE_STATUS_DESCRIPTION_P"));
		}

		CSaleStatus::Add($arFields);
	}
		
	if(CModule::IncludeModule("currency"))
	{
		$dbCur = CCurrency::GetList($by="currency", $o = "asc");
		while($arCur = $dbCur->Fetch())
		{	
			if($lang == "ru")
				CCurrencyLang::Update($arCur["CURRENCY"], $lang, Array("DECIMALS" => 0));
			elseif($arCur["CURRENCY"] == "EUR")
				CCurrencyLang::Update($arCur["CURRENCY"], $lang, Array("DECIMALS" => 2, "FORMAT_STRING" => "&euro;#"));
		}
	}
	WizardServices::IncludeServiceLang("step1.php", $lang);
	CModule::IncludeModule("catalog");

	$dbVat = CCatalogVat::GetList(array(), Array("SITE_ID" => WIZARD_SITE_ID));
	if(!($dbVat->Fetch()))
	{
		$arF = Array ("ACTIVE" => "Y", "SORT" => "100", "NAME" => GetMessage("WIZ_VAT_1"), "RATE" => 0);
		CCatalogVat::Set($arF);
		$arF = Array ("ACTIVE" => "Y", "SORT" => "200", "NAME" => GetMessage("WIZ_VAT_2"), "RATE" => GetMessage("WIZ_VAT_2_VALUE"));
		CCatalogVat::Set($arF);
	}
	$dbResultList = CCatalogGroup::GetList(Array(), Array("CODE" => "BASE"));
	if($arRes = $dbResultList->Fetch())
	{
		$arFields = Array();
		foreach($arLanguages as $langID)
		{
			WizardServices::IncludeServiceLang("step1.php", $langID);
			$arFields["USER_LANG"][$langID] = GetMessage("WIZ_PRICE_NAME");
		}
		$arFields["BASE"] = "Y";
		if($wizard->GetVar("installPriceBASE") == "Y"){
			$db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID"=>'1', "BUY"=>"Y"));
			if ($ar_res = $db_res->Fetch())
			{
			   $wizGroupId[] = $ar_res['GROUP_ID'];
			}
			$wizGroupId[] = 2;
			$arFields["USER_GROUP"] = $wizGroupId;
			$arFields["USER_GROUP_BUY"] = $wizGroupId;
		}
		CCatalogGroup::Update($arRes["ID"], $arFields);
	}

	//making orders
	function __MakeOrder($prdCnt=1, $arData = Array())
	{
		global $APPLICATION, $USER, $DB;
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("sale");
		CModule::IncludeModule("catalog");
		$arPrd = Array();
		$dbItem = CIBlockElement::GetList(Array("PROPERTY_MORE_PHOTO" => "DESC", "ID" => "ASC"), Array("IBLOCK_TYPE" => "catalog", "IBLOCK_SITE_ID" => WIZARD_SITE_ID, "PROPERTY_NEWPRODUCT" => false), false, Array("nTopCount" => 10), Array("ID", "IBLOCK_ID", "XML_ID", "NAME", "DETAIL_PAGE_URL", "IBLOCK_XML_ID"));
		while($arItem = $dbItem->GetNext())
			$arPrd[] = $arItem;

		if(!empty($arPrd))
		{
			for($i=0; $i<$prdCnt;$i++)
			{
				$prdID = $arPrd[mt_rand(0, 9)];
				$arProduct = CCatalogProduct::GetByID($prdID["ID"]);
				$CALLBACK_FUNC = "CatalogBasketCallback";
				$arCallbackPrice = CSaleBasket::ReReadPrice($CALLBACK_FUNC, "catalog", $prdID["ID"], 1);
				
				$arFields = array(
						"PRODUCT_ID" => $prdID["ID"],
						"PRODUCT_PRICE_ID" => $arCallbackPrice["PRODUCT_PRICE_ID"],
						"PRICE" => $arCallbackPrice["PRICE"],
						"CURRENCY" => $arCallbackPrice["CURRENCY"],
						"WEIGHT" => $arProduct["WEIGHT"],
						"QUANTITY" => 1,
						"LID" => WIZARD_SITE_ID,
						"DELAY" => "N",
						"CAN_BUY" => "Y",
						"NAME" => $prdID["NAME"],
						"CALLBACK_FUNC" => $CALLBACK_FUNC,
						"MODULE" => "catalog",
						"ORDER_CALLBACK_FUNC" => "CatalogBasketOrderCallback",
						"CANCEL_CALLBACK_FUNC" => "CatalogBasketCancelCallback",
						"PAY_CALLBACK_FUNC" => "CatalogPayOrderCallback",
						"DETAIL_PAGE_URL" => $prdID["DETAIL_PAGE_URL"],
						"CATALOG_XML_ID" => $prdID["IBLOCK_XML_ID"],
						"PRODUCT_XML_ID" => $prdID["XML_ID"],			
						"VAT_RATE" => $arCallbackPrice['VAT_RATE'],
					);
				$addres = CSaleBasket::Add($arFields);
			}
			
			$arOrder = Array(
					"LID" => $arData["SITE_ID"],
					"PERSON_TYPE_ID" => $arData["PERSON_TYPE_ID"],
					"PAYED" => "N",
					"CANCELED" => "N",
					"STATUS_ID" => "N",
					"PRICE" => 1,
					"CURRENCY" => $arData["CURRENCY"],
					"USER_ID" => $arData["USER_ID"],
					"PAY_SYSTEM_ID" => $arData["PAY_SYSTEM_ID"],
					//"PRICE_DELIVERY" => $arData["PRICE_DELIVERY"],
					//"DELIVERY_ID" => $arData["DELIVERY_ID"],
				);

			$dbFUserListTmp = CSaleUser::GetList(array("USER_ID" => $arData["USER_ID"]));
			if(empty($dbFUserListTmp))
			{
				$arFields = array(
						"=DATE_INSERT" => $DB->GetNowFunction(),
						"=DATE_UPDATE" => $DB->GetNowFunction(),
						"USER_ID" => $arData["USER_ID"]
					);

				$ID = CSaleUser::_Add($arFields);
			}

			$orderID = CSaleOrder::Add($arOrder);
			CSaleBasket::OrderBasket($orderID, CSaleBasket::GetBasketUserID(), WIZARD_SITE_ID);
			$dbBasketItems = CSaleBasket::GetList(
					array("NAME" => "ASC"),
					array(
							"FUSER_ID" => CSaleBasket::GetBasketUserID(),
							"LID" => WIZARD_SITE_ID,
							"ORDER_ID" => $orderID
						),
					false,
					false,
					array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME")
				);
			$ORDER_PRICE = 0;
			while ($arBasketItems = $dbBasketItems->GetNext())
			{
				$ORDER_PRICE += roundEx($arBasketItems["PRICE"], SALE_VALUE_PRECISION) * DoubleVal($arBasketItems["QUANTITY"]);
			}
			
			$totalOrderPrice = $ORDER_PRICE + $arData["PRICE_DELIVERY"];
			CSaleOrder::Update($orderID, Array("PRICE" => $totalOrderPrice));
			foreach($arData["PROPS"] as $val)
			{
				$arFields = Array(
						"ORDER_ID" => $orderID,
						"ORDER_PROPS_ID" => $val["ID"],
						"NAME" => $val["NAME"],
						"CODE" => $val["CODE"],
						"VALUE" => $val["VALUE"],
					);
				CSaleOrderPropsValue::Add($arFields);
			}
			return $orderID;
		}
	}

	$personType = $arGeneralInfo["personType"]["ur"];
	if(IntVal($arGeneralInfo["personType"]["fiz"]) > 0)
		$personType = $arGeneralInfo["personType"]["fiz"];
	if(IntVal($personType) <= 0)
	{
		$dbPerson = CSalePersonType::GetList(array(), Array("LID" => WIZARD_SITE_ID));
		if($arPerson = $dbPerson->Fetch())
		{
			$personType = $arPerson["ID"];
		}
	}
	if(IntVal($arGeneralInfo["paySystem"]["cash"][$personType]) > 0 )
		$paySystem = $arGeneralInfo["paySystem"]["cash"][$personType];
	elseif(IntVal($arGeneralInfo["paySystem"]["bill"][$personType]) > 0 )
		$paySystem = $arGeneralInfo["paySystem"]["bill"][$personType];
	elseif(IntVal($arGeneralInfo["paySystem"]["bill"][$personType]) > 0 )
		$paySystem = $arGeneralInfo["paySystem"]["sber"][$personType];
	elseif(IntVal($arGeneralInfo["paySystem"]["paypal"][$personType]) > 0 )
		$paySystem = $arGeneralInfo["paySystem"]["paypal"][$personType];
	else
	{
		$dbPS = CSalePaySystem::GetList(Array(), Array("LID" => WIZARD_SITE_ID));
		if($arPS = $dbPS->Fetch())
			$paySystem = $arPS["ID"];
	}
	if(IntVal($location) <= 0)
	{
		$dbLocation = CSaleLocation::GetList(Array("ID" => "ASC"), Array("LID" => $lang));
		if($arLocation = $dbLocation->Fetch())
		{
			$location = $arLocation["ID"];
		}
	}
	if(empty($arGeneralInfo["properies"][$personType]))
	{
		$dbProp = CSaleOrderProps::GetList(array(), Array("PERSON_TYPE_ID" => $personType));
		while($arProp = $dbProp->Fetch())
			$arGeneralInfo["properies"][$personType][$arProp["CODE"]] = $arProp;
	}
	
	if(WIZARD_INSTALL_DEMO_DATA)
	{
		
		$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), array("LID" => WIZARD_SITE_ID), false, false, array("ID"));
		while ($ar_sales = $db_sales->Fetch())
		{
			CSaleOrder::Delete($ar_sales["ID"]);
		}
	}
		
	$arData = Array(
			"SITE_ID" => WIZARD_SITE_ID,
			"PERSON_TYPE_ID" => $personType,
			"CURRENCY" => $defCurrency,
			"USER_ID" => 1,
			"PAY_SYSTEM_ID" => $paySystem,
			//"PRICE_DELIVERY" => "0",
			//"DELIVERY_ID" => "",
			"PROPS" => Array(),
		);
	foreach($arGeneralInfo["properies"][$personType] as $key => $val)
	{
		$arProp = Array(
					"ID" => $val["ID"],
					"NAME" => $val["NAME"],
					"CODE" => $val["CODE"],
					"VALUE" => "",
				);

		if($key == "FIO" || $key == "CONTACT_PERSON")
			$arProp["VALUE"] = GetMessage("WIZ_ORD_FIO");
		elseif($key == "ADDRESS" || $key == "COMPANY_ADR")
			$arProp["VALUE"] = GetMessage("WIZ_ORD_ADR");
		elseif($key == "EMAIL")
			$arProp["VALUE"] = "example@example.com";
		elseif($key == "PHONE")
			$arProp["VALUE"] = "8 495 2312121";
		elseif($key == "ZIP")
			$arProp["VALUE"] = "101000";
		elseif($key == "LOCATION")
			$arProp["VALUE"] = $location;
		elseif($key == "CITY")
			$arProp["VALUE"] = $shopLocation;
		$arData["PROPS"][] = $arProp;
	}
	$orderID = __MakeOrder(3, $arData);
	CSaleOrder::DeliverOrder($orderID, "Y");
	CSaleOrder::PayOrder($orderID, "Y");
	CSaleOrder::StatusOrder($orderID, "F");
	$orderID = __MakeOrder(4, $arData);
	CSaleOrder::DeliverOrder($orderID, "Y");
	CSaleOrder::PayOrder($orderID, "Y");
	CSaleOrder::StatusOrder($orderID, "F");
	$orderID = __MakeOrder(2, $arData);
	CSaleOrder::PayOrder($orderID, "Y");
	CSaleOrder::StatusOrder($orderID, "P");
	$orderID = __MakeOrder(1, $arData);
	$orderID = __MakeOrder(3, $arData);
	CSaleOrder::CancelOrder($orderID, "Y");
	CAgent::RemoveAgent("CSaleProduct::RefreshProductList();", "sale");
	CAgent::AddAgent("CSaleProduct::RefreshProductList();", "sale", "N", 60*60*24*4, "", "Y");

}
return true;
?>