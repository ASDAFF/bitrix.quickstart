<?define("NOT_CHECK_PERMISSIONS", true);

if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
{
	$siteID = $_REQUEST['site_id'];
	if($siteID !== '' && preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
	{
		define('SITE_ID', $siteID);
	}
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->ShowAjaxHead();
?>

<div id="bx_auth_popup_form" >
	<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "emarket_auth_popup",
		array(
			"BACKURL" => $_REQUEST["backurl"],
			"AUTH_FORGOT_PASSWORD_URL" => $_REQUEST["forgotPassUrl"],
		),
		false
	);
	?>
</div>