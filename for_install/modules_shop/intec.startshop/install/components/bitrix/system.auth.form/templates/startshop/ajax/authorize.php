<?define("NOT_CHECK_PERMISSIONS", true);
if (isset($_POST["site_id"])) { define("SITE_ID", $_POST["site_id"]); }


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->ShowAjaxHead();
?>
<div class="startshop-ajax-authorize">
    <?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "startshop",
        array(
            "BACKURL" => $_REQUEST["backurl"],
            "AUTH_FORGOT_PASSWORD_URL" => $_REQUEST["sForgotPasswordUrl"],
            "AUTH_REGISTER_URL" => $_REQUEST["sRegisterUrl"],
            "AUTH_URL" => $_REQUEST['sAuthUrl'],
            "USE_ADAPTABILITY" => "Y"
        ),
        false
    );?>
</div>
<script>
    BX.ready(function(){
        BX("bx_auth_popup_form").style.display = "block";
    });
</script>