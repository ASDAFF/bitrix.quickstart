<?IncludeTemplateLangFile(__FILE__);?>
<?if(defined('PERSONAL')):?>
</div>
</section>
<?endif?>
<?if(!defined('ERROR_404')):?>
</div>
<?endif?>

<?if(defined('INDEX')):?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "page",
        "AREA_FILE_SUFFIX" => "subscribe",
        "EDIT_TEMPLATE" => ""
    )
);?>
<?endif?>
</section>

<div class="clearfix visible-xs visible-sm"></div>
<!-- fixes floating problems when mobile menu is visible -->

<footer>
  <div class="container">
    <section class="row">
     <?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
    "ROOT_MENU_TYPE" => "bottom_1",
    "MENU_CACHE_TYPE" => "A",
    "MENU_CACHE_TIME" => "3600",
    "MENU_CACHE_USE_GROUPS" => "Y",
    "MENU_CACHE_GET_VARS" => array(
    ),
    "MAX_LEVEL" => "1",
    "CHILD_MENU_TYPE" => "left",
    "USE_EXT" => "N",
    "DELAY" => "N",
    "ALLOW_MULTI_SELECT" => "N",
    "MENU_TITLE" => GetMessage('FOOTER_COMPANY')
    ),
    false
);?><?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
    "ROOT_MENU_TYPE" => "bottom_2",
    "MENU_CACHE_TYPE" => "A",
    "MENU_CACHE_TIME" => "3600",
    "MENU_CACHE_USE_GROUPS" => "Y",
    "MENU_CACHE_GET_VARS" => array(
    ),
    "MAX_LEVEL" => "1",
    "CHILD_MENU_TYPE" => "left",
    "USE_EXT" => "N",
    "DELAY" => "N",
    "ALLOW_MULTI_SELECT" => "N",
    "MENU_TITLE" => GetMessage('FOOTER_HELP')
    ),
    false
);?><?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
    "ROOT_MENU_TYPE" => "bottom_3",
    "MENU_CACHE_TYPE" => "A",
    "MENU_CACHE_TIME" => "3600",
    "MENU_CACHE_USE_GROUPS" => "Y",
    "MENU_CACHE_GET_VARS" => array(
    ),
    "MAX_LEVEL" => "1",
    "CHILD_MENU_TYPE" => "left",
    "USE_EXT" => "N",
    "DELAY" => "N",
    "ALLOW_MULTI_SELECT" => "N",
    "MENU_TITLE" => GetMessage('FOOTER_PERSONAL')
    ),
    false
);?>
     
      <div class="col-md-3 col-sm-6">
        <h3 class="strong-header"><?=GetMessage('FOOTER_SOCIAL')?></h3>
        
        <div class="social-widget">
        <?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "EDIT_TEMPLATE" => "",
        "PATH" => SITE_DIR."include/social.php"
    )
);?>
         
        </div>
      </div>
    </section>
    <hr>
    <section class="row">
      <div class="col-md-12">
        <span class="copyright pull-left"><?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "EDIT_TEMPLATE" => "",
        "PATH" => SITE_DIR."include/copyright.php"
    )
);?></span>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "EDIT_TEMPLATE" => "",
        "PATH" => SITE_DIR."include/payment.php"
    )
);?>
      </div>
    </section>
    <div id="bx-composite-banner"></div>
  </div>
</footer>

</div>

<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/bootstrap/js/bootstrap.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.flexslider-min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.isotope.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.ba-bbq.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.raty.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.prettyPhoto.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/chosen.jquery.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/form/js/contact-form.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/twitter/js/jquery.tweet.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/product.js"></script>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "EDIT_TEMPLATE" => "",
        "PATH" => SITE_DIR."include/yandex.php"
    )
);?>
</body>
</html>