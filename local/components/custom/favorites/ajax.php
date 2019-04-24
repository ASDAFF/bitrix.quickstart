<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);

use Bitrix\Main\Loader;

if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
{
	$siteID = trim($_REQUEST['site_id']);
	if ($siteID !== '' && preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
	{
		define('SITE_ID', $siteID);
	}
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if (!Loader::includeModule('sale') || !Loader::includeModule('catalog'))
    return;
    
if($request['id']) {

    if(!$USER->IsAuthorized()) {
        $arElements = unserialize($_SESSION['favorites']);

        if(!in_array($request['id'], $arElements)) {
                $arElements[] = $request['id'];
        }
        else {
            $key = array_search($request['id'], $arElements);
            unset($arElements[$key]);
        }
        $_SESSION["favorites"] = serialize($arElements);
//        $APPLICATION->set_cookie("favorites", serialize($arElements));
    } else {
        $idUser = $USER->GetID();
        $res = CUser::GetByID($idUser);
        $arUser = $res->Fetch();
        
        if ($arUser !== false) {
            $arElements = $arUser['UF_FAVORITES']; 
            
            if(!in_array($request['id'], $arElements)) {
                $arElements[] = (int)$request['id'];
            } else {
                $key = array_search($request['id'], $arElements); 
                unset($arElements[$key]);
            
            }
            $USER->Update($idUser, ["UF_FAVORITES" => $arElements]);
        }
    }
}
