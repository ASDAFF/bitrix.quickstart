<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

if($_REQUEST["AJAX_CALL"] == "Y")
{
	$APPLICATION->RestartBuffer();
}

if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("SBB_TITLE"));

if (!array_key_exists("COLUMNS_LIST", $arParams) || empty($arParams["COLUMNS_LIST"]) || !is_array($arParams["COLUMNS_LIST"]))
	$arParams["COLUMNS_LIST"] = array("NAME", "PRICE", "TYPE", "QUANTITY", "DELETE", "DELAY", "WEIGHT");

$arParams["HIDE_COUPON"] = (($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N");

if (!isset($arParams['QUANTITY_FLOAT']))
	$arParams['QUANTITY_FLOAT'] = 'N';

$arParams['PRICE_VAT_SHOW_VALUE'] = $arParams['PRICE_VAT_SHOW_VALUE'] == 'N' ? 'N' : 'Y';
$arParams["SEND_NEW_USER_NOTIFY"] = (($arParams["SEND_NEW_USER_NOTIFY"] == "N") ? "N" : "Y");

$arParams["WEIGHT_UNIT"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
$arParams["WEIGHT_KOEF"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));

if (strlen($arParams["TEMPLATE_LOCATION"]) <= 0)
	$arParams["TEMPLATE_LOCATION"] = ".default";

$errorMessage = "";
$arResultProps = array();

$PERSON_TYPE = (isset($_POST["PERSON_TYPE"])) ? intval($_POST["PERSON_TYPE"]) : 0;
$PROFILE_ID = (isset($_POST["PROFILE_ID"])) ? intval($_POST["PROFILE_ID"]) : '';
$PROFILE_ID_OLD = (isset($_POST["PROFILE_ID_OLD"])) ? intval($_POST["PROFILE_ID_OLD"]) : '';
$PAYSYSTEM_ID = (isset($_POST["PAYSYSTEM_ID"])) ? htmlspecialcharsbx($_POST["PAYSYSTEM_ID"]) : '';
$DELIVERY_ID = (isset($_POST["DELIVERY_ID"])) ? htmlspecialcharsbx($_POST["DELIVERY_ID"]) : '';
$ORDER_DESCRIPTION = htmlspecialcharsbx(trim($_POST["ORDER_DESCRIPTION"]));
$ORDER_ID = (isset($_REQUEST["ORDER_ID"])) ? intval($_REQUEST["ORDER_ID"]) : '';

if(intval($ORDER_ID) <= 0)
{
	/*
	* person type
	*/
	$arPersonTypeList = array();
	$dbPersonType = CSalePersonType::GetList(array("ID" => "ASC", "NAME" => "ASC"), array("ACTIVE" => "Y"));
	while ($arPersonType = $dbPersonType->GetNext())
	{
		if (!in_array(SITE_ID, $arPersonType["LIDS"]))
			continue;

		if (!isset($PERSON_TYPE) || $PERSON_TYPE == "")
		{
			$PERSON_TYPE = $arPersonType["ID"];
			$arPersonType["CHECKED"] = "Y";
		}
		else
		{
			if ($_POST["PERSON_TYPE"] == $arPersonType["ID"])
				$arPersonType["CHECKED"] = "Y";
		}
		$arPersonTypeList[$arPersonType["ID"]] = $arPersonType;
	}
	$arResultProps["PERSON_TYPE"] = $arPersonTypeList;

	/*
	* user profile
	*/
	$arResultProps["USER_PROFILES"] = CSaleOrderUserProps::DoLoadProfiles(intval($USER->GetID()), $PERSON_TYPE);
	$arProfileTmp = array();

	if (!empty($arResultProps["USER_PROFILES"]) && is_array($arResultProps["USER_PROFILES"]))
	{
		foreach($arResultProps["USER_PROFILES"] as $key => $val)
		{
			if ($PROFILE_ID === "")
			{
				$arResultProps["USER_PROFILES"][$key]["CHECKED"] = "Y";
				$PROFILE_ID = $key;
			}
			elseif ($PROFILE_ID == $key)
			{
				$arResultProps["USER_PROFILES"][$key]["CHECKED"] = "Y";
			}
		}
	}
	else
		$PROFILE_ID = intval($PROFILE_ID);

	/*
	* orde props
	*/
	$userProfile = $arResultProps["USER_PROFILES"];
	$arPropValues = array();

	$arPropValues = $userProfile[$PROFILE_ID]["VALUES"];


	$arFilter = array("PERSON_TYPE_ID" => $PERSON_TYPE, "ACTIVE" => "Y", "UTIL" => "N");
	$dbProperties = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			$arFilter,
			false,
			false,
			array("ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "GROUP_NAME", "GROUP_SORT", "SORT", "USER_PROPS", "IS_ZIP", "INPUT_FIELD_LOCATION", "SUBSCRIBE")
	);
	$locationZipID = "";
	$locationID = "";
	$profileName = "";
	$payerName = "";
	$payerEMail = "";

	//load location for the index if isset index
	$locationForZip = "";
	if (isset($_REQUEST["CHANGE_ZIP"]) && $_REQUEST["CHANGE_ZIP"] == "Y")
	{
		$arFilterZip = array("PERSON_TYPE_ID" => $PERSON_TYPE, "IS_ZIP" => "Y", "ACTIVE" => "Y", "UTIL" => "N");
		$dbPropertiesZip = CSaleOrderProps::GetList(
				array("SORT" => "ASC"),
				$arFilterZip,
				false,
				false,
				array("ID")
		);
		$arPropZip = $dbPropertiesZip->GetNext();

		$zipCode = htmlspecialcharsEx($_POST["ORDER_PROP_".$arPropZip["ID"]]);

		$arZip = CSaleLocation::GetByZIP($zipCode);
		if (is_array($arZip) && count($arZip) > 1)
		{
			$locationForZip = intval($arZip["ID"]);
		}
	}

	while ($arProperties = $dbProperties->GetNext())
	{
		if ((isset($_POST["BasketOrder"]) || $_REQUEST["AJAX_CALL"] == "Y" || $_REQUEST["form"] == "Y") && $PROFILE_ID_OLD == $PROFILE_ID)
		{
			$curVal = htmlspecialcharsEx($_REQUEST["ORDER_PROP_".$arProperties["ID"]]);

			if (intval($_REQUEST["NEW_LOCATION_".$arProperties["ID"]]) > 0)
				$curVal = intval($_POST["NEW_LOCATION_".$arProperties["ID"]]);
		}
		else
			$curVal = $arPropValues[intval($arProperties["ID"])];

		$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"];

		if ($arProperties["REQUIED"]=="Y" || $arProperties["IS_EMAIL"]=="Y" || $arProperties["IS_PROFILE_NAME"]=="Y" || $arProperties["IS_LOCATION"]=="Y" || $arProperties["IS_LOCATION4TAX"]=="Y" || $arProperties["IS_PAYER"]=="Y" || $arProperties["IS_ZIP"]=="Y")
			$arProperties["REQUIED_FORMATED"]="Y";

		if ($arProperties["IS_PROFILE_NAME"] == "Y")
			$profileName = $curVal;
		if ($arProperties["IS_PAYER"] == "Y")
			$payerName = $curVal;
		if ($arProperties["IS_EMAIL"] == "Y")
			$payerEMail = $curVal;

		if ($arProperties["REQUIED_FORMATED"] == "Y" AND $curVal == "")
			$errorMessage .= str_replace("#NAME#", $arProperties["NAME"], GetMessage("SOE_EMPTY_PROP"))."<br>";

		if ($arProperties["TYPE"] == "CHECKBOX")
		{
			if ($curVal=="Y" || !isset($curVal) && $arProperties["DEFAULT_VALUE"]=="Y")
			{
				$arProperties["CHECKED"] = "Y";
				$arProperties["VALUE_FORMATED"] = GetMessage("SOA_Y");
			}
			else
				$arProperties["VALUE_FORMATED"] = GetMessage("SOA_N");

			$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 30);
		}
		elseif ($arProperties["TYPE"] == "TEXT")
		{
			if (strlen($curVal) <= 0)
			{
				if(strlen($arProperties["DEFAULT_VALUE"])>0 && !isset($curVal))
					$arProperties["VALUE"] = $arProperties["DEFAULT_VALUE"];
				elseif ($arProperties["IS_EMAIL"] == "Y")
					$arProperties["VALUE"] = $USER->GetEmail();
				elseif ($arProperties["IS_PAYER"] == "Y")
					$arProperties["VALUE"] = $USER->GetFullName();
			}
			else
				$arProperties["VALUE"] = $curVal;

			if ($arProperties["IS_ZIP"]=="Y")
			{
				$locationZipID = $arProperties["ID"];
				$_POST["ORDER_PROP_".$locationZipID] = $curVal;
			}

			$arProperties["VALUE"] = htmlspecialcharsEx($arProperties["VALUE"]);
			$arProperties["VALUE_FORMATED"] = $arProperties["VALUE"];
		}
		elseif ($arProperties["TYPE"] == "SELECT")
		{
			$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
			$dbVariants = CSaleOrderPropsVariant::GetList(
					array("SORT" => "ASC", "NAME" => "ASC"),
					array("ORDER_PROPS_ID" => $arProperties["ID"]),
					false,
					false,
					array("*")
			);
			$flagDefault = "N";
			$nameProperty = "";
			while ($arVariants = $dbVariants->GetNext())
			{
				if ($flagDefault == "N" && $nameProperty == "")
					$nameProperty = $arVariants["NAME"];

				if (($arVariants["VALUE"] == $curVal) || ((!isset($curVal) || $curVal == "") && ($arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])))
				{
					$arVariants["SELECTED"] = "Y";
					$arProperties["VALUE_FORMATED"] = $arVariants["NAME"];
					$flagDefault = "Y";
				}
				$arProperties["VARIANTS"][] = $arVariants;
			}
			if ($flagDefault == "N")
			{
				$arProperties["VARIANTS"][0]["SELECTED"]= "Y";
				$arProperties["VARIANTS"][0]["VALUE_FORMATED"] = $nameProperty;
			}
		}
		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"].'[]';
			$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 5);

			if (!is_array($curVal) && strlen($curVal) > 0)
				$curVal = explode(",", $curVal);

			$arDefVal = explode(",", $arProperties["DEFAULT_VALUE"]);
			for ($i = 0, $intCount = count($arDefVal); $i < $intCount; $i++)
				$arDefVal[$i] = Trim($arDefVal[$i]);

			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			$i = 0;
			while ($arVariants = $dbVariants->GetNext())
			{
				if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)) || (!isset($curVal) && in_array($arVariants["VALUE"], $arDefVal)))
				{
					$arVariants["SELECTED"] = "Y";
					if ($i > 0)
						$arProperties["VALUE_FORMATED"] .= ", ";
					$arProperties["VALUE_FORMATED"] .= $arVariants["NAME"];
					$i++;
				}
				$arProperties["VARIANTS"][] = $arVariants;
			}
		}
		elseif ($arProperties["TYPE"] == "TEXTAREA")
		{
			$arProperties["SIZE2"] = ((intval($arProperties["SIZE2"]) > 0) ? $arProperties["SIZE2"] : 4);
			$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 40);
			$arProperties["VALUE"] = (isset($curVal) ? $curVal : $arProperties["DEFAULT_VALUE"]);
			$arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arProperties["VALUE"]);
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			if (strlen($locationForZip) > 0 && $arProperties["IS_LOCATION"] == "Y")
				$curVal = $locationForZip;

			$locationID = $arProperties["ID"];
			$_POST["ORDER_PROP_".$locationID] = $curVal;

			//enable location text
			if ($_REQUEST["form"] == "Y" && $arProperties["IS_LOCATION"] == "Y" && intval($arProperties["INPUT_FIELD_LOCATION"]) > 0 && isset($_REQUEST["ORDER_PROP_".$arProperties["ID"]]))
			{
				$rsLocationsList = CSaleLocation::GetList(
					array(),
					array("ID" => $curVal),
					false,
					false,
					array("ID", "CITY_ID")
				);
				$arCity = $rsLocationsList->GetNext();

				if (intval($arCity["CITY_ID"]) <= 0)
					$bDeleteFieldLocation = "";
				else
					$bDeleteFieldLocation = intval($arProperties["INPUT_FIELD_LOCATION"]);
			}
			elseif ($arProperties["IS_LOCATION"] == "Y" && intval($arProperties["INPUT_FIELD_LOCATION"]) > 0)
				$bDeleteFieldLocation = intval($arProperties["INPUT_FIELD_LOCATION"]);

			$arProperties["SIZE1"] = ((intval($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
			$dbVariants = CSaleLocation::GetList(
					array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
					array("LID" => LANGUAGE_ID),
					false,
					false,
					array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
			);
			while ($arVariants = $dbVariants->GetNext())
			{
				if (intval($arVariants["ID"]) == intval($curVal) || (!isset($curVal) && intval($arVariants["ID"]) == intval($arProperties["DEFAULT_VALUE"])))
				{
					$arVariants["SELECTED"] = "Y";
					$arProperties["VALUE_FORMATED"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
					$arProperties["VALUE"] = $arVariants["ID"];
				}
				$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
				$arProperties["VARIANTS"][] = $arVariants;
			}
			if(count($arProperties["VARIANTS"]) == 1)
				$arProperties["VALUE"] = $arProperties["VARIANTS"][0]["ID"];
		}
		elseif ($arProperties["TYPE"] == "RADIO")
		{
			$dbVariants = CSaleOrderPropsVariant::GetList(
					array("SORT" => "ASC"),
					array("ORDER_PROPS_ID" => $arProperties["ID"]),
					false,
					false,
					array("*")
			);
			while ($arVariants = $dbVariants->GetNext())
			{
				if ($arVariants["VALUE"] == $curVal || (!isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"]))
				{
					$arVariants["CHECKED"]="Y";
					$arProperties["VALUE_FORMATED"] = $arVariants["NAME"];
				}

				$arProperties["VARIANTS"][] = $arVariants;
			}
		}

		if ($arProperties["TYPE"] == "CHECKBOX" && strlen($curVal) <= 0 && $arProperties["REQUIED"] != "Y")
		{
			$curVal = "N";
		}

		if ((!empty($curVal) && is_array($curVal)) || (!is_array($curVal) && (string)$curVal != "") )
			$arPropValues[$arProperties["ID"]] = $curVal;

		if($arProperties["USER_PROPS"]=="Y")
			$arResultProps["ORDER_PROPS"]["USER_PROPS_Y"][$arProperties["ID"]] = $arProperties;
		else
			$arResultProps["ORDER_PROPS"]["USER_PROPS_N"][$arProperties["ID"]] = $arProperties;
	}
	/*end order props*/

	//delete prop for text location
	$bDeleteFieldLocation = intval($bDeleteFieldLocation);
	if ($bDeleteFieldLocation > 0)
		unset($arResultProps["ORDER_PROPS"]["USER_PROPS_Y"][$bDeleteFieldLocation]);

	/*
	* action
	*/
	if ((strlen($_REQUEST["BasketRefresh"]) > 0 OR strlen($_REQUEST["action"]) > 0))
	{
		if(strlen($_REQUEST["action"]) > 0)
		{
			$id = intval($_REQUEST["id"]);
			if($id > 0)
			{
				$dbBasketItems = CSaleBasket::GetList(
						array("ID" => "ASC"),
						array(
								"FUSER_ID" => CSaleBasket::GetBasketUserID(),
								"LID" => SITE_ID,
								"ORDER_ID" => "NULL",
								"ID" => $id,
							),
						false,
						false,
						array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "CURRENCY")
				);
				if($arBasket = $dbBasketItems->Fetch())
				{
					if($_REQUEST["action"] == "delete" && in_array("DELETE", $arParams["COLUMNS_LIST"]))
					{
						CSaleBasket::Delete($arBasket["ID"]);
					}
					elseif($_REQUEST["action"] == "shelve" && in_array("DELAY", $arParams["COLUMNS_LIST"]))
					{
						if ($arBasket["DELAY"] == "N" && $arBasket["CAN_BUY"] == "Y")
							CSaleBasket::Update($arBasket["ID"], Array("DELAY" => "Y"));
					}
					elseif($_REQUEST["action"] == "add" && in_array("DELAY", $arParams["COLUMNS_LIST"]))
					{
						if ($arBasket["DELAY"] == "Y" && $arBasket["CAN_BUY"] == "Y")
							CSaleBasket::Update($arBasket["ID"], Array("DELAY" => "N"));
					}
				}
			}
		}
	}

	/*
	* coupons
	*/
	$COUPON = "";
	if ($arParams["HIDE_COUPON"] != "Y" AND isset($_REQUEST["COUPON"]))
	{
		$COUPON = trim($_REQUEST["COUPON"]);
		$arCupon = array();
		$cupons = explode(",", $COUPON);
		foreach($cupons as $val)
		{
			if (strlen(trim($val)) > 0)
				$arCupon[] = trim($val);
		}
	}

	/*
	 * register user if to order basket
	 */
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["BasketOrder"]) AND !$USER->IsAuthorized())
	{
		if (strlen($payerEMail) <= 0)
			$errorMessage .= GetMessage("STOF_ERROR_REG_EMAIL")."<br>";
		elseif (!check_email($payerEMail))
			$errorMessage .= GetMessage("STOF_ERROR_REG_BAD_EMAIL")."<br>";

		$pos = strpos($payerEMail, "@");
		$payerEMailNew = substr($payerEMail, 0, $pos);
		$dbUserLogin = CUser::GetByLogin($payerEMailNew);
		if ($arUserLogin = $dbUserLogin->Fetch())
			$errorMessage .= GetMessage("STOF_ERROR_REG_UNIQUE_LOGIN")."<br>";

		$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL" => $payerEMail));
		$arUser = $rsUsers->Fetch();
		if (count($arUser) > 1)
			$errorMessage .= GetMessage("STOF_ERROR_REG_UNIQUE_EMAIL")."<br>";

		if ('' == $errorMessage)
		{
			$user_id = CSaleUser::DoAutoRegisterUser($payerEMail, $payerName, SITE_ID, $arErrors, array());
			if ($user_id > 0 && empty($arErrors))
			{
				$USER->Authorize($user_id);

				//send mail register user
				if ($arParams["SEND_NEW_USER_NOTIFY"] == "Y")
				{
					$userNew = str_replace("#FIO#", "(".$arUser["LOGIN"].") ".$payerName, GetMessage("NEWO_BUYER_REG_OK"));
					CUser::SendUserInfo($user_id, SITE_ID, $userNew, true);
				}
			}
			else
			{
				foreach ($arErrors as $val)
					$errorMessage .= $val["TEXT"]."<br>";
			}
		}
	}

	/*
	* calc basket
	*/
	$arErrors = array();
	$arWarnings = array();
	$arShoppingCart = CSaleBasket::DoGetUserShoppingCart(SITE_ID, intval($USER->GetID()), intval(CSaleBasket::GetBasketUserID()), $arErrors, $arCupon);
	$productLimit = "";

	if (strlen($_REQUEST["BasketRefresh"]) > 0 || strlen($_REQUEST["BasketOrder"]) > 0 || strlen($_REQUEST["AJAX_CALL"]) > 0)
	{
		if (in_array("QUANTITY", $arParams["COLUMNS_LIST"]))
		{
			$arSelect = array(
				"ID",
				"QUANTITY",
				"QUANTITY_TRACE",
				"CAN_BUY_ZERO"
			);

			$arProductIDs = array();
			$arNewQuantity = array();
			foreach($arShoppingCart as $key => $val)
			{
				if (array_key_exists("QUANTITY_".$val["ID"], $_POST))
				{
					$_POST["QUANTITY_".$val["ID"]] = str_replace(",", ".", $_POST["QUANTITY_".$val["ID"]]);
					$dblQuantity = $arParams['QUANTITY_FLOAT'] == 'Y' ? DoubleVal($_POST["QUANTITY_".$val["ID"]]) : intval($_POST["QUANTITY_".$val["ID"]]);
					if ($dblQuantity != $val['QUANTITY'])
					{
						if ('catalog' == $val['MODULE'])
						{
							$arProductIDs[$val["PRODUCT_ID"]] = $key;
							$arNewQuantity[$val["PRODUCT_ID"]] = $dblQuantity;
						}
						else
						{
							$arFields = array(
								"QUANTITY" => $dblQuantity
							);
							CSaleBasket::Update($val["ID"], $arFields);
						}
					}
				}
			}

			if (!empty($arProductIDs) && CModule::IncludeModule('catalog'))
			{
				$rsProducts = CCatalogProduct::GetList(
					array(),
					array('ID' => array_keys($arProductIDs)),
					false,
					false,
					$arSelect
				);
				while ($arProduct = $rsProducts->Fetch())
				{
					if (array_key_exists($arProduct['ID'], $arProductIDs))
					{
						$key = $arProductIDs[$arProduct['ID']];
						if ($arNewQuantity[$arProduct['ID']] > $arProduct['QUANTITY'] && 'Y' == $arProduct['QUANTITY_TRACE'] && 'N' == $arProduct['CAN_BUY_ZERO'])
						{
							$arNewQuantity[$arProduct['ID']] = $arProduct['QUANTITY'];
							$productLimit .= GetMessage("STOF_WARNING_LIMIT_PRODUCT")." ".$arShoppingCart[$key]["NAME"]."<br>";
						}

						if ($arNewQuantity[$arProduct['ID']] != $arShoppingCart[$key]['QUANTITY'])
						{
							$arShoppingCart[$key]['QUANTITY'] = $arNewQuantity[$arProduct['ID']];
							$arFields = array(
								"QUANTITY" => $arNewQuantity[$arProduct['ID']]
							);
							CSaleBasket::Update($arShoppingCart[$key]["ID"], $arFields);
						}
					}
				}
			}
		}
	}

	$arBasketItems = CSaleOrder::DoCalculateOrder(
		SITE_ID,
		$USER->GetID(),
		$arShoppingCart,
		$PERSON_TYPE,
		$arPropValues,
		$DELIVERY_ID,
		$PAYSYSTEM_ID,
		array(),
		$arErrors,
		$arWarnings
	);

	if ((!empty($arErrors) || !empty($arWarnings)) && strlen($_REQUEST["AJAX_CALL"]) > 0 && !isset($_POST["BasketRefresh"]))
	{
		foreach($arErrors as $val)
			$errorMessage .= $val["TEXT"]."<br>";

		foreach($arWarnings as $val)
			$errorMessage .= $val["TEXT"]."<br>";

		$arErrors = array();
		$arWarnings = array();
		$DELIVERY_ID = "";
		$PAYSYSTEM_ID = "";
		$arBasketItems = CSaleOrder::DoCalculateOrder(
			SITE_ID,
			$USER->GetID(),
			$arShoppingCart,
			$PERSON_TYPE,
			$arPropValues,
			$DELIVERY_ID,
			$PAYSYSTEM_ID,
			array(),
			$arErrors,
			$arWarnings
		);
	}

	/*********************************************************/
	/*********************** SAVE ****************************/
	/*********************************************************/

	$ORDER_ID = "";
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["BasketOrder"]) AND strlen($errorMessage) <= 0 AND check_bitrix_sessid())
	{
		if (!empty($arWarnings))
		{
			foreach ($arWarnings as $val)
				$errorMessage .= $val["TEXT"]."<br>";
		}
		if (!empty($arErrors))
		{
			foreach ($arErrors as $val)
				$errorMessage .= $val["TEXT"]."<br>";
		}

		if ($PAYSYSTEM_ID == "")
			$errorMessage .= GetMessage("SBB_ERR_PAYSYSTEM")."<br>";

		if ('' == $errorMessage)
		{
			$arAdditionalFields = array(
				"LID" => SITE_ID,
				"STATUS_ID" => "N",
				"PAYED" => "N",
				"CANCELED" => "N",
				"USER_DESCRIPTION" => $ORDER_DESCRIPTION,
			);

			$affiliateID = CSaleAffiliate::GetAffiliate();
			if ($affiliateID > 0)
			{
				$dbAffiliat = CSaleAffiliate::GetList(array(), array("SITE_ID" => SITE_ID, "ID" => $affiliateID));
				$arAffiliates = $dbAffiliat->Fetch();
				if (count($arAffiliates) > 1)
					$arAdditionalFields["AFFILIATE_ID"] = $affiliateID;
			}
			else
				$arAdditionalFields["AFFILIATE_ID"] = false;

			foreach($arBasketItems["BASKET_ITEMS"] as $key => $val)
			{
				unset($arBasketItems["BASKET_ITEMS"][$key]["ID"]);
			}

			$ORDER_ID = CSaleOrder::DoSaveOrder($arBasketItems, $arAdditionalFields, 0, $arErrors, $arCupon);

			if ($ORDER_ID > 0 && empty($arErrors))
			{
				CSaleBasket::OrderBasket($ORDER_ID, CSaleBasket::GetBasketUserID(), SITE_ID, false);

				$dbBasketItems = CSaleBasket::GetList(
					array("ID" => "ASC"),
					array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => $ORDER_ID
					),
					false,
					false,
					array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "DISCOUNT_PRICE", "CURRENCY", "PRODUCT_PROVIDER_CLASS", "DIMENSIONS")
				);
				$arResult["ORDER_PRICE"] = 0;

				$arOrderForDiscount = array(
					'SITE_ID' => SITE_ID,
					'USER_ID' => $USER->GetID(),
					'ORDER_PRICE' => $arResult["ORDER_PRICE"],
					'ORDER_WEIGHT' => 0,
					'PRICE_DELIVERY' => $arResult["DELIVERY_PRICE"],
					'BASKET_ITEMS' => array(),
					"PERSON_TYPE_ID" => $arUserResult['PERSON_TYPE_ID'],
					"PAY_SYSTEM_ID" => $arUserResult["PAY_SYSTEM_ID"],
					"DELIVERY_ID" => $arUserResult["DELIVERY_ID"],
				);

				while ($arOneItem = $dbBasketItems->GetNext())
				{
					$arOrderForDiscount['BASKET_ITEMS'][] = $arOneItem;
					$arOrderForDiscount['ORDER_WEIGHT'] += doubleval($arOneItem['WEIGHT']);
				}

				$arDiscountOptions = array();

				$arDiscountErrors = array();

				CSaleDiscount::DoProcessOrder($arOrderForDiscount, $arDiscountOptions, $arDiscountErrors);

				$arResult["ORDER_PRICE"] = 0;

				foreach ($arOrderForDiscount['BASKET_ITEMS'] as &$arOneItem)
				{
					$arResult["ORDER_PRICE"] += doubleval($arOneItem['PRICE'])*doubleval($arOneItem['QUANTITY']);
					$arBasketInfo = array(
						'IGNORE_CALLBACK_FUNC' => 'Y',
						'PRICE' => $arOneItem['PRICE'],
					);
					if (array_key_exists('DISCOUNT_PRICE', $arOneItem))
					{
						$arBasketInfo['DISCOUNT_PRICE'] = $arOneItem['DISCOUNT_PRICE'];
					}
					CSaleBasket::Update(
						$arOneItem['ID'],
						$arBasketInfo
					);
				}
				if (isset($arOneItem))
					unset($arOneItem);
				$arBasketItems["BASKET_ITEMS"] = $arOrderForDiscount['BASKET_ITEMS'];
				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
				CSaleOrder::Update($arResult["ORDER_ID"], array("PRICE" => $totalOrderPrice));

				/*send mail order*/
				$strOrderList = "";
				foreach ($arBasketItems["BASKET_ITEMS"] as $val)
				{
					if (CSaleBasketHelper::isSetItem($val))
						continue;

					$strOrderList .= $val["NAME"]." - ".$val["QUANTITY"]." ".GetMessage("SOA_SHT").": ".SaleFormatCurrency($val["PRICE"], $arBasketItems["CURRENCY"]);
					$strOrderList .= "\n";
				}

				$arFields = array(
					"ORDER_ID" => $ORDER_ID,
					"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
					"ORDER_USER" => ( (strlen($payerName) > 0) ? $payerName : $USER->GetFullName() ),
					"PRICE" => SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]),
					"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"EMAIL" => (strlen($payerEMail)>0 ? $payerEMail : $USER->GetEmail()),
					"ORDER_LIST" => $strOrderList,
					"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
					"DELIVERY_PRICE" => $arBasketItems["PRICE_DELIVERY"],
				);
				$eventName = "SALE_NEW_ORDER";

				$bSend = true;
				foreach (GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
				{
					if (ExecuteModuleEventEx($arEvent, Array($ORDER_ID, &$eventName, &$arFields))===false)
						$bSend = false;
				}

				if($bSend)
				{
					$event = new CEvent;
					$event->Send($eventName, SITE_ID, $arFields, "N");
				}
				/*end mail*/

				CSaleMobileOrderPush::send("ORDER_CREATED", array("ORDER_ID" => $arFields["ORDER_ID"]));

				if(CModule::IncludeModule("statistic"))
				{
					$event1 = "eStore";
					$event2 = "order_confirm";
					$event3 = $arResult["ORDER_ID"];

					$e = $event1."/".$event2."/".$event3;

					if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"])))
					{
							CStatistic::Set_Event($event1, $event2, $event3);
							$_SESSION["ORDER_EVENTS"][] = $e;
					}
				}

				$urlError = "";
				if ($PAYSYSTEM_ID == "account")
				{
					if (!CSaleUserAccount::DoPayOrderFromAccount($USER->GetID(), $arBasketItems["CURRENCY"], $ORDER_ID, $arBasketItems["PRICE"], array(), $arErrors))
					{
						$urlError = "&erraccount=y";
					}
				}
				CSaleOrderUserProps::DoSaveUserProfile($USER->GetID(), $PROFILE_ID, $profileName, $PERSON_TYPE, $arPropValues, $arErrors);

				LocalRedirect($APPLICATION->GetCurPageParam("ORDER_ID=".$ORDER_ID.$urlError, Array("ORDER_ID", "action", "id")));
			}
			elseif (!empty($arErrors))
			{
				foreach($arErrors as $val)
					$errorMessage .= $val."<br>";
			}
		}
	}
	else
	{
		if (!isset($_POST["BasketOrder"]))
			$errorMessage = "";
	}

	/*********************************************************/
	/******************** take basket ************************/
	/*********************************************************/
	CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
	$dbDelayBasketItems = CSaleBasket::GetList(
			array("ID" => "ASC"),
			array(
					"FUSER_ID" => CSaleBasket::GetBasketUserID(),
					"LID" => SITE_ID,
					"ORDER_ID" => "NULL",
				),
			false,
			false,
			array("*")
	);
	$arBasketItems["BASKET_ITEMS"] = array();
	$arSetParentWeight = array();
	while ($arItems = $dbDelayBasketItems->GetNext())
	{
		/*if (strlen($arItems["CALLBACK_FUNC"]) > 0)
		{
			CSaleBasket::UpdatePrice($arItems["ID"], $arItems["CALLBACK_FUNC"], $arItems["MODULE"], $arItems["PRODUCT_ID"], $arItems["QUANTITY"]);
			$arItems = CSaleBasket::GetByID($arItems["ID"]);
		}*/

		$arItems['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($arItems['QUANTITY']), 2, '.', '') : intval($arItems['QUANTITY']);
		$arBasketItems["BASKET_ITEMS"][] = $arItems;

		if (CSaleBasketHelper::isSetItem($arItems))
			$arSetParentWeight[$arItems["SET_PARENT_ID"]] += $arItems["WEIGHT"] * $arItems['QUANTITY'];
	}

	// count weight for set parent products
	foreach ($arBasketItems["BASKET_ITEMS"] as &$arItems)
	{
		if (CSaleBasketHelper::isSetParent($arItems))
			$arItems["WEIGHT"] = $arSetParentWeight[$arItems["ID"]] / $arItems["QUANTITY"];
	}
	unset($arItems);

	if (!isset($arBasketItems["TAX_VALUE"]) || $arBasketItems["TAX_VALUE"] == "")
		$arBasketItems["TAX_VALUE"] = 0;

	if (is_array($arBasketItems))
	{
		foreach ($arBasketItems as $key => $val)
		{
			if (CSaleBasketHelper::isSetItem($val))
				continue;

			if ($key != "BASKET_ITEMS")
			{
				$arResult[$key] = $val;
				if ($key == "PRICE" OR $key == "VAT_SUM" OR $key == "DISCOUNT_PRICE" OR $key == "DISCOUNT_VALUE" OR $key == "PRICE_DELIVERY" OR $key == "TAX_VALUE" OR $key == "ORDER_PRICE")
					$arResult[$key."_FORMATED"] = SaleFormatCurrency($val, $arBasketItems["CURRENCY"]);
				if ($key == "QUANTITY")
					$arResult[$key."_FORMATED"] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($val), 2, '.', '') : intval($val);
				if ($key == "ORDER_WEIGHT")
					$arResult[$key."_FORMATED"] = DoubleVal($val/$arParams["WEIGHT_KOEF"])." ".$arParams["WEIGHT_UNIT"];

				if ($key == "DISCOUNT_PERCENT")
					$arResult["DISCOUNT_PERCENT_FORMATED"] = DoubleVal($val)."%";
			}
		}
	}//end if is_array

	$arResult["ITEMS"]["AnDelCanBuy"] = Array();
	$arResult["ITEMS"]["DelDelCanBuy"] = Array();
	$arResult["ITEMS"]["nAnCanBuy"] = Array();
	$arResult["ITEMS"]["AnSubscribe"] = Array();
	$DISCOUNT_PRICE_ALL = 0;

	$boolIBlock = CModule::IncludeModule('iblock');

	if (is_array($arBasketItems["BASKET_ITEMS"]))
	{
		foreach ($arBasketItems["BASKET_ITEMS"] as $key => $val)
		{
			if (CSaleBasketHelper::isSetItem($val))
				continue;

			$val['QUANTITY'] = $arParams['QUANTITY_FLOAT'] == 'Y' ? number_format(DoubleVal($val['QUANTITY']), 2, '.', '') : intval($val['QUANTITY']);
			$val["VAT_VALUE_FORMATED"] = SaleFormatCurrency($val["VAT_VALUE"], $val["CURRENCY"]);
			$val["PRICE_FORMATED"] = SaleFormatCurrency($val["PRICE"], $val["CURRENCY"]);
			$val["WEIGHT"] = DoubleVal($val["WEIGHT"]);
			$val["WEIGHT_FORMATED"] = DoubleVal($val["WEIGHT"]/$arParams["WEIGHT_KOEF"])." ".$arParams["WEIGHT_UNIT"];
			$val["DISCOUNT_PRICE_PERCENT"] = $val["DISCOUNT_PRICE"]*100 / ($val["DISCOUNT_PRICE"] + $val["PRICE"]);
			$val["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($val["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";

			$val["DETAIL_PAGE_URL"] = '';
			$val["CREATED_BY"] = 0;
			$val["IBLOCK_ID"] = 0;
			$val["IBLOCK_SECTION_ID"] = 0;
			$val["PREVIEW_PICTURE"] = 0;
			$val["DETAIL_PICTURE"] = 0;
			$val["PREVIEW_TEXT"] = '';
			$val["DETAIL_TEXT"] = '';

			if ($boolIBlock)
			{
				$arIBlockElement = GetIBlockElement($val["PRODUCT_ID"]);
				$val["DETAIL_PAGE_URL"] = $arIBlockElement["DETAIL_PAGE_URL"];
				$val["CREATED_BY"] = $arIBlockElement["CREATED_BY"];
				$val["IBLOCK_ID"] = $arIBlockElement["IBLOCK_ID"];
				$val["IBLOCK_SECTION_ID"] = $arIBlockElement["IBLOCK_SECTION_ID"];
				$val["PREVIEW_PICTURE"] = $arIBlockElement["PREVIEW_PICTURE"];
				$val["DETAIL_PICTURE"] = $arIBlockElement["DETAIL_PICTURE"];
				$val["PREVIEW_TEXT"] = $arIBlockElement["PREVIEW_TEXT"];
				$val["DETAIL_TEXT"] = $arIBlockElement["DETAIL_TEXT"];
			}

			$val["PROPS"] = Array();
			if(in_array("PROPS", $arParams["COLUMNS_LIST"]) && $val["ID"] > 0)
			{
				$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $val["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
				while($arProp = $dbProp -> GetNext())
					$val["PROPS"][] = $arProp;
			}

			if ($val["DELAY"] == "N" && $val["CAN_BUY"] == "Y")
			{
				$arResult["ITEMS"]["AnDelCanBuy"][] = $val;
				$DISCOUNT_PRICE_ALL += $val["DISCOUNT_PRICE"] * $val["QUANTITY"];
			}

			if ($val["DELAY"] == "Y" && $val["CAN_BUY"] == "Y")
				$arResult["ITEMS"]["DelDelCanBuy"][] = $val;
			if ($val["CAN_BUY"] == "N" && $val["SUBSCRIBE"] == "N")
				$arResult["ITEMS"]["nAnCanBuy"][] = $val;
			if ($val["CAN_BUY"] == "N" && $val["SUBSCRIBE"] == "Y")
				$arResult["ITEMS"]["AnSubscribe"][] = $val;
		}
	}//end if is_array

	if (is_array($arBasketItems["BASKET_ITEMS"]))
	{
		$DISCOUNT_PRICE_ALL += $arBasketItems["DISCOUNT_PRICE"];
		$arResult["DISCOUNT_PRICE_ALL"] = $DISCOUNT_PRICE_ALL;
		$arResult["DISCOUNT_PRICE_ALL_FORMATED"] = SaleFormatCurrency($DISCOUNT_PRICE_ALL, $arResult["CURRENCY"]);
		$arResult["PERSON_TYPE"] = $arResultProps["PERSON_TYPE"];
		$arResult["USER_PROFILES"] = $arResultProps["USER_PROFILES"];
		$arResult["ORDER_PROPS"]["USER_PROPS_Y"] = $arResultProps["ORDER_PROPS"]["USER_PROPS_Y"];
		$arResult["ORDER_PROPS"]["USER_PROPS_N"] = $arResultProps["ORDER_PROPS"]["USER_PROPS_N"];

		/*
		* delivery
		*/
		$location = $_POST["ORDER_PROP_".$locationID];
		$locationZip = $_POST["ORDER_PROP_".$locationZipID];
		$arDelivery = CSaleDelivery::DoLoadDelivery($location, $locationZip, $arResult["ORDER_WEIGHT"], $arResult["PRICE"], $arResult["CURRENCY"], SITE_ID);

		$arDeliveryResult = array();
		$deliveryCheckDesc = "";
		$arDeliveryResult[] = array("CHECKED" => "Y", "ID" => "", "TITLE" => GetMessage("SBB_SELECT_DELIVERY"));
		if (!empty($arDelivery))
		{
			foreach($arDelivery as $val)
			{
				$arFields = array();
				if (isset($val["PROFILES"]))
				{
					foreach($val["PROFILES"] as $k => $v)
					{
						$arFields = array();
						if ($v["ID"] == $DELIVERY_ID)
						{
							$arFields["CHECKED"] = "Y";
							$arDeliveryResult[0]["CHECKED"] = "N";
							$arFields["DELIVERY_PRICE"] = $arResult["DELIVERY_PRICE"];
							$deliveryCheckDesc = $val["DESCRIPTION"];
						}
						$arFields["ID"] = $v["ID"];
						$arFields["TITLE"] = $val["TITLE"]." (".$v["TITLE"].")";
						$arFields["DESCRIPTION"] = $val["DESCRIPTION"];
						$arDeliveryResult[] = $arFields;
					}
				}
				else
				{
					if ($val["ID"] == $_POST["DELIVERY_ID"])
					{
						$arFields["CHECKED"] = "Y";
						$arDeliveryResult[0]["CHECKED"] = "N";
						$arFields["DELIVERY_PRICE"] = $arResult["DELIVERY_PRICE"];
						$deliveryCheckDesc = $val["DESCRIPTION"];

						if (isset($val["PERIOD_TEXT"]) AND $val["PERIOD_TEXT"] != "")
						{
							if ($val["DESCRIPTION"] != "")
								$deliveryCheckDesc .= "<br>";
							$deliveryCheckDesc .= $val["PERIOD_TEXT"];
						}
					}
					$arFields["ID"] = $val["ID"];
					$arFields["TITLE"] = $val["NAME"];
					$arFields["DESCRIPTION"] = $val["DESCRIPTION"];
					$arDeliveryResult[] = $arFields;
				}
			}
		}
		$arResult["DELIVERY"] = $arDeliveryResult;
		$arResult["DELIVERY_CHECHED_DESC"] = $deliveryCheckDesc;

		/*
		* paysystem
		*/
		$userAccount = "";
		$dbUserAccount = CSaleUserAccount::GetList(
			array(),
			array(
				"USER_ID" => intval($USER->GetID()),
				"CURRENCY" => $arResult["CURRENCY"],
				"LOCKED" => "N"
			)
		);
		if ($arUserAccount = $dbUserAccount->GetNext())
		{
			if (DoubleVal($arUserAccount["CURRENT_BUDGET"]) > 0)
				$userAccount = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["CURRENCY"]);
		}
		$arPaySystem = CSalePaySystem::DoLoadPaySystems($PERSON_TYPE);
		$arNewPaySystem = array();
		$paysystemDesc = "";
		$arNewPaySystem[0] = array("ID" => "", "NAME" => GetMessage("SBB_SELECT_PAYSYSTEM"), "CHECKED" => "Y");
		foreach($arPaySystem as $key => $val)
		{
			$arNewPaySystem[$key]["ID"] = $val["ID"];
			$arNewPaySystem[$key]["NAME"] = $val["NAME"];
			$arNewPaySystem[$key]["DESCRIPTION"] = $val["DESCRIPTION"];
			$arNewPaySystem[$key]["ACTIVE"] = $val["ACTIVE"];
			if ($_POST["PAYSYSTEM_ID"] == $key)
			{
				$arNewPaySystem[$key]["CHECKED"] = "Y";
				$arNewPaySystem[0]["CHECKED"] = "N";
				$paysystemDesc = $val["DESCRIPTION"];
			}
		}
		//add pay from account
		if ('' != $userAccount)
		{
			$arUserAccount = array();
			$arUserAccount["ID"] = "account";
			$arUserAccount["NAME"] = GetMessage("SBB_PAY_USER_ACCOUNT");
			$arUserAccount["DESCRIPTION"] = GetMessage("SBB_USER_ACCOUNT").$userAccount;
			if ($PAYSYSTEM_ID == "account")
			{
				$arUserAccount["CHECKED"] = "Y";
				$paysystemDesc = GetMessage("SBB_USER_ACCOUNT")."<b>".$userAccount."</b>";
			}
			$arNewPaySystem[] = $arUserAccount;
		}
		$arResult["PAYSYSTEM"] = $arNewPaySystem;
		$arResult["PAYSYSTEM_CHECKED_DESC"] = $paysystemDesc;

		/*
		* show order props
		*/
		if (isset($display_props) AND $display_props == "block")
			$arParams['SHOW_BASKET_ORDER'] = "Y";

		if (isset($display_props) AND $display_props == "none")
			$arParams['SHOW_BASKET_ORDER'] = "N";

		$arResult["ORDER_DESCRIPTION"] = $ORDER_DESCRIPTION;

		if (strlen($COUPON) > 0)
			$arResult["COUPON"] = htmlspecialcharsEx($COUPON);

		$arOrderForDiscount = array(
			'SITE_ID' => SITE_ID,
			'USER_ID' => $USER->GetID(),
			'ORDER_PRICE' => $arResult['ORDER_PRICE'],
			'ORDER_WEIGHT' => $arResult["ORDER_WEIGHT"],
			'PRICE_DELIVERY' => $arResult["DELIVERY_PRICE"],
			'BASKET_ITEMS' => $arResult["ITEMS"]["AnDelCanBuy"],
			"PERSON_TYPE_ID" => $arUserResult['PERSON_TYPE_ID'],
			"PAY_SYSTEM_ID" => $arUserResult["PAY_SYSTEM_ID"],
			"DELIVERY_ID" => $arUserResult["DELIVERY_ID"],
		);

		$arDiscountOptions = array();

		$arDiscountErrors = array();

		CSaleDiscount::DoProcessOrder($arOrderForDiscount, $arDiscountOptions, $arDiscountErrors);

		$allSum = 0;
		$allVatSumm = 0;
		$allVatRate = 0;
		foreach ($arOrderForDiscount['BASKET_ITEMS'] as &$arOneItem)
		{
			$arOneItem["PRICE_FORMATED"] = SaleFormatCurrency($arOneItem["PRICE"], $arOneItem["CURRENCY"]);
			$arOneItem["DISCOUNT_PRICE_PERCENT"] = $arOneItem["DISCOUNT_PRICE"]*100 / ($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]);
			$arOneItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arOneItem["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
			$allSum += ($arOneItem["PRICE"] * $arOneItem["QUANTITY"]);
			$allVatSumm += 0;
			if (0 < $arOneItem["VAT_RATE"])
			{
				$arResult["bUsingVat"] = "Y";
				if ($arOneItem["VAT_RATE"] > $allVatRate)
					$allVatRate = $arOneItem["VAT_RATE"];
				$arOneItem["VAT_VALUE"] = (($arOneItem["PRICE"] / ($arOneItem["VAT_RATE"] +1)) * $arOneItem["VAT_RATE"]);
				$allVatSumm += roundEx($arOneItem["VAT_VALUE"] * $arOneItem["QUANTITY"], SALE_VALUE_PRECISION);
			}
		}
		if (isset($arOneItem))
			unset($arOneItem);

		$arResult["ORDER_PRICE"] = $allSum;
		$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

		$arResult["VAT_RATE"] = $allVatRate;
		$arResult["VAT_SUM"] = $allVatSumm;
		$arResult["VAT_SUM_FORMATED"] = SaleFormatCurrency($arResult["VAT_SUM"], $arResult["BASE_LANG_CURRENCY"]);

		$arResult['DELIVERY_PRICE'] = $arOrderForDiscount['PRICE_DELIVERY'];
		$arResult['DELIVERY_PRICE_FORMATED'] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);

		$arResult["ITEMS"]["AnDelCanBuy"] = $arOrderForDiscount['BASKET_ITEMS'];

	}//end if array basket

	if(empty($arBasketItems))
		$arResult["ERROR_MESSAGE"] = GetMessage("SALE_EMPTY_BASKET");

	if (!isset($_POST["BasketOrder"]))
		$errorMessage = "";

	$errorMessage .= $productLimit;

	$arResult["ERROR_MESSAGE"] = $errorMessage;
}//end of ORDER_ID <= 0
else
{
	$arResult["ORDER_BASKET"]["CONFIRM_ORDER"] = "Y";
	$arResult["ORDER_BASKET"]["ORDER_ID"] = intval($ORDER_ID);
	$arResult["ORDER_ID"] = intval($ORDER_ID);
	$dbOrder = CSaleOrder::GetList(
		array("DATE_UPDATE" => "DESC"),
		array(
				"LID" => SITE_ID,
				"USER_ID" => intval($USER->GetID()),
				"ID" => $arResult["ORDER_BASKET"]["ORDER_ID"]
		)
	);
	if ($arOrder = $dbOrder->GetNext())
	{
		if (intval($arOrder["PAY_SYSTEM_ID"]) > 0)
		{
			$dbPaySysAction = CSalePaySystemAction::GetList(
					array(),
					array(
							"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
							"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
						),
					false,
					false,
					array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING")
			);
			if ($arPaySysAction = $dbPaySysAction->Fetch())
			{
					$arPaySysAction["NAME"] = htmlspecialcharsEx($arPaySysAction["NAME"]);
					if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
					{
						if ($arPaySysAction["NEW_WINDOW"] != "Y")
						{
							CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], $arPaySysAction["PARAMS"]);

							$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

							$pathToAction = str_replace("\\", "/", $pathToAction);
							while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
								$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

							if (file_exists($pathToAction))
							{
								if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
									$pathToAction .= "/payment.php";

								$arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
							}

							if(strlen($arPaySysAction["ENCODING"]) > 0)
							{
								define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
								AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
								function ChangeEncoding($content)
								{
									global $APPLICATION;
									header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
									$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
									$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
								}
							}
						}
					}
					$arResult["PAY_SYSTEM"] = $arPaySysAction;
			}
		}
		$arResult["ORDER"] = $arOrder;
		if (isset($_GET["erraccount"]) AND $_GET["erraccount"] == "y")
		{
			$arResult["ERR_ACCOUNT"] = "Y";
		}
	}
}

$this->IncludeComponentTemplate();

if($_REQUEST["AJAX_CALL"] == "Y")
{
	die();
}

?>