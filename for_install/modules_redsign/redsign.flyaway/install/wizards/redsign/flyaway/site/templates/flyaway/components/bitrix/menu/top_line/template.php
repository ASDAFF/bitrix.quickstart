<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;
?>

<?php if(!empty($arResult)): ?>
<span class="top_line_menu_responsive dropdown visible-xs visible-sm">
    <a class="dropdown-toggle" id="ddTopLineMenu" data-toggle="dropdown" href="#"><i class="fa"></i><?=Loc::getMessage('RS.FLYAWAY.MENU')?></a>
    <ul class="dropdown-menu list-unstyled" aria-labelledby="ddTopLineMenu">
        <?php 
        foreach($arResult as $arItem): 
        if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
            continue;
        ?>
        <li><a href="<?=$arItem["LINK"]?>" <?if($arItem["SELECTED"]):?>class="selected"<?endif;?>><?=$arItem["TEXT"]?></a></li>
        <?php endforeach; ?>
    </ul>
</span>
<ul class="top_line_menu list-unstyled clearfix hidden-xs hidden-sm" style="visibility: hidden">
    <?php foreach($arResult as $arItem): ?>
    <li><a href="<?=$arItem["LINK"]?>" <?if($arItem["SELECTED"]):?>class="selected"<?endif;?>><?=$arItem["TEXT"]?></a></li>
    <?php endforeach; ?>
    <li class = "top_line_menu__more">
        <a href="javascript: void(0);" class="top_line_menu__more-link dropdown-toggle"  data-toggle="dropdown" id="dropdownMoreLink" aria-expanded="true"><?=Loc::getMessage('RS.FLYAWAY.MORE_LINK')?> </a>
        <ul class="dropdown-menu list-unstyled top_line_menu__more-list drop-panel" aria-labelledby="dropdownMoreLink"></ul>
    </li>
        
</ul>
<?php endif; 