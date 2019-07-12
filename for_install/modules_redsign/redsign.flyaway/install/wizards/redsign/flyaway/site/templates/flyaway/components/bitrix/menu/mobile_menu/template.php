<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if(!function_exists('showMenuItems')) {
    function showMenuItems($items, $isCatalog = false) {

        $lastIndex = count($items) - 1;
        foreach($items as $i => $item):
            $isSubItems = !empty($item['SUB_ITEMS']) && count($item['SUB_ITEMS'] > 0);

            if($item['IS_CATALOG_LINK']) {
                ?><li data-index="<?=$i?>" class="mobile-menu-nav__element __catalog"><?
                    ?><a class="mobile-menu-nav__link disabled" href="<?=$item['LINK']?>"><?=$item['TEXT']?></a><?
                ?></li><?
                if($isSubItems) {
                    showMenuItems($item['SUB_ITEMS'], true);
                }
                continue;
            }
            ?><li class="mobile-menu-nav__element<?if($isCatalog) echo ' __catalog';?><?if($i == $lastIndex) echo ' last';?>"><?
                ?><a class="mobile-menu-nav__link" href="<?=$item['LINK']?>"><?=$item['TEXT']?><?
                if($isSubItems):
                    ?><span class="mobile-menu-nav__arrow"></span><?
                endif;
                ?></a><?
                if($isSubItems):
                    ?><ul class="mobile-menu-nav__submenu js-mobile-menu-nav"><?
                        ?><li class="mobile-menu-nav__element"><?
                            ?><a class="mobile-menu-nav__link back" href="javascript: 0;"><?
                                ?><?=Loc::getMessage('RSFLYAWAY_BACK_BUTTON');?><?
                                ?><span class="mobile-menu-nav__arrow back"></span><?
                            ?></a><?
                        ?></li><?
                        ?><li class="mobile-menu-nav__element"><?
                            ?><a class="mobile-menu-nav__link disabled" href="<?=$item['LINK']?>"><?=$item['TEXT']?></a><?
                        ?></li><?
                        showMenuItems($item['SUB_ITEMS']);
                    ?></ul><?
                endif;
            ?></li><?
        endforeach;
    }

}


if(!empty($arResult)):
?>
<ul class="mobile-menu-nav js-mobile-menu-nav">
    <?php showMenuItems($arResult); ?>
</ul>
<?php endif; 
