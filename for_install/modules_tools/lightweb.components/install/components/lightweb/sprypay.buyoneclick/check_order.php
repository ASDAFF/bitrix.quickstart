<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("lw_components")) return false;

function redirect($url, $statusCode = 303) {
	header('Location: ' . $url, true, $statusCode);
	die();
}

function get_parameters() {
	$parameters = unserialize(base64_decode($_POST["params"]));
	return $parameters;
}

function get_request_fields() {
	$_POST['fields'] = urldecode($_POST['fields']);
	$request_fields = array();
	foreach (explode('&', $_POST['fields']) as $single_field){
		$request_field = explode('=', $single_field);
		$request_fields[$request_field[0]] = $request_field[1];
	}
	return $request_fields;
}

function ajax_handler(){
	$parameters = get_parameters();
	$request_fields = get_request_fields();
	$parameters['receiver'] = $request_fields;
	if (empty($parameters["USED_FIELDS"])) {
		$parameters["USED_FIELDS"] = array("NAME","PHONE","EMAIL");
	} else {
		$used_fields = $parameters["USED_FIELDS"];
		$allowed_fields = array('NAME','PHONE','EMAIL','MESSAGE'); 
		foreach ($parameters["USED_FIELDS"] as $k => $v){
			if (!in_array($v, $allowed_fields)){unset($used_fields[$k]);}
		}
		$parameters["USED_FIELDS"] = $used_fields;
	}
	$required_fields = $parameters["REQUIRED_FIELDS"];
	foreach ($parameters["REQUIRED_FIELDS"] as $k => $v) {
		if (!in_array($v, $parameters["USED_FIELDS"])) {unset($required_fields[$k]);}
	}
	$parameters["REQUIRED_FIELDS"] = $required_fields;
	$json_output["ERROR"] = array();
	if (!empty($parameters["REQUIRED_FIELDS"])) {
		foreach ($parameters["REQUIRED_FIELDS"] as $require_field) {
			if (!in_array($require_field, array_keys($request_fields)) || strlen($request_fields[$require_field]) <= 1) {
				$json_output["ERROR"]["REQUIRED_FIELDS"][] = $require_field;
			}
		}
	}
	
	if (empty($json_output["ERROR"])) {
		$json_output['SENT']='Y';
	} else {
		$json_output['SENT']='N';
	}
	
	echo json_encode($json_output);
}

function post_handler() {
	$arData = $_POST;
	$arData['PARAMS'] = unserialize(base64_decode($_POST['PARAMS']));
	$user_name = htmlspecialchars($_POST["NAME"]);
	$user_phone = '7'.htmlspecialchars($_POST["PHONE"]);
	$user_email = htmlspecialchars($_POST["EMAIL"]);
	$user_message = htmlspecialchars($_POST["MESSAGE"]);
	$product_id = htmlspecialchars($_POST["PRODUCT_ID"]);
	
	$obOption = new CLWOption();
	$arPaymentOptions = unserialize($obOption->Get($arData['PARAMS']['PAYMENT_OPTIONS']));
	$SP=CLWComponents::ConnectExtension('sprypay');
	
	
	$SP->SetOptions(array(
		'ACCOUNT'=>array(
			'LOGIN'=>$arPaymentOptions['LOGIN'],
			'PASSWORD'=>$arPaymentOptions['PASSWORD']
		),
		'PAYMENT_OPTIONS'=>$arData['PARAMS']['PAYMENT_OPTIONS'],
		'PAYMENT_TEST_MODE'=>$arData['PARAMS']['PAYMENT_TEST_MODE'],
		'PAYMENT_CURRENCY'=>$arData['PARAMS']['PAYMENT_CURRENCY'], // new валюта
		'PAYMENT_SECRET_KEY'=>$arData['PARAMS']['PAYMENT_SECRET_KEY'], // new пароль
		'PRODUCTS'=>array(
			'PROPERTY_COST'=>$arData['PARAMS']['PRODUCTS_COST'],
			'PRODUCTS_DESCRIPTION'=>$arData['PARAMS']['PRODUCTS_DESCRIPTION']
		),
		'ORDERS'=>array(
			'IBLOCK_TYPE_ID'=>$arData['PARAMS']['IBLOCK_TYPE_ID'],
			'IBLOCK_ID'=>$arData['PARAMS']['IBLOCK_ID'],
			'PROPERTY_PRODUCT_ID'=>$arData['PARAMS']['PRODUCT_ID'],
			'PROPERTY_SUM'=>$arData['PARAMS']['ORDER_SUM'],
			'PROPERTY_PAYMENT_STATUS'=>$arData['PARAMS']['PAID_PROP_NAME'],
			'PROPERTY_CUSTOMER_NAME'=>$arData['PARAMS']['CUSTOMER_PROP_NAME'],
			'PROPERTY_CUSTOMER_PHONE'=>$arData['PARAMS']['CUSTOMER_PHONE_PROP_NAME'],
			'PROPERTY_CUSTOMER_EMAIL'=>$arData['PARAMS']['CUSTOMER_EMAIL_PROP_NAME'],
			'PROPERTY_CUSTOMER_MESSAGE'=>$arData['PARAMS']['CUSTOMER_MESSAGE_PROP_NAME'],
			'PROPERTY_PASSWORD'=>$arData['PARAMS']['ORDER_PASSWORD_PROP_NAME'],
		),
		'SMS'=>array(
			'SMS_RU_STATE'=>$arData['PARAMS']['SMS_RU_STATE'],
			'SMS_RU_API_KEY'=>$arData['PARAMS']['SMS_RU_API_KEY'],
			'SMS_RU_FROM'=>$arData['PARAMS']['SMS_RU_FROM'],
			'SMS_RU_ADMIN_NUMBER'=>$arData['PARAMS']['SMS_RU_ADMIN_NUMBER'],
			'SMS_RU_TEMPLATE_FAIL'=>$arData['PARAMS']['SMS_RU_TEMPLATE_FAIL'],
			'SMS_RU_TEMPLATE_SUCCESS'=>$arData['PARAMS']['SMS_RU_TEMPLATE_SUCCESS'],
		),
		'EMAIL'=>array(
			'EVENT_NAME'=>$arData['PARAMS']['EVENT_NAME'],
			'EMAIL_ADMINISTRATOR'=>$arData['PARAMS']['EMAIL_ADMINISTRATOR'],
			'EMAIL_TEMPLATES_ADMINISTRATOR'=>$arData['PARAMS']['EVENT_TEMPLATES_ADMINISTRATOR'],
			'EMAIL_TEMPLATES_CUSTOMER'=>$arData['PARAMS']['EVENT_TEMPLATES_CUSTOMER'],
		)
	));
	
	$OrderID=$SP->Buy($product_id, 
		array(
			'NAME'=>$user_name,
			'PHONE'=>$user_phone,
			'EMAIL'=>$user_email,
			'MESSAGE'=>$user_message,
		)
	);
} 


if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	ajax_handler();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	post_handler();
} else {
	redirect('/');
}