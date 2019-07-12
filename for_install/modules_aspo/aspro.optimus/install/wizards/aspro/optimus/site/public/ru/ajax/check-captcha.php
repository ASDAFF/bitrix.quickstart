<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $DB;
if( strlen( $_REQUEST['captcha_word'] ) <= 0 || strlen( $_REQUEST['captcha_sid'] ) <= 0 ){ echo 'false'; exit; }
$_REQUEST['captcha_word'] = strtoupper( $_REQUEST['captcha_word'] );
$res = $DB->Query("SELECT CODE FROM b_captcha WHERE ID = '".$DB->ForSQL( $_REQUEST['captcha_sid'], 32 )."' ");
if( !$ar = $res->Fetch() ){ echo 'false'; exit; }
if( $ar["CODE"] != $_REQUEST['captcha_word'] ){ echo 'false'; exit; }
echo 'true';?>