<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if (!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule('sale'))
	return;

if (WIZARD_IS_RERUN)
	return;
	
	$wizard =& $this->GetWizard();
	//настройка заказчиков и платежных систем
	$personType = $wizard->GetVar("personType");			
	
	//создание типов платильщиков
		$arGeneralInfo["personType"]["fiz"] = CSalePersonType::Add(Array(
						"LID" => WIZARD_SITE_ID,
						"NAME" => GetMessage("SALE_WIZARD_PERSON_1"),
						"SORT" => "100"
						)
					);
			

		$arGeneralInfo["personType"]["ur"] = CSalePersonType::Add(Array(
						"LID" => WIZARD_SITE_ID,
						"NAME" => GetMessage("SALE_WIZARD_PERSON_2"),
						"SORT" => "150"
						)
					);
			
				
		//группы свойств
		$arGeneralInfo["propGroup"]["user_fiz"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ1"), "SORT" => 100));
		$arGeneralInfo["propGroup"]["adres_fiz"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["fiz"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_FIZ2"), "SORT" => 200));
		
		$arGeneralInfo["propGroup"]["user_ur"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR1"), "SORT" => 300));
		$arGeneralInfo["propGroup"]["adres_ur"] = CSaleOrderPropsGroup::Add(Array("PERSON_TYPE_ID" => $arGeneralInfo["personType"]["ur"], "NAME" => GetMessage("SALE_WIZARD_PROP_GROUP_UR2"), "SORT" => 400));
				
		//свойства физ лица
		$arProps = Array();
		
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
					"NAME" => GetMessage("SALE_WIZARD_PROP_2"),
					"TYPE" => "LOCATION",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 140,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "Y",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_fiz"],
					"SIZE1" => 3,
					"SIZE2" => 0,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "LOCATION",
					"IS_FILTERED" => "N",
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
		//свойства юр лица
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
					"NAME" => GetMessage("SALE_WIZARD_PROP_2"),
					"TYPE" => "LOCATION",
					"REQUIED" => "Y",
					"DEFAULT_VALUE" => "",
					"SORT" => 290,
					"USER_PROPS" => "Y",
					"IS_LOCATION" => "Y",
					"PROPS_GROUP_ID" => $arGeneralInfo["propGroup"]["adres_ur"],
					"SIZE1" => 3,
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
					"SIZE2" => 3,
					"DESCRIPTION" => "",
					"IS_EMAIL" => "N",
					"IS_PROFILE_NAME" => "N",
					"IS_PAYER" => "N",
					"IS_LOCATION4TAX" => "N",
					"CODE" => "ADDRESS",
					"IS_FILTERED" => "N",
				);

	foreach($arProps as $prop)
		{
			$variants = Array();
			if(!empty($prop["VARIANTS"]))
			{
				$variants = $prop["VARIANTS"];
				unset($prop["VARIANTS"]);
			}
			$id = CSaleOrderProps::Add($prop);
			
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

//настройка платежных систем
	
	
	$arPaySystems = Array();
			$arPaySystemTmp = Array(
						"NAME" => GetMessage("SALE_WIZARD_PS_CASH"),
						"SORT" => 50,
						"ACTIVE" => "Y",
						"DESCRIPTION" => GetMessage("SALE_WIZARD_PS_CASH_DESCR"),
						"CODE_TEMP" => "cash");
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
			$arPaySystems[] = $arPaySystemTmp;
			
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
						"RESULT_FILE" => "/bitrix/modules/sale/payment/assist_res.php",
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
//								"PAYER_ZIP_CODE" => Array("TYPE" => "PROPERTY", "VALUE" => "ZIP"),
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
	//создание платежных систем
	$currency="RUB";		
	foreach($arPaySystems as $val)
		{
			$id = CSalePaySystem::Add(
				Array(
					"LID" => WIZARD_SITE_ID,
					"CURRENCY" => $currency,
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
				$resAct=CSalePaySystemAction::Add($action);
			}
		}
		


?>