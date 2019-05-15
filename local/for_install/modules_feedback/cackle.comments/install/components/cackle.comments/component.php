<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
define('CACKLE_TIMER', 300);
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
define(SITE_NAME,CMainPage::GetSiteByHost());
//$site_name=
//global $USER;

function cackle_auth() {
    global $USER;
    $cackle_api = new CackleAPI();
    $siteApiKey = $cackle_api->cackle_get_param("site_api_".SITE_NAME);
    $timestamp = time();
    $arRes = CUser::GetByID($USER->GetID());
    if ($res = $arRes->Fetch()) {
        $user = array(
            'id' => $res["ID"],
            'name' =>  $res["LOGIN"],
            'email' => $res["EMAIL"],
            'avatar' => $_SERVER['HOST'] .CFile::GetPath($res["PERSONAL_PHOTO"]),
        );
        $user_data = base64_encode(json_encode($user));
    } else {
        $user = '{}';
        $user_data = base64_encode($user);
    }
    $sign = md5($user_data . $siteApiKey . $timestamp);
    return "$user_data $sign $timestamp";
}




function time_is_over($cron_time){
    $cackle_api = new CackleAPI();
    $get_last_time = $cackle_api->cackle_get_param("last_time_".SITE_NAME);
    $now=time();
    if ($get_last_time==""){
        $q="last_time_".SITE_NAME;
        $set_time = $cackle_api->cackle_set_param($q,$now);
        return time();
    }
    else{
        if($get_last_time + $cron_time > $now){
            return false;
        }
        if($get_last_time + $cron_time < $now){
            $q="last_time_".SITE_NAME;
            $set_time = $cackle_api->cackle_set_param($q,$now);
            return $cron_time;
        }
    }
}

function get_local_comments($post_id){
    //getting all comments for special post_id from database.

    $cackle_api = new CackleAPI();

    $sql = "select * from ".PREFIX."_comments where post_id = '$post_id' and approve = 1";
    $get_all_comments = $cackle_api->db_connect($sql);

    return $get_all_comments;
}


if (CModule::IncludeModule("cackle.comments")){
    $arResult = array();
    $cackle_api = new CackleAPI();
    $arResult['SITE_ID'] = $cackle_api->cackle_get_param("site_id_".SITE_NAME);
    $arResult['MC_CHANNEL'] = $APPLICATION->GetCurPage();
    $arResult['CACKLE_OBJ'] = get_local_comments($APPLICATION->GetCurPage());
    if ((strtolower($DB->type)=="mysql")){
        $sync = new Sync();
        if (time_is_over(CACKLE_TIMER)){
            //print_r('test');
            $sync->init();
        }
    }

    $arResult['SSO'] = cackle_auth();
    $arResult['SSO_PARAM'] = $cackle_api->cackle_get_param('cackle_sso_'.SITE_NAME);
    $this->IncludeComponentTemplate();
}

?>