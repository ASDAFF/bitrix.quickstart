<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 if($arResult["MESSAGES"]){?>
<div class="b-detail-reviews">
        <h3 class="b-h3">Отзывы</h3>
        <?foreach($arResult["MESSAGES"] as $message){?>
        <div class="b-reviews">
                <div class="b-reviews__author"><?=$message["AUTHOR_NAME"]?>, <?=$message["POST_DATE"];?></div>
                <div class="b-reviews__message"><?=$message["POST_MESSAGE_TEXT"];?></div>
        </div>
        <?}?>
<?        
        
        if (strlen($arResult["NAV_STRING"]) > 0 && $arResult["NAV_RESULT"]->NavPageCount > 1):
?>
<div class="reviews-navigation-box reviews-navigation-bottom">
	<div class="reviews-page-navigation">
		<?=$arResult["NAV_STRING"]?>
	</div>
	<div class="reviews-clear-float"></div>
</div>
<?
endif; 
?>
</div>
<?} 
if (!empty($arResult["ERROR_MESSAGE"])):
?>
<div class="reviews-note-box reviews-note-error">
	<div class="reviews-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "reviews-note-error");?></div>
</div>
<?
endif;
?>
   
<form name="REPLIER<?=$arParams["form_index"]?>" id="REPLIER<?=$arParams["form_index"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
	?> method="POST" enctype="multipart/form-data" onsubmit="" class="reviews-form">
	<input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
	<input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
	<input type="hidden" name="SECTION_ID" value="<?=$arResult["ELEMENT_REAL"]["IBLOCK_SECTION_ID"]?>" />
	<input type="hidden" name="save_product_review" value="Y" />
	<input type="hidden" name="preview_comment" value="N" />
	<?=bitrix_sessid_post()?>
        <div class="b-detail-reviews_add">
                <h3 class="b-h3">Добавить отзыв</h3>
                    <?if(!$USER->isAuthorized()){?>
                <div class="b-reviews_add__title">Ваше имя:</div>
                <div class="b-reviews_add__field"><input type="text"  name="REVIEW_AUTHOR"  class="b-text"></div>
                <?}?>
                <div class="b-reviews_add__title">Комментарий:</div>
                <div class="b-reviews_add__field"><textarea class="b-text" rows="5" name="REVIEW_TEXT"></textarea></div>
                 
<?                
    if (strLen($arResult["CAPTCHA_CODE"]) > 0):
?> <div class="b-reviews_add__title"><?=GetMessage("F_CAPTCHA_PROMT")?>:</div>
 <img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" />
 
<div class="b-reviews_add__field"><input type="text"  name="captcha_word" autocomplete="off"  class="b-text"></div>

<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
 			
<?
endif;            
?>                
              
                
                <div class="b-reviews_add__title"><input type="submit" value="ДОБАВИТЬ" class="b-button"></div>
        </div>


</form>


 



<?
return;
echo "<pre>";        
var_dump($arResult); 
echo "</pre>";        
 
CUtil::InitJSCore(array('ajax', 'fx'));
// ************************* Input params***************************************************************
$arParams["SHOW_LINK_TO_FORUM"] = ($arParams["SHOW_LINK_TO_FORUM"] == "N" ? "N" : "Y");
$arParams["FILES_COUNT"] = intVal(intVal($arParams["FILES_COUNT"]) > 0 ? $arParams["FILES_COUNT"] : 1);
$arParams["IMAGE_SIZE"] = (intVal($arParams["IMAGE_SIZE"]) > 0 ? $arParams["IMAGE_SIZE"] : 100);
if (LANGUAGE_ID == 'ru'):
	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
	include($path);
endif;

// *************************/Input params***************************************************************
if (!empty($arResult["MESSAGES"])):
if ($arResult["NAV_RESULT"] && $arResult["NAV_RESULT"]->NavPageCount > 1):
?>
<div class="reviews-navigation-box reviews-navigation-top">
	<div class="reviews-page-navigation">
		<?=$arResult["NAV_STRING"]?>
	</div>
	<div class="reviews-clear-float"></div>
</div>
<?
endif;
 
if (strlen($arResult["NAV_STRING"]) > 0 && $arResult["NAV_RESULT"]->NavPageCount > 1):
?>
<div class="reviews-navigation-box reviews-navigation-bottom">
	<div class="reviews-page-navigation">
		<?=$arResult["NAV_STRING"]?>
	</div>
	<div class="reviews-clear-float"></div>
</div>
<?
endif;


if (!empty($arResult["read"]) && $arParams["SHOW_LINK_TO_FORUM"] != "N"):
?>
<div class="reviews-link-box">
	<div class="reviews-link-box-text">
		<a href="<?=$arResult["read"]?>"><?=GetMessage("F_C_GOTO_FORUM");?></a>
	</div>
</div>

<?
endif;

endif;

if (empty($arResult["ERROR_MESSAGE"]) && !empty($arResult["OK_MESSAGE"])):
?>
<div class="reviews-note-box reviews-note-note">
	<a name="reviewnote"></a>
	<div class="reviews-note-box-text"><?=ShowNote($arResult["OK_MESSAGE"]);?></div>
</div>
<?
endif;

if ($arResult["SHOW_POST_FORM"] != "Y"):
	return false;
endif;


if (!empty($arResult["MESSAGE_VIEW"])):
?>
<div class="reviews-preview">
<div class="reviews-header-box">
	<div class="reviews-header-title"><a name="postform"><span><?=GetMessage("F_PREVIEW")?></span></a></div>
</div>

<div class="reviews-info-box reviews-post-preview">
	<div class="reviews-info-box-inner">
		<div class="reviews-post-entry">
			<div class="reviews-post-text"><?=$arResult["MESSAGE_VIEW"]["POST_MESSAGE_TEXT"]?></div>
<?
		if (!empty($arResult["REVIEW_FILES"])):
?>
			<div class="reviews-post-attachments">
				<label><?=GetMessage("F_ATTACH_FILES")?></label>
<?
			foreach ($arResult["REVIEW_FILES"] as $arFile):
?>
				<div class="reviews-post-attachment"><?
				?><?$GLOBALS["APPLICATION"]->IncludeComponent(
					"bitrix:forum.interface", "show_file",
					Array(
						"FILE" => $arFile,
						"WIDTH" => $arResult["PARSER"]->image_params["width"],
						"HEIGHT" => $arResult["PARSER"]->image_params["height"],
						"CONVERT" => "N",
						"FAMILY" => "FORUM",
						"SINGLE" => "Y",
						"RETURN" => "N",
						"SHOW_LINK" => "Y"),
					null,
					array("HIDE_ICONS" => "Y"));
				?></div>
<?
			endforeach;
?>
			</div>
<?
		endif;
?>
		</div>
	</div>
</div>
<div class="reviews-br"></div>
</div>
<?
endif;
?>

<? if ($arParams['SHOW_MINIMIZED'] == "Y") { ?>
<div class="reviews-collapse reviews-minimized" style='position:relative; float:none;'>
	<a class="reviews-collapse-link" onclick="fToggleCommentsForm(this)" href="javascript:void(0);"><?=$arParams['MINIMIZED_EXPAND_TEXT']?></a>
</div>
<? } ?>

<div class="reviews-reply-form" <?=(($arParams['SHOW_MINIMIZED'] == "Y")?'style="display:none;"':'')?>>
<a name="review_anchor"></a>
<?
if (!empty($arResult["ERROR_MESSAGE"])):
?>
<div class="reviews-note-box reviews-note-error">
	<div class="reviews-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "reviews-note-error");?></div>
</div>
<?
endif;
?>

<form name="REPLIER<?=$arParams["form_index"]?>" id="REPLIER<?=$arParams["form_index"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
	?> method="POST" enctype="multipart/form-data" onsubmit="return ValidateForm(this, '<?=$arParams["AJAX_TYPE"]?>', '<?=$arParams["AJAX_POST"]?>', '<?=$arParams["PREORDER"]?>');"<?
	?> class="reviews-form">
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
			$arSmiles[] = array(
				'name' => $arSmile["NAME"],
				'path' => $arParams["PATH_TO_SMILE"].$arSmile["IMAGE"],
				'code' => array_shift(explode(" ", str_replace("\\\\","\\",$arSmile["TYPING"])))
			);
		}
	}

	CModule::IncludeModule("fileman");
	AddEventHandler("fileman", "OnIncludeLightEditorScript", "CustomizeLHEForForum");

	$LHE = new CLightHTMLEditor();

	$arEditorParams = array(
		'id' => "REVIEW_TEXT",
		'content' => isset($arResult["REVIEW_TEXT"]) ? $arResult["REVIEW_TEXT"] : "",
		'inputName' => "REVIEW_TEXT",
		'inputId' => "",
		'width' => "100%",
		'height' => "200px",
		'minHeight' => "200px",
		'bUseFileDialogs' => false,
		'bUseMedialib' => false,
		'BBCode' => false,
		'bBBParseImageSize' => true,
		'jsObjName' => "oLHE",
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
	$sFileSize = CFile::FormatSize(intVal(COption::GetOptionString("forum", "file_max_size", 50000)));
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
if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):
?>
			<div class="reviews-reply-field-setting">
				<input type="checkbox" name="REVIEW_USE_SMILES" id="REVIEW_USE_SMILES<?=$arParams["form_index"]?>" <?
				?>value="Y" <?=($arResult["REVIEW_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
				?>tabindex="<?=$tabIndex++;?>" /><?
			?>&nbsp;<label for="REVIEW_USE_SMILES<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_ALLOW_SMILES")?></label></div>
<?
endif;
/* SUBSCRIBE */
if ($arResult["SHOW_SUBSCRIBE"] == "Y"):
?>
			<div class="reviews-reply-field-setting">
				<input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
					?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" /><?
				?>&nbsp;<label for="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_SUBSCRIBE_TOPIC")?></label></div>
<?
	if ($arResult["FORUM_SUBSCRIBE"] == "Y"):
?>			<div class="reviews-reply-field-setting">
				<input type="checkbox" name="FORUM_SUBSCRIBE" id="FORUM_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
				?><?=($arResult["FORUM_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>"/><?
				?>&nbsp;<label for="FORUM_SUBSCRIBE<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_SUBSCRIBE_FORUM")?></label></div>
<?
	endif;
endif;
?>
		</div>
<?

?>
		<div class="reviews-reply-buttons">
			<input name="send_button" type="submit" value="<?=GetMessage("OPINIONS_SEND")?>" tabindex="<?=$tabIndex++;?>" <?
				?>onclick="this.form.preview_comment.value = 'N';" />
			<input name="view_button" type="submit" value="<?=GetMessage("OPINIONS_PREVIEW")?>" tabindex="<?=$tabIndex++;?>" <?
				?>onclick="this.form.preview_comment.value = 'VIEW';" />
		</div>

	</div>
</form>
</div>
<script type="text/javascript">

if (typeof oErrors != "object")
	var oErrors = {};
oErrors['no_topic_name'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_TOPIC_NAME"))?>";
oErrors['no_message'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_MESSAGE"))?>";
oErrors['max_len'] = "<?=CUtil::addslashes(GetMessage("JERROR_MAX_LEN"))?>";
oErrors['no_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_URL"))?>";
oErrors['no_title'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_TITLE"))?>";
oErrors['no_path'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_PATH_TO_VIDEO"))?>";
if (typeof oText != "object")
	var oText = {};
oText['author'] = " <?=CUtil::addslashes(GetMessage("JQOUTE_AUTHOR_WRITES"))?>:\n";
oText['enter_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL"))?>";
oText['enter_url_name'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL_NAME"))?>";
oText['enter_image'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_IMAGE"))?>";
oText['list_prompt'] = "<?=CUtil::addslashes(GetMessage("FORUM_LIST_PROMPT"))?>";
oText['video'] = "<?=CUtil::addslashes(GetMessage("FORUM_VIDEO"))?>";
oText['path'] = "<?=CUtil::addslashes(GetMessage("FORUM_PATH"))?>:";
oText['preview'] = "<?=CUtil::addslashes(GetMessage("FORUM_PREVIEW"))?>:";
oText['width'] = "<?=CUtil::addslashes(GetMessage("FORUM_WIDTH"))?>:";
oText['height'] = "<?=CUtil::addslashes(GetMessage("FORUM_HEIGHT"))?>:";
oText['cdm'] = '<?=CUtil::addslashes(GetMessage("F_DELETE_CONFIRM"))?>';
oText['show'] = '<?=CUtil::addslashes(GetMessage("F_SHOW"))?>';
oText['hide'] = '<?=CUtil::addslashes(GetMessage("F_HIDE"))?>';
oText['wait'] = '<?=CUtil::addslashes(GetMessage("F_WAIT"))?>';

oText['BUTTON_OK'] = "<?=CUtil::addslashes(GetMessage("FORUM_BUTTON_OK"))?>";
oText['BUTTON_CANCEL'] = "<?=CUtil::addslashes(GetMessage("FORUM_BUTTON_CANCEL"))?>";
oText['smile_hide'] = "<?=CUtil::addslashes(GetMessage("F_HIDE_SMILE"))?>";
oText['MINIMIZED_EXPAND_TEXT'] = "<?=CUtil::addslashes($arParams["MINIMIZED_EXPAND_TEXT"])?>";
oText['MINIMIZED_MINIMIZE_TEXT'] = "<?=CUtil::addslashes($arParams["MINIMIZED_MINIMIZE_TEXT"])?>";

if (typeof oForum != "object")
	var oForum = {};
oForum.page_number = <?=intval($arResult['PAGE_NUMBER']);?>;
oForum.page_count = <?=intval($arResult['PAGE_COUNT']);?>;

if (typeof oHelp != "object")
	var oHelp = {};
if (typeof phpVars != "object")
	var phpVars = {};
phpVars.bitrix_sessid = '<?=bitrix_sessid()?>';

function reviewsCtrlEnterHandler<?=CUtil::JSEscape($arParams["form_index"]);?>()
{
	if (window.oLHE)
		window.oLHE.SaveContent();
	var form = document.forms["REPLIER<?=CUtil::JSEscape($arParams["form_index"]);?>"];
	if (BX.fireEvent(form, 'submit'))
		form.submit();
}

function replyForumFormOpen()
{
<? if ($arParams['SHOW_MINIMIZED'] == "Y") { ?>
	var link = BX.findChild(document, {'class': 'reviews-collapse-link'}, true);
	if (link) fToggleCommentsForm(link, true);
<? } ?>
	return;
}

function fToggleCommentsForm(link, forceOpen)
{
	if (forceOpen == null) forceOpen = false;
	forceOpen = !!forceOpen;
	var form = BX.findChild(link.parentNode.parentNode, {'class':'reviews-reply-form'}, true);
	var bHidden = (form.style.display != 'block') || forceOpen;
	form.style.display = (bHidden ? 'block' : 'none');
	link.innerHTML = (bHidden ? oText['MINIMIZED_MINIMIZE_TEXT'] : oText['MINIMIZED_EXPAND_TEXT']);
	var classAdd = (bHidden ? 'reviews-expanded' : 'reviews-minimized');
	var classRemove = (bHidden ? 'reviews-minimized' : 'reviews-expanded');
	BX.removeClass(BX.addClass(link.parentNode, classAdd), classRemove);
	BX.scrollToNode(BX.findChild(form, {'attribute': { 'name' : 'send_button' }}, true));
	if (window.oLHE)
		setTimeout(function() {
				if (!BX.browser.IsIE())
					window.oLHE.SetFocusToEnd();
				else
					window.oLHE.SetFocus();
			}, 100);
}

function reply2author(name)
{
	name = name.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/&quot;/gi, "\"");
	replyForumFormOpen();

	if (window.oLHE)
	{
		var content = '';
		if (window.oLHE.sEditorMode == 'code')
			content = window.oLHE.GetCodeEditorContent();
		else
			content = window.oLHE.GetEditorContent();
<? if ($arResult["FORUM"]["ALLOW_BIU"] == "Y") { ?>
		content += "[B]"+name+"[/B]";
<? } ?>
		content += " \n";
		if (window.oLHE.sEditorMode == 'code')
			window.oLHE.SetContent(content);
		else
			window.oLHE.SetEditorContent(content);
		setTimeout(function() { window.oLHE.SetFocusToEnd();}, 300);
	}
	return false;
}

BX(function() {
	BX.addCustomEvent(window,  'LHE_OnInit', function(lightEditor)
	{
		BX.addCustomEvent(lightEditor, 'onShow', function() {
			BX.style(BX('bxlhe_frame_REVIEW_TEXT').parentNode, 'width', '100%');
		});
	});
});
</script>
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->LoadScript(array(
		"formID" => "REPLIER".CUtil::JSEscape($arParams["form_index"]),
		"controlID" => "REVIEW_TEXT"
	));
?>
<?=ForumAddDeferredScript($this->GetFolder().'/script_deferred.js')?>
