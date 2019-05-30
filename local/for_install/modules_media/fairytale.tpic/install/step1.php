<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<?$moduleId = 'fairytale.tpic';?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="id" value="<?=$moduleId?>">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">

	<table cellpadding="3" cellspacing="0" border="0" width="0%">
		<tr>
			<td>
				<?=GetMessage($moduleId . '_CREATE_DIRECTORY', array('#PATH#' => ft\CTpic::PATH))?>
			</td>
		</tr>
	</table>		

	<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>">
</form>