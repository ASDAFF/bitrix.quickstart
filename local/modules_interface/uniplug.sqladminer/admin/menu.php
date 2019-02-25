<?
IncludeModuleLangFile(__FILE__);

if ( $USER->IsAdmin() ) {
	return array(
		'parent_menu' => 'global_menu_services',
		'section'     => 'uniplug_sqladminer',
		'sort'        => 110,
		'url'         => 'uniplug_sqladminer_admin.php',
		'text'        => GetMessage('UNIPLUG_SQLADMINER_ADMIN_MENU_TEXT'),
		'title'       => GetMessage('UNIPLUG_SQLADMINER_ADMIN_MENU_TITLE'),
		'icon'        => 'upsqladminer_menu_icon',
		'module_id'   => 'uniplug.sqladminer',
	);
}

return false;
