<?
define('ADMIN_MODULE_PATCH', dirname(__FILE__));
define('ADMIN_MODULE_ID', basename(ADMIN_MODULE_PATCH));
define('ADMIN_MODULE_NAME', str_replace('.', '_', ADMIN_MODULE_ID));
define('ADMIN_MODULE_ICON', '');
define('ADMIN_MODULE_LANG', strtoupper(ADMIN_MODULE_NAME).'_');

define('STOP_STATISTICS', true);
define('BX_SECURITY_SHOW_MESSAGE', true);
define('NO_AGENT_CHECK', true);

