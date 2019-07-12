<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams, $arResult
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 * @var CUser $USER
 */
?><? if ($arParams['SHOW_MINIMIZED'] == "Y")
{
	?>
	<div class="reviews-collapse reviews-minimized" style='position:relative; float:none;'>
		<span class="reviews-collapse-link button wicon" id="sw<?=$arParams["FORM_ID"]?>"><i></i><span><?=$arParams['MINIMIZED_EXPAND_TEXT']?></span></span>
	</div>
	<?
}

$tabIndex = ($tabIndex ? $tabIndex : 1);
?>

<a data-name="review_anchor"></a>
<?
if (!empty($arResult["ERROR_MESSAGE"])):
	$arResult["ERROR_MESSAGE"] = preg_replace(array("/<br(.*?)><br(.*?)>/is", "/<br(.*?)>$/is"), array("<br />", ""), $arResult["ERROR_MESSAGE"]);
	?>
	<div class="reviews-note-box reviews-note-error">
		<div class="reviews-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "reviews-note-error");?></div>
	</div>
<?
endif;
?>
<script type="text/javascript">
window.reviewsCtrlEnterHandler<?=CUtil::JSEscape($arParams["form_index"]);?> = function()
{
	if (window.<?=$arParams["jsObjName"]?>)
		window.<?=$arParams["jsObjName"]?>.SaveContent();
	BX.submit(BX('<?=$arParams["FORM_ID"] ?>'), 'preview_comment', 'N');
}
</script>
<div class="reviews-reply-form" <?=(($arParams['SHOW_MINIMIZED'] == "Y" && $iCount>=1) ? 'style="display:none;"' : '' )?>>
<form name="<?=$arParams["FORM_ID"] ?>" id="<?=$arParams["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
?> method="POST" enctype="multipart/form-data" <?
?> onsubmit="return window.UC.f<?=$arParams["FORM_ID"]?>.validate('<?=$arParams["AJAX_POST"]?>');"<?
?> class="reviews-form">
	<input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams["form_index"])?>" />
	<input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
	<input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
	<input type="hidden" name="SECTION_ID" value="<?=$arResult["ELEMENT_REAL"]["IBLOCK_SECTION_ID"]?>" />
	<input type="hidden" name="save_product_review" value="Y" />
	<input type="hidden" name="preview_comment" value="N" />
	<?=bitrix_sessid_post()?>
	<?
	if ($arParams['AUTOSAVE'])
		$arParams['AUTOSAVE']->Init();
	?>
	<div style="position:relative; display: block; width:100%;">
		<?
		/* GUEST PANEL */
		if (!$arResult["IS_AUTHORIZED"]):
			?>
			<div class="reviews-reply-fields">
				<div class="reviews-reply-field-user">
					<div class="reviews-reply-field reviews-reply-field-author"><label for="REVIEW_AUTHOR<?=$arParams["form_index"]?>"><?=GetMessage("OPINIONS_NAME")?><?
							?><span class="reviews-required-field">*</span></label>
						<span><input name="REVIEW_AUTHOR" id="REVIEW_AUTHOR<?=$arParams["form_index"]?>" size="30" type="text" value="<?=$arResult["REVIEW_AUTHOR"]?>" tabindex="<?=$tabIndex++;?>" /></span></div>
					<?
					if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y"):
						?>
						<div class="reviews-reply-field-user-sep">&nbsp;</div>
						<div class="reviews-reply-field reviews-reply-field-email"><label for="REVIEW_EMAIL<?=$arParams["form_index"]?>"><?=GetMessage("OPINIONS_EMAIL")?></label>
							<span><input type="text" name="REVIEW_EMAIL" id="REVIEW_EMAIL<?=$arParams["form_index"]?>" size="30" value="<?=$arResult["REVIEW_EMAIL"]?>" tabindex="<?=$tabIndex++;?>" /></span></div>
					<?
					endif;
					?>
					<div class="reviews-clear-float"></div>
				</div>
			</div>
		<?
		endif;
		?>
		<div class="reviews-reply-header"><span><?=$arParams["MESSAGE_TITLE"]?></span><span class="reviews-required-field">*</span></div>
		<div class="reviews-reply-field reviews-reply-field-text">
			<?
			$arSmiles = array();
			if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y")
			{
				foreach($arResult["SMILES"] as $arSmile)
				{
					$arSmiles[] = array_change_key_case($arSmile, CASE_LOWER) + array(
						'path' => $arSmile["IMAGE"],
						'code' => array_shift(explode(" ", str_replace("\\\\","\\",$arSmile["TYPING"]))));
				}
			}

			CModule::IncludeModule("fileman");
			AddEventHandler("fileman", "OnIncludeLightEditorScript", "CustomizeLHEForForum");

			$LHE = new CLightHTMLEditor();

			$arEditorParams = array(
				'bRecreate' => true,
				'id' => $arParams["LheId"],
				'content' => isset($arResult["REVIEW_TEXT"]) ? $arResult["REVIEW_TEXT"] : "",
				'inputName' => "REVIEW_TEXT",
				'inputId' => "",
				'width' => "100%",
				'height' => "200px",
				'minHeight' => "200px",
				'bUseFileDialogs' => false,
				'bUseMedialib' => false,
				'BBCode' => true,
				'bBBParseImageSize' => true,
				'jsObjName' => $arParams["jsObjName"],
				'toolbarConfig' => array(),
				'smileCountInToolbar' => 3,
				'arSmiles' => $arSmiles,
				'bQuoteFromSelection' => true,
				'ctrlEnterHandler' => 'reviewsCtrlEnterHandler'.$arParams["form_index"],
				'bSetDefaultCodeView' => ($arParams['EDITOR_CODE_DEFAULT'] === 'Y'),
				'bResizable' => true,
				'bAutoResize' => true
			);

			$arEditorParams['toolbarConfig'] = forumTextParser::GetEditorToolbar(array('forum' => $arResult['FORUM']));
			$LHE->Show($arEditorParams);
			?>
		</div>
		<?

		/* CAPTHCA */
		if (strLen($arResult["CAPTCHA_CODE"]) > 0):
			?>
			<div class="reviews-reply-field reviews-reply-field-captcha">
				<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
				<div class="reviews-reply-field-captcha-label">
					<label for="captcha_word"><?=GetMessage("F_CAPTCHA_PROMT")?><span class="reviews-required-field">*</span></label>
					<input type="text" size="30" name="captcha_word" tabindex="<?=$tabIndex++;?>" autocomplete="off" />
				</div>
				<div class="reviews-reply-field-captcha-image">
					<img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" />
				</div>
			</div>
		<?
		endif;
		/* ATTACH FILES */
		if ($arResult["SHOW_PANEL_ATTACH_IMG"] == "Y"):
			?>
			<div class="reviews-reply-field reviews-reply-field-upload">
				<?
				$iCount = 0;
				if (!empty($arResult["REVIEW_FILES"])):
					foreach ($arResult["REVIEW_FILES"] as $key => $val):
						$iCount++;
						$sFileSize = CFile::FormatSize(intval($val["FILE_SIZE"]));
						?>
						<div class="reviews-uploaded-file">
							<input type="hidden" name="FILES[<?=$key?>]" value="<?=$key?>" />
							<input type="checkbox" name="FILES_TO_UPLOAD[<?=$key?>]" id="FILES_TO_UPLOAD_<?=$key?>" value="<?=$key?>" checked="checked" />
							<label for="FILES_TO_UPLOAD_<?=$key?>"><?=$val["ORIGINAL_NAME"]?> (<?=$val["CONTENT_TYPE"]?>) <?=$sFileSize?>
								( <a href="/bitrix/components/bitrix/forum.interface/show_file.php?action=download&amp;fid=<?=$key?>"><?=GetMessage("F_DOWNLOAD")?></a> )
							</label>
						</div>
					<?
					endforeach;
				endif;
				if ($iCount < $arParams["FILES_COUNT"]):
					$sFileSize = CFile::FormatSize(intVal(COption::GetOptionString("forum", "file_max_size", 5242880)));
					?>
					<div class="reviews-upload-info" style="display:none;" id="upload_files_info_<?=$arParams["form_index"]?>">
						<?
						if ($arParams["FORUM"]["ALLOW_UPLOAD"] == "F"):
							?>
							<span><?=str_replace("#EXTENSION#", $arParams["FORUM"]["ALLOW_UPLOAD_EXT"], GetMessage("F_FILE_EXTENSION"))?></span>
						<?
						endif;
						?>
						<span><?=str_replace("#SIZE#", $sFileSize, GetMessage("F_FILE_SIZE"))?></span>
					</div>
					<?

					for ($ii = $iCount; $ii < $arParams["FILES_COUNT"]; $ii++):
						?>

						<div class="reviews-upload-file" style="display:none;" id="upload_files_<?=$ii?>_<?=$arParams["form_index"]?>">
							<input name="FILE_NEW_<?=$ii?>" type="file" size="30" />
						</div>
					<?
					endfor;
					?>
					<a class="forum-upload-file-attach" href="javascript:void(0);" onclick="AttachFile('<?=$iCount?>', '<?=($ii - $iCount)?>', '<?=$arParams["form_index"]?>', this); return false;">
						<span><?=($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y") ? GetMessage("F_LOAD_IMAGE") : GetMessage("F_LOAD_FILE") ?></span>
					</a>
				<?
				endif;
				?>
			</div>
		<?
		endif;
		?>
		<div class="reviews-reply-field reviews-reply-field-settings filter">
			<?
			/* SMILES */
			if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):
				?>
				<div class="reviews-reply-field-setting">
					<input type="checkbox" name="REVIEW_USE_SMILES" id="REVIEW_USE_SMILES<?=$arParams["form_index"]?>" <?
					?>value="Y" <?=($arResult["REVIEW_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
					?>tabindex="<?=$tabIndex++;?>" /><?
					?><label for="REVIEW_USE_SMILES<?=$arParams["form_index"]?>"><span class="bx_filter_input_checkbox"><?=GetMessage("F_WANT_ALLOW_SMILES")?></span></label></div>
			<?
			endif;
			/* SUBSCRIBE */
			if ($arResult["SHOW_SUBSCRIBE"] == "Y"):
				?>
				<div class="reviews-reply-field-setting">
					<input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
					?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" /><?
					?><label for="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>"><span class="bx_filter_input_checkbox"><?=GetMessage("F_WANT_SUBSCRIBE_TOPIC")?></span></label></div>
			<?
			endif;
			?>
		</div>
		<?

		?>
		<div class="reviews-reply-buttons">
			<input name="send_button" type="submit" class="button" value="<?=GetMessage("OPINIONS_SEND")?>" tabindex="<?=$tabIndex++;?>" <?
			?>onclick="this.form.preview_comment.value = 'N';" />
			<input name="view_button" type="submit" class="button transparent" value="<?=GetMessage("OPINIONS_PREVIEW")?>" tabindex="<?=$tabIndex++;?>" <?
			?>onclick="this.form.preview_comment.value = 'VIEW';" />
		</div>

	</div>
</form>
<script type="text/javascript">
BX.ready(function(){
	window["UC"] = (!!window["UC"] ? window["UC"] : {});
	window["UC"]["l<?=$arParams["FORM_ID"]?>"] = new FTRList({
		id : <?=CUtil::PhpToJSObject(array_keys($arResult["MESSAGES"]))?>,
		form : BX('<?=$arParams["FORM_ID"]?>'),
		preorder : '<?=$arParams["PREORDER"]?>',
		pageNumber : <?=intval($arResult['PAGE_NUMBER']);?>,
		pageCount : <?=intval($arResult['PAGE_COUNT']);?>
	});
	window["UC"]["f<?=$arParams["FORM_ID"]?>"] = new FTRForm({
		form : BX('<?=$arParams["FORM_ID"]?>'),
		editorName : '<?=$arParams["jsObjName"]?>',
		editorId : '<?=$arParams["LheId"]?>'
	});
	<? if ($arParams['SHOW_MINIMIZED'] == "Y")
	{
	?>
	BX.addCustomEvent(this.form, 'onBeforeShow', function(obj) {
		var link = BX('sw<?=$arParams["FORM_ID"]?>');
		if (link) {
			link.innerHTML = BX.message('MINIMIZED_EXPAND_TEXT');
			BX.removeClass(BX.addClass(link.parentNode, "reviews-expanded"), "reviews-minimized");
		}
	});
	BX.addCustomEvent(this.form, 'onBeforeHide', function(obj) {
		var link = BX('sw<?=$arParams["FORM_ID"]?>');
		if (link) {
			link.innerHTML = BX.message('MINIMIZED_MINIMIZE_TEXT');
			BX.removeClass(BX.addClass(link.parentNode, "reviews-minimized"), "reviews-expanded");
		}
	});
	<?
	}
	?>
});
</script>
</div>
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->LoadScript(array(
		"formID" => CUtil::JSEscape($arParams["FORM_ID"]),
		"controlID" => "REVIEW_TEXT"
	));
?>