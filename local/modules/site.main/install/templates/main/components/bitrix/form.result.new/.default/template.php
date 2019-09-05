<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

$formId = 'web-form-' . $arResult['arForm']['ID'];
$labelId = 'form-result-new-label-' . $arResult['arForm']['ID'];
$showTitle = $arResult['isFormTitle'] == 'Y' && $arParams['HIDE_TITLE'] != 'Y';
?>

<?if ($arParams['POPUP_MODE'] == 'Y') {
	?>
	<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="<?=$labelId?>" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<?if ($showTitle) {
					?>
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=GetMessage('FORM_CLOSE')?></span></button>
						<h4 class="modal-title" id="<?=$labelId?>"><?=$arResult['FORM_TITLE']?></h4>
					</div>
					<?
					$showTitle = false;
				}?>
				<div class="modal-body">
	<?
}?>

<div class="form-result-new form-result-new-default form" role="form">
	<?
	if (($arResult['isFormErrors'] == 'Y' || $arResult['isFormNote'] == 'Y')
		&& $arParams['SCROLL_TO'] == 'Y') {
		?>
		<a name="<?=$formId?>"></a>
		<script>
			$(function() {
				document.location.hash = '#<?=$formId?>';
			})
		</script>
		<?
	}
	
	if ($arResult['isFormErrors'] == 'Y') {
		print $arResult['FORM_ERRORS_TEXT'];
	}
	
	if ($arResult['isFormNote'] == 'Y') {
		ShowNote($arResult['FORM_NOTE']);
		
		if ($arParams['POPUP_MODE'] == 'Y') {
			?>
			<div class="form-group form-toolbar">
				<button class="btn btn-default btn-close"><?=GetMessage('FORM_CLOSE')?></button>
			</div>
			<?
		}
	} else {
		?>
		<?=$arResult['FORM_HEADER']?>
		
		<?if ($arResult['isFormDescription'] == 'Y'
			|| $showTitle
			|| $arResult['isFormImage'] == 'Y'
		) {
			?>
			<div class="form-header">
				<?if ($showTitle) {
					?>
					<h2 class="form-header-title"><?=$arResult['FORM_TITLE']?></h2>
					<?
				}?>
				<div class="form-header-body">
					<?if ($arResult['isFormImage'] == 'Y') {
						?>
						<img
							class="form-header-image img-responsive"
							src="<?=$arResult['FORM_IMAGE']['URL']?>"
							<?=$arResult['FORM_IMAGE']['ATTR']?>
							alt="<?=$arResult['FORM_TITLE']?>"
						/>
						<?
					}?>
					<?if($arResult['FORM_DESCRIPTION']) {
						print $arResult['FORM_DESCRIPTION'];
					}?>
				</div>
			</div>
			<?
		}?>
		
		<div class="form-body">
			<?foreach($arResult['QUESTIONS'] as $questionID => $question) {
				switch ($question['STRUCTURE'][0]['FIELD_TYPE']) {
					case 'hidden':
						print $question['HTML_CODE'];
						break;
					
					case 'checkbox':
					case 'radio':
						?>
						<div class="form-group group-<?=$questionID?><?=$question['HAS_ERROR'] ? ' has-error' : ''?>">
							<div>
								<label class="control-label<?=$question['REQUIRED'] == 'Y' ? ' required' : ''?>"><?=$question['CAPTION']?>:</label>
							</div>
							<?=$question['HTML_CODE']?>
						</div>
						<?
						break;
					
					default:
						?>
						<div class="form-group group-<?=$questionID?><?=$question['HAS_ERROR'] ? ' has-error' : ''?>">
							<label class="control-label<?=$question['REQUIRED'] == 'Y' ? ' required' : ''?>" for="<?=$question['DOM_ID']?>"><?=$question['CAPTION']?>:</label>
							<?=$question['HTML_CODE']?>
							<?=$question['IS_INPUT_CAPTION_IMAGE'] == 'Y' ? '<span class="field-image">' . $question['IMAGE']['HTML_CODE'] . '</span>' : ''?>
						</div>
						<?
				}?>
				<?
			}?>
			
			<?if($arResult['isUseCaptcha'] == 'Y') {
				$isError = is_array($arResult['FORM_ERRORS']) && array_key_exists(0, $arResult['FORM_ERRORS']);
				?>
				<div class="group-captcha<?=$isError ? ' has-error' : ''?>">
					<label class="control-label required" for="field-captcha">
						<?/*=GetMessage('FORM_CAPTCHA_TABLE_TITLE')*/?>
						<?=GetMessage('FORM_CAPTCHA_FIELD_TITLE')?>:
					</label>
					<div class="row">
						<div class="form-group col-sm-5 col-md-<?=$arParams['POPUP_MODE'] == 'Y' ? 5 : 3?>">
							<img
								class="form-captcha-img img-responsive"
								src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialchars($arResult['CAPTCHACode'])?>"
								alt="captcha"
							/>
						</div>
						<div class="form-group col-sm-7 col-md-<?=$arParams['POPUP_MODE'] == 'Y' ? 7 : 9?>">
							<input
								class="form-control"
								id="field-captcha"
								type="text"
								name="captcha_word"
								required=""
								value=""
							/>
						</div>
					</div>
					<input type="hidden" name="captcha_sid" value="<?=htmlspecialchars($arResult['CAPTCHACode'])?>"/>
				</div>
				<?
			}?>
			
			<div class="form-group form-toolbar">
				<button
					class="btn btn-default"
					type="submit"
					<?=intval($arResult['F_RIGHT']) < 10 ? ' disabled="disabled"' : ''?>
				>
					<?=htmlspecialchars(trim($arResult['arForm']['BUTTON']) ? $arResult['arForm']['BUTTON'] : GetMessage('FORM_ADD'))?>
				</button>
				<input type="hidden" name="web_form_submit" value="Y"/>
			</div>
		</div>
		
		<div class="form-group form-info">
			<p class="help-block">
				<span class="required"></span> &mdash; <?=GetMessage('FORM_REQUIRED_FIELDS')?>
			</p>
		</div>
		
		<?=$arResult['FORM_FOOTER']?>
		<?
	}?>
</div>

<?if ($arParams['POPUP_MODE'] == 'Y') {
	?>
				</div>
			</div>
		</div>
	</div>
	<?
}?>
