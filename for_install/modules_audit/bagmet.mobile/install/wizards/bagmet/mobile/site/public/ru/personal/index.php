<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<div class="page_wrapper">
	<p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию.</p>
	<br>
	<h4 class="auth_form_title">Личная информация</h4>
	<ul>
		<li><a href="profile/">Изменить регистрационные данные</a></li>
	</ul>
	<br>
	<h4 class="auth_form_title">Заказы</h4>
	<ul>
		<li><a href="order/">Ознакомиться с состоянием заказов</a></li>
		<li><a href="cart/">Посмотреть содержимое корзины</a></li>
		<li><a href="order/?filter_history=Y">Посмотреть историю заказов</a></li>
	</ul>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
