<?php
/**
 * Date: 17.05.2014
 */
define('IWEB_DS', DIRECTORY_SEPARATOR);
define('IWEB_APP_PATH',$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/imaginweb.sms/classes/iweb/');

abstract class Autoloader
{
	public static function autoload($class)
	{
		//debmes($class,IWEB_APP_PATH.$class.'.php');
		$class = str_replace('\\', IWEB_DS, $class);
		//debmes($class,IWEB_APP_PATH.$class.'.php');
		if(file_exists(IWEB_APP_PATH.$class.'.php'))
		{
			include_once IWEB_APP_PATH.$class.'.php';
		}
	}
}

\spl_autoload_register('Autoloader::autoload');
