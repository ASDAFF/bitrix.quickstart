<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="messenger">
	<?foreach($arResult['PROPERTIES']['MESSENGER'] as $keyMessenger => $valueMessenger):?>
		<noindex>
		<a style="width: <?=$arResult['PROPERTIES']['SIZE']?>" class="<?=$arResult['PROPERTIES']['POSITION']?>" title="<?=$valueMessenger['TITLE']?>" href="<?=$valueMessenger['PROTOCOLE'].$valueMessenger['TITLE']?>" rel="nofollow">
			<div>
          			<img width="100%" class="img-round" src="<?=$valueMessenger['ICONS']?>">
          	</div>
        </a>
        </noindex>	
     <?endforeach;?>
</div>