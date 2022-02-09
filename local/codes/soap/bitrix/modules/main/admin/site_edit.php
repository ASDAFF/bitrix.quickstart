<?
##############################################
# Bitrix Site Manager                        #
# Copyright (c) 2002-2007 Bitrix             #
# http://www.bitrixsoft.com                  #
# mailto:admin@bitrixsoft.com                #
##############################################

require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
define("HELP_FILE", "settings/sites/site_edit.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/condition.php");

ClearVars();

if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings') && !$USER->CanDoOperation('lpa_template_edit'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings') || $USER->CanDoOperation('lpa_template_edit');

IncludeModuleLangFile(__FILE__);
$aMsg=array(); $message=null;
/***************************************************************************
			Helper functions
***************************************************************************/

function SaveFileLang($strFileName, $strContent)
{
	global $APPLICATION;

	if(strlen($strContent)<=0)
	{
		return;
	}

	$APPLICATION->SaveFileContent($strFileName, $strContent);
}


$bVarsFromForm = false;
$bNew = ($LID == '' || $_REQUEST['new'] == 'Y');

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB"), "ICON" => "site_edit", "TITLE" => GetMessage("MAIN_TAB_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arTemplates = Array();
if(!$bNew)
{
	$dbSiteRes = CSite::GetTemplateList($LID);
	while($arSiteRes = $dbSiteRes->Fetch())
		$arTemplates[$arSiteRes["ID"]] = $arSiteRes['CONDITION'];
}

if($REQUEST_METHOD=="POST" && (strlen($save)>0 || strlen($apply)>0) && $isAdmin && check_bitrix_sessid())
{

	$em = new CEventMessage;
	$arFields = Array(
		"ACTIVE"			=> $_POST["ACTIVE"],
		"SORT"				=> $_POST["SORT"],
		"DEF"				=> $_POST["DEF"],
		"NAME"				=> $_POST["NAME"],
		"DIR"				=> $_POST["DIR"],
		"FORMAT_DATE"		=> trim($_POST["FORMAT_DATE"]),
		"FORMAT_DATETIME"	=> trim($_POST["FORMAT_DATETIME"]),
		"WEEK_START"		=> intval($_POST["WEEK_START"]),
		"CHARSET"			=> $_POST["CHARSET"],
		"FORMAT_NAME"		=> CSite::GetNameFormatByValue($_POST["FORMAT_NAME"]),
		"SITE_NAME"			=> $_POST["SITE_NAME"],
		"SERVER_NAME"		=> $_POST["SERVER_NAME"],
		"EMAIL"				=> $_POST["EMAIL"],
		"LANGUAGE_ID"		=> $_POST["LANGUAGE_ID"],
		"DOC_ROOT"			=> $_POST["DOC_ROOT"],
		"DOMAINS"			=> $_POST["DOMAINS"]
	);

	$arFields["TEMPLATE"]=array();
	$bSet=false;
	foreach($_POST["SITE_TEMPLATE"] as $key=>$val)
	{
		if ($USER->CanDoOperation('edit_php') || $_POST['selected_type'][$key] != 'php')
		{
			$cond = ConditionCompose($val, $key);
		}
		else
		{
			if(isset($arTemplates[$key]))
				$cond = $arTemplates[$key];
			else
				continue;
		}

		$arFields["TEMPLATE"][] = array(
			"TEMPLATE" => $val['TEMPLATE'],
			"SORT" => $val['SORT'],
			"CONDITION" => $cond
		);
	}

	if($bNew)
	{
		$arFields["LID"]=$LID;
		if ($_POST["START_SITE_WIZARD"] == "Y")
			unset($arFields["TEMPLATE"]);
	}

	$res = false;
	$ber = true;
	if ($bNew && $_POST["START_SITE_WIZARD"] == "Y")
	{
		if (!array_key_exists("START_SITE_WIZARD_REWRITE", $_POST) || $_POST["START_SITE_WIZARD_REWRITE"] != "Y")
		{
			if (strlen($arFields["DOC_ROOT"]) > 0)
				$sr = Rel2Abs($_SERVER["DOCUMENT_ROOT"], $arFields["DOC_ROOT"]);
			else
				$sr = rtrim($_SERVER["DOCUMENT_ROOT"], "/\\");

			$ber = !file_exists($sr.$_POST["DIR"]."/index.php");

			if (!$ber)
				$APPLICATION->ThrowException(GetMessage("START_SITE_WIZARD_REWRITE_ERROR"));
		}
	}

	if ($ber)
	{
		$langs = new CLang;
		if(!$bNew)
			$res = $langs->Update($LID, $arFields);
		else
			$res = (strlen($langs->Add($arFields))>0);
	}

	if(!$res)
	{
		$bVarsFromForm = true;
	}
	else
	{
		if($SITE_MESSAGE_LINK=="C" && strlen($SITE_MESSAGE_LINK_C_SITE)>0)
		{
			$db_msg = CEventMessage::GetList($o, $b, Array("SITE_ID"=>$SITE_MESSAGE_LINK_C_SITE));
			while($ar_msg = $db_msg->Fetch())
			{
				unset($ar_msg["TIMESTAMP_X"]);
				$ar_msg["LID"] = $LID;
				$em->Add($ar_msg);
			}
		}
		elseif($SITE_MESSAGE_LINK=="E" && strlen($SITE_MESSAGE_LINK_E_SITE)>0)
		{
			$db_msg = CEventMessage::GetList($o, $b, Array("SITE_ID"=>$SITE_MESSAGE_LINK_E_SITE));
			while($ar_msg = $db_msg->Fetch())
			{
				$msg_id = $ar_msg["ID"];
				$db_msg_sites = CEventMessage::GetSite($ar_msg["ID"]);
				$ar_msg = Array(
						"NAME"=>$ar_msg["NAME"],
						"LID"=>Array($LID)
						);

				while($ar_msg_sites = $db_msg_sites->Fetch())
					$ar_msg["LID"][] = $ar_msg_sites["SITE_ID"];

				$em->Update($msg_id, $ar_msg);
			}
		}

		if ($bNew && $_POST["START_SITE_WIZARD"] == "Y")
		{
			$rsSite = CSite::GetList($by="sort", $order="asc", array("ID" => $LID));
			$arSite = $rsSite->GetNext();

			$p = CSite::GetSiteDocRoot($LID).$arSite["DIR"];
			CheckDirPath($p);

			$indexContent = '<'.'?'.
				'define("B_PROLOG_INCLUDED", true);'.
				'define("WIZARD_DEFAULT_SITE_ID", "'.$LID.'");'.
				'define("WIZARD_DEFAULT_TONLY", true);'.
				'define("PRE_LANGUAGE_ID","'.$arSite["LANGUAGE_ID"].'");'.
				'define("PRE_INSTALL_CHARSET","'.$arSite["CHARSET"].'");'.
				'include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard/wizard.php");'.
				'?'.'>';

			$handler = fopen($p."/index.php","wb");
			fwrite($handler, $indexContent);
			fclose($handler);

			$u = "";
			if (is_array($arSite["DOMAINS"]) && strlen($arSite["DOMAINS"][0]) > 0 || strlen($arSite["DOMAINS"]) > 0)
				$u .= "http://";
			if (is_array($arSite["DOMAINS"]))
				$u .= $arSite["DOMAINS"][0];
			else
				$u .= $arSite["DOMAINS"];
			$u .= $arSite["DIR"];

			LocalRedirect($u);
		}

		if (strlen($save)>0)
			LocalRedirect(BX_ROOT."/admin/site_admin.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/site_edit.php?lang=".LANGUAGE_ID."&LID=".$LID."&".$tabControl->ActiveTabParam());
	}
}

if($bNew && $COPY_ID == '')
{
	$str_ACTIVE = 'Y';
	$str_SORT = '1';
	$str_DIR = '/';
	$str_FORMAT_DATE = (LANGUAGE_ID == 'ru'? 'DD.MM.YYYY' : 'MM/DD/YYYY');
	if (LANGUAGE_ID == 'ru')
		$str_FORMAT_DATETIME = 'DD.MM.YYYY HH:MI:SS';
	elseif (LANGUAGE_ID == 'en')
		$str_FORMAT_DATETIME = 'MM/DD/YYYY H:MI T';
	else
		$str_FORMAT_DATETIME = 'DD.MM.YYYY HH:MI:SS';

	$str_FORMAT_NAME = CSite::GetDefaultNameFormat();
	$str_WEEK_START = GetMessage('SITE_EDIT_WEEK_START_DEFAULT');
	if (!$str_WEEK_START && $str_WEEK_START !== '0')
		$str_WEEK_START = 1;
	$str_WEEK_START = intval($str_WEEK_START);

	$str_CHARSET = (defined('BX_UTF')? 'UTF-8' : (LANGUAGE_ID == 'ru'? 'Windows-1251' : 'ISO-8859-1'));
}

if($COPY_ID <> '')
{
	$LID = $COPY_ID;
	$lng = CSite::GetByID($COPY_ID);
	if(!$lng->ExtractFields("str_"))
		$bNew = true;
}
elseif(!$bNew)
{
	$lng = CSite::GetByID($LID);
	if(!$lng->ExtractFields("str_"))
		$bNew = true;
}

if($bVarsFromForm)
{
	$DB->InitTableVarsForEdit("b_lang", "", "str_");
	$str_DOMAINS = htmlspecialcharsbx($DOMAINS);
	$str_SERVER_NAME = htmlspecialcharsbx($_POST["SERVER_NAME"]);
	$str_FORMAT_NAME = CSite::GetNameFormatByValue($_POST["FORMAT_NAME"]);
}

$APPLICATION->SetTitle(($bNew? GetMessage("NEW_SITE_TITLE") : GetMessage("EDIT_SITE_TITLE", array("#ID#"=>$str_LID))));

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

if($bNew)
{
	$sites_cnt = 0;
	$r = CSite::GetList($o1, $b1, Array("ACTIVE"=>"Y"));
	while($r->Fetch())
		$sites_cnt++;
}

$aMenu = array(
	array(
		"TEXT"	=> GetMessage("RECORD_LIST"),
		"LINK"	=> "/bitrix/admin/site_admin.php?lang=".LANGUAGE_ID."&set_default=Y",
		"TITLE"	=> GetMessage("RECORD_LIST_TITLE"),
		"ICON"	=> "btn_list"
	)
);

if(!$bNew)
{
	$aMenu[] = array("SEPARATOR"=>"Y");

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_NEW_RECORD"),
		"LINK"	=> "/bitrix/admin/site_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> GetMessage("MAIN_NEW_RECORD_TITLE"),
		"ICON"	=> "btn_new"
		);

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_COPY_RECORD"),
		"LINK"	=> "/bitrix/admin/site_edit.php?lang=".LANGUAGE_ID."&amp;COPY_ID=".urlencode($str_LID),
		"TITLE"	=> GetMessage("MAIN_COPY_RECORD_TITLE"),
		"ICON"	=> "btn_copy"
		);

	$aMenu[] = array(
		"TEXT"	=> GetMessage("MAIN_DELETE_RECORD"),
		"LINK"	=> "javascript:if(confirm('".CUtil::JSEscape(GetMessage("MAIN_DELETE_RECORD_CONF"))."')) window.location='/bitrix/admin/site_admin.php?ID=".urlencode($str_LID)."&lang=".LANGUAGE_ID."&action=delete&".bitrix_sessid_get()."';",
		"TITLE"	=> GetMessage("MAIN_DELETE_RECORD_TITLE"),
		"ICON"	=> "btn_delete"
		);
}

$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($e = $APPLICATION->GetException())
	$message = new CAdminMessage(GetMessage("MAIN_ERROR_SAVING"), $e);

if($message)
	echo $message->Show();

$limitSitesCount = IntVal(COption::GetOptionInt("main", "PARAM_MAX_SITES", 100));
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="bform" <?if($bNew && $limitSitesCount > 0 && $limitSitesCount <= $sites_cnt)echo ' OnSubmit="alert(\''.GetMessage("SITE_EDIT_WARNING_MAX").'\')"';?>>
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<?if($bNew):?>
<input type="hidden" name="new" value="Y">
<?endif?>
<?if($COPY_ID <> ''):?>
<input type="hidden" name="COPY" value="<?echo htmlspecialcharsbx($COPY_ID)?>">
<?endif?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
	<tr class="adm-detail-required-field">
		<td width="40%">ID:</td>
		<td width="60%"><?
			if(!$bNew):
				echo $str_LID;
				?><input type="hidden" name="LID" value="<? echo $str_LID?>"><?
			else:
				?><input type="text" name="LID" size="2" maxlength="2" value="<? echo $str_LID?>"><?
			endif;
				?></td>
	</tr>
	<tr>
		<td><label for="ACTIVE"><?echo GetMessage('ACTIVE')?></label></td>
		<td><input type="checkbox" name="ACTIVE" value="Y" id="ACTIVE"<?if($str_ACTIVE=="Y")echo " checked"?>></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage('NAME')?></td>
		<td><input type="text" name="NAME" size="30" maxlength="50" value="<? echo $str_NAME?>"></td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_SITE_DEFINITIONS")?></td>
	</tr>
	<tr>
		<td><label for="DEF"><?echo GetMessage('DEF')?></label></td>
		<td><input type="checkbox" name="DEF" value="Y" id="DEF"<?if($str_DEF=="Y")echo " checked"?>></td>
	</tr>
	<tr>
		<td class="adm-detail-valign-top"><?echo GetMessage("MAIN_SITE_DOMAIN")?><br>
		<?echo GetMessage("MAIN_SITE_EDIT_DOMAINS")?>
		</td>
		<td><textarea name="DOMAINS" cols="40" rows="5"><? echo $str_DOMAINS?></textarea>
		<?=BeginNote();?>
		<?echo GetMessage("MAIN_SITE_EDIT_DOMAINS_HELP")?>
		<?=EndNote();?>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('DIR')?></td>
		<td><input type="text" name="DIR" size="30" maxlength="50" value="<? echo $str_DIR?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage('SORT')?></td>
		<td><input type="text" name="SORT" size="10" maxlength="10" value="<? echo $str_SORT?>"></td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_SITE_PARAMS")?></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?echo GetMessage("MAIN_SITE_LANG")?></td>
		<td><?echo CLanguage::SelectBox("LANGUAGE_ID", $str_LANGUAGE_ID);?></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('FORMAT_DATE')?></td>
		<td><input type="text" name="FORMAT_DATE" size="30" maxlength="50" value="<? echo $str_FORMAT_DATE?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('FORMAT_DATETIME')?></td>
		<td><input type="text" name="FORMAT_DATETIME" size="30" maxlength="50" value="<?echo $str_FORMAT_DATETIME?>"></td>
	</tr>
	<tr>
		<td><? echo GetMessage('SITE_EDIT_WEEK_START')?></td>
		<td><select name="WEEK_START">
<?
for ($i = 0; $i < 7; $i++)
{
	echo '<option value="'.$i.'"'.($i == $str_WEEK_START ? ' selected="selected"' : '').'>'.GetMessage('DAY_OF_WEEK_' .$i).'</option>';
}
?>
		</select></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('FORMAT_NAME')?></td>
		<td><?echo CSite::SelectBoxName("FORMAT_NAME", $str_FORMAT_NAME);?></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><? echo GetMessage('CHARSET')?></td>
		<td><input type="text" name="CHARSET" size="30" maxlength="50" value="<?echo $str_CHARSET?>"></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_SITE_NAME")?></td>
		<td><input type="text" name="SITE_NAME" size="30" maxlength="50" value="<?echo $str_SITE_NAME?>"></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_SERVER_URL")?></td>
		<td><input type="text" name="SERVER_NAME" size="30" maxlength="50" value="<?echo $str_SERVER_NAME?>"></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DEFAULT_EMAIL")?></td>
		<td><input type="text" name="EMAIL" size="30" maxlength="50" value="<?echo $str_EMAIL?>"></td>
	</tr>
	<tr>
		<td><?echo GetMessage("MAIN_DOC_ROOT")?><br />
		<?echo GetMessage("MAIN_DOC_ROOT_TIPS")?>
		</td>
		<td><input type="text" name="DOC_ROOT" size="30" value="<?echo $str_DOC_ROOT?>">
		<a title="<?=GetMessage('MAIN_DOC_ROOT_INS')?>" href="javascript:void(0)" onClick="document.bform.DOC_ROOT.value='<?=htmlspecialcharsbx(CUtil::addslashes($_SERVER["DOCUMENT_ROOT"]))?>'; BX.fireEvent(document.bform.DOC_ROOT, 'change')"><?echo GetMessage("MAIN_DOC_ROOT_SET")?></a>
		</td>
	</tr>
	<?if($bNew):?>
	<tr>
		<td class="adm-detail-valign-top"><?echo GetMessage("MAIN_SITE_CREATE_MESS_TEPL")?></td>
		<td>
			<input type="radio"<?if($SITE_MESSAGE_LINK!="E" && $SITE_MESSAGE_LINK!="C") echo " checked"?> name="SITE_MESSAGE_LINK" value="N" id="SITE_MESSAGE_LINK_n" onClick="if(this.checked){document.bform.SITE_MESSAGE_LINK_E_SITE.disabled=true; document.bform.SITE_MESSAGE_LINK_C_SITE.disabled=true}"><label for="SITE_MESSAGE_LINK_n"> <?echo GetMessage("MAIN_SITE_CREATE_MESS_TEPL_N")?></label><br>
			<input type="radio"<?if($SITE_MESSAGE_LINK=="E") echo " checked"?> name="SITE_MESSAGE_LINK" id="SITE_MESSAGE_LINK_e" value="E" onClick="if(this.checked){document.bform.SITE_MESSAGE_LINK_C_SITE.disabled=true; document.bform.SITE_MESSAGE_LINK_E_SITE.disabled=false}"><label for="SITE_MESSAGE_LINK_e"> <?echo GetMessage("MAIN_SITE_CREATE_MESS_TEPL_LINK")?></label><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=CSite::SelectBox("SITE_MESSAGE_LINK_E_SITE", $SITE_MESSAGE_LINK_E_SITE, "", "", ($SITE_MESSAGE_LINK!="E"?'disabled':''));?><br>
			<input type="radio"<?if($SITE_MESSAGE_LINK=="C") echo " checked"?> name="SITE_MESSAGE_LINK" id="SITE_MESSAGE_LINK_c" value="C" onClick="if(this.checked){document.bform.SITE_MESSAGE_LINK_E_SITE.disabled=true; document.bform.SITE_MESSAGE_LINK_C_SITE.disabled=false}"><label for="SITE_MESSAGE_LINK_c"> <?echo GetMessage("MAIN_SITE_CREATE_MESS_TEPL_COPY")?></label><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=CSite::SelectBox("SITE_MESSAGE_LINK_C_SITE", $SITE_MESSAGE_LINK_C_SITE, "", "", ($SITE_MESSAGE_LINK!="C"?'disabled':''));?><br />

		</td>
	</tr>
	<?endif?>
	<?ConditionJS();?>
	<tr class="heading">
		<td colspan="2"><?echo GetMessage("MAIN_SITE_EDIT_TEMPLATE")?></td>
	</tr>
	<?if ($bNew):?>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="radio" name="START_SITE_WIZARD" value="Y"<?= ($str_START_SITE_WIZARD != "N") ? " checked" : "" ?> onclick="TurnStartSiteWizardOn(false)" id="ID_START_SITE_WIZARD_Y"> <label for="ID_START_SITE_WIZARD_Y"><?= GetMessage("M_START_SITE_WIZARD_Y") ?></label><br />
			<input type="radio" name="START_SITE_WIZARD" value="N"<?= ($str_START_SITE_WIZARD == "N") ? " checked" : "" ?> onclick="TurnStartSiteWizardOn(true)" id="ID_START_SITE_WIZARD_N"> <label for="ID_START_SITE_WIZARD_N"><?= GetMessage("M_START_SITE_WIZARD_N") ?></label><br />
			<script language="JavaScript">
			<!--
				function TurnStartSiteWizardOn(bOn)
				{
					document.getElementById("ID_HIDDENABLE_TR").style.display = (bOn ? "" : "none");
					document.getElementById("ID_START_SITE_WIZARD_REWRITE").disabled = (bOn ? true : false);
				}
			//-->
			</script>
			<input type="checkbox" name="START_SITE_WIZARD_REWRITE" value="Y" id="ID_START_SITE_WIZARD_REWRITE"><label for="ID_START_SITE_WIZARD_REWRITE"><?= GetMessage("M_START_SITE_WIZARD_REWRITE") ?></label>
		</td>
	</tr>
	<?endif;?>
	<tr id="ID_HIDDENABLE_TR"<?= ($bNew && ($str_START_SITE_WIZARD != "N")) ? "style='display:none'" : ""?>>
		<td colspan="2" align="center">
			<table border="0" cellspacing="0" cellpadding="0" class="internal">
			<tr class="heading">
				<td align="center" colspan=2><?echo GetMessage("MAIN_SITE_EDIT_TEMPL")?></td>
				<td align="center"><?echo GetMessage("MAIN_SITE_EDIT_SORT")?></td>
				<td align="center"><?echo GetMessage("MAIN_SITE_EDIT_TYPE")?></td>
				<td align="center"><?echo GetMessage("MAIN_SITE_EDIT_COND")?></td>
			</tr>
			<?
			$dbSiteRes = CSite::GetTemplateList($LID);
			if(!$bVarsFromForm)
			{
				$SITE_TEMPLATE = Array();
				$max_sort = 0;
				while($arSiteRes = $dbSiteRes->Fetch())
				{
					$SITE_TEMPLATE[$arSiteRes["ID"]] = $arSiteRes;
					if($max_sort<$arSiteRes["SORT"])
						$max_sort = $arSiteRes["SORT"];
				}
				for($i=0; $i<3; $i++)
					$SITE_TEMPLATE["N".$i] = Array("SORT"=>$max_sort+1+$i);
			}
			else
			{
				$SITE_TEMPLATE = Array();
				foreach($_POST["SITE_TEMPLATE"] as $key=>$val)
				{
					if ($USER->CanDoOperation('edit_php') || $_POST['selected_type'][$key] != 'php')
					{
						$cond = ConditionCompose($val, $key);
					}
					else
					{
						if(isset($arTemplates[$key]))
							$cond = $arTemplates[$key];
						else
							continue;
					}

					$SITE_TEMPLATE[$key] = array(
						"TEMPLATE" => $val['TEMPLATE'],
						"SORT" => $val['SORT'],
						"CONDITION" => $cond
					);
				}

			}

			//templates
			$arSiteTemplates = array();
			$db_res = CSiteTemplate::GetList();
			while($arRes = $db_res->GetNext())
				$arSiteTemplates[] = $arRes;

			foreach($SITE_TEMPLATE as $i=>$val):
				ConditionParse($val['CONDITION']);
			?>
			<tr>
				<td>
					<select name="SITE_TEMPLATE[<?=$i?>][TEMPLATE]" id="SITE_TEMPLATE[<?=$i?>][TEMPLATE]">
						<option value=""><?echo GetMessage("SITE_EDIT_TEMPL_NO")?></option>
						<?foreach($arSiteTemplates as $arRes):?>
						<option value="<?=$arRes["ID"]?>"<?if($val["TEMPLATE"]==$arRes["ID"])echo " selected"?>><?=$arRes["NAME"]?></option>
						<?endforeach;?>
					</select>
				</td>
				<td><a title="<?=GetMessage('MAIN_PREVIEW_TEMPLATE')?>" href="javascript:void(0)" onClick="if(document.getElementById('SITE_TEMPLATE[<?=$i?>][TEMPLATE]').selectedIndex>0)window.open((document.bform.SERVER_NAME.value?'http://'+document.bform.SERVER_NAME.value:'') + document.bform.DIR.value+'?bitrix_preview_site_template='+document.getElementById('SITE_TEMPLATE[<?=$i?>][TEMPLATE]')[document.getElementById('SITE_TEMPLATE[<?=$i?>][TEMPLATE]').selectedIndex].value);return false;"><img src="/bitrix/images/main/preview.gif" width="16" height="16" border="0"></a></td>
				<td><input type="text" size="2" name="SITE_TEMPLATE[<?=$i?>][SORT]" value="<?=htmlspecialcharsex($val["SORT"])?>"></td>
				<td><?ConditionSelect($i);?></td>
				<td style="width:100%" align="left"><?
ConditionShow(array(
	"i"		=>$i,
	"field_name"	=>"SITE_TEMPLATE[$i]",
	"form"		=>"bform"
		)
	);
			?></td>
			</tr>
			<?endforeach;?>
			</table>
		</td>
	</tr>


<?$tabControl->Buttons(array("disabled" => !$isAdmin, "back_url"=>"site_admin.php?lang=".LANGUAGE_ID));
$tabControl->End();
$tabControl->ShowWarnings("bform", $message);
?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
