<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (count($arResult["ITEMS"]) > 0) {?>
<h3><?=GetMessage("TITLE")?></h3>
<div id="reviewlist">
<?foreach ($arResult["ITEMS"] as $arItem) {
    $rsUser = CUser::GetByID($arItem["DISPLAY_PROPERTIES"]["reviews_user"]["VALUE"]);
    $arUser = $rsUser->Fetch(); 
    
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
    <div class="hreview" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
        <p class="review-meta"><span class="dtreviewed"> <?=$arItem["DISPLAY_ACTIVE_FROM"]?><span class="value-title" title="<?=$arItem["DISPLAY_ACTIVE_FROM"]?>"></span></span> <span class="sep">|</span> <span class="reviewer"><?=$arUser["NAME"]?></span> <?=GetMessage("WR")?></p>
        <p class="description"><?=$arItem["PREVIEW_TEXT"]?></p>
    </div>
<?}?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <?=$arResult["NAV_STRING"]?>
<?endif;?>
</div><!-- #reviewlist -->
<?} else {?>
<h3><?=GetMessage("NOTITLE")?></h3>
<?}?>