<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arServices = Array(
	'site_create' => array(
		'NAME' => GetMessage('SERVICE_STEP_SITE_CREATE'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'files' => array(
		'NAME' => GetMessage('SERVICE_STEP_COPY_FILES'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'forum' => array(
		'NAME' => GetMessage('SERVICE_STEP_FORUM'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'template' => array(
		'NAME' => GetMessage('SERVICE_STEP_COPY_TEMPLATES'),
		'STAGES' => array(
			'template.php',
			'theme.php',
		),
		'MODULE_ID' => 'main',
	),
	'menu' => array(
		'NAME' => GetMessage('SERVICE_STEP_SET_MENU_TYPES'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'urlrewrite' => array(
		'NAME' => GetMessage('SERVICE_STEP_URL_REWRITE'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'settings' => array(
		'NAME' => GetMessage('SERVICE_STEP_SETTINGS'),
		'STAGES' => array(
			'index.php',
			'last_settings.php',
		),
		'MODULE_ID' => 'main',
	),
	'currency' => array(
		'NAME' => GetMessage('SERVICE_STEP_CURRENCY_SETTINGS'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'iblock',
	),
	'subscribe_rubric' => array(
		'NAME' => GetMessage('SERVICE_STEP_SUBSCRIBE'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'store' => array(
		'NAME' => GetMessage('SERVICE_STEP_ADD_STORE'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'catalog',
	),
	'hl' => array(
		'NAME' => GetMessage('SERVICE_STEP_ADD_HBLOCK'),
		'STAGES' => array(
			'index.php',
			'add.php',
		),
		'MODULE_ID' => 'highloadblock',
	),
	'iblock' => array(
		'NAME' => GetMessage('SERVICE_STEP_IBLOCK'),
		'STAGES' => array(
			'types.php',
			'catalog.php',
			'offers.php',
			'banners.php',
			'news.php',
			'action.php',
			'brands.php',
			'files.php',
			'shops.php',
			'binds_iblocks.php',
			'binds_items.php',
		),
		'MODULE_ID' => array('iblock','catalog'),
	),
	'sale' => array(
		'NAME' => GetMessage('SERVICE_STEP_SALE'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'locations' => array(
		'NAME' => GetMessage('SERVICE_STEP_LOCATIONS'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'prices' => array(
		'NAME' => GetMessage('SERVICE_STEP_PRICES'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_dasyarticle2' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_DAYSARTICLE2'),
		'STAGES' => array(
			'index.php',
			'add.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_quickbuy' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_QUICKBUY'),
		'STAGES' => array(
			'index.php',
			'add.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_grupper' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_GRUPPER'),
		'STAGES' => array(
			'index.php',
			'add.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_devcom' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_DEVCOM'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_devfunc' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_DEVFUNC'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_location' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_LOCATION'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_favorite' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_FAVORITE'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'module_easycart' => array(
		'NAME' => GetMessage('SERVICE_STEP_INSTALL_MODULE').': '.GetMessage('SERVICE_STEP_INSTALL_MODULE_EASYCART'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
	'macros' => array(
		'NAME' => GetMessage('SERVICE_STEP_MACROS'),
		'STAGES' => array(
			'index.php',
		),
		'MODULE_ID' => 'main',
	),
);