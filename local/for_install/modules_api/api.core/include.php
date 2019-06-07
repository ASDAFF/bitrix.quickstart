<?
/**
 * api.core
 *
 * NOTE: Support latest modern browsers
 *
 * @package      Api
 * @subpackage   Core
 * @link         https://tuning-soft.ru/shop/api.core/
 *
 * @author       Kuchkovsky Anton
 * @copyright    © 2009-2018 Tuning-Soft
 *
 */


/*\Bitrix\Main\Loader::registerAutoLoadClasses('api.core', array(
	 '\Api\Core\Image' => 'lib/image.php',
));*/


/*
 * /bitrix/modules/main/jscore.php
 *
ex: CJSCore::RegisterExt('timeman', array(
	'js' => '/bitrix/js/timeman/core_timeman.js',
	'css' => '/bitrix/js/timeman/css/core_timeman.css',
	'lang' => '/bitrix/modules/timeman/lang/#LANG#/js_core_timeman.php',
	'rel' => array("ajax", "window") //needed extensions for automatic inclusion
	'skip_core' => false | true, //core.js
	'use' => CJSCore::USE_ADMIN|CJSCore::USE_PUBLIC
	'lang_additional' => array('TITLE_PREFIX' => CUtil::JSEscape(COption::GetOptionString("main", "site_name", $_SERVER["SERVER_NAME"]))." - ")
));
*/


//Example for include extensions in public
/*
if(Loader::includeModule('api.core')){
	CUtil::InitJSCore(array('api_modal','api_button'));
}
*/


$arJsConfig = array(
	
	//-------------------- JQUERY PLUGINS --------------------//
	'api_inputmask'      => array(
		'css' => '/bitrix/css/api.core/inputmask.css',
		'js'  => array(
			'/bitrix/js/api.core/inputmask/jquery.inputmask.bundle.min.js',
			'/bitrix/js/api.core/inputmask/phone-codes/phone.min.js',
			//'/bitrix/js/api.core/inputmask/phone-codes/phone-ru.js',
		),
	),
	'api_flatpickr'      => array(
		'css' => array(
			'/bitrix/js/api.core/flatpickr/flatpickr.min.css',
			'/bitrix/js/api.core/flatpickr/plugins/confirmDate/confirmDate.css',
		),
		'js'  => array(
			'/bitrix/js/api.core/flatpickr/flatpickr.min.js',
			'/bitrix/js/api.core/flatpickr/l10n/ru.js',
			'/bitrix/js/api.core/flatpickr/plugins/confirmDate/confirmDate.js',
		),
	),
	'api_matchheight'    => array(
		'js' => '/bitrix/js/api.core/plugins/jquery.matchHeight.min.js',
	),
	'api_formvalidation' => array(
		'css' => '/bitrix/js/api.core/formvalidation/formValidation.min.css',
		'js'  => array(
			'/bitrix/js/api.core/formvalidation/formValidation.popular.min.js',
			'/bitrix/js/api.core/formvalidation/framework/bootstrap4.min.js',
		),
	),
	'api_magnific_popup' => array(
		'css' => '/bitrix/js/api.core/magnific_popup/magnific-popup.min.css',
		'js'  => '/bitrix/js/api.core/magnific_popup/jquery.magnific-popup.min.js',
	),
	'api_select2'        => array(
		'css' => '/bitrix/js/api.core/select2/select2.min.css',
		'js'  => array(
			'/bitrix/js/api.core/select2/select2.min.js',
			'/bitrix/js/api.core/select2/i18n/ru.js'
		),
	),
	
	
	//-------------------- API CORE --------------------//
	'api_button'         => array(
		'css' => '/bitrix/css/api.core/button.css',
	),
	'api_form'           => array(
		'css' => '/bitrix/css/api.core/form.css',
		'js'  => '/bitrix/js/api.core/form.js',
	),
	'api_modal'          => array(
		'css' => '/bitrix/css/api.core/modal.css',
		'js'  => '/bitrix/js/api.core/modal.js',
	),
	'api_tab'            => array(
		'css' => '/bitrix/css/api.core/tab.css',
		'js'  => '/bitrix/js/api.core/tab.js',
	),
	'api_utility'        => array(
		'css' => '/bitrix/css/api.core/utility.css',
	),
	'api_width'          => array(
		'css' => '/bitrix/css/api.core/width.css',
	),
	'api_upload'         => array(
		'css' => '/bitrix/css/api.core/upload.css',
		'js'  => '/bitrix/js/api.core/upload.js',
	),
	'api_alert'          => array(
		'css' => '/bitrix/css/api.core/alert.css',
		'js'  => '/bitrix/js/api.core/alert.js',
		'rel' => array('api_button'),
	),
	'api_icon'           => array(
		'css' => '/bitrix/css/api.core/icon.css',
	),
	'api_message'        => array(
		'css' => '/bitrix/css/api.core/message.css',
	),
	'api_grid'           => array(
		'css' => '/bitrix/css/api.core/grid.css',
	),
	'api_badge'          => array(
		'css' => '/bitrix/css/api.core/badge.css',
	),
	'api_color'          => array(
		'css' => '/bitrix/css/api.core/color.css',
	),
	'api_tooltip'        => array(
		'css' => '/bitrix/css/api.core/tooltip.css',
		'js'  => '/bitrix/js/api.core/tooltip.js',
	),
	'api_offcanvas'      => array(
		'css' => '/bitrix/css/api.core/offcanvas.css',
		'js'  => '/bitrix/js/api.core/offcanvas.js',
	),
	'api_lightbox'       => array(
		'css' => '/bitrix/css/api.core/lightbox.css',
		'js'  => '/bitrix/js/api.core/lightbox.js',
	),
	'api_dropdown'       => array(
		'css' => '/bitrix/css/api.core/dropdown.css',
		'js'  => '/bitrix/js/api.core/dropdown.js',
	),

);

foreach($arJsConfig as $ext => $arExt){
	
	$arExt['lang']       = '/bitrix/modules/api.core/lang/' . LANGUAGE_ID . '/ext.php';
	$arExt['bundle_js']  = 'api_core';
	$arExt['bundle_css'] = 'api_core';
	
	CJSCore::RegisterExt($ext, $arExt);
}

?>