<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="slobel-social-icons">
	<?foreach($arResult['PROPERTIES']['SOCIAL'] as $keysoc => $valsoc):?>
		<noindex>
		<a style="width: <?=$arResult['PROPERTIES']['SIZE']?>" class="<?=$arResult['PROPERTIES']['POSITION']?>" target="_blank" href="<?=$valsoc['LINK']?>">
			<div>
          			<img width="100%" class="img-round nohover" src="<?=$valsoc['ICONS']?>">
          			<img width="100%" class="img-round hover" src="<?=$valsoc['ICONS_HOVER']?>">
          	</div>
        </a>
        </noindex>	
     <?endforeach;?>
</div>