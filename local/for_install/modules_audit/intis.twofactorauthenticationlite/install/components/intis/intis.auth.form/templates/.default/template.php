<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult["FORM_TYPE"] == "login"):
?>
	<a href="<?=$arResult["AUTH_URL"]?>" class="intis_signin" onclick="var ModalName = $('#loginIntis'); CentriredModalWindow(ModalName);OpenModalWindow(ModalName);return false;"><?=GetMessage("INTIS_AUTH_ENTER")?></a>
<?
	if($arResult["NEW_USER_REGISTRATION"] == "Y")
	{
?>
	<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" class="intis_signup"><?=GetMessage("INTIS_AUTH_REGISTER")?></a>
<?
	}
?>
    <div class="modal login_window" id="loginIntis">

            <p>
                <input type="hidden" name="AUTH_FORM" value="Y" />
                <input type="hidden" name="TYPE" value="AUTH" />

                <strong><?=GetMessage("INTIS_AUTH_LOGIN")?></strong><br>
                <input class="intis_text_style" type="text" id="INTIS_LOGIN" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" /><br><br>
                <strong><?=GetMessage("INTIS_AUTH_PASSWORD")?></strong><br>
                <input class="intis_text_style" type="password" id="INTIS_PASSWORD" name="USER_PASSWORD" maxlength="255" /><br>
                <div class="block_alert"></div>
            </p>
            <p class="intis_tac"><input class="intis_login_button" type="submit" id="INTIS_AUTH_BUTTON" name="Login" value="<?=GetMessage("INTIS_AUTH_AUTHORIZE")?>" /></p>

            <script type="text/javascript">
                $("#INTIS_AUTH_BUTTON").click (function() {
                    var IntisLogin = document.getElementById("INTIS_LOGIN").value;
                    var IntisPassword = document.getElementById("INTIS_PASSWORD").value;
                    $.ajax({
                        type: "POST",
                        url: "<?=$arParams["INTIS_AUTH_URL"]?>",
                        data: "REQUESTLOGIN="+IntisLogin+"&REQUESTPASS="+IntisPassword,
                        beforeSend: function(){
                            $("#loginIntis").html("<p><?=GetMessage("TWOFACTORAUTHENTIFICATION_PLEASE_WAIT")?></p>");
                        },
                        success: function(data){
                            $("#loginIntis").html(data);
                        }
                    });
                });
            </script>
        <div class="close button"></div>
    </div>

<?
else:
?>
	<a href="<?=$arResult['PROFILE_URL']?>" class="intis_username"><?
	$name = trim($USER->GetFullName());
	if (strlen($name) <= 0)
		$name = $USER->GetLogin();

	echo htmlspecialcharsEx($name);
	?></a>
	<a href="<?=$APPLICATION->GetCurPageParam("logout=yes", Array("logout"))?>" class="intis_logout"><?=GetMessage("INTIS_AUTH_LOGOUT")?></a>
<?
endif;
?>