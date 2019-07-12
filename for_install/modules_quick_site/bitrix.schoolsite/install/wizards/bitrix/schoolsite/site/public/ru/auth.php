<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (strlen($backurl)>0) LocalRedirect($backurl);
$APPLICATION->SetTitle("Авторизация");
?>
<p class="notetext"><font >Вы зарегистрированы и успешно авторизовались.</font></p>
<p><a href="<?=SITE_DIR?>">Вернуться на главную страницу</a></p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>