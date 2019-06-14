<?
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("NOT_CHECK_PERMISSIONS", true);
$HTTP_ACCEPT_ENCODING = "";
$_SERVER["HTTP_ACCEPT_ENCODING"] = "";

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/mystery.thumbs/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/mystery.thumbs/include.php');

$mysteryThumbs = new MysteryThumbs();

$res = $mysteryThumbs->checkURL ( GetEnv ( 'REQUEST_URI' ) );
if ($res) {
    $mysteryThumbs->showImage (); // return previously created image
} else {
    $mysteryThumbs->createImageFromParams ();
}
?>
