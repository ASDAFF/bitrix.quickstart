<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? use \Bitrix\Main\Localization\Loc; ?>

<?
$active = $_POST['do_register'] == "Y" ? "reg" : "auth";
?>

<?
function getAuthForm($arResult) 
{
	global $APPLICATION;
	?>
		<form method = "POST" action = "" name = "order_auth_form">
			<?=bitrix_sessid_post()?>
			<? foreach ($arResult['POST'] as $key => $value): ?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>">
			<? endforeach; ?>
			<span id="helpBlock" class="help-block"><?=Loc::getMessage("STOF_LOGIN_PROMT")?></span>
			<div class="form-group">
				<label class="control-label" for="USER_LOGIN"><?=Loc::getMessage("STOF_LOGIN")?> <span class = "required">*</span></label>
				<input type="text" class = "form-control" id="USER_LOGIN" name="USER_LOGIN" maxlength="30" size="30" value="<?=$arResult["AUTH"]["USER_LOGIN"]?>">
			</div>
			<div class="form-group">
				<label class="control-label" for="USER_LOGIN"><?=Loc::getMessage("STOF_PASSWORD")?> <span class = "required">*</span></label>
				<input type="password" class = "form-control" id="USER_PASSWORD" name="USER_PASSWORD" >
			</div>
			<div class="form-group">
				<input class = "btn btn btn-default btn2" type="submit" value="<?=Loc::getMessage("STOF_NEXT_STEP")?>">
				<input type="hidden" name="do_authorize" value="Y">
			</div>
				<a href="<?=$arParams["PATH_TO_AUTH"]?>?forgot_password=yes&back_url=<?= urlencode($APPLICATION->GetCurPageParam()); ?>"><?=Loc::getMessage("STOF_FORGET_PASSWORD")?></a>
		</form>
	<?
}

function getNewUserForm($arResult) 
{	
	?>
		<form method="post" action="" name="order_reg_form">
			<?=bitrix_sessid_post()?>
			<? foreach ($arResult["POST"] as $key => $value): ?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>">
			<? endforeach; ?>
			<div class="form-group">
				<label class="control-label" for="NEW_NAME"><?=Loc::getMessage("STOF_NAME")?> <span class = "required">*</span></label>
				<input type="text" class = "form-control" id="NEW_NAME" name="NEW_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_NAME"]?>">
			</div>
			<div class="form-group">
				<label class="control-label" for="NEW_LAST_NAME"><?=Loc::getMessage("STOF_LASTNAME")?> <span class = "required">*</span></label>
				<input type="text" class = "form-control" id="NEW_LAST_NAME" name="NEW_LAST_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_LAST_NAME"]?>">
			</div>
			<div class="form-group">
				<label class="control-label" for="NEW_EMAIL">E-mail <span class = "required">*</span></label>
				<input type="text" class = "form-control" id="NEW_EMAIL"  name="NEW_EMAIL" size="40" value="<?=$arResult["AUTH"]["NEW_EMAIL"]?>">
			</div>
			<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
                <div class="form-group gui-box">
                    <label class="gui-radiobox" for="NEW_GENERATE_N">
                        
                        <input 
                          type="radio" 
                          id="NEW_GENERATE_N"
                          name="NEW_GENERATE"
                          class="gui-radiobox-item"
                          value="Y"
                          OnClick="ChangeGenerate(false)"<?if ($POST["NEW_GENERATE"] == "N") echo " checked";?>
                        ><span class="gui-out"><span class="gui-inside"></span></span>
                        <?=Loc::getMessage("STOF_MY_PASSWORD")?>
                    </label>
				</div>
            <? endif; ?>
            <? if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"): ?>
				<div id="sof_choose_login">
					<div class="form-group">
						<label class="control-label" for = "NEW_LOGIN"> <?=Loc::getMessage("STOF_LOGIN")?> <span class = "required">*</span> </label>
						<input class = "form-control" type="text" id="NEW_LOGIN" name="NEW_LOGIN" size="30" value="<?=$arResult["AUTH"]["NEW_LOGIN"]?>">
					</div>
					<div class="form-group">
						<label class="control-label" for = "NEW_PASSWORD"> <?=Loc::getMessage("STOF_PASSWORD")?> <span class = "required">*</span> </label>
						<input class = "form-control" type="password" id="NEW_PASSWORD" name="NEW_PASSWORD" size="30">
					</div>
					<div class="form-group">
						<label class="control-label" for = "NEW_PASSWORD_CONFIRM"> <?=Loc::getMessage("STOF_RE_PASSWORD")?> <span class = "required">*</span> </label>
						<input class = "form-control" type="password" id="NEW_PASSWORD_CONFIRM" name="NEW_PASSWORD_CONFIRM" size="30">
					</div>
				</div>
            <? endif; ?>
            <? if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"): ?>
				<div class="form-group gui-box">
                    <label class="gui-radiobox" for="NEW_GENERATE_Y">
                        
                        <input 
                          type="radio" 
                          id="NEW_GENERATE_Y"
                          name="NEW_GENERATE"
                          class="gui-radiobox-item"
                          value="Y"
                          OnClick="ChangeGenerate(true)"<?if ($POST["NEW_GENERATE"] != "N") echo " checked";?>
                        ><span class="gui-out"><span class="gui-inside"></span></span>
                        <?=Loc::getMessage("STOF_SYS_PASSWORD")?>
                    </label>
				</div>
				<script>
					<!--
					ChangeGenerate(<?= (($_POST["NEW_GENERATE"] != "N") ? "true" : "false") ?>);
					//-->
				</script>
			<? endif; ?>
			<? if($arResult["AUTH"]["captcha_registration"] == "Y"): ?>
				<div class="form-group">
					<label class="control-label"><?=Loc::getMessage("CAPTCHA_REGF_TITLE")?></label>
					<div class = "row">
						<div class = "col col-md-4">
							<input type="hidden" name="captcha_sid" value="<?=$arResult["AUTH"]["capCode"]?>">
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["AUTH"]["capCode"]?>" width="180" height="40" alt="CAPTCHA">
						</div>
						<div class = "col col-md-4">
							<label class="control-label" for = "captcha_word"> <?=Loc::getMessage("CAPTCHA_REGF_PROMT")?> <span class = "required">*</span> </label>
							<input type="text" id = "captcha_word" name="captcha_word" size="30" maxlength="50" value="">
						</div>
					</div>
				</div>
			<? endif; ?>
			<div class="form-group">
				<input class = "btn btn-default btn2" type="submit" value="<?=Loc::getMessage("STOF_NEXT_STEP")?>">
				<input type="hidden" name="do_register" value="Y">
			</div>
		</form>	
	<?
}
?>

<script>
<!--
function ChangeGenerate(val)
{
	if(val)
	{
		document.getElementById("sof_choose_login").style.display='none';
	}
	else
	{
		document.getElementById("sof_choose_login").style.display='block';
		document.getElementById("NEW_GENERATE_N").checked = true;
	}

	try{document.order_reg_form.NEW_LOGIN.focus();}catch(e){}
}
//-->
</script>
<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
	<div class="row">
        <div class="col col-md-4">
         <?php
        if(!empty($arResult["ERROR"])) {
            foreach($arResult["ERROR"] as $v) {			
                echo ShowError($v);
            }
        } elseif(!empty($arResult["OK_MESSAGE"])) {

            foreach($arResult["OK_MESSAGE"] as $v) {
                echo ShowNote($v);
            }
        }
        ?>
        </div>
		<div class = "col col-md-12">
			<ul class="nav nav-buttons" role="tablist">
			
				<li class="<?=$active=="auth" ? 'active': ''?>">
					<a href="#STOF_2REG"
					   role = "tab" 
					   aria-controls="STOF_2REG"
					   data-toggle = "tab"
                       class="btn btn-default btn-button"
					><?=Loc::getMessage("STOF_2REG")?></a>
				</li>
				
				<li class="<?=$active=="reg" ? 'active': ''?>">
					<a href="#STOF_2NEW"
					   role = "tab" 
					   aria-controls="STOF_2NEW"
					   data-toggle = "tab"
                       class="btn btn-default btn-button"
					><?=Loc::getMessage("STOF_2NEW")?></a>
				</li>
				
			</ul>
		</div>
	</div>
	
	<div class="row">
		<div class="col col-md-4">
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane <?=$active=="auth" ? 'active': ''?>" id="STOF_2REG">
					<? getAuthForm($arResult); ?>
				</div>
				<div role="tabpanel" class="tab-pane <?=$active=="reg" ? 'active': ''?>" id="STOF_2NEW">
					<? getNewUserForm($arResult); ?>
				</div>
			</div>
		</div>
	</div>	
<?else:?>
	<div class="row">
		<div class="col col-md-4">
			<? getAuthForm($arResult); ?>
		</div>
	</div>
<?endif;?>

<div class = "row">
	<div class = "col col-md-4">
		<?=Loc::getMessage("STOF_REQUIED_FIELDS_NOTE"); ?>
	</div>
    <div class="col col-md-12"></div>
	<div class = "col col-md-4">
		<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
			<?=Loc::getMessage("STOF_EMAIL_NOTE")?><br>
			<?=Loc::getMessage("STOF_PRIVATE_NOTES")?><br>
		<?endif;?>
	</div>
</div>
