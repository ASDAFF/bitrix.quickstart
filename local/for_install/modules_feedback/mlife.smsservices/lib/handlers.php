<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.smsservices
 * @copyright  2015 Zahalski Andrew
 */

namespace Mlife\Smsservices;


class Handlers {
	
	//новый заказ
	public static function OnSaleComponentOrderOneStepCompleteHandler($orderId,$arOrderFields)
	{
		$active = \COption::GetOptionString('mlife.smsservices', 'activesale', "N");
		
		if($active!="N"){
		
			//заказ
			//$arOrderFields = CSaleOrder::GetByID($orderId);
			
			$arParameters["LID"] = $arOrderFields["LID"]; //ид сайта
			$arParameters["PERSON_TYPE_ID"] = $arOrderFields["PERSON_TYPE_ID"]; //ид плательщика
			
			$phoneFieldCode = \COption::GetOptionString('mlife.smsservices', "property_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			$adminPhone = \COption::GetOptionString('mlife.smsservices', "admin_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			if(strlen($phoneFieldCode)>0) {
				
				$obStatusName = \CSaleStatus::GetList(array(),array('ID'=>$arOrderFields['STATUS_ID'], 'LID'=>LANGUAGE_ID));
				if($ar = $obStatusName->Fetch()) {
					$statusName = $arStat['NAME'];
				}else{
					$statusName = "";
				}
				
				$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"), array("TYPE" => array('TEXT','TEXTAREA')));
				$arMakros = array();
				
				while($arProp = $dbProperty->Fetch()) {
					$arMakros['#PROPERTY_'.$arProps['CODE'].'#'] = $arProps['CODE'];
				}
				
				$dbOrderProps = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID"=>$orderId));
				
				while($arOrderProps = $dbOrderProps->Fetch()) {
					if($arOrderProps['CODE'] == $phoneFieldCode) {
						$userPhone = $arOrderProps['VALUE'];
						$arMakros["#USER_PHONE#"] = $userPhone;
					}
					//if(in_array($arOrder['CODE'],$arMakros)) {
						$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
					//}
				}
				
				$arDelivery = \CSaleDelivery::GetByID($arOrderFields['DELIVERY_ID']); //NAME
				if(is_array($arDelivery) && isset($arDelivery["NAME"])){
					$delivery = $arDelivery["NAME"];
				}else{
					$delivery = "";
				}
				
				$arMakros['#ORDER_NUM#'] = $arOrderFields['ID'];
				$arMakros['#ORDER_PRICE#'] = $arOrderFields['PRICE'];
				$arMakros['#DELIVERY_PRICE#'] = $arOrderFields['PRICE_DELIVERY'];
				$arMakros['#STATUS_NAME#'] = $statusName;
				$arMakros['#DELIVERY_NAME#'] = $delivery;
				$arMakros['#ORDER_SUM#'] = round($arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY']);
				
				$userMess = \COption::GetOptionString('mlife.smsservices', "mess_status_".$arParameters["LID"]."_new_".$arParameters["PERSON_TYPE_ID"], "");
				if(strlen($userMess)>0) {
					$userMess = str_replace(array_keys($arMakros), $arMakros, $userMess);
				}else{
					$userMess = false;
				}
				
				$adminMess = \COption::GetOptionString('mlife.smsservices', "mess_status_".$arParameters["LID"]."_new2_".$arParameters["PERSON_TYPE_ID"], "");
				if(strlen($adminMess)>0) {
					$adminMess = str_replace(array_keys($arMakros), $arMakros, $adminMess);
				}else{
					$adminMess = false;
				}
				
				//$adminMess = COption::GetOptionString('mlife.smsservices', 'adminmess_status_'.$arOrderFields['LID']);
				//$adminMess = str_replace(array_keys($arMakros), $arMakros, $adminMess);
				
				//отправка смс пользователю
				if($userMess){
					$obSmsServ = new \Mlife\Smsservices\Sender();
					$phoneCheck = $obSmsServ->checkPhoneNumber($userPhone);
					$userPhone = $phoneCheck['phone'];
					if($phoneCheck['check']) $arSend = $obSmsServ->sendSms($userPhone,$userMess);
				}
				if($adminMess){
					$obSmsServ = new \Mlife\Smsservices\Sender();
					$phoneCheck = $obSmsServ->checkPhoneNumber($adminPhone);
					$adminPhone = $phoneCheck['phone'];
					if($phoneCheck['check']) $arSend = $obSmsServ->sendSms($adminPhone,$adminMess);
				}
				
			}
		
		}
		
	}
	
	//смена статуса заказа
	public static function OnSaleStatusOrderHandler($orderId,$statusid)
	{
		$active = \COption::GetOptionString('mlife.smsservices', 'activesale', "N");
		
		if($active!="N"){
		
			//заказ
			$arOrderFields = \CSaleOrder::GetByID($orderId);
			
			$arParameters["LID"] = $arOrderFields["LID"]; //ид сайта
			$arParameters["PERSON_TYPE_ID"] = $arOrderFields["PERSON_TYPE_ID"]; //ид плательщика
			
			$phoneFieldCode = \COption::GetOptionString('mlife.smsservices', "property_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			$adminPhone = \COption::GetOptionString('mlife.smsservices', "admin_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			if(strlen($phoneFieldCode)>0) {
				
				$obStatusName = \CSaleStatus::GetList(array(),array('ID'=>$statusid, 'LID'=>LANGUAGE_ID));
				if($ar = $obStatusName->Fetch()) {
					$statusName = $arStat['NAME'];
				}else{
					$statusName = "";
				}
				
				$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"), array("TYPE" => array('TEXT','TEXTAREA')));
				$arMakros = array();
				
				while($arProp = $dbProperty->Fetch()) {
					$arMakros['#PROPERTY_'.$arProps['CODE'].'#'] = $arProps['CODE'];
				}
				
				$dbOrderProps = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID"=>$orderId));
				
				while($arOrderProps = $dbOrderProps->Fetch()) {
					if($arOrderProps['CODE'] == $phoneFieldCode) {
						$userPhone = $arOrderProps['VALUE'];
						$arMakros["#USER_PHONE#"] = $userPhone;
					}
					//if(in_array($arOrder['CODE'],$arMakros)) {
						$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
					//}
				}
				
				$arDelivery = \CSaleDelivery::GetByID($arOrderFields['DELIVERY_ID']); //NAME
				if(is_array($arDelivery) && isset($arDelivery["NAME"])){
					$delivery = $arDelivery["NAME"];
				}else{
					$delivery = "";
				}
				
				$arMakros['#ORDER_NUM#'] = $arOrderFields['ID'];
				$arMakros['#ORDER_PRICE#'] = $arOrderFields['PRICE'];
				$arMakros['#DELIVERY_PRICE#'] = $arOrderFields['PRICE_DELIVERY'];
				$arMakros['#STATUS_NAME#'] = $statusName;
				$arMakros['#DELIVERY_NAME#'] = $delivery;
				$arMakros['#ORDER_SUM#'] = round($arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY']);
				
				$userMess = \COption::GetOptionString('mlife.smsservices', "mess_status_".$arParameters["LID"]."_".$statusid."_".$arParameters["PERSON_TYPE_ID"], "");
				if(strlen($userMess)>0) {
					$userMess = str_replace(array_keys($arMakros), $arMakros, $userMess);
				}else{
					$userMess = false;
				}
				
				//$adminMess = COption::GetOptionString('mlife.smsservices', 'adminmess_status_'.$arOrderFields['LID']);
				//$adminMess = str_replace(array_keys($arMakros), $arMakros, $adminMess);
				
				//отправка смс пользователю
				if($userMess){
					$obSmsServ = new \Mlife\Smsservices\Sender();
					$phoneCheck = $obSmsServ->checkPhoneNumber($userPhone);
					$userPhone = $phoneCheck['phone'];
					if($phoneCheck['check']) $arSend = $obSmsServ->sendSms($userPhone,$userMess);
				}
				
			}
		
		}
		
	}
	
	//отмена заказа
	public static function OnSaleCancelOrderHandler($orderId,$flag)
	{
		
		$active = \COption::GetOptionString('mlife.smsservices', 'activesale', "N");
		
		if($active!="N"){
		
			//заказ
			$arOrderFields = \CSaleOrder::GetByID($orderId);
			
			$arParameters["LID"] = $arOrderFields["LID"]; //ид сайта
			$arParameters["PERSON_TYPE_ID"] = $arOrderFields["PERSON_TYPE_ID"]; //ид плательщика
			
			$phoneFieldCode = \COption::GetOptionString('mlife.smsservices', "property_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			$adminPhone = \COption::GetOptionString('mlife.smsservices', "admin_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			if(strlen($phoneFieldCode)>0) {
				
				$obStatusName = \CSaleStatus::GetList(array(),array('ID'=>$arOrderFields['STATUS_ID'], 'LID'=>LANGUAGE_ID));
				if($ar = $obStatusName->Fetch()) {
					$statusName = $arStat['NAME'];
				}else{
					$statusName = "";
				}
				
				$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"), array("TYPE" => array('TEXT','TEXTAREA')));
				$arMakros = array();
				
				while($arProp = $dbProperty->Fetch()) {
					$arMakros['#PROPERTY_'.$arProps['CODE'].'#'] = $arProps['CODE'];
				}
				
				$dbOrderProps = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID"=>$orderId));
				
				while($arOrderProps = $dbOrderProps->Fetch()) {
					if($arOrderProps['CODE'] == $phoneFieldCode) {
						$userPhone = $arOrderProps['VALUE'];
						$arMakros["#USER_PHONE#"] = $userPhone;
					}
					//if(in_array($arOrder['CODE'],$arMakros)) {
						$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
					//}
				}
				
				$arDelivery = \CSaleDelivery::GetByID($arOrderFields['DELIVERY_ID']); //NAME
				if(is_array($arDelivery) && isset($arDelivery["NAME"])){
					$delivery = $arDelivery["NAME"];
				}else{
					$delivery = "";
				}
				
				$arMakros['#ORDER_NUM#'] = $arOrderFields['ID'];
				$arMakros['#ORDER_PRICE#'] = $arOrderFields['PRICE'];
				$arMakros['#DELIVERY_PRICE#'] = $arOrderFields['PRICE_DELIVERY'];
				$arMakros['#STATUS_NAME#'] = $statusName;
				$arMakros['#DELIVERY_NAME#'] = $delivery;
				$arMakros['#ORDER_SUM#'] = round($arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY']);
				
				$userMess = \COption::GetOptionString('mlife.smsservices', "mess_status_".$arParameters["LID"]."_cancel".$flag."_".$arParameters["PERSON_TYPE_ID"], "");
				if(strlen($userMess)>0) {
					$userMess = str_replace(array_keys($arMakros), $arMakros, $userMess);
				}else{
					$userMess = false;
				}
				
				//$adminMess = COption::GetOptionString('mlife.smsservices', 'adminmess_status_'.$arOrderFields['LID']);
				//$adminMess = str_replace(array_keys($arMakros), $arMakros, $adminMess);
				
				//отправка смс пользователю
				if($userMess){
					$obSmsServ = new \Mlife\Smsservices\Sender();
					$phoneCheck = $obSmsServ->checkPhoneNumber($userPhone);
					$userPhone = $phoneCheck['phone'];
					if($phoneCheck['check']) $arSend = $obSmsServ->sendSms($userPhone,$userMess);
				}
				
			}
		
		}
	}
	
	//флаг разрешения доставки
	public static function OnSaleDeliveryOrderHandler($orderId,$flag)
	{
		
		$active = \COption::GetOptionString('mlife.smsservices', 'activesale', "N");
		
		if($active!="N"){
		
			//заказ
			$arOrderFields = \CSaleOrder::GetByID($orderId);
			
			$arParameters["LID"] = $arOrderFields["LID"]; //ид сайта
			$arParameters["PERSON_TYPE_ID"] = $arOrderFields["PERSON_TYPE_ID"]; //ид плательщика
			
			$phoneFieldCode = \COption::GetOptionString('mlife.smsservices', "property_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			$adminPhone = \COption::GetOptionString('mlife.smsservices', "admin_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			if(strlen($phoneFieldCode)>0) {
				
				$obStatusName = \CSaleStatus::GetList(array(),array('ID'=>$arOrderFields['STATUS_ID'], 'LID'=>LANGUAGE_ID));
				if($ar = $obStatusName->Fetch()) {
					$statusName = $arStat['NAME'];
				}else{
					$statusName = "";
				}
				
				$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"), array("TYPE" => array('TEXT','TEXTAREA')));
				$arMakros = array();
				
				while($arProp = $dbProperty->Fetch()) {
					$arMakros['#PROPERTY_'.$arProps['CODE'].'#'] = $arProps['CODE'];
				}
				
				$dbOrderProps = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID"=>$orderId));
				
				while($arOrderProps = $dbOrderProps->Fetch()) {
					if($arOrderProps['CODE'] == $phoneFieldCode) {
						$userPhone = $arOrderProps['VALUE'];
						$arMakros["#USER_PHONE#"] = $userPhone;
					}
					//if(in_array($arOrder['CODE'],$arMakros)) {
						$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
					//}
				}
				
				$arDelivery = \CSaleDelivery::GetByID($arOrderFields['DELIVERY_ID']); //NAME
				if(is_array($arDelivery) && isset($arDelivery["NAME"])){
					$delivery = $arDelivery["NAME"];
				}else{
					$delivery = "";
				}
				
				$arMakros['#ORDER_NUM#'] = $arOrderFields['ID'];
				$arMakros['#ORDER_PRICE#'] = $arOrderFields['PRICE'];
				$arMakros['#DELIVERY_PRICE#'] = $arOrderFields['PRICE_DELIVERY'];
				$arMakros['#STATUS_NAME#'] = $statusName;
				$arMakros['#DELIVERY_NAME#'] = $delivery;
				$arMakros['#ORDER_SUM#'] = round($arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY']);
				
				$userMess = \COption::GetOptionString('mlife.smsservices', "mess_status_".$arParameters["LID"]."_delivery".$flag."_".$arParameters["PERSON_TYPE_ID"], "");
				if(strlen($userMess)>0) {
					$userMess = str_replace(array_keys($arMakros), $arMakros, $userMess);
				}else{
					$userMess = false;
				}
				
				//$adminMess = COption::GetOptionString('mlife.smsservices', 'adminmess_status_'.$arOrderFields['LID']);
				//$adminMess = str_replace(array_keys($arMakros), $arMakros, $adminMess);
				
				//отправка смс пользователю
				if($userMess){
					$obSmsServ = new \Mlife\Smsservices\Sender();
					$phoneCheck = $obSmsServ->checkPhoneNumber($userPhone);
					$userPhone = $phoneCheck['phone'];
					if($phoneCheck['check']) $arSend = $obSmsServ->sendSms($userPhone,$userMess);
				}
				
			}
		
		}
	}
	
	//флаг разрешения оплаты
	public static function OnSalePayOrderHandler($orderId,$flag)
	{
		
		$active = \COption::GetOptionString('mlife.smsservices', 'activesale', "N");
		
		if($active!="N"){
		
			//заказ
			$arOrderFields = \CSaleOrder::GetByID($orderId);
			
			$arParameters["LID"] = $arOrderFields["LID"]; //ид сайта
			$arParameters["PERSON_TYPE_ID"] = $arOrderFields["PERSON_TYPE_ID"]; //ид плательщика
			
			$phoneFieldCode = \COption::GetOptionString('mlife.smsservices', "property_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			$adminPhone = \COption::GetOptionString('mlife.smsservices', "admin_phone_".$arParameters["LID"].'_'.$arParameters["PERSON_TYPE_ID"], "");
			
			if(strlen($phoneFieldCode)>0) {
				
				$obStatusName = \CSaleStatus::GetList(array(),array('ID'=>$arOrderFields['STATUS_ID'], 'LID'=>LANGUAGE_ID));
				if($ar = $obStatusName->Fetch()) {
					$statusName = $arStat['NAME'];
				}else{
					$statusName = "";
				}
				
				$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"), array("TYPE" => array('TEXT','TEXTAREA')));
				$arMakros = array();
				
				while($arProp = $dbProperty->Fetch()) {
					$arMakros['#PROPERTY_'.$arProps['CODE'].'#'] = $arProps['CODE'];
				}
				
				$dbOrderProps = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID"=>$orderId));
				
				while($arOrderProps = $dbOrderProps->Fetch()) {
					if($arOrderProps['CODE'] == $phoneFieldCode) {
						$userPhone = $arOrderProps['VALUE'];
						$arMakros["#USER_PHONE#"] = $userPhone;
					}
					//if(in_array($arOrder['CODE'],$arMakros)) {
						$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
					//}
				}
				
				$arDelivery = \CSaleDelivery::GetByID($arOrderFields['DELIVERY_ID']); //NAME
				if(is_array($arDelivery) && isset($arDelivery["NAME"])){
					$delivery = $arDelivery["NAME"];
				}else{
					$delivery = "";
				}
				
				$arMakros['#ORDER_NUM#'] = $arOrderFields['ID'];
				$arMakros['#ORDER_PRICE#'] = $arOrderFields['PRICE'];
				$arMakros['#DELIVERY_PRICE#'] = $arOrderFields['PRICE_DELIVERY'];
				$arMakros['#STATUS_NAME#'] = $statusName;
				$arMakros['#DELIVERY_NAME#'] = $delivery;
				$arMakros['#ORDER_SUM#'] = round($arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY']);
				
				$userMess = \COption::GetOptionString('mlife.smsservices', "mess_status_".$arParameters["LID"]."_pay".$flag."_".$arParameters["PERSON_TYPE_ID"], "");
				if(strlen($userMess)>0) {
					$userMess = str_replace(array_keys($arMakros), $arMakros, $userMess);
				}else{
					$userMess = false;
				}
				
				//$adminMess = COption::GetOptionString('mlife.smsservices', 'adminmess_status_'.$arOrderFields['LID']);
				//$adminMess = str_replace(array_keys($arMakros), $arMakros, $adminMess);
				
				//отправка смс пользователю
				if($userMess){
					$obSmsServ = new \Mlife\Smsservices\Sender();
					$phoneCheck = $obSmsServ->checkPhoneNumber($userPhone);
					$userPhone = $phoneCheck['phone'];
					if($phoneCheck['check']) $arSend = $obSmsServ->sendSms($userPhone,$userMess);
				}
				
			}
		
		}
	}
	
}