<?php
header('Content-type: text/html; charset=windows-1251');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$module_id="step2use.redirects";
CModule::IncludeModule($module_id);

$requestUri = strip_tags($_GET['requestUri']);

$redirect = S2uRedirectsRulesDB::FindRedirect($requestUri, SITE_ID);
$_404IsActive = COption::GetOptionString($module_id, '404_IS_ACTIVE', 'Y');

$main_mirror = COption::GetOptionString($module_id, 'main_mirror_' . SITE_ID);
$slash_redirect = COption::GetOptionString($module_id, 'slash_add_' . SITE_ID);

$responseArray = array(
    'code' => 'OK'
);

if($redirect) {
    
    if($redirect['STATUS'] == "410"){
        $header = "HTTP/1.0 410 Gone";
    }else{
        $newUrl = $redirect['NEW_LINK'];
        $oldUrl = $redirect['OLD_LINK'];
        if($main_mirror != ""){
            $newUrl = S2uRedirects::mainMirror($newUrl, $main_mirror);
        }
        if($slash_redirect == "Y"){
            $newUrl = S2uRedirects::addSlash($newUrl);
        }
        if($oldUrl != $newUrl ){
            $responseArray['newUrl'] = $newUrl;
        }
    }

} else {
    $url = $oldUrl = $_SERVER['HTTP_REFERER']; //S2uRedirects::curPageURL();

    $arrUrl = array();
    $arrUrl = parse_url($url);
    if(substr($arrUrl["path"], 0, 8) != "/bitrix/"){
        if($main_mirror != ""){
            //var_dump("MIRROR");exit;
            $url = S2uRedirects::mainMirror($url, $main_mirror);
        }
        if($slash_redirect == "Y"){
            //var_dump("SLASH");
            $url = S2uRedirects::addSlash($url);
            //var_dump($url);
            //var_dump($oldUrl);exit;
        }
        $responseArray['oldUrl'] = $oldUrl;
        if($url != $oldUrl){
            $responseArray['newUrl'] = $url;
        }
    }
}

echo json_encode($responseArray);
