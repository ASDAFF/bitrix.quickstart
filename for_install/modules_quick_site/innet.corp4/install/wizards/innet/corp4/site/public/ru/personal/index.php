<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");?>

<div class="bx_page" style="margin-bottom: 40px; overflow: hidden;">
    <p style="margin-bottom:30px;">В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки. </p>
    <div style="margin-bottom:30px;">
		<h2 class="title5 fs24">Личная информация</h2>
		<a href="profile/">Изменить регистрационные данные</a>
	</div>
	<div style="margin-bottom:30px;">
		<h2 class="title5 fs24">Заказы</h2>
		<a href="cart/">Посмотреть содержимое корзины</a><br/>
		<a href="order/">Посмотреть историю / состояние заказов</a><br/>
	</div>
	<div>
		<h2 class="title5 fs24">Подписка</h2>
		<a href="subscribe/">Изменить подписку</a>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>