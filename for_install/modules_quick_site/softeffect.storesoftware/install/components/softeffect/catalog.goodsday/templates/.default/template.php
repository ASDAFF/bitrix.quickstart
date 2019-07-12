<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$i=0;
foreach ($arResult["SECTIONS"] as $item) { ?>
	<div class="featured<? if ($i==0) { ?> featuredspacer<? $i++; } else { $i=0; } ?>">
		<div class="boxheader nobgp"><?=htmlspecialcharsback($item['NAME_CAT'])?></div>
		<div class="content">
			<div class="image">
				<a title="<?=$item['NAME']?>" href="<?=$item['URL']?>">
				<img alt="<?=$item['NAME']?>" src="<?=$item['PIC']?>"></a>
			</div>
			<div class="about">
				<div class="heighthere">
					<h3 class="item"><a title="Купить <?=htmlspecialcharsback($item['NAME'])?>" href="<?=htmlspecialcharsback($item['URL'])?>">
					<?=htmlspecialcharsback($item['NAME'])?></a></h3>
					<? if ($item['DELIVERY_TIME']>0) { ?>
						<p style="color:#383838!important"> <?=GetMessage('SE_GOODSDAY_DELIVERY')?>:  <?=$item['DELIVERY_TIME']?></p>
					<? } ?>
					<div class="pricebox">
						<?if ($item['OLD_PRICE']!=$item['PRICE']) {?>
							<del><b><font size="1"><?=SaleFormatCurrency($item['OLD_PRICE'], "RUB");?></font></b></del>
						<?}?>
						
						<?if ($item['DISCOUNT']) {?>
							&nbsp; <?=GetMessage('SE_GOODSDAY_DISCOUNT')?> - <b><font size="1">  <?=$item['DISCOUNT']?> %</font></b><br>
						<?}?>
						<span class="pricenovat_detail"><?=SaleFormatCurrency($item['PRICE'], "RUB");?> </span>
					</div>
					<br clear="both" />
				</div>
				<div class="controls noprint">
					<form name="AddToBasket" action="<?=SITE_DIR?>basket/" method="post" enctype="multipart/form-data">
						<table cellspacing="0" cellpadding="0" width="100%">
							<col />
							<col width="60" />
							<tbody>
								<tr>
									<td>
										<? if ($item['CATEGORY_NAME']!='' && $item['CATEGORY_LINK']!='') { ?>
											<a style="color:grey!important" title="<?=$item['CATEGORY_NAME']?>" href="<?=htmlspecialcharsback($item['CATEGORY_LINK'])?>"><?=htmlspecialcharsback($item['CATEGORY_NAME'])?> &raquo;</a>
										<? } ?>
									</td>
									<td style="padding-right: 3px;">
										<input type="submit" name="add" class="btn do pt-1 pb-1 pl-10 pr-10" alt="Купить <?=htmlspecialcharsback($item['NAME'])?>" title="<?=htmlspecialcharsback($item['NAME'])?>" value="<?=GEtMessage('SE_GOODSDAY_BUY')?>"<? if ($item['PRICE']<=0) { ?> disabled="true"<? } ?> />
										<input type="hidden" name="ID" value="<?=$item['ID']?>" />
										<input type="hidden" name="action" value="add" />
										<input type="hidden" name="QTY" value="1" />
									</td>
								</tr>
							</tbody>
						</table>
					</form>
				</div>
			</div>
		</div>
		<div class="clearfloat boxfooter"></div>
	</div>
<? } ?>
<script type="text/javascript">
	var maxHeight=0;
	var i=1;
	$('.heighthere').each(function (num) {
		if ($(this).height()>maxHeight) maxHeight = $(this).height();
		if (i==2) {
			$('.heighthere').eq(num).css('height', maxHeight);
			$('.heighthere').eq(num-1).css('height', maxHeight);
			i=1;
			maxHeight=0;
		} else {
			i++;
		}
	});
</script>
<br clear="both" />