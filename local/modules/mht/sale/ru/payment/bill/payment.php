<?

CCurrencyLang::disableUseHideZero();

if (!empty($_REQUEST['pdf']))
	return include(dirname(__FILE__).'/pdf.php');
else
	return include(dirname(__FILE__).'/html.php');

CCurrencyLang::enableUseHideZero();

?>