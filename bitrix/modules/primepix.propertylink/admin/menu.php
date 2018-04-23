<?

IncludeModuleLangFile(__FILE__);

$aMenu = array(
	'parent_menu' => 'global_menu_services',
	'section'     => 'primepix_propertylink',
	'sort'        => 99999,
	'items_id'    => 'primepix_propertylink_menu',
	'text'        => GetMessage('primepix.propertylink_TITLE'),
	'url'         => '/bitrix/admin/primepix.propertylink_sync.php',
);

return $aMenu;


?>