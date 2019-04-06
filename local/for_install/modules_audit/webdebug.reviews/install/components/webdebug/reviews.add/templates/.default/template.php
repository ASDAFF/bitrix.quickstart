<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?if($arParams["INCLUDE_JQUERY"]=="Y"):?><script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script><?endif?>

<input type="button" id="webdebug-reviews-add-button" value="<?=GetMessage("WEBDEBUG_REVIEWS_ADD_BUTTON")?>" style="display:none" />
<div id="webdebug-reviews-add">
	<form action="" method="post" enctype="multipart/form-data">
		<div id="webdebug-reviews-form-data">
			
			<?// Show messages ?>
			<?if($arResult["SUCCESS"]=="Y"):?>
				<div class="webdebug-reviews-add-success"><?=$arParams["SUCCESS_MESSAGE"]?></div>
				<?if($arParams["USE_MODERATE"]!="N"):?>
					<div class="webdebug-reviews-add-success"><?=GetMessage("WEBDEBUG_REVIEWS_SUCCESS_BUT_USING_MODERATE")?></div>
				<?endif?>
			<?elseif($arResult["SUCCESS"]=="N" && is_array($arResult["ERROR_MESSAGES"])):?>
				<br/>
				<?foreach($arResult["ERROR_MESSAGES"] as $ErrorMessage):?>
					<div class="webdebug-reviews-add-error"><?=$ErrorMessage?></div>
				<?endforeach?>
			<?endif?>
			
			<?// Show moderate message ?>
			<?if($arParams["USE_MODERATE"]!="N"):?>
				<div class="webdebug-reviews-add-notice"><?=GetMessage("WEBDEBUG_REVIEWS_NOTICE_USE_MODERATE");?></div>
			<?endif?>
		
			<?// Iterate all fields ?>
			<?if(in_array("NAME", $arParams["DISPLAY_FIELDS"])):?>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_NAME")?><?if(in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>:</div>
				<div class="input"><input class="text" type="text" size="36" name="name" value="<?=strip_tags($arResult["NAME"]);?>" /></div>
				<div class="space"></div>
			<?endif?>
			<?if(in_array("EMAIL", $arParams["DISPLAY_FIELDS"])):?>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_EMAIL")?><?if(in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>:</div>
				<div class="input"><input class="text" type="text" size="36" name="email" value="<?=strip_tags($arResult["EMAIL"]);?>" /><?if($arParams["EMAIL_PUBLIC"]=="Y"):?><label><input type="checkbox" size="36" name="email_public" value="Y"<?if($arResult["EMAIL_PUBLIC"]=="Y"):?> checked="checked"<?endif?> /><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_EMAIL_PUBLIC")?></label><?endif?></div>
				<div class="space"></div>
			<?endif?>
			<?if(in_array("WWW", $arParams["DISPLAY_FIELDS"])):?>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_WWW")?><?if(in_array("WWW", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>:</div>
				<div class="input"><input class="text" type="text" size="36" name="www" value="<?=strip_tags($arResult["WWW"]);?>" /></div>
				<div class="space"></div>
			<?endif?>
			<?if(in_array("TEXT_PLUS", $arParams["DISPLAY_FIELDS"])):?>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_TEXT_PLUS")?><?if(in_array("TEXT_PLUS", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>:</div>
				<div class="input"><textarea class="text" name="text_plus" cols="34" rows="3"><?=$arResult["TEXT_PLUS"]?></textarea></div>
				<div class="space"></div>
			<?endif?>
			<?if(in_array("TEXT_MINUS", $arParams["DISPLAY_FIELDS"])):?>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_TEXT_MINUS")?><?if(in_array("TEXT_MINUS", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>:</div>
				<div class="input"><textarea class="text" name="text_minus" cols="34" rows="3"><?=$arResult["TEXT_MINUS"]?></textarea></div>
				<div class="space"></div>
			<?endif?>
			<?if(in_array("TEXT_COMMENTS", $arParams["DISPLAY_FIELDS"])):?>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_TEXT_COMMENTS")?><?if(in_array("TEXT_COMMENTS", $arParams["REQUIRED_FIELDS"])):?><span class="req">*</span><?endif?>:</div>
				<div class="input"><textarea class="text" name="text_comments" cols="34" rows="3"><?=$arResult["TEXT_COMMENTS"]?></textarea></div>
				<div class="space"></div>
			<?endif?>
			
			<?// VOTES ?>
			<?for($i=0; $i<10; $i++):?>
				<?if(in_array("VOTE_".$i, $arParams["DISPLAY_FIELDS"])):?>
					<div class="label"><?=$arResult["VOTE_NAME_".$i]?>:</div>
					<div class="input">
						<div class="webdebug-rating" id="vote_<?=$i?>">
							<input type="hidden" value="<?=$arResult["VOTE_".$i]?>" />
							<label><input type="radio" name="vote_<?=$i?>" value="1" />1</label>
							<label><input type="radio" name="vote_<?=$i?>" value="2" checked="checked" />2</label>
							<label><input type="radio" name="vote_<?=$i?>" value="3" />3</label>
							<label><input type="radio" name="vote_<?=$i?>" value="4" />4</label>
							<label><input type="radio" name="vote_<?=$i?>" value="5" />5</label>
						</div>
					</div>
					<div class="space"></div>
				<?endif?>
			<?endfor?>
			<?if ($arResult["USE_CAPTCHA"]):?>
				<div class="captcha" id="webdebug-reviews-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_ID"]?>&<?=mt_rand()/1000000000000000;?>" alt="CAPTCHA" height="40" width="180" /></div>
				<div class="label"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_CAPTCHA")?> (<a id="webdebug-reviews-refresh" href="#"><?=GetMessage("WEBDEBUG_REVIEWS_FIELD_CAPTCHA_REFRESH")?></a>):</div>
				<div class="input"><input type="text" size="10" name="captcha_word" /><input type="hidden" name="captcha_id" value="<?=$arResult["CAPTCHA_ID"]?>"></div>
			<?endif?>
			<br/>
			<input type="hidden" name="sent" value="Y" />
			<?=bitrix_sessid_post();?>
			<input id="webdebug-reviews-submit" type="submit" value="<?=GetMessage("WEBDEBUG_REVIEWS_FORM_SUBMIT");?>" />
		</div>
	</form>
</div>

<script type="text/javascript" src="<?=$this->__folder?>/js/webdebug.jquery.rating.js"></script>
<?if ($arResult["USE_CAPTCHA"]):?>
	<script type="text/javascript">
	$("#webdebug-reviews-refresh").click(function(){
		var CaptchaID = $(this).parent().parent().find("[name=captcha_id]").val();
		$("#webdebug-reviews-captcha img").attr("src", "/bitrix/tools/captcha.php?captcha_sid="+CaptchaID+"&"+Math.random());
		return false;
	});
	</script>
<?endif?>

<?if($arResult["SUCCESS"]!="Y"):?>
<script type="text/javascript">
<?if(!in_array($arResult["SUCCESS"],array("Y","N"))):?>$("#webdebug-reviews-add").slideUp(0);<?endif?>
$("#webdebug-reviews-add-button").css("display", "").click(function(){
	$(this).remove();
	$("#webdebug-reviews-add").slideDown(500);
});
</script>
<?endif?>