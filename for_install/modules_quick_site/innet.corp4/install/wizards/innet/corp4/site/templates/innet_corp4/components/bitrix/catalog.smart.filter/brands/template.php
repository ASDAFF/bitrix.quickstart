<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<?if (!empty($arResult['BRAND_FILTER'])) {?>
    <div><span><?=GetMessage('INNET_CATALOG_BRANDS')?>:</span> <a href="<?=$arResult['BRAND_FILTER']['LINK']?>" target="_blank"><?=$arResult['BRAND_FILTER']['NAME']?></a></div>
<?}?>