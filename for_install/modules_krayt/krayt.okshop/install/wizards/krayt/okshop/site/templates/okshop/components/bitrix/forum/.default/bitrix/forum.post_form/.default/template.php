<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$postMessageTabIndex = $tabIndex = $arParams["tabIndex"];
$fileControlId = 'forumfiles'.$arParams["FORUM"]["ID"];
?>
<script type="text/javascript">
var bSendForm = false;
BX.message({
	'no_topic_name' : '<?=GetMessageJS("JERROR_NO_TOPIC_NAME")?>',
	'no_message' : '<?=GetMessageJS("JERROR_NO_MESSAGE")?>',
	'max_len' : '<?=GetMessageJS("JERROR_MAX_LEN")?>',
	'author' : ' <?=GetMessageJS("JQOUTE_AUTHOR_WRITES")?>:\n',
	'vote_drop_answer_confirm' : '<?=GetMessageJS("F_VOTE_DROP_ANSWER_CONFIRM")?>',
	'vote_drop_question_confirm' : '<?=GetMessageJS("F_VOTE_DROP_QUESTION_CONFIRM")?>'
});

function __ctrl_enter_<?=$arParams["FORM_ID"]?>(e, bNeedSubmit)
{
	if (!ValidateForm(document.forms['<?=$arParams["FORM_ID"]?>'], '<?=$arParams["AJAX_TYPE"]?>',  '<?=$arParams["AJAX_POST"]?>'))
	{
		if (e) BX.PreventDefault(e);
		return false;
	}
	if (bNeedSubmit !== false)
		BX.submit(document.forms['<?=$arParams["FORM_ID"]?>']);
	return true;
}

BX.ready( function() {
	BX.bind(
		document.forms['<?=$arParams["FORM_ID"]?>'],
		"submit",
		function(e){__ctrl_enter_<?=$arParams["FORM_ID"]?>(e, false);}
	);

	BX.addCustomEvent(window,  'LHE_OnInit', function(lightEditor)
	{
		lightEditor. insertImageAfterUpload = true;
		if (document.forms['<?=$arParams["FORM_ID"]?>']['hidden_focus'])
			BX.remove(document.forms['<?=$arParams["FORM_ID"]?>']['hidden_focus'].parentNode);

		BX.addCustomEvent(lightEditor, 'onShow', function() {
			BX.style(lightEditor.pFrame.parentNode, 'width', '100%');
			BX.style(lightEditor.pFrameTable.rows[2], 'display', 'none');
			lightEditor.pTextarea.setAttribute("tabindex", <?=$postMessageTabIndex?>);
			BX.bind(BX('post_message_hidden'), "focus", function(){lightEditor.SetFocus();});
		<?
		if (!$USER->IsAuthorized() && $arParams["FORUM"]["USE_CAPTCHA"]=="Y")
		{
		?>
				BX.loadScript('/bitrix/js/forum/captcha.js', function() {
					var formid = "<?=CUtil::JSEscape($arParams["FORM_ID"]);?>";
					var form = document.forms[formid];
					var captchaParams = {
						'image' : null,
						'hidden' : BX.findChild(form, {attr : {'name': 'captcha_code'}}, true),
						'input' : BX.findChild(form, {attr: {'name':'captcha_word'}}, true),
						'div' : BX.findChild(form, {'className':'forum-reply-field-captcha'}, true)
					};
					if (captchaParams.div)
						captchaParams.image = BX.findChild(captchaParams.div, {'tag':'img'}, true);
					var oCaptcha = new ForumFormCaptcha(captchaParams);
					setTimeout(function() {
						BX.bind(BX('forum-refresh-captcha'), 'click', BX.proxy(oCaptcha.Update, oCaptcha));
					}, 200);
				});
		<?
		}
		?>
		});
	});
});
</script>
<a name="postform"></a>
<div class="forum-header-box">
	<div class="forum-header-options">
		<span class="forum-option-bbcode"><a href="<?=$arResult["URL"]["HELP"]?>#bbcode">BBCode</a></span>&nbsp;&nbsp;
		<span class="forum-option-rules"><a href="<?=$arResult["URL"]["RULES"]?>"><?=GetMessage("F_RULES")?></a></span>
	</div>
	<div class="forum-header-title"><span><?
if ($arResult["MESSAGE_TYPE"] == "NEW")
{
	?><?=GetMessage("F_CREATE_IN_FORUM")?>: <a href="<?=$arResult["URL"]["LIST"]?>"><?=$arResult["FORUM"]["NAME"]?></a><?
}
elseif ($arResult["MESSAGE_TYPE"] == "REPLY") 
{ 
	?><?=GetMessage("F_REPLY_FORM")?><?
}
else
{
	?><?=GetMessage("F_EDIT_FORM")?> <?=GetMessage("F_IN_TOPIC")?>:
		<a href="<?=$arResult["URL"]["READ"]?>"><?=htmlspecialcharsEx($arResult["TOPIC_FILTER"]["TITLE"])?></a>, <?=GetMessage("F_IN_FORUM")?>: 
		<a href="<?=$arResult["URL"]["LIST"]?>"><?=$arResult["FORUM"]["NAME"]?></a><?
};	
	?></span></div>
</div>

<div class="forum-reply-form">
<?
if (!empty($arResult["ERROR_MESSAGE"])) 
{
?>
<div class="forum-note-box forum-note-error">
	<div class="forum-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "forum-note-error");?></div>
</div>
<?
};
?>
<form name="<?=$arParams["FORM_ID"]?>" id="<?=$arParams["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>#postform"<?
	?> method="POST" enctype="multipart/form-data" class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="<?=$arParams["PAGE_NAME"];?>" />
	<input type="hidden" name="FID" value="<?=$arParams["FID"]?>" />
	<input type="hidden" name="TID" value="<?=$arParams["TID"]?>" />
	<input type="hidden" name="MID" value="<?=$arResult["MID"];?>" />
	<input type="hidden" name="MESSAGE_TYPE" value="<?=$arParams["MESSAGE_TYPE"];?>" />
	<input type="hidden" name="AUTHOR_ID" value="<?=$arResult["TOPIC"]["AUTHOR_ID"];?>" />
	<input type="hidden" name="forum_post_action" value="save" />
	<input type="hidden" name="MESSAGE_MODE" value="NORMAL" />
	<input type="hidden" name="jsObjName" value="<?=$arParams["jsObjName"]?>" />
	<?=bitrix_sessid_post()?>
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->Init();
?>
<?
if (($arResult["SHOW_PANEL_NEW_TOPIC"] == "Y" || $arResult["SHOW_PANEL_GUEST"] == "Y") && $arParams["AJAX_CALL"] == "N")
{
?>
	<div class="forum-reply-fields">
<?
/* NEW TOPIC */
	if ($arResult["SHOW_PANEL_NEW_TOPIC"] == "Y")
	{
?>
		<div class="forum-reply-field forum-reply-field-title">
			<label for="TITLE<?=$arParams["form_index"]?>"><?=GetMessage("F_TOPIC_NAME")?><span class="forum-required-field">*</span></label>
			<input name="TITLE" id="TITLE<?=$arParams["form_index"]?>" type="text" value="<?=$arResult["TOPIC"]["TITLE"];?>" tabindex="<?=$tabIndex++;?>" size="70" />
		</div>
		<div class="forum-reply-field forum-reply-field-desc">
			<label for="DESCRIPTION<?=$arParams["form_index"]?>"><?=GetMessage("F_TOPIC_DESCR")?></label>
			<input name="DESCRIPTION" id="DESCRIPTION<?=$arParams["form_index"]?>" type="text" value="<?=$arResult["TOPIC"]["DESCRIPTION"];?>" tabindex="<?=$tabIndex++;?>" size="70"/>
		</div>
<?
/*?> for the future
	<tr title="<?=GetMessage("F_TOPIC_ICON_DESCRIPTION")?>"><td>
		<span class="title title-icons"><?=GetMessage("F_TOPIC_ICON")?></span>
		<span class="value value-icons"><?=$arResult["ForumPrintIconsList"];?></span></td></tr>
<?*/
	};
/* GUEST PANEL */
	if ($arResult["SHOW_PANEL_GUEST"] == "Y")
	{
?>
		<div class="forum-reply-field-user">
			<div class="forum-reply-field forum-reply-field-author"><label for="AUTHOR_NAME<?=$arParams["form_index"]?>"><?=GetMessage("F_TYPE_NAME")?><?
				?><span class="forum-required-field">*</span></label>
				<span><input name="AUTHOR_NAME" id="AUTHOR_NAME<?=$arParams["form_index"]?>" size="30" type="text" value="<?=$arResult["MESSAGE"]["AUTHOR_NAME"];?>" tabindex="<?=$tabIndex++;?>" /></span></div>
<?		
		if ($arResult["FORUM"]["ASK_GUEST_EMAIL"]=="Y")
		{
?>
			<div class="forum-reply-field-user-sep">&nbsp;</div>
			<div class="forum-reply-field forum-reply-field-email"><label for="AUTHOR_EMAIL<?=$arParams["form_index"]?>"><?=GetMessage("F_TYPE_EMAIL")?></label>
				<span><input type="text" name="AUTHOR_EMAIL" id="AUTHOR_EMAIL<?=$arParams["form_index"]?>" size="30" value="<?=$arResult["MESSAGE"]["AUTHOR_EMAIL"];?>" tabindex="<?=$tabIndex++;?>" /></span>
			</div>
<?
		};
?>
			<div class="forum-clear-float"></div>
		</div>
<?
	};
$arSwitchers = array();
if ($arResult["SHOW_PANEL_NEW_TOPIC"] == "Y" && $arParams["SHOW_TAGS"] == "Y")
{
	$iIndex = $tabIndex++;
?>
	<div class="forum-reply-field forum-reply-field-tags" <?if (!empty($arResult["TOPIC"]["TAGS"])): ?> style="display:block; "<? endif; ?>>
		<label for="TAGS"><?=GetMessage("F_TOPIC_TAGS")?></label>
<?
		if ($arResult["SHOW_SEARCH"] == "Y")
		{
			$APPLICATION->IncludeComponent(
				"bitrix:search.tags.input",
				"",
				array(
					"VALUE" => $arResult["TOPIC"]["~TAGS"],
					"NAME" => "TAGS",
					"TEXT" => 'tabindex="'.$iIndex.'" size="70" onmouseover="CorrectTags(this)"',
					"TMPL_IFRAME" => "N"),
				$component,
				array("HIDE_ICONS" => "Y"));
		?><iframe id="TAGS_div_frame" name="TAGS_div_frame" src="javascript:void(0);" style="display:none;"/></iframe><?
		}
		else
		{
			?><input name="TAGS" id="TAGS" type="text" value="<?=$arResult["TOPIC"]["TAGS"]?>" tabindex="<?=$iIndex?>" size="70" /><?
		}
		?>
		<div class="forum-clear-float"></div>
	</div><?
}	if (($arResult["SHOW_PANEL_NEW_TOPIC"] & ($arResult["SHOW_PANEL_VOTE"]|$arParams["SHOW_TAGS"])) == "Y" &&
		(empty($arResult["TOPIC"]["TAGS"]) || empty($arResult["QUESTIONS"])))
	{
	?><div class="forum-reply-field forum-reply-field-switcher"><?
		if (empty($arResult["TOPIC"]["TAGS"]) && $arParams["SHOW_TAGS"] == "Y")
		{
			?><span class="forum-reply-field forum-reply-field-switcher-tag"><?
				?><a href="javascript:void(0);" onclick="return AddTags(this);" <?
					?>onfocus="AddTags(this);" tabindex="<?=$iIndex?>"><?=GetMessage("F_ADD_TAGS")?></a><?
		?>&nbsp;&nbsp;</span><?
		}
		if (empty($arResult["QUESTIONS"]) && $arResult["SHOW_PANEL_VOTE"] == "Y")
		{
		?><span class="forum-reply-field forum-reply-field-switcher-vote"><?
			?><a href="javascript:void(0);" onclick="return ShowVote(this);" <?
				?>onfocus="ShowVote(this);" tabindex="<?=$tabIndex++?>"><?=GetMessage("F_ADD_VOTE")?></a>
		</span><?
		}
	?></div><?
	}
?>
</div>
<?

	if ($arResult["SHOW_PANEL_NEW_TOPIC"] == "Y" && $arResult["SHOW_PANEL_VOTE"] == "Y")
	{
	ob_start();
	?><li id="ANS_#Q#__#A#_"><input type="text" name="ANSWER[#Q#][#A#]" value="#A_VALUE#" /><?
		?><label>[<a onclick="return vote_remove_answer(this)" title="<?=GetMessage("F_VOTE_DROP_ANSWER")?>" href="#">X</a>]</label></li><?
	$sAnswer = ob_get_clean();
	ob_start();
	?><div class="forum-reply-field-vote-question"><?
		?><div id="QST_#Q#_" class="forum-reply-field-vote-question-title"><?
			?><input type="text" name="QUESTION[#Q#]" id="QUESTION_#Q#" value="#Q_VALUE#" /><?
			?><label for="QUESTION_#Q#">[<a onclick="return vote_remove_question(this)" title="<?=GetMessage("F_VOTE_DROP_QUESTION")?>" href="#">X</a>]</label><?
		?></div><?
		?><div class="forum-reply-field-vote-question-options"><?
			?><input type="checkbox" value="Y" name="MULTI[#Q#]" id="MULTI_#Q#" #Q_MULTY# /><?
			?><label for="MULTI_#Q#"><?=GetMessage("F_VOTE_MULTI")?></label><?
		?></div><?
		?><ol class="forum-reply-field-vote-answers">#Q_ANSWERS#<?
			?><li>[<a onclick="return vote_add_answer(this)" name="addA#Q#" href="#"><?=GetMessage("F_VOTE_ADD_ANSWER")?></a>]</li><?
		?></ol><?
	?></div><?
	$sQuestion = ob_get_clean();
?>
<script type="text/javascript">
	var arVoteParams = {
		'template_answer' : '<?=CUtil::JSEscape(str_replace("#A_VALUE#", "", $sAnswer))?>',
		'template_question' : '<?=CUtil::JSEscape(str_replace(
			array("#Q_VALUE#", "#Q_MULTY#", "#Q_ANSWERS#", "#A#", "#A_VALUE#"),
			array("", "", $sAnswer, 1, ""), $sQuestion
		))?>'
	}
</script>
<div id="vote_params" <?if (empty($arResult["QUESTIONS"])): ?>style="display:none;"<? endif; ?>>
	<div class="forum-reply-header"><?=GetMessage("F_VOTE")?></div>
	<div class="forum-reply-fields">
		<div class="forum-reply-field forum-reply-field-vote-duration">
			<label><?=GetMessage('VOTE_DURATION')?></label>
			<?$APPLICATION->IncludeComponent(
				"bitrix:main.calendar",
				"",
				array(
					"SHOW_INPUT"=>"Y",
					"SHOW_TIME"=>"Y",
					"INPUT_NAME"=>"DATE_END",
					"INPUT_VALUE"=>$arResult['DATE_END'],
					"FORM_NAME"=>$arParams["FORM_ID"],
				),
				$component,
				array("HIDE_ICONS"=>true)
			);?>
		</div>
		<div class="forum-reply-field forum-reply-field-vote">
<?
		foreach ($arResult["QUESTIONS"] as $qq => $arQuestion)
		{
			?><input type="hidden" name="QUESTION_ID[<?=$qq?>]" value="<?=$arQuestion["ID"]?>" /><?
			?><input type="hidden" name="QUESTION_DEL[<?=$qq?>]" value="<?=$arQuestion["DEL"]?>" /><?

			if ($arQuestion["DEL"] == "Y")
				continue;

			$arAnswers = array();
			foreach ($arQuestion["ANSWERS"] as $aa => $arAnswer)
			{
				?><input type="hidden" name="ANSWER_ID[<?=$qq?>][<?=$aa?>]" value="<?=$arAnswer["ID"]?>" /><?
				?><input type="hidden" name="ANSWER_DEL[<?=$qq?>][<?=$aa?>]" value="<?=$arAnswer["DEL"]?>" /><?
				if ($arAnswer["DEL"] == "Y")
					continue;
				$arAnswers[] = str_replace(
					array("#A#", "#A_VALUE#"),
					array($aa, $arAnswer["MESSAGE"]),
					$sAnswer);
			}
			?><?=str_replace(
				array("#Q_VALUE#", "#Q_MULTY#", "#Q_ANSWERS#", "#Q#"),
				array($arQuestion["QUESTION"], ($arQuestion["MULTI"] == "Y" ? "checked" : ""), implode("", $arAnswers), $qq),
				$sQuestion
			);?><?
		}
		if (empty($arResult["QUESTIONS"]))
		{
			$qq = 1;
			?><?=str_replace(
			array("#Q_VALUE#", "#Q_MULTY#", "#Q_ANSWERS#", "#Q#", "#A#", "#A_VALUE#"),
			array("", "", $sAnswer, 1, 1, ""),
			$sQuestion
			)?><?
		}
			?><div class="forum-reply-field-vote-question" id="vote_question_add"><?
				?><a onclick="return vote_add_question(this.parentNode, '<?=$qq?>');" href="#"><?=GetMessage("F_VOTE_ADD_QUESTION")?></a><?
			?></div>
		</div>
	</div>
</div>
<?
	}
}
?>
	<div class="forum-reply-header"><span><?=GetMessage("F_MESSAGE_TEXT")?></span><span class="forum-required-field">*</span></div>
	<div class="forum-reply-fields">
		<div class="forum-reply-field forum-reply-field-text">
			<?
				$postMessageTabIndex = $tabIndex++;
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

				if (LANGUAGE_ID == 'ru')
					AddEventHandler("fileman", "OnIncludeLightEditorScript", "CustomizeLHEForForum");
			$APPLICATION->IncludeComponent(
				"bitrix:main.post.form",
				"",
				Array(
					"FORM_ID" => $arParams["FORM_ID"],
					"SHOW_MORE" => "Y",
					"PARSER" => forumTextParser::GetEditorToolbar(array('forum' => $arResult['FORUM'])),

					"LHE" => array(
						'id' => 'POST_MESSAGE',
						'jsObjName' => $arParams["jsObjName"],
						'bSetDefaultCodeView' => ($arParams['EDITOR_CODE_DEFAULT'] == 'Y'),
						'bResizable' => true,
						'bAutoResize' => true,
						'bManualResize' => false,
						"documentCSS" => "body {color:#434343; font-size: 14px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 20px;}",
						"ctrlEnterHandler" => "__ctrl_enter_".$arParams["FORM_ID"]
					),

					"ADDITIONAL" => array(),

					"TEXT" => Array(
						"ID" => "POST_MESSAGE",
						"NAME" => "POST_MESSAGE",
						"VALUE" => isset($arResult['MESSAGE']["~POST_MESSAGE"]) ? $arResult['MESSAGE']["~POST_MESSAGE"] : "",
						"SHOW" => "Y",
						"HEIGHT" => "200px"),

					"UPLOAD_FILE" => array(
						'CONTROL_ID' => $fileControlId,
						"INPUT_NAME" => 'FILES',
						"INPUT_VALUE" => (!empty($arResult["MESSAGE"]["FILES"]) ? array_keys($arResult["MESSAGE"]["FILES"]) : false),
						"MAX_FILE_SIZE" => COption::GetOptionString("forum", "file_max_size", 5242880),
						"MULTIPLE" => "Y",
						"MODULE_ID" => "forum",
						"ALLOW_UPLOAD" => ($arParams["FORUM"]["ALLOW_UPLOAD"] == "N" ? 'N' :
							($arResult["FORUM"]["ALLOW_UPLOAD"] == "Y" ? "I" : $arResult["FORUM"]["ALLOW_UPLOAD"])),
						"ALLOW_UPLOAD_EXT" => $arResult["FORUM"]["ALLOW_UPLOAD_EXT"]
					),
					"UPLOAD_FILE_PARAMS" => array("width" => $arParams["IMAGE_SIZE"], "height" => $arParams["IMAGE_SIZE"]),
					"UPLOAD_WEBDAV_ELEMENT" => $arResult["USER_FIELDS"]["UF_FORUM_MESSAGE_DOC"],

//					"DESTINATION" => array(),

//					"TAGS" => Array(),

					"SMILES" => array("VALUE" => $arSmiles),
					"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
				)
			);
					?><a href="#" tabindex="<?=$postMessageTabIndex?>" id="post_message_hidden"></a>
				</div>
<?
/* ATTACH FILES */
if (!empty($arResult["USER_FIELDS"]))
{
	ob_start();
	foreach ($arResult["USER_FIELDS"] as $k => $v)
	{
		if ($k != "UF_FORUM_MESSAGE_DOC")
		{
			$v["VALUE"] = (!empty($_REQUEST[$k]) ? $_REQUEST[$k] : $v["VALUE"]);
			?><dt><?=$v["EDIT_FORM_LABEL"]?><?if ($v["MANDATORY"] == "Y"): ?><span class="forum-required-field">*</span><? endif; ?></dt><dd><?
			$GLOBALS["APPLICATION"]->IncludeComponent(
				"bitrix:system.field.edit",
				$v["USER_TYPE"]["USER_TYPE_ID"],
				array("arUserField" => $v, "bVarsFromForm" => ($arParams["bVarsFromForm"] == "Y")),
				null,
				array("HIDE_ICONS" => "Y")
			);?></dd><?
		}
	}
	$res = ob_get_clean();
	if (!empty($res))
	{
		?><dl><?=$res?></dl><?
	}
}
/* EDIT PANEL */
if ($arResult["SHOW_PANEL_EDIT"] == "Y")
{
?>
		<div class="forum-reply-field forum-reply-field-lastedit">
<?
	$checked = true;
	if ($arResult["SHOW_PANEL_EDIT_ASK"] == "Y")
	{
		$checked = ($_REQUEST["EDIT_ADD_REASON"]=="Y" ? true : false);
		?><div class="forum-reply-field-lastedit-view"><?
			?><input type="checkbox" id="EDIT_ADD_REASON" name="EDIT_ADD_REASON<?=$arParams["form_index"]?>" <?=($checked ? "checked=\"checked\"" : "")?> value="Y" <?
				?>onclick="ShowLastEditReason(this.checked, this.parentNode.nextSibling)" />&nbsp;<?
			?><label for="EDIT_ADD_REASON<?=$arParams["form_index"]?>"><?=GetMessage("F_EDIT_ADD_REASON")?></label></div><?
	};
	
		?><div class="forum-reply-field-lastedit-reason" <?
		if (!$checked)
		{
			?> style="display:none;" <?
		};
		?>  id=""><?
			if ($arResult["SHOW_EDIT_PANEL_GUEST"] == "Y")
			{
			?><input name="EDITOR_NAME" type="hidden" value="<?=$arResult["EDITOR_NAME"];?>" /><?
				if ($arResult["FORUM"]["ASK_GUEST_EMAIL"] == "Y")
				{
			?><input type="hidden" name="EDITOR_EMAIL" value="<?=$arResult["EDITOR_EMAIL"];?>" /></br><?
				};
			};
		?>
			<label for="EDIT_REASON"><?=GetMessage("F_EDIT_REASON")?></label>
			<input type="text" name="EDIT_REASON" id="EDIT_REASON" size="70" value="<?=$arResult["EDIT_REASON"]?>" /></div>
		</div>
<?
};

/* CAPTHCA */
if (!$USER->IsAuthorized() && $arParams["FORUM"]["USE_CAPTCHA"]=="Y")
{
?>
		<div class="forum-reply-field forum-reply-field-captcha" style='display: none;'>
			<input type="hidden" name="captcha_code" value=""/>
			<div class="forum-reply-field-captcha-label">
				<label for="captcha_word"><?=GetMessage("F_CAPTCHA_PROMT")?><span class="forum-required-field">*</span></label>
				<input type="text" size="30" name="captcha_word" id="captcha_word" tabindex="<?=$tabIndex++;?>" autocomplete="off" />
				<a href='javascript:void(0);' class='forum-ajax-link' id='forum-refresh-captcha'><?=GetMessage("F_REFRESH_CAPTCHA")?></a>
			</div>
			<div class="forum-reply-field-captcha-image">
				<img src="" alt="<?=GetMessage("F_CAPTCHA_TITLE")?>" />
			</div>
		</div>
<?
}
?>
		<div class="forum-reply-field forum-reply-field-settings">
<?
/* SMILES */
if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y")
{
?>
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="USE_SMILES" id="USE_SMILES<?=$arParams["form_index"]?>" <?
				?>value="Y" <?=($arResult["MESSAGE"]["USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
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
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->LoadScript(CUtil::JSEscape($arParams["FORM_ID"]));
?>