<?
define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>

<article>
	<p>В личном кабинете Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки. </p>
	<ul>
		<li>
			<a href="profile/">Профиль</a>
		</li>
		<li>
			<a href="orders/">Заказы</a>
		</li>
		<li>
			<a href="subscribe/">Подписка</a>
		</li>
		<li>
			<a href="?logout=yes">Выйти</a>
		</li>
	</ul>
</article>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>