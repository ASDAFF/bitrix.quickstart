<?
define('NEED_AUTH', true);
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

if (isset($_REQUEST['backurl']) && strlen($_REQUEST['backurl'])>0)
	LocalRedirect($backurl);

$APPLICATION->SetTitle('Authorization');

?><p>You have successfully signed up and logged in to the site!</p>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
