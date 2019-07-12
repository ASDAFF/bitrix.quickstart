<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$this->setFrameMode(true);
if (!empty($arResult)):
?>

<ul class="nav navbar-nav list-unstyled mainmenu mainmenu--wide mainJS">
    <li class="dropdown mainmenu__other nav other invisible">
        <a href="javascript: void(0)" class="mainmenu__other-link">...</a>
        <ul class="list-unstyled dropdown-menu-right drop-panel mainmenu__other-list nav"></ul>
    </li>
    <?php foreach ($arResult as $rootItem): ?>
    <li class="dropdown lvl1 mainmenu__root-item">
        <a href="<?=$rootItem['LINK']?>" class="mainmenu__item-link element">
            <?=$rootItem['TEXT']?>
        </a>
        <?php if (is_array($rootItem['SUB_ITEMS'])): $countElements = 0; $countSubItems = countMenuItems($rootItem['SUB_ITEMS']) ?>

        <div class="dropdown-submenu nav drop-panel mainmenu__submenu">
            <?php if ($countSubItems < 14): ?> <div class="mainmenu__linecolumns"> <?php endif; ?>
            <?php
            $columns = getColumns($rootItem['SUB_ITEMS'], 3);
            foreach ($columns as $columnItems):
            ?>
            <ul class="list-unstyled mainmenu__column js-mainmenu__column">
            <?php foreach ($columnItems as $secondItem):  ?>
                <?php if ($countElements === 0): ?>
                <?php endif; ?>
                    <li><a href="<?=$secondItem['LINK']?>" class="mainmenu__item-link element"><?=trim($secondItem['TEXT'])?></a>
                        <ul class="list-unstyled mainmenu__subitems">
                            <?php showThirdLevelElements($secondItem['SUB_ITEMS'], $countElements); ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
              </ul>
            <?php endforeach; ?>
            <?php if ($countSubItems < 14): ?> </div> <?php endif; ?>
            <?php if ($arParams['RSFLYAWAY_IS_SHOW_IMAGE'] == "Y" && $rootItem['PARAMS']['PICTURE']): ?>
                <div class="mainmenu__background"
                    style="
                    background-image: url('<?=$rootItem['PARAMS']['PICTURE']?>');
                    <?php if (!empty($rootItem['PARAMS']['PICTURE_POSITION'])) {
                echo 'background-position: '.$rootItem['PARAMS']['PICTURE_POSITION'].';';
            } ?>
                    <?php if (!empty($rootItem['PARAMS']['PICTURE_OFFSET_X'])) {
                echo 'left: '.$rootItem['PARAMS']['PICTURE_OFFSET_X'].';';
            } ?>
                    <?php if (!empty($rootItem['PARAMS']['PICTURE_OFFSET_Y'])) {
                echo 'top: '.$rootItem['PARAMS']['PICTURE_OFFSET_Y'].';';
            } ?>
                    "
                >
                </div>
                <?php if ($countSubItems < 14): ?> <div class="list-unstyled mainmenu__column js-mainmenu__column"></div><?php endif; ?>
            <?php endif; ?>
            <?php if ($arParams['RSFLYAWAY_IS_SHOW_PRODUCTS'] && !empty($arParams['PROPERTY_CODE_ELEMENT_IN_MENU'])): ?>
                <?php
                global $menuProductsFilter;
                $menuProductsFilter = array("!PROPERTY_".$arParams['PROPERTY_CODE_ELEMENT_IN_MENU'] => false);
                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "inmenu",
                    array(
                        "IBLOCK_TYPE" => $arParams['IBLOCK_ID'],
                        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                        "SECTION_ID" => $rootItem["PARAMS"]["SECTION_ID"],
                        "SECTION_CODE" => "",
                        "SECTION_USER_FIELDS" => array(
                            0 => "",
                            1 => "",
                        ),
                        "ELEMENT_SORT_FIELD" => "",
                        "ELEMENT_SORT_ORDER" => "",
                        "ELEMENT_SORT_FIELD2" => "sort",
                        "ELEMENT_SORT_ORDER2" => "asc",
                        "FILTER_NAME" => "menuProductsFilter",
                        "INCLUDE_SUBSECTIONS" => "A",
                        "SHOW_ALL_WO_SECTION" => "Y",
                        "HIDE_NOT_AVAILABLE" => "N",
                        "PAGE_ELEMENT_COUNT" => "1",
                        "LINE_ELEMENT_COUNT" => "3",
                        "PROPERTY_CODE" => array(
                            0 => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
                        ),
                        "OFFERS_FIELD_CODE" => array(
                            0 => "ID"
                        ),
                        "OFFERS_PROPERTY_CODE" => array(
                            0 => $arParams['RSFLYAWAY_PROP_SKU_MORE_PHOTO']
                        ),
                        "OFFERS_SORT_FIELD" => "sort",
                        "OFFERS_SORT_ORDER" => "asc",
                        "OFFERS_SORT_FIELD2" => "id",
                        "OFFERS_SORT_ORDER2" => "desc",
                        "OFFERS_LIMIT" => "1",
                        "SECTION_URL" => "",
                        "DETAIL_URL" => "",
                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                        "SEF_MODE" => "N",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_GROUPS" => "Y",
                        "SET_LAST_MODIFIED" => "N",
                        "USE_MAIN_ELEMENT_SECTION" => "N",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "CACHE_FILTER" => "N",
                        "ACTION_VARIABLE" => "sas",
                        "PRODUCT_ID_VARIABLE" => "id",
                        "PRICE_CODE" => $arParams['PRICE_CODE'],
                        "USE_PRICE_COUNT" => "N",
                        "SHOW_PRICE_COUNT" => "1",
                        "PRICE_VAT_INCLUDE" => "N",
                        "CONVERT_CURRENCY" => "N",
                        "BASKET_URL" => "/personal/basket.php",
                        "USE_PRODUCT_QUANTITY" => "N",
                        "PRODUCT_QUANTITY_VARIABLE" => "",
                        "ADD_PROPERTIES_TO_BASKET" => "Y",
                        "PRODUCT_PROPS_VARIABLE" => "prop",
                        "PARTIAL_PRODUCT_PROPERTIES" => "N",
                        "PRODUCT_PROPERTIES" => array(
                        ),
                        "OFFERS_CART_PROPERTIES" => array(
                        ),
                        "PAGER_TEMPLATE" => ".default",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "PAGER_BASE_LINK_ENABLE" => "N",
                        "SET_STATUS_404" => "N",
                        "SHOW_404" => "N",
                        "MESSAGE_404" => "",
                        "SET_TITLE" => "N",
                        "SET_BROWSER_TITLE" => "N"
                    ),
                    $component,
                    array("HIDE_ICONS"=>"Y")
                );
                ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>

</ul>
<?php endif;

function showThirdLevelElements($elements, &$countElements)
{
    if (empty($elements) && !is_array($elements)) {
        return;
    }
    echo '<pre style="display: none">';
    var_dump($elements);
    echo '</pre>';
    foreach ($elements as $element) {
        $countElements++; ?>
        <li><a href="<?=$element['LINK']?>" class="mainmenu__item-link element"><?=$element['TEXT']?></a></li>
        <?php
        if (!empty($element['SUB_ITEMS']) && is_array($element['SUB_ITEMS'])) {
            showThirdLevelElements($element['SUB_ITEMS'], $countElements);
        }
    }
}

function countMenuItems(&$arItems)
{
    $count = count($arItems);

    foreach ($arItems as $arItem) {
        if (!empty($arItem['SUB_ITEMS']) && is_array($arItem['SUB_ITEMS'])) {
            $count += countMenuItems($arItem['SUB_ITEMS']);
        }
    }

    return $count;
}

function getColumns($arMenuItems, $columnsCount = 4)
{
    $columns = array();
    $columnWeight = 0;

    for ($i = 0; $i < $columnsCount; $i++) {
        $columns[$i] = array();

        $columnWeight = countMenuItems($arMenuItems) / ($columnsCount - $i);

        $weight = 0;
        while (!empty($arMenuItems[0])) {
            $columns[$i][] = $arMenuItems[0];

            $weight++;
            if (!empty($arMenuItems[0]['SUB_ITEMS'])) {
                $weight += countMenuItems($arMenuItems[0]['SUB_ITEMS']);
            }

            array_shift($arMenuItems);

            if ($weight >= $columnWeight || ($i >= $countMenuItems - 2 && count($arMenuItems) < 1)) {
                break;
            }
        }
    }

    return $columns;
}
