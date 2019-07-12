<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); $this->setFrameMode(true);

if (count($arResult["ITEMS"]) > 0) {
    ?>
    <div class="span10 scroll-block">
        <h4><?= GetMessage("RECOMMEND") ?></h4>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.slider1').bxSlider({
                    slideWidth: 98,
                    minSlides: 2,
                    maxSlides: 6,
                    slideMargin: 12
                });
            });
        </script>
        <div class="slider1 recom-sl">
            <? foreach ($arResult["ITEMS"] as $item) { ?>
                <div class="slide"><a class="detail-card" href="<?= $item['PRODUCT_URL'] ?>">
                        <?= CFile::ShowImage($item['IMAGE_SRC'], 89, 119, "") ?>
                    </a></div>
            <? } ?>
        </div>
    </div>
<?
}
?>