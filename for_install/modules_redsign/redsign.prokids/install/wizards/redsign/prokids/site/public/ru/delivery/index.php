<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Доставка");
?> 

<div style="overflow-x: auto; overflow-y: hidden;">
	<table align="left" width="100%" cellspacing="1" cellpadding="18" border="0" bgcolor="#999999" class="delivery_table"> 	 
		<tbody> 
		<tr>
			<td bgcolor="#D7D7D7" rowspan="2"><font size="2"><b>Территориальная зона доставки</b></font></td>
			<td align="center" valign="middle" bgcolor="#D7D7D7" colspan="3"><font size="2"><b>Стоимость и условия доставки</b></font></td>
		</tr>
		<tr>
			<td align="center" bgcolor="#D7D7D7"><font size="2">Вес заказа до 20 кг</font></td>
			<td align="center" bgcolor="#D7D7D7"><font size="2">Вес заказа от 20 кг до 900 кг</font></td>
			<td align="center" bgcolor="#D7D7D7"><font size="2">Вес заказа свыше 900 кг</font></td>
		</tr>
		<tr>
			<td bgcolor="#EBEBEB" colspan="4"><font size="2"><b>МОСКВА:</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2"><b>Все районы</b> (кроме Зеленограда и мкр. Жулебино)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>300р</b> <br />(Минимальная сумма заказа 1000р.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>500р</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2">Индивидуальный расчет в зависимости от стоимости заказа</font></td>
		</tr>
		<tr>
			<td bgcolor="#EBEBEB" colspan="4"><font size="2"><b>РАЙОНЫ МОСКВЫ ЗА МКАД И МОСКОВСКАЯ ОБЛАСТЬ</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2"><b>Люберецкий район, Некрасовка и Жулебино:</b>(Люберцы, Красково, Томилино, Жилино, Малаховка, Мирный, Октябрьский, Пехорка, Сосновка, Часовня, Чкалово)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>Бесплатно</b><br />(Минимальная сумма заказа 500р.)</font></td><td align="center" bgcolor="#FFFFFF"><font size="2"><b>Бесплатно</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>500р</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2">Кожухово, Латкарино, Островцы, Жуковский, Раменское, Дзержинский, Котельники, Реутов, Железнодорожный</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>300р</b><br />(Минимальная сумма заказа 1000р.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>300р</b><br />(Минимальная сумма заказа 1000р.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>500р</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2">Другие районы и города Московской области в пределах от МКАД до Московского Малого Кольца</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1000р</b><br />(Минимальная сумма заказа 3000р.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1000р</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2">Индивидуальный расчет в зависимости от стоимость заказа</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2">Другие районы и города Московской области за пределы <b>Московского Малого Кольца</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1500р</b> <br />(Минимальная сумма заказа 3000р.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1500р</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2">Индивидуальный расчет в зависимости от стоимость заказа</font></td>
		</tr>
		<tr>
			<td bgcolor="#EBEBEB" colspan="4"><font size="2"><b>ЗА ПРЕДЕЛЫ МОСКОВСКОЙ ОБЛАСТИ</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2"><b>Доставка по областям:</b> <br />Рязанская, Владимирская, Ярославская, Тверская, Смоленская, Калужская, Тульская</font></td>
			<td bgcolor="#FFFFFF" colspan="3"><font size="2">Индивидуальный расчет в зависимости от стоимости заказа</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" colspan="4"><font size="2">Доставка заказов осуществляется ежедневно кроме субботы и воскресенья</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" colspan="4"><font size="2">Доставка заказов осуществляется до подъезда. На охраняемых территориях необходимо обеспечить подъезд транспорта к подъезду (выписать пропуск, предупредить охрану и т.п.)</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" colspan="4"><font size="2">Услуга доставки до дверей квартиры предоставляется по предварительной договоренности с оператором</font></td>
		</tr>
		</tbody>
	</table>
</div>
<style>
#content{
	line-height:21px;
}
#content ul{
	margin-left:16px;
}
	#content .delivery_table td{
	border-collapse:collapse;
	border-spacing:2px;
	border:1px solid #808080;
	padding:20px;
}
</style>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>