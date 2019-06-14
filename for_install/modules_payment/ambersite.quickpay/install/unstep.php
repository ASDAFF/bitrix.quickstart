<?php

IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("UDALENIE_MODULYA_PRIEMA_PLATEGEJ")); 

?>
	<form action="<?=$APPLICATION->GetCurPage(); ?>">
		<?=bitrix_sessid_post(); ?>
		<input type="hidden" name="lang" value="<?=LANG; ?>">
        <input type="hidden" name="id" value="ambersite.quickpay">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
		<?=CAdminMessage::ShowMessage(GetMessage("VNOMANIE_MODUL_BUDET_UDALEN_IZ_SISTEMU")); ?>
		<?php /*?><p><?=GetMessage("VU_MOGETE_SOHRANIT_DANNUE_V_TABLICAH_BAZU_DANNUH")?>:</p><?php */?>
		<p><input type="checkbox" name="savedata" id="savedata" value="Y" checked><label for="savedata"><?=GetMessage("SOHRANIT_TABLICU")?></label></p>
        <?php /*?><p><?=GetMessage("VU_MOGETE_SOHRANIT_POCHTOVUE_SHABLONU")?>:</p><?php */?>
		<p><input type="checkbox" name="savemess" id="savemess" value="Y" checked><label for="savemess"><?=GetMessage("SOHRANIT_POCHTOVUE_SHABLONU")?></label></p><br />
		<input type="submit" name="inst" value="<?=GetMessage("UDALIT_MODYL")?>">
	</form>
