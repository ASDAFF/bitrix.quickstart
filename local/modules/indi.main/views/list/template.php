<?
$arProductListConfig = \Indi\Main\ListConfig::get($result['arParams']['LIST_CONFIG']);
?>
<div class="list-elements-<?=$result['arParams']['LIST_CONFIG']?>">
    <div class="change-columns">
        <a class="icon-link fake" data-toggle="modal" data-target="#change-columns-modal-<?=$result['arParams']['LIST_CONFIG']?>" href1="javascript:;" href="#change-columns-modal-<?=$result['arParams']['LIST_CONFIG']?>">
            <span class="icon icon-settings"></span>
            <span>Изменить столбцы</span>
        </a>
    </div>
    <table class="simple-little-table" cellspacing='0'>
        <thead>
            <tr>
                <?foreach ($arProductListConfig['PAGE_ELEMENT_COL_OPTION'] as $key => $arCol){
                    if ($arCol['SORT']) {?>
                        <th class="td td-<?=$key?> js-sort-to-set-cookie"
                            data-name="<?=$arProductListConfig['PAGE_ELEMENT_NAME']?>"
                            data-sort="<?=$arCol['SORT']?>" data-container=".list-elements-<?=$result['arParams']['LIST_CONFIG']?>"
                            data-source=".list-elements-<?=$result['arParams']['LIST_CONFIG']?>" data-ajax="Y">
                            <div class="title-element">
                                <div class="inline">
                                    <a><?=$arCol['NAME']?>
                                    </a>
                                    <span class="sorting-arrows<?=$arCol['SORT'] == $arProductListConfig['PAGE_ELEMENT_SORT']['FIELD'] ? ($arProductListConfig['PAGE_ELEMENT_SORT']['ORDER'] == 'ASC' ? ' up' : ' down') : ''?>">
                                    </span>
                                </div>
                            </div>
                        </th>
                    <? }?>
                <? }?>
            </tr><!-- Table Header -->
        </thead>
        <tbody>
        <?foreach($result['arResult']['ITEMS'] as $arItem) {
            /*$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));*/
            $showLink = !$result['arParams']['HIDE_LINK_WHEN_NO_DETAIL'] || ($arItem['DETAIL_TEXT'] && $result['arResult']['USER_HAVE_ACCESS']);
            ?>
            <tr>
                <?if($arProductListConfig['PAGE_ELEMENT_COL_OPTION']['NAME']){?>
                    <td>
                        <?
                        if ($showLink) {
                            ?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
                        } else {
                            ?><?=$arItem['NAME']?><?
                        }
                        ?>
                    </td>
                <? }?>
                <?if($arProductListConfig['PAGE_ELEMENT_COL_OPTION']['ACTIVE_FROM']){?>
                    <td><span class="news-list-date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></span></td>
                <? }?>
                <?if($arProductListConfig['PAGE_ELEMENT_COL_OPTION']['PREVIEW_TEXT']){?>
                    <td class="news-list-preview"><?=$arItem['PREVIEW_TEXT']?></td>
                <? }?>
            </tr><!-- Table Row -->
            <?
        }
        ?>
        </tbody>
    </table>

</div>