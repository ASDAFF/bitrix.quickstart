<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!CModule::IncludeModule("iblock"))
	return;

$send = new Model_Apitm();	
$db_connect = CIBlockElement::GetList(false, array('NAME'=>$_SESSION['city']['name'], 
		'IBLOCK_CODE'=>'connect', 'ACTIVE'=>'Y'), false, false, array(
		'ID', 
		'IBLOCK_ID', 
		'PROPERTY_HOST', 
		'PROPERTY_KEY', 
		'PROPERTY_PORT', 
		'PROPERTY_SECRET'));
if ($element = $db_connect->GetNext()){
	$send->setIp($element['PROPERTY_HOST_VALUE']);
	$send->setPort($element['PROPERTY_PORT_VALUE']);
	$send->setKey($element['PROPERTY_KEY_VALUE']);
	
	if (!empty($_GET['car_id'])){
		$method = 'get_car_info';
		$result_car_info = $send->get($method, array('car_id'=>(int)$_GET['car_id']));
		preg_match('#\[.+\]#', $result_crew_info->data->name, $name_car);
		preg_match('#\(.+\)#', $result_crew_info->data->name, $color_car);
		echo json_encode(array('name_car'=>strtr($name_car[0], array('['=>'', ']'=>'')), 'gos_number'=>$result_car_info->data->gos_number, 'color_car'=>strtr($color_car[0], array('('=>'', ')'=>''))));
	}else
	{		
		$method = 'get_order_state';	
		$result = $send->get($method, array('order_id'=>$_SESSION['order']->data->order_id));
		if ($result->descr == 'OK'){
			$_SESSION['crew_id'] = $result->data->crew_id;
		}
		echo json_encode(array('car_id'=>$result->data->car_id, 'crew_id'=>$result->data->crew_id, 'state_kind'=>$result->data->state_kind, 'source'=>$result->data->source, 'destination'=>$result->data->destination));		
	}		
}
		
?>