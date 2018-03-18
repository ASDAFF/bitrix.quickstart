<?/** * * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс. * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010 * */
class COnlineDengiPayment {
	// следовать по редиректу при запросе курсов валют или нет (например, при 301 статусе)
	protected static $bCurrencyFollowRedirect = true;
	// проверять ли статус 200 при получении результата курсов валют
	protected static $bCurrencyCheckStatus200 = true;
	protected static $arPaymentsList = false;
			
	// singletone
	private function __construct() {}
	public function __clone() {
        	trigger_error('Clone is not allowed.', E_USER_ERROR);
    	}

	public static function CheckPaymentRequiredFields($arFields) {
		$bOk = true;
		$bOk = isset($arFields['id']) ? $bOk : false;
		$bOk = !empty($arFields['currency']) ? $bOk : false;
		$bOk = !empty($arFields['classname']) ? $bOk : false;
		return $bOk;
	}

	// инициализация списка доступных способов оплаты (платежных систем)
	public static function SetPaymentTypesList() {
		$arOnlineDengiAvailablePaymentTypes = array();
		$rsItems = GetModuleEvents('onlinedengi_payment', 'OnPaymentsGetList');
		while($arItem = $rsItems->Fetch()) {
			$arItemEvent = ExecuteModuleEvent($arItem);			//echo "<pre>";print_r($arItemEvent);echo "</pre>";						//echo "<pre>";print_r($arItemEvent);echo "</pre>";			
			if(self::CheckPaymentRequiredFields($arItemEvent)) {
				$arItemEvent['precission'] = isset($arItemEvent['precission']) ? intval($arItemEvent['precission']) : 2;
				$arItemEvent['display_currency'] = isset($arItemEvent['display_currency']) ? $arItemEvent['display_currency'] : $arItemEvent['currency'];
				$arOnlineDengiAvailablePaymentTypes[$arItemEvent['id']] = $arItemEvent;
			}
		}		uasort($arOnlineDengiAvailablePaymentTypes, "COnlineDengiPayment::PaymentsListSort");
		self::$arPaymentsList = $arOnlineDengiAvailablePaymentTypes;
	}
	public static function PaymentsListSort($first, $second){		if($first['sort'] > $second['sort'])		{			return 1;		}		elseif($first['sort'] < $second['sort'])		{			return -1;		}		else		{			return $first['id'] > $second['id'] ? 1 : -1 ;		}		}
	// возвращает список доступных способов оплаты через веб-форму
	public static function GetPaymentTypesList() {
		if(!self::$arPaymentsList) {
			self::SetPaymentTypesList();
		}
		return self::$arPaymentsList;
	}
	
	// сброс static-кэша доступных способов оплаты через веб-форму
	public static function ResetPaymentTypesList() {
		self::$arPaymentsList = false;
	}

	// возвращает поля запроса по умолчанию для обработчиков способов оплаты
	public static function GetModeTypeFieldsDefault() {
		$arReturn = array(
			'project' => array(
				'name' => 'project',
				'lang' => 'ONLINEDENGI_FIELD_PROJECT',
			),
			'amount' => array(
				'name' => 'amount',
				'lang' => 'ONLINEDENGI_FIELD_AMOUNT',
			),
			'nickname' => array(
				'name' => 'nickname',
				'lang' => 'ONLINEDENGI_FIELD_NICKNAME',
			),
			'mode_type' => array(
				'name' => 'mode_type',
				'lang' => 'ONLINEDENGI_FIELD_MODE_TYPE',
			),
			'source' => array(
				'name' => 'source',
				'lang' => 'ONLINEDENGI_FIELD_SOURCE',
			),
			'order_id' => array(
				'name' => 'order_id',
				'lang' => 'ONLINEDENGI_FIELD_ORDER_ID',
			),
			'comment' => array(
				'name' => 'comment',
				'lang' => 'ONLINEDENGI_FIELD_COMMENT',
			),
			'nick_extra' => array(
				'name' => 'nick_extra',
				'lang' => 'ONLINEDENGI_FIELD_NICK_EXTRA',
			),
		);
		return $arReturn;
	}

	// возвращает список полей для веб-формы выбранного способа оплаты 
	public static function GetModeTypeFields($arTypeMeta) {
		$arReturn = array();
		if(!empty($arTypeMeta) && is_array($arTypeMeta) && isset($arTypeMeta['classname']) && is_callable(array($arTypeMeta['classname'], 'GetModeTypeFields'))) {
			$arReturn = call_user_func(array($arTypeMeta['classname'], 'GetModeTypeFields'));
		} else {
			$arReturn = self::GetModeTypeFieldsDefault();
		}
		return $arReturn;
	}

	// возвращает список полей для веб-формы выбранного способа оплаты по его id
	public static function GetModeTypeFieldsById($iModeType) {
		$arReturn = array();
		$arTypeMeta = self::GetModeTypeById($iModeType);
		if(!empty($arTypeMeta)) {
			$arReturn = self::GetModeTypeFields($arTypeMeta);
		}
		return $arReturn;
	}

	// возвращает мета-данные способа оплаты по его id
	public static function GetModeTypeById($iModeType) {
		$arReturn = array();
		$iModeType = intval($iModeType);
		if(!empty($iModeType)) {
			$arModeTypes = self::GetPaymentTypesList();
			if(!empty($arModeTypes) && !empty($arModeTypes[$iModeType])) {
				$arReturn = $arModeTypes[$iModeType];
			}
		}
		return $arReturn;
	}

	// возвращает список доступных способов оплаты через веб-форму
	public static function GetSecretHash($iOrderId, $sSecretKey) {
		return md5('0'.intval($iOrderId).'0'.$sSecretKey);
	}

	// запрос курсов валют у сервиса OnlineDengi
	public static function GetArCurrencyPostData() {
		$arResult = array(
			'errstr' => false,
			'errno' => false,
			'result' => false,
			'status' => false,
		);
		$obCHTTP = new CHTTP();
		$obCHTTP->http_timeout = intval(ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_TIMEOUT);
		$obCHTTP->setFollowRedirect(self::GetCurrencyPostFollowRedirect());
		$arPostData = array(
			'xml' => '<request><action>get_currency_rates</action></request>'
		);
		$arResult['result'] = $obCHTTP->Post(ONLINEDENGI_PAYMENT_CURRENCY_REQUEST_URL, $arPostData);
		$arResult['errstr'] = $obCHTTP->errstr;
		$arResult['status'] = $obCHTTP->status;
		$arResult['errno'] = $obCHTTP->errno;
		return $arResult;
	}

	// установить режим следования по редиректу при запросе курсов валют
	public static function SetCurrencyPostFollowRedirect($bSetTo) {
		if(is_bool($bSetTo)) {
			self::$bCurrencyFollowRedirect = $bSetTo;
		}
	}
	
	public static function GetCurrencyPostFollowRedirect() {
		return self::$bCurrencyFollowRedirect;
	}

	// установить режим проверки статуса 200 при обработке результат запроса курсов валют
	public static function SetCurrencyCheckStatus200($bSetTo) {
		if(is_bool($bSetTo)) {
			self::$bCurrencyCheckStatus200 = $bSetTo;
		}
	}

	public static function GetCurrencyCheckStatus200() {
		return self::$bCurrencyCheckStatus200;
	}

	// возвращает массив с курсами валют сервиса OnlineDengi
	public static function GetCurrancyRates($bSkipCache = false) {
		static $arCurrencyRates = false;
		if($arCurrencyRates === false || $bSkipCache) {
			$arCurrencyRates = array();
                       	$sCacheId = 'onlinedengi_currency';
                       	if(!$bSkipCache && $GLOBALS['CACHE_MANAGER']->Read(ONLINEDENGI_PAYMENT_CURRENCY_CACHE_TIME, $sCacheId, ONLINEDENGI_PAYMENT_CURRENCY_CACHE_PATH)) {
                       		// берем курсы из кэша
                               	$arCurrencyRates = $GLOBALS['CACHE_MANAGER']->Get($sCacheId);
			} else {
				// обращаемся к сервису за курсами
				$arRequestResult = self::GetArCurrencyPostData();
				$bContinue = !self::GetCurrencyCheckStatus200() || $arRequestResult['status'] == 200;
				if(!empty($arRequestResult['result']) && $bContinue) {
					$arRequestResult['result'] = trim($arRequestResult['result']);
					if(strpos($arRequestResult['result'], '<response>') === 0) {
						// парсим xml
						$arTmp = xmlize_xmldata($arRequestResult['result']);
						if(!empty($arTmp['response']['#']['currency'])) {
							foreach($arTmp['response']['#']['currency'] as $arItem) {
								$arCurrencyRates[$arItem['#']['name'][0]['#']] = array(
									'name' => htmlspecialchars($arItem['#']['name'][0]['#']),
									'code' => htmlspecialchars($arItem['#']['code'][0]['#']),
									'value' => doubleval($arItem['#']['value'][0]['#']),
								);
							}
						}
					}
				}
				if(!$bSkipCache && !empty($arCurrencyRates)) {
					// сохраняем в кэше
					$GLOBALS['CACHE_MANAGER']->Set($sCacheId, $arCurrencyRates);
				}
			}
		}
		return $arCurrencyRates;
	}

	// конвертация суммы из одной валюты в другую
	// трёхбуквенные обозначения валют согласно стандарту ISO 4217 (http://www.iso.org/iso/support/currency_codes_list-1.htm)
	// @$fAmount - сумма
	// @$sCurrencyCodeFrom - валюта суммы
	// @$sCurrencyCodeTo - валюта в которую нужно конвертировать сумму
	// @$iPrecisionTo - число знаков после запятой
	// @$arCurrencyRates - кастомные курсы валют (дополнительный)
	// @$bSkipCache - не использовать кэш при получении курсов валют (дополнительный)
	public static function ConvertCurrancyAmount($fAmount, $sCurrencyCodeFrom, $sCurrencyCodeTo, $iPrecisionTo, $bRoundUp = false, $arCurrencyRates = array(), $bSkipCache = false) {
		$mReturn = false;
		$arCurrencyRates = empty($arCurrencyRates) || !is_array($arCurrencyRates) ? self::GetCurrancyRates($bSkipCache) : $arCurrencyRates;
		$fAmount = doubleval($fAmount);
		$iPrecisionTo = intval($iPrecisionTo);
		if(!empty($arCurrencyRates) && !empty($sCurrencyCodeFrom) && !empty($sCurrencyCodeTo) && $fAmount > 0) {
			$sCurrencyCodeFrom = strtoupper($sCurrencyCodeFrom);
			$sCurrencyCodeTo = strtoupper($sCurrencyCodeTo);
			if(!empty($arCurrencyRates[$sCurrencyCodeFrom]) && !empty($arCurrencyRates[$sCurrencyCodeTo])) {
				$fAmountFrom = $fAmount;
				if($sCurrencyCodeFrom != 'RUB') {
					// конвертируем сумму сначала в рубли, т.к. курсы валют относительно рубля задаются
					$fAmountFrom = $fAmount / $arCurrencyRates[$sCurrencyCodeFrom]['value'];
				}
				if($fAmountFrom > 0) {
					if($bRoundUp) {
						// если режим округления к наибольшему, например, 2.121 = 2.13
						$fAmountTo = self::RoundUp(($fAmountFrom * $arCurrencyRates[$sCurrencyCodeTo]['value']), $iPrecisionTo);
					} else {
						// если арифметическое округление, например, 2.121 = 2.12
						$fAmountTo = round(($fAmountFrom * $arCurrencyRates[$sCurrencyCodeTo]['value']), $iPrecisionTo);
					}
				}
				if($fAmountTo > 0) {
					$mReturn = $fAmountTo;
				}
			}
		}
		return $mReturn;
	}

	// Округление вверх (float)
	public static function RoundUp($fValue, $iPrecision = 2) {
		$fValue = doubleval($fValue);
		$iPrecision = intval($iPrecision);
		$iPrecision = $iPrecision > 0 ? $iPrecision : 0;
		$iDiv = pow(10, $iPrecision);
		$fValue = ceil($fValue * $iDiv) / $iDiv;
		return $fValue;
	}

	// возвращает курс для выбранного способа оплаты
	public static function GetModeTypeCurrencyRate($iModeType, $bSkipCache = false) {
		$fReturn = false;
		$iModeType = intval($iModeType);
		if($iModeType > 0) {
			$arRates = self::GetCurrancyRates($bSkipCache);
			$arModeType = self::GetModeTypeById($iModeType);
			if(!empty($arModeType) && !empty($arRates[$arModeType['currency']])) {
				$fReturn = $arRates[$arModeType['currency']];
			}
		}
		return $fReturn;
	}

	public static function SetAppErrors($arErrors) {
		$arErrors = is_array($arErrors) ? $arErrors : array($arErrors);
		$arMsg = array();
		foreach($arErrors as $mErrId => $sErrText) {
			$arMsg[] = array(
				'id' => $mErrId,
				'text' => $sErrText
			);
		}
		$obErr = new CAdminException($arMsg);
		$GLOBALS['APPLICATION']->ThrowException($obErr);
	}

	// форматирование вывода чисел
	public static function FormatNumberEx($fSum, $thousSep = ' ', $iDecimals = false) {
		$fSum = doubleval($fSum);
		if($iDecimals === false) {
			$iSum = intval($fSum);
			if($iSum != $fSum) {
				$iDecimals = strlen($fSum - $iSum) - 2;
			}
		}
		$num = number_format($fSum, $iDecimals);
		if($thousSep != ',') {
			$num = str_replace(',', $thousSep, $num);
		}
		return $num;
	}
}
