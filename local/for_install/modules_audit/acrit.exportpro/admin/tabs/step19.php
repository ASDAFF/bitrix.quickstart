<?php
IncludeModuleLangFile(__FILE__);

$idConvertCnt = 0;                      
?>
<tr class="heading" align="center">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_CONVERT_FIELDSET_HEADER" )?></td>
</tr>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_EXPORTPRO_CONVERT_FIELDSET_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <table id="convert-fieldset-container" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
        <?foreach( $arProfile["CONVERT_DATA"] as $id => $fields ):?>
        <tr class="fieldset-item" data-id="<?=$idConvertCnt++?>">
            <td align="right">
                <input type="text" name="PROFILE[CONVERT_DATA][<?=$id?>][0]" value="<?=$fields[0]?>" />
            </td>
            <td align="left" style="position: relative" class="adm-detail-content-cell-r">
                <input type="text" name="PROFILE[CONVERT_DATA][<?=$id?>][1]" value="<?=$fields[1]?>" />
                <span class="fieldset-item-delete">&times</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <hr style="opacity: 0.2;">
            </td>
        </tr>
        <?endforeach?>
        </tbody>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2" align="center" id="fieldset-item-add-button">
        <button class="adm-btn" onclick="ConvertFieldsetAdd( this ); return false;">
            <?=GetMessage( 'ACRIT_EXPORTPRO_CONVERT_FIELDSET_CONDITION_ADD' )?>
        </button>
    </td>
</tr>