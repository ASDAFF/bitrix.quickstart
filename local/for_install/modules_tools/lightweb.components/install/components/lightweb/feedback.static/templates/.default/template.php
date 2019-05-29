<? if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die(); ?>

<div class="lw-fs-form-wrapper">
	<form class="form-block" name="<?=$arParams['FORM_ID']?>" action="<?=$arParams['EXECUTE_URL']?>">
		<? foreach ($arParams["USED_FIELDS"] as $UsedField) { ?>
			<div class="holder">
				<? if (in_array($UsedField, $arParams["REQUIRED_FIELDS"])){$REQ='required'; $REQ_P='*';}else{$REQ=''; $REQ_P='';} ?>
				<? if ($UsedField=='PHONE'){ ?>
					<label for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"><?=GetMessage("MFT_".$UsedField);?> <?=$REQ_P?></label>
					<div class="field-block">
						<label class="addition" for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>">+7</label>
						<input class="phone-field field" id="<?=$UsedField?>-<?=$arParams['FORM_ID']?>" name="<?=$UsedField?>" type="tel" value="<?=$arResult[$UsedField]?>" data-error="<?=GetMessage("MFT_ERR_".$UsedField)?>" <?=$REQ?> />
					</div>
				<?} elseif ($UsedField=='MESSAGE') {?>
					<label for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"><?=GetMessage("MFT_".$UsedField);?> <?=$REQ_P?></label>
					<div class="field-block">
						<textarea class="text-field field" id="<?=$UsedField?>-<?=$arParams['FORM_ID']?>" name="<?=$UsedField?>" data-error="<?=GetMessage("MFT_ERR_".$UsedField)?>" <?=$REQ?>><?=$arResult[$UsedField]?></textarea>
					</div>
				<?} else {?>
					<? $input_type = ($UsedField=='EMAIL')?'email':'text'; ?>
					<label for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"><?=GetMessage("MFT_".$UsedField);?> <?=$REQ_P?></label>
					<div class="field-block">
						<input class="field" id="<?=$UsedField?>-<?=$arParams['FORM_ID']?>" name="<?=$UsedField?>" type="<?=$input_type?>" value="<?=$arResult[$UsedField]?>" data-error="<?=GetMessage("MFT_ERR_".$UsedField)?>" <?=$REQ?> />
					</div>
				<? } ?>
			</div>
		<? } ?>
		<button class="button form-submit"><?=$arParams['BUTTON_NAME']?></button>
		<div style="display:none;">
			<?=bitrix_sessid_post();?>
			<input type="hidden" name="PARAMETERS" value="<?=$arParams['IN_BASE64']?>" />
		</div>
	</form>
	<div class="success-block">
		<p><?=$arParams['OK_TEXT']?></p>
	</div>
</div>