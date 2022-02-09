<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*******************************************************************/
if (!$this->__component->__parent || empty($this->__component->__parent->__name)):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/themes/gray/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/styles/additional.css');
endif;
$arUserSettings = array("smiles" => "hide");
if ($GLOBALS["USER"]->IsAuthorized())
{
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".strToLower($GLOBALS["DB"]->type)."/favorites.php");
	$arUserSettings = @unserialize(CUserOptions::GetOption("forum", "default_template", ""));
}

/********************************************************************
				Input params
********************************************************************/
/***************** BASE ********************************************/
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] != "N" ? "Y" : "N");
$arParams["FILES_COUNT"] = intVal(intVal($arParams["FILES_COUNT"]) > 0 ? $arParams["FILES_COUNT"] : 5);
$arParams["IMAGE_SIZE"] = (intVal($arParams["IMAGE_SIZE"]) > 0 ? $arParams["IMAGE_SIZE"] : 100);
$arParams["SMILES_COUNT"] = (intVal($arParams["SMILES_COUNT"]) > 0 ? intVal($arParams["SMILES_COUNT"]) : 0);
$arParams["VOTE_COUNT_QUESTIONS"] = (intVal($arParams["VOTE_COUNT_QUESTIONS"]) > 0 ? intVal($arParams["VOTE_COUNT_QUESTIONS"]) : 10);
$arParams["VOTE_COUNT_ANSWERS"] = (intVal($arParams["VOTE_COUNT_ANSWERS"]) > 0 ? intVal($arParams["VOTE_COUNT_ANSWERS"]) : 20);
$arParams["form_index"] = $_REQUEST["INDEX"];
if (!empty($arParams["form_index"]))
	$arParams["form_index"] = preg_replace("/[^a-z0-9]/is", "_", $arParams["form_index"]);
$tabIndex = 10;
/*******************************************************************/
if (LANGUAGE_ID == 'ru')
{
	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
	@include_once($path);
}
/********************************************************************
				/Input params
********************************************************************/
?>
<a name="postform"></a>
<div class="forum-header-box">
<?/*?>	<div class="forum-header-options">
		<span class="forum-option-bbcode"><a href="<?=$arResult["URL"]["HELP"]?>#bbcode">BBCode</a></span>&nbsp;&nbsp;
		<span class="forum-option-rules"><a href="<?=$arResult["URL"]["RULES"]?>"><?=GetMessage("F_RULES")?></a></span>
	</div>
<?*/?>
	<div class="forum-header-title"><span><?
if ($arParams["MESSAGE_TYPE"] == "NEW"):
	?><?=GetMessage("F_CREATE_FORM")?><?
elseif ($arParams["MESSAGE_TYPE"] == "REPLY"):
	?><?=GetMessage("F_REPLY_FORM")?><?
else:
	?><?=GetMessage("F_EDIT_FORM")?>
		<a href="<?=$arResult["URL"]["READ"]?>"><?=htmlspecialcharsEx($arResult["TOPIC_FILTER"]["TITLE"])?></a>
	<?
endif;	
	?></span></div>
</div>

<div class="forum-reply-form">
<?
if (!empty($arResult["ERROR_MESSAGE"])): 
?>
<div class="forum-note-box forum-note-error">
	<div class="forum-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "forum-note-error");?></div>
</div>
<?
endif;
?>

<form name="REPLIER<?=$arParams["form_index"]?>" id="REPLIER<?=$arParams["form_index"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
	?> method="post" enctype="multipart/form-data" onsubmit="return ValidateForm(this, '<?=$arParams["AJAX_TYPE"]?>', '<?=$arParams["AJAX_POST"]?>');"<?
	?> class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="<?=$arParams["PAGE_NAME"];?>" />
	<input type="hidden" name="FID" value="<?=$arParams["FID"]?>" />
	<input type="hidden" name="TID" value="<?=$arParams["TID"]?>" />
	<input type="hidden" name="MID" value="<?=$arParams["MID"];?>" />
	<input type="hidden" name="MESSAGE_TYPE" value="<?=$arParams["MESSAGE_TYPE"];?>" />
	<input type="hidden" name="AUTHOR_ID" value="<?=$arResult["DATA"]["AUTHOR_ID"];?>" />
	<input type="hidden" name="forum_post_action" value="save" />
	<input type="hidden" name="MESSAGE_MODE" value="NORMAL" />
	<?=bitrix_sessid_post()?>
	<? if ($arParams['AUTOSAVE']) $arParams['AUTOSAVE']->Init(); ?>
	<?
	if (($arResult["SHOW_PANEL"]["TOPIC"] == "Y" || $arResult["SHOW_PANEL"]["GUEST"] == "Y") && $arParams["AJAX_CALL"] == "N")
	{
	?>
		<div class="forum-reply-fields">
			<?
			/* NEW TOPIC */
			if ($arResult["SHOW_PANEL"]["TOPIC"] == "Y")
			{
			?>
				<div class="forum-reply-field forum-reply-field-title">
					<label for="TITLE<?=$arParams["form_index"]?>"><?=GetMessage("F_TOPIC_NAME")?><span class="forum-required-field">*</span></label>
					<input name="TITLE" id="TITLE<?=$arParams["form_index"]?>" type="text" value="<?=$arResult["DATA"]["TITLE"];?>" tabindex="<?=$tabIndex++;?>" size="70" /></div>
				<div class="forum-reply-field forum-reply-field-desc">
					<label for="DESCRIPTION<?=$arParams["form_index"]?>"><?=GetMessage("F_TOPIC_DESCR")?></label>
					<input name="DESCRIPTION" id="DESCRIPTION<?=$arParams["form_index"]?>" type="text" value="<?=$arResult["DATA"]["DESCRIPTION"];?>" <?
						?>tabindex="<?=$tabIndex++;?>" size="70"/></div>
			<?
			}
			/* GUEST PANEL */
			if ($arResult["SHOW_PANEL"]["GUEST"] == "Y")
			{
			?>
				<div class="forum-reply-field-user">
					<div class="forum-reply-field forum-reply-field-author"><label for="AUTHOR_NAME<?=$arParams["form_index"]?>"><?=GetMessage("F_TYPE_NAME")?><?
						?><span class="forum-required-field">*</span></label>
						<span><input name="AUTHOR_NAME" id="AUTHOR_NAME<?=$arParams["form_index"]?>" size="30" type="text" value="<?=$arResult["DATA"]["AUTHOR_NAME"]?>"<?
							?> tabindex="<?=$tabIndex++;?>" /></span></div>
			<?		
					if ($arResult["FORUM"]["ASK_GUEST_EMAIL"] == "Y")
					{
			?>
						<div class="forum-reply-field-user-sep">&nbsp;</div>
						<div class="forum-reply-field forum-reply-field-email"><label for="AUTHOR_EMAIL<?=$arParams["form_index"]?>"><?=GetMessage("F_TYPE_EMAIL")?><?
							?><span class="forum-required-field">*</span></label>
							<span><input type="text" name="AUTHOR_EMAIL" id="AUTHOR_EMAIL<?=$arParams["form_index"]?>" size="30" value="<?=$arResult["DATA"]["AUTHOR_EMAIL"];?>"<?
								?> tabindex="<?=$tabIndex++;?>" /></span></div>
			<?
					}
			?>
					<div class="forum-clear-float"></div>
				</div>
			<?
			}

			if ($arResult["SHOW_PANEL"]["TOPIC"] == "Y" && (($arParams["SHOW_TAGS"] == "Y") || ($arResult["SHOW_PANEL"]["VOTE"] == "Y" && empty($arResult["QUESTIONS"]))))
			{
			//if ($arResult["SHOW_PANEL"]["TOPIC"] == "Y" && $arParams["SHOW_TAGS"] == "Y"):
				$iIndex = $tabIndex++;
			?>
				<div class="forum-reply-field forum-reply-field-tags" <?
				if (!empty($arResult["DATA"]["TAGS"]))
				{
							?> style="display:block; "<?
				}
				?>>
					<label for="TAGS"><?=GetMessage("F_TOPIC_TAGS")?></label>
					<?
					if ($arResult["SHOW_SEARCH"] == "Y")
					{
						$APPLICATION->IncludeComponent(
							"bitrix:search.tags.input", 
							"", 
							(
								array(
									"VALUE" => $arResult["DATA"]["~TAGS"], 
									"NAME" => "TAGS",
									"TEXT" => 'tabindex="'.$iIndex.'" size="70" onmouseover="CorrectTags(this)"', 
									"TMPL_IFRAME" => "N") + 
								(
									$arParams["MODE"] == "GROUP" 
									? 
									array(
										"arrFILTER" => "socialnetwork", 
										"arrFILTER_socialnetwork" => $arParams["SOCNET_GROUP_ID"]
									) 
									: 
									array(
										"arrFILTER" => "socialnetwork_user",
										"arrFILTER_socialnetwork_user" => $arParams["USER_ID"]
									) 
								)
							),
							$component,
							array("HIDE_ICONS" => "Y"));
						
						?><iframe id="TAGS_div_frame" name="TAGS_div_frame" src="javascript:void(0)" style="display:none;"/></iframe><?
					}
					else
					{
						?><input name="TAGS" type="text" value="<?=$arResult["DATA"]["TAGS"]?>" tabindex="<?=$iIndex?>" size="70" /><?
					}
					?>
				</div><?
				?><br style="clear:left;" /><div class="forum-reply-field forum-reply-field-addtags" style="float:left; margin-bottom:7px;" <?
					if (!empty($arResult["DATA"]["TAGS"]))
					{
							?> style="display:none; "<?
					}
				?>><?
					?><a href="javascript:void(0);" style="margin-right:10px;" onclick="AddTags(this); return false;" onfocus="AddTags(this);" tabindex="<?=$iIndex?>"><?
						?><?=GetMessage("F_TOPIC_TAGS_DESCRIPTION")?><?
					?></a><?
				?></div>
			<?
			}

			if ($arResult["SHOW_PANEL"]["TOPIC"] == "Y" && $arResult["SHOW_PANEL"]["VOTE"] == "Y" && empty($arResult["QUESTIONS"]))
			{
				?><div class="forum-reply-field forum-reply-field-vote" id="vote_switcher" style="float: left;margin-bottom:7px;">
					<a href="#" onclick="return ShowVote(this);"><?=GetMessage("F_ADD_VOTE")?></a>
				</div><?	
			}
			if ($arResult["SHOW_PANEL"]["TOPIC"] == "Y" && $arResult["SHOW_PANEL"]["VOTE"] == "Y")
			{
				$iCountQuestions = count($arResult["QUESTIONS"]);
				$iCountQuestions = ($iCountQuestions > 0 ? $iCountQuestions : 1);
				?>
				<script>
				var arVoteParams = {
					'count_q': <?=$iCountQuestions?>, 
					'coun_max_q': <?=($arParams["VOTE_COUNT_QUESTIONS"])?>, 
					'count_max_a': <?=($arParams["VOTE_COUNT_ANSWERS"])?>, 
					'template_answer' : ('<input type="text" name="ANSWER[#Q#][#A#]" value="" />' + 
								'<label>[<a onclick=\'return vote_remove_answer(this, "#Q#", "Y")\' title="<?=CUtil::JSEscape(GetMessage("F_VOTE_DROP_ANSWER"))?>" href="#">X</a>]</label>'), 
					'template_question' : ('<div class="forum-reply-field-vote-question-title">' + 
							'<input type="text" name="QUESTION[#Q#]" name="QUESTION_#Q#" value="" />' + 
							'<label>[<a onclick=\'return vote_remove_question(this, "Y")\' title="<?=CUtil::JSEscape(GetMessage("F_VOTE_DROP_QUESTION"))?>" href="#">X</a>]</label>' + 
						'</div>' + 
						'<div class="forum-reply-field-vote-question-options">' + 
							'<input type="checkbox" value="Y" name="MULTI[#Q#]" id="MULTI_#Q#" class="checkbox" />' + 
							'<label for="MULTI_#Q#"><?=CUtil::JSEscape(GetMessage("F_VOTE_MULTI"))?></label>' + 
						'</div>' + 
						'<ol class="forum-reply-field-vote-answers">' + 
							'<li><input type="text" name="ANSWER[#Q#][1]" value="" />' + 
								'<label>[<a onclick=\'return vote_remove_answer(this, "#Q#", "Y")\' title="<?=CUtil::JSEscape(GetMessage("F_VOTE_DROP_ANSWER"))?>" href="#">X</a>]</label></li>' + 
							'<li>[<a onclick=\'return vote_add_answer(this.parentNode, #Q#, 1)\' href="#"><?=CUtil::JSEscape(GetMessage("F_VOTE_ADD_ANSWER"))?></a>]</li>' + 
						'</ol>')};
				</script>
				<div id="vote_params" <?
				if (empty($arResult["QUESTIONS"])) {
					?>style="display:none;"<?
				}?>>
					<div class="forum-reply-header" style="clear:left;"><?=GetMessage("F_VOTE")?></div>
					<div class="forum-reply-fields">
						<div class="forum-reply-field forum-reply-field-vote">
							<label><?=GetMessage('VOTE_DURATION')?></label>
							<?
							$APPLICATION->IncludeComponent(
								"bitrix:main.calendar",
								"",
								array(
									"SHOW_INPUT"=>"Y",
									"SHOW_TIME"=>"N",
									"INPUT_NAME"=>"DATE_END",
									"INPUT_VALUE"=>$arResult['DATE_END'],
									"FORM_NAME"=>"REPLIER".$arParams["form_index"],
								),
								$component,
								array("HIDE_ICONS"=>true)
							);?>
							<br /><br />
							<?
							$qq = 0;
							foreach ($arResult["QUESTIONS"] as $arQuestion)
							{
								if ($arQuestion["DEL"] == "Y")
								{
									?><input type="hidden" name="QUESTION_ID[<?=$qq?>]" value="<?=$arQuestion["ID"]?>" /><?
									?><input type="hidden" name="QUESTION[<?=$qq?>]" value="<?=$arQuestion["QUESTION"]?>" /><?
									?><input type="hidden" name="QUESTION_DEL[<?=$qq?>]" value="Y" /><?
									continue;
								}
								$qq++;
								?>
								<div class="forum-reply-field-vote-question">
									<div class="forum-reply-field-vote-question-title">
										<input type="hidden" name="QUESTION_ID[<?=$qq?>]" value="<?=$arQuestion["ID"]?>" /><?
										?><input type="text" name="QUESTION[<?=$qq?>]" value="<?=$arQuestion["QUESTION"]?>" /><?
										?><input type="hidden" name="QUESTION_DEL[<?=$qq?>]" value="N" /><?
										?><label>[<a onclick='return vote_remove_question(this);' title="<?=GetMessage("F_VOTE_DROP_QUESTION")?>" href="#">X</a>]</label>
									</div>
									<div class="forum-reply-field-vote-question-options">
										<input type="checkbox" value="Y" name="MULTI[<?=$qq?>]" id="MULTI_<?=$qq?>" class="checkbox" <?
											?><?=($arQuestion["MULTI"] == "Y" ? "checked='checked'" : "")?> />
										<label for="MULTI_<?=$qq?>"><?=GetMessage("F_VOTE_MULTI")?></label>
									</div><?
									?><ol class="forum-reply-field-vote-answers"><?
										$aa = 0;
										foreach ($arQuestion["ANSWERS"] as $aa => $arAnswer)
										{
											if ($arAnswer["DEL"] == "Y")
											{
												?><input type="hidden" name="ANSWER_ID[<?=$qq?>][<?=$aa?>]" value="<?=$arAnswer["ID"]?>" /><?
												?><input type="text" name="ANSWER[<?=$qq?>][<?=$aa?>]" value="<?=$arAnswer["MESSAGE"]?>" /><?
												?><input type="hidden" name="ANSWER_DEL[<?=$qq?>][<?=$aa?>]" value="Y" /><?
												continue;
											}
										
											$aa++;
											?><li><input type="hidden" name="ANSWER_ID[<?=$qq?>][<?=$aa?>]" value="<?=$arAnswer["ID"]?>" /><?
												?><input type="text" name="ANSWER[<?=$qq?>][<?=$aa?>]" value="<?=$arAnswer["MESSAGE"]?>" /><?
												?><input type="hidden" name="ANSWER_DEL[<?=$qq?>][<?=$aa?>]" value="N" /><?
												?><label>[<a onclick='return vote_remove_answer(this, "<?=$qq?>", "N");' title="<?=GetMessage("F_VOTE_DROP_ANSWER")?>" href="#">X</a>]</label>
											</li><?
										}
										?><li <?=($aa >= $arParams["VOTE_COUNT_ANSWERS"] ? "style='display:none;'" : "")?>><?
											?>[<a onclick='return vote_add_answer(this.parentNode, "<?=$qq?>", "<?=$aa?>");' href="#"><?=GetMessage("F_VOTE_ADD_ANSWER")?></a>]</li><?
									?></ol>
								</div><?
							}
							if (empty($arResult["QUESTIONS"]))
							{
								$qq++;
								?><div class="forum-reply-field-vote-question">
									<div class="forum-reply-field-vote-question-title"><?
										?><input type="text" name="QUESTION[1]" name="QUESTION_1" value="" /><?
										?><label>[<a onclick='return vote_remove_question(this, "Y")' title="<?=GetMessage("F_VOTE_DROP_QUESTION")?>" href="#">X</a>]</label><?
										?>
									</div>
									<div class="forum-reply-field-vote-question-options">
										<input type="checkbox" value="Y" name="MULTI[1]" id="MULTI_1" class="checkbox" />
										<label for="MULTI_1"><?=GetMessage("F_VOTE_MULTI")?></label>
									</div><?
									?><ol class="forum-reply-field-vote-answers"><?
										?><li><input type="text" name="ANSWER[1][1]" value="" /><?
										?><label>[<a onclick='return vote_remove_answer(this, "1", "Y")' title="<?=GetMessage("F_VOTE_DROP_ANSWER")?>" href="#">X</a>]</label></li><?
										?><li>[<a onclick='return vote_add_answer(this.parentNode, 1, 1)' href="#"><?=GetMessage("F_VOTE_ADD_ANSWER")?></a>]</li><?
									?></ol>
								</div><?
							}
							?>
							<div class="forum-reply-field-vote-question" id="vote_question_add" <?=($qq >= $arParams["VOTE_COUNT_QUESTIONS"] ? "style='display:none;'" : "")?>>
								<a onclick="return vote_add_question('<?=$qq?>', this.parentNode);" href="#"><?=GetMessage("F_VOTE_ADD_QUESTION")?></a>
							</div>
						</div>
					</div>
				</div>
				<?
			}
			?>
		</div>
	<?
	}
	?>
	<div class="forum-reply-header" style="clear:left;"><span><?=GetMessage("F_MESSAGE_TEXT")?></span><span class="forum-required-field">*</span></div>
	<div class="forum-reply-fields">
		<div class="forum-reply-field forum-reply-field-text">
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
					'id' => "POST_MESSAGE",
					'content' => isset($arResult['DATA']["~POST_MESSAGE"]) ? $arResult['DATA']["~POST_MESSAGE"] : "",
					'inputName' => "POST_MESSAGE",
					'inputId' => "",
					'width' => "100%",
					'height' => "200px",
					'minHeight' => "200px",
					'bUseFileDialogs' => false,
					'bUseMedialib' => false,
					'BBCode' => true,
					'bBBParseImageSize' => true,
					'jsObjName' => "oLHE",
					'toolbarConfig' => array(),
					'smileCountInToolbar' => 3,
					'arSmiles' => $arSmiles,
					'bQuoteFromSelection' => true,
					'ctrlEnterHandler' => 'postformCtrlEnterHandler'.$arParams["form_index"],
					'bSetDefaultCodeView' => ($arParams['EDITOR_CODE_DEFAULT'] == 'Y'),
					'bResizable' => true,
					'bAutoResize' => true
				);

				$arEditorParams['toolbarConfig'] = forumTextParser::GetEditorToolbar(array('forum' => $arResult['FORUM']));
				$LHE->Show($arEditorParams);
			?>
		</div>
<?
/* EDIT PANEL */

if ($arResult["SHOW_PANEL"]["EDIT_INFO"] == "Y"):
?>
		<div class="forum-reply-field forum-reply-field-lastedit">
<?
	$checked = true;
	if ($arResult["SHOW_PANEL_EDIT_ASK"] == "Y"):
		$checked = ($_REQUEST["EDIT_ADD_REASON"]=="Y" ? true : false);
		?><div class="forum-reply-field-lastedit-view"><?
			?><input type="checkbox" id="EDIT_ADD_REASON" name="EDIT_ADD_REASON<?=$arParams["form_index"]?>" <?=($checked ? "checked=\"checked\"" : "")?> value="Y" <?
				?>onclick="ShowLastEditReason(this.checked, this.parentNode.nextSibling)" />&nbsp;<?
			?><label for="EDIT_ADD_REASON<?=$arParams["form_index"]?>"><?=GetMessage("F_EDIT_ADD_REASON")?></label></div><?
	endif;
	
		?><div class="forum-reply-field-lastedit-reason" <?
		if (!$checked):
			?> style="display:none;" <?	
		endif;
		?>  id=""><?
		if ($arResult["SHOW_EDIT_PANEL_GUEST"] == "Y"):
			?><input name="EDITOR_NAME" type="hidden" value="<?=$arResult["EDITOR_NAME"];?>" /><?
			if ($arResult["FORUM"]["ASK_GUEST_EMAIL"] == "Y"):
			?><input type="hidden" name="EDITOR_EMAIL" value="<?=$arResult["EDITOR_EMAIL"];?>" /></br><?
			endif;
		endif;
		?>
			<label for="EDIT_REASON"><?=GetMessage("F_EDIT_REASON")?></label>
			<input type="text" name="EDIT_REASON" id="" size="70" value="<?=$arResult["EDIT_REASON"]?>" /></div>
		</div>
<?
endif;

/* CAPTHCA */
if (strLen($arResult["DATA"]["CAPTCHA_CODE"]) > 0):
?>
		<div class="forum-reply-field forum-reply-field-captcha">
			<input type="hidden" name="captcha_code" value="<?=$arResult["DATA"]["CAPTCHA_CODE"]?>"/>
			<div class="forum-reply-field-captcha-label">
				<label for="captcha_word"><?=GetMessage("F_CAPTCHA_PROMT")?><span class="forum-required-field">*</span></label>
				<input type="text" size="30" name="captcha_word" tabindex="<?=$tabIndex++;?>" autocomplete="off" />
			</div>
			<div class="forum-reply-field-captcha-image">
				<img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["DATA"]["CAPTCHA_CODE"]?>" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" />
			</div>
		</div>
<?
endif;

/* ATTACH FILES */
if ($arResult["SHOW_PANEL"]["ATTACH"] == "Y"):
?>
		<div class="forum-reply-field forum-reply-field-upload">
<?
$iCount = 0;
if (!empty($arResult["DATA"]["FILES"])):
	foreach ($arResult["DATA"]["FILES"] as $key => $val):
	$iCount++;
	$sFileSize = CFile::FormatSize(intVal($val["FILE_SIZE"]));
?>
			<div class="forum-uploaded-file">
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
			<div class="forum-upload-info" style="display:none;" id="upload_files_info_<?=$arParams["form_index"]?>">
<?
if ($arResult["FORUM"]["ALLOW_UPLOAD"] == "F"):
?>
				<span><?=str_replace("#EXTENSION#", $arResult["FORUM"]["ALLOW_UPLOAD_EXT"], GetMessage("F_FILE_EXTENSION"))?></span>
<?
endif;
?>
				<span><?=str_replace("#SIZE#", $sFileSize, GetMessage("F_FILE_SIZE"))?></span>
			</div>
<?
			
	for ($ii = $iCount; $ii < $arParams["FILES_COUNT"]; $ii++):
?>

			<div class="forum-upload-file" style="display:none;" id="upload_files_<?=$ii?>_<?=$arParams["form_index"]?>">
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
		<div class="forum-reply-field forum-reply-field-settings">
<?
/* SMILES */
if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y")
{
?>
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="USE_SMILES" id="USE_SMILES<?=$arParams["form_index"]?>" <?
				?>value="Y" <?=($arResult["DATA"]["USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
				?>tabindex="<?=$tabIndex++;?>" /><?
			?>&nbsp;<label for="USE_SMILES<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_ALLOW_SMILES")?></label></div>
<?
};
/* SUBSCRIBE */
if ($arResult["SHOW_SUBSCRIBE"] == "Y")
{
?>
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
					?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" /><?
				?>&nbsp;<label for="TOPIC_SUBSCRIBE<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_SUBSCRIBE_TOPIC")?></label></div>
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="FORUM_SUBSCRIBE" id="FORUM_SUBSCRIBE<?=$arParams["form_index"]?>" value="Y" <?
				?><?=($arResult["FORUM_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>"/><?
				?>&nbsp;<label for="FORUM_SUBSCRIBE<?=$arParams["form_index"]?>"><?=GetMessage("F_WANT_SUBSCRIBE_FORUM")?></label></div>
<?
};
?>
		</div>
<?

?>
		<div class="forum-reply-buttons">
			<input name="send_button" type="submit" value="<?=$arResult["SUBMIT"]?>" tabindex="<?=$tabIndex++;?>" <?
				?>onclick="this.form.MESSAGE_MODE.value = 'NORMAL';" />
			<input name="view_button" type="submit" value="<?=GetMessage("F_VIEW")?>" tabindex="<?=$tabIndex++;?>" <?
				?>onclick="this.form.MESSAGE_MODE.value = 'VIEW';" />
		</div>

	</div>
</div>
</form>
<script type="text/javascript">

var bSendForm = false;
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
oText['vote_drop_answer_confirm'] = "<?=CUtil::addslashes(GetMessage("F_VOTE_DROP_ANSWER_CONFIRM"))?>";
oText['vote_drop_question_confirm'] = "<?=CUtil::addslashes(GetMessage("F_VOTE_DROP_QUESTION_CONFIRM"))?>";

oText['BUTTON_OK'] = "<?=CUtil::addslashes(GetMessage("FORUM_BUTTON_OK"))?>";
oText['BUTTON_CANCEL'] = "<?=CUtil::addslashes(GetMessage("FORUM_BUTTON_CANCEL"))?>";
oText['smile_hide'] = "<?=CUtil::addslashes(GetMessage("F_HIDE_SMILE"))?>";

if (typeof oHelp != "object")
	var oHelp = {};

function postformCtrlEnterHandler<?=CUtil::JSEscape($arParams["form_index"]);?>()
{
	if (window.oLHE)
		window.oLHE.SaveContent();
	var formid = "REPLIER<?=CUtil::JSEscape($arParams["form_index"]);?>";
	if (document.forms[formid].onsubmit()) {document.forms[formid].submit();}
}

BX( function() {
	BX.addCustomEvent(window,  'LHE_OnInit', function(lightEditor)
	{
		BX.addCustomEvent(lightEditor, 'onShow', function() {
			BX.style(BX('bxlhe_frame_POST_MESSAGE').parentNode, 'width', '100%');
		});
	});
});
</script>
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->LoadScript("REPLIER".CUtil::JSEscape($arParams["form_index"]));
?>
