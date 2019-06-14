<?
$module_id = "acrit.exportpro";


$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT >= "R"):
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
	Cmodule::IncludeModule($module_id);
	IncludeModuleLangFile(__FILE__);
    
    AcritLicence::Show();
    
	$aTabs = array(
		array(
			"DIV" => "edit1",
			"TAB" => GetMessage("MAIN_TAB_RIGHTS"),
            "ICON" => "main_settings",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
		),
		array(
			"DIV" => "edit2",
			"TAB" => GetMessage("MAIN_TAB_SUPPORT"),
            "ICON" => "main_settings",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_SUPPORT")
		),
	);

	$tabControl = new CAdminTabControl("tabControl", $aTabs);

	if ($REQUEST_METHOD == "POST" && strlen($Update . $Apply . $RestoreDefaults) > 0 && $POST_RIGHT == "W" && check_bitrix_sessid()){
		$Update = $Update . $Apply;
		ob_start();
		require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
		ob_end_clean();
		if (strlen($_REQUEST["back_url_settings"]) > 0){
			if ((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0)){
				LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . urlencode(LANGUAGE_ID) . "&back_url_settings=" . urlencode($_REQUEST["back_url_settings"]) . "&" . $tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect($_REQUEST["back_url_settings"]);
			}
		}
		else{
			LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($module_id) . "&lang=" . urlencode(LANGUAGE_ID) . "&" . $tabControl->ActiveTabParam());
		}
	}
	                                       
    require __DIR__."/admin/auto_tests.php";?>
    
	<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($module_id) ?>&amp;lang=<?= LANGUAGE_ID ?>">
		<?
		$tabControl->Begin();
		?>
	<?
	
	$tabControl->BeginNextTab();
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
	
	$tabControl->BeginNextTab();
	?>
		<tr>
            <td class="heading" colspan="2"><?=GetMessage( "SC_FRM_1" );?></td>
        </tr>
        <tr>
            <td valign="top" class="adm-detail-content-cell-l"><span class="required">*</span><?=GetMessage( "SC_FRM_2" );?><br>
                    <small><?=GetMessage( "SC_FRM_3" );?></small></td>
            <td valign="top" class="adm-detail-content-cell-r"><textarea cols="60" rows="6" name="ticket_text_proxy"
                id="ticket_text_proxy"><?=htmlspecialcharsbx( implode( "\n", $arAutoProblemsToSupportMessage ) );?></textarea></td>
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
		
		<? $tabControl->Buttons(); ?>
		<input <? if ($POST_RIGHT < "W") echo "disabled" ?> class="adm-btn-save" type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>" title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>">
		<input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="Apply" value="<?= GetMessage("MAIN_OPT_APPLY") ?>" title="<?= GetMessage("MAIN_OPT_APPLY_TITLE") ?>">
		<? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
			<input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="button" name="Cancel" value="<?= GetMessage("MAIN_OPT_CANCEL") ?>" title="<?= GetMessage("MAIN_OPT_CANCEL_TITLE") ?>" onclick="window.location = '<? echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
			<input type="hidden" name="back_url_settings" value="<?= htmlspecialcharsbx($_REQUEST["back_url_settings"]) ?>">
		<? endif ?>
		<input <? if ($POST_RIGHT < "W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>" OnClick="return confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>')" value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">
		<?= bitrix_sessid_post(); ?>
		<? $tabControl->End(); ?>
	</form>
    
    <form target="_blank" name="fticket" action="<?=GetMessage( "A_SUPPORT_URL" );?>" method="POST">
        <input type="hidden" name="send_ticket" value="Y">
        <input type="hidden" name="ticket_title" value="<?=GetMessage( "SC_RUS_L1" )." ".htmlspecialcharsbx( $_SERVER["HTTP_HOST"] );?>">
        <input type="hidden" name="ticket_text" value="Y">
    </form>
<? endif; ?>

<script type="text/javascript">
    function SubmitToSupport(){
        var frm = document.forms.fticket;

        frm.ticket_text.value = BX( 'ticket_text_proxy' ).value;

        if( frm.ticket_text.value == '' ){
            alert( '<?=GetMessage( "SC_NOT_FILLED" )?>' );
            return;
        }

        frm.submit();
    }
</script>