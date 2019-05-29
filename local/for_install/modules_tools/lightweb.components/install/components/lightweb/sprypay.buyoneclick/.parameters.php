<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$LID = ($_REQUEST["site"] <> ''? $_REQUEST["site"] : ($_REQUEST["src_site"] <> ''? $_REQUEST["src_site"] : false));
$arEvent = array();

//Проверяем есть ли соответствующие компоненту почтовые события
//Пробуем получить необходимый тип почтового события
$EventType=new CEventType;
$resEventType = $EventType->GetByID("LW_SPRYPAY_BUYONECLICK", LANGUAGE_ID);
$arEventType = $resEventType->Fetch();
if (empty($arEventType)){ 
	$EventType->Add(array(//при отсутствии добавляем
			"LID" => LANGUAGE_ID, 
			"EVENT_NAME" =>"LW_SPRYPAY_BUYONECLICK",
			"NAME"=> GetMessage("SP_BOC_EVENT_TYPE_NAME"),
			"DESCRIPTION"=>
				"#ORDER_ID# - ".GetMessage("SP_BOC_ORDER_ID")."\n".
				"#ORDER_SUM# - ".GetMessage("SP_BOC_ORDER_SUM")."\n".
				"#ORDER_DATE# - ".GetMessage("SP_BOC_ORDER_DATE")."\n".
				"#ORDER_PASSWORD# - ".GetMessage("SP_BOC_ORDER_PASSWORD")."\n".
				"#PRODUCT_NAME# - ".GetMessage("SP_BOC_PRODUCT_NAME")."\n".
				"#PRODUCT_ID# - ".GetMessage("SP_BOC_PRODUCT_ID")."\n".
				"#PRODUCT_DESCRIPTION# - ".GetMessage("SP_BOC_PRODUCT_DESCRIPTION")."\n".
				
				"#CUSTOMER_NAME# - ".GetMessage("SP_BOC_NAME")."\n".
				"#CUSTOMER_PHONE# - ".GetMessage("SP_BOC_PHONE")."\n".
				"#CUSTOMER_EMAIL# - ".GetMessage("SP_BOC_EMAIL")."\n".
				"#CUSTOMER_MESSAGE# - ".GetMessage("SP_BOC_MESSAGE")."\n".
				
				"#EMAIL_ADMINISTRATOR# - ".GetMessage("SP_BOC_EMAIL_ADMINISTRATOR")."\n".
				
				"#DEFAULT_EMAIL_FROM# - ".GetMessage("SP_BOC_DEFAULT_EMAIL_FROM")."\n".
				"#SITE_NAME# - ".GetMessage("SP_BOC_SITE_NAME")."\n".
				"#SERVER_NAME# - ".GetMessage("SP_BOC_SERVER_NAME")
	));
} else {
	//Пробуем получить список шаблонов почтового события
	$EventMessage = new CEventMessage;
	$arFilter = array("TYPE_ID" => "LW_SPRYPAY_BUYONECLICK", "ACTIVE" => "Y");
	if($LID !== false){$arFilter["LID"] = $LID;}
	$arEvent = array();
	$resEventMessage = $EventMessage->GetList($by="ID", $order="DESC", $arFilter);
	while($arEventMessage = $resEventMessage->GetNext()){
		$arEvent[$arEventMessage["ID"]] = "[".$arEventMessage["ID"]."] ".$arEventMessage["SUBJECT"];
	}
}

if (!CModule::IncludeModule("iblock")) return;
if (!CModule::IncludeModule("lightweb.components")) return; 
	$CLWOption = new CLWOption();

//Получаем все активные типы ИБ
$dbIBlockType = CIBlockType::GetList(
	array("sort" => "asc"),
	array("ACTIVE" => "Y")
);
$arIBlockTypeList[0]='--';
while ($arIBlockType = $dbIBlockType->Fetch()){
	if ($arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType["ID"], LANGUAGE_ID)){
		$arIBlockTypeList[$arIBlockType["ID"]] = "[".$arIBlockType["ID"]."] ".$arIBlockTypeLang["NAME"];
	}
}

//Массив возможных полей
$arFields=Array("NAME" => GetMessage("SP_BOC_NAME"),
				"PHONE" => GetMessage("SP_BOC_PHONE"), 
				"EMAIL" => GetMessage("SP_BOC_EMAIL"), 
				"MESSAGE" => GetMessage("SP_BOC_MESSAGE"));

//Получаем дирректорию компонента
$component_dir=substr(__DIR__, strpos(__DIR__, "/bitrix/"), strlen(__DIR__));

$arComponentParameters = array(
	"GROUPS" => array(
		"STORAGE" => array(
			"NAME" => GetMessage("SP_BOC_GROUP_STORAGE_OPTION"),
			"SORT"	=> "100"
		),
		
		"STORAGE_PRODUCTS" => array(
			"NAME" => GetMessage("SP_BOC_GROUP_STORAGE_PRODUCTS_OPTION"),
			"SORT"	=> "150"
		),
		
		"STORAGE_ORDER" => array(
			"NAME" => GetMessage("SP_BOC_GROUP_STORAGE_ORDER_OPTION"),
			"SORT"	=> "200"
		),
		"ORDERING" => array(
			"NAME" => GetMessage("SP_BOC_GROUP_ORDERING_OPTION"),
			"SORT"	=> "300"
		),
		"NOTIFICATION" => array(
			"NAME" => GetMessage("SP_BOC_GROUP_NOTIFICATION_OPTION"),
			"SORT"	=> "400"
		),
		"SMS_RU" => array(
			"NAME" => GetMessage("SP_BOC_SMS_RU"),
			"SORT" => "500"
		),
	),
	
	"PARAMETERS" => array(
		
		//Настройки магазина

		"PAYMENT_TEST_MODE" => array(
			"PARENT" => "STORAGE",
			"NAME" => '* '.GetMessage("SP_BOC_PAYMENT_TEST_MODE"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array('Y'=>GetMessage("SP_BOC_STATE_ACTIVE"),'N'=>GetMessage("SP_BOC_STATE_DISABLED")),
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"SORT"=>"10"
		),
		
		"PAYMENT_CURRENCY" => array(
			"PARENT" => "STORAGE",
			"NAME" => '* '.GetMessage("SP_BOC_PAYMENT_CURRENCY"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => array('RUR'=>GetMessage("SP_BOC_PAYMENT_CURRENCY_RUR"),'USD'=>GetMessage("SP_BOC_PAYMENT_CURRENCY_USD"),'EUR'=>GetMessage("SP_BOC_PAYMENT_CURRENCY_EUR"),'UAH'=>GetMessage("SP_BOC_PAYMENT_CURRENCY_UAH")),
			"DEFAULT" => "RUR",
			"REFRESH" => "N",
			"SORT"=>"10"
		),
		
		"PAYMENT_OPTIONS" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_PAYMENT_OPTIONS"), 
			"TYPE" => "CUSTOM",
			"PARENT" => "STORAGE",
			"JS_FILE" => $component_dir."/js/settings.js?v=".time(),
			'JS_EVENT' => 'SetPaymentOptions',
			'JS_DATA' => array(
				'LANG'=>array(
					'STORAGE_LOGIN'=>GetMessage("SP_BOC_STORAGE_LOGIN"),
					'SECRET_KEY'=>GetMessage("SP_BOC_STORAGE_SECRET_KEY"),
					'HIDDEN'=>GetMessage("SP_BOC_HIDDEN"),
					'SAVE_BUTTON'=> GetMessage("SP_BOC_SAVE_BUTTON"),
					'SAVE_MESSAGE'=> (!empty($CLWOption->SP_BOC)?GetMessage("SP_BOC_SAVE_MESSAGE"):''),
					'ONSUCCESS'=>GetMessage("SP_BOC_ONSUCCESS"),
					'ONFAILURE'=>GetMessage("SP_BOC_ONFAILURE"),
				),
				'EXECUTE_FILE'=>$component_dir."/save_option.php"
			),
			"SORT"=>"30"
		),
		
		//Настройка каталога товаров
		"IBLOCK_PRODUCTS_TYPE_ID" => array(
			"PARENT" => "STORAGE_PRODUCTS",
			"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_PRODUCTS_IBLOCK_TYPE_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arIBlockTypeList,
			"REFRESH" => "Y",
			"SORT"=>"10"
		),
		
		//Настройка хранения заказов
		"IBLOCK_TYPE_ID" => array(
			"PARENT" => "STORAGE_ORDER",
			"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_ORDER_IBLOCK_TYPE_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "N",
			"VALUES" => $arIBlockTypeList,
			"REFRESH" => "Y",
			"SORT"=>"10"
		),
		
		//Настройка окна оформления заказа
		"FORM_ID" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_FORM_ID"), 
			"TYPE" => "STRING",
			"DEFAULT" => time(), 
			"PARENT" => "ORDERING",
			"SORT"=>"10"
		),
		"FORM_NAME" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_FORM_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("LIGHTWEB_COMPONENTS_POKUPKA_PROGNOZA"),
			"PARENT" => "ORDERING",
			"SORT"=>"20"
		),
		"USED_FIELDS" => Array(
			"NAME" => GetMessage("SP_BOC_USED_FIELDS"), 
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"Y", 
			"VALUES" => $arFields,
			"DEFAULT"=>"", 
			"COLS"=>25, 
			"PARENT" => "ORDERING",
			"SORT"=>"30"
		),
		"REQUIRED_FIELDS" => Array(
			"NAME" => GetMessage("SP_BOC_REQUIRED_FIELDS"), 
			"TYPE"=>"LIST", 
			"MULTIPLE"=>"Y", 
			"VALUES" => $arFields,
			"DEFAULT"=>"", 
			"COLS"=>25, 
			"PARENT" => "ORDERING",
			"SORT"=>"40"
		),
		"BUTTON_NAME" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_BUTTON_NAME"), 
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("SP_BOC_DEFAULT_BUTTON_NAME"), 
			"PARENT" => "ORDERING",
			"SORT"=>"50"
		),
		
		//Настройка api для SMS.RU
		"SMS_RU_STATE" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_SMS_RU_STATE"),
			"TYPE"=>"LIST",
			"MULTIPLE"=>"N",
			"VALUES" => array(
				"ACTIVE" => GetMessage("SP_BOC_STATE_ACTIVE"),
				"DISABLED" => GetMessage("SP_BOC_STATE_DISABLED"),
				"TESTING" => GetMessage("SP_BOC_SMS_RU_STATE_TESTING")
			),
			"DEFAULT"=>"DISABLED",
			"REFRESH" => "Y",
			"PARENT" => "SMS_RU",
			"SORT"=>"10",
		),
		
		//Настройка уведомления о заказе
		"EMAIL_ADMINISTRATOR" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_EMAIL_ADMINISTRATOR"), 
			"TYPE" => "STRING",
			"DEFAULT" => htmlspecialcharsbx(COption::GetOptionString("main", "email_from")), 
			"PARENT" => "NOTIFICATION",
			"SORT"=>"10"
		),
		"EVENT_TEMPLATES_ADMINISTRATOR" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_EMAIL_TEMPLATES_FOR_ADMINISTRATOR"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "NOTIFICATION",
			"SORT"=>"20"
		),
		"EVENT_TEMPLATES_CUSTOMER" => Array(
			"NAME" => '* '.GetMessage("SP_BOC_EMAIL_TEMPLATES_FOR_CUSTOMER"), 
			"TYPE"=>"LIST", 
			"VALUES" => $arEvent,
			"DEFAULT"=>"", 
			"MULTIPLE"=>"Y", 
			"COLS"=>25, 
			"PARENT" => "NOTIFICATION",
			"SORT"=>"30"
		),

	)
);



//Обработка событий выбора данных

//КАТАЛОГ ТОВАРОВ

//Если указан тип ИБ выбираем все ИБ заданного типа
if ($arCurrentValues["IBLOCK_PRODUCTS_TYPE_ID"]){
	$dbIBlockID = CIBlock::GetList(
		array("sort" => "asc"),
		array("ACTIVE" => "Y", "TYPE"=>$arCurrentValues["IBLOCK_PRODUCTS_TYPE_ID"])
	);
	$arProductIBlockIDList[0]='--';
	while($arIBlockID = $dbIBlockID->Fetch()){
		$arProductIBlockIDList[$arIBlockID["ID"]] = "[".$arIBlockID["ID"]."] ".$arIBlockID["NAME"];
	}
			
	$arComponentParameters["PARAMETERS"]["PRODUCTS_IBLOCK_ID"]=array(
		"PARENT" => "STORAGE_PRODUCTS",
		"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_PRODUCTS_IBLOCK_ID"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arProductIBlockIDList,
		"REFRESH" => "Y",
		"SORT"=>"20"
	);
}

//Если указан ИБ, выбираем все свойства заданного ИБ
if ($arCurrentValues["PRODUCTS_IBLOCK_ID"]){
	$arProductPropertyList=array();
	$resProperties = CIBlockProperty::GetList(
		array("sort"=>"asc", "name"=>"asc"), 
		array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["PRODUCTS_IBLOCK_ID"])
	);
	$arProductPropertyList[0]='--';
	while ($arProperty = $resProperties->GetNext()){
		$arProductPropertyList[$arProperty['CODE']]=$arProperty['NAME'];
	}
		
	$arComponentParameters["PARAMETERS"]["PRODUCTS_COST"]=array(
		"PARENT" => "STORAGE_PRODUCTS",
		"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_PRODUCTS_COST"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arProductPropertyList,
		"REFRESH" => "N",
		"SORT"=>"30"
	);
	$arComponentParameters["PARAMETERS"]["PRODUCTS_DESCRIPTION"]=array(
		"PARENT" => "STORAGE_PRODUCTS",
		"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_PRODUCTS_DESCRIPTION"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arProductPropertyList,
		"REFRESH" => "N",
		"SORT"=>"30"
	);
}



//ХРАНЕНИЕ ЗАКАЗОВ
	
//Если указан тип ИБ выбираем все ИБ заданного типа
if ($arCurrentValues["IBLOCK_TYPE_ID"]){
	$dbIBlockID = CIBlock::GetList(
		array("sort" => "asc"),
		array("ACTIVE" => "Y", "TYPE"=>$arCurrentValues["IBLOCK_TYPE_ID"])
	);
	$arIBlockIDList[0]='--';
	while($arIBlockID = $dbIBlockID->Fetch()){
		$arIBlockIDList[$arIBlockID["ID"]] = "[".$arIBlockID["ID"]."] ".$arIBlockID["NAME"];
	}
			
	$arComponentParameters["PARAMETERS"]["IBLOCK_ID"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_ORDER_IBLOCK_ID"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arIBlockIDList,
		"REFRESH" => "Y",
		"SORT"=>"20"
	);
}

//Если указан ИБ, выбираем все свойства заданного ИБ
if ($arCurrentValues["IBLOCK_ID"]){
	$resProperties = CIBlockProperty::GetList(
		array("sort"=>"asc", "name"=>"asc"), 
		array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"])
	);
	$arPropertyList[0]='--';
	while ($arProperty = $resProperties->GetNext()){
		$arPropertyList[$arProperty['CODE']]=$arProperty['NAME'];
	}
		
	$arComponentParameters["PARAMETERS"]["PRODUCT_ID"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_ORDER_PRODUCT_ID"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"30"
	);
	
	$arComponentParameters["PARAMETERS"]["ORDER_SUM"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_PROPERTY_ORDER_SUM"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"40"
	);

	$arComponentParameters["PARAMETERS"]["PAID_PROP_NAME"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_PAID_PROP_NAME"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"50"
	);
	
	$arComponentParameters["PARAMETERS"]["CUSTOMER_PROP_NAME"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_CUSTOMER_PROP_NAME"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"60"
	);

	$arComponentParameters["PARAMETERS"]["CUSTOMER_PHONE_PROP_NAME"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_CUSTOMER_PHONE_PROP_NAME"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"70"
	);

	$arComponentParameters["PARAMETERS"]["CUSTOMER_EMAIL_PROP_NAME"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_CUSTOMER_EMAIL_PROP_NAME"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"80"
	);
	
	$arComponentParameters["PARAMETERS"]["CUSTOMER_MESSAGE_PROP_NAME"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_CUSTOMER_MESSAGE_PROP_NAME"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"80"
	);

	$arComponentParameters["PARAMETERS"]["ORDER_PASSWORD_PROP_NAME"]=array(
		"PARENT" => "STORAGE_ORDER",
		"NAME" => '* '.GetMessage("SP_BOC_ORDER_PASSWORD_PROP_NAME"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "N",
		"VALUES" => $arPropertyList,
		"REFRESH" => "N",
		"SORT"=>"90"
	);
}



if ($arCurrentValues["SMS_RU_STATE"] != 'DISABLED' and $arCurrentValues["SMS_RU_STATE"] != '') {
	
	$arComponentParameters["PARAMETERS"]["SMS_RU_API_KEY"] = array(
		"NAME" => '* '.GetMessage("SP_BOC_SMS_RU_API_KEY"), 
		"TYPE" => "CUSTOM",
		"PARENT" => "SMS_RU",
		"JS_FILE" => $component_dir."/js/settings.js?v=".time(),
		'JS_EVENT' => 'SetSMSOptions',
		'JS_DATA' => array(
			'LANG'=>array(
				'HIDDEN'=>GetMessage("SP_BOC_HIDDEN"),
				'SAVE_BUTTON'=> GetMessage("SP_BOC_SAVE_BUTTON"),
				'SAVE_MESSAGE'=> (!empty($CLWOption->SMS_API_KEY)?GetMessage("SP_BOC_SAVE_MESSAGE"):''),
				'ONSUCCESS'=>GetMessage("SP_BOC_ONSUCCESS"),
				'ONFAILURE'=>GetMessage("SP_BOC_ONFAILURE"),
			),
			'EXECUTE_FILE'=>$component_dir."/save_option.php"
		),
		"SORT"=>"30"
	);
	
	$arComponentParameters["PARAMETERS"]["SMS_RU_FROM"]=array(
		"NAME" => GetMessage("SP_BOC_SMS_RU_FROM"),
		"TYPE" => "STRING",
		"DEFAULT" => "LightWeb",
		"PARENT" => "SMS_RU",
		"SORT"=>"50",
	);
	$arComponentParameters["PARAMETERS"]["SMS_RU_ADMIN_NUMBER"]=array(
		"NAME" => GetMessage("SP_BOC_SMS_RU_ADMIN_NUMBER"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"PARENT" => "SMS_RU",
		"SORT"=>"60",
	);
	$arComponentParameters["PARAMETERS"]["SMS_RU_TEMPLATE_SUCCESS"]=array(
		"NAME" => GetMessage("SP_BOC_SMS_RU_TEMPLATE_SUCCESS"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("SP_BOC_SMS_RU_TEMPLATE_SUCCESS_DEFAULT"),
		"PARENT" => "SMS_RU",
		"SORT"=>"80",
	);
}

?>