<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//echo '<pre>';print_r($arResult);echo '</pre>';?>
<?if (is_array($arResult['DETAIL_PICTURE_350']) || count($arResult["MORE_PHOTO"])>0):?>
<script type="text/javascript">
	$(function(){
		$("a[rel=gal1]").fancybox({
			'transitionIn' : 'elastic',
			'transitionOut' : 'elastic'
		});
	})
</script>
<div class="gallery">
	<?if (is_array($arResult['DETAIL_PICTURE_350'])):?>
		<div class="big"><a title="<?=(strlen($arResult["DETAIL_PICTURE"]["DESCRIPTION"]) > 0 ? $arResult["DETAIL_PICTURE"]["DESCRIPTION"] : $arResult["NAME"])?>" rel="gal1" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>"><img src="<?=$arResult['DETAIL_PICTURE_350']['SRC']?>" alt="<?=$arResult["NAME"]?>"/></a></div>
	<?endif;?>
	<?if(count($arResult["MORE_PHOTO"])>0):?>
		<ul class="thumbs">
			<?foreach($arResult["MORE_PHOTO"] as $PHOTO):?>
				<li><a title="<?=(strlen($PHOTO["DESCRIPTION"]) > 0 ? $PHOTO["DESCRIPTION"] : $arResult["NAME"])?>" rel="gal1" href="<?=$PHOTO["SRC"]?>"><img src="<?=$PHOTO["SRC_PREVIEW"]?>" alt="<?=$arResult["NAME"]?>"/></a></li>
			<?endforeach;?>
		</ul>
	<?endif;?>
</div>
<?endif;?>
<?if(is_array($arResult["RESPONSIBLE"]) && !empty($arResult["RESPONSIBLE"])):?>
<div class="desc">
	<div class="inCharge">
		<h3><?=GetMessage("CATALOG_RESPONSIBLE")?>:</h3>
		<div class="person">
			<div class="personId">
				<?if(is_array($arResult["RESPONSIBLE"]["PERSONAL_PHOTO"])):?>
					<div class="personImg greyBorder"><img src="<?=$arResult["RESPONSIBLE"]["PERSONAL_PHOTO"]["SRC"]?>" alt=""/><div class="c tl"></div><div class="c tr"></div><div class="c bl"></div><div class="c br"></div></div>
				<?endif;?>
				<div class="personName"><?=$arResult["RESPONSIBLE"]["NAME"]?></div>
			</div>
			<div class="personNote">
            Email:<a href="mailto:<?=$arResult["RESPONSIBLE"]["MAIL"]?>"><?=$arResult["RESPONSIBLE"]["MAIL"]?></a>
            <br>
				<?=$arResult["RESPONSIBLE"]["TEXT"]?>
                
			</div>
		</div>
	</div>
</div>
<?endif;?>
<div class="classInfoTxt">
	<?=(!empty($arResult["DETAIL_TEXT"]))?$arResult["DETAIL_TEXT"]:$arResult["PREVIEW_TEXT"]?>
</div>