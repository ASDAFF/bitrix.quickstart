<?IncludeModuleLangFile( __FILE__ );?>
                                     
<tr class="heading" align="center">
    <td colspan="2"><b><?=GetMessage( "ACRIT_EXPORTPRO_OP_UA_HOTLINE_UA_TITLE" )?></b></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[HOTLINE_FIRM_ID]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[HOTLINE_FIRM_ID]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_OP_UA_HOTLINE_UA_FIRMID_HELP" )?>' );</script>
        <label for="PROFILE[HOTLINE_FIRM_ID]"><b><?=GetMessage( "ACRIT_EXPORTPRO_OP_UA_HOTLINE_UA_FIRMID" )?></b> </label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" size="30" name="PROFILE[HOTLINE_FIRM_ID]" value="<?=$arProfile["HOTLINE_FIRM_ID"];?>" />
    </td>
</tr>