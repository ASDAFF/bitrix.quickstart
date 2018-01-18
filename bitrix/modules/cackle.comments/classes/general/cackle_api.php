<?php
/*Enter database settings*/
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
$site_name=CMainPage::GetSiteByHost();
global $MAIN_OPTIONS;
define('PREFIX', 'b');
global $APPLICATION;
global $cackle_site_id, $cackle_site_name, $cackle_site_api, $cackle_account_api, $cackle_sso, $cackle_encoding, $cackle_activated;
$cackle_site_id = COption::GetOptionString("cackle.comments", "site_id_".$site_name, $_POST['siteId']);
$cackle_site_name = CMainPage::GetSiteByHost();
$cackle_account_api = COption::GetOptionString("cackle.comments", "account_api_".$site_name, $_POST['accountApiKey']);
$cackle_sso = COption::GetOptionString("cackle.comments", "cackle_sso_".$site_name, $_POST['enable_sso']);
$cackle_encoding = COption::GetOptionString("cackle.comments", "cackle_encoding_".$site_name, $_POST['enable_encoding']);
$cackle_activated = COption::GetOptionString("cackle.comments", "cackle_activated_".$site_name, $_POST['cackle_activated']);


class CackleAPI{

    function __construct(){
        $this->site_id = $GLOBALS['cackle_site_id'];
        $this->site_api = $GLOBALS['$cackle_site_api'];
        $this->account_api = $GLOBALS['$cackle_account_api'];
        $this->cackle_sso =$GLOBALS['$cackle_sso'];
        $this->cackle_encoding = $GLOBALS['$cackle_encoding'];
        $this->cackle_activated = $GLOBALS['$cackle_activated'];

       // var_dump($GLOBALS['cackle_site_id']);
    }
    function db_connect($query,$return=true,$list=false){

        global $DB;
        global $site_name;
        if ($this->cackle_get_param("cackle_encoding_".$GLOBALS['cackle_site_name']) == 1){

            $db_d=('SET NAMES cp1251;');
        }
        else{
            $db_d=('SET NAMES utf8;');
        }
        $res = $DB->Query($query,true);


        if($return){
            $i = 0;
            $result_arr = array();
            //var_dump($query);
            if($res->SelectedRowsCount()>1){
                while ($result = $res->Fetch()) {
                    $result_arr[$i] = $result;
                    $i++;
                }
                return $result_arr;
            }
            else{
                $res = $res->Fetch();
                $result = array();
                $result[0] = $res;
                return $result;
            }

        }



    }
    function conn(){
        try {
            global $DB;
            return $DB;
        }
        catch (Exception $e) {
            echo "invalid sql -  - " . $e;
        }
    }
    function db_table_exist($table){

        return true;
    }

    function cackle_set_param($param, $value){
        COption::SetOptionString("cackle.comments", $param, $value);
    }

    public function  cackle_get_param($param){
        return COption::GetOptionString("cackle.comments", $param);

    }


    function cackle_db_prepare(){

        if ($this->db_table_exist("".PREFIX."_comments")){
            //    $this->db_connect("ALTER TABLE ".PREFIX."_comments ADD user_agent VARCHAR(64) NOT NULL default ''");
            // $this->db_connect("ALTER TABLE ".PREFIX."_comments MODIFY 'user_agent' varchar(64) NOT NULL default ''");
        }

    }
    function import_wordpress_comments(&$wxr, $timestamp, $eof) {
        global $site_name;
        $postdata = http_build_query(
                array(
                    'siteId' =>$this->cackle_get_param("site_id_".$GLOBALS['cackle_site_name']),
                    'accountApiKey' => $this->cackle_get_param("account_api_".$GLOBALS['cackle_site_name']),
                    'siteApiKey' => $this->cackle_get_param("site_api_".$GLOBALS['cackle_site_name']),

                    'wxr' => $wxr,

                    'eof' => (int)$eof
                )
            );
            $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded;Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'content' => $postdata
            )
            );

            $context  = stream_context_create($opts);

            $response = file_get_contents('http://import.cackle.me/api/import-wordpress-comments', false, $context);

        if ($response['body']=='fail') {
            $this->api->last_error = $response['body'];
            return -1;
        }
        $data = $response;
        if (!$data || $data== 'fail') {
            return -1;
        }

        return $data;
    }
    function get_last_error() {
        if (empty($this->last_error)) return;
        if (!is_string($this->last_error)) {
            return var_export($this->last_error);
        }
        return $this->last_error;
    }
    function curl($url) {

        return file_get_contents($url);
    }

}