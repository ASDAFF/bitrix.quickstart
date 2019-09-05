<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {
    $cp =& $this->__component;
    if (strlen($cp->getCachePath())) {
        $GLOBALS['CACHE_MANAGER']->RegisterTag(REFEREE_CACHE_TAG);
    }
}