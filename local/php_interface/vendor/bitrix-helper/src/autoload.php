<?php

/* An autoloader for BitrixHelper\Foo classes. This should be require()d
 * by the user before attempting to instantiate any of the BitrixHelper
 * classes.
 */

spl_autoload_register(function ($class) {
	//print_r($class);
	if (!preg_match('/^(BitrixHelper\\\)/', $class)) {
		/* If the class does not lie under the "BitrixHelper" namespace,
		   * then we can exit immediately.
		   */
		return;
	}
	/* All of the classes have names like "BitrixHelper\Foo", so we need
	 * to replace the backslashes with frontslashes if we want the
	 * name to map directly to a location in the filesystem.
	 */
	$class = str_replace('\\', '/', $class);

	/* First, check under the current directory. It is important that
	 * we look here first, so that we don't waste time searching for
	 * test classes in the common case.
	 */
	$path = dirname(__FILE__) . '/' . $class . '.php';
	if (is_readable($path)) {
		require_once $path;
	}
});
