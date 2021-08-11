<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<ul style="margin-bottom:10px;">
    <?foreach($arResult["ITEMS"] as $cell=>$arElement):?>
	<li>
		<?if(strlen($arElement["DETAIL_TEXT"]) > 0):?>
			<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" style="font-weight:bold;"><?=$arElement["NAME"]?></a><br />
		<?else:?>
			<span style="font-weight:bold;"><?=$arElement["NAME"]?></span><br />
		<?endif;?>
		<?if(strlen($arElement["PREVIEW_TEXT"]) > 0):?>
			<span><?=$arElement["PREVIEW_TEXT"]?></span>
		<?endif?>
	</li>
  <?endforeach; // foreach($arResult["ITEMS"] as $arElement):?>
</ul>


