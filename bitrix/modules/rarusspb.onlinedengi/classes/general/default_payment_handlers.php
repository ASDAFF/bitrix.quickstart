<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 * Обработчики доступных платежных систем по умолчанию
 * 
 */


AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_1', 'GetPaymentMeta'), 100);
class ConlineDengiPaymentType_1{
function GetPaymentMeta() {   
	$arReturn = array(
		'currency' => 'USD',
		'display_currency' => 'WMZ',
		'precision' => '2',
		'classname' => 'ConlineDengiPaymentType_1',
		'sort' => 250,
		'id' => '1',
		'default' => '1',
		'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_1',
		'img' => 'http://www.onlinedengi.ru/img/systems/wmz.gif'
		);
	return $arReturn;
	}
}
 
AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_2', 'GetPaymentMeta'), 200);
 
class ConlineDengiPaymentType_2{
function GetPaymentMeta() {   
	$arReturn = array(
		'currency' => 'RUB',
		'display_currency' => 'WMR',
		'precision' => '2',
		'classname' => 'ConlineDengiPaymentType_2',
		'sort' => 90,
		'id' => '2',
		'default' => '1',
		'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_2',
		'img' => 'http://www.onlinedengi.ru/img/systems/wmr.gif'
		);
	return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_3', 'GetPaymentMeta'), 300);
class ConlineDengiPaymentType_3{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'EUR',
			'display_currency' => 'WME',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_3',
			'sort' => 270,
			'id' => '3',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_3',
			'img' => 'http://www.onlinedengi.ru/img/systems/wme.gif'
			);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_4', 'GetPaymentMeta'), 400);
class ConlineDengiPaymentType_4{
	function GetPaymentMeta() {   
		 $arReturn = array(
			 'currency' => 'BYR',
			 'display_currency' => 'WMB',
			 'precision' => '0',
   			'classname' => 'ConlineDengiPaymentType_4',
			'sort' => 1000,
			'id' => '4',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_4',			
			 'img' => 'http://www.onlinedengi.ru/img/systems/wmb.gif'
			);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_5', 'GetPaymentMeta'), 1000);
class ConlineDengiPaymentType_5{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'UAH',
			'display_currency' => 'WMU',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_5',
			'sort' => 1000,
			'id' => '5',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_5',			
			'img' => 'http://www.onlinedengi.ru/img/systems/wmu.gif'
			);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_6', 'GetPaymentMeta'), 600);
class ConlineDengiPaymentType_6{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'UZS',
			'display_currency' => 'WMY',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_6',
			'sort' => 700,
			'id' => '6',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_6',			
			'img' => 'http://www.onlinedengi.ru/img/systems/wmy.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_7', 'GetPaymentMeta'), 700);
class ConlineDengiPaymentType_7{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_7',
			'sort' => 70,
			'id' => '7',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_7',
			'img' => 'http://www.onlinedengi.ru/img/systems/yamoney.gif'
		);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_8', 'GetPaymentMeta'), 800);
class ConlineDengiPaymentType_8{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_8',
			'sort' => 1000,
			'id' => '8',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_8',
			'img' => 'http://www.onlinedengi.ru/img/systems/chronopay.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_9', 'GetPaymentMeta'), 900);
class ConlineDengiPaymentType_9{ 
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_9',
			'sort' => 310,
			'id' => '9',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_9',			
			'img' => 'http://www.onlinedengi.ru/img/systems/rbk.gif'
			);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_10', 'GetPaymentMeta'), 1000);
class ConlineDengiPaymentType_10{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_10',
			'sort' => 1000,
			'id' => '10',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_10',			
			'img' => 'http://www.onlinedengi.ru/img/systems/wm-cards.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_12', 'GetPaymentMeta'), 1100);
class ConlineDengiPaymentType_12{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_12',
			'sort' => 1000,
			'id' => '12',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_12',			
			'img' => 'http://www.onlinedengi.ru/img/systems/zgold-cards.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_13', 'GetPaymentMeta'), 1200);
class ConlineDengiPaymentType_13{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_13',
			'sort' => 350,
			'id' => '13',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_13',			
			'img' => 'http://www.onlinedengi.ru/img/systems/moneymail.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_14', 'GetPaymentMeta'), 1300);
class ConlineDengiPaymentType_14{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_14',
			'sort' => 110,
			'id' => '14',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_14',			
			'img' => 'http://www.onlinedengi.ru/img/systems/mk.gif'
			);
		return $arReturn;
	}
	
	function GetModeTypeFields() {
		$arReturn = COnlineDengiPayment::GetModeTypeFieldsDefault();
		$arReturn['paymode'] = array(
			'name' => 'paymode',
			'lang' => 'ONLINEDENGI_FIELD_PAYMODE',
			'value' => 'mk'
		);
		$arReturn['qiwi_phone'] = array(
			'name' => 'qiwi_phone',
			'lang' => 'ONLINEDENGI_FIELD_QIWI_PHONE',
		);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_15', 'GetPaymentMeta'), 1400);
class ConlineDengiPaymentType_15{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_15',
			'sort' => 290,
			'id' => '15',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_15',			
			'img' => 'http://www.onlinedengi.ru/img/systems/webcreds.gif'
			);
		return $arReturn;
		}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_16', 'GetPaymentMeta'), 11000);
class ConlineDengiPaymentType_16{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'BYR',
			'display_currency' => 'BYR',
			'precision' => '0',
			'classname' => 'ConlineDengiPaymentType_16',
			'sort' => 350,
			'id' => '16',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_16',			
			'img' => 'http://www.onlinedengi.ru/img/systems/easypay.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_20', 'GetPaymentMeta'), 2000);
class ConlineDengiPaymentType_20{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_20',
			'sort' => 370,
			'id' => '20',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_20',			
			'img' => 'http://www.onlinedengi.ru/img/systems/oceanbank.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_30', 'GetPaymentMeta'), 2100);
class ConlineDengiPaymentType_30{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_30',
			'sort' => 330,
			'id' => '30',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_30',			
			'img' => 'http://www.onlinedengi.ru/img/systems/intellectmoney.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_32', 'GetPaymentMeta'), 2200);
class ConlineDengiPaymentType_32{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_32',
			'sort' => 390,
			'id' => '32',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_32',			
			'img' => 'http://www.onlinedengi.ru/img/systems/dengimail_newlogo.jpg'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_39', 'GetPaymentMeta'), 2300);
class ConlineDengiPaymentType_39{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_39',
			'sort' => 150,
			'id' => '39',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_39',			
			'img' => 'http://www.onlinedengi.ru/img/systems/mobile_payment_megafon.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_40', 'GetPaymentMeta'), 2350);
class ConlineDengiPaymentType_40{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'USD',
			'display_currency' => 'USD',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_40',
			'sort' => 410,
			'id' => '40',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_40',			
			'img' => 'http://www.onlinedengi.ru/img/systems/liqpay.png'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_43', 'GetPaymentMeta'), 2400);
class ConlineDengiPaymentType_43{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_43',
			'sort' => 430,
			'id' => '43',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_43',			
			'img' => 'http://www.onlinedengi.ru/img/systems/domru.png'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_44', 'GetPaymentMeta'), 2100);
class ConlineDengiPaymentType_44{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_44',		
			'sort' => 450,
			'id' => '44',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_44',			
			'img' => 'http://www.onlinedengi.ru/img/systems/logo_tvt_88x31.jpg'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_45', 'GetPaymentMeta'), 2100);
class ConlineDengiPaymentType_45{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_45',
			'sort' => 160,
			'id' => '45',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_45',
			'img' => 'http://www.onlinedengi.ru/img/systems/mobile_payment_beeline.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_46', 'GetPaymentMeta'), 2600);
class ConlineDengiPaymentType_46{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_46',
			'sort' => 470,
			'id' => '46',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_46',			
			'img' => 'http://www.onlinedengi.ru/img/systems/alfa_bank.png'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_48', 'GetPaymentMeta'), 2700);
class ConlineDengiPaymentType_48{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_48',
			'sort' => 1000,
			'id' => '48',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_48',			
			'img' => 'http://www.onlinedengi.ru/img/systems/vtb24.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_49', 'GetPaymentMeta'), 2800);
class ConlineDengiPaymentType_49{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'USD',
			'display_currency' => 'USD',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_49',
			'sort' => 190,
			'id' => '49',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_49',			
			'img' => 'http://www.onlinedengi.ru/img/systems/paypal.png'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_50', 'GetPaymentMeta'), 2900);
class ConlineDengiPaymentType_50{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_50',
			'sort' => 130,
			'id' => '50',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_50',			
			'img' => 'http://www.onlinedengi.ru/img/systems/mobile_payment_mts_new.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_51', 'GetPaymentMeta'), 3000);
class ConlineDengiPaymentType_51{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'USD',
			'display_currency' => 'USD',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_51',
			'sort' => 210,
			'id' => '51',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_51',			
			'img' => 'http://www.onlinedengi.ru/img/systems/bank_cards_usa_europe.gif'
			);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_53', 'GetPaymentMeta'), 3100);
class ConlineDengiPaymentType_53{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_53',
			'sort' => 1000,
			'id' => '53',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_53',			
			'img' => 'http://www.onlinedengi.ru/img/systems/mc_Visa_brand_88x31.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_54', 'GetPaymentMeta'), 3200);
class ConlineDengiPaymentType_54{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_54',
			'sort' => 480,
			'id' => '54',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_54',			
			'img' => 'http://www.onlinedengi.ru/img/systems/rapida.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_55', 'GetPaymentMeta'), 3300);
class ConlineDengiPaymentType_55{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'USD',
			'display_currency' => 'USD',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_55',
			'sort' => 510,
			'id' => '55',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_55',			
			'img' => 'http://www.onlinedengi.ru/img/systems/Logo_Moneybookers.gif'
			);
		return $arReturn;
	}
}

//AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_57', 'GetPaymentMeta'), 3400);
class ConlineDengiPaymentType_57{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_57',
			'sort' => 1000,
			'id' => '57',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_57',			
			'img' => 'http://www.onlinedengi.ru/img/systems/uniteller.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_58', 'GetPaymentMeta'), 31000);
class ConlineDengiPaymentType_58{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'USD',
			'display_currency' => 'USD',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_58',	
			'sort' => 530,
			'id' => '58',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_58',			
			'img' => 'http://www.onlinedengi.ru/img/systems/libertyreserve.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_59', 'GetPaymentMeta'), 3600);
class ConlineDengiPaymentType_59{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_59',
			'sort' => 550,
			'id' => '59',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_59',			
			'img' => 'http://www.onlinedengi.ru/img/systems/pay_vkontakte.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_61', 'GetPaymentMeta'), 3700);
class ConlineDengiPaymentType_61{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_61',
			'sort' => 560,
			'id' => '61',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_61',			
			'img' => 'http://www.onlinedengi.ru/img/systems/psbank.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_62', 'GetPaymentMeta'), 3800);
class ConlineDengiPaymentType_62{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_62',
			'sort' => 230,
			'id' => '62',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_62',			
			'img' => 'http://www.onlinedengi.ru/img/systems/euroset.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_63', 'GetPaymentMeta'), 3900);
class ConlineDengiPaymentType_63{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_63',		
			'sort' => 50,
			'id' => '63',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_63',			
			'img' => 'http://www.onlinedengi.ru/img/systems/mc_Visa_brand_88x31.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_64', 'GetPaymentMeta'), 4000);
class ConlineDengiPaymentType_64{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_64',
			'sort' => 590,
			'id' => '64',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_64',
			'img' => 'http://www.onlinedengi.ru/img/systems/elecsnet.gif'
			);
		return $arReturn;
	}
}

AddEventHandler('onlinedengi_payment', 'OnPaymentsGetList', array('COnlineDengiPaymentType_65', 'GetPaymentMeta'), 4100);
class ConlineDengiPaymentType_65{
	function GetPaymentMeta() {   
		$arReturn = array(
			'currency' => 'RUB',
			'display_currency' => 'RUB',
			'precision' => '2',
			'classname' => 'ConlineDengiPaymentType_65',
			'sort' => 610,
			'id' => '65',
			'default' => '1',
			'lang' => 'ONLINEDENGI_AVAILABLE_TYPE_65',			
			'img' => 'http://www.onlinedengi.ru/img/systems/Logo_Ukash.gif'
			);
		return $arReturn;
	}
}
