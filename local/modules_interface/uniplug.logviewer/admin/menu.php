<?
IncludeModuleLangFile(__FILE__);

return array(
	'parent_menu' => 'global_menu_services',
	'section'     => 'uniplug_logviewer',
	'sort'        => 110,
	'url'         => 'uniplug_logviewer.php?lang=' . LANGUAGE_ID,
	'text'        => GetMessage('UNIPLUG_LOGVIEWER_ADMIN_MENU_TITLE'),
	'title'       => GetMessage('UNIPLUG_LOGVIEWER_ADMIN_MENU_TITLE'),
	'icon'        => 'uniplug_logviewer_menu_icon',
	'page_icon'   => 'uniplug_logviewer_menu_icon',
	'module_id'   => 'uniplug.logviewer',
);
