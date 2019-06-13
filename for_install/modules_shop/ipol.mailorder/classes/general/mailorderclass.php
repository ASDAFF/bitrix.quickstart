<?
IncludeModuleLangFile(__FILE__);
class mailorderdriver
{
	static $MODULE_ID = "ipol.mailorder";

	function insertMacrosData(&$event, &$lid, &$arFields){
		if(!cmodule::includeModule('sale'))
			return;
		$chozen = explode(',',COption::GetOptionString(self::$MODULE_ID,"IPOLMO_OPT_EVENTS","SALE_NEW_ORDER"));
		$manual = unserialize(COption::GetOptionString(self::$MODULE_ID,"IPOLMO_OPT_ADDEVENTS","a:{}"));
		$rightEvent = true;
		if(!in_array($event,$chozen) && !in_array($event,$manual)){
			if(strpos($event,"SALE_STATUS_CHANGED")===0 && in_array("SALE_STATUS_CHANGED",$chozen))
				$rightEvent = true;
			else
				$rightEvent = false;
		}

		if(strlen($arFields['ORDER_ID'])<=0 || !$rightEvent || !CModule::includeModule('sale'))
			return;

		$orderId=$arFields['ORDER_ID'];
		$checkAN=CSaleOrder::GetList(array(),array('ACCOUNT_NUMBER'=>$orderId))->Fetch();
		if($checkAN)
			$orderId=$checkAN['ID'];
		$addedArray=array();
		$addedStr='';
		$arSavedProps=explode('|',COption::GetOptionString(mailorderdriver::$MODULE_ID,'IPOLMO_OPT_PROPS',''));
		$savedProps='';
		foreach($arSavedProps as $propStr)
			$savedProps.=','.substr($propStr,strpos($propStr,'{')+1,strpos($propStr,'}')-strpos($propStr,'{')-1);
		$savedProps=str_replace(',,',',',$savedProps);

		$arSavedProps = explode(',', $savedProps);
		foreach($arSavedProps as $key=>$val) {
			$val = trim($val);
			$val = trim(substr($val, 0, strpos($val, ' ')));
			if(!$val) unset($arSavedProps[$key]); else $arSavedProps[$key] = $val;
		}

		$orderProps = CSaleOrderPropsValue::GetOrderProps($orderId);
		$newStringSign="\n";
		if(COption::GetOptionString(mailorderdriver::$MODULE_ID,'IPOLMO_OPT_TEXTMODE','1')=='2')
			$newStringSign="<br>";
		while($prop=$orderProps->Fetch()){
			if(in_array($prop['CODE'], $arSavedProps) && $prop['VALUE']){ // == MOD ищем в массиве
				if($prop['TYPE']=="LOCATION"){
					$strLocation = self::getLocation($prop['VALUE']);
					$addedArray['IPOLMO_'.$prop['CODE']]=$strLocation;
					$addedStr.=$prop['NAME']." - ".$strLocation.$newStringSign;
				}else{
					$value = $prop['VALUE'];
					if($prop['TYPE']=="RADIO" || $prop['TYPE']=="SELECT"){
						$value = CSaleOrderPropsVariant::GetByValue($prop['ORDER_PROPS_ID'],$value);
						$value = $value['NAME'];
					}
					$addedArray['IPOLMO_'.$prop['CODE']]=$value;
					if($prop['TYPE']=="CHECKBOX")
						$addedStr.=$prop['NAME'].$newStringSign;
					else
						$addedStr.=$prop['NAME']." - ".$value.$newStringSign;
				}
			}
		}
		foreach($arSavedProps as $val){
			if(!isset($addedArray['IPOLMO_'.$val])) 
				$addedArray['IPOLMO_'.$val] = '';
		}
		$orderHimself=CSaleOrder::GetByID($orderId);
		// платежная система
		if(strpos($savedProps,',IMOPAYSYSTEM ( #IPOLMO_IMOPAYSYSTEM# ),')!==false){
			$paySystem=CSalePaySystem::GetByID($orderHimself['PAY_SYSTEM_ID']);
			$addedArray['IPOLMO_IMOPAYSYSTEM']=$paySystem['NAME'];
			$addedStr.=GetMessage("IPOLMO_OPT_PROPS_PAYSYSTEM")." - ".$paySystem['NAME'].$newStringSign;
		}
		// доставка
		if(strpos($savedProps,',IMODELIVERY ( #IPOLMO_IMODELIVERY# ),')!==false){
			if(self::isConverted()){
				$orderInfo = Bitrix\Sale\Order::load($orderId);
				$ds = $orderInfo->getDeliverySystemId();
				$ds = Bitrix\Sale\Delivery\Services\Table::getList(array('filter'=>array('ID' =>$ds[0])))->Fetch();
				if($ds)
					$deliveryName=$ds['NAME'];
			}else{
				if(strpos($orderHimself['DELIVERY_ID'],':')){
					$deliveryId=explode(':',$orderHimself['DELIVERY_ID']);
					if($deliverySystem=CSaleDeliveryHandler::GetBySID($deliveryId[0])->Fetch()){
						$deliveryName=$deliverySystem['NAME'];
						if($deliverySystem['PROFILES'][$deliveryId[1]]['TITLE'])
							$deliveryName.=" (".$deliverySystem['PROFILES'][$deliveryId[1]]['TITLE'].")";
					}
					else
						$deliveryName=false;
				}else{
					$deliverySystem=CSaleDelivery::GetByID($orderHimself['DELIVERY_ID']);
					$deliveryName=$deliverySystem['NAME'];
				}
			}
			if($deliveryName){
				$addedArray['IPOLMO_IMODELIVERY']=$deliveryName;
				$addedStr.=GetMessage("IPOLMO_OPT_PROPS_DELIVERY")." - ".$deliveryName.$newStringSign;
			}
		}
		// стоимость доставки
		if(strpos($savedProps,',IMODELIVERYPRICE ( #IPOLMO_IMODELIVERYPRICE# ),')!==false){
			$deliveryPrice = $orderHimself['PRICE_DELIVERY'];
			if(cmodule::includeModule('currency'))
				$deliveryPrice = CCurrencyLang::CurrencyFormat($deliveryPrice,$orderHimself['CURRENCY'],true);
			$addedArray['IPOLMO_IMODELIVERYPRICE']=$deliveryPrice;
			$addedStr.=GetMessage("IPOLMO_OPT_PROPS_DELIVERYPRC")." - ".$deliveryPrice.$newStringSign;
		}
		// документ об оплате
		if(strpos($savedProps,',IMOPAYED ( #IPOLMO_IMOPAYED# ),')!==false){
			$strOfPayed=false;
			if($orderHimself['PAY_VOUCHER_NUM']){
				$strOfPayed=GetMessage("IPOLMO_SIGN_PAYDOC").$orderHimself['PAY_VOUCHER_NUM'];
				if(preg_match('/([\d]{4})-([\d]{2})-([\d]{2})/',$orderHimself['PAY_VOUCHER_DATE'],$matches))
					$strOfPayed.=" ".GetMessage("IPOLMO_SIGN_FROM").$matches[3].".".$matches[2].".".$matches[1];
			}
			if($strOfPayed){
				$addedArray['IPOLMO_IMOPAYED']=$strOfPayed;
				$addedStr.=$strOfPayed.$newStringSign;
			}
		}
		// идентификатор отправления
		if(strpos($savedProps,',IMOTRACKING ( #IPOLMO_IMOTRACKING# ),')!==false){
			if($orderHimself['TRACKING_NUMBER']){
				$addedArray['IPOLMO_IMOTRACKING']=$orderHimself['TRACKING_NUMBER'];
				$addedStr.=GetMessage("IPOLMO_SIGN_TRACKING")." - ".$orderHimself['TRACKING_NUMBER'].$newStringSign;
			}
		}		
		// Сумма заказа
		if(strpos($savedProps,',IMOPRICE ( #IPOLMO_IMOPRICE# ),')!==false){
			$strOfPayed=CCurrencyLang::CurrencyFormat($orderHimself['PRICE'],$orderHimself['CURRENCY'],true);
			if($strOfPayed){
				$addedArray['IPOLMO_IMOPRICE']=$strOfPayed;
				$addedStr.=GetMessage("IPOLMO_SIGN_PRICE")." - ".$strOfPayed.$newStringSign;
			}
		}
		// Комментарий покупателя
		if(strpos($savedProps,',IMOCOMMENT ( #IPOLMO_IMOCOMMENT# ),')!==false){
			if($orderHimself['USER_DESCRIPTION']){
				$addedArray['IPOLMO_IMOCOMMENT']=$orderHimself['USER_DESCRIPTION'];
				$addedStr.=GetMessage("IPOLMO_SIGN_COMMENT")." - ".$orderHimself['USER_DESCRIPTION'].$newStringSign;
			}
		}

		$mode=COption::GetOptionString(mailorderdriver::$MODULE_ID,'IPOLMO_OPT_WORKMODE','1');
		if($mode=='1')
			$arFields=array_merge($arFields,$addedArray);
		if($mode=='2')
			$arFields['IPOLMOALL_PROPS']=$addedStr;
	}

	// Вспомогательный функционал
	function isConverted(){
		return (COption::GetOptionString("main","~sale_converted_15",'N') == 'Y');
	}

	function isNewLocations(){
		return (self::isConverted()  && method_exists("CSaleLocation","isLocationProMigrated") && CSaleLocation::isLocationProMigrated());
	}

	function getLocationTypes(){
		if(!cmodule::includeModule('sale'))
			return false;
		if(self::isConverted()){
			$arLocations = array();
			$locTypes = \Bitrix\Sale\Location\TypeTable::getList(array('select'=>array('CODE','LBL'=>'NAME.NAME'),'filter'=>array('NAME.LANGUAGE_ID' => LANGUAGE_ID)));
			while($element=$locTypes->Fetch())
				$arLocations[$element['CODE']] = $element['LBL'];
		}else
			$arLocations = array(
				'COUNTRY' => GetMessage('IPOLMO_SIGN_COUNTRY'),
				'REGION'  => GetMessage('IPOLMO_SIGN_REGION'),
				'CITY'    => GetMessage('IPOLMO_SIGN_CITY')
			);
		return $arLocations;
	}

	function getLocation($location){
		$place = '';
		$separator = COption::GetOptionString(self::$MODULE_ID,'IPOLMO_OPT_LOCATIONSEPARATOR',', ');
		$svd = unserialize(COption::GetOptionString(self::$MODULE_ID,'IPOLMO_OPT_LOCATIONDETAILS',self::getDefLocationTypes()));
		if(self::isNewLocations()){
			if(\Bitrix\Main\Loader::includeModule('sale')){
				if(strlen($location) == 10)
					$arFilter = array('=CODE' => $location);
				else
					$arFilter = array('=ID'=>$location);
				$result = \Bitrix\Sale\Location\LocationTable::getPathToNodeByCondition($arFilter, array(
					'select' => array('CHAIN' => 'NAME.NAME','DETAIL'=>'TYPE.CODE'),
					'filter' => array('NAME.LANGUAGE_ID' => LANGUAGE_ID)
				));
				while($element=$result->Fetch())
					if(in_array($element['DETAIL'],$svd))
						$place .= $element['CHAIN'].$separator;
			}
		}elseif(cmodule::includeModule('sale')){
			if(self::isConverted())
				$location = CSaleLocation::getLocationIDbyCODE($prop['VALUE']);
			$location = CSaleLocation::GetByID($location);
			foreach($svd as $code)
				if($location[$code.'_NAME_LANG'])
					$place .= $location[$code.'_NAME_LANG'].$separator;
		}
		return substr($place,0,(strlen($place) - strlen($separator)));
	}

	function getDefLocationTypes(){
		return serialize(array('COUNTRY','REGION','CITY'));
	}
}
?>