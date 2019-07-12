<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<div class="personal-page-nav">
	<p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки. </p>
	<div>
		<h2>Личная информация</h2>
		<ul>
			<li><a href="profile/">Изменить регистрационные данные</a></li>
		</ul>
	</div>
	<div>
		<h2>Заказы</h2>
		<ul>
			<li><a href="order/">Ознакомиться с состоянием заказов</a></li>
			<li><a href="cart/">Посмотреть содержимое корзины</a></li>
			<li><a href="order/?filter_history=Y">Посмотреть историю заказов</a></li>
		</ul>
	</div>
	<div>
		<h2>Подписка</h2>
		<ul>
			<li><a href="subscribe/">Изменить подписку</a></li>
		</ul>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
