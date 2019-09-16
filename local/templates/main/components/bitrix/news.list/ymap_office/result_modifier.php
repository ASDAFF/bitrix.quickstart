<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

$cp = $this->__component; // объект компонента

if (is_object($cp))
{
    $cp->SetResultCacheKeys(array('ITEMS'));
} ?>