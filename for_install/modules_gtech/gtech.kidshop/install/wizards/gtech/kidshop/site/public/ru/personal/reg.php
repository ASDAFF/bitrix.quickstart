<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0) 
	LocalRedirect($backurl);

$APPLICATION->SetTitle("Авторизация");
?><p>Вы зарегистрированы и успешно авторизовались.</p>
 
<p><a href="<?=SITE_DIR?>">Вернуться на главную страницу</a> | <a href="/catalog/">Продолжить покупки</a> | <a href="/personal/order/">Проверить мои заказы</a> | <a href="/personal/cart/">Перейти в корзину</a></p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>