<?
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<b><?echo GetMessage('VACANCY_IS_NEED_CREATE_IBLOCK')?> </b><br>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="hidden" name="id" value="mcart.vacancy">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="2">
	<input type="radio" value="1" name="isnewiblock" checked> <?echo GetMessage('VACANCY_CREATE_IBLOCK')?> <br>
    <input type="radio" value="0" name="isnewiblock" >  <?echo GetMessage('VACANCY_SELECT_IBLOCK')?><br>
	<input type="submit" name="inst" value="<?echo GetMessage("VACANCY_CONTINUE")?>">
<form>