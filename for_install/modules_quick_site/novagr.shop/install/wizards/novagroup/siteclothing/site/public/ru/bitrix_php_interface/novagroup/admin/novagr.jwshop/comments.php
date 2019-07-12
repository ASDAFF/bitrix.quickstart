<?
if (!defined("ADMIN_SECTION") || ADMIN_SECTION !== true ) return;

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("NOVAGROUP_COMMENTS_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("NOVAGROUP_COMMENTS_TITLE")),
    );

    $arResult = unserialize(COption::GetOptionString("novagroup","comments"));
    
    if (!isset($arResult["on"])) {
    	$arResult["on"] = "1";
    	$content = serialize($arResult);
        COption::SetOptionString("novagroup","comments",$content);
    }
       
    if (!empty($_REQUEST["save"]))
    {
       
        echo CAdminMessage::ShowNote(GetMessage("NOVAGROUP_COMMENTS_OK"));

        $arResult["on"] = (int)$_REQUEST['commentsCheck'];
        $content = serialize($arResult);
        COption::SetOptionString("novagroup","comments",$content);
		
		BXClearCache(true, "");
    }
    

    $tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="fs1">
    <?
            $tabControl->Begin();
            $tabControl->BeginNextTab();   
    ?>
    <tr>
        <td width="40%"><?echo GetMessage("NOVAGROUP_COMMENTS_CHECK")?></td>
        <td width="60%"><input type="checkbox" name="commentsCheck" <? if ($arResult["on"] == 1) echo 'checked';?> value="1"></td>
     </tr>
    <?
            $tabControl->Buttons();
    ?>
    <input class="mybutton" 
        type="submit" name="save" 
        value="<?echo GetMessage("NOVAGROUP_COMMENTS_SAVE")?>" 
        title="<?echo GetMessage("NOVAGROUP_COMMENTS_SAVE")?>" />
    <?
            $tabControl->End();
    ?>
</form>
<?echo BeginNote();?>
<?= GetMessage("NOVAGROUP_COMMENTS_NOTE"); ?>
<?echo EndNote(); ?>
