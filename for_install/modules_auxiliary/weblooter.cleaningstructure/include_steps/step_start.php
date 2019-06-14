<?
$MODULE_PATH = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/weblooter.cleaningstructure';
IncludeModuleLangFile(__FILE__);
?>

<div class="adm-detail-content">
    <div class="adm-detail-title">
        <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_NASTROYKA_SKANIROVAN")?></div>
    <div class="adm-detail-content-item-block">
        <form id="weblooter_cleaningstructure" name="weblooter_cleaningstructure" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
            <input type="hidden" name="STEP" value="1" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <strong><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_DANNYY_MODULQ_SOVERS")?></strong><br/>
                    <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_MODULQ_NE_PREDPRIMET")?></div>
            </div>
            <p><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_NA_ETOM_ETAPE_MODULQ")?></p><br/>
            <table class="adm-detail-content-table edit-table">
                <tbody>
                <tr class="heading">
                    <td colspan="2"><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_NASTROYKI_SKANIROVAN")?></td>
                </tr>
                <tr>
                    <td width="35%"><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_PAPKA_PO_UMOLCANIU_D")?></td>
                    <td><input type="text" name="UPLOAD_DIRECTORY" required value="/upload/" size="40" /></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="adm-info-message-wrap">
                            <div class="adm-info-message">
                                <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_DANNOE_ZANCENIE_AVLA")?><a href="/bitrix/admin/settings.php?mid=main"><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_NASTROYKAH_GLAVNOGO")?></a>, <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_TO_NE_MENAYTE_ZNACEN")?></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="35%" valign="top">
                        <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_DIREKTORII_DLA_ISKLU")?></td>
                    <td>
                        <input type="text" name="DIR_IGNORE" size="70" value="resize_cache;sale" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="adm-info-message-wrap">
                            <div class="adm-info-message">
                                <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_PERECISLETI_DIREKTOR")?><strong>;</strong>"
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="35%" valign="top">
                        <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_FAYLY_DLA_ISKLUCENIA")?></td>
                    <td>
                        <input type="text" name="FILE_IGNORE" size="70" value=".htaccess" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="adm-info-message-wrap">
                            <div class="adm-info-message">
                                <?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_PERECISLETI_FAYLY_K")?><strong>;</strong>"
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<div class="adm-detail-content-btns-wrap adm-detail-content-btns-fixed adm-detail-content-btns-pin">
    <div class="adm-detail-content-btns">
        <input type="submit" form="weblooter_cleaningstructure" value="<?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_ZAPUSTITQ_SKANIROVAN")?>" class="adm-btn-save" />
    </div>
</div>