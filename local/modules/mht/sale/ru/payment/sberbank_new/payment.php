<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Квитанция</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?= LANG_CHARSET ?>">
<style type="text/css">
H1 {font-size: 12pt;}
p, ul, ol, h1 {margin-top:6px; margin-bottom:6px}
td {font-size: 9pt;}
small {font-size: 7pt;}
body {font-size: 10pt;}
</style>
</head>
<body bgColor="#ffffff">

<table border="0" cellspacing="0" cellpadding="0" style="width:180mm; height:145mm;">
<tr valign="top">
	<td style="width:50mm; height:70mm; border:1pt solid #000000; border-bottom:none; border-right:none;" align="center">
	<b>Извещение</b><br>
	<font style="font-size:53mm">&nbsp;<br></font>
	<b>Кассир</b>
	</td>
	<td style="border:1pt solid #000000; border-bottom:none;" align="center">
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><small><i>Форма № ПД-4</i></small></td>
			</tr>
			<tr>
				<td style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("COMPANY_NAME"))?></td>
			</tr>
			<tr>
				<td align="center"><small>(наименование получателя платежа)</small></td>
			</tr>
		</table>

		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:37mm; border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("INN"))."/".(CSalePaySystemAction::GetParamValue("KPP"))?></td>
				<td style="width:9mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("SETTLEMENT_ACCOUNT"))?></td>
			</tr>
			<tr>
				<td align="center"><small>(ИНН получателя платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер счета получателя платежа)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>в&nbsp;</td>
				<td style="width:73mm; border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("BANK_NAME"))?></td>
				<td align="right">БИК&nbsp;&nbsp;</td>
				<td style="width:33mm; border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("BANK_BIC"))?></td>
			</tr>
			<tr>
				<td></td>
				<td align="center"><small>(наименование банка получателя платежа)</small></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("BANK_COR_ACCOUNT"))?></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:60mm; border-bottom:1pt solid #000000;">Оплата заказа №
	<?=(CSalePaySystemAction::GetParamValue("ORDER_ID"))?>
	от
	<?=(CSalePaySystemAction::GetParamValue("DATE_INSERT"))?></td>
				<td style="width:2mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;">&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><small>(наименование платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер лицевого счета (код) плательщика)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Ф.И.О. плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("PAYER_CONTACT_PERSON"))?></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Адрес плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;"><?
					//собираем фактический
					$sAddrFact = "";
					(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_REGION"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_REGION"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_CITY"))>0)
					{
						$g = substr(CSalePaySystemAction::GetParamValue("PAYER_CITY"), 0, 2);
						$sAddrFact .= ($sAddrFact<>""? ", ":"").($g<>"г." && $g<>"Г."? "г. ":"").(CSalePaySystemAction::GetParamValue("PAYER_CITY"));
					}
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"));
					echo $sAddrFact;
				?>&nbsp;</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Сумма платежа&nbsp;<?
				if(strpos(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), ".")!==false)
					$a = explode(".", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));
				else
					$a = explode(",", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));

				if ($a[1] <= 9 && $a[1] > 0)
					$a[1] = $a[1]."0";
				elseif ($a[1] == 0)
					$a[1] = "00";

				echo "<font style=\"text-decoration:underline;\">&nbsp;".$a[0]."&nbsp;</font>&nbsp;руб.&nbsp;<font style=\"text-decoration:underline;\">&nbsp;".$a[1]."&nbsp;</font>&nbsp;коп.";
				?></td>
				<td align="right">&nbsp;&nbsp;Сумма платы за услуги&nbsp;&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп.</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>
				<td align="right">&nbsp;&nbsp;&laquo;______&raquo;________________ 201____ г.</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td><small>С условиями приема указанной в платежном документе суммы,
				в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><b>Подпись плательщика _____________________</b></td>
			</tr>
		</table>
	</td>
</tr>



<tr valign="top">
	<td style="width:50mm; height:70mm; border:1pt solid #000000; border-right:none;" align="center">
	<b>Извещение</b><br>
	<font style="font-size:53mm">&nbsp;<br></font>
	<b>Кассир</b>
	</td>
	<td style="border:1pt solid #000000;" align="center">
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><small><i>Форма № ПД-4</i></small></td>
			</tr>
			<tr>
				<td style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("COMPANY_NAME"))?></td>
			</tr>
			<tr>
				<td align="center"><small>(наименование получателя платежа)</small></td>
			</tr>
		</table>

		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:37mm; border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("INN"))."/".(CSalePaySystemAction::GetParamValue("KPP"))?></td>
				<td style="width:9mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("SETTLEMENT_ACCOUNT"))?></td>
			</tr>
			<tr>
				<td align="center"><small>(ИНН получателя платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер счета получателя платежа)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>в&nbsp;</td>
				<td style="width:73mm; border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("BANK_NAME"))?></td>
				<td align="right">БИК&nbsp;&nbsp;</td>
				<td style="width:33mm; border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("BANK_BIC"))?></td>
			</tr>
			<tr>
				<td></td>
				<td align="center"><small>(наименование банка получателя платежа)</small></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Номер кор./сч. банка получателя платежа&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("BANK_COR_ACCOUNT"))?></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td style="width:60mm; border-bottom:1pt solid #000000;">Оплата заказа №
	<?=(CSalePaySystemAction::GetParamValue("ORDER_ID"))?>
	от
	<?=(CSalePaySystemAction::GetParamValue("DATE_INSERT"))?></td>
				<td style="width:2mm;">&nbsp;</td>
				<td style="border-bottom:1pt solid #000000;">&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><small>(наименование платежа)</small></td>
				<td><small>&nbsp;</small></td>
				<td align="center"><small>(номер лицевого счета (код) плательщика)</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Ф.И.О. плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;"><?=(CSalePaySystemAction::GetParamValue("PAYER_CONTACT_PERSON"))?></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td width="1%" nowrap>Адрес плательщика&nbsp;&nbsp;</td>
				<td width="100%" style="border-bottom:1pt solid #000000;"><?
					//собираем фактический
					$sAddrFact = "";
					(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_REGION"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_REGION"));
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_CITY"))>0)
					{
						$g = substr(CSalePaySystemAction::GetParamValue("PAYER_CITY"), 0, 2);
						$sAddrFact .= ($sAddrFact<>""? ", ":"").($g<>"г." && $g<>"Г."? "г. ":"").(CSalePaySystemAction::GetParamValue("PAYER_CITY"));
					}
					if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"))>0)
						$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"));
					echo $sAddrFact;
				?>&nbsp;</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Сумма платежа&nbsp;<?
				if(strpos(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), ".")!==false)
					$a = explode(".", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));
				else
					$a = explode(",", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));

				if ($a[1] <= 9 && $a[1] > 0)
					$a[1] = $a[1]."0";
				elseif ($a[1] == 0)
					$a[1] = "00";

				echo "<font style=\"text-decoration:underline;\">&nbsp;".$a[0]."&nbsp;</font>&nbsp;руб.&nbsp;<font style=\"text-decoration:underline;\">&nbsp;".$a[1]."&nbsp;</font>&nbsp;коп.";
				?></td>
				<td align="right">&nbsp;&nbsp;Сумма платы за услуги&nbsp;&nbsp;_____&nbsp;руб.&nbsp;____&nbsp;коп.</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td>Итого&nbsp;&nbsp;_______&nbsp;руб.&nbsp;____&nbsp;коп.</td>
				<td align="right">&nbsp;&nbsp;&laquo;______&raquo;________________ 201____ г.</td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td><small>С условиями приема указанной в платежном документе суммы,
				в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.</small></td>
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" style="width:122mm; margin-top:3pt;">
			<tr>
				<td align="right"><b>Подпись плательщика _____________________</b></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<br />
<h1>Внимание! В стоимость заказа не включена комиссия банка.</h1>

<!-- Условия поставки -->
<h1><b>Метод оплаты:</b></h1>
<ol>
	<li>Распечатайте квитанцию. Если у вас нет принтера, перепишите верхнюю часть квитанции и заполните по этому образцу стандартный бланк квитанции в вашем банке.</li>
	<li>Вырежьте по контуру квитанцию.</li>
	<li>Оплатите квитанцию в любом отделении банка, принимающего платежи от частных лиц.</li>
	<li>Сохраните квитанцию до подтверждения исполнения заказа.</li>
</ol>

<h1><b>Условия поставки:</b> </h1>
<ul>
	<li>Отгрузка оплаченного товара производится после подтверждения факта платежа.</li>
	<li>Идентификация платежа производится по квитанции, поступившей в наш банк.</li>
</ul>


<p><b>Примечание:</b>
<?=(CSalePaySystemAction::GetParamValue("COMPANY_NAME"))?>
	не может гарантировать конкретные сроки проведения вашего платежа. За дополнительной информацией о сроках доставки квитанции в банк получателя, обращайтесь в свой банк.</p>
</body>
</html>