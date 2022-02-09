<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
 
<div class="b-main-news">
    <h3 class="b-h3 m-recommended">ЭТО ИНТЕРЕСНО</h3>
    <ul class="b-main-news_list clearfix">

        <? foreach ($arResult["ITEMS"] as $arItem) {  
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
 
            <li class="b-main-news_list__item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <div class="b-main-news_list__image"><img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>" alt="<? echo $arItem["NAME"] ?>" /></div>
                <div class="b-main-news_list__link"><a href="<? echo $arItem["DETAIL_PAGE_URL"]; ?>"><? echo $arItem["NAME"] ?></a></div>
                <div class="b-main-news_list__text"><? echo $arItem["PREVIEW_TEXT"]; ?></div>
            </li>
        <? } ?>
    </ul>
</div>