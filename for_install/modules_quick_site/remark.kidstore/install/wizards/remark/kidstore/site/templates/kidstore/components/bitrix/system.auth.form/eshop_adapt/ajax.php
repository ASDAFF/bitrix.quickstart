<?define("NOT_CHECK_PERMISSIONS", true);
if (isset($_POST["site_id"]))
	define("SITE_ID", $_POST["site_id"]);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->ShowAjaxHead();
?>
<div style="min-width:350px;min-height:350px;">
	<div id="bx_auth_popup_form" style="display:none;">
	<?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "",
		array(
			"BACKURL" => $_REQUEST["backurl"],
			"AUTH_FORGOT_PASSWORD_URL" => $_REQUEST["forgotPassUrl"],
		),
		false
	);
	?>
	</div>
</div>

<script>
	BX.ready(function(){
		BX("bx_auth_popup_form").style.display = "block";
	});
</script>