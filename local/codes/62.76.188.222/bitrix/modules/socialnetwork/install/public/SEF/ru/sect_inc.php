<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:socialnetwork.events_dyn", ".default", Array(
	"PATH_TO_USER"	=>	"#SEF_FOLDER#user/#user_id#/",
	"PATH_TO_GROUP"	=>	"#SEF_FOLDER#group/#group_id#/",
	"PATH_TO_MESSAGE_FORM"	=>	"#SEF_FOLDER#messages/form/#user_id#/",
	"PATH_TO_MESSAGE_FORM_MESS"	=>	"#SEF_FOLDER#messages/form/#user_id#/#message_id#/",
	"PATH_TO_MESSAGES_CHAT"	=>	"#SEF_FOLDER#messages/chat/#user_id#/",
	"PATH_TO_SMILE"	=>	"/bitrix/images/socialnetwork/smile/",
	"MESSAGE_VAR"	=>	"message_id",
	"PAGE_VAR"	=>	"page",
	"USER_VAR"	=>	"user_id",
	"NAME_TEMPLATE" => "",
	)
);
?>