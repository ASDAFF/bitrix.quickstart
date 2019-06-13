<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<div class="mcallback">
<?if(!empty($arResult["ERROR_MESSAGE"]))
{
	foreach($arResult["ERROR_MESSAGE"] as $v)
		ShowError($v);
}
if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
}
?>

<form action="<?=$APPLICATION->GetCurPage()?>" method="POST">
<?=bitrix_sessid_post()?>
	<div class="mf-name">
		<div class="mf-text">
			<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
	</div>
	<div class="mf-phone">
		<div class="mf-text">
			<?=GetMessage("MFT_PHONE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("PHONE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<input type="text" name="user_phone" value="<?=$arResult["AUTHOR_PHONE"]?>">
	</div>
	<div class="mf-time">
		<div class="mf-text">
			<?=GetMessage("MFT_TIME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TIME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
		</div>
		<?
		    $now = date("H:i");
		    $arNow = split(":",$now);
		?>
		<?=GetMessage("MFT_TIME_FROM")?>:
		<?$APPLICATION->IncludeComponent("bitrix:main.clock", "", Array(
				"INPUT_ID" => "user_time_from",
				"INPUT_TIME" => GetMessage("MFT_INPUT_TIME"),
				"INPUT_TITLE" => GetMessage("MFT_INPUT_TITLE_FROM"),
				"INIT_TIME" => $now,
				"STEP" => 0
			)
		);?>
		<?=GetMessage("MFT_TIME_TO")?>: 
		<?$APPLICATION->IncludeComponent("bitrix:main.clock", "", Array(
				"INPUT_ID" => "user_time_to",
				"INPUT_TIME" => GetMessage("MFT_INPUT_TIME"),
				"INPUT_TITLE" => GetMessage("MFT_INPUT_TITLE_TO"),
				"INIT_TIME" => (IntVal($arNow[0])+1).":".$arNow[1],
				"STEP" => 0
			)
		);?>
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
	<input type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>">
</form>
</div>