<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Delivery");
?> 

<div style="overflow-x: auto; overflow-y: hidden;">
<table align="left" width="100%" cellspacing="1" cellpadding="18" border="0" bgcolor="#999999" class="delivery_table"> 	 
		<tbody> 
		<tr>
			<td bgcolor="#D7D7D7" rowspan="2"><font size="2"><b>Delivery zone</b></font></td>
			<td align="center" valign="middle" bgcolor="#D7D7D7" colspan="3"><font size="2"><b>Delivery terms &amp; Cost</b></font></td>
		</tr>
		<tr>
			<td align="center" bgcolor="#D7D7D7"><font size="2">Weight < 20 ��</font></td>
			<td align="center" bgcolor="#D7D7D7"><font size="2">20 �� < Weight < 900 ��</font></td>
			<td align="center" bgcolor="#D7D7D7"><font size="2">Weight > 900 ��</font></td>
		</tr>
		<tr>
			<td bgcolor="#EBEBEB" colspan="4"><font size="2"><b>Moscow:</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2"><b>All distinct</b> (except Zelenograd and md. ZHulebino)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>300 rub.</b> <br />(Minimum order 1000�.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>500 rub.</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2">Individual payment depending on the order value</font></td>
		</tr>
		<tr>
			<td bgcolor="#EBEBEB" colspan="4"><font size="2"><b>Moscow distincts outer ring & Moscow region</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2"><b>Districts Lyuberetskiy, Nekrasivka ZHulebino:</b> (Lyubertsy, Kraskovo Tomilino Zilina, Malahovka, Mirny, October, Pehorka, SOSNOVKA Chapel, Chkalovo)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>Free</b><br />(Minimum order 500 rub.)</font></td><td align="center" bgcolor="#FFFFFF"><font size="2"><b>���������</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>500 rub.</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2">Kozhukhovo, Latkarino, Ostrovtsy, Zhukovskiy, Ramenskoye, Dzerzhinskiy, Kotel'niki, Reutov, Zheleznodorozhnyy</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>300�</b><br />(Minimum order 1000�.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>300�</b><br />(Minimum order 1000�.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>500�</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2">Other cities &amp; distincts Moscow region(inner ring)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1000�</b><br />(Minimum order 3000�.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1000�</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2">Individual payment depending on the order value</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2">Other cities &amp; distincts Moscow region(outer ring)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1500�</b> <br />(Minimum order 3000�.)</font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2"><b>1500�</b></font></td>
			<td align="center" bgcolor="#FFFFFF"><font size="2">Individual payment depending on the order value</font></td>
		</tr>
		<tr>
			<td bgcolor="#EBEBEB" colspan="4"><font size="2"><b>Outside the Moscow region</b></font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><font size="2"><b>Federal deliveries:</b> <br />Ryazanskaya, Vladimirskaya, Yaroslavskaya, Tverskaya, Smolenskaya, Kaluzhskaya, Tul'skaya</font></td>
			<td bgcolor="#FFFFFF" colspan="3"><font size="2">Individual payment depending on the order value</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" colspan="4"><font size="2">Shipping is daily except Saturdays and Sundays</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" colspan="4"><font size="2">Delivery of orders is carried to the entrance. The protected area is necessary to provide transport to the entrance staircase (write pass, warn security, etc.)</font></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF" colspan="4"><font size="2">Service delivery to the door of the apartment is available by prior arrangement with the operator</font></td>
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