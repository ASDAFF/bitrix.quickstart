<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="wsm_Callback">
	<span class="openForm" id="wsm_openForm" name="wsm_openForm"><?=GetMessage("OPEN_FORM")?></span>
	<div class="callback_form" id="wsm_callback_block">
		<h2 class="close"><?=GetMessage("OPEN_FORM")?></h2>
		<a class="close">X</a>
		<div class="message"></div>

		<form action="" method="post" name="wsm_callback_form">
			<?foreach($arResult["FORM_PROPERTY"] as $pid => $arProp):?>
			<input type="hidden" name="CALLBACK[SITE_ID]" value="<?=SITE_ID?>"/>
			<div class="line">
				<label for="wsm_callback_<?=$pid?>"><?=$arProp['NAME']?>:<?if($arProp['IS_REQUIRED'] == 'Y'):?><span class="red">*</span><?endif;?>
				</label>
				<?if($arParams["PROPERTY_TIME"] == $arProp["ID"] && $arParams["PROPERTY_TIME"] > 0):?>
					<div class="hour">
						<em><?=GetMessage("CALL_FORM")?></em><?=$arProp['FIELD'][0];?>
						<em><?=GetMessage("CALL_TO")?></em><?=$arProp['FIELD'][1];?>
					</div>
				<?else:?>
					<?=$arProp['FIELD'];?>
				<?endif;?>
			</div>
			<?endforeach;?>
			
			<?if($arParams["FORM_CAPTCHA"] == 'Y'):?>
			<input type="hidden" name="CALLBACK[captcha_sid]" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
			<div class="line">
				<img name="captcha" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
			</div>
			<div class="line captcha">
				<label for="wsm_callback_captcha_word"><?=GetMessage("CAPTCHA_CODE")?>:<span class="red">*</span></label>
				<input type="text" name="CALLBACK[captcha_word]" id="wsm_callback_captcha_word" maxlength="50" value="" placeholder="<?=GetMessage("CAPTCHA_CODE_PH")?>"/>	
			</div>
			<?endif;?>
			
			<div class="line">
				<input type="submit" value="<?=GetMessage("SEND_FORM")?>"></button>
			</div>
		</form>
	</div>
</div>

<script>
var Callback = new BX.wsmCallback({
	form: 'wsm_callback_form', 
	block_id: 'wsm_Callback',
	window_id: 'wsm_callback_block'
	});
</script>