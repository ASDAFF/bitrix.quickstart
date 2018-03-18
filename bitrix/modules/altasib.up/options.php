<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Serge                            #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2011 ALTASIB             #
#################################################
?>
<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

if(!$USER->IsAdmin()) return;

$module_id = 'altasib_up';
$strWarning = '';

$arAllOptions = array(
                'enabled' => Array(
                        Array('altasib_up_enable', GetMessage('ALTASIB_UP_ENABLE'), 'Y', array('checkbox'))
                ),
        'main' => Array(
                Array('altasib_up_inverse_link', GetMessage('ALTASIB_UP_INVERSE_LINK'), 'N', array('checkbox')),
                Array('altasib_up_link', GetMessage('ALTASIB_UP_LINK'), '', Array('textarea', 4,40)),
                Array('enable_jquery', GetMessage('ALTASIB_ENABLE_JQUERY').'', 0, array('selectbox', array(GetMessage('ALTASIB_ENABLE_JQUERY_YES'), GetMessage('ALTASIB_ENABLE_JQUERY_ALREADY'), GetMessage('ALTASIB_ENABLE_JQUERY_NO')))),
                Array('altasib_up_pos', GetMessage('ALTASIB_POS').'', 3, array('selectbox', array(GetMessage('ALTASIB_TOP_LEFT'), GetMessage('ALTASIB_TOP_RIGHT'), GetMessage('ALTASIB_BOTTOM_LEFT'), GetMessage('ALTASIB_BOTTOM_RIGHT'), GetMessage('ALTASIB_BOTTOM_CENTER')))),
                Array('altasib_up_pos_xy', GetMessage('ALTASIB_POS_XY').'', '10', Array('text', 3)),
        ),
        'add' => Array(
                      Array('altasib_up_button', 'altasib_up_button', '', Array('text', 20))
                ),
);
$aTabs = array(
        array('DIV' => 'edit1', 'TAB' => GetMessage('MAIN_TAB_SET'), 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')),
);

        $arSites = CSite::GetList($by="",$order="");
    while($site = $arSites->GetNext())
        {
                $arAllOptions[$site['LID']] = Array(
                Array('altasib_up_enable_site'.'_'.$site['LID'], GetMessage('ALTASIB_UP_ENABLE_SITE'), 'Y', array('checkbox')),
                Array('altasib_up_inverse_link'.'_'.$site['LID'], GetMessage('ALTASIB_UP_INVERSE_LINK'), 'N', array('checkbox')),
                Array('altasib_up_link'.'_'.$site['LID'], GetMessage('ALTASIB_UP_LINK'), '', Array('textarea', 4,40)),
                Array('enable_jquery'.'_'.$site['LID'], GetMessage('ALTASIB_ENABLE_JQUERY').' ', 0, array('selectbox', array(GetMessage('ALTASIB_ENABLE_JQUERY_YES'), GetMessage('ALTASIB_ENABLE_JQUERY_ALREADY'), GetMessage('ALTASIB_ENABLE_JQUERY_NO')))),
                Array('altasib_up_pos'.'_'.$site['LID'], GetMessage('ALTASIB_POS').' ', 3, array('selectbox', array(GetMessage('ALTASIB_TOP_LEFT'), GetMessage('ALTASIB_TOP_RIGHT'), GetMessage('ALTASIB_BOTTOM_LEFT'), GetMessage('ALTASIB_BOTTOM_RIGHT'), GetMessage('ALTASIB_BOTTOM_CENTER')))),
                Array('altasib_up_pos_xy'.'_'.$site['LID'], GetMessage('ALTASIB_POS_XY').' ', '10', Array('text', 3)),
                );
                $arAllOptions['add'.'_'.$site['LID']] = Array(
                        Array('altasib_up_button'.'_'.$site['LID'], 'altasib_up_button', '', Array('text', 20)),
                        );
        }

//Restore defaults
if ($USER->IsAdmin() && $_SERVER['REQUEST_METHOD']=='GET' && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
    COption::RemoveOption('altasib_up');
}
$tabControl = new CAdminTabControl('tabControl', $aTabs);

function ShowParamsHTMLByArray($arParams)
{
        foreach($arParams as $Option)
        {
                 __AdmSettingsDrawRow('altasib_up', $Option);
        }
}

//Save options
if($REQUEST_METHOD=='POST' && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
        if(strlen($RestoreDefaults)>0)
        {
                COption::RemoveOption('altasib_up');
        }
        else
        {
                foreach($arAllOptions as $aOptGroup)
                {
                        foreach($aOptGroup as $option)
                        {
                                __AdmSettingsSaveOption($module_id, $option);
                        }
                }
        }
        if(strlen($Update)>0 && strlen($_REQUEST['back_url_settings'])>0)
                LocalRedirect($_REQUEST['back_url_settings']);
        else
                LocalRedirect($APPLICATION->GetCurPage().'?mid='.urlencode($mid).'&lang='.urlencode(LANGUAGE_ID).'&back_url_settings='.urlencode($_REQUEST['back_url_settings']).'&'.$tabControl->ActiveTabParam());
}
?>
<?$tabControl->Begin();?>
<form method='POST' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>'>
<?=bitrix_sessid_post();?>
<?
$tabControl->BeginNextTab();
?>
        <tr><td colspan="2">
         <div style='background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;'><div style='background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff'>
                 <a href='http://www.is-market.ru?param=cl' target='_blank'><img src='/bitrix/images/altasib.up/is-market.gif' style='float: left; margin-right: 15px;' border='0' /></a>
                 <div style='margin: 13px 0px 0px 0px'>
                         <a href='http://www.is-market.ru?param=cl' target='_blank' style='color: #fff; font-size: 10px; text-decoration: none'><?=GetMessage('ALTASIB_IS')?></a>
                 </div>
         </div></div>
        </td></tr>

<?
        $dir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/altasib.up/button/';
        if (is_dir($dir))
        {
            if ($d = opendir($dir))
                {
                        while (($file = readdir($d)) !== false)
                        {
                                $f = pathinfo($dir . $file);
                                if ($f['extension'] == 'gif' || $f['extension'] == 'png' || $f['extension'] == 'jpg')
                                        $file_list[] = '/bitrix/images/altasib.up/button/'.$file;
                        }
                        closedir($d);
                }
        }
        $altasib_up_button = COption::GetOptionString('altasib_up', 'altasib_up_button', $file_list[0]);

        $all = COption::GetOptionString('altasib_up', 'altasib_up_enable', 'Y');
        ?>
        <tr>
                <td valign='top' width='50%' class='field-name'><label for='altasib_up_enable'><?=GetMessage('ALTASIB_UP_ENABLE')?></label></td>
                <td valign='middle' width='50%'><input type='checkbox'  id='altasib_up_enable' name='altasib_up_enable' value='<?echo $all?>' <?if($all == 'Y') echo ' checked';?> onChange = 'func()'></td>
        </tr>
        <tr>
			<td colspan="2">
				<div id = 'alx_all_options' <?if($all == 'N') echo 'style = "display:none"'?>>
						<table cellpadding='5' cellspacing='0' border='0' class='edit-table' id='edit2_edit_table'>
						<?ShowParamsHTMLByArray($arAllOptions['main']);?>
                                                                <tr>
                                                                        <td valign='top' width='50%'><?=GetMessage('ALTASIB_SEL_BUTTON')?></td>
                                                                        <td valign='middle' width='50%'></td>
                                                                </tr>

								<tr>
										<td align='center' colspan="2">
                                            						<table cellpadding="10" class="alx_tbl">
											<tr>
	                                        					<?
											foreach ($file_list as $key => $filename)
												{
												if ($key/4 == round($key/4))echo "</tr><tr>";
											?>
												<td style="border: 1px solid #999; " valign="top" onClick="document.getElementById('altasib_up_button_<?=$key?>').checked = true">
												<input type="radio" <?if($altasib_up_button == $filename):?> checked <?endif;?> name="altasib_up_button" id="altasib_up_button_<?=$key?>" value="<?=$filename?>"> <?=$key?> <img src="<?=$filename?>" />
												</td>

											<?
												}
											?>
											</tr>
											</table>

										</td>
								</tr>
						</table>
				</div>
				<div id = 'alx_sites_options' <?if($all == 'Y') echo 'style = "display:none"'?>>
						<table cellpadding='5' cellspacing='0' border='0' class='edit-table' id='edit3_edit_table'>
						<?
						$arSites = CSite::GetList();
						while($site = $arSites->GetNext()):

								$altasib_up_button = COption::GetOptionString('altasib_up', 'altasib_up_button'.'_'.$site['LID'], $file_list[0]);
								?>
								<tr class='heading'>
										<td colspan='2'><?=GetMessage('ALTASIB_UP_OPTIONS_FOR_SITE').$site['LID']?></td>
								</tr>
								<?ShowParamsHTMLByArray($arAllOptions[$site['LID']]);?>
										<tr>
                                                                                        <td valign='top' width='50%'><?=GetMessage('ALTASIB_SEL_BUTTON')?></td>
                                                                                        <td valign='middle' width='50%'></td>
										</tr>
                                                                                <tr>
										<td align='center' colspan="2">
                                                                                        <table cellpadding="10" class="alx_tbl">
											<tr>


                                                                                        <?
	                                                                                foreach ($file_list as $key => $filename)
	                                                                                {
                                                                                             if ($key/4 == round($key/4))echo "</tr><tr>";
											?>
	                                                                                          <td style="border: 1px solid #999;" valign="top" onClick="document.getElementById('altasib_up_button_<?=$key.'_'.$site['LID']?>').checked = true">
												  	 <input type="radio" <?if($altasib_up_button == $filename):?> checked <?endif;?> name="<?='altasib_up_button'.'_'.$site['LID']?>" id="altasib_up_button_<?=$key."_".$site['LID']?>" value="<?=$filename?>"> <?=$key?> <img src="<?=$filename?>" />
												  </td>
	                                                                                <?
	                                                                                }
											?>
                                                                                        </tr>
											</table>


												</td>
										</tr>
								<?
						endwhile;
		?>
						</table>
				</div>
			</td>
        </tr>
<?$tabControl->Buttons();?>
<style>
.alx_tbl td:hover
{
	background: #1198DA;
}


</style>
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
        <input type='button' <?if(!$USER->IsAdmin())echo ' disabled ';?>  type='button' title='<?echo GetMessage('MAIN_HINT_RESTORE_DEFAULTS')?>' OnClick='RestoreDefaults();' value='<?echo GetMessage('MAIN_RESTORE_DEFAULTS')?>'>
<?$tabControl->End();?>
</form>

<script>
function func()
{
        if(document.getElementById('altasib_up_enable').checked)
        {
                document.getElementById('altasib_up_enable').value = 'Y';
                document.getElementById('alx_all_options').style.display = 'block';
                document.getElementById('alx_sites_options').style.display = 'none';
        }
        else
        {
                document.getElementById('altasib_up_enable').value = 'N';
                document.getElementById('alx_all_options').style.display = 'none';
                document.getElementById('alx_sites_options').style.display = 'block';
        }
}
</script>
