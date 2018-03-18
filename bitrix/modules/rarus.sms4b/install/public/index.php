<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Презентация SMS");
?>В данном разделе демонстрируются возможности модуля SMS4B
<div style = "margin:10px">
<table cellpadding="5" border = 0>
	<tr><td><a href = "subscribe_demo.php">Компонент подписки</a></td></tr>
	<tr><td><a href = "subscr_edit.php">Компонент редактирования почтовой подписки</a></td></tr>
	<tr><td><a href = "subscr_edit_sms.php">Компонент редактирования sms подписки</a></td></tr>
	<tr><td><a href = "minisub.php">Компонент мини-подписки</a></td></tr>
	<tr><td><a href = "corportal.php">Компонент отправки SMS для корпортала</a></td></tr>
</table>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>