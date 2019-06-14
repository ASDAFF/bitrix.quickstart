<?
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="hidden" name="id" value="mcart.vacancy">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="4">

	<?	
	$ck = "";
	$id = "<select id='id_iblock' name='id_iblock' size='1'>";
	
				if (CModule::IncludeModule('iblock'))
			{
				$el = new CIBlock;
				$spr = CIBlock::GetList();
				while ($el=$spr->GetNext()) $ck .= "<option value='".$el["ID"]."'>".$el["NAME"]."</option>";
				
			}	
			$ck .= "</select>";	
			
			echo "<br>".$id.$ck."<br><br>";
			
			?>
	<input type="submit" name="inst" value="<?echo GetMessage("MOD_INSTALL")?>">
<form>