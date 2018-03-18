<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<a name="tb"></a>
<a href="<?=$arResult["URL_TO_LIST"]?>"><?=GetMessage("SPOD_RECORDS_LIST")?></a>
<br /><br />
<?if(strlen($arResult["ERROR_MESSAGE"])<=0):?>
	<table class="sale_personal_order_detail data-table">
	<tr>
		<th colspan="2" align="center"><b><?=GetMessage("SPOD_ORDER_NO")?>&nbsp;<?=$arResult["ID"]?>&nbsp;<?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT"] ?></b></th>
	</tr>
<!-- UnitellerPlugin change -->
	<tr>
		<td colspan="2" align="center">
<?php
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . ps_uniteller::UNITELLER_SALE_PATH . '/result.php')) {
			include($_SERVER['DOCUMENT_ROOT'] . ps_uniteller::UNITELLER_SALE_PATH . '/result.php');
		}
?>
		</td>
	</tr>
<!-- /UnitellerPlugin change -->

	<tr>
		<th colspan="2" align="center"><b><?=GetMessage("P_ORDER_BASKET")?></b></th>
	</tr>
	<tr>
		<td colspan="2">
			<table class="sale_personal_order_detail data-table">
				<tr>
					<th><?= GetMessage("SPOD_NAME") ?></th>
					<th><?= GetMessage("SPOD_PROPS") ?></th>
					<th><?= GetMessage("SPOD_DISCOUNT") ?></th>
					<th><?= GetMessage("SPOD_PRICETYPE") ?></th>
					<th><?= GetMessage("SPOD_QUANTITY") ?></th>
					<th><?= GetMessage("SPOD_PRICE") ?></th>
				</tr>
				<?
				foreach($arResult["BASKET"] as $val)
				{
					?>
					<tr>
						<td><?
							if (strlen($val["DETAIL_PAGE_URL"])>0)
								echo "<a href=\"".$val["DETAIL_PAGE_URL"]."\">";
							echo htmlspecialcharsEx($val["NAME"]);
							if (strlen($val["DETAIL_PAGE_URL"])>0)
								echo "</a>";
							?></td>
						<td> <?
							if(!empty($val["PROPS"])):?>
								<table cellspacing="0">
								<?
								foreach($val["PROPS"] as $vv)
								{
										?>
										<tr>
											<td style="border:0px;"><?=$vv["NAME"]?>:</td>
											<td style="border:0px;"><?=$vv["VALUE"]?></td>
										</tr>
										<?
								}
								?>
								</table>
							<?endif;?></td>
						<td><?=$val["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td>
						<td><?=$val["NOTES"]?></td>
						<td><?=$val["QUANTITY"]?></td>
						<td align="right"><?=$val["PRICE_FORMATED"]?></td>
					</tr>
					<?
				}
				?>
				<?if(strlen($arResult["DISCOUNT_VALUE_FORMATED"]) > 0):?>
				<tr>
					<td align="right"><b><?=GetMessage("SPOD_DISCOUNT")?>:</b></td>
					<td align="right" colspan="5"><?=$arResult["DISCOUNT_VALUE_FORMATED"]?></td>
				</tr>
				<?endif;?>
				<?
				foreach($arResult["TAX_LIST"] as $val)
				{
					?>
					<tr>
						<td align="right"><?
							echo $val["TAX_NAME"];
							echo $val["VALUE_FORMATED"];
							?>:</td>
						<td align="right" colspan="5"><?=$val["VALUE_MONEY_FORMATED"]?></td>
					</tr>
					<?
				}
				?>
				<?if(strlen($arResult["TAX_VALUE_FORMATED"]) > 0):?>
				<tr>
					<td align="right"><b><?=GetMessage("SPOD_TAX")?>:</b></td>
					<td align="right" colspan="5"><?=$arResult["TAX_VALUE_FORMATED"]?></td>
				</tr>
				<?endif;?>
				<?if(strlen($arResult["PRICE_DELIVERY_FORMATED"]) > 0):?>
				<tr>
					<td align="right"><b><?=GetMessage("SPOD_DELIVERY")?>:</b></td>
					<td align="right" colspan="5"><?=$arResult["PRICE_DELIVERY_FORMATED"]?></td>
				</tr>
				<?endif;?>
				<tr>
					<td align="right"><b><?=GetMessage("SPOD_ITOG")?>:</b></td>
					<td align="right" colspan="5"><?=$arResult["PRICE_FORMATED"]?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?else:?>
	<?=ShowError($arResult["ERROR_MESSAGE"]);?>
<?endif;?>
