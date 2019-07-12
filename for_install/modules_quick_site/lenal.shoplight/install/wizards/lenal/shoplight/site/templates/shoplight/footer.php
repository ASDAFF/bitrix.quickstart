<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>


</section>
</div>
<footer class="b-footer">
    <? if ($APPLICATION->GetCurPage(false) == SITE_DIR): ?> 
        <?
        $APPLICATION->IncludeFile(
                SITE_DIR . "include/news.php", Array(), Array("MODE" => "html")
        );
        ?>
    <? endif; ?>
    <div class="b-footer__bottom">
        <?
        $APPLICATION->IncludeComponent("bitrix:menu", "bottom_menu", array(
            "ROOT_MENU_TYPE" => "top",
            "MENU_CACHE_TYPE" => "A",
            "MENU_CACHE_TIME" => "36000000",
            "MENU_CACHE_USE_GROUPS" => "Y",
            "MENU_CACHE_GET_VARS" => array(
            ),
            "MAX_LEVEL" => "1",
            "USE_EXT" => "Y",
            "DELAY" => "N",
            "ALLOW_MULTI_SELECT" => "N"
                ), false
        );
        ?>

        <div class="b-footer__support"><?
            $APPLICATION->IncludeFile(
                    SITE_DIR . "include/footer_phone.php", Array(), Array("MODE" => "html")
            );
            ?></div>
        <div class="b-footer__copyright"><?
            $APPLICATION->IncludeFile(
                    SITE_DIR . "include/copyright.php", Array(), Array("MODE" => "html")
            );
            ?></div>
    </div>

</footer>
</div>

<script type="text/javascript">
function add2cart(id) {
    var qtu = $('#quantity_select_' + id).val(),
            href = '<?=SITE_DIR?>ajax/ajax.php';
            if (qtu == 'undefined') qtu = 1; 
    $.get(href, {count: qtu, id: id, action: 'additem'}).done(function(data) {
        $('#cartNav a.b-main-menu__item-link').html(data);
        $('.b-shadow, .b-dialog').show();
    })
}
    $(function() {
        var dialog = $('.b-dialog');
        $(dialog).on('hide', function() {
            var dialog = this;
            $('.b-shadow').hide().unbind('click');
            $(dialog).hide();
        }).on('show', function() {
            var dialog = this;
            $(dialog).show();
            $('.b-shadow').show()
                    .click(function() {
                        $(dialog).find('.b-dialog__close').click();
                    });
        }).find('.b-dialog__close').click(function() {
            $(dialog).trigger('hide');
        });
    });
</script>
<div class="b-shadow"></div>

</body></html>