<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

function redirect($url, $statusCode = 303) {
	header('Location: ' . $url, true, $statusCode);
	die();
}

function get_parameters($input) {
	$parameters = unserialize(base64_decode($input));
	return $parameters;
}

function cook_template($options) {
	$raw = $options['template'];
	$search = array(
		'#USER_NAME#',
		'#USER_PHONE#',
		'#USER_EMAIL#',
		'#USER_MESSAGE#',
		'#FORM#',
		'#FORM_ID#',
		'#EMAIL_TO#',
		'#SITE_NAME#',
	);
	$replaces = array(
		$options['user_name'],
		$options['user_phone'],
		$options['user_email'],
		$options['user_message'],
		$options['form_name'],
		$options['form_id'],
		$options['email_to'],
		$options['site_name'],
	);
	$cooked = str_replace($search,$replaces,$raw);
	return $cooked;
}

function send_sms($options) {
	$testing = ($options['state']=="TESTING")?1:0;
	$cooked_templates = cook_template($options);
	$sms_text = $cooked_templates;
	if (!CModule::IncludeModule("lw_components")) return false;
	$sms_instance = CLWComponents::ConnectExtension('smsru');
	$sms_instance->login(array(
		'api_id' => $options['api'],
	));
	$sms_response = $sms_instance->send(array(
		'to' => $options['phone'],
		'text' => $sms_text,
		"from" => $options['from'],
		'test' => $testing,
	));

	return $sms_response['response'];
}

function sms_handler($input) {
	$request_fields = $input['fields'];
	$parameters = $input['fields']['PARAMETERS'];

	$output = array(
		'sms_sent' => 'Y',
		'error_code' => array(),
	);
	if ($parameters["SMS_RU_STATE"] != "DISABLED" &&
		!empty($parameters["SMS_RU_ADMIN_NUMBER"]) &&
		!empty($parameters["SMS_RU_FROM"]) &&
		!empty($parameters["SMS_RU_TEMPLATE"]) &&
		!empty($parameters["SMS_RU_API_KEY"])) {
		// everything is fine
		$options = array();
		$options['state'] = $parameters["SMS_RU_STATE"];
		$options['from'] = $parameters["SMS_RU_FROM"];
		$options['api'] = $parameters["SMS_RU_API_KEY"];
		$options['template'] = $parameters["SMS_RU_TEMPLATE"];
		$options['phone'] = $parameters['SMS_RU_ADMIN_NUMBER'];
		$options['user_name'] = $request_fields['NAME'];
		$options['user_email'] = $request_fields['EMAIL'];
		$options['user_phone'] = $request_fields['PHONE'];
		$options['user_message'] = $request_fields['MESSAGE'];
		$options['form_name'] = $parameters["FORM"];
		$options['form_id'] = $parameters["FORM_ID"];
		$options['email_to'] = $parameters["EMAIL_TO"];
		$options['site_name'] = $parameters['SITE_NAME'];
	} else {
		$output['error_message'] = 'Some SMS.RU options are not set';
		$output['error_code'] = '500';
		$output['sms_sent'] = 'N';
		return $output;
	}
	if (!empty($parameters["SMS_RU_ADMIN_NUMBER"]) && $parameters["SMS_RU_STATE"] != "DISABLED") {
		$sms_response = send_sms($options);
		if ($sms_response!='100') {
			$output['error_code'] = $sms_response;
			$output['sms_sent'] = 'N';
		}
	}
	return $output;
}

function send_mail($input) {
	$request_fields = $input['fields'];
	$parameters = $input['fields']['PARAMETERS'];
	$answer = array();
	$email_fields = Array(
		"EMAIL_TO" => $parameters["EMAIL_TO"],
		"FORM" => $parameters["FORM_NAME"],
		"FORM_ID" => $parameters["FORM_ID"],
		"SITE_NAME" => $parameters["SITE_NAME"],
		"USER_NAME" => $request_fields["NAME"],
		"USER_PHONE" => $request_fields["PHONE"],
		"USER_EMAIL" => $request_fields["EMAIL"],
		"USER_MESSAGE" => $request_fields["MESSAGE"],
	);
	if (!empty($parameters['EVENT_MESSAGE_ID'])) {
		foreach($parameters['EVENT_MESSAGE_ID'] as $template)
			if (IntVal($template) > 0) {
				$answer[] = CEvent::Send($parameters["EVENT_NAME"], SITE_ID, $email_fields, "N", IntVal($template));
			}
	}
	return $answer;
}

function ajax_handler(){
	$request_fields = array();
	$request_fields['fields'] = $_POST;
	$request_fields['fields']['PARAMETERS'] = get_parameters($_POST['PARAMETERS']);
	$fields = $request_fields['fields'];
	$parameters = $request_fields['fields']['PARAMETERS'];
	if (empty($parameters["USED_FIELDS"])) {
		$parameters["USED_FIELDS"] = array("NAME","PHONE","EMAIL");
	} else {
		$used_fields = $parameters["USED_FIELDS"];
		$allowed_fields = array('NAME','PHONE','EMAIL','MESSAGE');
		foreach ($parameters["USED_FIELDS"] as $k => $v){
			if (!in_array($v, $allowed_fields)) unset($used_fields[$k]);
		}
		$parameters["USED_FIELDS"] = $used_fields;
	}
	$required_fields = $parameters["REQUIRED_FIELDS"];
	foreach ($parameters["REQUIRED_FIELDS"] as $k => $v) {
		if (!in_array($v, $parameters["USED_FIELDS"])) unset($required_fields[$k]);
	}
	$parameters["REQUIRED_FIELDS"] = $required_fields;
	$json_output["ERROR"] = array();
	if (!empty($parameters["REQUIRED_FIELDS"])) {
		foreach ($parameters["REQUIRED_FIELDS"] as $require_field) {
			if (!in_array($require_field, array_keys($fields)) || strlen($fields[$require_field]) <= 1) {
				$json_output["ERROR"]["REQUIRED_FIELDS"][] = $require_field;
			}
		}
	}
	$sms_send_check = sms_handler($request_fields);
	if ($sms_send_check == '202') $json_output["ERROR"]["SMS_RU_ERROR"] = '202';
	$email_send_check = send_mail($request_fields);
	$json_output['email_status'] = $email_send_check;
	$json_output['sms_status'] = $sms_send_check;
	if (empty($json_output["ERROR"])) {
		$json_output['SENT'] = 'Y';
	} else {
		$json_output['SENT'] = 'N';
	}
	echo json_encode($json_output);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	ajax_handler();
} else {
	redirect('/');
}
	
