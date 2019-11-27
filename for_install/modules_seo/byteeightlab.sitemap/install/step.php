<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("BEL_INST_OK"));
?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage("BEL_BACK")?>">
	<script>
		function GoSetting(){
			window.location = "/bitrix/admin/settings.php?lang=<?=LANG?>&mid=byteeightlab.sitemap&mid_menu=1";
		}	
	</script>	
	<input type="button" name="Setting" OnClick="GoSetting();" value="<?=GetMessage("BEL_INST_BUTTON_SETTING")?>" title="<?=GetMessage("BEL_INST_BUTTON_SETTING_TITLE")?>" class="adm-btn-save">
<form>
