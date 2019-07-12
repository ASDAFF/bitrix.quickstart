<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
if (isset($arParams['OFFER_ID']) and intval($arParams['OFFER_ID']) > 0 and trim($arParams['PROPERTY_CODE'])<>"") {
    $res = CIBlockElement::GetList(false, array("ID" => (int)$arParams['OFFER_ID']), false, false, array("PROPERTY_".$arParams['PROPERTY_CODE'].".PROPERTY_class_stone_color","PROPERTY_".$arParams['PROPERTY_CODE'].".NAME"));
    if ($ar_res = $res->GetNext()) {//deb($ar_res);
        ?>
        <p><span class="m-demo"><?=GetMessage("COLOR_PRODUCT")?>:</span>
        <span title="<?=$ar_res['PROPERTY_'.$arParams['PROPERTY_CODE'].'_NAME']?>" class="<?= $ar_res['PROPERTY_'.$arParams['PROPERTY_CODE'].'_PROPERTY_CLASS_STONE_COLOR_VALUE'] ?>">
            <span class="color-catalog"><i class="icon-diamond"></i></span>
        </span>
        </p>
    <?php
    }
}
?>