<?php
namespace Mlife\Smsservices;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Events {
	
	public static $cache;
	
	public static function getList(){
		
		$events = array(
			"MSMS_NEWORDER" => array(
				"BX_EVENT" => array(
					array('sale','OnSaleOrderSaved','mlife.smsservices','\Mlife\Smsservices\Events','OnSaleOrderEntitySaved','new'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','newOrderHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','newOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_NEWORDER")
			),
			"MSMS_STATUSUPDATE" => array(
				"BX_EVENT" => array(
					array('sale','OnSaleOrderSaved','mlife.smsservices','\Mlife\Smsservices\Events','OnSaleOrderEntitySaved','new'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','statusOrderHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','statusOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_STATUSUPDATE")
			),
			"MSMS_PAYED" => array(
				"BX_EVENT" => array(
					array('sale','OnSaleOrderSaved','mlife.smsservices','\Mlife\Smsservices\Events','OnSaleOrderEntitySaved','new'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','payedOrderHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','payedOrderSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_PAYED")
			),
			/*"MSMS_BXEVENT" => array(
				"BX_EVENT" => array(
					array('main','OnBeforeEventSend','mlife.smsservices','\Mlife\Smsservices\Events','OnBeforeEventSend','old'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','eventSendHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','eventSendSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_BXEVENT")
			)*/
		);
		
		$event = new \Bitrix\Main\Event("mlife.smsservices", "OnAfterEventsAdd",array("EVENTS"=>$events));
		$event->send();
		   if ($event->getResults()){
			  foreach($event->getResults() as $evenResult){
				 if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
				 $params = $evenResult->getParameters();
				 if(is_array($params['EVENTS'])) $events = $params['EVENTS'];
			  }
		   }
		}
		
		$allType = \Bitrix\Main\Mail\Internal\EventTypeTable::getList(array(
			'select' => array('NAME','EVENT_NAME'),
			'filter' => array('LID'=>LANG)
		));
		$arAllType = array();
		while($dt = $allType->fetch()){
			$events["MSMS_BXEVENT_".$dt['EVENT_NAME']] = array(
				"BX_EVENT" => array(
					array('main','OnBeforeEventSend','mlife.smsservices','\Mlife\Smsservices\Events','OnBeforeEventSend','old'),
				),
				"FIELD" => array(
					"HTML" => array('\Mlife\Smsservices\Fields','eventSendHtml'),
					"BEFORE_SAVE" => array('\Mlife\Smsservices\Fields','eventSendSave')
				),
				"NAME" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_BXEVENT").' - '.$dt['NAME']
			);
		}
		
		return $events;
		
	}
	
	public static function OnSaleOrderEntitySaved(\Bitrix\Main\Event $event){
		
		$order = $event->getParameter("ENTITY");
		
		if($order){
		
			$orderId = $order->getId();
			
			$oldValues = $event->getParameter("VALUES");
			$isNew = $order->isNew();
			
			$arOrderFields = \Bitrix\Sale\Internals\OrderTable::getList(
				array(
					'select' => array(
						"ID",
						"DATE_INSERT_FORMAT",
						"DATE_INSERT",
						"LID",
						"ACCOUNT_NUMBER",
						"TRACKING_NUMBER",
						"PAY_SYSTEM_ID",
						"DELIVERY_ID",
						"PERSON_TYPE_ID",
						"USER_ID",
						"PAYED",
						"STATUS_ID",
						"PRICE_DELIVERY",
						"ALLOW_DELIVERY",
						/*"PRICE_PAYMENT",*/
						"PRICE",
						"CURRENCY",
						"DISCOUNT_VALUE",
						"TAX_VALUE",
						"SUM_PAID",
						"USER_DESCRIPTION",
						"AFFILIATE_ID",
						//"BASKET_PRICE_TOTAL",
						"STATUS_NAME"=>"STATUS.NAME",
						"USER_EMAIL"=>"USER.EMAIL",
						"USER_NAME"=>"USER.NAME",
						"USER_PERSONAL_PHONE"=>"USER.PERSONAL_PHONE",
						"USER_PERSONAL_MOBILE"=>"USER.PERSONAL_MOBILE",
						"USER_PERSONAL_CITY"=>"USER.PERSONAL_CITY",
						"USER_WORK_PHONE"=>"USER.WORK_PHONE",
						"USER_PERSONAL_GENDER"=>"USER.PERSONAL_GENDER"
						//"*"
						),
					'filter' => array("ID"=>$orderId)
				)
			)->fetch();
			$arOrderFields['DATE_INSERT'] = $arOrderFields['DATE_INSERT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY")));
			
			$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"));
			$arMakros = array();
			
			foreach($arOrderFields as $prop_code=>$val){
				$arMakros['#'.$prop_code.'#'] = $val;
			}
			
			while($arProp = $dbProperty->Fetch()) {
				$arMakros['#PROPERTY_'.$arProp['CODE'].'#'] = '';
			}
			
			$dbOrderProps = \Bitrix\Sale\Internals\OrderPropsValueTable::getList(array(
				'select'=> array("*"), 
				'filter' => array("ORDER_ID"=>$orderId)
			)
			);
					
			while($arOrderProps = $dbOrderProps->fetch()) {
				$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
			}
			
			if ($propertyCollection = $order->getPropertyCollection())
			{
				$propVal = $propertyCollection->getArray();
				foreach($propVal['properties'] as $v){
					$arMakros['#PROPERTY_'.$v['CODE'].'#'] = $v['VALUE'][0];
				}
			}
			
			$arDelivery =  array();
			if($arOrderFields['DELIVERY_ID']) $arDelivery = \Bitrix\Sale\Delivery\Services\Table::getRowById($arOrderFields['DELIVERY_ID']); //NAME
			if(is_array($arDelivery) && isset($arDelivery["NAME"])){
				$delivery = $arDelivery["NAME"];
			}else{
				$delivery = "";
			}
			
			$arPayment = array();
			if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemActionTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
			if(is_array($arPayment) && isset($arPayment["NAME"])){
				$payment = $arPayment["NAME"];
			}else{
				if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
				if(is_array($arPayment) && isset($arPayment["NAME"])){
					$payment = $arPayment["NAME"];
				}else{
					$payment = "";
				}
			}
			
			$arMakros['#ORDER_SUM#'] = $arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY'];
			$arMakros['#DELIVERY_NAME#'] = $delivery;
			$arMakros['#PAYMENT_NAME#'] = $payment;
			
			if(\Bitrix\Main\Loader::includeModule('currency') && \Bitrix\Main\Loader::includeModule('catalog')){
				$arMakros['#ORDER_SUM_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#ORDER_SUM#'],$arOrderFields['CURRENCY']);
				$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
				$arMakros['#PRICE_DELIVERY_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE_DELIVERY#'],$arOrderFields['CURRENCY']);
				//$arMakros['#PRICE_PAYMENT_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE_PAYMENT#'],$arOrderFields['CURRENCY']);
				$arMakros['#PRICE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE#'],$arOrderFields['CURRENCY']);
				$arMakros['#DISCOUNT_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#DISCOUNT_VALUE#'],$arOrderFields['CURRENCY']);
				$arMakros['#TAX_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#TAX_VALUE#'],$arOrderFields['CURRENCY']);
				$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
			}
			$arMakros['#EVENT_NAME#'] = 'MSMS_ORDER_'.$arOrderFields['ID'];
			
			if($isNew){
				
				//MSMS_NEWORDER новый заказ 
				$res = \Mlife\Smsservices\EventlistTable::getList(
					array(
						'select' => array("*"),
						'filter' => array("=EVENT"=>'MSMS_NEWORDER',"ACTIVE"=>"Y","SITE_ID"=>$arOrderFields['LID'])
					)
				);
				while($arData = $res->fetch()){
					$arData['PARAMS'] = unserialize($arData['PARAMS']);
					if($arData['PARAMS']['PHONE']){
						$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
						$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
						
						$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
						$phoneAr = explode(",",$phoneAr);
						$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
						
						foreach($phoneAr as $phone){
							
							if(strlen($phone)>7){
								
								
								if(trim($arData['TEMPLATE'])){
									$smsOb = new \Mlife\Smsservices\Sender();
									$smsOb->event = $arMakros['#EVENT_NAME#'];
									$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_NEWORDER");
									$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
									$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
									
									
									$smsOb->event = null;
									$smsOb->eventName = null;
								}
								
								break;
							}
						}
					}
				}
				
			}
			
			//MSMS_STATUSUPDATE смена статуса заказа
			if($oldValues['STATUS_ID'] && ($oldValues['STATUS_ID'] != $arOrderFields['STATUS_ID'])){
				$res = \Mlife\Smsservices\EventlistTable::getList(
					array(
						'select' => array("*"),
						'filter' => array("=EVENT"=>'MSMS_STATUSUPDATE',"ACTIVE"=>"Y","SITE_ID"=>$arOrderFields['LID'])
					)
				);
				while($arData = $res->fetch()){
					$arData['PARAMS'] = unserialize($arData['PARAMS']);
					
					$right = false;
					
					if($arData['PARAMS']['STATUS_FROM'] == 'ALL') {
						$right = true;
					}else{
						if($arData['PARAMS']['STATUS_FROM'] == $oldValues['STATUS_ID']) $right = true;
					}
					if($right){
						$right = false;
						if($arData['PARAMS']['STATUS_TO'] == 'ALL') {
							$right = true;
						}else{
							if($arData['PARAMS']['STATUS_TO'] == $arOrderFields['STATUS_ID']) $right = true;
						}
					}
					
					if($arData['PARAMS']['PHONE'] && $right){
						$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
						$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
						$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
						$phoneAr = explode(",",$phoneAr);
						$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
						
						foreach($phoneAr as $phone){
							if(strlen($phone)>7){
								
								
								if(trim($arData['TEMPLATE'])){
									$smsOb = new \Mlife\Smsservices\Sender();
									$smsOb->event = $arMakros['#EVENT_NAME#'];
									$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_STATUSUPDATE");
									$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
									$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
									
									$smsOb->event = null;
									$smsOb->eventName = null;
								}
								
								break;
							}
						}
					}
					
				}
			}
			
			//MSMS_PAYED - оплата заказа
			if($oldValues['PAYED'] && ($oldValues['PAYED'] != $arOrderFields['PAYED'])){
				$res = \Mlife\Smsservices\EventlistTable::getList(
					array(
						'select' => array("*"),
						'filter' => array("=EVENT"=>'MSMS_PAYED',"ACTIVE"=>"Y","SITE_ID"=>$arOrderFields['LID'])
					)
				);
				while($arData = $res->fetch()){
					$arData['PARAMS'] = unserialize($arData['PARAMS']);
					
					$right = false;
					if($arOrderFields['PAYED'] == $arData['PARAMS']['PAYED']) $right = true;
					
					if($arData['PARAMS']['PHONE'] && $right){
						$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
						$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
						$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
						$phoneAr = explode(",",$phoneAr);
						$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
						
						foreach($phoneAr as $phone){
							if(strlen($phone)>7){
								
								
								if(trim($arData['TEMPLATE'])){
									$smsOb = new \Mlife\Smsservices\Sender();
									$smsOb->event = $arMakros['#EVENT_NAME#'];
									$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_PAYED");
									$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
									$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
									
									$smsOb->event = null;
									$smsOb->eventName = null;
								}
								
								break;
							}
						}
					}
					
				}
			}
			
			
		}
		
	}
	
	public static function executePhp($template,&$macros,&$arParams)
	{
		$result = eval('use \Bitrix\Main\Mail\EventMessageThemeCompiler; ob_start();?>' . $template . '<? return ob_get_clean();');
		return $result;
	}
	
	public static function compileTemplate($template, &$macros){
		$arParams = array();
		foreach($macros as $k=>$v){
			$arParams[str_replace("#","",$k)] = $v;
		}
		$template = str_replace(array_keys($macros), $macros, $template);
		$template = self::executePhp($template,$macros,$arParams);
		foreach($arParams as $k=>$v){
			$macros['#'.$k.'#'] = $v;
		}
		//$template = preg_replace('/(\#[^#]+\#)/is',"",$template);
		return $template;
	}
	
	//вывод таба в админке
	public static function OnAdminTabControlBegin(&$form){
		
		$module_id = "mlife.smsservices";
		$MODULE_RIGHT_ = $GLOBALS["APPLICATION"]->GetGroupRight($module_id);
		
		if( ($MODULE_RIGHT_ >= "R") && (($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order_view.php") || ($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order_edit.php")))
		{
			$orderId = intval($_REQUEST["ID"]);
			
			if($orderId) {
			
				$GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/css/mlife.smsservices/style.css");
				
				$res = \Mlife\Smsservices\ListTable::getList(array(
					'select' => array("*"),
					'filter' => array("=EVENT"=>'MSMS_ORDER_'.$orderId),
					'order' => array("TIME"=>"ASC")
				));
				$html = '<tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_TABCONTROL_NAME").'</td>
				</tr>';
				$html .= '<tr><td colspan="2"><table style="width:100%;border:1px solid #000000;">';
				
				while ($arData = $res->fetch()){
					if(!is_object($arData['TIME'])) $arData['TIME'] = \Bitrix\Main\Type\DateTime::createFromTimestamp($arData['TIME']);
					$html .= '<tr>
					<td style="border:1px solid #000000;">'.$arData['SENDER'].' -> <br>'.$arData['PHONE'].'
					</td>
					<td style="border:1px solid #000000;">'.$arData['TIME']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY HH:MI"))).' -> <br>
					<font class="status_'.(($arData['STATUS']==14 || $arData['STATUS']==15) ? 4 : $arData['STATUS']).'">'.Loc::getMessage("MLIFE_SMSSERVICES_LIST_STATUS_".$arData['STATUS']).'</font>
					</td>
					<td style="border:1px solid #000000;">'.$arData['MEWSS'].'
					</td>
					</tr>';
				}
				$html .= '</table>
				<br/>
				<a href="/bitrix/admin/mlife_smsservices_sendform.php?lang=ru&event=MSMS_ORDER_'.$orderId.'">'.Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_SENDSMS").'</a>
				</td></tr>';
				
				$form->tabs[] = array("DIV" => "my_edit", "TAB" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_TABCONTROL_NAME"), "ICON"=>"aszmagazin", "TITLE"=>Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_TABCONTROL_NAME"), "CONTENT"=>$html);
				
			}
			
		}
		
	}
	
	//отправка писем
	public static function OnBeforeEventSend($arFields, $eventMessage){
		
		//$eventMessage['EVENT_NAME'] - тип события
		//$eventMessage['ID'] - ид шаблона
		//$eventMessage['LID'] - ид сайта
		
		//print_r(array($arFields, $eventMessage)); die();
		
		$returnSendMail = true;
		
		\Bitrix\Main\Loader::includeModule('mlife.smsservices');
		$res = \Mlife\Smsservices\EventlistTable::getList(
			array(
				'select' => array("*"),
				'filter' => array("=EVENT"=>'MSMS_BXEVENT_'.$eventMessage['EVENT_NAME'],"ACTIVE"=>"Y")
			)
		);
		while($arData = $res->fetch()){
			
			$arData['PARAMS'] = unserialize($arData['PARAMS']);
			
			$right = false;
			
			$r_site = \Bitrix\Main\Mail\Internal\EventMessageSiteTable::getList(array(
				'select' => array("SITE_ID"),
				'filter' => array("EVENT_MESSAGE_ID"=>$eventMessage['ID'])
			));
			while($dt = $r_site->fetch()){
				if($arData['SITE_ID'] == $dt['SITE_ID']) {
					$right = true;
					break;
				}
			}
			
			if(!$right) continue;
			
			if($arData['PARAMS']['EVENT_NAME'] == $eventMessage['EVENT_NAME']){
				//print_r($arData);
				//print_r($eventMessage);
				$right = false;
				
				if($arData['PARAMS']['ID'] == 'ALL') {
					$right = true;
				}else{
					if($arData['PARAMS']['ID'] == $eventMessage['ID']) $right = true;
				}
				
				if(!$right) continue;
				
				$arMakros = array();
				foreach($arFields as $fieldKey=>$fieldVal){
					$arMakros['#'.$fieldKey.'#'] = $fieldVal;
				}
				
				$arMakros['#EVENT_NAME#'] = 'MSMS_BXEVENT_'.$eventMessage['EVENT_NAME'];
				
				$orderId = $arMakros['#ORDER_ID#'];
				
				if($orderId && strpos($eventMessage['EVENT_NAME'],'SALE')===false) $orderId = false;
				
				$arOldMacros = $arMakros;
				
				//TODO not working cron events
				if($orderId && \Bitrix\Main\Loader::includeModule('sale')){
					$arMakros = array();
					$arOrderFields = \Bitrix\Sale\Internals\OrderTable::getList(
						array(
							'select' => array(
								"ID",
								"DATE_INSERT_FORMAT",
								"DATE_INSERT",
								"LID",
								"ACCOUNT_NUMBER",
								"TRACKING_NUMBER",
								"PAY_SYSTEM_ID",
								"DELIVERY_ID",
								"PERSON_TYPE_ID",
								"USER_ID",
								"PAYED",
								"STATUS_ID",
								"PRICE_DELIVERY",
								"ALLOW_DELIVERY",
								"PRICE",
								"CURRENCY",
								"DISCOUNT_VALUE",
								"TAX_VALUE",
								"SUM_PAID",
								"USER_DESCRIPTION",
								"AFFILIATE_ID",
								"STATUS_NAME"=>"STATUS.NAME",
								"USER_EMAIL"=>"USER.EMAIL",
								"USER_NAME"=>"USER.NAME",
								"USER_PERSONAL_PHONE"=>"USER.PERSONAL_PHONE",
								"USER_PERSONAL_MOBILE"=>"USER.PERSONAL_MOBILE",
								"USER_PERSONAL_CITY"=>"USER.PERSONAL_CITY",
								"USER_WORK_PHONE"=>"USER.WORK_PHONE",
								"USER_PERSONAL_GENDER"=>"USER.PERSONAL_GENDER"
								),
							'filter' => array("ID"=>$orderId)
						)
					)->fetch();
					if($arOrderFields){
						$arOrderFields['DATE_INSERT'] = $arOrderFields['DATE_INSERT']->toString(new \Bitrix\Main\Context\Culture(array("FORMAT_DATETIME" => "DD.MM.YYYY")));
						
						$dbProperty = \CSaleOrderProps::GetList(array("SORT" => "ASC"));
						$arMakros = array();
						
						foreach($arOrderFields as $prop_code=>$val){
							$arMakros['#'.$prop_code.'#'] = $val;
						}
						
						while($arProp = $dbProperty->Fetch()) {
							$arMakros['#PROPERTY_'.$arProp['CODE'].'#'] = '';
						}
						
						$dbOrderProps = \Bitrix\Sale\Internals\OrderPropsValueTable::getList(array(
							'select'=> array("*"), 
							'filter' => array("ORDER_ID"=>$orderId)
						)
						);
								
						while($arOrderProps = $dbOrderProps->fetch()) {
							$arMakros['#PROPERTY_'.$arOrderProps['CODE'].'#'] = $arOrderProps['VALUE'];
						}
						
						/*if ($propertyCollection = $order->getPropertyCollection())
						{
							$propVal = $propertyCollection->getArray();
							foreach($propVal['properties'] as $v){
								$arMakros['#PROPERTY_'.$v['CODE'].'#'] = $v['VALUE'][0];
							}
						}*/
						
						$arDelivery =  array();
						if($arOrderFields['DELIVERY_ID']) $arDelivery = \Bitrix\Sale\Delivery\Services\Table::getRowById($arOrderFields['DELIVERY_ID']); //NAME
						if(is_array($arDelivery) && isset($arDelivery["NAME"])){
							$delivery = $arDelivery["NAME"];
						}else{
							$delivery = "";
						}
						
						$arPayment = array();
						if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemActionTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
						if(is_array($arPayment) && isset($arPayment["NAME"])){
							$payment = $arPayment["NAME"];
						}else{
							if($arOrderFields['PAY_SYSTEM_ID']) $arPayment = \Bitrix\Sale\Internals\PaySystemTable::getRowById($arOrderFields['PAY_SYSTEM_ID']); //NAME
							if(is_array($arPayment) && isset($arPayment["NAME"])){
								$payment = $arPayment["NAME"];
							}else{
								$payment = "";
							}
						}
						
						$arMakros['#ORDER_SUM#'] = $arOrderFields['PRICE']-$arOrderFields['PRICE_DELIVERY'];
						$arMakros['#DELIVERY_NAME#'] = $delivery;
						$arMakros['#PAYMENT_NAME#'] = $payment;
						
						if(false && \Bitrix\Main\Loader::includeModule('currency') && \Bitrix\Main\Loader::includeModule('catalog')){
							$arMakros['#ORDER_SUM_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#ORDER_SUM#'],$arOrderFields['CURRENCY']);
							$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
							$arMakros['#PRICE_DELIVERY_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE_DELIVERY#'],$arOrderFields['CURRENCY']);
							$arMakros['#PRICE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#PRICE#'],$arOrderFields['CURRENCY']);
							$arMakros['#DISCOUNT_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#DISCOUNT_VALUE#'],$arOrderFields['CURRENCY']);
							$arMakros['#TAX_VALUE_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#TAX_VALUE#'],$arOrderFields['CURRENCY']);
							$arMakros['#SUM_PAID_FORMAT#'] = \CCurrencyLang::CurrencyFormat($arMakros['#SUM_PAID#'],$arOrderFields['CURRENCY']);
						}
						
						$arNmakros = array();
						foreach($arMakros as $k_m=>$v_m){
							$arNmakros["#MSS_".substr($k_m,1)] = $v_m;
						}
						$arMakros = $arNmakros;
						$arMakros['#EVENT_NAME#'] = 'MSMS_ORDER_'.$arOrderFields['ID'];
						
						if(is_array($arOldMacros)){
							foreach($arOldMacros as $k_m=>$v_m){
								$arMakros[$k_m] = $v_m;
							}
						}
						
					}
				}elseif($userId = $arMakros['#USER_ID#']){
					$r = \Bitrix\Main\UserTable::getList(array(
						'select' => array("NAME", "PERSONAL_PHONE", "PERSONAL_MOBILE", "PERSONAL_CITY", "PERSONAL_GENDER", "EMAIL", "WORK_PHONE"),
						'filter' => array("ID"=>$userId)
					))->fetch();
					if($r){
						$arMakros = array();
						foreach($r as $k_m=>$v_m){
							$arMakros['#'.$k_m.'#'] = $v_m;
						}
						$arNmakros = array();
						foreach($arMakros as $k_m=>$v_m){
							$arNmakros["#MSS_USER_".substr($k_m,1)] = $v_m;
						}
						$arMakros = $arNmakros;
						if(is_array($arOldMacros)){
							foreach($arOldMacros as $k_m=>$v_m){
								$arMakros[$k_m] = $v_m;
							}
						}
					}
				}
				
				
				if($arData['PARAMS']['PHONE']){
					$arData['TEMPLATE'] = self::compileTemplate($arData['TEMPLATE'], $arMakros);
					$phoneAr = str_replace(array_keys($arMakros), $arMakros, $arData['PARAMS']['PHONE']);
					$phoneAr = preg_replace("/([^0-9,])/is","",$phoneAr);
					$phoneAr = explode(",",$phoneAr);
					$sender = ($arData['SENDER']) ? $arData['SENDER'] : "";
					foreach($phoneAr as $phone){
						if(strlen($phone)>7){
							
							if(trim($arData['TEMPLATE'])){
								$smsOb = new \Mlife\Smsservices\Sender();
								$smsOb->event = $arMakros['#EVENT_NAME#'];
								$smsOb->eventName = Loc::getMessage("MLIFE_SMSSERVICES_EVENTCODE_MSMS_BXEVENT");
								$smsOb->app = ($arData['PARAMS']['APPSMS']=='Y') ? true : false;
								$smsOb->sendSms($phone, $arData['TEMPLATE'],0,$sender);
								
								$smsOb->event = null;
								$smsOb->eventName = null;
								
								if($arData['PARAMS']['BREAK'] == "Y") $returnSendMail = false;
							}
							
							break;
						}
					}
				}
				
				
			}
			
			
		}

		
		//запрет отправки письма
		if(!$returnSendMail) return false;
		if($eventMessage['EMAIL_FROM'] == 'EMPTY@emptu.ru') return false;
	}
	
}