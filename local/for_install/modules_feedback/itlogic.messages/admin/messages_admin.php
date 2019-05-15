<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("messages");
global $DB;

$POST_RIGHT = $APPLICATION->GetGroupRight("messages");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$arSite       = CSite::GetByID("s1")->Fetch();
$sTableID     = "tbl_messages"; // ID таблицы
$oSort        = new CAdminSorting($sTableID, "ID", "asc"); // объект сортировки
$lAdmin       = new CAdminList($sTableID, $oSort); // основной объект списка
$messages     = array();
$events       = array();
$reference    = array();
$reference_id = array();
$where        = '';
$where_count  = 0;

// ******************************************************************** //
//                ПОДГОТОВКА ИМЕН ШАБЛОНОВ                              //
// ******************************************************************** //
$res = $DB->Query( "SELECT `b_event_message`.`ID`, `b_event_message`.`EVENT_NAME`, `b_event_message`.`MESSAGE`, `b_event_type`.`NAME`
	FROM `b_event_type`,`b_event_message`
	WHERE `b_event_type`.`EVENT_NAME`=`b_event_message`.`EVENT_NAME` AND `b_event_type`.`LID` = '".LANGUAGE_ID."' ORDER BY `b_event_type`.`NAME`" );

while( $row = $res->getNext() ){
    $events[ $row['ID'] ] = array( 'name'=>$row['NAME'], 'eventname'=>$row['EVENT_NAME'] );
    $messages[ $row['EVENT_NAME'] ] = $row['MESSAGE'];

    $reference[] = $row['NAME'];
    $reference_id[] = $row['EVENT_NAME'];
}

// ******************************************************************** //
//                dialog params prepare                                 //
// ******************************************************************** //

$arDialogParams = array(
    'title' => GetMessage("TITLE_MODAL"),
    'width' => 1000,
    'height' => 700,
);

// ******************************************************************** //
//                           ФИЛЬТР                                     //
// ******************************************************************** //

$arr_if_send = array(
    "reference" => array(
        GetMessage("POST_YES"),
        GetMessage("POST_NO"),
    ),
    "reference_id" => array(
        "Y",
        "N",
    )
);

$arr_find_code = array("reference"=>$reference,"reference_id"=>$reference_id);

// опишем элементы фильтра
$FilterArr = Array(
    "find_id",
    "find_code",
    "SEND_date_from",
    "SEND_date_to",
    "SET_date_to",
    "SET_date_from",
    "find_if_send",
);

// инициализируем фильтр
$lAdmin->InitFilter($FilterArr);

// создадим массив фильтрации на основе значений фильтра
if(count($lAdmin->arFilterErrors)==0){
    $arFilter = Array(
        "ID"             => $find_id,
        "EVENT_NAME"     => $find_code,
        "SUCCESS_EXEC"   => $find_if_send,
        "DATE_INSERT"    => NULL,
        "DATE_EXEC"      => NULL,
    );
}

if(strtolower($order) != "asc" && strtolower($order) != "desc")
    $order = "asc";

if(strtolower($by) != "id" && strtolower($by) != "event_name" && strtolower($by) != "date_insert" && strtolower($by) != "success_exec" && strtolower($by) != "date_exec")
    $by = "ID";


if( (!is_null($SEND_date_to) && '' != $SEND_date_to) && (is_null($SEND_date_from) || '' == $SEND_date_from) ){
    $arFilter['DATE_EXEC'] = "DATE_EXEC < '". $DB->FormatDate($SEND_date_to, "DD.MM.YYYY", "YYYY-MM-DD") ."'";
}
if( (!is_null($SEND_date_from) && '' != $SEND_date_from) && (is_null($SEND_date_to) || '' == $SEND_date_to) ){
    $arFilter['DATE_EXEC'] = "DATE_EXEC > '". $DB->FormatDate($SEND_date_from, "DD.MM.YYYY", "YYYY-MM-DD") ."'";
}
if( (!is_null($SEND_date_to) && '' != $SEND_date_to) && (!is_null($SEND_date_from) && '' != $SEND_date_from) ){
    $arFilter['DATE_EXEC'] = "(DATE_EXEC BETWEEN '". $DB->FormatDate($SEND_date_from, "DD.MM.YYYY", "YYYY-MM-DD") ."' AND '". $DB->FormatDate($SEND_date_to, "DD.MM.YYYY", "YYYY-MM-DD") ."')";
}

if( (!is_null($SET_date_to) && '' != $SET_date_to) && (is_null($SET_date_from) || '' == $SET_date_from) ){
    $arFilter['DATE_INSERT'] = "DATE_INSERT < '". $DB->FormatDate($SET_date_to, "DD.MM.YYYY", "YYYY-MM-DD") ."'";
}
if( (!is_null($SET_date_from) && '' != $SET_date_from) && (is_null($SET_date_to) || '' == $SET_date_to) ){
    $arFilter['DATE_INSERT'] = "DATE_INSERT > '". $DB->FormatDate($SET_date_from, "DD.MM.YYYY", "YYYY-MM-DD") ."'";
}
if( (!is_null($SET_date_to) && '' != $SET_date_to) && (!is_null($SET_date_from) && '' != $SET_date_from) ){
    $arFilter['DATE_INSERT'] = "(DATE_INSERT BETWEEN '". $DB->FormatDate($SET_date_from, "DD.MM.YYYY", "YYYY-MM-DD") ."' AND '". $DB->FormatDate($SET_date_to, "DD.MM.YYYY", "YYYY-MM-DD") ."')";
}

foreach($arFilter as $key=>$val){
    if( !is_null($val) && '' != $val ){
        if( 0 == $where_count ){
            if( $key != "DATE_EXEC" && $key != "DATE_INSERT" ){
                $where .= "WHERE ".$key." = '".$DB->ForSql($val)."'";
            }else{
                $where .= "WHERE ".$val;
            }
        }
        else{
            if( $key != "DATE_EXEC" && $key != "DATE_INSERT" ){
                $where .= " AND ".$key." = '".$DB->ForSql($val)."'";
            }else{
                $where .= " AND ".$val;
            }

        }

        $where_count++;
    }
}

$res = $DB->Query( "SELECT * FROM `b_event` ".$where." ORDER BY ".$DB->ForSql(strtolower($by))." ".$DB->ForSql(strtolower($order)) );

// преобразуем список в экземпляр класса CAdminResult
$rsData = new CAdminResult($res, $sTableID);

// аналогично CDBResult инициализируем постраничную навигацию.
$rsData->NavStart();

// отправим вывод переключателя страниц в основной объект $lAdmin
$lAdmin->NavText($rsData->GetNavPrint(""));


// ******************************************************************** //
//                ПОДГОТОВКА СПИСКА К ВЫВОДУ                            //
// ******************************************************************** //

$lAdmin->AddHeaders(array(
    array(  "id"    =>"ID",
        "content"  =>"ID",
        "sort"    =>"ID",
        "align"    =>"right",
        "default"  =>true,
    ),
    array(  "id"    =>"EVENT_NAME_ROW",
        "content"  =>GetMessage("TEMPLATE_CODE"),
        "sort"    =>"EVENT_NAME",
        "default"  =>true,
    ),
    array(  "id"    =>"DATE_INSERT_ROW",
        "content"  =>GetMessage("SET_DATE"),
        "sort"    =>"DATE_INSERT",
        "default"  =>true,
    ),
    array(  "id"    =>"SUCCESS_EXEC_ROW",
        "content"  =>GetMessage("SEND"),
        "sort"    =>"SUCCESS_EXEC",
        "align"    =>"right",
        "default"  =>true,
    ),
    array(  "id"    =>"DATE_EXEC_ROW",
        "content"  =>GetMessage("SEND_DATE"),
        "sort"    =>"DATE_EXEC",
        "default"  =>true,
    ),
));

while($arRes = $rsData->NavNext(true, "f_")){
    if( isset($messages[ $f_EVENT_NAME ]) ){
        $mess = $messages[ $f_EVENT_NAME ];
        parse_str( $f_C_FIELDS, $fields );
        foreach( $fields as $key => $val ){
            $mess = str_replace( '#'.$key.'#', $val, $mess );
        }
    }else{
        $mess = str_replace( '&', '; ', $f_C_FIELDS );
    }
    $mess = str_replace( '#SITE_NAME#', '"'.$arSite["SITE_NAME"].'"', $mess );
    $mess = str_replace( '#SERVER_NAME#', $arSite["SERVER_NAME"], $mess );

    $event_name = $f_EVENT_NAME;
    foreach($events as $key=>$val){
        if($val['eventname']==$f_EVENT_NAME){
            $event_name = $val['name'];
        }else{
            continue;
        }
    }

    // создаем строку. результат - экземпляр класса CAdminListRow
    $row =& $lAdmin->AddRow($f_ID, $arRes);
    $row->AddViewField("EVENT_NAME_ROW", $event_name);
    $row->AddViewField("DATE_INSERT_ROW", $f_DATE_INSERT);
    $row->AddViewField("SUCCESS_EXEC_ROW", ($f_SUCCESS_EXEC=="Y")?GetMessage("YES"):GetMessage("NO"));
    $row->AddViewField("DATE_EXEC_ROW", $f_DATE_EXEC);

    $arDialogParams["content"] = htmlspecialcharsback($mess);
    // преобразование в объект и замена кавычек
    $strParams = CUtil::PhpToJsObject($arDialogParams);
    $strParams = str_replace('\'[code]', '', $strParams);
    $strParams = str_replace('[code]\'', '', $strParams);

    // сформируем контекстное меню
    $arActions = Array();

    // редактирование элемента
    $arActions[] = array(
        "ICON"=>"edit",
        "DEFAULT"=>true,
        "TEXT"=>GetMessage("MES_DETAIL"),
        "ACTION"=>"(new BX.CDialog(".$strParams.")).Show()"
    );

    // если последний элемент - разделитель, почистим мусор.
    if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
        unset($arActions[count($arActions)-1]);

    // применим контекстное меню к строке
    $row->AddActions($arActions);
}

// ******************************************************************** //
//                ВЫВОД                                                 //
// ******************************************************************** //

// альтернативный вывод
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// ******************************************************************** //
//                ВЫВОД ФИЛЬТРА                                         //
// ******************************************************************** //

// создадим объект фильтра
$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        "ID",
        GetMessage("TEMPLATE_CODE"),
        GetMessage("SET_DATE"),
        GetMessage("SEND_DATE"),
        GetMessage("SEND"),
    )
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
    <?$oFilter->Begin();?>
    <tr>
        <td><?="ID"?>:</td>
        <td>
            <input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>">
        </td>
    </tr>
    <tr>
        <td><?=GetMessage("TEMPLATE_CODE")?>:</td>
        <td><?echo SelectBoxFromArray("find_code", $arr_find_code, $find_code, GetMessage("POST_ALL"), "");?></td>
    </tr>
    <tr>
        <td><?=GetMessage("SET_DATE")?>:</td>
        <td><?echo CalendarPeriod("SET_date_from", "", "SET_date_to", "", "find_form", "Y");?></td>
    </tr>
    <tr>
        <td><?=GetMessage("SEND_DATE")?>:</td>
        <td><?echo CalendarPeriod("SEND_date_from", "", "SEND_date_to", "", "find_form", "Y");?></td>
    </tr>
    <tr>
        <td><?=GetMessage("SEND")?></td>
        <td><?echo SelectBoxFromArray("find_if_send", $arr_if_send, $find_if_send, GetMessage("POST_ALL"), "");?></td>
    </tr>
    <?
    $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
    $oFilter->End();
    ?>
</form>

<?
// выведем таблицу списка элементов
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

?>