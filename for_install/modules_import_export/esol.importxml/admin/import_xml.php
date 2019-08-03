<?
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$moduleId = 'esol.importxml';
$moduleFilePrefix = 'esol_import_xml';
$moduleJsId = str_replace('.', '_', $moduleId);
$moduleDemoExpiredFunc = $moduleJsId.'_demo_expired';
$moduleShowDemoFunc = $moduleJsId.'_show_demo';
$moduleRunnerClass = 'CEsolImportXMLRunner';
CModule::IncludeModule("iblock");
CModule::IncludeModule($moduleId);
$bCatalog = CModule::IncludeModule('catalog');
$bCurrency = CModule::IncludeModule("currency");
CJSCore::Init(array('fileinput', $moduleJsId));
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
IncludeModuleLangFile(__FILE__);

include_once(dirname(__FILE__).'/../install/demo.php');
if ($moduleDemoExpiredFunc()) {
	require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$moduleShowDemoFunc();
	require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$siteEncoding = \Bitrix\EsolImportxml\Utils::getSiteEncoding();
$oProfile = new \Bitrix\EsolImportxml\Profile();
if(strlen($PROFILE_ID) > 0 && $PROFILE_ID!=='new')
{
	$oProfile->Apply($SETTINGS_DEFAULT, $SETTINGS, $PROFILE_ID);
	if($EXTRASETTINGS)
	{
		foreach($EXTRASETTINGS as $k=>$v)
		{
			if($v && !is_array($v))
			{
				$EXTRASETTINGS[$k] = CUtil::JsObjectToPhp($v);
			}
		}
	}
	$oProfile->ApplyExtra($EXTRASETTINGS, $PROFILE_ID);
}

$SHOW_FIRST_LINES = 10;
$SETTINGS_DEFAULT['IBLOCK_ID'] = intval($SETTINGS_DEFAULT['IBLOCK_ID']);
$STEP = intval($STEP);
if ($STEP <= 0)
	$STEP = 1;

$notRewriteFile = false;
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(isset($_POST["backButton"]) && strlen($_POST["backButton"]) > 0) $STEP = $STEP - 2;
	if(isset($_POST["backButton2"]) && strlen($_POST["backButton2"]) > 0) $STEP = 1;
	if(isset($_POST["saveConfigButton"]) && strlen($_POST["saveConfigButton"]) > 0)
	{
		$STEP = $STEP - 1;
		$notRewriteFile = true;
	}

}

$strError = $oProfile->GetErrors();
$io = CBXVirtualIo::GetInstance();

/////////////////////////////////////////////////////////////////////
if ($REQUEST_METHOD == "POST" && $MODE=='AJAX')
{
	if($ACTION=='SHOW_MODULE_MESSAGE')
	{
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		?><div><?
		$moduleShowDemoFunc(true);
		?></div><?
		die();
	}
	
	if($ACTION=='DELETE_TMP_DIRS')
	{
		\Bitrix\EsolImportxml\Utils::RemoveTmpFiles();
		die();
	}
	
	if($ACTION=='REMOVE_PROCESS_PROFILE')
	{
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		$oProfile = new \Bitrix\EsolImportxml\Profile();
		$oProfile->RemoveProcessedProfile($PROCCESS_PROFILE_ID);
		die();
	}
	
	if($ACTION=='GET_PROCESS_PARAMS')
	{
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		$oProfile = new \Bitrix\EsolImportxml\Profile();
		echo CUtil::PhpToJSObject($oProfile->GetProccessParams($PROCCESS_PROFILE_ID));
		die();
	}
	
	if($ACTION=='GET_UID')
	{
		$fl = new \Bitrix\EsolImportxml\FieldList($SETTINGS_DEFAULT);
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		?><div><?
		$fl->ShowSelectUidFields($IBLOCK_ID, 'fields[]');
		$OFFERS_IBLOCK_ID = \Bitrix\EsolImportxml\Utils::GetOfferIblock($IBLOCK_ID);
		if($OFFERS_IBLOCK_ID)
		{
			$fl->ShowSelectUidFields($OFFERS_IBLOCK_ID, 'fields_sku[]', false, 'OFFER_');
		}
		else
		{
			echo '<select name="fields_sku[]" multiple></select>';
		}
		$fl->ShowSelectPropertyList($IBLOCK_ID, 'properties[]');
		?></div><?
		die();
	}
	
	if($ACTION=='DELETE_PROFILE')
	{
		$fl = new \Bitrix\EsolImportxml\Profile();
		$fl->Delete($_REQUEST['ID']);
		die();
	}
	
	if($ACTION=='COPY_PROFILE')
	{
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		$fl = new \Bitrix\EsolImportxml\Profile();
		$id = $fl->Copy($_REQUEST['ID']);
		echo CUtil::PhpToJSObject(array('id'=>$id));
		die();
	}
	
	if($ACTION=='RENAME_PROFILE')
	{
		$newName = $_REQUEST['NAME'];
		if($siteEncoding!='utf-8') $newName = $APPLICATION->ConvertCharset($newName, 'UTF-8', $siteEncoding);
		$fl = new \Bitrix\EsolImportxml\Profile();
		$fl->Rename($_REQUEST['ID'], $newName);
		die();
	}
}

if ($REQUEST_METHOD == "POST" && $STEP > 1 && check_bitrix_sessid())
{
	//*****************************************************************//	
	if ($STEP > 1)
	{
		//*****************************************************************//		
		$sess = $_SESSION;
		session_write_close();
		$_SESSION = $sess;
		
		if (strlen($strError) <= 0)
		{
			if($STEP==2 && !$notRewriteFile)
			{
				if((!isset($_FILES["DATA_FILE"]) || !$_FILES["DATA_FILE"]["tmp_name"]) && (!isset($_POST['DATA_FILE']) || is_numeric($_POST['DATA_FILE'])))
				{
					if($_POST["EXT_DATA_FILE"]) $_POST['DATA_FILE'] = $_POST["EXT_DATA_FILE"];
					elseif($SETTINGS_DEFAULT["EXT_DATA_FILE"]) $_POST['DATA_FILE'] = $SETTINGS_DEFAULT["EXT_DATA_FILE"];
					elseif($SETTINGS_DEFAULT['EMAIL_DATA_FILE'])
					{
						$fileId = \Bitrix\EsolImportxml\SMail::GetNewFile($SETTINGS_DEFAULT['EMAIL_DATA_FILE']);
						if($fileId > 0)
						{
							if($_POST['OLD_DATA_FILE'])
							{
								CFile::Delete($_POST['OLD_DATA_FILE']);
							}
							$SETTINGS_DEFAULT["DATA_FILE"] = $_POST['DATA_FILE'] = $fileId;
						}
					}
				}
				elseif($SETTINGS_DEFAULT['EMAIL_DATA_FILE'])
				{
					unset($SETTINGS_DEFAULT['EMAIL_DATA_FILE']);
				}
			}
		
			$DATA_FILE_NAME = "";
			if((isset($_FILES["DATA_FILE"]) && $_FILES["DATA_FILE"]["tmp_name"]) || (isset($_POST['DATA_FILE']) && $_POST['DATA_FILE'] && !is_numeric($_POST['DATA_FILE'])))
			{
				$extFile = false;
				$fid = 0;
				if(isset($_FILES["DATA_FILE"]) && is_uploaded_file($_FILES["DATA_FILE"]["tmp_name"]))
				{
					//$fid = \Bitrix\EsolImportxml\Utils::SaveFile($_FILES["DATA_FILE"], $moduleId);
					$arFile = \Bitrix\EsolImportxml\Utils::MakeFileArray($_FILES["DATA_FILE"]);
					$fid = \Bitrix\EsolImportxml\Utils::SaveFile($arFile, $moduleId);
				}
				elseif(isset($_POST['DATA_FILE']) && strlen($_POST['DATA_FILE']) > 0)
				{
					$extFile = true;
					if(strpos($_POST['DATA_FILE'], '/')===0) 
					{
						$filepath = $_POST['DATA_FILE'];
						if(!file_exists($filepath))
						{
							$filepath = $_SERVER["DOCUMENT_ROOT"].$filepath;
						}
						if(!file_exists($filepath))
						{
							if($siteEncoding=='utf-8') $filepath = $APPLICATION->ConvertCharsetArray($filepath, LANG_CHARSET, 'CP1251');
							else $filepath = $APPLICATION->ConvertCharsetArray($filepath, LANG_CHARSET, 'UTF-8');
						}
					}
					else
					{
						//$extFile = true;
						$filepath = $_POST['DATA_FILE'];
						if($filepath && $_POST['OLD_DATA_FILE'])
						{
							$arOldFile = CFile::GetFileArray($_POST['OLD_DATA_FILE']);
							$oldFileSize = (int)filesize($_SERVER['DOCUMENT_ROOT'].$arOldFile['SRC']);
							$client = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true));
							$newFileSize = 0;
							if(is_callable(array($client, 'head')) && ($headers = $client->head($filepath)) && $client->getStatus()!=404) $newFileSize = (int)$headers->get('content-length');
							if($oldFileSize > 0 && $newFileSize > 0 && $oldFileSize==$newFileSize)
							{
								$fid = $_POST['OLD_DATA_FILE'];
							}
						}
					}
					if(!$fid)
					{
						$arFile = \Bitrix\EsolImportxml\Utils::MakeFileArray($filepath);
						if($arFile['name'])
						{
							if(strpos($arFile['name'], '.')===false) $arFile['name'] .= '.xml';
							$fid = \Bitrix\EsolImportxml\Utils::SaveFile($arFile, $moduleId);
						}
					}
				}
				
				if(!$fid)
				{
					$strError.= GetMessage("ESOL_IX_FILE_UPLOAD_ERROR")."<br>";
					if($extFile)
					{
						$SETTINGS_DEFAULT["EXT_DATA_FILE"] = $_POST['DATA_FILE'];
					}
				}
				else
				{
					$SETTINGS_DEFAULT["DATA_FILE"] = $fid;
					if($_POST['OLD_DATA_FILE'] && $_POST['OLD_DATA_FILE']!=$fid)
					{
						CFile::Delete($_POST['OLD_DATA_FILE']);
					}
					$SETTINGS_DEFAULT["EXT_DATA_FILE"] = ($extFile ? $_POST['DATA_FILE'] : false);
				}
			}
		}
		
		if(!$SETTINGS_DEFAULT["DATA_FILE"] && $_POST['OLD_DATA_FILE'])
		{
			$SETTINGS_DEFAULT["DATA_FILE"] = $_POST['OLD_DATA_FILE'];
		}
		
		if($SETTINGS_DEFAULT["DATA_FILE"])
		{
			$arFile = CFile::GetFileArray($SETTINGS_DEFAULT["DATA_FILE"]);
			if(stripos($arFile['SRC'], 'http')===0)
			{
				$arFileUrl = parse_url($arFile['SRC']);
				if($arFileUrl['path']) $arFile['SRC'] = $arFileUrl['path'];
			}
			$SETTINGS_DEFAULT['URL_DATA_FILE'] = $arFile['SRC'];
		}
		
		if(strlen($PROFILE_ID)==0)
		{
			$strError.= GetMessage("ESOL_IX_PROFILE_NOT_CHOOSE")."<br>";
		}

		if (strlen($strError) <= 0)
		{
			if (strlen($DATA_FILE_NAME) <= 0)
			{
				if (strlen($SETTINGS_DEFAULT['URL_DATA_FILE']) > 0)
				{
					$SETTINGS_DEFAULT['URL_DATA_FILE'] = trim(str_replace("\\", "/", trim($SETTINGS_DEFAULT['URL_DATA_FILE'])) , "/");
					$FILE_NAME = rel2abs($_SERVER["DOCUMENT_ROOT"], "/".$SETTINGS_DEFAULT['URL_DATA_FILE']);
					if (
						(strlen($FILE_NAME) > 1)
						&& ($FILE_NAME === "/".$SETTINGS_DEFAULT['URL_DATA_FILE'])
						&& $io->FileExists($_SERVER["DOCUMENT_ROOT"].$FILE_NAME)
						/*&& ($APPLICATION->GetFileAccessPermission($FILE_NAME) >= "W")*/
					)
					{
						$DATA_FILE_NAME = $FILE_NAME;
					}
				}
			}

			if (strlen($DATA_FILE_NAME) <= 0)
				$strError.= GetMessage("ESOL_IX_NO_DATA_FILE")."<br>";
			else
				$SETTINGS_DEFAULT['URL_DATA_FILE'] = $DATA_FILE_NAME;
			
			if(!in_array(ToLower(GetFileExtension($DATA_FILE_NAME)), array('xml', 'yml')))
			{
				$strError.= GetMessage("ESOL_IX_FILE_NOT_SUPPORT")."<br>";
			}

			if(!$SETTINGS_DEFAULT['IBLOCK_ID'])
				$strError.= GetMessage("ESOL_IX_NO_IBLOCK")."<br>";
			elseif (!CIBlockRights::UserHasRightTo($SETTINGS_DEFAULT['IBLOCK_ID'], $SETTINGS_DEFAULT['IBLOCK_ID'], "element_edit_any_wf_status"))
				$strError.= GetMessage("ESOL_IX_NO_IBLOCK")."<br>";
			
			if((!$DATA_FILE_NAME = \Bitrix\EsolImportxml\Utils::GetFileName($DATA_FILE_NAME)))
			{
				$strError.= GetMessage("ESOL_IX_FILE_NOT_FOUND")."<br>";
			}
			
			if(empty($SETTINGS_DEFAULT['ELEMENT_UID']))
			{
				$strError.= GetMessage("ESOL_IX_NO_ELEMENT_UID")."<br>";
			}
		}
		
		if (strlen($strError) <= 0)
		{
			/*Write profile*/
			$oProfile = new \Bitrix\EsolImportxml\Profile();
			if($PROFILE_ID === 'new')
			{
				$PID = $oProfile->Add($NEW_PROFILE_NAME);
				if($PID===false)
				{
					if($ex = $APPLICATION->GetException())
					{
						$strError .= $ex->GetString().'<br>';
					}
				}
				else
				{
					$PROFILE_ID = $PID;
				}
			}
			/*/Write profile*/
		}

		if (strlen($strError) > 0)
			$STEP = 1;
		//*****************************************************************//

	}
	
	if($ACTION == 'SHOW_REVIEW_LIST')
	{
		$sess = $_SESSION;
		session_write_close();
		$_SESSION = $sess;

		$error = '';
		$fl = new \Bitrix\EsolImportxml\FieldList($SETTINGS_DEFAULT);
		$xmlViewer = new \Bitrix\EsolImportxml\XMLViewer($DATA_FILE_NAME, $SETTINGS_DEFAULT);
		try{
			$arStruct = $xmlViewer->GetFileStructure();
			$arXPathsMulti = $xmlViewer->GetXPathsMulti();
		}catch(Exception $ex){
			$error = GetMessage("ESOL_IX_ERROR").$ex->getMessage();
		}
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		
		if(strlen($error) > 0) echo $error;
		//print_r($arStruct);
		
		echo '<div class="esol_ix_section_section">'.GetMessage("ESOL_IX_SECTION");
			$fl->ShowSelectSections($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS[SECTION_ID]', $SETTINGS['SECTION_ID']);
		echo '</div>';
		
		echo '<div class="esol_ix_xml_wrap" id="esol_ix_xml_wrap">';
		
		echo '<div class="esol_ix_xml_settings">';
		echo '<input type="hidden" name="SETTINGS[XPATHS_MULTI]" value="'.base64_encode(serialize($arXPathsMulti)).'">';
		echo '<input type="hidden" name="SETTINGS[INACTIVE_FIELDS]" value="'.htmlspecialcharsbx($SETTINGS['INACTIVE_FIELDS']).'">';
		echo '<input type="hidden" name="defaultsettings_json" value="'.htmlspecialcharsex(CUtil::PhpToJSObject($SETTINGS_DEFAULT)).'">';
		echo '<input type="hidden" name="settings_json" value="'.htmlspecialcharsex(CUtil::PhpToJSObject($SETTINGS)).'">';
		echo '<input type="hidden" name="extrasettings_json" value="'.htmlspecialcharsex(CUtil::PhpToJSObject($EXTRASETTINGS)).'">';
		//echo '<input type="hidden" name="struct_json" value="'.htmlspecialcharsex(CUtil::PhpToJSObject($arStruct)).'">';
		echo '<input type="hidden" name="struct_base64" value="'.base64_encode(serialize($arStruct)).'">';
		$fl->ShowSelectFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'element_fields');
		$fl->ShowSelectOfferFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'offer_fields');
		$fl->ShowSelectSectionFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'section_fields');
		$fl->ShowSelectSubSectionFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'subsection_fields');
		$fl->ShowSelectPropertyFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'property_fields');
		$fl->ShowSelectIbPropertyFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'ibproperty_fields');
		echo '</div>';
		
		echo '<div class="esol_ix_xml_struct">';
		$xmlViewer->ShowXmlTag($arStruct);
		echo '</div>';
		
		echo '</div>';
		
		die();
	}
	
	if($ACTION == 'DO_IMPORT')
	{
		unset($EXTRASETTINGS);
		$oProfile = new \Bitrix\EsolImportxml\Profile();
		$oProfile->ApplyExtra($EXTRASETTINGS, $PROFILE_ID);
		$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
		$stepparams = $_POST['stepparams'];
		$sess = $_SESSION;
		session_write_close();
		$_SESSION = $sess;
		$arResult = $moduleRunnerClass::ImportIblock($DATA_FILE_NAME, $params, $EXTRASETTINGS, $stepparams, $PROFILE_ID);
		$APPLICATION->RestartBuffer();
		if(ob_get_contents()) ob_end_clean();
		echo CUtil::PhpToJSObject($arResult);
		
		require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
		die();
	}
	
	/*Profile update*/
	if(strlen($PROFILE_ID) > 0 && $PROFILE_ID!=='new')
	{
		$oProfile->Update($PROFILE_ID, $SETTINGS_DEFAULT, $SETTINGS);
		if(is_array($EXTRASETTINGS)) $oProfile->UpdateExtra($PROFILE_ID, $EXTRASETTINGS);
	}
	/*/Profile update*/
	
	if ($STEP > 2)
	{
		/*$params = array_merge($SETTINGS_DEFAULT, $SETTINGS);
		$ie = new CEsolImportXml($DATA_FILE_NAME, $params);
		$ie->Import();
		die();*/
	}
	//*****************************************************************//

}

/////////////////////////////////////////////////////////////////////
$APPLICATION->SetTitle(GetMessage("ESOL_IX_PAGE_TITLE").$STEP);
require ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/

if (!$moduleDemoExpiredFunc()) {
	$moduleShowDemoFunc();
}

$arSubMenu = array();

$arSubMenu[] = array(
	"TEXT"=>GetMessage("ESOL_IX_MENU_PROFILE_LIST"),
	"TITLE"=>GetMessage("ESOL_IX_MENU_PROFILE_LIST"),
	"LINK" => "/bitrix/admin/".$moduleFilePrefix."_profile_list.php?lang=".LANG,
);
$arSubMenu[] = array(
	"TEXT"=>GetMessage("ESOL_IX_SHOW_CRONTAB"),
	"TITLE"=>GetMessage("ESOL_IX_SHOW_CRONTAB"),
	"ONCLICK" => "EProfile.ShowCron();",
);
$aMenu = array(
	array(
		"TEXT"=>GetMessage("ESOL_IX_MENU_HELP"),
		"TITLE"=>GetMessage("ESOL_IX_MENU_HELP"),
		"ONCLICK" => "EHelper.ShowHelp();",
		"ICON" => "",
	),
	array(
		"TEXT"=>GetMessage("ESOL_IX_MENU_FAQ"),
		"TITLE"=>GetMessage("ESOL_IX_MENU_FAQ"),
		"ONCLICK" => "EHelper.ShowHelp(1);",
		"ICON" => "",
	),
	array(
		"TEXT"=>GetMessage("ESOL_IX_TOOLS_LIST"),
		"TITLE"=>GetMessage("ESOL_IX_TOOLS_LIST"),
		"MENU" => $arSubMenu,
		"ICON" => "btn_green",
	)
);
$context = new CAdminContextMenu($aMenu);
$context->Show();


if ($STEP < 2)
{
	$oProfile = new \Bitrix\EsolImportxml\Profile();
	$arProfiles = $oProfile->GetProcessedProfiles();
	if(!empty($arProfiles))
	{
		$message = '';
		foreach($arProfiles as $k=>$v)
		{
			$message .= '<div class="kda-proccess-item">'.GetMessage("ESOL_IX_PROCESSED_PROFILE").': '.$v['name'].' ('.GetMessage("ESOL_IX_PROCESSED_PERCENT_LOADED").' '.$v['percent'].'%). &nbsp; &nbsp; &nbsp; &nbsp; <a href="javascript:void(0)" onclick="EProfile.ContinueProccess(this, '.$v['key'].')">'.GetMessage("ESOL_IX_PROCESSED_CONTINUE").'</a> &nbsp; <a href="javascript:void(0)" onclick="EProfile.RemoveProccess(this, '.$v['key'].')">'.GetMessage("ESOL_IX_PROCESSED_DELETE").'</a></div>';
		}
		CAdminMessage::ShowMessage(array(
			'TYPE' => 'error',
			'MESSAGE' => GetMessage("ESOL_IX_PROCESSED_TITLE"),
			'DETAILS' => $message,
			'HTML' => true
		));
	}
}

/*if($SETTINGS_DEFAULT['ONLY_DELETE_MODE']=='Y')
{
	CAdminMessage::ShowMessage(array(
		'TYPE' => 'ok',
		'MESSAGE' => GetMessage("ESOL_IX_DELETE_MODE_TITLE"),
		'DETAILS' => GetMessage("ESOL_IX_DELETE_MODE_MESSAGE"),
		'HTML' => true
	));	
}*/

CAdminMessage::ShowMessage($strError);
?>

<form method="POST" action="<?echo $sDocPath ?>?lang=<?echo LANG ?>" ENCTYPE="multipart/form-data" name="dataload" id="dataload" class="esol-ix-s1-form">

<?
$arProfile = (strlen($PROFILE_ID) > 0 ? $oProfile->GetFieldsByID($PROFILE_ID) : array());
$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => GetMessage("ESOL_IX_TAB1") ,
		"ICON" => "iblock",
		"TITLE" => GetMessage("ESOL_IX_TAB1_ALT"),
	) ,
	array(
		"DIV" => "edit2",
		"TAB" => GetMessage("ESOL_IX_TAB2") ,
		"ICON" => "iblock",
		"TITLE" => sprintf(GetMessage("ESOL_IX_TAB2_ALT"), (isset($arProfile['NAME']) ? $arProfile['NAME'] : '')),
	) ,
	array(
		"DIV" => "edit3",
		"TAB" => GetMessage("ESOL_IX_TAB3") ,
		"ICON" => "iblock",
		"TITLE" => sprintf(GetMessage("ESOL_IX_TAB3_ALT"), (isset($arProfile['NAME']) ? $arProfile['NAME'] : '')),
	) ,
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();
?>

<?$tabControl->BeginNextTab();
if ($STEP == 1)
{
	$fl = new \Bitrix\EsolImportxml\FieldList($SETTINGS_DEFAULT);
	$oProfile = new \Bitrix\EsolImportxml\Profile();
?>

	<tr class="heading">
		<td colspan="2" class="esol-ix-profile-header">
			<div>
				<?echo GetMessage("ESOL_IX_PROFILE_HEADER"); ?>
				<a href="javascript:void(0)" onclick="EHelper.ShowHelp();" title="<?echo GetMessage("ESOL_IX_MENU_HELP"); ?>" class="esol-ix-help-link"></a>
			</div>
		</td>
	</tr>

	<tr>
		<td><?echo GetMessage("ESOL_IX_PROFILE"); ?>:</td>
		<td>		
			<?$oProfile->ShowProfileList('PROFILE_ID');?>
			
			<?if(strlen($PROFILE_ID) > 0 && $PROFILE_ID!='new'){?>
				<span class="esol-ix-edit-btns">
					<a href="javascript:void(0)" class="adm-table-btn-edit" onclick="EProfile.ShowRename();" title="<?echo GetMessage("ESOL_IX_RENAME_PROFILE");?>" id="action_edit_button"></a>
					<a href="javascript:void(0);" class="adm-table-btn-copy" onclick="EProfile.Copy();" title="<?echo GetMessage("ESOL_IX_COPY_PROFILE");?>" id="action_copy_button"></a>
					<a href="javascript:void(0);" class="adm-table-btn-delete" onclick="if(confirm('<?echo GetMessage("ESOL_IX_DELETE_PROFILE_CONFIRM");?>')){EProfile.Delete();}" title="<?echo GetMessage("ESOL_IX_DELETE_PROFILE");?>" id="action_delete_button"></a>
				</span>
			<?}?>
		</td>
	</tr>
	
	<tr id="new_profile_name">
		<td><?echo GetMessage("ESOL_IX_NEW_PROFILE_NAME"); ?>:</td>
		<td>
			<input type="text" name="NEW_PROFILE_NAME" value="<?echo htmlspecialcharsbx($NEW_PROFILE_NAME)?>">
		</td>
	</tr>

	<?
	if(strlen($PROFILE_ID) > 0)
	{
	?>
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_DEFAULT_SETTINGS"); ?></td>
		</tr>
		
		<tr>
			<td width="40%"><?echo GetMessage("ESOL_IX_URL_DATA_FILE"); ?></td>
			<td width="60%" class="esol-ix-file-choose">
				<!--ESOL_IX_CHOOSE_FILE-->
				<?if($SETTINGS_DEFAULT['EMAIL_DATA_FILE']) echo '<input type="hidden" name="SETTINGS_DEFAULT[EMAIL_DATA_FILE]" value="'.htmlspecialcharsbx($SETTINGS_DEFAULT['EMAIL_DATA_FILE']).'">';?>
				<?if($SETTINGS_DEFAULT['EXT_DATA_FILE']) echo '<input type="hidden" name="EXT_DATA_FILE" value="'.htmlspecialcharsbx($SETTINGS_DEFAULT['EXT_DATA_FILE']).'">';?>
				<input type="hidden" name="OLD_DATA_FILE" value="<?echo htmlspecialcharsbx($SETTINGS_DEFAULT['DATA_FILE']); ?>">
				<?
				$arFile = CFile::GetFileArray($SETTINGS_DEFAULT["DATA_FILE"]);
				if(stripos($arFile['SRC'], 'http')===0)
				{
					$arFileUrl = parse_url($arFile['SRC']);
					if($arFileUrl['path']) $arFile['SRC'] = $arFileUrl['path'];
				}
				if($arFile['SRC'])
				{
					if(!file_exists($_SERVER['DOCUMENT_ROOT'].$arFile['SRC']))
					{
						if($siteEncoding=='utf-8') $arFile['SRC'] = $APPLICATION->ConvertCharsetArray($arFile['SRC'], LANG_CHARSET, 'CP1251');
						else $arFile['SRC'] = $APPLICATION->ConvertCharsetArray($arFile['SRC'], LANG_CHARSET, 'UTF-8');
						if(!file_exists($_SERVER['DOCUMENT_ROOT'].$arFile['SRC']))
						{
							unset($SETTINGS_DEFAULT["DATA_FILE"]);
						}
					}
				}
				else
				{
					unset($SETTINGS_DEFAULT["DATA_FILE"]);
				}
				//Cmodule::IncludeModule('fileman');
				echo \Bitrix\EsolImportxml\CFileInput::Show("DATA_FILE", $SETTINGS_DEFAULT["DATA_FILE"], array(
					"IMAGE" => "N",
					"PATH" => "Y",
					"FILE_SIZE" => "Y",
					"DIMENSIONS" => "N"
				), array(
					'upload' => true,
					'medialib' => false,
					'file_dialog' => true,
					'cloud' => true,
					'email' => true,
					'linkauth' => true,
					'del' => false,
					'description' => false,
				));
				\Bitrix\EsolImportxml\Utils::AddFileInputActions();
				?>
				<!--/ESOL_IX_CHOOSE_FILE-->
			</td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESOL_IX_INFOBLOCK"); ?></td>
			<td>
				<?echo GetIBlockDropDownList($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS_DEFAULT[IBLOCK_TYPE_ID]', 'SETTINGS_DEFAULT[IBLOCK_ID]', false, 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"'); ?>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_PROCESSING"); ?></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_UID"); ?>: <span id="hint_ELEMENT_UID"></span><script>BX.hint_replace(BX('hint_ELEMENT_UID'), '<?echo GetMessage("ESOL_IX_ELEMENT_UID_HINT"); ?>');</script></td>
			<td>
				<?$fl->ShowSelectUidFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS_DEFAULT[ELEMENT_UID][]', $SETTINGS_DEFAULT['ELEMENT_UID']);?>
			</td>
		</tr>

		<?
		$OFFERS_IBLOCK_ID = \Bitrix\EsolImportxml\Utils::GetOfferIblock($SETTINGS_DEFAULT['IBLOCK_ID']);
		?>	
		<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> id="element_uid_sku">
			<td><?echo GetMessage("ESOL_IX_ELEMENT_UID_SKU"); ?>: <span id="hint_ELEMENT_UID_SKU"></span><script>BX.hint_replace(BX('hint_ELEMENT_UID_SKU'), '<?echo GetMessage("ESOL_IX_ELEMENT_UID_SKU_HINT"); ?>');</script></td>
			<td>
			<?
			if($OFFERS_IBLOCK_ID)
			{
				$fl->ShowSelectUidFields($OFFERS_IBLOCK_ID, 'SETTINGS_DEFAULT[ELEMENT_UID_SKU][]', $SETTINGS_DEFAULT['ELEMENT_UID_SKU'], 'OFFER_');
			}
			else
			{
				echo '<select name="SETTINGS_DEFAULT[ELEMENT_UID_SKU][]" multiple></select>';
			}
			?>
			</td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESOL_IX_ONLY_UPDATE_MODE"); ?>: <span id="hint_ONLY_UPDATE_MODE_ELEMENT"></span><script>BX.hint_replace(BX('hint_ONLY_UPDATE_MODE_ELEMENT'), '<?echo GetMessage("ESOL_IX_ONLY_UPDATE_MODE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ONLY_UPDATE_MODE_ELEMENT]" value="Y" <?if($SETTINGS_DEFAULT['ONLY_UPDATE_MODE']=='Y' || $SETTINGS_DEFAULT['ONLY_UPDATE_MODE_ELEMENT']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[ONLY_CREATE_MODE_ELEMENT]', 'SETTINGS_DEFAULT[ONLY_DELETE_MODE]'])">
			</td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESOL_IX_ONLY_CREATE_MODE"); ?>: <span id="hint_ONLY_CREATE_MODE_ELEMENT"></span><script>BX.hint_replace(BX('hint_ONLY_CREATE_MODE_ELEMENT'), '<?echo GetMessage("ESOL_IX_ONLY_CREATE_MODE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ONLY_CREATE_MODE_ELEMENT]" value="Y" <?if($SETTINGS_DEFAULT['ONLY_CREATE_MODE']=='Y' || $SETTINGS_DEFAULT['ONLY_CREATE_MODE_ELEMENT']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[ONLY_UPDATE_MODE_ELEMENT]', 'SETTINGS_DEFAULT[ONLY_DELETE_MODE]'])">
			</td>
		</tr>
		
		<?/*?>
		<tr>
			<td><?echo GetMessage("ESOL_IX_ONLY_DELETE_MODE"); ?>: <span id="hint_ONLY_DELETE_MODE"></span><script>BX.hint_replace(BX('hint_ONLY_DELETE_MODE'), '<?echo GetMessage("ESOL_IX_ONLY_DELETE_MODE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ONLY_DELETE_MODE]" value="Y" <?if($SETTINGS_DEFAULT['ONLY_DELETE_MODE']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[ONLY_UPDATE_MODE]', 'SETTINGS_DEFAULT[ONLY_CREATE_MODE]'], '<?echo htmlspecialcharsex(GetMessage("ESOL_IX_ONLY_DELETE_MODE_CONFIRM")); ?>')">
			</td>
		</tr>
		<?*/?>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_NEW_DEACTIVATE"); ?>: <span id="hint_ELEMENT_NEW_DEACTIVATE"></span><script>BX.hint_replace(BX('hint_ELEMENT_NEW_DEACTIVATE'), '<?echo GetMessage("ESOL_IX_ELEMENT_NEW_DEACTIVATE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_NEW_DEACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_NEW_DEACTIVATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<?if($bCatalog){?>
			<tr>
				<td><?echo GetMessage("ESOL_IX_ELEMENT_NO_QUANTITY_DEACTIVATE"); ?>: <span id="hint_ELEMENT_NO_QUANTITY_DEACTIVATE"></span><script>BX.hint_replace(BX('hint_ELEMENT_NO_QUANTITY_DEACTIVATE'), '<?echo GetMessage("ESOL_IX_ELEMENT_NO_QUANTITY_DEACTIVATE_HINT"); ?>');</script></td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_NO_QUANTITY_DEACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_NO_QUANTITY_DEACTIVATE']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
			
			<tr>
				<td><?echo GetMessage("ESOL_IX_ELEMENT_NO_PRICE_DEACTIVATE"); ?>: <span id="hint_ELEMENT_NO_PRICE_DEACTIVATE"></span><script>BX.hint_replace(BX('hint_ELEMENT_NO_PRICE_DEACTIVATE'), '<?echo GetMessage("ESOL_IX_ELEMENT_NO_PRICE_DEACTIVATE_HINT"); ?>');</script></td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_NO_PRICE_DEACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_NO_PRICE_DEACTIVATE']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_LOADING_ACTIVATE"); ?>: <span id="hint_ELEMENT_LOADING_ACTIVATE"></span><script>BX.hint_replace(BX('hint_ELEMENT_LOADING_ACTIVATE'), '<?echo GetMessage("ESOL_IX_ELEMENT_LOADING_ACTIVATE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_LOADING_ACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_LOADING_ACTIVATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_NOT_UPDATE_WO_CHANGES"); ?>: <span id="hint_ELEMENT_NOT_UPDATE_WO_CHANGES"></span><script>BX.hint_replace(BX('hint_ELEMENT_NOT_UPDATE_WO_CHANGES'), '<?echo GetMessage("ESOL_IX_ELEMENT_NOT_UPDATE_WO_CHANGES_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_NOT_UPDATE_WO_CHANGES]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_NOT_UPDATE_WO_CHANGES']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_ADD_NEW_SECTIONS"); ?>: <span id="hint_ELEMENT_ADD_NEW_SECTIONS"></span><script>BX.hint_replace(BX('hint_ELEMENT_ADD_NEW_SECTIONS'), '<?echo GetMessage("ESOL_IX_ELEMENT_ADD_NEW_SECTIONS_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_ADD_NEW_SECTIONS]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_ADD_NEW_SECTIONS']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_NOT_CHANGE_SECTIONS"); ?>: <span id="hint_ELEMENT_NOT_CHANGE_SECTIONS"></span><script>BX.hint_replace(BX('hint_ELEMENT_NOT_CHANGE_SECTIONS'), '<?echo GetMessage("ESOL_IX_ELEMENT_NOT_CHANGE_SECTIONS_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_NOT_CHANGE_SECTIONS]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_NOT_CHANGE_SECTIONS']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_NOT_LOAD_ELEMENTS_WO_SECTION"); ?>: <span id="hint_ELEMENT_NOT_LOAD_ELEMENTS_WO_SECTION"></span><script>BX.hint_replace(BX('hint_ELEMENT_NOT_LOAD_ELEMENTS_WO_SECTION'), '<?echo GetMessage("ESOL_IX_ELEMENT_NOT_LOAD_ELEMENTS_WO_SECTION_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[NOT_LOAD_ELEMENTS_WO_SECTION]" value="Y" <?if($SETTINGS_DEFAULT['NOT_LOAD_ELEMENTS_WO_SECTION']=='Y'){echo 'checked';}?>>
			</td>
		</tr>

		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_MULTIPLE_SEPARATOR"); ?>:</td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[ELEMENT_MULTIPLE_SEPARATOR]" size="3" value="<?echo ($SETTINGS_DEFAULT['ELEMENT_MULTIPLE_SEPARATOR'] ? htmlspecialcharsbx($SETTINGS_DEFAULT['ELEMENT_MULTIPLE_SEPARATOR']) : ';'); ?>">
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_PROCESSING_MISSING_ELEMENTS"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="esol_ix_head_more show"><?echo GetMessage("ESOL_IX_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_DEACTIVATE"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[CELEMENT_MISSING_DEACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['CELEMENT_MISSING_DEACTIVATE']=='Y' || $SETTINGS_DEFAULT['ELEMENT_MISSING_DEACTIVATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<?if($bCatalog){?>
			<tr>
				<td><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_TO_ZERO"); ?>:</td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[CELEMENT_MISSING_TO_ZERO]" value="Y" <?if($SETTINGS_DEFAULT['CELEMENT_MISSING_TO_ZERO']=='Y' || $SETTINGS_DEFAULT['ELEMENT_MISSING_TO_ZERO']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
			
			<tr>
				<td><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_REMOVE_PRICE"); ?>:</td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[CELEMENT_MISSING_REMOVE_PRICE]" value="Y" <?if($SETTINGS_DEFAULT['CELEMENT_MISSING_REMOVE_PRICE']=='Y' || $SETTINGS_DEFAULT['ELEMENT_MISSING_REMOVE_PRICE']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_REMOVE_ELEMENT"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[CELEMENT_MISSING_REMOVE_ELEMENT]" value="Y" <?if($SETTINGS_DEFAULT['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y'){echo 'checked';}?> data-confirm="<?echo GetMessage("ESOL_IX_ELEMENT_MISSING_REMOVE_ELEMENT_CONFIRM"); ?>">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_MISSING_ACTIONS_IN_SECTION"); ?>: <span id="hint_MISSING_ACTIONS_IN_SECTION"></span><script>BX.hint_replace(BX('hint_MISSING_ACTIONS_IN_SECTION'), '<?echo GetMessage("ESOL_IX_MISSING_ACTIONS_IN_SECTION_HINT"); ?>');</script></td>
			<td>
				<input type="hidden" name="SETTINGS_DEFAULT[MISSING_ACTIONS_IN_SECTION]" value="N">
				<input type="checkbox" name="SETTINGS_DEFAULT[MISSING_ACTIONS_IN_SECTION]" value="Y" <?if($SETTINGS_DEFAULT['MISSING_ACTIONS_IN_SECTION']!='N'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td colspan="2" align="center">
				<input type="hidden" id="CELEMENT_MISSING_DEFAULTS" name="SETTINGS_DEFAULT[CELEMENT_MISSING_DEFAULTS]" value="<?echo htmlspecialcharsbx($SETTINGS_DEFAULT['CELEMENT_MISSING_DEFAULTS']);?>">
				<a href="javascript:void(0)" onclick="EProfile.OpenMissignElementFields(this)"><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_SET_FIELDS"); ?></a>
			</td>
		</tr>
		
		<tr>
			<td colspan="2" align="center">
				<input type="hidden" id="CELEMENT_MISSING_FILTER" name="SETTINGS_DEFAULT[CELEMENT_MISSING_FILTER]" value="<?echo htmlspecialcharsbx($SETTINGS_DEFAULT['CELEMENT_MISSING_FILTER']);?>">
				<a href="javascript:void(0)" onclick="EProfile.OpenMissignElementFilter(this)"><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_SET_FILTER"); ?></a>
			</td>
		</tr>
		
		<tr>
			<td colspan="2" align="center">
				<?
				echo BeginNote();
				echo sprintf(GetMessage("ESOL_IX_ELEMENT_MISSING_NOTE"), ' href="javascript:void(0)" onclick="EProfile.OpenMissignElementFilter(this)"');
				echo EndNote();
				?>
			</td>
		</tr>
		
		<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="heading kda-sku-block">
			<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_PROCESSING_MISSING_OFFERS"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="esol_ix_head_more show"><?echo GetMessage("ESOL_IX_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="kda-sku-block">
			<td><?echo GetMessage("ESOL_IX_OFFER_MISSING_DEACTIVATE"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[OFFER_MISSING_DEACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['OFFER_MISSING_DEACTIVATE']=='Y' || $SETTINGS_DEFAULT['ELEMENT_MISSING_DEACTIVATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<?if($bCatalog){?>
			<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="kda-sku-block">
				<td><?echo GetMessage("ESOL_IX_OFFER_MISSING_TO_ZERO"); ?>:</td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[OFFER_MISSING_TO_ZERO]" value="Y" <?if($SETTINGS_DEFAULT['OFFER_MISSING_TO_ZERO']=='Y' || $SETTINGS_DEFAULT['ELEMENT_MISSING_TO_ZERO']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
			
			<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="kda-sku-block">
				<td><?echo GetMessage("ESOL_IX_OFFER_MISSING_REMOVE_PRICE"); ?>:</td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[OFFER_MISSING_REMOVE_PRICE]" value="Y" <?if($SETTINGS_DEFAULT['OFFER_MISSING_REMOVE_PRICE']=='Y' || $SETTINGS_DEFAULT['ELEMENT_MISSING_REMOVE_PRICE']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="kda-sku-block">
			<td><?echo GetMessage("ESOL_IX_OFFER_MISSING_REMOVE_ELEMENT"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[OFFER_MISSING_REMOVE_ELEMENT]" value="Y" <?if($SETTINGS_DEFAULT['OFFER_MISSING_REMOVE_ELEMENT']=='Y'){echo 'checked';}?> data-confirm="<?echo GetMessage("ESOL_IX_OFFER_MISSING_REMOVE_ELEMENT_CONFIRM"); ?>">
			</td>
		</tr>
		
		<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="kda-sku-block">
			<td colspan="2" align="center">
				<input type="hidden" id="OFFER_MISSING_DEFAULTS" name="SETTINGS_DEFAULT[OFFER_MISSING_DEFAULTS]" value="<?echo htmlspecialcharsbx($SETTINGS_DEFAULT['OFFER_MISSING_DEFAULTS']);?>">
				<a href="javascript:void(0)" onclick="EProfile.OpenMissignElementFields(this)"><?echo GetMessage("ESOL_IX_ELEMENT_MISSING_SET_FIELDS"); ?></a>
			</td>
		</tr>
		
		<tr <?if(!$OFFERS_IBLOCK_ID){echo 'style="display: none;"';}?> class="kda-sku-block">
			<td colspan="2" align="center">
				<?
				echo BeginNote();
				echo sprintf(GetMessage("ESOL_IX_OFFER_MISSING_NOTE"), ' href="javascript:void(0)" onclick="EProfile.OpenMissignElementFilter(this)"');
				echo EndNote();
				?>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_PROCESSING_SECTIONS"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="esol_ix_head_more show"><?echo GetMessage("ESOL_IX_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_SECTION_UID"); ?>:</td>
			<td>
				<?$fl->ShowSelectSectionUidFields($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS_DEFAULT[SECTION_UID]', $SETTINGS_DEFAULT['SECTION_UID']);?>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ONLY_UPDATE_MODE_SECTION"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ONLY_UPDATE_MODE_SECTION]" value="Y" <?if($SETTINGS_DEFAULT['ONLY_UPDATE_MODE']=='Y' || $SETTINGS_DEFAULT['ONLY_UPDATE_MODE_SECTION']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_ONLY_CREATE_MODE_SECTION"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ONLY_CREATE_MODE_SECTION]" value="Y" <?if($SETTINGS_DEFAULT['ONLY_CREATE_MODE']=='Y' || $SETTINGS_DEFAULT['ONLY_CREATE_MODE_SECTION']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_SECTION_EMPTY_DEACTIVATE"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[SECTION_EMPTY_DEACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['SECTION_EMPTY_DEACTIVATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_SECTION_NOTEMPTY_ACTIVATE"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[SECTION_NOTEMPTY_ACTIVATE]" value="Y" <?if($SETTINGS_DEFAULT['SECTION_NOTEMPTY_ACTIVATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_MAX_SECTION_LEVEL"); ?>:  <span id="hint_MAX_SECTION_LEVEL"></span><script>BX.hint_replace(BX('hint_MAX_SECTION_LEVEL'), '<?echo GetMessage("ESOL_IX_MAX_SECTION_LEVEL_HINT"); ?>');</script></td>
			<td>
				<input type="text" name="SETTINGS_DEFAULT[MAX_SECTION_LEVEL]" size="3" value="<?echo (strlen($SETTINGS_DEFAULT['MAX_SECTION_LEVEL']) > 0 ? htmlspecialcharsbx($SETTINGS_DEFAULT['MAX_SECTION_LEVEL']) : '5'); ?>" maxlength="3">
			</td>
		</tr>
		
		
		<?if($bCatalog){?>
			<tr class="heading">
				<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_CATALOG"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="esol_ix_head_more show"><?echo GetMessage("ESOL_IX_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
			</tr>

			<?if($bCurrency){?>
			<tr>
				<td><?echo GetMessage("ESOL_IX_DEFAULT_CURRENCY"); ?>:</td>
				<td>
					<select name="SETTINGS_DEFAULT[DEFAULT_CURRENCY]">
					<?
					$lcur = CCurrency::GetList(($by="sort"), ($order1="asc"), LANGUAGE_ID);
					while($arr = $lcur->Fetch())
					{
						?><option value="<?echo $arr['CURRENCY']?>" <?if($arr['CURRENCY']==$SETTINGS_DEFAULT['DEFAULT_CURRENCY'] || (!$SETTINGS_DEFAULT['DEFAULT_CURRENCY'] && $arr['BASE']=='Y')){echo 'selected';}?>>[<?echo $arr['CURRENCY']?>] <?echo $arr['FULL_NAME']?></option><?
					}
					?>
					</select>
				</td>
			</tr>
			<?}?>
			
			<tr>
				<td><?echo GetMessage("ESOL_IX_QUANTITY_TRACE"); ?>:</td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[QUANTITY_TRACE]" value="Y" <?if($SETTINGS_DEFAULT['QUANTITY_TRACE']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
			
			<tr>
				<td><?echo GetMessage("ESOL_IX_QUANTITY_AS_SUM_STORE"); ?>:</td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[QUANTITY_AS_SUM_STORE]" value="Y" <?if($SETTINGS_DEFAULT['QUANTITY_AS_SUM_STORE']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[QUANTITY_AS_SUM_PROPERTIES]'])">
				</td>
			</tr>
			
			<tr>
				<td><?echo GetMessage("ESOL_IX_QUANTITY_AS_SUM_PROPERTIES"); ?>:</td>
				<td>
					<table cellspacing="0"><tr>
					<td style="padding-left: 0px;"><input type="checkbox" name="SETTINGS_DEFAULT[QUANTITY_AS_SUM_PROPERTIES]" value="Y" <?if($SETTINGS_DEFAULT['QUANTITY_AS_SUM_PROPERTIES']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[QUANTITY_AS_SUM_STORE]']); if(this.checked){$('#quantity_sum_props').show();}else{$('#quantity_sum_props').hide();}"></td>
					<td>&nbsp; &nbsp;</td>
					<td id="quantity_sum_props"<?if($SETTINGS_DEFAULT['QUANTITY_AS_SUM_PROPERTIES']!='Y'){echo ' style="display: none;"';}?>>
						<div id="properties_for_sum"><?$fl->ShowSelectPropertyListForSum($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS_DEFAULT[ELEMENT_PROPERTIES_FOR_QUANTITY][]', $SETTINGS_DEFAULT['ELEMENT_PROPERTIES_FOR_QUANTITY']);?></div>
						<div id="properties_for_sum_sku"><?$fl->ShowSelectPropertyListForSum($OFFERS_IBLOCK_ID, 'SETTINGS_DEFAULT[OFFER_PROPERTIES_FOR_QUANTITY][]', $SETTINGS_DEFAULT['OFFER_PROPERTIES_FOR_QUANTITY'], true);?></div>
					</td>
					</tr></table>
				</td>
			</tr>
		<?}?>
		
		<?/*?>
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_STATISTIC"); ?></td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_STAT_SAVE"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[STAT_SAVE]" value="Y" <?if($SETTINGS_DEFAULT['STAT_SAVE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_STAT_DELETE_OLD"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[STAT_DELETE_OLD]" value="Y" <?if($SETTINGS_DEFAULT['STAT_DELETE_OLD']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		<?*/?>
		
		<tr class="heading">
			<td colspan="2"><?echo GetMessage("ESOL_IX_SETTINGS_ADDITONAL"); ?> <a href="javascript:void(0)" onclick="EProfile.ToggleAdditionalSettings(this)" class="esol_ix_head_more show" id="kda-head-more-link"><?echo GetMessage("ESOL_IX_SETTINGS_ADDITONAL_SHOW_HIDE"); ?></a></td>
		</tr>
		
		<?/*?>
		<tr>
			<td><?echo GetMessage("ESOL_IX_STAT_SAVE"); ?>:</td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[STAT_SAVE]" value="Y" <?if($SETTINGS_DEFAULT['STAT_SAVE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		<?*/?>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_REMOVE_COMPOSITE_CACHE"); ?>: <span id="hint_REMOVE_COMPOSITE_CACHE"></span><script>BX.hint_replace(BX('hint_REMOVE_COMPOSITE_CACHE'), '<?echo GetMessage("ESOL_IX_REMOVE_COMPOSITE_CACHE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[REMOVE_COMPOSITE_CACHE]" value="Y" <?if($SETTINGS_DEFAULT['REMOVE_COMPOSITE_CACHE']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[REMOVE_COMPOSITE_CACHE_PART]'])">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_REMOVE_COMPOSITE_CACHE_PART"); ?>: <span id="hint_REMOVE_COMPOSITE_CACHE_PART"></span><script>BX.hint_replace(BX('hint_REMOVE_COMPOSITE_CACHE_PART'), '<?echo GetMessage("ESOL_IX_REMOVE_COMPOSITE_CACHE_PART_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[REMOVE_COMPOSITE_CACHE_PART]" value="Y" <?if($SETTINGS_DEFAULT['REMOVE_COMPOSITE_CACHE_PART']=='Y'){echo 'checked';}?> onchange="EProfile.RadioChb(this, ['SETTINGS_DEFAULT[REMOVE_COMPOSITE_CACHE]'])">
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_REMOVE_CACHE_AFTER_IMPORT"); ?>: <span id="hint_REMOVE_CACHE_AFTER_IMPORT"></span><script>BX.hint_replace(BX('hint_REMOVE_CACHE_AFTER_IMPORT'), '<?echo GetMessage("ESOL_IX_REMOVE_CACHE_AFTER_IMPORT_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[REMOVE_CACHE_AFTER_IMPORT]" value="Y" <?if($SETTINGS_DEFAULT['REMOVE_CACHE_AFTER_IMPORT']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_HTML_ENTITY_DECODE"); ?>:</td>
			<td>
				<input type="hidden" name="SETTINGS_DEFAULT[HTML_ENTITY_DECODE]" value="N">
				<input type="checkbox" name="SETTINGS_DEFAULT[HTML_ENTITY_DECODE]" value="Y" <?if($SETTINGS_DEFAULT['HTML_ENTITY_DECODE']=='Y' || $PROFILE_ID=='new'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_SAVE_DISAPPEARED_TAGS"); ?>: <span id="hint_SAVE_DISAPPEARED_TAGS"></span><script>BX.hint_replace(BX('hint_SAVE_DISAPPEARED_TAGS'), '<?echo GetMessage("ESOL_IX_SAVE_DISAPPEARED_TAGS_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[SAVE_DISAPPEARED_TAGS]" value="Y" <?if($SETTINGS_DEFAULT['SAVE_DISAPPEARED_TAGS']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_IMAGES_FORCE_UPDATE"); ?>: <span id="hint_ELEMENT_IMAGES_FORCE_UPDATE"></span><script>BX.hint_replace(BX('hint_ELEMENT_IMAGES_FORCE_UPDATE'), '<?echo GetMessage("ESOL_IX_IMAGES_FORCE_UPDATE_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_IMAGES_FORCE_UPDATE]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_IMAGES_FORCE_UPDATE']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_NOT_CHECK_NAME_IMAGES"); ?>: <span id="hint_ELEMENT_NOT_CHECK_NAME_IMAGES"></span><script>BX.hint_replace(BX('hint_ELEMENT_NOT_CHECK_NAME_IMAGES'), '<?echo GetMessage("ESOL_IX_NOT_CHECK_NAME_IMAGES_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[ELEMENT_NOT_CHECK_NAME_IMAGES]" value="Y" <?if($SETTINGS_DEFAULT['ELEMENT_NOT_CHECK_NAME_IMAGES']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_NOT_USE_XML_READER"); ?>: <span id="hint_NOT_USE_XML_READER"></span><script>BX.hint_replace(BX('hint_NOT_USE_XML_READER'), '<?echo GetMessage("ESOL_IX_NOT_USE_XML_READER_HINT"); ?>');</script></td>
			<td>
				<input type="checkbox" name="SETTINGS_DEFAULT[NOT_USE_XML_READER]" value="Y" <?if($SETTINGS_DEFAULT['NOT_USE_XML_READER']=='Y'){echo 'checked';}?>>
			</td>
		</tr>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_AUTO_FIX_XML_ERRORS"); ?>: <span id="hint_AUTO_FIX_XML_ERRORS"></span><script>BX.hint_replace(BX('hint_AUTO_FIX_XML_ERRORS'), '<?echo GetMessage("ESOL_IX_AUTO_FIX_XML_ERRORS_HINT"); ?>');</script></td>
			<td>
				<table cellspacing="0"><tr>
				<td style="padding-left: 0px;"><input type="checkbox" name="SETTINGS_DEFAULT[AUTO_FIX_XML_ERRORS]" value="Y" <?if($SETTINGS_DEFAULT['AUTO_FIX_XML_ERRORS']=='Y'){echo 'checked';}?> onchange="if(this.checked){$('#fix_xml_errors_params').show();}else{$('#fix_xml_errors_params').hide();}"></td>
				<td>&nbsp; &nbsp;</td>
				<td id="fix_xml_errors_params"<?if($SETTINGS_DEFAULT['AUTO_FIX_XML_ERRORS']!='Y'){echo ' style="display: none;"';}?>>
					<div>
						<div><?echo GetMessage("ESOL_IX_AUTO_FIX_XML_ERRORS_TAGS"); ?>:</div>
						<div><input type="text" name="SETTINGS_DEFAULT[AUTO_FIX_XML_CDATA]" value="<?echo htmlspecialcharsex($SETTINGS_DEFAULT['AUTO_FIX_XML_CDATA'])?>" size="65"></div>
					</div>
					<br>
					<div>
						<input type="checkbox" name="SETTINGS_DEFAULT[AUTO_FIX_XML_NUMTAGS]" value="Y" <?if($SETTINGS_DEFAULT['AUTO_FIX_XML_NUMTAGS']=='Y'){echo 'checked';}?> id="esol_ix_auto_fix_xml_numtags">
						<label for="esol_ix_auto_fix_xml_numtags"><?echo GetMessage("ESOL_IX_AUTO_FIX_XML_ERRORS_NUM_TAGS"); ?></label>
					</div>
				</td>
				</tr></table>
			</td>
		</tr>
		
		<?if($bCatalog){?>
			<tr>
				<td><?echo GetMessage("ESOL_IX_SEARCH_OFFERS_WO_PRODUCTS"); ?>: <span id="hint_SEARCH_OFFERS_WO_PRODUCTS"></span><script>BX.hint_replace(BX('hint_SEARCH_OFFERS_WO_PRODUCTS'), '<?echo GetMessage("ESOL_IX_SEARCH_OFFERS_WO_PRODUCTS_HINT"); ?>');</script></td>
				<td>
					<input type="checkbox" name="SETTINGS_DEFAULT[SEARCH_OFFERS_WO_PRODUCTS]" value="Y" <?if($SETTINGS_DEFAULT['SEARCH_OFFERS_WO_PRODUCTS']=='Y'){echo 'checked';}?>>
				</td>
			</tr>
		<?}?>
		
		<tr>
			<td><?echo GetMessage("ESOL_IX_PROPERTIES_REMOVE"); ?>: <span id="hint_ELEMENT_PROPERTIES_REMOVE"></span><script>BX.hint_replace(BX('hint_ELEMENT_PROPERTIES_REMOVE'), '<?echo GetMessage("ESOL_IX_PROPERTIES_REMOVE_HINT"); ?>');</script></td>
			<td>
				<?$fl->ShowSelectPropertyList($SETTINGS_DEFAULT['IBLOCK_ID'], 'SETTINGS_DEFAULT[ELEMENT_PROPERTIES_REMOVE][]', $SETTINGS_DEFAULT['ELEMENT_PROPERTIES_REMOVE']);?>
			</td>
		</tr>
		
		<tr>
			<td class="esol-ix-settings-margin-container" colspan="2" align="center">
				<a href="javascript:void(0)" onclick="ESettings.ShowPHPExpression(this)"><?echo GetMessage("ESOL_IX_ONAFTERSAVE_HANDLER");?></a>
				<div class="esol-ix-settings-phpexpression" style="display: none;">
					<?echo GetMessage("ESOL_IX_ONAFTERSAVE_HANDLER_HINT");?>
					<textarea name="SETTINGS_DEFAULT[ONAFTERSAVE_HANDLER]"><?echo $SETTINGS_DEFAULT['ONAFTERSAVE_HANDLER']?></textarea>
				</div>
			</td>
		</tr>
		
	<?
	}
}
$tabControl->EndTab();
?>

<?$tabControl->BeginNextTab();
if ($STEP == 2)
{
?>
	
	<tr>
		<td colspan="2" id="preview_file">
			<div class="esol-ix-file-preloader">
				<?echo GetMessage("ESOL_IX_PRELOADING"); ?>
			</div>
		</td>
	</tr>
	
	<?
}
$tabControl->EndTab();
?>


<?$tabControl->BeginNextTab();
if ($STEP == 3)
{
?>
	<tr>
		<td id="resblock" class="esol-ix-result">
		 <table width="100%"><tr><td width="50%">
			<div id="progressbar"><span class="pline"></span><span class="presult load"><b>0%</b><span 
				data-prefix="<?echo GetMessage("ESOL_IX_READ_LINES"); ?>" 
				data-import_props="<?echo GetMessage("ESOL_IX_STATUS_IMPORT_PROPS"); ?>" 
				data-import_sections="<?echo GetMessage("ESOL_IX_STATUS_IMPORT_SECTIONS"); ?>" 
				data-import="<?echo GetMessage("ESOL_IX_STATUS_IMPORT"); ?>" 
				data-deactivate_elements="<?echo GetMessage("ESOL_IX_STATUS_DEACTIVATE_ELEMENTS"); ?>" 
				data-deactivate_sections="<?echo GetMessage("ESOL_IX_STATUS_DEACTIVATE_SECTIONS"); ?>" 
			><?echo GetMessage("ESOL_IX_IMPORT_INIT"); ?></span></span></div>

			<div id="block_error_import" style="display: none;">
				<?echo CAdminMessage::ShowMessage(array(
					"TYPE" => "ERROR",
					"MESSAGE" => GetMessage("ESOL_IX_IMPORT_ERROR_CONNECT"),
					"DETAILS" => '<div>'.(COption::GetOptionString($moduleId, 'AUTO_CONTINUE_IMPORT', 'N')=='Y' ? sprintf(GetMessage("ESOL_IX_IMPORT_AUTO_CONTINUE"), '<span id="esol_ix_auto_continue_time"></span>').'<br>' : '').'<a href="javascript:void(0)" onclick="EProfile.ContinueProccess(this, '.$PROFILE_ID.');" id="kda_ie_continue_link">'.GetMessage("ESOL_IX_PROCESSED_CONTINUE").'</a><br><br>'.sprintf(GetMessage("ESOL_IX_IMPORT_ERROR_CONNECT_COMMENT"), '/bitrix/admin/settings.php?lang=ru&mid='.$moduleId.'&mid_menu=1').'</div>',
					"HTML" => true,
				))?>
			</div>
			
			<div id="block_error" style="display: none;">
				<?echo CAdminMessage::ShowMessage(array(
					"TYPE" => "ERROR",
					"MESSAGE" => GetMessage("ESOL_IX_IMPORT_ERROR"),
					"DETAILS" => '<div id="res_error"></div>',
					"HTML" => true,
				))?>
			</div>
		 </td><td>
			<div class="detail_status">
				<?echo CAdminMessage::ShowMessage(array(
					"TYPE" => "PROGRESS",
					"MESSAGE" => '<!--<div id="res_continue">'.GetMessage("ESOL_IX_AUTO_REFRESH_CONTINUE").'</div><div id="res_finish" style="display: none;">'.GetMessage("ESOL_IX_SUCCESS").'</div>-->',
					"DETAILS" =>

					GetMessage("ESOL_IX_SU_ALL").' <b id="total_line">0</b><br>'
					.GetMessage("ESOL_IX_SU_CORR").' <b id="correct_line">0</b><br>'
					.GetMessage("ESOL_IX_SU_ER").' <b id="error_line">0</b><br>'
					.GetMessage("ESOL_IX_SU_ELEMENT_ADDED").' <b id="element_added_line">0</b><br>'
					.GetMessage("ESOL_IX_SU_ELEMENT_UPDATED").' <b id="element_updated_line">0</b><br>'
					.($SETTINGS_DEFAULT['ONLY_DELETE_MODE']=='Y' ? (GetMessage("ESOL_IX_SU_ELEMENT_DELETED").' <b id="element_removed_line">0</b><br>') : '')
					.(!empty($SETTINGS_DEFAULT['ELEMENT_UID_SKU']) ? (GetMessage("ESOL_IX_SU_SKU_ADDED").' <b id="sku_added_line">0</b><br>') : '')
					.(!empty($SETTINGS_DEFAULT['ELEMENT_UID_SKU']) ? (GetMessage("ESOL_IX_SU_SKU_UPDATED").' <b id="sku_updated_line">0</b><br>') : '')
					.GetMessage("ESOL_IX_SU_SECTION_ADDED").' <b id="section_added_line">0</b><br>'
					.GetMessage("ESOL_IX_SU_SECTION_UPDATED").' <b id="section_updated_line">0</b><br>'
					.($SETTINGS_DEFAULT['CELEMENT_MISSING_DEACTIVATE']=='Y' ? (GetMessage("ESOL_IX_SU_HIDED").' <b id="killed_line">0</b><br>') : '')
					.($SETTINGS_DEFAULT['OFFER_MISSING_DEACTIVATE']=='Y' ? (GetMessage("ESOL_IX_SU_OFFER_HIDED").' <b id="offer_killed_line">0</b><br>') : '')
					.($SETTINGS_DEFAULT['CELEMENT_MISSING_TO_ZERO']=='Y' ? (GetMessage("ESOL_IX_SU_ZERO_STOCK").' <b id="zero_stock_line">0</b><br>') : '')
					.($SETTINGS_DEFAULT['OFFER_MISSING_TO_ZERO']=='Y' ? (GetMessage("ESOL_IX_SU_OFFER_ZERO_STOCK").' <b id="offer_zero_stock_line">0</b><br>') : '')
					.($SETTINGS_DEFAULT['CELEMENT_MISSING_REMOVE_ELEMENT']=='Y' ? (GetMessage("ESOL_IX_SU_REMOVE_ELEMENT").' <b id="old_removed_line">0</b><br>') : '')
					.($SETTINGS_DEFAULT['OFFER_MISSING_REMOVE_ELEMENT']=='Y' ? (GetMessage("ESOL_IX_SU_OFFER_REMOVE_ELEMENT").' <b id="offer_old_removed_line">0</b><br>') : '')
					.($SETTINGS_DEFAULT['STAT_SAVE']=='Y' ? ('<b><a target="_blank" href="/bitrix/admin/'.$moduleFilePrefix.'_event_log.php?lang='.LANGUAGE_ID.'&find_audit_type_id=ESOL_IX_PROFILE_'.$PROFILE_ID.'">'.GetMessage("ESOL_IX_STATISTIC_LINK").'</a></b>') : ''),
					"HTML" => true,
				))?>
			</div>
		 </td></tr></table>
		</td>
	</tr>
<?
}
$tabControl->EndTab();
?>

<?$tabControl->Buttons();
?>


<?echo bitrix_sessid_post(); ?>
<?
if($STEP > 1)
{
	if(strlen($PROFILE_ID) > 0)
	{
		?><input type="hidden" name="PROFILE_ID" value="<?echo htmlspecialcharsbx($PROFILE_ID) ?>"><?
	}
	else
	{
		foreach($SETTINGS_DEFAULT as $k=>$v)
		{
			?><input type="hidden" name="SETTINGS_DEFAULT[<?echo $k?>]" value="<?echo htmlspecialcharsbx($v) ?>"><?
		}
	}
}
?>


<?
if($STEP == 2){ ?>
<input type="submit" name="backButton" value="&lt;&lt; <?echo GetMessage("ESOL_IX_BACK"); ?>">
<input type="submit" name="saveConfigButton" value="<?echo GetMessage("ESOL_IX_SAVE_CONFIGURATION"); ?>" style="float: right;">
<?
}

if($STEP < 3)
{
?>
	<input type="hidden" name="STEP" value="<?echo $STEP + 1; ?>">
	<input type="submit" value="<?echo ($STEP == 2) ? GetMessage("ESOL_IX_NEXT_STEP_F") : GetMessage("ESOL_IX_NEXT_STEP"); ?> &gt;&gt;" name="submit_btn" class="adm-btn-save">
<? 
}
else
{
?>
	<input type="hidden" name="STEP" value="1">
	<input type="submit" name="backButton2" value="&lt;&lt; <?echo GetMessage("ESOL_IX_2_1_STEP"); ?>" class="adm-btn-save">
<?
}
?>

<?$tabControl->End();
?>

</form>

<?
if($STEP == 2)
{
	echo BeginNote();
	?>
	<p><?echo sprintf(GetMessage("ESOL_IX_LEGEND_MAIN_VIDEO"), '<a href="https://www.youtube.com/watch?v=2gMUw1Mtolg" target="_blank">https://www.youtube.com/watch?v=2gMUw1Mtolg</a>')?></p>
	<b><?echo GetMessage("ESOL_IX_LEGEND_TITLE")?></b><br>
	<ol class="esol-ix-legend-ol">
		<li>
			<div class="esol-ix-legend-p1"></div>
			<b><?echo GetMessage("ESOL_IX_LEGEND_SUBTITLE1")?></b>
			<p><?echo GetMessage("ESOL_IX_LEGEND_TEXT1")?></p>
			<ol>
				<li>
					<div class="esol-ix-legend-subtitle"><a href="#"><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_ELEMENT")?></a></div> 
					<div><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_ELEMENT_DESC")?><div class="esol-ix-legend-block-element"></div></div>
				</li>
				<li>
					<div class="esol-ix-legend-subtitle"><a href="#"><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_PROPERTY")?></a></div>
					<div><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_PROPERTY_DESC")?><div class="esol-ix-legend-block-property"></div></div>
				</li>
				<li>
					<div class="esol-ix-legend-subtitle"><a href="#"><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_OFFER")?></a></div>
					<div><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_OFFER_DESC")?><div class="esol-ix-legend-block-offer"></div></div>
				</li>
				<li>
					<div class="esol-ix-legend-subtitle"><a href="#"><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_SECTION")?></a></div>
					<div><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_SECTION_DESC")?><div class="esol-ix-legend-block-section"></div></div>
				</li>
				<li>
					<div class="esol-ix-legend-subtitle"><a href="#"><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_SUBSECTION")?></a></div>
					<div><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_SUBSECTION_DESC")?><div class="esol-ix-legend-block-subsection"></div></div>
				</li>
				<li>
					<div class="esol-ix-legend-subtitle"><a href="#"><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_IBPROPERTY")?></a></div>
					<div><?echo GetMessage("ESOL_IX_LEGEND_BLOCK_IBPROPERTY_DESC")?><div class="esol-ix-legend-block-ibproperty"></div></div>
				</li>
			</ol>
		</li>
		<li>
			<div class="esol-ix-legend-p2"></div>
			<b><?echo GetMessage("ESOL_IX_LEGEND_SUBTITLE2")?></b>
			<p><?echo GetMessage("ESOL_IX_LEGEND_TEXT2")?></p>
		</li>
	</ol>
	<?
	echo EndNote();
}
?>

<script language="JavaScript">
<?if ($STEP < 2): 
	$arFile = \Bitrix\EsolImportxml\Utils::GetShowFileBySettings($SETTINGS_DEFAULT);
	if($arFile['link'])
	{
		?>
		$('#bx_file_data_file_cont .adm-input-file-name').attr('target', '_blank').attr('href', '<?echo htmlspecialcharsex($arFile['link'])?>');<?
	}
	if($arFile['path'])
	{
		?>
		$('#bx_file_data_file_cont .adm-input-file-name').text('<?echo $arFile['path']?>');<?
	}
?>
tabControl.SelectTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit3");
<?elseif ($STEP == 2): 
	/*$fl = new \Bitrix\EsolImportxml\FieldList($SETTINGS_DEFAULT);
	$arMenu = $fl->GetLineActions();
	foreach($arMenu as $k=>$v)
	{
		$arMenu[$k] = $k.": {text: '".$v['TEXT']."', title: '".$v['TITLE']."'}";
	}*/
?>
tabControl.SelectTab("edit2");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit3");

/*var admKDAMessages = {};
admKDAMessages['lineActions'] = {<?echo implode(', ', $arMenu);?>};*/
<?elseif ($STEP > 2): ?>
tabControl.SelectTab("edit3");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit2");

<?
$arPost = $_POST;
unset($arPost['settings_json'], $arPost['defaultsettings_json'], $arPost['struct_json'], $arPost['struct_base64'], $arPost['SETTINGS']);
if(COption::GetOptionString($moduleId, 'SET_MAX_EXECUTION_TIME')=='Y')
{
	$delay = (int)COption::GetOptionString($moduleId, 'EXECUTION_DELAY');
	$stepsTime = (int)COption::GetOptionString($moduleId, 'MAX_EXECUTION_TIME');
	if($delay > 0) $arPost['STEPS_DELAY'] = $delay;
	if($stepsTime > 0) $arPost['STEPS_TIME'] = $stepsTime;
}
else
{
	$stepsTime = intval(ini_get('max_execution_time'));
	if($stepsTime > 0) $arPost['STEPS_TIME'] = $stepsTime;
}

if($_POST['PROCESS_CONTINUE']=='Y'){
	$oProfile = new \Bitrix\EsolImportxml\Profile();
?>
	EImport.Init(<?=CUtil::PhpToJSObject($arPost);?>, <?=CUtil::PhpToJSObject($oProfile->GetProccessParams($_POST['PROFILE_ID']));?>);
<?}else{?>
	EImport.Init(<?=CUtil::PhpToJSObject($arPost);?>);
<?}?>
<?endif; ?>
//-->
</script>

<?
require ($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");
?>
