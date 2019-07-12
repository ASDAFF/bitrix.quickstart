<?
use \Bitrix\Main\Loader;
use \Bitrix\Sale\DiscountCouponsManager;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-type: application/json; charset=utf-8');
require(dirname(__FILE__)."/lang/" . LANGUAGE_ID . "/script.php");
require(dirname(__FILE__)."/functions.php");

ob_start();

if(!function_exists('json_encode')){
    function json_encode($value){
        if(is_int($value)){
			return (string)$value;
		}
		elseif(is_string($value)){
	        $value = str_replace(array('\\', '/', '"', "\r", "\n", "\b", "\f", "\t"),  array('\\\\', '\/', '\"', '\r', '\n', '\b', '\f', '\t'), $value);
	        $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
	        $result = "";
	        for ($i = mb_strlen($value) - 1; $i >= 0; $i--){
	            $mb_char = mb_substr($value, $i, 1);
	            if (mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) { $result = sprintf("\\u%04x", $match[1]) . $result;  }
				else { $result = $mb_char . $result;  }
	        }
	        return '"' . $result . '"';
        }
		elseif(is_float($value)) { return str_replace(",", ".", $value); }
		elseif(is_null($value)) {  return 'null';}
		elseif(is_bool($value)) { return $value ? 'true' : 'false';   }
		elseif(is_array($value)){
            $with_keys = false;
            $n = count($value);
            for ($i = 0, reset($value); $i < $n; $i++, next($value))  { if (key($value) !== $i) {  $with_keys = true; break;  }  }
        }
		elseif (is_object($value)) { $with_keys = true; }
		else { return ''; }
        $result = array();
        if ($with_keys)  {  foreach ($value as $key => $v) {  $result[] = json_encode((string)$key) . ':' . json_encode($v); }  return '{' . implode(',', $result) . '}'; }
		else {  foreach ($value as $key => $v) { $result[] = json_encode($v); } return '[' . implode(',', $result) . ']';  }
    }
}

if(!function_exists('getJson')) {
	function getJson($message, $res='N', $error=''){
		global $APPLICATION;
		$result = array(
			'result' => $res=='Y'?'Y':'N',
			'message' => $APPLICATION->ConvertCharset($message, SITE_CHARSET, 'utf-8')
		);
		if (strlen($error) > 0) { $result['err'] = $APPLICATION->ConvertCharset($error, SITE_CHARSET, 'utf-8'); }
		return json_encode($result);
	}
}

if(!CModule::IncludeModule('sale') || !CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog') || !CModule::IncludeModule('currency')){
	die(getJson(GetMessage('CANT_INCLUDE_MODULE')));
}

global $APPLICATION, $USER;
$user_registered =$user_exists = false;
$bAllBasketBuy = $_POST['BUY_TYPE'] == 'ALL';
$_POST['ONE_CLICK_BUY']['FIO'] = $APPLICATION->ConvertCharset($_POST['ONE_CLICK_BUY']['FIO'], 'utf-8', SITE_CHARSET);
$_POST['ONE_CLICK_BUY']['COMMENT'] = $APPLICATION->ConvertCharset($_POST['ONE_CLICK_BUY']['COMMENT'], 'utf-8', SITE_CHARSET);

// check input data
if(!empty($_POST['ONE_CLICK_BUY']['EMAIL']) && !preg_match('/^[0-9a-zA-Z\-_\.]+@[0-9a-zA-Z\-]+[\.]{1}[0-9a-zA-Z\-]+[\.]?[0-9a-zA-Z\-]+$/', $_POST['ONE_CLICK_BUY']['EMAIL'])) die(getJson(GetMessage('BAD_EMAIL_FORMAT')));
elseif(empty($_POST['ONE_CLICK_BUY']['FIO']) && $_POST['PERSON_TYPE_ID'] == 1) die(getJson(GetMessage('NO_USER_NAME')));
// elseif(empty($_POST['ONE_CLICK_BUY']['PHONE'])) die(getJson(GetMessage('NO_PHONE')));

$basketUserID = CSaleBasket::GetBasketUserID();
$arBasketItemsAll=array();

// register user if not registered
if(!$USER->IsAuthorized()){
	$resBasketItems = CSaleBasket::GetList(array('SORT' => 'DESC'), array('FUSER_ID' => $basketUserID, 'LID' => SITE_ID, 'ORDER_ID' => NULL));
	while($arBasketItem = $resBasketItems->Fetch()){
		// get props
		$arProps = array();
		$dbRes = CSaleBasket::GetPropsList(array(), array('BASKET_ID' => $arBasketItem['ID']));
		while($arProp = $dbRes->Fetch()){
		   $arProps[] = $arProp;
		}
		if($arProps){
			$arBasketItem["BASKET_PROPS"]=$arProps;
		}
		$arBasketItemsAll[]=$arBasketItem;
	}

	if(!isset($_POST['ONE_CLICK_BUY']['EMAIL']) || trim($_POST['ONE_CLICK_BUY']['EMAIL']) == ''){
		$login = 'user_' . substr((microtime(true) * 10000), 0, 12);
		if (strlen(SITE_SERVER_NAME)) { $server_name = SITE_SERVER_NAME; } else { $server_name = $_SERVER["SERVER_NAME"];}
		$server_name = Cutil::translit($server_name, "ru");
		if($dotPos = strrpos($server_name, "_")){
			$server_name = substr($server_name, 0, $dotPos).str_replace("_", ".", substr($server_name, $dotPos));
		}
		else{
			$server_name .= ".ru";
		}
		$_POST['ONE_CLICK_BUY']['EMAIL'] = $login.'@'.$server_name;
		$user_registered = true;
	}
	else{
		$dbUser = CUser::GetList(($by = 'ID'), ($order = 'ASC'), array('=EMAIL' => trim($_POST['ONE_CLICK_BUY']['EMAIL'])));
		if($dbUser->SelectedRowsCount() == 0){
			$login = 'user_'.substr((microtime(true) * 10000), 0, 12);
			$user_registered = true;
		}
		elseif($dbUser->SelectedRowsCount() == 1){
			$ar_user = $dbUser->Fetch();
			$registeredUserID = $ar_user['ID'];

			if(!checkNewVersionExt('sale')){
				$USER->Authorize($registeredUserID);
			}
			$user_registered = true;
			$user_exists = true;
		}
		else die(getJson(GetMessage('TOO_MANY_USERS')));
	}

	if($user_registered && !$user_exists){
		$captcha = COption::GetOptionString('main', 'captcha_registration', 'N');
		if($captcha == 'Y'){COption::SetOptionString('main', 'captcha_registration', 'N');}
		$userPassword = randString(10);
		$username = explode(' ', trim($_POST['ONE_CLICK_BUY']['FIO']));
		$newUser = $USER->Register($login, $username[0], $username[1], $userPassword,  $userPassword, $_POST['ONE_CLICK_BUY']['EMAIL']);
		// $newUser = $USER->Add(array("LOGIN"=>$login, "NAME"=>$username[0], "LAST_NAME"=>$username[1], "PASSWORD"=>$userPassword,  "CONFIRM_PASSWORD"=>$userPassword, "EMAIL"=>$_POST['ONE_CLICK_BUY']['EMAIL']));
		
		if($captcha == 'Y'){
			COption::SetOptionString('main', 'captcha_registration', 'Y');
		}
		if($newUser['TYPE'] == 'ERROR') {
			die(getJson(GetMessage('USER_REGISTER_FAIL'), 'N', $newUser['MESSAGE']));
		}
		else{
			$registeredUserID = $newUser['ID'];
			// $registeredUserID = $newUser;
			if (!empty($_POST['ONE_CLICK_BUY']['PHONE']) && ($arParams["AUTO_LOGOUT"]=="Y")) {
				$USER->Update($registeredUserID,  array('PERSONAL_PHONE' => $_POST['ONE_CLICK_BUY']['PHONE']));
			}
			if (!empty($username[2])) {
				$USER->Update($registeredUserID,  array('SECOND_NAME' => $username[2]));
			}
			 //$USER->Logout();
		}
	}
}
else{
	$registeredUserID = $USER->GetID();
}

if(!$_POST['ONE_CLICK_BUY']['EMAIL']){
	$_POST['ONE_CLICK_BUY']['EMAIL'] = $USER->GetEmail();
}

if(!$_POST['ONE_CLICK_BUY']['LOCATION']){
	$arLocation = CSaleOrderProps::GetList(array("SORT" => "ASC"), array("PERSON_TYPE_ID" => intval($_POST['PERSON_TYPE_ID']) > 0 ? $_POST['PERSON_TYPE_ID']: 1, "CODE" => "LOCATION"), false, false, array())->Fetch();
   	$_POST['ONE_CLICK_BUY']['LOCATION'] = $arLocation["DEFAULT_VALUE"];
}

$deliveryId = intval($_POST['DELIVERY_ID']) > 0 ? intval($_POST['DELIVERY_ID']) : "";

if(class_exists('\Bitrix\Sale\Delivery\Services\Table')){
	$deliveryId = intval($deliveryId) > 0 ? \Bitrix\Sale\Delivery\Services\Table::getCodeById($deliveryId) : "";
}

$isOrderConverted = \Bitrix\Main\Config\Option::get("main", "~sale_converted_15", 'N');

/* New discount */
DiscountCouponsManager::init();

$newOrder = array(
	'LID' => SITE_ID,
	'PAYED' => 'N',
	"CANCELED" => "N",
	"STATUS_ID" => "N",
	'USER_ID' => $registeredUserID,
	'PERSON_TYPE_ID' => intval($_POST['PERSON_TYPE_ID']) > 0 ? $_POST['PERSON_TYPE_ID'] : 1,
	'DELIVERY_ID' => $deliveryId,
	'PAY_SYSTEM_ID' => intval($_POST['PAY_SYSTEM_ID']) > 0 ? $_POST['PAY_SYSTEM_ID'] : 1,
	'USER_DESCRIPTION' => $_POST['ONE_CLICK_BUY']['COMMENT'],
	'COMMENTS' => GetMessage('FAST_ORDER_COMMENT'),
);

if($bAllBasketBuy){
	$arBasketItems = array();
	if($user_registered){
		if($arBasketItemsAll){
			$arProductIDs=array();
			foreach($arBasketItemsAll as $arItem){
				if (CSaleBasketHelper::isSetItem($arItem) || $arItem["CAN_BUY"]=="N" || $arItem["DELAY"]=="Y" || $arItem["SUBSCRIBE"]=="Y") // set item
				continue;
				$arBasketItems[] = $arItem;
				$arProductIDs[]=$arItem["PRODUCT_ID"];
			}
		}
	}else{
		$arSelFields = array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "CURRENCY", "CATALOG_XML_ID", "VAT_RATE", "NOTES", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "DIMENSIONS", "TYPE", "SET_PARENT_ID", "DETAIL_PAGE_URL");
		$resBasketItems = CSaleBasket::GetList(array('SORT' => 'DESC'), array('FUSER_ID' => $basketUserID, 'LID' => SITE_ID, 'ORDER_ID' => 'NULL', 'DELAY' => 'N', 'CAN_BUY' => 'Y'), false, false, $arSelFields);
		while($arBasketItem = $resBasketItems->Fetch()){
			if (CSaleBasketHelper::isSetItem($arBasketItem)) // set item
				continue;
			$arBasketItems[] = $arBasketItem;
		}
	}

	if($arBasketItems){
		// update basket items prices
		CSaleBasket::UpdateBasketPrices($basketUserID, SITE_ID);

		// calculate order prices
		$arOrderDat = CSaleOrder::DoCalculateOrder(SITE_ID, $registeredUserID, $arBasketItems, $newOrder['PERSON_TYPE_ID'], array(), $deliveryId, $newOrder['PAY_SYSTEM_ID'], array(), $arErrors, $arWarnings);

		// set delivery price to 0
		$newOrder['PRICE_DELIVERY_DIFF'] = $arOrderDat["PRICE_DELIVERY"];
		$newOrder["PRICE_DELIVERY"] = $newOrder["DELIVERY_PRICE"] = $arOrderDat["DELIVERY_PRICE"] = $arOrderDat["PRICE_DELIVERY"] = 0;

		$newOrder['CURRENCY'] = $arOrderDat["CURRENCY"];
		$newOrder['PRICE'] = $arOrderDat["PRICE"] = $arOrderDat["ORDER_PRICE"] + $arOrderDat["DELIVERY_PRICE"] + $arOrderDat["TAX_PRICE"] - $arOrderDat["DISCOUNT_PRICE"];
		$newOrder["DISCOUNT_VALUE"] = $arOrderDat["DISCOUNT_PRICE"];
		$newOrder["TAX_VALUE"] = $arOrderDat["bUsingVat"] == "Y" ? $arOrderDat["VAT_SUM"] : $arOrderDat["TAX_PRICE"];
		$arOrderDat['USER_ID'] = $registeredUserID;

		// create order
		if(!checkNewVersionExt('sale')){
			$orderID = $arResult["ORDER_ID"] = (int)CSaleOrder::DoSaveOrder($arOrderDat, $newOrder, 0, $arErrors);
		}else{
			$order = placeOrder($registeredUserID, $basketUserID, $newOrder, $arOrderDat, $_POST);
			$orderID = $order->GetId();
		}
		if($orderID == false){
			$strError = '';
			if($ex = $APPLICATION->GetException()) $strError = $ex->GetString();

			if($user_registered)
				$USER->Logout();

			die(getJson(GetMessage('ORDER_CREATE_FAIL'), 'N', $strError));
		}

		if($orderID){
			// add basket to order
			if($user_registered){
				foreach($arProductIDs as $id)
					CSaleBasket::Update($id, array('ORDER_ID' => $orderID));
			}else{
				CSaleBasket::OrderBasket($orderID, $basketUserID, SITE_ID, false);
			}

			if($user_registered){
				// if latest sale version with converted module sale, than check items

				$resBasketItems = CSaleBasket::GetList(array('SORT' => 'DESC'), array(/*'FUSER_ID' => $basketUserID,*/ 'LID' => SITE_ID, 'ORDER_ID' => $orderID, '!PRODUCT_ID' => $arProductIDs), false, false, array('ID', 'QUANTITY', 'PRODUCT_ID', 'TYPE', 'SET_PARENT_ID'));
				while($arBasketItem = $resBasketItems->Fetch()){
					$bSetItem = CSaleBasketHelper::isSetItem($arBasketItem);
					if($bSetItem) // set item
						continue;
					// get props
					$arProps = array();
					$dbRes = CSaleBasket::GetPropsList(array(), array('BASKET_ID' => $arBasketItem['ID']));
					while($arProp = $dbRes->Fetch()){
					   $arProps[] = $arProp;
					}

					// delete from order
					CSaleBasket::Delete($arBasketItem['ID']);

					// add to basket again
					if(!$bSetItem){
						Add2BasketByProductID($arBasketItem['PRODUCT_ID'], $arBasketItem['QUANTITY'], array(), $arProps);
					}				
				}
			}

			if(!checkNewVersionExt('sale')){
				// fix bug with DELIVERY_PRICE, when count of products more than one (bitrix bug with delivery price)
				$arUpdateFields = array('PRICE' => $newOrder['PRICE'], 'PRICE_DELIVERY' => 0);
				if(class_exists('\Bitrix\Sale\Internals\OrderTable')){
					\Bitrix\Sale\Internals\OrderTable::update($orderID, $arUpdateFields);

					// fix bug with payment SUM, when buy set
					if(class_exists('\Bitrix\Sale\Internals\PaymentTable')){
						$res = \Bitrix\Sale\Internals\PaymentTable::getList(array('order' => array('ID' => 'ASC'), 'filter' => array('ORDER_ID' => $orderID)));
						if($payment = $res->fetch()){
							\Bitrix\Sale\Internals\PaymentTable::update($payment['ID'], array('SUM' => $newOrder['PRICE']));
						}
					}
				}
				else{
					CSaleOrder::Update($orderID, $arUpdateFields);
				}
			}
		}
	}
}
else{
	$arProps = array();
	$productID = intval($_POST['ELEMENT_ID']);
	$iblockID = intval($_POST['IBLOCK_ID']);
	$productQuantity = ((float)$_POST['ELEMENT_QUANTITY'] > 0 ? (float)$_POST['ELEMENT_QUANTITY'] : 1);

	$resProduct = CIBlockElement::GetByID($productID);
	$arProduct = $resProduct->GetNext();

	if(strlen($_REQUEST['OFFER_PROPERTIES']) && $iblockID > 0){
		$arOfferProperties=json_decode($_REQUEST["OFFER_PROPERTIES"]);
		if($arOfferProperties){
			$intProductIBlockID = (int)CIBlockElement::GetIBlockByID($productID);
			if($intProductIBlockID == $iblockID){
				$arProps = CIBlockPriceTools::CheckProductProperties(
					$iblockID,
					$productID,
					$arOfferProperties,
					$_REQUEST["prop"],
					true
				);
			}else{
				$arProps = CIBlockPriceTools::GetOfferProperties($productID, $iblockID, $arOfferProperties, $skuAddProps);
			}
		}
	}

	// if this product is already in basket, then fix quantity
	$arBasketItems = CSaleBasket::GetList(array(), array("PRODUCT_ID" => $productID, "FUSER_ID" => $basketUserID, "LID" => SITE_ID, "ORDER_ID" => NULL), false, false, array("ID"))->Fetch();
	if($arBasketItems){
		$productBasketID = $arBasketItems['ID'];
		$arFields = array("DELAY" => "N", "SUBSCRIBE" => "N", "QUANTITY" => $productQuantity);
		CSaleBasket::Update($productBasketID, $arFields);
	}
	else{
		// add product to basket
		$productBasketID = Add2BasketByProductID($productID, $productQuantity, array(), $arProps);
		if(!$productBasketID){
			$strError = '';
			if($ex = $APPLICATION->GetException()) {$strError = $ex->GetString();}

			if($user_registered)
				$USER->Logout();

			die(getJson(GetMessage('ITEM_ADD_FAIL'), 'N', $strError));
		}
	}

	$arBasketItems = array(CSaleBasket::GetByID($productBasketID));

	// update basket items prices
	CSaleBasket::UpdateBasketPrices($basketUserID, SITE_ID);

	// calculate order prices
	$arOrderDat = CSaleOrder::DoCalculateOrder(SITE_ID, $registeredUserID, $arBasketItems, $newOrder['PERSON_TYPE_ID'], array(), $deliveryId, $newOrder['PAY_SYSTEM_ID'], array(), $arErrors, $arWarnings);
	if($arErrors){
		if($user_registered)
			$USER->Logout();
		die(getJson(GetMessage('ORDER_CREATE_FAIL'), 'N', implode('<br />', (array)$arErrors)));
	}
	if(is_array($arOrderDat) && array_key_exists("ORDER_PRICE", $arOrderDat)){
		\Bitrix\Main\Loader::IncludeModule('aspro.optimus');
		$arError = COptimus::checkAllowDelivery($arOrderDat["ORDER_PRICE"], $arOrderDat["CURRENCY"]);

		if($arError["ERROR"]){
			CSaleBasket::Delete($productBasketID);
			if($user_registered){
				$USER->Logout();
				if(!$USER->IsAuthorized() && $arBasketItemsAll && !$bAllBasketBuy){
					foreach($arBasketItemsAll as $arItem){
						// get props
						$arProps = array();
						if($arItem['BASKET_PROPS']){
							$arProps=$arItem['BASKET_PROPS'];
						}
						Add2BasketByProductID($arItem['PRODUCT_ID'], $arItem['QUANTITY'], array(), $arProps);
					}
				}
			}
			COptimusCache::ClearCacheByTag('sale_basket');
			die(getJson($arError["TEXT"]));
		}
	}

	// set delivery price to 0
	$newOrder["PRICE_DELIVERY"] = $arOrderDat["DELIVERY_PRICE"] = $arOrderDat["PRICE_DELIVERY"] = 0;

	$newOrder['CURRENCY'] = $arOrderDat["CURRENCY"];
	$newOrder['PRICE'] = $arOrderDat["PRICE"] = $arOrderDat["ORDER_PRICE"] + $arOrderDat["DELIVERY_PRICE"] + $arOrderDat["TAX_PRICE"] - $arOrderDat["DISCOUNT_PRICE"];
	$newOrder["DISCOUNT_VALUE"] = $arOrderDat["DISCOUNT_PRICE"];
	$newOrder["TAX_VALUE"] = $arOrderDat["bUsingVat"] == "Y" ? $arOrderDat["VAT_SUM"] : $arOrderDat["TAX_PRICE"];
	$arOrderDat['USER_ID'] = $registeredUserID;
	
	// create order
	if(!checkNewVersionExt('sale')){
		$orderID = $arResult['ORDER_ID'] = (int)CSaleOrder::DoSaveOrder($arOrderDat, $newOrder, 0, $arErrors);
	}else{
		$order = placeOrder($registeredUserID, $basketUserID, $newOrder, $arOrderDat, $_POST);
		$orderID = $order->GetId();
	}
		
	if($orderID == false){
		$strError = '';
		if($ex = $APPLICATION->GetException()) $strError = $ex->GetString();

		if($user_registered)
			$USER->Logout();

		die(getJson(GetMessage('ORDER_CREATE_FAIL'), 'N', $strError));
	}
	if($orderID){
		// add product to order
		CSaleBasket::Update($productBasketID, array('ORDER_ID' => $orderID));
		// if latest sale version with converted module sale, than check items
		$resBasketItems = CSaleBasket::GetList(array('SORT' => 'DESC'), array(/*'FUSER_ID' => $basketUserID,*/ 'LID' => SITE_ID, 'ORDER_ID' => $orderID), false, false, array('ID', 'QUANTITY', 'PRODUCT_ID', 'TYPE', 'SET_PARENT_ID'));
		while($arBasketItem = $resBasketItems->Fetch()){
			if($arBasketItem['ID'] == $productBasketID){
				$product_id=$arBasketItem['PRODUCT_ID'];
			}
			if($arBasketItem['ID'] != $productBasketID){
				$bSetItem = CSaleBasketHelper::isSetItem($arBasketItem);
				if($bSetItem && $arBasketItem['SET_PARENT_ID'] == $productBasketID) // set item
					continue;

				// get props
				$arProps = array();
				$dbRes = CSaleBasket::GetPropsList(array(), array('BASKET_ID' => $arBasketItem['ID']));
				while($arProp = $dbRes->Fetch()){
				   $arProps[] = $arProp;
				}

				// delete from order
				CSaleBasket::Delete($arBasketItem['ID']);

				// add to basket again
				if(!$bSetItem  && $product_id!=$arBasketItem['PRODUCT_ID'] && !$user_registered){
					Add2BasketByProductID($arBasketItem['PRODUCT_ID'], $arBasketItem['QUANTITY'], array(), $arProps);
				}
			}
			
		}
		
		if(!checkNewVersionExt('sale')){
			// fix bug with DELIVERY_PRICE, when count of products more than one (bitrix bug with delivery price)
			$arUpdateFields = array('PRICE' => $newOrder['PRICE'], 'PRICE_DELIVERY' => 0);
			if(class_exists('\Bitrix\Sale\Internals\OrderTable')){
				\Bitrix\Sale\Internals\OrderTable::update($orderID, $arUpdateFields);

				// fix bug with payment SUM, when buy set
				if(class_exists('\Bitrix\Sale\Internals\PaymentTable')){
					$res = \Bitrix\Sale\Internals\PaymentTable::getList(array('order' => array('ID' => 'ASC'), 'filter' => array('ORDER_ID' => $orderID)));
					if($payment = $res->fetch()){
						\Bitrix\Sale\Internals\PaymentTable::update($payment['ID'], array('SUM' => $newOrder['PRICE']));
					}
				}
			}
			else{
				CSaleOrder::Update($orderID, $arUpdateFields);
			}
		}
	}
}

if($user_registered){
	$USER->Logout();
	if(!$USER->IsAuthorized() && $arBasketItemsAll && !$bAllBasketBuy){
		foreach($arBasketItemsAll as $arItem){
			// get props
			$arProps = array();
			if($arItem['BASKET_PROPS']){
				$arProps=$arItem['BASKET_PROPS'];
			}
			Add2BasketByProductID($arItem['PRODUCT_ID'], $arItem['QUANTITY'], array(), $arProps);
		}
	}
}

\Bitrix\Main\Loader::IncludeModule('aspro.optimus');
COptimus::clearBasketCounters();
COptimusCache::ClearCacheByTag('sale_basket');

// add order properties
$personType = intval($_POST['PERSON_TYPE_ID']) > 0 ? $_POST['PERSON_TYPE_ID']: 1;
$res = CSaleOrderProps::GetList(array(), array('@CODE' => unserialize($_POST["PROPERTIES"]), 'PERSON_TYPE_ID' =>$personType));

while($prop = $res->Fetch()){
	if($_POST['ONE_CLICK_BUY'][$prop['CODE']]){
		$dbP = CSaleOrderPropsValue::GetList(Array(),array('ORDER_ID' => $orderID, 'ORDER_PROPS_ID' => $prop['ID']));
		if($arP = $dbP->Fetch()){
			CSaleOrderPropsValue::Update($arP['ID'], array( 'VALUE' => $_POST['ONE_CLICK_BUY'][$prop['CODE']]));
		}else{
			CSaleOrderPropsValue::Add(array('ORDER_ID' => $orderID, 'NAME' => $prop['NAME'], 'ORDER_PROPS_ID' => $prop['ID'], 'CODE' => $prop['CODE'], 'VALUE' => $_POST['ONE_CLICK_BUY'][$prop['CODE']]));
		}
	}
}

// send mail
if($orderID){
	$orderPrice = 0;
	$orderList = '';
	$arCurrency = CCurrencyLang::GetByID($newOrder['CURRENCY'], LANGUAGE_ID);
	$currencyThousandsSep = (!$arCurrency["THOUSANDS_VARIANT"] ? $arCurrency["THOUSANDS_SEP"] : ($arCurrency["THOUSANDS_VARIANT"] == "S" ? " " : ($arCurrency["THOUSANDS_VARIANT"] == "D" ? "." : ($arCurrency["THOUSANDS_VARIANT"] == "C" ? "," : ($arCurrency["THOUSANDS_VARIANT"] == "B" ? "\xA0" : "")))));

	$arSelFields = array("ID", "PRODUCT_ID", "QUANTITY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "CURRENCY", "DISCOUNT_PRICE", "TYPE", "SET_PARENT_ID", "DETAIL_PAGE_URL");
	$resBasketItems = CSaleBasket::GetList(array('SORT' => 'DESC'), array(/*'FUSER_ID' => $basketUserID,*/ 'LID' => SITE_ID, 'ORDER_ID' => $orderID), false, false, $arSelFields);
	while($arBasketItem = $resBasketItems->Fetch()){
		if(CSaleBasketHelper::isSetItem($arBasketItem)) // set item
			continue;

		if($arBasketItem['CAN_BUY'] === 'Y'){
			$curPrice = roundEx($arBasketItem['PRICE'], SALE_VALUE_PRECISION) * DoubleVal($arBasketItem['QUANTITY']);
			$orderPrice += $curPrice;
			$orderList .= GetMessage('ITEM_NAME') . $arBasketItem['NAME']
				. GetMessage('ITEM_PRICE') . str_replace('#', number_format($arBasketItem['PRICE'], $arCurrency["DECIMALS"], $arCurrency["DEC_POINT"], $currencyThousandsSep), $arCurrency['FORMAT_STRING'])
				. GetMessage('ITEM_QTY') . intval($arBasketItem['QUANTITY'])
				. GetMessage('ITEM_TOTAL') . str_replace('#', number_format($curPrice, $arCurrency["DECIMALS"], $arCurrency["DEC_POINT"], $currencyThousandsSep), $arCurrency['FORMAT_STRING']) . "\n";
		}
	}

	$arOrderQuery=CSaleOrder::GetList(array(), array("ID"=>$orderID), false, false, array("ID", "ACCOUNT_NUMBER", "PRICE"))->Fetch();

	$arMessageFields = array(
		"RS_ORDER_ID" => $orderID,
		"CLIENT_NAME" => $_POST['ONE_CLICK_BUY']['FIO'],
		"ACCOUNT_NUMBER" => $arOrderQuery["ACCOUNT_NUMBER"],
		"PHONE" => $_POST["ONE_CLICK_BUY"]["PHONE"],
		"ORDER_ITEMS" => $orderList,
		"ORDER_PRICE" => str_replace('#', number_format(($arOrderQuery["PRICE"] ? $arOrderQuery["PRICE"] : $orderPrice), $arCurrency["DECIMALS"], $arCurrency["DEC_POINT"], $currencyThousandsSep), $arCurrency['FORMAT_STRING']),
		"COMMENT" => $_POST['ONE_CLICK_BUY']['COMMENT'],
		"RS_DATE_CREATE" => ConvertTimeStamp(false, "FULL"),
	);
	if($_POST['ONE_CLICK_BUY']['EMAIL']){
		$arMessageFields["EMAIL_BUYER"]=$_POST['ONE_CLICK_BUY']['EMAIL'];
	}

	CEvent::Send("NEW_ONE_CLICK_BUY", SITE_ID, $arMessageFields);
}

$_SESSION['SALE_BASKET_NUM_PRODUCTS'][SITE_ID] = 0;

/*bind sale events*/
foreach(GetModuleEvents("sale", "OnSaleComponentOrderOneStepComplete", true) as $arEvent)
	ExecuteModuleEventEx($arEvent, Array($orderID, $arOrder, $arParams));

ob_clean();

die(getJson($orderID, 'Y'));
?>