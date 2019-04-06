<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("akazakov.reindex");

if (!CModule::IncludeModule("search")) {
CAdminMessage::ShowMessage(array(
            "MESSAGE"=>GetMessage("ALERT_ZAG"),
            "DETAILS"=> GetMessage("ALERT_DESC"),
            "HTML"=>true,
            "TYPE"=>"ERROR"
            
         ));
?>

<form action="/bitrix/admin/module_admin.php" method="GET" id="form_for_search">
				<input type="hidden" name="action" value="" id="action_for_search">
				<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
				<input type="hidden" name="id" value="search">
				<input type="hidden" name="sessid" id="sessid" value="<?=bitrix_sessid()?>">
				<br>
				<input style="display:block;margin:0 auto;" type="submit" class="adm-btn-green adm-btn-big" name="install" value="<?=GetMessage("INSTALL_SEARCH")?>">
</form>

<?
exit;
}
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @var CAdminMessage $message */

if(!empty($_GET['tabControl_active_tab']) && $_GET['tabControl_active_tab']=="about_window") {
	COption::SetOptionString("akazakov.reindex", 'informer', 'N');
	//print_r($_GET);
}

if($_POST['period']) {
    COption::SetOptionString("akazakov.reindex", 'period', $_POST['period']);
}
if($_POST['period']) {
    COption::SetOptionString("akazakov.reindex", 'clear_now', $_POST['clear_now']);
} else {
    COption::SetOptionString("akazakov.reindex", 'clear_now', 'N');
}





$bVarsFromForm = false;
$aTabs = array(
    array(
        "DIV" => "reindex_settings",
        "TAB" => GetMessage("AK_SETTINGS"),
        "ICON" => "search_settings",
        "TITLE" => GetMessage("AK_HOW"),
        "OPTIONS" => Array(
            "period" => Array(GetMessage("AK_PERIOD"), Array("select", array(
                "1" => GetMessage("AK_NIKOGDA"),
				"2" => GetMessage("AK_DAY"),
                "3" => GetMessage("AK_WEEK"),
                "4" => GetMessage("AK_MOUNTH"),
                "5" => GetMessage("AK_YEAR")
				
            ))),
            "clear_now" => array(GetMessage("AK_NOW"), Array("checkbox", "N"))
        )
    ),
	array(
			"DIV" => "about_window",
			"TAB" => GetMessage("ABOUT_TAB"),
			"ICON" => "",
			"TITLE" => "",
			"CONTENT" => GetMessage("DONATE_TEXT").GetMessage("DONATE_LINK")
				
		)

	
);

$tabControl = new CAdminTabControl("tabControl", $aTabs,false);

if($REQUEST_METHOD=="POST")
{
    if(COption::GetOptionString("akazakov.reindex","clear_now") == 'Y') {
        
		
        $NS = false;
		$NS = CSearch::ReIndexAll(true, 2, $NS);
		while(is_array($NS))
			$NS = CSearch::ReIndexAll(true, 2, $NS);
		//echo $NS;
		CAdminMessage::ShowMessage(array(
			"MESSAGE"=>GetMessage('AK_ADMZAG'),
			"DETAILS"=>GetMessage('AK_ADMTXT'),
			"HTML"=>true,
			"TYPE"=>"OK",
		));

    }



    if(COption::GetOptionString("akazakov.reindex","period")) {
        // Функция установки агента
        //  echo "Агент установлен";
        $res = CAgent::GetList(Array("ID" => "DESC"), array("NAME" => "CSearch::ReIndexAll(true, 2);"));
        while($res->NavNext(true, "agent_")):
            $agent_ID;
        endwhile;
        $time = 20;
        if(COption::GetOptionString("akazakov.reindex","period")== '1') {
            $time = '';
        }
		if(COption::GetOptionString("akazakov.reindex","period")== '2') {
            $time = 86400;
        }
        if(COption::GetOptionString("akazakov.reindex","period")== '3') {
            $time = 604800;
        }
        if(COption::GetOptionString("akazakov.reindex","period")== '4') {
            $time = 2592000;
        }
        if(COption::GetOptionString("akazakov.reindex","period")== '5') {
            $time = 31536000;
        }
		

        if (CAgent::Delete($agent_ID)) {
            $x = CAgent::AddAgent(
                "CSearch::ReIndexAll(true, 5);", // имя функции
                "search",                          // идентификатор модуля
                "N",                                  // агент не критичен к кол-ву запусков
                $time,                                // интервал запуска - 1 сутки
                date("d.m.Y H:i:s"),                // дата первой проверки на запуск
                "Y",                                  // агент активен
                date("d.m.Y H:i:s"),                // дата первого запуска
                30);
			//echo $x;
        }
    }
}
if(is_object($message))
    echo $message->Show();

$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>" id="options">
    <?=bitrix_sessid_post();?>
    <?
    foreach($aTabs as $aTab):
        //
        foreach($aTab["OPTIONS"] as $name => $arOption):
            if ($bVarsFromForm)
                $val = $_POST[$name];
            else
                $val = COption::GetOptionString("akazakov.reindex", $name);
            $type = $arOption[1];
            $disabled = array_key_exists("disabled", $arOption)? $arOption["disabled"]: "";
            ?>
            <tr <?if(isset($arOption[2])) echo 'style="display:none" class="show-for-'.htmlspecialcharsbx($arOption[2]).'"'?>>
                <td width="40%" <?if($type[0]=="textarea") echo 'class="adm-detail-valign-top"'?>>
                    <label for="<?echo htmlspecialcharsbx($name)?>"><?echo $arOption[0]?></label>
                <td width="60%">
                    <?if($type[0]=="checkbox"):?>
                        <input type="checkbox" name="<?echo htmlspecialcharsbx($name)?>" id="<?echo htmlspecialcharsbx($name)?>" value="Y" <?if($val=="Y")echo" checked";?><?if($disabled)echo' disabled="disabled"';?>><?if($disabled) echo '<br>'.$disabled;?>
                    <?elseif($type[0]=="text"):?>
                        <input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialcharsbx($val)?>" name="<?echo htmlspecialcharsbx($name)?>">
                    <?elseif($type[0]=="textarea"):?>
                        <textarea rows="<?echo $type[1]?>" name="<?echo htmlspecialcharsbx($name)?>" style=
                        "width:100%"><?echo htmlspecialcharsbx($val)?></textarea>
                    <?elseif($type[0]=="select"):?>
                        <select name="<?echo htmlspecialcharsbx($name)?>" onchange="doShowAndHide()">
                            <?foreach($type[1] as $key => $value):?>
                                <option value="<?echo htmlspecialcharsbx($key)?>" <?if ($val == $key) echo 'selected="selected"'?>><?echo htmlspecialcharsEx($value)?></option>
                            <?endforeach?>
                        </select>
                    <?elseif($type[0]=="note"):?>
                        <?echo BeginNote(), $type[1], EndNote();?>
                    <?endif?>
                </td>
            </tr>
        <?endforeach;
    endforeach;?>
    <?$tabControl->Buttons();?>
    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
    <input type="submit" name="Apply" value="<?=GetMessage("AK_PRIM")?>" title="<?=GetMessage("AK_PRIM")?>">
	
</form>
<?

$tabControl->BeginNextTab();

$tabControl->End();

?>

