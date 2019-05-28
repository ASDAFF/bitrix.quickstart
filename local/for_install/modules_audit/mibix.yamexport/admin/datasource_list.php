<?
$iModuleID = "mibix.yamexport";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/include.php");

// подключим языковой файл
IncludeModuleLangFile(__FILE__);

// получим права доступа текущего пользователя на модуль
$POST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if($POST_RIGHT <= "D")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "tbl_datasource";
$oSort = new CAdminSorting($sTableID, "id", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

// проверка значений фильтра
function CheckFilter()
{
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;

    if(strlen(trim($find_update_1)) > 0 || strlen(trim($find_update_2)) > 0)
    {
        $date_1_ok = false;
        $date1_stm = MkDateTime(FmtDate($find_update_1,"D.M.Y"),"d.m.Y");
        $date2_stm = MkDateTime(FmtDate($find_update_2,"D.M.Y")." 23:59","d.m.Y H:i");
        if(!$date1_stm && strlen(trim($find_update_1)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_UPDATE_FROM"));
        }
        else
        {
            $date_1_ok = true;
        }
        if(!$date2_stm && strlen(trim($find_update_2)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_UPDATE_TILL"));
        }
        elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_FROM_TILL_UPDATE"));
        }

    }
    if(strlen(trim($find_insert_1)) > 0 || strlen(trim($find_insert_2)) > 0)
    {
        $date_1_ok = false;
        $date1_stm = MkDateTime(FmtDate($find_insert_1,"D.M.Y"),"d.m.Y");
        $date2_stm = MkDateTime(FmtDate($find_insert_2,"D.M.Y")." 23:59","d.m.Y H:i");
        if(!$date1_stm && strlen(trim($find_insert_1)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_INSERT_FROM"));
        }
        else
        {
            $date_1_ok = true;
        }
        if(!$date2_stm && strlen(trim($find_insert_2)) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_WRONG_INSERT_TILL"));
        }
        elseif($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm) > 0)
        {
            $lAdmin->AddFilterError(GetMessage("MIBIX_YAM_POST_FROM_TILL_INSERT"));
        }
    }
    return count($lAdmin->arFilterErrors) == 0;
}

// опишем элементы фильтра
$FilterArr = Array(
    "find",
    "find_type",
    "find_id",
    "find_name",
    "find_shop_id",
    "find_iblock_id",
    "find_data_name",
    "find_active",
    "find_update_1",
    "find_update_2",
    "find_insert_1",
    "find_insert_2",
);

// инициализируем фильтр
$lAdmin->InitFilter($FilterArr);

// если все значения фильтра корректны, обработаем его
if(CheckFilter())
{
    $arFilter = Array(
        "ID"		=> ($find!="" && $find_type == "id"? $find : $find_id),
        "NAME"      => $find_name,
        "SHOP_ID"	=> $find_shop_id,
        "IBLOCK_ID"	=> $find_iblock_id,
        "DATA_NAME"	=> ($find!="" && $find_type == "data_name"? $find : $find_data_name),
        "UPDATE_1"	=> $find_update_1,
        "UPDATE_2"	=> $find_update_2,
        "INSERT_1"	=> $find_insert_1,
        "INSERT_2"	=> $find_insert_2,
        "ACTIVE"	=> $find_active,
    );
}

// Обработка действий над элементами
if($lAdmin->EditAction() && $POST_RIGHT == "W")
{
    // пройдем по списку переданных элементов
    foreach($FIELDS as $ID=>$arFields)
    {
        if(!$lAdmin->IsUpdated($ID)) continue;

        // сохраним изменения каждого элемента
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $ob = new CMibixModelDataSource();
        if(!$ob->Update($ID, $arFields))
        {
            $lAdmin->AddUpdateError(GetMessage("MIBIX_YAM_POST_SAVE_ERROR").$ID.": ".$ob->LAST_ERROR, $ID);
            $DB->Rollback();
        }
        $DB->Commit();
    }
}

$strError = $strOk = "";

// обработка одиночных и групповых действий
if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W")
{
    // если выбрано "Для всех элементов"
    if($_REQUEST['action_target']=='selected')
    {
        $cData = new CMibixModelDataSource();
        $rsData = $cData->GetList(array($by=>$order), $arFilter);
        while($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    // пройдем по списку элементов
    foreach($arID as $ID)
    {
        if(strlen($ID)<=0) continue;
        $ID = IntVal($ID);

        // для каждого элемента совершим требуемое действие
        switch($_REQUEST['action'])
        {
            // удаление
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if(!CMibixModelDataSource::Delete($ID))
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(GetMessage("MIBIX_YAM_DATASOURCE_DEL_ERR"), $ID);
                }
                $DB->Commit();
                break;
            // активация/деактивация
            case "activate":
            case "deactivate":
                $ob = new CMibixModelDataSource();
                $arFields = Array("active"=>($_REQUEST['action']=="activate"?"Y":"N"));
                if(!$ob->Update($ID, $arFields))
                    $lAdmin->AddGroupError(GetMessage("MIBIX_YAM_DATASOURCE_SAVE_ERROR").$ob->LAST_ERROR, $ID);
                break;
        }
    }
}

// Выборка элементов
$cData = new CMibixModelDataSource();
$rsData = $cData->GetList(array($by=>$order), $arFilter, array("nPageSize" => CAdminResult::GetNavSize($sTableID)));

// преобразуем список в экземпляр класса CAdminResult
$rsData = new CAdminResult($rsData, $sTableID);

// аналогично CDBResult инициализируем постраничную навигацию.
$rsData->NavStart();

// отправим вывод переключателя страниц в основной объект $lAdmin
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("MIBIX_YAM_NAV")));

// Подготовка списка элементов к выводу (загловки)
$lAdmin->AddHeaders(array(
    array(
        "id"		=> "id", // Идентификатор колонки
        "content"	=> "ID", // Заголовок колонки
        "sort"		=> "id", // Значение параметра GET-запроса для сортировки
        "align"		=> "right", // Будет ли колонка по умолчанию отображаться в списке
        "default"	=> true,
    ),
    array(
        "id"		=> "date_insert",
        "content"	=> GetMessage("MIBIX_YAM_POST_DATE_INSERT"),
        "sort"		=> "date_insert",
        "default"	=> true,
    ),
    array(
        "id"		=> "name_data",
        "content"	=> GetMessage("MIBIX_YAM_NAME_DATA"),
        "sort"		=> "name_data",
        "default"	=> true,
    ),
    array(
        "id"		=> "iblock_id",
        "content"	=> GetMessage("MIBIX_YAM_IBLOCK_ID"),
        "sort"		=> "iblock_id",
        "default"	=> true,
    ),
    array(
        "id"		=> "name",
        "content"	=> GetMessage("MIBIX_YAM_SHOP_ID"),
        "sort"		=> "name",
        "default"	=> true,
    ),
    array(
        "id"		=> "active",
        "content"	=> GetMessage("MIBIX_YAM_ACT"),
        "sort"		=> "act",
        "default"	=> true,
    ),
    array(
        "id"		=> "date_update",
        "content"	=> GetMessage("MIBIX_YAM_POST_DATE_UPDATE"),
        "sort"		=> "date_update",
        "default"	=> false,
    ),
));

// Передача списка элементов в основной объект
while($arRes = $rsData->NavNext(true, "f_"))
{
    // создаем строку. результат - экземпляр класса CAdminListRow
    $row =& $lAdmin->AddRow($f_id, $arRes);

    // отображение для инфоблока (id - название)
    $strIblock = $f_iblock_id;
    if(CModule::IncludeModule("iblock"))
    {
        $resIblock = CIBlock::GetByID($f_iblock_id);
        if($arResIblock = $resIblock->GetNext())
            $strIblock = "[ID:".$f_iblock_id."] " . $arResIblock['NAME'];
    }

    // редактируется как текст
    $row->AddInputField("name_data", array("size"=>20));
    $row->AddViewField("name_data", '<a href="mibix.yamexport_datasource_edit.php?ID='.$f_id.'&lang='.LANG.'">'.$f_name_data.'</a>');
    $row->AddViewField("name", '[ID:'.$f_shop_id.'] '.$f_name);
    $row->AddViewField("iblock_id", $strIblock);
    // редактируется как чекбокс
    $row->AddCheckField("active");

    $arActions = Array();
    $arActions[] = array(
        "ICON"=>"edit",
        "DEFAULT"=>true,
        "TEXT"=>GetMessage("MIBIX_YAM_DATASOURCE_UPD"),
        "ACTION"=>$lAdmin->ActionRedirect($iModuleID."_datasource_edit.php?ID=".$f_id)
    );
    if ($POST_RIGHT>="W")
    {
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>GetMessage("MIBIX_YAM_DATASOURCE_DEL"),
            "ACTION"=>"if(confirm('".GetMessage("MIBIX_YAM_DATASOURCE_DEL_CONF")."')) ".$lAdmin->ActionDoGroup($f_id, "delete")
        );
    }

    // Формируем контекстное меню для строки
    $row->AddActions($arActions);
}

// резюме таблицы
$lAdmin->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
        array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
    )
);

// групповые действия
$lAdmin->AddGroupActionTable(Array(
    "activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
    "deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
    //"dublicate" => GetMessage("MAIN_ADMIN_LIST_DUBLICATE"),
    "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

// сформируем меню из одного пункта - добавить запись
$aContext = array(
    array(
        "TEXT" => GetMessage("MAIN_ADD"),
        "LINK" => $iModuleID."_datasource_edit.php?lang=".LANG,
        "TITLE" => GetMessage("MIBIX_YAM_ADD_TITLE"),
        "ICON" => "btn_new",
    ),
);
// и прикрепим его к списку
$lAdmin->AddAdminContextMenu($aContext);

// отобразим альтернтативные методы вывода списка
$lAdmin->CheckListMode();

// установим заголовок страницы
$APPLICATION->SetTitle(GetMessage("MIBIX_YAM_DATASOURCE_TITLE"));

// разделяем подготовку данных и вывод подключением административного файла
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// инициализация фильтра и передача в него списка полей
$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        GetMessage("MIBIX_YAM_POST_F_ID"),
        GetMessage("MIBIX_YAM_POST_F_INSERT"),
        GetMessage("MIBIX_YAM_POST_F_UPDATE"),
        GetMessage("MIBIX_YAM_POST_F_NAME_DATA"),
        GetMessage("MIBIX_YAM_POST_F_SHOP_ID"),
        GetMessage("MIBIX_YAM_POST_F_SHOP"),
        GetMessage("MIBIX_YAM_POST_F_IBLOCK"),
        GetMessage("MIBIX_YAM_POST_F_ACTIVE"),
    )
);

// далее ручное формирвоание формы фильтра
?>
    <form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
        <?$oFilter->Begin();?>
        <tr>
            <td><b><?=GetMessage("MIBIX_YAM_POST_F_FIND")?>:</b></td>
            <td>
                <input type="text" size="25" name="find" value="<?=htmlspecialchars($find)?>" title="<?=GetMessage("MIBIX_YAM_POST_F_FIND_TITLE")?>">
                <?
                $arr = array(
                    "reference" => array(
                        GetMessage("MIBIX_YAM_POST_F_NAME_DATA"),
                        GetMessage("MIBIX_YAM_POST_F_ID"),
                        GetMessage("MIBIX_YAM_POST_F_SHOP_ID"),
                        GetMessage("MIBIX_YAM_POST_F_SHOP"),
                        GetMessage("MIBIX_YAM_POST_F_IBLOCK"),
                    ),
                    "reference_id" => array(
                        "data_name",
                        "id",
                        "shop_id",
                        "name",
                        "iblock_id",
                    )
                );
                echo SelectBoxFromArray("find_type", $arr, $find_type, "", "");
                ?>
            </td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_ID")?>:</td>
            <td><input type="text" name="find_id" size="47" value="<?=htmlspecialchars($find_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_INSERT")." (".FORMAT_DATE."):"?></td>
            <td><?=CalendarPeriod("find_insert_1", htmlspecialchars($find_insert_1), "find_insert_2", htmlspecialchars($find_insert_2), "find_form","Y")?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_UPDATE")." (".FORMAT_DATE."):"?></td>
            <td><?=CalendarPeriod("find_update_1", htmlspecialchars($find_update_1), "find_update_2", htmlspecialchars($find_update_2), "find_form","Y")?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_NAME_DATA")?>:</td>
            <td><input type="text" name="find_data_name" size="47" value="<?=htmlspecialchars($find_data_name)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_SHOP_ID")?>:</td>
            <td><input type="text" name="find_shop_id" size="47" value="<?=htmlspecialchars($find_shop_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_SHOP")?>:</td>
            <td><input type="text" name="find_name" size="47" value="<?=htmlspecialchars($find_name)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_IBLOCK")?>:</td>
            <td><input type="text" name="find_iblock_id" size="47" value="<?=htmlspecialchars($find_iblock_id)?>">&nbsp;<?=ShowFilterLogicHelp()?></td>
        </tr>
        <tr>
            <td><?=GetMessage("MIBIX_YAM_POST_F_ACTIVE")?>:</td>
            <td><?
                $arr = array("reference"=>array(GetMessage("MAIN_YES"), GetMessage("MAIN_NO")), "reference_id"=>array("Y","N"));
                echo SelectBoxFromArray("find_active", $arr, htmlspecialchars($find_active), GetMessage("MAIN_ALL"));
                ?></td>
        </tr>
        <?
        $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
        $oFilter->End();
        ?>
    </form>

<?
// выведем таблицу списка элементов
$lAdmin->DisplayList();
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>