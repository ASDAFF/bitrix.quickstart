<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

foreach ($arResult['SITES'] as &$site) {
	$domain = (array) $site['DOMAINS'];
	$domain = array_shift($domain);
	$site['URL'] .= ($domain ? '//' : '') . $domain . $site['DIR'];
}
unset($site);