<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (CModule::IncludeModule("sale")) {
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$header = ""; 
		
		// Read the post from OKPAY and add 'ok_verify' 
		$req = 'ok_verify=true'; 
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exits = true;
		} 
		foreach ($_POST as $key => $value) { 
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value)); 
			} else { 
				$value = urlencode($value); 
			}
			$req .= "&$key=$value";  
		}
		// Post back to OKPAY to validate 
		$header .= "POST /ipn-verify.html HTTP/1.0\r\n"; 
		$header .= "Host: www.okpay.com\r\n"; 
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; 
		$fp = fsockopen ('www.okpay.com', 80, $errno, $errstr, 30); 
		
		// Process validation from OKPAY
		if (!$fp) {
			// HTTP ERROR
		} else {
		// NO HTTP ERROR  
			fputs ($fp, $header . $req); 
			while (!feof($fp)) {
				$res = fgets ($fp, 1024); 
				if (strcmp ($res, "VERIFIED") == 0) {
					if ($arOrder = CSaleOrder::GetByID(intval($_POST["ok_invoice"]))) {
						CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);
						$strPS_STATUS_DESCRIPTION = $strPS_STATUS_MESSAGE = $strCOMMENTS = "";
						if($_POST['ok_txn_kind']=='payment_link' && $_POST['ok_txn_status']=='completed') {
							if (strlen($_POST["ok_ipn_test"]) > 0)
								$strPS_STATUS_DESCRIPTION .= "тестовый режим, реально деньги не переводились; ";
							$strPS_STATUS_DESCRIPTION .= "кошелек продавца - ".$_POST["ok_receiver_wallet"]."; ";
							$strPS_STATUS_DESCRIPTION .= "ID продавца - ".$_POST["ok_receiver_id"]."; ";
							$strPS_STATUS_DESCRIPTION .= "E-mail продавца - ".$_POST["ok_receiver_email"]."; ";
							$strPS_STATUS_DESCRIPTION .= "номер операции - ".$_POST["ok_invoice"]."; ";
							$strPS_STATUS_DESCRIPTION .= "дата платежа - ".$_POST["ok_txn_datetime"]."";
							
							$strPS_STATUS_MESSAGE .= "Уникальный ID счета покупателя - ".$_POST["ok_payer_id"]."; ";
							$strPS_STATUS_MESSAGE .= "Имя и Фамилия покупателя - ".$_POST["ok_payer_first_name"]." ".$_POST["ok_payer_last_name"]."; ";
							
							$strCOMMENTS .= "Является ли покупатель удостоверенным покупателем OKPAY - ".($_POST["ok_payer_status"]=='verified'?'Покупатель удостоверил свой счет в OKPAY':'Покупатель имеет непроверенный счет в OKPAY')."; ";
							$strCOMMENTS .= "Электронный адрес покупателя - ".$_POST["ok_payer_email"]."; ";
							$strCOMMENTS .= "Телефонный номер покупателя - ".$_POST["ok_payer_phone"]."; ";
							$strCOMMENTS .= "Страна адреса покупателя - ".$_POST["ok_payer_country"]."; ";
							$strCOMMENTS .= "Город адреса покупателя - ".$_POST["ok_payer_city"]."; ";
							$strCOMMENTS .= "ISO 3166 код страны, связанный с адресом покупателя - ".$_POST["ok_payer_country_code"]."; ";
							$strCOMMENTS .= "Штат/провинция/регион адреса покупателя - ".$_POST["ok_payer_state"]."; ";
							$strCOMMENTS .= "Улица адреса покупателя - ".$_POST["ok_payer_street"]."; ";
							$strCOMMENTS .= "Zip/почтовый индекс адреса покупателя - ".$_POST["ok_payer_zip"]."; ";
							$strCOMMENTS .= "Название адреса покупателя - ".$_POST["ok_payer_address_name"]."; ";
							$strCOMMENTS .= "Подтвердил ли покупатель свой адрес - ".($_POST["ok_payer_address_status"]=='confirmed'?'Покупатель предоставил подтвержденный адрес':'Покупатель предоставил неподтвержденный адрес')."; ";
							$strCOMMENTS .= "Имя компании покупателя - ".$_POST["ok_payer_business_name"]."; ";
							$strCOMMENTS .= "Бизнес оценка покупателя - ".$_POST["ok_payer_reputation"]."; ";
							$strCOMMENTS .= "Комментарии платильщика - ".$_POST["ok_txn_comment"]."";
							// You can comment this code if you want PAYED flag not to be set automatically
							if ($arOrder["PRICE"] == $_POST["ok_txn_gross"]) {
								CSaleOrder::PayOrder($arOrder["ID"], "Y");
							}
						}
						if(strlen($strPS_STATUS_DESCRIPTION)>0 && strlen($strPS_STATUS_MESSAGE)>0 && strlen($strCOMMENTS)>0) {
							$arFields = array(
								"PS_STATUS" => $_POST['ok_txn_status']=='completed' ? "Y" : "N",
								"PS_STATUS_CODE" => "-",
								"PS_STATUS_DESCRIPTION" => $strPS_STATUS_DESCRIPTION,
								"PS_STATUS_MESSAGE" => $strPS_STATUS_MESSAGE,
								"PS_SUM" => $_POST["ok_txn_gross"],
								"PS_CURRENCY" => $arOrder["CURRENCY"],
								"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
								"USER_ID" => $arOrder["USER_ID"],
								"COMMENTS" => $strCOMMENTS
							);
							CSaleOrder::Update($arOrder["ID"], $arFields);
							if(!empty($arParams['SET_STATUS_AFTER_PAYMENT'])){
								CSaleOrder::StatusOrder($arOrder["ID"], $arParams['SET_STATUS_AFTER_PAYMENT']);
							}
						}
					}
				} else if (strcmp ($res, "INVALID") == 0) { 
					
				}
			}
		fclose ($fp); 
		}
	}
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>