<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (method_exists($this, 'setFrameMode')) {
	$this->setFrameMode(true);
}
?>

<?if (!empty($arResult)):?>
<style type="text/css">
	.asd_slider_window {
		height:<?= $arResult[0]['PICTURE']['HEIGHT']?>px;width:<?= $arResult[0]['PICTURE']['WIDTH']?>px;
	}
	#asd_slider_overtext {
		width: <?= $arResult[0]['PICTURE']['WIDTH']?>px;
	}
	#asd_slider_overtext .asd_slider_title {
		width: <?= $arResult[0]['PICTURE']['WIDTH']-10?>px;
	}
	#asd_slider_overtext .asd_slider_des {
		width: <?= $arResult[0]['PICTURE']['WIDTH']-10?>px;
	}
</style>
<script type="text/javascript">
	var timerSpeed = <?= $arParams['TIME']*1000?>;
</script>

<div class="asd_slider_main_view">
	<div class="asd_slider_window">
		<div class="asd_slider_image_reel">
			<?foreach ($arResult as $i => $arItem):?>
				<?if (strlen($arItem['DETAIL_PAGE_URL'])):?>
			<a href="<?= $arItem['DETAIL_PAGE_URL']?>" title="<?= $arItem['NAME']?>"><img src="<?= $arItem['PICTURE']['SRC']?>" width="<?= $arItem['PICTURE']['WIDTH']?>" height="<?= $arItem['PICTURE']['HEIGHT']?>" alt="<?= $arItem['NAME']?>" title="<?= $arItem['NAME']?>" hspace="0" /></a>
				<?else:?>
				<img src="<?= $arItem['PICTURE']['SRC']?>" width="<?= $arItem['PICTURE']['WIDTH']?>" height="<?= $arItem['PICTURE']['HEIGHT']?>" alt="<?= $arItem['NAME']?>" title="<?= $arItem['NAME']?>" />
				<?endif;?>
				<div class="asd_slider_hidden" id="asd_slider_title_<?= $i?>"><?= $arItem['NAME']?></div>
				<div class="asd_slider_hidden" id="asd_slider_text_<?= $i?>"><?= $arItem['PREVIEW_TEXT']?></div>
				<div class="asd_slider_hidden" id="asd_slider_link_<?= $i?>"><?= $arItem['DETAIL_PAGE_URL']?></div>
			<?endforeach;?>
		</div>
		<?if ($arParams['SHOW_PREVIEW_TEXT'] == 'Y'):?>
		<div id="asd_slider_overtext">
			<a class="asd_slider_title" href="<?= $arResult[0]['DETAIL_PAGE_URL']?>"><?= $arResult[0]['NAME']?></a>
			<a class="asd_slider_des" href="<?= $arResult[0]['DETAIL_PAGE_URL']?>"><?= $arResult[0]['PREVIEW_TEXT']?></a>
		</div>
		<?endif;?>
	</div>
	<div id="asd_slider_paging">
		<?foreach ($arResult as $i => $arItem):?>
		<a href="#" rel="<?= $i+1?>"><?= $i+1?></a>
		<?endforeach;?>
	</div>
</div>
<br clear="all" />
<?endif;?>
