<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<?foreach($arResult["TWITS"] as $twit):?>
    <?=$twit["TEXT"]?>&nbsp;<strong><?=$twit["DATE"]?></strong>&nbsp;<?=$arResult["LINK"]?>
<?endforeach;?>