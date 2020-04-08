<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!$arResult['NavShowAlways']) {
	if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && $arResult['NavShowAll'] == false)) {
		return;
	}
}

$strNavQueryString = $arResult['NavQueryString'] ? $arResult['NavQueryString'] . '&amp;' : '';
$strNavQueryStringFull = $arResult['NavQueryString'] ? '?' . $arResult['NavQueryString'] : '';
?>

<div class="system-pagenavigation system-pagenavigation-default">
	<?if ($arResult['NavTitle']) {
		?><div class="title">
			<?=$arResult['NavTitle']?> <?=$arResult['NavFirstRecordShow']?> <?=GetMessage('nav_to')?> <?=$arResult['NavLastRecordShow']?> <?=GetMessage('nav_of')?> <?=$arResult['NavRecordCount']?>
		</div><?
	}?>
	
	<ul class="pagination">
		<?if ($arResult['bDescPageNumbering']) {
			if ($arResult['NavPageNomer'] < $arResult['NavPageCount']) {
				if($arResult['bSavePage']) {
					?>
					<?/*<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=$arResult['NavPageCount']?>"><?=GetMessage('nav_begin')?></a></li>*/?>
					<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=($arResult['NavPageNomer'] + 1)?>"><?=GetMessage('nav_prev')?></a></li><?
				} else {
					?>
					<?/*<li><a href="<?=$arResult['sUrlPath']?><?=$strNavQueryStringFull?>"><?=GetMessage('nav_begin')?></a></li>*/?>
					<?if ($arResult['NavPageCount'] == $arResult['NavPageNomer'] + 1) {
						?><li><a href="<?=$arResult['sUrlPath']?><?=$strNavQueryStringFull?>"><?=GetMessage('nav_prev')?></a></li><?
					} else {
						?><li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=($arResult['NavPageNomer'] + 1)?>"><?=GetMessage('nav_prev')?></a></li><?
					}
				}
			} else {
				?>
				<?/*<li class="disabled"><span><?=GetMessage('nav_begin')?></span></li>*/?>
				<li class="disabled"><span><?=GetMessage('nav_prev')?></span></li>
				<?
			}
			
			while ($arResult['nStartPage'] >= $arResult['nEndPage']) {
				$isCurrent = $arResult['nStartPage'] == $arResult['NavPageNomer'];
				$NavRecordGroupPrint = $arResult['NavPageCount'] - $arResult['nStartPage'] + 1;
				?>
				<li class="number<?=$isCurrent ? ' active' : ''?>">
					<?
					if ($isCurrent) {
						?><?=$NavRecordGroupPrint?><?
					} elseif ($arResult['nStartPage'] == $arResult['NavPageCount'] && $arResult['bSavePage'] == false) {
						?><a href="<?=$arResult['sUrlPath']?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a><?
					} else {
						?><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=$arResult['nStartPage']?>"><?=$NavRecordGroupPrint?></a><?
					}
					?>
				</li>
				<?
				$arResult['nStartPage']--;
			}?>
			
			<?
			if ($arResult['NavPageNomer'] > 1) {
				?>
				<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=($arResult['NavPageNomer'] - 1)?>"><?=GetMessage('nav_next')?></a></li>
				<?/*<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=1"><?=GetMessage('nav_end')?></a></li>*/?>
				<?
			} else {
				?>
				<li class="disabled"><span><?=GetMessage('nav_next')?></span></li>
				<?/*<li class="disabled"><span><?=GetMessage('nav_end')?></span></li>*/?>
				<?
			}
		} else {
			if ($arResult['NavPageNomer'] > 1) {
				if($arResult['bSavePage']) {
					?>
					<?/*<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=1"><?=GetMessage('nav_begin')?></a></li>*/?>
					<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=($arResult['NavPageNomer'] - 1)?>"><?=GetMessage('nav_prev')?></a></li>
					<?
				} else {
					?>
					<?/*<li><a href="<?=$arResult['sUrlPath']?><?=$strNavQueryStringFull?>"><?=GetMessage('nav_begin')?></a></li>*/?>
					<li>
						<?
						if ($arResult['NavPageNomer'] > 2) {
							?><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=($arResult['NavPageNomer'] - 1)?>"><?=GetMessage('nav_prev')?></a><?
						} else {
							?><a href="<?=$arResult['sUrlPath']?><?=$strNavQueryStringFull?>"><?=GetMessage('nav_prev')?></a><?
						}?>
					</li>
					<?
				}
			} else {
				?>
				<?/*<li class="disabled"><span><?=GetMessage('nav_begin')?></span></li>*/?>
				<li class="disabled"><span><?=GetMessage('nav_prev')?></span></li>
				<?
			}
			
			while ($arResult['nStartPage'] <= $arResult['nEndPage']) {
				$isCurrent = $arResult['nStartPage'] == $arResult['NavPageNomer'];
				?>
				<li class="number<?=$isCurrent ? ' active' : ''?>">
					<?if ($isCurrent) {
						?><span><?=$arResult['nStartPage']?></span><?
					} elseif ($arResult['nStartPage'] == 1 && $arResult['bSavePage'] == false) {
						?><a href="<?=$arResult['sUrlPath']?><?=$strNavQueryStringFull?>"><?=$arResult['nStartPage']?></a><?
					} else {
						?><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=$arResult['nStartPage']?>"><?=$arResult['nStartPage']?></a><?
					}
					?>
				</li>
				<?
				$arResult['nStartPage']++;
			}
			
			if ($arResult['NavPageNomer'] < $arResult['NavPageCount']) {
				?>
				<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=($arResult['NavPageNomer'] + 1)?>"><?=GetMessage('nav_next')?></a></li>
				<?/*<li><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>PAGEN_<?=$arResult['NavNum']?>=<?=$arResult['NavPageCount']?>"><?=GetMessage('nav_end')?></a></li>*/?>
				<?
			} else {
				?>
				<li class="disabled"><span><?=GetMessage('nav_next')?></span></li>
				<?/*<li class="disabled"><span><?=GetMessage('nav_end')?></span></li>*/?>
				<?
			}
		}
		
		if ($arResult['bShowAll']) {
			?>
			<li>
				<?if ($arResult['NavShowAll']) {
					?><a href="<?=$arResult['sUrlPath']?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult['NavNum']?>=0" rel="nofollow"><?=GetMessage('nav_paged')?></a><?
				} else {
					?><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult['NavNum']?>=1" rel="nofollow"><?=GetMessage('nav_all')?></a><?
				}?>
			</li>
			<?
		}?>
	</ul>
</div>