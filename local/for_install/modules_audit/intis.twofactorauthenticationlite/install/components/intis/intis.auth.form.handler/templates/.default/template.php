<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arResult['ONE_TIME_PASS_FORM']==true):?>
    <p style='background-color: #3b9f2a; color:#ffffff; font-size:.9em; padding: 5px;text-align: center;'>
        <?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_PASSWORD_SEND")?>
        <?=$arResult['PHONE']?>
    </p>
    <p style="margin-top:20px;">
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />

        <strong><?=GetMessage("TWOFACTORAUTHENTIFICATION_ENTER_ONE_TIME_PASS")?></strong><br>
        <input class="intis_text_style" type="password" id="USER_ONE_TIME_PASS" name="USER_ONE_TIME_PASS" maxlength="255" value="" /><br /><br />
    </p>
    <p class="intis_tac"><input class="intis_login_button" type="submit" id="INTIS_AUTH_BUTTON" name="Login" value="<?=GetMessage("TWOFACTORAUTHENTIFICATION_DONE")?>" /></p>

    <script type="text/javascript">
        $("#INTIS_AUTH_BUTTON").click (function() {
            var IntisOneTimePass = document.getElementById("USER_ONE_TIME_PASS").value;
            $.ajax({
                type: "POST",
                url: "<?=$arParams["INTIS_AUTH_URL"]?>",
                data: "REQUESTONETIME="+IntisOneTimePass,
                beforeSend: function(){
                    $("#loginIntis").html("<p><?=GetMessage("TWOFACTORAUTHENTIFICATION_WAIT")?></p>");
                },
                success: function(data){
                    $("#loginIntis").html(data);
                }
            });
        });
    </script>
    <div class="close button"></div>
<?endif;?>

<?if ($arResult['WRONG_PASS']==true):?>
    <p>
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />

        <strong><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_LOGIN")?></strong><br>
        <input class="intis_text_style" type="text" id="INTIS_LOGIN" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" /><br /><br />
        <strong><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_PASSWORD")?></strong><br>
        <input class="intis_text_style" style="background-color: #fda2a2;" type="password" id="INTIS_PASSWORD" name="USER_PASSWORD" maxlength="255" placeholder="<?=GetMessage("TWOFACTORAUTHENTIFICATION_WRONG_PASS")?>" /><br>
        <div class="block_alert"><?if ($arResult['SHOW_ALERT']==true):?><?=GetMessage("TWOFACTORAUTHENTIFICATION_BLOCK_ALERT")?><?endif;?></div>
    </p>
    <p class="intis_tac"><input class="intis_login_button" type="submit" id="INTIS_AUTH_BUTTON" name="Login" value="<?=GetMessage("TWOFACTORAUTHENTIFICATION_ENTER")?>" /></p>

    <script type="text/javascript">
        $("#INTIS_AUTH_BUTTON").click (function() {
            var IntisLogin = document.getElementById("INTIS_LOGIN").value;
            var IntisPassword = document.getElementById("INTIS_PASSWORD").value;
            $.ajax({
                type: "POST",
                url: "<?=$arParams["INTIS_AUTH_URL"]?>",
                data: "REQUESTLOGIN="+IntisLogin+"&REQUESTPASS="+IntisPassword,
                beforeSend: function(){
                    $("#loginIntis").html("<p><?=GetMessage("TWOFACTORAUTHENTIFICATION_WAIT")?></p>");
                },
                success: function(data){
                    $("#loginIntis").html(data);
                }
            });
        });
    </script>
    <div class="close button"></div>
<?endif;?>

<?if ($arResult['WRONG_LOGIN']==true):?>
    <p>
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />

        <strong><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_LOGIN")?></strong><br>
        <input class="intis_text_style" style="background-color: #fda2a2;" type="text" id="INTIS_LOGIN" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="<?=GetMessage("TWOFACTORAUTHENTIFICATION_WRONG_LOGIN")?>" /><br /><br />
        <strong><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_PASSWORD")?></strong><br>
        <input class="intis_text_style" type="password" id="INTIS_PASSWORD" name="USER_PASSWORD" maxlength="255" /><br>
    <div class="block_alert"><?if ($arResult['SHOW_ALERT']==true):?><?=GetMessage("TWOFACTORAUTHENTIFICATION_BLOCK_ALERT")?><?endif;?></div>
    </p>
    <p class="intis_tac"><input class="intis_login_button" type="submit" id="INTIS_AUTH_BUTTON" name="Login" value="<?=GetMessage("TWOFACTORAUTHENTIFICATION_ENTER")?>" /></p>

    <script type="text/javascript">
        $("#INTIS_AUTH_BUTTON").click (function() {
            var IntisLogin = document.getElementById("INTIS_LOGIN").value;
            var IntisPassword = document.getElementById("INTIS_PASSWORD").value;
            $.ajax({
                type: "POST",
                url: "<?=$arParams["INTIS_AUTH_URL"]?>",
                data: "REQUESTLOGIN="+IntisLogin+"&REQUESTPASS="+IntisPassword,
                beforeSend: function(){
                    $("#loginIntis").html("<p><?=GetMessage("TWOFACTORAUTHENTIFICATION_WAIT")?></p>");
                },
                success: function(data){
                    $("#loginIntis").html(data);
                }
            });
        });
    </script>
    <div class="close button"></div>
<?endif;?>

<?if ($arResult['ONE_TIME_PASS_DONE']==true):?>
    <p class="tal" style="margin-top: 60px;">
        <?=GetMessage("TWOFACTORAUTHENTIFICATION_HI")?>, <?=$arResult['USER_LOGIN']?>
    </p>
    <p class="tac" style="margin-top: 30px;"><a href="javascript:window.location.reload()" class="intis_login_button"><?=GetMessage("TWOFACTORAUTHENTIFICATION_NEXT")?></a></p>
<?endif;?>