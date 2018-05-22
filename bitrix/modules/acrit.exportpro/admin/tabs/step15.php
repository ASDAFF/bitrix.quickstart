<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );
IncludeModuleLangFile( __FILE__ );
?>

<tr class="heading">
    <td colspan="2"><?=GetMessage( "ACRIT_EXPORTPRO_FAQ_BASE" )?></td>
</tr>
<tr>
    <td colspan="2" align="center">
        <a href="http://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/" target="_blank">http://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/</a>
    </td>
</tr>
<tr>
    <td class="heading" colspan="2"><?=GetMessage( "SC_FRM_1" );?></td>
</tr>
<tr>
    <td valign="top" class="adm-detail-content-cell-l"><span class="required">*</span><?=GetMessage( "SC_FRM_2" );?><br>
            <small><?=GetMessage( "SC_FRM_3" );?></small></td>
    <td valign="top" class="adm-detail-content-cell-r"><textarea cols="60" rows="6" name="ticket_text_proxy"
        id="ticket_text_proxy"></textarea>
        <textarea style="display:none" name="ticket_text_log" id="ticket_text_log">
            <b><?=GetMessage( "ACRIT_EXPORTPRO_LOG_STATISTICK" )?></b><br>
            <b><?=GetMessage( "ACRIT_EXPORTPRO_LOG_ALL" )?></b><br>
            <?=GetMessage( "ACRIT_EXPORTPRO_LOG_ALL_IB" )?> <?=$arProfile["LOG"]["IBLOCK"]?><br>
            <?=GetMessage( "ACRIT_EXPORTPRO_LOG_ALL_SECTION" )?> <?=$arProfile["LOG"]["SECTIONS"]?><br>
            <?=GetMessage( "ACRIT_EXPORTPRO_LOG_ALL_OFFERS" )?> <?=$arProfile["LOG"]["PRODUCTS"]?><br>
            <b><?=GetMessage( "ACRIT_EXPORTPRO_LOG_EXPORT" )?></b><br>
            <?=GetMessage( "ACRIT_EXPORTPRO_LOG_OFFERS_EXPORT" )?> <?=$arProfile["LOG"]["PRODUCTS_EXPORT"]?><br>
            <b><?=GetMessage( "ACRIT_EXPORTPRO_LOG_ERROR" )?></b><br>
            <?=GetMessage( "ACRIT_EXPORTPRO_LOG_ERR_OFFERS" )?> <?=$arProfile["LOG"]["PRODUCTS_ERROR"]?><br>
            <?if( file_exists( $_SERVER["DOCUMENT_ROOT"].$arProfile["LOG"]["FILE"] ) ):?>
                <?=GetMessage( "ACRIT_EXPORTPRO_LOG_FILE" )?> <?=$arProfile["LOG"]["FILE"]?><br>
            <?endif?>
        </textarea>
        </td>
</tr>
<tr>
    <td class="adm-detail-content-cell-l"></td>
    <td class="adm-detail-content-cell-r">
        <input type="button" value="<?=GetMessage( "SC_FRM_4" );?>" onclick="SubmitToSupport()" name="submit_button">
    </td>
</tr>
<tr>
    <td colspan="2">
        <?=BeginNote();?>
            <?=GetMessage( "SC_TXT_1" );?> <a href="<?=GetMessage( "A_SUPPORT_URL" );?>"><?=GetMessage( "A_SUPPORT_URL" );?></a>
        <?=EndNote();?>
    </td>
</tr>
<tr>
	<td colspan="2">
		<?=GetMessage( "ACRIT_EXPORTPRO_RECOMMENDS" );?>
	</td>
</tr>