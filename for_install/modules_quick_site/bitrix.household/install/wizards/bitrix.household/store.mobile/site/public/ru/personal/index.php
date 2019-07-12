<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?><p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию</p>
<h2>Заказы</h2>
<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">
	<li><a href="order/">Заказы</a></li>
	<li><a href="cart/">Корзина</a></li>
	<li><a href="order/?filter_history=Y">История заказов</a></li>
</ul>
							
<h2>Личная информация</h2>
<ul data-role="listview" data-inset="true" data-theme="c">
	<li><a href="profile/">Изменить регистрационные данные</a></li>
	<li><a href="profile/?change_password=yes">Изменить пароль</a></li>
	<li><a href="profile/?forgot_password=yes">Забыли пароль?</a></li>
</ul>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>