<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle(GetMessage('CFT_BTN_FEEDBACK'));
?>


<div id="post-14" class="post-14 page type-page status-publish hentry page">
<div class="row">
<div class="span12">

<?$APPLICATION->IncludeComponent("likeit:main.feedback", "contacts", array(
	"USE_CAPTCHA" => "Y",
	"OK_TEXT" => "�������, ���� ��������� �������.",
	"EMAIL_TO" => "demo@likeit.pro",
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