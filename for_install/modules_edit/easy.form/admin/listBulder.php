<?
$arHeaders = array();
global $APPLICATION;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
CUtil::InitJSCore(array('jquery'));

foreach ($map as $field) {

    if ($field instanceof Entity\ReferenceField) {
        continue;
    }

    $arHeaders[] = array("id" => $field->getName(), "content" => (loc::GetMessage("SLAM_EASYFORM_FIELD_{$field->getName()}") ?: $field->getTitle()), "sort" => $field->getName(), "default" => (count($arHeaders) < 6));
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$FINDBY = $request['FINDBY'];
if (!empty($FINDBY ) && empty($request['W_ID']) && !Main\Application::isUtfMode()) {
    $FINDBY = Main\Text\Encoding::convertEncoding($FINDBY, 'UTF-8', $context->getCulture()->getCharset());
}

$arFilter = array();

if (!empty($FINDBY) && !isset($_REQUEST['del_filter'])) {
    $arFilter = array_filter($FINDBY, function (&$a) {
        if (is_array($a)) {

            $a = array_filter($a, function ($b) {
                $b = (!is_array($b)) ? trim($b) : $b;
                return !empty($b);
            });
        }

        return !empty($a);
    });
}

if(isset($arFilter['DATE_CREATE'])){

    if(!empty($arFilter['DATE_CREATE']['FROM'])){
        $arFilter[">=DATE_CREATE"] = $arFilter['DATE_CREATE']['FROM'];
    }

    if(!empty($arFilter['DATE_CREATE']['TO'])){
        if ($arDate = ParseDateTime($arFilter['DATE_CREATE']['TO'], CSite::GetDateFormat("FULL")))
        {
            if (StrLen($arFilter['DATE_CREATE']['TO']) < 11)
            {
                $arDate["HH"] = 23;
                $arDate["MI"] = 59;
                $arDate["SS"] = 59;
            }
            global $DB;
            $arFilter["<=DATE_CREATE"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
        }
    }

    unset($arFilter['DATE_CREATE']);
}

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext, false);

if (($arID = $lAdmin->GroupAction())) {


    if ($_REQUEST['action_target'] == 'selected') {
        $rsData = $queryObj
            ->setFilter($arFilter)
            ->setOrder(array($by => $order))
            ->exec();

        while ($arRes = $rsData->Fetch()) {
            $arID[] = $arRes['ID'];
        }
    }

    foreach ($arID as  $propID) {
        if (intval($propID) <= 0) {
            continue;
        }
        switch ($_REQUEST['action']) {

            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                $APPLICATION->ResetException();
                if (!$tblObj->Delete($propID)) {
                    $DB->Rollback();

                    if($ex = $APPLICATION->GetException())
                        $lAdmin->AddGroupError(GetMessage("SLAM_EASYFORM_DELETE_ERROR")." [".$ex->GetString()."]", $propID);
                    else
                        $lAdmin->AddGroupError(GetMessage("SLAM_EASYFORM_DELETE_ERROR"), $propID);
                }
                $DB->Commit();
                break;

        }


    }
}

$rsData = $queryObj->setSelect(array(
    "*",
))
    ->setFilter($arFilter)
    ->setOrder(array($by => $order))
    ->exec();


$lAdmin->AddHeaders($arHeaders);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(Loc::GetMessage("SLAM_EASYFORM_GET_NAV_PRINT")));

$events = array();
while ($arRes = $rsData->Fetch()) {

    //echo '<pre>'; print_r($arRes); echo '</pre>';
    foreach ($map as $field) {

        if ($field instanceof Entity\ReferenceField) {
            continue;
        }
        if (method_exists($field, "getValues") && count($field->getValues())) {
            $valuesList = $field->getValues();
            $arRes[$field->getName()] = isset($valuesList[$arRes[$field->getName()]]) ? $valuesList[$arRes[$field->getName()]] : $arRes[$field->getName()];
        }
    }

    $contentMenu = array(
        array("ICON" => "delete", "TEXT" => Loc::GetMessage("SLAM_EASYFORM_DELETE"), "ACTION" => "if(confirm('" . Loc::GetMessage("SLAM_EASYFORM_DEL") . "')) " . $lAdmin->ActionDoGroup($arRes["ID"], "delete")),

    );

    $row = &$lAdmin->AddRow($arRes["ID"], $arRes);

    foreach ($arRes as $key => $val) {
        if($key == 'ACTIVE') {
            $row->AddViewField($key, ($arRes[$key] == 'Y'  ? 'Да' : 'Нет') );
        } elseif($key == 'FIELDS') {
            $str = '';
            foreach($arRes[$key] as $code => $txt){
                $str .= '<b>'.$code."</b>: ".$txt.'<br/>';
            }
            $row->AddViewField($key,$str);
        } else {
            $row->AddViewField($key, $arRes[$key] );
        }
    }

    $row->AddActions($contentMenu);
    $lAdmin->BeginEpilogContent();


    $lAdmin->EndEpilogContent();
}

$lAdmin->AddFooter(array(
        array("title" => Loc::GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
        array("counter" => true, "title" => Loc::GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"))
);

$contentMenu = array("delete" => Loc::GetMessage("MAIN_ADMIN_LIST_DELETE"));

$lAdmin->AddGroupActionTable($contentMenu);

$lAdmin->CheckListMode();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <form name="form1" method="GET" action="<?= $APPLICATION->GetCurPage() ?>">
        <?
        $arValues = $FINDBY;
        $propsArray = array();
        foreach ($map as $field) {

            if ($field instanceof Entity\ReferenceField) {
                continue;
            }

            $propsArray["FINDBY[{$field->getName()}]"] = (loc::GetMessage("SLAM_EASYFORM_FIELD_{$field->getName()}") ?: $field->getTitle());

        }
        $oFilter = new CAdminFilter($sTableID . "_filter", $propsArray);
        $oFilter->Begin();
        foreach ($map as $field) :

            if ($field instanceof Entity\ReferenceField) {
                continue;
            }

            ?>
            <tr>
                <td><?= (loc::GetMessage("SLAM_EASYFORM_FIELD_{$field->getName()}") ?: $field->getTitle()) ?>:</td>
                <td>
                    <?
                    switch (get_class($field)):
                        case "Bitrix\Main\Entity\DatetimeField":
                            echo \CalendarPeriod("FINDBY[{$field->getName()}][FROM]", $arValues[$field->getName()]['FROM'], "FINDBY[{$field->getName()}][TO]",  $arValues[$field->getName()]['TO'], "form1", "N");
                            break;
                        case "Bitrix\Main\Entity\EnumField":
                            $values = $field->getValues();
                            $values[0] = "--//--";
                            ?>
                            <select name="<?= "FINDBY[{$field->getName()}]"; ?>">
                                <? foreach ($values as $valueID => $valueName): ?>
                                    <option value="<?= $valueID ?>" <?= $arValues[$field->getName()] == $valueID ? "selected" : "" ?> ><?= $valueName ?></option>
                                <? endforeach; ?>
                            </select>
                            <?
                            break;
                        case "Bitrix\Main\Entity\BooleanField":
                            ?>
                            <input type="hidden" name="<?= "FINDBY[{$field->getName()}]"; ?>" value="0"/>
                            <input type="checkbox" name="<?= "FINDBY[{$field->getName()}]"; ?>"
                                   value="1" <?= ($arValues[$field->getName()] ? "checked" : ""); ?>/>
                            <?
                            break;
                        case "Bitrix\Main\Entity\IntegerField":
                            ?>
                            <input type="text" name="<?= "FINDBY[{$field->getName()}]"; ?> "
                                   value="<?= strlen($arValues[$field->getName()]) ? $arValues[$field->getName()] : ""; ?>"/>
                            <?
                            break;
                        case "Bitrix\Main\Entity\FloatField":
                        default :
                            if($field->getName() == 'NAME'){
                                $values = array();
                                $values[''] = "--//--";
                                if(Loader::IncludeModule("slam.easyform")) {
                                    $tblObj = new Slam\Easyform\EasyformTable();
                                    $queryObj = $tblObj->query();
                                    $rsData = $queryObj
                                        ->setSelect(array('NAME', 'ID'))
                                        ->setOrder(array('NAME' => 'ASC'))
                                        ->setGroup(array('NAME'))->exec();
                                    while ($arRes = $rsData->Fetch()) {
                                        $values[$arRes['NAME']] = $arRes['NAME'];
                                    }
                                ?>
                                <select name="<?= "FINDBY[{$field->getName()}]"; ?>">
                                    <? foreach ($values as $valueID => $valueName): ?>
                                        <option value="<?= $valueID ?>" <?= $arValues[$field->getName()] == $valueID ? "selected" : "" ?> ><?= $valueName ?></option>
                                    <? endforeach; ?>
                                </select>
                                    <?
                                }
                            } else {
                                ?>
                                <input type="text" name="<?= "FINDBY[{$field->getName()}]"; ?> "
                                       value="<?= htmlspecialchars(strlen($arValues[$field->getName()]) ? $arValues[$field->getName()] : ""); ?>"/>
                                <?
                            }
                    endswitch; ?>
            </tr>
        <? endforeach; ?>
        <?
        $oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage()));
        $oFilter->End();
        ?>
    </form>

<?
$lAdmin->DisplayList();

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
