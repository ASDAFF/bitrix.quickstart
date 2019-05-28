<?
if ( ! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use MHT\Product;

global $APPLICATION;

$this->addExternalCss($templateFolder . "/inc/TimeCircles.css");
$this->addExternalJS($templateFolder . "/inc/TimeCircles.js");

?>

<div class="stock_main">

    <div class="stock_wrap">
        <p>ОСТАЛОСЬ ВСЕГО</p>
        <div id="CountDownTimer" data-timer="<?= $arResult['DISCOUNT']['ACTIVE_TO_FORMAT_U'] ?>"
             style="width: 420px; height: 115px;"></div>
        <p>ТОЛЬКО СЕЙЧАС СКИДКИ <span class="until">от</span><span><?= round($arResult['DISCOUNT']['VALUE'], 0) ?>
                %</span></p>
    </div>


    <div class="dsc_slider js-upload-to-add">


<?
if ( $arResult['isPost'] ) {
    $APPLICATION->RestartBuffer();
}

    ?>

        <?
        foreach ($arResult['ITEMS'] as $item) {
            $product = Product::byID($item['ID']);
            ?>

            <div class="dsc_product product">
                <a href="<?= $product->get('link') ?>" class="dsc_item">
                    <p class="dsc_count">Скидки от <span><?= round($item['DISCOUNT']['VALUE'], 0) ?>%</span></p>
                    <div class="dsc_item-left-block">

                        <div class="dsc_image"><img class="product_image_original" src="<?= $product->get('small-image', 'src') ?>">
                        </div>

                    </div>
                    <div class="dsc_item-right-block">

                        <div class="item-title"> <?= $item['NAME'] ?> </div>

                        <div class="item-block-price">
                            <div class="item-price"><span class="item-price-value"><?= $product->get('price') ?></span>
                                ₽
                            </div>
                            <a class="item-actions-buy product_cart"
                               href="<?= $product->get('link') ?>/index.php?action=BUY&amp;id=<?= $item['ID'] ?>"
                               data-id="<?= $item['ID'] ?>"
                               onmousedown="try { rrApi.addToBasket(<?= $item['ID'] ?>) } catch(e) {}"></a>

                        </div>
                    </div>
                </a>
            </div>

            <?

        }


?>

<?
if ( $arResult['isPost'] ) {
    die;
}
?>

    </div>


    <?
    $navResult                 = new CDBResult();
    $navResult->NavPageCount   = ceil($arResult['COUNT'] / $arParams['PER_PAGE']);
    $navResult->NavPageNomer   = $arParams['PAGEN'];
    $navResult->NavNum         = $arParams['PAGEN'];
    $navResult->NavPageSize    = $arParams['PER_PAGE'];
    $navResult->NavRecordCount = $arResult['COUNT'];
    ?>


    <?
    $APPLICATION->IncludeComponent(
        "bitrix:system.pagenavigation",
        "ajax",
        array(
            'NAV_RESULT' => $navResult,
            'AJAX_MODE'  => 'Y',
        ),
        false
    );
    ?>

</div>
    </div>


