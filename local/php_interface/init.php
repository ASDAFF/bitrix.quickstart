<?
	use BitrixQuickStart;

	// autoloading
	require_once(dirname(__FILE__) . '/Autoloader.php');
	$autoloader = new \BitrixQuickStart\Autoloader();


	/**
	 * Event handling.
	 * 
	 * We strongly recommend to group event handlers in classes.
	 * 
	 * For example, you can handle events "OnBeforeUserAdd" and "OnBeforeUserUpdate" 
	 * with methods UserHandlers::OnBeforeUserAdd() and UserHandlers::OnBeforeUserUpdate(), like this:
	 * 
	 * AddEventHandler("main", "OnBeforeUserAdd", Array("UserHandlers", "OnBeforeUserAdd"));
	 */
	