<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (empty($arResult))
    return;
?>
<ul id="categories-menu">
    <? foreach ($arResult["ALL_ITEMS_ID"] as $itemIdLevel_1 => $arItemsLevel_2): ?> <!-- first level-->
        <li class="categories-menu-item-top cat<?= $arResult["ID"] ?>">
            <a href="<?= $arResult["ALL_ITEMS"][$itemIdLevel_1]["LINK"] ?>" class="menu-separator"><?= $arResult["ALL_ITEMS"][$itemIdLevel_1]["TEXT"] ?></a>
            <? if (is_array($arItemsLevel_2) && !empty($arItemsLevel_2)): ?>
                <ul class="categories-level1">
                    <? foreach ($arItemsLevel_2 as $itemIdLevel_2 => $arItemsLevel_3): ?> <!-- second level-->
                        <li><a href="<?= $arResult["ALL_ITEMS"][$itemIdLevel_2]["LINK"] ?>" class="menu-separator"><?= $arResult["ALL_ITEMS"][$itemIdLevel_2]["TEXT"] ?></a>
                            <? if (is_array($arItemsLevel_3) && !empty($arItemsLevel_3)): ?>
                                <ul class="categories-level2">	
                                    <? foreach ($arItemsLevel_3 as $itemIdLevel_3): ?> <!-- third level-->
                                        <li><a href="<?= $arResult["ALL_ITEMS"][$itemIdLevel_3]["LINK"] ?>" class="menu-item"><?= $arResult["ALL_ITEMS"][$itemIdLevel_3]["TEXT"] ?></a></li>
                                    <? endforeach ?>
                                </ul>
                            <? endif ?>
                        </li>
                    <? endforeach ?>
                </ul>
            <? endif ?>
        </li><li class="categories-menu-item-separator"></li>
        <? endforeach ?>
</ul>
<!--<a href="" class="menu-separator-user">
        <span class="menu-item-avatar" style=""></span>
</a>
-->

