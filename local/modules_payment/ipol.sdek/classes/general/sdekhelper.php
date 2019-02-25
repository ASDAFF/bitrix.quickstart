<?
class sdekHelper{
	// логи
	static $tmpLogFile = false;
	function toLog($wat,$sign=''){
		if($sign) $sign.=" ";
		if(!self::$tmpLogFile){
			self::$tmpLogFile = fopen($_SERVER['DOCUMENT_ROOT'].'/SDEKLog.txt','w');
			fwrite(self::$tmpLogFile,"\n\n".date('H:i:s d.m')."\n"); 
		}
		fwrite(self::$tmpLogFile,$sign.print_r($wat,true)."\n"); 
	}
	static $ERROR_REF;
	function errorLog($error,$module_id='ipol.sdek'){
		if(!COption::GetOptionString($module_id,'logged',false))
			return;
		$file=fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$module_id."/errorLog.txt","a");
		fwrite($file,"\n".date("d.m.Y H:i:s")." ".$error);
		fclose($file);
	}
	static $ANSWER_REF;
	function toAnswer($wat,$sign=''){
		if($sign) $sign.=" ";
		if(self::$ANSWER_REF) self::$ANSWER_REF.="\n";
		self::$ANSWER_REF.=$sign.print_r($wat,true);
	}
	//кодировки
	function zajsonit($handle){
		if(LANG_CHARSET !== 'UTF-8'){
			if(is_array($handle))
				foreach($handle as $key => $val){
					unset($handle[$key]);
					$key=self::zajsonit($key);
					$handle[$key]=self::zajsonit($val);
				}
			else
				$handle=$GLOBALS['APPLICATION']->ConvertCharset($handle,LANG_CHARSET,'UTF-8');
		}
		return $handle;
	}
	function zaDEjsonit($handle){
		if(LANG_CHARSET !== 'UTF-8'){
			if(is_array($handle))
				foreach($handle as $key => $val){
					unset($handle[$key]);
					$key=self::zaDEjsonit($key);
					$handle[$key]=self::zaDEjsonit($val);
				}
			else
				$handle=$GLOBALS['APPLICATION']->ConvertCharset($handle,'UTF-8',LANG_CHARSET);
		}
		return $handle;
	}

	function toUpper($str){
		$str = str_replace( //H8 ANSI
			array(
				GetMessage('IPOLSDEK_LANG_YO_S'),
				GetMessage('IPOLSDEK_LANG_CH_S'),
				GetMessage('IPOLSDEK_LANG_YA_S')
			),
			array(
				GetMessage('IPOLSDEK_LANG_YO_B'),
				GetMessage('IPOLSDEK_LANG_CH_B'),
				GetMessage('IPOLSDEK_LANG_YA_B')
			),
			$str
		);
		if(function_exists('mb_strtoupper'))
			return mb_strtoupper($str,LANG_CHARSET);
		else
			return strtoupper($str);
	}
	// авторизация
	function auth($params){
		if(!$params['login'] || !$params['password'])
			die('No auth data');
		if(!class_exists('CDeliverySDEK'))
			die('No main class founded');
		sdekdriver::$MODULE_ID;
		if(!function_exists('curl_init'))
			die(GetMessage("IPOLSDEK_AUTH_NOCURL"));

		COption::SetOptionString(sdekdriver::$MODULE_ID,'logSDEK',$params['login']);
		COption::SetOptionString(sdekdriver::$MODULE_ID,'pasSDEK',$params['password']);
		
		CDeliverySDEK::$sdekCity   = 44;
		CDeliverySDEK::$sdekSender = 44;
		CDeliverySDEK::setOrder();

		$resAuth = CDeliverySDEK::calculateDost(136);
		
		if($resAuth['success']){
			COption::SetOptionString(sdekdriver::$MODULE_ID,'logged',true);

			RegisterModuleDependences("main", "OnEpilog", sdekdriver::$MODULE_ID, "sdekdriver", "onEpilog");
			RegisterModuleDependences("main", "OnEndBufferContent", sdekdriver::$MODULE_ID, "CDeliverySDEK", "onBufferContent");
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", sdekdriver::$MODULE_ID, "CDeliverySDEK", "pickupLoader",900);
			// RegisterModuleDependences("main", "OnEpilog", imldriver::$MODULE_ID, "CDeliveryIML", "onOEPageLoad"); // editing order
			RegisterModuleDependences("main", "OnAdminListDisplay", sdekdriver::$MODULE_ID, "sdekdriver", "displayActPrint");
			RegisterModuleDependences("main", "OnBeforeProlog", sdekdriver::$MODULE_ID, "sdekdriver", "OnBeforePrologHandler");
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", sdekdriver::$MODULE_ID, "sdekdriver", "orderCreate"); // создание заказа
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", sdekdriver::$MODULE_ID, "CDeliverySDEK", "checkNalD2P"); // проверка платежных систем
			RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", sdekdriver::$MODULE_ID, "CDeliverySDEK", "checkNalP2D"); // проверка платежных систем

			CAgent::AddAgent("sdekdriver::agentUpdateList();", sdekdriver::$MODULE_ID);//обновление листов
			CAgent::AddAgent("sdekdriver::agentOrderStates();",sdekdriver::$MODULE_ID,"N",1800);//обновление статусов заказов
			
			CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".sdekdriver::$MODULE_ID."/install/delivery/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery/", true, true);
			
			echo "G".GetMessage('IPOLSDEK_AUTH_YES');
		}
		else{
			COption::SetOptionString(sdekdriver::$MODULE_ID,'logSDEK','');
			COption::SetOptionString(sdekdriver::$MODULE_ID,'pasSDEK','');
			
			$retStr=GetMessage('IPOLSDEK_AUTH_NO');
			foreach($resAuth as $erCode => $erText)
				$retStr.=$erText." (".$erCode."). ";
			
			echo $retStr;
		}
	}
	
	function logoff(){
		COption::SetOptionString(sdekdriver::$MODULE_ID,'logSDEK','');
		COption::SetOptionString(sdekdriver::$MODULE_ID,'pasSDEK','');
		COption::SetOptionString(sdekdriver::$MODULE_ID,'logged',false);
		CAgent::RemoveModuleAgents('ipol.sdek');
		UnRegisterModuleDependences("main", "OnEpilog", sdekdriver::$MODULE_ID, "sdekdriver", "onEpilog");
		UnRegisterModuleDependences("main", "OnEndBufferContent", sdekdriver::$MODULE_ID, "CDeliverySDEK", "onBufferContent");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", sdekdriver::$MODULE_ID, "CDeliverySDEK", "pickupLoader",900);
		UnRegisterModuleDependences("main", "OnAdminListDisplay", sdekdriver::$MODULE_ID, "sdekdriver", "displayActPrint");
		UnRegisterModuleDependences("main", "OnBeforeProlog", sdekdriver::$MODULE_ID, "sdekdriver", "OnBeforePrologHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", sdekdriver::$MODULE_ID, "sdekdriver", "orderCreate");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepPaySystem", sdekdriver::$MODULE_ID, "CDeliverySDEK", "checkNalD2P");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepDelivery", sdekdriver::$MODULE_ID, "CDeliverySDEK", "checkNalP2D");
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_delivery/delivery_sdek.php");
	}
	// получем данные из LIST - файла в том формате, в котором они... должны... быть...
	function getListFile($module_id='ipol.sdek',$noEnc=false){
		if(!file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".$module_id."/list.php")) return array();
		$arList = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".$module_id."/list.php"),true);
		if(!$noEnc)
			$arList = self::zaDEjsonit($arList);
		return $arList;
	}

	//получаем город заказа по его id
	static $optCity = false;
	static $arTmpArLocation=false;
	function getOrderCity($id){
		if(!self::$optCity)
			self::$optCity = COption::GetOptionString(sdekdriver::$MODULE_ID,'location',false);
		if(!is_array(self::$arTmpArLocation)) self::$arTmpArLocation=array();

		$oCity=CSaleOrderPropsValue::GetList(array(),array('ORDER_ID'=>$id,'CODE'=>self::$optCity))->Fetch();
		if($oCity['VALUE']){
			if(is_numeric($oCity['VALUE'])){
				if(in_array($oCity['VALUE'],self::$arTmpArLocation))
					$oCity=self::$arTmpArLocation[$oCity['VALUE']];
				else{
					$cityId = self::getNormalCity($oCity['VALUE']);
					$tmpCity=CSaleLocation::GetByID($cityId);
					self::$arTmpArLocation[$oCity['VALUE']]=$tmpCity['CITY_NAME_LANG'];
					$oCity=str_replace(GetMessage('IPOLSDEK_LANG_YO_S'),GetMessage('IPOLSDEK_LANG_YE_S'),$tmpCity['CITY_NAME_LANG']);
				}
			}
			else
				$oCity=$oCity['VALUE'];
		}
		else
			$oCity=false;

		return $oCity;
	}

	//Выдает срок и стоимость доставки для виджета 
	function cntDelivs($arOrder){
		if($arOrder["DIMS"])
			$goods = array(array(
				"QUANTITY"   => 1,
				"PRICE"      => ($arOrder['PRICE'])  ? $arOrder['PRICE']  : CDeliverySDEK::$orderPrice,
				"WEIGHT"     => ($arOrder['WEIGHT']) ? $arOrder['WEIGHT'] : CDeliverySDEK::$orderWeight,
				"DIMENSIONS" => array(
					"WIDTH"  => $arOrder["DIMS"]["WIDTH"],
					"HEIGHT" => $arOrder["DIMS"]["HEIGHT"],
					"LENGTH" => $arOrder["DIMS"]["LENGTH"],			
				),
			));
		cmodule::includeModule('sale');
		if($arOrder['CITY_TO_ID'])
			$cityTo = $arOrder['CITY_TO_ID'];
		else{
			$cityTo = CSaleLocation::getList(array(),array('CITY_NAME'=>self::zaDEjsonit($arOrder['CITY_TO'])))->Fetch();
			if($cityTo){
				$_SESSION['IPOLSDEK_city'] = $arOrder['CITY_TO'];
				$cityTo = $cityTo['ID'];
			}
		}
		$cityFrom = COption::getOptionString('ipol.sdek','departure');
		$pPrice = 'no';
		$cPrice = 'no';
		if($arOrder["DIMS"])
			$arOrder['GOODS'] = $goods;
		CDeliverySDEK::setOrder($arOrder);

		$list = self::getListFile();

		$psevdoOrder = array(
			"LOCATION_TO"   => $cityTo,
			"LOCATION_FROM" => $cityFrom,
			"PRICE"         => ($arOrder['PRICE'])  ? $arOrder['PRICE']  : CDeliverySDEK::$orderPrice,
			"WEIGHT"        => ($arOrder['WEIGHT']) ? $arOrder['WEIGHT'] : CDeliverySDEK::$orderWeight,
		);
		if($arOrder["DIMS"])
			$psevdoOrder['ITEMS']=$goods;
		$arHandler = CSaleDeliveryHandler::GetBySID('sdek')->Fetch();
		$arProfiles = CSaleDeliveryHandler::GetHandlerCompability($psevdoOrder,$arHandler);
		foreach($arProfiles as $profName => $someArray){
			if(in_array($profName,$arOrder['FORBIDDEN'])) continue;
			$calc = CSaleDeliveryHandler::CalculateFull('sdek',$profName,$psevdoOrder,"RUB");
			if($calc['RESULT'] != 'ERROR')
				$arProfiles[$profName]['calc'] = (CDeliverySDEK::$price[$profName])?CCurrencyLang::CurrencyFormat(CDeliverySDEK::$price[$profName],'RUB',true):GetMessage("IPOLSDEK_FREEDELIV");	
		}
		$arReturn = self::zajsonit(array(
				'courier' => ($arProfiles['courier']['calc']) ? $arProfiles['courier']['calc'] : 'no',
				'pickup'  => ($arProfiles['pickup']['calc'])  ? $arProfiles['pickup']['calc']  : 'no',
				'date'    => CDeliverySDEK::$date,
				'c_date'  => CDeliverySDEK::$profiles['courier']['TRANSIT'],
				'p_date'  => CDeliverySDEK::$profiles['pickup']['TRANSIT'],
			));

		if($arOrder['action'])
			echo json_encode($arReturn);
		else
			return $arReturn;
	}
	
	//Очистка кэша
	function clearCache(){
		$obCache = new CPHPCache();
		$obCache->CleanDir('/IPOLSDEK/');
		echo "Y";
	}
	
	//ошибочные города
	function getErrCities($module_id='ipol.sdek'){
		if(!file_exists($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".$module_id."/errCities.json"))
			return false;
		return self::zaDEjsonit(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/js/".$module_id."/errCities.json"),true));
	}
	// Проверка активности СД
	function isActive(){
		$dS = CSaleDeliveryHandler::GetBySID('sdek')->Fetch();
		return ($dS && $dS['ACTIVE'] == 'Y');
	}
	
	// Свойство заказа, куда пишутся тарифы
	function controlProps($mode){//1-add/update, 2-delete "TOKEN-prop"
		if(!CModule::IncludeModule("sale"))
			return false;
		$tmpGet=CSaleOrderProps::GetList(array("SORT" => "ASC"),array("CODE" => "IPOLSDEK_CNTDTARIF"));
		$existedProps=array();
		while($tmpElement=$tmpGet->Fetch())
			$existedProps[$tmpElement['PERSON_TYPE_ID']]=$tmpElement['ID'];

		if($mode=='1'){
			$return = true;
			$tmpGet = CSalePersonType::GetList(Array("SORT" => "ASC"), Array());
			$allPayers=array();
			while($tmpElement=$tmpGet->Fetch())
				$allPayers[]=$tmpElement['ID'];

			foreach($allPayers as $payer){
				$tmpGet = CSaleOrderPropsGroup::GetList(array("SORT" => "ASC"),array("PERSON_TYPE_ID" => $payer),false,array('nTopCount' => '1'));
				$tmpVal=$tmpGet->Fetch();
				$arFields = array(
				   "PERSON_TYPE_ID" => $payer,
				   "NAME" => GetMessage('IPOLSDEK_prop_name'),
				   "TYPE" => "TEXT",
				   "REQUIED" => "N",
				   "DEFAULT_VALUE" => "",
				   "SORT" => 100,
				   "CODE" => "IPOLSDEK_CNTDTARIF",
				   "USER_PROPS" => "N",
				   "IS_LOCATION" => "N",
				   "IS_LOCATION4TAX" => "N",
				   "PROPS_GROUP_ID" => $tmpVal['ID'],
				   "SIZE1" => 10,
				   "SIZE2" => 1,
				   "DESCRIPTION" => GetMessage('IPOLSDEK_prop_descr'),
				   "IS_EMAIL" => "N",
				   "IS_PROFILE_NAME" => "N",
				   "IS_PAYER" => "N",
				   "IS_FILTERED" => "Y",
				   "IS_ZIP" => "N",
				   "UTIL" => "Y"
				);
				if(!array_key_exists($payer,$existedProps))
					if(!CSaleOrderProps::Add($arFields))
						$return = false;
			}
			return $return;
		}
		if($mode=='2'){
			foreach($existedProps as $existedPropId)
				if (!CSaleOrderProps::Delete($existedPropId))
					echo "Error delete CNTDTARIF-prop id".$existedPropId."<br>";
		}
	}
	
	// местоположения 2.0, получаем id городa
	function getNormalCity($cityId){
		if(method_exists("CSaleLocation","isLocationProMigrated") && CSaleLocation::isLocationProMigrated() && strlen($cityId) > 8)
			$cityId = CSaleLocation::getLocationIDbyCODE($cityId);
		return $cityId;
	}
	// Проверка возможности доставки в город
	function isCityAvail($city,$mode=false){
		if(!is_numeric($city)){

			$cityName = str_replace(GetMessage('IPOLSDEK_LANG_YO_S'),GetMessage('IPOLSDEK_LANG_YE_S'),$city);
			$city = CSaleLocation::getList(array(),array('CITY_NAME'=>self::zaDEjsonit($city)))->Fetch();
			if($city)
				$cityId = $city['ID'];
		}else{
			$cityId = $city;
			$city = CSaleLocation::GetByID($cityId);
			$cityName = str_replace(GetMessage('IPOLSDEK_LANG_YO_S'),GetMessage('IPOLSDEK_LANG_YE_S'),$city['CITY_NAME']);
		}
		$return = false;
		if($city){
			$arCity = sqlSdekCity::getByBId($cityId);
			if($arCity['SDEK_ID']){
				$return = array('courier');
				if(CDeliverySDEK::checkPVZ($cityName))
					$return[]='pickup';
			}
		}
		return $return;
	}

	// тарифы	
	function getTarifList($params=array()){
		// type - тип, pickup или courier
		// mode - что выдавать: 
		// answer - выводить строкой (string) или массивом со значениями (array)
		$arList = array(
			'pickup'  => array(
				'usual'   => array(136,138),
				'heavy'   => array(15,17),
				'express' => array(62,63,5,10,12)
			),
			'courier' => array(
				'usual'   => array(137,139),
				'heavy'   => array(16,18),
				'express' => array(11,1,3,61,60,59,58,57,83)
			)
		);
		$blocked = unserialize(COption::GetOptionString(sdekdriver::$MODULE_ID,'tarifs','a:{}'));
		if(count($blocked) && (!array_key_exists('fSkipCheckBlocks',$params) || !$params['fSkipCheckBlocks'])){
			foreach($blocked as $key => $val)
				if(!array_key_exists('BLOCK',$val))
					unset($blocked[$key]);
			if(count($blocked))
				foreach($arList as $tarType => $arTars)
					foreach($arTars as $tarMode => $arTarIds)
						foreach($arTarIds as $key => $arTarId)
							if(array_key_exists($arTarId,$blocked))
								unset($arList[$tarType][$tarMode][$key]);
		}
		$answer = $arList;
		if($params['type']){
			if(is_numeric($params['type'])) $type = ($params['type']==136)?$type='pickup':$type='courier';
			else $type = $params['type'];
			$answer = $answer[$type];
			
			if($params['mode'] && array_key_exists($params['mode'],$answer))
				$answer = $answer[$params['mode']];
		}
		
		if(array_key_exists('answer',$params)){
			$answer = self::arrVals($answer);
			if($params['answer'] == 'string'){
				$answer = implode(',',$answer);
				$answer = substr($answer,0,strlen($answer)-1);
			}
		}
		return $answer;
	}

	function checkTarifAvail($profile = false){ // проверяет доступность рассчета доставки по отключенным тарифам
		$tarifs = self::getTarifList(array('type'=>$profile,'answer'=>'array'));
		return (count($tarifs)>0);
	}

	function arrVals($arr){
		$return = array();
		foreach($arr as $key => $val)
			if(is_array($val))
				$return = array_merge($return,self::arrVals($val));
			else
				$return []= $val;
		return $return;
	}
}
?>