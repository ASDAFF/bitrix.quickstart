<? if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") { require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); } ?>
<?$APPLICATION->IncludeComponent(
	"studiofact:feedback", 
	".default", 
	array(
		"IBLOCK_TYPE" => "services",
		"IBLOCK_ID" => "#FEEDBACK_FORM_ID#",
		"PARENT_ID" => "feedback_form",
		"EVENT_TYPE" => "PVKD_FEEDBACK_EVENT",
		"HEAD" => "Заказать звонок",
		"PATH" => SITE_DIR."include/feedback_form.php",
		"CHECK_EMAIL" => "Y",
		"CHECK_PHONE" => "Y",
		"CHECK_PHONE_EXP" => "^((8|\\+7)[\\- ]?)?(\\(?\\d{3}\\)?[\\- ]?)?[\\d\\- ]{6,10}\$",
		"VISIBLE" => array(
			0 => "LINK",
			1 => "USER_ID",
		)
	),
	false
);?>
<? if (strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") { require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php"); } ?>