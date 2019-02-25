<?
	class sdekShipment{
		// города
		static $sender;
		static $receiver;

		// товары
		static $gabs;
		static $goods;

		// расчет
		static $profiles;

		function sdekShipment($params=array()){
			if(!self::checkField('RECEIVER',$params))
				return;
			if(!self::checkField('ITEMS',$params) && !self::checkField('GABS',$params))
				return;

			$this->sender = (self::checkField('SENDER',$params)) ? $params['SENDER'] : CDeliverySDEK::getHomeCity();
			$this->receiver = $params['RECEIVER'];

			if(self::checkField('ITEMS',$params)){
				$this->goods = $params['ITEMS'];
				CDeliverySDEK::setGoods($this->goods);
				$this->gabs = CDeliverySDEK::$goods;
			}else
				$this->gabs = $params['GABS'];
		}

		function calcProfile($profile){
			CDeliverySDEK::$goods = $this->gabs;
			CDeliverySDEK::$sdekSender = $this->sender;
			CDeliverySDEK::$sdekCity   = $this->receiver;

			foreach(GetModuleEvents(CDeliverySDEK::$MODULE_ID, "onBeforeRequestDelivery", true) as $arEvent)
				ExecuteModuleEventEx($arEvent,Array($profile));

			$cachename = "IPOLSDEK|$profile|".$this->sender."|".$this->receiver."|".implode('|',$this->gabs);
			$obCache = new CPHPCache();
			if($obCache->InitCache(defined("IPOLSDEK_CACHE_TIME")?IPOLSDEK_CACHE_TIME:86400,$cachename,"/IPOLSDEK/") && !defined("IPOLSDEK_NOCACHE"))
				$result = $obCache->GetVars();
			else{
				$result = CDeliverySDEK::formCalcRequest($profile);
				if($result['success']){
					$obCache->StartDataCache();
					$obCache->EndDataCache($result);
				}
			}

			if(!is_array($this->profiles))
				$this->profiles = array();

			if($result['success']){
				$addTerm = intval(COption::GetOptionString(CDeliverySDEK::$MODULE_ID,'termInc',false));
				$this->profiles[$profile] = array(
					'RESULT'   => 'OK',
					'PRICE'    => $result['price'],
					'CURRENCY'  => $result['currency'],
					'PRICE_CUR' => $result['priceByCurrency'],
					'TERMSBAZE' => array(
						'MIN' => $result['termMin'],
						'MAX' => $result['termMax']
					),
					'TERMS' => array(
						'MIN' => $result['termMin']+$addTerm,
						'MAX' => $result['termMax']+$addTerm
					),
					'TARIF' => $result['tarif']
				);
			}else{
				$erStr = '';
				foreach($result as $erCode => $erLabl)
					$erStr.="$erLabl ($erCode) ";
				$this->profiles[$profile] = array(
					'RESULT' => 'ERROR',
					'TEXT'	 => CDeliverySDEK::zaDEjsonit($erStr)
				);
			}
		}

		function calcProfiles($arProfiles){
			foreach($arProfiles as $profile)
				$this->calcProfile($profile);
		}

		function compability(){
			if(!is_array($this->profiles))
				return false;
			$arReturn = array();
			foreach($this->profiles as $profile => $result)
				if($result['RESULT'] == 'OK')
					$arReturn[] = $profile;
			return $arReturn;
		}

		private function checkField($wat,$src){
			return (array_key_exists($wat,$src) && $src[$wat]);
		}

		function getProfiles(){
			return $this->profiles;
		}

		function getProfile($profile){
			return (array_key_exists($profile,$this->profiles)) ? $this->profiles[$profile] : false;
		}

		function getProfileTarif($profile){
			return (array_key_exists($profile,$this->profiles) && $this->profiles[$profile]['RESULT'] == 'OK') ? $this->profiles[$profile]['TARIF'] : false;
		}
	}
?>