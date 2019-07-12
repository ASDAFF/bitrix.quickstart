<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;


$isAjax = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["ajax_action"]) && $_POST["ajax_action"] == "Y");
$showProperties = array();
$showOfferProperties = array();

?>
<div class="row compare-result" id="bx_catalog_compare_block">

    <?php if ($isAjax) $APPLICATION->RestartBuffer(); ?>

    <div class="col col-md-12 compare-result__sort text-right">
        <a class="btn btn-default btn-button<?php if(!$arResult["DIFFERENT"]) echo ' active'; ?>"
           href="<?=$arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=N';?>"
        >
            <?=Loc::getMessage('CATALOG_ALL_CHARACTERISTICS')?>
        </a>
        <a class="btn btn-default btn-button<?php if($arResult["DIFFERENT"]) echo ' active'; ?>"
           href="<?=$arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=Y';?>"
        >
            <?=Loc::getMessage('CATALOG_ONLY_DIFFERENT')?>
        </a>
    </div>

    <div class="col col-md-12 compare-result__block">
        <div class="compare-result__scroll">

            <div class="compare-result__header">

                <div class="compare-result__info"></div>
                <?php foreach($arResult['ITEMS'] as $arElement): ?>
                <div class="compare-result__info">
                    <div class="compare-result__info-image">
                        <?php
                        $image = $arResult['NO_PHOTO']['src'];
                        if(!empty($arElement['FIRST_PIC'])) {
                            $image = $arElement['FIRST_PIC']['RESIZE']['src'];
                        } elseif(!empty($arElement['FIRST_PIC_DETAIL'])) {
                            $image = $arElement['FIRST_PIC_DETAIL']['RESIZE']['src'];
                        }
                        ?>
                        <img src="<?=$image?>" alt="<?=$arElement['NAME']?>">
                    </div>
                    <div class="compare-result__info-name">
                        <?php if(!empty($arResult['SHOW_FIELDS']['NAME'])): ?>
                        <a href="<?=$arElement['DETAIL_PAGE_URL']?>"><?=$arElement['FIELDS']['NAME']?></a>
                        <?php endif; ?>
                    </div>
                    <div class="compare-result__item-remove">
                        <a onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arElement['~DELETE_URL'])?>');" href="javascript:void(0)">
				<i class="fa fa-times"></i>
			</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="compare-result__table">

                <?php if (!empty($arResult["SHOW_FIELDS"])): ?>
                    <?php
                    foreach ($arResult["SHOW_FIELDS"] as $code => $arProp):

                        $showRow = true;
                        if (!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT']) {
                            $arCompare = array();
                            foreach ($arResult["ITEMS"] as &$arItem) {
                                    $arPropertyValue = $arItem["FIELDS"][$code];
                                    if (is_array($arPropertyValue)) {
                                            sort($arPropertyValue);
                                            $arPropertyValue = implode(" / ", $arPropertyValue);
                                    }
                                    $arCompare[] = $arPropertyValue;
                            }

                            unset($arItem);
                            $showRow = (count(array_unique($arCompare)) > 1);
                        }

                        if ($showRow):
                            if ($code=='NAME' || $code=='PREVIEW_PICTURE' || $code=='DETAIL_PICTURE')
                                    continue;
                        ?>
                        <div class="compare-result__table-row">
                            <div class="compare-result__table-property"><?=Loc::getMessage("IBLOCK_FIELD_".$code)?></div>
                            <?php foreach($arResult["ITEMS"] as &$arElement): ?>
                            <div class="compare-result__table-col">
                                <?=$arElement["FIELDS"][$code];?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; unset($arElement); ?>

                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="compare-result__table-row">
                    <div class="compare-result__table-property"><?=Loc::getMessage('CATALOG_COMPARE_PRICE')?></div>
                    <?php foreach($arResult["ITEMS"] as &$arElement): ?>
                            <?php if (isset($arElement['MIN_PRICE']) && is_array($arElement['MIN_PRICE'])): ?>
                                <div class="compare-result__table-col"><?=$arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></div>
                            <?php else: ?>
                                <div class="compare-result__table-col">&nbsp;</div>
                            <?php endif; ?>
                    <?php endforeach; unset($arElement); ?>
                </div>

                <?php foreach ($arResult["SHOW_PROPERTIES"] as $code => $arProperty): ?>
                    <?php
                    $showRow = true;
                    if ($arResult['DIFFERENT'])  {
                            $arCompare = array();
                            foreach($arResult["ITEMS"] as &$arElement)
                            {
                                    $arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
                                    if (is_array($arPropertyValue))
                                    {
                                            sort($arPropertyValue);
                                            $arPropertyValue = implode(" / ", $arPropertyValue);
                                    }
                                    $arCompare[] = $arPropertyValue;
                            }
                            unset($arElement);
                            $showRow = (count(array_unique($arCompare)) > 1);
                    }

                    if($showRow):
                    ?>
                    <div class="compare-result__table-row">
                        <div class="compare-result__table-property"><?=$arProperty["NAME"]?></div>
                        <?php foreach($arResult["ITEMS"] as &$arElement): ?>
                            <div class="compare-result__table-col">
                                <?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
                            </div>
                        <?php endforeach; unset($arElement); ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php foreach ($arResult["SHOW_OFFER_PROPERTIES"] as $code => $arProperty): ?>
                    <?php
                    $showRow = true;
                    if ($arResult['DIFFERENT'])  {
                            $arCompare = array();
                            foreach($arResult["ITEMS"] as &$arElement)
                            {
                                    $arPropertyValue = $arElement["OFFER+DISPLAY_PROPERTIES"][$code]["VALUE"];
                                    if (is_array($arPropertyValue))
                                    {
                                            sort($arPropertyValue);
                                            $arPropertyValue = implode(" / ", $arPropertyValue);
                                    }
                                    $arCompare[] = $arPropertyValue;
                            }
                            unset($arElement);
                            $showRow = (count(array_unique($arCompare)) > 1);
                    }

                    if($showRow):
                    ?>
                    <div class="compare-result__table-row">
                        <div class="compare-result__table-property"><?=$arProperty["NAME"]?></div>
                        <?php foreach($arResult["ITEMS"] as &$arElement): ?>
                            <div class="compare-result__table-col">
                                <?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
                            </div>
                        <?php endforeach; unset($arElement); ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="compare-result__clone-properties"></div>
            </div>

        </div>

    </div>
    <?php if ($isAjax) die(); ?>

</div>

<script>
	var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("bx_catalog_compare_block");
</script>
