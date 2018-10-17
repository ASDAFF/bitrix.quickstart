<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
global $USER;
//if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')

if(!CModule::IncludeModule("iblock") or !CModule::IncludeModule("sale") or !CModule::IncludeModule("catalog") or !CModule::IncludeModule("mlife.bistroklick")) return;

$error = array();

foreach($arParams['FIELD_SHOW'] as $value) {
	$send[$value] = '';
	$send_req[$value] = 0;
}

if(is_array($arParams['FIELD_REQ']) && count($arParams['FIELD_REQ'])>0) {
	foreach($arParams['FIELD_REQ'] as $value) {
		$send_req[$value] = 1;
	}
}else{
	$send_req = array('phone'=>1,'email'=>1,'name'=>1,'mess'=>1);
}
$arResult['SEND_REQ'] = $send_req;

$arResult['SHOW_KAPCHA'] = 0;
if($arParams['SHOW_KAPCHA']==0) {
	$arResult['SHOW_KAPCHA'] = 0;
}elseif($arParams['SHOW_KAPCHA']==1) {
	$arResult['SHOW_KAPCHA'] = 1;
}elseif($arParams['SHOW_KAPCHA']==2 && !$USER->IsAuthorized()) {
	$arResult['SHOW_KAPCHA'] = 1;
}

//ВАЛИДАЦИЯ ДАННЫХ ФОРМЫ
if(intval($_REQUEST["name_bk"])==1){

	//получаем значения label
	$dbResultList = CSaleOrderProps::GetList(
		array(),
		array(),
		false,
		false,
		array("ID","NAME")
	);
	while ($arR = $dbResultList->Fetch()) {
		$arResult['LABEL']['addfield_'.$arR["ID"]] = $arR["NAME"];
	}

	if(!check_bitrix_sessid('bistrclick_sessid')) {
		$error['bistrclick_sessid'] = GetMessage('MLIFE_CAT_BK_FIELD_ERROR_SESS');
	}
	
	foreach($arResult['SEND_REQ'] as $key=>$value) {
		if($value==1 && !trim($_REQUEST[$key])) {
			if(isset($arResult['LABEL'][$key])) {
				$error[$key] = $arResult['LABEL'][$key].' '.GetMessage("MLIFE_CAT_BK_FIELD_ERROR_REQ");
			}else{
				$error[$key] = GetMessage("MLIFE_CAT_BK_FIELD_ERROR_".strtoupper($key));
			}
		}
	}
	 
	if ($arResult['SHOW_KAPCHA']==1 && !$GLOBALS["APPLICATION"]->CaptchaCheckCode($_REQUEST['cap'], $_REQUEST['captcha_sid'])) {
		$error['cap'] = GetMessage("MLIFE_CAT_BK_FIELD_ERROR_CAPCHA");
	}
	
	foreach($arParams['FIELD_SHOW'] as $key=>$value) {
		if($value=='phone') {
			if(CMlifeBistrockick::getname($_REQUEST[$value])) {
				$send[$value] = CMlifeBistrockick::getname($_REQUEST[$value]);
				unset($error[$value]);
				if(CModule::IncludeModule("mlife.smsservices")) {
					$obSmsServ = new CMlifeSmsServices();
					$phoneCheck = $obSmsServ->checkPhoneNumber($send[$value]);
					$send[$value] = $phoneCheck[$value];
					if(!$phoneCheck['check']) {
						$error[$value] = GetMessage("MLIFE_CAT_BK_FIELD_ERROR_PHONE2");
					}
				}
			}
		}elseif($value=='email') {
			$requlEmail = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD";
			if(CMlifeBistrockick::getname($_REQUEST[$value]) && !preg_match($requlEmail,CMlifeBistrockick::getname($_REQUEST[$value]))) {
				$error[$value] = GetMessage("MLIFE_CAT_BK_FIELD_ERROR_EMAIL2");
				$send[$value] = CMlifeBistrockick::getname($_REQUEST[$value]);
			}elseif(CMlifeBistrockick::getname($_REQUEST[$value])) {
				$send[$value] = CMlifeBistrockick::getname($_REQUEST[$value]);
				unset($error[$value]);
			}
		}else {
			if(CMlifeBistrockick::getname($_REQUEST[$value])) {
				$send[$value] = CMlifeBistrockick::getname($_REQUEST[$value]);
				unset($error[$value]);
			}
		}
	}
	
	$send['delivery'] = CMlifeBistrockick::getname($_REQUEST['delivery']);
	$send['paysystem'] = CMlifeBistrockick::getname($_REQUEST['paysystem']);

$arResult['SEND'] = $send;
$arResult['ERROR'] = $error;
}

$showform = 0;
if($_REQUEST["formclick"]) $showform = intval($_REQUEST["formclick"]);
$arResult['SHOW_FORM'] = $showform;
$arResult['REF_START'] = $APPLICATION->GetCurPage();

$product_id = intval($_REQUEST["pr_id"]);
if($product_id>0) {
	$arResult['PRODUCT_ID'] = $product_id;
}
else{
	$showform = 0;
}
//mprint($arParams);
if($showform) {

	//получаем значения label
	$dbResultList = CSaleOrderProps::GetList(
		array(),
		array(),
		false,
		false,
		array("ID","NAME","IS_LOCATION")
	);
	while ($arR = $dbResultList->Fetch()) {
		$arResult['LABEL']['addfield_'.$arR["ID"]] = $arR["NAME"];
		if($arR['IS_LOCATION']=='Y') $arResult['LOC_ID'] = $arR["ID"];
	}
	
	//получаем названия доставки
	$db_dtype = CSaleDelivery::GetList(
		array(
				"SORT" => "ASC",
				"NAME" => "ASC"
			),
		array(
				"ACTIVE" => "Y",
				"ID" => $arParams["FIELD_DELIVERY"]
			),
		false,
		false,
		array()
	);
	while ($ar_dtype = $db_dtype->Fetch())
	{
		$arResult['DELIVERY_NAME'][$ar_dtype["ID"]] = $ar_dtype["NAME"];
	}
	
	//получаем название оплаты
	$db_ptype = CSalePaySystem::GetList(Array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), Array("ID" => $arParams["FIELD_PAYSYSTEM"]));
	while ($ptype = $db_ptype->Fetch())
	{
		$arResult['PAYSYSTEM_NAME'][$ptype["ID"]] = $ptype["NAME"];
	}

	$arResult['IBLOCK_ID'] = intval($arParams['IBLOCK_ID']);

	$key=trim($arParams['KEY']);//ключ для доступа к форме
	$referer_start = trim($_REQUEST['referer']); //хеш адреса страницы с которого запущена форма
	$date_ref=strtotime(date("M-d-Y H:00:00"));
	$ref1 = CMlifeBistrockick::getref($date_ref,$key,$arResult['REF_START'],1);
	$ref2 = CMlifeBistrockick::getref($date_ref,$key,$arResult['REF_START'],2);

	$arResult['CHECK_SPAM'] = CMlifeBistrockick::checkspam($referer_start, $ref1, $ref2);

	if($arResult['SHOW_FORM']==1 && $arResult['CHECK_SPAM']!=1) {
			global $USER;
			
			//товар
			$arFilter = array('IBLOCK_ID' => $arResult['IBLOCK_ID'], 'ID' => $arResult['PRODUCT_ID'], 'ACTIVE' => 'Y');
			$rsProduct = CIBlockElement::GetList(array(),$arFilter,false,false,array());
			
			//добавляем пользовательские данные в форму если пользователь авторизирован
			$uid = intval($USER->GetID());
			if($uid > 0) {
				$rsUser = CUser::GetByID($uid);
				$arUser = $rsUser->Fetch();
				if($arUser['EMAIL'] && !$arResult['SEND']['email']) $arResult['SEND']['email'] = $arUser['EMAIL'];
				if($arUser['NAME'] && !$arResult['SEND']['name']) $arResult['SEND']['name'] = $arUser['NAME'];
				if(isset($arUser['PERSONAL_MOBILE']) && strlen($arUser['PERSONAL_MOBILE'])>0 && !$arResult['SEND']['phone']) {
					$arResult['SEND']['phone'] = $arUser['PERSONAL_MOBILE'];
				}
				elseif(isset($arUser['PERSONAL_PHONE']) && strlen($arUser['PERSONAL_PHONE'])>0 && !$arResult['SEND']['phone']) {
					$arResult['SEND']['phone'] = $arUser['PERSONAL_PHONE'];
				}
				
			}
			
			while($ob = $rsProduct->GetNextElement())
			{
			$arResult['TOVAR'] = $ob->GetFields();
			continue;
			}
			$arFilterPrice = array();
			$arFilterPrice['PRODUCT_ID'] = $arResult['PRODUCT_ID'];
			if(is_array($arParams['PRICE_CODE'])) {
				$arFilterPrice['CATALOG_GROUP_ID'] = $arParams['PRICE_CODE'];
			}
			$dbPrice = CPrice::GetList(
				array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", 
				  "SORT" => "ASC"),
				$arFilterPrice,
				false,
				false,
				array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", 
				  "QUANTITY_FROM", "QUANTITY_TO")
			);
			while ($arPrice = $dbPrice->Fetch())
			{
				$arDiscounts = CCatalogDiscount::GetDiscountByPrice(
					$arPrice["ID"],
					$USER->GetUserGroupArray(),
					"N",
					SITE_ID
				);
				$discountPrice = CCatalogProduct::CountPriceWithDiscount(
					$arPrice["PRICE"],
					$arPrice["CURRENCY"],
					$arDiscounts
				);
				$arPrice["DISCOUNT_PRICE"] = $discountPrice;
				

				$arResult['TOVAR']['PRICES'][] = $arPrice;
			}
			if(is_array($arResult['TOVAR']['PRICES']) && count($arResult['TOVAR']['PRICES'])>1) {
				$price_min = 10000000000000;
				$mainprice = false;
				foreach($arResult['TOVAR']['PRICES'] as $price) {
						$pr = CCurrencyRates::ConvertCurrency($price['DISCOUNT_PRICE'], $price['CURRENCY'], $arParams['CURRENCY_ID']);
						if($pr && $price_min>$pr) {
							$price_min = $pr;
							$mainprice = $price;
						}
				}
				if($mainprice) {
					unset($arResult['TOVAR']['PRICES']);
					$arResult['TOVAR']['PRICES'][] = $mainprice;
				}
			}
			
			//торговые предложения
			$arCatalog = CCatalog::GetByIDExt($arParams['IBLOCK_ID']);
			$arFilter = array('IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'], 'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $arResult['PRODUCT_ID'], 'ACTIVE' => 'Y');
			$rsOffers = CIBlockElement::GetList(array(),$arFilter,false,false,array());
			$i=0;
			while($ob = $rsOffers->GetNextElement())
			{
			$arResult['OFFERS'][$i] = $ob->GetFields();
			$arResult['OFFERS'][$i]['PROP'] = $ob->GetProperties();
			$arFilterPrice = array();
			$arFilterPrice['PRODUCT_ID'] = $arResult['OFFERS'][$i]['ID'];
			if(is_array($arParams['PRICE_CODE'])) {
				$arFilterPrice['CATALOG_GROUP_ID'] = $arParams['PRICE_CODE'];
			}
			$dbPrice = CPrice::GetList(
				array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", 
				  "SORT" => "ASC"),
				$arFilterPrice,
				false,
				false,
				array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", 
				  "QUANTITY_FROM", "QUANTITY_TO")
			);
			while ($arPrice = $dbPrice->Fetch())
			{
				$arDiscounts = CCatalogDiscount::GetDiscountByPrice(
					$arPrice["ID"],
					$USER->GetUserGroupArray(),
					"N",
					SITE_ID
				);
				$discountPrice = CCatalogProduct::CountPriceWithDiscount(
					$arPrice["PRICE"],
					$arPrice["CURRENCY"],
					$arDiscounts
				);
				$arPrice["DISCOUNT_PRICE"] = $discountPrice;
				$arResult['OFFERS'][$i]['PRICES'][] = $arPrice;
				
			}
			
			if(is_array($arResult['OFFERS'][$i]['PRICES']) && count($arResult['OFFERS'][$i]['PRICES'])>1) {
				$price_min = 10000000000000;
				$mainprice = false;
				foreach($arResult['OFFERS'][$i]['PRICES'] as $price) {
						$pr = CCurrencyRates::ConvertCurrency($price['DISCOUNT_PRICE'], $price['CURRENCY'], $arParams['CURRENCY_ID']);
						if($pr && $price_min>$pr) {
							$price_min = $pr;
							$mainprice = $price;
						}
				}
				if($mainprice) {
					unset($arResult['OFFERS'][$i]['PRICES']);
					$arResult['OFFERS'][$i]['PRICES'][] = $mainprice;
				}
			}
			
			$i++;
			}
			//тут получаем максимальную и минимальные цены товара
			$price_min = 10000000000000;
			$price_max = 0;
			if(is_array($arResult['OFFERS'])) {
				foreach($arResult['OFFERS'] as $offer) {
					if(is_array($offer['PRICES'])) {
						foreach ($offer['PRICES'] as $price) {
							$pr = CCurrencyRates::ConvertCurrency($price['DISCOUNT_PRICE'], $price['CURRENCY'], $arParams['CURRENCY_ID']);
							if($price_max<$pr) $price_max = $pr;
							if($price_min>$pr) $price_min = $pr;
						}
					}
				}
			}
			if(is_array($arResult['TOVAR']['PRICES'])){
				foreach($arResult['TOVAR']['PRICES'] as $price) {
					$pr = CCurrencyRates::ConvertCurrency($price['DISCOUNT_PRICE'], $price['CURRENCY'], $arParams['CURRENCY_ID']);
					if($price_max<$pr) $price_max = $pr;
					if($price_min>$pr) $price_min = $pr;
				}
			}
			if($price_min<$price_max) {
				$arResult['PRICE_MIN'] = $price_min;
				$arResult['PRICE_MAX'] = $price_max;
			}
	//mprint($arResult);die();		
	}
}

//если данные валидны
if(count($arResult['ERROR'])==0 && intval($_REQUEST["name_bk"])==1) {

	//получаем значения label
	$dbResultList = CSaleOrderProps::GetList(
		array(),
		array(),
		false,
		false,
		array("ID","NAME")
	);
	while ($arR = $dbResultList->Fetch()) {
		$arResult['LABEL']['addfield_'.$arR["ID"]] = $arR["NAME"];
	}

	$product_id = intval($_REQUEST["pr_id"]);
	
	//данные о товаре
	$offer_code = intval($_REQUEST['offer']);
	//mprint($offer_code);
	$arProduct = CMlifeBistrockick::formatTovar($product_id,$arResult,$arProduct,$offer_code);
	$arEventUser = array();
	//запись заказа в базу
	if($arParams['CREATE_ORDER']=='Y'){

		$newuser = false;
		global $USER;
		//если авторизирован, получаем ид пользователя
		if(intval($USER->GetID())>0) $newuser = intval($USER->GetID());
		
		//ищем пользователя по его email
		if($arParams['CHECK_USER']=='Y' && !$newuser && $arResult['SEND']['email']){
			$newuser = CMlifeBistrockick::getuserforEmail($arResult);
		}
		
		//если пользователь пуст делаем нового
		if(!$newuser && $arParams['CREATE_USER']=='CUR') {

			$prefix = 'user_';
			if($arParams['USER_PREFIX']) $prefix = $arParams['USER_PREFIX'];
			if(!isset($arResult['SEND']['email']) || (isset($arResult['SEND']['name']) && !$arResult['SEND']['name'])) $arResult['SEND']['name'] = $prefix.time();
			if(!isset($arResult['SEND']['email']) || (isset($arResult['SEND']['email']) && !$arResult['SEND']['email'])) $arResult['SEND']['email'] = $prefix.time().'@noemail.gav';
			$pass = rand(1000000,10000000).'_fG';
			
			$user = new CUser;
			$login = $prefix.time();
			$arFields = Array(
			"NAME"              => $arResult['SEND']['name'],
			"EMAIL"             => $arResult['SEND']['email'],
			"LOGIN"             => $login,
			"ACTIVE"            => "Y",
			"PASSWORD"          => $pass,
			"CONFIRM_PASSWORD"  => $pass,
			);
			if(is_array($arParams["USER_GROUP"]) && count($arParams["USER_GROUP"])>0){
				$arFields['GROUP_ID'] = $arParams["USER_GROUP"];
			}
			// add Guest ID
			$U_ID = $user->Add($arFields);
			//file_put_contents("text.txt",print_r(array($arFields,$user->LAST_ERROR),true));
			$newuser = $U_ID;
			$newuserAr = array('USERID' => $newuser, 'USER_LOGIN' => $login, 'USER_PASS' => $pass);
			
			if(is_array($newuserAr)) {
				$newuser = $newuserAr['USERID'];
				$arEventUser['USER_LOGIN'] = $newuserAr['USER_LOGIN'];
				$arEventUser['USER_PASS'] = $newuserAr['USER_PASS'];
			}
			
		}
		elseif(!$newuser && $arParams['CREATE_USER']=='SET') {
			$newuser = intval($arParams['CUR_USER']);
		}
		
		//создаем заказ
		if(isset($arResult['SEND']['mess']) && !$arResult['SEND']['mess']) $arResult['SEND']['mess'] = '';
		
		if(!$_REQUEST['offer']) {
			$price = $_REQUEST['price_'.$product_id];
		}
		else {
			$product_offer_id = intval($_REQUEST['offer']);
			$price = $_REQUEST['price_'.$product_offer_id];
		}
		
		if($arProduct["CATALOG_PRICE_1"] && $arParams["CHECK_PRICE"]!="N") $price = $arProduct["CATALOG_PRICE_1"];
		
		$arFields = CMlifeBistrockick::fieldarr(SITE_ID,$arParams['PERSON_TYPE'],$price,$arParams['CURRENCY_ID'],$newuser,$arResult['SEND'],GetMessage("MLIFE_CAT_BK_MESS_ORDER"));

		// add Guest ID
		if (CModule::IncludeModule("statistic"))
		   $arFields["STAT_GID"] = CStatistic::GetEventParam();

		$ORDER_ID = CSaleOrder::Add($arFields);
		$ORDER_ID = IntVal($ORDER_ID);
		
		$arEventUser["ORDER_ID"] = $ORDER_ID;
		
		//добавляем значения свойств заказа
		foreach($arResult['SEND'] as $key=>$namefield) {
			if($namefield) {
				if(isset($arParams['PERSON_FIELD_'.strtoupper($key)])){
					$field_id = $arParams['PERSON_FIELD_'.strtoupper($key)];
				}else {
					$field_id = str_replace('addfield_','',$key);
				}
				$db_props = CSaleOrderProps::GetList(array(),array("ID" => $field_id),false,false,array("NAME", "CODE"));
				if ($props = $db_props->Fetch()){
					CSaleOrderPropsValue::Add(array("ORDER_ID" => $ORDER_ID, "ORDER_PROPS_ID" =>$field_id, "VALUE" => $namefield,"CODE"=>$props["CODE"],"NAME"=>$props["NAME"]));
				}
			}
		}
		
		//добавление товаров к заказу
		if ($ORDER_ID>0){
			
			if(isset($arProduct["ID"]) && $arProduct["ID"]>0) {
				$arFields = CMlifeBistrockick::getFieldsOrder($arProduct,$newuser,$ORDER_ID,$arParams['OFFERS_PROPERTY_CODE']);
				CSaleBasket::Add($arFields);
			}
		}

	}
	
	foreach($arResult['SEND'] as $key=>$value) {
		if($value) $arEventUser['USER_'.strtoupper($key)] = $value;
	}
	
	//подготовка массива макросов, для подстановки в шаблоны писем и смс
	$arEventFields = array();
	
	$arEventFields['PRODUCT_ID'] = $arProduct['ID'];
	
	if($arProduct['CATALOG_CURRENCY_1']==$arParams['CURRENCY_ID']) {
		$arEventFields["PRODUCT_PRICE"] = CurrencyFormat($arProduct['CATALOG_PRICE_1'], $arProduct['CATALOG_CURRENCY_1']);
	}else {
		$tempprice = CCurrencyRates::ConvertCurrency($arProduct['CATALOG_PRICE_1'], $arProduct['CATALOG_CURRENCY_1'], $arParams['CURRENCY_ID']);
		$arEventFields["PRODUCT_PRICE"] = CurrencyFormat($tempprice, $arParams['CURRENCY_ID']);
	}
	
	$arEventFields["PRODUCT_NAME"] = $arProduct['NAME'];
	$arEventFields["PRODUCT_LINK"] = $arProduct['DETAIL_PAGE_URL'];
	if(isset($arProduct['PRODUCT_IMG_LINK'])) $arEventFields["PRODUCT_IMG_LINK"] = $arProduct['PRODUCT_IMG_LINK'];
	
	foreach($arResult['SEND'] as $key=>$value) {
		if(isset($arEventUser['USER_'.strtoupper($key)])) $arEventFields['USER_'.strtoupper($key)] = $arEventUser['USER_'.strtoupper($key)];
	}
	
	if(isset($arEventUser['USER_LOGIN'])) $arEventFields['USER_LOGIN'] = $arEventUser['USER_LOGIN'];
	if(isset($arEventUser['USER_PASS'])) $arEventFields['USER_PASS'] = $arEventUser['USER_PASS'];
	
	if($arParams['CREATE_ORDER']=='Y') $arEventFields['ORDER_ID'] = $ORDER_ID;

	//отправка уведомления на email админу
	if($arParams['NOTICE_ADMIN']=='Y' && $arParams['NOTICE_ADMIN_MAIL']=='Y' && $arParams['NOTICE_ADMIN_MAIL_EMAIL']) {
		$arEventFields['SEND_EMAIL'] = $arParams['NOTICE_ADMIN_MAIL_EMAIL'];
		CEvent::Send("MLIFE_BISTROKLICK", SITE_ID, $arEventFields, 'Y', $arParams['NOTICE_ADMIN_MAIL_EVENT_MESSAGE_ID']);
	}
	//отправка уведомления на email пользователю
	if($arParams['NOTICE_ADMIN']=='Y' && $arParams['NOTICE_USER_MAIL']=='Y') {
		
		if(isset($arEventFields['USER_PASS'])) {
			CEvent::Send("MLIFE_BISTROKLICK", SITE_ID, $arEventFields, 'Y', $arParams['NOTICE_USER_MAIL_EVENT_MESSAGE_ID2']);
		}else{
			CEvent::Send("MLIFE_BISTROKLICK", SITE_ID, $arEventFields, 'Y', $arParams['NOTICE_USER_MAIL_EVENT_MESSAGE_ID']);
		}
	}
	//отправка уведомления по смс админу
	if($arParams['NOTICE_ADMIN']=='Y' && $arParams['NOTICE_ADMIN_SMS']=='Y' && $arParams['NOTICE_ADMIN_SMS_PHONE']) {
		if((CModule::IncludeModule("mlife.smsservices") &&  $arParams['NOTICE_SMS_MODULE']=='smsservices') 
		|| (CModule::IncludeModule("asd.smsswitcher") && $arParams['NOTICE_SMS_MODULE']=='smsswitcher')) {
			$arEventFields['SEND_PHONE'] = $arParams['NOTICE_ADMIN_SMS_PHONE'];
				$phones = $arEventFields['SEND_PHONE'];
				$rsEM = CEventMessage::GetByID($arParams['NOTICE_ADMIN_SMS_EVENT_MESSAGE_ID']);
				$arEM = $rsEM->Fetch();
				$shab = $arEM['MESSAGE'];
				$mess = CMlifeBistrockick::mlife_bistroklick_macros_replace($arEventFields,$shab);
				if(CModule::IncludeModule("mlife.smsservices") && $arParams['NOTICE_SMS_MODULE']=='smsservices') {
					$obSmsServ = new CMlifeSmsServices();
					$arSend = $obSmsServ->sendSms($phones,$mess,0);
				}else{
					CSMSS::Send($phones, $mess);
				}
		}
	}
	//отправка уведомления по смс пользователю
	if($arParams['NOTICE_ADMIN']=='Y' && $arParams['NOTICE_USER_SMS']=='Y') {
		if((CModule::IncludeModule("mlife.smsservices") &&  $arParams['NOTICE_SMS_MODULE']=='smsservices') 
		|| (CModule::IncludeModule("asd.smsswitcher") && $arParams['NOTICE_SMS_MODULE']=='smsswitcher')) {
			
				$phones = $arEventFields['USER_PHONE'];
				
			if(isset($arEventFields['USER_PASS'])) {
			
				$rsEM = CEventMessage::GetByID($arParams['NOTICE_USER_SMS_EVENT_MESSAGE_ID2']);
				$arEM = $rsEM->Fetch();
				$shab = $arEM['MESSAGE'];
				$mess = CMlifeBistrockick::mlife_bistroklick_macros_replace($arEventFields,$shab);
			
				if(CModule::IncludeModule("mlife.smsservices") && $arParams['NOTICE_SMS_MODULE']=='smsservices') {
					$obSmsServ = new CMlifeSmsServices();
					$arSend = $obSmsServ->sendSms($phones,$mess,0);
				}
				else {
					CSMSS::Send($phones, $mess);
				}
				
			}else{
			
				$rsEM = CEventMessage::GetByID($arParams['NOTICE_USER_SMS_EVENT_MESSAGE_ID']);
				$arEM = $rsEM->Fetch();
				$shab = $arEM['MESSAGE'];
				$mess = CMlifeBistrockick::mlife_bistroklick_macros_replace($arEventFields,$shab);
				
				if(CModule::IncludeModule("mlife.smsservices") && $arParams['NOTICE_SMS_MODULE']=='smsservices') {
					$obSmsServ = new CMlifeSmsServices();
					$arSend = $obSmsServ->sendSms($phones,$mess,0);
				}
				else {
					CSMSS::Send($phones, $mess);
				}
			}
		}
	}
	
	$arResult['SENDFORM'] = true;
}
else{
	$arResult['SENDFORM'] = false;
}



$this->IncludeComponentTemplate();
?>