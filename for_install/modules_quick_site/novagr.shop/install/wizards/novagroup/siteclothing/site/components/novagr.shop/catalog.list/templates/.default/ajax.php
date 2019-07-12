<?php
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true); 
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
function callback($buffer)
{
	$arResult = array();
	global $USER;
	if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule('iblock'))
	{
	
	} else {
		$arResult['result'] = 'ERROR';
	}
	
	$arResult = array("STATUS" => "N", "NOTIFY_URL" => "", "ERRORS" => "NOTIFY_ERR_REG");
	
	if (isset($_POST['user_mail']) && $_SERVER["REQUEST_METHOD"] == "POST" && $_POST['ajax'] == "Y" && check_bitrix_sessid() && !$USER->IsAuthorized())
	{
		$arResult["ERRORS"] = "";
		$arErrors = array();
		$user_mail = trim($_POST['user_mail']);
		$id = IntVal($_POST['id']);
		$user_login = trim($_POST["user_login"]);
		$user_password = trim($_POST["user_password"]);
		$url = trim($_POST["notifyurl"]);
	
		if (strlen($user_login) <= 0 && strlen($user_password) <= 0 && strlen($user_mail) <= 0)
			$arResult["ERRORS"] = 'NOTIFY_ERR_NULL';
	
		if (isset($_SESSION["NOTIFY_PRODUCT"]["CAPTHA"]) && $_SESSION["NOTIFY_PRODUCT"]["CAPTHA"] == "Y")
		{
			if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
				$arResult["ERRORS"] = 'NOTIFY_ERR_CAPTHA';
		}
	
		if (strlen($user_mail) > 0 && strlen($arResult["ERRORS"]) <= 0)
		{
			if( CUser::IsAuthorized() )
			{
				
				if (strlen($user_mail) > 0)
				{
					$user_id = CSaleUser::DoAutoRegisterUser($user_mail, array(), SITE_ID, $arErrors);
					if ($user_id > 0)
					{
						$USER->Authorize($user_id);
						if (count($arErrors) > 0)
							$arResult["ERRORS"] = $val[0]["TEXT"];
					}
					else
						$arResult["ERRORS"] = 'NOTIFY_ERR_REG';
				}else{
					$arAuthResult = $USER->Login($user_login, $user_password, "Y");
					$rs = $APPLICATION->arAuthResult = $arAuthResult;
					if (count($rs) > 0 && $rs["TYPE"] == "ERROR")
						$arResult["ERRORS"] = $rs["MESSAGE"];
				}
		
				if (strlen($arResult["ERRORS"]) <= 0)
				{
					$arResult["STATUS"] = "Y";
					$_REQUEST["action"] = 'productSubsribe';
				}
				
			}else{
				$res = CUser::GetList($b, $o, array("=EMAIL" => $user_mail));
				if($res->Fetch())
					$arResult["ERRORS"] = 'NOTIFY_ERR_MAIL_EXIST';
			}
		}
	
		if (strlen($arResult["ERRORS"]) <= 0)
		{
			if (strlen($user_mail) > 0)
			{
				$user_id = CSaleUser::DoAutoRegisterUser($user_mail, array(), SITE_ID, $arErrors);
				if ($user_id > 0)
				{
					$USER->Authorize($user_id);
					if (count($arErrors) > 0)
						$arResult["ERRORS"] = $val[0]["TEXT"];
				}
				else
					$arResult["ERRORS"] = 'NOTIFY_ERR_REG';
			}
			else
			{
				$arAuthResult = $USER->Login($user_login, $user_password, "Y");
				$rs = $APPLICATION->arAuthResult = $arAuthResult;
				if (count($rs) > 0 && $rs["TYPE"] == "ERROR")
					$arResult["ERRORS"] = $rs["MESSAGE"];
			}
	
			if (strlen($arResult["ERRORS"]) <= 0)
			{
				$arResult["STATUS"] = "Y";
				$_REQUEST["action"] = 'productSubsribe';
			}
		}
	}
	
	if ( $_REQUEST["action"] == 'productSubsribe' && $_REQUEST["elemId"]>0) {
	
		$arResult['elemId'] = $_REQUEST["elemId"];
	
		//deb($USER->GetID());
		$idUser = $USER->GetID();
		//deb($idUser);
		if ($idUser>0) {
			$arRewriteFields = array("SUBSCRIBE" => "Y", "CAN_BUY" => "N");
			// , "PRODUCT_PROVIDER_CLASS" => "none"
			$product_properties = array();
			$arResult['result'] = 'ERROR';
			//$arResult['message'] = 'Не подписан.';
	
			$FUSER_ID = CSaleBasket::GetBasketUserID();
			//deb($FUSER_ID);
			$dbBasketItems = CSaleBasket::GetList(
					array(
							"ID" => "ASC"
					),
					array(
							"PRODUCT_ID" => $_REQUEST["elemId"],
							"FUSER_ID" => $FUSER_ID,
							"LID" => SITE_ID,
							"ORDER_ID" => "NULL",
					),
					false,
					false,
					array()
			);
			
			
			if($_REQUEST['UNSUBSCRIBE'] == "Y")
			{
				if( $arBasket = $dbBasketItems->Fetch() )
					CSaleBasket::Delete($arBasket["ID"]);
			}elseif($_REQUEST['CHECK'] == "Y"){
				if( $arBasket = $dbBasketItems->Fetch() )
					$arResult['CHECK'] = "Y";
				else
					$arResult['CHECK'] = "N";
			}else{
				if($arBasket = $dbBasketItems->Fetch())
				{
					// товар уже в корзине
					if ($arBasket["SUBSCRIBE"] == "Y" ) {
		
					} else {
						$arFields = array(
								"SUBSCRIBE" => "Y"
						);
						CSaleBasket::Update($arBasket["ID"], $arFields);
					}
					$arResult['result'] = 'OK';
					//$arResult['message'] = 'inSubscribe';
					$arResult["ERRORS"] = 'NOTIFY_ALREADY_SUBSCRIBED';
					//$arSkuTmp["CART"] = "inSubscribe";
					//else
					//	$arSkuTmp["CART"] = "inCart";
				} else {
					//get props sku
					$product_properties = array();
					$arPropsSku = array();
		
					$productID = $_REQUEST["elemId"];
					$arParentSku = CCatalogSku::GetProductInfo($productID);
					if ($arParentSku && count($arParentSku) > 0)
					{
						$dbProduct = CIBlockElement::GetList(array(), array("ID" => $productID), false, false, array('IBLOCK_ID', 'IBLOCK_SECTION_ID'));
						$arProduct = $dbProduct->Fetch();
		
						$dbOfferProperties = CIBlock::GetProperties($arProduct["IBLOCK_ID"], array(), array("!XML_ID" => "CML2_LINK"));
						while($arOfferProperties = $dbOfferProperties->Fetch())
							$arPropsSku[] = $arOfferProperties["CODE"];
		
						$product_properties = CIBlockPriceTools::GetOfferProperties(
							$productID,
							$arParentSku["IBLOCK_ID"],
							$arPropsSku
						);
					}
					if (Add2BasketByProductID($_REQUEST["elemId"], 1, $arRewriteFields, $product_properties)) {
						$arResult["STATUS"] = "Y";
						$arResult['result'] = 'OK';
						//$arResult['message'] = 'Подписан';
					}
				}
			}
	
		} else {
	
			$arResult['result'] = 'ERROR';
			//$arResult['message'] = 'Не подписан';
		}
	}
	
	$arResultJson = json_encode($arResult);
	return($arResultJson);
}
ob_start("callback");
ob_end_flush();
?>