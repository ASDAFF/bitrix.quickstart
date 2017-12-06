<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
/**
 * 	Bitrix vars
 *
 * 	@var array $arParams
 * 	@var array $arResult
 * 	@var string $ParamsInBase64
 * 	@var CBitrixComponentTemplate $this
 * 	@global CMain $APPLICATION
 * 	@global CUser $USER
 */
?>

<div class="lw-sp-wrapper-block" data-window-id="<?=$arParams['FORM_ID']?>">
	<div class="lw-sp-window-block sp-window-wrapper" id="window-<?=$arParams['FORM_ID']?>">
		<button class="button close-button close-cross-button"></button>
		<h2><?=$arParams['FORM_NAME']?></h2>
		<form class="form-block" name="form-<?=$arParams['FORM_ID']?>" action="<?=$arParams['CHECK_ORDER_URL']?>" method="POST">
			<p><b class="product-title"></b></p>
			<p class="product-description"></p>
			<p class="price"><?=GetMessage("SP_BOC_PRICE");?> — <span class="product-price">0</span> <?=GetMessage("SP_BOC_RUR");?></p>
			<? foreach ($arParams["USED_FIELDS"] as $UsedField) { ?>
				<div class="holder">
					<? if (in_array($UsedField, $arParams["REQUIRED_FIELDS"])){$REQ='required'; $REQ_P='*';}else{$REQ=''; $REQ_P='';} ?>
					<? if ($UsedField=='PHONE'){?>
						<div class="field-block">
							<label for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"><?=GetMessage("SP_BOC_".$UsedField);?> <?=$REQ_P?></label>
							<label class="addition" for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>">+7</label>
							<input
								id="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"
								class="field phone-field"
								type="tel"
								name="<?=$UsedField?>"
								value="<?=$arResult[$UsedField]?>"
								data-error="<?=GetMessage("SP_BOC_ERR_".$UsedField)?>"
								data-number-error="<?=GetMessage("SP_BOC_ERR_PHONE_NUMBER")?>" <?=$REQ?> />
						</div>
					<? } elseif ($UsedField=='MESSAGE'){?>
						<div class="field-block">
							<label for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"><?=GetMessage("SP_BOC_".$UsedField);?> <?=$REQ_P?></label>
							<textarea
								id="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"
								class="field textarea-field"
								name="<?=$UsedField?>"
								data-error="<?=GetMessage("SP_BOC_ERR_".$UsedField)?>" <?=$REQ?>
								<?=$REQ?>><?=$arResult[$UsedField]?><?=$arResult[$UsedField]?> </textarea>
						</div>
						<? } else {?>
						<? $input_type = ($UsedField=='EMAIL')?'email':'text'; ?>
						<div class="field-block">
							<label for="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"><?=GetMessage("SP_BOC_".$UsedField);?> <?=$REQ_P?></label>
							<input
								id="<?=$UsedField?>-<?=$arParams['FORM_ID']?>"
								class="field"
								type="<?=$input_type;?>"
								name="<?=$UsedField?>"
								value="<?=$arResult[$UsedField]?>"
								data-error="<?=GetMessage("SP_BOC_ERR_".$UsedField)?>" <?=$REQ?> />
						</div>
					<? } ?>
				</div>
			<? } ?>
			<button class="button lw-sp-form-submit"><?=$arParams['BUTTON_NAME']?></button>
			<div class="hidden">
				<?=bitrix_sessid_post();?>
				<input type="hidden" id="PARAMS" data-params="<?=$arParams['IN_BASE64']?>" />
				<input type="hidden" id="PRODUCT-ID" name="PRODUCT_ID" value="" />
			</div>
		</form>
	</div>
	
	<div class="lw-sp-window-block sp-get-wrapper" id="get-<?=$arParams['FORM_ID']?>">
		<? $form_id = $arParams['FORM_ID']; ?>
		<button class="button close-button close-cross-button"></button>
		<h2><?=GetMessage("SP_BOC_GET_FORM_TITLE");?></h2>
		<form class="form-block get-form-block" action="<?=$arParams['GET_ORDER_URL']?>" method="POST">
			<div class="holder">
				<div class="field-block">
					<label for="order-get-<?=$form_id;?>"><?=GetMessage("SP_BOC_GET_FORM_ORDER_INPUT_LABEL");?></label>
					<input class="field" id="order-get-<?=$form_id;?>" type="tel" name="ORDER_ID" data-error="<?=GetMessage("SP_BOC_GET_FORM_ORDER_INPUT_ERROR");?>" required />
				</div>
			</div>
			<div class="holder">
				<div class="field-block">
					<label for="password-get-<?=$form_id;?>"><?=GetMessage("SP_BOC_GET_FORM_PASSWORD_INPUT_LABEL");?></label>
					<input class="field" id="password-get-<?=$form_id;?>" type="tel" name="PASSWORD" data-error="<?=GetMessage("SP_BOC_GET_FORM_PASSWORD_INPUT_ERROR");?>" required />
				</div>
			</div>
			<button class="button lw-sp-form-submit"><?=GetMessage("SP_BOC_GET_FORM_SUBMIT_BUTTON");?></button>
			<div class="hidden">
				<?=bitrix_sessid_post();?>
				<input type="hidden" name="PARAMS" value="<?=$arParams['IN_BASE64'];?>" />
			</div>
		</form>
		<div class="message-block">
			<p class="message" data-old-title="<?=GetMessage("SP_BOC_GET_FORM_SUCCESS_MESSAGE");?>"><?=GetMessage("SP_BOC_GET_FORM_SUCCESS_MESSAGE");?></p>
			<p class="email-value"></p>
			<button class="button close-button close-text-button"><?=GetMessage("SP_BOC_GET_FORM_CLOSE_BUTTON");?></button>
		</div>
	</div>

</div>