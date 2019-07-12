<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="row trainings" id='trainings'>

<?foreach($arResult["ITEMS"] as $i=>$arItem):?>

<? $img = getResizedImgById($arItem["PREVIEW_PICTURE"]["ID"], 130, 130); ?>

<div class="col-md-3 col-xs-6 hov<?=strval($i+1)?>">
	<figure class='thumbnails'>
		<i class='fa' style='background-image:url("<?=$img["src"]?>")'></i>
	</figure>
	<h4 class='xxsmall-h text-center transition-h'><?=$arItem["NAME"]?></h4>
	<div class="full-text">
		<?=$arItem["PREVIEW_TEXT"]?>
	</div>
</div>

<?endforeach;?>

</div>		