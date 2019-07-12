<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt'; ;
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $module_id . '/include.php');

// проверка прав
$RIGHTS = $APPLICATION->GetGroupRight($module_id);
if ($RIGHTS < 'R') $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));

if (CModule::IncludeModule($module_id))
{
    IncludeModuleLangFile(__FILE__);
    // Восстановление настроек по умолчанию
    if ($REQUEST_METHOD == 'GET' && $RIGHTS == 'W' && isset($_GET['RestoreDefaults']) && $_GET['RestoreDefaults'] === 'Y')
    {
        COption::RemoveOption($module_id);              
        $z = CGroup::GetList($v1 = 'id', $v2 = 'asc', array('ACTIVE' => 'Y', 'ADMIN' => 'N'));
        while($zr = $z->Fetch())
        {
            $APPLICATION->DelGroupRight($module_id, array($zr['ID']));
        }              
        LocalRedirect($APPLICATION->GetCurPageParam('', array('RestoreDefaults')));
        die();
    }
    
    $arGroups = array();
    $rsGr = CGroup::GetList($by='NAME', $order='asc', array('ACTIVE'=>'Y'));
    while ($arGr = $rsGr->GetNext())
        $arGroups[$arGr['ID']] = '['.$arGr['ID'].'] '.$arGr['NAME'];
    
    $arOptions = array (
        array(
            "TAB" => GetMessage("SM_MAIN_TAB"),
            "ICON" => "",
            "TITLE" => GetMessage("SM_MAIN_TAB_TITLE"),
            "WITH_CHECK" => 'Y',   //параметр означает что на вкладке необходимы будут проверки
            "ITEMS" => array( 
                array("TYPE" => "TEXT", "NAME" => "TOKEN", "TITLE" => GetMessage("OPT_TOKEN"), "VALUE" => "", "PARAMS" => "size='50' maxlength='36'"),
                array("TYPE" => "LIST", "NAME" => "MAP_TYPE", "TITLE" => GetMessage("OPT_MAP_TYPE"), "DATA" => array('google' => 'Google Maps', 'yandex' => GetMessage("OPT_YANDEX_MAP")), "PARAMS" => "size='1'"),                
                array("TYPE" => "TEXT", "NAME" => "ELEMENT_ON_PAGE", "TITLE" => GetMessage("OPT_ELEMENT_ON_PAGE"), "VALUE" => SmartRealt_Common::GetElementCountonPage(), "PARAMS" => "size='13' maxlength='5'"),
                array("TYPE" => "CHECKBOX", "NAME" => "SHOW_EMPTY_PARAMETERS", "TITLE" => GetMessage("OPT_SHOW_EMPTY_PARAMETERS"), "VALUE" => SmartRealt_Options::GetShowEmptyParameters(), "PARAMS" => ""),
            ),
        ),
        array(
            "TAB" => GetMessage("SM_URL_TAB"),
            "ICON" => "",
            "TITLE" => GetMessage("SM_URL_TAB_TITLE"),
            "WITH_CHECK" => 'Y',   //параметр означает что на вкладке необходимы будут проверки
            "ITEMS" => array( 
                array("TYPE" => "TEXT", "NAME" => "SEF_FOLDER", "TITLE" => GetMessage("OPT_SEF_FOLDER"), "VALUE" => "/", "PARAMS" => "size='50'"),                
                array("TYPE" => "TEXT", "NAME" => "CATALOG_LIST_URL", "TITLE" => GetMessage("OPT_CATALOG_LIST_URL"), "VALUE" => SMARTREALT_CATALOG_LIST_URL_DEF, "PARAMS" => "size='50'"),                
                array("TYPE" => "TEXT", "NAME" => "CATALOG_DETAIL_URL", "TITLE" => GetMessage("OPT_CATALOG_DETAIL_URL"), "VALUE" => SMARTREALT_CATALOG_DETAIL_URL_DEF, "PARAMS" => "size='50'"),
            ),
        ),
        array(
            "TAB" => GetMessage("MAIN_TAB_RIGHTS"),
            "ICON" => "Settings",
            "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS"),
            "ITEMS" => array(
                array("TYPE" => "MODULE_RIGHTS")
            )
        )        
    );        

    /** сохранение **/
    $eSaveErrors = new CAdminException();
    $arErrors=array(); //все ошибки
    if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $RIGHTS=="W")  
    {     
        foreach ($arOptions as $i => $arTab)
        {   
            /** проверка правильности введенных данных **/
            if ($arTab['WITH_CHECK']=='Y')
            {  
                $arTabErrors=array(); //ошибки на вкладке  
                foreach ($arTab['ITEMS'] as $arOption)
                {     
                    switch ($arOption['NAME'])
                    {               
                        /*case "SMALL_PHOTO_WIDTH":                
                        case "SMALL_PHOTO_HEIGHT":
                        case "BIG_PHOTO_WIDTH":                
                        case "BIG_PHOTO_HEIGHT":
                        case "SPECIALIST_PHOTO_WIDTH":                
                        case "SPECIALIST_PHOTO_HEIGHT":
                            $value = trim($_POST[$arOption['NAME']]);
                            if (strlen($value) == 0)
                            {
                                $arTabErrors[]=array(
                                    'id' => $arOption['NAME'],
                                    'text' => GetMessage('SM_ERROR_FIELD_EMPTY',array('#FIELD_NAME#'=>GetMessage('OPT_'.$arOption['NAME'])))
                                    );    
                            }
                            elseif (!preg_match('/^([0-9]{1,4})$/i', $value) || $value<=0)
                            {
                                $arTabErrors[]=array(
                                    'id' => $arOption['NAME'],
                                    'text' => GetMessage('SM_ERROR_FIELD_NOT_CORRECT',array('#FIELD_NAME#'=>GetMessage('OPT_'.$arOption['NAME'])))
                                    );        
                            }
                            break; 
                        case "IMPORT_DB_NAME": 
                            $value = trim($_POST[$arOption['NAME']]);
                            if (strlen($value) == 0)
                            {
                                $arTabErrors[]=array(
                                    'id' => $arOption['NAME'],
                                    'text' => GetMessage('SM_ERROR_FIELD_EMPTY',array('#FIELD_NAME#'=>GetMessage('OPT_'.$arOption['NAME'])))
                                    );    
                            }
                            break;                           
                        case "SERVICE":        
                            foreach ($_POST['SERVICE'] as $i=>$arService)
                            {                           
                                $value = trim($arService['NAME']);
                                if (strlen($value) == 0)
                                {
                                    $arTabErrors[]=array(
                                        'id' => 'SERVICE_'.$i.'_NAME',
                                        'text' => GetMessage('SM_ERROR_FIELD_EMPTY',array('#FIELD_NAME#'=>GetMessage('OPT_'.'SERVICE_'.$i.'_NAME')))
                                        );    
                                }
                                $value = trim($arService['IMG']);
                                if (strlen($value) == 0)
                                {
                                    $arTabErrors[]=array(
                                        'id' => 'SERVICE_'.$i.'_IMG',
                                        'text' => GetMessage('SM_ERROR_FIELD_EMPTY',array('#FIELD_NAME#'=>GetMessage('OPT_'.'SERVICE_'.$i.'_IMG')))
                                        );    
                                }
                            }
                            break;                            
                        case "IMPORT_NEXT_EXEC":
                            $value = trim($_POST[$arOption['NAME']]); 
                            $sFormat = 'DD.MM.YYYY';
                            if ($arOption['TIME'])
                                $sFormat .= ' HH:MI:SS';
                            if (!$DB->IsDate($value, $sFormat))
                            {
                                $arTabErrors[]=array(
                                    'id' => $arOption['NAME'],
                                    'text' => GetMessage('SM_ERROR_FIELD_NOT_CORRECT',array('#FIELD_NAME#'=>GetMessage('OPT_'.$arOption['NAME'])))
                                    );
                            }
                            break;*/
                    }            
                }
                $arErrors=array_merge($arErrors,$arTabErrors);
                if (count($arTabErrors)>0) continue;  //пропускаем вкладку без сохранения
            }
            /****/
            
            foreach ($arTab['ITEMS'] as $arOption)
            {
                switch ($arOption['TYPE'])
                {    
                    case "TEXT":
                    case "DATE":
                    case "LIST":
                    case "RND":
                        COption::SetOptionString($module_id, $arOption["NAME"], $_POST[$arOption["NAME"]]);
                        break;
                    case "SERVICE":
                        foreach ($_POST['SERVICE'] as $i=>$arService)
                        {
                            COption::SetOptionString($module_id, 'SERVICE_'.$i.'_NAME', $arService['NAME']);
                            COption::SetOptionString($module_id, 'SERVICE_'.$i.'_IMG', $arService['IMG']);
                        }
                        break;
                    case "CHECKBOX":
                        $sVal=(isset($_POST[$arOption['NAME']]) && $_POST[$arOption['NAME']]=='Y')?'Y':'N';
                        COption::SetOptionString($module_id, $arOption["NAME"], $_POST[$arOption["NAME"]]);
                        break;
                }
            }            
        }
        foreach ($arErrors as $arError)
            $eSaveErrors->AddMessage($arError);        
    }
    
    SmartRealt_Common::CheckToken();
    
    /****/
    
    $aTabs=array();
    reset($arOptions);
    foreach ($arOptions as $i=>$arTab){ 
        $aTabs[] = array(
            "DIV"   => 'div'.$i,             
            "ICON"  => (strlen($arTab['ICON'])>0)?$arTab['ICON']:'Settings',
            "TAB"   => $arTab['TAB'],
            "TITLE" => $arTab['TITLE'],
        );        
    } 
    
    /** вывод ошибок **/
    if (count($eSaveErrors->GetMessages()) > 0)
    {
        $message = new CAdminMessage(GetMessage('SM_SAVE_ERROR'), $eSaveErrors);
        echo $message->Show();
    } 
    /****/  
   
    $tabControl = new CAdminTabControl('tabControl', $aTabs);
    $tabControl->Begin();
    ?>

    <form name='options' method='POST' action='<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&mid_menu=1&lang=<?=LANGUAGE_ID?>&<?=$tabControl->ActiveTabParam()?>'>
    <?=bitrix_sessid_post()?>
                                                                                    
    <?php
    reset($arOptions);
    foreach($arOptions as $i => $arTab)
    {
        $tabControl->BeginNextTab();
                
        foreach($arTab["ITEMS"] as $j => $arOption)
        {
            if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && $RIGHTS=="W")
                $val=$_POST[$arOption['NAME']];
            else
            {
                if ($arOption['NAME'] == 'SERVICE')
                {
                    $val = array();
                    for ($i=1;$i<=5;$i++)
                    {
                        $val[$i]['NAME'] = COption::GetOptionString($module_id, 'SERVICE_'.$i.'_NAME');        
                        $val[$i]['IMG'] = COption::GetOptionString($module_id, 'SERVICE_'.$i.'_IMG');          
                    }
                }
                else
                    $val = COption::GetOptionString($module_id, $arOption["NAME"]/*, $arOption["VALUE"]*/);
            }
            
            if ($arOption["TYPE"]=="AREA"){
                ?><tr class="heading">
                    <td colspan="2"><?=$arOption["TITLE"]?></td>
                </tr>
                <?
            }else{
                if (!in_array($arOption["TYPE"], array(
                        'SERVICE'
                        ))){    
                   ?><tr>
                        <td width="40%"><?=strlen($arOption["TITLE"])>0?$arOption["TITLE"].':':''?></td>
                        <td width="60%" nowrap><?
                } 
                switch ($arOption['TYPE']){
                    case 'TEXT':
                        ?><input type="text" value="<?=htmlspecialchars($val)?>" name="<?=$arOption["NAME"]?>" <?=(strlen($arOption["PARAMS"])) ? $arOption["PARAMS"] : 'maxlength="255"'?>><?
                        break;  
                    case 'DATE':
                        ?><input type="text" value="<?=htmlspecialchars($val)?>" name="<?=$arOption["NAME"]?>" <?=(strlen($arOption["PARAMS"])) ? $arOption["PARAMS"] : 'maxlength="255"'?>><?
                        echo CAdminCalendar::Calendar($arOption["NAME"], "", "", $arOption['TIME']);
                        break;  
                    case 'LIST':
                        ?>
                        <select name="<?=$arOption['NAME']?>" <?=$arOption["PARAMS"]?>>
                            <?
                            foreach ($arOption['DATA'] as $iKey=>$sVal)
                            {
                                ?><option value="<?=$iKey?>" <?=($val==$iKey)?'selected':''?>><?=$sVal?></option><?    
                            }
                            ?>
                        </select>
                        <?
                        break;  
                    case 'CHECKBOX':
                        ?><input type="checkbox" value="Y" <?=($val=='Y')?'checked':'';?> name="<?=$arOption['NAME']?>" <?=(strlen($arOption["PARAMS"])) ? $arOption["PARAMS"] : ''?>><?
                        break;
                    case 'SERVICE':
                        ?>
                        <tr>
                            <td colspan="2">
                            <style> 
                                table.serv td {
                                    vertical-align: middle;
                                }    
                            </style>
                            <table cellpadding="0" cellspacing="5" border="0" width="100%" class="serv">
                                <tr>
                                    <td class="field-name" width="30%">&nbsp;</td>
                                    <td width="205px"><b><?=GetMessage('OPT_SERVICE_NAME')?></b></td>
                                    <td width="305px"><b><?=GetMessage('OPT_SERVICE_IMG')?></b></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <?
                                foreach ($val as $i=>$arServ)
                                {
                                    ?>
                                    <tr>                                                                        
                                        <td class="field-name"><?=GetMessage('OPT_SERVICE_'.$i)?>:&nbsp;</td>   
                                        <td><input type="text" name="SERVICE[<?=$i?>][NAME]" value="<?=$arServ['NAME']?>" style="width:200px;"></td>
                                        <td>
                                            <input type="text" name="SERVICE[<?=$i?>][IMG]" value="<?=$arServ['IMG']?>" style="width:300px;">    
                                        </td>                                                                           
                                        <td><img src="<?=$arServ['IMG']?>" /></td>  
                                    </tr>
                                    <?
                                }    
                                ?>
                            </table>
                            </td>
                        </tr>
                        <?
                        break;
                    case "MODULE_RIGHTS":
                        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
                        break; 
                }
                if (!in_array($arOption["TYPE"], array(
                            'SERVICE'
                        ))){
                            ?></td>
                        </tr><?
                        }                                
            }
        }
           
    }
       
    $tabControl->Buttons();
    ?>

    <script language='JavaScript'>
    function RestoreDefaults()
    {
        if(confirm('<?=AddSlashes(GetMessage('SM_MOD_SET_DEFAULT_WARNING'))?>'))
        {
            window.location = '<?=$APPLICATION->GetCurPage()?>?RestoreDefaults=Y&mid_menu=1&lang=<?=LANGUAGE_ID?>&mid=<?=urlencode($mid)?>&<?=$tabControl->ActiveTabParam()?>';
        }
    }     
    </script>

    <input <?php if ($RIGHTS < 'W') echo 'disabled' ?> type='submit' name='Update' value='<?=GetMessage('SM_MOD_SAVE')?>'>
    <input type='hidden' name='Update' value='Y'>
    <input type='reset' class='button' name='reset' value='<?=GetMessage('SM_RESET')?>'>
    <input <?php if ($RIGHTS < 'W') echo 'disabled' ?> type='button' OnClick='RestoreDefaults();' value='<?=GetMessage('SM_MOD_DEFAULT')?>'>    

    <?php
    $tabControl->End();
    ?>

    </form>
    
    <script type="text/javascript">
    function RandomString(length) {
        var str = '';
        for ( ; str.length < length; str += Math.random().toString(36).substr(2) );
        return str.substr(0, length);
    }
    function GenerateRnd(id)
    {
        var s = 'wd'+RandomString(10);
        document.getElementById(id).value = s;
    }
    </script>
    
    <?
    if (count($eSaveErrors->GetMessages()) > 0)
    {
        echo $tabControl->ShowWarnings('options', $eSaveErrors);
    }       
}
else
{
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED')); 
}
?>
