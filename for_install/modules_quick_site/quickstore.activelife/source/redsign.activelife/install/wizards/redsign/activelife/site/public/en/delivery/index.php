<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Delivery");
?><div class="p-delivery">
	<div class="p-delivery__guarantee">
		<div class="p-delivery__guarantee-icon">
 <img src="guarantee.png" alt="Warranty" title="Warranty">
		</div>
		<div class="p-delivery__guarantee-text">
			If the purchased item does not fit, you can always return it while maintaining its presentation, packaging and labels.
		</div>
	</div>
	<div class="p-delivery__table-wrap">
		<table class="table">
		<tbody>
		<tr>
			<th>
				Territorial delivery area
			</th>
			<th colspan="3" style="text-align: center;">
				Price and delivery terms
			</th>
		</tr>
		<tr>
			<td>
			</td>
			<td>
				<i>Order Weight 20 kg</i>
			</td>
			<td>
				<i>Order weight from 20 kg to 900 kg</i>
			</td>
			<td>
				<i>Order Weight over 900 kg</i>
			</td>
		</tr>
		<tr>
			<th colspan="4">
				Moscow
			</th>
		</tr>
		<tr>
			<td>
				All areas (except Zelenograd and mkr.Zhulebino)
			</td>
			<td>
				<div class="p-delivery__price">
					300rub.
				</div>
				<br>
				<div>
					(Minimum order of 1000 r.)
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					500 rub
				</div>
			</td>
			<td>
				Individual calculation, depending on the value of the order
			</td>
		</tr>
		<tr>
			<th colspan="4">
				Moscow outside Moscow and Moscow region
			</th>
		</tr>
		<tr>
			<td>
				Lyubertsky area Nekrasovka and Zhulebino: (Lyubertsy, Kraskovo, Tomilino, Zilina, Malakhovka, Mirny, October, Pekhorka, Sosnovka, Chapel, Chkalov)
			</td>
			<td>
				<div class="p-delivery__freeprice">
					Free
				</div>
				<br>
				<div>
					(Minimum order is 500 p.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__freeprice">
					Is free
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					500 rub
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Kozhukhovo, Lytkarino, Ostrovtsy, Zhukovsky, Ramenskoye, Dzerzhinsky Kotelniki, Reutov, Railway
			</td>
			<td>
				<div class="p-delivery__price">
					300 rub
				</div>
				<br>
				<div>
					(Minimum order of 1000 r.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					300 rub
				</div>
				<br>
				<div>
					(Minimum order of 1000 r.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					500 rub
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Other districts and towns of the Moscow region ranging from Moscow to the Moscow Small Ring
			</td>
			<td>
				<div class="p-delivery__price">
					1000 rub
				</div>
				<br>
				<div>
					(Minimum order is 3,000 p.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					1000 rub
				</div>
			</td>
			<td>
				Individual calculation, depending on the value of the order
			</td>
		</tr>
		<tr>
			<td>
				Other districts and towns of the Moscow region outside Moscow Small Ring
			</td>
			<td>
				<div class="p-delivery__price">
					1500 rub
				</div>
				<br>
				<div>
					(Minimum order is 3,000 p.)
					<div>
					</div>
				</div>
			</td>
			<td>
				<div class="p-delivery__price">
					1500 rub
				</div>
			</td>
			<td>
				Individual calculation, depending on the value of the order
			</td>
		</tr>
		<tr>
			<th colspan="4">
				Outside Moscow region
			</th>
		</tr>
		<tr>
			<td>
				 for the delivery: <br>
				<br>
				 Ryazan, Vladimir, Yaroslavl, Tver, Smolensk, Kaluga, Tula
			</td>
			<td colspan="3">
				Individual calculation, depending on the value of the order
			</td>
		</tr>
		</tbody>
		</table>
	</div>
	<div class="p-delivery__ps">
		<div class="p-delivery__delivery-time">
		<svg class="icon-svg p-delivery__svg-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-payment-clock"></use></svg>
 <b>Delivery of orders is carried out every day except Saturday and Sunday</b>
		</div>
		<div class="p-delivery__delivery-point">
		<svg class="icon-svg p-delivery__svg-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-payment-box"></use></svg>
 <i>Orders are delivered to the door. The protected area is necessary to provide transport access to the entrance (to write out a pass, warn security, etc.)</i><br>
			<br>
 <i>Courier service door to the apartment is available upon prior arrangement with the operator</i>
		</div>
	</div>
</div><br><?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
