<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!defined('PUBLIC_AJAX_MODE') && IsModuleInstalled("socialnetwork") && $GLOBALS["USER"]->IsAuthorized())
	CUser::SetLastActivityDate($GLOBALS["USER"]->GetID());

?>