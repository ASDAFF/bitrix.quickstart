<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
if (!is_array($arOrder))
	$arOrder = CSaleOrder::GetByID($ORDER_ID);
?>
<? /*if ($USER->GetID()==32) {
	ob_start();
	var_dump($GLOBALS["SALE_INPUT_PARAMS"]);
	$text = ob_get_clean();
	ob_end_clean();
	file_put_contents($_SERVER['DOCUMENT_ROOT'].'/payment.txt', $text, FILE_APPEND);
}*/ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>����</title>
			<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET?>">
			<link type="text/css" rel="stylesheet" href="/css/bill_1c.css" media="all, screen" />
	</head>
	<body bgColor="#ffffff">
		<div id="invoice_conteiner">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<col />
				<col width="177" />
				<tr>
					<td style="font-size: 0.84em;">
						<span style="font-weight: bold; font-size: 100%; line-height: 1.2em;"><?= (CSalePaySystemAction::GetParamValue("SELLER_NAME")) ?></span><br />
						<?=(CSalePaySystemAction::GetParamValue("SELLER_ADDRESS")) ?><br />
						�������/����: <?= (CSalePaySystemAction::GetParamValue("SELLER_PHONE")) ?>
					</td>
					<td>
						<img src="/images/header_logo.jpg" alt="" width="177" height="55" class="noprint">
					</td>
				</tr>
			</table>
			<br />
			<table class="invoice_preview_account">
				<tbody>
					<tr>
						<td style="width: 25%;"><span class="label">���</span>  <?= (CSalePaySystemAction::GetParamValue("SELLER_INN")) ?></td>
						<td style="width: 25%;"><span class="label">���</span>  <?= (CSalePaySystemAction::GetParamValue("SELLER_KPP")) ?></td>
						<td style="width: 25%;" rowspan="2"><span class="label">��������� ����</span></td>
						
						<?var_dump()?>
						<td style="width: 25%;" rowspan="2"><?//= (CSalePaySystemAction::GetParamValue("SELLER_RS")) ?>40702810160290178601</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="label">����������</span><br /><?=(CSalePaySystemAction::GetParamValue("SELLER_NAME"))?>
						</td>
					</tr>
					<tr>
						<td colspan="2" rowspan="2"><span class="label">���� ����������</span><br>��� "�������������"  �. ������</td>
						<td><span class="label">���</span></td><td><?=(CSalePaySystemAction::GetParamValue("SELLER_BIK"))?></td>
					</tr>
					<tr>
						<td><span class="label">����. ����</span></td>
						<td><?= (CSalePaySystemAction::GetParamValue("SELLER_KS")) ?></td>
					</tr>
				</tbody>
			</table>
			<h3>���� � C-<?= $ORDER_ID ?> �� <?=convertDate(CSalePaySystemAction::GetParamValue("DATE_INSERT")) ?></h3>
			<table class="payers">
				<tbody>
					<tr>
						<td style="width: 135px;"><span class="label">����������</span></td>
						<td><?=(CSalePaySystemAction::GetParamValue("BUYER_NAME"))?></td>
					</tr>
					<tr>
						<td><span class="label">����������</span></td>
						<td><?=(CSalePaySystemAction::GetParamValue("SELLER_NAME"))?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<?
			$dbBasket = CSaleBasket::GetList(array("NAME" => "ASC"), array("ORDER_ID" => $ORDER_ID));
			if ($arBasket = $dbBasket->Fetch()): ?>
				<table class="invoice_preview_items">
					<tr bgcolor="#E2E2E2">
						<td align="center" style="border-right:none;">�</td>
						<td align="center" style="border-right:none;">������� �����</td>
						<td nowrap align="center" style="border-right:none;">���-��</td>
						<td nowrap align="center" style="border-right:none;">����, ���</td> 
						<td nowrap align="center" style=" ">�����, ���</td>
					</tr>
					<?
					$n = 1;
					$sum = 0.00;
					do { ?>
						<tr valign="top">
							<td  align="center" bgcolor="#ffffff" style="border-right:none; border-top:none;">
								<?= $n++ ?> 
							</td>
							<td bgcolor="#ffffff" style="border-right:none; border-top:none;">
								<?= ("&nbsp;".$arBasket["NAME"]); ?>
							</td>
							<td align="right" bgcolor="#ffffff" style="border-right:none; border-top:none;">
								<?= $arBasket["QUANTITY"]; ?>
							</td>
							<td align="right" bgcolor="#ffffff" style="border-right:none; border-top:none;">
								<?= SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"], true) ?>
							</td>
							<td align="right" bgcolor="#ffffff" style="border-top:none;">
								<?= SaleFormatCurrency(($arBasket["PRICE"])*$arBasket["QUANTITY"], $arBasket["CURRENCY"], true) ?>
							</td>
						</tr>
						<?
						$sum += doubleval(($arBasket["PRICE"])*$arBasket["QUANTITY"]);
					} while ($arBasket = $dbBasket->Fetch());
					?>
					<? if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"])>0): ?>
						<tr>
							<td bgcolor="#ffffff" style="border-right:none; border-top:none;">
								<?echo $n?>
							</td>
							<td bgcolor="#ffffff" style="border-right:none; border-top:none;">
								�������� <?
								$arDelivery_tmp = CSaleDelivery::GetByID($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DELIVERY_ID"]);
								echo ((strlen($arDelivery_tmp["NAME"])>0) ? "([".$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DELIVERY_ID"]."] " : "" );
								echo ($arDelivery_tmp["NAME"]);
								echo ((strlen($arDelivery_tmp["NAME"])>0) ? ")" : "" );
								?>
							</td>
							<td valign="top" align="right" bgcolor="#ffffff" style="border-right:none; border-top:none;">1 </td>
							<td align="right" bgcolor="#ffffff" style="border-right:none; border-top:none;">
								<?= SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?>
							</td>
							<td align="right" bgcolor="#ffffff" style="border-top:none;">
								<?= SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?>
							</td>
						</tr>
					<?endif?>
					<?
					$dbTaxList = CSaleOrderTax::GetList(array("APPLY_ORDER" => "ASC"), array("ORDER_ID"=>$ORDER_ID));
					while ($arTaxList = $dbTaxList->Fetch()) { ?>
						<tr>
							<td align="right" bgcolor="#ffffff" colspan="4" style="border-right:none; border-top:none;">
								<?
								if ($arTaxList["IS_IN_PRICE"]=="Y") {
									echo "� ��� ����� ";
								}
								echo ($arTaxList["TAX_NAME"]);
								if ($arTaxList["IS_PERCENT"]=="Y") {
									echo " (".$arTaxList["VALUE"]."%)";
								}
								?>:
							</td>
							<td align="right" bgcolor="#ffffff" style="border-top:none;">
								<?echo SaleFormatCurrency($arTaxList["VALUE_MONEY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true)?>
							</td>
						</tr>
						<?
					}
					
					if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"])>0) { ?>
						<tr>
							<td align="right" bgcolor="#ffffff" colspan="4" style="border-right:none; border-top:none;">��� ��������:</td>
							<td align="right" bgcolor="#ffffff" style="border-top:none;"><?= SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?></td>
						</tr>
						<?
					}
					
					if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"])>0) { ?>
						<tr>
							<td align="right" bgcolor="#ffffff" colspan="4" style="border-right:none; border-top:none;">������:</td>
							<td align="right" bgcolor="#ffffff" style="border-top:none;"><?= SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?></td>
						</tr>
						<?
					}
					?>
					<tr class="amount">
						<td class="label" align="right" bgcolor="#ffffff" colspan="4">�����</td>
						<td align="right" bgcolor="#ffffff" style="border-top:none;" nowrap><?=SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?></td>
					</tr>
					<tr class="amount">
						<td class="label" align="right" bgcolor="#ffffff" colspan="4" style="border-right:none; border-top:none;">��� ������ (���)</td>
						<td align="right" bgcolor="#ffffff" style="border-top:none;" nowrap>---</td>
					</tr>
					<tr class="amount">
						<td class="label" align="right" bgcolor="#ffffff" colspan="4" style="border-right:none; border-top:none;"><b>����� � ������</b></td>
						<td align="right" bgcolor="#ffffff" style="border-top:none;" nowrap><b><?=SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?></b></td>
					</tr>
				</table>
			<?endif?>
			<p>
				����� ������������ <?=$n-1?>, �� ����� <?=SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true) ?><br />
				<?
				$price=$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
				$priceR=roundEx($price, $prec=0);//������� ���������� �������
				?>
				<b>
					<? if ($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]=="RUR" || $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]=="RUB") {
						echo Number2Word_Rus($priceR);
					} else {
						echo SaleFormatCurrency($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"], $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]);
					}
					?>
				</b>
			</p>
			��� ���������� ��������� ��������� � ���� "���������� �������" ������ ��������� ����� ����� (� C-<?= $ORDER_ID ?> ).<br /><br />
			<b>������� ����������� ���������� ���������: <br /></b>
			<table class="invoice_preview_account">
				<tbody>
					<tr>
						<td colspan="2">
							<span class="label">���������� �������</span><br />
							������ �� ����� � C-<?= $ORDER_ID ?> �� <?= convertDate(CSalePaySystemAction::GetParamValue("DATE_INSERT")) ?><br />
							����� <?= $priceR ?><br />
							��� ������ (���)
						</td>
					</tr>
				</tbody>
			</table>
			<table class="signature"><tbody>
				<tr><td style="border: 0;"><img width="390" align="top" src="/images/bill/stamp.jpg" border="0" alt="" /></td></tr>
			</tbody></table>
			<small>
				��������� ������ ������ ����� ����������� ����� �� ��������� ���� ��������.<br>
				� ������ ������������� ������� �� ��������� ���� �������� � ������� ������
				���������� ���� �� ��� ������� �����, �������� ��������� �� ����� �����
				������������ ��������� ���� ������ � ������ ��������������� ��������� ����� �������
				� ��������� ���� �� �������.
			</small>
		</div>
	</body>
</html>