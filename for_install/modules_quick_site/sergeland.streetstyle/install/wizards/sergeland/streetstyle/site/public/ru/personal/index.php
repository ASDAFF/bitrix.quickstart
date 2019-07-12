<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<?$APPLICATION->IncludeComponent("sergeland:sale.auth.hash", ".default", array(), false);?>
<?if($USER->IsAuthorized()):?>
<div class="personal-page-nav">
	<p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию. </p>
	<div>
		<h2>Личная информация</h2>
		<ul class="lsnn">
			<li><a href="profile/">Изменить регистрационные данные</a></li>
		</ul>
	</div>
	<div>
		<h2>Заказы</h2>
		<ul class="lsnn">
			<li><a href="order/">Ознакомиться с состоянием заказов</a></li>
			<li><a href="order/?filter_history=Y&filter_status=F">Посмотреть историю заказов</a></li>
		</ul>
	</div>
	<div>
		<h2>Подписка</h2>
		<ul class="lsnn">
			<li><a href="subscribe/">Изменить подписку</a></li>
		</ul>
	</div>	
	<div>
		<h2>Выйти</h2>
		<ul class="lsnn">
			<li><a href="logout/">Закончить сеанс авторизации</a></li>
		</ul>
	</div>	
</div>
<?else:?>
	<?$APPLICATION->AuthForm("")?>
<?endif?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>