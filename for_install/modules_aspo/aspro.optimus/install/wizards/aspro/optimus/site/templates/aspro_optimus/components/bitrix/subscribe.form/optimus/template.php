<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<div class="subscribe-form_footer"  id="subscribe-form_footer">
	<div class="wrap_md">
		<div class="wrap_bg iblock">
			<div class="wrap_text">
				<div class="wrap_icon iblock">
					
				</div>
				<div class="wrap_more_text iblock">
					<?$APPLICATION->IncludeFile(SITE_DIR."include/subscribe_text_footer.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("TEXT_BLOCK_FOOTER"),));?>
				</div>
			</div>
		</div>
		<div class="forms iblock">
			
				<form action="<?=$arResult["FORM_ACTION"];?>" class="sform_footer box-sizing">
					<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
						<label for="sf_RUB_ID_<?=$itemValue["ID"]?>" class="hidden">
							<input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?=$itemValue["ID"]?>" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /> <?=$itemValue["NAME"]?>
						</label>
					<?endforeach;?>
					<div class="wrap_md">
						<div class="email_wrap form-control iblock">
							<input type="email" name="sf_EMAIL" class="grey medium" required size="20" value="<?=$arResult["EMAIL"]?>" placeholder="<?=GetMessage("subscr_form_email_title")?>" />
						</div>
						<div class="button_wrap iblock">
							<input type="submit" name="OK" class="button medium" value="<?=($arResult["EMAIL"] ? GetMessage("subscr_form_button_change") : GetMessage("subscr_form_button"));?>" />
						</div>
					</div>
				</form>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$("form.sform_footer").validate({
			rules:{ "sf_EMAIL": {email: true} }
		});
	})
</script>
<?$frame->end();?>
