<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
IncludeTemplateLangFile(__FILE__);
?>
</div>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div class="row">
                <div class="col-sm-12">
                    <div class="footer__text">
                        <?=GetMessage('FOOTER_CONTACTS')?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="row">
                <div class="col-sm-8">
                    <?
                    $APPLICATION->IncludeComponent(
                        "msnet:variable.set",
                        "soc_footer",
                        [
                            "COMPONENT_TEMPLATE" => "soc_header",
                            "LINK_VK" => "https://vk.com/nervyofficial",
                            "LINK_INSTAGRAM" => "https://www.instagram.com/nervy_official/",
                            "LINK_YOUTUBE" => "https://www.youtube.com/channel/UC1bb2kw4IZwbUNHC1LeG4Zg",
                            "BLOCK_TITLE" => GetMessage('FOOTER_SOC_TITLE')
                        ],
                        false
                    );
                    ?>
                </div>
                <div class="col-sm-4">
                    <div class="footer__copyright">
                        <?=GetMessage('FOOTER_COPYRIGHT')?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>