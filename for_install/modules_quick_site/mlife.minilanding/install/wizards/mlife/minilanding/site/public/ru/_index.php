<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle('#SITE_TEXT1#');

?><?$APPLICATION->IncludeComponent("mlife:mlife.minilanding.form.ajax", "zvonok", array(
	"KEY" => "3jdfsdgdfgj5h3k4j5h89",
	"FORMID" => "1",
	"FIELD_SHOW" => array(
		0 => "name",
		1 => "phone",
	),
	"FIELD_REQ" => array(
		0 => "name",
		1 => "phone",
	),
	"FIELD_SHOW_HIDDEN" => array(
	),
	"NOTICE_ADMIN" => "Y",
	"NOTICE_EMAIL" => "#EVENT1#",
	"NOTICE_EMAIL_EMAIL" => "#SITE_EMAIL#",
	"NOTICE_ADMIN_SMS" => "N",
	"IBL_ADMIN" => "N",
	"MESS_OK" => "Ваш запрос принят, наш менеджер свяжется с вами в ближайшее время.",
	"F_NAME" => "Заказать звонок",
	"F_DESC" => "Заполните форму и мы вам обязательно перезвоним",
	"CLASS_LINK" => ".zvonok a"
	),
	false
);?> 
<?$APPLICATION->IncludeComponent("mlife:mlife.minilanding.form.ajax", "loadajax", array(
	"KEY" => "3jdf45lhgj5h3k4j5h89",
	"FORMID" => "3",
	"FIELD_SHOW" => array(
		0 => "name",
		1 => "phone",
		2 => "email",
	),
	"FIELD_REQ" => array(
		0 => "name",
		1 => "phone",
	),
	"FIELD_SHOW_HIDDEN" => array(
		0 => "addfield1",
	),
	"NOTICE_ADMIN" => "Y",
	"NOTICE_EMAIL" => "#EVENT2#",
	"NOTICE_EMAIL_EMAIL" => "#SITE_EMAIL#",
	"NOTICE_ADMIN_SMS" => "N",
	"IBL_ADMIN" => "N",
	"MESS_OK" => "Ваша заявка принята, наш менеджер свяжется с вами в ближайшее время.",
	"F_NAME" => "Оставьте заявку",
	"F_DESC" => "на бесплатную консультацию и наш специалист свяжется с вами",
	"CLASS_LINK" => ".formShare"
	),
	false
);?> 
<?$APPLICATION->IncludeComponent("mlife:mlife.minilanding.form.ajax", "vopros", array(
	"KEY" => "3jdf45hhgj5h3k4j5h89",
	"FORMID" => "2",
	"FIELD_SHOW" => array(
		0 => "name",
		1 => "phone",
		2 => "email",
		3 => "mess",
	),
	"FIELD_REQ" => array(
		0 => "name",
		1 => "phone",
	),
	"FIELD_SHOW_HIDDEN" => array(
	),
	"NOTICE_ADMIN" => "Y",
	"NOTICE_EMAIL" => "#EVENT3#",
	"NOTICE_EMAIL_EMAIL" => "#SITE_EMAIL#",
	"NOTICE_ADMIN_SMS" => "N",
	"IBL_ADMIN" => "N",
	"MESS_OK" => "Ваш вопрос принят, наш менеджер свяжется с вами в ближайшее время.",
	"F_NAME" => "Задать вопрос",
	"F_DESC" => "Заполните форму и мы вам обязательно перезвоним",
	"CLASS_LINK" => ".formlink a"
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>