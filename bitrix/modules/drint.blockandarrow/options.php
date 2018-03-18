<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

if(!$USER->IsAdmin()) return;

$module_id = 'drint_blockandarrow';
$message = '';
$error = false;

$arAllOptions = array(
		'enable_jquery' => array('enable_jquery', GetMessage('ENABLE_JQUERY'), 'Y', array('checkbox')),
		'include_block' => array('include_block', GetMessage('INCLUDE_BLOCK'), 'N', array('checkbox')),
		'link' => array('link', GetMessage('LINK'), '', Array('text', 20)),
		'type' => array('type', GetMessage('TYPE'), 3, array('selectbox', array(GetMessage('TYPE_FLOAT'), GetMessage('TYPE_FIXED') ))),
		'type_fix' => array('type_fix', GetMessage('TYPE_FIX')),
		'pos' => array('pos', GetMessage('POS'), 3, array('selectbox', array(GetMessage('TOP_LEFT'), GetMessage('TOP_RIGHT'), GetMessage('BOTTOM_LEFT'), GetMessage('BOTTOM_RIGHT'), GetMessage('CENTER')))),
		'backgound_black' => array('backgound_black', GetMessage('BLACK'), 'Y', array('checkbox')),
		'pos_xy' => array('pos_xy', GetMessage('POS_XY'), '10', Array('text', 3)),
		'pos_yx' => array('pos_yx', GetMessage('POS_YX'), '10', Array('text', 3)),
		
);

$arAllOptionsUp = array(
		'include_up' => array('include_up', GetMessage('INCLUDE_UP'), 'N', array('checkbox')),
		'up_button' => array("up_button", GetMessage("SNG_UP_FILE"), COption::GetOptionString('drint_blockandarrow', 'up_button', "/bitrix/images/drint.blockandarrow/top.png"), "file"),
		'up_pos' => array('up_pos', GetMessage('UP_POS'), 3, array('selectbox', array(GetMessage('BOTTOM_LEFT'), GetMessage('BOTTOM_RIGHT')))),
		'up_pos_xy' => array('up_pos_xy', GetMessage('UP_POS_XY'), '10', Array('text', 3)),
		'up_pos_yx' => array('up_pos_yx', GetMessage('UP_POS_YX'), '10', Array('text', 3)),
		
);

$aTabs = array(
        array('DIV' => 'edit1', 'TAB' => GetMessage('MAIN_TAB_SET'), 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET'), 'OPTIONS' => $arAllOptions),
        array('DIV' => 'edit2', 'TAB' => GetMessage('UP'), 'TITLE' => GetMessage('UP_TITLE'), 'OPTIONS' => $arAllOptionsUp),
);
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();
//Restore defaults
if ($USER->IsAdmin() && $_SERVER['REQUEST_METHOD']=='GET' && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
    COption::RemoveOption('drint_blockandarrow');
}


function ShowParamsHTMLByArray($arParams)
{
        foreach($arParams as $Option)
        {
            __AdmSettingsDrawRow('drint_blockandarrow', $Option);
        }
}

//Save options

if($REQUEST_METHOD == 'POST' && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
        if(strlen($RestoreDefaults)>0)
        {
            COption::RemoveOption('drint_blockandarrow');
        }
        else
        {		
			foreach($aTabs as $i => $aTab)
			{
				foreach($aTab["OPTIONS"] as $name => $arOption)
				{
					if(strlen($_FILES['up_button']["tmp_name"])>0 and $name == 'up_button')
					{	
						$img_type = preg_split("#\/#", $_FILES["up_button"]["type"]);
						if($img_type[0] == 'image'){
							$tmp_name = $_FILES["up_button"]["tmp_name"];
							$filename = $_FILES["up_button"]["name"];
							move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/bitrix/images/drint.blockandarrow/".$filename);
							COption::SetOptionString('drint_blockandarrow', 'up_button', "/bitrix/images/drint.blockandarrow/".$filename);		
						}
						else
						{
							$message = CAdminMessage::ShowMessage(GetMessage("ERROR").$img_type[0]);	
							$error = true;	
						}			
					}
					
					if($name == 'pos' or $name == 'pos_xy' or $name == 'up_pos' or $name == 'pos_yx' or $name == 'up_pos_xy' or $name == 'up_pos_yx')
					{	
						if(intval($_REQUEST[$name]) >= 0)
							__AdmSettingsSaveOption($module_id, $arOption);
						else
						{
							$message = CAdminMessage::ShowMessage(GetMessage("INT_ERROR").$arOption[1].' = '.$_REQUEST[$name]);	
							$error = true;	
						}
					}
					else
					{
						__AdmSettingsSaveOption($module_id, $arOption);
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
?>

<?
//errors
if(!$message)
{
	echo $message;
}
?>
<form method='POST'  enctype="multipart/form-data" action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>'>
<?=bitrix_sessid_post();?>
<?
foreach($aTabs as $aTab):
	$tabControl->BeginNextTab();
	
?>

        <tr>
			<td colspan="2">
				<div>
						<table cellpadding='5' cellspacing='0' border='0' class='edit-table'>
						<?ShowParamsHTMLByArray($aTab["OPTIONS"]);?>
						<?
						if($aTab['DIV'] == 'edit2')
						{?>
							<tr>
								<td><? echo GetMessage('FILE_UPLOAD'); ?></td>
								<td>								
									<img src="<?=COption::GetOptionString('drint_blockandarrow', 'up_button', "/bitrix/images/drint.blockandarrow/top.png")?>">
									<input type="file" name="up_button"  /> 
								</td>
							</tr>
						<?
						}
						?>
						</table>
				</div>
			</td>
        </tr>
		<?
endforeach;?>


<?$tabControl->Buttons();?>

        <input type='hidden' name='Update' value='Y'>
        <input type='submit' <?if(!$USER->IsAdmin())echo ' disabled ';?> name='Update' value='<?echo GetMessage('MAIN_SAVE')?>'>
        <input type='reset' <?if(!$USER->IsAdmin())echo ' disabled ';?> name='reset' value='<?echo GetMessage('MAIN_RESET')?>' onClick = 'window.location.reload()'>
        <input type='button' <?if(!$USER->IsAdmin())echo ' disabled ';?>  type='button' title='<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>' OnClick='RestoreDefaults();' value='<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>'>
<?$tabControl->End();?>
</form>

