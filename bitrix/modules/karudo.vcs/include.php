<?

//define('KARUDO_VCS_MODULE_DIR', dirname(__FILE__) . '/');

//global $DBType;
CModule::AddAutoloadClasses('karudo.vcs', array(
	'CVCSDriverIteratorAbstract' => '/classes/general/driver.iterator.abstract.php',
	'CVCSDriverItemAbstract' => '/classes/general/driver.item.abstract.php',

	'CVCSDriverIteratorFiles' => '/classes/general/driver.iterator.files.php',
	'CVCSDriverItemFiles' => '/classes/general/driver.item.files.php',

	'CVCSItemFactory' => '/classes/general/item.factory.php',
	'CVCSItemDBResult' => '/classes/general/item.dbresult.php',
	'CVCSItem' => '/classes/general/item.item.php',

	'CVCSChangedItemFactory' => '/classes/general/changed.item.factory.php',

	'CVCSAjaxService' => '/classes/general/ajax.abstract.php',
	'CVCSAjaxVcs' => '/classes/general/ajax.vcs.php',
	'CVCSAjaxCommit' => '/classes/general/ajax.commit.php',
	'CVCSAjaxDrivers' => '/classes/general/ajax.drivers.php',
	'CVCSAjaxItem' => '/classes/general/ajax.items.php',

	'CVCSMain' => 'classes/general/main.php',
	'CVCSTimer' => 'classes/general/timer.php',
	'CVCSArrayObject' => '/classes/general/arrayobject.php',
	'CVCSConfig' => 'config.php',

	'CVCSAjaxException' => 'exceptions.php',
	'CVCSAjaxExceptionSystemError' => 'exceptions.php',
	'CVCSAjaxExceptionServiceError' => 'exceptions.php',
	'CVCSAjaxExceptionAuthError' => 'exceptions.php',

	'CVCSRevisionFactory' => '/classes/general/revision.factory.php',

	'CVCSDriversFactory' => '/classes/general/drivers.factory.php',

	'CVCSAdminHelpers' => '/classes/general/admin.helpers.php',
));

function __VCSDemoMode() {
	return defined("karudo_vcs_DEMO") && constant("karudo_vcs_DEMO") == "Y";
}


?>