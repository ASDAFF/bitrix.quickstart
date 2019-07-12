<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?$this->setFrameMode(true);?>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/dealers.js"></script>

<div class="right_part_dealers">
    <div id="sidemap" style="height: 600px;"></div>
</div>

<div class="staff_wrapp_dealers">
    <?foreach ($arResult["SECTIONS"] as $key => $arSection) {?>
        <div class="section">
            <a data-citycoord="<?=$arSection['UF_COORD']?>" class="section_title<?if ($key == 0) {?> opened<?}?>"><span class="icon"><i></i></span><span class="pseudo"><span class="item_title"><?=$arSection["NAME"];?></span></span></a>
            <div class="items clearfix" <?if ($key == 0) {?>style="display: block;"<?}?>>
                <table class="item">
                    <?foreach ($arSection["ITEMS"] as $key => $arItem) {?>
                        <tr id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                            <td>
                                <div class="radio-box"><input type="radio" id="dealer_<?=$arItem['ID']?>" name="coord" value="<?=$arItem['PROPERTIES']['MAP']['VALUE']?>" data-adr="<?=$arItem['PROPERTIES']['ADDRESS']['VALUE']?>">
                                    <label for="dealer_<?=$arItem['ID']?>"> <span class="circle"></span> <span class="name"><?=$arItem["NAME"]?></span></label>
                                </div>
                                <?if ($arItem["PROPERTIES"]["DEALER_LINK"]["VALUE"]) {?>
                                    <span class="site"><a rel="nofollow" href="<?=$arItem["PROPERTIES"]["DEALER_LINK"]["VALUE"]?>" target="_blank"><?=$arItem["PROPERTIES"]["DEALER_LINK"]["VALUE"]?></a></span>
                                <?}?>
                            </td>
                            <td>
                                <?if ($arItem["PROPERTIES"]["EMAIL"]["VALUE"]) {?>
                                    <span class="email"><a href="mailto:<?=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?>"><?=$arItem["PROPERTIES"]["EMAIL"]["VALUE"]?></a></span>
                                <?}?>
                            </td>
                            <td>
                                <?if ($arItem["PROPERTIES"]["PHONE"]["VALUE"]) {?>
                                    <span class="phone"><?=$arItem["PROPERTIES"]["PHONE"]["VALUE"]?></span>
                                <?}?>
                            </td>
                        </tr>
                    <?}?>
                </table>
            </div>
        </div>
    <?}?>
</div>

<div class="clearfix"></div>