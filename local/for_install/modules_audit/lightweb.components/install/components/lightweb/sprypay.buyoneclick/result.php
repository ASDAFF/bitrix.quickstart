<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (!CModule::IncludeModule("lw_components")) return false;
if (!CLWComponents::ConnectExtension('sprypay', false)) return false;
$SP=new sprypay;
$obOption = new CLWOption();
$arOptions = $SP->GetOptions();
$arPaymentOptions = unserialize($obOption->Get($arOptions['PAYMENT_OPTIONS']));
$smsApiKey=$obOption->Get($arOptions['SMS']['SMS_RU_API_KEY']);
$account = array('ACCOUNT'=>array('LOGIN'=>$arPaymentOptions['LOGIN'], 'PAYMENT_SECRET_KEY'=>$arPaymentOptions['SECRET_KEY']));
$SP->SetOptions($account);
if ($SP->CheckPayment()){ //Подтвержденно схождения сигнатур заказа
	if ($SP->CheckOrder()){ //Заказ помечен как оплаченный, сформирован пароль для получения заказа
		//Отправляем уведомления об оплаченном заказе
		$arOptions=$SP->GetOptions();
		
		//Готовим Email уведомление
		$EmailFields = array(
			"ORDER_ID" => 			$arOptions['PAYMENT']['InvId'],
			"ORDER_SUM" => 			$arOptions['PAYMENT']['OutSum'],
			"ORDER_DATE" => 		date('d.m.Y', $arOptions['ORDERS']['DATE']),
			"ORDER_PASSWORD" => 	$arOptions['ORDERS']['PASSWORD'],
			"PRODUCT_NAME" => 		$arOptions['PRODUCTS']['NAME'],
			"PRODUCT_ID" => 		$arOptions['PRODUCTS']['ID'],
			"PRODUCT_DESCRIPTION" =>$arOptions['PRODUCTS']['PRODUCTS_DESCRIPTION'],
			
			"CUSTOMER_NAME" => 		$arOptions['CUSTOMER']['NAME'],
			"CUSTOMER_PHONE" => 	$arOptions['CUSTOMER']['PHONE'],
			"CUSTOMER_EMAIL" => 	$arOptions['CUSTOMER']['EMAIL'],
			"CUSTOMER_MESSAGE" => 	$arOptions['CUSTOMER']['MESSAGE'],
			
			"EMAIL_ADMINISTRATOR" =>$arOptions['EMAIL']['EMAIL_ADMINISTRATOR'],
		);
		
		//Отправляем если указанны соотвествующие шаблоны
		if (!empty($arOptions['EMAIL']['EMAIL_TEMPLATES_ADMINISTRATOR'])) { //Для администратора
			foreach($arOptions['EMAIL']['EMAIL_TEMPLATES_ADMINISTRATOR'] as $template){
				$template=IntVal($template);
				if ($template > 0) {
					CEvent::Send($arOptions['EMAIL']["EVENT_NAME"], SITE_ID, $EmailFields, "N", $template);
				}
			}
		}
		if (!empty($arOptions['EMAIL']['EMAIL_TEMPLATES_CUSTOMER'])) { //Для заказчика
			foreach($arOptions['EMAIL']['EMAIL_TEMPLATES_CUSTOMER'] as $template){
				$template=IntVal($template);
				if ($template > 0) {
					CEvent::Send($arOptions['EMAIL']["EVENT_NAME"], SITE_ID, $EmailFields, "N", $template);
				}
			}
		}
		
		//Отправляем СМС Уведомления
		if ($arOptions['SMS']['SMS_RU_STATE']!='' and $arOptions['SMS']['SMS_RU_STATE']!='DISABLE'){
			$SMSTesting=($arOptions['SMS']['SMS_RU_STATE']=='TESTING'?1:0);
			$SMSText=str_replace(array('#ORDER_NAME#','#ORDER_ID#','#PASSWORD#'), array($arOptions['PRODUCTS']['NAME'], $arOptions['PAYMENT']['InvId'], $arOptions['ORDERS']['PASSWORD']), $arOptions['SMS']['SMS_RU_TEMPLATE_SUCCESS']);
			$obSMSRU=CLWComponents::ConnectExtension('smsru');
			$obSMSRU->login(array('api_id'=>$smsApiKey));
			$obSMSRU->send(array('to'=>$arOptions['SMS']['SMS_RU_ADMIN_NUMBER'], 'text'=>$SMSText, 'from'=>$arOptions['SMS']['SMS_RU_FROM'], 'test'=>$SMSTesting));
			$obSMSRU->send(array('to'=>$arOptions['CUSTOMER']['PHONE'], 'text'=>$SMSText, 'from'=>$arOptions['SMS']['SMS_RU_FROM'], 'test'=>$SMSTesting));
		}
		exit("ok");
	}
}
?>