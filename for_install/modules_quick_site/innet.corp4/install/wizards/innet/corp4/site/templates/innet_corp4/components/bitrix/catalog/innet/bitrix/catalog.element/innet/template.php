<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$this->setFrameMode(true);?>

<div class="element" data-element_id="<?=$arResult['ID']?>" data-page="detail">
    <div class="card-top clearfix">
        <div class="pull-left topTabs">
            <a href="#tabs-1"><span><?=GetMessage('INNET_CATALOG_ELEMENT_DESCRIPTION')?></span></a>
            <?if (!empty($arResult['PROPERTIES']['DOCS']['VALUE'])){?>
                <a href="#tabs-2"><span><?=GetMessage('INNET_CATALOG_ELEMENT_DOCS')?></span></a>
            <?}?>
            <a href="#tabs-3"><span><?=GetMessage('INNET_CATALOG_ELEMENT_HOW_BUY')?></span></a>
            <a href="#tabs-4"><span><?=GetMessage('INNET_CATALOG_ELEMENT_SHIPPING')?></span></a>
            <a href="#tabs-5"><span><?=GetMessage('INNET_CATALOG_ELEMENT_PAYMENT')?></span></a>
            <a href="#tabs-6"><span><?=GetMessage('INNET_CATALOG_ELEMENT_WARRANTY')?></span></a>
            <a href="#tabs-7"><span><?=GetMessage('INNET_CATALOG_ELEMENT_RETURN')?></span></a>
            <?if ($arParams['INNET_ALLOW_REVIEWS'] == 'Y'){?>
                <a href="#tabs-8"><span><?=GetMessage('INNET_CATALOG_ELEMENT_REVIEWS')?></span> <i>(<?=($arResult['BLOG_COMMENTS_CNT']['VALUE']) ? $arResult['BLOG_COMMENTS_CNT']['VALUE'] : 0?>)</i></a>
            <?}?>
        </div>
        <?/*<div class="pull-right">
            <a href="#"><?=GetMessage('INNET_CATALOG_ELEMENT_FOUND_ERROR')?></a>
            <a href="#"><?=GetMessage('INNET_CATALOG_ELEMENT_SEND_DESCRIPTION_EMAIL')?></a>
            <a href="#" class="btn-print"><?=GetMessage('INNET_CATALOG_ELEMENT_PRINT')?></a>
        </div>*/?>
    </div>

    <div class="card-wrap">
        <div class="card-gallery">
            <?if (!empty($icon_sale)){?>
                <div class="label label-red label-sec"><?=$icon_sale?></div>
            <?} else if ($arResult['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE_XML_ID'] == 'new'){?>
                <div class="label label-green"><?=$arResult['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE']?></div>
            <?} else if ($arResult['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE_XML_ID'] == 'special'){?>
                <div class="label label-blue"><?=$arResult['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE']?></div>
            <?} else {?>
                <?if (!empty($arResult['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE'])){?>
                    <div class="label label-purple"><?=$arResult['PROPERTIES']['NEW_SPECIAL_OFFER']['VALUE']?></div>
                <?}?>
            <?}?>
            <div class="bxslider1 catalog_slider">
                <ul>
                    <?foreach ($arResult['IMAGES_SLIDER'] as $key => $pic){?>
                        <li><a href="<?=$pic['SRC']?>" data-gal="prettyPhoto[Gallery]"><img src="<?=$pic['SRC']?>" alt="<?=$arResult['NAME']?>"></a></li>
                    <?}?>
                </ul>
            </div>
            <div id="bx-pager1" class="small-slider-box">
                <?foreach ($arResult['IMAGES_SLIDER'] as $key => $pic){?>
                    <a data-slide-index="<?=$key?>" href=""><span><img src="<?=$pic['RESIZE_SRC']['src']?>" alt="<?=$arResult['NAME']?>"></span></a>
                <?}?>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                $('.bxslider1 ul').bxSlider({
                    pagerCustom: '#bx-pager1',
                    controls:false
                });
            });
        </script>

        <div class="card-main">
            <div class="card-more">
                <div class="card-more-main">
                    <?if (!empty($arResult['PROPERTIES']['ARTICLE']['VALUE'])){?>
                        <div><span><?=$arResult['PROPERTIES']['ARTICLE']['NAME']?>:</span> <span class="article"><?=$arResult['PROPERTIES']['ARTICLE']['VALUE']?></span></div>
                    <?}?>
                    <?if (!empty($arResult['PROPERTIES']['BRAND']['VALUE'])){?>
                        <?if (!empty($arResult['PROPERTIES']['BRAND']['VALUE'])){?>
                            <div><span><?=$arResult['PROPERTIES']['BRAND']['NAME']?>:</span> <?=$arResult['PROPERTIES']['BRAND']['VALUE']?></div>
                        <?}?>
                    <?}?>
                </div>
                <?if (!empty($arResult['PROPERTIES']['PRESENCE']['VALUE'])){?>
                    <?
                    $label_availability = '';
                    switch ($arResult['PROPERTIES']['PRESENCE']['VALUE_XML_ID']) {
                        case 'order':
                            $label_availability = 'bx_order';
                            break;
                        case 'availability':
                            $label_availability = 'bx_available';
                            break;
                        case 'notavailabile':
                            $label_availability = 'bx_notavailable';
                            break;
                    }
                    ?>
                    <div>
                        <span class="item-status <?=$label_availability?>"><?=$arResult['PROPERTIES']['PRESENCE']['VALUE']?></span>
                        <?if (!empty($arResult['PROPERTIES']['QUANTITY_PRODUCT']['VALUE'])){?>
                            (<span class="catalog_quantity"></span> <?=$arResult['PROPERTIES']['QUANTITY_PRODUCT']['VALUE']?>)
                        <?}?>
                    </div>
                <?}?>
            </div>
            <?if (!empty($arResult['DISPLAY_PROPERTIES'])){?>
                <div>
                    <div class="characteristics">
                        <?foreach (array_slice($arResult['DISPLAY_PROPERTIES'], 0, 5) as $prop){?>
                            <div>
                                <div class="col1"><?=$prop['NAME']?>:</div>
                                <div class="col2">
                                    <?
                                    if (is_array($prop['VALUE'])) {
                                        if (!empty($prop['VALUE']['TEXT'])){
                                            echo $prop['VALUE']['TEXT'];
                                        } else {
                                            foreach ($prop['VALUE'] as $val){
                                                echo $val . ' ';
                                            }
                                        }
                                    } else {
                                        echo $prop['VALUE'];
                                    }
                                    ?>
                                </div>
                            </div>
                        <?}?>
                        <div class="topTabs"><a href="#tabs-1" class="btn-style5 toogle-block-title"><span><?=GetMessage('INNET_CATALOG_ELEMENT_DETAILED_FEATURES')?></span></a></div>
                    </div>
                </div>
            <?}?>
            <div class="share-block">
                <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/contacts/share.php", "EDIT_TEMPLATE" => "" ), false );?>
            </div>
        </div>

        <div class="col3">
            <div class="card-info">
                <?
                $arPrice = INNETGetPrice(
                    $arResult['PROPERTIES']['PRICE']['VALUE'],
                    $arResult['PROPERTIES']['SALE']['VALUE'],
                    $arResult['PROPERTIES']['SALE_TYPE']['VALUE_XML_ID'],
                    $arResult['PROPERTIES']['CURRENCY']['VALUE']
                );
                ?>
                <?if (!empty($arPrice['PRICE'])){?>
                    <div class="card-price card-price-sale">
                        <div class="col1">
                            <?if (!empty($arPrice['OLD_PRICE'])){?>
                                <p><?=GetMessage('INNET_CATALOG_ELEMENT_OLD_PRICE')?>:</p>
                                <div class="price-old old_price"><?=$arPrice['OLD_PRICE']?></div>
                                <br/>
                                <p><?=GetMessage('INNET_CATALOG_ELEMENT_NEW_PRICE')?>:</p>
                            <?} else {?>
                                <p><?=GetMessage('INNET_CATALOG_ELEMENT_PRICE')?>:</p>
                            <?}?>
                            <div class="price"><?=$arPrice['PRICE']?></div>
                            <?if (!empty($arPrice['OLD_PRICE'])){?>
                                <br/><br/>
                                <div class="price_diff_box"><?=GetMessage('INNET_CATALOG_ELEMENT_SALE')?>: <span class="price_diff"><?=$arPrice['PRICE_DIFF']?></span></div>
                            <?}?>
                        </div>
                    </div>
                <?}?>
                <div class="lvl2">
                    <?if ($arResult['PROPERTIES']['QUICK_ORDER']['VALUE'] == 'Y'){?>
                        <a class="btn-style4 btn-fast-order popbutton" data-window="order_product"><?=GetMessage('INNET_CATALOG_ELEMENT_QUICK_ORDER')?></a>
                    <?}?>
                    <?if ($arResult['PROPERTIES']['QUESTION_PRODUCT']['VALUE'] == 'Y'){?>
                        <a class="btn question_product popbutton" data-window="question_product"><?=GetMessage('INNET_CATALOG_ELEMENT_ASK_QUESTION')?></a>
                    <?}?>
                </div>
            </div>
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/shipping_payment.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>
    </div>

    <div id="tabs">
        <div class="tabs">
            <ul>
                <li class="current"><a href="#tabs-1"><span><?=GetMessage('INNET_CATALOG_ELEMENT_DESCRIPTION')?></span></a></li>
                <?if (!empty($arResult['PROPERTIES']['DOCS']['VALUE'])){?>
                    <li><a href="#tabs-2"><span><?=$arResult['PROPERTIES']['DOCS']['NAME']?></span></a></li>
                <?}?>
                <li><a href="#tabs-3"><span><?=GetMessage('INNET_CATALOG_ELEMENT_HOW_BUY')?></span></a></li>
                <li><a href="#tabs-4"><span><?=GetMessage('INNET_CATALOG_ELEMENT_SHIPPING')?></span></a></li>
                <li><a href="#tabs-5"><span><?=GetMessage('INNET_CATALOG_ELEMENT_PAYMENT')?></span></a></li>
                <li><a href="#tabs-6"><span><?=GetMessage('INNET_CATALOG_ELEMENT_WARRANTY')?></span></a></li>
                <li><a href="#tabs-7"><span><?=GetMessage('INNET_CATALOG_ELEMENT_RETURN')?></span></a></li>
                <?if (!$arParams['INNET_QUICK_VIEW'] && $arParams['INNET_ALLOW_REVIEWS'] == 'Y'){?>
                    <li><a href="#tabs-8"><span><?=GetMessage('INNET_CATALOG_ELEMENT_REVIEWS')?></span> <i>(<?=($arResult['BLOG_COMMENTS_CNT']['VALUE']) ? $arResult['BLOG_COMMENTS_CNT']['VALUE'] : 0?>)</i></a></li>
                <?}?>
            </ul>
        </div>

        <div class="tab" id="tabs-1">
            <?if (!empty($arResult['DISPLAY_PROPERTIES'])){?>
                <div>
                    <div class="characteristics">
                        <?foreach ($arResult['DISPLAY_PROPERTIES'] as $prop){?>
                            <div>
                                <div class="col1"><?=$prop['NAME']?>:</div>
                                <div class="col2">
                                    <?
                                    if (is_array($prop['VALUE'])) {
                                        if (!empty($prop['VALUE']['TEXT'])){
                                            echo $prop['VALUE']['TEXT'];
                                        } else {
                                            foreach ($prop['VALUE'] as $val){
                                                echo $val . ' ';
                                            }
                                        }
                                    } else {
                                        echo $prop['VALUE'];
                                    }
                                    ?>
                                </div>
                            </div>
                        <?}?>
                    </div>
                </div>
            <?}?>
            <?if (!empty($arResult['DETAIL_TEXT'])){?>
                <div>
                    <div class="title-style3"><?=GetMessage('INNET_CATALOG_ELEMENT_DESCRIPTION')?></div>
                    <div class="text2">
                        <?=$arResult['DETAIL_TEXT']?>
                    </div>
                </div>
            <?}?>
        </div>

        <?if (!empty($arResult['PROPERTIES']['DOCS']['VALUE'])){?>
            <div class="tab" id="tabs-2">
                <div>
                    <div class="title-style3"><?=$arResult['PROPERTIES']['DOCS']['NAME']?></div>
                    <div class="docs">
                        <?foreach ($arResult['PROPERTIES']['DOCS']['VALUE'] as $docs) {
                            $arDoc = CFile::GetByID($docs);
                            $doc = $arDoc->GetNext();

                            $format_file = end(explode('.', $doc['ORIGINAL_NAME']));
                            $name_file = current(explode('.', $doc['ORIGINAL_NAME']));
                            $doc_icon = '';

                            if ($format_file == 'doc' || $format_file == 'docx') {
                                $doc_icon = SITE_TEMPLATE_PATH . '/images/doc_icon.png';
                            } elseif ($format_file == 'pdf') {
                                $doc_icon = SITE_TEMPLATE_PATH . '/images/pdf_icon.png';
                            } elseif ($format_file == 'xls' || $format_file == 'xlsx') {
                                $doc_icon = SITE_TEMPLATE_PATH . '/images/xls_icon.png';
                            }
                            ?>
                            <div class="in-row-bot">
                                <img src="<?=$doc_icon?>">
                                <div>
                                    <a href="<?=CFile::GetPath($docs)?>" target="_blank"><?=$name_file?></a>
                                    <div><?=GetMessage('INNET_CATALOG_ELEMENT_SIZE')?>: <?=round($doc['FILE_SIZE'] / 1024, 1)?> <?=GetMessage('INNET_CATALOG_ELEMENT_KB')?></div>
                                </div>
                            </div>
                        <?}?>
                    </div>
                </div>
            </div>
        <?}?>

        <div class="tab" id="tabs-3">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/buy.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>

        <div class="tab" id="tabs-4">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/delivery.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>

        <div class="tab" id="tabs-5">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/payment.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>

        <div class="tab" id="tabs-6">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/guarantee.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>

        <div class="tab" id="tabs-7">
            <?$APPLICATION->IncludeComponent("bitrix:main.include", ".default", array( "AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR . "include/company/return.php", "EDIT_TEMPLATE" => "" ), false );?>
        </div>