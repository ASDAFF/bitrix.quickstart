<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("BEL_UNINST_OK"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
  <input type="hidden" name="lang" value="<?=LANG?>">
  <input type="submit" name="" value="<?=GetMessage("BEL_BACK")?>"> 
<form>
