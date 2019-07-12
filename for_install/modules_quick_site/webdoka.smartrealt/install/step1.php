<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt';

$sOldToken = COption::GetOptionString($module_id, "TOKEN");
?>
<form action="<?php echo $APPLICATION->GetCurPage ()?>"    name="smartrealt_install">
<?php echo bitrix_sessid_post ()?>
    <input type="hidden" name="lang" value="<?php echo LANG?>"> 
    <input type="hidden" name="id" value="<?php echo $module_id?>"> 
    <input type="hidden" name="install" value="Y"> 
    <input type="hidden" name="step" value="2">
    <?php
    // на этой странице можно установить какие-либо параметры                

    ?>
    <table class="list-table">
    <tr class="head">
        <td colspan="2"><?php echo GetMessage ( "PARAMS_TITLE" )?></td>
    </tr>
    <tr>
        <td width="50%" align="right"><?php
        echo GetMessage ( "SM_TOKEN" )?>:</td>
        <td><input type="text" name="Token" id="Token" value="<?php echo $sOldToken;?>" maxlength="36" size="50"></td>
    </tr>
</table>

<br>
<input type="submit" name="inst" value="<?php echo GetMessage ( "MOD_INSTALL" )?>">
</form>