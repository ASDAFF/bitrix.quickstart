<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if (count($arResult['GOODS'])>0) { ?>
	<div class="sidebox" id="topsellers">
		<h3 class="boxheader"><?=GetMessage('SE_TOPSELLERS_BLOCK_TITLE')?></h3>
		<ol class="sidelist sideproduct nclink" id="topsellerscontent">
			<? foreach ($arResult['GOODS'] as $key => $value) { ?>
				<li>
					<div>
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td rowspan="2" class="image">
									<a href="<?=$value['URL']?>" title="Купить <?=$value['NAME']?>">
										<img src="<?=$value['PICTURE']?>" alt="<?=GetMessage('SE_TOPLEVEL_BUY')?> <?=$value['NAME']?>" title="<?=GetMessage('SE_TOPLEVEL_BUY')?> <?=$value['NAME']?>" width="50" />
									</a>
								</td>
								<td class="item" valign="top"><a href="<?=$value['URL']?>" title="<?=$value['NAME']?>"><?=$value['NAME']?></a></td>
							</tr>
							<tr>
								<td class="pricebox" valign="bottom"><span class="pricenovat"><?=$value['PRICE']?></span></td>
							</tr>
						</table>
					</div>
				</li>
			<? } ?>
		</ol>
		<div class="boxfooter"></div>
	</div>
<? } ?>