<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

function redirect($url, $statusCode = 303) {
	header('Location: ' . $url, true, $statusCode);
	die();
}

function get_parameters($string) {
	$parameters = unserialize(base64_decode($string));
	return $parameters;
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
		"ORDER_DESCRIPTION" => GetMessage("LIGHTWEB_COMPONENTS_VREMENNO_OTSUTSTVUET"),
		"PROGNOSIS" => $options['product']['score'],
	);
	if (!empty($options['templates_user'])) {
		foreach($options['templates_user'] as $template)
			if (IntVal($template) > 0) {
				$answer['user'][] = CEvent::Send($parameters["EVENT_NAME"], SITE_ID, $email_fields, "N", IntVal($template));
			}
	}
	return $answer;
}

function get_order_properties($order_id) {
	$answer = false;
	if (!CModule::IncludeModule("iblock")) return false;
	$order_object = CIBlockElement::GetByID((int)$order_id);
	$order_result = array();
	if ($order_object_result = $order_object->GetNext()) {
		$order_iblock_id = $order_object_result['IBLOCK_ID'];
		$order_object_properties = CIBlockElement::GetProperty($order_iblock_id, $order_id, array(), array());
		while ($order_object_property = $order_object_properties->GetNext()) {
			$order_result[$order_object_property['CODE']] = $order_object_property;
		}
	}
	if ((int)$order_result['PASSWORD']['VALUE'] > 0) {
		$answer = array(
			'name' => $order_result['NAME']['VALUE'],
			'password' => $order_result['PASSWORD']['VALUE'],
			'email' => $order_result['MAIL']['VALUE'],
			'product_id' => $order_result['BINDING']['VALUE'],
			'phone' => $order_result['PHONE']['VALUE'],
			'paid' => $order_result['PAID']['VALUE_XML_ID'],
			'cost' => $order_result['COST']['VALUE'],
		);
	}
	return $answer;
}

function get_product_properties($product_id) {
	$answer = false;
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
	if ($product_result['KIND_OF_SPORT']['VALUE_ENUM'] != null && $product_result['WHO']['VALUE'] != null && $product_result['WITH_WHOM']['VALUE'] != null && $product_result['COST']['VALUE'] != null) {
		$answer = array(
			'kind_of_sport' => $product_result['KIND_OF_SPORT']['VALUE_ENUM'],
			'participant_1' => $product_result['WHO']['VALUE'],
			'participant_2' => $product_result['WITH_WHOM']['VALUE'],
			'competition' => $product_result['COMPETITION']['VALUE'],
			'cost' => $product_result['COST']['VALUE'],
			'date' => $product_result['DATE_COMPETITION']['VALUE'],
			'score' => $product_result['SCORE']['VALUE'],
		);
		$answer['name'] = $answer['date'].' | '.$answer['kind_of_sport'].'. '.$answer['participant_1'].' - '.$answer['participant_2'].'. '.$answer['competition'];
	}
	return $answer;
}

function ajax_handler() {
	global $USER;
	global $MESS;
	//$file_name_array = explode("/",__FILE__);
	$file_path_parts = pathinfo(__FILE__);
	$file_path = $_SERVER["DOCUMENT_ROOT"].stristr(__DIR__, '/bitrix').'/lang/'.LANGUAGE_ID.'/'.$file_path_parts['basename'];
	if (file_exists($file_path)) {
		include($file_path);
	}
	$order_id = htmlspecialchars($_POST["order_id"]);
	$password = htmlspecialchars($_POST["password"]);
	$params = get_parameters(htmlspecialchars($_POST["params"]));
	$response = array();
	$response['ERROR'] = array();
	$order_properties = get_order_properties($order_id);
	$product_properties = get_product_properties($order_properties['product_id']);
	$order_password = $order_properties['password'];
	if (!$order_properties) {
		$response['ERROR'][] = array(
			'message' => $MESS["RK_BOC_WRONG_ORDER_ID_MESSAGE"],
			'error_code' => 1,
		);
		if (!$USER->IsAdmin()) {
			echo json_encode($response);
			exit();
		}
	}
	if ((int)$order_password != (int)$password) {
		$error_message = str_replace('#ORDER_ID#',$order_id,$MESS["RK_BOC_WRONG_PASSWORD_MESSAGE"]);
		$response['ERROR'][] = array(
			'message' => $error_message,
			'error_code' => 2,
		);
		if (!$USER->IsAdmin()) {
			echo json_encode($response);
			exit();
		}
	}
	$options = array();
	$options['product'] = $product_properties;
	$options['order_id'] = $order_id;
	$options['parameters'] = array();
	$options['parameters']['NAME'] = $order_properties['name'];
	$options['parameters']['EMAIL'] = $order_properties['email'];
	$options['parameters']['PHONE'] = $order_properties['phone'];
	$options['parameters']['PRODUCT_ID'] = $order_properties['product_id'];
	$options['parameters']['MESSAGE'] = '';
	$options['parameters']['PARAMS'] = $params;
	$email_response = send_mail($options);
	if (!(int)$email_response['user'][0] > 0) {
		$response['ERROR'][] = array(
			'message' => $MESS["RK_BOC_EMAIL_NOT_SENT_MESSAGE"],
			'error_code' => 3,
		);
		if (!$USER->IsAdmin()) {
			echo json_encode($response);
			exit();
		}
	}
	$response['EMAIL'] = $order_properties['email'];
	if ($USER->IsAdmin()) {

	}
	echo json_encode($response);
}

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	ajax_handler();
} else {
	redirect('/');
}

?>
