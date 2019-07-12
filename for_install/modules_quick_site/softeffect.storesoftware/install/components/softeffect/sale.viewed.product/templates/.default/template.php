<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="sidebox boxg" id="recently">
	<h3 class="boxheader">
		<? if (count($arResult['VIEWED'])>0) { ?><span class="sidenavbtn" id="clear_recently"><a href="?clear_viewed_products=Y"><img width="9" height="9" class="noprint" alt="X" src="<?=SITE_DIR?>images/buttons/btn_clear.gif"></a></span><? } ?>
		<?=GetMessage('SE_VIEWED_BACKVIEW')?><? if (count($arResult['VIEWED'])>0) { ?>&nbsp;&nbsp;&nbsp;<? } ?>
	</h3>
	<? if (count($arResult['VIEWED'])<=0) { ?>
		<p id="recentlycontent"><?=GetMessage('SE_VIEWED_BACKVIEW_NULL')?></p>
	<? } else { ?>
		<ol class="sidelist sidelistbull nclink" id="recentlycontent">
			<? foreach ($arResult['VIEWED'] as $item) {?>
				<li><div><a title="<?=$item['NAME']?>" href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a></div></li>
			<? } ?>
		</ol>
	<? } ?>
	<div class="boxfooter"></div>
</div>