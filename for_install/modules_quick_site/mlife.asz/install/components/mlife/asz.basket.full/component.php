<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $DB;
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

//echo'<pre>';print_r($arParams);echo'</pre>';

if(!CModule::IncludeModule('mlife.asz')) {
		return;
}

if(!CModule::IncludeModule("iblock"))
{
	$this->AbortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

$quantTrue = 0;

//добавление товаров в корзину
if($_REQUEST["ajax"]==1){
	$APPLICATION->restartBuffer();
	if($_REQUEST['action']=="basket_add"){
		$prod = intval($_REQUEST['prodid']);
		if(intval($_REQUEST['quant'])>0){
			$quant = intval($_REQUEST['quant']);
		}else{
			$quant = 1;
		}
		$desc = false;
		if(trim($_REQUEST['desc'])) $desc = trim($_REQUEST['desc']);
		$priceAr = false;
		if(count($price)==3){
			$priceAr = array(
				"VAL" => $price[0],
				"CUR" => $price[1],
				"ID" => $price[2],
			);
		}
		if($prod>0 && $quant>0) {
			//получаем доступные остатки
			$res = \Mlife\Asz\QuantTable::GetList(array('select'=>array("PRODID","KOL"),'filter'=>array("PRODID"=>$prod)));
			if($arRes = $res->Fetch()){
				$quantTrue = $arRes["KOL"];
			}
			
			if($quantTrue<$quant && $arParams["QUANT"]=="Y") {
				if($arParams["ZAKAZ"]=="Y"){
				
					$res = \Mlife\Asz\BasketUserFunc::addItemBasket($prod,$quant,$desc,false,$priceAr);
					if(isset($res['error'])){
						echo $res['error'];
					}else{
						echo 'ok';
					}
					
				}else{
					echo GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR1")." ".$quantTrue." ".GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR2");
				}
			}else{
			
				$res = \Mlife\Asz\BasketUserFunc::addItemBasket($prod,$quant,$desc,false,$priceAr);
				if(isset($res['error'])){
					echo $res['error'];
				}else{
					echo 'ok';
				}
			
			}
		}else{
			echo GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR3");
		}
	}elseif($_REQUEST['action']=="basket_delete"){
		$bid = intval($_REQUEST['bid']);
		if($bid>0) {
			$res = \Mlife\Asz\BasketUserFunc::deleteItemBasket(false,$bid);
			echo 'ok';
		}else{
			echo GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR3");
		}
	}elseif($_REQUEST['action']=="basket_update"){
		
		$bid = intval($_REQUEST['bid']);
		$prod = intval($_REQUEST['prodid']);
		$quant = intval($_REQUEST['quant']);
		if($bid>0 && $quant>0 && $prod) {
			//получаем доступные остатки
			$res = \Mlife\Asz\QuantTable::GetList(array('select'=>array("PRODID","KOL"),'filter'=>array("PRODID"=>$prod)));
			if($arRes = $res->Fetch()){
				$quantTrue = $arRes["KOL"];
			}
			if($quantTrue<$quant && $arParams["QUANT"]=="Y") {
				if($arParams["ZAKAZ"]=="Y"){
					$res = \Mlife\Asz\BasketUserFunc::updateItemQuantBasket($bid,$quant);
					echo 'ok';
				}else{
					echo GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR1")." ".$quantTrue." ".GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR2");
				}
			}else{
				$res = \Mlife\Asz\BasketUserFunc::updateItemQuantBasket($bid,$quant);
				echo 'ok';
			}
		}
		
	}
	die();
}

$arResult = array();

$ASZ_USER = \Mlife\Asz\BasketUserFunc::getAszUid();

if(intval($ASZ_USER)>0) {
	$arResult["SHOW_BASKET"] = true;
	
	$arResult["BASE_CURENCY"] = \Mlife\Asz\CurencyFunc::getBaseCurency(SITE_ID);
	
	$res = \Mlife\Asz\BasketTable::getList(
		array(
			'select' => array("*"),
			'filter' => array("USERID"=>$ASZ_USER,"ORDER_ID"=>null)
		)
	);
	$arProd = array();
	$arResult["BASKET_ITEMS"] = array();
	$arResult["ORDER"] = array();
	$arResult["ORDER"]["ITEMSUM"] = 0;
	$arResult["ORDER"]["ITEMDISCOUNT"] = 0;
	while($arRes = $res->Fetch()){
		//echo'<pre>';print_r($arRes);echo'</pre>';
		$arProd[$arRes["PROD_ID"]] = $arRes["PROD_ID"];
		if(!$arRes["DISCOUNT_VAL"]) $arRes["DISCOUNT_VAL"] = 0;
		if(!$arRes["DISCOUNT_CUR"]) $arRes["DISCOUNT_CUR"] = $arResult["BASE_CURENCY"];
		$arRes["PRICE_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arRes["PRICE_VAL"],$arRes["PRICE_CUR"],SITE_ID);
		$arRes["PRICE_DISPLAY_ALL"] = \Mlife\Asz\CurencyFunc::priceFormat((($arRes["PRICE_VAL"]-$arRes["DISCOUNT_VAL"])*$arRes["QUANT"]),$arRes["PRICE_CUR"],SITE_ID);
		$arRes["DISCOUNT_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arRes["DISCOUNT_VAL"],$arRes["DISCOUNT_CUR"],SITE_ID);
		$arResult["ORDER"]["ITEMSUM"] = $arResult["ORDER"]["ITEMSUM"] + (\Mlife\Asz\CurencyFunc::convertBase($arRes["PRICE_VAL"],$arRes["PRICE_CUR"],SITE_ID) * $arRes["QUANT"]);
		$arResult["ORDER"]["ITEMDISCOUNT"] = $arResult["ORDER"]["ITEMDISCOUNT"] + (\Mlife\Asz\CurencyFunc::convertBase($arRes["DISCOUNT_VAL"],$arRes["DISCOUNT_CUR"],SITE_ID) * $arRes["QUANT"]);
		$arResult["BASKET_ITEMS"][] = $arRes;
	}
	
	
	if(count($arProd)>0){
		
		$arSelect = array("ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL");
		$arFilter = array("ID"=>$arProd,"ACTIVE"=>"Y");
		$rs = CIBlockElement::GetList(array(),$arFilter,false,false,$arSelect);
		while($ar = $rs->GetNext(false,false)) {
			$artempFile = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array('width'=>100, 'height'=>100), BX_RESIZE_IMAGE_EXACT, false);
			$ar["IMG_SRC"] = $artempFile['src'];
			$arResult["PROD"][$ar["ID"]] = $ar;
		}
		if(count($arResult["PROD"])==0) {
			$arResult["SHOW_BASKET"] = false;
		}else{
			if(count($arResult["PROD"])!=count($arProd)) {
				//удаляем лишние записи
				foreach($arProd as $val){
					if(!isset($arResult["PROD"][$val])) {
						\Mlife\Asz\BasketUserFunc::deleteItemBasket($val);
					}
				}
			}
		}
		
		$arResult["QUANT"] = array();
		$resQ = \Mlife\Asz\QuantTable::GetList(array('select'=>array("PRODID","KOL"),'filter'=>array("PRODID"=>$arProd)));
		while($arResQ = $resQ->Fetch()){
			$arResult["QUANT"][$arResQ["PRODID"]] = $arResQ["KOL"];
		}
		
		$arResult["ORDER"]["ITEMSUM_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMSUM"],false,SITE_ID);
		$arResult["ORDER"]["ITEMDISCOUNT_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMDISCOUNT"],false,SITE_ID);
		$arResult["ORDER"]["ITEMSUMFIN"] = $arResult["ORDER"]["ITEMSUM"] - $arResult["ORDER"]["ITEMDISCOUNT"];
		
		//общая сумма заказа
		$arResult["ORDER"]["ORDERSUM"] = $arResult["ORDER"]["ITEMSUMFIN"];
		$arResult["ORDER"]["ORDERSUM_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ITEMSUMFIN"],false,SITE_ID);
		
		//данные клиента
		$res = \Mlife\Asz\OrderpropsTable::getList(array(
			'order' => array("SORT"=>"ASC"),
			'filter' => array("SITEID"=>SITE_ID,"ACTIVE"=>"Y")
		));
		$arResult["USERPROPS"] = array();
		while($arRes = $res->Fetch()){
		
			if($arRes['CODE']){
				$arRes['VALUE'] = "";
				if(isset($_REQUEST['user_'.$arRes['CODE']])) {
					if(ToLower(SITE_CHARSET) == "windows-1251") {
						$arRes['VALUE'] = $GLOBALS["APPLICATION"]->ConvertCharset($_REQUEST['user_'.$arRes['CODE']], 'UTF-8', SITE_CHARSET);
					}else{
						$arRes['VALUE'] = $_REQUEST['user_'.$arRes['CODE']];
					}
				}
				
				if($arRes['TYPE']=="LOCATION") {
					$state = \Mlife\Asz\StateTable::getList(array(
						'order' => array("CN.NAME"=>"ASC","SORT"=>"ASC","NAME"=>"ASC"),
						'filter' => array("CN.SITEID"=>SITE_ID,"ACTIVE"=>"Y"),
						'select' => array("CN.NAME","NAME","ID"),
					));
					while($arState = $state->Fetch()){
						$arRes["VALUES"][$arState["ID"]] = $arState["MLIFE_ASZ_STATE_CN_NAME"].' - '.$arState["NAME"];
					}
					if($arRes['VALUE'])
						$arResult["ORDER"]['LOCATION_ID'] = $_REQUEST['user_'.$arRes['CODE']];
				}
				
				$arResult["USERPROPS"][$arRes['CODE']] = $arRes;
			}
		}
		
		//способы доставки
		$arResult["DELIVERY"] = array();
		$res = \Mlife\Asz\DeliveryTable::getList(
			array(
				'filter' => array("SITEID"=>SITE_ID,"ACTIVE"=>"Y"),
				'select' => array("ID","NAME","ACTIONFILE","DESC")
				)
		);
		$i = 0;
		$arResult["ORDER"]["DELIVERY_ID"] = false;
		$arResult["ORDER"]['DELIVERYCOST'] = 0;
		while($arRes = $res->Fetch()){
			$cl = "\Mlife\\Asz\\Deliver\\".$arRes["ACTIONFILE"];
			if($arRes["ACTIONFILE"] && class_exists($cl)){
				if($cl::getRight($arRes["ID"],$arResult["ORDER"])){
					$i++;
					$arDelivery = $arRes;
					if($_REQUEST['DELIVERY_ID'] && $_REQUEST['DELIVERY_ID']==$arRes['ID']){
						$arResult["ORDER"]["DELIVERY_ID"] = $arRes["ID"];
					}elseif($i==1){
						$arResult["ORDER"]["DELIVERY_ID"] = $arRes["ID"];
					}
					$arDelivery['COST'] = 0;
					
					$arDelivery['COST'] = $cl::getCost($arRes["ID"],$arResult["ORDER"]);
					$arDelivery['PARAMS'] = $cl::getParamsArray($arRes['PARAMS']);
					$arDelivery['COST_DISPLAY'] = \Mlife\Asz\CurencyFunc::priceFormat($arDelivery['COST'],false,SITE_ID);
					$arDelivery['IMAGE'] = $cl::getImage($arRes["ID"]);
					
					$arResult["DELIVERY"][$arRes['ID']] = $arDelivery;
				}
			}
		}
		if($arResult["ORDER"]["DELIVERY_ID"]){
			$arResult["ORDER"]['DELIVERYCOST'] = $arResult["DELIVERY"][$arResult["ORDER"]["DELIVERY_ID"]]['COST'];
			$arResult["ORDER"]['DELIVERYCOST_DISPLAY'] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]['DELIVERYCOST'],false,SITE_ID);
		}
		
		//способы оплаты
		$arResult["PAYMENT"] = array();
		$res = \Mlife\Asz\PaysystemTable::getList(
			array(
				'filter' => array("SITEID"=>SITE_ID,"ACTIVE"=>"Y"),
				'select' => array("ID","NAME","ACTIONFILE","DESC")
				)
		);
		$i = 0;
		$arResult["ORDER"]["PAYMENT_ID"] = false;
		$arResult["ORDER"]['PAYMENTCOST'] = 0;
		while($arRes = $res->Fetch()){
			$cl = "\Mlife\\Asz\\Payment\\".$arRes["ACTIONFILE"];
			if($arRes["ACTIONFILE"] && class_exists($cl)){
				if($cl::getRight($arRes["ID"],$arResult["ORDER"])){
					$i++;
					$arPayment = $arRes;
					if($_REQUEST['PAYMENT_ID'] && $_REQUEST['PAYMENT_ID']==$arRes['ID']){
						$arResult["ORDER"]["PAYMENT_ID"] = $arRes["ID"];
					}elseif($i==1){
						$arResult["ORDER"]["PAYMENT_ID"] = $arRes["ID"];
					}
					$arPayment['COST'] = 0;
					
					$arPayment['COST'] = $cl::getCost($arRes["ID"],$arResult["ORDER"]);
					$arPayment['PARAMS'] = $cl::getParamsArray($arRes['PARAMS']);
					$arPayment['COST_DISPLAY'] = \Mlife\Asz\CurencyFunc::priceFormat($arPayment['COST'],false,SITE_ID);
					$arPayment['IMAGE'] = $cl::getImage($arRes["ID"]);
					
					$arResult["PAYMENT"][$arRes['ID']] = $arPayment;
				}
			}
		}
		if($arResult["ORDER"]["PAYMENT_ID"]){
			$arResult["ORDER"]['PAYMENTCOST'] = $arResult["PAYMENT"][$arResult["ORDER"]["PAYMENT_ID"]]['COST'];
			$arResult["ORDER"]['PAYMENTCOST_DISPLAY'] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]['PAYMENTCOST'],false,SITE_ID);
		}
		
		//общая сумма заказа
		$arResult["ORDER"]["DISCOUNT"] = $arResult["ORDER"]["ITEMDISCOUNT"];
		$arResult["ORDER"]["DISCOUNT_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["DISCOUNT"],false,SITE_ID);
		$arResult["ORDER"]["ORDERSUM"] = $arResult["ORDER"]["ITEMSUMFIN"] + $arResult["ORDER"]['DELIVERYCOST'] + $arResult["ORDER"]['PAYMENTCOST'];
		$arResult["ORDER"]["ORDERSUM_DISPLAY"] = \Mlife\Asz\CurencyFunc::priceFormat($arResult["ORDER"]["ORDERSUM"],false,SITE_ID);
		
		$arResult['ORDER_ERROR'] = array();
		$arResult['ORDER_CREATE'] = false;
		if($_REQUEST['orderfin']==1){
			//проверка обызятельных полей
			foreach($arResult["USERPROPS"] as &$prop){
				if($prop['REQ']=="Y" && $prop["VALUE"]=="") {
					$arResult['ORDER_ERROR'][] = GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR4")." ".$prop["NAME"];
				}elseif($prop['TYPE']=="EMAIL" && $prop['VALUE']){
					$requlEmail = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD";
					if(!preg_match($requlEmail,$prop['VALUE'])) {
						$arResult['ORDER_ERROR'][] = GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR5");
					}
				}elseif($prop['CODE']=="PHONE" && $prop['VALUE']){
					$check = \Mlife\Asz\Functions::checkPhoneNumber($prop['VALUE']);
					if($check['check']){
						$prop['VALUE'] = $check["phone"];
					}else{
						$arResult['ORDER_ERROR'][] = GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR_PHONE");
					}
				}
			}
		}
		
		if($_REQUEST['orderfin']==1){
			if(count($arResult['ORDER_ERROR'])==0){
				$error = false;
				$passwZakaz = rand(100,900).'a_A'.rand(100,900);
				//добавляем заказ в базу данных
				$arFields = array(
					"SITEID" => SITE_ID,
					"USERID" => $ASZ_USER,
					"PAY_ID" => $arResult["ORDER"]["PAYMENT_ID"],
					"DELIVERY_ID" => $arResult["ORDER"]["DELIVERY_ID"],
					"PRICE" => $arResult["ORDER"]["ORDERSUM"],
					"DISCOUNT" => 0.00,
					"TAX" => 0.00,
					"CURRENCY" => $arResult["BASE_CURENCY"],
					"DELIVERY_PRICE" => $arResult["ORDER"]['DELIVERYCOST'],
					"PAYMENT_PRICE" => $arResult["ORDER"]['PAYMENTCOST'],
					"DATE" => time(),
					"PASSW" => $passwZakaz,
				);
				
				//получаем начальный статус заказа
				$res = \Mlife\Asz\OrderStatusTable::getList(
					array(
						'select' => array("ID"),
						'filter' => array("SITEID"=>SITE_ID,"CODE"=>"N")
					)
				);
				if($arRes = $res->Fetch()) {
					$arFields["STATUS"] = $arRes["ID"];
				}else{
					$arResult['ORDER_ERROR'][] = GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR6");
					$error = true;
				}
				
				if(!$error) {
					//создаем пользователя
					$newuser = false;
					$user = new CUser;
					
					$autorize = true;
					
					if($USER->IsAuthorized()) $newuser = $USER->GetID();
					
					if($arParams["ORDERPRIV"]=="Y"){
						$newuser = $arParams["ORDERPRIV_USERID"];
					}
					
					if(!$newuser){
						if(isset($arResult["USERPROPS"][$arParams['PROP_EMAIL']]["VALUE"]) && $arResult["USERPROPS"][$arParams['PROP_EMAIL']]["VALUE"]){
							$userEmail = true;
						}else{
							$userEmail = false;
							if($arParams['NOEMAIL']=="GEN") {
								$newEmail = time().'@noemail.gav';
							}elseif($arParams['NOEMAIL']=="USER"){
								$newuser = $arParams['NOEMAIL_USER'];
							}
						}
					}
					
					
					if($arParams['FINDUSER']=="Y" && !$newuser && $userEmail) {
						$rsUsers = CUser::GetList(
						($by="ID"), ($order="desc"), 
						array("EMAIL" => $arResult["USERPROPS"][$arParams['PROP_EMAIL']]["VALUE"], "!GROUPS_ID" => $arParams["GROUP_ADMIN"]), 
						array('SELECT'=> array(),'NAV_PARAMS'=> array('nPageSize'=>1),"FIELD" => array('ID')));
						if($arUser = $rsUsers->Fetch()) {
							if($arParams['FINDEMAIL']=="Y") {
								$error = true;
								$arResult['ORDER_ERROR'][] = GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR7");
							}else{
								$newuser = $arUser['ID'];
								if($ArParams["FINDEMAIL_NOAUT"]=="Y"){
									$autorize = false;
								}
							}
						}
					}
					
					if(!$newuser && !$error) {
						
						if($arParams["LOGIN"]=='EMAIL') {
							if($userEmail) {
								$login = $arResult["USERPROPS"][$arParams['PROP_EMAIL']]["VALUE"];
							}else{
								$login = $newEmail;
							}
						}elseif($arParams["LOGIN"]=='PREFIX'){
							$prefix = $arParams['LOGIN_PREFIX'];
							$login = $prefix.time();
						}elseif($arParams["LOGIN"]=='PREFIXEMAIL'){
							if(!$userEmail) {
								$prefix = $arParams['LOGIN_PREFIX'];
								$login = $prefix.time();
							}else{
								$login = $arResult["USERPROPS"][$arParams['PROP_EMAIL']]["VALUE"];
							}
						}
					
						
						$pass = rand(100,900).'a_A'.rand(100,900);
						$user = new CUser;
						
						$arFieldsUser = Array(
						"NAME"              => $arResult["USERPROPS"][$arParams['PROP_NAME']]['VALUE'],
						"EMAIL"             => ($userEmail) ? $arResult["USERPROPS"][$arParams['PROP_EMAIL']]['VALUE'] : $newEmail,
						"LOGIN"             => $login,
						"ACTIVE"            => "Y",
						"PASSWORD"          => $pass,
						"CONFIRM_PASSWORD"  => $pass,
						);
						
						if(is_array($arParams["GROUP_ADDUSER"])){
							$arFieldsUser["GROUP_ID"] = $arParams["GROUP_ADDUSER"];
						}
						
						// add Guest ID
						$newuser = $user->Add($arFieldsUser);
					}
					
					$ckeckadmin = false;
					//проверить или разрешена авторизация под группой данного пользователя
					if(count($arParams["ORDERPRIV_GROUP"])>0 && $newuser){
						$autorize = false;
						foreach($arParams["ORDERPRIV_GROUP"] as $group_id) {
							if(in_array($group_id, CUser::GetUserGroup($newuser))){
								$autorize = true;
							}
						}
						if(!$USER->IsAuthorized()) $ckeckadmin = true;
					}else{
						$autorize = false;
					}
					
					if($autorize && $newuser){
						if((!$USER->IsAdmin() && $ckeckadmin) || !$ckeckadmin){
							$USER->Authorize($newuser);
						}
					}elseif($newuser){
						
					}else{
						$error = true;
						$arResult['ORDER_ERROR'][] = GetMessage("MLIFE_ASZ_BASKET_FULL_C_ERR8");
					}
					
					if(!$error){
						//добавляем связь пользователей корзины и сайта
						\Mlife\Asz\UserTable::update($ASZ_USER,array("BX_UID"=>$newuser));
						
						//добавляем значения свойств корзины для пользователя
						foreach($arResult["USERPROPS"] as $proper){
							if($proper['ID']) {
								\Mlife\Asz\OrderpropsValuesTable::add(array(
									"UID" => $ASZ_USER,
									"PROPID" => $proper['ID'],
									"VALUE" => $proper['VALUE']
								));
							}
						}
					}
				}
				
				if(!$error) {
					
					//добавляем в обработчики массив с корзиной (без ид заказа при его добавлении неизвестен состав корзины)
					\Mlife\Asz\Handlers::$basketItemsArray = $arResult["BASKET_ITEMS"];
					
					$res = \Mlife\Asz\OrderTable::add($arFields);
					$arResult["ORDERID"] = $res->getId();
					$arResult["ORDERPASS"] = $passwZakaz;
					
					//добавляем корзину к заказу
					foreach($arResult["BASKET_ITEMS"] as $item) {
						if($item['ID']) {
							\Mlife\Asz\BasketTable::update($item['ID'],array("ORDER_ID" => $arResult["ORDERID"]));
						}
					}
					
					//обнуляем пользака из сессии
					\Mlife\Asz\BasketUserFunc::setAszUid();
					
				}
				
				if(!$error) $arResult['ORDER_CREATE'] = true;
			}
		}
		
	}else{
		$arResult["SHOW_BASKET"] = false;
	}
	
}else{
	$arResult["SHOW_BASKET"] = false;
}

$this->IncludeComponentTemplate();

?>