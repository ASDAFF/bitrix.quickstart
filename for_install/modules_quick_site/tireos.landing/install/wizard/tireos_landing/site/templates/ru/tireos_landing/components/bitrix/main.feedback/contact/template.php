<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
?>

<div class="mfeedback">
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	/*foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);*/
?>

    <div class="alert alert-danger">
		<a class="close" data-dismiss="alert">x</a>
		<?=GetMessage("MFT_WARN")?>
    </div>

<?
}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
} else {
?>

		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>

		<form id='contact' action="<?=POST_FORM_ACTION_URI?>" method="post" accept-charset="utf-8" role="form">
        <?=bitrix_sessid_post()?>
			<input type="hidden" name='resultCaptcha' value=''>
			<div class='control-group'>
				<input type="text" name='user_name' value="<?=$arResult["AUTHOR_NAME"]?>" placeholder='<?=GetMessage("MFT_NAME")?>' data-required>
			</div>
			<div class='control-group'>
				<input type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>" placeholder='<?=GetMessage("MFT_EMAIL")?>' data-required class='insert-attr'>
			</div>
			<div class='control-group'>
				<textarea name='MESSAGE' cols="30" rows="10" maxlength="300" placeholder='<?=GetMessage("MFT_MESSAGE")?>' data-required><?=$arResult["MESSAGE"]?></textarea>
			</div>
            
            
            
            
            
            <?if($arParams["USE_CAPTCHA"] == "Y"):?>
			<div class='control-group captcha'>
				<div class="picture-code">
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" alt="CAPTCHA" style="width:120px; height:44px;">
					<? /*What is <span id="numb1">4</span> + <span id="numb2">1</span> (Anti-spam)*/?>
				</div>
				<input type="text" placeholder='<?=GetMessage("MFT_CAPTCHA_CODE")?>' name='captcha_word' id='chek' data-required data-pattern="5">
                <input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
			</div>
            <?endif;?>
            
            
            
            <input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
            <button type="submit" class='btn submit' name="submit" value="<?=GetMessage("MFT_SUBMIT")?>"><?=GetMessage("MFT_SUBMIT")?></button>

		</form>


<? } ?>




<? /*<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
<?=bitrix_sessid_post()?>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
	</div>
	<div class="mf-email">
		<div class="mf-text">
			<?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<input type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
	</div>

	<div class="mf-message">
		<div class="mf-text">
			<?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<textarea name="MESSAGE" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
	</div>

	<?if($arParams["USE_CAPTCHA"] == "Y"):?>
	<div class="mf-captcha">
		<div class="mf-text"><?=GetMessage("MFT_CAPTCHA")?></div>
		<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
		<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA">
		<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
		<input type="text" name="captcha_word" size="30" maxlength="50" value="">
	</div>
	<?endif;?>
	<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
	<input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>">
</form>
</div>*/ ?>