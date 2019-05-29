<?
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="hidden" name="id" value="mcart.vacancy">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="5">

	<?echo GetMessage('VACANCY_ERROR_MESSAGE')?>
	<br>
	
	<br>
	<input type="submit" name="inst" value="OK">
<form>