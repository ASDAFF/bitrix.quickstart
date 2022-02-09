<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
/*
Скопируйте этот файл в папку /bitrix/admin/reports и измените по своему усмотрению

$ORDER_ID - ID текущего заказа

$arOrder - массив атрибутов заказа (ID, доставка, стоимость, дата создания и т.д.)
Следующий PHP код:
print_r($arOrder);
выведет на экран содержимое массива $arOrder.

$arOrderProps - массив свойств заказа (вводятся покупателями при оформлении заказа) следующей структуры:
array(
	"мнемонический код (или ID если мнемонический код пуст) свойства" => "значение свойства"
	)
	
$arParams - массив из настроек Печатных форм

$arUser - массив из настроек пользователя, совершившего заказ
*/
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=<?=LANG_CHARSET?>">
<title langs="ru">Счет</title>
<style>
<!--
/* Style Definitions */
p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin:0cm;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman";
	mso-fareast-font-family:"Times New Roman";}
p
	{margin-right:0cm;
	mso-margin-top-alt:auto;
	mso-margin-bottom-alt:auto;
	margin-left:0cm;
	mso-pagination:widow-orphan;
	font-size:12.0pt;
	font-family:"Times New Roman";
	mso-fareast-font-family:"Times New Roman";}
@page Section1
	{size:595.3pt 841.9pt;
	margin:2.0cm 42.5pt 2.0cm 3.0cm;
	mso-header-margin:35.4pt;
	mso-footer-margin:35.4pt;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
</head>

<body bgcolor=white lang=RU style='tab-interval:35.4pt'>

<div class=Section1>

<!-- REPORT BODY -->
<!-- ИЗМЕНИТЕ ЭТОТ ТЕКСТ НА РЕАЛЬНЫЕ ДАННЫЕ ПОСЛЕ КОПИРОВАНИЯ В ПАПКУ /bitrix/admin/reports -->
<p><b>ПОСТАВЩИК:</b> 
<?=$arParams["COMPANY_NAME"]?>
<br>
Адрес: <? echo $arParams["COUNTRY"].", ".$arParams["INDEX"].", г. ".$arParams["CITY"].", ".$arParams["ADDRESS"];?><br>
Телефон: <?=$arParams["PHONE"]?><br>
ИНН: <?=$arParams["INN"]?> / КПП: <?=$arParams["KPP"]?><br>
Банковские реквизиты:<br>
р/с <?=$arParams["RSCH"]?> в <?=$arParams["RSCH_BANK"]?> г. <?=$arParams["RSCH_CITY"]?><br>
к/с <?=$arParams["KSCH"]?><br>
БИК <?=$arParams["BIK"]?></p>

<p><b>ЗАКАЗЧИК: </b> 
<!-- ИЗМЕНИТЕ КЛЮЧИ МАССИВА $arOrderProps НА РЕАЛЬНЫЕ ПОСЛЕ КОПИРОВАНИЯ В ПАПКУ /bitrix/admin/reports -->
<?
if(empty($arParams))
{
	echo "[".$arOrder["USER_ID"]."] ";
	$db_user = CUser::GetByID($arOrder["USER_ID"]);
	$arUser = $db_user->Fetch();
	echo htmlspecialcharsbx($arUser["NAME"])." ".htmlspecialcharsbx($arUser["LAST_NAME"]);

	if (strlen($arOrderProps["F_INN"])>0) echo "<br>ИНН: ".$arOrderProps["F_INN"];?>
	<br>Адрес: 
	<?
	if (strlen($arOrderProps["F_INDEX"])>0) echo $arOrderProps["F_INDEX"].",";

	$arVal = CSaleLocation::GetByID($arOrderProps["F_LOCATION"], "ru");
	if(strlen($arVal["COUNTRY_NAME"])>0 && strlen($arVal["CITY_NAME"])>0)
		echo htmlspecialcharsbx($arVal["COUNTRY_NAME"]." - ".$arVal["CITY_NAME"]);
	elseif(strlen($arVal["COUNTRY_NAME"])>0 || strlen($arVal["CITY_NAME"])>0)
		echo htmlspecialcharsbx($arVal["COUNTRY_NAME"].$arVal["CITY_NAME"]);

	if (strlen($arOrderProps["F_CITY"])>0) echo ", г. ".$arOrderProps["F_CITY"];
	if (strlen($arOrderProps["F_ADDRESS"])>0 && strlen($arOrderProps["F_CITY"])>0) 
		echo ", ".$arOrderProps["F_ADDRESS"];
	elseif(strlen($arOrderProps["F_ADDRESS"])>0)
		echo $arOrderProps["F_ADDRESS"];

	if (strlen($arOrderProps["F_EMAIL"])>0) echo "<br>E-Mail: ".$arOrderProps["F_EMAIL"];?>
	<br>Контактное лицо: <?echo $arOrderProps["F_NAME"];?>
	<?
}
else
{
	if(strlen($arParams["BUYER_COMPANY_NAME"]) > 0)
		echo $arParams["BUYER_COMPANY_NAME"];
	else
		echo $arParams["BUYER_LAST_NAME"]." ".$arParams["BUYER_FIRST_NAME"]." ".$arParams["BUYER_SECOND_NAME"];
	
	if (strlen($arParams["BUYER_INN"])>0) echo "<br>ИНН/КПП: ".$arParams["BUYER_INN"]." / ".$arParams["BUYER_KPP"];
	
	echo "<br>Адрес: ".$arParams["BUYER_COUNTRY"].", ".$arParams["BUYER_INDEX"].", г. ".$arParams["BUYER_CITY"].", ".$arParams["BUYER_ADDRESS"];
	
	if (strlen($arParams["BUYER_CONTACT"])>0) echo "<br>Контактное лицо: ".$arParams["BUYER_CONTACT"];

}
?>
<br>Платежная система:
[<?echo $arOrder["PAY_SYSTEM_ID"];?>]
<?
$arPaySys = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
echo htmlspecialcharsbx($arPaySys["NAME"]);
?>
</p>
<p><b>СЧЕТ N:</b> 
<?echo $ORDER_ID?>
от 
<?echo $arOrder["DATE_INSERT_FORMAT"]?>
</p>

<?
//состав заказа
ClearVars("b_");
$db_basket = CSaleBasket::GetList(($b="NAME"), ($o="ASC"), array("ORDER_ID"=>$ORDER_ID));
if ($db_basket->ExtractFields("b_")):
	?>
	<table border="0" cellspacing="0" cellpadding="2" width="100%">
		<tr bgcolor="#E2E2E2">
			<td align="center" style="border: 1pt solid #000000; border-right:none;">№</td>
			<td align="center" style="border: 1pt solid #000000; border-right:none;">Предмет счета</td>
			<td nowrap align="center" style="border: 1pt solid #000000; border-right:none;">Кол-во</td>
			<td nowrap align="center" style="border: 1pt solid #000000; border-right:none;">Цена, руб</td>
			<td nowrap align="center" style="border: 1pt solid #000000;">Сумма, руб</td>
		</tr>
		<?
		$n = 1;
		$sum = 0.00;
		do
		{
			?>
			<tr valign="top">
				<td bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo $n++ ?>
				</td>
				<td bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo "[$b_PRODUCT_ID] ".$b_NAME; ?>
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo $b_QUANTITY; ?>
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo SaleFormatCurrency(($b_PRICE), $b_CURRENCY, true) ?>
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-top:none;">
					<?echo SaleFormatCurrency(($b_PRICE)*$b_QUANTITY, $b_CURRENCY, true) ?>
				</td>
			</tr>
			<?
			$sum += doubleval(($b_PRICE)*$b_QUANTITY);
		}
		while ($db_basket->ExtractFields("b_"));
		?>

		<?if (False && DoubleVal($arOrder["DISCOUNT_VALUE"])>0):?>
			<tr>
				<td bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo $n++?>
				</td>
				<td bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					Скидка
				</td>
				<td valign="top" align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">1 </td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo SaleFormatCurrency($arOrder["DISCOUNT_VALUE"], $arOrder["CURRENCY"], true) ?>
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-top:none;">
					<?echo SaleFormatCurrency($arOrder["DISCOUNT_VALUE"], $arOrder["CURRENCY"], true) ?>
				</td>
			</tr>
		<?endif?>

		<?if (DoubleVal($arOrder["PRICE_DELIVERY"])>0):?>
			<tr>
				<td bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo $n?>
				</td>
				<td bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					Доставка <?
					$arDelivery_tmp = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
					echo ((strlen($arDelivery_tmp["NAME"])>0) ? "([".$arOrder["DELIVERY_ID"]."] " : "" );
					echo $arDelivery_tmp["NAME"];
					echo ((strlen($arDelivery_tmp["NAME"])>0) ? ")" : "" );
					?>
				</td>
				<td valign="top" align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">1 </td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?echo SaleFormatCurrency($arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"], true) ?>
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-top:none;">
					<?echo SaleFormatCurrency($arOrder["PRICE_DELIVERY"], $arOrder["CURRENCY"], true) ?>
				</td>
			</tr>
		<?endif?>

		<?
		$db_tax_list = CSaleOrderTax::GetList(array("APPLY_ORDER"=>"ASC"), Array("ORDER_ID"=>$ORDER_ID));
		while ($ar_tax_list = $db_tax_list->Fetch())
		{
			?>
			<tr>
				<td align="right" bgcolor="#ffffff" colspan="4" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					<?
					if ($ar_tax_list["IS_IN_PRICE"]=="Y")
					{
						echo "В том числе ";
					}
					echo htmlspecialcharsbx($ar_tax_list["TAX_NAME"]); 
					if ($ar_tax_list["IS_PERCENT"]=="Y")
					{
						echo " (".$ar_tax_list["VALUE"]."%)";
					}
					?>:
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-top:none;">
					<?echo SaleFormatCurrency($ar_tax_list["VALUE_MONEY"], $arOrder["CURRENCY"], true)?>
				</td>
			</tr>
			<?
		}
		?>

<!--
		<?if (DoubleVal($arOrder["TAX_VALUE"])>0):?>
			<tr>
				<td align="right" bgcolor="#ffffff" colspan="4" style="border: 1pt solid #000000; border-right:none; border-top:none;">
					Налоги:
				</td>
				<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-top:none;">
					<?echo SaleFormatCurrency($arOrder["TAX_VALUE"], $arOrder["CURRENCY"])?>
				</td>
			</tr>
		<?endif?>
-->

		<tr>
			<td align="right" bgcolor="#ffffff" colspan="4" style="border: 1pt solid #000000; border-right:none; border-top:none;">Итого:</td>
			<td align="right" bgcolor="#ffffff" style="border: 1pt solid #000000; border-top:none;"><?echo SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"], true) ?></td>
		</tr>
	</table>
<?endif?>
<p><b>Итого к оплате:</b> 
	<?
	if ($arOrder["CURRENCY"]=="RUR" || $arOrder["CURRENCY"]=="RUB")
	{
		echo Number2Word_Rus($arOrder["PRICE"]);
	}
	else
	{
		echo SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]);
	}
	?>.</p>

<p><font size="2">В случае непоступления средств на расчетный счет продавца в течение пяти
банковских дней со дня выписки счета, продавец оставляет за собой право
пересмотреть отпускную цену товара в рублях пропорционально изменению курса доллара
и выставить счет на доплату.<br><br>
В платежном поручении обязательно указать - "Оплата по счету № <?echo $ORDER_ID?> от <?echo $arOrder["DATE_INSERT_FORMAT"] ?>".<br><br>
Получение товара только после прихода денег на расчетный счет компании.
</font></p>
<!-- END REPORT BODY -->

<p>&nbsp;</p>
<table border=0 cellspacing=0 cellpadding=0 width="100%">
<tr>
<td width="40%">
<p class=MsoNormal>Руководитель организации:</p>
</td>
<td width="60%">
<p class=MsoNormal>_______________ <input size="16" style="border:0px solid #000000;font-size:14px;font-style:bold;" type="text" value="/ <?echo ((strlen($arParams["DIRECTOR"]) > 0) ? $arParams["DIRECTOR"] : "_______________")?> /"></p>
</td>
</tr>
<tr>
<td width="40%">
<p class=MsoNormal>&nbsp;</p>
</td>
<td width="60%">
<p class=MsoNormal>&nbsp;</p>
</td>
</tr>
<tr>
<td width="40%">
<p class=MsoNormal>&nbsp;</p>
</td>
<td width="60%">
<p class=MsoNormal>&nbsp;</p>
</td>
</tr>
<tr>
<td width="40%" >
<p class=MsoNormal>Гл. бухгалтер:</p>
</td>
<td width="60%">
<p class=MsoNormal>_______________ <input size="16" style="border:0px solid #000000;font-size:14px;font-style:bold;" type="text" value="/ <?echo ((strlen($arParams["BUHG"]) > 0) ? $arParams["BUHG"] : "_______________")?> /"></p>
</td>
</tr>
</table>
</div>
</body>
</html>