<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/forum.interface/templates/popup/script.js"></script>', true);
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/forum.interface/templates/.default/script.js"></script>', true);
// ************************* Input params***************************************************************
$arParams["SHOW_LINK_TO_FORUM"] = ($arParams["SHOW_LINK_TO_FORUM"] == "N" ? "N" : "Y");
// *************************/Input params***************************************************************
if (!empty($arResult["MESSAGES"])):
	if (strlen($arResult["NAV_STRING"]) > 0):
		?><div class="forum-nav top"><?=$arResult["NAV_STRING"]?></div><?
	endif

?>
<table class="forum-reviews-messages" cellpadding="0" cellspacing="0" border="0" width="100%">
<?
foreach ($arResult["MESSAGES"] as $res):
?>
	<tr><th><div class="controls-reviews">
<?
	if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):
?>
	<a href="#review_anchor" title="<?=GetMessage("FTR_QUOTE_HINT")?>" class="button-small" <?
			?>onMouseDown="quoteMessageEx('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>', 'message_text_<?=$res["ID"]?>')"><?=GetMessage("FTR_QUOTE_FULL")?></a>
<?
	endif;
?>
		<a href="#review_anchor"  title="<?=GetMessage("FTR_NAME")?>"  class="button-small" <?
			?>onMouseDown="reply2author('<?=$res["FOR_JS"]["AUTHOR_NAME"]?>,')"><?=GetMessage("FTR_NAME")?></a></div>
		<a name="message<?=$res["ID"]?>"></a><b>
<?
		if (intVal($res["AUTHOR_ID"]) > 0 && !empty($res["AUTHOR_URL"])):
			?><a href="<?=$res["AUTHOR_URL"]?>"><?=$res["AUTHOR_NAME"]?></a><?
		else:
			?><?=$res["AUTHOR_NAME"]?><?
		endif;
		?></b>, <?=$res["POST_DATE"]?>
	</th></tr>
	<tr><td>
		<div class="forum-text" id="message_text_<?=$res["ID"]?>"><?=$res["POST_MESSAGE_TEXT"]?></div>
<?
		if (strLen($res["ATTACH_IMG"]) > 0):
?>
		<div class="forum-message-img">
			<?$GLOBALS["APPLICATION"]->IncludeComponent(
				"bitrix:forum.interface",
				"show_file",
				Array(
					"FILE" => $res["~ATTACH_FILE"],
					"WIDTH"=> $arResult["PARSER"]->image_params["width"],
					"HEIGHT"=> $arResult["PARSER"]->image_params["height"],
					"CONVERT" => "N",
					"FAMILY" => "FORUM",
					"SINGLE" => "Y",
					"RETURN" => "N",
					"SHOW_LINK" => "Y"
				),
				null,
				array("HIDE_ICONS" => "Y"));?>
		</div>
<?
		endif;
?>
	</td></tr>
	<tr><td class="clear"></td></tr>
<?
endforeach;
?>
</table>
<?

if (strlen($arResult["NAV_STRING"]) > 0):
?>
	<div class="forum-nav bottom"><?=$arResult["NAV_STRING"]?></div>
<?
endif;

if (!empty($arResult["read"]) && $arParams["SHOW_LINK_TO_FORUM"] != "N"):
?>
	<a href="<?=$arResult["read"]?>" class="forum-link"><?=GetMessage("F_C_GOTO_FORUM") ?></a>
<?
endif;

endif;
	
if (!empty($arResult["ERROR_MESSAGE"])):
?><?=ShowError($arResult["ERROR_MESSAGE"])?><?
elseif (!empty($arResult["OK_MESSAGE"])):
?><?=ShowNote($arResult["OK_MESSAGE"])?><?
endif;

if ($arResult["SHOW_POST_FORM"] != "Y"):
	return false;
endif;

$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
include($path);
?>
<a name="review_anchor"></a>
<form action="<?=POST_FORM_ACTION_URI?>#review_anchor" method="post" <?
	?>name="REPLIER" id="REPLIER" enctype="multipart/form-data" onsubmit="return ValidateForm(this);" <?
	?>onmouseover="if(init_form){init_form(this)}" >
	<input type="hidden" name="back_page" value="<?=$arResult["CURRENT_PAGE"]?>" />
	<input type="hidden" name="ELEMENT_ID" value="<?=$arParams["ELEMENT_ID"]?>" />
	<input type="hidden" name="SECTION_ID" value="<?=$arParams["SECTION_ID"]?>" />
	<input type="hidden" name="save_product_review" value="Y" />
	<?=$arResult["sessid"]?>
	<table class="forum-reviews-form data-tabe" cellpadding="0" cellspacing="0" border="0" width="100%">
	<thead><?
		if ($arResult["IS_AUTHORIZED"]):
		?><tr>
			<th><?=GetMessage("OPINIONS_NAME")?>:</th>
			<td><?=$arResult["REVIEW_AUTHOR"]?></th>
		</tr><?
		else:
		?><tr>
			<th><?=GetMessage("OPINIONS_NAME")?>:</th>
			<td><input type="text" name="REVIEW_AUTHOR" value="<?=$arResult["REVIEW_AUTHOR"]?>" /></th>
		</tr><?
			if ($arResult["FORUM"]["ASK_GUEST_EMAILd"]=="Y"):
		?><tr>
			<th><?=GetMessage("OPINIONS_EMAIL")?>:</th>
			<td><input type="text" name="REVIEW_EMAIL" value="<?=$arResult["REVIEW_EMAIL"]?>"/></th>
		</tr><?
			endif;
		endif;
	?></thead>
	<tbody>
		<tr><?
		
		if ($arResult["FORUM"]["ALLOW_SMILES"] == "Y"):
			?><td class="forum-smile">
				<table class="forum-smile">
					<tr><th colspan="3"><?=GetMessage("FTR_SMILES")?></th></tr>
					<?=$arResult["ForumPrintSmilesList"]?>
				</table>
			</td>
			<td><?
		else:
			?><td colspan="2"><?
		endif;
		
if ($arResult["FORUM"]["ALLOW_FONT"] == "Y" || $arResult["FORUM"]["ALLOW_BIU"] == "Y" || $arResult["FORUM"]["ALLOW_ANCHOR"] == "Y" || 
	$arResult["FORUM"]["ALLOW_IMG"] == "Y" || $arResult["FORUM"]["ALLOW_QUOTE"] == "Y" || $arResult["FORUM"]["ALLOW_CODE"] == "Y"):
?>
<table class="forum-toolbars" cellpadding="0" cellspacing="0" border="0" width="100%"><tr class="top"><td>
<?
if ($arResult["FORUM"]["ALLOW_FONT"] == "Y"):
?>
	<div class="form_button button_font">
		<select name='FONT' class='button_font' id='form_font' title="<?=GetMessage("FTR_FONT_TITLE")?>">
			<option value='0'><?=GetMessage("FTR_FONT")?></option>
			<option value='Arial' style='font-family:Arial'>Arial</option>
			<option value='Times' style='font-family:Times'>Times</option>
			<option value='Courier' style='font-family:Courier'>Courier</option>
			<option value='Impact' style='font-family:Impact'>Impact</option>
			<option value='Geneva' style='font-family:Geneva'>Geneva</option>
			<option value='Optima' style='font-family:Optima'>Optima</option>
			<option value='Verdana' style='font-family:Verdana'>Verdana</option>
		</select>
	</div>
	<div class="form_button button_color" id="form_palette" title="<?=GetMessage("FTR_COLOR_TITLE")?>"></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_BIU"] == "Y"):
?>
	<div class="form_button button_bold" id="form_b" title="<?=GetMessage("FTR_BOLD")?>"></div>
	<div class="form_button button_italic" id="form_i" title="<?=GetMessage("FTR_ITAL")?>"></div>
	<div class="form_button button_underline" id="form_u" title="<?=GetMessage("FTR_UNDER")?>"></div>
<?
endif;
if ($arResult["FORUM"]["ALLOW_ANCHOR"] == "Y"):
?>
	<div class="form_button button_url" id="form_url" title="<?=GetMessage("FTR_HYPERLINK_TITLE")?>"></div>
<?
endif;
if ($arResult["FORUM"]["ALLOW_IMG"] == "Y"):
?>
	<div class="form_button button_img" id="form_img" title="<?=GetMessage("FTR_IMAGE_TITLE")?>"></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_QUOTE"] == "Y"):
?>
	<div class="form_button button_quote" id="form_quote" title="<?=GetMessage("FTR_QUOTE_TITLE")?>"></div>
<?
endif;
if ($arResult["FORUM"]["ALLOW_CODE"] == "Y"):
?>
	<div class="form_button button_code" id="form_code" title="<?=GetMessage("FTR_CODE_TITLE")?>"></div>
<?
endif;

if ($arResult["FORUM"]["ALLOW_LIST"] == "Y"):
?>
	<div class="form_button button_list" id="form_list" title="<?=GetMessage("FTR_LIST_TITLE")?>"></div>
<?
endif;

if (LANGUAGE_ID == 'ru'):
?>
	<div class="form_button button_translit" id="form_translit" title="<?=GetMessage("FTR_TRANSLIT_TITLE")?>"></div>
<?
endif;
?>
	<div class="button_closeall" title="<?=GetMessage("FTR_CLOSE_OPENED_TAGS")?>" id="form_closeall" style="display:none;">
		<a href="javascript:void(0)"><?=GetMessage("FTR_CLOSE_ALL_TAGS")?></a></div>
</td></tr>
<tr class="post_message"><td>
<textarea name="REVIEW_TEXT" id="REVIEW_TEXT" tabindex="<?=$tabIndex++;?>"><?=$arResult["REVIEW_TEXT"];?></textarea></td></tr>
</table><?
else:
?><textarea name="REVIEW_TEXT" id="REVIEW_TEXT" tabindex="<?=$tabIndex++;?>"><?=$arResult["REVIEW_TEXT"];?></textarea><?
endif;

?><div class="forum-reviews-info"><?
if ($arResult["FORUM"]["ALLOW_SMILES"]=="Y"):?>
	<div class="smiles"><input type="checkbox" name="REVIEW_USE_SMILES" id="REVIEW_USE_SMILES" <?
		?>value="Y" <?=($arResult["REVIEW_USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
		?>tabindex="<?=$tabIndex++;?>" /><label for="REVIEW_USE_SMILES"><?=GetMessage("FTR_WANT_ALLOW_SMILES")?></label></div><?
endif;

if ($arResult["SHOW_SUBSCRIBE"] == "Y"):
?>
	<div class="subscribe">
<?
	if ($arResult["FORUM_SUBSCRIBE"] == "Y"):
		?><?=GetMessage("WD_SUBSCRIBED")?><?
		?> <a href="<?=$APPLICATION->GetCurPageParam("subscribe_forum=N&".bitrix_sessid_get(), array("subscribe_forum", "subscribe_topic", "sessid"))?>"><?=GetMessage("WD_UNSUBSCRIBE")?></a>. <?
	elseif ($arResult["TOPIC_SUBSCRIBE"] == "Y"):
		?><?=GetMessage("WD_SUBSCRIBED2")?><?
		?> <a href="<?=$APPLICATION->GetCurPageParam("subscribe_topic=N&".bitrix_sessid_get(), array("subscribe_forum", "subscribe_topic", "sessid"))?>"><?=GetMessage("WD_UNSUBSCRIBE")?></a>.<?
	elseif ($arResult["TOPIC_SUBSCRIBE"] != "Y"):
	?>
		<input type="checkbox" name="TOPIC_SUBSCRIBE" id="TOPIC_SUBSCRIBE" value="Y" <?
			?><?=($arResult["TOPIC_SUBSCRIBE"] == "Y")? "checked disabled " : "";?> tabindex="<?=$tabIndex++;?>" />
		<label for="TOPIC_SUBSCRIBE"><?=GetMessage("FTR_WANT_SUBSCRIBE_TOPIC")?></label>
	<?
	endif;
?>
	</div>
<?
endif;

if ($arResult["SHOW_PANEL_ATTACH_IMG"] == "Y"):?>
	<div class="attach"><?=($arResult["FORUM"]["ALLOW_UPLOAD"]=="Y") ? GetMessage("FTR_LOAD_IMAGE") : GetMessage("FTR_LOAD_FILE") 
		?>: <br /><input name="REVIEW_ATTACH_IMG" type="file"/></div><?
endif;

/* CAPTHCA */
if (!empty($arResult["CAPTCHA_CODE"])):
	?>
	<div class="captcha" style="clear:both;">
	<?=GetMessage("CAPTCHA_TITLE")?>:<br />
	<span style="float:left;"><img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" alt="<?=GetMessage("CAPTCHA_TITLE")?>" /></span>
	<?=GetMessage("CAPTCHA_PROMT")?>:<br /><input type="text" name="captcha_word" />
	
	<input type="hidden" name="captcha_code" value="<?=$arResult["CAPTCHA_CODE"]?>"/>
	</div>
	<div style="clear:both;"></div><?
endif;

	?><input type="submit" value="<?=GetMessage("OPINIONS_SEND"); ?>" name="send_comment" />
</div>
		</td>
	</tr>
	</tbody>
</table>
</form>

<script type="text/javascript">
if (typeof oErrors != "object")
	var oErrors = {};

oErrors['no_topic_name'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_TOPIC_NAME"))?>";
oErrors['no_message'] = "<?=CUtil::addslashes(GetMessage("JERROR_NO_MESSAGE"))?>";
oErrors['max_len1'] = "<?=CUtil::addslashes(GetMessage("JERROR_MAX_LEN1"))?>";
oErrors['max_len2'] = "<?=CUtil::addslashes(GetMessage("JERROR_MAX_LEN2"))?>";
oErrors['no_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_URL"))?>";
oErrors['no_title'] = "<?=CUtil::addslashes(GetMessage("FORUM_ERROR_NO_TITLE"))?>";


if (typeof oText != "object")
	var oText = {};

oText['author'] = " <?=CUtil::addslashes(GetMessage("JQOUTE_AUTHOR_WRITES"))?>:\n";
oText['translit_en'] = "Ru->En";
oText['enter_url'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL"))?>";
oText['enter_url_name'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_URL_NAME"))?>";
oText['enter_image'] = "<?=CUtil::addslashes(GetMessage("FORUM_TEXT_ENTER_IMAGE"))?>";
oText['list_prompt'] = "<?=CUtil::addslashes(GetMessage("FORUM_LIST_PROMPT"))?>";


if (typeof oHelp != "object")
	var oHelp = {};

oHelp['B'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_BOLD"))?>";
oHelp['I'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_ITALIC"))?>";
oHelp['U'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_UNDER"))?>";
oHelp['FONT'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_FONT"))?>";
oHelp['COLOR'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_COLOR"))?>";
oHelp['CLOSE'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_CLOSE"))?>";
oHelp['URL'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_URL"))?>";
oHelp['IMG'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_IMG"))?>";
oHelp['QUOTE'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_QUOTE"))?>";
oHelp['LIST'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_LIST"))?>";
oHelp['CODE'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_CODE"))?>";
oHelp['CLOSE_CLICK'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_CLICK_CLOSE"))?>";
oHelp['TRANSLIT'] = "<?=CUtil::addslashes(GetMessage("FORUM_HELP_TRANSLIT"))?>";

function reply2author(name)
{
	<?if ($arResult["FORUM"]["ALLOW_BIU"] == "Y"):?>
	document.REPLIER.REVIEW_TEXT.value += "[B]"+name+"[/B] \n";
	<?else:?>
	document.REPLIER.REVIEW_TEXT.value += name+" \n";
	<?endif;?>
	return false;
}
</script>
<?
if (LANGUAGE_ID == 'ru'):
	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
	include($path);
endif;
?>