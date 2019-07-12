<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["LAST_ERROR"]!=""):?>
	<?ShowError( $arResult["LAST_ERROR"] );?>
<?endif;?>

<div class="form_wrap">
	<form action="<?=$arResult["ACTION_URL"]?>" method="POST">
		<?=bitrix_sessid_post()?>
		<h3><?=$arParams['FORM_TITLE']?></h3>
		<input type="hidden" name="<?=$arParams["REQUEST_PARAM_NAME"]?>" value="Y" />
		<? foreach ($arResult['FIELDS'] as $arField) { ?>
			<? if ($arField['SHOW']=="Y") { ?>
				<? if ($arField['CONTROL_NAME']=='RS_TEXTAREA') { ?>
					<div class="field_wrap">
						<? if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!="") { ?>
							<?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:
						<? } else { ?>
							<?=GetMessage("MSG_".$arField["CONTROL_NAME"])?>: 
						<? } ?> 
						<?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])):?><span class="redsign_devcom_required">*</span><?endif;?>
						<textarea name="<?=$arField["CONTROL_NAME"]?>"><?=$arField["HTML_VALUE"]?></textarea><br />
					</div>
				<? } else { ?>
					<div class="field_wrap">
						<? if (isset($arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]) && $arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]!="") { ?>
							<?=$arParams["INPUT_NAME_".$arField["CONTROL_NAME"]]?>:
						<? } else { ?>
							<?=GetMessage("MSG_".$arField["CONTROL_NAME"])?>: 
						<? } ?>
						<?if(in_array($arField["CONTROL_NAME"], $arParams["REQUIRED_FIELDS"])):?><span class="redsign_devcom_required">*</span><?endif;?>
						<input type="text" name="<?=$arField["CONTROL_NAME"]?>" value="<?=$arField["HTML_VALUE"]?>" /><br />
					</div>
				<? } ?>
			<? } ?>
		<? } ?>
		
		<?if($arParams["ALFA_USE_CAPTCHA"]=="Y"):?>
			<div class="captcha_wrap">
				<?=GetMessage("MSG_CAPTHA")?><span class="redsign_devcom_required">*</span>:<br />
				<input type="text" name="captcha_word" size="30" maxlength="50" value=""><br />
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CATPCHA_CODE"]?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CATPCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA"><br />
			</div>
		<?endif;?>
		<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
		<input type="submit" name="submit" value="<?=GetMessage("MSG_SUBMIT")?>">
	</form>
</div>