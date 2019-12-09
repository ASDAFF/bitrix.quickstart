<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( !function_exists('RSGoPro_GetResult20') ) {
	function RSGoPro_GetResult20($amount,$arParams) {
		$return = 0;
		if($arParams['GOPRO_USE_MIN_AMOUNT']=='Y') {
			if( $amount<1 ) {
				$return = '<span class="empty">'.GetMessage('GOPRO_QUANTITY_EMPTY').'</span>';
			} elseif( $amount<$arParams['MIN_AMOUNT'] ) {
				$return = GetMessage('GOPRO_QUANTITY_LOW');
			} else {
				$return = '<span class="isset">'.GetMessage('GOPRO_QUANTITY_ISSET').'</span>';
			}
		} else {
			$return = $amount;
		}
		return $return;
	}
}

?><div class="stores gopro_20" data-firstElement="<?=$arParams['FIRST_ELEMENT_ID']?>"><?
	?><span><?
		?><?=$arParams['MAIN_TITLE']?><?
		if( is_array($arResult['JS']['SKU']) && count($arResult['JS']['SKU'])>1 ) {
			?><a class="genamount<?if(count($arResult['STORES'])<1 || $arParams['SHOW_GENERAL_STORE_INFORMATION']=='Y'):?> cantopen<?endif;?>" href="#popupstores_<?=$arParams['~ELEMENT_ID']?>" title="<?=$arParams['MAIN_TITLE']?>"><?
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
				?><i class="icon pngicons"></i><?
			?></a><?
			if($arParams['SHOW_GENERAL_STORE_INFORMATION']!='Y') {
				?><div class="popupstores noned" id="popupstores_<?=$arParams['~ELEMENT_ID']?>"><?
					?><table><?
						foreach($arResult['STORES'] as $key1 => $arStore) {
							?><tr class="store_<?=$arStore['ID']?>" style="display:<?=($arParams['SHOW_EMPTY_STORE'] == 'N' && $arResult['JS']['SKU'][$arParams['FIRST_ELEMENT_ID']][$arStore['ID']] <= 0 ? 'none' : '')?>;"><?
								if( in_array('TITLE',$arParams['FIELDS'])) {
									?><td class="title"><?=$arStore['TITLE']?></td><?
								}
								if( in_array('PHONE',$arParams['FIELDS'])) {
									?><td><?=$arStore['PHONE']?></td><?
								}
								if( in_array('SCHEDULE',$arParams['FIELDS'])) {
									?><td><?=$arStore['SCHEDULE']?></td><?
								}
								if( in_array('EMAIL',$arParams['FIELDS'])) {
									?><td><?=$arStore['EMAIL']?></td><?
								}
								if( in_array('COORDINATES',$arParams['FIELDS'])) {
									?><td><?=$arStore['COORDINATES']?></td><?
								}
								if( in_array('DESCRIPTION',$arParams['FIELDS'])) {
									?><td><?=$arStore['DESCRIPTION']?></td><?
								}
								?><td class="amount"><?=RSGoPro_GetResult20($arResult['JS']['SKU'][$arParams['FIRST_ELEMENT_ID']][$arStore['ID']],$arParams)?></td><?
							?></tr><?
						}
					?></table><?
				?></div><?
			}
		} else {
			?><a class="genamount<?if(count($arResult['STORES'])<1 || $arParams['SHOW_GENERAL_STORE_INFORMATION']=='Y'):?> cantopen<?endif;?>" href="#popupstores_<?=$arParams['~ELEMENT_ID']?>" title="<?=$arParams['MAIN_TITLE']?>"><?
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
			if($arParams['SHOW_GENERAL_STORE_INFORMATION']!='Y') {
				?><div class="popupstores noned" id="popupstores_<?=$arParams['~ELEMENT_ID']?>"><?
					?><table><?
						foreach($arResult['STORES'] as $key1 => $arStore) {
							?><tr class="store_<?=$arStore['ID']?>" style="display:<?=($arParams['SHOW_EMPTY_STORE'] == 'N' && $arStore['AMOUNT'] <= 0 ? 'none' : '')?>;"><?
								if( in_array('TITLE',$arParams['FIELDS'])) {
									?><td class="title"><?=$arStore['TITLE']?></td><?
								}
								if( in_array('PHONE',$arParams['FIELDS'])) {
									?><td><?=$arStore['PHONE']?></td><?
								}
								if( in_array('SCHEDULE',$arParams['FIELDS'])) {
									?><td><?=$arStore['SCHEDULE']?></td><?
								}
								if( in_array('EMAIL',$arParams['FIELDS'])) {
									?><td><?=$arStore['EMAIL']?></td><?
								}
								if( in_array('COORDINATES',$arParams['FIELDS'])) {
									?><td><?=$arStore['COORDINATES']?></td><?
								}
								if( in_array('DESCRIPTION',$arParams['FIELDS'])) {
									?><td><?=$arStore['DESCRIPTION']?></td><?
								}
								?><td class="amount"><?=RSGoPro_GetResult20($arStore['AMOUNT'],$arParams)?></td><?
							?></tr><?
						}
					?></table><?
				?></div><?
			}
		}
	?></span><?
?></div><?

?><script>
RSGoPro_STOCK = {
	'<?=$arParams['~ELEMENT_ID']?>' : {
		'QUANTITY' : <?=json_encode($arParams['DATA_QUANTITY'])?>,
		'JS' : <?=CUtil::PhpToJSObject( $arResult['JS'] )?>,
		'USE_MIN_AMOUNT' : <?=( $arParams['GOPRO_USE_MIN_AMOUNT']=='Y' ? 'true' : 'false' )?>,
		'MIN_AMOUNT' : <?=(IntVal($arParams['MIN_AMOUNT'])>0 ? $arParams['MIN_AMOUNT'] : 0 )?>,
		'MESSAGE_ISSET' : <?=CUtil::PhpToJSObject( GetMessage('GOPRO_QUANTITY_ISSET') )?>,
		'MESSAGE_LOW' : <?=CUtil::PhpToJSObject( GetMessage('GOPRO_QUANTITY_LOW') )?>,
		'MESSAGE_EMPTY' : <?=CUtil::PhpToJSObject( GetMessage('GOPRO_QUANTITY_EMPTY') )?>,
		'SHOW_EMPTY_STORE' : <?=( $arParams['SHOW_EMPTY_STORE']=='Y' ? 'true' : 'false' )?>
	}
};
</script>