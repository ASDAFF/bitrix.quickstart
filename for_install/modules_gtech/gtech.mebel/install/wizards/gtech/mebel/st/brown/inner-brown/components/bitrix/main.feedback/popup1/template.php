<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?if(!empty($arResult["ERROR_MESSAGE"]))
{?>
<script>
$(document).ready(function(){
	$("div.mf-ok-text").slideToggle("500").delay("5000").slideToggle("500");
});
</script>
	<div class="mf-ok-text"><?foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);?>
	</div>
<?}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{?>
<script>
$(document).ready(function(){
	$("div.mf-ok-text").slideToggle("500").delay("5000").slideToggle("500");
});
</script>
	<div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div>
<?}?>

<div class="profile-menu profile-menu-group">

	<div class="profile-menu-inner">
		<a class="profile-menu-avatar" id="profile-menu-avatar" onclick="return OpenProfileMenuPopup(this);">
		<span style="border-bottom: dashed 1px #fff;"><?=GetMessage("FEEDBACK_TITLE")?></span>
		</a>
	</div>

</div>

<div class="profile-menu-popup profile-menu-popup-group" id="profile-menu-popup">
	<div class="profile-menu-popup-items">
        <div class="feedback-wrap">
                <div class="mfeedback">
                  <form action="" method="POST">
                  <?=bitrix_sessid_post()?>
                  	<div class="mf-name">
                  		<div class="mf-text">
                  			<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
                  		</div>
                          <input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>" class="right">
                  	</div>
                  	<div class="mf-email">
                  		<div class="mf-text">
                  			<?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
                  		</div>
                          <input type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>" class="right">
                  	</div>

                  	<div class="mf-message">
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
                  	<input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>" style="float: right;">
                      <div style="clear: both;"></div>
                  </form>
                </div>
              </div>
    </div>
</div>

<script type="text/javascript">
function OpenProfileMenuPopup(source)
{
	var offsetTop = -20;
	var offsetLeft = -180;

	var ie7 = false;

	if (ie7 || (document.documentMode && document.documentMode <= 7))
	{
		offsetTop = -54;
	    offsetLeft = -12;
	}

	var popup = BX.PopupWindowManager.create("profile-menu", BX("profile-menu-avatar"), {
		offsetTop : offsetTop,
		offsetLeft : offsetLeft,
		autoHide : true,
		closeIcon : true,
		content : BX("profile-menu-popup")
	});

	popup.show();


	BX.bind(popup.popupContainer, "mouseover", BX.proxy(function() {
		if (this.params._timeoutId)
		{
			clearTimeout(this.params._timeoutId);
			this.params._timeoutId = undefined;
		}

		this.show();
	}, popup));

	return false;
}

function CloseProfileMenuPopup(event)
{
	if (!this.params._timeoutId)
		this.params._timeoutId = setTimeout(BX.proxy(function() { this.close()}, this), 300);
}
</script>