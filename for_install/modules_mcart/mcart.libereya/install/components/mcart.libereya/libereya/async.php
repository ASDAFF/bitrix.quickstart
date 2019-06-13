<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/mcart.libereya/libereya/lang/".LANGUAGE_ID."/async.php");
define("STOP_STATISTICS", true);
$result = array();
$result['status']  = "false";
/*right charset: covert to utf-8 if needs*/
function rch($value)
{
	if(tolower(LANG_CHARSET) != 'UTF-8')
	{
		$value = iconv(LANG_CHARSET, 'UTF-8', $value);
	}
	return $value;
}

if(!empty($_GET['async']))
{
	$element_id = intval($_GET['element_id']);
	if(empty($element_id)){
		$result['message'] = rch(GetMessage('LIBEREYA_WRONG_ID'));
		echo json_encode($result); die();
	}
	
	if (!$USER->IsAuthorized())
	{
		$result['message'] = rch(GetMessage('LIBEREYA_ERROR_NEED_AUTH')); 
		echo json_encode($result); die();
	}
	CModule::IncludeModule('iblock');
	
	$res = CIBlockElement::GetByID($element_id);
	if($ar_res = $res->GetNextElement())
	{
		$el	= $ar_res->GetFields();
		$el['props']	= $ar_res->GetProperties();
	}
	//dump($el['props']);
	switch($_GET['action'])
	{
		case 'booking':
			if($el['props']['READER']['VALUE'] || $el['props']['BOOKING']['VALUE'])
			{ 
				$result['message'] = rch(GetMessage('LIBEREYA_RESERVED_ALREADY')); 
				echo json_encode($result); die();
			}
			$properties = array(
								'BOOKING'		=> strval($USER->GetID()),
								'BOOKING_TIME'	=> date('d.m.Y H:i:s'),
								);
			//print_r($properties['BOOKING_TIME']);
			$result['message'] = rch(GetMessage('LIBEREYA_RESERVED_SUCCESSFULLY'));
			$result['new_html'] = '<span class=\'btn btn-greys\'>'.rch(GetMessage('LIBEREYA_RESERVED_BY_YOURSELF')).'</span>';
			$result['status']  = "true";
		break;
		 case 'return_message':
			if(in_array($USER->GetID(), $el['props']['RETURN_MESSAGE']['VALUE']))
			{ 
				$result['message'] = rch(GetMessage('LIBEREYA_YOU_SUBSCRIBED_ALREADY')); 
				echo json_encode($result); die();
			}
			$users = $el['props']['RETURN_MESSAGE']['VALUE'];
			$users[] = $USER->GetID();
			
			
			
			$properties = array(
								'RETURN_MESSAGE' => array_unique($users),
								);
			$result['message'] = rch(GetMessage('LIBEREYA_YOU_SUBSCRIBED_SUCCESSFULLY'));
			$result['status']  = "true";
		break;
	}
	if(!empty($properties))
	{
		CIBlockElement::SetPropertyValuesEx($el['ID'], $el['IBLOCK_ID'], $properties);

		if(defined('BX_COMP_MANAGED_CACHE'))
		$GLOBALS['CACHE_MANAGER']->ClearByTag('iblock_id_'.$el['IBLOCK_ID']);
	}
	 
	 echo  json_encode($result);
}



?>
