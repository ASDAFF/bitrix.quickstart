<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix vars
 *
 * @var array $arParams, $arResult
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 * @var CUser $USER
 */
 
 use Bitrix\Main\Localization\Loc;
 
$tabIndex = 1;
?><? if ($arParams['SHOW_MINIMIZED'] == "Y")
{
	?>
	<div class="reviews-collapse reviews-minimized" style='position:relative; float:none;'>
		<a class="btn btn2 reviews-collapse-link" id="sw<?=$arParams["FORM_ID"]?>" onclick="BX.onCustomEvent(BX('<?=$arParams["FORM_ID"]?>'), 'onTransverse', [event])" href="javascript:void(0);" title="<?=$arParams["MESSAGE_TITLE"]?>"><?=$arParams['MINIMIZED_EXPAND_TEXT']?></a>
	</div>
	<?
}
?>
<a name="review_anchor"></a>
<?
if (!empty($arResult["ERROR_MESSAGE"])):
	$arResult["ERROR_MESSAGE"] = preg_replace(array("/<br(.*?)><br(.*?)>/is", "/<br(.*?)>$/is"), array("<br />", ""), $arResult["ERROR_MESSAGE"]);
	?>
	<div class="reviews-note-box alert alert-danger">
		<?=ShowError($arResult["ERROR_MESSAGE"], "reviews-note-error");?>
	</div>
<?
endif;
?>
<div class="reviews-reply-form" <?=(($arParams['SHOW_MINIMIZED'] == "Y") ? 'style="display:none;"' : '' )?>>
<form name="<?=$arParams["FORM_ID"] ?>" id="<?=$arParams["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
?> method="POST" enctype="multipart/form-data" class="reviews-form rsform" data-fancybox-title="<?=$arParams['MINIMIZED_EXPAND_TEXT']?>">
<script type="text/javascript">
	BX.ready(function(){
		BX.Forum.Init({
			id : <?=CUtil::PhpToJSObject(array_keys($arResult["MESSAGES"]))?>,
			form : BX('<?=$arParams["FORM_ID"]?>'),
			preorder : '<?=$arParams["PREORDER"]?>',
			pageNumber : <?=intval($arResult['PAGE_NUMBER']);?>,
			pageCount : <?=intval($arResult['PAGE_COUNT']);?>,
			bVarsFromForm : '<?=$arParams["bVarsFromForm"]?>',
			ajaxPost : '<?=$arParams["AJAX_POST"]?>',
			lheId : 'REVIEW_TEXT'
		});
		<? if ($arParams['SHOW_MINIMIZED'] == "Y")
		{
		?>
		BX.addCustomEvent(BX('<?=$arParams["FORM_ID"]?>'), 'onBeforeHide', function() {
			var link = BX('sw<?=$arParams["FORM_ID"]?>');
			if (link) {
				link.innerHTML = BX.message('MINIMIZED_EXPAND_TEXT');
				BX.removeClass(BX.addClass(link.parentNode, "reviews-expanded"), "reviews-minimized");
			}
		});
		BX.addCustomEvent(BX('<?=$arParams["FORM_ID"]?>'), 'onBeforeShow', function() {
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
	<input type="hidden" name="index" value="<?=htmlspecialcharsbx($arParams["form_index"])?>" />
	<input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
	<input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
	<input type="hidden" name="SECTION_ID" value="<?=$arResult["ELEMENT_REAL"]["IBLOCK_SECTION_ID"]?>" />
	<input type="hidden" name="save_product_review" value="Y" />
	<input type="hidden" name="preview_comment" value="N" />
	<input type="hidden" name="AJAX_POST" value="<?=$arParams["AJAX_POST"]?>" />
	<?=bitrix_sessid_post()?>
	<?
	if ($arParams['AUTOSAVE'])
		$arParams['AUTOSAVE']->Init();
	?>
	<div>
        <?/*
		<div class="reviews-reply-header"><span><?=$arParams["MESSAGE_TITLE"]?></span><span class="reviews-required-field">*</span></div>
        */?>
      
        <div class="form-group">
            <span><?=GetMessage('RS.FLYAWAY.REVIEW_RATING')?>:</span>
            <span class="rate js-stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="rate__icon icon-star icon-svg" data-index=<?=$i?>><use xlink:href="#svg-star"></use></svg>
                <?php endfor; ?>
                <input type="hidden" name="REVIEW_RATE" value="5">
            </span>
        </div>
		<?
		/* GUEST PANEL */
		if (!$arResult["IS_AUTHORIZED"]):
			?>
            <div class="form-group">
                <input class="form-control" id="REVIEW_AUTHOR<?=$arParams['form_index']?>" name="REVIEW_AUTHOR" type="text" value="<?=$arResult['REVIEW_AUTHOR']?>" tabindex="<?=$tabIndex++;?>" placeholder="<?=GetMessage('OPINIONS_NAME')?>*">
            </div>
            <div class="form-group">
                <input class="form-control" type="text" name="REVIEW_EMAIL" id="REVIEW_EMAIL<?=$arParams["form_index"]?>" size="30" value="<?=$arResult["REVIEW_EMAIL"]?>" tabindex="<?=$tabIndex++;?>" placeholder="<?=GetMessage('OPINIONS_EMAIL')?>*">
            </div>
		<?
		endif;
		?>
        <div class="form-group">
            <textarea class="form-control" name="REVIEW_TEXT_plus" tabindex="<?=$tabIndex++;?>" placeholder="<?=GetMessage('POST_MSG_TEXT_PLUS')?>"><?=(isset($arResult['REVIEW_TEXT_EXT']['PLUS']) ? $arResult['REVIEW_TEXT_EXT']['PLUS'] : '')?></textarea>
        </div>

        <div class="form-group">
            <textarea class="form-control" name="REVIEW_TEXT_minus" tabindex="<?=$tabIndex++;?>" placeholder="<?=GetMessage('POST_MSG_TEXT_MINUS')?>"><?=(isset($arResult['REVIEW_TEXT_EXT']['MINUS']) ? $arResult['REVIEW_TEXT_EXT']['MINUS'] : '')?></textarea>
        </div>

        <div class="form-group">
            <textarea class="form-control" name="REVIEW_TEXT_comment" tabindex="<?=$tabIndex++;?>" placeholder="<?=GetMessage('POST_MSG_TEXT_COMMENT')?>"><?=(isset($arResult['REVIEW_TEXT_EXT']['COMMENT']) ? $arResult['REVIEW_TEXT_EXT']['COMMENT'] : '')?></textarea>
        </div>

        <textarea style="display:none;" name="REVIEW_TEXT"><?=(isset($arResult['REVIEW_TEXT']) ? $arResult['REVIEW_TEXT'] : '')?></textarea>
        <?php
        $frame = $this->createFrame()->begin("");

        /* CAPTHCA */
        if (strLen($arResult["CAPTCHA_CODE"]) > 0):
			?>
            <div class="form-group clearfix">
                <input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>">
                <img class="captcha-img pull-right" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" />
                <div class="l-overflow">
                    <input class="form-control" type="text" size="30" name="captcha_word" tabindex="<?=$tabIndex++;?>" autocomplete="off" placeholder="<?=Loc::getMessage('F_CAPTCHA_PROMT');?>">
                </div>
            </div>
		<?
        endif;

        $frame->end();

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
							<input name="FILE_NEW_<?=$ii?>" type="file" value="" size="30" />
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
		<div class="reviews-reply-field reviews-reply-field-settings">
			<?
			/* SMILES */
            /*
			if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):
				?>
				<div class="reviews-reply-field-setting">
					<input type="checkbox" name="REVIEW_USE_SMILES" id="REVIEW_USE_SMILES<?=$arParams["form_index"]?>" <?
					?>value="Y" <?=($arResult["REVIEW_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
					?>tabindex="<?=$tabIndex++;?>" /><?
					?>&nbsp;<label for="REVIEW_USE_SMILES<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_ALLOW_SMILES")?></label></div>
			<?
			endif;
            */
			/* SUBSCRIBE */
			if ($arResult["SHOW_SUBSCRIBE"] == "Y"):
				?>
				<div class="reviews-reply-field-setting checkbox">
                    <label for="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>">
                        <input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
                        ?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" />
                        <svg class="checkbox__icon icon-check icon-svg"><use xlink:href="#svg-check"></use></svg>&nbsp;<?=GetMessage("F_WANT_SUBSCRIBE_TOPIC")?>
                    </label>
                </div>
			<?
			endif;
			?>
		</div>
		<?

		?>
		<div class="reviews-reply-buttons">
			<input class="btn btn2 form-control" name="send_button" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" tabindex="<?=$tabIndex++;?>" <?
			?>onclick="this.form.preview_comment.value = 'N';" />
            <?/*
			<input class="btn btn1 form-control" name="view_button" type="submit" value="<?=GetMessage("OPINIONS_PREVIEW")?>" tabindex="<?=$tabIndex++;?>" <?
			?>onclick="this.form.preview_comment.value = 'VIEW';" />
            */?>
		</div>

	</div>
</form>
</div>
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->LoadScript(array(
		"formID" => CUtil::JSEscape($arParams["FORM_ID"]),
		"controlID" => "REVIEW_TEXT"
	));
?>