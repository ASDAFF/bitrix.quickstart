<?
if(!$USER->IsAdmin())
	return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "defa.tools";

function DefaTools_HandleRegister( $reg_type, $option_code, $module_id = "defa.tools") { 

	if($reg_type == "Register") {
		$reg_func_name = "RegisterModuleDependences";
	} elseif($reg_type == "UnRegister") {
		$reg_func_name = "UnRegisterModuleDependences";
	} else {
		return;
	}

	switch($option_code) {
		
		case "active_ibprop_multiplefiles" :
			$reg_func_name("iblock", "OnIBlockPropertyBuildList", $module_id, "DefaTools_IBProp_MultipleFiles", "GetUserTypeDescription");
			$reg_func_name("iblock", "OnBeforeIBlockPropertyAdd", $module_id, "DefaTools_IBProp_MultipleFiles", "OnAfterIBlockPropertyHandler");
			$reg_func_name("iblock", "OnBeforeIBlockPropertyUpdate", $module_id, "DefaTools_IBProp_MultipleFiles", "OnAfterIBlockPropertyHandler");
			$reg_func_name("iblock", "main.file.input.upload", $module_id, "DefaTools_IBProp_MultipleFiles", "OnMainFileInputUploadHandler");
			break;
		
		case "active_ibprop_filemanex" : 
			$reg_func_name("iblock", "OnIBlockPropertyBuildList", $module_id, "DefaTools_IBProp_FileManEx", "GetUserTypeDescription");	
			break;
		
		case "active_ibprop_elemlistdescr" : 
			$reg_func_name("iblock", "OnIBlockPropertyBuildList", $module_id, "DefaTools_IBProp_ElemListDescr", "GetUserTypeDescription");	
			break;	
		
		case "active_ibprop_optionsgrid" : 
			$reg_func_name("iblock", "OnIBlockPropertyBuildList", $module_id, "DefaTools_IBProp_OptionsGrid", "GetUserTypeDescription");
			$reg_func_name("iblock", "OnBeforeIBlockPropertyAdd", $module_id, "DefaTools_IBProp_OptionsGrid", "CheckProperty");
			$reg_func_name("iblock", "OnAfterIBlockPropertyAdd", $module_id, "DefaTools_IBProp_OptionsGrid", "OnAfterPropertyAdd");
			$reg_func_name("iblock", "OnBeforeIBlockPropertyUpdate", $module_id, "DefaTools_IBProp_OptionsGrid", "CheckProperty");
			$reg_func_name("iblock", "OnBeforeIBlockPropertyUpdate", $module_id, "DefaTools_IBProp_OptionsGrid", "SetEnums");
			$reg_func_name("iblock", "OnBeforeIBlockPropertyDelete", $module_id, "DefaTools_IBProp_OptionsGrid", "DeleteEnums");
			break;
		
		case "active_ibprop_elemcompleter" : 
			$reg_func_name("iblock", "OnIBlockPropertyBuildList", $module_id, "DefaTools_IBProp_ElemCompleter", "GetUserTypeDescription");	
			break;	
			
		case "active_usertype_auth" : 
			$reg_func_name("main", "OnUserTypeBuildList", $module_id, "DefaTools_UserType_Auth", "GetUserTypeDescription");	
			break;	
		
		case "active_typograf" : 
			$reg_func_name("fileman", "OnBeforeHTMLEditorScriptsGet", $module_id, "DefaTools_Typograf", "addEditorScriptsHandler" );
			$reg_func_name("fileman", "OnIncludeHTMLEditorScript", $module_id, "DefaTools_Typograf", "OnIncludeHTMLEditorHandler" );
			break;	
			
		case "active_ib_tools" :
			$reg_func_name("main", "OnAdminContextMenuShow", $module_id, 'DefaToolsGetMenu', 'GetTopMenu');
			$reg_func_name("main", "OnAdminListDisplay", $module_id, 'DefaToolsGetMenu', 'GetActionsMenu');
			$reg_func_name("main", "OnAdminContextMenuShow", $module_id, 'DefaToolsController', 'OnAdminContextMenuShowHandler');
			break;
	}
	
}


$arAllOptions = Array(
	Array("active_ibprop_multiplefiles", GetMessage("DEFATOOLS_IBPROP_MULTIPLEFILES"), Array("checkbox", "Y")),
	Array("active_ibprop_filemanex", GetMessage("DEFATOOLS_IBPROP_FILEMANEX"), Array("checkbox", "Y")),
	Array("active_ibprop_elemlistdescr", GetMessage("DEFATOOLS_ELEMLISTDESCR"), Array("checkbox", "Y")),
	Array("active_ibprop_optionsgrid", GetMessage("DEFATOOLS_IBPROP_OPTIONSGRID"), Array("checkbox", "Y")),
	Array("active_ibprop_elemcompleter", GetMessage("DEFATOOLS_IBPROP_ELEMCOMPLETER"), Array("checkbox", "Y")),
	Array("active_usertype_auth", GetMessage("DEFATOOLS_USERTYPE_AUTH"), Array("checkbox", "Y")),
	Array("active_typograf", GetMessage("DEFATOOLS_TYPOGRAF"), Array("checkbox", "Y")),
	Array("active_ib_tools", GetMessage("DEFATOOLS_IB_TOOLS"), Array("checkbox", "Y")),
);

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{	
	if(strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption($module_id);
	}
	else
	{
		foreach($arAllOptions as $arOption)
		{
			$option_code = $arOption[0]; 
			$option_descr = $arOption[1]; 
			
			$option_val = $_REQUEST["TOOLS_CODES"][$option_code]; 
		
			$prev_option_val = COption::GetOptionString($module_id, $option_code);
		
			if(isset($_REQUEST["TOOLS_CODES"][$option_code])) {
				DefaTools_HandleRegister("Register", $option_code);	
				COption::SetOptionString($module_id, $option_code, "Y", $option_descr);
			} else {
				if( $prev_option_val != "N" ) {
					DefaTools_HandleRegister("UnRegister", $option_code);
				}	
				COption::SetOptionString($module_id, $option_code, "N", $option_descr);
			}
			
		} // end foreach
	}
}

$tabControl->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
	
	<tr class="heading">
		<td valign="top" colspan="2"><?=GetMessage("DEFATOOLS_ACTIVE_ITEMS_TITLE")?></td>
	</tr>
	
	<?
	foreach($arAllOptions as $arOption):
		$val = COption::GetOptionString($module_id, $arOption[0], $arOption[2][1]); 
	?>
	<tr>
		<td valign="top" width="50%"><?
			echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label>";
		?>:</td>
		<td valign="top" width="50%">
			<input type="checkbox" id="<?=htmlspecialchars($arOption[0])?>" name="TOOLS_CODES[<?=htmlspecialchars($arOption[0])?>]" value="Y"<?if($val=="Y") echo ' checked="checked"';?> />
		</td>
	</tr>
	<?endforeach?>
	
<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
