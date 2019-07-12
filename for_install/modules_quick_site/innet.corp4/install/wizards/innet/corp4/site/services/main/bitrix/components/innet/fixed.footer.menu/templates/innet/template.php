<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();?>

<?$frame = $this->createFrame()->begin();?>
<?$frame->beginStub();?>
    <!--    check the availability of goods in comparison and deferred    <<<     -->
    <?
    $arCompare = array();
    $arDelay = array();

    if (!empty($_SESSION['CATALOG_COMPARE_LIST'])) {
        foreach ($_SESSION['CATALOG_COMPARE_LIST'] as $val) {
            foreach ($val['ITEMS'] as $product_id => $val2) {
                $arCompare[$product_id] = true;
            }
        }
    }

    if (CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")){
        $dbBasketItemsDelay = CSaleBasket::GetList(array(), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", "DELAY" => "Y"), false, false, array("PRODUCT_ID"));
        while ($arItemsDelay = $dbBasketItemsDelay->Fetch()) {
            $arDelay[$arItemsDelay['PRODUCT_ID']] = true;
        }
    }

    $cntBasketItems = CSaleBasket::GetList(array(), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), array());
    ?>
    <!--    >>>    check the availability of goods in comparison and deferred     -->
    <div class="fixed-menu">
        <div class="on-click">
            <a class="b1" href="<?=SITE_DIR?>personal/cart/"><img src="<?=SITE_TEMPLATE_PATH?>/images/basket.png"><span><?=$cntBasketItems?></span></a>
            <a class="b2" href="<?=SITE_DIR?>catalog/compare/"><img src="<?=SITE_TEMPLATE_PATH?>/images/chosen.png"><span><?=count($arCompare)?></span></a>
        </div>
    </div>
    <script>
        INNET_CATALOG_COMPARE_LIST = <?=json_encode($arCompare)?>;
        INNET_DELAY_LIST = <?=json_encode($arDelay)?>;
    </script>
<?$frame->beginStub();?>
<?$frame->end();?>