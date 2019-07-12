<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (!function_exists('RSFLYAWAY_GetResult')) {
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

?>
<span class="stores" data-firstElement="<?=$arParams['FIRST_ELEMENT_ID']?>">
    <span class="stores-label">
    <?php
    if ($arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']] < 1 && $arParams['CATALOG_SUBSCRIBE'] == 'Y') {
        
	} else {
        echo $arParams['MAIN_TITLE'].':';
    }
    ?>
    </span>
	<?=RSFLYAWAY_GetResult($arParams['DATA_QUANTITY'][$arParams['FIRST_ELEMENT_ID']], $arParams)?>
</span>
<?php
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
