<?
    global $APPLICATION;

    $arJSConstants = array(
        'BX_PERSONAL_ROOT' => BX_PERSONAL_ROOT,
        'SITE_ID' => SITE_ID,
        'SITE_DIR' => SITE_DIR,
        'SITE_CHARSET' => SITE_CHARSET,
        'SITE_TEMPLATE_ID' => SITE_TEMPLATE_ID,
        'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH,
        'MODULE_DIR' => BX_PERSONAL_ROOT.'/modules/intec.startshop'
    );

    $APPLICATION->AddHeadString('<script type="text/javascript">var StartshopConstants = '.CUtil::PhpToJSObject($arJSConstants).'; if (typeof Startshop != "undefined") { Startshop.Constants = StartshopConstants; StartshopConstants = undefined; }</script>');
    unset($arJSConstants);
?>