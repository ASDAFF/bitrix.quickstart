<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz\Deliver;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
 
class dlvdefault {
	
	private static $row = false;
	
	public static function showParamsForm($str_PARAMS){
		
		$paramArray = self::getParamsArray($str_PARAMS);
		
		$res = \Mlife\Asz\StateTable::getList(
			array(
				'filter' => array("ACTIVE"=>"Y"),
				'select' => array("ID","NAME","CN.SITEID","CN.NAME"),
				'order' => array("CN.NAME"=>"ASC","SORT"=>"ASC","NAME"=>"ASC"),
				)
		);
		
		$activeDelivery = array();
		if($paramArray['location'] && $paramArray['location']!='all'){
			$activeDelivery = explode(",",$paramArray['location']);
		}
		$arDelivery = array();
		while($arRes = $res->Fetch()){
			$arDelivery[$arRes['ID']] = '['.$arRes['MLIFE_ASZ_STATE_CN_SITEID'].']['.$arRes['MLIFE_ASZ_STATE_CN_NAME'].']['.$arRes['ID'].']'.$arRes['NAME'];
		}
		
		$html = "";
		$html .= "<tr><td>".Loc::getMessage("MLIFE_ASZ_DLVDEFAULT_LOGO")."</td><td>";
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
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_DLVDEFAULT_SUMM").'</td><td>';
		$html .= '<input name="summ1" type="text" value="'.$paramArray['summ1'].'"/> - <input type="text" name="summ2" value="'.$paramArray['summ2'].'"/>';
		$html .= '</td></tr>';
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_DLVDEFAULT_TAX").'</td><td>';
		$html .= '<input name="tax" type="text" value="'.$paramArray['tax'].'"/>';
		$html .= '</td></tr>';
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_DLVDEFAULT_COST").'</td><td>';
		$html .= '<input name="cost" type="text" value="'.$paramArray['cost'].'"/>';
		$html .= '</td></tr>';
		
		$html .= '<tr><td>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_LOCATION").'</td><td>';
		$html .= '<select name="location[]" multiple="multiple">';
		$selected = ($paramArray['location']=='all') ? " selected=selected" : "";
		$html .= '<option value="all"'.$selected.'>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_LOCATION_DEF").'</option>';
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
		if($_REQUEST['cost']) $arParams['cost'] = $_REQUEST['cost'];
		if($_REQUEST['location']) {
			if(is_array($_REQUEST['location'])) {
				if(in_array('all',$_REQUEST['location'])) {
					$arParams['location'] = 'all';
				}else{
					$arParams['location'] = implode(",",$_REQUEST['location']);
				}
			}
		}else{
			$arParams['location'] = 'all';
		}
		
		$strParams = "";
		foreach($arParams as $key=>$val) {
			$strParams .= $key.'='.$val.'|';
		}
		
		return $strParams;
	
	}
	
	public static function getCost($deliverId,$order){
		
		if(intval($deliverId)>0) {
			
			if(!isset(self::$row[$deliverId])) {
				$res = \Mlife\Asz\DeliveryTable::getRowById($deliverId);
				self::$row[$deliverId] = $res;
			}
			
			$paramArray = self::getParamsArray(self::$row[$deliverId]['PARAMS']);
			
			return $paramArray['cost'];
			
		}
		
		return '0';
		
	}
	
	public static function getRight($deliverId,$order) {
		
		if(!isset(self::$row[$deliverId])) {
			$res = \Mlife\Asz\DeliveryTable::getRowById($deliverId);
			self::$row[$deliverId] = $res;
		}
		$paramArray = self::getParamsArray(self::$row[$deliverId]['PARAMS']);
		
		if($paramArray['summ2']<=0) {
			$right = true;
		}elseif(($paramArray['summ2'] >= $order['ITEMSUMFIN']) && ($paramArray['summ1'] <= $order['ITEMSUMFIN'])) {
			$right = true;
		}else{
			$right = false;
		}
		
		//проверка привязок к местоположениям
		if($right) {
			if($paramArray['location']=='all'){
				$right = true;
			}else{
				if($order['LOCATION_ID']){
					if(in_array($order['LOCATION_ID'],explode(',',$paramArray['location']))) {
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
	
	public static function getImage($deliverId){
		
		if(!isset(self::$row[$deliverId])) {
			$res = \Mlife\Asz\DeliveryTable::getRowById($deliverId);
			self::$row[$deliverId] = $res;
		}
		$paramArray = self::getParamsArray(self::$row[$deliverId]['PARAMS']);
		
		return $paramArray['image'];
		
	}
	
	public static function getParamsArray($str_PARAMS) {
		
		$paramArray = array(
		"image" => "/bitrix/images/mlife/asz/deliver.png",
		"summ1" => "0",
		"summ2" => "0",
		"tax" => "0",
		"cost" => "0",
		"location" => "all",
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
	
	
}