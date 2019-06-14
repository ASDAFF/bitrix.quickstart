<? IncludeModuleLangFile(__FILE__); 
if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default/images/prmedia.sape_screen.jpg'))
{
	copy($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/prmedia.sape/images/sape_add_screen.jpg', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default/images/prmedia.sape_screen.jpg');	
}

?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1" method="post" enctype="multipart/form-data">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="id" value="prmedia.sape">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">
	<h1><?=GetMessage('PRMEDIA_STEP1_TITLE')?></h1>
    <table cellpadding="3" cellspacing="0" border="0" width="100%">
        <tr>
            <td><p><?=GetMessage('PRMEDIA_STEP1_FILE')?></p></td>
            <td style="width:100%;"><input type="file" name="file"></td>
        </tr>
        <tr>
        	<td colspan="2">
            	<?=CAdminMessage::ShowNote(GetMessage('PRMEDIA_STEP1_INSTRUCTION'));?>
                <p><?=GetMessage('PRMEDIA_STEP1_ARCHIVE')?>:</p>
            	<img src="/bitrix/themes/.default/images/prmedia.sape_screen.jpg" style="border: solid 1px #000" alt="" />
            </td>
        </tr>
        <tr>
    </table>
	<input type="submit" name="inst" value="<?=GetMessage('PRMEDIA_STEP1_SUBMIT')?>">
</form>