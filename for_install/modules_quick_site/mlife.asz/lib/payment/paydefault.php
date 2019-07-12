<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz\Payment;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
 
class paydefault {

	private static $row = false;
	
	public static function showParamsForm($str_PARAMS){
		
		$paramArray = self::getParamsArray($str_PARAMS);
		
		$res = \Mlife\Asz\DeliveryTable::getList(
			array(
				'filter' => array("ACTIVE"=>"Y"),
				'select' => array("ID","NAME","SITEID")
				)
		);
		
		$activeDelivery = array();
		if($paramArray['delivery'] && $paramArray['delivery']!='all'){
			$activeDelivery = explode(",",$paramArray['delivery']);
		}
		$arDelivery = array();
		while($arRes = $res->Fetch()){
			$arDelivery[$arRes['ID']] = '['.$arRes['SITEID'].']['.$arRes['ID'].']'.$arRes['NAME'];
		}
		
		$html = "";
		$html .= "<tr><td>".Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_LOGO")."</td><td>";
		$html .= \CAdminFileDialog::ShowScript(array(
						"event" => "image2",
						"arResultDest" => array("ELEMENT_ID" => "image"),
						"arPath" => array("PATH" => GetDirPath($paramArray['image'])),
						"select" => 'F',// F - file only, D - folder only
						"operation" => 'O',// O - open, S - save
						"showUploadTab" => true,
						"showAddToMenuTab" => false,
						"fileFilter" => 'jpg,jpeg,png,gif',
						"allowAllFiles" => false,
						"SaveConfig" => true,
					));
		$html .=	'<input
						name="image"
						id="image"
						type="text"
						value="'.htmlspecialcharsbx($paramArray['image']).'"
						size="35">&nbsp;<input
						type="button"
						value="..."
						onClick="image2()"
					>';
		$html .= "</td></tr>";
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_SUMM").'</td><td>';
		$html .= '<input name="summ1" type="text" value="'.$paramArray['summ1'].'"/> - <input type="text" name="summ2" value="'.$paramArray['summ2'].'"/>';
		$html .= '</td></tr>';
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_TAX").'</td><td>';
		$html .= '<input name="tax" type="text" value="'.$paramArray['tax'].'"/>';
		$html .= '</td></tr>';
		
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_DELIVERY").'</td><td>';
		$html .= '<select name="delivery[]" multiple="multiple">';
		$selected = ($paramArray['delivery']=='all') ? " selected=selected" : "";
		$html .= '<option value="all"'.$selected.'>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_DELIVERY_DEF").'</option>';
		foreach($arDelivery as $key=>$val){
			$selected = (in_array($key,$activeDelivery)) ? " selected=selected" : "";
			$html .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		}
		$html .= '</select>';
		$html .= '</td></tr>';
		
		return $html;
	}
	
	public static function onSendParamsForm(){
		
		$arParams = array();
		if($_REQUEST['image']) $arParams['image'] = $_REQUEST['image'];
		if($_REQUEST['summ1']) $arParams['summ1'] = $_REQUEST['summ1'];
		if($_REQUEST['summ2']) $arParams['summ2'] = $_REQUEST['summ2'];
		if($_REQUEST['tax']) $arParams['tax'] = $_REQUEST['tax'];
		if($_REQUEST['delivery']) {
			if(is_array($_REQUEST['delivery'])) {
				if(in_array('all',$_REQUEST['delivery'])) {
					$arParams['delivery'] = 'all';
				}else{
					$arParams['delivery'] = implode(",",$_REQUEST['delivery']);
				}
			}
		}else{
			$arParams['delivery'] = 'all';
		}
		
		$strParams = "";
		foreach($arParams as $key=>$val) {
			$strParams .= $key.'='.$val.'|';
		}
		
		return $strParams;
	
	}
	
	public static function getCost($paymentId,$order){
		
		if(intval($paymentId)>0) {
			
			if(!isset(self::$row[$paymentId])) {
				$res = \Mlife\Asz\PaysystemTable::getRowById($paymentId);
				self::$row[$paymentId] = $res;
			}
			
			$paramArray = self::getParamsArray(self::$row[$paymentId]['PARAMS']);
			
			if($paramArray['tax']>0) {
				$cost = round((($order['ITEMSUMFIN'] * $paramArray['tax']) / 100),2);
			}else{
				$cost = 0;
			}
			
			return $cost;
			
		}
		
		return '0';
		
	}
	
	public static function getRight($paymentId,$order) {
		
		if(!isset(self::$row[$paymentId])) {
			$res = \Mlife\Asz\PaysystemTable::getRowById($paymentId);
			self::$row[$paymentId] = $res;
		}
		$paramArray = self::getParamsArray(self::$row[$paymentId]['PARAMS']);
		
		if($paramArray['summ2']<=0) {
			$right = true;
		}elseif(($paramArray['summ2'] >= $order['ITEMSUMFIN']) && ($paramArray['summ1'] <= $order['ITEMSUMFIN'])) {
			$right = true;
		}else{
			$right = false;
		}
		
		//проверка привязок служб доставки
		if($right) {
			if($paramArray['delivery']=='all'){
				$right = true;
			}else{
				if($order['DELIVERY_ID']){
					if(in_array($order['DELIVERY_ID'],explode(',',$paramArray['delivery']))) {
						$right = true;
					}else{
						$right = false;
					}
				}else{
					$right = true;
				}
			}
		}
		
		return $right;
		
	}
	
	public static function getImage($paymentId){
		
		if(!isset(self::$row[$paymentId])) {
			$res = \Mlife\Asz\PaysystemTable::getRowById($paymentId);
			self::$row[$paymentId] = $res;
		}
		$paramArray = self::getParamsArray(self::$row[$paymentId]['PARAMS']);
		
		return $paramArray['image'];
		
	}
	
	public static function getParamsArray($str_PARAMS) {
		
		$paramArray = array(
		"image" => "/bitrix/images/mlife/asz/deliver.png",
		"summ1" => "0",
		"summ2" => "0",
		"tax" => "0",
		"delivery" => "all",
		);
		
		if(strpos($str_PARAMS,"|")!==false) {
			$tempAr = explode("|",$str_PARAMS);
			foreach($tempAr as $val){
				if(strlen($val)>0){
					$temp = explode("=",$val);
					if(count($temp)==2){
						$paramArray[$temp[0]] = $temp[1];
					}
				}
			}
		}
		
		return $paramArray;
		
	}
	
	public static function getPayButton($orderId){
		return "";
	}
	
	
}