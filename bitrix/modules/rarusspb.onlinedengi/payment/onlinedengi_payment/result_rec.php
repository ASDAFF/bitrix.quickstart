<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?><?
//
// author Sergey Leshchenko
// mailto:dev@1-integrator.com
//

// �� ��������� ���������� ���
$sResultXml = '<code>NO</code>';
if(CModule::IncludeModule('rarusspb.onlinedengi') && $_SERVER['REQUEST_METHOD'] == 'POST') {
	// ��������� �������� ����
	include(GetLangFileName(dirname(__FILE__).'/', '/payment.php'));

	// ������������� ������� � ������� OnlineDengi (�� 20 ����)
	$_POST['paymentid'] = intval($_POST['paymentid']);
	// ID ������
	$_POST['userid'] = intval($_POST['userid']);
	// ID ��������� ������� � ��������
	$arParams['PAY_SYSTEM_ID'] = intval($arParams['PAY_SYSTEM_ID']);
	// ����� ����������
	$bPayNotifyMode = $_POST['paymentid'] > 0;

	$bCorrectPayment = true;
	if($_POST['userid'] <= 0 || !($arOrder = CSaleOrder::GetByID($_POST['userid']))) {
		$bCorrectPayment = false;
	}

	if($bCorrectPayment) {
		//�������� ����� ����� �������������� ��������� �������
		$bCorrectPayment = $arParams['PAY_SYSTEM_ID'] == $arOrder['PAY_SYSTEM_ID'] ? $bCorrectPayment : false;
	}

	if(!$bPayNotifyMode && $bCorrectPayment && $arOrder['PAYED'] == 'Y') {
		// � ������ �������� �������������� ������ �������� ������ ������
		// ���� ��� �������, �� ������� NO, ����� �� ������������� ������� ������ ������ � ���� �� ������
		$bCorrectPayment = false;
	}

	if($bCorrectPayment) {
		CSalePaySystemAction::InitParamArrays($arOrder, $arOrder['ID']);
		// ��������� ����
		$ONLINEDENGI_SECRET_KEY = CSalePaySystemAction::GetParamValue('ONLINEDENGI_SECRET_KEY');
		$sSecretHash = COnlineDengiPayment::GetSecretHash($arOrder['ID'], $ONLINEDENGI_SECRET_KEY);
		$_POST['key'] = htmlspecialchars(trim($_POST['key']));
		// ������� ������ ��������
		$fShouldPay = CSalePaySystemAction::GetParamValue('ONLINEDENGI_AMOUNT');
		if($fShouldPay <= 0 || !$sSecretHash == $_POST['key']) {
			$bCorrectPayment = false;
		}
	}

	if($bCorrectPayment) {
		if(!$bPayNotifyMode) {
			// ����� �������� �������������� ������������
			$sResultXml = '<code>YES</code>';
		} else {
			// ����� ���������� ���������� �������
			$arErrors = array();
			$fUpdateAccountValue = false;
			$bPayOrder = false;
			$iOrderPayId = false;
			$arFields = array(
				'PS_STATUS' => 'Y',
				'PS_STATUS_CODE' => '-',
				'PS_RESPONSE_DATE' => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat('FULL', LANG))),
				'USER_ID' => $arOrder['USER_ID']
			);

			// !!! ������ ��������� ������� OnlineDengi - ����� �� (RUB)
			$sSysCurrencyCode = 'RUB';
			if($arOrder['PAYED'] != 'Y' && $arOrder['PS_STATUS'] != 'Y') {
				// ������� ���������� ������� ������
				$iModeType = intval($_POST['paymode']);
				$arModeType = COnlineDengiPayment::GetModeTypeById($iModeType);
				if(!empty($arModeType)) {
					//�������� ����� ������ ������ ��� �������� ��� ��������� �������
					$iTmpVal = intval(CSalePaySystemAction::GetParamValue('ONLINEDENGI_AVAILABLE_TYPE_'.$iModeType));
					if(!$iTmpVal) {
						$arModeType = false;
					}
				}

				if(!empty($arModeType)) {
					// ����� �������� � ������ ��
					$fAmount = doubleval($_POST['amount']);
					$fAmountConverted = $arOrder['CURRENCY'] == $sSysCurrencyCode ? $fAmount : false;

					$arCurrencyRates = array();
					if(($arOrder['CURRENCY'] != $sSysCurrencyCode) || ($arModeType['currency'] != $arOrder['CURRENCY'])) {
						// ������� ����� ����� ��� �����������
						$arCurrencyRates = COnlineDengiPayment::GetCurrancyRates();
						if(empty($arCurrencyRates)) {
							$arErrors['ERR_ONLINEDENGI_RESPONSE_CURRENCY_RATES'] = GetMessage('ERR_ONLINEDENGI_RESPONSE_CURRENCY_RATES');
							$fAmountConverted = false;
						}
					}
					
					if($arOrder['CURRENCY'] != $sSysCurrencyCode && !empty($arCurrencyRates)) {
						$bRoundUp = false;
						// ��������� ���������� ����� � ������ ������
						$fAmountConverted = COnlineDengiPayment::ConvertCurrancyAmount($fAmount, $sSysCurrencyCode, $arOrder['CURRENCY'], $arModeType['precission'], $bRoundUp, $arCurrencyRates);
					}

					if($fAmountConverted) {
						$arFields['PS_CURRENCY'] = $sSysCurrencyCode; 
						$arFields['PS_SUM'] = $fAmount;
						$arFields['PS_STATUS_DESCRIPTION'] = htmlspecialchars('paymentid: '.$_POST['paymentid'].'; ');
						$arFields['PS_STATUS_DESCRIPTION'] .= GetMessage('ONLINEDENGI_REC_MODE_TYPE').'['.$iModeType.'] '.GetMessage($arModeType['lang']);
						
						$fModeTypeSum = $arOrder['CURRENCY'] == $arModeType['currency'] ? $arOrder['PRICE'] : COnlineDengiPayment::ConvertCurrancyAmount($arOrder['PRICE'], $arOrder['CURRENCY'], $arModeType['currency'], $arModeType['precission'], $arCurrencyRates);
						$arFields['PS_STATUS_MESSAGE'] = GetMessage('ONLINEDENGI_REC_MODE_TYPE_SUM').$fModeTypeSum.' '.$arModeType['display_currency'].'; ';
						if(!empty($arCurrencyRates)) {
							// ������� ����� �����, ������� ����������� � �����������
							if($arOrder['CURRENCY'] != $sSysCurrencyCode) {
								$arFields['PS_STATUS_MESSAGE'] .= GetMessage('ONLINEDENGI_REC_COURSE').'1 '.$sSysCurrencyCode.' = '.$arCurrencyRates[$arOrder['CURRENCY']]['value'].' '.$arOrder['CURRENCY'].'; ';
							}
							if($arOrder['CURRENCY'] != $arModeType['currency']) {
								$arFields['PS_STATUS_MESSAGE'] .= GetMessage('ONLINEDENGI_REC_COURSE').'1 '.$sSysCurrencyCode.' = '.$arCurrencyRates[$arModeType['currency']]['value'].' '.$arModeType['currency'].'; ';
							}
							$arFields['PS_STATUS_MESSAGE'] .= GetMessage('ONLINEDENGI_REC_PAYED').$fAmountConverted.' '.$arOrder['CURRENCY'].'; ';
						}
						
						if($arOrder['PAYED'] != 'Y') {
							if($fAmountConverted >= $fShouldPay) {
								$bPayOrder = true;
							} else {
								// ���������� ����� ������
								$iWrongPayMode = intval(CSalePaySystemAction::GetParamValue('ONLINEDENGI_WRONG_PAY_MODE'));
								if($iWrongPayMode == 1) {
									// ��������� �������� ��� ����������� ������ ������ �����
									$bPayOrder = true;
								}elseif($iWrongPayMode == -1) {
									// �������� ���������, �� �������� ������������ �� ���� ������������
									$fUpdateAccountValue = $fAmountConverted;
								}
							}
                                        	}

                                        	if($bPayOrder) {
							// !!! �������� !!! 
							// ��� �������� ����������� ����������� ����� ������������, �� ���� ����� ��������� � ������� �������� ������ ��������� ������,
							// � ��� ������, ��� ��� ������ ������ ������ ����� ����� ������� ������� �� ���� ���������� (�� ���������� ����������!!!).
							// ��������-������� ������ 8.5.2 (2009-12-14), ���������� ������������ ����������� �����, �������� ������ �����
							/*
							$bUseWithDraw = intval(CSalePaySystemAction::GetParamValue('ONLINEDENGI_USE_WITHDRAW'));
							$bUseWithDraw = $bUseWithDraw == 1;
							*/

							// ���������� �������� ������ ������ 
							$bUseWithDraw = true;
							$arAdditionalFields = array();
							CSaleOrder::PayOrder($arOrder['ID'], 'Y', $bUseWithDraw, true, 0, $arAdditionalFields);
						}

						if($fAmountConverted > $fShouldPay) {
							// ���� ��������� ���������, �� �������� ����� � ���������������� ������
							$sChangeStatusCode = trim(CSalePaySystemAction::GetParamValue('ONLINEDENGI_OVERPAY_STATUS'));
							if(strlen($sChangeStatusCode) > 0) {
								CSaleOrder::StatusOrder($arOrder['ID'], $sChangeStatusCode);
							}

							$iOverPayMode = intval(CSalePaySystemAction::GetParamValue('ONLINEDENGI_OVERPAY_MODE'));
							if($iOverPayMode == 1) {
								$fUpdateAccountValue = $fAmountConverted - $fShouldPay;
							}
						}

						if($fAmountConverted < $fShouldPay) {
							// ���� ������ ������, �� �������� ����� � ���������������� ������
							$sChangeStatusCode = trim(CSalePaySystemAction::GetParamValue('ONLINEDENGI_DEFICIT_PAY_STATUS'));
							if(strlen($sChangeStatusCode) > 0) {
								CSaleOrder::StatusOrder($arOrder['ID'], $sChangeStatusCode);
							}
						}
					} else {
						$arErrors['ERR_ONLINEDENGI_RESPONSE_WRONG_AMOUNT'] = GetMessage('ERR_ONLINEDENGI_RESPONSE_WRONG_AMOUNT');
					}
				} else {
					$arErrors['ERR_ONLINEDENGI_RESPONSE_WRONG_MODE_TYPE'] = GetMessage('ERR_ONLINEDENGI_RESPONSE_WRONG_MODE_TYPE');
				}
			}

			if(empty($arErrors)) {
				// ���������� ������ �� ������
				$iOrderPayId = intval(CSaleOrder::Update($arOrder['ID'], $arFields));
				if($fUpdateAccountValue) {
					// �������������� ��������� �� �������� ���������� �������
					$iCountTransact = CSaleUserTransact::GetList(array(), array('ORDER_ID' => $arOrder['ID'], 'DESCRIPTION' => 'ONLINEDENGI_PAYMENT'), array());
					if($iCountTransact == 0) {
						CSaleUserAccount::UpdateAccount($arOrder['USER_ID'], $fUpdateAccountValue, $arOrder['CURRENCY'], 'ONLINEDENGI_PAYMENT', $arOrder['ID']);
					}
				}
			}

			$sResponseValue = $iOrderPayId > 0 ? 'YES' : 'NO';
			$sResultXml = '';
			$sResultXml .= '<id>'.$iOrderPayId.'</id>';
			$sResultXml .= '<code>'.$sResponseValue.'</code>';
			if(!empty($arErrors)) {
				$sErrors = '';
				foreach($arErrors as $sKey=>$sText) {
					$sErrors .= $sKey.' - '.$sText.'; ';
				}
				if(LANG_CHARSET != ONLINEDENGI_PAYMENT_RESPONSE_CHARSET) {
					$sErrors = $GLOBALS['APPLICATION']->ConvertCharset($sErrors, LANG_CHARSET, ONLINEDENGI_PAYMENT_RESPONSE_CHARSET);
				}
				$sResultXml .= '<comment>'.htmlspecialchars($sErrors).'</comment>';
			}
		}
	}
}
header('Content-Type: text/html; charset='.ONLINEDENGI_PAYMENT_RESPONSE_CHARSET);
$GLOBALS['APPLICATION']->RestartBuffer();
$sResultXml = '<?xml version="1.0" encoding="'.ONLINEDENGI_PAYMENT_RESPONSE_CHARSET.'"?><result>'.$sResultXml.'</result>';
echo $sResultXml;
die();
