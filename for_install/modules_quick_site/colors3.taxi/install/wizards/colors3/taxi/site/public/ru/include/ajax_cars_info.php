<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!CModule::IncludeModule("iblock"))
	return;

$db_connect = CIBlockElement::GetList(false, array('ACTIVE'=>'Y', 'IBLOCK_CODE'=>'connect'), false, false, array(
								'ID', 
								'IBLOCK_ID',
								'NAME',		
								'PROPERTY_HOST', 
								'PROPERTY_KEY', 
								'PROPERTY_PORT', 
								'PROPERTY_SECRET'));

if ($element = $db_connect->Fetch()){
	$send = new Model_Apitm();
	$send->setIp($element['PROPERTY_HOST_VALUE']);
	$send->setPort($element['PROPERTY_PORT_VALUE']);
	$send->setKey($element['PROPERTY_KEY_VALUE']);
	
	$method = 'get_crews_coords';
	$json = array();

	if (!empty($_GET['crew_id']) && (int)$_GET['crew_id'] > 0){
		$result = $send->get($method, array('crew_id' => $_GET['crew_id']));
		$_SESSION['crew_id'] = $_GET['crew_id'];

		$method = 'get_crew_info';
		$result_crew_info = $send->get($method, array('crew_id'=>$_GET['crew_id']));
		$method = 'get_car_info';
		$result_car_info = $send->get($method, array('car_id'=>$result_crew_info->data->car_id));
		
		if (!$result_car_info->data->mark || !$result_car_info->data->color) continue;


		$json[] = array('crew_code'=>$result->data->crews_coords[0]->crew_code,'name_car'=>$result_car_info->data->mark.' '.$result_car_info->data->model, 'gos_number'=>$result_car_info->data->gos_number, 'color_car'=>$result_car_info->data->color, 'lat'=>$result->data->crews_coords[0]->lat, 'lon'=>$result->data->crews_coords[0]->lon);
		echo json_encode($json);

	}else{
		$result = $send->get($method);						
		foreach ($result->data->crews_coords as $value){
			
			$method = 'get_crew_info';
			$result_crew_info = $send->get($method, array('crew_id'=>$value->crew_id));
			
			$method = 'get_car_info';
			$result_car_info = $send->get($method, array('car_id'=>$result_crew_info->data->car_id));
			
			
			if (!$result_car_info->data->mark || !$result_car_info->data->color) continue;
				
			//$json[] = array('status'=>$value->state_kind, 'name_car'=>strtr($name_car[0], array('['=>'', ']'=>'')), 'gos_number'=>$result_car_info->data->gos_number, 'color_car'=>strtr($color_car[0], array('('=>'', ')'=>'')), 'crew_id'=>$value->crew_id, 'crew_code'=>$value->crew_code, 'lat'=>$value->lat, 'lon'=>$value->lon);
			$json[] = array('status'=>$value->state_kind, 'name_car'=>$result_car_info->data->mark.' '.$result_car_info->data->model, 'gos_number'=>$result_car_info->data->gos_number, 'color_car'=>$result_car_info->data->color, 'crew_id'=>$value->crew_id, 'crew_code'=>$value->crew_code, 'lat'=>$value->lat, 'lon'=>$value->lon);		

		}
		echo json_encode($json);
	}

		
}
?>