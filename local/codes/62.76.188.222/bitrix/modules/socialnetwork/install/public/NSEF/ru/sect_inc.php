<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:socialnetwork.events_dyn", ".default", Array(
	"PATH_TO_USER"	=>	"#SEF_FOLDER#index.php?page=user&user_id=#user_id#",
	"PATH_TO_GROUP"	=>	"#SEF_FOLDER#index.php?page=group&group_id=#group_id#",
	"PATH_TO_MESSAGE_FORM"	=>	"#SEF_FOLDER#index.php?page=message_form&user_id=#user_id#",
	"PATH_TO_MESSAGE_FORM_MESS"	=>	"#SEF_FOLDER#index.php?page=message_form_mess&user_id=#user_id#&message_id=#message_id#",
	"PATH_TO_MESSAGES_CHAT"	=>	"#SEF_FOLDER#index.php?page=messages_chat&user_id=#user_id#",
	"PATH_TO_SMILE"	=>	"/bitrix/images/socialnetwork/smile/",
	"MESSAGE_VAR"	=>	"message_id",
	"PAGE_VAR"	=>	"page",
	"USER_VAR"	=>	"user_id",
	"NAME_TEMPLATE" => "",
	)
);
?>