<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult)):?>
<ul>
<?foreach($arResult as $arItem):?>
    <?if($arItem['PARAMS']['FROM_IBLOCK']=="1"):?>
        <li><a href="<?=$arItem["LINK"]?>" <?=$class?> <?=$dataToggle?>><?=$arItem["TEXT"]?></a></li>
    <?endif?>
<?endforeach?>
</ul>
<?endif?>