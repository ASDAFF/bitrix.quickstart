<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);
//echo "<pre>";print_r($arResult);echo "</pre>";
?>
<div class="map">
    <!--Контейнер в который прилетит сформированная яндекс карта-->
    <div class="ymap" id="map"></div>
</div>
<section class="box-list-contacts">
    <div class="row">
        <? foreach ($arResult["ITEMS"] as $arItem) { ?>
            <div class="col-xl-4 item-city">
                <div class="item-city">
                    <div class="city-name"><?=$arItem["NAME"]; ?></div>
                    <? if ($arItem["PROPERTIES"]["ADDRESS"]["VALUE"]) { ?>
                        <div class="address">Адрес:</div>
                        <div class="txt"><?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"]; ?></div>
                    <? } ?>

                    <? if ($arItem["PROPERTIES"]["PHONE"]["VALUE"]) { ?>
                        <div class="phone">Телефон:</div>
                        <div class="txt">
                            <? foreach ($arItem["PROPERTIES"]["PHONE"]["VALUE"] as $sPhone) { ?>
                                <? echo $sPhone; ?>
                                <br/>
                            <? } ?>
                        </div>
                    <? } ?>

                    <? if ($arItem["PROPERTIES"]["FAX"]["VALUE"]) { ?>
                        <div class="fax">Факс:</div>
                        <div class="txt"><?=$arItem["PROPERTIES"]["FAX"]["VALUE"]; ?></div>
                    <? } ?>

                    <? if ($arItem["PROPERTIES"]["EMAIL"]["VALUE"]) { ?>
                        <div class="email">E-mail:</div>
                        <div class="txt"><? foreach ($arItem["PROPERTIES"]["EMAIL"]["VALUE"] as $sEmail) { ?><? echo $sEmail; ?>
                                <br/>
                            <? } ?>
                        </div>
                    <? } ?>
                </div>
            </div>
        <? } ?>
    </div>
</section>
