<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=<?=LANG_CHARSET?>">
<title langs="ru">Бланк заказа</title>
<style>
<!--
.header{font-size:17px; font-family:Tahoma;padding-left:8px;}
.sub_header{font-size:13px; font-family:Tahoma;padding-left:8px;}
.date{font-style:italic; font-family:Tahoma;padding-left:8px;}
.number{font-size:24px;font-family:Tahoma;font-style:italic;padding-left:8px;}
.user{font-size:12px;font-family:Tahoma;font-weight:bold;padding-left:8px;}
.summa{font-size:12px;font-family:Tahoma;font-weight:bold;padding-left:15px;}
-->
</style>
</head>

<body bgcolor=white lang=RU style='tab-interval:35.4pt'>
<?
$page = IntVal($page);
if ($page<=0) $page = 1;
?>
<table height="920" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td colspan="3">
			<!-- Верхний колонтитул height="109" -->
		</td>
	</tr>
	<tr valign="top">
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" border="0" width="595" align="center">
				<tr><td><br><br></td></tr>
				<tr>
					<td width="180"><font class="header">ТОВАРНЫЙ ЧЕК №</font></td>
					<td style="border-bottom : 1px solid Black;">
						<font class="number"><?echo $ORDER_ID;?></font> 
						- <input size="30" style="border:1px;font-size:16px;font-style:italic;" type="text" value="<?echo $page;?>">
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td width="180"><font class="sub_header">ДАТА:</font></td>
					<td style="border-bottom : 1px solid Black;">
						<input class="date" size="30" style="border:0px solid #000000;" type="text" value="<?echo $arOrder["DATE_INSERT_FORMAT"];?>">
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td width="180"><font class="sub_header">КОМУ:</font></td>
					<td style="border-bottom : 1px solid Black;">
						<?if(empty($arParams))
						{
							$userName = $arOrderProps["F_NAME"];
						}
						else
						{
							if(strlen($arParams["BUYER_COMPANY_NAME"]) > 0)
								$userName = $arParams["BUYER_COMPANY_NAME"];
							else
								$userName = $arParams["BUYER_LAST_NAME"]." ".$arParams["BUYER_FIRST_NAME"]." ".$arParams["BUYER_SECOND_NAME"];
						}?>
						<input class="user" size="50" style="border:0px solid #000000;" type="text" value="<?=$userName?>	">
					</td>
					<td>
					</td>
				</tr>
				<tr><td><br></td></tr>
			</table>

			<br>
			<?
			if (count($arBasketIDs)>0)
			{
				?>
				<table width="585" border="1" bordercolor="#000000" style="padding-left:10px" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td align="center">№</td>
						<td align="center">Наименование</td>
						<td align="center">Количество</td>
						<td align="center">Цена, руб</td>
						<td align="center">Cумма, руб</td>
					</tr>
					<?
					$arTaxList = array();
					$db_tax_list = CSaleOrderTax::GetList(array("APPLY_ORDER"=>"ASC"), Array("ORDER_ID"=>$ORDER_ID));
					while ($ar_tax_list = $db_tax_list->Fetch())
					{
						$arTaxList[] = $ar_tax_list;
					}

					$db_basket = CSaleBasket::GetList(($b="NAME"), ($o="ASC"), array("ORDER_ID"=>$ORDER_ID));
					$ORDER_PRICE = 0;
					$i = 0;
					while ($ar_basket = $db_basket->Fetch())
					{
						$ORDER_PRICE += DoubleVal($ar_basket["PRICE"])*DoubleVal($ar_basket["QUANTITY"]);
						$i++;
					}

					//$NUMPERPAGE = 150;
					$NUMPERPAGE = $i;
					$curNum = ($page-1)*$NUMPERPAGE;
					$start_i = $curNum;
					$end_i = $curNum+$NUMPERPAGE;
					if ($end_i >= count($arBasketIDs))
					{
						$start_i = 0;
						$end_i = count($arBasketIDs);
					}

					$sum_price = 0;
					$discount_price = 0;
					$arTax = array();
					$bVat = false;
					for ($i = $start_i; $i < $end_i; $i++)
					{
						$arBasket = CSaleBasket::GetByID($arBasketIDs[$i]);
						if ($arQuantities[$i] > DoubleVal($arBasket["QUANTITY"]))
						{
							$arQuantities[$i] = DoubleVal($arBasket["QUANTITY"]);
						}
						$sum_price += (DoubleVal($arBasket["PRICE"]))*$arQuantities[$i];

						$discount = DoubleVal($arBasket["DISCOUNT_PRICE"]);
						$discount_price += $discount * $arQuantities[$i];
						if(DoubleVal($arBasket["VAT_RATE"]) > 0)
							$bVat = true;
						
						if (count($arTaxList)>0)
						{
							if(!$bVat)
							{
								$TAX_PRICE_tmp = CSaleOrderTax::CountTaxes(
									DoubleVal(DoubleVal($arBasket["PRICE"])-$discount) * $arQuantities[$i],
									$arTaxList,
									$arOrder["CURRENCY"]);
								for ($di = 0; $di<count($arTaxList); $di++)
								{
									$arTaxList[$di]["TAX_SUM"] += $arTaxList[$di]["TAX_VAL"];
								}
							}
							elseif(DoubleVal($arBasket["VAT_RATE"]) > 0)
							{
								for ($di = 0; $di<count($arTaxList); $di++)
								{
									$arTaxList[$di]["TAX_SUM"] += (roundEx($arBasket["PRICE"] - DoubleVal($arBasket["PRICE"]/(1+$arBasket["VAT_RATE"])), 2))*$arQuantities[$i];
									
								}
							}
						}

						if ($i>=$curNum && $i<$curNum+$NUMPERPAGE)
						{
							?>
							<tr>
								<td><?echo $i+1;?></td>
								<td><?echo htmlspecialcharsbx($arBasket["NAME"]);?></td>
								<td><?echo $arQuantities[$i] ?></td>
								<td align="right"><?echo SaleFormatCurrency(($arBasket["PRICE"]), $arOrder["CURRENCY"], true);?></td>
								<td align="right"><?echo SaleFormatCurrency(((DoubleVal($arBasket["PRICE"]))*$arQuantities[$i]),$arOrder["CURRENCY"], true);?></td>
							</tr>
							<?
						}
					}
					if ($end_i == count($arBasketIDs))
					{
						?>
						<tr>
							<td align="right" colspan="4">
								Сумма:
							</td>
							<td align="right">
								<?echo SaleFormatCurrency($sum_price, $arOrder["CURRENCY"], true);?>
							</td>
						</tr>
						<!--<tr>
							<td align="right" colspan="4">
								Скидка:
							</td>
							<td align="right">
								<?echo SaleFormatCurrency($discount_price, $arOrder["CURRENCY"], true);?>
							</td>
						</tr>-->
						<?
						$sum_tax_val = 0;
						if (is_array($arTaxList) && count($arTaxList)>0):
							foreach ($arTaxList as $key => $val):
								if ($val["IS_IN_PRICE"]!="Y")
								{
									$sum_tax_val += $val["TAX_SUM"];
								}
								?>
								<tr>
									<td align="right" colspan="4">
										<?
										if ($val["IS_IN_PRICE"]=="Y")
										{
											echo "В том числе ";
										}
										echo htmlspecialcharsbx($val["TAX_NAME"]); 
										if ($val["IS_PERCENT"]=="Y")
										{
											echo " (".DoubleVal($val["VALUE"])."%)";
										}
										?>:
									</td>
									<td align="right">
										<?echo SaleFormatCurrency($val["TAX_SUM"], $arOrder["CURRENCY"], true) ?>
									</td>
								</tr>
								<?
							endforeach;
						endif;
						?>
						<tr>
							<td align="right" colspan="4">
								Итого (без стоимости доставки):
							</td>
							<td align="right">
								<?echo SaleFormatCurrency(($arOrder["PRICE"] - $arOrder["PRICE_DELIVERY"]), $arOrder["CURRENCY"], true);?>
								<? //echo SaleFormatCurrency(($sum_price+$sum_tax_val), $arOrder["CURRENCY"], true);?>
							</td>
						</tr>
						<?
					}
					?>
				</table>
				<?
			}
			?>
			<br>
		</td>
	</tr>
</table>

</body>
</html>