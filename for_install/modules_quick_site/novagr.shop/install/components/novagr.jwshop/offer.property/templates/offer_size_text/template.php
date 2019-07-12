<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
if (isset($arParams['OFFER_ID']) and intval($arParams['OFFER_ID']) > 0) {
    $res = CIBlockElement::GetList(false, array("ID" => (int)$arParams['OFFER_ID']), false, false, array("PROPERTY_STD_SIZE.NAME"));
    if ($ar_res = $res->GetNext()) {
        if(trim($ar_res['PROPERTY_STD_SIZE_NAME'])=="")return;
        ?>
        <p><span class="m-demo"><?=GetMessage("SIZE_PRODUCT")?>:</span>
            <span class="size-bas-demo"><?=$ar_res['PROPERTY_STD_SIZE_NAME']?></span>
        </p>
    <?php
    }
}
?>