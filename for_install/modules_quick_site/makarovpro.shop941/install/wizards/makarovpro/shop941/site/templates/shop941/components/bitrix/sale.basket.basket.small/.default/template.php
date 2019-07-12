<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arResult["READY"]=="Y" || $arResult["DELAY"]=="Y" || $arResult["NOTAVAIL"]=="Y" || $arResult["SUBSCRIBE"]=="Y"):?>
	

<table class="sale_basket_small">
	<?if ($arResult["READY"]=="Y"):?>
		<tr>
			<td align="center"><?= GetMessage("TSBS_READY") ?></td>
		</tr>
		<?
		foreach ($arResult["ITEMS"] as $v)
		{
			if ($v["DELAY"]=="N" && $v["CAN_BUY"]=="Y")
			{
				?>
				<tr>
					<td><li>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								<a href="<?echo $v["DETAIL_PAGE_URL"] ?>">
							<?endif;?>
							<b><?echo $v["NAME"]?></b>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								</a>
							<?endif;?>
							<br />
							<?= GetMessage("TSBS_PRICE") ?>&nbsp;<b><?echo $v["PRICE_FORMATED"]?></b><br />
							<?= GetMessage("TSBS_QUANTITY") ?>&nbsp;<?echo $v["QUANTITY"]?>
					</li></td>
				</tr>
				<?
			}
		}
		?>
		<?if (strlen($arParams["PATH_TO_BASKET"])>0):?>
			<tr>
				<td align="center">
					<form method="get" action="<?=$arParams["PATH_TO_BASKET"]?>">
						<input type="submit" value="<?= GetMessage("TSBS_2BASKET") ?>">
					</form>
				</td>
			</tr>
		<?endif;?>
		<?if (strlen($arParams["PATH_TO_ORDER"])>0):?>
			<tr>
				<td align="center">
					<form method="get" action="<?= $arParams["PATH_TO_ORDER"] ?>">
						<input type="submit" value="<?= GetMessage("TSBS_2ORDER") ?>">
					</form>
				</td>
			</tr>
		<?endif;?>
	<?endif;?>
	<?if ($arResult["DELAY"]=="Y"):?>
		<tr>
			<td align="center"><?= GetMessage("TSBS_DELAY") ?></td>
		</tr>
		<tr>
		<?
		foreach ($arResult["ITEMS"] as $v)
		{
			if ($v["DELAY"]=="Y" && $v["CAN_BUY"]=="Y")
			{
				?>
				<tr>
					<td>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								<a href="<?echo $v["DETAIL_PAGE_URL"] ?>">
							<?endif;?>
							<b><?echo $v["NAME"]?></b>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								</a>
							<?endif;?>
							<br />
							<?= GetMessage("TSBS_PRICE") ?>&nbsp;<b><?echo $v["PRICE_FORMATED"]?></b><br />
							<?= GetMessage("TSBS_QUANTITY") ?>&nbsp;<?echo $v["QUANTITY"]?>
					</td>
				</tr>
				<?
			}
		}
		?>
		<?if (strlen($arParams["PATH_TO_BASKET"])>0):?>
			<tr>
				<td>
					<form method="get" action="<?=$arParams["PATH_TO_BASKET"]?>">
						<input type="submit" value="<?= GetMessage("TSBS_2BASKET") ?>">
					</form>
				</td>
			</tr>
		<?endif;?>
	<?endif;?>
	
	<?if ($arResult["SUBSCRIBE"]=="Y"):?>
		<tr>
			<td align="center"><?= GetMessage("TSBS_SUBSCRIBE") ?></td>
		</tr>
		<?
		foreach ($arResult["ITEMS"] as $v)
		{
			if ($v["CAN_BUY"]=="N" && $v["SUBSCRIBE"]=="Y")
			{
				?>
				<tr>
					<td>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								<a href="<?echo $v["DETAIL_PAGE_URL"] ?>">
							<?endif;?>
							<b><?echo $v["NAME"]?></b>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								</a>
							<?endif;?>
					</td>
				</tr>
				</tr>
				<?
			}
		}
		?>
	<?endif;?>
	
	<?if ($arResult["NOTAVAIL"]=="Y"):?>
		<tr>
			<td align="center"><?= GetMessage("TSBS_UNAVAIL") ?></td>
		</tr>
		<?
		foreach ($arResult["ITEMS"] as $v)
		{
			if ($v["CAN_BUY"]=="N" && $v["SUBSCRIBE"]=="N")
			{
				?>
				<tr>
					<td>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								<a href="<?echo $v["DETAIL_PAGE_URL"] ?>">
							<?endif;?>
							<b><?echo $v["NAME"]?></b>
							<?if (strlen($v["DETAIL_PAGE_URL"])>0):?>
								</a>
							<?endif;?>
							<br />
							<?= GetMessage("TSBS_PRICE") ?>&nbsp;<b><?echo $v["PRICE_FORMATED"]?></b><br />
							<?= GetMessage("TSBS_QUANTITY") ?>&nbsp;<?echo $v["QUANTITY"]?>
					</td>
				</tr>
				</tr>
				<?
			}
		}
		?>
	<?endif;?>
	</table>
<?endif;?>
