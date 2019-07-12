<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="gallery">
<? foreach ($arResult['ROWS'] as $arRow): ?>
	<? foreach ($arRow as $arItem): ?>
		<? if (is_array($arItem['PREVIEW_PICTURE'])): ?>
			<a href="<?=$arItem['DETAIL_PICTURE']['SRC']?>"  title="Увеличить"><img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>"/></a>
		<? endif ?>
	<? endforeach ?>
<? endforeach ?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
	/*Инициализация галереи*/
		$(".gallery").wGallery({  
			bNext: "<?=SITE_TEMPLATE_PATH?>/images/webrover-gallery-button-next.gif",
			bPrev: "<?=SITE_TEMPLATE_PATH?>/images/webrover-gallery-button-prev.gif",
			bClose: "<?=SITE_TEMPLATE_PATH?>/images/webrover-gallery-button-close.gif",
			wgTitle: "<?=GetMessage('GALLERY_TITLE')?>",
			srcPreloader: "<?=SITE_TEMPLATE_PATH?>/images/webrover-gallery-preloader.gif"
		});
	/*\Инициализация галереи*/
	});
</script>
