<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if( !function_exists('RSFLYAWAY_GetResult') ) {
	function RSFLYAWAY_GetResult($amount,$arParams) {
		$return = 0;
		if($arParams['FLYAWAY_USE_MIN_AMOUNT']=='Y') {
			if( $amount<1 ) {
				$return = '<span class="stores-icon"></span><span class="genamount empty">'.GetMessage('RS.FLYAWAY.QUANTITY_EMPTY').'</span>';
			} elseif( $amount<$arParams['MIN_AMOUNT'] ) {
				$return = '<span class="stores-icon stores-mal"></span><span class="genamount">'.GetMessage('RS.FLYAWAY.QUANTITY_LOW').'</span>';
			} else {
				$return = '<span class="stores-icon stores-full"></span><span class="genamount isset">'.GetMessage('RS.FLYAWAY.QUANTITY_ISSET').'</span>';
			}
		} else {
			$return = $amount;
		}
		return $return;
	}
}
?><span class="js-stores stores dropdown" data-firstElement="<?=$arParams['FIRST_ELEMENT_ID']?>"><?
	?><span><?
		if(count($arResult['STORES'])<1 || $arParams['SHOW_GENERAL_STORE_INFORMATION']=='Y'){
			?><span class="stores-label"><?=$arParams['MAIN_TITLE']?>:</span> <?
		} else {
			?><a class="dropdown-toggle" id="ddmStores_<?=$arParams['~ELEMENT_ID']?>" href="#" data-toggle="dropdown" title="<?=$arParams['MAIN_TITLE']?>" aria-expanded="true"><?
				?><?=$arParams['MAIN_TITLE']?><?
			?></a>: <?
		}
		if( is_array($arResult['JS']['SKU']) && count($arResult['JS']['SKU'])>1 ) {
			echo RSFLYAWAY_GetResult($arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']],$arParams);
			if($arParams['SHOW_GENERAL_STORE_INFORMATION']!='Y') {
				?><div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="ddmStores_<?=$arParams['~ELEMENT_ID']?>"><?
					?><div class="stores-table"><?
						foreach($arResult['STORES'] as $key1 => $arStore) {
							?><div class="store-item store_<?=$arStore['ID']?>" <?=($arParams['SHOW_EMPTY_STORE'] == 'N' && $arResult['JS']['SKU'][$arParams['FIRST_ELEMENT_ID']][$arStore['ID']] <= 0 ? 'style="display:none;"' : '')?>><?
								if( in_array('TITLE',$arParams['FIELDS'])) {
									?><div class="title"><?=$arStore['TITLE']?><span class="amount"><?=RSFLYAWAY_GetResult($arResult['JS']['SKU'][$arParams['FIRST_ELEMENT_ID']][$arStore['ID']],$arParams)?></span></div><?
								}
								if( in_array('PHONE',$arParams['FIELDS'])) {
									?><div class="phone"><?=$arStore['PHONE']?></div><?
								}
								if( in_array('SCHEDULE',$arParams['FIELDS'])) {
									?><div class="schedule"><?=$arStore['SCHEDULE']?></div><?
								}
								if( in_array('EMAIL',$arParams['FIELDS'])) {
									?><div class="mail"><?=$arStore['EMAIL']?></div><?
								}
								if( in_array('COORDINATES',$arParams['FIELDS'])) {
									?><div><?=$arStore['COORDINATES']?></div><?
								}
								if( in_array('DESCRIPTION',$arParams['FIELDS'])) {
									?><div class="descr"><?=$arStore['DESCRIPTION']?></div><?
								}
							?></div><?
						}
					?></div><?
				?></div><?
			}
		} else {
			echo RSFLYAWAY_GetResult($arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']],$arParams);
			if($arParams['SHOW_GENERAL_STORE_INFORMATION']!='Y') {
				?><div class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="ddmStores_<?=$arParams['~ELEMENT_ID']?>"><?
					?><div><?
						foreach($arResult['STORES'] as $key1 => $arStore) {
							?><div class="store-item store_<?=$arStore['ID']?>" <?=($arParams['SHOW_EMPTY_STORE'] == 'N' && $arStore['AMOUNT'] <= 0 ? 'style="display:none;"' : '')?>><?
								if( in_array('TITLE',$arParams['FIELDS'])) {
									?><div class="title"><?=$arStore['TITLE']?><span class="amount"><?=RSFLYAWAY_GetResult($arStore['AMOUNT'],$arParams)?></span></div><?
								}
								if( in_array('PHONE',$arParams['FIELDS'])) {
									?><div class="phone"><?=$arStore['PHONE']?></div><?
								}
								if( in_array('SCHEDULE',$arParams['FIELDS'])) {
									?><div class="schedule"><?=$arStore['SCHEDULE']?></div><?
								}
								if( in_array('EMAIL',$arParams['FIELDS'])) {
									?><div class="mail"><?=$arStore['EMAIL']?></div><?
								}
								if( in_array('COORDINATES',$arParams['FIELDS'])) {
									?><div><?=$arStore['COORDINATES']?></div><?
								}
								if( in_array('DESCRIPTION',$arParams['FIELDS'])) {
									?><div><?=$arStore['DESCRIPTION']?></div><?
								}
							?></div><?
						}
                    ?></div><?
				?></div><?
			}
		}
	?></span><?
?></span><?

	$arResult['JS']['MESSAGES'] = array(
		'MESSAGE_ISSET' =>  GetMessage('RS.FLYAWAY.QUANTITY_ISSET'),
		'MESSAGE_LOW' =>  GetMessage('RS.FLYAWAY.QUANTITY_LOW'),
		'MESSAGE_EMPTY' => GetMessage('RS.FLYAWAY.QUANTITY_EMPTY')
	);

	$arResult['JS']['LOW_QUANTITY'] = $arParams['MIN_AMOUNT'];

	$arResult['JS']['QUANTITY'] = $arParams['DATA_QUANTITY'];

?>
<script>
	rsFlyaway.stocks[<?=$arParams['~ELEMENT_ID']?>] =  <?=CUtil::PhpToJSObject( $arResult['JS'] )?>;
</script>
