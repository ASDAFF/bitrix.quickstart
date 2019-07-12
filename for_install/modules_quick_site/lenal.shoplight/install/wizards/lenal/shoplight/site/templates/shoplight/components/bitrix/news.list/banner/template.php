<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>
<div class="carousel-banner">
    <div class="carousel">
        <ul class="carousel-container">
            <?
            foreach ($arResult["ITEMS"] as $arItem):
                ?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <li class="carousel-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="carousel_item" title="<?=$arItem["NAME"]?>" height="298" width="940">
                </li>
            <? endforeach; ?>
        </ul>

        <span class="carousel-prev"></span>
        <span class="carousel-next"></span>

    </div>
    </div>
    <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/jquery.js"></script>
    <script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/carousel.js"></script>
    <link rel="stylesheet" href="<?= SITE_TEMPLATE_PATH ?>/css/carousel.css" media="all" type="text/css">
    <?
?>