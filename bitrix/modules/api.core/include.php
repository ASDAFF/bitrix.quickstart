<?
/**
 * api.core
 *
 * NOTE: Support latest modern browsers
 *
 * @package      API
 * @subpackage   CApiCore
 * @link         https://tuning-soft.ru/shop/api.core/
 *
 * @author       Anton Kuchkovsky <support@tuning-soft.ru> (https://tuning-soft.ru)
 * @copyright    © 2009-2017 Tuning-Soft
 *
 */

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
	//-------------------- JQUERY --------------------//
	'api_jquery1'     => array(
		 'js' => '/bitrix/js/api.core/jquery/jquery-1.12.4_min.js',
	),
	'api_jquery2'     => array(
		 'js' => '/bitrix/js/api.core/jquery/jquery-2.2.4_min.js',
	),
	'api_jquery3'     => array(
		 'js' => '/bitrix/js/api.core/jquery/jquery-3.2.1_min.js',
	),


	//-------------------- JQUERY PLUGINS --------------------//
	'api_inputmask'   => array(
		 'css' => '/bitrix/css/api.core/inputmask.css',
		 'js'  => array(
				'/bitrix/js/api.core/inputmask/jquery.inputmask.bundle.min.js',
				'/bitrix/js/api.core/inputmask/phone-codes/phone.min.js',
				//'/bitrix/js/api.core/inputmask/phone-codes/phone-ru.js',
		 ),
	),
	'api_flatpickr'   => array(
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
	'api_matchheight' => array(
		 'js' => '/bitrix/js/api.core/plugins/jquery.matchHeight.min.js',
	),
	'api_formvalidation' => array(
		 'css' => '/bitrix/js/api.core/formvalidation/formValidation.min.css',
		 'js' => array(
			  '/bitrix/js/api.core/formvalidation/formValidation.popular.min.js',
		 	 '/bitrix/js/api.core/formvalidation/framework/bootstrap4.min.js',
		 ),
	),
	/*'api_easypiechart' => array(
		 'js' => '/bitrix/js/api.core/jquery/easypiechart.min.js',
	),*/


	//-------------------- WYSIWYG --------------------//
	'api_redactor2'   => array(
		 'css'  => array(
				'/bitrix/js/api.core/wysiwyg/redactor2/redactor.min.css',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/css/alignment.css',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/css/clips.css',
		 ),
		 'js'   => array(
				'/bitrix/js/api.core/wysiwyg/redactor2/redactor.min.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/langs/' . LANGUAGE_ID . '.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/alignment.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/clips.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/codemirror.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/counter.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/definedlinks.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/filemanager.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/fontcolor.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/fontfamily.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/fontsize.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/fullscreen.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/imagemanager.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/inlinestyle.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/limiter.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/properties.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/source.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/table.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/textdirection.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/textexpander.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/video.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/bufferbuttons.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/scriptbuttons.js',
				'/bitrix/js/api.core/wysiwyg/redactor2/plugins/underline.js',
				//'/bitrix/js/api.core/wysiwyg/redactor2/plugins/anchor.js',
		 ),
	),


	//-------------------- API CORE --------------------//
	'api_button'      => array(
		 'css' => '/bitrix/css/api.core/button.css',
	),
	'api_form'        => array(
		 'css' => '/bitrix/css/api.core/form.css',
		 'js'  => '/bitrix/js/api.core/form.js',
		 'rel' => array('api_button','api_message'),
	),
	'api_modal'       => array(
		 'css' => '/bitrix/css/api.core/modal.css',
		 'js'  => '/bitrix/js/api.core/modal.js',
	),
	'api_tab'         => array(
		 'css' => '/bitrix/css/api.core/tab.css',
		 'js'  => '/bitrix/js/api.core/tab.js',
	),
	'api_utility'     => array(
		 'css' => '/bitrix/css/api.core/utility.css',
	),
	'api_width'       => array(
		 'css' => '/bitrix/css/api.core/width.css',
	),
	'api_upload'      => array(
		 'css' => '/bitrix/css/api.core/upload.css',
		 'js'  => '/bitrix/js/api.core/upload.js',
	),
	'api_alert'       => array(
		 'css' => '/bitrix/css/api.core/alert.css',
		 'js'  => '/bitrix/js/api.core/alert.js',
		 'rel' => array('api_button'),
	),
	'api_icon'       => array(
		 'css' => '/bitrix/css/api.core/icon.css',
	),
	'api_message'       => array(
		 'css' => '/bitrix/css/api.core/message.css',
	),
	'api_grid'       => array(
		 'css' => '/bitrix/css/api.core/grid.css',
	),

);

foreach($arJsConfig as $ext => $arExt) {

	$arExt['lang'] = '/bitrix/modules/api.core/lang/' . LANGUAGE_ID . '/ext.php';

	\CJSCore::RegisterExt($ext, $arExt);
}

?>