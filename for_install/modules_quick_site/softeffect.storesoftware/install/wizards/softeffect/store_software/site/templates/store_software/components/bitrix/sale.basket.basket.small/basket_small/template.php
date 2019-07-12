<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? //var_dump($arResult); ?>
<div class="sidebox<? if ($arResult["READY"]!="Y") { ?> boxg<? } ?>" id="quickbasket">
	<h3 class="boxheader"><a href="#SITE_DIR#basket/" title="My Basket" rel="noindex, nofollow" tabindex="31"><?=GetMessage('TSBS_BLOCK_TITLE');?></a></h3>
	<div id="quickbasketcontent">
		<? if ($arResult["READY"]=="Y") { ?>
		<ul class="sidecart nclink">
			<? foreach ($arResult["ITEMS"] as $v) {
				if ($v["DELAY"]=="N" && $v["CAN_BUY"]=="Y") { ?>
					<li>
						<div class="remove">
							<a tabindex="32" title="Remove this item" href="#SITE_DIR#basket/?action=delete&ID=<?=$v["ID"]?>">
								<img width="9" height="9" alt="Remove" src="#SITE_DIR#images/bullets/bullet_delete.gif">
							</a>
						</div>
						<div class="item">
							<strong><?=intval($v["QUANTITY"])?></strong>&nbsp;x&nbsp;<a title="<?=$v["NAME"]?>" href="<?=$v["DETAIL_PAGE_URL"]?>"><?=$v["NAME"]?></a><br><span class="pricenovat"><?=$v["PRICE_FORMATED"]?></span>
						</div>
					</li>
					<?
					$allPrice += $v["PRICE"]*intval($v["QUANTITY"]);
				}
			}
			?>
			<li class="totals">
				<table width="150px" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td class="pricelabel"><?=GetMessage('TSBS_BLOCK_ALL');?>:</td>
							<td class="pricebox"><span class="pricenovat"><?=SaleFormatCurrency($allPrice, "RUB");?></span></td>
						</tr>
					</tbody>
				</table>
			</li>
		</ul>
		<div class="links">
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<?if (strlen($arParams["PATH_TO_BASKET"])>0):?>
					<td align="center">
						<?/*<form method="get" action="<?=$arParams["PATH_TO_BASKET"]?>">
							<input type="submit" value="<?= GetMessage("TSBS_2BASKET") ?>">
						</form>*/?>
						<a href="<?=$arParams["PATH_TO_BASKET"]?>" title="Перейти в корзину" rel="noindex, nofollow" tabindex="32"><img width="84" height="19" src="#SITE_DIR#images/buttons/quickbasket_viewbasket.gif" alt="<?=GetMessage('TSBS_TO_BASKET')?>"></a>
					</td>
					<?endif;?>
					<?if (strlen($arParams["PATH_TO_ORDER"])>0):?>
						<td align="center">
							<?/*<form method="get" action="<?= $arParams["PATH_TO_ORDER"] ?>">
								<input type="submit" value="<?= GetMessage("TSBS_2ORDER") ?>">
							</form>*/?>
							<a href="<?= $arParams["PATH_TO_ORDER"] ?>" title="Оформить заказ" rel="noindex, nofollow" tabindex="32"><img width="75" height="19" src="#SITE_DIR#images/buttons/quickbasket_checkout.gif" alt="<?=GetMessage('TSBS_TO_ORDER')?>"></a>
						</td>
					<?endif;?>
				</tr>
			</table>
		</div>
		<? } else { ?>
			<p><?=GetMessage('TSBS_BLOCK_NULL');?></p>
		<? } ?>
	</div>
	<div class="boxfooter"></div>
</div>