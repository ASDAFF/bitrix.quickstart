<?
set_time_limit(0);
ini_set('memory_limit', -1);

$moduleId = 'fairytale.tpic';
IncludeModuleLangFile(__FILE__);
$FORM_RIGHT = $APPLICATION->GetGroupRight($moduleId);
CModule::IncludeModule($moduleId);
if ($FORM_RIGHT >= "R"):
	
	if($_SERVER['METHOD_REQUEST'] = 'POST' && isset($_REQUEST['deleteFiles'])) {		
		$deleteFiles = array();
		if($_REQUEST['deleleNotActual']) {
			$deleteFiles = unserialize(htmlspecialcharsBack($_REQUEST['deleteNotActualFiles']));
		}
		
		ft\CTPic::deleteTPicFiles($deleteFiles);
		LocalRedirect($APPLICATION->GetCurPageParam());
	}
	

	$aTabs = array(
		array('DIV' => 'fairytale_tpic', 'TAB' => GetMessage('MAIN_TAB_SET'), 'ICON' => 'fairtale_tpic_settings', 'TITLE' => GetMessage($moduleId . '_SETTINGS_TITLE')),
	);
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	?>
	<?
	$tabControl->Begin();
	?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?=LANGUAGE_ID?>"><?=bitrix_sessid_post()?><?
	$tabControl->BeginNextTab();
	
		$tpicResult = ft\CTPic::getTPicInfo();
		
	?>
	
	<tr>
		<td valign="top"><?=GetMessage($moduleId . '_ALL_FILES_COUNT');?>: </td>
		<td valign="top"><?=$tpicResult['allCount']?></td>
	</tr>
	<tr>
		<td valign="top"><?=GetMessage($moduleId . '_ALL_FILES_SIZE');?>: </td>
		<td valign="top"><?=$tpicResult['allSize']?></td>
	</tr>
	<tr>
		<td valign="top"><?=GetMessage($moduleId . '_NOT_ACTUAL_FILES_COUNT');?>: </td>
		<td valign="top"><?=$tpicResult['notActualCount']?></td>
	</tr>
	<tr>
		<td valign="top"><?=GetMessage($moduleId . '_NOT_ACTUAL_FILES_SIZE');?>: </td>
		<td valign="top"><?=$tpicResult['notActualSize']?></td>
	</tr>
	<tr>
		<td valign="top">
			<label for="deleleNotActual"><?=GetMessage($moduleId . '_DELETE_ONLY_NOT_ACTUAL_FILES');?></label>
		</td>
		<td valign="top">
			<input type="hidden" name="deleteNotActualFiles" value="<?=htmlspecialchars(serialize($tpicResult['notActualFiles']))?>">
			<input id="deleleNotActual" type="checkbox" name="deleleNotActual" value="Y" checked>
		</td>
	</tr>
	<tr>
		<td valign="top"></td>
		<td valign="top">
			<input type="submit" name="deleteFiles" value="<?=GetMessage($moduleId . '_DELETE_FILES');?>">
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<?echo BeginNote();?>
			<?=GetMessage($moduleId . '_NOTE')?>
			<?echo EndNote(); ?>
		</td>
	</tr>
	
	<?$tabControl->End();?>
	</form>
<?endif;?>
