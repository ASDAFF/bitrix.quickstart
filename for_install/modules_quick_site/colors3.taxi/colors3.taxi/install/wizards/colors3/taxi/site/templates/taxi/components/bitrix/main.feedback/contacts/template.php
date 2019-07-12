<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?><div class="widget form_widget">
	<div class="clearfix cont">
		<h2><?=GetMessage("COLORS3_TAXI_NAPISATQ_PISQMO")?></h2>
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
		
		<form class="form-horizontal" action="<?=$APPLICATION->GetCurPage()?>" method="POST">
		<?=bitrix_sessid_post()?>
			<div class="control-group">
				<label class="control-label">
					<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</label>
				<div class="controls">
					<input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">
					<?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</label>
				<div class="controls">
					<input type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label">
					<?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</label>
				<div class="controls">
					<textarea name="MESSAGE" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
				</div>
			</div>
		
			<?if($arParams["USE_CAPTCHA"] == "Y"):?>
				<div class="mf-captcha">
					<label class="control-label"><?=GetMessage("MFT_CAPTCHA")?></label>
					<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>">
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
					<div class="mf-text"><?=GetMessage("MFT_CAPTCHA_CODE")?><span class="mf-req">*</span></div>
					<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
				</div>
			<?endif;?>
		
			<div class="control-group">
	            <div class="controls submit_controls">
	            	<button style="display: none; width: 187px;" class="btn rel call_me" type="button" name="submit"><?=GetMessage("COLORS3_TAXI_OTPRAVITQ_SOOBSENIE")?></button>
					<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>" />
					<input class="btn call_me" type="submit" name="submit" value="<?=GetMessage("MFT_SUBMIT")?>" />
					
					<script type="text/javascript">
						jQuery(document).ready(function($){
							
						var button = $('button[name=submit]');
							var submit = $('input[name=submit]');
							button.toggle();
							submit.toggle();
							button.on('click', function(){submit.click()});
						})
					</script>

	            </div>
	        </div>
		</form>
	</div>
</div>
<hr />