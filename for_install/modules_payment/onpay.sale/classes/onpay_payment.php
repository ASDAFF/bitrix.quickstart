<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include(GetLangFileName(dirname(__FILE__)."/", "/onpay_payment.php"));

class COnpayPayment {
	static $module_id = "onpay.sale";
	static $pay_url = "http://secure.onpay.ru/pay/";
	static $logo_url = "http://onpay.ru/images/onpay_logo.gif";
	static $log_path = "/upload/.log.onpay.sale";
	static $currency = array('RUR', 'EUR', 'USD',
		'WMB', 'WME', 'WMR', 'WMU', 'WMZ', 
		'LIE', 'LIQ', 'LIU', 'LIZ',
		'MBR', 'TST');
	static $form_design = array('7' => 'DESIGN_N7', '8' => 'DESIGN_N8', '9' => 'MOBILE_FORM', '10' => 'DESIGN_N10', '11' => 'DESIGN_N11');
	static $_df_pay_mode = "fix";
	static $_df_form_id = "7";

	static function toFloat($sum) {
		$sum = floatval($sum);
		if (strpos($sum, ".")) {
			$sum = round($sum, 2);
		} else {
			$sum = $sum.".0";
		}
		return $sum;
	}
	
	function _GetAllOptions() {
		$arAllOptions = Array(
			Array("login", GetMessage("ONPAY.SALE_OPTIONS_LOGIN")." ", Array("text", ""), GetMessage("ONPAY.SALE_OPTIONS_LOGIN_DESC")),
			Array("api_in_key", GetMessage("ONPAY.SALE_OPTIONS_API_IN_KEY")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_API_IN_KEY_DESC")),
			Array("success_url", GetMessage("ONPAY.SALE_OPTIONS_SUCCESS_URL")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_SUCCESS_URL_DESC")),
			Array("fail_url", GetMessage("ONPAY.SALE_OPTIONS_FAIL_URL")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_FAIL_URL_DESC")),
			Array("form_id", GetMessage("ONPAY.SALE_OPTIONS_FORM")." ", Array("form_id"), GetMessage("ONPAY.SALE_OPTIONS_FORM_DESC")),
			Array("convert", GetMessage("ONPAY.SALE_OPTIONS_CONVERT")." ", Array("checkbox", 60), GetMessage("ONPAY.SALE_OPTIONS_CONVERT_DESC")),
			Array("price_final", GetMessage("ONPAY.SALE_OPTIONS_PRICE_FINAL")." ", Array("checkbox", 60), GetMessage("ONPAY.SALE_OPTIONS_PRICE_FINAL_DESC")),
			Array("form_lang", GetMessage("ONPAY.SALE_OPTIONS_LANG")." ", Array("lang", 60), GetMessage("ONPAY.SALE_OPTIONS_LANG_DESC")),
		);
		if(CModule::IncludeModule("currency")) {
			$lcur = CCurrency::GetList(($b="name"), ($order1="asc"), LANGUAGE_ID);
			while($lcur_res = $lcur->Fetch()) {
				$arAllOptions[] = Array("currency_".$lcur_res['CURRENCY'], GetMessage("ONPAY.SALE_OPTIONS_CURRENCY", array("#CURRENCY#"=>$lcur_res['CURRENCY']))." ", Array("currency"), GetMessage("ONPAY.SALE_OPTIONS_CURRENCY_DESC"));
			}
		}
		$arAllOptions[] = Array("ext_params", GetMessage("ONPAY.SALE_OPTIONS_EXT_PARAMS")." ", Array("text", 60), GetMessage("ONPAY.SALE_OPTIONS_EXT_PARAMS_DESC"));
		$arAllOptions[] = Array("width_debug", GetMessage("ONPAY.SALE_OPTIONS_WIDTH_DEBUG")." ", Array("checkbox", 60), GetMessage("ONPAY.SALE_OPTIONS_WIDTH_DEBUG_DESC"));
		
		return $arAllOptions;
	}

	static function GetWMCurrency($currency) {
		$arCurrency = array();
		if(CModule::IncludeModule("currency")) {
			$lcur = CCurrency::GetList(($b="name"), ($order1="asc"), LANGUAGE_ID);
			while($lcur_res = $lcur->Fetch()) {
				$arCurrency[$lcur_res['CURRENCY']] = COption::GetOptionString(self::$module_id, "currency_".$lcur_res['CURRENCY']);
			}
		}
		if(isset($arCurrency, $currency)) $currency = $arCurrency[$currency];
		return $currency;
	}
	
	static function GetLogin($login="") {
		return $login ? $login : COption::GetOptionString(self::$module_id, "login", "");
	}
	
	static function GetApiInKey($key="") {
		return $key ? $key : COption::GetOptionString(self::$module_id, "api_in_key", "");
	}
	
	static function GetSuccessUrl() {
		return COption::GetOptionString(self::$module_id, "success_url", "");
	}
	
	static function GetConvert() {
		return COption::GetOptionString(self::$module_id, "convert", "Y");
	}
	
	static function GetFormId() {
		return COption::GetOptionString(self::$module_id, "form_id", "7");
	}
	
	static function GetPriceFinal() {
		return COption::GetOptionString(self::$module_id, "price_final", "N");
	}
	
	static function GetLang() {
		return COption::GetOptionString(self::$module_id, "form_lang", false);
	}
	
	static function GetExtParams() {
		return COption::GetOptionString(self::$module_id, "ext_params", false);
	}
	
	static function GetWidthDebug() {
		return COption::GetOptionString(self::$module_id, "width_debug", false);
	}
	
	function SaveLog($data) {
		if(!isset($GLOBALS[self::$module_id]["width_debug"])) {
			$GLOBALS[self::$module_id]["width_debug"] = self::GetWidthDebug();
		}
		if($GLOBALS[self::$module_id]["width_debug"] == 'Y' ) {
			$log_name = $_SERVER['DOCUMENT_ROOT'].self::$log_path;
			if(!file_exists($log_name)) {
				mkdir($log_name);
				chmod($log_name, BX_DIR_PERMISSIONS);
			}
			$log_name .= "/".date('d').".log";
			$td = mktime(0, 0, 0, intval(date("m")), intval(date("d")), intval(date("Y")));
			$log_open = (file_exists($log_name) && filemtime($log_name) < $td) ? "w" : "a+";
			if($fh = fopen($log_name, $log_open)) {
				fwrite($fh, date("d.m.Y H:i:s")." ip:{$_SERVER['REMOTE_ADDR']} => http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\n");
				if(is_array($data)) {
					$key = $data['key'] && in_array($data['type'], array('check', 'pay')) ? $data['key'] : false ;
					$str = serialize($data);
					if($key) {
						$str = str_replace($key, "#KEY#", $str);
					}
				} else {
					$str = $data;
				}
				fwrite($fh, $str."\n");
				fclose($fh);
				chmod($log_name, BX_FILE_PERMISSIONS);
			}
		}
	}
	
	function SaveError2Log() {
		global $APPLICATION;
		self::SaveLog("LAST_ERROR: ".var_export($APPLICATION->LAST_ERROR, true));
	}
	
	function CheckOrderPayed($order_id) {
		$order_id = intval($order_id);
		$ret = false;
		
		if($order_id > 0 && CModule::IncludeModule("sale") && ($arOrder = CSaleOrder::GetByID($order_id))) {
			$ret = ($arOrder['PAYED'] == 'Y');
		}
		
		return $ret;
	}
	
	function CheckAction($request) {
		self::SaveLog($request);
		$check = array(
			'type' => 'check',
			'pay_for' => intval($request['pay_for']),
			'amount' => self::toFloat($request['order_amount']),
			'currency' => trim($request['order_currency']),
			'code' => 2,
			'key' => self::GetApiInKey(),
			);
		$text = "Error order_id: {$check['pay_for']}";
		$order_amount = floatval($request['order_amount']);
		if(self::_Validate($request) && CModule::IncludeModule("sale") && ($arOrder = CSaleOrder::GetByID($request['ORDER_ID']))) {
			self::SaveLog($arOrder);
			$needSum = floatval($arOrder['PRICE']) - floatval($arOrder['SUM_PAID']);
			$currency = self::GetWMCurrency($arOrder['CURRENCY']);
			if($arOrder['PAYED'] == 'N' && $needSum <= $order_amount && $currency == $check['currency']) {
				$check['code'] = 0;
				$text = "OK";
			}
		}
		$check['md5_string'] = implode(";", $check);
		$check['md5'] = strtoupper(md5($check['md5_string']));
		self::SaveLog($check);
		$out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<result>
<code>{$check['code']}</code>
<pay_for>{$check['pay_for']}</pay_for>
<comment>{$text}</comment>
<md5>{$check['md5']}</md5>
</result>";
		echo $out;
		self::SaveLog($out."\n\n");
	}
	
	function PayAction($request) {
		self::SaveLog($request);
		$_request = $request;
		$pay = $payOut = array(
			'type' => 'pay',
			'pay_for' => intval($request['pay_for']),
			'onpay_id' => intval($request['onpay_id']),
			'order_id' => intval($request['pay_for']),
			'amount' => self::toFloat($request['order_amount']),
			'currency' => trim($request['order_currency']),
			'code' => 3,
			'key' => self::GetApiInKey(),
			);
		unset($pay['code']);
		unset($pay['order_id']);
		$pay['md5_string'] = implode(";", $pay);
		$pay['md5'] = strtoupper(md5($pay['md5_string']));
		$order_amount = floatval($request['order_amount']);
		$text = "Error in parameters data";
		if(self::_Validate($request) && CModule::IncludeModule("sale")) {
			$text = "Cannot find any pay rows acording to this parameters: wrong payment";
			if($arOrder = CSaleOrder::GetByID($request['ORDER_ID'])) {
				self::SaveLog($arOrder);
				$needSum = floatval($arOrder['PRICE']) - floatval($arOrder['SUM_PAID']);
				$currency = self::GetWMCurrency($arOrder['CURRENCY']);
				if($arOrder['PAYED'] == 'N' && $needSum <= $order_amount && $currency == $pay['currency']) {
					if($pay['md5'] != $request['md5']) {
						$text = "Md5 signature is wrong";
						$payOut['code'] = 7;
					} else {
						$arFields = array(
							'PS_STATUS' => 'Y',
							'PS_STATUS_CODE' => '0',
							'PS_STATUS_DESCRIPTION' => 'OK',
							'PS_STATUS_MESSAGE' => '',
							'PS_SUM' => floatval($arOrder['PS_SUM']) + $order_amount,
							'PS_CURRENCY' => $pay['currency'],
							'PS_RESPONSE_DATE' => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
							);
						foreach($_request as $key=>$val) {
							if(!in_array($key, array('onpay_id', 'pay_for', 'paid_amount', 'paymentDateTime'))) continue;
							if($val) $arFields['PS_STATUS_MESSAGE'] .= "{$key}:{$val};\n";
						}
						self::SaveLog($arFields);
						if(CSaleOrder::PayOrder($arOrder["ID"], "Y")) {
							$payOut['code'] = 0;
							$text = "OK";
							if(CSaleOrder::Update($arOrder["ID"], $arFields) === false) {
								self::SaveError2Log();
							}
						} else {
							self::SaveError2Log();
							$text = "Error in mechant database queries: operation or balance tables error";
						}
					}
				}
			}
		}
		$payOut['md5_string'] = implode(";", $payOut);
		$payOut['md5'] = strtoupper(md5($payOut['md5_string']));
		self::SaveLog($pay);
		self::SaveLog($payOut);
		$out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<result>
<code>{$payOut['code']}</code>
<comment>{$text}</comment>
<onpay_id>{$payOut['onpay_id']}</onpay_id>
<pay_for>{$payOut['pay_for']}</pay_for>
<order_id>{$payOut['order_id']}</order_id>
<md5>{$payOut['md5']}</md5>
</result>";
		echo $out;
		self::SaveLog($out."\n\n");
	}
	
	static function _Validate(&$request) {
		$request['ORDER_ID'] = intval($request['pay_for']);
		if($request['type'] == 'check') {
			return ($request['ORDER_ID']>0);
		} elseif($request['type'] == 'pay') {
			$request['error'] = "";
			if (empty($request['onpay_id'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_ORDER_EMPTY");
			} else {
				if (!is_numeric(intval($request['onpay_id']))) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			if (empty($request['order_amount'])) {
				$error .= GetMessage("ONPAY.SALE_SUM_EMPTY");
			} else {
				if (!is_numeric($request['order_amount'])) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			if (empty($request['balance_amount'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_SUM_EMPTY");
			} else {
				if (!is_numeric(intval($request['balance_amount']))) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			if (empty($request['balance_currency'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_EMPTY");
			} else {
				if (strlen($request['balance_currency'])>4) {
					$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_LONG");
				}
			}
			if (empty($request['order_currency'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_EMPTY");
			} else {
				if (strlen($request['order_currency'])>4) {
					$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_LONG");
				}
			}
			if (empty($request['exchange_rate'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_SUM_EMPTY");
			} else {
				if (!is_numeric($request['exchange_rate'])) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			return empty($request['error']);
		}
		return false;
	}

}

class COnpayPaymentV2 extends COnpayPayment {
	
	function GetData() {
		$ret = false;
		if(function_exists('json_decode')) {
			if(isset($GLOBALS['__inputData'])) {
				$ret = $GLOBALS['__inputData'];
			} elseif($hSource = fopen('php://input', 'r')) {
				$input = "";
				while (!feof($hSource)) {
					$input .= fread($hSource, 1024);
				}
				fclose($hSource);
				$input = trim($input);
				
				$ret = json_decode($input, true);
				if(is_null($ret)) $ret = json_decode(iconv("cp1251", "utf-8", $input), true);
				
				$GLOBALS['__inputData'] = $ret;
			}
		}
		return $ret;
	}
	
	function CheckAction($request) {
		$check = array(
			'type' => 'check',
			'pay_for' => intval($request['pay_for']),
			'amount' => self::toFloat($request['amount']),
			'currency' => trim($request['way']),
			'mode' => trim($request['mode']),
			'key' => self::GetApiInKey(),
			);
		$check['signature_string'] = implode(";", $check);
		$check['signature'] = sha1($check['signature_string']);
		$checkOut = array(
			'type' => 'check',
			'status' => 'false',
			'pay_for' => intval($request['pay_for']),
			'key' => self::GetApiInKey(),
			);
		$order_amount = floatval($request['amount']);
		if(self::_Validate($request, $check['signature']) && CModule::IncludeModule("sale") && ($arOrder = CSaleOrder::GetByID($request['ORDER_ID']))) {
			self::SaveLog($arOrder);
			$needSum = floatval($arOrder['PRICE']) - floatval($arOrder['SUM_PAID']);
			$currency = self::GetWMCurrency($arOrder['CURRENCY']);
			if($arOrder['PAYED'] == 'N' && $needSum <= $order_amount && $currency == $check['currency']) {
				$checkOut['status'] = 'true';
			}
		}
		self::_Response($checkOut, $request);
	}
	
	static function _ar2text($ar, $tbc=0) {
		$ret = "";
		$tb = str_repeat("\t", $tbc);
		if(is_array($ar)) foreach($ar as $key=>$val) {
			if(is_array($val)) {
				$ret .= $tb.$key."\n".self::_ar2text($val, $tbc+1);
			} else {
				$ret .= $tb.$key.":".$val.";\n";
			}
		}
		return $ret;
	}
	
	function PayAction($request) {
		$_request = $request;
		$pay = array(
			'type' => 'pay',
			'pay_for' => intval($request['pay_for']),
			'payment.amount' => self::toFloat($request['payment']['amount']),
			'payment.currency' => trim($request['payment']['way']),
			'amount' => self::toFloat($request['balance']['amount']),
			'currency' => trim($request['balance']['way']),
			'key' => self::GetApiInKey(),
			);
		$pay['signature_string'] = implode(";", $pay);
		$pay['signature'] = sha1($pay['signature_string']);
		$payOut = array(
			'type' => 'pay',
			'status' => 'false',
			'pay_for' => intval($request['pay_for']),
			'key' => self::GetApiInKey(),
			);
		$order_amount = floatval($request['balance']['amount']);
		if(self::_Validate($request, $pay['signature']) && CModule::IncludeModule("sale")) {
			if($arOrder = CSaleOrder::GetByID($request['ORDER_ID'])) {
				self::SaveLog($arOrder);
				$needSum = floatval($arOrder['PRICE']) - floatval($arOrder['SUM_PAID']);
				$currency = self::GetWMCurrency($arOrder['CURRENCY']);
				if($arOrder['PAYED'] == 'N' && $needSum <= $order_amount && $currency == $pay['currency']) {
					$arFields = array(
							'PS_STATUS' => 'Y',
							'PS_STATUS_CODE' => '0',
							'PS_STATUS_DESCRIPTION' => 'OK',
							'PS_STATUS_MESSAGE' => self::_ar2text($_request['payment']),
							'PS_SUM' => floatval($arOrder['PS_SUM']) + $order_amount,
							'PS_CURRENCY' => $pay['currency'],
							'PS_RESPONSE_DATE' => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
							);
					self::SaveLog($arFields);
					if(CSaleOrder::PayOrder($arOrder["ID"], "Y")) {
						$payOut['status'] = 'true';
						if(CSaleOrder::Update($arOrder["ID"], $arFields) === false) {
							self::SaveError2Log();
						}
					} else {
						self::SaveError2Log();
						self::SaveLog("LAST_ERROR: ".var_export($APPLICATION->LAST_ERROR, true));
					}
				}
			}
		}
		self::_Response($payOut, $request);
	}
	
	static function _Response($response, $request) {
		if($request) {
			self::SaveLog($request);
		}
		$response['signature_string'] = implode(";", $response);
		$response['signature'] = sha1($response['signature_string']);
		$out = "{\"status\":{$response['status']},\"pay_for\":\"{$response['pay_for']}\",\"signature\":\"{$response['signature']}\"}";
		self::SaveLog($out."\n\n");
		
		header("Content-type: application/json; charset=utf-8");
		echo iconv("cp1251", "utf-8", $out);
	}
	
	static function _Validate(&$request, $signature) {
		$request['ORDER_ID'] = intval($request['pay_for']);
		if($request['signature'] != $signature) {
			$request['error'] .= GetMessage("ONPAY.SALE_MD5_WRONG");
		}
		if($request['type'] == 'check') {
			$request['error'] = "";
			if($request['ORDER_ID'] <= 0) {
				$request['error'] .= GetMessage("ONPAY.SALE_ORDER_EMPTY");
			}
		} elseif($request['type'] == 'pay') {
			$request['error'] = "";
			if (empty($request['payment']['id'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_ORDER_EMPTY");
			} else {
				if (!is_numeric(intval($request['payment']['id']))) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			if (empty($request['payment']['amount'])) {
				$error .= GetMessage("ONPAY.SALE_SUM_EMPTY");
			} else {
				if (!is_numeric($request['payment']['amount'])) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			if (empty($request['balance']['amount'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_SUM_EMPTY");
			} else {
				if (!is_numeric(intval($request['balance']['amount']))) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
			if (empty($request['payment']['way'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_EMPTY");
			} else {
				if (strlen($request['payment']['way'])>4) {
					$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_LONG");
				}
			}
			if (empty($request['balance']['way'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_EMPTY");
			} else {
				if (strlen($request['balance']['way'])>4) {
					$request['error'] .= GetMessage("ONPAY.SALE_CURRENCY_LONG");
				}
			}
			if (empty($request['payment']['rate'])) {
				$request['error'] .= GetMessage("ONPAY.SALE_SUM_EMPTY");
			} else {
				if (!is_numeric($request['payment']['rate'])) {
					$request['error'] .= GetMessage("ONPAY.SALE_NOT_NUMERIC");
				}
			}
		} else {
			return false;
		}
		return empty($request['error']);
	}
}
?>