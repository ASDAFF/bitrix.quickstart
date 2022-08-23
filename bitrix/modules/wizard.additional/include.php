<?
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $DBType;

Loader::registerAutoLoadClasses(
	"wizard.additional",
	[
		"\\Wizard\\Additional\\HLAdditional"        => "lib/hladditional.php",
		"\\Wizard\\Additional\\HLEvents"            => "lib/hlEvents.php",
		"\\Wizard\\Additional\\Errors"              => "lib/errors.php",
		"\\Wizard\\Additional\\CheckResources"      => "lib/checkResources.php",
		"\\Wizard\\Additional\\CreateMultiLvlArray" => "lib/createMultiLvlArray.php",
		"\\Wizard\\Additional\\Main"                => "lib/main.php",
		"\\Wizard\\Additional\\Logger"              => "lib/logger.php",
		"\\Wizard\\Additional\\Ftp"              => "lib/ftp.php",
	]
);