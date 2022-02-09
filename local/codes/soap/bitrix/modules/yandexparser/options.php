<?
$module_id = "yandexparser";

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php"); 
IncludeModuleLangFile(__FILE__); 
CModule::IncludeModule($module_id); 

if($_REQUEST['settings'] == 'proxy'){  

    if($_REQUEST["Apply"]){  
        
        $proxyList = $_REQUEST['proxyList'];
        yandexProxy::save($proxyList);
        
    } else {
        
        $proxyList = yandexProxy::open();
        
    }
    
    $aTabs = array(
         array("DIV" => "edit1", 
               "TAB" => 'Настройка прокси-серверов', 
               "ICON" => "translate_settings",
               "TITLE" => 'Настройка прокси-серверов'),
         array("DIV" => "edit2", 
               "TAB" => 'Просмотр заблокированных прокси-серверов', 
               "ICON" => "translate_settings",
               "TITLE" => 'Заблокированные прокси-сервера'),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs); 
    $tabControl->Begin();
    ?>
    <form method="POST" action="/bitrix/admin/settings.php?lang=ru&mid=yandexparser&mid_menu=1&settings=proxy"><?
    $tabControl->BeginNextTab(); 
    ?>  
    <a href="/bitrix/admin/settings.php?lang=ru&mid=yandexparser&mid_menu=1">Вернуться к основным настройкам</a>
    <hr>
    <p>Список прокси-серверов, каждый в новой строке:</p>    
    <textarea name="proxyList" style="width: 270px; height: 250px;"><?=$proxyList;?></textarea>
    <? $tabControl->BeginNextTab(); 
    ?>
    <? 
    $blockedProxy = yandexProxy::getBlockedList();
    if($blockedProxy){
        
    } else {
        ?><p>Заблокиованных прокси нет</p><?
    }
    ?> 
    <?$tabControl->Buttons();?>
    <input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
    <?=bitrix_sessid_post();?> 
    <?$tabControl->End();?>
    </form> 
    <?
} else {

    $TRANS_RIGHT = $APPLICATION->GetGroupRight($module_id);
    if ($TRANS_RIGHT>="R"){

    if ($REQUEST_METHOD=="GET" && $TRANS_RIGHT=="W" && strlen($RestoreDefaults)>0)
    {
            COption::RemoveOption($module_id);
            $z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
            while($zr = $z->Fetch())
                    $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
    }

    CModule::IncludeModule('iblock');
    $arIBlocks=Array();
    $db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array());
    while($arRes = $db_iblock->Fetch())
            $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

    $arAllOptions =	Array(
            array("ACTIVE",         "Активность", "", array('checkbox', "Y")),
            array("IBLOCK",         'Каталог(и) c товарами', "U", Array("selectbox",$arIBlocks)), 
            array("PROP_CODE",      'Код свойства с YandexID', "", array("text", "")), 
            array("SOAP_SERVER",    'Адрес SOAP-сервера', "", array("text","")),
            array("CNT",            'Сколько товаров отправляем за раз', "", array("text","")),
            array("USE_PROXY",      "Используем прокси <br>
                    (<a href='/bitrix/admin/settings.php?lang=ru&mid=yandexparser&mid_menu=1&settings=proxy'>Настройка</a>)", "", array('checkbox', "Y")),
            array("ANTIGATE_KEY",   'Ключ от учетной записи antigate.com', "", array("text","")),
        );   

    $aTabs = array(
            array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "translate_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
            array("DIV" => "edit2", "TAB" => 'Доступ', "ICON" => "translate_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    if(($REQUEST_METHOD=="POST") && (strlen($Update.$Apply.$RestoreDefaults)>0) && ($TRANS_RIGHT=="W") && check_bitrix_sessid())
    {
            if(strlen($RestoreDefaults)>0)
            {
                    COption::RemoveOption($module_id);
                    $z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
                    while($zr = $z->Fetch())
                            $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
            }
            else
            {
                    foreach($arAllOptions as $option)
                    {
                            if(!is_array($option))
                                    continue;

                            $name = $option[0];
                            $val = ${$name};
                            if($option[3][0] == "checkbox" && $val != "Y")
                                    $val = "N";
                            if($option[3][0] == "multiselectbox")
                                    $val = @implode(",", $val);

                            COption::SetOptionString($module_id, $name, $val, $option[1]);
                    }
            }

            $Update = $Update.$Apply;
            ob_start();
            require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
            ob_end_clean();

            if(strlen($_REQUEST["back_url_settings"]) > 0)
            {
                    if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
                            LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
                    else
                            LocalRedirect($_REQUEST["back_url_settings"]);
            }
            else
            {
                    LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&".$tabControl->ActiveTabParam());
            }
    }

    ?>
    <?
    $tabControl->Begin();
    ?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
    $tabControl->BeginNextTab();
    __AdmSettingsDrawList($module_id, $arAllOptions);
    $tabControl->BeginNextTab();?>
    <?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
    <?$tabControl->Buttons();?>
            <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
            <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
            <?if(strlen($_REQUEST["back_url_settings"])>0):?>
                    <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
                    <input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
            <?endif?>
            <input <?if ($TRANS_RIGHT<"W") echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
            <?=bitrix_sessid_post(); 
     $tabControl->End();?>
    </form>
    <?}?>


<?}?>