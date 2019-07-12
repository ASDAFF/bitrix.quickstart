<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require(dirname(__FILE__)."/lang/" . LANGUAGE_ID . "/script.php");
if (!CModule::IncludeModule('sale')
	|| !CModule::IncludeModule('iblock')
	|| !CModule::IncludeModule('catalog')
	|| !CModule::IncludeModule('currency'))
	die();

if (!function_exists('json_encode')) {
	function json_encode($array) {
		if (!is_array($array) || empty($array)) return '';
		$json_string = '{';
		foreach ($array as $k=>$v)
			$json_string .= '"' . $k . '":"' . str_replace('"', '\"', $v) . '",';
		$json_string = substr($json_string, 0, strlen($json_string)-1).'}';
		return $json_string;
	}
}

function parseFIOString($fio_string) {
	$fio_string = trim($fio_string);
	if (!strpos($fio_string, ' ')) return array($fio_string);
	$fio_parts = explode(' ', $fio_string);
	return array($fio_parts[0], $fio_parts[1]);
}

function getResultJsonArray($msg_string, $ok='N', $err_string='')  {
	global $APPLICATION;
	$result = array(
		'ok' => $ok=='Y'?'Y':'N',
		'msg' => $APPLICATION->ConvertCharset($msg_string, SITE_CHARSET, 'utf-8')
	);
	if (strlen($err_string) > 0)
		$result['err'] = $APPLICATION->ConvertCharset($err_string, SITE_CHARSET, 'utf-8');
	return json_encode($result);
}

function getSKUOfferParentDetailUrl($iblock_id, $element_id) {
	$arCatalog = CCatalog::GetByID($iblock_id);
	if (is_array($arCatalog)
		&& 0 < intval($arCatalog['PRODUCT_IBLOCK_ID'])
		&& 0 < intval($arCatalog['SKU_PROPERTY_ID'])) {
		$dbSKUProp = CIBlockElement::GetProperty(
			$iblock_id,
			$element_id,
			array(),
			array('ID' => $arCatalog['SKU_PROPERTY_ID'])
		);
		if ($arSKUProp = $dbSKUProp->Fetch()) {
			if (0 < intval($arSKUProp['VALUE'])) {
				$db_parent = CIBlockElement::GetByID($arSKUProp['VALUE']);
				$ar_parent = $db_parent->GetNext();
				return !empty($ar_parent['DETAIL_PAGE_URL'])? $ar_parent['DETAIL_PAGE_URL']: '';
			}
		}
	}
	return '';
}

header('Content-type: application/json; charset=utf-8');
if (isset($_POST['buyMode'])) {
	global $USER, $APPLICATION;

    $register_new_user = $send_letter = false;

	if (isset($_POST['antispam_check']) && !isset($_POST['antispam']))
		die(getResultJsonArray(GetMessage('1CB_ANTISPAM')));

	if (empty($_POST['new_order']['FIO']))
		die(getResultJsonArray(GetMessage('1CB_NO_FIO')));
	$_POST['new_order']['FIO'] = $APPLICATION->ConvertCharset($_POST['new_order']['FIO'], 'utf-8', SITE_CHARSET);

	if (!empty($_POST['new_order']['PHONE'])
		&& !preg_match('/^[+0-9\-\(\)\s]+$/', $_POST['new_order']['PHONE']))
		die(getResultJsonArray(GetMessage('1CB_NO_PHONE')));

	if (!empty($_POST['new_order']['EMAIL'])
		&& !preg_match('/^[0-9a-zA-Z\-_\.]+@[0-9a-zA-Z\-]+[\.]{1}[0-9a-zA-Z\-]+[\.]?[0-9a-zA-Z\-]+$/', $_POST['new_order']['EMAIL']))
		die(getResultJsonArray(GetMessage('1CB_BAD_EMAIL_FORMAT')));

	if (strlen($_POST['currencyCode']) != 3)
		$_POST['currencyCode'] = COption::GetOptionString('sale', 'default_currency', 'RUB');
	$currencyData = CCurrencyLang::GetByID($_POST['currencyCode'], LANGUAGE_ID);
	if (!$currencyData)
		die(getResultJsonArray(GetMessage('1CB_CURRENCY_NOT_FOUND')));
	$currencyName = $currencyData['FORMAT_STRING'];

	$basketUserID = CSaleBasket::GetBasketUserID();

	if (!$USER->IsAuthorized()) {
		if (!isset($_POST['new_order']['EMAIL'])
			|| $_POST['new_order']['EMAIL'] == '') {
			$login = 'user_' . (microtime(true) * 100);
			$server_name = 0 < strlen(SITE_SERVER_NAME)? 
				SITE_SERVER_NAME : 'server.com';
			$_POST['new_order']['EMAIL'] = $login . '@' . $server_name;
			$register_new_user = true;
		} else {
			$dbUser = CUser::GetList(($by = 'ID'), ($order = 'ASC'), array('=EMAIL' => $_POST['new_order']['EMAIL']));
			if ($dbUser->SelectedRowsCount() == 0) {
				$login = 'user_' . (microtime(true) * 100);
				$register_new_user = true;
			} elseif ($dbUser->SelectedRowsCount() == 1) {
				$ar_user = $dbUser->Fetch();
				$registeredUserID = $ar_user['ID'];
			} else
				die(getResultJsonArray(GetMessage('1CB_TOO_MANY_USERS')));
			$send_letter = true;
		}

		if ($register_new_user) {
			$use_captcha = COption::GetOptionString('main', 'captcha_registration', 'N');
			if ($use_captcha == 'Y')
				COption::SetOptionString('main', 'captcha_registration', 'N');
			$userPassword = randString(10);
			$userFIO = parseFIOString($_POST['new_order']['FIO']);
			$newUser = $USER->Register($login, $userFIO[0], $userFIO[1],
				$userPassword,  $userPassword,$_POST['new_order']['EMAIL']);
			if ($use_captcha == 'Y')
				COption::SetOptionString('main', 'captcha_registration', 'Y');
			if ($newUser['TYPE'] == 'ERROR') {
				die(getResultJsonArray(GetMessage('1CB_USER_REGISTER_FAIL'), 'N', $newUser['MESSAGE']));
			} else {
				$registeredUserID = $USER->GetID();
				if (!empty($_POST['new_order']['PHONE']))
					$userUpd = $USER->Update($registeredUserID, 
						array('PERSONAL_PHONE' => $_POST['new_order']['PHONE']));
				$USER->Logout();
			}
		}
	} else {
		$send_letter = true;
		$registeredUserID = $USER->GetID();
	}

	$newOrder = array(
		'LID' => SITE_ID,
		'PERSON_TYPE_ID' => intval($_POST['personTypeId']) > 0? $_POST['personTypeId']: 1,
		'PAYED' => 'N',
		'CURRENCY' => $_POST['currencyCode'],
		'USER_ID' => $registeredUserID
	);
	if (0 < $_POST['deliveryId'])
		$newOrder['DELIVERY_ID'] = $_POST['deliveryId'];
	if (0 < $_POST['paysystemId'])
		$newOrder['PAY_SYSTEM_ID'] = $_POST['paysystemId'];
	$newOrder['COMMENTS'] = GetMessage('1CB_ORDER_COMMENT');
	$newOrderID = CSaleOrder::Add($newOrder);

	if ($newOrderID == false) {
		$strError = '';
		if($ex = $APPLICATION->GetException())
			$strError = $ex->GetString();
		die(getResultJsonArray(GetMessage('1CB_ORDER_CREATE_FAIL'), 'N', $strError));
	}

	$db_basket_items = CSaleBasket::GetList(
		array('SORT' => 'DESC'),
		array('FUSER_ID' => $basketUserID, 'LID' => SITE_ID,
			'ORDER_ID' => 'NULL', 'DELAY' => 'N')
	);
	$addProduct = true;
	$orderPrice = 0;
	$currency = $orderList = '';
	if ($_POST['buyMode'] == 'ALL') {
		while ($ar_tmp = $db_basket_items->Fetch()) {
			if ($ar_tmp['PRODUCT_ID'] == $_POST['itemId'])
				$addProduct = false;
			if ($ar_tmp['CAN_BUY'] == 'Y') {
				if ($ar_tmp['CURRENCY'] != $_POST['currencyCode'])
					$ar_tmp['PRICE'] = CCurrencyRates::ConvertCurrency($ar_tmp['PRICE'], $ar_tmp['CURRENCY'], $_POST['currencyCode']);
				CSaleBasket::Update(
					$ar_tmp['ID'],
					array('ORDER_ID' => $newOrderID, 'PRICE' => $ar_tmp['PRICE'], 'FUSER_ID' => $registeredUserID)
				);
				$curPrice = roundEx($ar_tmp['PRICE'], SALE_VALUE_PRECISION) * DoubleVal($ar_tmp['QUANTITY']);
				$orderPrice += $curPrice;
				$orderList .= GetMessage('ITEM_NAME') . $ar_tmp['NAME']
					. GetMessage('ITEM_PRICE') . str_replace('#', $ar_tmp['PRICE'], $currencyName)
					. GetMessage('ITEM_QTY') . $ar_tmp['QUANTITY']
					. GetMessage('ITEM_TOTAL') . str_replace('#', $curPrice, $currencyName) . "\n";
			}
		}
	} //else
	//	CSaleBasket::DeleteAll($basketUserID);
  
	if ($_POST['itemId']>0 && $addProduct) {
		$db_product = CIBlockElement::GetByID($_POST['itemId']);
		$arProduct = $db_product->GetNext();
		if ($useSku)
			$arProduct['DETAIL_PAGE_URL'] = getSKUOfferParentDetailUrl($arProduct['IBLOCK_ID'], $_POST['itemId']);

		$dbPrice = CPrice::GetList(
			array(),
			array(
				'PRODUCT_ID' => $_POST['itemId'],
				'CATALOG_GROUP_ID' => $_POST['priceId']
			)
		);
		if ($dbPrice->SelectedRowsCount() != 1)
			die(getResultJsonArray(GetMessage('1CB_PRODUCT_PRICE_NOT_FOUND')));
		$arPrice = $dbPrice->Fetch();
		$arProps = array();
		$iblockID = intval($_POST['iblockId']);
		$product_desc_string = '';
		$useSku = (isset($_POST['useSku']) && $_POST['useSku']=='Y');

		if ($useSku && 0 < $iblockID) {
			$skuCodes = explode('|', $_POST['skuCodes']);
			if (is_array($skuCodes)) {
				foreach ($skuCodes as $k => $v)
					if ($v === '')
						unset($skuCodes[$k]);

				if (!empty($skuCodes))
					$arProps = CIBlockPriceTools::GetOfferProperties(
						$_POST['itemId'], $iblockID, $skuCodes);
			}
		}

		$is_vers_twelve = 11 < intval(substr(SM_VERSION, 0, 2));
		
		if ($is_vers_twelve) 
			$add = Add2BasketByProductID($_POST['itemId'], 1, 
				array('ORDER_ID' => $newOrderID), $arProps);
		else
			$add = Add2BasketByProductID($_POST['itemId'], 1, $arProps);

		if (!$add) {
			$strError = '';
			if($ex = $APPLICATION->GetException())
				$strError = $ex->GetString();
			die(getResultJsonArray(GetMessage('1CB_ITEM_ADD_FAIL'), 'N', $strError));
		} else {
			if (!$is_vers_twelve) {
				$upd_res = CSaleBasket::Update($add, array('ORDER_ID' => $newOrderID));
				if (!$upd_res) {
					$strError = '';
					if($ex = $APPLICATION->GetException())
						$strError = $ex->GetString();
					die(getResultJsonArray(GetMessage('1CB_ITEM_UPDATE_FAIL'), 'N', $strError));
				}
			}
			$ar_basket_item = CSaleBasket::GetByID($add);
			if ($ar_basket_item['CURRENCY'] != $_POST['currencyCode'])
				$ar_basket_item['PRICE'] = CCurrencyRates::ConvertCurrency(
					$ar_basket_item['PRICE'], $ar_basket_item['CURRENCY'], $_POST['currencyCode']);
			$orderPrice += roundEx($ar_basket_item['PRICE'], SALE_VALUE_PRECISION);
			$orderList .= GetMessage('ITEM_NAME') . $arProduct['NAME'] . $product_desc_string
				. GetMessage('ITEM_PRICE') . str_replace('#', $ar_basket_item['PRICE'], $currencyName)
				. GetMessage('ITEM_QTY') . '1'
				. GetMessage('ITEM_TOTAL') . str_replace('#', $ar_basket_item['PRICE'], $currencyName) . "\n";
		}
	}

	$phone_prop_id = (defined('ONECLICKBUY_PHONE_PROP_ID') && 0 < intval(ONECLICKBUY_PHONE_PROP_ID))? 
		intval(ONECLICKBUY_PHONE_PROP_ID): 3;
	$email_prop_id = (defined('ONECLICKBUY_EMAIL_PROP_ID') && 0 < intval(ONECLICKBUY_EMAIL_PROP_ID))? 
		intval(ONECLICKBUY_EMAIL_PROP_ID): 2;
	$db_props = CSaleOrderProps::GetList(array(),
		array('@ID' => array($phone_prop_id, $email_prop_id)));

	while ($row = $db_props->Fetch())
		CSaleOrderPropsValue::Add(array(
			'ORDER_ID' => $newOrderID,
			'NAME' => $row['NAME'],
			'ORDER_PROPS_ID' => $row['ID'],
			'CODE' => $row['CODE'],
			'VALUE' => $_POST['new_order'][$row['ID'] == $email_prop_id? 'EMAIL' : 'PHONE']
		));

	$_SESSION['SALE_BASKET_NUM_PRODUCTS'][SITE_ID] = 0;
	$orderUpdate = CSaleOrder::Update($newOrderID, 
		array('PRICE' => $orderPrice));

	$email = '';
	$bcc = array();
	$duplicate = 'N';
	if (isset($_POST['dubLetter']) && 0 < strlen($_POST['dubLetter'])) {
		if (strpos($_POST['dubLetter'], 'a') !== false) {
			$admin_email = COption::GetOptionString('main', 'email_from', '');
			if (!empty($admin_email))
				$bcc[] = $admin_email;
		}

		if (strpos($_POST['dubLetter'], 's') !== false) {
			$sales_email = COption::GetOptionString('sale', 'order_email', '');
			if (!empty($sales_email))
				$bcc[] = $sales_email;
		}

		if (strpos($_POST['dubLetter'], 'd') !== false) {
			$dub_email = COption::GetOptionString('main', 'all_bcc', '');
			if (!empty($dub_email))
				$duplicate = 'Y';
		}
	}
	$bcc = array_unique($bcc);

	if ($send_letter) {
		$email = $_POST['new_order']['EMAIL'];
	} else {
		if (empty($bcc)) {
			if ($duplicate == 'Y')
				$email = COption::GetOptionString('main', 'all_bcc', '');
		} else
			$email = array_shift($bcc);
	}

	if (strlen($email) > 0)	{
		$letterFields = array(
			'ORDER_ID' => $newOrderID,
			'ORDER_DATE' => date('d.m.Y'),
			'ORDER_USER' => $_POST['new_order']['FIO'],
			'PRICE' => str_replace('#', $orderPrice, $currencyName),
			'EMAIL' => $email,
			'BCC' => !empty($bcc)? implode(',', $bcc) : '',
			'ORDER_LIST' => $orderList,
			'SALE_EMAIL' => COption::GetOptionString('sale', 
				'order_email', 'sales@' . SITE_SERVER_NAME)
		);

		// Event "OnOrderNewSendEmail" processing
		$eventName = 'SALE_NEW_ORDER';
		$send_letter = true;
		$db_events = GetModuleEvents('sale', 'OnOrderNewSendEmail');
		while ($arEvent = $db_events->Fetch())
			if (ExecuteModuleEventEx($arEvent, array($newOrderID, &$eventName, &$letterFields)) === false)
				$send_letter = false;

		if ($send_letter)
			CEvent::SendImmediate($eventName, SITE_ID, $letterFields, $duplicate);
	}
	die(getResultJsonArray(GetMessage('1CB_EMPTY_BASKET'), 'Y'));
}
die(getResultJsonArray(GetMessage('1CB_NO_PROPER_DATA')));?>
