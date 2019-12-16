
<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

if (!function_exists('getStringCatalogStoreAmountEx')){
	function getStringCatalogStoreAmountEx($amount, $minAmount, $arReturn){
		$message = $arReturn[1];
		if (intval($amount) == 0){
			$message = $arReturn[0];
		} elseif (intval($amount) >= $minAmount){
			$message = $arReturn[2];
		}
		return $message;
	}
}

$arMessage = array(getMessage('RS_SLINE.BCSA_AL.OUT_OF_STOCK'), getMessage('RS_SLINE.BCSA_AL.LIMITED_AVAILABILITY'), getMessage('RS_SLINE.BCSA_AL.IN_STOCK'));
$arClasses = array('is-outofstock', 'is-limited', 'is-instock');
$arSchemaAvailability = array('http://schema.org/OutOfStock', 'http://schema.org/LimitedAvailability', 'http://schema.org/InStock');

$this->setFrameMode(true);

?>
<div class="stocks">

	<span><?=$arParams['MAIN_TITLE']?></span>
    <span class="tooltip">
        <?php if (!empty($arResult['JS']['SKU'])): ?>
            <span class="stocks__amount tooltip__link anchor"><?php
                echo ($arParams['USE_MIN_AMOUNT_TMPL'] == 'Y')
                    ? getStringCatalogStoreAmountEx($arResult['JS']['SKU'][$arParams['OFFER_ID']]['MAX_AMOUNT'], $arParams['MIN_AMOUNT'], $arMessage)
                    : $arResult['JS']['SKU'][$arParams['OFFER_ID']]['MAX_AMOUNT'];
            ?></span><?php
            ?><span class="stocks__scale scale <?=getStringCatalogStoreAmountEx($arResult['JS']['SKU'][$arParams['OFFER_ID']]['MAX_AMOUNT'], $arParams['MIN_AMOUNT'], $arClasses)?>">
                <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                <span class="scale__over" <?if(0 < $arResult['JS']['SKU'][$arParams['OFFER_ID']]['TOTAL_AMOUNT']){ echo ' style="width:100%"';}?>>
                    <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                </span>
            </span>
            <link itemprop="availability" href="<?=getStringCatalogStoreAmountEx($arResult['JS']['SKU'][$arParams['OFFER_ID']]['MAX_AMOUNT'], $arParams['MIN_AMOUNT'], $arSchemaAvailability)?>">
        <?php else: ?>
            <span class="stocks__amount tooltip__link anchor"><?php
                echo ($arParams['USE_MIN_AMOUNT_TMPL'] == 'Y')
                    ? getStringCatalogStoreAmountEx($arResult['TOTAL_AMOUNT'], $arParams['MIN_AMOUNT'], $arMessage)
                    : $arResult['TOTAL_AMOUNT'];
            ?></span><?php
            ?><span class="stocks__scale scale <?=getStringCatalogStoreAmountEx($arResult['MAX_AMOUNT'], $arParams['MIN_AMOUNT'], $arClasses)?>">
                <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                <span class="scale__over" <?if(0 < $arResult['TOTAL_AMOUNT']){ echo ' style="width:100%"'; }?>>
                    <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                </span>
            </span>
            <link itemprop="availability" href="<?=getStringCatalogStoreAmountEx($arResult['MAX_AMOUNT'], $arParams['MIN_AMOUNT'], $arSchemaAvailability)?>">
		<?php endif; ?>
	
        <div class="tooltip__in">
            <table class="stocks__table">
                <?php foreach($arResult['STORES'] as $iStore => $arStore): ?>
                <tr class="stock" id="<?=$arResult['JS']['ID']?>_<?=$arStore['ID']?>">
                    <?php if (isset($arStore['TITLE'])): ?>
                        <?php
                        /*?><td><a href="<?=$arStore['URL']?>"> <?=$arStore['TITLE']?></a></td><?*/
                        ?>
                        <td class="stock__name"><?=$arStore['TITLE']?></td>
                    <?php endif; ?>
                    
                    <?php if (isset($arStore['IMAGE_ID']) && !empty($arStore['IMAGE_ID'])): ?>
                        <td><?=CFile::ShowImage($arStore['IMAGE_ID'], 200, 200, '', '', true)?></td>
                    <?php endif; ?>
                    
                    <?php if (isset($arStore['PHONE'])): ?>
                        <td><?=$arStore['PHONE']?></td>
                    <?php endif; ?>
                    
                    <?php if (isset($arStore['SCHEDULE'])): ?>
                        <td><?=$arStore['SCHEDULE']?></td>
                    <?php endif; ?>
                    
                    <?php if (isset($arStore['EMAIL'])): ?>
                        <td><?=$arStore['EMAIL']?></td>
                    <?php endif; ?>
                    
                    <?php if (isset($arStore['DESCRIPTION'])): ?>
                        <td><?=$arStore['DESCRIPTION']?></td>
                    <?php endif; ?>
                    
                    <?php if (isset($arStore['COORDINATES'])): ?>
                        <td><?=$arStore['COORDINATES']['GPS_N']?>, <?=$arStore['COORDINATES']['GPS_S']?></td>
                    <?php endif; ?>
                    
                    <?php  if (!empty($arStore['USER_FIELDS']) && is_array($arStore['USER_FIELDS'])): ?>
                        <?php foreach ($arStore['USER_FIELDS'] as $userField): ?>
                            <?php if (isset($userField['CONTENT'])): ?>
                                <td><?=$userField['TITLE']?>: <?=$userField['CONTENT']?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($arResult['JS']['SKU'])): ?>
                        <td class="stock__amount">
                            <span class="stock__quantity anchor"><?php
                                echo ($arParams['USE_MIN_AMOUNT_TMPL'] == 'Y')
                                    ? getStringCatalogStoreAmountEx($arResult['JS']['SKU'][$arParams['OFFER_ID']]['STORES'][$arStore['ID']], $arParams['MIN_AMOUNT'], $arMessage)
                                    : $arResult['JS']['SKU'][$arParams['OFFER_ID']]['STORES'][$arStore['ID']];
                            ?></span>&nbsp;<?php
                            ?><span class="stock__scale scale <?=getStringCatalogStoreAmountEx($arResult['JS']['SKU'][$arParams['OFFER_ID']]['STORES'][$arStore['ID']], $arParams['MIN_AMOUNT'], $arClasses)?>">
                                <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                                <span class="scale__over"<?if($arResult['JS']['SKU'][$arParams['OFFER_ID']]['MAX_AMOUNT'] > 0){ echo ' style="width:'.(100 * $arResult['JS']['SKU'][$arParams['OFFER_ID']]['STORES'][$arStore['ID']] / $arResult['JS']['SKU'][$arParams['OFFER_ID']]['MAX_AMOUNT']).'%"';}?>>
                                    <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                                </span>
                            </span>
                        </td>
                    <?php else: ?>
                        <td class="stock__amount">
                            <span class="stock__quantity anchor"><?php
                                echo ($arParams['USE_MIN_AMOUNT_TMPL'] == 'Y')
                                    ? getStringCatalogStoreAmountEx($arStore['AMOUNT'], $arParams['MIN_AMOUNT'], $arMessage)
                                    : $arStore['AMOUNT'];
                            ?></span>&nbsp;<?php
                            ?><span class="stock__scale scale <?=getStringCatalogStoreAmountEx($arStore['AMOUNT'], $arParams['MIN_AMOUNT'], $arClasses)?>">
                                <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                                <span class="scale__over"<?if($arResult['MAX_AMOUNT'] > 0){ echo ' style="width:'.(100 * $arStore['AMOUNT'] / $arResult['MAX_AMOUNT']).'%;"';}?>>
                                    <svg class="scale__icon icon-scale icon-svg"><use xlink:href="#svg-scale"></use></svg>
                                </span>
                            </span>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </span>
</div>
<?php
if (!empty($arResult['JS']['SKU'])):
	$arResult['JS']['USE_MIN_AMOUNT'] = ($arParams['USE_MIN_AMOUNT_TMPL'] == 'Y');
	$arResult['JS']['MESSAGES'] = array(
		'IN_STOCK' => getMessage('RS_SLINE.BCSA_AL.IN_STOCK'),
		'LIMITED_AVAILABILITY' => getMessage('RS_SLINE.BCSA_AL.LIMITED_AVAILABILITY'),
		'OUT_OF_STOCK' => getMessage('RS_SLINE.BCSA_AL.OUT_OF_STOCK'),
	);
?>
<script>appSLine.stocks[<?=$arParams['~ELEMENT_ID']?>] = <?=CUtil::PhpToJSObject($arResult['JS'])?>;</script>
<?php endif; ?>