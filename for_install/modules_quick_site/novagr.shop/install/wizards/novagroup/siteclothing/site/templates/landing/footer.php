<?php 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);

?>

    </div>

    <div id="push"></div>
    </div>
</div>
    <div id="footer">
        <div class="copyright">
            <div class="sub link-click">
                <!--<?=GetMessage("T_COPYRIGHT_COMMENT")?>-->
                <div class="copy"><a href="<?= SITE_DIR ?>"><?=GetMessage("T_COPYRIGHT_LABEL")?></a> <span class="right"><span class="cp">&copy; <?=date("Y")?>&nbsp;&nbsp; <?=GetMessage("T_RIGHT_PROTECTED")?></span><?=GetMessage("T_DEVELOPED_IN")?> <a href="http://trendylist.ru/" target="_blank">TrendyList</a></span></div>

                <div class="clear"></div>
            </div>
        </div>
    </div>

<?

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/product.js');
$APPLICATION->IncludeFile(SITE_DIR."include/pSubscribe.php");
?>
</body>
</html>
