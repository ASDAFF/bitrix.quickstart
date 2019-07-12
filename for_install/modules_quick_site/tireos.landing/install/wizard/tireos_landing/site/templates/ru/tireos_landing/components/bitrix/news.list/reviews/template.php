<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="container">
	<ul class="aboutUs-slider unstyled">
<?
foreach($arResult["ITEMS"] as $arItem):?>
	<li>
    	<figure class="thumbnail"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>"></figure>
			<div class="quote"><?=$arItem["PREVIEW_TEXT"];?></div>
            <? if ($arItem["PROPERTIES"]["AUTHOR"]["VALUE"]){?>
				<span class="author"><? echo $arItem["PROPERTIES"]["AUTHOR"]["VALUE"]?></span>
                <?}?> 
	</li>

<?endforeach?>
	</ul>
    
</div>
