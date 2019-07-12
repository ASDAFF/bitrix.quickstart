<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="slide bg-gray projects">
    <div class="inner">
        <div class="title"><span><?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/projects_1.php", "EDIT_TEMPLATE" => "" ), false );?></span></div>
        <p class="margin1">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/index/projects_2.php", "EDIT_TEMPLATE" => "" ), false );?>
        </p>
        <div class="owl-carousel">
            <?foreach ($arResult['ITEMS'] as $arItem){?>
                <?$pic = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array("width" => 290, "height" => 160), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
                <div>
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="hid"><img src="<?=$pic['src']?>" alt="<?=$arItem['NAME']?>"></a>
                    <div class="title3"><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a></div>
                    <p><?=TruncateText($arItem['PREVIEW_TEXT'], 150)?></p>
                </div>
            <?}?>
        </div>
    </div>
</div>