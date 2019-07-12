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
            <div class="card-lider-m">
                <?
                    if($val['PROPERTIES']['SPECIALOFFER']['VALUE_XML_ID'] == "1")
                        echo'<div class="card-spec-min"></div>';
                    if($val['PROPERTIES']['NEWPRODUCT']['VALUE_XML_ID'] == "1")
                        echo'<div class="card-new-min"></div>';
                    if($val['PROPERTIES']['SALELEADER']['VALUE_XML_ID'] == "1")
                        echo'<div class="card-lider-min"></div>';
                ?>
            </div>    
            <?php 
                $CURRENT_ELEMENT_COLORS = (is_array($arResult['CURRENT_ELEMENT']["COLORS"]) && count($arResult['CURRENT_ELEMENT']["COLORS"])>0) ? $arResult['CURRENT_ELEMENT']["COLORS"] : array( 0 => array ( 'ID' => 0 ) );

                foreach ($CURRENT_ELEMENT_COLORS as $key => $color) {
                    $style = ($key == 0 ) ? '' : 'display:none;';
                ?>
                <a style="<?=$style?>" href="<?=$detail?>#color-<?=$color['ID']?>-<?=$val['ID']?>" class="detail catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>">
                    <img src="<?$APPLICATION->IncludeComponent(
                                "novagroup:catalog.element.photo",
                                "common",    
                                Array(
                                    "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                                    "CATALOG_ELEMENT_ID" => $val['ID'],
                                    "PHOTO_ID" => $color['ID'],
                                    "PHOTO_WIDTH" => "177",
                                    "PHOTO_HEIGHT" => "236"
                                ),
                                false,
                                Array(
                                    'ACTIVE_COMPONENT' => 'Y',
                                    "HIDE_ICONS"=>"Y"
                                )
                            );?>" alt="<?=htmlspecialchars($val['NAME'],null,SITE_CHARSET);?>" width="177" height="236">
                </a>
                <?php if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
                <span style="cursor:pointer;<?=$style?>" class="link-popover-card catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>">
                    <a style="text-decoration: none;" href="<?=$detail?>#color-<?=$color['ID']?>-<?=$val['ID']?>" name="<?=$val['ID']?>" class="quickViewLink"><?=GetMessage('QUICK_VIEW')?></a>
                </span>
                <?php endif;?>
                <?php  
                }
            ?>
        </div>
        <?php 
            foreach ($CURRENT_ELEMENT_COLORS as $key => $color) {
                $style = ($key == 0 ) ? '' : 'display:none;';
            ?>
            <div style="<?=$style?>" class="name catalog-preview-color-element-<?=$val['ID']?> catalog-preview-color-element-<?=$val['ID']?>-<?=$color['ID']?>"><a href="<?=$detail?>#color-<?=$color['ID']?>-<?=$val['ID']?>"><?=$val['NAME']?></a></div>
            <?php  
            }
        ?>
        <div class="color-catalog">
            <?php 
                if (is_array($arResult['CURRENT_ELEMENT']["COLORS"]))
                    foreach ($arResult['CURRENT_ELEMENT']["COLORS"] as $key => $color) {
                        $addClass = ($key == 0 ) ? ' active-color' : '';
                    ?>
                    <span data-pic="<?$APPLICATION->IncludeComponent(
                        "novagroup:catalog.element.photo",
                        "path",
                        Array(
                            "CATALOG_IBLOCK_ID" => $val['IBLOCK_ID'],
                            "CATALOG_ELEMENT_ID" => $val['ID'],
                            "PHOTO_ID" => $color['ID'],
                            "PHOTO_WIDTH" => "177",
                            "PHOTO_HEIGHT" => "236"
                        ),
                        false,
                        Array(
                            'ACTIVE_COMPONENT' => 'Y',
                            "HIDE_ICONS"=>"Y"
                        )
                    );?>" data-color-id="<?=$val['ID']?>" data-color-code="<?=$color['ID']?>" name="data-color-button-<?=$val['ID']?>-<?=$color['ID']?>" class="button-color-button-<?=$color['ID']?> color-min<?=$addClass?>"><div class="b-C"><span ><?=CFile::ShowImage($color['PREVIEW_PICTURE'], 0, 0, " width='12' height='10' class='88' alt='".htmlspecialchars($color["NAME"],null,SITE_CHARSET)."' ");?></span></div></span>
                    <?php  
                }
            ?>
        </div>
        <?php 
        $COLOR_PRICES = array();
            if (is_array($arResult['CURRENT_ELEMENT']["COLORS"]))
            { 
                foreach ($arResult['CURRENT_ELEMENT']["COLORS"] as $color) {
                    foreach($arResult['OFFERS'] as $OFFER)
                    {
                        if($OFFER['DISPLAY_PROPERTIES']['COLOR_STONE']['VALUE']==$color['ID']){
                            $COLOR_PRICES[$val['ID']][] = $OFFER['PRICES'][$arResult["BASE_PRICE_CODE"]];
                        }
                    }     
                }
            }

            // extract the first price - it must be the lowest of all offers
            foreach($COLOR_PRICES as $PRICES)
            {
                
                $some_price = $some_prices = array();
                foreach($PRICES as $PRICE)
                {
                    $some_price[$PRICE['DISCOUNT_VALUE']] = $PRICE['DISCOUNT_VALUE'];
                    $some_prices[$PRICE['DISCOUNT_VALUE']] = $PRICE;
                }
               
                $min_price = min($some_price);
                $_price = $some_prices[$min_price];
                $from = (count($some_price)>1) ? GetMessage('PRICE_FROM') : "";

                if ($_price['DISCOUNT_VALUE'] < $_price['VALUE']) {
                ?>
                <div class="price">
                    <div class="actual discount"><a href="<?=$detail?>"><?=$from.$_price['PRINT_DISCOUNT_VALUE'];?></a></div>
                    <div class="actual old-price"><a href="<?=$detail?>"><?=$from.$_price['PRINT_VALUE'];?></a></div>
                </div>
                <?php 
                } else {
                ?>
                <div class="price">
                    <div class="actual default-value"><a href="<?=$detail?>"><?=$from.$_price['PRINT_DISCOUNT_VALUE'];?></a></div>

                </div>
                <?php 
                }
            }
        ?>
    </div>
    <?php
    }
?>
                   