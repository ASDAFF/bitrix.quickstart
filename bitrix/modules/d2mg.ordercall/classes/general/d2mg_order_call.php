<?
####################################
#   Company: Digital Dream         #
#   Developer: Andrey Sidorov      #
#   Site: http://www.d2mg.ru       #
#   E-mail: info@d2mg.ru           #
####################################
?>
<?
IncludeModuleLangFile(__FILE__);
?>
<?
class COrderCall{
    
	static function EventTypeCreate()
	//Добавление нового типа почтового события (ORDER_CALL) для русского и английского языков
	{
		$et = new CEventType;
		$ru_ets = CEventType::GetByID("ORDER_CALL","ru");
		if (!$ru_ets_el = $ru_ets->Fetch())
		{
			$et->Add(array(
				"LID"           => "ru",
				"EVENT_NAME"    => "ORDER_CALL",
				"NAME"          => GetMessage("EVENT_NAME_DESC"),
				"DESCRIPTION"   => "
#AUTHOR# - ".GetMessage('AUTHOR_DESC')."
#AUTHOR_PHONE# - ".GetMessage('AUTHOR_PHONE_DESC')."
#CALL_TIME_TEXT# - ".GetMessage('CALL_TIME_DESC')."
#EMAIL_TO# - ".GetMessage('EMAIL_DESC')."
				"
			));
		}
		$en_ets = CEventType::GetByID("ORDER_CALL","en");
		if (!$en_ets_el = $en_ets->Fetch())
		{
			$et->Add(array(
				"LID"           => "en",
				"EVENT_NAME"    => "ORDER_CALL",
				"NAME"          => "Order Call",
				"DESCRIPTION"   => "
#AUTHOR# - Message author
#AUTHOR_PHONE# - Author's phone number
#CALL_TIME_TEXT# - Call time
#EMAIL_TO# - Recipient's e-mail address
				"
			));
		}
	}
	
	
	static function EventMessageCreate()
	{
		$ems = CEventMessage::GetList($by="", $order="desc", Array(				
				"TYPE_ID"       => "ORDER_CALL",				
				"ACTIVE"        => "Y",
				"SUBJECT"       => GetMessage("EVENT_MESSAGE_SUBJECT_DESC"),
				"BODY_TYPE"     => "text",
			)
		);
		if (!$ems_el = $ems->Fetch())
		{
			//Получение масива идентификаторов сайта
			$arSites = array();
			$rsSites = CSite::GetList($by="sort", $order="desc", Array());
			while ($arSite = $rsSites->Fetch())
			{
				$arSites[] = $arSite['LID'];
			}
		
			$emess = new CEventMessage;
			$res = $emess->Add(Array(
					"ACTIVE" => "Y",
					"EVENT_NAME" => "ORDER_CALL",
					"LID" => $arSites,
					"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
					"EMAIL_TO" => "#EMAIL_TO#",					
					"SUBJECT" => GetMessage("EVENT_MESSAGE_SUBJECT_DESC"),
					"BODY_TYPE" => "text",
					"MESSAGE" => 
GetMessage("EVENT_MESSAGE_HEADER_DESC")." #SITE_NAME#
------------------------------------------

".GetMessage("EVENT_MESSAGE_ACT_DESC")."

".GetMessage("EVENT_MESSAGE_AUTHOR_DESC").": #AUTHOR#
".GetMessage("EVENT_MESSAGE_PHONE_DESC").": #AUTHOR_PHONE#

".GetMessage("EVENT_MESSAGE_CALLTIME_DESC").":
#CALL_TIME_TEXT#

".GetMessage("EVENT_MESSAGE_FOOTER_DESC").".
					"
				)
			);
			//Получение идентификатора созданного почтового шаблона для сохранения в настройках модуля.
			$ems = CEventMessage::GetList($by="", $order="desc", Array(				
					"TYPE_ID"       => "ORDER_CALL",				
					"ACTIVE"        => "Y",
					"SUBJECT"       => GetMessage("EVENT_MESSAGE_SUBJECT_DESC"),
					"BODY_TYPE"     => "text",
				)
			);
			if ($ems_el = $ems->Fetch())//Установка идентификатора почтового шаблона в настройках модуля
				COption::SetOptionString("d2mg.ordercall","event_mess_id",$ems_el['ID']);
		}
	}
	
	static function IblockCreate()
	{
		CModule::IncludeModule('iblock');
		
		//Проверка наличия необходимого типа инфоблока
		$db_iblock_type = CIBlockType::GetList(array(),array("ID"=>"orders_call"));
		if (!$ar_iblock_type = $db_iblock_type->Fetch())
		{
			$arFields = Array(
				'ID'=>'d2mg_orderscall',
				'SECTIONS'=>'Y',
				'IN_RSS'=>'N',
				'SORT'=>100,
				'LANG'=>Array(
					'ru'=>Array(
							'NAME'=>GetMessage("IBLOCK_TYPE_NAME_DESC"),
							'SECTION_NAME'=>GetMessage("IBLOCK_TYPE_SECTION_DESC"),
							'ELEMENT_NAME'=>GetMessage("IBLOCK_TYPE_ELEMENT_DESC")
						),
					'en'=>Array(
							'NAME'=>'Orders call list',
							'SECTION_NAME'=>'Sections',
							'ELEMENT_NAME'=>'Orders call'
						)
					)
			);
			
			$obBlocktype = new CIBlockType;
			
			if ($res = $obBlocktype->Add($arFields))
			//Если тип инфоблока успешно добавлен, добавляется инфоблок
			{
				//Получение масива идентификаторов сайта
				$arSites = array();
				$rsSites = CSite::GetList($by="sort", $order="desc", Array());
				while ($arSite = $rsSites->Fetch())
				{
					$arSites[] = $arSite['LID'];
				}				
				
				//Получение идентификатора типа инфоблока перед добавлением инфоблока
				$db_iblock_type = CIBlockType::GetByID("d2mg_orderscall");
				if ($ar_iblock_type = $db_iblock_type->Fetch())
					$iblock_type_id=$ar_iblock_type["ID"];
				
				$db_iblock = CIBlock::GetList(array(),array("CODE"=>"d2mg_ordercall"),true);
				if (!$ar_iblock = $db_iblock->Fetch())
				{
					//dump($ar_iblock);
					$ib = new CIBlock;
					$arFields = Array(
						"ACTIVE" => "Y",
						"IBLOCK_TYPE_ID" =>$iblock_type_id,
						"NAME" => GetMessage("IBLOCK_NAME_DESC"),
						"CODE" => "d2mg_ordercall",
						"SITE_ID" => $arSites,						
						"DESCRIPTION" => GetMessage("IBLOCK_DESC")
					);
					
					if ($ib_id = $ib->Add($arFields))
					{
						//Установка идентификатора инфоблока в настройках модуля
							COption::SetOptionString("d2mg.ordercall","iblock_id",$ib_id);
					}					
				}
			}
		}
	}
	
	static function Save(
	//Сохранение заказа в инфоблок
		$arFields = Array(	
			"AUTHOR", //Автор сообщения
			"AUTHOR_PHONE", //Номер телефона автора для звонка
			"CALL_TIME_TEXT" //Время, удобное для звонка
		)
	)
    {
		//Добавление инфоблока для сохранения заказов, если он отсутствует
		COrderCall::IblockCreate();
	
		// Получение идентификатора инфоблока из настроек модуля
		$iblock_id=COption::GetOptionString("d2mg.ordercall","iblock_id");
		
		$ib_elem = new CIBlockElement;
		
		$arLoadFields = Array(
			"IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
			"IBLOCK_ID"      => $iblock_id,			
			"NAME"           => $arFields['AUTHOR'],
			"ACTIVE"         => "Y", // активен
			"PREVIEW_TEXT"   => $arFields['AUTHOR_PHONE'],
			"DETAIL_TEXT"    => $arFields['CALL_TIME_TEXT'],
		);
		$res=$ib_elem->Add($arLoadFields);
	}
    static function SendEmail(
	//Отправка заказа звонка на почту
		$arFields = Array(	
			"AUTHOR", //Автор сообщения
			"AUTHOR_PHONE", //Номер телефона автора для звонка
			"CALL_TIME_TEXT" //Время, удобное для звонка
		)
	)
    {	
	
	
		//Добавление нового типа почтового события (ORDER_CALL) для русского и английского языков
		COrderCall::EventTypeCreate();
		//Добавление нового почтового шаблона для типа почтового события ORDER_CALL
		COrderCall::EventMessageCreate();
		
		
		//Логика функции

		$email_to=COption::GetOptionString("d2mg.ordercall","email");		
		if (strlen($email_to)==0)
			$email_to=COption::GetOptionString('main', 'email_from');		
		if (strlen($email_to)!=0)
		{			
			//Получение масива идентификаторов сайта
			$arSites = array();
			$rsSites = CSite::GetList($by="sort", $order="desc", Array());
			while ($arSite = $rsSites->Fetch())
			{
				$arSites[] = $arSite['LID'];
			}
			
			//Получение идентификатора почтового шаблона из настроек модуля
			$event_mess_id=COption::GetOptionString("d2mg.ordercall","event_mess_id");			
			//Получение имени типа почтового события для полученного идентификатора почтового шаблона			
			$ems = CEventMessage::GetByID($event_mess_id);
			
			if ($ems_el=$ems->Fetch())
				$event_name=$ems_el["EVENT_NAME"];
			//Установка email получателя перед отправкой сообщения
			$arFields["EMAIL_TO"]=$email_to;									
			
			CEvent::Send($event_name, $arSites, $arFields, "N", $event_mess_id);			
		}
			
    }
	
	static function SendSMS(
	//Отправка заказа звонка на мобильный телефон
		$arFields = Array(	
			"AUTHOR", //Автор сообщения
			"AUTHOR_PHONE", //Номер телефона автора для звонка
			"CALL_TIME_TEXT" //Время, удобное для звонка
		)		
	)
	{
		// Получение заничений login и api key из настроек модуля для дальнейшего использований класса LittleSMS
		$LSMS_login=COption::GetOptionString("d2mg.ordercall","LSMS_login");
		$LSMS_api_key=COption::GetOptionString("d2mg.ordercall","LSMS_api_key");		
		
		//Получение номера телефона из настроек модуля для отправки на него сообщений СМС
		$phone=COption::GetOptionString("d2mg.ordercall","phone");
		
		$message=
GetMessage("EVENT_MESSAGE_AUTHOR_DESC").": #AUTHOR#
".GetMessage("EVENT_MESSAGE_PHONE_DESC").": #AUTHOR_PHONE#
".GetMessage("EVENT_MESSAGE_CALLTIME_DESC").":
#CALL_TIME_TEXT#";

			//Заполняем почтовый шаблон данными для отправки
			$message=str_replace("#SITE_NAME#",$SITE_NAME,$message);
			$message=str_replace("#AUTHOR#",$arFields["AUTHOR"],$message);
			$message=str_replace("#AUTHOR_PHONE#",$arFields["AUTHOR_PHONE"],$message);
			$message=str_replace("#CALL_TIME_TEXT#",$arFields["CALL_TIME_TEXT"],$message);
		
		
		// Подключение класса LittleSMS для отправки СМС сообщений
		require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/d2mg.ordercall/classes/general/LittleSMS.class.php");
		
		$api = new LittleSMS($LSMS_login, $LSMS_api_key, false);
		
		// Получение имени отправителя
		$sender=COption::GetOptionString("d2mg.ordercall","LSMS_sender_name");
		
		// отправка СМС
		if (strlen(trim($sender," "))==0)// Если значение имени отправителя в настройках модуля пусто
		{
			$res = $api->sendSMS($phone, $message);			
		}
		else
		{		
			$res = $api->sendSMS($phone, $message, $sender);
		}
	}
	
	static function Order(
	$arFields = Array(	
			"AUTHOR", //Автор сообщения
			"AUTHOR_PHONE", //Номер телефона автора для звонка
			"CALL_TIME_TEXT" //Время, удобное для звонка
		)
	)
	{		
		if (COption::GetOptionString("d2mg.ordercall","email_use")=="Y")
		{
			COrderCall::SendEmail($arFields);			
		}
		if (COption::GetOptionString("d2mg.ordercall","phone_use")=="Y")
		{
			COrderCall::SendSMS($arFields);			
		}
		if (COption::GetOptionString("d2mg.ordercall","iblock_use")=="Y")
		{
			COrderCall::Save($arFields);			
		}
	}
}
?>