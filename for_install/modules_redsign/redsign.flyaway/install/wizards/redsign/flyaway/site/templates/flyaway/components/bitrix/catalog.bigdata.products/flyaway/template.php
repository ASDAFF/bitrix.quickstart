<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use \Bitrix\Main\Security\Sign\Signer;
use    \Bitrix\Main\Localization\Loc;

$frame = $this->createFrame()->begin("");
$injectId = 'bigdata_recommeded_products_'.rand();
?>
<?php if(isset($arResult['REQUEST_ITEMS'])): ?>

    <?php
    CJSCore::Init(array('ajax'));
    $signer = new Signer;
    $signedParameters = $signer->sign(
        base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
        'bx.bd.products.recommendation'
    );

    $signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');
    ?>
    <span id="<?=$injectId?>" class="bigdata_recommended_products_container"></span>
    <script>
        BX.ready(function() {
            bx_rcm_get_from_cloud(
                '<?=CUtil::JSEscape($injectId)?>',
                <?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
                {
                    'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
                    'template': '<?=CUtil::JSEscape($signedTemplate)?>',
                    'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
                    'rcm': 'yes'
                }
            );
        });
    </script>

    <?php
    $frame->end();
    return;
    ?>

<?php endif; ?>
<div class="row">
<div class="col col-xs-12 col-sm-12 col-md-9 col-lg-10">
<?php if(!empty($arResult['ITEMS'])): ?>
    <div id="<?=$injectId?>_items" class="bigdata_recommended_products_items section-cart">
        <input type="hidden" name="bigdata_recommendation_id" value="<?=htmlspecialcharsbx($arResult['RID'])?>">
            <h2 class="product-content__title">
                <span><?=Loc::getMessage("RS.FLYAWAY.RCM");?></span>
            </h2>
            <div class="products_showcase products owlslider owlbigdata" data-items = "5">
                <?php foreach($arResult['ITEMS'] as $arItem): ?>
                    <?php
                    if(empty($arItem['OFFERS'])) {
                        $HAVE_OFFERS = false; $PRODUCT = &$arItem;
                    } else {
                        $HAVE_OFFERS = true; $PRODUCT = &$arItem['OFFERS'][0];
                    }
                    ?>

                    <div class ="products__item item js-element js-elementid<?=$arItem['ID']?>" data-elementid="<?=$arItem['ID']?>" data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>">
                        <div class="products__in">
                            <?php if($arParams['RSFLYAWAY_USE_FAVORITE'] == "Y"): ?>
                                <div class="favorite favorite-heart" data-elementid = "<?=$arItem['ID']?>" data-detailpageurl="<?=$arItem['DETAIL_PAGE_URL']?>">
                                </div>
                            <?php endif;?>
                            <?php
                                    $strTitle = (
                                    isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != ''
                                    ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]
                                    : $arItem['NAME']
                                );
                                $strAlt = (
                                isset($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != ''
                                ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]
                                : $arItem['NAME']
                            );
                            ?>
                            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$strTitle?>">
                            <div class="products__pic">
                                <?php if(!empty($arItem['FIRST_PIC'])): ?>
                                    <img class="products__img" src="<?=$arItem['FIRST_PIC']['RESIZE']['src']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>">
                                <?php else: ?>
                                    <img class="products__img" src="<?=$arResult['NO_PHOTO']['src']?>" title="<?=$strTitle?>" alt="<?=$strAlt?>">
                                <?php endif; ?>
                            </div>
                            </a>
                            <div class="products__data">

                                <?php if($arParams['SHOW_NAME'] == "Y"): ?>
                                    <div class="products__name">
                                        <a class="products-title" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                            <?=$arItem['NAME']?></a><br>
                                        </a>
                                    </div>
                                    <div class="hidden-xs products__category separator"></div>
                                    <div class="visible-xs separator"></div>
                                <?php endif; ?>

                                <div class="products__prices">

                                    <div class="prices">
                                         <div class="hidden-xs prices__title"></div>
                                         <div class="prices__values">
                                              <?php if((int)$PRODUCT['MIN_PRICE']['DISCOUNT_DIFF'] > 0): ?>
                                                  <div class="hidden-xs prices__val prices__val_old"><?=$PRODUCT['MIN_PRICE']['PRINT_VALUE']?></div>
                                                  <div class="prices__val prices__val_cool prices__val_new"> <?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                                              <?php else: ?>
                                                  <div class="prices__val prices__val_cool"><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?></div>
                                              <?php endif; ?>
                                         </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
</div>
</div>

<?php
$frame->end();
