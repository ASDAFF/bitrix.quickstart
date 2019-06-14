<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	
	if (!CModule::IncludeModule("lw_components")) return false;
	if (!CLWComponents::ConnectExtension('sprypay', false)) return false;
	
	//���������� �������� �����
	$file_path_parts = pathinfo(__FILE__);
	$file_path = $_SERVER["DOCUMENT_ROOT"].stristr(__DIR__, '/bitrix').'/lang/'.LANGUAGE_ID.'/'.$file_path_parts['basename'];
	if (file_exists($file_path)) {include($file_path);}
	
	//�������� ���������� ������
	$response = array();
	$response['ERROR'] = array();
	
	//��������� �������� ������
	$order_id = htmlspecialchars($_POST["order_id"]);
	$password = htmlspecialchars($_POST["password"]);
	$arData['PARAMS'] = unserialize(base64_decode($_POST['params']));
	
	//��������� ������� ����� ��� ���������
	$SP=new sprypay;
	$SP->SetOptions(array(
		'ORDERS'=>array(
			'PROPERTY_PRODUCT_ID'=>$arData['PARAMS']['PRODUCT_ID'],
			'PROPERTY_PASSWORD'=>$arData['PARAMS']['ORDER_PASSWORD_PROP_NAME'],
		),
	));
	
	//������������ ���������� � ������ �� ��� ������ � ������
	$arOrder=$SP->GetOrderByPassword($order_id, $password);
	if ($arOrder){
		
		//������� Email �����������
		$EmailFields = array(
			"ORDER_ID" => 			$arOrder['ORDER']['FIELD']['ID'],
			"ORDER_SUM" => 			$arOrder['ORDER']['PROPERTY'][$arData['PARAMS']['ORDER_SUM']]['VALUE'],
			"ORDER_DATE" => 		date('d.m.Y', $arOrder['ORDER']['FIELD']['DATE_CREATE_UNIX']),
			"ORDER_PASSWORD" => 	$arOrder['ORDER']['PROPERTY'][$arData['PARAMS']['ORDER_PASSWORD_PROP_NAME']]['VALUE'],
			"PRODUCT_NAME" => 		$arOrder['PRODUCT']['FIELD']['NAME'],
			"PRODUCT_ID" => 		$arOrder['PRODUCT']['FIELD']['ID'],
			"PRODUCT_DESCRIPTION" =>$arOrder['PRODUCT']['PROPERTY'][$arData['PARAMS']['PRODUCTS_DESCRIPTION']]['VALUE'],
			
			"CUSTOMER_NAME" => 		$arOrder['ORDER']['PROPERTY'][$arData['PARAMS']['CUSTOMER_PROP_NAME']]['VALUE'],
			"CUSTOMER_PHONE" => 	$arOrder['ORDER']['PROPERTY'][$arData['PARAMS']['CUSTOMER_PHONE_PROP_NAME']]['VALUE'],
			"CUSTOMER_EMAIL" => 	$arOrder['ORDER']['PROPERTY'][$arData['PARAMS']['CUSTOMER_EMAIL_PROP_NAME']]['VALUE'],
			"CUSTOMER_MESSAGE" => 	$arOrder['ORDER']['PROPERTY'][$arData['PARAMS']['CUSTOMER_MESSAGE_PROP_NAME']]['VALUE'],
			
			"EMAIL_ADMINISTRATOR" =>$arData['PARAMS']['EMAIL_ADMINISTRATOR'],
		);
		
		
		//���������� ���� �������� �������������� �������
		if (!empty($arData['PARAMS']['EVENT_TEMPLATES_CUSTOMER'])) { //��� ���������
			foreach($arData['PARAMS']['EVENT_TEMPLATES_CUSTOMER'] as $template){
				$template=IntVal($template);
				if ($template > 0) {
					CEvent::Send($arData['PARAMS']['EVENT_NAME'], SITE_ID, $EmailFields, "N", $template);
				}
			}
		}
		
		if (empty($EmailFields["CUSTOMER_EMAIL"])){
			//���������� ��������� �� ���������� ��. �����
			$response['ERROR'][] = array(
				'message' => str_replace('#ORDER_ID#', $order_id, $MESS["SP_BOC_EMAIL_NOT_SENT_MESSAGE"]),
				'error_code' => 1,
			);
		} else {
			//���������� �����
			$response['EMAIL']=$EmailFields["CUSTOMER_EMAIL"];
		}
	} else {
		//���� ����� � ����� ������� � ������� �� ������
		//���������� �������
		$response['ERROR'][] = array(
			'message' => str_replace('#ORDER_ID#', $order_id, $MESS["SP_BOC_WRONG_PASSWORD_MESSAGE"]),
			'error_code' => 2,
		);
	}
	
	echo json_encode($response);

} else {
	header('Location: ' . '/', true, 303);
	die();
}

?>