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

//TODO вынести ошибки в языковые файлы

class payw {
	
	private static $row = false; //статическая переменная, сюда кешируем параметры
	
	//html форма с параметрами обработчика
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
		
		$activeStatus = array();
		if($paramArray['ASZ_STATUS'] && $paramArray['ASZ_STATUS']!='all'){
			$activeStatus = explode(",",$paramArray['ASZ_STATUS']);
		}
		$arStatus = array();
		//статусы
		$res = \Mlife\Asz\OrderStatusTable::getList(
			array(
				'select' => array("ID","NAME","SITEID"),
				'filter' => array("ACTIVE"=>"Y"),
			)
		);
		$arStatus = array();
		while($resAr = $res->Fetch()){
			$arStatus[$resAr["ID"]] = "[".$resAr["SITEID"]."] - ".$resAr["NAME"];
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
		
		$html .= '<tr class="heading"><td colspan="2">'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM3").'</td></tr>';
		$html .= '<tr><td colspan="2">'.Loc::getMessage("MLIFE_ASZ_PAYW1_URL").'/bitrix/tools/mlife.asz/pay.php</td></tr>';
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM4").'</td>
		<td><input name="WMI_MERCHANT_ID" type="text" value="'.$paramArray['WMI_MERCHANT_ID'].'"/></td>
		</tr>';
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM5").'</td>
		<td><input name="WMI_SIGNATURE" type="text" value="'.$paramArray['WMI_SIGNATURE'].'"/></td>
		</tr>';
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM6").'</td>
		<td><input name="WMI_CURRENCY_ID" type="text" value="'.$paramArray['WMI_CURRENCY_ID'].'"/></td>
		</tr>';
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM7").'</td>
		<td><input name="ASZ_CURRENCY_ID" type="text" value="'.$paramArray['ASZ_CURRENCY_ID'].'"/></td>
		</tr>';
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM8").'</td>
		<td><input name="ASZ_ADRESS1" type="text" value="'.$paramArray['ASZ_ADRESS1'].'"/></td>
		</tr>';
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM9").'</td>
		<td><input name="ASZ_ADRESS2" type="text" value="'.$paramArray['ASZ_ADRESS2'].'"/></td>
		</tr>';

		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM10").'</td>
		<td>';
		$html .= '<select name="ASZ_STATUS[]" multiple="multiple">';
		$selected = ($paramArray['ASZ_STATUS']=='all') ? " selected=selected" : "";
		$html .= '<option value="all"'.$selected.'>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_STATUS_DEF").'</option>';
		foreach($arStatus as $key=>$val){
			$selected = (in_array($key,$activeStatus)) ? " selected=selected" : "";
			$html .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		}
		$html .= '</select>';
		$html .= '</tr>';
		
		$html .= '<tr>
		<td>'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM11").'</td>
		<td>';
		$html .= '<select name="ASZ_STATUS_S">';
		$selected = ($paramArray['ASZ_STATUS_S']=='') ? " selected=selected" : "";
		$html .= '<option value=""'.$selected.'>'.Loc::getMessage("MLIFE_ASZ_PAYDEFAULT_STATUS_DEF2").'</option>';
		foreach($arStatus as $key=>$val){
			$selected = ($key==$paramArray["ASZ_STATUS_S"]) ? " selected=selected" : "";
			$html .= '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		}
		$html .= '</select>';
		$html .= '</tr>';
		
		return $html;
	}
	
	//проверка формы и возврат сериализованной строки с параметрами
	public static function onSendParamsForm(){
		
		$arParams = array();
		if($_REQUEST['image']) $arParams['image'] = $_REQUEST['image'];
		if($_REQUEST['summ1']) $arParams['summ1'] = $_REQUEST['summ1'];
		if($_REQUEST['summ2']) $arParams['summ2'] = $_REQUEST['summ2'];
		if($_REQUEST['tax']) $arParams['tax'] = $_REQUEST['tax'];
		if($_REQUEST['WMI_MERCHANT_ID']) $arParams['WMI_MERCHANT_ID'] = $_REQUEST['WMI_MERCHANT_ID'];
		if($_REQUEST['WMI_SIGNATURE']) $arParams['WMI_SIGNATURE'] = $_REQUEST['WMI_SIGNATURE'];
		if($_REQUEST['WMI_CURRENCY_ID']) $arParams['WMI_CURRENCY_ID'] = $_REQUEST['WMI_CURRENCY_ID'];
		if($_REQUEST['ASZ_CURRENCY_ID']) $arParams['ASZ_CURRENCY_ID'] = $_REQUEST['ASZ_CURRENCY_ID'];
		if($_REQUEST['ASZ_ADRESS1']) $arParams['ASZ_ADRESS1'] = $_REQUEST['ASZ_ADRESS1'];
		if($_REQUEST['ASZ_ADRESS2']) $arParams['ASZ_ADRESS2'] = $_REQUEST['ASZ_ADRESS2'];
		if($_REQUEST['ASZ_STATUS_S']) $arParams['ASZ_STATUS_S'] = intval($_REQUEST['ASZ_STATUS_S']);
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
		
		if($_REQUEST['ASZ_STATUS']) {
			if(is_array($_REQUEST['ASZ_STATUS'])) {
				if(in_array('all',$_REQUEST['ASZ_STATUS'])) {
					$arParams['ASZ_STATUS'] = 'all';
				}else{
					$arParams['ASZ_STATUS'] = implode(",",$_REQUEST['ASZ_STATUS']);
				}
			}
		}else{
			$arParams['ASZ_STATUS'] = 'all';
		}
		
		$strParams = "";
		foreach($arParams as $key=>$val) {
			$strParams .= $key.'+='.$val.'||';
		}
		
		return $strParams;
	
	}
	
	//стоимость данного способа оплаты
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
	
	//права на вывод данного способо оплаты
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
	
	//возвращает иконку платежки
	public static function getImage($paymentId){
		
		if(!isset(self::$row[$paymentId])) {
			$res = \Mlife\Asz\PaysystemTable::getRowById($paymentId);
			self::$row[$paymentId] = $res;
		}
		$paramArray = self::getParamsArray(self::$row[$paymentId]['PARAMS']);
		
		return $paramArray['image'];
		
	}
	
	//получение параметров из сериализованной строки
	public static function getParamsArray($str_PARAMS) {
		
		$paramArray = array(
		"image" => "/bitrix/images/mlife/asz/deliver.png",
		"summ1" => "0",
		"summ2" => "0",
		"tax" => "0",
		"delivery" => "all",
		);
		
		if(strpos($str_PARAMS,"||")!==false) {
			$tempAr = explode("||",$str_PARAMS);
			foreach($tempAr as $val){
				if(strlen($val)>0){
					$temp = explode("+=",$val);
					if(count($temp)==2){
						$paramArray[$temp[0]] = $temp[1];
					}
				}
			}
		}
		
		return $paramArray;
		
	}
	
	//проверка запроса, полученного от сервиса
	public static function checkPay($order){
		
		$paymentId = $order["PAY_ID"];
		
		if(!isset(self::$row[$paymentId])) {
			self::$row[$paymentId] = $order["PAYN_PARAMS"];
		}
		
		$paramArray = self::getParamsArray(self::$row[$paymentId]['PARAMS']);
		
		if (!isset($_POST["WMI_SIGNATURE"])) self::checkPayPrinter("Retry","WMI_SIGNATURE not found");
		if (!isset($_POST["WMI_PAYMENT_NO"])) self::checkPayPrinter("Retry","WMI_PAYMENT_NO not found");
		if (!isset($_POST["WMI_ORDER_STATE"])) self::checkPayPrinter("Retry","WMI_ORDER_STATE not found");
		
		// Извлечение всех параметров POST-запроса, кроме WMI_SIGNATURE
		foreach($_POST as $name => $value)
		{
		  if ($name !== "WMI_SIGNATURE") $params[$name] = $value;
		}
		// Сортировка массива по именам ключей в порядке возрастания
		// и формирование сообщения, путем объединения значений формы
		 
		uksort($params, "strcasecmp"); $values = "";
		 
		foreach($params as $name => $value)
		{
			if(ToLower(SITE_CHARSET) == 'utf-8'){
				$value = $GLOBALS["APPLICATION"]->ConvertCharset($value, "Windows-1251", SITE_CHARSET);
			}
			$values .= $value;
		}
		
		// Формирование подписи для сравнения ее с параметром WMI_SIGNATURE
		$signature = base64_encode(pack("H*", md5($values . $paramArray["WMI_SIGNATURE"])));
		
		//Сравнение полученной подписи с подписью W1
		if ($signature == $_POST["WMI_SIGNATURE"]){
			if (strtoupper($_POST["WMI_ORDER_STATE"]) == "ACCEPTED"){
				
				if($paramArray["ASZ_STATUS_S"]>0){
					//обновление статуса
					$res = \Mlife\Asz\OrderTable::update($order["ID"],array("STATUS"=>$paramArray["ASZ_STATUS_S"]));
					self::checkPayPrinter("Ok", "Order #" . intval($_POST["WMI_PAYMENT_NO"]) . " payed!");
				}else{
					self::checkPayPrinter("Ok", "Order #" . intval($_POST["WMI_PAYMENT_NO"]) . " payed! Status not Found!");
				}
				
				
			}else{
				//Случилось что-то странное, пришло неизвестное состояние заказа
				self::checkPayPrinter("Retry", "WMI_ORDER_STATE fail");
			}
		}else{
			//Подпись не совпадает, возможно вы поменяли настройки интернет-магазина
			self::checkPayPrinter("Retry", "WMI_SIGNATURE fail");
		}
		
		return true;
		
	}
	
	//выдача ответа сервису
	public static function checkPayPrinter($result, $description){
		print "WMI_RESULT=" . strtoupper($result) . "&";
		print "WMI_DESCRIPTION=" .urlencode($description);
		exit();
	}
	
	//получение кнопки для оплаты
	public static function getPayButton($orderId){
		
		if(!$orderId) return '';
		
		$res = \Mlife\Asz\OrderTable::getList(array("select"=>array("*"),"filter"=>array("ID"=>$orderId)));
		if($dataAr = $res->Fetch()){
			
			$paymentId = $dataAr["PAY_ID"];
			
			if(!isset(self::$row[$paymentId])) {
				$res = \Mlife\Asz\PaysystemTable::getRowById($paymentId);
				self::$row[$paymentId] = $res;
			}
			
			$paramArray = self::getParamsArray(self::$row[$paymentId]['PARAMS']);
			
			if($paramArray['ASZ_STATUS']=='all'){
				$right = true;
			}else{
				if($dataAr['STATUS']){
					if(in_array($dataAr['STATUS'],explode(',',$paramArray['ASZ_STATUS']))) {
						$right = true;
					}else{
						$right = false;
					}
				}else{
					$right = false;
				}
			}
			if(!$right) return "";
			
			$price = \Mlife\Asz\CurencyFunc::convertFromBase($dataAr["PRICE"],$paramArray["ASZ_CURRENCY_ID"],$dataAr["SITEID"]);
			$price = number_format($price,2,".","");
			
			$arField = array(
				"WMI_MERCHANT_ID" => $paramArray["WMI_MERCHANT_ID"],
				"WMI_PAYMENT_AMOUNT" => $price,
				"WMI_CURRENCY_ID" => $paramArray["WMI_CURRENCY_ID"],
				"WMI_DESCRIPTION" => Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM1").' '.$orderId,
				"WMI_SUCCESS_URL" => $paramArray["ASZ_ADRESS1"],
				"WMI_FAIL_URL" => $paramArray["ASZ_ADRESS2"],
				"WMI_PAYMENT_NO" => $orderId,
			);
			
			//Сортировка значений внутри полей
			  foreach($arField as $name => $val)
			  {
				if (is_array($val))
				{
				   usort($val, "strcasecmp");
				   $arField[$name] = $val;
				}
			  }
			uksort($arField, "strcasecmp");
			$fieldValues = "";
			
			foreach($arField as $value){
				if (is_array($value)){
					foreach($value as $v){
						if(ToLower(SITE_CHARSET) == 'utf-8'){
							$v = $GLOBALS["APPLICATION"]->ConvertCharset($v, "Windows-1251", SITE_CHARSET);
						}
						$fieldValues .= $v;
					}
				}else{
				   if(ToLower(SITE_CHARSET) == 'utf-8'){
						$v = $GLOBALS["APPLICATION"]->ConvertCharset($v, "Windows-1251", SITE_CHARSET);
					}
				   $fieldValues .= $value;
				}
			}
			
			$key = $paramArray["WMI_SIGNATURE"];
			
			$signature = base64_encode(pack("H*", md5($fieldValues . $key)));
			$arField["WMI_SIGNATURE"] = $signature;
			
			$html = '<form method="post" action="https://www.walletone.com/checkout/default.aspx" accept-charset="'.SITE_CHARSET.'">';
			
			foreach($arField as $key=>$val){
				$html .= '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
			}
			
			$html .= '<input type="submit" value="'.Loc::getMessage("MLIFE_ASZ_PAYW1_PARAM2").'"/>';
			$html .= '</form>';
			
			return $html;
			
		}
		return "";
		
	}
	
	
}