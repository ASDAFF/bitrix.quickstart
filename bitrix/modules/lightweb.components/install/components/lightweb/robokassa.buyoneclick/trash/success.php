<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


echo '<pre>';
print_r($_REQUEST);
echo '</pre>';

exit;
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

function edit_order_object($order_id,$parameters,$one_time_password) {
	if (!CModule::IncludeModule("iblock")) return false;
	$order_object = new CIBlockElement;
	$properties = array();
	$properties[$parameters['PARAMS']['PRODUCT_ID']] = $parameters['PRODUCT_ID'];
	$properties[$parameters['PARAMS']['CUSTOMER_PHONE_PROP_NAME']] = '7'.$parameters['PHONE'];
	$properties[$parameters['PARAMS']['CUSTOMER_EMAIL_PROP_NAME']] = $parameters['EMAIL'];
	$properties[$parameters['PARAMS']['ORDER_SUM']] = $parameters['PRODUCT_COST'];
	$paid_property_enums = CIBlockPropertyEnum::GetList(array(), array("IBLOCK_ID"=>$parameters['PARAMS']['IBLOCK_ID']));
	while($paid_enum_fields = $paid_property_enums->GetNext())
	{
		if ($paid_enum_fields["XML_ID"] == 'Y' and
			$paid_enum_fields['PROPERTY_CODE'] == $parameters['PARAMS']['PAID_PROP_NAME'])
			$properties[$parameters['PARAMS']['PAID_PROP_NAME']] = Array("Y" => $paid_enum_fields["ID"]);
	}
	$properties[$parameters['PARAMS']['ORDER_PASSWORD_PROP_NAME']] = $one_time_password;
	$properties[$parameters['PARAMS']['CUSTOMER_PROP_NAME']] = $parameters['NAME'];
	$order_main_options = Array(
		"PROPERTY_VALUES"=> $properties,
	);
	$order_result = $order_object->Update($order_id, $order_main_options);
	return $order_result;
}

function generate_password($length = 6) {
	$chars = '0123456789';
	$count = mb_strlen($chars);
	for ($i = 0, $result = ''; $i < $length; $i++) {
		$index = rand(0, $count - 1);
		$result .= mb_substr($chars, $index, 1);
	}
	return strtoupper($result);
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
	global $USER;
	$testing = ($options['state']=="TESTING")?1:0;
	$cooked_templates = cook_template($options);
	if ($USER->IsAdmin()) {
		?><pre><? print_r(cook_template($options)); ?></pre><?
	}
	if ($options['situation'] == 'success') {
		$sms_text = $cooked_templates['template_success'];
	} else {
		$sms_text = $cooked_templates['template_fail'];
	}
	$phone_list_string = join(",",$options['phones_list']);
	if (!CModule::IncludeModule("lw_components")) return false;
	$sms_instance = CLWComponents::ConnectExtension('smsru');
	$sms_instance->login(array(
		'login' => $options['login'],
		'password' => $options['password'],
		'api_id' => $options['api'],
	));
	$sms_response = $sms_instance->send(array(
		'to' => $phone_list_string,
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
		$options['phones_list'] = array('7'.$request_fields["PHONE"],$parameters['SMS_RU_ADMIN_NUMBER']);
	} else {
		$output['error_message'] = 'Указаны не все данные';
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

function send_mail($options) {
	$request_fields = $options['parameters'];
	$parameters = $options['parameters']['PARAMS'];
	$options['templates_admin'] = $parameters["EVENT_TEMPLATES_ADMINISTRATOR"];
	$options['templates_user'] = $parameters["EVENT_TEMPLATES_CUSTOMER"];
	$answer = array();
	$email_fields = Array(
		"EMAIL_TO" => $parameters["EMAIL_TO"],
		"TEXT" => $request_fields["MESSAGE"],
		"FORM" => $parameters["FORM_NAME"],
		"FORM_ID" => $parameters["FORM_ID"],
		"CUSTOMER_NAME" => $request_fields["NAME"],
		"CUSTOMER_PHONE" => $request_fields["PHONE"],
		"CUSTOMER_EMAIL" => $request_fields["EMAIL"],
		"CUSTOMER_MESSAGE" => $request_fields["MESSAGE"],
		"EMAIL_ADMINISTRATOR" => $parameters["EMAIL_ADMINISTRATOR"],
		"EMAIL_CUSTOMER" => $request_fields["EMAIL"],
		"ORDER_ID" => $options['order_id'],
		"PRODUCT_ID" => $request_fields["PRODUCT_ID"],
		"ORDER_SUM" => $options['product']['cost'],
		"ORDER_NAME" => $options['product']['name'],
		"ORDER_DESCRIPTION" => 'Временно отсутствует',
		"PROGNOSIS" => $options['product']['score'],
	);
	if (!empty($options['templates_admin'])) {
		foreach($options['templates_admin'] as $template)
			if (IntVal($template) > 0) {
				$answer['admin'][] = CEvent::Send($parameters["EVENT_NAME"], SITE_ID, $email_fields, "N", IntVal($template));
			}
	}
	if (!empty($options['templates_user'])) {
		foreach($options['templates_user'] as $template)
			if (IntVal($template) > 0) {
				$answer['user'][] = CEvent::Send($parameters["EVENT_NAME"], SITE_ID, $email_fields, "N", IntVal($template));
			}
	}
	return $answer;
}

function check_hashes($store_password) {
	$answer = false;
	$rk_password = $store_password;
	$cost = $_REQUEST["OutSum"];
	$order_id = $_REQUEST["InvId"];
	$rk_constanta = $_REQUEST["Shp_item"];
	$md5_hash = $_REQUEST["SignatureValue"];
	$fields = $_REQUEST["Shp_fields"];
	$md5_hash = strtoupper($md5_hash);
	$control_hash = strtoupper(md5("$cost:$order_id:$rk_password:Shp_fields=$fields:Shp_item=$rk_constanta"));
	if ($control_hash == $md5_hash) $answer = true;
	return $answer;
}

function redirect($url, $statusCode = 303) {
	header('Location: ' . $url, true, $statusCode);
	die();
}

$parameters = get_parameters($_POST["Shp_fields"]);
$one_time_password = generate_password();
$options = array(
	'situation' => 'success',
	'parameters' => $parameters,
	'product_id' => $parameters['PRODUCT_ID'],
	'product' => get_product_object($parameters['PRODUCT_ID']),
	'one_time_password' => $one_time_password,
	'order_id' => $_REQUEST["InvId"],
	'order_payed' => edit_order_object($_REQUEST["InvId"],$parameters,$one_time_password),
	'hashes_agreed' => check_hashes($parameters['PARAMS']['PAYMENT_PASSWORD']),
);
$response = array();
$response['SMS'] = sms_handler($options);
$response['MAIL'] = send_mail($options);
$response['OPTIONS'] = $options;
$options = base64_encode(serialize($options));

?>
<form method="post" action="<?=$parameters['PARAMS']['SUCCESS_REDIRECT_PAGE'];?>" style="display:none;" id="redirect-form">
	<input type="hidden" name="options" value="<?=$options;?>" />
</form>
<script>window.onload=function(){document.getElementById('redirect-form').submit();}</script>