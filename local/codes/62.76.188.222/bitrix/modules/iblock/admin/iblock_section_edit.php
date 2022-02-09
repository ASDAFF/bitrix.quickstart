<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

$strWarning = "";
$bVarsFromForm = false;
$message = false;
$ID = intval($ID);
$IBLOCK_SECTION_ID = intval($IBLOCK_SECTION_ID);
$IBLOCK_ID = intval($IBLOCK_ID);

$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
if($arIBlock)
	$bBadBlock = !(CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $ID, "iblock_admin_display")
			&& (
				CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $ID, "section_read")
				|| CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_SECTION_ID, "section_section_bind")
			)
		);
else
	$bBadBlock = true;

if(!$bBadBlock)
{
	$arIBTYPE = CIBlockType::GetByIDLang($arIBlock['IBLOCK_TYPE_ID'], LANGUAGE_ID);
	if($arIBTYPE === false)
		$bBadBlock = true;
	else
		$type = $arIBlock['IBLOCK_TYPE_ID'];
}

if($bBadBlock)
{
	$APPLICATION->SetTitle($arIBTYPE["NAME"]);
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	?>
	<?echo ShowError(GetMessage("IBSEC_E_BAD_IBLOCK"));?>
	<a href="iblock_admin.php?lang=<?=LANG?>&amp;type=<?=urlencode($type)?>"><?echo GetMessage("IBSEC_E_BACK_TO_ADMIN")?></a>
	<?
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$APPLICATION->AddHeadScript('/bitrix/js/iblock/iblock_edit.js');

if(!$arIBlock["SECTION_NAME"])
	$arIBlock["SECTION_NAME"] = $arIBTYPE["SECTION_NAME"]? $arIBTYPE["SECTION_NAME"]: GetMessage("IBLOCK_SECTION");

$bEditRights = $arIBlock["RIGHTS_MODE"] === "E" && CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $ID, "section_rights_edit");

$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => $arIBlock["SECTION_NAME"],
		"ICON" => "iblock_section",
		"TITLE" => htmlspecialcharsbx($ID > 0 ? $arIBlock["SECTION_EDIT"] : $arIBlock["SECTION_ADD"]),
	),
	array(
		"DIV" => "edit2",
		"TAB" => GetMessage("IBSEC_E_TAB2"),
		"ICON" => "iblock_section",
		"TITLE" => GetMessage("IBSEC_E_TAB2_TITLE"),
	),
);
//Add user fields tab only when there is fields defined or user has rights for adding new field
if(
	(count($USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$IBLOCK_ID."_SECTION")) > 0) ||
	($USER_FIELD_MANAGER->GetRights("IBLOCK_".$IBLOCK_ID."_SECTION") >= "W")
)
{
	$aTabs[] = $USER_FIELD_MANAGER->EditFormTab("IBLOCK_".$IBLOCK_ID."._SECTION");
}

if($bEditRights)
{
	$aTabs[] = array(
		"DIV" => "edit3",
		"TAB" => GetMessage("IBSEC_E_TAB_RIGHTS"),
		"ICON" => "iblock_section",
		"TITLE" => GetMessage("IBSEC_E_TAB_RIGHTS_TITLE"),
	);
}

if($arIBlock["SECTION_PROPERTY"] === "Y" && defined("CATALOG_PRODUCT"))
{
	$aTabs[] = array(
		"DIV" => "edit4",
		"TAB" => GetMessage("IBSEC_E_PROPERTY_TAB"),
		"ICON" => "iblock_section",
		"TITLE" => GetMessage("IBSEC_E_PROPERTY_TAB_TITLE"),
	);
}

$tabControl = new CAdminForm("form_section_".$IBLOCK_ID, $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && check_bitrix_sessid())
{
	$DB->StartTransaction();
	$bs = new CIBlockSection;

	if(array_key_exists("PICTURE", $_FILES))
		$arPICTURE = $_FILES["PICTURE"];
	elseif(isset($_REQUEST["PICTURE"]))
	{
		$arPICTURE = CFile::MakeFileArray($_REQUEST["PICTURE"]);
		$arPICTURE["COPY_FILE"] = "Y";
	}
	else
		$arPICTURE = array();

	$arPICTURE["del"] = ${"PICTURE_del"};

	if(array_key_exists("DETAIL_PICTURE", $_FILES))
		$arDETAIL_PICTURE = $_FILES["DETAIL_PICTURE"];
	elseif(isset($_REQUEST["DETAIL_PICTURE"]))
	{
		$arDETAIL_PICTURE = CFile::MakeFileArray($_REQUEST["DETAIL_PICTURE"]);
		$arDETAIL_PICTURE["COPY_FILE"] = "Y";
	}
	else
		$arDETAIL_PICTURE = array();

	$arDETAIL_PICTURE["del"] = ${"DETAIL_PICTURE_del"};

	$arFields = array(
		"ACTIVE" => $ACTIVE,
		"IBLOCK_SECTION_ID" => $IBLOCK_SECTION_ID,
		"IBLOCK_ID" => $IBLOCK_ID,
		"NAME" => $NAME,
		"SORT" => $SORT,
		"CODE" => $_POST["CODE"],
		"PICTURE" => $arPICTURE,
		"DETAIL_PICTURE" => $arDETAIL_PICTURE,
		"DESCRIPTION" => $DESCRIPTION,
		"DESCRIPTION_TYPE" => $DESCRIPTION_TYPE,
	);

	if(isset($_POST["SECTION_PROPERTY"]) && is_array($_POST["SECTION_PROPERTY"]))
	{
		$arFields["SECTION_PROPERTY"] = array();
		foreach($_POST["SECTION_PROPERTY"] as $PID => $arLink)
			if($arLink["SHOW"] === "Y")
				$arFields["SECTION_PROPERTY"][$PID] = array(
					"SMART_FILTER" => $arLink["SMART_FILTER"],
				);
	}

	if($bEditRights && $arIBlock["RIGHTS_MODE"] === "E")
	{
		if(is_array($_POST["RIGHTS"]))
			$arFields["RIGHTS"] = CIBlockRights::Post2Array($_POST["RIGHTS"]);
		else
			$arFields["RIGHTS"] = array();
	}

	$USER_FIELD_MANAGER->EditFormAddFields("IBLOCK_".$IBLOCK_ID."_SECTION", $arFields);

	if(COption::GetOptionString("iblock", "show_xml_id", "N")=="Y" && is_set($_POST, "XML_ID"))
		$arFields["XML_ID"] = $_POST["XML_ID"];

	if($ID>0)
	{
		$res = $bs->Update($ID, $arFields, true, true, true);
	}
	else
	{
		$ID = $bs->Add($arFields, true, true, true);
		$res = ($ID>0);
	}

	if(!$res)
	{
		$strWarning .= $bs->LAST_ERROR;
		$bVarsFromForm = true;
		$DB->Rollback();
		if($e = $APPLICATION->GetException())
			$message = new CAdminMessage(GetMessage("admin_lib_error"), $e);
	}
	else
	{
		$DB->Commit();
		if(strlen($apply) <= 0 && strlen($save_and_add) <= 0)
		{
			if(strlen($return_url)>0)
			{
				if(strpos($return_url, "#")!==false)
				{
					$rsSection = CIBlockSection::GetList(array(), array("ID" => $ID), false, array("SECTION_PAGE_URL"));
					$arSection = $rsSection->Fetch();
					if($arSection)
						$return_url = CIBlock::ReplaceDetailUrl($return_url, $arSection, true, "S");
				}
				LocalRedirect($return_url);
			}
			else
			{
				LocalRedirect("/bitrix/admin/".CIBlock::GetAdminSectionListLink($IBLOCK_ID, array('find_section_section'=>intval($find_section_section))));
			}
		}
		elseif(strlen($save_and_add) > 0)
		{
			if (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1)
			{
				while(ob_end_clean());
				?>
					<script type="text/javascript">
						top.BX.ajax.post(
							'/bitrix/admin/<?echo $l = CUtil::JSEscape(CIBlock::GetAdminSectionEditLink($IBLOCK_ID, 0, array(
								"find_section_section" => intval($find_section_section),
								"return_url" => (strlen($return_url) > 0? $return_url: null),
								"from_module" => "iblock",
								"bxpublic" => "Y",
								"nobuttons" => "Y",
							), "&".$tabControl->ActiveTabParam()))?>',
							{},
							function (result) {
								top.BX.closeWait();
								top.window.reloadAfterClose = true;
								top.BX.WindowManager.Get().SetContent(result);
							}
						);
					</script>';
				<?
				die();
			}
			else
			{
				LocalRedirect("/bitrix/admin/".CIBlock::GetAdminSectionEditLink($IBLOCK_ID, 0, array(
					"find_section_section" => intval($find_section_section),
					"return_url" => (strlen($return_url) > 0? $return_url: null),
				), "&".$tabControl->ActiveTabParam()));
			}
		}
		else
		{
			LocalRedirect("/bitrix/admin/".CIBlock::GetAdminSectionEditLink($IBLOCK_ID, $ID, array(
				'find_section_section' => intval($find_section_section),
				'return_url' => strlen($return_url) > 0? $return_url: null,
			), "&".$tabControl->ActiveTabParam()));
		}
	}
}

ClearVars("str_");
$str_ACTIVE="Y";
$str_NAME = htmlspecialcharsbx($arIBlock["FIELDS"]["SECTION_NAME"]["DEFAULT_VALUE"]);
$str_DESCRIPTION_TYPE = $arIBlock["FIELDS"]["SECTION_DESCRIPTION_TYPE"]["DEFAULT_VALUE"] !== "html"? "text": "html";
$str_DESCRIPTION = htmlspecialcharsbx($arIBlock["FIELDS"]["SECTION_DESCRIPTION"]["DEFAULT_VALUE"]);
$str_SORT="500";
$str_IBLOCK_SECTION_ID = $IBLOCK_SECTION_ID;

$result = CIBlockSection::GetByID($ID);
$arSection = $result->ExtractFields("str_");
if(!$arSection)
	$ID=0;

if($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_iblock_section", "", "str_");

if($ID>0)
	$APPLICATION->SetTitle(GetMessage("IBSEC_E_EDIT_TITLE", array("#IBLOCK_NAME#"=>$arIBlock["NAME"], "#SECTION_TITLE#"=>$arIBlock["SECTION_NAME"])));
else
	$APPLICATION->SetTitle(GetMessage("IBSEC_E_NEW_TITLE", array("#IBLOCK_NAME#"=>$arIBlock["NAME"], "#SECTION_TITLE#"=>$arIBlock["SECTION_NAME"])));

if(!defined("CATALOG_PRODUCT"))
{
	$adminChain->AddItem(array(
		"TEXT" => htmlspecialcharsex($arIBlock["NAME"]),
		"LINK" => htmlspecialcharsbx(CIBlock::GetAdminSectionListLink($IBLOCK_ID, array(
			'find_section_section' => 0,
		))),
	));
	if ($find_section_section > 0)
	{
		$nav = CIBlockSection::GetNavChain($IBLOCK_ID, IntVal($find_section_section));
		while ($ar_nav = $nav->GetNext())
		{
			$last_nav = CIBlock::GetAdminSectionListLink($IBLOCK_ID, array(
				'find_section_section' => $ar_nav["ID"],
			));
			$adminChain->AddItem(array(
				"TEXT" => $ar_nav["NAME"],
				"LINK" => htmlspecialcharsbx($last_nav),
			));
		}
	}
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$aMenu = array(
	array(
		"TEXT" => htmlspecialcharsbx($arIBlock["SECTIONS_NAME"]),
		"LINK" => CIBlock::GetAdminSectionListLink($IBLOCK_ID, array(
			'find_section_section' => intval($find_section_section),
		)),
		"ICON" => "btn_list",
	),
);

if ($ID > 0)
{
	$aMenu[] = array(
		"SEPARATOR" => "Y",
	);
	$aMenu[] = array(
		"TEXT" => htmlspecialcharsbx($arIBlock["SECTION_ADD"]),
		"LINK" => CIBlock::GetAdminSectionEditLink($IBLOCK_ID, null, array("find_section_section" => intval($find_section_section), "IBLOCK_SECTION_ID" => ($IBLOCK_SECTION_ID > 0 ? $IBLOCK_SECTION_ID : $find_section_section))),
		"ICON" => "btn_new",
	);
	$urlDelete = CIBlock::GetAdminSectionListLink($IBLOCK_ID, array(
		'find_section_section' => intval($find_section_section),
		'action' => 'delete',
	));
	$urlDelete.= '&'.bitrix_sessid_get();
	$urlDelete.= '&ID[]='.(preg_match('/^iblock_list_admin\.php/', $urlDelete) ? "S" : "").$ID;
	$aMenu[] = array(
		"TEXT" => htmlspecialcharsbx($arIBlock["SECTION_DELETE"]),
		"LINK" => "javascript:if(confirm('".GetMessage("IBSEC_E_CONFIRM_DEL_MESSAGE")."'))window.location='".CUtil::JSEscape($urlDelete)."'",
		"ICON" => "btn_delete",
	);
}

$context = new CAdminContextMenu($aMenu);
$context->Show();

if($strWarning)
	CAdminMessage::ShowOldStyleError($strWarning."<br>");
elseif($message)
	echo $message->Show();

//We have to explicitly call calendar and editor functions because
//first output may be discarded by form settings
$bFileman = CModule::IncludeModule("fileman");

$arTranslit = $arIBlock["FIELDS"]["SECTION_CODE"]["DEFAULT_VALUE"];
$bLinked = !$ID && $_POST["linked_state"]!=='N';

$tabControl->BeginPrologContent();
if(method_exists($USER_FIELD_MANAGER, 'showscript'))
	echo $USER_FIELD_MANAGER->ShowScript();
echo CAdminCalendar::ShowScript();
if(COption::GetOptionString("iblock", "use_htmledit", "Y")=="Y" && $bFileman)
{
	//TODO:This dirty hack will be replaced by special method like calendar do
	echo '<div style="display:none">';
	CFileMan::AddHTMLEditorFrame(
		"SOME_TEXT",
		"",
		"SOME_TEXT_TYPE",
		"text",
		array(
			'height' => 450,
			'width' => '100%'
		),
		"N",
		0,
		"",
		"",
		$arIBlock["LID"]
	);
	echo '</div>';
}

if($arTranslit["TRANSLITERATION"] == "Y")
{
	CUtil::InitJSCore(array('translit'));
	?>
	<script>
		var linked=<?if($bLinked) echo 'true'; else echo 'false';?>;
		function set_linked()
		{
			linked=!linked;

			var name_link = document.getElementById('name_link');
			if(name_link)
			{
				if(linked)
					name_link.src='/bitrix/themes/.default/icons/iblock/link.gif';
				else
					name_link.src='/bitrix/themes/.default/icons/iblock/unlink.gif';
			}
			var code_link = document.getElementById('code_link');
			if(code_link)
			{
				if(linked)
					code_link.src='/bitrix/themes/.default/icons/iblock/link.gif';
				else
					code_link.src='/bitrix/themes/.default/icons/iblock/unlink.gif';
			}
			var linked_state = document.getElementById('linked_state');
			if(linked_state)
			{
				if(linked)
					linked_state.value='Y';
				else
					linked_state.value='N';
			}
		}
		var oldValue = '';
		function transliterate()
		{
			if(linked)
			{
				var from = document.getElementById('NAME');
				var to = document.getElementById('CODE');
				if(from && to && oldValue != from.value)
				{
					BX.translit(from.value, {
						'max_len' : <?echo intval($arTranslit['TRANS_LEN'])?>,
						'change_case' : '<?echo $arTranslit['TRANS_CASE']?>',
						'replace_space' : '<?echo $arTranslit['TRANS_SPACE']?>',
						'replace_other' : '<?echo $arTranslit['TRANS_OTHER']?>',
						'delete_repeat_replace' : <?echo $arTranslit['TRANS_EAT'] == 'Y'? 'true': 'false'?>,
						'use_google' : <?echo $arTranslit['USE_GOOGLE'] == 'Y'? 'true': 'false'?>,
						'callback' : function(result){to.value = result; setTimeout('transliterate()', 250);}
					});
					oldValue = from.value;
				}
				else
				{
					setTimeout('transliterate()', 250);
				}
			}
			else
			{
				setTimeout('transliterate()', 250);
			}
		}
		transliterate();
	</script>
	<?
}

$tabControl->EndPrologContent();

$tabControl->BeginEpilogContent();
?>
<?=bitrix_sessid_post()?>
<?echo GetFilterHiddens("find_");?>
<input type="hidden" name="linked_state" id="linked_state" value="<?if($bLinked) echo 'Y'; else echo 'N';?>">
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="ID" value="<?echo $ID?>">
<?if(strlen($return_url)>0):?>
	<input type="hidden" name="return_url" value="<?=htmlspecialcharsbx($return_url)?>">
<?endif?>
<?
$tabControl->EndEpilogContent();

$tabControl->Begin(array(
	"FORM_ACTION" => "/bitrix/admin/".CIBlock::GetAdminSectionEditLink($IBLOCK_ID, null, array("find_section_section" => intval($find_section_section))),
));
$tabControl->BeginNextFormTab();
?>
	<?$tabControl->BeginCustomField("ID", "ID:");?>
<?if($ID>0):?>
	<tr>
		<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?></td>
		<td width="60%"><?echo $str_ID?></td>
	</tr>
<?endif;?>
	<?$tabControl->EndCustomField("ID", '');?>

	<?$tabControl->BeginCustomField("DATE_CREATE", GetMessage("IBSEC_E_CREATED"));?>
<?if($ID>0):?>
		<?if(strlen($str_DATE_CREATE) > 0):?>
			<tr>
				<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?></td>
				<td width="60%"><?echo $str_DATE_CREATE?><?
				if(intval($str_CREATED_BY)>0):
					?>&nbsp;&nbsp;&nbsp;[<a href="user_edit.php?lang=<?=LANG?>&amp;ID=<?=$str_CREATED_BY?>"><?echo $str_CREATED_BY?></a>]<?
					$rsUser = CUser::GetByID($str_CREATED_BY);
					$arUser = $rsUser->GetNext();
					if($arUser):
						echo "&nbsp;(".$arUser["LOGIN"].") ".$arUser["NAME"]." ".$arUser["LAST_NAME"];
					endif;
				endif;
				?></td>
			</tr>
		<?endif;?>
<?endif;?>
	<?$tabControl->EndCustomField("DATE_CREATE", '');?>

	<?$tabControl->BeginCustomField("TIMESTAMP_X", GetMessage("IBSEC_E_LAST_UPDATE"));?>
<?if($ID>0):?>
	<tr>
		<td width="40%"><?echo $tabControl->GetCustomLabelHTML()?></td>
		<td width="60%"><?echo $str_TIMESTAMP_X?><?
		if(intval($str_MODIFIED_BY)>0):
			?>&nbsp;&nbsp;&nbsp;[<a href="user_edit.php?lang=<?=LANG?>&amp;ID=<?=$str_MODIFIED_BY?>"><?echo $str_MODIFIED_BY?></a>]<?
			if(intval($str_CREATED_BY) != intval($str_MODIFIED_BY))
			{
				$rsUser = CUser::GetByID($str_MODIFIED_BY);
				$arUser = $rsUser->GetNext();
			}
			if($arUser):
				echo "&nbsp;(".$arUser["LOGIN"].") ".$arUser["NAME"]." ".$arUser["LAST_NAME"];
			endif;
		endif?></td>
	</tr>
<?endif;?>
	<?$tabControl->EndCustomField("TIMESTAMP_X", '');?>

<?
$tabControl->AddCheckBoxField("ACTIVE", GetMessage("IBSEC_E_ACTIVE"), false, "Y", $str_ACTIVE=="Y");

$tabControl->BeginCustomField("IBLOCK_SECTION_ID", GetMessage("IBSEC_E_PARENT_SECTION").":");?>
	<tr>
		<td><?echo $tabControl->GetCustomLabelHTML()?></td>
		<td>
		<?$l = CIBlockSection::GetTreeList(Array("IBLOCK_ID"=>$IBLOCK_ID));?>
		<select name="IBLOCK_SECTION_ID" >
			<option value="0"><?echo GetMessage("IBLOCK_UPPER_LEVEL")?></option>
		<?
			while($a = $l->Fetch()):
				?><option value="<?echo intval($a["ID"])?>"<?if($str_IBLOCK_SECTION_ID==$a["ID"])echo " selected"?>><?echo str_repeat(".", $a["DEPTH_LEVEL"])?><?echo htmlspecialcharsbx($a["NAME"])?></option><?
			endwhile;
		?>
		</select>
		</td>
	</tr>
<?
$tabControl->EndCustomField("IBLOCK_SECTION_ID", '<input type="hidden" id="IBLOCK_SECTION_ID" name="IBLOCK_SECTION_ID" value="'.$str_IBLOCK_SECTION_ID.'">');

if($arTranslit["TRANSLITERATION"] == "Y")
{
	$tabControl->BeginCustomField("NAME", GetMessage("IBLOCK_FIELD_NAME").":", true);
	?>
		<tr id="tr_NAME">
			<td><?echo $tabControl->GetCustomLabelHTML()?></td>
			<td nowrap>
				<input type="text" size="50" name="NAME" id="NAME" maxlength="255" value="<?echo $str_NAME?>"><image id="name_link" title="<?echo GetMessage("IBSEC_E_LINK_TIP")?>" class="linked" src="/bitrix/themes/.default/icons/iblock/<?if($bLinked) echo 'link.gif'; else echo 'unlink.gif';?>" onclick="set_linked()" />
			</td>
		</tr>
	<?
	$tabControl->EndCustomField("NAME",
		'<input type="hidden" name="NAME" id="NAME" value="'.$str_NAME.'">'
	);

	$tabControl->BeginCustomField("CODE", GetMessage("IBLOCK_FIELD_CODE").":", $arIBlock["FIELDS"]["SECTION_CODE"]["IS_REQUIRED"] === "Y");
	?>
		<tr id="tr_CODE">
			<td><?echo $tabControl->GetCustomLabelHTML()?></td>
			<td nowrap>

				<input type="text" size="50" name="CODE" id="CODE" maxlength="255" value="<?echo $str_CODE?>"><image id="code_link" title="<?echo GetMessage("IBSEC_E_LINK_TIP")?>" class="linked" src="/bitrix/themes/.default/icons/iblock/<?if($bLinked) echo 'link.gif'; else echo 'unlink.gif';?>" onclick="set_linked()" />
			</td>
		</tr>
	<?
	$tabControl->EndCustomField("CODE",
		'<input type="hidden" name="CODE" id="CODE" value="'.$str_CODE.'">'
	);
}
else
{
	$tabControl->AddEditField("NAME", GetMessage("IBLOCK_FIELD_NAME").":", true, array("size" => 50, "maxlength" => 255), $str_NAME);
}

$tabControl->BeginCustomField("PICTURE", GetMessage("IBSEC_E_PICTURE"), $arIBlock["FIELDS"]["SECTION_PICTURE"]["IS_REQUIRED"] === "Y");
if($bVarsFromForm && !array_key_exists("PICTURE", $_REQUEST) && $arSection)
	$str_PICTURE = intval($arSection["PICTURE"]);
?>
	<tr>
		<td class="adm-detail-valign-top"><?echo $tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?echo CFileInput::Show("PICTURE", $str_PICTURE,
				array(
					"IMAGE" => "Y",
					"PATH" => "Y",
					"FILE_SIZE" => "Y",
					"DIMENSIONS" => "Y",
					"IMAGE_POPUP" => "Y",
					"MAX_SIZE" => array("W" => 200, "H"=>200),
				), array(
					'upload' => true,
					'medialib' => true,
					'file_dialog' => true,
					'cloud' => true,
					'del' => true,
					'description' => false,
				)
			);
			?>
		</td>
	</tr>
<?
$tabControl->EndCustomField("PICTURE", '');

$tabControl->BeginCustomField("DESCRIPTION", GetMessage("IBSEC_E_DESCRIPTION"), $arIBlock["FIELDS"]["SECTION_DESCRIPTION"]["IS_REQUIRED"] === "Y");
?>
	<tr class="heading">
		<td colspan="2"><?echo $tabControl->GetCustomLabelHTML()?></td>
	</tr>

	<?if(COption::GetOptionString("iblock", "use_htmledit", "Y")=="Y" && $bFileman):?>
	<tr>
		<td colspan="2" align="center">
			<?CFileMan::AddHTMLEditorFrame("DESCRIPTION", $str_DESCRIPTION, "DESCRIPTION_TYPE", $str_DESCRIPTION_TYPE, 300, "N", 0, "", "", $arIBlock["LID"]);?>
		</td>
	</tr>
	<?else:?>
	<tr>
		<td  ><?echo GetMessage("IBSEC_E_DESC_TYPE")?></td>
		<td >
			<input type="radio" name="DESCRIPTION_TYPE" id="DESCRIPTION_TYPE_text" value="text"<?if($str_DESCRIPTION_TYPE!="html")echo " checked"?>> <label for="DESCRIPTION_TYPE_text"><?echo GetMessage("IBSEC_E_DESC_TYPE_TEXT")?></label> /
			<input type="radio" name="DESCRIPTION_TYPE" id="DESCRIPTION_TYPE_html" value="html"<?if($str_DESCRIPTION_TYPE=="html")echo " checked"?>> <label for="DESCRIPTION_TYPE_html"><?echo GetMessage("IBSEC_E_DESC_TYPE_HTML")?></label>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<textarea cols="60" rows="15"  name="DESCRIPTION" style="width:100%"><?echo $str_DESCRIPTION?></textarea>
		</td>
	</tr>
	<?endif;
$tabControl->EndCustomField("DESCRIPTION",
	'<input type="hidden" name="DESCRIPTION" value="'.$str_DESCRIPTION.'">'.
	'<input type="hidden" name="DESCRIPTION_TYPE" value="'.$str_DESCRIPTION_TYPE.'">'
);

$tabControl->BeginNextFormTab();

$tabControl->AddEditField("SORT", GetMessage("IBLOCK_FIELD_SORT").":", false, array("size" => 7, "maxlength" => 10), $str_SORT);


if(COption::GetOptionString("iblock", "show_xml_id", "N")=="Y")
{
	$tabControl->AddEditField(
		"XML_ID",
		GetMessage("IBLOCK_FIELD_XML_ID").":",
		$arIBlock["FIELDS"]["SECTION_XML_ID"]["IS_REQUIRED"] === "Y",
		array("size" => 20, "maxlength" => 255),
		$str_XML_ID
	);
}

if($arTranslit["TRANSLITERATION"] != "Y")
{
	$tabControl->AddEditField("CODE", GetMessage("IBLOCK_FIELD_CODE").":", $arIBlock["FIELDS"]["SECTION_CODE"]["IS_REQUIRED"] === "Y", array("size" => 20, "maxlength" => 255), $str_CODE);
}

$tabControl->BeginCustomField("DETAIL_PICTURE", GetMessage("IBLOCK_FIELD_DETAIL_PICTURE").":", $arIBlock["FIELDS"]["SECTION_DETAIL_PICTURE"]["IS_REQUIRED"] === "Y");
if($bVarsFromForm && !array_key_exists("DETAIL_PICTURE", $_REQUEST) && $arSection)
	$str_DETAIL_PICTURE = intval($arSection["DETAIL_PICTURE"]);
?>
	<tr>
		<td class="adm-detail-valign-top"><?echo $tabControl->GetCustomLabelHTML()?></td>
		<td>
			<?echo CFileInput::Show("DETAIL_PICTURE", $str_DETAIL_PICTURE,
				array(
					"IMAGE" => "Y",
					"PATH" => "Y",
					"FILE_SIZE" => "Y",
					"DIMENSIONS" => "Y",
					"IMAGE_POPUP" => "Y",
					"MAX_SIZE" => array("W" => 200, "H"=>200),
				), array(
					'upload' => true,
					'medialib' => true,
					'file_dialog' => true,
					'cloud' => true,
					'del' => true,
					'description' => false,
				)
			);
			?>
		</td>
	</tr>
<?
$tabControl->EndCustomField("DETAIL_PICTURE", '');

//Add user fields tab only when there is fields defined or user has rights for adding new field
$entity_id = "IBLOCK_".$IBLOCK_ID."_SECTION";
if(
	(count($USER_FIELD_MANAGER->GetUserFields($entity_id)) > 0) ||
	($USER_FIELD_MANAGER->GetRights($entity_id) >= "W")
)
{
	$tabControl->BeginNextFormTab();

	if($USER_FIELD_MANAGER->GetRights($entity_id) >= "W")
	{
		$tabControl->BeginCustomField("USER_FIELDS_ADD", GetMessage("IBSEC_E_USER_FIELDS_ADD_HREF"));
		?>
			<tr colspan="2">
				<td align="left">
					<a href="/bitrix/admin/userfield_edit.php?lang=<?echo LANG?>&amp;ENTITY_ID=<?echo urlencode($entity_id)?>&amp;back_url=<?echo urlencode($APPLICATION->GetCurPageParam('', array('bxpublic'))."&tabControl_active_tab=user_fields_tab")?>"><?echo $tabControl->GetCustomLabelHTML()?></a>
				</td>
			</tr>
		<?
		$tabControl->EndCustomField("USER_FIELDS_ADD", '');
	}

	$arUserFields = $USER_FIELD_MANAGER->GetUserFields($entity_id, $ID, LANGUAGE_ID);
	foreach($arUserFields as $FIELD_NAME => $arUserField)
	{
		$arUserField["VALUE_ID"] = intval($ID);
		$strLabel = $arUserField["EDIT_FORM_LABEL"]? $arUserField["EDIT_FORM_LABEL"]: $arUserField["FIELD_NAME"];
		$arUserField["EDIT_FORM_LABEL"] = $strLabel;

		$tabControl->BeginCustomField($FIELD_NAME, $strLabel, $arUserField["MANDATORY"]=="Y");
			echo $USER_FIELD_MANAGER->GetEditFormHTML($bVarsFromForm, $GLOBALS[$FIELD_NAME], $arUserField);


		$form_value = $GLOBALS[$FIELD_NAME];
		if(!$bVarsFromForm)
			$form_value = $arUserField["VALUE"];
		elseif($arUserField["USER_TYPE"]["BASE_TYPE"]=="file")
			$form_value = $GLOBALS[$arUserField["FIELD_NAME"]."_old_id"];

		$hidden = IBlockGetHiddenHTML($FIELD_NAME, $form_value);

		$tabControl->EndCustomField($FIELD_NAME, $hidden);
	}
}

if($bEditRights)
{
	$tabControl->BeginNextFormTab();

	if($ID > 0)
	{
		$obSectionRights = new CIBlockSectionRights($IBLOCK_ID, $ID);
		$htmlHidden = '';
		foreach($obSectionRights->GetRights() as $RIGHT_ID => $arRight)
			$htmlHidden .= '
				<input type="hidden" name="RIGHTS[][RIGHT_ID]" value="'.htmlspecialcharsbx($RIGHT_ID).'">
				<input type="hidden" name="RIGHTS[][GROUP_CODE]" value="'.htmlspecialcharsbx($arRight["GROUP_CODE"]).'">
				<input type="hidden" name="RIGHTS[][TASK_ID]" value="'.htmlspecialcharsbx($arRight["TASK_ID"]).'">
			';
	}
	else
	{
		$obSectionRights = new CIBlockSectionRights($IBLOCK_ID, $str_IBLOCK_SECTION_ID);
		$htmlHidden = '';
	}

	$tabControl->BeginCustomField("RIGHTS", GetMessage("IBSEC_E_RIGHTS_FIELD"));
		IBlockShowRights(
			'section',
			$IBLOCK_ID,
			$ID,
			GetMessage("IBSEC_E_RIGHTS_SECTION_TITLE"),
			"RIGHTS",
			$obSectionRights->GetRightsList(),
			$obSectionRights->GetRights(array("count_overwrited" => true, "parent" => $str_IBLOCK_SECTION_ID)),
			true, /*$bForceInherited=*/($ID <= 0)
		);
	$tabControl->EndCustomField("RIGHTS", $htmlHidden);
}

if($arIBlock["SECTION_PROPERTY"] === "Y" && defined("CATALOG_PRODUCT"))
{
	$tabControl->BeginNextFormTab();
	$tabControl->BeginCustomField("SECTION_PROPERTY", GetMessage("IBSEC_E_SECTION_PROPERTY_FIELD"));
	?>
		<tr><td align="center" colspan="2">
			<table class="internal" id="table_SECTION_PROPERTY">
			<tr class="heading">
				<td><?echo GetMessage("IBSEC_E_PROP_TABLE_NAME");?></td>
				<td><?echo GetMessage("IBSEC_E_PROP_TABLE_TYPE");?></td>
				<td><?echo GetMessage("IBSEC_E_PROP_TABLE_SMART_FILTER");?></td>
				<td><?echo GetMessage("IBSEC_E_PROP_TABLE_ACTION");?></td>
			</tr>
			<?
			if(CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_edit"))
				$arShadow = $arHidden = array(
					-1 => GetMessage("IBSEC_E_PROP_SELECT_CHOOSE"),
					0 => GetMessage("IBSEC_E_PROP_SELECT_CREATE"),
				);
			else
				$arShadow = $arHidden = array(
					-1 => GetMessage("IBSEC_E_PROP_SELECT_CHOOSE"),
				);

			$arPropLinks = CIBlockSectionPropertyLink::GetArray($arIBlock["ID"], $ID, $ID <= 0);
         
			$rsProps =  CIBlockProperty::GetList(array("SORT"=>"ASC",'ID' => 'ASC'), array("IBLOCK_ID" => $arIBlock["ID"], "CHECK_PERMISSIONS" => "N", "ACTIVE"=>"Y"));
			$rows = 0;   
			while ($arProp = $rsProps->Fetch()):
                             
				if(array_key_exists($arProp["ID"], $arPropLinks))
				{
					$rows++;
					$arLink = $arPropLinks[$arProp["ID"]];
					if($arLink["INHERITED"] == "N")
						$arShadow[$arProp["ID"]] = $arProp["NAME"];
				}
				else
				{
					$arLink = false;
					$arHidden[$arProp["ID"]] = $arProp["NAME"];
					$arShadow[$arProp["ID"]] = $arProp["NAME"];
				}
			?>
			<tr id="tr_SECTION_PROPERTY_<?echo $arProp["ID"]?>" <?if(!is_array($arLink)) echo 'style="display:none"';?>>
				<td align="left">
					<?if(!is_array($arLink) || $arLink["INHERITED"] == "N"):?>
					<input type="hidden" name="SECTION_PROPERTY[<?echo $arProp["ID"]?>][SHOW]" id="hidden_SECTION_PROPERTY_<?echo $arProp["ID"]?>" value="<?echo is_array($arLink)? "Y": "N";?>">
					<?endif?>
					<?echo htmlspecialcharsex($arProp["NAME"])?>
				</td>
				<td align="left"><?
					if($arProp['PROPERTY_TYPE'] == "S" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_S");
					elseif($arProp['PROPERTY_TYPE'] == "N" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_N");
					elseif($arProp['PROPERTY_TYPE'] == "L" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_L");
					elseif($arProp['PROPERTY_TYPE'] == "F" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_F");
					elseif($arProp['PROPERTY_TYPE'] == "G" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_G");
					elseif($arProp['PROPERTY_TYPE'] == "E" && !$arProp['USER_TYPE'])
						echo GetMessage("IBLOCK_PROP_E");
					elseif($arProp['USER_TYPE'] && is_array($ar = CIBlockProperty::GetUserType($arProp['USER_TYPE'])))
						echo htmlspecialcharsex($ar["DESCRIPTION"]);
					else
						echo GetMessage("IBSEC_E_PROP_TYPE_S");
				?></td>
				<td style="text-align:center"><?
					echo '<input type="checkbox" value="Y" '.((is_array($arLink) && $arLink["INHERITED"] == "Y")? 'disabled="disabled"': '').' name="SECTION_PROPERTY['.$arProp['ID'].'][SMART_FILTER]" '.($arLink["SMART_FILTER"] == "Y"? 'checked="checked"': '').'>';
				?></td>
				<td align="left"><?
					if(!is_array($arLink) || $arLink["INHERITED"] == "N")
						echo '<a class="bx-action-href" href="javascript:deleteSectionProperty('.$arProp['ID'].', \'select_SECTION_PROPERTY\', \'shadow_SECTION_PROPERTY\', \'table_SECTION_PROPERTY\')">'.GetMessage("IBSEC_E_PROP_TABLE_ACTION_DELETE").'</a>';
					else
						echo '&nbsp;';
				?></td>
			</tr>
			<?endwhile?>
			<tr <?echo ($rows == 0)? '': 'style="display:none"';?>>
				<td align="center" colspan="4">
					<?echo GetMessage("IBSEC_E_PROP_TABLE_EMPTY")?>
				</td>
			</tr>
			</table><br>
			<select id="shadow_SECTION_PROPERTY" style="display:none">
			<?foreach($arShadow as $key => $value):?>
				<option value="<?echo htmlspecialcharsex($key)?>"><?echo htmlspecialcharsbx($value)?></option>
			<?endforeach?>
			</select>
			<select id="select_SECTION_PROPERTY">
			<?foreach($arHidden as $key => $value):?>
				<option value="<?echo htmlspecialcharsex($key)?>"><?echo htmlspecialcharsbx($value)?></option>
			<?endforeach?>
			</select>
			<input type="button" value="<?echo GetMessage("IBSEC_E_PROP_TABLE_ACTION_ADD")?>" onclick="javascript:addSectionProperty(<?echo $arIBlock["ID"];?>, 'select_SECTION_PROPERTY', 'shadow_SECTION_PROPERTY', 'table_SECTION_PROPERTY')">
			<script>
			var target_id = '';
			var target_select_id = '';
			var target_shadow_id = '';
			function addSectionProperty(iblock_id, select_id, shadow_id, table_id)
			{
				var select = BX(select_id);
				if(select && select.value > 0)
				{
					var hidden = BX('hidden_SECTION_PROPERTY_' + select.value);
					var tr = BX('tr_SECTION_PROPERTY_' + select.value);
					if(hidden && tr)
					{
						jsSelectUtils.deleteOption(select_id, select.value);
						hidden.value = 'Y';
						var tds = BX.findChildren(tr, {tag:'td'}, true);
						BX.fx.colorAnimate.addRule('animationRule',"#F8F9FC","#faeeb4", "background-color", 50, 1, true);
						tr.style.display = 'table-row';
						for(var i = 0; i < tds.length; i++)
							BX.fx.colorAnimate(tds[i], 'animationRule');
					}
					adjustEmptyTR(table_id);
				}

				if(select && select.value == 0)
				{
					target_id = table_id;
					target_select_id = select_id;
					target_shadow_id = shadow_id;
					(new BX.CDialog({
						'content_url' : '/bitrix/admin/iblock_edit_property.php?lang=<?echo LANGUAGE_ID?>&IBLOCK_ID='+iblock_id+'&ID=n0&bxpublic=Y&from_module=iblock&return_url=section_edit',
						'width' : 700,
						'height' : 400,
						'buttons': [BX.CDialog.btnSave, BX.CDialog.btnCancel]
					})).Show();
				}
			}
			function deleteSectionProperty(id, select_id, shadow_id, table_id)
			{
				var hidden = BX('hidden_SECTION_PROPERTY_' + id);
				var tr = BX('tr_SECTION_PROPERTY_' + id);
				if(hidden && tr)
				{
					hidden.value = 'N';
					tr.style.display = 'none';
					var select = BX(select_id);
					var shadow = BX(shadow_id);
					if(select && shadow)
					{
						jsSelectUtils.deleteAllOptions(select);
						for(var i = 0; i < shadow.length; i++)
						{
							if(shadow[i].value <= 0)
								jsSelectUtils.addNewOption(select, shadow[i].value, shadow[i].text);
							else if (BX('hidden_SECTION_PROPERTY_' + shadow[i].value).value == 'N')
								jsSelectUtils.addNewOption(select, shadow[i].value, shadow[i].text);
						}
					}
					adjustEmptyTR(table_id);
				}
			}
			function createSectionProperty(id, name, type)
			{
				var tbl = BX(target_id);
				if(tbl)
				{
					var cnt = tbl.rows.length;
					var row = tbl.insertRow(cnt-1);
					//row.vAlign = 'top';
					row.id = 'tr_SECTION_PROPERTY_' + id;
					row.insertCell(-1);
					row.insertCell(-1);
					row.insertCell(-1);
					row.insertCell(-1);
					row.cells[0].align = 'left';
					row.cells[0].innerHTML = '<input type="hidden" name="SECTION_PROPERTY['+id+'][SHOW]" id="hidden_SECTION_PROPERTY_'+id+'" value="Y">'+name;
					row.cells[1].align = 'left';
					row.cells[1].innerHTML = type;
					row.cells[2].align = 'center';
					row.cells[2].style.textAlign = 'center';
					row.cells[2].innerHTML = '<input type="checkbox" value="Y" name="SECTION_PROPERTY['+id+'][SMART_FILTER]">';
					row.cells[3].align = 'left';
					row.cells[3].innerHTML = '<a class="bx-action-href" href="javascript:deleteSectionProperty('+id+', \''+target_select_id+'\', \''+target_shadow_id+'\', \''+target_id+'\')"><?echo GetMessageJS("IBSEC_E_PROP_TABLE_ACTION_DELETE")?></a>';
					var shadow = BX(target_shadow_id);
					if(shadow)
						jsSelectUtils.addNewOption(shadow, id, name);
					adjustEmptyTR(target_id);
				}
			}
			function adjustEmptyTR(table_id)
			{
				var tbl = BX(table_id);
				if(tbl)
				{
					var cnt = tbl.rows.length;
					var tr = tbl.rows[cnt-1];

					var display = 'table-row';
					for(var i = 1; i < cnt-1; i++)
					{
						if(tbl.rows[i].style.display != 'none')
							display = 'none';
					}
					tr.style.display = display;
				}
			}
			</script>
		</td></tr>
	<?
	$tabControl->EndCustomField("SECTION_PROPERTY", '');
}

if(strlen($return_url)>0)
	$bu = $return_url;
else
	$bu = "/bitrix/admin/".CIBlock::GetAdminSectionListLink($IBLOCK_ID, array('find_section_section'=>intval($find_section_section)));

if (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1):
	$tabControl->Buttons(array(
		"disabled" => false,
		"btnSaveAndAdd" => true,
		"return_url" => $bu,
	));
elseif($nobuttons !== "Y"):
	$save_and_add = "{
		title: '".CUtil::JSEscape(GetMessage("IBSEC_E_SAVE_AND_ADD"))."',
		name: 'save_and_add',
		id: 'save_and_add',
		className: 'adm-btn-add',
		action: function () {
			var FORM = this.parentWindow.GetForm();
			FORM.appendChild(BX.create('INPUT', {
				props: {
					type: 'hidden',
					name: 'save_and_add',
					value: 'Y'
				}
			}));

			this.parentWindow.hideNotify();
			this.disableUntilError();
			this.parentWindow.Submit();
		}
	}";
	$cancel = "{
		title: '".CUtil::JSEscape(GetMessage("admin_lib_edit_cancel"))."',
		name: 'cancel',
		id: 'cancel',
		action: function () {
			BX.WindowManager.Get().Close();
			if(window.reloadAfterClose)
				top.BX.reload(true);
		}
	}";
	$tabControl->ButtonsPublic(array(
		'.btnSave',
		$cancel,
		$save_and_add,
	));
endif;

$tabControl->Show();

$tabControl->ShowWarnings($tabControl->GetName(), $message);

if(CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_edit") && (!defined('BX_PUBLIC_MODE') || BX_PUBLIC_MODE != 1))
{
	echo
		BeginNote(),
		GetMessage("IBSEC_E_IBLOCK_MANAGE_HINT"),
		' <a href="iblock_edit.php?type='.htmlspecialcharsbx($type).'&amp;lang='.LANG.'&amp;ID='.$IBLOCK_ID.'&amp;admin=Y&amp;return_url='.urlencode(CIBlock::GetAdminSectionEditLink($IBLOCK_ID, $ID, array("find_section_section" => intval($find_section_section), "return_url" => strlen($return_url) > 0? $return_url: null))).'">',
		GetMessage("IBSEC_E_IBLOCK_MANAGE_HINT_HREF"),
		'</a>',
		EndNote()
	;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
