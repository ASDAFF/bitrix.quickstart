<?php
if (count($arResult['ELEMENTS'])) {
    $val = array_pop($arResult['ELEMENTS']);
    $detail = $val['DETAIL_PAGE_URL'];
    ?>

    <div class="preview">
        <?php
        // deb($arResult['CURRENT_ELEMENT']["COLORS"][0]['NAME']);
        ?>
        <div class="min-catal-img">
            <?php
            $CURRENT_ELEMENT_COLORS = (is_array($arResult['CURRENT_ELEMENT']["COLORS"]) && count($arResult['CURRENT_ELEMENT']["COLORS"]) > 0) ? $arResult['CURRENT_ELEMENT']["COLORS"] : array(0 => array('ID' => 0));

            foreach ($CURRENT_ELEMENT_COLORS as $key => $color) {
                $style = ($key == 0) ? '' : 'display:none;';

                //$detailLink = $detail . "#color-" . $color['ID'] . "-" .$val['ID'];
                $detailLink = $detail;
                ?>
                <div style="<?= $style ?>" class="card-lider-m catalog-preview-color-element-<?= $val['ID'] ?> catalog-preview-color-element-<?= $val['ID'] ?>-<?= $color['ID'] ?>">
                    <?
                    if ($val['PROPERTIES']['SPECIALOFFER']['VALUE_XML_ID'] == "1")
                        echo '<a href="'.$detail.'#color-'.$color['ID'].'-'. $val['ID'].'"><div class="card-spec-min">%</div></a>';
                    if ($val['PROPERTIES']['NEWPRODUCT']['VALUE_XML_ID'] == "1")
                        echo '<a href="'.$detail.'#color-'.$color['ID'].'-'. $val['ID'].'"><div class="card-new-min">new</div></a>';
                    if ($val['PROPERTIES']['SALELEADER']['VALUE_XML_ID'] == "1")
                        echo '<a href="'.$detail.'#color-'.$color['ID'].'-'. $val['ID'].'"><div class="card-lider-min">'.GetMessage('SALELEADER').'</div></a>';
                    ?>
                </div>
                <a style="<?= $style ?>" href="<?=$detailLink?>"
                   class="detail-card catalog-preview-color-element-<?= $val['ID'] ?> catalog-preview-color-element-<?= $val['ID'] ?>-<?= $color['ID'] ?>">
<? $html =  $APPLICATION->IncludeComponent(
                        "novagroup:catalog.element.photo",
                        "common",
                        Array(
                            "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                            "CATALOG_ELEMENT_ID" => $val['ID'],
                            "PHOTO_ID" => $color['ID'],
                            "PHOTO_WIDTH" => "177",
                            "PHOTO_HEIGHT" => "236",
							"I_FROM_CATALOG" => "Y"
                        ),
                        false,
                        Array(
                            'ACTIVE_COMPONENT' => 'Y',
                            "HIDE_ICONS" => "Y"
                        )
                    );?>
                    <img src="<?=$html;?>" alt="<?= htmlspecialchars($val['NAME'], null, SITE_CHARSET); ?>" width="177"
                         height="236">
                </a>
                <?php if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
                    <span style="cursor:pointer;<?= $style ?>"
                          class="link-popover-card catalog-preview-color-element-<?= $val['ID'] ?> catalog-preview-color-element-<?= $val['ID'] ?>-<?= $color['ID'] ?>">
                    <a style="text-decoration: none;" href="<?=$detailLink?>"
                       name="<?= $val['ID'] ?>" class="quickViewLink"><?= GetMessage('QUICK_VIEW') ?></a>
                </span>
                <?php endif; ?>
            <?php
            }
            ?>
        </div>
        <?php
        foreach ($CURRENT_ELEMENT_COLORS as $key => $color) {
            $style = ($key == 0) ? '' : 'display:none;';
            ?>
            <div style="<?= $style ?>"
                 class="name catalog-preview-color-element-<?= $val['ID'] ?> catalog-preview-color-element-<?= $val['ID'] ?>-<?= $color['ID'] ?>">
                <a class="detail-card" href="<?=$detailLink?>"><?= $val['NAME'] ?></a></div>
        <?php
        }
        ?>
        <div class="color-catalog">
            <?php
            if (is_array($arResult['CURRENT_ELEMENT']["COLORS"]))
                foreach ($arResult['CURRENT_ELEMENT']["COLORS"] as $key => $color) {
                    $addClass = ($key == 0) ? ' active-color' : '';
                    ?>
<? $html = $APPLICATION->IncludeComponent(
                        "novagroup:catalog.element.photo",
                        "common",
                        Array(
                            "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                            "CATALOG_ELEMENT_ID" => $val['ID'],
                            "PHOTO_ID" => $color['ID'],
                            "PHOTO_WIDTH" => "177",
                            "PHOTO_HEIGHT" => "236",
							'I_FROM_CATALOG' => "Y"
                        ),
                        false,
                        Array(
                            'ACTIVE_COMPONENT' => 'Y',
                            "HIDE_ICONS" => "Y"
                        )
                    );?>
                    <span data-pic="<?=$html;?>" data-color-id="<?= $val['ID'] ?>" data-color-code="<?= $color['ID'] ?>"
                          name="data-color-button-<?= $val['ID'] ?>-<?= $color['ID'] ?>"
                          class="button-color-button-<?= $color['ID'] ?> color-min<?= $addClass ?>"><span class="b-C">
                            <span class="<?php
                            print($color['PROPERTY_CLASS_STONE_COLOR_VALUE']);
                            ?>"><i class="icon-diamond"></i></span>
                        </span></span>
                <?php
                }
            ?>
        </div>
        <?php
        $COLOR_PRICES = array();
        if (is_array($arResult['CURRENT_ELEMENT']["COLORS"])) {
            foreach ($arResult['CURRENT_ELEMENT']["COLORS"] as $color) {
                foreach ($arResult['OFFERS'] as $OFFER) {
                    if ($OFFER['DISPLAY_PROPERTIES']['COLOR_STONE']['VALUE'] == $color['ID']) {
                        $COLOR_PRICES[$val['ID']][] = $OFFER['PRICES'][$arResult["BASE_PRICE_CODE"]];
                    }
                }
            }
        }

        // extract the first price - it must be the lowest of all offers
        foreach ($COLOR_PRICES as $PRICES) {

            $some_price = $some_prices = array();
            foreach ($PRICES as $PRICE) {
                $some_price[$PRICE['DISCOUNT_VALUE']] = $PRICE['DISCOUNT_VALUE'];
                $some_prices[$PRICE['DISCOUNT_VALUE']] = $PRICE;
            }

            $min_price = min($some_price);
            $_price = $some_prices[$min_price];
            $from = (count($some_price) > 1) ? GetMessage('PRICE_FROM') : "";

            if ($_price['DISCOUNT_VALUE'] < $_price['VALUE']) {
                ?>
                <div class="price">
                    <div class="actual discount"><a
                            href="<?= $detail ?>"><?= $from . $_price['PRINT_DISCOUNT_VALUE']; ?></a></div>
                    <div class="actual old-price"><a href="<?= $detail ?>"><?= $from . $_price['PRINT_VALUE']; ?></a>
                    </div>
                </div>
            <?php
            } else {
                ?>
                <div class="price">
                    <div class="actual default-value"><a
                            href="<?= $detail ?>"><?= $from . $_price['PRINT_DISCOUNT_VALUE']; ?></a></div>

                </div>
            <?php
            }
        }
        ?>
    </div>
<?php
}
?>
                   