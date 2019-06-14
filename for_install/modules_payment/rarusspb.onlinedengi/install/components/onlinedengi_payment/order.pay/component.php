<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();?><?

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SOA_MODULE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("currency"))
{
	ShowError(GetMessage("SOA_CURRENCY_MODULE_NOT_INSTALL"));
	return;
}

if(!CModule::IncludeModule('rarusspb.onlinedengi')) {
	return;
}

if($_REQUEST['AJAX_OD'] == 'Y'){
	CSalePaySystemAction::InitParamArrays(array(),$_REQUEST['ORDER_ID']);
}

$arResult = array();
$arResult['ERRORS'] = array();
$arResult['sCurPage'] = $GLOBALS['APPLICATION']->GetCurPageParam();
$arResult['FIELDS']['project'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_PROJECT');
$arResult['FIELDS']['source'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_SOURCE');

$arResult['FIELDS']['order_id'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_ORDER_ID');
$arResult['FIELDS']['nickname'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_NICKNAME');
$arResult['FIELDS']['nick_extra'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_NICK_EXTRA');
$arResult['FIELDS']['comment'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_COMMENT');
$arResult['PAYMENT'] = CSalePaySystem::GetByID($GLOBALS['SALE_INPUT_PARAMS']['ORDER']['PAY_SYSTEM_ID'], $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['PERSON_TYPE_ID']);
$arResult['ORDER']['AMOUNT'] = CSalePaySystemAction::GetParamValue('ONLINEDENGI_AMOUNT');
$arResult['ORDER']['CURRENCY'] = $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['CURRENCY'];
$arResult['ORDER']['ID'] = $GLOBALS['SALE_INPUT_PARAMS']['ORDER']['ID'];

// получим список доступных способов оплаты
$arResult['arOnlineDengiAvailablePaymentTypes'] = COnlineDengiPayment::GetPaymentTypesList();
$arResult['arModeTypeList'] = array();
// подготовим список для предоставления выбора покупателю
foreach($arResult['arOnlineDengiAvailablePaymentTypes'] as $arPaymentType) {
	// код валюты обязательное поле
	if(!empty($arPaymentType['currency'])) {
		$iTmpVal = intval(CSalePaySystemAction::GetParamValue('ONLINEDENGI_AVAILABLE_TYPE_'.$arPaymentType['id']));
		if($iTmpVal > 0) {
			$arResult['arModeTypeList'][] = array(
				'value' => $arPaymentType['id'],
				'description' => GetMessage($arPaymentType['lang']),
				'img' => $arPaymentType['img']
			);
		}
	}
}

$arResult['bAdminModeTypeDefined'] = (!empty($arResult['arModeTypeList']) && count($arResult['arModeTypeList']) == 1);

if(!$arResult['bAdminModeTypeDefined'] && $_SERVER['REQUEST_METHOD'] == 'POST' && intval($_REQUEST['ORDER_ID']) > 0) {
	// получим способ оплаты из формы
	$arResult['FIELDS']['mode_type'] = intval($_POST['mode_type']);
}

// Обработаем способы оплаты
if(empty($arResult['FIELDS']['mode_type'])) {
	// доступные способы оплаты не заданы в настройках платежной системы
	if(empty($arResult['arModeTypeList'])) {
		$arResult['ERRORS']['ERR_ONLINEDENGI_MODE_TYPES_EMPTY'] = GetMessage('ERR_ONLINEDENGI_MODE_TYPES_EMPTY');
	} else {
		// если всего один способ оплаты доступен, то установим его автоматом
		if($arResult['bAdminModeTypeDefined']) {
			reset($arResult['arModeTypeList']);
			$arResult['FIELDS']['mode_type'] = intval($arResult['arModeTypeList'][key($arResult['arModeTypeList'])]['value']);
		}
	}
} else {
	// проверим доступен ли выбранный способ оплаты
	if(empty($arResult['arOnlineDengiAvailablePaymentTypes'][$arResult['FIELDS']['mode_type']]) || empty($arResult['arOnlineDengiAvailablePaymentTypes'][$arResult['FIELDS']['mode_type']]['currency'])) {
		$arResult['FIELDS']['mode_type'] = false;
		$arResult['ERRORS']['ERR_ONLINEDENGI_MODE_TYPE_WRONG'] = GetMessage('ERR_ONLINEDENGI_MODE_TYPE_WRONG');
	}
}

// подготовка поля amount для выбранного способа
if($arResult['FIELDS']['mode_type']) {
	$arCurPaymentModeType =& $arResult['arOnlineDengiAvailablePaymentTypes'][$arResult['FIELDS']['mode_type']];
	if(!empty($arResult['PAYMENT']['CURRENCY']) && !empty($arCurPaymentModeType['currency'])) {
		$arCurPaymentModeType['currency'] = strtoupper($arCurPaymentModeType['currency']);
		$arResult['ORDER']['CURRENCY'] = strtoupper($arResult['ORDER']['CURRENCY']);
		$arResult['FIELDS']['amount'] = $arResult['ORDER']['AMOUNT'];

		// конвертируем в валюту выбранного способа оплаты если нужно
		if($arCurPaymentModeType['currency'] != $arResult['ORDER']['CURRENCY']) {
			// режим округления к большему или арифметическое
			$bRoundUp = intval(CSalePaySystemAction::GetParamValue('ONLINEDENGI_CONVERT_ROUND_UP')) == 1;
			// трёхбуквенные обозначения валют согласно стандарту ISO 4217 (http://www.iso.org/iso/support/currency_codes_list-1.htm)
			$arResult['FIELDS']['amount'] = COnlineDengiPayment::ConvertCurrancyAmount($arResult['FIELDS']['amount'], $arResult['ORDER']['CURRENCY'], $arCurPaymentModeType['currency'], $arCurPaymentModeType['precission'], $bRoundUp);
			if(!$arResult['FIELDS']['amount']) {
				$arResult['ERRORS']['ERR_ONLINEDENGI_CURRENCY_CONVERT'] = GetMessage('ERR_ONLINEDENGI_CURRENCY_CONVERT');
			}
		}
	} else {
		$arResult['ERRORS']['ERR_ONLINEDENGI_CURRENCY_WRONG'] = GetMessage('ERR_ONLINEDENGI_CURRENCY_WRONG');
	}
}

$this->IncludeComponentTemplate();
