<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
if (strlen($arParams["PATH_TO_BASKET"]) <= 0)
	$arParams["PATH_TO_BASKET"] = "/basket/";

$arParams["PATH_TO_PERSONAL"] = Trim($arParams["PATH_TO_PERSONAL"]);
if (strlen($arParams["PATH_TO_PERSONAL"]) <= 0)
	$arParams["PATH_TO_PERSONAL"] = "index.php";

$arParams["PATH_TO_PAYMENT"] = Trim($arParams["PATH_TO_PAYMENT"]);
if (strlen($arParams["PATH_TO_PAYMENT"]) <= 0)
	$arParams["PATH_TO_PAYMENT"] = "payment.php";

$arParams["PATH_TO_AUTH"] = Trim($arParams["PATH_TO_AUTH"]);
if (strlen($arParams["PATH_TO_AUTH"]) <= 0)
	$arParams["PATH_TO_AUTH"] = "/auth.php";
	
$arParams["ALLOW_PAY_FROM_ACCOUNT"] = (($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "N") ? "N" : "Y");
$arParams["COUNT_DELIVERY_TAX"] = (($arParams["COUNT_DELIVERY_TAX"] == "Y") ? "Y" : "N");
$arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] = (($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N");
$arParams["PATH_TO_ORDER"] = $APPLICATION->GetCurPage();
$arParams["SHOW_MENU"] = ($arParams["SHOW_MENU"] == "N" ? "N" : "Y" );
$arParams["ALLOW_EMPTY_CITY"] = ($arParams["CITY_OUT_LOCATION"] == "N" ? "N" : "Y" );

$arParams["SHOW_AJAX_LOCATIONS"] = $arParams["SHOW_AJAX_LOCATIONS"] == 'N' ? 'N' : 'Y';

$arParams['PRICE_VAT_SHOW_VALUE'] = $arParams['PRICE_VAT_SHOW_VALUE'] == 'N' ? 'N' : 'Y';

$arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] = (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N");
$arParams["SEND_NEW_USER_NOTIFY"] = (($arParams["SEND_NEW_USER_NOTIFY"] == "N") ? "N" : "Y");
$arResult["AUTH"]["new_user_registration_email_confirmation"] = ((COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y") ? "Y" : "N");
$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");
 
    
if($arParams["SET_TITLE"] == "Y")
{
	if($USER->IsAuthorized())
		$APPLICATION->SetTitle(GetMessage("STOF_MAKING_ORDER"));
	else
		$APPLICATION->SetTitle(GetMessage("STOF_AUTH"));
}

if(strlen($arResult["POST"]["ORDER_PRICE"])>0)
	$arResult["ORDER_PRICE"]  = doubleval($arResult["POST"]["ORDER_PRICE"]);
if(strlen($arResult["POST"]["ORDER_WEIGHT"])>0)
	$arResult["ORDER_WEIGHT"] = doubleval($arResult["POST"]["ORDER_WEIGHT"]);


if($_SESSION['PERSON_TYPE']){ 
     $_REQUEST["PERSON_TYPE"] = $_POST["PERSON_TYPE"] = $_SESSION['PERSON_TYPE'];
     $_REQUEST['CurrentStep'] = $_POST['CurrentStep'] = 2; 
     $_REQUEST['SKIP_FIRST_STEP'] = $_POST['SKIP_FIRST_STEP'] = 'Y'; 
     $_SESSION['PERSON_TYPE'] = false; 
}


foreach($_POST as $k => $v)
{
	if(!is_array($v))
	{
		$arResult["POST"][$k] = htmlspecialcharsex($v);
		$arResult["POST"]['~'.$k] = $v;
	}
	else
	{
		foreach($v as $kk => $vv)
		{
			$arResult["POST"][$k][$kk] = htmlspecialcharsex($vv);
			$arResult["POST"]['~'.$k][$kk] = $vv;
		}
	}
}

$arResult["SKIP_FIRST_STEP"] = (($arResult["POST"]["SKIP_FIRST_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_SECOND_STEP"] = (($arResult["POST"]["SKIP_SECOND_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_THIRD_STEP"] = (($arResult["POST"]["SKIP_THIRD_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_FORTH_STEP"] = (($arResult["POST"]["SKIP_FORTH_STEP"] == "Y") ? "Y" : "N");

if(strlen($arResult["POST"]["PERSON_TYPE"])>0)
	$arResult["PERSON_TYPE"] = IntVal($arResult["POST"]["PERSON_TYPE"]);
if(strlen($arResult["POST"]["PROFILE_ID"])>0)
	$arResult["PROFILE_ID"] = IntVal($arResult["POST"]["PROFILE_ID"]);
if(strlen($arResult["POST"]["DELIVERY_ID"])>0)
{
	if (strpos($arResult["POST"]["DELIVERY_ID"], ":") === false)
		$arResult["DELIVERY_ID"] = IntVal($arResult["POST"]["DELIVERY_ID"]);
	else
		$arResult["DELIVERY_ID"] = explode(":", $arResult["POST"]["DELIVERY_ID"]);
}
if(strlen($arResult["POST"]["PAY_SYSTEM_ID"])>0)
	$arResult["PAY_SYSTEM_ID"] = IntVal($arResult["POST"]["PAY_SYSTEM_ID"]);
if(strlen($arResult["POST"]["PAY_CURRENT_ACCOUNT"])>0)
	$arResult["PAY_CURRENT_ACCOUNT"] = $arResult["POST"]["PAY_CURRENT_ACCOUNT"];
if(strlen($arResult["POST"]["TAX_EXEMPT"])>0)
	$arResult["TAX_EXEMPT"] = $arResult["POST"]["TAX_EXEMPT"];
if(strlen($arResult["POST"]["ORDER_DESCRIPTION"])>0)
	$arResult["ORDER_DESCRIPTION"] = trim($arResult["POST"]["ORDER_DESCRIPTION"]);

if(IntVal($_REQUEST["ORDER_ID"])>0)
	$arResult["ORDER_ID"] = IntVal($_REQUEST["ORDER_ID"]);
if(IntVal($_REQUEST["CurrentStep"])>0)
	$arResult["CurrentStep"] = IntVal($_REQUEST["CurrentStep"]);
	
if(IntVal($_REQUEST["CurrentStep"])>0)
	$CurrentStepTmp = IntVal($_REQUEST["CurrentStep"]);
elseif(IntVal($arResult["POST"]["CurrentStep"])>0)
	$CurrentStepTmp = IntVal($arResult["POST"]["CurrentStep"]);
	
$arResult["BACK"] = (($arResult["POST"]["BACK"] == "Y") ? "Y" : "");
if ($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_REQUEST["backButton"]) > 0)
{
	if($arResult["POST"]["CurrentStep"] == 6 && $arResult["SKIP_FORTH_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 3;

	if($arResult["POST"]["CurrentStepTmp"] <= 5 && $arResult["SKIP_THIRD_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 2;
		
	if($arResult["POST"]["CurrentStepTmp"] <= 3 && $arResult["SKIP_SECOND_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 1;	
	
	if(IntVal($arResult["CurrentStepTmp"])>0)
		$arResult["CurrentStep"] = $arResult["CurrentStepTmp"];
	else
		$arResult["CurrentStep"] = $arResult["CurrentStep"] - 2;
	$arResult["BACK"] = "Y";
       
}
 
 if($arResult["CurrentStep"] == 2
         && $_REQUEST["CurrentStep"] == 3
         && isset($_REQUEST["backButton"]))
 $arResult["CurrentStep"] = 1;
 
  
if ($arResult["CurrentStep"] <= 0)
	$arResult["CurrentStep"] = 1;
$arResult["ERROR_MESSAGE"] = "";

/*******************************************************************************/
/*****************  ACTION  ****************************************************/
/*******************************************************************************/
if (!$USER->IsAuthorized())
{
	$arResult["USER_LOGIN"] = ((strlen($arResult["POST"]["USER_LOGIN"]) > 0) ? $arResult["POST"]["USER_LOGIN"] : htmlspecialchars(${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"}));
	$arResult["AUTH"]["captcha_registration"] = ((COption::GetOptionString("main", "captcha_registration", "N") == "Y") ? "Y" : "N");
	if($arResult["AUTH"]["captcha_registration"] == "Y")
		$arResult["AUTH"]["capCode"] = htmlspecialchars($APPLICATION->CaptchaGetCode());
	$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");
	
	if ($arResult["POST"]["do_authorize"] == "Y")
	{
		if (strlen($arResult["POST"]["USER_LOGIN"]) <= 0)
			$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_AUTH_LOGIN").".<br />";

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
		{
			$arAuthResult = $USER->Login($arResult["POST"]["~USER_LOGIN"], $arResult["POST"]["~USER_PASSWORD"], "N");
			if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_AUTH").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : ".<br />" );
			else
				LocalRedirect($arParams["PATH_TO_ORDER"]);
			
		}
	}
	elseif ($arResult["POST"]["do_register"] == "Y" && $arResult["AUTH"]["new_user_registration"] == "Y")
	{
             
            
            
             $_SESSION['PERSON_TYPE'] = $_REQUEST['PERSON_TYPE'];
              
               
                   $arResult["POST"]["NEW_NAME"] = $arResult["POST"]["~NEW_NAME"] = $arResult["POST"]["~NEW_EMAIL"];
                
		if (strlen($arResult["POST"]["NEW_NAME"]) <= 0)
			$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_NAME").".<br />";
                
         
		if (strlen($arResult["POST"]["NEW_LAST_NAME"]) <= 0)
			$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_LASTNAME").".<br />";

		if (strlen($arResult["POST"]["NEW_EMAIL"]) <= 0)
			$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_EMAIL").".<br />";
		elseif (!check_email($arResult["POST"]["NEW_EMAIL"]))
			$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_BAD_EMAIL").".<br />";
  
                
                
                		$arResult["POST"]["~NEW_LOGIN"] = $arResult["POST"]["~NEW_EMAIL"];

			$pos = strpos($arResult["POST"]["~NEW_LOGIN"], "@");
			if ($pos !== false)
				$arResult["POST"]["~NEW_LOGIN"] = substr($arResult["POST"]["~NEW_LOGIN"], 0, $pos);

			if (strlen($arResult["POST"]["~NEW_LOGIN"]) > 47)
				$arResult["POST"]["~NEW_LOGIN"] = substr($arResult["POST"]["`NEW_LOGIN"], 0, 47);
				
			if (strlen($arResult["POST"]["~NEW_LOGIN"]) < 3)
				$arResult["POST"]["~NEW_LOGIN"] .= "_";
				
			if (strlen($arResult["POST"]["~NEW_LOGIN"]) < 3)
				$arResult["POST"]["~NEW_LOGIN"] .= "_";

			$dbUserLogin = CUser::GetByLogin($arResult["POST"]["~NEW_LOGIN"]);
			if ($arUserLogin = $dbUserLogin->Fetch())
			{
				$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
				$uind = 0;
				do
				{
					$uind++;
					if ($uind == 10)
					{
						$arResult["POST"]["~NEW_LOGIN"] = $arResult["POST"]["~NEW_EMAIL"];
						$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
					}
					elseif ($uind > 10)
					{
						$arResult["POST"]["~NEW_LOGIN"] = "buyer".time().GetRandomCode(2);
						$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
						break;
					}
					else
					{
						$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"].$uind;
					}
					$dbUserLogin = CUser::GetByLogin($newLoginTmp);
				}
				while ($arUserLogin = $dbUserLogin->Fetch());
				$arResult["POST"]["~NEW_LOGIN"] = $newLoginTmp;
			}
                        
                        $arResult["POST"]["NEW_LOGIN"] = $arResult["POST"]["~NEW_LOGIN"];
                    
                        
                        if($arResult["POST"]["NEW_PASSWORD_CONFIRM"]==
                             $arResult["POST"]["NEW_PASSWORD"]
                                &&
                              !$arResult["POST"]["NEW_PASSWORD"]  
                                ) 
		{
			 

			$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
			if($def_group!="")
			{
				$GROUP_ID = explode(",", $def_group);
				$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
			}
			else
			{
				$arPolicy = $USER->GetGroupPolicy(array());
			}
			
			$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
			if($password_min_length <= 0)
				$password_min_length = 6;
			$password_chars = array(
				"abcdefghijklnmopqrstuvwxyz",
				"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
				"0123456789",
			);
			if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
				$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
			$arResult["POST"]["NEW_PASSWORD"] = $arResult["POST"]["NEW_PASSWORD_CONFIRM"] = $arResult["POST"]["~NEW_PASSWORD"] = $arResult["POST"]["~NEW_PASSWORD_CONFIRM"] = randString($password_min_length, $password_chars);
		}
		else
		{
			if (strlen($arResult["POST"]["NEW_LOGIN"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG").".<br />";

			if (strlen($arResult["POST"]["NEW_PASSWORD"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG1").".<br />";

			if (strlen($arResult["POST"]["NEW_PASSWORD"]) > 0 && strlen($arResult["POST"]["NEW_PASSWORD_CONFIRM"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG1").".<br />";

			if (strlen($arResult["POST"]["NEW_PASSWORD"]) > 0
				&& strlen($arResult["POST"]["NEW_PASSWORD_CONFIRM"]) > 0
				&& $arResult["POST"]["NEW_PASSWORD"] != $arResult["POST"]["NEW_PASSWORD_CONFIRM"])
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_PASS").".<br />";
		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
		{
                    
                        $_SESSION['PERSON_TYPE'] = $arResult["POST"]["PERSON_TYPE"];
                    
			$arAuthResult = $USER->Register($arResult["POST"]["~NEW_LOGIN"], $arResult["POST"]["~NEW_NAME"], $arResult["POST"]["~NEW_LAST_NAME"], $arResult["POST"]["~NEW_PASSWORD"], $arResult["POST"]["~NEW_PASSWORD_CONFIRM"], $arResult["POST"]["~NEW_EMAIL"], LANG, $arResult["POST"]["~captcha_word"], $arResult["POST"]["~captcha_sid"]);
			if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : ".<br />" );
			else
			{
				if ($USER->IsAuthorized())
				{
					if($arParams["SEND_NEW_USER_NOTIFY"] == "Y")
					CUser::SendUserInfo($USER->GetID(), SITE_ID, GetMessage("INFO_REQ"), true);
					LocalRedirect($arParams["PATH_TO_ORDER"]);
				}
				else
				{
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_CONFIRM")."<br />";
				}
			}
		}
	}
}
else
{
    

    
	$arResult["BASE_LANG_CURRENCY"] = CSaleLang::GetLangCurrency(SITE_ID);

	if ($arResult["CurrentStep"] > 0 && $arResult["CurrentStep"] <= 6)
	{
		if ($arResult["PAY_CURRENT_ACCOUNT"] != "N" && $arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
			$arResult["PAY_CURRENT_ACCOUNT"] = "Y";

		// <***************** BEFORE 1 STEP
		$arResult["ORDER_PRICE"] = 0;
		$arResult["ORDER_WEIGHT"] = 0;
		$bProductsInBasket = False;
		$arResult["bUsingVat"] = "N";
		$arResult["vatRate"] = 0;
		$arResult["vatSum"] = 0;
		$arProductsInBasket = array();

		$dbBasketItems = CSaleBasket::GetList(
				array("NAME" => "ASC"),
				array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL"
					),
				false,
				false,
				array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME")
			);
		while ($arBasketItems = $dbBasketItems->GetNext())
		{
			if (strlen($arBasketItems["CALLBACK_FUNC"])>0)
			{
				CSaleBasket::UpdatePrice($arBasketItems["ID"], $arBasketItems["CALLBACK_FUNC"], $arBasketItems["MODULE"], $arBasketItems["PRODUCT_ID"], $arBasketItems["QUANTITY"]);
				$arBasketItems = CSaleBasket::GetByID($arBasketItems["ID"]);
			}

			if ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
			{
				$arBasketItems["PRICE"] = roundEx($arBasketItems["PRICE"], SALE_VALUE_PRECISION);
				$arBasketItems["QUANTITY"] = DoubleVal($arBasketItems["QUANTITY"]);
				$arBasketItems["WEIGHT"] = DoubleVal($arBasketItems["WEIGHT"]);
				$arBasketItems["VAT_RATE"] = DoubleVal($arBasketItems["VAT_RATE"]);
				//$arBasketItems["DISCOUNT_PRICE"] = roundEx($arBasketItems["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
				
				$arResult["ORDER_PRICE"] += $arBasketItems["PRICE"] * $arBasketItems["QUANTITY"];
				$arResult["ORDER_WEIGHT"] += $arBasketItems["WEIGHT"] * $arBasketItems["QUANTITY"];
				if(DoubleVal($arBasketItems["VAT_RATE"]) > 0)
				{
				
					$arResult["bUsingVat"] = "Y";
					if($arBasketItems["VAT_RATE"] > $arResult["vatRate"])
						$arResult["vatRate"] = $arBasketItems["VAT_RATE"];

					$arBasketItems["VAT_VALUE"] = roundEx((($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]), SALE_VALUE_PRECISION);
					$arResult["vatSum"] += roundEx($arBasketItems["VAT_VALUE"] * $arBasketItems["QUANTITY"], SALE_VALUE_PRECISION);
				}
				$arBasketItems["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);
				$arBasketItems["WEIGHT_FROMATED"] = $arBasketItems["WEIGHT"]." ".$arResult["WEIGHT_UNIT"];	
				
				$arProductsInBasket[] = $arBasketItems;
				$bProductsInBasket = true;
			}
		}

		if (!$bProductsInBasket)
		{
			LocalRedirect($arParams["PATH_TO_BASKET"]);
			$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_BASKET_EMPTY");
		}

		// DISCOUNT
		for ($i = 0; $i < count($arProductsInBasket); $i++)
			$arProductsInBasket[$i]["DISCOUNT_PRICE"] = DoubleVal($arProductsInBasket[$i]["PRICE"]);
			
		$dbDiscount = CSaleDiscount::GetList(
				array("SORT" => "ASC"),
				array(
						"LID" => SITE_ID,
						"ACTIVE" => "Y",
						"!>ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
						"!<ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
						"<=PRICE_FROM" => $arResult["ORDER_PRICE"],
						">=PRICE_TO" => $arResult["ORDER_PRICE"]
					),
				false,
				false,
				array("*")
			);
		$arResult["DISCOUNT_PRICE"] = 0;
		$arResult["DISCOUNT_PERCENT"] = 0;
		$arDiscounts = array();
		
		if ($arDiscount = $dbDiscount->Fetch())
		{
			if ($arDiscount["DISCOUNT_TYPE"] == "P")
			{
				$arResult["DISCOUNT_PERCENT"] = $arDiscount["DISCOUNT_VALUE"];
				for ($bi = 0; $bi < count($arProductsInBasket); $bi++)
				{
					if($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
					{
						$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arProductsInBasket[$bi]["QUANTITY"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arDiscounts[IntVal($arProductsInBasket[$bi]["ID"])] = roundEx($curDiscount / $arProductsInBasket[$bi]["QUANTITY"], SALE_VALUE_PRECISION);
						$arResult["DISCOUNT_PRICE"] += $curDiscount;
					}
					else
					{
						$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);

						$arDiscounts[IntVal($arProductsInBasket[$bi]["ID"])] = $curDiscount;
						$arResult["DISCOUNT_PRICE"] += $curDiscount * $arProductsInBasket[$bi]["QUANTITY"];
					
					}
					$arProductsInBasket[$bi]["DISCOUNT_PRICE"] = $arProductsInBasket[$bi]["PRICE"] - $curDiscount;
				}
			}
			else
			{
				$arResult["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]);
				$arResult["DISCOUNT_PRICE"] = roundEx($arResult["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
				$arResult["DISCOUNT_PRICE_tmp"] = 0;
				for ($bi = 0; $bi < count($arProductsInBasket); $bi++)
				{
					$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arResult["DISCOUNT_PRICE"] / $arResult["ORDER_PRICE"], SALE_VALUE_PRECISION);
					$arDiscounts[IntVal($arProductsInBasket[$bi]["ID"])] = $curDiscount;
					$arProductsInBasket[$bi]["DISCOUNT_PRICE"] = $arProductsInBasket[$bi]["PRICE"] - $curDiscount;
					$arResult["DISCOUNT_PRICE_tmp"] += $curDiscount * $arProductsInBasket[$bi]["QUANTITY"];
				}
				$arResult["DISCOUNT_PRICE"] = $arResult["DISCOUNT_PRICE_tmp"];
			}
		}
		
		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 1)
		{
			// <***************** AFTER 1 STEP
			if ($arResult["PERSON_TYPE"] <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_NO_PERS_TYPE")."<br />";

			if (($arResult["PERSON_TYPE"] > 0) && !($arPersType = CSalePersonType::GetByID($arResult["PERSON_TYPE"])))
				$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_PERS_TYPE_NOT_FOUND")."<br />";

			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 1;
		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 2)
		{
			// <***************** AFTER 2 STEP
			if ($arResult["PROFILE_ID"] > 0 && $USER->IsAuthorized())
			{
				$dbUserProps = CSaleOrderUserPropsValue::GetList(
						array("SORT" => "ASC", "NAME" => "ASC"),
						array("USER_PROPS_ID" => $arResult["PROFILE_ID"]),
						false,
						false,
						array("ID", "ORDER_PROPS_ID", "VALUE", "SORT")
					);
				while ($arUserProps = $dbUserProps->GetNext())
				{
					$arResult["POST"]["ORDER_PROP_".$arUserProps["ORDER_PROPS_ID"]] = $arUserProps["VALUE"];
					$arResult["POST"]["~ORDER_PROP_".$arUserProps["ORDER_PROPS_ID"]] = $arUserProps["~VALUE"];
				}
			}

			$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]);
			if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
				$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

			$dbOrderProps = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					false,
					false,
					array("ID", "NAME", "TYPE", "IS_LOCATION", "IS_LOCATION4TAX", "IS_PROFILE_NAME", "IS_PAYER", "IS_EMAIL", "IS_ZIP", "REQUIED", "SORT")
				);
			while ($arOrderProps = $dbOrderProps->GetNext())
			{
                            
                            
                           // prent($arOrderProps);
                            
                            
				$bErrorField = False;
				$curVal = $arResult["POST"]["~ORDER_PROP_".$arOrderProps["ID"]];
				
				if ($arOrderProps["TYPE"]=="LOCATION" && ($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y"))
				{
					if ($arOrderProps["IS_LOCATION"]=="Y")
						$arResult["DELIVERY_LOCATION"] = IntVal($curVal);
					if ($arOrderProps["IS_LOCATION4TAX"]=="Y")
						$arResult["TAX_LOCATION"] = IntVal($curVal);

					if (IntVal($curVal)<=0) $bErrorField = True;
				}
				elseif ($arOrderProps["IS_ZIP"]=="Y")
				{
					$arResult["DELIVERY_LOCATION_ZIP"] = $curVal;
				}
				elseif ($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y" || $arOrderProps["IS_EMAIL"]=="Y")
				{
					if ($arOrderProps["IS_PROFILE_NAME"]=="Y")
					{
						$arResult["PROFILE_NAME"] = Trim($curVal);
						if (strlen($arResult["PROFILE_NAME"])<=0) 
							$bErrorField = True;
					}
					if ($arOrderProps["IS_PAYER"]=="Y")
					{
						$arResult["PAYER_NAME"] = Trim($curVal);
						if (strlen($arResult["PAYER_NAME"])<=0) 
							$bErrorField = True;
					}
					if ($arOrderProps["IS_EMAIL"]=="Y")
					{
						$arResult["USER_EMAIL"] = Trim($curVal);
						if (strlen($arResult["USER_EMAIL"])<=0 || !check_email($arResult["USER_EMAIL"])) 
							$bErrorField = True;
					}
				}
				elseif ($arOrderProps["REQUIED"]=="Y")
				{
					if ($arOrderProps["TYPE"]=="TEXT" || $arOrderProps["TYPE"]=="TEXTAREA" || $arOrderProps["TYPE"]=="RADIO" || $arOrderProps["TYPE"]=="SELECT" || $arOrderProps["TYPE"] == "CHECKBOX")
					{
						if (strlen($curVal)<=0) 
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="LOCATION")
					{
						if (IntVal($curVal)<=0) 
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="MULTISELECT")
					{
						if (!is_array($curVal) || count($curVal)<=0) 
							$bErrorField = True;
					}
				}
				if ($bErrorField)
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_EMPTY_FIELD")." \"".$arOrderProps["NAME"]."\".<br />";
			}

			//prent($arResult["POST"]);
			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 2;
		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 3)
		{
			// <***************** AFTER 3 STEP
			$arResult["TaxExempt"] = array();
			$arUserGroups = $USER->GetUserGroupArray();
			
			if($arResult["bUsingVat"] != "Y")
			{
				$dbTaxExemptList = CSaleTax::GetExemptList(array("GROUP_ID" => $arUserGroups));
				while ($TaxExemptList = $dbTaxExemptList->Fetch())
				{
					if (!in_array(IntVal($TaxExemptList["TAX_ID"]), $arResult["TaxExempt"]))
					{
						$arResult["TaxExempt"][] = IntVal($TaxExemptList["TAX_ID"]);
					}
				}
			}

			// DELIVERY
			
			$arResult["DELIVERY_PRICE"] = 0;
			
			if (is_array($arResult["DELIVERY_ID"]))
			{
				$arOrder = array(
					"PRICE" => $arResult["ORDER_PRICE"],
					"WEIGHT" => $arResult["ORDER_WEIGHT"],
					"LOCATION_FROM" => COption::GetOptionInt('sale', 'location'),
					"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],
				);

				$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($arResult["DELIVERY_ID"][0], $arResult["DELIVERY_ID"][1], $arOrder, $arResult["BASE_LANG_CURRENCY"]);
				
				if ($arDeliveryPrice["RESULT"] == "ERROR")
					$arResult["ERROR_MESSAGE"] = $arDeliveryPrice["TEXT"];
				else
					$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
			}
			else
			{
				if (($arResult["DELIVERY_ID"] > 0) && !($arDeliv = CSaleDelivery::GetByID($arResult["DELIVERY_ID"])))
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_DELIVERY_NOT_FOUND")."<br />";
				elseif (($arResult["DELIVERY_ID"] > 0) && $arDeliv)
					$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
			}
			
			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 3;
		}
		
		// TAX
		$arResult["TAX_EXEMPT"] = (($_REQUEST["TAX_EXEMPT"]=="Y") ? "Y" : "N");
		if ($arResult["TAX_EXEMPT"] == "N")
		{
			unset($arResult["TaxExempt"]);
			$arResult["TaxExempt"] = array();
		}

		
		$arResult["TAX_PRICE"] = 0;
		$arResult["arTaxList"] = array();
		if($arResult["bUsingVat"] != "Y")
		{
			$dbTaxRate = CSaleTaxRate::GetList(
					array("APPLY_ORDER"=>"ASC"),
					array(
							"LID" => SITE_ID,
							"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
							"ACTIVE" => "Y",
							"LOCATION" => IntVal($arResult["TAX_LOCATION"])
						)
				);
			while ($arTaxRate = $dbTaxRate->GetNext())
			{
				if (!in_array(IntVal($arTaxRate["TAX_ID"]), $arResult["TaxExempt"]))
				{
					$arResult["arTaxList"][] = $arTaxRate;
				}
			}

			$arTaxSums = array();
			if (count($arResult["arTaxList"]) > 0)
			{
				for ($i = 0; $i < count($arProductsInBasket); $i++)
				{
					$arResult["TAX_PRICE_tmp"] = CSaleOrderTax::CountTaxes(
							$arProductsInBasket[$i]["DISCOUNT_PRICE"] * $arProductsInBasket[$i]["QUANTITY"],
							$arResult["arTaxList"],
							$arResult["BASE_LANG_CURRENCY"]
						);

					for ($j = 0; $j < count($arResult["arTaxList"]); $j++)
					{
						$arResult["arTaxList"][$j]["VALUE_MONEY"] += $arResult["arTaxList"][$j]["TAX_VAL"];
					}
				}
				if(DoubleVal($arResult["DELIVERY_PRICE"])>0 && $arParams["COUNT_DELIVERY_TAX"] == "Y")
				{
					$arResult["TAX_PRICE_tmp"] = CSaleOrderTax::CountTaxes(
							$arResult["DELIVERY_PRICE"],
							$arResult["arTaxList"],
							$arResult["BASE_LANG_CURRENCY"]
						);

					for ($j = 0; $j < count($arResult["arTaxList"]); $j++)
					{
						$arResult["arTaxList"][$j]["VALUE_MONEY"] += $arResult["arTaxList"][$j]["TAX_VAL"];
					}
				}

				for ($i = 0; $i < count($arResult["arTaxList"]); $i++)
				{
					$arTaxSums[$arResult["arTaxList"][$i]["TAX_ID"]]["VALUE"] = $arResult["arTaxList"][$i]["VALUE_MONEY"];
					$arTaxSums[$arResult["arTaxList"][$i]["TAX_ID"]]["NAME"] = $arResult["arTaxList"][$i]["NAME"];
					if ($arResult["arTaxList"][$i]["IS_IN_PRICE"] != "Y")
					{
						$arResult["TAX_PRICE"] += $arResult["arTaxList"][$i]["VALUE_MONEY"];
					}
				}
			}
		}
		else
		{
			$arResult["arTaxList"][] = Array(
						"NAME" => GetMessage("STOF_VAT"),
						"IS_PERCENT" => "Y",
						"VALUE" => $arResult["vatRate"]*100,
						"VALUE_MONEY" => $arResult["vatSum"],
						"APPLY_ORDER" => 100,
						"IS_IN_PRICE" => "Y",
						"CODE" => "VAT"
			);
			
		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] >= 4)
		{
			// <***************** AFTER 4 STEP
			// PAY_SYSTEM
			if($arResult["CurrentStep"] > 4)
			{
				$arResult["PAY_SYSTEM_ID"] = IntVal($_REQUEST["PAY_SYSTEM_ID"]);
				if ($arResult["PAY_SYSTEM_ID"] <= 0)
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_NO_PAY_SYS")."<br />";
				if (($arResult["PAY_SYSTEM_ID"] > 0) && !($arPaySys = CSalePaySystem::GetByID($arResult["PAY_SYSTEM_ID"], $arResult["PERSON_TYPE"])))
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_PAY_SYS_NOT_FOUND")."<br />";
			}
			//if ($arResult["PAY_CURRENT_ACCOUNT"] != "Y")
				//$arResult["PAY_CURRENT_ACCOUNT"] = "N";
			
			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 4;
		}
		       ////////////////////////////  4444444 -------------5555555555
		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 4)
		{
			
			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 5;

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];

				$arFields = array(
						"LID" => SITE_ID,
						"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
						"PAYED" => "N",
						"CANCELED" => "N",
						"STATUS_ID" => "N",
						"PRICE" => $totalOrderPrice,
						"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
						"USER_ID" => IntVal($USER->GetID()),
						"PAY_SYSTEM_ID" => $arResult["PAY_SYSTEM_ID"],
						"PRICE_DELIVERY" => $arResult["DELIVERY_PRICE"],
						"DELIVERY_ID" => is_array($arResult["DELIVERY_ID"]) ? implode(":", $arResult["DELIVERY_ID"]) : ($arResult["DELIVERY_ID"] > 0 ? $arResult["DELIVERY_ID"] : false),
						"DISCOUNT_VALUE" => $arResult["DISCOUNT_PRICE"],
						"TAX_VALUE" => $arResult["bUsingVat"] == "Y" ? $arResult["vatSum"] : $arResult["TAX_PRICE"],
						"USER_DESCRIPTION" => $arResult["ORDER_DESCRIPTION"]
					);

				// add Guest ID
				if (CModule::IncludeModule("statistic"))
					$arFields["STAT_GID"] = CStatistic::GetEventParam();

				$affiliateID = CSaleAffiliate::GetAffiliate();
				if ($affiliateID > 0)
					$arFields["AFFILIATE_ID"] = $affiliateID;
				else
					$arFields["AFFILIATE_ID"] = false;

				$arResult["ORDER_ID"] = CSaleOrder::Add($arFields);
				$arResult["ORDER_ID"] = IntVal($arResult["ORDER_ID"]);

				if ($arResult["ORDER_ID"] <= 0)
				{
					if($ex = $APPLICATION->GetException())
						$arResult["ERROR_MESSAGE"] .= $ex->GetString();
					else
						$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_ERROR_ADD_ORDER")."<br />";
				}

			}

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				CSaleBasket::OrderBasket($arResult["ORDER_ID"], CSaleBasket::GetBasketUserID(), SITE_ID, $arDiscounts);
				
				$dbBasketItems = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array(
								"FUSER_ID" => CSaleBasket::GetBasketUserID(),
								"LID" => SITE_ID,
								"ORDER_ID" => $arResult["ORDER_ID"]
							),
						false,
						false,
						array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME")
					);
				$arResult["ORDER_PRICE"] = 0;
				while ($arBasketItems = $dbBasketItems->GetNext())
				{
					$arResult["ORDER_PRICE"] += DoubleVal($arBasketItems["PRICE"]) * DoubleVal($arBasketItems["QUANTITY"]);
				}
				
				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
				CSaleOrder::Update($arResult["ORDER_ID"], Array("PRICE" => $totalOrderPrice));
			}

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				//if($arResult["bUsingVat"] != "Y")
				//{
					for ($i = 0; $i < count($arResult["arTaxList"]); $i++)
					{
						$arFields = array(
								"ORDER_ID" => $arResult["ORDER_ID"],
								"TAX_NAME" => $arResult["arTaxList"][$i]["NAME"],
								"IS_PERCENT" => $arResult["arTaxList"][$i]["IS_PERCENT"],
								"VALUE" => ($arResult["arTaxList"][$i]["IS_PERCENT"]=="Y") ? $arResult["arTaxList"][$i]["VALUE"] : RoundEx(CCurrencyRates::ConvertCurrency($arResult["arTaxList"][$i]["VALUE"], $arResult["arTaxList"][$i]["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION),
								"VALUE_MONEY" => $arResult["arTaxList"][$i]["VALUE_MONEY"],
								"APPLY_ORDER" => $arResult["arTaxList"][$i]["APPLY_ORDER"],
								"IS_IN_PRICE" => $arResult["arTaxList"][$i]["IS_IN_PRICE"],
								"CODE" => $arResult["arTaxList"][$i]["CODE"]
							);
						CSaleOrderTax::Add($arFields);
					}
				//}
				/*
				elseif($arResult["vatRate"] > 0)
				{
					$arFields = array(
							"ORDER_ID" => $arResult["ORDER_ID"],
							"TAX_NAME" => GetMessage("STOF_VAT"),
							"IS_PERCENT" => "Y",
							"VALUE" => $arResult["vatRate"],
							"VALUE_MONEY" => $arResult["vatSum"],
							"APPLY_ORDER" => 100,
							"IS_IN_PRICE" => "Y",
							"CODE" => "VAT"
						);
					CSaleOrderTax::Add($arFields);
				
				}
				*/
				$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]);
				if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
					$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

				$dbOrderProperties = CSaleOrderProps::GetList(
						array("SORT" => "ASC"),
						$arFilter,
						false,
						false,
						array("ID", "TYPE", "NAME", "CODE", "USER_PROPS", "SORT")
					);
				while ($arOrderProperties = $dbOrderProperties->Fetch())
				{
					$curVal = $arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]];
					if ($arOrderProperties["TYPE"] == "MULTISELECT")
					{
						$curVal = "";
						for ($i = 0; $i < count($arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]]); $i++)
						{
							if ($i > 0)
								$curVal .= ",";
							$curVal .= $arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]][$i];
						}
					}

					if (strlen($curVal) > 0)
					{
						$arFields = array(
								"ORDER_ID" => $arResult["ORDER_ID"],
								"ORDER_PROPS_ID" => $arOrderProperties["ID"],
								"NAME" => $arOrderProperties["NAME"],
								"CODE" => $arOrderProperties["CODE"],
								"VALUE" => $curVal
							);
						CSaleOrderPropsValue::Add($arFields);
						if ( $arOrderProperties["USER_PROPS"] == "Y" && IntVal($arResult["PROFILE_ID"]) <= 0 && IntVal($arResult["USER_PROPS_ID"])<=0)
						{
							if (strlen($arResult["PROFILE_NAME"]) <= 0)
								$arResult["PROFILE_NAME"] = GetMessage("SALE_PROFILE_NAME")." ".Date("Y-m-d");

							$arFields = array(
									"NAME" => $arResult["PROFILE_NAME"],
									"USER_ID" => IntVal($USER->GetID()),
									"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]
								);
							$arResult["USER_PROPS_ID"] = CSaleOrderUserProps::Add($arFields);
							$arResult["USER_PROPS_ID"] = IntVal($arResult["USER_PROPS_ID"]);
						}

						if (IntVal($arResult["PROFILE_ID"]) <= 0 && $arOrderProperties["USER_PROPS"] == "Y" && $arResult["USER_PROPS_ID"] > 0)
						{
							$arFields = array(
									"USER_PROPS_ID" => $arResult["USER_PROPS_ID"],
									"ORDER_PROPS_ID" => $arOrderProperties["ID"],
									"NAME" => $arOrderProperties["NAME"],
									"VALUE" => $curVal
								);
							CSaleOrderUserPropsValue::Add($arFields);
						}
					}
				}
			}
			
			$withdrawSum = 0.0;
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				if ($arResult["PAY_CURRENT_ACCOUNT"] == "Y" && $arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
				{
					$dbUserAccount = CSaleUserAccount::GetList(
							array(),
							array(
									"USER_ID" => $USER->GetID(),
									"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
								)
						);
					if ($arUserAccount = $dbUserAccount->GetNext())
					{
						if ($arUserAccount["CURRENT_BUDGET"] > 0)
						{
							if(($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($totalOrderPrice)) || $arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] != "Y")
							{
								$withdrawSum = CSaleUserAccount::Withdraw(
										$USER->GetID(),
										$totalOrderPrice,
										$arResult["BASE_LANG_CURRENCY"],
										$arResult["ORDER_ID"]
									);

								if ($withdrawSum > 0)
								{
									$arFields = array(
											"SUM_PAID" => $withdrawSum,
											"USER_ID" => $USER->GetID()
										);

									CSaleOrder::Update($arResult["ORDER_ID"], $arFields);

									if ($withdrawSum == $totalOrderPrice)
										CSaleOrder::PayOrder($arResult["ORDER_ID"], "Y", False, False);
								}
							}
						}
					}
				}
			}
			// mail message
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				$event = new CEvent;

				$strOrderList = "";
				$dbBasketItems = CSaleBasket::GetList(
						array("NAME" => "ASC"),
						array("ORDER_ID" => $arResult["ORDER_ID"]),
						false,
						false,
						array("ID", "NAME", "QUANTITY")
					);
				while ($arBasketItems = $dbBasketItems->Fetch())
				{
					$strOrderList .= $arBasketItems["NAME"]." - ".$arBasketItems["QUANTITY"]." ".GetMessage("SALE_QUANTITY_UNIT");
					$strOrderList .= "\n";
				}

				$arFields = Array(
						"ORDER_ID" => $arResult["ORDER_ID"],
						"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
						"ORDER_USER" => ( (strlen($arResult["PAYER_NAME"]) > 0) ? $arResult["PAYER_NAME"] : $USER->GetFullName() ),
						"PRICE" => SaleFormatCurrency($totalOrderPrice, $arResult["BASE_LANG_CURRENCY"]),
						"BCC" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
						"EMAIL" => $arResult["USER_EMAIL"],
						"ORDER_LIST" => $strOrderList,
						"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
					);
				$event->Send("SALE_NEW_ORDER", SITE_ID, $arFields);
			}
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				LocalRedirect($arParams["PATH_TO_ORDER"]."?CurrentStep=7&ORDER_ID=".$arResult["ORDER_ID"]);
			}

			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 5;
		}
	}
}




$stores = CCatalogStore::GetList(
array(), array(),false,false,array());
while ($store = $stores->Fetch())
    $arResult['STORES'][] = $store;


 
 if($arResult["CurrentStep"] == 3 && $arResult["BACK"] == 'Y')
        $arResult["CurrentStep"] = 2;
 

if($arResult["CurrentStep"] == 3) 
    $arResult["CurrentStep"] = 4;


/*******************************************************************************/
/*****************  BODY  ******************************************************/
/*******************************************************************************/
if ($USER->IsAuthorized())
{
	if ($arResult["CurrentStep"] == 1)
	{
		$arResult["SKIP_FIRST_STEP"] = "N";
		$arResult["SKIP_SECOND_STEP"] = "N";
		
		$numPersonTypes = 0;
		$curOnePersonType = 0;

		$dbPersonTypesList = CSalePersonType::GetList(
				array("SORT" => "ASC"),
				array("LID" => SITE_ID)
			);
		while ($arPersonTypesList = $dbPersonTypesList->Fetch())
		{
			$numPersonTypes++;
			if ($numPersonTypes >= 2)
				break;

			if ($curOnePersonType <= 0)
				$curOnePersonType = IntVal($arPersonTypesList["ID"]);
		}

		if ($numPersonTypes < 2)
		{
			$arResult["SKIP_FIRST_STEP"] = "Y";
			$arResult["CurrentStep"] = 2;
			$arResult["PERSON_TYPE"] = $curOnePersonType;
		}
	}

	if ($arResult["CurrentStep"] < 3)
	{
		if ($arResult["SKIP_THIRD_STEP"] != "Y" && IntVal($arResult["PERSON_TYPE"]) > 0)
		{
			$arResult["SKIP_THIRD_STEP"] = "N";

			$dbOrderProps = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					array(
							"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
							"IS_LOCATION" => "Y"
						),
					false,
					false,
					array("ID", "SORT")
				);
			if (!($arOrderProps = $dbOrderProps->Fetch()))
				$arResult["SKIP_THIRD_STEP"] = "Y";
		}
	
		if($arResult["SKIP_SECOND_STEP"] != "Y" && IntVal($arResult["PERSON_TYPE"]) > 0)
		{
			$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]);
			if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
				$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

			$dbOrderProps = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					false,
					false,
					array("ID", "SORT")
				);
			if (!($arOrderProps = $dbOrderProps->Fetch()))
			{
				$arResult["SKIP_SECOND_STEP"] = "Y";
				if($arResult["SKIP_THIRD_STEP"] == "Y")
					$arResult["CurrentStep"] = 4;

			}
		}
		
		if($arResult["SKIP_SECOND_STEP"] == "Y" && $arResult["BACK"] == "Y")
		{
			$arResult["CurrentStep"] = 1;
		}
		elseif($arResult["SKIP_SECOND_STEP"] == "Y")
		{  
			$arResult["CurrentStep"] = 3;
		}
	}
	if ($arResult["CurrentStep"] == 3)
	{
		if (IntVal($arResult["DELIVERY_LOCATION"]) > 0)
		{
			// if your custom handler needs something else, ex. cart content, you may put it here or get it from your handler using API
			$arFilter = array(
				"COMPABILITY" => array(
					"WEIGHT" => $arResult["ORDER_WEIGHT"],
					"PRICE" => $arResult["ORDER_PRICE"],
					"LOCATION_FROM" => COption::GetOptionString('sale', 'location'),
					"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],
				)
			);

			$rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), $arFilter);
			$arDeliveryServicesList = array();
			while ($arDeliveryService = $rsDeliveryServicesList->Fetch())
			{
				$arDeliveryServicesList[] = $arDeliveryService;
			}
				
			//$numDelivery = count($arDeliveryServicesList);
			
			$curOneDelivery = false;

			$numDelivery = 0;
			foreach ($arDeliveryServicesList as $key => $arDelivery)
			{
				foreach ($arDelivery['PROFILES'] as $pkey => $arProfile)
				{
					if ($arProfile['ACTIVE'] != 'Y')
					{
						unset($arDeliveryServicesList[$key]['PROFILES'][$pkey]);
					}
				}
			
				$cnt = count($arDeliveryServicesList[$key]["PROFILES"]);
				if ($cnt <= 0) 
					unset($arDeliveryServicesList[$key]);
				else 
				{
					$numDelivery += $cnt;
					if($cnt == 1 && empty($curOneDelivery)) 
					{
						foreach ($arDeliveryServicesList[$key]["PROFILES"] as $pkey => $arProfile)
							$curOneDelivery = array($arDeliveryServicesList[$key]['SID'], $pkey);
					}
				}
			}

			$dbDelivery = CSaleDelivery::GetList(
					array(),
					array(
							"LID" => SITE_ID,
							"+<=WEIGHT_FROM" => $arResult["ORDER_WEIGHT"],
							"+>=WEIGHT_TO" => $arResult["ORDER_WEIGHT"],
							"+<=ORDER_PRICE_FROM" => $arResult["ORDER_PRICE"],
							"+>=ORDER_PRICE_TO" => $arResult["ORDER_PRICE"],
							"ACTIVE" => "Y",
							"LOCATION" => $arResult["DELIVERY_LOCATION"],
						)
				);
			while ($arDelivery = $dbDelivery->Fetch())
			{
				$numDelivery++;
				if ($numDelivery >= 2)
					break;

				if (!is_array($curOneDelivery) || count($curOneDelivery) <= 0 || $curOneDelivery <= 0)
				{
					$curOneDelivery = $arDelivery["ID"];
				}
			}
			
			if ($numDelivery < 2)
			{
				$arResult["SKIP_THIRD_STEP"] = "Y";
				$arResult["CurrentStep"] = 4;
				$arResult["DELIVERY_ID"] = $curOneDelivery;
			}
		}
		else
		{
			$arResult["SKIP_THIRD_STEP"] = "Y";
			$arResult["CurrentStep"] = 4;
		}
	}
	if ($arResult["CurrentStep"] == 4)
	{
		//if($arResult["PAY_CURRENT_ACCOUNT"] == "N")
		//{
			//if (IntVal($arResult["PAY_SYSTEM_ID"]) <= 0)
			//{
				$numPaySys = 0;
				$curOnePaySys = 0;
				$arFilter = array(
									"LID" => SITE_ID,
									"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
									"ACTIVE" => "Y",
									"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
									"PSA_HAVE_PAYMENT" => "Y"
								);
				if(!empty($arParams["DELIVERY2PAY_SYSTEM"]))
				{
					foreach($arParams["DELIVERY2PAY_SYSTEM"] as $val)
					{
						if(is_array($val[$arUserResult["DELIVERY_ID"]]))
						{
							foreach($val[$arUserResult["DELIVERY_ID"]] as $v)
								$arFilter["ID"][] = $v;
						}
						elseif(IntVal($val[$arUserResult["DELIVERY_ID"]]) > 0)
							$arFilter["ID"][] = $val[$arUserResult["DELIVERY_ID"]];
					}
				}
				$dbPaySystem = CSalePaySystem::GetList(
							array("SORT" => "ASC", "PSA_NAME" => "ASC"),
							$arFilter
					);
				while ($arPaySystem = $dbPaySystem->Fetch())
				{
					$numPaySys++;
					if ($numPaySys >= 2)
						break;

					if ($curOnePaySys <= 0)
						$curOnePaySys = $arPaySystem["ID"];
				}
				

				if ($numPaySys < 2 && $numPaySys > 0)
				{
					if($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
					{
						$dbUserAccount = CSaleUserAccount::GetList(
								array(),
								array(
										"USER_ID" => $USER->GetID(),
										"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
									)
							);
						if ($arUserAccount = $dbUserAccount->GetNext())
						{
							if ($arUserAccount["CURRENT_BUDGET"] <= 0)
							{
								$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "N";
							}
							else
							{
								if($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y")
								{
									if(DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($arResult["ORDER_PRICE"]))
									{
										$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "Y";
									}
									else
										$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "N";
								}
								else
								{
									$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "Y";
								}
							}

						}
						else
							$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "N";
					}
					
					
					if($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "N")
					{
						$arResult["SKIP_FORTH_STEP"] = "Y";
						$arResult["CurrentStep"] = 5;
						$arResult["PAY_SYSTEM_ID"] = $curOnePaySys;
					}
				}
			//}
			//else
			//{
			//	$arResult["SKIP_FORTH_STEP"] = "Y";
			//	$arResult["CurrentStep"] = 5;
			//}
		//}
	}
	
	//------------------ STEP 1 ----------------------------------------------
	if ($arResult["CurrentStep"] == 1)
	{
		$arResult["PERSON_TYPE_INFO"] = Array();
		$dbPersonType = CSalePersonType::GetList(
				array("SORT" => "ASC"),
				array("LID" => SITE_ID)
			);
		$bFirst = True;
		while ($arPersonType = $dbPersonType->GetNext())
		{
			if (IntVal($arResult["POST"]["PERSON_TYPE"]) == IntVal($arPersonType["ID"]) || IntVal($arResult["POST"]["PERSON_TYPE"]) <= 0 && $bFirst)
				$arPersonType["CHECKED"] = "Y";
			$arResult["PERSON_TYPE_INFO"][] = $arPersonType;
			$bFirst = False;
		}
			
		if(CModule::IncludeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "Step4_1";
			$event3 = "";

			if (is_array($arProductsInBasket))
			{
				foreach($arProductsInBasket as $ar_prod)
				{
					$event3 .= $ar_prod["PRODUCT_ID"].", ";
				}
			}
			$e = $event1."/".$event2."/".$event3;
			
			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
			{
					CStatistic::Set_Event($event1, $event2, $event3);
					$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	}
	//------------------ STEP 2 ----------------------------------------------
	elseif ($arResult["CurrentStep"] == 2)
	{
		$arResult["USER_PROFILES"] = Array();
		$bFillProfileFields = False;
		$bFirstProfile = True;

		$dbUserProfiles = CSaleOrderUserProps::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
						"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
						"USER_ID" => IntVal($USER->GetID())
					)
			);
		if ($arUserProfiles = $dbUserProfiles->GetNext())
		{
			$bFillProfileFields = True;
			do
			{
				if (IntVal($arResult["PROFILE_ID"])==IntVal($arUserProfiles["ID"]) || !isset($arResult["PROFILE_ID"]) && $bFirstProfile) 
					$arUserProfiles["CHECKED"] = "Y";
				$bFirstProfile = False;
				$arUserProfiles["USER_PROPS_VALUES"] = Array();
				$dbUserPropsValues = CSaleOrderUserPropsValue::GetList(
						array("SORT" => "ASC"),
						array("USER_PROPS_ID" => $arUserProfiles["ID"]),
						false,
						false, 
						array("VALUE", "PROP_TYPE", "VARIANT_NAME", "SORT", "ORDER_PROPS_ID")
					);
				while ($arUserPropsValues = $dbUserPropsValues->GetNext())
				{
					$valueTmp = "";
					if ($arUserPropsValues["PROP_TYPE"] == "SELECT"
						|| $arUserPropsValues["PROP_TYPE"] == "MULTISELECT"
						|| $arUserPropsValues["PROP_TYPE"] == "RADIO")
					{
						$arUserPropsValues["VALUE_FORMATED"] = $arUserPropsValues["VARIANT_NAME"];
					}
					elseif ($arUserPropsValues["PROP_TYPE"] == "LOCATION")
					{
						if ($arLocation = CSaleLocation::GetByID($arUserPropsValues["VALUE"], LANGUAGE_ID))
						{
							$arUserPropsValues["VALUE_FORMATED"] = htmlspecialcharsEx($arLocation["COUNTRY_NAME"]);
							if (strlen($arLocation["COUNTRY_NAME"]) > 0
								&& strlen($arLocation["CITY_NAME"]) > 0)
							{
								$arUserPropsValues["VALUE_FORMATED"] .= " - ";
							}
							$arUserPropsValues["VALUE_FORMATED"] .= htmlspecialcharsEx($arLocation["CITY_NAME"]);
						}
					}
					else
						$arUserPropsValues["VALUE_FORMATED"] = $arUserPropsValues["VALUE"];
					$arUserProfiles["USER_PROPS_VALUES"][] = $arUserPropsValues;
				}
				$arResult["USER_PROFILES"][] = $arUserProfiles;
			}
			while ($arUserProfiles = $dbUserProfiles->GetNext());
			
			if (isset($arResult["PROFILE_ID"]) && IntVal($arResult["PROFILE_ID"]) > 0 && $bFirstProfile)
				$arResult["USER_PROFILES_0"] = "Y";
			
		}

		if ($bFillProfileFields)
		{
			$arResult["USER_PROFILES_TO_FILL"] = "Y";
			if(isset($arResult["PROFILE_ID"]) && IntVal($arResult["PROFILE_ID"]) > 0 && $bFirstProfile)
				$arResult["USER_PROFILES_TO_FILL_VALUE"] = "Y";
		}

		//for function PrintPropsForm
		$propertyGroupID = 0;
		$propertyUSER_PROPS = "";

		$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]);
		if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
			$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

		$dbProperties = CSaleOrderProps::GetList(
				array(
						"GROUP_SORT" => "ASC",
						"PROPS_GROUP_ID" => "ASC",
						"SORT" => "ASC",
						"NAME" => "ASC"
					),
				$arFilter,
				false,
				false,
				array("ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "GROUP_NAME", "GROUP_SORT", "SORT", "USER_PROPS")
			);
		while ($arProperties = $dbProperties->GetNext())
		{
			unset($curVal);
			if(strlen($arResult["POST"]["ORDER_PROP_".$arProperties["ID"]])>0)
				$curVal = $arResult["POST"]["ORDER_PROP_".$arProperties["ID"]];
				
			$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"];
			if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID || $propertyUSER_PROPS != $arProperties["USER_PROPS"])
				$arProperties["SHOW_GROUP_NAME"] = "Y";
			$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
			$propertyUSER_PROPS = $arProperties["USER_PROPS"];
				
			if ($arProperties["REQUIED"]=="Y" || $arProperties["IS_EMAIL"]=="Y" || $arProperties["IS_PROFILE_NAME"]=="Y" || $arProperties["IS_LOCATION"]=="Y" || $arProperties["IS_LOCATION4TAX"]=="Y" || $arProperties["IS_PAYER"]=="Y")
				$arProperties["REQUIED_FORMATED"]="Y";
				

			if ($arProperties["TYPE"] == "CHECKBOX")
			{
				if ($curVal=="Y" || !isset($curVal) && $arProperties["DEFAULT_VALUE"]=="Y")
					$arProperties["CHECKED"] = "Y";
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 30);
			}
			elseif ($arProperties["TYPE"] == "TEXT")
			{
				if (strlen($curVal) <= 0)
				{
					if ($arProperties["IS_EMAIL"] == "Y")
						$arProperties["VALUE"] = $USER->GetEmail();
					elseif ($arProperties["IS_PAYER"] == "Y")
						$arProperties["VALUE"] = $USER->GetFullName();
					elseif(strlen($arProperties["DEFAULT_VALUE"])>0)
						$arProperties["VALUE"] = $arProperties["DEFAULT_VALUE"];
				}
				else
					$arProperties["VALUE"] = $curVal;

			}
			elseif ($arProperties["TYPE"] == "SELECT")
			{
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
				$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arProperties["ID"]),
						false,
						false,
						array("*")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					
					if ($arVariants["VALUE"] == $curVal || !isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])
						$arVariants["SELECTED"] = "Y";
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			elseif ($arProperties["TYPE"] == "MULTISELECT")
			{
				$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"].'[]';
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 5);
				$arDefVal = explode(",", $arProperties["DEFAULT_VALUE"]);
				for ($i = 0; $i < count($arDefVal); $i++)
					$arDefVal[$i] = Trim($arDefVal[$i]);
				
				$dbVariants = CSaleOrderPropsVariant::GetList(
						array("SORT" => "ASC"),
						array("ORDER_PROPS_ID" => $arProperties["ID"]),
						false,
						false,
						array("*")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)) || (!isset($curVal) && in_array($arVariants["VALUE"], $arDefVal)))
						$arVariants["SELECTED"] = "Y";
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			elseif ($arProperties["TYPE"] == "TEXTAREA")
			{
				$arProperties["SIZE2"] = ((IntVal($arProperties["SIZE2"]) > 0) ? $arProperties["SIZE2"] : 4);
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 40);
				$arProperties["VALUE"] = ((isset($curVal)) ? $curVal : $arProperties["DEFAULT_VALUE"]);
			}
			elseif ($arProperties["TYPE"] == "LOCATION")
			{
				$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
				$dbVariants = CSaleLocation::GetList(
						array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
						array("LID" => LANGUAGE_ID),
						false,
						false,
						array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
					);
				while ($arVariants = $dbVariants->GetNext())
				{
					if (IntVal($arVariants["ID"]) == IntVal($curVal) || !isset($curVal) && IntVal($arVariants["ID"]) == IntVal($arProperties["DEFAULT_VALUE"]))
						$arVariants["SELECTED"] = "Y";
					$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
					$arProperties["VARIANTS"][] = $arVariants;
				}
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
					if ($arVariants["VALUE"] == $curVal || (strlen($curVal)<=0 && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"]))
						$arVariants["CHECKED"]="Y";
					
					$arProperties["VARIANTS"][] = $arVariants;
				}
			}
			if($arProperties["USER_PROPS"]=="Y")
				$arResult["PRINT_PROPS_FORM"]["USER_PROPS_Y"][$arProperties["ID"]] = $arProperties;
			else
				$arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"][$arProperties["ID"]] = $arProperties;
		}
		if(empty($arResult["PRINT_PROPS_FORM"]["USER_PROPS_Y"]))
		{
			$arResult["USER_PROFILES"] = Array();
			$arResult["USER_PROFILES_TO_FILL_VALUE"] = "N";
			$arResult["USER_PROFILES_TO_FILL"] = "N";
		
		}
		
		if(CModule::IncludeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "Step4_2";
			$event3 = "";

			foreach($arProductsInBasket as $ar_prod)
			{
				$event3 .= $ar_prod["PRODUCT_ID"].", ";
			}
			$e = $event1."/".$event2."/".$event3;

			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
			{
					CStatistic::Set_Event($event1, $event2, $event3);
					$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	 
		$arResult["DELIVERY"] = Array();
		
		$dbDelivery = CSaleDelivery::GetList(
					array("SORT"=>"ASC", "NAME"=>"ASC"),
					array(
							"LID" => SITE_ID,
//							"+<=WEIGHT_FROM" => $arResult["ORDER_WEIGHT"],
//							"+>=WEIGHT_TO" => $arResult["ORDER_WEIGHT"],
//							"+<=ORDER_PRICE_FROM" => $arResult["ORDER_PRICE"],
//							"+>=ORDER_PRICE_TO" => $arResult["ORDER_PRICE"],
 							"ACTIVE" => "Y",
						//	"LOCATION" => $arResult["DELIVERY_LOCATION"]
						)
			);
		
		$bFirst = True;
		while ($arDelivery = $dbDelivery->GetNext())
		{
			$arDelivery["FIELD_NAME"] = "DELIVERY_ID";
			if (IntVal($arResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"]) 
				|| IntVal($arResult["DELIVERY_ID"]) <= 0 && $bFirst)
				$arDelivery["CHECKED"] = "Y";
			if (IntVal($arDelivery["PERIOD_FROM"]) > 0 || IntVal($arDelivery["PERIOD_TO"]) > 0)
			{
				$arDelivery["PERIOD_TEXT"] = GetMessage("SALE_DELIV_PERIOD");
				if (IntVal($arDelivery["PERIOD_FROM"]) > 0)
					$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_FROM")." ".IntVal($arDelivery["PERIOD_FROM"]);
				if (IntVal($arDelivery["PERIOD_TO"]) > 0)
					$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_TO")." ".IntVal($arDelivery["PERIOD_TO"]);
				if ($arDelivery["PERIOD_TYPE"] == "H")
					$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_PER_HOUR")." ";
				elseif ($arDelivery["PERIOD_TYPE"]=="M")
					$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_PER_MONTH")." ";
				else
					$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_PER_DAY")." ";
			}
			$arDelivery["PRICE_FORMATED"] = SaleFormatCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"]);
			$arResult["DELIVERY"][] = $arDelivery;
			$bFirst = false;
		}
		
		if (is_array($arDeliveryServicesList))
		{
			$bFirst = true;
			foreach ($arDeliveryServicesList as $arDeliveryInfo)
			{
				$delivery_id = $arDeliveryInfo["SID"];
			
				if (!is_array($arDeliveryInfo) || !is_array($arDeliveryInfo["PROFILES"])) continue;

				foreach ($arDeliveryInfo["PROFILES"] as $profile_id => $arDeliveryProfile)
				{
					$arProfile = array(
						"SID" => $profile_id,
						"TITLE" => $arDeliveryProfile["TITLE"],
						"DESCRIPTION" => $arDeliveryProfile["DESCRIPTION"],
						//"CHECKED" => $bFirst ? "Y" : "N",
						"FIELD_NAME" => "DELIVERY_ID",
					);

					if ($arResult['DELIVERY_ID'])
						if(strpos($arResult["DELIVERY_ID"], ":") !== false &&
							implode(":", $arResult["DELIVERY_ID"]) == $delivery_id.":".$profile_id 
							|| empty($arResult["DELIVERY_ID"]) && $bFirst
						)
						$arProfile["CHECKED"] = "Y";
					
					if (!is_array($arResult["DELIVERY"][$delivery_id])) 
					{
						$arResult["DELIVERY"][$delivery_id] = array(
							"SID" => $delivery_id,
							"TITLE" => $arDeliveryInfo["NAME"],
							"DESCRIPTION" => $arDeliveryInfo["DESCRIPTION"],
							"PROFILES" => array(),
						);
					}
					
					$arResult["DELIVERY"][$delivery_id]["PROFILES"][$profile_id] = $arProfile;
				
					$bFirst = false;
				}
			}
		}

		if(CModule::IncludeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "Step4_3";
			$event3 = "";

			foreach($arProductsInBasket as $ar_prod)
			{
				$event3 .= $ar_prod["PRODUCT_ID"].", ";
			}
			$e = $event1."/".$event2."/".$event3;

			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
			{
					CStatistic::Set_Event($event1, $event2, $event3);
					$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	}
	//------------------ STEP 4 ----------------------------------------------
	elseif ($arResult["CurrentStep"] == 4)
	{
		if ($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
		{
			$dbUserAccount = CSaleUserAccount::GetList(
					array(),
					array(
							"USER_ID" => $USER->GetID(),
							"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
						)
				);
			if ($arUserAccount = $dbUserAccount->GetNext())
			{
				if ($arUserAccount["CURRENT_BUDGET"] > 0)
				{
				
					if($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y")
					{
						if(DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($arResult["ORDER_PRICE"]))
						{
							$arResult["PAY_FROM_ACCOUNT"] = "Y";
							$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["BASE_LANG_CURRENCY"]);
							$arResult["USER_ACCOUNT"] = $arUserAccount;
						}
						else
							$arResult["PAY_FROM_ACCOUNT"] = "N";
					}
					else
					{
						$arResult["PAY_FROM_ACCOUNT"] = "Y";
						$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["BASE_LANG_CURRENCY"]);
						$arResult["USER_ACCOUNT"] = $arUserAccount;
					}
				}
			}
		}
		$arResult["PAY_SYSTEM"] = Array();
		$arFilter = array(
					//		"LID" => SITE_ID,
					//		"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
							"ACTIVE" => "Y",
							"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
					//		"PSA_HAVE_PAYMENT" => "Y"
				  );  
		if(!empty($arParams["DELIVERY2PAY_SYSTEM"]))
		{
			foreach($arParams["DELIVERY2PAY_SYSTEM"] as $val)
			{
				if(is_array($val[$arUserResult["DELIVERY_ID"]]))
				{
					foreach($val[$arUserResult["DELIVERY_ID"]] as $v)
						$arFilter["ID"][] = $v;
				}
				elseif(IntVal($val[$arUserResult["DELIVERY_ID"]]) > 0)
					$arFilter["ID"][] = $val[$arUserResult["DELIVERY_ID"]];
			}
		}
		$dbPaySystem = CSalePaySystem::GetList(
					array("SORT" => "ASC", "PSA_NAME" => "ASC"),
					$arFilter
			);
		$bFirst = True;
		while ($arPaySystem = $dbPaySystem->Fetch())
		{
			if (IntVal($arResult["PAY_SYSTEM_ID"]) == IntVal($arPaySystem["ID"]) || IntVal($arResult["PAY_SYSTEM_ID"]) <= 0 && $bFirst)
				$arPaySystem["CHECKED"] = "Y";
			$arPaySystem["PSA_NAME"] = htmlspecialcharsEx($arPaySystem["PSA_NAME"]);
			$arResult["PAY_SYSTEM"][] = $arPaySystem;
			$bFirst = false;
		}

		$bHaveTaxExempts = False;
		if (is_array($arResult["TaxExempt"]) && count($arResult["TaxExempt"])>0)
		{
			$dbTaxRateList = CSaleTaxRate::GetList(
					array("APPLY_ORDER" => "ASC"),
					array(
						"LID" => SITE_ID,
						"PERSON_TYPE_ID" => $PERSON_TYPE,
						"IS_IN_PRICE" => "N",
						"ACTIVE" => "Y",
						"LOCATION" => IntVal($TAX_LOCATION)
					)
				);
			while ($arTaxRateList = $dbTaxRateList->GetNext())
			{
				if (in_array(IntVal($arTaxRateList["TAX_ID"]), $arResult["TaxExempt"]))
				{
					$arResult["HaveTaxExempts"] = "Y";
					break;
				}
			}
		}
		
		if(CModule::IncludeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "Step4_4";
			$event3 = "";

			foreach($arProductsInBasket as $ar_prod)
			{
				$event3 .= $ar_prod["PRODUCT_ID"].", ";
			}
			$e = $event1."/".$event2."/".$event3;

			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
			{
					CStatistic::Set_Event($event1, $event2, $event3);
					$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
 	}
//	//------------------ STEP 5 ----------------------------------------------
 	elseif ($arResult["CurrentStep"] == 5)
 	{
         
            
		$arResult["ORDER_PROPS_PRINT"] = Array();
		$propertyGroupID = -1;

		$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]);
		if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
			$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

		$dbProperties = CSaleOrderProps::GetList(
				array(
						"GROUP_SORT" => "ASC",
						"PROPS_GROUP_ID" => "ASC",
						"SORT" => "ASC",
						"NAME" => "ASC"
					),
				$arFilter,
				false,
				false,
				array("ID", "NAME", "TYPE", "PROPS_GROUP_ID", "GROUP_NAME", "GROUP_SORT", "SORT")
			);
		while ($arProperties = $dbProperties->GetNext())
		{
			if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
			{
				$arProperties["SHOW_GROUP_NAME"] = "Y";
				$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
			}
			$curVal = $arResult["POST"]["ORDER_PROP_".$arProperties["ID"]];
			if ($arProperties["TYPE"] == "CHECKBOX")
			{
				if ($curVal == "Y")
					$arProperties["VALUE_FORMATED"] = GetMessage("SALE_YES");
				else
					$arProperties["VALUE_FORMATED"] = GetMessage("SALE_NO");
			}
			elseif ($arProperties["TYPE"] == "TEXT" || $arProperties["TYPE"] == "TEXTAREA")
			{
				$arProperties["VALUE_FORMATED"] = $curVal;
			}
			elseif ($arProperties["TYPE"] == "SELECT" || $arProperties["TYPE"] == "RADIO")
			{
				$arVal = CSaleOrderPropsVariant::GetByValue($arProperties["ID"], $curVal);
				$arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arVal["NAME"]);
			}
			elseif ($arProperties["TYPE"] == "MULTISELECT")
			{
				for ($i = 0; $i < count($curVal); $i++)
				{
					$arVal = CSaleOrderPropsVariant::GetByValue($arProperties["ID"], $curVal[$i]);
					if ($i > 0)
						$arProperties["VALUE_FORMATED"] .= ", ";
					$arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($arVal["NAME"]);
				}
			}
			elseif ($arProperties["TYPE"] == "LOCATION")
			{
				$arVal = CSaleLocation::GetByID($curVal, LANGUAGE_ID);
				$arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arVal["COUNTRY_NAME"]);
				if (strlen($arVal["COUNTRY_NAME"]) > 0 && strlen($arVal["CITY_NAME"]) > 0)
					$arProperties["VALUE_FORMATED"] .= " - ";
				$arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($arVal["CITY_NAME"]);
			}
			$arResult["ORDER_PROPS_PRINT"][] = $arProperties;
		}
		
		if (is_array($arResult["DELIVERY_ID"]))
		{
			$obDeliveryHandler = CSaleDeliveryHandler::GetBySID($arResult["DELIVERY_ID"][0]);
			$arResult["DELIVERY"] = $obDeliveryHandler->Fetch();
			
			$arResult["DELIVERY_PROFILE"] = $arResult["DELIVERY_ID"][1];

			$arOrderTmpDel = array(
				"PRICE" => $arResult["ORDER_PRICE"],
				"WEIGHT" => $arResult["ORDER_WEIGHT"],
				"LOCATION_FROM" => COption::GetOptionInt('sale', 'location'),
				"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
				"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],
				
			); 

			$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($arResult["DELIVERY_ID"][0], $arResult["DELIVERY_ID"][1], $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);
			
			if ($arDeliveryPrice["RESULT"] == "ERROR")
				$arResult["ERROR_MESSAGE"] = $arDeliveryPrice["TEXT"];
			else
				$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);

		}
		elseif ((IntVal($arResult["DELIVERY_ID"]) > 0) && ($arDeliv = CSaleDelivery::GetByID($arResult["DELIVERY_ID"])))
		{
			$arDeliv["NAME"] = htmlspecialcharsEx($arDeliv["NAME"]);
			$arResult["DELIVERY"] = $arDeliv;
			$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
		}
		elseif (IntVal($DELIVERY_ID)>0)
		{
			$arResult["DELIVERY"] = "ERROR";
		}
		
		if ((IntVal($arResult["PAY_SYSTEM_ID"]) > 0) && ($arPaySys = CSalePaySystem::GetByID($arResult["PAY_SYSTEM_ID"], $arResult["PERSON_TYPE"])))
		{
			$arResult["PAY_SYSTEM"] = $arPaySys;
			$arResult["PAY_SYSTEM"]["PSA_NAME"] = htmlspecialcharsEx($arResult["PAY_SYSTEM"]["PSA_NAME"]);
			$arResult["PAY_SYSTEM"]["~PSA_NAME"] = $arResult["PAY_SYSTEM"]["PSA_NAME"];
		}
		elseif (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
		{
			$arResult["PAY_SYSTEM"] = "ERROR";
		}
		
		$arResult["BASKET_ITEMS"] = Array();
		$arResult["WEIGHT_UNIT"] = htmlspecialchars(COption::GetOptionString('sale', 'weight_unit'));
		$arResult["ORDER_WEIGHT"] = 0;
		
		$dbBasketItems = CSaleBasket::GetList(
				array("NAME" => "ASC"),
				array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => "NULL"
					)
			);
		while ($arBasketItems = $dbBasketItems->Fetch())
		{
			if (strlen($arBasketItems["CALLBACK_FUNC"]) > 0)
			{
				CSaleBasket::UpdatePrice($arBasketItems["ID"], $arBasketItems["CALLBACK_FUNC"], $arBasketItems["MODULE"], $arBasketItems["PRODUCT_ID"], $arBasketItems["QUANTITY"]);
				$arBasketItems = CSaleBasket::GetByID($arBasketItems["ID"]);
			}
			
			if ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
			{
				$arBasketItems['NAME'] = htmlspecialcharsEx($arBasketItems['NAME']);			
				$arBasketItems['NOTES'] = htmlspecialcharsEx($arBasketItems['NOTES']);		
				$arResult["ORDER_WEIGHT"] += $arBasketItems["WEIGHT"] * $arBasketItems["QUANTITY"];
				$arBasketItems['WEIGHT_FROMATED'] = DoubleVal($arBasketItems['WEIGHT'])." ".$arResult["WEIGHT_UNIT"];	
			
				$arBasketItems["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);
				if(DoubleVal($arBasketItems["DISCOUNT_PRICE"]) > 0)
				{
					if(DoubleVal($arBasketItems["VAT_RATE"]) > 0)
						$arBasketItems["VAT_VALUE"] = DoubleVal(($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]);

					$arBasketItems["DISCOUNT_PRICE_PERCENT"] = $arBasketItems["DISCOUNT_PRICE"]*100 / ($arBasketItems["DISCOUNT_PRICE"] + $arBasketItems["PRICE"]);
					$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arBasketItems["DISCOUNT_PRICE_PERCENT"], 0)."%";
				}
				
				$arBasketItems["PROPS"] = Array();
				$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arBasketItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
				while($arProp = $dbProp -> GetNext())
					$arBasketItems["PROPS"][] = $arProp;

				
				$arResult["BASKET_ITEMS"][] = $arBasketItems;
			}
		}
		
		$arResult["ORDER_WEIGHT_FORMATED"] = DoubleVal($arResult["ORDER_WEIGHT"])." ".$arResult["WEIGHT_UNIT"];
		$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
		$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
		if (DoubleVal($arResult["DISCOUNT_PERCENT"])>0)
			$arResult["DISCOUNT_PERCENT_FORMATED"] = DoubleVal($arResult["DISCOUNT_PERCENT"])."%";
		if (is_array($arResult["arTaxList"]) && count($arResult["arTaxList"])>0)
		{
			foreach ($arResult["arTaxList"] as $key => $val)
			{
				if ($val["IS_IN_PRICE"]=="Y")
				{
					$arResult["arTaxList"][$key]["VALUE_FORMATED"] = " (".(($val["IS_PERCENT"]=="Y")?"".DoubleVal($val["VALUE"])."%, ":" ").GetMessage("SALE_TAX_INPRICE").")";
				}
				elseif ($val["IS_PERCENT"]=="Y")
				{
					$arResult["arTaxList"][$key]["VALUE_FORMATED"] = " (".DoubleVal($val["VALUE"])."%)";
				}
				$arResult["arTaxList"][$key]["VALUE_MONEY_FORMATED"] = SaleFormatCurrency($val["VALUE_MONEY"], $arResult["BASE_LANG_CURRENCY"]);
			}
		}
		
		if(IntVal($arResult["DELIVERY_PRICE"])>0)
			$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
		$orderTotalSum = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
		$arResult["ORDER_TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($orderTotalSum, $arResult["BASE_LANG_CURRENCY"]);
		if ($arResult["PAY_CURRENT_ACCOUNT"] == "Y")
		{
			$dbUserAccount = CSaleUserAccount::GetList(
					array(),
					array(
							"USER_ID" => $USER->GetID(),
							"CURRENCY" => $arResult["BASE_LANG_CURRENCY"]
						)
				);
			if ($arUserAccount = $dbUserAccount->Fetch())
			{
				if ($arUserAccount["CURRENT_BUDGET"] > 0)
				{
					$arResult["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($arUserAccount["CURRENT_BUDGET"] >= $orderTotalSum) ? $orderTotalSum : $arUserAccount["CURRENT_BUDGET"]),	$arResult["BASE_LANG_CURRENCY"]);
				}
				if($arUserAccount["CURRENT_BUDGET"] >= $orderTotalSum)
				{
					$arResult["PAYED_FROM_ACCOUNT"] = "Y";
				}
			}
		}	
	
		if(CModule::IncludeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "Step4_5";
			$event3 = "";

			foreach($arProductsInBasket as $ar_prod)
			{
				$event3 .= $ar_prod["PRODUCT_ID"].", ";
			}
			$e = $event1."/".$event2."/".$event3;

			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
			{
					CStatistic::Set_Event($event1, $event2, $event3);
					$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}
	}
	//------------------ STEP 6 ----------------------------------------------
	elseif ($arResult["CurrentStep"] == 7)
	{
		$dbOrder = CSaleOrder::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
						"LID" => SITE_ID,
						"USER_ID" => IntVal($USER->GetID()),
						"ID" => $arResult["ORDER_ID"]
					)
			);
		if ($arOrder = $dbOrder->GetNext())
		{
			if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0)
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
							CSalePaySystemAction::InitParamArrays($arOrder, $ID, $arPaySysAction["PARAMS"]);
							
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
			
			$arDateInsert = explode(" ", $arOrder["DATE_INSERT"]);
			if (is_array($arDateInsert) && count($arDateInsert) > 0)
				$arResult["ORDER"]["DATE_INSERT_FORMATED"] = $arDateInsert[0];
			else
				$arResult["ORDER"]["DATE_INSERT_FORMATED"] = $arOrder["DATE_INSERT"];

			if(CModule::IncludeModule("statistic"))
			{
				$event1 = "eStore";
				$event2 = "order_confirm";
				$event3 = $arResult["ORDER"]["ID"];

				$e = $event1."/".$event2."/".$event3;

				if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
				{
						CStatistic::Set_Event($event1, $event2, $event3);
						$_SESSION["ORDER_EVENTS"][] = $e;
				}
			}
			
			$events = GetModuleEvents("sale", "OnSaleComponentOrderComplete");
			while($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, Array($ID, $arOrder));

		}


	}
	//------------------------------------------------------------------------
}

$this->IncludeComponentTemplate();
