<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
	global $APPLICATION;

	if (!CJSCore::IsExtRegistered('jquery'))
        CJSCore::RegisterExt('jquery', array(
            'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/jquery.js',
            'skip_core' => true
        ));

    CJSCore::RegisterExt('jquery_colorpicker', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/colorpicker/js/colorpicker.js',
        'css' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/colorpicker/css/colorpicker.css',
        'rel' => array('jquery'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('jquery_mask', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/mask/js/mask.min.js',
        'rel' => array('jquery'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('startshop', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/js/core.js',
        'rel' => array('jquery'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('startshop_functions', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/js/functions.js',
        'rel' => array('startshop'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('startshop_classes', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/js/classes.js',
        'rel' => array('startshop', 'startshop_functions'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('startshop_catalog', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/js/catalog.js',
        'rel' => array('startshop', 'startshop_functions'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('startshop_controls', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/js/controls.js',
        'rel' => array('startshop', 'startshop_functions'),
        'skip_core' => true
    ));

    CJSCore::RegisterExt('startshop_basket', array(
        'js' => BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/js/basket.js',
        'rel' => array('startshop', 'startshop_functions'),
        'skip_core' => true
    ));

    CJSCore::Init(array(
        'jquery',
        'jquery_colorpicker',
        'jquery_mask',
        'startshop',
        'startshop_functions',
        'startshop_classes',
        'startshop_catalog',
        'startshop_controls',
        'startshop_basket'
    ));

    $APPLICATION->SetAdditionalCSS(BX_PERSONAL_ROOT.'/themes/intec.startshop/web/startshop/css/admin.css');

    include($_SERVER['DOCUMENT_ROOT'].BX_PERSONAL_ROOT.'/modules/intec.startshop/web/startshop/php/js.constants.php');
?>