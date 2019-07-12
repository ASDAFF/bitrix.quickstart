<?
define('NEED_AUTH', 'Y');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?> 
<div id="contactfaq" class="content contenttext">
	<p>
		В данном разделе Вы можете проверить текущее состояние корзины, ход выполнения Ваших заказов, просмотреть или изменить личную информацию, а также подписаться на новости и другие информационные рассылки. 
		<br />

		<br />
	</p>
</div>
<h2>Заказы </h2>
<br />
<table width="100%" border="0" cellpadding="1" cellspacing="1">
	<tbody>
		<tr>
			<td style="text-align: center; border-image: initial; "><a href="order/" title="Список заказов" ><img src="#SITE_DIR#images/medialibrary/e2a88dac8106766e723b422a44f3d8b5.jpg" title="Список заказов" border="0" alt="spisok.jpg" width="100" height="100"  /></a></td><td style="text-align: center; border-image: initial; "><a href="/basket/" title="Корзина" ><img src="#SITE_DIR#images/medialibrary/3be6b8bcd3221051332347652d1b6be9.jpg" title="Корзина" border="0" alt="karzina.jpg" width="100" height="100"  /></a></td><td style="text-align: center; border-image: initial; "><a href="order/?filter_history=Y" title="История заказов" ><img src="#SITE_DIR#images/medialibrary/78b661f12b419df648c6a76834235e23.jpg" title="История заказов" border="0" alt="history.jpg" width="100" height="100"  /></a></td>
		</tr>

		<tr>
			<td style="text-align: center; border-image: initial; "><a href="order/" >Список заказов</a></td><td style="text-align: center; border-image: initial; "><a href="/basket/" title="Корзина" >Корзина</a></td><td style="text-align: center; border-image: initial; "><a href="order/?filter_history=Y" >История заказов</a></td>
		</tr>
	</tbody>
</table>
<br />
<h2>Личная информация, подписка, поддержка</h2>
<br /><br />
<table width="100%" border="0" cellpadding="1" cellspacing="1">
	<tbody>
		<tr>
			<td style="text-align: center; border-image: initial; "><a href="profile/" title="Профиль" ><img src="#SITE_DIR#images/medialibrary/90fd8dd52a886692c6d8dc11ba066084.jpg" border="0" alt="polzovatel.jpg" width="100" height="100"  /></a></td><td style="text-align: center; border-image: initial; "><a href="subscribe/" title="Изменить подписку" ><img src="#SITE_DIR#images/medialibrary/d4dbcaa17ade33118bf0c0e0da46b19e.jpg" title="Подписка" border="0" alt="podpiska.jpg" width="100" height="100"  /></a></td><td style="text-align: center; border-image: initial; ">&nbsp;</td>
		</tr>

		<tr>
			<td style="border-image: initial; ">
			<br />
			<br />
			<div style="text-align: center; ">
				<a href="profile/" title="Профиль" >Регистрационные данные</a>
			</div>
			<div style="text-align: center; ">
				<a href="#SITE_DIR#login/?change_password=yes" >Изменить пароль</a>
			</div>
			<div style="text-align: center; ">
				<a href="profile/?change_password=yes" ></a><a href="#SITE_DIR#login/?change_password=yes" >Забыли пароль?</a>
			</div></td><td style="text-align: center; border-image: initial; "><a href="subscribe/">Изменить подписку</a></td><td style="border-image: initial; ">
			<div style="text-align: -webkit-auto; ">
				<br />

			</div><ul></ul></td>
		</tr>
	</tbody>
</table>
<br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>