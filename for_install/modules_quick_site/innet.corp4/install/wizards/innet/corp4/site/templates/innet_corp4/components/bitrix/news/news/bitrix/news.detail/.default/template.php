<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['DETAIL_PICTURE']['ID'])){?>
    <?$res_detail = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array("width" => 300, "height" => 218), BX_RESIZE_IMAGE_EXACT, true);?>
    <img class="detail_img" src="<?=$res_detail['src']?>" alt="<?=$arResult['NAME']?>"/>
<?}?>

<p><?=$arResult['DETAIL_TEXT'];?></p>

<div class="share-block">
    <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/share.php", "EDIT_TEMPLATE" => "" ), false );?>
</div>