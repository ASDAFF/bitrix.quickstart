<?
/**
 * Copyright (c) 3/12/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage('CFT_BTN_FEEDBACK'));
?>


<div id="post-14" class="post-14 page type-page status-publish hentry page">
<div class="row">
<div class="span12">

<?$APPLICATION->IncludeComponent("site:main.feedback", "contacts", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "Спасибо, ваше сообщение принято.",
	"EMAIL_TO" => "demo@site.pro",
	"REQUIRED_FIELDS" => array(
	),
	"EVENT_MESSAGE_ID" => array(
		0 => "7",
	)
	),
	false
);?>

</div>
</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>