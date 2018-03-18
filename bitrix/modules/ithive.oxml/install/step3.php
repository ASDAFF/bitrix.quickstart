<?if(!IsModuleInstalled("iblock"))
{
	echo CAdminMessage::ShowMessage(GetMessage("ITHIVE_OXML_INSTALL_IBLOCK"));
	?>
	<form action="<?echo $APPLICATION->GetCurPage()?>">
	<p>
		<input type="hidden" name="lang" value="<?echo LANG?>">
		<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">	
	</p>
	<form>
	<?
}
else
{
	require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
	?>
	<form action="<?= $APPLICATION->GetCurPage()?>" name="ithive_oxml_install" method="POST">
	<script>
		document.getElementById('adm-title').innerHTML = '<?=GetMessage('ITHIVE_ADMIN_TITLE');?>';
	</script>
		<?
			$arOptions = unserialize(COption::GetOptionString('ithive.oxml', 'options'));
			
			$arOptions = array_merge($arOptions, Array(
				'more_photo' => $more_photo,
				'sections_export' => $sections_to_export,
				'property_export' => $properties_to_export,
				'skuprops_export' => $sku_properties_to_export
			));
			
			COption::SetOptionString('ithive.oxml', 'options', serialize($arOptions));			
		?>
		<?=GetMessage('INSTALL_OK', Array("#DIR#" => $arOptions['site']['dir_full']))?>
		<?=CAdminMessage::ShowNote(GetMessage("MODULE_INSTALL_OK"));?>
		<input type="submit" name="inst" value="<?= GetMessage("MOD_BACK")?>">
	</form>
	<?
}
?>