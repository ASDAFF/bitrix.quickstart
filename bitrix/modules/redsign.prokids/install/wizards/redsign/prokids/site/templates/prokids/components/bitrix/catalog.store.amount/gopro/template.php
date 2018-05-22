<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="stores"><?

if(isset($arResult['IS_SKU']) && $arResult['IS_SKU'] == 1) {
	?><span><?
		$FirstWasHere = false;
		if(count($arResult['SKU'])>0) {
			foreach($arResult['SKU'] as $arStore) {
				if( $arParams['FIRST_ELEMENT_ID']==$arStore['ELEMENT_ID'] ) { $FirstWasHere = true; break; }
			}
		}
		?><?=$arParams['MAIN_TITLE']?><?
		?><a class="genamount<?if(!$FirstWasHere):?> cantopen<?endif;?>" href="#popupstores_<?=$arParams['~ELEMENT_ID']?>" title="<?=$arParams['MAIN_TITLE']?>"><?
			if($arParams['GOPRO_USE_MIN_AMOUNT']=='Y') {
				if( $arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']]<1 ) {
					?><span style="color:#ff0000;"><?=GetMessage('GOPRO_QUANTITY_EMPTY')?></span><?
				} elseif( $arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']]<$arParams['MIN_AMOUNT'] ) {
					?><span><?=GetMessage('GOPRO_QUANTITY_LOW')?></span><?
				} else {
					?><span style="color:#00cc00;"><?=GetMessage('GOPRO_QUANTITY_ISSET')?></span><?
				}
			} else {
				?><span><?=$arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']]?></span><?
			}
		?><i class="icon pngicons"></i></a><?
		if(count($arResult['SKU'])>0) {
			?><div class="popupstores noned" id="popupstores_<?=$arParams['~ELEMENT_ID']?>"><?
				?><table><?
					foreach($arResult['SKU'] as $arStore) {
						?><tr class="offerstore offer_<?=$arStore['ELEMENT_ID']?>"<?
							if( $arParams['FIRST_ELEMENT_ID']!=$arStore['ELEMENT_ID'] ) { ?> style="display:none;"<? } else { $FirstWasHere = true; }
							?>><?
							?><td class="title"><?=$arStore['TITLE']?></td><?
							?><td class="amount"><?=$arStore['RESULT']?></td><?
						?></tr><?
					}
				?></table><?
			?></div><?
		}
	?></span><?
} else {
	?><span><?
		?><?=$arParams['MAIN_TITLE']?><?
		?><a class="genamount<?if(count($arResult['STORES'])<1):?> cantopen<?endif;?>" href="#popupstores_<?=$arParams['~ELEMENT_ID']?>" title="<?=$arParams['MAIN_TITLE']?>"><?
			if($arParams['GOPRO_USE_MIN_AMOUNT']=='Y') {
				if( $arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']]<1 ) {
					?><span style="color:#ff0000;"><?=GetMessage('GOPRO_QUANTITY_EMPTY')?></span><?
				} elseif( $arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']]<$arParams['MIN_AMOUNT'] ){
					?><span><?=GetMessage('GOPRO_QUANTITY_LOW')?></span><?
				} else {
					?><span style="color:#00cc00;"><?=GetMessage('GOPRO_QUANTITY_ISSET')?></span><?
				}
			} else {
				?><span><?=$arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']]?></span><?
			}
		?><i class="icon pngicons"></i></a><?
		if(count($arResult['STORES'])>0) {
			?><div class="popupstores noned" id="popupstores_<?=$arParams['~ELEMENT_ID']?>"><?
				?><table><?
					foreach($arResult['STORES'] as $arStore) {
						?><tr><?
							?><td class="title"><?=$arStore['TITLE']?></td><?
							?><td class="amount"><?=$arStore['RESULT']?></td><?
						?></tr><?
					}
				?></table><?
			?></div><?
		}
	?></span><?
}
?></div><?

?><script>
RSGoPro_STOCK = {
	'<?=$arParams['~ELEMENT_ID']?>' : {
		'QUANTITY' : <?=json_encode($arParams['DATA_QUANTITY'])?>,
		'USE_MIN_AMOUNT' : <?=( $arParams['GOPRO_GOPRO_USE_MIN_AMOUNT']=='Y' ? 'true' : 'false' )?>,
		'MIN_AMOUNT' : <?=(IntVal($arParams['MIN_AMOUNT'])>0 ? $arParams['MIN_AMOUNT'] : 0 )?>,
		'MESSAGE_ISSET' : <?=CUtil::PhpToJSObject(GetMessage('GOPRO_QUANTITY_ISSET'))?>,
		'MESSAGE_LOW' : <?=CUtil::PhpToJSObject(GetMessage('GOPRO_QUANTITY_LOW'))?>,
		'MESSAGE_EMPTY' : <?=CUtil::PhpToJSObject(GetMessage('GOPRO_QUANTITY_EMPTY'))?>
	}
};
</script>