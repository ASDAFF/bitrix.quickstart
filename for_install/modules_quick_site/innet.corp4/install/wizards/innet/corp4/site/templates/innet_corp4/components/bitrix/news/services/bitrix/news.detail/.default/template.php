<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="cols3">
    <div class="col1">
        <div class="ms-lightbox-template">
            <div class="master-slider ms-skin-default" id="masterslider">
                <?if (!empty($arResult['IMAGES_SLIDER'])){?>
                    <?foreach ($arResult['IMAGES_SLIDER'] as $slide){?>
                        <div class="ms-slide">
                            <img src="<?=$slide['SRC']?>" alt="<?=$arResult['NAME']?>"/>
                            <img class="ms-thumb" src="<?=$slide['RESIZE_SRC']['src']?>" alt="<?=$arResult['NAME']?>"/>
                            <a href="<?=$slide['SRC']?>" data-gal="prettyPhoto[Gallery1]"></a>
                        </div>
                    <?}?>
                <?}?>
            </div>
        </div>
    </div>

    <div class="col2 text"><?=$arResult['DETAIL_TEXT']?></div>

    <div class="share-block">
        <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/share.php", "EDIT_TEMPLATE" => "" ), false );?>
    </div>
	<div class="cl"></div>
</div>

<?if(count($arResult['IMAGES_SLIDER']) < 2){?>
	<style>
		.ms-thumb-list,.ms-nav-next,.ms-nav-prev{display:none!important;}
	</style>
<?}?>

<?if ($arResult['PROPERTIES']['ORDER_SERVICE']['VALUE'] == "Y"){?>
    <div class="cons cons-small in-row-mid">
        <div class="col1">
            <div class="title4"><?=GetMessage('INNET_SERVICES_ORDER')?></div>
            <p>
                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR . "include/services/desc_7.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );?>
            </p>
        </div>
        <div class="col2"><a class="btn popbutton" data-window="2"><?=GetMessage('INNET_SERVICES_ORDER')?></a></div>
    </div>
<?}?>


<?
if (!empty($arResult['PROPERTIES']['TABLE_2']['VALUE']['TEXT'])){
    echo $arResult['PROPERTIES']['TABLE_2']['~VALUE']['TEXT'];
}

if (!empty($arResult['PROPERTIES']['PACKAGE']['VALUE']['TEXT'])){
    echo $arResult['PROPERTIES']['PACKAGE']['~VALUE']['TEXT'];
}

if (!empty($arResult['PROPERTIES']['QUOTE']['VALUE']['TEXT'])){
    echo $arResult['PROPERTIES']['QUOTE']['~VALUE']['TEXT'];
}

if (!empty($arResult['PROPERTIES']['NOTICE']['VALUE']['TEXT'])){
    echo $arResult['PROPERTIES']['NOTICE']['~VALUE']['TEXT'];
}

if (!empty($arResult['PROPERTIES']['TABLE_1']['VALUE']['TEXT'])){
    echo $arResult['PROPERTIES']['TABLE_1']['~VALUE']['TEXT'];
}
?>


<?if ($arResult['PROPERTIES']['CONSULTATION_SERVICE']['VALUE'] == "Y"){?>
    <div class="cons cons-small in-row-mid">
        <div class="col1">
            <div class="title4"><?=GetMessage('INNET_SERVICES_CONSULTATION')?></div>
            <p>
                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array(
                        "AREA_FILE_SHOW" => "file",
                        "PATH" => SITE_DIR . "include/services/desc_6.php",
                        "EDIT_TEMPLATE" => ""
                    ),
                    false
                );?>
            </p>
        </div>
        <div class="col2"><a class="btn4 popbutton" data-window="5"><?=GetMessage('INNET_SERVICES_CONSULTATION_ASC')?></a></div>
    </div>
<?}?>


<?if (!empty($arResult['PROPERTIES']['DOCS']['VALUE'])){?>
    <div class="margin4">
        <div class="title5 fs24"><?=$arResult['PROPERTIES']['DOCS']['NAME']?></div>
        <div class="docs">
            <?foreach ($arResult['PROPERTIES']['DOCS']['VALUE'] as $docs) {
                $arDoc = CFile::GetByID($docs);
                $doc = $arDoc->GetNext();

                $format_file = end(explode('.', $doc['ORIGINAL_NAME']));
                $name_file = current(explode('.', $doc['ORIGINAL_NAME']));
                $doc_icon = '';

                if ($format_file == 'doc' || $format_file == 'docx') {
                    $doc_icon = SITE_TEMPLATE_PATH . '/images/doc_icon.png';
                } elseif ($format_file == 'pdf') {
                    $doc_icon = SITE_TEMPLATE_PATH . '/images/pdf_icon.png';
                } elseif ($format_file == 'xls' || $format_file == 'xlsx') {
                    $doc_icon = SITE_TEMPLATE_PATH . '/images/xls_icon.png';
                }  elseif ($format_file == 'png' || $format_file == 'jpg' || $format_file == 'jpeg') {
                      $doc_icon = SITE_TEMPLATE_PATH . '/images/xls_icon.png';
                    $doc_img =  $arFileTmp = CFile::ResizeImageGet(
                                $doc,
                              array('width' => 45, 'height' => 60),
                              BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                              true
                            );
                            $doc_icon = $doc_img['src'];
                  }
                ?>
                <div class="in-row-bot">
                    <img src="<?=$doc_icon?>" alt="">
                    <div>
                        <a href="<?=CFile::GetPath($docs)?>" target="_blank"><?=$name_file?></a>
                        <div><?=GetMessage('INNET_SERVICES_SIZE')?>: <?=round($doc['FILE_SIZE'] / 1024, 1)?> <?=GetMessage('INNET_SERVICES_KB')?></div>
                    </div>
                </div>
            <?}?>
        </div>
    </div>
<?}?>


<?if (!empty($arResult['PROPERTIES']['PROJECTS']['VALUE'])){?>
    <div class="projects margin4">
        <div class="title5 fs24"><?=$arResult['PROPERTIES']['PROJECTS']['NAME']?></div>
        <?$GLOBALS["arrFilter"] = array("ID" => $arResult['PROPERTIES']['PROJECTS']['VALUE']);?>
        <?$APPLICATION->IncludeComponent("bitrix:news.list", "projects_services", array(
                "IBLOCK_TYPE" => "innet_objects_" . SITE_ID,
                "IBLOCK_ID" => $arParams['INNET_IBLOCK_ID_PROJECTS'],
                "NEWS_COUNT" => "10",
                "SORT_BY1" => "SORT",
                "SORT_ORDER1" => "ASC",
                "SORT_BY2" => "SORT",
                "SORT_ORDER2" => "ASC",
                "FILTER_NAME" => "arrFilter",
                "FIELD_CODE" => array(),
                "PROPERTY_CODE" => array(),
                "CHECK_DATES" => "Y",
                "DETAIL_URL" => "",
                "AJAX_MODE" => "N",
                "AJAX_OPTION_JUMP" => "N",
                "AJAX_OPTION_STYLE" => "N",
                "AJAX_OPTION_HISTORY" => "N",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "Y",
                "PREVIEW_TRUNCATE_LEN" => "",
                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                "SET_STATUS_404" => "Y",
                "SET_TITLE" => "N",
                "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                "ADD_SECTIONS_CHAIN" => "N",
                "HIDE_LINK_WHEN_NO_DETAIL" => "N",
                "PARENT_SECTION" => "",
                "PARENT_SECTION_CODE" => "",
                "INCLUDE_SUBSECTIONS" => "N",
                "PAGER_TEMPLATE" => "",
                "DISPLAY_TOP_PAGER" => "N",
                "DISPLAY_BOTTOM_PAGER" => "N",
                "PAGER_TITLE" => "",
                "PAGER_SHOW_ALWAYS" => "N",
                "PAGER_DESC_NUMBERING" => "N",
                "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                "PAGER_SHOW_ALL" => "N",
                "AJAX_OPTION_ADDITIONAL" => ""
            ),
            false
        );?>
    </div>
<?}?>