<?

IncludeModuleLangFile(__FILE__);

$aMenu = array(
	'parent_menu' => 'global_menu_services',
	'section'     => 'asdaff_proplink',
	'sort'        => 99999,
	'items_id'    => 'asdaff_proplink_menu',
	'text'        => GetMessage('asdaff.proplink_TITLE'),
	'url'         => '/bitrix/admin/asdaff.proplink_sync.php',
);

return $aMenu;


?>
