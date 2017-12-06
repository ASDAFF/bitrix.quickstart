<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

function get_parameters($string) {
	$parameters = unserialize(base64_decode($string));
	return $parameters;
}

function get_product_object($product_id) {
	if (!CModule::IncludeModule("iblock")) return false;
	$product_object = CIBlockElement::GetByID((int)$product_id);
	$product_result = array();
	if ($product_object_result = $product_object->GetNext()) {
		$product_iblock_id = $product_object_result['IBLOCK_ID'];
		$product_object_properties = CIBlockElement::GetProperty($product_iblock_id, $product_id, array(), array());
		while ($product_object_property = $product_object_properties->GetNext()) {
			$product_result[$product_object_property['CODE']] = $product_object_property;
		}
	}
	$product = array(
		'kind_of_sport' => $product_result['KIND_OF_SPORT']['VALUE_ENUM'],
		'participant_1' => $product_result['WHO']['VALUE'],
		'participant_2' => $product_result['WITH_WHOM']['VALUE'],
		'competition' => $product_result['COMPETITION']['VALUE'],
		'cost' => $product_result['COST']['VALUE'],
		'date' => $product_result['DATE_COMPETITION']['VALUE'],
		'score' => $product_result['SCORE']['VALUE'],
	);
	$product['name'] = $product['date'].' | '.$product['kind_of_sport'].'. '.$product['participant_1'].' - '.$product['participant_2'].'. '.$product['competition'];
	return $product;
}

function cook_template($options) {
	$success_raw = $options['template_success'];
	$fail_raw = $options['template_fail'];
	$search = array('#ORDER_ID#','#ORDER_NAME#','#PASSWORD#');
	$replaces = array($options['order_id'],$options['product']['name'],$options['one_time_password']);
	$success_cooked = str_replace($search,$replaces,$success_raw);
	$fail_cooked = str_replace($search,$replaces,$fail_raw);
	return array(
		'template_success' => $success_cooked,
		'template_fail' => $fail_cooked,
	);
}

function send_sms($options) {
	$testing = ($options['state']=="TESTING")?1:0;
	$cooked_templates = cook_template($options);
	if ($options['situation'] == 'success') {
		$sms_text = $cooked_templates['template_success'];
	} else {
		$sms_text = $cooked_templates['template_fail'];
	}
	if (!CModule::IncludeModule("lw_components")) return false;
	$sms_instance = CLWComponents::ConnectExtension('smsru');
	$sms_instance->login(array(
		'login' => $options['login'],
		'password' => $options['password'],
		'api_id' => $options['api'],
	));
	$sms_response = $sms_instance->send(array(
		'to' => $options['phones_list'],
		'text' => $sms_text,
		"from" => $options['from'],
		'test' => $testing,
	));

	return $sms_response['response'];
}

function sms_handler($options) {
	$custom_fields = $options;
	$request_fields = $options['parameters'];
	$parameters = $options['parameters']['PARAMS'];
	$output = array(
		'sms_sent' => 'Y',
		'error_code' => array(),
	);
	if (in_array('PHONE',$parameters["REQUIRED_FIELDS"]) &&
		($parameters["SMS_RU_STATE"] == "ACTIVE" || $parameters["SMS_RU_STATE"] = "TESTING") &&
		!empty($request_fields["PHONE"]) &&
		!empty($parameters["SMS_RU_FROM"]) &&
		!empty($parameters["SMS_RU_TEMPLATE_FAIL"]) &&
		!empty($parameters["SMS_RU_TEMPLATE_SUCCESS"]) &&
		!empty($parameters["SMS_RU_LOGIN"]) &&
		!empty($parameters["SMS_RU_PASSWORD"]) &&
		!empty($parameters["SMS_RU_API_KEY"])) {
		// everything is fine
		$options = array();
		$options['situation'] = $custom_fields["situation"];
		$options['product_id'] = $custom_fields["product_id"];
		$options['product'] = $custom_fields["product"];
		$options['one_time_password'] = $custom_fields["one_time_password"];
		$options['order_id'] = $custom_fields["order_id"];
		$options['state'] = $parameters["SMS_RU_STATE"];
		$options['from'] = $parameters["SMS_RU_FROM"];
		$options['login'] = $parameters["SMS_RU_LOGIN"];
		$options['password'] = $parameters["SMS_RU_PASSWORD"];
		$options['api'] = $parameters["SMS_RU_API_KEY"];
		$options['template_fail'] = $parameters["SMS_RU_TEMPLATE_FAIL"];
		$options['template_success'] = $parameters["SMS_RU_TEMPLATE_SUCCESS"];
		$options['phones_list'] = '7'.$request_fields["PHONE"];
	} else {
		$output['error_message'] = GetMessage("LIGHTWEB_COMPONENTS_ODIN_IZ_PARAMETROV_N");
		$output['error_code'] = '500';
		$output['sms_sent'] = 'N';
		return $output;
	}
	if (in_array('PHONE',$parameters["REQUIRED_FIELDS"]) && $parameters["SMS_RU_STATE"] != "DISABLED") {
		$sms_response = send_sms($options);
		if ($sms_response!='100') {
			$output['error_code'] = $sms_response;
			$output['sms_sent'] = 'N';
		}
	}
	return $output;
}

function redirect($url, $statusCode = 303) {
	header('Location: ' . $url, true, $statusCode);
	die();
}

$parameters = get_parameters($_POST["Shp_fields"]);
$options = array(
	'situation' => 'fail',
	'parameters' => $parameters,
	'product_id' => $parameters['PRODUCT_ID'],
	'product' => get_product_object($parameters['PRODUCT_ID']),
	'order_id' => $_REQUEST["InvId"],
);
$sms_response = sms_handler($options);
?>

<form method="post" action="<?=$parameters['PARAMS']['FAIL_REDIRECT_PAGE'];?>" style="display:none;" id="redirect-form"></form>
<script>window.onload=function(){document.getElementById('redirect-form').submit();}</script>
