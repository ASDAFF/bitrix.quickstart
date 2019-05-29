<?
$iModuleID = "mibix.yamexport";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/include.php"); // инициализация модуля
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/prolog.php"); // пролог модуля

IncludeModuleLangFile(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if($POST_RIGHT=="D")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

// сформируем список закладок
$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("MIBIX_YAM_TAB_DATASOURCE"), "ICON" => "main_user_edit", "TITLE" => GetMessage("MIBIX_YAM_TAB_DATASOURCE_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID); // идентификатор редактируемой записи
$strError = ""; // сообщение об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.

// ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ
if($REQUEST_METHOD == "POST" && ($save != "" || $apply != "") && $POST_RIGHT >= "W" && check_bitrix_sessid())
{
    // обработка переменных filter
    $strFilters = "";
    if(isset($f_filter_name[0]) && isset($f_filter_unit[0]) && isset($f_filter_value[0]) && strlen($f_filter_name[0])>0 && strlen($f_filter_unit[0])>0 && strlen($f_filter_value[0])>0)
    {
        // количество параметров в массиве
        $cntPv = count($f_filter_name);

        // Проходимся по всем параметрам в массиве и преобразуем их к виду "name,unit,value|name,unit,value|name,unit,value.." для записи в базу
        $arStrFilters = array();
        for($i=0;$i<$cntPv;$i++)
        {
            if(isset($f_filter_name[$i]) && isset($f_filter_unit[$i]))
            {
                $strFiltName = (isset($f_filter_name[$i]))?$f_filter_name[$i]:"";
                $strFiltUnit = (isset($f_filter_unit[$i]))?$f_filter_unit[$i]:"";
                $strFiltValue = (isset($f_filter_value[$i]))?$f_filter_value[$i]:"";

                if(strlen($strFiltName)>0 && strlen($strFiltUnit)>0)
                    $arStrFilters[] = $strFiltName.",".$strFiltUnit.",".$strFiltValue;
            }
        }
        if(count($arStrFilters)>0) $strFilters = implode("|", $arStrFilters);
    }

    $datasource = new CMibixModelDataSource();

    // обработка данных формы
    $arFields = Array(
        "name_data"		    => $f_name_data,
        "shop_id"           => $f_shop_id,
        "site_id"		    => $f_site_id,
        "iblock_type"		=> $f_iblock_type,
        "iblock_id"		    => intval($f_iblock_id),
        "include_sections"  => ($f_include_sections!=null?$f_include_sections:''), // array
        "exclude_sections"	=> ($f_exclude_sections!=null?$f_exclude_sections:''), // array
        "include_items"	    => $f_include_items, // array
        "exclude_items"		=> $f_exclude_items, // array
        "include_sku"       => ($f_include_sku <> "Y"? "N":"Y"),
        "dpurl_use_sku"     => ($f_dpurl_use_sku <> "Y"? "N":"Y"),
        "filters"           => $strFilters,
        "active"		    => ($f_active <> "Y"? "N":"Y"),
    );

    // сохранение данных (обновление или добавление)
    if($ID > 0)
    {
        $res = $datasource->Update($ID, $arFields, $SITE_ID);
    }
    else
    {
        $ID = $datasource->Add($arFields, $SITE_ID);
        $res = ($ID>0);
    }

    if($res)
    {
        // если сохранение прошло удачно - перенаправим на новую страницу
        // (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
        if($apply!="")
            LocalRedirect("/bitrix/admin/mibix.yamexport_datasource_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
        else
            LocalRedirect("/bitrix/admin/mibix.yamexport_datasource_list.php?lang=".LANG);
    }
    else
    {
        // если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
        if($e = $APPLICATION->GetException())
        {
            $message = new CAdminMessage(GetMessage("MIBIX_YAM_DATASOURCE_SAVE_ERROR"), $e);
        }
        $bVarsFromForm = true;
    }
}

// Удаление глобальных переменных с префиксом str_
ClearVars();

// Выберем данные из базы и сохранем в переменные с префиксом str_
if($ID > 0)
{
    $datasource = CMibixModelDataSource::GetByID($ID);
    if(!$datasource->ExtractFields("str_"))
    {
        $ID=0;
    }
}

// если данные переданы из формы, инициализируем их
if($bVarsFromForm)
{
    $DB->InitTableVarsForEdit("b_mibix_yam_datasource", "", "str_");
}

// определяем значения для параметров
if(strlen($str_filters)>0)
{
    // из строки формируем массив параметров
    $arFilters = explode("|", $str_filters);
    if(count($arFilters)>0)
    {
        $str_filter_name = array();
        $str_filter_unit = array();
        $str_filter_value = array();
        foreach($arFilters as $str_filter)
        {
            // формируем отдельный массива для элементов каждого параметра
            $arFilterElements = explode(",", $str_filter);
            if(count($arFilterElements)==3 && isset($arFilterElements[0]) && isset($arFilterElements[1]) && isset($arFilterElements[2]))
            {
                $str_filter_name[] = $arFilterElements[0];
                $str_filter_unit[] = $arFilterElements[1];
                $str_filter_value[] = $arFilterElements[2];
            }
        }
    }
}

// Устанавливаем заголовок в зависимости от ее типа (обновление/добавление)
$APPLICATION->SetTitle(($ID > 0 ? GetMessage("MIBIX_YAM_DATASOURCE_EDIT_TITLE").$ID : GetMessage("MIBIX_YAM_DATASOURCE_ADD_TITLE")));

// второй общий пролог
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Проверка статуса модуля
switch(CModule::IncludeModuleEx($iModuleID))
{
    case MODULE_NOT_FOUND:
        echo '<div style="padding-bottom:10px;color:red;">'.GetMessage("MIBIX_YAM_MODULE_NOT_FOUND").'</div>';
        return;
    case MODULE_DEMO:
        echo '<div style="padding-bottom:10px;color:red;">'.GetMessage("MIBIX_YAM_MODULE_DEMO").'</div>';
        break;
    case MODULE_DEMO_EXPIRED:
        echo '<div style="padding-bottom:10px;color:red;">'.GetMessage("MIBIX_YAM_MODULE_DEMO_EXPIRED").'</div>';
}

// Административное меню, которое будет отображаться над таблицей со списком (Вернуться к списку)
$aMenu = array(
    array(
        "TEXT"=>GetMessage("MIBIX_YAM_DATASOURCE_LIST_TEXT"),
        "TITLE"=>GetMessage("MIBIX_YAM_DATASOURCE_LIST"),
        "LINK"=>"mibix.yamexport_datasource_list.php?lang=".LANG,
        "ICON"=>"btn_list",
    )
);

// В режиме редактирования добавляем дополнительные пункты меню (Добавить/Удалить)
if($ID>0)
{
    $aMenu[] = array("SEPARATOR"=>"Y");
    $aMenu[] = array(
        "TEXT"=>GetMessage("MIBIX_YAM_DATASOURCE_ADD_TEXT"),
        "TITLE"=>GetMessage("MIBIX_YAM_DATASOURCE_MNU_ADD"),
        "LINK"=>"imaginweb.sms_subscr_edit.php?lang=".LANG,
        "ICON"=>"btn_new",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("MIBIX_YAM_DATASOURCE_DEL_TEXT"),
        "TITLE"=>GetMessage("MIBIX_YAM_DATASOURCE_MNU_DEL"),
        "LINK"=>"javascript:if(confirm('".GetMessage("MIBIX_YAM_DATASOURCE_MNU_DEL_CONF")."'))window.location='mibix.yamexport_datasource_list.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
        "ICON"=>"btn_delete",
    );
}

// создадим экземпляр класса административного меню
$context = new CAdminContextMenu($aMenu);

// выведем меню
$context->Show();

// если есть сообщения об ошибках или об успешном сохранении - выведем их
if($_REQUEST["mess"] == "ok" && $ID>0)
    CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("MIBIX_YAM_DATASOURCE_SAVED"), "TYPE"=>"OK"));
if($message)
    echo $message->Show();
?>

    <form method="POST" action="<?=$APPLICATION->GetCurPage();?>"  enctype="multipart/form-data" name="datasourceform">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        ?>
        <tr class="heading">
            <td colspan="2"><?=GetMessage("MIBIX_YAM_DATASOURCE_TITLE")?></td>
        </tr>
        <?if($ID > 0):?>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_DATE_ADD");?>:</td>
                <td width="60%" class="adm-detail-content-cell-r"><?=$str_date_insert;?></td>
            </tr>
            <?if($str_date_update <> ""):?>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_DATE_UPD");?>:</td>
                    <td width="60%" class="adm-detail-content-cell-r"><?=$str_date_update;?></td>
                </tr>
            <?endif?>
        <?endif?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_ACTIVE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_active" value="Y"<?if($str_active=="Y" || empty($str_active)) echo " checked";?>>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_DATASOURCE_NAME")?></span>:<br>(<?=GetMessage("MIBIX_YAM_DATASOURCE_NAME_NOTE")?>)
            </td>
            <td width="60%">
                <input type="text" size="50" maxlength="255" value="<?=$str_name_data;?>" name="f_name_data" />
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_DATASOURCE_SHOP");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getSelectBoxProfileShop($str_shop_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_DATASOURCE_SHOP_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_SITE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getSelectBoxSiteId($str_site_id);?>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_DATASOURCE_IBLOCK_TYPE");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getSelectBoxIBlockType($str_iblock_type);?>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_DATASOURCE_IBLOCK_ID");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getSelectBoxIBlockId($str_site_id,$str_iblock_type,$str_iblock_id);?>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_SECTIONS_INC");?>:<br>(<?=GetMessage("MIBIX_YAM_DATASOURCE_SECTIONS_INC_NOTE")?>)</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getSelectBoxSections("f_include_sections", $str_iblock_id, $str_include_sections);?>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_SECTIONS_EXC");?>:<br>(<?=GetMessage("MIBIX_YAM_DATASOURCE_SECTIONS_EXC_NOTE")?>)</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getSelectBoxSections("f_exclude_sections", $str_iblock_id, $str_exclude_sections);?>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_ELEMENTS_INC");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?
                // Нужно вызывать это из ajax.php когда выбран инфоблок
                //_ShowElementPropertyField($name, $property_fields, $values, $bVarsFromForm = false)
                $property_fields = array("PROPERTY_TYPE"=>"E", "MULTIPLE"=>"Y", "MULTIPLE_CNT"=>1);
                _ShowPropertyField("f_include_items", $property_fields, explode(",",$str_include_items), false, $bVarsFromForm);
                ?>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_ELEMENTS_EXC");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?
                //_ShowElementPropertyField($name, $property_fields, $values, $bVarsFromForm = false)
                $property_fields = array("PROPERTY_TYPE"=>"E", "MULTIPLE"=>"Y", "MULTIPLE_CNT"=>1);
                _ShowPropertyField("f_exclude_items", $property_fields, explode(",",$str_exclude_items), false, $bVarsFromForm);
                ?>
            </td>
            </td>
        </tr>
        <?if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")):?>
        <tr class="heading">
            <td colspan="2"><?=GetMessage("MIBIX_YAM_DATASOURCE_SKU_TITLE")?></td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_INCLUDE_SKU");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_include_sku" value="Y"<?if($str_include_sku=="Y" || empty($str_include_sku)) echo " checked";?>>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_DPURL_USE_SKU");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_dpurl_use_sku" value="Y"<?if($str_dpurl_use_sku=="Y" || empty($str_dpurl_use_sku)) echo " checked";?>>
            </td>
        </tr>
        <?endif;?>
        <tr class="heading">
            <td colspan="2"><?=GetMessage("MIBIX_YAM_DATASOURCE_FILTER_TITLE")?></td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_DATASOURCE_FILTER_SET");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelDataSource::getControlFilter($str_iblock_id, $str_filter_name, $str_filter_unit, $str_filter_value);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_DATASOURCE_FILTER_SET_NOTE");?>
                </div>
            </td>
        </tr>
        <?
        $tabControl->Buttons(
            array(
                "disabled"=>($POST_RIGHT<"W"),
                "back_url"=>"mibix.yamexport_datasource_list.php?lang=".LANG
            )
        );
        ?>
        <?=bitrix_sessid_post();?>
        <input type="hidden" name="lang" value="<?=LANG?>">
        <?if($ID>0):?>
            <input type="hidden" name="ID" value="<?=$ID?>">
        <?endif;?>
        <?
        $tabControl->End();
        ?>
    </form>
    <script src="/bitrix/js/mibix.yamexport/script.js"></script>

<?
$tabControl->ShowWarnings("datasourceform", $message);
?>

<?=BeginNote();?>
    <span class="required">*</span> <?=GetMessage("REQUIRED_FIELDS");?>
<?=EndNote();?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>