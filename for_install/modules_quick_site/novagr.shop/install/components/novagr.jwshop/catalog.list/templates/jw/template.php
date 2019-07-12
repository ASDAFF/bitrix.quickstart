<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><!--start_html-->
<?
/**
 * шаблон для вывода списка элементов каталога
 */
?>
<div class="col3-list stuff-box" id="elements">

<div class="product-count-bottom">
    <span>
<?
	$str = $arResult['NavRecordCount'];
	if (substr( $str, strlen($str)-1, 1 ) == 1)
		echo GetMessage("FOUND_LABEL1");
	else
		echo GetMessage("FOUND_LABEL");
?> <?= $arResult['NavRecordCount']; ?> <?= Novagroup_Classes_General_Main::pluralForm($arResult['NavRecordCount'], GetMessage("MODEL_ONE"), GetMessage("MODEL_MANY"), GetMessage("MODEL_OF")) ?></span>
    <?
    if ($arResult['NavRecordCount'] > 16) {
        ?>
        <a class="npagesize <? if ($arParams['nPageSize'] == 16) {
            echo 'active';
        } else {
            echo 'incative';
        } ?>" value="16"><?= GetMessage("FORMAT_LABEL") ?> 16</a><a
            class="npagesize <? if ($arParams['nPageSize'] == 160) {
                echo 'active';
            } else {
                echo 'incative';
            } ?>" value="160"><?= GetMessage("FORMAT_LABEL") ?> 160</a>
    <?
    }
    ?>
</div>
<?
if(is_array($arParams['orderRows']) && count($arParams['orderRows'])>0):
    ?>
    <div class="choice-issuance">
        <label for="id_select"><?=GetMessage('SORT_BY')?></label>
        <select name="arOrder" class="selectpicker show-tick span2">
            <?php
            foreach($arParams['orderRows'] as $row => $params){
                $selected = (key($arParams['currentOrder'])==$row) ? 'selected="selected"' : '';
                ?>
                <option <?=$selected?> value="<?=$row?>"><?=$params['NAME']?></option>
            <?php
            }
            ?>
        </select>
    </div>
<?php
endif;
?>

<div class="clear"></div>

<div class="list">
    <div class="line">
        <div class="item_number">
            <div class="item-block">
                <?
                if (isset($arResult['ELEMENT'])) {
                    $keyIteration = -1;
                    foreach ($arResult['ELEMENT'] as $val) {
                        $keyIteration ++;
                        $arButtons = CIBlock::GetPanelButtons(
                            $val["IBLOCK_ID"],
                            $val["ID"],
                            0,
                            array("SECTION_BUTTONS" => false, "SESSID" => false)
                        );
                        $val["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

                        $this->AddEditAction($val['ID'], $val["EDIT_LINK"], CIBlock::GetArrayByID($val["IBLOCK_ID"], "ELEMENT_EDIT"));

                        $idElem = $this->GetEditAreaId($val['ID']);
                        ?>
                        <?php
                        if ($keyIteration % 4 == 0 and $keyIteration > 1) {
                            print '</div><div class="item-block">';
                        }
                        ?>
                        <div class="item" id="<?= $idElem ?>" data-catalog-id="<?= $val['ID'] ?>"
                             data-iblock-id="<?= $arParams["CATALOG_IBLOCK_ID"] ?>">
                            <div class="over">
                                <?php
                                $SECTION = GetIBlockSection($val['IBLOCK_SECTION_ID']);
                                ?>
                                <?$APPLICATION->IncludeComponent(
                                    "novagr.jwshop:catalog.element.preview",
                                    "jw",
                                    Array(
                                        "SORT_FIELD" => "ID",
                                        "SORT_BY" => "DESC",
                                        "CATALOG_IBLOCK_TYPE" => $arParams['CATALOG_IBLOCK_TYPE'],
                                        "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
                                        "CATALOG_OFFERS_IBLOCK_ID" => $arParams['OFFERS_IBLOCK_ID'],
                                        "ARTICLES_IBLOCK_ID" => $arParams['ARTICLES_IBLOCK_ID'],
                                        "FASHION_IBLOCK_ID" => $arParams['FASHION_IBLOCK_ID'],
                                        "SAMPLES_IBLOCK_CODE" => $arParams['SAMPLES_IBLOCK_CODE'],
                                        "BRANDNAME_IBLOCK_CODE" => $arParams['BRANDNAME_IBLOCK_CODE'],
                                        "COLORS_IBLOCK_CODE" => $arParams['COLORS_IBLOCK_CODE'],
                                        "MATERIALS_IBLOCK_CODE" => $arParams['MATERIALS_IBLOCK_CODE'],
                                        "STD_SIZES_IBLOCK_CODE" => $arParams['STD_SIZES_IBLOCK_CODE'],
                                        "INET_MAGAZ_ADMIN_USER_GROUP_ID" => $arParams['INET_MAGAZ_ADMIN_USER_GROUP_ID'],
                                        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                                        "CACHE_TIME" => $arParams['CACHE_TIME'],
                                        "SET_TITLE" => "N",
                                        "DISABLE_QUICK_VIEW" => $arParams['DISABLE_QUICK_VIEW'],
                                        "COMPONENT_CURRENT_PAGE" => $arParams['ROOT_PATH'] . $SECTION['CODE'] . "/" . $val['CODE'] . "/",
                                    ),
                                    false,
                                    Array(
                                        'ACTIVE_COMPONENT' => 'Y',
                                        "HIDE_ICONS" => "Y"
                                    )
                                );?>
                            </div>
                            <div class="preview-info-boxover" style="opacity: 1; display: none;">
                                <div class="middle">
                                    <?$APPLICATION->IncludeComponent(
                                        "novagr.jwshop:catalog.element.preview",
                                        "jw",
                                        Array(
                                            "SORT_FIELD" => "ID",
                                            "SORT_BY" => "DESC",
                                            "CATALOG_IBLOCK_TYPE" => $arParams['CATALOG_IBLOCK_TYPE'],
                                            "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
                                            "CATALOG_OFFERS_IBLOCK_ID" => $arParams['OFFERS_IBLOCK_ID'],
                                            "ARTICLES_IBLOCK_ID" => $arParams['ARTICLES_IBLOCK_ID'],
                                            "FASHION_IBLOCK_ID" => $arParams['FASHION_IBLOCK_ID'],
                                            "SAMPLES_IBLOCK_CODE" => $arParams['SAMPLES_IBLOCK_CODE'],
                                            "BRANDNAME_IBLOCK_CODE" => $arParams['BRANDNAME_IBLOCK_CODE'],
                                            "COLORS_IBLOCK_CODE" => $arParams['COLORS_IBLOCK_CODE'],
                                            "MATERIALS_IBLOCK_CODE" => $arParams['MATERIALS_IBLOCK_CODE'],
                                            "STD_SIZES_IBLOCK_CODE" => $arParams['STD_SIZES_IBLOCK_CODE'],
                                            "INET_MAGAZ_ADMIN_USER_GROUP_ID" => $arParams['INET_MAGAZ_ADMIN_USER_GROUP_ID'],
                                            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                                            "CACHE_TIME" => $arParams['CACHE_TIME'],
                                            "SET_TITLE" => "N",
                                            "DISABLE_QUICK_VIEW" => $arParams['DISABLE_QUICK_VIEW'],
                                            "COMPONENT_CURRENT_PAGE" => $arParams['ROOT_PATH'] . $SECTION['CODE'] . "/" . $val['CODE'] . "/",
                                        ),
                                        false,
                                        Array(
                                            'ACTIVE_COMPONENT' => 'Y',
                                            "HIDE_ICONS" => "Y"
                                        )
                                    );?>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="clear"></div>

    </div>
</div>

<div class="product-count-bottom bot-p">
    <span><?= GetMessage("FOUND_LABEL") ?> <?= $arResult['NavRecordCount']; ?> <?= Novagroup_Classes_General_Main::pluralForm($arResult['NavRecordCount'], GetMessage("MODEL_ONE"), GetMessage("MODEL_MANY"), GetMessage("MODEL_OF")) ?></span>
    <?
    if ($arResult['NavRecordCount'] > 16) {
        ?>
        <a class="npagesize <? if ($arParams['nPageSize'] == 16) {
            echo 'active';
        } else {
            echo 'incative';
        } ?>" value="16"><?= GetMessage("FORMAT_LABEL") ?> 16</a><a
            class="npagesize <? if ($arParams['nPageSize'] == 160) {
                echo 'active';
            } else {
                echo 'incative';
            } ?>" value="160"><?= GetMessage("FORMAT_LABEL") ?> 160</a>
    <?
    }
    ?>
</div>
<div class="pagination pagination-right bot-p">
    <?= $arResult['NAV_STRING']; ?>
</div>
<div class="clear"></div>

<?php
if (trim($arResult['SEO_DATA']['HEADER']) <> "" || trim($arResult['SEO_DATA']['DESCRIPTION']) <> "") {
    ?>
    <div class="info-block">
        <?php
        if (trim($UF_TITLE_H1 = $arResult['SEO_DATA']['HEADER']) <> "") print "<h1>{$UF_TITLE_H1}</h1>";
        echo $arResult['SEO_DATA']['DESCRIPTION'];
        ?>
    </div>
<?php
}
?>
<?php if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
    <script type="text/javascript">

        /*catalog.element.preview - всплывающий быстрый просмотр*/
        $('.list .line .item').hover(
            function() {$(this).find('.preview-info-boxover').stop(true).fadeIn("600");},
            function() {$(this).find('.preview-info-boxover').stop(true).fadeOut("600");}
        );
        $('.quickViewLink').click(function(){
            return loadPreviewElementModalWindow($(this).attr('href'));
        });
        $('span.link-popover-card').click(function(){
            return loadPreviewElementModalWindow($(this).find('a').attr('href'));
        });
        /*конец всплывающий быстрый просмотр*/
    </script>
<?php endif;?>
<?
if(count($arParams["SEARCH_COLORS"])>0)
{
    foreach($arParams["SEARCH_COLORS"] as $SEARCH_COLOR)
    {
        $_REQUEST['arOffer'][] = array( "PROPERTY_COLOR_STONE" => $SEARCH_COLOR['ID']);
    }
}
	if (count($_REQUEST['arOffer'])>0)
	foreach($_REQUEST['arOffer'] as $rOffer)
    {
        if (isset($rOffer['PROPERTY_COLOR_STONE']))
        {
?>
            <script type="text/javascript">
                $(document).ready(function(){

                    $(".item").each(function() {
                        var obj = $(this).find(".button-color-button-<?=(int)$rOffer['PROPERTY_COLOR_STONE']?> :first");
                        obj.unbind("click");
                        obj.click();
                    });
                    return;
                    <?/*
                    //$(".button-color-button-<?=(int)$rOffer['PROPERTY_COLOR_STONE']?>").unbind("click");
                    //$(".button-color-button-<?=(int)$rOffer['PROPERTY_COLOR_STONE']?>").click();
                    */?>
                });

            </script>
<?php
        }
    }
?>

</div>

<!--end_html-->