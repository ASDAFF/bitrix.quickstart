<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
$wizard =& $this->GetWizard();
if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("mlife.asz"))
	return;

if(COption::GetOptionString("mlife.asz", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA){
	return;
}

WizardServices::IncludeServiceLang("status.php", "ru");

$baseCheck = \Mlife\Asz\OrderStatusTable::getList(
	array(
		'select' => array('ID','CODE'),
		'filter' => array("SITEID"=>WIZARD_SITE_ID),
		'limit' => 100,
	)
);
if(!$baseCheck->Fetch()){
	$arStatus = Array(
		"CODE" => "N",
		"SITEID" => WIZARD_SITE_ID,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATUS_N"),
		"ACTIVE" => "Y",
		"DESC" => "",
	);
	$res = \Mlife\Asz\OrderStatusTable::add($arStatus);
	$statusId = $res->getId();
	//добавляем тип
	$rsET = CEventType::GetByID('MLIFE_ASZ_STATUS_'.$statusId,'ru')->Fetch();
	if(!$rsET){
	$et = new CEventType;
	$evTypeId = $et->Add(array(
	"LID" => 'ru',
	"EVENT_NAME"    => 'MLIFE_ASZ_STATUS_'.$statusId,
	"NAME"          => GetMessage('MLIFE_ASZ_WZ_STATUS_N_1'),
	"DESCRIPTION"   => GetMessage('MLIFE_ASZ_WZ_STATUS_N_2'),
	));
	}
	//добавляем шаблоны
	$eventMess = array(
				"ACTIVE" => "Y", "EVENT_NAME" => 'MLIFE_ASZ_STATUS_'.$statusId, "LID" => WIZARD_SITE_ID, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USERPROP_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_ASZ_WZ_STATUS_N_3'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_ASZ_WZ_STATUS_N_4'),
			);
	$emess = new CEventMessage;
	$id = $emess->Add($eventMess);
	\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status1", $id, WIZARD_SITE_ID);
	\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status4", $id, WIZARD_SITE_ID);
	
	$arStatus = Array(
		"CODE" => "F",
		"SITEID" => WIZARD_SITE_ID,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATUS_F"),
		"ACTIVE" => "Y",
		"DESC" => "",
	);
	$res = \Mlife\Asz\OrderStatusTable::add($arStatus);
	$statusId = $res->getId();
	//добавляем тип
	$rsET = CEventType::GetByID('MLIFE_ASZ_STATUS_'.$statusId,'ru')->Fetch();
	if(!$rsET){
	$et = new CEventType;
	$evTypeId = $et->Add(array(
	"LID" => 'ru',
	"EVENT_NAME"    => 'MLIFE_ASZ_STATUS_'.$statusId,
	"NAME"          => GetMessage('MLIFE_ASZ_WZ_STATUS_F_1'),
	"DESCRIPTION"   => GetMessage('MLIFE_ASZ_WZ_STATUS_N_2'),
	));
	}
	//добавляем шаблоны
	$eventMess = array(
				"ACTIVE" => "Y", "EVENT_NAME" => 'MLIFE_ASZ_STATUS_'.$statusId, "LID" => WIZARD_SITE_ID, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USERPROP_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_ASZ_WZ_STATUS_F_3'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_ASZ_WZ_STATUS_F_4'),
			);
	$emess = new CEventMessage;
	$id = $emess->Add($eventMess);
	\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status2", $id, WIZARD_SITE_ID);
	
	$arStatus = Array(
		"CODE" => "T",
		"SITEID" => WIZARD_SITE_ID,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATUS_T"),
		"ACTIVE" => "Y",
		"DESC" => "",
	);
	$res = \Mlife\Asz\OrderStatusTable::add($arStatus);
	$statusId = $res->getId();
	//добавляем тип
	$rsET = CEventType::GetByID('MLIFE_ASZ_STATUS_'.$statusId,'ru')->Fetch();
	if(!$rsET){
	$et = new CEventType;
	$evTypeId = $et->Add(array(
	"LID" => 'ru',
	"EVENT_NAME"    => 'MLIFE_ASZ_STATUS_'.$statusId,
	"NAME"          => GetMessage('MLIFE_ASZ_WZ_STATUS_T_1'),
	"DESCRIPTION"   => GetMessage('MLIFE_ASZ_WZ_STATUS_N_2'),
	));
	}
	//добавляем шаблоны
	$eventMess = array(
				"ACTIVE" => "Y", "EVENT_NAME" => 'MLIFE_ASZ_STATUS_'.$statusId, "LID" => WIZARD_SITE_ID, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USERPROP_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_ASZ_WZ_STATUS_T_3'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_ASZ_WZ_STATUS_T_4'),
			);
	$emess = new CEventMessage;
	$id = $emess->Add($eventMess);
	
	$arStatus = Array(
		"CODE" => "O",
		"SITEID" => WIZARD_SITE_ID,
		"NAME" => GetMessage("MLIFE_ASZ_WZ_STATUS_O"),
		"ACTIVE" => "Y",
		"DESC" => "",
	);
	$res = \Mlife\Asz\OrderStatusTable::add($arStatus);
	$statusId = $res->getId();
	//добавляем тип
	$rsET = CEventType::GetByID('MLIFE_ASZ_STATUS_'.$statusId,'ru')->Fetch();
	if(!$rsET){
	$et = new CEventType;
	$evTypeId = $et->Add(array(
	"LID" => 'ru',
	"EVENT_NAME"    => 'MLIFE_ASZ_STATUS_'.$statusId,
	"NAME"          => GetMessage('MLIFE_ASZ_WZ_STATUS_O_1'),
	"DESCRIPTION"   => GetMessage('MLIFE_ASZ_WZ_STATUS_N_2'),
	));
	}
	//добавляем шаблоны
	$eventMess = array(
				"ACTIVE" => "Y", "EVENT_NAME" => 'MLIFE_ASZ_STATUS_'.$statusId, "LID" => WIZARD_SITE_ID, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USERPROP_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_ASZ_WZ_STATUS_O_3'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_ASZ_WZ_STATUS_O_4'),
			);
	$emess = new CEventMessage;
	$id = $emess->Add($eventMess);
	\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status3", $id, WIZARD_SITE_ID);
	
	//новый заказ
	//добавляем тип
	$rsET = CEventType::GetByID('MLIFE_ASZ_ORDER','ru')->Fetch();
	if(!$rsET){
	$et = new CEventType;
	$evTypeId = $et->Add(array(
	"LID" => 'ru',
	"EVENT_NAME"    => 'MLIFE_ASZ_ORDER',
	"NAME"          => GetMessage('MLIFE_ASZ_WZ_ORDER'),
	"DESCRIPTION"   => GetMessage('MLIFE_ASZ_WZ_STATUS_N_2'),
	));
	}
	//добавляем шаблоны
	$eventMess = array(
				"ACTIVE" => "Y", "EVENT_NAME" => 'MLIFE_ASZ_ORDER', "LID" => WIZARD_SITE_ID, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#USERPROP_EMAIL#", 
				"SUBJECT" => GetMessage('MLIFE_ASZ_WZ_ORDER_2'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_ASZ_WZ_ORDER_3'),
			);
	$emess = new CEventMessage;
	$id = $emess->Add($eventMess);
	$eventMess = array(
				"ACTIVE" => "Y", "EVENT_NAME" => 'MLIFE_ASZ_ORDER', "LID" => WIZARD_SITE_ID, 
				"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#", "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#", 
				"SUBJECT" => GetMessage('MLIFE_ASZ_WZ_ORDER_4'), "BODY_TYPE" => "text", "MESSAGE" => GetMessage('MLIFE_ASZ_WZ_ORDER_5'),
			);
	$emess = new CEventMessage;
	$id = $emess->Add($eventMess);
	
}