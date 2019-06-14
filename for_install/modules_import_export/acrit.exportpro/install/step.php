<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(GetMessage("ACRIT_EXPORTPRO_MODULQ"));

IncludeModuleLangFile(__FILE__);

//$instruction = GetMessage('ACRIT_EXPORTPRO_INSTRUCTIONS');
//$h2 = array_shift($instruction);

?>

<table id="instruction">
	<?/*<tr class="heading"><td colspan="2"><?=$h2?></td></tr>
    <tr>
        <td align="center" colspan="2">
            <ul>
                <?foreach ($instruction as $line):?>
                    <li><?=$line?></li>
                <?endforeach?>
            </ul>
        </td>
    </tr>
    <tr class="heading about "><td colspan="2"><?=implode('<br>', GetMessage('ACRIT_EXPORTPRO_ABOUT'))?></td></tr>*/?>
	<tr>
        <td>
            <?= GetMessage('ACRIT_EXPORTPRO_RECOMMENDS');?>
        </td>
    </tr>
    <tr class="">
		<td>
            <form action="/bitrix/admin/partner_modules.php" method="GET">
                <input type="submit" class="adm-btn adm-btn-save" value="<?=GetMessage("MOD_BACK")?>" />
            </form>  
			<form action="/bitrix/admin/partner_modules.php" method="GET">
				<input type="hidden" name="id" value="acrit.exportpro">
                <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
                <input type="hidden" name="install" value="Y">
                <input type="hidden" name="sessid" value="<?=bitrix_sessid()?>">
                <input type="hidden" name="step" value="2">
                <input type="submit" class="adm-btn adm-btn-save" value="<?=GetMessage("MOD_INSTALL")?>" />
			</form>
		</td>
	</tr>
</table>
<style>
	table#instruction{
		width: 100%;
	}
	table#instruction form{
		/*text-align: right;*/
		height: 40px;
		margin-top: 20px;
		/*margin-right: 30px;*/
        display: inline-block;
	}
    table#instruction tr td{
        font-size: 13px;
        line-height: 17px;
    }
</style>