<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"])>0) 
	LocalRedirect($backurl);

$APPLICATION->SetTitle("Authorization");
?>
<p class="notetext">You have successfully registered and authorized.</p>

<p><a href="#SITE_DIR#">Back to home page</a></p>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>