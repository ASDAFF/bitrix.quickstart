<?php
/**
 * @var \CUser $USER
 */
IncludeModuleLangFile(__FILE__);

if ($USER->IsAdmin())
{
	if(!\CModule::IncludeModule('multiline.ml2webforms'))
	{
		return false;
	}

	return array(
		"parent_menu" => "global_menu_services",
		"section" => "ml2webforms",
		"sort" => 5,
		"text" => GetMessage('ML2WEBFORMS_ADMIN_MENU_TITLE'),
		"url" => 'ml2webforms_admin.php',
		"icon" => "sys_menu_icon",
		"page_icon" => "sys_menu_icon",
		"more_url" => array(
            'ml2webforms_admin.php',
            'ml2webforms_edit.php',
            'ml2webforms_results.php',
        ),
		"items_id" => "menu_ml2webforms",
		"items" => \Ml2WebForms\Ml2WebFormsEntity::compileMenu(),
	);
}
else
{
	return false;
}
