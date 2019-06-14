<?
#################################################
#   Developer: Semen Golikov                    #
#   Site: http://www.sng-it.ru                  #
#   E-mail: info@sng-it.ru                      #
#   Copyright (c) 2009-2014 Semen Golikov       #
#################################################

IncludeModuleLangFile(__FILE__);  //Connecting the language files for the current script
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
$APPLICATION->SetAdditionalCSS("/bitrix/js/sng.up/style-up.css");

$module_id = 'sng.up';
 
$aTabs = array(
	array('DIV' => 'edit1', 'TAB' => GetMessage('MAIN_TAB_SET'), 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();

$arMass=array();
$arS = CSite::GetList();
while($st = $arS->GetNext())
{
	$arMass["REFERENCE"][] = $st[NAME];
	$arMass["REFERENCE_ID"][] = $st[LID];
}	

foreach($arMass["REFERENCE_ID"] as $k => $site['LID'])
{
	$arAllOptions[$site['LID']] = array(		
		"sng_up_site" => Array('sng_up_site'.'_'.$site['LID'], GetMessage('SNG_UP_SITE'), COption::GetOptionString('sng.up', 'sng_up_site'.'_'.$site['LID'], "N"), $arMass),
		"sng_up_jquery" => Array('sng_up_jquery'.'_'.$site['LID'], GetMessage('SNG_UP_INCLUDE_JQUERY'), COption::GetOptionString('sng.up', 'sng_up_jquery'.'_'.$site['LID'], "N"), array("REFERENCE" => array(GetMessage('SNG_UP_ENABLE_JQUERY_YES'), GetMessage('SNG_UP_ENABLE_JQUERY_ALREADY')),"REFERENCE_ID" => array('Y','N'))),
		//"sng_up_admin" => Array('sng_up_admin'.'_'.$site['LID'], GetMessage('SNG_UP_ADMIN'), COption::GetOptionString('sng.up', 'sng_up_admin'.'_'.$site['LID'], "Y"), array("checkbox", "Y")),	
		"sng_up_button_opacity" =>  Array("sng_up_button_opacity".'_'.$site['LID'], GetMessage("SNG_UP_OPACITY_BUTTOM"), COption::GetOptionString('sng.up', 'sng_up_button_opacity'.'_'.$site['LID'], "55"), Array("text", 5)),
		"sng_up_position" =>  Array('sng_up_position'.'_'.$site['LID'], GetMessage('SNG_UP_POSITION'), COption::GetOptionString('sng.up', 'sng_up_position'.'_'.$site['LID'], "right"), array("REFERENCE" =>array(GetMessage('SNG_UP_LEFT'), GetMessage('SNG_UP_RIGHT'), GetMessage('SNG_UP_CENTER')),"REFERENCE_ID" => array('left','right','center'))),
		"sng_up_button" => Array("sng_up_button".'_'.$site['LID'], GetMessage("SNG_UP_FILE"), COption::GetOptionString('sng.up', 'sng_up_button'.'_'.$site['LID'], "/bitrix/images/sng.up/up1.png"), "file"),
		"sng_up_pos_y" => Array("sng_up_pos_y".'_'.$site['LID'], GetMessage("SNG_UP_MARGIN_BUTTOM"), COption::GetOptionString('sng.up', 'sng_up_pos_y'.'_'.$site['LID'], "55"), Array("text", 5)),
		"sng_up_pos_x" => Array("sng_up_pos_x".'_'.$site['LID'], GetMessage("SNG_UP_MARGIN_SIDE"), COption::GetOptionString('sng.up', 'sng_up_pos_x'.'_'.$site['LID'], "20"), Array("text", 5)),
	);
}
if(strlen($_REQUEST["sng_up_site"])>0)
{	
	$_SESSION['sng_up_site']=htmlspecialcharsbx($_REQUEST["sng_up_site"]);	
}
if(strlen($_SESSION['sng_up_site'])>0)
{
	$sitech = $_SESSION['sng_up_site'];
}

$message='';
$error=false;

//Save options
if(($REQUEST_METHOD=='POST' || $REQUEST_METHOD=='GET') && (strlen($Update.$Apply.$RestoreDefaults)>0 || strlen($sitech)>0) &&  $sitech!='ru' && (check_bitrix_sessid() || strlen($sitech)>0))
{	 
	if(strlen($RestoreDefaults)>0)
	{	
		COption::RemoveOption('sng.up');
	}
	/**/
	foreach($arAllOptions[$site['LID']] as $key => $value)
	{			
		$name=htmlspecialcharsbx($key);	
 
		if($name=='sng_up_button')
		{	
		/*echo strlen($_FILES['sng_up_button']["tmp_name"]).strlen($_REQUEST['sng_up_choise_button']);
		?><pre><?print_r($_REQUEST);?></pre><? 
		die;*/			
			if(strlen($_FILES['sng_up_button']["tmp_name"])>0)
			{
				$sp = preg_split("#\/#", $_FILES[sng_up_button][type]);
				if($sp[0] == 'image'){
					$tmp_name = $_FILES[sng_up_button]["tmp_name"];
					$filename = $_FILES[sng_up_button]["name"];
					move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/bitrix/images/sng.up/".$filename);
					COption::SetOptionString($module_id, $name.'_'.$sitech, "/bitrix/images/sng.up/".$filename);		
				}
				else
				{
					//error file
					$message=CAdminMessage::ShowMessage(GetMessage("SNG_UP_FILE_ERROR").$sp[0]);	
					$error=true;	
				}		
			}
			elseif(strlen($_REQUEST['sng_up_choise_button'])>0)
			{			
				COption::SetOptionString($module_id, $name.'_'.$sitech, htmlspecialcharsbx($_REQUEST['sng_up_choise_button']));					
			}	
		}
		else
		{		
			if(strlen($_REQUEST[$name])>0) 
			{	
				if($name=='sng_up_pos_y' || $name=='sng_up_pos_x' || $name=='sng_up_button_opacity')
				{					
					if(intval($_REQUEST[$name])>0)
					{
						COption::SetOptionString($module_id, $name.'_'.$sitech, htmlspecialcharsbx(intval($_REQUEST[$name])));
					}
					else
					{ 					
						$message=CAdminMessage::ShowMessage(GetMessage("SNG_UP_INT_ERROR").$value[1].'='.$_REQUEST[$name]);	
						$error=true;						
					}
				}
				else
				{
					COption::SetOptionString($module_id, $name.'_'.$sitech, htmlspecialcharsbx($_REQUEST[$name]));			
				}
			}	
		}
	}

	if(strlen($Update.$Apply.$RestoreDefaults)>0 && !$error)
	{	
        if(strlen($Update)>0 && strlen($_REQUEST['back_url_settings'])>0)
                LocalRedirect($_REQUEST['back_url_settings']);
        else
                LocalRedirect($APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam());
	}	
}

if(strlen($sitech)>0)
{
	$site['LID']=$sitech;	
}


//errors
if(!$message)
{
	echo $message;
}
?>
<form enctype="multipart/form-data" name="sng_up" method='POST' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>'>
<?=bitrix_sessid_post();?>
<?$tabControl->BeginNextTab();?>
<tr class="field-str">
    <td align="left" valign='middle' width='50%'>
		<div style="text-align:left;"><a href="http://www.sng-it.ru" target="new" style="text-decoration:none;"><img src="/bitrix/images/sng.up/logo_sng.png" width="137" height="43" alt="www.sng-it.ru"></a></div> 
	</td>
	 <td valign='middle' width='50%'></td>
</tr>	
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name adm-detail-content-cell-l'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_SITE')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<?echo SelectBoxFromArray("sng_up_site",$arAllOptions[$site['LID']]["sng_up_site"][3], $arAllOptions[$site['LID']]["sng_up_site"][2],"","onchange=\"fn_redirect(this.value,'".$APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam()."');\"",false,'sng_up')?>
	</td>
</tr>	
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name adm-detail-content-cell-l'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_INCLUDE_JQUERY')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<?echo SelectBoxFromArray("sng_up_jquery",$arAllOptions[$site['LID']]["sng_up_jquery"][3], $arAllOptions[$site['LID']]["sng_up_jquery"][2])?>
	</td>
</tr>	
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name adm-detail-content-cell-l'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_FILE')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<img src="<?=COption::GetOptionString('sng.up', 'sng_up_button'.'_'.$site['LID'], "/bitrix/images/sng.up/up1.png")?>">
		<input type="file" name="sng_up_button">
	</td>
</tr>
<tr class="field-str">
    <td valign='middle' align="center" colspan="2" width='100%' class=''><label for='altasib_up_enable'><?=GetMessage('SNG_UP_FILE_CHOSE')?>: &nbsp;</label></td>
</tr> 
<tr class="field-str">
	<td valign='middle' width='100%' colspan="2">
	<input id="choise_button" type="hidden" name="sng_up_choise_button" value="">	
	<table cellspacing="1" cellpadding="5" border="1" style="margin:0 auto;">
	<tr>
		<?		
		$arFile = scandir($_SERVER['DOCUMENT_ROOT'].'/bitrix/images/sng.up/'); 
		foreach ($arFile as $key => $file){
			if($file=='logo_sng.png')
			{
				unset($arFile[$key]);
			}
		}	
		$arFile=array_values($arFile);
		foreach ($arFile as $key => $file){
			if($key>1)
			{
				if(($key-2) % 5 == 0)
				{
					?></tr><tr><?
				}			
				?>
				<td id="td_choise_button<?=$key;?>" onclick="fn_choise_button('/bitrix/images/sng.up/<?=$file;?>','<?=$key;?>')" valign='middle' align="center" width="20%" class="<?=(COption::GetOptionString('sng.up', 'sng_up_button'.'_'.$site['LID'], "/bitrix/images/sng.up/up1.png")=='/bitrix/images/sng.up/'.$file) ? 'selected_file' :'';?>">		
					<img src="<?='/bitrix/images/sng.up/'.$file;?>">					
				</td>
				<?
			}
		}
		?>
	</tr>
	</table>		
	</td>
</tr>
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_OPACITY_BUTTOM')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<input type="text" size="<?echo $arAllOptions[$site['LID']]["sng_up_button_opacity"][3][1]?>" maxlength="255" value="<?=COption::GetOptionString('sng.up', 'sng_up_button_opacity'.'_'.$site['LID'], "100");?>" name="sng_up_button_opacity">
	</td>
</tr>
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_POSITION')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<?echo SelectBoxFromArray("sng_up_position",$arAllOptions[$site['LID']]["sng_up_position"][3], $arAllOptions[$site['LID']]["sng_up_position"][2])?>
	</td>
</tr>
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_MARGIN_BUTTOM')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<input type="text" size="<?echo $arAllOptions[$site['LID']]["sng_up_pos_y"][3][1]?>" maxlength="255" value="<?=COption::GetOptionString('sng.up', 'sng_up_pos_y'.'_'.$site['LID'], "55");?>" name="sng_up_pos_y"> px
	</td>
</tr>
<tr class="field-str">
    <td valign='middle' width='50%' class='field-name'><label for='altasib_up_enable'><?=GetMessage('SNG_UP_MARGIN_SIDE')?>: &nbsp;</label></td>
    <td valign='middle' width='50%'>
		<input type="text" size="<?echo $arAllOptions[$site['LID']]["sng_up_pos_x"][3][1]?>" maxlength="255" value="<?=COption::GetOptionString('sng.up', 'sng_up_pos_x'.'_'.$site['LID'], "20");?>" name="sng_up_pos_x"> px
	</td>
</tr>
<?$tabControl->Buttons();?>
<script language='JavaScript'>
function RestoreDefaults()
{
        if(confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>'))
                window.location = '<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>';
}
</script>
<input type='hidden' name='Update' value='Y'>
<input type='submit' <?if(!$USER->IsAdmin())echo ' disabled ';?> name='Update' value='<?echo GetMessage('MAIN_SAVE')?>'>
<input type='reset' <?if(!$USER->IsAdmin())echo ' disabled ';?> name='reset' value='<?echo GetMessage('MAIN_RESET')?>' onClick = 'window.location.reload()'>
<input type='button' <?if(!$USER->IsAdmin())echo ' disabled ';?>  title='<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>' OnClick='RestoreDefaults();' value='<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>'>
<?
$tabControl->End();
CUtil::InitJSCore(Array("jquery"));
?>		
</form>
<script>
function fn_redirect(id,href)
{
	window.location.href=href+'&sng_up_site='+id;
}
function fn_choise_button(link,id)
{
	$('#choise_button').attr("value", link);	
	$('.field-str td table td').removeClass("selected_file");	
	$('#td_choise_button'+id).addClass("selected_file");	
}
</script>