<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_SEARCH_CLEAR_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("NOVAGROUP_SEARCH_CLEAR_TITLE")),
    );

    $messageNote = array();
    if($_REQUEST['clear_stat']=='Y')
    {
        CModule::IncludeModule("search");
        $DB = CDatabase::GetModuleConnection('search');
        $DB->Query("DELETE FROM b_search_phrase");
        $messageNote[] = GetMessage("NOVAGROUP_SEARCH_CLEAR_OK");
    }
    if(count($messageNote)>0)
    {
        echo CAdminMessage::ShowNote(implode("<br>",$messageNote));
    }


    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="fs1">
    <?
            $tabControl->Begin();
            $tabControl->BeginNextTab();   
    ?>
    <tr>
        <td width="40%"><?echo GetMessage("NOVAGROUP_CLEAR_STATISTIC")?></td>
        <td width="60%"><input type="checkbox" name="clear_stat" value="Y"></td>
    </tr>
    <?
            $tabControl->Buttons();
    ?>
    <input class="mybutton" 
        type="submit" name="save" 
        value="<?echo GetMessage("NOVAGROUP_SEARCH_CLEAR_BUTTON_VALUE")?>" 
        title="<?echo GetMessage("NOVAGROUP_SEARCH_CLEAR_BUTTON_TITLE")?>" />
    <?
            $tabControl->End();
    ?>
</form>
<?echo BeginNote();?>
<?= GetMessage("NOVAGROUP_SEARCH_CLEAR_NOTE"); ?>
<?echo EndNote(); ?>
