<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

	if (!CModule::IncludeModule("lightweb.components ")) return;
	
	global $USER;
	
	//������������� �������� �� ���������
	if (empty($arParams['PAYMENT_TEST_MODE'])){$arParams['PAYMENT_TEST_MODE']='Y';}
	if (empty($arParams["EVENT_NAME"])) {$arParams["EVENT_NAME"] = "LW_SPRYPAY_BUYONECLICK";}
	if (empty($arParams['USED_FIELDS'])){array(0 => "NAME",1 => "PHONE");}
	if (empty($arParams['REQUIRED_FIELDS'])){array(0 => "NAME",1 => "PHONE");}
	if (empty($arParams['FORM_NAME'])){GetMessage("SP_BOC_ORDERING");}
	if (empty($arParams['BUTTON_NAME'])){GetMessage("SP_BOC_CHECKOUT");}
	
	//��������� ������ ������������ ����� ��� ������ ������� ����������
	$arRequiredParams = array( 
		'PAYMENT_OPTIONS',//��������� �������
		'PRODUCTS_COST', //������� �������
		'PRODUCTS_DESCRIPTION',
		'PRODUCT_ID', //�������� ������
		'ORDER_SUM',
		'PAID_PROP_NAME',
		'CUSTOMER_PHONE_PROP_NAME',
		'CUSTOMER_EMAIL_PROP_NAME',
		'CUSTOMER_MESSAGE_PROP_NAME',
		'ORDER_PASSWORD_PROP_NAME',
		'FORM_ID', //���������� ������ 
		'EVENT_TEMPLATES_ADMINISTRATOR',//����������� � ������
		'EVENT_TEMPLATES_CUSTOMER',
		'SMS_RU_STATE', //��������� SMSRU
	);
	
	//��������� ����������� ���� ������������ �����
	$resCheckElement = CLWTools::ArrayCheckElement($arRequiredParams,$arParams);
	if (!empty($resCheckElement) and is_array($resCheckElement)){
		if ($USER->IsAdmin()) {
			echo GetMessage("SP_BOC_NO_COMPONENT_SETTINGS");
			$this->IncludeComponentTemplate();
			return;
		}
	}
	
	//��������� JS ����������
	CLWComponents::ConnectPlugin('jquery.lewindow');
	$component_dir = substr(__DIR__, strpos(__DIR__, "/bitrix/"), strlen(__DIR__));
	$APPLICATION->AddHeadScript($component_dir."/js/custom.js");
	
	//������������ ��������� ��� ��������
	$arParams['IN_BASE64'] = base64_encode(serialize($arParams));
	
	//URL ��� ��������� ������ �� ����
	$arParams['CHECK_ORDER_URL'] = $component_dir.'/check_order.php'; //��������� �������
	$arParams['GET_ORDER_URL'] = $component_dir.'/get_order.php'; //��������� ��������� ������
	
	$this->IncludeComponentTemplate();
?>