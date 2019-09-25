<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if (!$this->__component->__parent || empty($this->__component->__parent->__name)):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/themes/blue/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/styles/additional.css');
endif;
/********************************************************************
				Input params
********************************************************************/
/***************** BASE ********************************************/
/*******************************************************************/
if (LANGUAGE_ID == 'ru')
{
	$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/ru/script.php");
	@include_once($path);
}
$tabIndex = 1;

$arParams["SEO_USER"] = (in_array($arParams["SEO_USER"], array("Y", "N", "TEXT")) ? $arParams["SEO_USER"] : "Y");
$arParams["USER_TMPL"] = '<noindex><a rel="nofollow" href="#URL#" title="'.GetMessage("F_USER_PROFILE").'">#NAME#</a></noindex>';
if ($arParams["SEO_USER"] == "N") $arParams["USER_TMPL"] = '<a href="#URL#" title="'.GetMessage("F_USER_PROFILE").'">#NAME#</a>';
elseif ($arParams["SEO_USER"] == "TEXT") $arParams["USER_TMPL"] = '#NAME#';
/********************************************************************
				/Input params
********************************************************************/
?>
<div style="float:right;">
	<div class="out"><div class="in" style="width:<?=$arResult["count"]?>%">&nbsp;</div></div>
	<div class="out1"><div class="in1"><?=GetMessage("F_POST_FULLY")." ".$arResult["count"]?>%</div></div>
</div>
<div class="forum-clear-float"></div>

<a name="postform"></a>
<div class="forum-header-box">
	<div class="forum-header-options">
<?
if ($arResult["mode"] != "new"):
?>
	<span class="forum-option-folder"><a href="<?=$arResult["URL"]["HELP"]?>"><?=$arResult["FolderName"]?></a></span>
<?
endif;
?>
	</div>
	<div class="forum-header-title"><span><?
if ($arResult["mode"] != "new"):
?>
	<?=$arResult["FolderName"]?>
<?
else:
?>
	<?=GetMessage("F_NEW_PM")?>
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
<IFRAME style="width:0px; height:0px; border: 0px" src="javascript:void(0)" name="frame_USER_ID" id="frame_USER_ID"></IFRAME>
<form name="REPLIER" id="REPLIER" action="<?=POST_FORM_ACTION_URI?>" method="POST" onsubmit="return ValidateForm(this);"<?
	?> class="forum-form">
	<input type="hidden" name="PAGE_NAME" value="pm_edit" />
	<input type="hidden" name="action" id="action" value="<?=$arResult["action"]?>" />
	<input type="hidden" name="FID" value="<?=$arResult["FID"]?>" />
	<input type="hidden" name="MID" value="<?=$arResult["MID"]?>" />
	<input type="hidden" name="mode" value="<?=$arResult["mode"]?>" />
	<input type="hidden" name="USER_ID" id="USER_ID" value="<?=$arResult["POST_VALUES"]["USER_ID"]?>" readonly="readonly" />
	<?=bitrix_sessid_post()?>
<?
	if ($arParams['AUTOSAVE'])
		$arParams['AUTOSAVE']->Init();
?>

	<div class="forum-reply-fields">
		<div class="forum-reply-field forum-reply-field-title">
			<label for="POST_SUBJ"><?=GetMessage("F_HEAD_SUBJ")?><span class="forum-required-field">*</span></label>
			<input name="POST_SUBJ" id="POST_SUBJ" type="text" value="<?=$arResult["POST_VALUES"]["POST_SUBJ"];?>" tabindex="<?=$tabIndex++;?>" size="70" />
		</div>
		<div class="forum-reply-field-user">
			<div class="forum-reply-field forum-reply-field-author"><label for="input_USER_ID"><?=GetMessage("F_HEAD_TO")
				?><span class="forum-required-field">*</span></label>
				<span><input type="text" name="input_USER_ID" id="input_USER_ID" tabindex="<?=$tabIndex++;?>" <?
					?>value="<?
					if (!empty($arResult["POST_VALUES"]["SHOW_NAME"]["text"])):
						?><?=$arResult["POST_VALUES"]["SHOW_NAME"]["text"]?><?
					elseif (!empty($arResult["POST_VALUES"]["USER_ID"])):
						?><?=$arResult["POST_VALUES"]["USER_ID"]?><?
					endif;
					?>" onfocus="fSearchUser()" /></span>
			</div>
			<div class="forum-reply-field-user-sep">&nbsp;</div>
			<div class="forum-reply-field forum-reply-field-email"><br />
				<span class="forum-pmessage-recipient">
<?
	if ($arResult["mode"] != "edit"):
?>
				<a href="javascript:void(0);" onclick="window.open('<?=$arResult["pm_search"]?>', '', 'scrollbars=yes,resizable=yes,width=760,height=500,<?
					?>top='+Math.floor((screen.height - 500)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));" title="<?=GetMessage("F_SEARCH_USER")?>">
					<?=GetMessage("F_FIND_USER")?></a>
<?
	endif;
?>
				<span id="div_USER_ID" name="div_USER_ID"><?
				if (!empty($arResult["POST_VALUES"]["SHOW_NAME"])):
					?>[<?=str_replace(
						array("#URL#", "#NAME#"),
						array($arResult["POST_VALUES"]["SHOW_NAME"]["link"], $arResult["POST_VALUES"]["SHOW_NAME"]["text"]),
						$arParams["USER_TMPL"])?>]<?
				elseif (!empty($arResult["POST_VALUES"]["USER_ID"])):
					?><i><?=GetMessage("PM_NOT_FINED");?></i><?
				endif;
				?></span></span>
				</div>

			<div class="forum-clear-float"></div>
		</div>
	</div>

	<div class="forum-reply-header"><?=GetMessage("F_HEAD_MESS")?><span class="forum-required-field">*</span></div>

	<div class="forum-reply-fields">
		<div class="forum-reply-field forum-reply-field-text">
			<?
				$arSmiles = array();
				foreach($arResult["SMILES"] as $arSmile)
				{
					$arSmiles[] = array(
						'name' => $arSmile["NAME"],
						'path' => $arParams["PATH_TO_SMILE"].$arSmile["IMAGE"],
						'code' => array_shift(explode(" ", str_replace("\\\\","\\",$arSmile["TYPING"])))
					);
				}

				CModule::IncludeModule("fileman");
				AddEventHandler("fileman", "OnIncludeLightEditorScript", "CustomizeLHEForForum");

				$LHE = new CLightHTMLEditor();

				$arEditorParams = array(
					'id' => "POST_MESSAGE",
					'content' => isset($arResult['POST_VALUES']["POST_MESSAGE"]) ? $arResult['POST_VALUES']["POST_MESSAGE"] : "",
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
					'bSetDefaultCodeView' => ($arParams['EDITOR_CODE_DEFAULT'] === 'Y'),
					'bResizable' => true,
					'bAutoResize' => true
				);

				$arEditorParams['toolbarConfig'] = forumTextParser::GetEditorToolbar(array('mode'=>'full'));
				$LHE->Show($arEditorParams);
			?>
		</div>

		<div class="forum-reply-field forum-reply-field-settings">
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="USE_SMILES" id="USE_SMILES" <?
				?>value="Y" <?=($arResult["POST_VALUES"]["USE_SMILES"]=="Y") ? "checked=\"checked\"" : "";?> <?
				?>tabindex="<?=$tabIndex++;?>" />&nbsp;<label for="USE_SMILES"><?=GetMessage("F_WANT_ALLOW_SMILES")?></label></div>

<?
	if ($arParams["version"] == 2 && $arResult["action"] == "send"):
?>
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="COPY_TO_OUTBOX" id="COPY_TO_OUTBOX" value="Y" tabindex="<?=$tabIndex++;?>" <?
				?><?=(($arResult["POST_VALUES"]["COPY_TO_OUTBOX"] != "N") ? "checked" : "")?> />&nbsp;<?
				?><label for="COPY_TO_OUTBOX"><?=GetMessage("F_COPY_TO_OUTBOX")?></label></div>
			<div class="forum-reply-field-setting">
				<input type="checkbox" name="REQUEST_IS_READ" id="REQUEST_IS_READ" value="Y" tabindex="<?=$tabIndex++;?>" <?
					?><?=(($arResult["POST_VALUES"]["REQUEST_IS_READ"] == "Y") ? "checked" : "")?> />&nbsp;<?
				?><label for="REQUEST_IS_READ"><?=GetMessage("F_REQUEST_IS_READ")?></label></div><?
	endif;
?>
		</div>
		<div class="forum-reply-buttons">
			<input type="submit" name="SAVE_BUTTON" id="SAVE_BUTTON" tabindex="<?=$tabIndex++;?>" <?
				?> value="<?=($arResult["action"] == "save" ? GetMessage("F_ACT_SAVE") : GetMessage("F_ACT_SEND"))?>" tabindex="<?=$tabIndex++;?>" />
		</div>
	</div>
</div>
</form>

<script language="Javascript">
window.switcher = '<?=CUtil::JSEscape( !empty($arResult["POST_VALUES"]["SHOW_NAME"]["text"]) ?
	$arResult["POST_VALUES"]["SHOW_NAME"]["text"] : (!empty($arResult["POST_VALUES"]["USER_ID"]) ?
		$arResult["POST_VALUES"]["USER_ID"] : ''))?>';
function fSearchUser()
{
	var
			name = 'USER_ID',
			template_path = '<?=CUtil::JSEscape($arResult["pm_search_for_js"])?>',
			handler = document.getElementById('input_'+name),
			div_ = document.getElementById('div_'+name);
	if (typeof handler != "object" || null == handler || typeof div_ != "object")
		return false;
	if (window.switcher != handler.value)
	{
		window.switcher = handler.value;
		handler.form.elements[name].value=handler.value;
		if (handler.value != '')
		{
			div_.innerHTML = '<i><?=CUtil::JSEscape(GetMessage("FORUM_MAIN_WAIT"))?></i>';
			document.getElementById('frame_'+name).src=template_path.replace(/\#LOGIN\#/gi, handler.value);
		}
		else
			div_.innerHTML = '';
	}
	setTimeout(fSearchUser, 1000);
	return true;
}
fSearchUser();

var bSendForm = false;
if (typeof oErrors != "object")
	var oErrors = {};
oErrors['no_topic_name'] = "<?=GetMessageJS("JERROR_NO_TOPIC_NAME")?>";
oErrors['no_message'] = "<?=GetMessageJS("JERROR_NO_MESSAGE")?>";
oErrors['max_len'] = "<?=GetMessageJS("JERROR_MAX_LEN")?>";
</script>
<?
if ($arParams['AUTOSAVE'])
	$arParams['AUTOSAVE']->LoadScript("REPLIER".CUtil::JSEscape($arParams["form_index"]));
?>