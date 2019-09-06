<?php
use WS\SaleUserProfilesPlus\Module;
use WS\SaleUserProfilesPlus\Profile;
use WS\SaleUserProfilesPlus\helpers\AdminHelper;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ws.saleuserprofilesplus/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$POST_RIGHT = $APPLICATION->GetGroupRight("ws.saleuserprofilesplus");
if ($POST_RIGHT == "D"){
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "b_sale_user_props";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);
?>
<?
// *********************** CheckFilter ******************************** //
function CheckFilter() {
    global $FilterArr, $lAdmin;
    foreach ($FilterArr as $f) global $$f;
    return count($lAdmin->arFilterErrors) == 0;
}
// *********************** /CheckFilter ******************************* //
$FilterArr = Array(
    "find_id",
    "find_name",
    "find_user_id",
    "find_person_type_id",
);

$arUserProps = array();
$dbProps = CSaleOrderProps::GetList(
    array("PERSON_TYPE_ID" => "ASC", "SORT" => "ASC"),
    array(),
    false,
    false,
    array("ID", "NAME", "PERSON_TYPE_NAME", "PERSON_TYPE_ID", "SORT", "IS_FILTERED", "TYPE", "CODE")
);
while ($arProps = $dbProps->GetNext()) {
    $arUserProps[IntVal($arProps["ID"])] = $arProps;
}

foreach ($arUserProps as $key => $value) {
    if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT") {
        $arFilterFields[] = "filter_prop_".$key;
    }
}

$lAdmin->InitFilter($FilterArr);

if (CheckFilter()) {
    $arFilter = array();
    if(!empty($find_id)) {
        $arFilter["ID"] = $find_id;
    }
    if(!empty($find_name)) {
        $arFilter["%NAME"] = $find_name;
    }
    if(!empty($find_person_type_id)) {
        $arFilter["PERSON_TYPE_ID"] = $find_person_type_id;
    }
    if(!empty($find_user_id)) {
        $arFilter["USER_ID"] = $find_user_id;
    }

    foreach ($arUserProps as $key => $value) {
        if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT") {
            $tmp = Trim(${"filter_prop_".$key});
            if (StrLen($tmp) > 0) {
                if($value["TYPE"]=="TEXT" || $value["TYPE"]=="TEXTAREA") {
                    if(preg_match("/^\d+$/", $tmp)) {
                        $arFilter["PROPERTY_VALUE_".$key] = $tmp;
                    } else {
                        $arFilter["%PROPERTY_VALUE_".$key] = $tmp;
                    }
                } else {
                    $arFilter["PROPERTY_VALUE_".$key] = $tmp;
                }
            }
        }
    }
}

if($lAdmin->EditAction() && $POST_RIGHT=="W"){
    foreach($FIELDS as $ID=>$arFields) {
        if(!$lAdmin->IsUpdated($ID)){
            continue;
        }

        $res = Profile::Update($ID, $arFields);
        if ($err = $res->getErrorsAsString()) {
            $lAdmin->AddGroupError($err, $ID);
        }
    }
}

if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT=="W") {
    if($_REQUEST['action_target']=='selected')
    {
        $rsData = CSaleOrderUserProps::GetList(array($by=>$order), $arFilter);
        while($arRes = $rsData->Fetch()){
            $arID[] = $arRes['ID'];
        }
    }

    foreach($arID as $ID) {
        if(strlen($ID)<=0){
            continue;
        }
        $ID = IntVal($ID);

        switch($_REQUEST['action'])
        {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if(!CSaleOrderUserProps::Delete($ID))
                {
                    $DB->Rollback();
                    $lAdmin->AddGroupError(Module::get()->getMessage("del_err"), $ID);
                }
                $DB->Commit();
                break;
        }
    }
}

// ******************************************************************** //
$arData = array();
$profileIDs = array();
$arSelect = array(
    "ID",
    "NAME",
    "USER_ID",
    "PERSON_TYPE_ID",
    "DATE_UPDATE"
);
$rsData = Profile::GetList(array($by => $order), $arFilter, false, false, $arSelect);

$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(Module::get()->getMessage("nav")));

// ******************************************************************** //

$arColumnsTmp = array(
    array(  "id"    =>"ID",
        "content"  =>Module::get()->getMessage("row_id"),
        "sort"    =>"ID",
        "align"    =>"right",
        "default"  =>true,
    ),
    array(  "id"    =>"NAME",
        "content"  =>Module::get()->getMessage("row_name"),
        "sort"    =>"NAME",
        "default"  =>true,
    ),
    array(  "id"    =>"USER_ID",
        "content"  =>Module::get()->getMessage("row_user_id"),
        "sort"    =>"USER_ID",
        "default"  =>true,
    ),
    array(  "id"    =>"PERSON_TYPE_ID",
        "content"  =>Module::get()->getMessage("row_person_type_id"),
        "sort"    =>"PERSON_TYPE_ID",
        "default"  =>true,
    ),
    array(  "id"    =>"DATE_UPDATE",
        "content"  =>Module::get()->getMessage("row_person_date_update"),
        "sort"    =>"DATE_UPDATE",
        "default"  =>true,
    ),
);
foreach ($arUserProps as $key => $value) {
    $arColumnsTmp[] = array(
        "id" => "PROP_" . $key,
        "content" => $value["NAME"] . " (" . $value["PERSON_TYPE_NAME"] . ")",
        "sort"  => "PROPERTY_VALUE_" . $key,
        "default"   => false
    );
}
$lAdmin->AddHeaders($arColumnsTmp);
$personTypes = array();
$users = array();
while($arRes = $rsData->NavNext(true, "f_")){

    $arUserPropsValues = array();
    $rsProps = CSaleOrderUserPropsValue::GetList(array(), array("USER_PROPS_ID" => $arRes['ID']));
    while ($arPropsRes = $rsProps->Fetch()) {
        if (empty($arUserPropsValues[$arPropsRes["USER_PROPS_ID"]])) {
            $arUserPropsValues[$arPropsRes["USER_PROPS_ID"]] = array();
        }
        $arUserPropsValues[$arPropsRes["USER_PROPS_ID"]][$arPropsRes["PROP_ID"]] = $arPropsRes;
    }

    if (!empty($f_PERSON_TYPE_ID)) {
        if (empty($personTypes[$f_PERSON_ID])) {
            $rs = CSalePersonType::GetList(Array(), Array('ID'=>$f_PERSON_TYPE_ID), false, array('nTopCount' => 1));
            if ($arRs = $rs->Fetch()) {
                $personTypes[$arRs['ID']] = $arRs;
            }
        }
        $GLOBALS['f_PERSON_TYPE_NAME'] = $personTypes[$f_PERSON_TYPE_ID]['NAME'];
    }

    if (!empty($f_USER_ID)) {
        if (empty($users[$f_USER_ID])) {
            $rs =  CUser::GetList(($sort_by="personal_country"), ($sort_order="desc"), array('ID'=>$f_USER_ID));
            while ($arRs = $rs->Fetch()) {
                $users[$arRs['ID']] = $arRs;
            }
        }
        $GLOBALS['f_USER_NAME'] = $users[$f_USER_ID]['LOGIN'];
    }

    $row =& $lAdmin->AddRow($f_ID, $arRes);

    $row->AddViewField("NAME", '<a href="ws.saleuserprofilesplus_edit.php?ID='.$f_ID.'&lang='.LANG.'">'.$f_NAME.'</a>');
    $row->AddInputField("NAME", array("size"=>20));
    $row->AddViewField("USER_ID", $f_USER_NAME . ' (<a href="user_edit.php?ID='.$f_USER_ID.'&lang='.LANG.'">'.$f_USER_ID.'</a>)');
    $row->AddInputField("USER_ID", array("size"=>20));
    $row->AddViewField("PERSON_TYPE_ID", $f_PERSON_TYPE_NAME . ' (<a href="sale_person_type_edit.php?ID='.$f_PERSON_TYPE_ID.'&lang='.LANG.'">'.$f_PERSON_TYPE_ID.'</a>)');
    $row->AddViewField("DATE_UPDATE", $f_DATE_UPDATE);

    foreach($arUserPropsValues[$f_ID] as $arProps) {
        if ($arProps["PROP_PERSON_TYPE_ID"] == $f_PERSON_TYPE_ID) {
            if($arProps["PROP_TYPE"] == "MULTISELECT" || $arProps["PROP_TYPE"] == "SELECT" || $arProps["PROP_TYPE"] == "RADIO") {
                if($arProps["PROP_TYPE"] == "MULTISELECT") {
                    $valMulti = "";
                    $curVal = explode(",", $arProps["VALUE"]);
                    $bNeedLine = false;
                    foreach ($curVal as $val) {
                        if ($bNeedLine)
                            $valMulti .= "<hr size=\"1\" width=\"90%\">";
                        $arPropVariant = CSaleOrderPropsVariant::GetByValue($arProps["PROP_ID"], $val);
                        $valMulti .= "[".htmlspecialcharsEx($val)."] ".htmlspecialcharsEx($arPropVariant["NAME"])."<br />";
                        $bNeedLine = true;
                    }
                    $row->AddField("PROP_".$arProps["PROP_ID"], $valMulti);
                } else {
                    $row->AddField("PROP_".$arProps["PROP_ID"], "[".htmlspecialcharsEx($arProps["VALUE"])."] ".htmlspecialcharsEx($arProps["VARIANT_NAME"]));
                }
            } elseif($arProps["PROP_TYPE"] == "CHECKBOX") {
                if($arProps["VALUE"] == "Y") {
                    $row->AddField("PROP_".$arProps["PROP_ID"], Module::get()->getMessage("yes"));
                }
            } elseif($arProps["PROP_TYPE"] == "LOCATION") {
                $arVal = CSaleLocation::GetByID($arProps["VALUE"], LANG);
                $row->AddField("PROP_".$arProps["PROP_ID"], htmlspecialcharsEx($arVal["COUNTRY_NAME"] . ((!empty($arVal["REGION_NAME"]))?' - '.$arVal["REGION_NAME"]:'') . ((!empty($arVal["CITY_NAME"]))?' - '.$arVal["CITY_NAME"]:'')));
            } else {
                $row->AddField("PROP_".$arProps["PROP_ID"], $arProps["VALUE"]);
            }
        }
    }

    $arActions = Array();

    $arActions[] = array(
        "ICON"=>"edit",
        "DEFAULT"=>true,
        "TEXT"=>Module::get()->getMessage("edit"),
        "ACTION"=>$lAdmin->ActionRedirect("ws.saleuserprofilesplus_edit.php?ID=".$f_ID)
    );

    if ($POST_RIGHT>="W") {
        $arActions[] = array(
            "ICON"=>"delete",
            "TEXT"=>Module::get()->getMessage("del"),
            "ACTION"=>"if(confirm('".GetMessage('ws.saleuserprofilesplus_del_conf')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
        );
    }

    $arActions[] = array("SEPARATOR"=>true);

    $rsSites = CSite::GetList($sort_by="sort", $sort_order="asc", array());
    while ($arSite = $rsSites->Fetch()) {
        $arActions[] = array(
            "ICON"=>"edit",
            "TEXT"=>Module::get()->getMessage("order_create") . " [" . $arSite["ID"] . "]",
            "ACTION"=> $lAdmin->ActionRedirect("sale_order_new.php?user_id=" . $f_USER_ID . "&LID=" . $arSite["ID"])
        );
    }


    if(is_set($arActions[count($arActions)-1], "SEPARATOR"))
        unset($arActions[count($arActions)-1]);

    $row->AddActions($arActions);

}

$lAdmin->AddFooter(
    array(
        array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
        array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
    )
);

$lAdmin->AddGroupActionTable(Array(
    "delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

// ******************************************************************** //

$aContext = array(
    array(
        "TEXT"=>Module::get()->getMessage("add"),
        "LINK"=>"ws.saleuserprofilesplus_edit.php?lang=".LANG,
        "TITLE"=>Module::get()->getMessage("add"),
        "ICON"=>"btn_new",
    ),
);

$lAdmin->AddAdminContextMenu($aContext);

// ******************************************************************** //

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Module::get()->getMessage("title"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// ******************************************************************** //
$arFilterFieldsTmp = array(
    Module::get()->getMessage("filter_id"),
    Module::get()->getMessage("filter_user_id"),
    Module::get()->getMessage("filter_person_type_id"),
);

foreach ($arUserProps as $key => $value) {
    if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT") {
        $arFilterFieldsTmp[] = $value["NAME"] . " (" . $value["PERSON_TYPE_NAME"] . ")";
    }
}

$oFilter = new CAdminFilter(
    $sTableID."_filter",
    $arFilterFieldsTmp
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
    <?$oFilter->Begin();?>
    <tr>
        <td><b><?=Module::get()->getMessage("filter_name").":"?></b></td>
        <td><input type="text" name="find_name" size="47" value="<?echo htmlspecialchars($find_name)?>"></td>
    </tr>
    <tr>
        <td><?=Module::get()->getMessage("filter_id")?>:</td>
        <td><input type="text" name="find_id" size="47" value="<?echo htmlspecialchars($find_id)?>"></td>
    </tr>
    <tr>
        <td><?=Module::get()->getMessage("filter_user_id").":"?></td>
        <td>
            <input type="text" id="find_user_id" name="find_user_id" size="41" value="<?echo htmlspecialchars($find_user_id)?>">
            <input class="tablebodybutton" type="button" name="FindUser" id="FindUser" onclick="window.open('/bitrix/admin/user_search.php?lang=ru&amp;FN=find_form&amp;FC=find_user_id', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));" value="...">
        </td>
    </tr>
    <tr>
        <td><?=Module::get()->getMessage("filter_person_type_id").":"?></td>
        <td><?= AdminHelper::SelectBoxPersonTypes($find_person_type_id, "find_person_type_id", 'style="width: 100%;"')?></td>
    </tr>
    <?
    foreach ($arUserProps as $key => $value)
    {
        if ($value["IS_FILTERED"] == "Y" && $value["TYPE"] != "MULTISELECT" )
        {
            ?>
            <tr>
                <td valign="top"><?=$value["NAME"]?> (<?=$value["PERSON_TYPE_NAME"]?>):</td>
                <td valign="top">
                    <?
                    $curVal = ${"filter_prop_".$key};
                    if ($value["TYPE"]=="CHECKBOX")
                    {
                        ?><input type="checkbox" name="filter_prop_<?= $key ?>" value="Y"<?if ($curVal == "Y") echo " checked";?>><?
                    }
                    elseif ($value["TYPE"]=="TEXT" || $value["TYPE"]=="TEXTAREA")
                    {
                        ?><input type="text" size="30" maxlength="250" value="<?= htmlspecialcharsbx($curVal) ?>" name="filter_prop_<?= $key ?>"><?=ShowFilterLogicHelp()?><?
                    }
                    elseif ($value["TYPE"]=="SELECT" || $value["TYPE"]=="MULTISELECT")
                    {
                        ?>
                        <select name="filter_prop_<?= $key ?>">
                            <option value=""><?echo GetMessage("SALE_F_ALL")?></option>
                            <?
                            $db_vars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID" => $key));
                            while ($vars = $db_vars->Fetch())
                            {
                                ?><option value="<?echo $vars["VALUE"]?>"<?if ($vars["VALUE"]==$curVal) echo " selected"?>><?echo htmlspecialcharsbx($vars["NAME"])?></option><?
                            }
                            ?>
                        </select>
                    <?
                    }
                    elseif ($value["TYPE"]=="LOCATION")
                    {
                        ?>
                        <select name="filter_prop_<?= $key ?>">
                            <option value=""><?echo GetMessage("SALE_F_ALL")?></option>
                            <?
                            $db_vars = CSaleLocation::GetList(Array("SORT"=>"ASC", "COUNTRY_NAME_LANG"=>"ASC", "CITY_NAME_LANG"=>"ASC"), array(), LANG);
                            while ($vars = $db_vars->Fetch())
                            {
                                ?><option value="<?echo $vars["ID"]?>"<?if (IntVal($vars["ID"])==IntVal($curVal)) echo " selected"?>><?echo htmlspecialcharsbx($vars["COUNTRY_NAME"] . ((!empty($vars["REGION_NAME"]))?' - '.$vars["REGION_NAME"]:'') . ((!empty($vars["CITY_NAME"]))?' - '.$vars["CITY_NAME"]:''))?></option><?
                            }
                            ?>
                        </select>
                    <?
                    }
                    elseif ($value["TYPE"]=="RADIO")
                    {
                        ?><input type="radio" name="filter_prop_<?= $key ?>" value=""><?echo GetMessage("SALE_F_ALL")?><br /><?
                        $db_vars = CSaleOrderPropsVariant::GetList(($by="SORT"), ($order="ASC"), Array("ORDER_PROPS_ID"=>$key));
                        while ($vars = $db_vars->Fetch())
                        {
                            ?><input type="radio" name="filter_prop_<?= $key ?>" value="<?echo $vars["VALUE"]?>"<?if ($vars["VALUE"]==$curVal) echo " checked"?>><?echo htmlspecialcharsbx($vars["NAME"])?><br /><?
                        }
                    }
                    ?>
                </td>
            </tr>
        <?
        }
    }
    ?>
    <?
    $oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
    $oFilter->End();
    ?>
</form>
<?
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");