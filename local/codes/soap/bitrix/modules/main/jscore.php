<?
$pathJS = '/bitrix/js/main/core';
$pathCSS = '/bitrix/js/main/core/css';
$pathCSSPanel = '/bitrix/panel/main';
$pathLang = BX_ROOT.'/modules/main/lang/'.LANGUAGE_ID;

//WARNING: Don't use CUserOptions here! CJSCore::Init can be called from php_interface/init.php where no $USER exists

$arJSCoreConfig = array(
	'ajax' => array(
		'js' => $pathJS.'/core_ajax.js',
	),
	'admin' => array(
		'js' => $pathJS.'/core_admin.js',
		'css' => array($pathCSS.'/core_panel.css', $pathCSSPanel.'/admin-public.css'),
		'lang' => $pathLang.'/js_core_admin.php',
		'rel' => array('ajax'),
		'use' => CJSCore::USE_PUBLIC,
	),
	'admin_interface' => array(
		'js' => $pathJS.'/core_admin_interface.js',
		'lang' => $pathLang.'/js_core_admin_interface.php',
		'css' => $pathCSSPanel.'/admin-public.css',
		'rel' => array('ajax', 'popup', 'window', 'date', 'fx'),
		'lang_additional' => array('TITLE_PREFIX' => CUtil::JSEscape(COption::GetOptionString("main", "site_name", $_SERVER["SERVER_NAME"]))." - ")
	),
	"admin_login" => array(
		'js' => $pathJS."/core_admin_login.js",
		'css' => $pathCSSPanel."/login.css",
		'rel' => array("ajax", "window"),
	),
	'autosave' => array(
		'js' => $pathJS.'/core_autosave.js',
		'lang' => $pathLang.'/js_core_autosave.php',
		'rel' => array('ajax'),
	),
	'fx' => array(
		'js' => $pathJS.'/core_fx.js',
	),
	'popup' => array(
		'js' => $pathJS.'/core_popup.js',
		'css' => $pathCSS.'/core_popup.css',
	),
	'tags' => array(
		'js' => $pathJS.'/core_tags.js',
		'css' => $pathCSS.'/core_tags.css',
		'lang' => $pathLang.'/js_core_tags.php',
		'rel' => array('popup'),
	),
	'timer' => array(
		'js' => $pathJS.'/core_timer.js',
	),
	'tooltip' => array(
		'js' => $pathJS.'/core_tooltip.js',
		'css' => $pathCSS.'/core_tooltip.css',
		'rel' => array('ajax'),
		'lang_additional' => array('TOOLTIP_ENABLED' => (IsModuleInstalled("socialnetwork") && COption::GetOptionString("socialnetwork", "allow_tooltip", "Y") == "Y" ? "Y" : "N")),
	),
	'translit' => array(
		'js' => $pathJS.'/core_translit.js',
		'lang' => $pathLang.'/js_core_translit.php',
/*		'lang_additional' => array('BING_KEY' => COption::GetOptionString('main', 'translate_key_bing', '')),*/
	),
	'image' => array(
		'js' => $pathJS.'/core_image.js',
		'css' => $pathCSS.'/core_image.css'
	),
	'window' => array(
		'js' => $pathJS.'/core_window.js',
		//'css' => $pathCSS.'/core_window.css',
		'css' => $pathCSSPanel.'/popup.css',
		'rel' => array('ajax'),
	),
	'access' => array(
		'js' => $pathJS.'/core_access.js',
		'css' => $pathCSS.'/core_access.css',
		'rel' => array('popup', 'ajax', 'finder'),
		'lang' => $pathLang.'/js_core_access.php',
	),
	'finder' => array(
		'js' => $pathJS.'/core_finder.js',
		'css' => $pathCSS.'/core_finder.css',
		'rel' => array('popup', 'ajax'),
	),
	'date' => array(
		'js' => $pathJS.'/core_date.js',
		'css' => $pathCSS.'/core_date.css',
		'lang' => $pathLang.'/date_format.php',
		'lang_additional' => array('WEEK_START' => CSite::GetWeekStart()),
		'rel' => array('popup'),
	),
	'ls' => array(
		'js' => $pathJS.'/core_ls.js',
		'rel' => array('json')
	),

	/* external libs */

	'jquery' => array(
		'js' => '/bitrix/js/main/jquery/jquery-1.8.3.min.js',
		'skip_core' => true,
	),
	'jquery_src' => array(
		'js' => '/bitrix/js/main/jquery/jquery-1.8.3.js',
		'skip_core' => true,
	),
	'json' => array(
		'js' => '/bitrix/js/main/json/json2.min.js',
		'skip_core' => true,
	),
	'json_src' => array(
		'js' => '/bitrix/js/main/json/json2.js',
		'skip_core' => true,
	),
);

foreach ($arJSCoreConfig as $ext => $arExt)
{
	CJSCore::RegisterExt($ext, $arExt);
}
?>