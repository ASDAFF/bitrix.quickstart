<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
    <div class="news-list">
        <?foreach($arResult["ITEMS"] as $arItem):?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="news-item">

                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">

                    <h2><?=$arItem["NAME"]?></h2>

                </a>

                <?=CFile::ShowImage($arItem['PREVIEW_PICTURE'])?>

                <?=$arItem['PREVIEW_TEXT']?>
            </div>
        <?endforeach;?>
    </div>

<?=$arResult['NAV_STRING']?>