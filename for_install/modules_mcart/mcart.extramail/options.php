<?
global $MESS;
IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.extramail/prolog.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

$module_id = "mcart.extramail";
CModule::IncludeModule($module_id);

$MOD_RIGHT = $APPLICATION->GetGroupRight($module_id);

if($MOD_RIGHT>="R"):

	$arAllOptions = array(
		array("smtp_use", GetMessage("opt_smtp_check"),"n", array("checkbox", "y")),
		array("smtp_host", GetMessage("opt_smtp_host"), "", array("text", 35)),
		array("smtp_port", GetMessage("opt_smtp_host_port"), "25", array("text", 5)),
		array("smtp_login", GetMessage("opt_smtp_user_login"), "", array("text",35)),
		array("smtp_password", GetMessage("opt_smtp_user_password"), "", array("password",35)),

	);

if($MOD_RIGHT>="W"):
if($REQUEST_METHOD=="POST" && strlen($Update)>0) 
{ 
	for($i=0; $i<count($arAllOptions); $i++) { 
   	$name=$arAllOptions[$i][0]; 
   	$val=$$name; 

   	if($arAllOptions[$i][3][0]=="checkbox" && $val!="Y") $val="N"; 
   	COption::SetOptionString($module_id, $name, $val, $arAllOptions[$i][1]); 
   	} 
} 
endif; //if($MOD_RIGHT>="W"): 

?>

<?//иниациализация вкладок
$aTabs = array();
$aTabs[] = array("DIV" => "edit0", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "main_settings", "TITLE" => GetMessage("MAIN_TAB_RIGHTS"));
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("MCART_TAB_EXTRAMAIL_SETTINGSOUTSMTP"), "ICON" => "extramail_settings", "TITLE" => GetMessage("MCART_TAB_TITLE_EXTRAMAIL_SETTINGSOUTSMTP"));

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>"> 
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

<?$tabControl->BeginNextTab();?>
   
   <? 
   	for($i=0; $i<count($arAllOptions); $i++): 
	   $Option = $arAllOptions[$i]; 
	   $val = COption::GetOptionString($module_id, $Option[0], $Option[2]); 
	   $type = $Option[3]; 
   ?> 
   <tr> 
   	<td valign="top"><?echo $Option[1]?></font></td> 
   	<td valign="top"> 
   		<font class="tablebodytext"> 
   		<?if($type[0]=="checkbox"):?> 
   			<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>> 
   		
		<?elseif($type[0]=="text"):?> 
			<input type="text" class="typeinput" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>"> 
		<?elseif($type[0]=="password"):?> 
			<input type="password" class="typeinput" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>"> 	

		<?elseif($type[0]=="textarea"):?> 
			<textarea rows="<?echo $type[1]?>" class="typearea" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea> 
		<?endif?> 
	</td>
   </tr> 
     <? endfor; ?> 
     

<?$tabControl->Buttons();?>
<input type="submit" name="Update" <?if ($MOD_RIGHT<"W") echo "disabled" ?> value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="hidden" name="Update" value="Y">

<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form> 
<?endif;?>
