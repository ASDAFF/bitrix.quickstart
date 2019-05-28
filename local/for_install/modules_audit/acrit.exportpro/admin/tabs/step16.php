<?IncludeModuleLangFile( __FILE__ );?>
                                     
<tr class="heading" align="center">
    <td colspan="2"><b><?=GetMessage( "ACRIT_EXPORTPRO_OP_OZON_TITLE" )?></b></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[OZON_APPID]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[OZON_APPID]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_OP_OZON_APPID_HELP" )?>' );</script>
        <label for="PROFILE[OZON_APPID]"><b><?=GetMessage( "ACRIT_EXPORTPRO_OP_OZON_APPID" )?></b> </label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" size="30" name="PROFILE[OZON_APPID]" value="<?=$arProfile["OZON_APPID"];?>" />
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[OZON_APPKEY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[OZON_APPKEY]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_OP_OZON_APPKEY_HELP" )?>' );</script>
        <label for="PROFILE[OZON_APPKEY]"><b><?=GetMessage( "ACRIT_EXPORTPRO_OP_OZON_APPKEY" )?></b> </label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" size="30" name="PROFILE[OZON_APPKEY]" value="<?=$arProfile["OZON_APPKEY"];?>" />
    </td>
</tr>