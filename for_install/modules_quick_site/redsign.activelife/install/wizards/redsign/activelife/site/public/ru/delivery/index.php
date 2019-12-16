<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Доставка");
?><div class="p-delivery">
	<div class="p-delivery__guarantee">
		<div class="p-delivery__guarantee-icon">
 <img src="guarantee.png" alt="Гарантия" title="Гарантия">
		</div>
		<div class="p-delivery__guarantee-text">
			Если купленная вещь не подошла, вы всегда можете вернуть ее при условии сохранения её товарного вида, упаковки и этикетки.
		</div>
	</div>
	<div class="p-delivery__table-wrap">
		<table class="table">
		<tbody>
		<tr>
			<th>
				Территориальная зона доставки
			</th>
			<th colspan="3" style="text-align: center;">
				Стоимость и условия доставки
			</th>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<i>Вес заказа до 20кг</i>
			</td>
			<td>
				<i>Вес заказа от 20кг до 900кг</i>
			</td>
			<td>
				<i>Вес заказа свыше 900кг</i>
			</td>
		</tr>
		<tr>
			<th colspan="4">
				Москва
			</th>
		</tr>
		<tr>
			<td>
				Все районы(кроме Зеленограда и мкр.Жулебино)
			</td>
			<td>
				<div class="p-delivery__price">
					300р
				</div>
				<br>
				<div>
					(Минимальная сумма заказа 1000р.)
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					500р
				</div>
			</td>
			<td>
				Индивидуальный расчет в зависимости от стоимости заказа
			</td>
		</tr>
		<tr>
			<th colspan="4">
				Москва за МКАД и Московская область
			</th>
		</tr>
		<tr>
			<td>
				Люберцкий район, Некрасовка и Жулебино:(Люберцы, Красково, Томилино, Жилино, Малаховка, Мирный, Октябрьский, Пехорка, Сосновка, Часовня, Чкалова)
			</td>
			<td>
				<div class="p-delivery__freeprice">
					Бесплатно
				</div>
				<br>
				<div>
					(Минимальная сумма заказа 500р.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__freeprice">
					Бесплатно
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					500р
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Кожухово, Латкарино, Островцы, Жуковский, Раменское, Дзержинский, Котельники, Реутов, Железнодорожный
			</td>
			<td>
				<div class="p-delivery__price">
					300р
				</div>
				<br>
				<div>
					(Минимальная сумма заказа 1000р.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					300р
				</div>
				<br>
				<div>
					(Минимальная сумма заказа 1000р.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					500р
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Другие районы и города Московской области в пределах от МКАД до Московского Малого Кольца
			</td>
			<td>
				<div class="p-delivery__price">
					1000р
				</div>
				<br>
				<div>
					(Минимальная сумма заказа 3000р.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					1000р
				</div>
			</td>
			<td>
				Индивидуальный расчет в зависимости от стоимости заказа
			</td>
		</tr>
		<tr>
			<td>
				Другие районы и города Московской области за пределы Московского Малого Кольца
			</td>
			<td>
				<div class="p-delivery__price">
					1500р
				</div>
				<br>
				<div>
					(Минимальная сумма заказа 3000р.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					1500р
				</div>
			</td>
			<td>
				Индивидуальный расчет в зависимости от стоимости заказа
			</td>
		</tr>
		<tr>
			<th colspan="4">
				За пределами Московской области
			</th>
		</tr>
		<tr>
			<td>
				 Доставка по областям: <br>
				<br>
				 Рязанская, Владимирская, Ярославская, Тверская, Смоленская, Калужская, Тульская
			</td>
			<td colspan="3">
				Индивидуальный расчет в зависимости от стоимости заказа
			</td>
		</tr>
		</tbody>
		</table>
	</div>
	<div class="p-delivery__ps">
		<div class="p-delivery__delivery-time">
		<svg class="icon-svg p-delivery__svg-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-payment-clock"></use></svg>
 <b>Доставка заказов осуществляется ежедневно кроме субботы и воскресенья</b>
		</div>
		<div class="p-delivery__delivery-point">
		<svg class="icon-svg p-delivery__svg-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-payment-box"></use></svg>
 <i>Доставка заказов осуществляется до подъезда. На охраняемых территориях необходимо обеспечить подъезд транспорта к подъезду (выписать пропуск, предупредить охрану и т.п.)</i><br>
			<br>
 <i>Услуга доставки до дверей квартиры предоставляется по предварительной договоренности с оператором</i>
		</div>
	</div>
</div><br><?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>