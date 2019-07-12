<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<div class="bx_page">
	<p class="b-block-right">В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки. </p>
	<div class="b-block">
		<div class="b-form__fieldset__caption">Личная информация</div>
		<a href="#SITE_DIR#personal/profile/">Изменить регистрационные данные</a>
	</div>
	<div class="b-block">
		<div class="b-form__fieldset__caption">Заказы</div>
		<a href="#SITE_DIR#personal/order/">Ознакомиться с состоянием заказов</a><br/>
		<a href="#SITE_DIR#personal/cart/">Посмотреть содержимое корзины</a><br/>
		<a href="#SITE_DIR#personal/order/?filter_history=Y">Посмотреть историю заказов</a><br/>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
