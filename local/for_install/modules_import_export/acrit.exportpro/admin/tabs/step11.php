<?php
    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
    IncludeModuleLangFile( __FILE__ );
?>
<tr class="heading">
    <td colspan="2"><?=GetMessage('ACRIT_EXPORTPRO_LOG_STATISTICK')?></td>
</tr>
<tr id="log_detail">
    <td colspan="2" align="center">
        <table width="30%" border="1">
            <tbody>
                <tr>
                    <td colspan="2" align="center"><b><?=GetMessage('ACRIT_EXPORTPRO_LOG_ALL')?></b></td>
                </tr>
                <tr>
                    <td width="50%"><?=GetMessage('ACRIT_EXPORTPRO_LOG_ALL_IB')?></td>
                    <td width="50%"><?=$arProfile['LOG']['IBLOCK']?></td>
                </tr>
                <tr>
                    <td width="50%"><?=GetMessage('ACRIT_EXPORTPRO_LOG_ALL_SECTION')?></td>
                    <td width="50%"><?=$arProfile['LOG']['SECTIONS']?></td>
                </tr>
                <tr>
                    <td width="50%"><?=GetMessage('ACRIT_EXPORTPRO_LOG_ALL_OFFERS')?></td>
                    <td width="50%"><?=$arProfile['LOG']['PRODUCTS']?></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><b><?=GetMessage('ACRIT_EXPORTPRO_LOG_EXPORT')?></b></td>
                </tr>
                <tr>
                    <td width="50%"><?=GetMessage('ACRIT_EXPORTPRO_LOG_OFFERS_EXPORT')?></td>
                    <td width="50%"><?=$arProfile['LOG']['PRODUCTS_EXPORT']?></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"><b><?=GetMessage('ACRIT_EXPORTPRO_LOG_ERROR')?></b></td>
                </tr>
                <tr>
                    <td width="50%"><?=GetMessage('ACRIT_EXPORTPRO_LOG_ERR_OFFERS')?></td>
                    <td width="50%"><?=$arProfile['LOG']['PRODUCTS_ERROR']?></td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
<tr id="log_detail_file">
    <?if(file_exists($_SERVER['DOCUMENT_ROOT'].$arProfile['LOG']['FILE'])):?>
        <td width="50%" style="padding: 15px 0;"><b><?=GetMessage('ACRIT_EXPORTPRO_LOG_FILE')?></b></td>
        <td width="50%"><a href="<?=$arProfile['LOG']['FILE']?>" target="_blank" download="export_log"><?=$arProfile['LOG']['FILE']?></a></td>
    <?endif?>
</tr>

</div>
<tr align="center">
    <td colspan="2">
        <a class="adm-btn adm-btn-save" onclick="UpdateLog(this)" profileID="<?=$arProfile['ID']?>"><?=GetMessage('ACRIT_EXPORTPRO_LOG_UPDATE')?></a>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?=GetMessage("ACRIT_EXPORTPRO_LOG_ALL_STAT")?></td>
</tr>
<?/*<tr>
    <td colspan="2" align="center"><?=GetMessage("ACRIT_EXPORTPRO_LOG_OPEN")?><a href="/upload/acrit_exportpro/export_log.php">/upload/acrit_exportpro/export_log.php</a></td>
</tr>*/?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="PROFILE[SEND_LOG_EMAIL]"><?=GetMessage("ACRIT_EXPORTPRO_LOG_SEND_EMAIL")?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" name="PROFILE[SEND_LOG_EMAIL]" placeholder="email@email.com" size="30" value="<?=$arProfile["SEND_LOG_EMAIL"];?>">
    </td>
</tr>

