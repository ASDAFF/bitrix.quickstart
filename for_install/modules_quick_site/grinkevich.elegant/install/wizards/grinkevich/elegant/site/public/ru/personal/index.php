<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>

<div class="personal-page-nav text">
	<p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки. </p>
	<div>
		<h2>Личная информация</h2>
		<ul class="lsnn">
			<li><a href="#SITE_DIR#personal/profile/">Изменить регистрационные данные</a></li>
		</ul>
	</div>
	<div>
		<h2>Заказы</h2>
		<ul class="lsnn">
			<li><a href="#SITE_DIR#personal/order/">Ознакомиться с состоянием заказов</a></li>
			<li><a href="#SITE_DIR#personal/cart/">Посмотреть содержимое корзины</a></li>
			<li><a href="#SITE_DIR#personal/order/?filter_history=Y">Посмотреть историю заказов</a></li>
		</ul>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>



