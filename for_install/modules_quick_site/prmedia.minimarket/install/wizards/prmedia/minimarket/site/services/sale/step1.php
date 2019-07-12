<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!CModule::IncludeModule('catalog') || !CModule::IncludeModule('sale'))
{
	return;
}

$permission1c = COption::GetOptionString('catalog', '1C_GROUP_PERMISSIONS');
if (empty($permission1c))
{
	COption::SetOptionString('catalog', '1C_GROUP_PERMISSIONS', '1', GetMessage('SALE_1C_GROUP_PERMISSIONS'));
}

$moduleId = 'prmedia.minimarket';
$generalInfo = array();

// get currency and language
$defaultCurrency = 'RUB';
$lang = 'ru';
$rsSite = CSite::GetByID(WIZARD_SITE_ID);
if ($arSite = $rsSite->Fetch())
{
	$lang = $arSite['LANGUAGE_ID'];
}
if (empty($lang))
{
	$lang = 'ru';
}

// get site languages
$languages = array();
$rsLanguage = CLanguage::GetList($by = 'id', $order = 'asc', array());
while ($language = $rsLanguage->Fetch())
{
	$languages[] = $language['LID'];
}

// include lang file
WizardServices::IncludeServiceLang('step1.php', $lang);

// install locations if necessary
$varLocations = $wizard->GetVar('locations_csv');
if ($varLocations != 'loc_none' && in_array($varLocations, array('loc_ussr.csv')))
{
	define('LOC_STEP_LENGTH', 20);

	$timeLimit = ini_get('max_execution_time');
	if ($timeLimit < LOC_STEP_LENGTH)
	{
		set_time_limit(LOC_STEP_LENGTH + 5);
	}
	$timeStart = time();
	$timeFinish = $timeStart + LOC_STEP_LENGTH;

	$fileUrl = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH . "/locations/ru/$varLocations";
	if (file_exists($fileUrl))
	{
		$actionFinish = true;

		$sysLangs = array();
		$rsLangAdmin = CLangAdmin::GetList($by = 'sort', $order = 'asc', array('ACTIVE' => 'Y'));
		while ($langAdmin = $rsLangAdmin->Fetch())
		{
			$sysLangs[$langAdmin['LID']] = $langAdmin['LID'];
		} 

		// if there are another locations then locations'll be synchronized
		$actionSync = true;
		$rsLocations = CSaleLocation::GetList();
		if ($location = $rsLocations->Fetch())
		{
			$actionSync = false;
		}
		
		// import class for work with CSV files
		include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/csv_data.php');

		// open CSV locations file
		$csvFile = new CCSVData();
		$csvFile->LoadFile($fileUrl);
		$csvFile->SetFieldsType('R');
		$csvFile->SetFirstHeader(false);
		$csvFile->SetDelimiter(',');

		$arRes = $csvFile->Fetch();
		if (is_array($arRes) && count($arRes) > 0 && strlen($arRes[0]) == 2)
		{
			$DefLang = $arRes[0];
			if (in_array($DefLang, $sysLangs))
			{
				if (is_set($_SESSION['LOC_POS']))
				{
					$csvFile->SetPos($_SESSION['LOC_POS']);
					$CurCountryID = $_SESSION['CUR_COUNTRY_ID'];
					$CurRegionID = $_SESSION['CUR_REGION_ID'];
					$numCountries = $_SESSION['NUM_COUNTRIES'];
					$numRegiones = $_SESSION['NUM_REGIONES'];
					$numCities = $_SESSION['NUM_CITIES'];
					$numLocations = $_SESSION['NUM_LOCATIONS'];
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
					$ararrayTmp = array();
					foreach ($arRes as $ind => $value)
					{
						if ($ind % 2 && isset($sysLangs[$value]))
						{
							$ararrayTmp[$value] = array(
								'LID' => $value,
								'NAME' => $arRes[$ind + 1]
							);

							if ($value == $DefLang)
							{
								$ararrayTmp ['NAME'] = $arRes[$ind + 1];
							}
						}
					}

					//country
					if (is_array($ararrayTmp) && strlen($ararrayTmp['NAME']) > 0)
					{
						if ($type == 'S')
						{
							$CurRegionID = null;
							$arRegionList = array();
							$CurCountryID = null;
							$arContList = array();
							$LLL = 0;
							if ($actionSync)
							{
								$db_contList = CSaleLocation::GetList(
										array(), array(
										'COUNTRY_NAME' => $ararrayTmp['NAME'],
										'LID' => $DefLang
										)
								);
								if ($arContList = $db_contList->Fetch())
								{
									$LLL = IntVal($arContList['ID']);
									$CurCountryID = IntVal($arContList['COUNTRY_ID']);
								}
							}
							if (IntVal($CurCountryID) <= 0)
							{
								$CurCountryID = CSaleLocation::AddCountry($ararrayTmp);
								$CurCountryID = IntVal($CurCountryID);
								if ($CurCountryID > 0)
								{
									$numCountries++;
									if (IntVal($LLL) <= 0)
									{
										$LLL = CSaleLocation::AddLocation(array('COUNTRY_ID' => $CurCountryID));
										if (IntVal($LLL) > 0)
											$numLocations++;
									}
								}
							}
						}
						elseif ($type == 'R') //region
						{
							$CurRegionID = null;
							$arRegionList = array();
							$LLL = 0;
							if ($actionSync)
							{
								$db_rengList = CSaleLocation::GetList(
									array(), array(
									'COUNTRY_ID' => $CurCountryID,
									'REGION_NAME' => $ararrayTmp['NAME'],
									'LID' => $DefLang
									)
								);
								if ($arRegionList = $db_rengList->Fetch())
								{
									$LLL = $arRegionList['ID'];
									$CurRegionID = IntVal($arRegionList['REGION_ID']);
								}
							}

							if (IntVal($CurRegionID) <= 0)
							{
								$CurRegionID = CSaleLocation::AddRegion($ararrayTmp);
								$CurRegionID = IntVal($CurRegionID);
								if ($CurRegionID > 0)
								{
									$numRegiones++;
									if (IntVal($LLL) <= 0)
									{
										$LLL = CSaleLocation::AddLocation(array('COUNTRY_ID' => $CurCountryID, 'REGION_ID' => $CurRegionID));
										if (IntVal($LLL) > 0)
											$numLocations++;
									}
								}
							}
						}
						elseif ($type == 'T' && IntVal($CurCountryID) > 0) //city
						{
							$city_id = 0;
							$LLL = 0;
							$arCityList = array();

							if ($actionSync)
							{
								$arFilter = array(
									'COUNTRY_ID' => $CurCountryID,
									'CITY_NAME' => $ararrayTmp['NAME'],
									'LID' => $DefLang);
								if (IntVal($CurRegionID) > 0)
									$arFilter['REGION_ID'] = $CurRegionID;

								$db_cityList = CSaleLocation::GetList(
										array(), $arFilter
								);
								if ($arCityList = $db_cityList->Fetch())
								{
									$LLL = $arCityList['ID'];
									$city_id = IntVal($arCityList['CITY_ID']);
								}
							}



							if ($city_id <= 0)
							{
								$city_id = CSaleLocation::AddCity($ararrayTmp);
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
												'COUNTRY_ID' => $CurCountryID,
												'REGION_ID' => $CurRegionID,
												'CITY_ID' => $city_id
									));

									if (intval
											($LLL) > 0)
										$numLocations++;
								}
							}
						}
					}

					if ($tt == 10)
					{
						$tt = 0;
						$cur_time = time();

						if ($cur_time >= $timeFinish)
						{
							$cur_step = $csvFile->GetPos();
							$amount = $csvFile->iFileLength;

							$_SESSION['LOC_POS'] = $cur_step;
							$_SESSION['CUR_COUNTRY_ID'] = $CurCountryID;
							$_SESSION['CUR_REGION_ID'] = $CurRegionID;
							$_SESSION ['NUM_COUNTRIES'] = $numCountries;
							$_SESSION['NUM_REGIONES'] = $numRegiones;
							$_SESSION['NUM_CITIES'] = $numCities;
							$_SESSION['NUM_LOCATIONS'] = $numLocations;

							$this->repeatCurrentService = true;

							$actionFinish = false;
						}
					}
				}
			}
		}

		if ($actionFinish)
		{
			unset($_SESSION['LOC_POS']);
		}
		else
		{
			return true;
		}

		// export ZIP for Russian cities
		$timeStart = time();
		$timeFinish = $timeStart + LOC_STEP_LENGTH;
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH . '/locations/ru/zip_ussr.csv'))
		{
			$rsLocations = CSaleLocation::GetList(array(), array('LID' => 'ru'), false, false, array('ID','CITY_NAME_LANG', 'REGION_NAME_LANG'));
			$arLocationMap = array();
			while ($arLocation = $rsLocations->Fetch())
			{
				if (strlen($arLocation['CITY_NAME_LANG']) > 0)
				{
					if (strlen($arLocation ['REGION_NAME_LANG']) > 0)
						$arLocationMap[$arLocation['CITY_NAME_LANG']][$arLocation['REGION_NAME_LANG']] = $arLocation['ID'];
					else
						$arLocationMap[$arLocation['CITY_NAME_LANG']] = $arLocation['ID'];
				}
			}

			$DB->StartTransaction();

			$csvFile = new CCSVData();
			$csvFile->LoadFile($_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH . '/locations/ru/zip_ussr.csv');
			$csvFile->SetFieldsType('R');
			$csvFile->SetFirstHeader(false);
			$csvFile->SetDelimiter(';');

			if (is_set($_SESSION, 'ZIP_POS'))
			{
				$numZIP = $_SESSION['NUM_ZIP'];
				$csvFile->SetPos($_SESSION['ZIP_POS']);
			}
			else
			{
				CSaleLocation::ClearAllLocationZIP();

				unset($_SESSION['NUM_ZIP']);
				$numZIP = 0;
			}

			$actionFinish = true;
			$arLocationsZIP = array();
			$tt = 0;
			$REGION = '';
			while ($arRes = $csvFile->Fetch())
			{
				$tt++;
				$CITY = $arRes[1];
				if (strlen($arRes[3]) > 0)
					$REGION = $arRes[3];

				if (array_key_exists($CITY, $arLocationMap))
				{
					if (strlen($REGION) > 0)
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

				if ($tt == 10)
				{
					$tt = 0;

					$cur_time = time();

					if ($cur_time >= $timeFinish)
					{
						$cur_step = $csvFile->GetPos();
						$amount = $csvFile->iFileLength;


						$_SESSION['ZIP_POS'] = $cur_step;
						$_SESSION[
							'NUM_ZIP'] = $numZIP;

						$actionFinish = false;

						$this->repeatCurrentService = true;
					}
				}
			}

			$DB->Commit();

			if ($actionFinish)
			{
				unset($_SESSION['ZIP_POS']);
			}
			else
			{
				return true;
			}
		}
	}
}

// add/update sale language
$saleLangFields = array(
	'LID' => WIZARD_SITE_ID,
	'CURRENCY' => $defaultCurrency
);
if (CSaleLang::GetByID(WIZARD_SITE_ID))
{
	CSaleLang::Update(WIZARD_SITE_ID, $saleLangFields);
}
else
{
	CSaleLang::Add($saleLangFields);
}

// save payment details
$paymentDetails = array(
	'shopname', 'shoplocation', 'shopaddr', 'shopinn', 'shopkpp',
	'shopns', 'shopbank', 'shopbankrekv', 'shopks',
	'sitename', 'shopemail'
);
foreach ($paymentDetails as $details)
{
	${$details} = $wizard->GetVar($details);
	COption::SetOptionString($moduleId, $details, ${$details}, false, WIZARD_SITE_ID);
}

// update site information
$site = new CSite;
$site->Update(WIZARD_SITE_ID, array(
	'EMAIL' => $shopemail,
	'SITE_NAME' => $sitename,
	'SERVER_NAME' => $_SERVER['SERVER_NAME']
));

// payment stamp (update)
$shopstamp = intval($wizard->GetVar('shopstamp'));
if ($shopstamp > 0)
{
	$rsStamp = CFile::GetByID($shopstamp);
	if ($stamp = $rsStamp->Fetch())
	{
		$stampPath = WIZARD_SITE_ROOT_PATH . '/';
		$stampPath .= COption::GetOptionString('main', 'upload_dir', 'upload') . '/' . $stamp['SUBDIR'] . '/' . $stamp['FILE_NAME'];
		@copy($stampPath, WIZARD_SITE_PATH . 'include_areas/stamp.gif');
		CFile::Delete($stamp['ID']);
		$sitestamp = WIZARD_SITE_DIR . 'include_areas/stamp.gif';
		COption::SetOptionString($moduleId, 'shopstamp', $shopstamp, false, WIZARD_SITE_ID);
	}
}

// person types
$personTypes = $wizard->GetVar('persontype');

$personTypeExists = array(
	'f' => false,
	'u' => false
);
$personTypeNames = array();
$personTypeParam = array(
	'filter' => array(
		'LID' => WIZARD_SITE_ID
	)
);
$rsPersonType = CSalePersonType::GetList(false, $personTypeParam['filter']);
while ($personType = $rsPersonType->Fetch())
{
	$personTypeNames[$personType['ID']] = $personType['NAME'];
}

$personTypeExists['f'] = in_array(GetMessage('SALE_WIZARD_PERSON_1'), $personTypeNames) ? true : false;
$personTypeExists['u'] = in_array(GetMessage('SALE_WIZARD_PERSON_2'), $personTypeNames) ? true : false;

COption::SetOptionString($moduleId, 'person_type_f', $personTypes['f'] == 'Y' ? 'Y' : 'N', false, WIZARD_SITE_ID);
COption::SetOptionString($moduleId, 'person_type_u', $personTypes['u'] == 'Y' ? 'Y' : 'N', false, WIZARD_SITE_ID);

// fiz
if (in_array(GetMessage('SALE_WIZARD_PERSON_1'), $personTypeNames))
{
	$generalInfo['persontype']['f'] = array_search(GetMessage('SALE_WIZARD_PERSON_1'), $personTypeNames);
	CSalePersonType::Update($generalInfo['persontype']['f'], array(
		'ACTIVE' => $personTypes['f'] == 'Y' ? 'Y' : 'N'
	));
}
else if ($personTypes['f'] == 'Y')
{
	$generalInfo['persontype']['f'] = CSalePersonType::Add(array(
		'LID' => WIZARD_SITE_ID,
		'NAME' => GetMessage('SALE_WIZARD_PERSON_1'),
		'SORT' => 100
	));
}

// ur
if (in_array(GetMessage('SALE_WIZARD_PERSON_2'), $personTypeNames))
{
	$generalInfo['persontype']['u'] = array_search(GetMessage('SALE_WIZARD_PERSON_2'), $personTypeNames);
	CSalePersonType::Update($generalInfo['persontype']['u'], array(
		'ACTIVE' => $personTypes['u'] == 'Y' ? 'Y' : 'N'
	));
}
else if ($personTypes['u'] == 'Y')
{
	$generalInfo['persontype']['u'] = CSalePersonType::Add(array(
		'LID' => WIZARD_SITE_ID,
		'NAME' => GetMessage('SALE_WIZARD_PERSON_2'),
		'SORT' => 150
	));
}

// set 'sale' module options
if (COption::GetOptionString($moduleId, 'wizard_installed', 'N', WIZARD_SITE_ID) != 'Y' || WIZARD_INSTALL_DEMO_DATA)
{
	COption::SetOptionString('sale', 'QUANTITY_FACTORIAL', 'Y');
	COption::SetOptionString('sale', 'default_currency', $defaultCurrency);
	COption::SetOptionString('sale', 'delete_after', '30');
	COption::SetOptionString('sale', 'order_list_date', '30');
	COption::SetOptionString('sale', 'MAX_LOCK_TIME', '30');
	COption::SetOptionString('sale', 'GRAPH_WEIGHT', '600');
	COption::SetOptionString('sale', 'GRAPH_HEIGHT', '600');
	COption::SetOptionString('sale', 'path2user_ps_files', '/bitrix/php_interface/include/sale_payment/');
	COption::SetOptionString('sale', 'lock_catalog', 'Y');
	COption::SetOptionString('sale', 'order_list_fields', 'ID,USER,PAY_SYSTEM,PRICE,STATUS,PAYED,PS_STATUS,CANCELED,BASKET');
	COption::SetOptionString('sale', 'GROUP_DEFAULT_RIGHT', 'D');
	COption::SetOptionString('sale', 'affiliate_param_name', 'partner');
	COption::SetOptionString('sale', 'show_order_sum', 'N');
	COption::SetOptionString('sale', 'show_order_product_xml_id', 'N');
	COption::SetOptionString('sale', 'show_paysystem_action_id', 'N');
	COption::SetOptionString('sale', 'affiliate_plan_type', 'N');
	COption::SetOptionString('sale', '1C_SALE_SITE_LIST', WIZARD_SITE_ID);
	COption::SetOptionString('sale', '1C_EXPORT_PAYED_ORDERS', 'N');
	COption::SetOptionString('sale', '1C_EXPORT_ALLOW_DELIVERY_ORDERS', 'N');
	COption::SetOptionString('sale', '1C_EXPORT_FINAL_ORDERS', '');
	COption::SetOptionString('sale', '1C_FINAL_STATUS_ON_DELIVERY', 'F');
	COption::SetOptionString('sale', '1C_REPLACE_CURRENCY', GetMessage('SALE_WIZARD_PS_BILL_RUB'));
	COption::SetOptionString('sale', '1C_SALE_USE_ZIP', 'Y');
	COption::SetOptionString('sale', 'weight_unit', GetMessage('SALE_WIZARD_WEIGHT_UNIT'), false, WIZARD_SITE_ID);
	COption::SetOptionString('sale', 'WEIGHT_different_set', 'N', false, WIZARD_SITE_ID);
	COption::SetOptionString('sale', 'ADDRESS_different_set', 'N');
	COption::SetOptionString('sale', 'measurement_path', '/bitrix/modules/sale/measurements.php');
	COption::SetOptionString('sale', 'delivery_handles_custom_path', '/bitrix/php_interface/include/sale_delivery/');
	COption::SetOptionString('sale', 'location_zip', '101000');
	COption::SetOptionString('sale', 'weight_koef', '1000', false, WIZARD_SITE_ID);
	COption::SetOptionString('sale', 'recalc_product_list', 'Y');
	COption::SetOptionString('sale', 'recalc_product_list_period', '4');
	COption::SetOptionString('sale', 'order_email', $shopemail);
	COption::SetOptionString('sale', 'encode_fuser_id', 'Y');
	$shopLocation = GetMessage('WIZ_CITY');
	
	$location = 0;
	$dbLocation = CSaleLocation::GetList(array('ID' => 'ASC'), array('LID' => $lang, 'CITY_NAME' => $shopLocation));
	if ($arLocation = $dbLocation->Fetch())
	{
		$location = $arLocation['ID'];
	}
	if (IntVal($location) <= 0)
	{
		$CurCountryID = 0;
		$db_contList = CSaleLocation::GetList(
				array(), array(
				'COUNTRY_NAME' => GetMessage('WIZ_COUNTRY_RU'),
				'LID' => $lang
				)
		);
		if ($arContList = $db_contList->Fetch())
		{

			$LLL = IntVal($arContList['ID']);
			$CurCountryID = IntVal($arContList['COUNTRY_ID']);
		}
		if (IntVal($CurCountryID) <= 0)
		{
			$ararrayTmp = array();
			$ararrayTmp['NAME'] = GetMessage('WIZ_COUNTRY_RU');
			foreach ($languages as $langId)
			{
				WizardServices::IncludeServiceLang('step1.php', $langId);
				$ararrayTmp[$langId] = array(
					'LID' => $langId,
					'NAME' => GetMessage('WIZ_COUNTRY_RU')
				);
			}
			$CurCountryID = CSaleLocation::AddCountry($ararrayTmp);
		}

		$ararrayTmp = array();
		$ararrayTmp['NAME'] = 'ru';
		foreach ($languages as $langId)
		{
			$ararrayTmp[$langId] = array(
				'LID' => $langId,
				'NAME' => 'ru'
			);
		}
		$city_id = CSaleLocation::AddCity($ararrayTmp);

		$location = CSaleLocation::AddLocation(array(
			'COUNTRY_ID' => $CurCountryID,
			'CITY_ID' => $city_id
		));
		CSaleLocation::AddLocationZIP($location, '101000');

		WizardServices::IncludeServiceLang('step1.php', $lang);
	}
	COption::SetOptionString('sale', 'location', $location);
}


// order propepties groups
if ($personTypeExists['f'])
{
	$dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList(false, array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_FIZ1')
	), false, false, array('ID'));
	if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext())
	{
		$generalInfo['propGroup']['user_f'] = $arSaleOrderPropsGroup['ID'];
	}

	$dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList(false, array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_FIZ2')
	), false, false, array('ID'));
	if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext())
	{
		$generalInfo['propGroup']['address_f'] = $arSaleOrderPropsGroup['ID'];
	}
}
else if ($personTypes['f'] == 'Y')
{
	$generalInfo['propGroup']['user_f'] = CSaleOrderPropsGroup::Add(array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_FIZ1'),
		'SORT' => 100
	));
	$generalInfo['propGroup']['address_f'] = CSaleOrderPropsGroup::Add(array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_FIZ2'),
		'SORT' => 200
	));
}

if ($personTypeExists['u'])
{
	$dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList(false, array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_UR1')
	), false, false, array('ID'));
	if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext())
	{
		$generalInfo['propGroup']['user_u'] = $arSaleOrderPropsGroup['ID'];
	}
		
	$dbSaleOrderPropsGroup = CSaleOrderPropsGroup::GetList(false, array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_UR2')
	), false, false, array('ID'));
	if ($arSaleOrderPropsGroup = $dbSaleOrderPropsGroup->GetNext())
	{
		$generalInfo['propGroup']['address_u'] = $arSaleOrderPropsGroup['ID'];
	}
}
else if ($personTypes['u'] == 'Y')
{
	$generalInfo['propGroup']['user_u'] = CSaleOrderPropsGroup::Add(array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_UR1'),
		'SORT' => 300
	));
	$generalInfo['propGroup']['address_u'] = CSaleOrderPropsGroup::Add(array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_GROUP_UR2'),
		'SORT' => 400
	));
}

// order propepties 
$arProps = array();
if ($personTypes['f'] == 'Y')
{
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_6'),
		'TYPE' => 'TEXT',
		'REQUIED' => 'Y',
		'DEFAULT_VALUE' => '',
		'SORT' => 100,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'N',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_f'],
		'SIZE1' => 40,
		'SIZE2' => 0,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'N',
		'IS_PROFILE_NAME' => 'Y',
		'IS_PAYER' => 'Y',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'FIO',
		'IS_FILTERED' => 'Y',
	);
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => 'E-Mail',
		'TYPE' => 'TEXT',
		'REQUIED' => 'Y',
		'DEFAULT_VALUE' => '',
		'SORT' => 110,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'N',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_f'],
		'SIZE1' => 40,
		'SIZE2' => 0,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'Y',
		'IS_PROFILE_NAME' => 'N',
		'IS_PAYER' => 'N',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'EMAIL',
		'IS_FILTERED' => 'Y',
	);
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_9'),
		'TYPE' => 'TEXT',
		'REQUIED' => 'Y',
		'DEFAULT_VALUE' => '',
		'SORT' => 120,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'N',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_f'],
		'SIZE1' => 0,
		'SIZE2' => 0,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'N',
		'IS_PROFILE_NAME' => 'N',
		'IS_PAYER' => 'N',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'PHONE',
		'IS_FILTERED' => 'N',
	);
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_4'),
		'TYPE' => 'TEXT',
		'REQUIED' => 'N',
		'DEFAULT_VALUE' => '101000',
		'SORT' => 130,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'N',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_f'],
		'SIZE1' => 8,
		'SIZE2' => 0,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'N',
		'IS_PROFILE_NAME' => 'N',
		'IS_PAYER' => 'N',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'ZIP',
		'IS_FILTERED' => 'N',
		'IS_ZIP' => 'Y',
	);
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_21'),
		'ACTIVE' => 'N',
		'TYPE' => 'TEXT',
		'REQUIED' => 'N',
		'DEFAULT_VALUE' => $shopLocation,
		'SORT' => 145,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'N',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_f'],
		'SIZE1' => 40,
		'SIZE2' => 0,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'N',
		'IS_PROFILE_NAME' => 'N',
		'IS_PAYER' => 'N',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'CITY',
		'IS_FILTERED' => 'Y',
	);
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_2'),
		'TYPE' => 'LOCATION',
		'REQUIED' => 'Y',
		'DEFAULT_VALUE' => $location,
		'SORT' => 140,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'Y',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_f'],
		'SIZE1' => 40,
		'SIZE2' => 0,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'N',
		'IS_PROFILE_NAME' => 'N',
		'IS_PAYER' => 'N',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'LOCATION',
		'IS_FILTERED' => 'N',
		'INPUT_FIELD_LOCATION' => ''
	);
	$arProps[] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PROP_5'),
		'TYPE' => 'TEXTAREA',
		'REQUIED' => 'Y',
		'DEFAULT_VALUE' => '',
		'SORT' => 150,
		'USER_PROPS' => 'Y',
		'IS_LOCATION' => 'N',
		'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_f'],
		'SIZE1' => 30,
		'SIZE2' => 3,
		'DESCRIPTION' => '',
		'IS_EMAIL' => 'N',
		'IS_PROFILE_NAME' => 'N',
		'IS_PAYER' => 'N',
		'IS_LOCATION4TAX' => 'N',
		'CODE' => 'ADDRESS',
		'IS_FILTERED' => 'N',
	);
}
if ($personTypes['u'] == 'Y')
{
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_8'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'Y',
			'DEFAULT_VALUE' => '',
			'SORT' => 200,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_u'],
			'SIZE1' => 40,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'Y',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'COMPANY',
			'IS_FILTERED' => 'Y',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_7'),
			'TYPE' => 'TEXTAREA',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => '',
			'SORT' => 210,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_u'],
			'SIZE1' => 40,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'COMPANY_ADR',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_13'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => '',
			'SORT' => 220,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_u'],
			'SIZE1' => 0,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'INN',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_14'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => '',
			'SORT' => 230,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['user_u'],
			'SIZE1' => 0,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'KPP',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_10'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'Y',
			'DEFAULT_VALUE' => '',
			'SORT' => 240,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 0,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'Y',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'CONTACT_PERSON',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => 'E-Mail',
			'TYPE' => 'TEXT',
			'REQUIED' => 'Y',
			'DEFAULT_VALUE' => '',
			'SORT' => 250,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 40,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'Y',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'EMAIL',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_9'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => '',
			'SORT' => 260,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 0,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'PHONE',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_11'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => '',
			'SORT' => 270,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 0,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'FAX',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_4'),
			'TYPE' => 'TEXT',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => '101000',
			'SORT' => 280,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 8,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'ZIP',
			'IS_FILTERED' => 'N',
			'IS_ZIP' => 'Y',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_21'),
			'ACTIVE' => 'N',
			'TYPE' => 'TEXT',
			'REQUIED' => 'N',
			'DEFAULT_VALUE' => $shoplocation,
			'SORT' => 285,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 40,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'CITY',
			'IS_FILTERED' => 'Y',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_2'),
			'TYPE' => 'LOCATION',
			'REQUIED' => 'Y',
			'DEFAULT_VALUE' => '',
			'SORT' => 290,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'Y',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 40,
			'SIZE2' => 0,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'Y',
			'CODE' => 'LOCATION',
			'IS_FILTERED' => 'N',
		);
		$arProps[] = array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PROP_12'),
			'TYPE' => 'TEXTAREA',
			'REQUIED' => 'Y',
			'DEFAULT_VALUE' => '',
			'SORT' => 300,
			'USER_PROPS' => 'Y',
			'IS_LOCATION' => 'N',
			'PROPS_GROUP_ID' => $generalInfo['propGroup']['address_u'],
			'SIZE1' => 30,
			'SIZE2' => 40,
			'DESCRIPTION' => '',
			'IS_EMAIL' => 'N',
			'IS_PROFILE_NAME' => 'N',
			'IS_PAYER' => 'N',
			'IS_LOCATION4TAX' => 'N',
			'CODE' => 'ADDRESS',
			'IS_FILTERED' => 'N'
		);
}
$propCityId = 0;
foreach ($arProps as $prop)
{
	$variants = array();
	if (!empty($prop['VARIANTS']))
	{
		$variants = $prop['VARIANTS'];
		unset($prop['VARIANTS']);
	}

	if ($prop['CODE'] == 'LOCATION' && $propCityId > 0)
	{
		$prop['INPUT_FIELD_LOCATION'] = $propCityId;
		$propCityId = 0;
	}

	$dbSaleOrderProps = CSaleOrderProps::GetList(false, array(
		'PERSON_TYPE_ID' => $prop['PERSON_TYPE_ID'],
		'CODE' => $prop['CODE']
	));
	if ($arSaleOrderProps = $dbSaleOrderProps->GetNext())
	{
		$id = $arSaleOrderProps['ID'];
	}
	else
	{
		$id = CSaleOrderProps::Add($prop);
	}
	if ($prop['CODE'] == 'CITY')
	{
		$propCityId = $id;
	}
	if (strlen($prop['CODE']) > 0)
	{
		$generalInfo['propCodeID'][$prop['CODE']] = $id;
		$generalInfo['properies'][$prop['PERSON_TYPE_ID']][$prop['CODE']] = $prop;
		$generalInfo['properies'][$prop['PERSON_TYPE_ID']][$prop['CODE']]['ID'] = $id;
	}

	if (!empty($variants))
	{
		foreach ($variants as $val)
		{
			$val['ORDER_PROPS_ID'] = $id;
			CSaleOrderPropsVariant::Add($val);
		}
	}
}

// 1C export
if ($personTypes['f'] == 'Y' && !$personTypeExists['f'])
{
	$val = serialize(array(
		'AGENT_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['FIO']['ID']),
		'FULL_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['FIO']['ID']),
		'SURNAME' => array('TYPE' => 'USER', 'VALUE' => 'LAST_NAME'),
		'NAME' => array('TYPE' => 'USER', 'VALUE' => 'NAME'),
		'ADDRESS_FULL' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['ADDRESS']['ID']),
		'INDEX' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['ZIP']['ID']),
		'COUNTRY' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['LOCATION']['ID'] . '_COUNTRY'),
		'CITY' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['LOCATION']['ID'] . '_CITY'),
		'STREET' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['ADDRESS']['ID']),
		'EMAIL' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['EMAIL']['ID']),
		'CONTACT_PERSON' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['f']]['CONTACT_PERSON']['ID']),
		'IS_FIZ' => 'Y'
	));
	CSaleExport::Add(array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'VARS' => $val
	));
}
if ($personTypes['u'] == 'Y' && !$personTypeExists['u'])
{
	$val = serialize(array(
		'AGENT_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['COMPANY']['ID']),
		'FULL_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['COMPANY']['ID']),
		'ADDRESS_FULL' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['COMPANY_ADR']['ID']),
		'COUNTRY' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['LOCATION']['ID'] . '_COUNTRY'),
		'CITY' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['LOCATION']['ID'] . '_CITY'),
		'STREET' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['COMPANY_ADR']['ID']),
		'INN' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['INN']['ID']),
		'KPP' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['KPP']['ID']),
		'PHONE' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['PHONE']['ID']),
		'EMAIL' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['EMAIL']['ID']),
		'CONTACT_PERSON' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['NAME']['ID']),
		'F_ADDRESS_FULL' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['ADDRESS']['ID']),
		'F_COUNTRY' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['LOCATION']['ID'] . '_COUNTRY'),
		'F_CITY' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['LOCATION']['ID'] . '_CITY'),
		'F_INDEX' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['ZIP']['ID']),
		'F_STREET' => array('TYPE' => 'PROPERTY', 'VALUE' => $generalInfo['properies'][$generalInfo['persontype']['u']]['ADDRESS']['ID']),
		'IS_FIZ' => 'N',
	));
	CSaleExport::Add(array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
		'VARS' => $val
	));
}

$paysystem = $wizard->GetVar('paysystem');

// pay systems
$arPaySystems = array();
$arPaySystemTmp = array(
	'NAME' => GetMessage('SALE_WIZARD_PS_CASH'),
	'SORT' => 80,
	'ACTIVE' => $paysystem['cash'] == 'Y' ? 'Y' : 'N',
	'DESCRIPTION' => GetMessage('SALE_WIZARD_PS_CASH_DESCR'),
	'CODE_TEMP' => 'cash');
if ($personTypes['f'] == 'Y')
{
	$arPaySystemTmp['ACTION'][] = array(
		'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
		'NAME' => GetMessage('SALE_WIZARD_PS_CASH'),
		'ACTION_FILE' => '/bitrix/modules/sale/payment/cash',
		'RESULT_FILE' => '',
		'NEW_WINDOW' => 'N',
		'PARAMS' => '',
		'HAVE_PAYMENT' => 'Y',
		'HAVE_ACTION' => 'N',
		'HAVE_RESULT' => 'N',
		'HAVE_PREPAY' => 'N',
		'HAVE_RESULT_RECEIVE' => 'N',
	);
}
$arPaySystems[] = $arPaySystemTmp;

$arPaySystems[] = array(
	'NAME' => GetMessage('SALE_WIZARD_PS_COLLECT'),
	'SORT' => 110,
	'ACTIVE' => $paysystem['collect'] == 'Y' ? 'Y' : 'N',
	'DESCRIPTION' => GetMessage('SALE_WIZARD_PS_COLLECT_DESCR'),
	'CODE_TEMP' => 'collect',
	'ACTION' => array(
		array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
			'NAME' => GetMessage('SALE_WIZARD_PS_COLLECT'),
			'ACTION_FILE' => '/bitrix/modules/sale/payment/payment_forward_calc',
			'RESULT_FILE' => '',
			'NEW_WINDOW' => 'N',
			'HAVE_PAYMENT' => 'Y',
			'HAVE_ACTION' => 'N',
			'HAVE_RESULT' => 'N',
			'HAVE_PREPAY' => 'N',
			'HAVE_RESULT_RECEIVE' => 'N',
		),
		array(
			'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
			'NAME' => GetMessage('SALE_WIZARD_PS_COLLECT'),
			'ACTION_FILE' => '/bitrix/modules/sale/payment/payment_forward_calc',
			'RESULT_FILE' => '',
			'NEW_WINDOW' => 'N',
			'HAVE_PAYMENT' => 'Y',
			'HAVE_ACTION' => 'N',
			'HAVE_RESULT' => 'N',
			'HAVE_PREPAY' => 'N',
			'HAVE_RESULT_RECEIVE' => 'N'
		)
	)
);
if ($personTypes['f'] == 'Y')
{
	$arPaySystems[] = array(
		'NAME' => GetMessage('SALE_WIZARD_YMoney'),
		'SORT' => 50,
		'DESCRIPTION' => '',
		'CODE_TEMP' => 'yandex_3x',
		'ACTION' => array(
			array(
				'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
				'NAME' => GetMessage('SALE_WIZARD_YMoney'),
				'ACTION_FILE' => '/bitrix/modules/sale/payment/yandex_3x',
				'RESULT_FILE' => '',
				'NEW_WINDOW' => 'N',
				'PARAMS' => serialize(array(
					'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
					'USER_ID' => array('TYPE' => 'PROPERTY', 'VALUE' => 'FIO'),
					'ORDER_DATE' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT'),
					'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'PRICE'),
					'PAYMENT_VALUE' => array('VALUE' => 'PC'),
					'IS_TEST' => array('VALUE' => 'Y'),
					'CHANGE_STATUS_PAY' => array('VALUE' => 'Y')
				)),
				'HAVE_PAYMENT' => 'Y',
				'HAVE_ACTION' => 'N',
				'HAVE_RESULT' => 'N',
				'HAVE_PREPAY' => 'N',
				'HAVE_RESULT_RECEIVE' => 'Y',
			)
		)
	);

	$logo = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH . '/images/yandex_cards.gif';
	$arPicture = CFile::MakeFilearray($logo);
	$arPaySystems[] = array(
		'NAME' => GetMessage('SALE_WIZARD_YCards'),
		'SORT' => 60,
		'DESCRIPTION' => '',
		'CODE_TEMP' => 'yandex_3x',
		'ACTION' => array(
			array(
				'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
				'NAME' => GetMessage('SALE_WIZARD_YCards'),
				'ACTION_FILE' => '/bitrix/modules/sale/payment/yandex_3x',
				'RESULT_FILE' => '',
				'NEW_WINDOW' => 'N',
				'PARAMS' => serialize(array(
					'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
					'USER_ID' => array('TYPE' => 'PROPERTY', 'VALUE' => 'FIO'),
					'ORDER_DATE' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT'),
					'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'PRICE'),
					'PAYMENT_VALUE' => array('VALUE' => 'AC'),
					'IS_TEST' => array('VALUE' => 'Y'),
					'CHANGE_STATUS_PAY' => array('VALUE' => 'Y'),
				)),
				'HAVE_PAYMENT' => 'Y',
				'HAVE_ACTION' => 'N',
				'HAVE_RESULT' => 'N',
				'HAVE_PREPAY' => 'N',
				'HAVE_RESULT_RECEIVE' => 'Y',
				'LOGOTIP' => $arPicture
			))
	);
	$logo = $_SERVER['DOCUMENT_ROOT'] . WIZARD_SERVICE_RELATIVE_PATH . '/images/yandex_terminals.gif';
	$arPicture = CFile::MakeFilearray($logo);
	$arPaySystems[] = array(
		'NAME' => GetMessage('SALE_WIZARD_YTerminals'),
		'SORT' => 70,
		'DESCRIPTION' => '',
		'CODE_TEMP' => 'yandex_3x',
		'ACTION' => array(
			array(
				'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
				'NAME' => GetMessage('SALE_WIZARD_YTerminals'),
				'ACTION_FILE' => '/bitrix/modules/sale/payment/yandex_3x',
				'RESULT_FILE' => '',
				'NEW_WINDOW' => 'N',
				'PARAMS' => serialize(array(
					'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ID'),
					'USER_ID' => array('TYPE' => 'PROPERTY', 'VALUE' => 'FIO'),
					'ORDER_DATE' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT'),
					'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'PRICE'),
					'PAYMENT_VALUE' => array('VALUE' => 'GP'),
					'IS_TEST' => array('VALUE' => 'Y'),
					'CHANGE_STATUS_PAY' => array('VALUE' => 'Y')
				)),
				'HAVE_PAYMENT' => 'Y',
				'HAVE_ACTION' => 'N',
				'HAVE_RESULT' => 'N',
				'HAVE_PREPAY' => 'N',
				'HAVE_RESULT_RECEIVE' => 'Y',
				'LOGOTIP' => $arPicture
			))
	);
	$arPaySystems[] = array(
		'NAME' => GetMessage('SALE_WIZARD_PS_WM'),
		'SORT' => 90,
		'ACTIVE' => 'N',
		'DESCRIPTION' => GetMessage('SALE_WIZARD_PS_WM_DESCR'),
		'CODE_TEMP' => 'webmoney',
		'ACTION' => array(
			array(
				'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
				'NAME' => GetMessage('SALE_WIZARD_PS_WM'),
				'ACTION_FILE' => '/bitrix/modules/sale/payment/webmoney_web',
				'RESULT_FILE' => '',
				'NEW_WINDOW' => 'Y',
				'PARAMS' => '',
				'HAVE_PAYMENT' => 'Y',
				'HAVE_ACTION' => 'N',
				'HAVE_RESULT' => 'Y',
				'HAVE_PREPAY' => 'N',
				'HAVE_RESULT_RECEIVE' => 'N',
			))
	);

	if ($paysystem['sber'] == 'Y')
	{
		$arPaySystems[] = array(
			'NAME' => GetMessage('SALE_WIZARD_PS_SB'),
			'SORT' => 110,
			'DESCRIPTION' => GetMessage('SALE_WIZARD_PS_SB_DESCR'),
			'CODE_TEMP' => 'sberbank',
			'ACTION' => array(
				array(
					'PERSON_TYPE_ID' => $generalInfo['persontype']['f'],
					'NAME' => GetMessage('SALE_WIZARD_PS_SB'),
					'ACTION_FILE' => '/bitrix/modules/sale/payment/sberbank_new',
					'RESULT_FILE' => '',
					'NEW_WINDOW' => 'Y',
					'PARAMS' => serialize(array(
						'COMPANY_NAME' => array('TYPE' => '', 'VALUE' => $shopname),
						'INN' => array('TYPE' => '', 'VALUE' => $shopinn),
						'KPP' => array('TYPE' => '', 'VALUE' => $shopkpp),
						'SETTLEMENT_ACCOUNT' => array('TYPE' => '', 'VALUE' => $shopns),
						'BANK_NAME' => array('TYPE' => '', 'VALUE' => $shopbank),
						'BANK_BIC' => array('TYPE' => '', 'VALUE' => $shopbankrekv),
						'BANK_COR_ACCOUNT' => array('TYPE' => '', 'VALUE' => $shopks),
						'ORDER_ID' => array('TYPE' => 'ORDER', 'VALUE' => 'ACCOUNT_NUMBER'),
						'DATE_INSERT' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
						'PAYER_CONTACT_PERSON' => array('TYPE' => 'PROPERTY', 'VALUE' => 'FIO'),
						'PAYER_ZIP_CODE' => array('TYPE' => 'PROPERTY', 'VALUE' => 'ZIP'),
						'PAYER_COUNTRY' => array('TYPE' => 'PROPERTY', 'VALUE' => 'LOCATION_COUNTRY'),
						'PAYER_REGION' => array('TYPE' => 'PROPERTY', 'VALUE' => 'LOCATION_REGION'),
						'PAYER_CITY' => array('TYPE' => 'PROPERTY', 'VALUE' => 'LOCATION_CITY'),
						'PAYER_ADDRESS_FACT' => array('TYPE' => 'PROPERTY', 'VALUE' => 'ADDRESS'),
						'SHOULD_PAY' => array('TYPE' => 'ORDER', 'VALUE' => 'PRICE'),
					)),
					'HAVE_PAYMENT' => 'Y',
					'HAVE_ACTION' => 'N',
					'HAVE_RESULT' => 'N',
					'HAVE_PREPAY' => 'N',
					'HAVE_RESULT_RECEIVE' => 'N',
				))
		);
	}
}
if ($personTypes['u'] == 'Y' && $paysystem['bill'] == 'Y')
{
	$arPaySystems[] = array(
		'NAME' => GetMessage('SALE_WIZARD_PS_BILL'),
		'SORT' => 100,
		'DESCRIPTION' => '',
		'CODE_TEMP' => 'bill',
		'ACTION' => array(
			array(
				'PERSON_TYPE_ID' => $generalInfo['persontype']['u'],
				'NAME' => GetMessage('SALE_WIZARD_PS_BILL'),
				'ACTION_FILE' => '/bitrix/modules/sale/payment/bill',
				'RESULT_FILE' => '',
				'NEW_WINDOW' => 'Y',
				'PARAMS' => serialize(array(
					'DATE_INSERT' => array('TYPE' => 'ORDER', 'VALUE' => 'DATE_INSERT_DATE'),
					'SELLER_NAME' => array('TYPE' => '', 'VALUE' => $shopname),
					'SELLER_ADDRESS' => array('TYPE' => '', 'VALUE' => $shopaddr),
					'SELLER_PHONE' => array('TYPE' => '', 'VALUE' => $wizard->GetVar('phone')),
					'SELLER_INN' => array('TYPE' => '', 'VALUE' => $shopinn),
					'SELLER_KPP' => array('TYPE' => '', 'VALUE' => $shopkpp),
					'SELLER_RS' => array('TYPE' => '', 'VALUE' => $shopns),
					'SELLER_KS' => array('TYPE' => '', 'VALUE' => $shopks),
					'SELLER_BIK' => array('TYPE' => '', 'VALUE' => $shopbankrekv),
					'BUYER_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => 'COMPANY_NAME'),
					'BUYER_INN' => array('TYPE' => 'PROPERTY', 'VALUE' => 'INN'),
					'BUYER_ADDRESS' => array('TYPE' => 'PROPERTY', 'VALUE' => 'COMPANY_ADR'),
					'BUYER_PHONE' => array('TYPE' => 'PROPERTY', 'VALUE' => 'PHONE'),
					'BUYER_FAX' => array('TYPE' => 'PROPERTY', 'VALUE' => 'FAX'),
					'BUYER_PAYER_NAME' => array('TYPE' => 'PROPERTY', 'VALUE' => 'CONTACT_PERSON'),
					'PATH_TO_STAMP' => array('TYPE' => '', 'VALUE' => $shopstamp),
				)),
				'HAVE_PAYMENT' => 'Y',
				'HAVE_ACTION' => 'N',
				'HAVE_RESULT' => 'N',
				'HAVE_PREPAY' => 'N',
				'HAVE_RESULT_RECEIVE' => 'N',
			))
	);
}

foreach ($arPaySystems as $val)
{
	$dbSalePaySystem = CSalePaySystem::GetList(array(), array(
		'LID' => WIZARD_SITE_ID,
		'NAME' => $val['NAME']
	), false, false, array('ID', 'NAME'));
	if ($arSalePaySystem = $dbSalePaySystem->GetNext())
	{
		if ($arSalePaySystem['NAME'] == GetMessage('SALE_WIZARD_PS_SB') || $arSalePaySystem['NAME'] == GetMessage('SALE_WIZARD_PS_BILL') || $arSalePaySystem['NAME'] == GetMessage('SALE_WIZARD_PS_OS'))
		{
			foreach ($val['ACTION'] as $action)
			{
				$generalInfo['paySystem'][$val['CODE_TEMP']][$action['PERSON_TYPE_ID']] = $arSalePaySystem['ID'];
				$action['PAY_SYSTEM_ID'] = $arSalePaySystem['ID'];
				$dbSalePaySystemAction = CSalePaySystemAction::GetList(array(), array(
					'PAY_SYSTEM_ID' => $arSalePaySystem['ID'],
					'PERSON_TYPE_ID' => $action['PERSON_TYPE_ID']
				), false, false, array('ID'));
				if ($arSalePaySystemAction = $dbSalePaySystemAction->GetNext())
				{
					CSalePaySystemAction::Update($arSalePaySystemAction['ID'], $action);
				}
				else
				{
					if (strlen($action['ACTION_FILE']) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $action['ACTION_FILE'] . '/logo.gif'))
					{
						$action['LOGOTIP'] = CFile::MakeFilearray($_SERVER['DOCUMENT_ROOT'] . $action['ACTION_FILE'] . '/logo.gif');
					}

					CSalePaySystemAction::Add($action);
				}
			}
		}
	}
	else
	{
		$id = CSalePaySystem::Add(array(
			'LID' => WIZARD_SITE_ID,
			'CURRENCY' => $defaultCurrency,
			'NAME' => $val['NAME'],
			'ACTIVE' => ($val['ACTIVE'] == 'N') ? 'N' : 'Y', 'SORT' => $val['SORT'],
			'DESCRIPTION' => $val['DESCRIPTION']
		));

		foreach ($val['ACTION'] as &$action)
		{
			$generalInfo['paySystem'][$val['CODE_TEMP']][$action['PERSON_TYPE_ID']] = $id;
			$action['PAY_SYSTEM_ID'] = $id;
			if (
				strlen($action['ACTION_FILE']) > 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $action['ACTION_FILE'] . '/logo.gif') && !is_array($action['LOGOTIP'])
			)
			{
				$action['LOGOTIP'] = CFile::MakeFilearray($_SERVER['DOCUMENT_ROOT'] . $action['ACTION_FILE'] . '/logo.gif');
			}

			CSalePaySystemAction::Add($action);
		}
	}
}

if (COption::GetOptionString($moduleId, 'wizard_installed', 'N', WIZARD_SITE_ID) != 'Y' || WIZARD_INSTALL_DEMO_DATA)
{
	// set order statuses
	$bStatusP = false;
	$rsStatus = CSaleStatus::GetList();
	while ($status = $rsStatus->Fetch())
	{
		$fields = array();
		foreach ($languages as $langId)
		{
			WizardServices::IncludeServiceLang('step1.php', $langId);
			$fields['LANG'][] = array(
				'LID' => $langId,
				'NAME' => GetMessage('WIZ_SALE_STATUS_' . $status['ID']),
				'DESCRIPTION' => GetMessage('WIZ_SALE_STATUS_DESCRIPTION_' . $status['ID'])
			);
		}
		$fields['ID'] = $status['ID'];
		CSaleStatus::Update($status['ID'], $fields);
		if ($arStatus['ID'] == 'P')
		{
			$bStatusP = true;
		}
	}
	if (!$bStatusP)
	{
		$fields = array('ID' => 'P', 'SORT' => 150);
		foreach ($languages as $langId)
		{
			WizardServices::IncludeServiceLang('step1.php', $langId);
			$arFields['LANG'][] = array(
				'LID' => $langId,
				'NAME' => GetMessage('WIZ_SALE_STATUS_P'),
				'DESCRIPTION' => GetMessage('WIZ_SALE_STATUS_DESCRIPTION_P')
			);
		}
		CSaleStatus::Add($arFields);
	}

	// set currency
	if (CModule::IncludeModule('currency'))
	{
		$rsCurrency = CCurrency::GetList($by = 'currency', $order = 'asc');
		while ($currency = $rsCurrency->Fetch())
		{
			CCurrencyLang::Update($currency['CURRENCY'], $lang, array(
				'DECIMALS' => 2,
				'HIDE_ZERO' => 'N'
			));
		}
	}
	
	if (CModule::IncludeModule('catalog'))
	{
		$vatParams = array(
			'filter' => array(
				'RATE' => 0
			),
			'select' => array(
				'ID', 'RATE'
			)
		);
		
		// type: no vat
		$rsVat = CCatalogVat::GetListEx(false, $vatParams['filter'], false, false, $vatParams['select']);
		if (!($rsVat->Fetch()))
		{
			$fields = array(
				'ACTIVE' => 'Y',
				'SORT' => '100',
				'NAME' => GetMessage('WIZ_VAT_1'),
				'RATE' => 0
			);
			CCatalogVat::Add($fields);
		}
		
		// type: nds (18%)
		$vatParams['filter']['RATE'] = GetMessage('WIZ_VAT_2_VALUE');
		$rsVat = CCatalogVat::GetListEx(false, $vatParams['filter'], false, false, $vatParams['select']);
		if (!($rsVat->Fetch()))
		{
			$fields = array(
				'ACTIVE' => 'Y',
				'SORT' => '200',
				'NAME' => GetMessage('WIZ_VAT_2'),
				'RATE' => GetMessage('WIZ_VAT_2_VALUE')
			);
			CCatalogVat::Add($fields);
		}
		
		// base price
		$catalogGroupParams = array(
			'filter' => array(
				'NAME' => 'BASE'
			)
		);
		$rsCatalogGroup = CCatalogGroup::GetList(false, $catalogGroupParams['filter']);
		if ($catalogGroup = $rsCatalogGroup->Fetch())
		{
			$fields = array(
				'BASE' => 'Y'
			);
			foreach ($languages as $langId)
			{
				WizardServices::IncludeServiceLang('step1.php', $langId);
				$fields['USER_LANG'][$langId] = GetMessage('WIZ_PRICE_NAME');
			}
			
			// all users can by
			$catalogGroupFilter = array(
				'CATALOG_GROUP_ID' => '1',
				'BUY' => 'Y'
			);
			$rsCatalogGroup = CCatalogGroup::GetGroupsList($catalogGroupFilter);
			if ($cg = $rsCatalogGroup->Fetch())
			{
				$wizGroupId[] = $cg['GROUP_ID'];
			}
			$wizGroupId[] = 2;
			$fields['USER_GROUP'] = $wizGroupId;
			$fields['USER_GROUP_BUY'] = $wizGroupId;
			CCatalogGroup::Update($catalogGroup['ID'], $fields);
		}
		else
		{
			// add base price
			$fields = array(
				'CODE' => 'BASE',
				'BASE' => 'Y'
			);
			foreach ($languages as $langId)
			{
				WizardServices::IncludeServiceLang('step1.php', $langId);
				$fields['NAME'] = 'BASE';
				$fields['USER_LANG'][$langId] = GetMessage('WIZ_PRICE_NAME');
			}
			
			// all users can by
			$catalogGroupFilter = array(
				'CATALOG_GROUP_ID' => '1',
				'BUY' => 'Y'
			);
			$rsCatalogGroup = CCatalogGroup::GetGroupsList($catalogGroupFilter);
			if ($cg = $rsCatalogGroup->Fetch())
			{
				$wizGroupId[] = $cg['GROUP_ID'];
			}
			$wizGroupId[] = 2;
			$fields['USER_GROUP'] = $wizGroupId;
			$fields['USER_GROUP_BUY'] = $wizGroupId;
			CCatalogGroup::Add($fields);
		}
	}

	// remove orders for this site
	if (WIZARD_INSTALL_DEMO_DATA)
	{
		$orderParams = array(
			'order' => array(
				'DATE_INSERT' => 'ASC'
			),
			'filter' => array(
				'LID' => WIZARD_SITE_ID
			),
			'select' => array(
				'ID'
			)
		);
		$rsOrder = CSaleOrder::GetList($orderParams['order'], $orderParams['filter'], false, false, $orderParams['select']);
		while ($order = $rsOrder->Fetch())
		{
			CSaleOrder::Delete($order['ID']);
		}
	}
	
	CAgent::RemoveAgent('CSaleProduct::RefreshProductList();', 'sale');
	CAgent::AddAgent('CSaleProduct::RefreshProductList();', 'sale', 'N', 60 * 60 * 24 * 4, '', 'Y');
}