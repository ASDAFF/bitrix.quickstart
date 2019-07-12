<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?
if (!$arResult["NavShowAlways"]) {
    if (0 == $arResult["NavRecordCount"] || (1 == $arResult["NavPageCount"] && false == $arResult["NavShowAll"]))
        return;
}

if ('' != $arResult["NavTitle"])
    $arResult["NavTitle"] .= ' ';

$arSizes = array(5, 10, 15, 20, 25, 30, 40, 50, 100);

if (0 < $arResult['NavPageSize'] && !in_array($arResult['NavPageSize'], $arSizes))
    $arSizes[] = $arResult['NavPageSize'];

sort($arSizes);

$strSelectPath = $arResult['sUrlPathParams'].($arResult["bSavePage"] ? '&PAGEN_'.$arResult["NavNum"].'='.(true !== $arResult["bDescPageNumbering"] ? 1 : '').'&' : '').'SHOWALL_'.$arResult["NavNum"].'=0&SIZEN_'.$arResult["NavNum"].'=';
?>

<?if ($arResult["NavShowAll"]){?>
    <div class="pagination_pages"><a href="<?=$arResult['sUrlPathParams']; ?>SHOWALL_<?=$arResult["NavNum"]?>=0&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>"><?// echo GetMessage('INNET_SHOW_PAGES'); ?></a></div>
<?} else {?>
    <div class="pagination">
        <?if (1 < $arResult["NavPageNomer"]) {?>
            <a class="pag-prev" href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo GetMessage('nav_prev_title'); ?>"></a>
        <?}?>

        <?if (true === $arResult["bDescPageNumbering"]) {?>

            <?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {?>
                <a class="pag-prev" href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo GetMessage('nav_prev_title'); ?>">&#8592;</a>
            <?} else {?>
                &#8592;
            <?}?>

            <?
            $NavRecordGroup = $arResult["NavPageCount"];
            while ($NavRecordGroup >= 1) {
                $NavRecordGroupPrint = $arResult["NavPageCount"] - $NavRecordGroup + 1;
                $strTitle = GetMessage(
                    'nav_page_num_title',
                    array('#NUM#' => $NavRecordGroupPrint)
                );
                if ($NavRecordGroup == $arResult["NavPageNomer"]) {?>
                    <a class="active" title="<? echo GetMessage('nav_page_current_title'); ?>"><? echo $NavRecordGroupPrint; ?></a>
                <?} elseif ($NavRecordGroup == $arResult["NavPageCount"] && $arResult["bSavePage"] == false) {?>
                    <a href="<?=$arResult['sUrlPathParams']; ?>SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>"><?=$NavRecordGroupPrint?></a>
                <?} else {?>
                    <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$NavRecordGroup?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>"><?=$NavRecordGroupPrint?></a>
                <? }?>

                <?if (1 == ($arResult["NavPageCount"] - $NavRecordGroup) && 2 < ($arResult["NavPageCount"] - $arResult["nStartPage"])) {
                    $middlePage = floor(($arResult["nStartPage"] + $NavRecordGroup)/2);
                    $NavRecordGroupPrint = $arResult["NavPageCount"] - $middlePage + 1;
                    $strTitle = GetMessage(
                        'nav_page_num_title',
                        array('#NUM#' => $NavRecordGroupPrint)
                    );?>

                    <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$middlePage?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>">...</a>

                    <?$NavRecordGroup = $arResult["nStartPage"];
                } elseif ($NavRecordGroup == $arResult["nEndPage"] && 3 < $arResult["nEndPage"]) {
                    $middlePage = ceil(($arResult["nEndPage"] + 2)/2);
                    $NavRecordGroupPrint = $arResult["NavPageCount"] - $middlePage + 1;
                    $strTitle = GetMessage(
                        'nav_page_num_title',
                        array('#NUM#' => $NavRecordGroupPrint)
                    );
                    ?><a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$middlePage?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>">...</a><?
                    $NavRecordGroup = 2;
                } else {
                    $NavRecordGroup--;
                }
            }?>


                <?if ($arResult["NavPageNomer"] > 1) { ?>
                    <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo GetMessage('nav_next_title'); ?>">&#8594;</a>
                <? } else { ?>
                    &#8594;
                <? } ?>


        <? } else {
            $NavRecordGroup = 1;
            while($NavRecordGroup <= $arResult["NavPageCount"]) {
                $strTitle = GetMessage(
                    'nav_page_num_title',
                    array('#NUM#' => $NavRecordGroup)
                );

                if ($NavRecordGroup == $arResult["NavPageNomer"]) { ?>
                    <a class="active" title="<?=GetMessage('nav_page_current_title'); ?>"><?=$NavRecordGroup?></a>
                <? } elseif ($NavRecordGroup == 1 && $arResult["bSavePage"] == false) { ?>
                    <a href="<?=$arResult['sUrlPathParams']; ?>SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>"><?=$NavRecordGroup?></a>
                <? } else { ?>
                    <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$NavRecordGroup?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>"><?=$NavRecordGroup?></a>
                <? }?>

                <?if ($NavRecordGroup == 2 && $arResult["nStartPage"] > 3 && $arResult["nStartPage"] - $NavRecordGroup > 1) {
                    $middlePage = ceil(($arResult["nStartPage"] + $NavRecordGroup)/2);
                    $strTitle = GetMessage(
                        'nav_page_num_title',
                        array('#NUM#' => $middlePage)
                    ); ?>
                    <a href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=$middlePage?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>">...</a>

                    <?
                    $NavRecordGroup = $arResult["nStartPage"];
                } elseif ($NavRecordGroup == $arResult["nEndPage"] && $arResult["nEndPage"] < ($arResult["NavPageCount"] - 2)) {
                    $middlePage = floor(($arResult["NavPageCount"] + $arResult["nEndPage"] - 1)/2);
                    $strTitle = GetMessage(
                        'nav_page_num_title',
                        array('#NUM#' => $middlePage)
                    ); ?>
                    <a href="<?=$arResult['sUrlPathParams'];?>PAGEN_<?=$arResult["NavNum"]?>=<?=$middlePage?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo $strTitle; ?>">...</a>

                    <?
                    $NavRecordGroup = $arResult["NavPageCount"]-1;
                } else {
                    $NavRecordGroup++;
                }
            }
        }?>

        <?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {?>
            <a class="pag-next" href="<?=$arResult['sUrlPathParams']; ?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>&SIZEN_<?=$arResult["NavNum"]?>=<?=$arResult['NavPageSize']; ?>" title="<? echo GetMessage('nav_next_title'); ?>"><? //echo GetMessage('INNET_NEXT'); ?></a>
        <?}?>
    </div>
<?}?>