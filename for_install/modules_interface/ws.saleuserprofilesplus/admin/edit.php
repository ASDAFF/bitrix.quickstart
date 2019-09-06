<?php
use WS\SaleUserProfilesPlus\Module;
use WS\SaleUserProfilesPlus\Profile;
use WS\SaleUserProfilesPlus\helpers\AdminHelper;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ws.saleuserprofilesplus/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

$POST_RIGHT = $APPLICATION->GetGroupRight("ws.saleuserprofilesplus");
if($POST_RIGHT=="D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = array(
	array("DIV" => "edit1", "TAB" => Module::get()->getMessage("tabname"), "ICON"=>"main_user_edit", "TITLE"=>Module::get()->getMessage("tabname")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);		// Id of the edited record
$message = null;
$bVarsFromForm = false;
if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){

    if (!$ID) {
        $res = Profile::Add($_REQUEST["FIELDS"]);
        if (is_numeric($res)) {
            $ID = $res;
        }
    }

    if ($ID) {
        $res = Profile::Update($ID, $_REQUEST["FIELDS"]);
    }

	if(!$err = $res->getErrorsAsString()){
		if($apply!="")
			LocalRedirect("/bitrix/admin/ws.saleuserprofilesplus_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect("/bitrix/admin/ws.saleuserprofilesplus_list.php?lang=".LANG);
	}
	else{
		if($e = $APPLICATION->GetException()){
			$message = new CAdminMessage($err, $e);
        } else {
            $message = new CAdminMessage($err);
        }
		$bVarsFromForm = true;
	}

}

//Edit/Add part
ClearVars();
foreach (Profile::GetProfileFieldsByID($ID) as $name => $value) {
    $GLOBALS["f_".$name] = $value;
}
foreach ($_REQUEST["FIELDS"] as $name => $value) {
    $GLOBALS["f_".$name] = $value;
}
$f_PROPS = Profile::GetProfileProps($f_ID, $f_PERSON_TYPE_ID);
foreach ($_REQUEST["FIELDS"]["PROPS"] as $propID=>$value) {
    $f_PROPS[$propID]["VALUE"] = $value;
}

//print_r($f_PROPS);


$APPLICATION->SetTitle(Module::get()->getMessage("tabname"). ': ' .$profileFields['NAME']);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($_REQUEST["mess"] == "ok" && $ID>0)
	CAdminMessage::ShowMessage(array("MESSAGE"=>Module::get()->getMessage("saved"), "TYPE"=>"OK"));

if($message)
	echo $message->Show();
elseif($rubric->LAST_ERROR!="")
	CAdminMessage::ShowMessage($rubric->LAST_ERROR);
?>

<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?
$tabControl->Begin();
?>
<?
$tabControl->BeginNextTab();
?>
    <tr class="adm-detail-required-field">
        <td style="width:40%"><?=Module::get()->getMessage("field_id")?></td>
        <td><?=$f_ID?></td>
    </tr>
    <tr>
        <td><?=Module::get()->getMessage("field_date_update")?></td>
        <td><?=$f_DATE_UPDATE?></td>
    </tr>
    <tr class="adm-detail-required-field">
        <td><?=Module::get()->getMessage("field_person_type")?></td>
        <td>
            <?= AdminHelper::SelectBoxPersonTypes($f_PERSON_TYPE_ID, "FIELDS[PERSON_TYPE_ID]", 'onchange="this.form.submit()"');?>
        </td>
    </tr>
    <tr>
        <td><?= Module::get()->getMessage("field_name")?></td>
        <td>
            <input type="text" name="FIELDS[NAME]" value="<?=$f_NAME?>" />
        </td>
    </tr>
    <tr class="adm-detail-required-field">
        <td><?=Module::get()->getMessage("field_user_id")?></td>
        <td>
            <input type="text" id="post_form_user_id" name="FIELDS[USER_ID]" value="<?=$f_USER_ID?>"/>
            <input class="tablebodybutton" type="button" name="FindUser" id="FindUser" onclick="window.open('/bitrix/admin/user_search.php?lang=ru&amp;FN=post_form&amp;FC=post_form_user_id', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));" value="...">
        </td>
    </tr>
    <?foreach($f_PROPS as &$arProp):?>
	<tr <?if($arProp["REQUIED"] === "Y"):?>class="adm-detail-required-field"<?endif?>>
		<td><?=$arProp["NAME"]?></td>
        <td>
            <?
                switch ($arProp["TYPE"]) {
                    case "CHECKBOX":
                        ?><input type="checkbox" name="FIELDS[PROPS][<?=$arProp["ID"]?>]" value="Y" <?if($arProp["VALUE"] === "Y"):?>checked="checked"<?endif?> /><?
                        break;
                    case "TEXT":
                        ?><input type="text" name="FIELDS[PROPS][<?=$arProp["ID"]?>]" value="<?=$arProp["VALUE"]?>" /><?
                        break;
                    case "SELECT":
                        ?>
                        <select name="FIELDS[PROPS][<?=$arProp["ID"]?>]">
                            <?foreach($arProp["variants"] as $variant):?>
                            <option value="<?=$variant['VALUE']?>" <?=(($variant['VALUE'] === $arProp["VALUE"]) ? " selected ": "")?>><?=$variant["NAME"]?></option>
                            <?endforeach?>
                        </select>
                        <?
                        break;
                    case "MULTISELECT":
                        $curVal = explode(",", $arProp["VALUE"]);
                        ?>
                        <select name="FIELDS[PROPS][<?=$arProp["ID"]?>][]" multiple="multiple">
                            <?foreach($arProp["variants"] as $variant):?>
                            <option value="<?=$variant['VALUE']?>" <?=((in_array($variant['VALUE'], $curVal)) ? " selected ": "")?>><?=$variant["NAME"]?></option>
                            <?endforeach?>
                        </select>
                        <?
                        break;
                    case "TEXTAREA":
                        ?>
                        <textarea style="width:487px;height:200px;" name="FIELDS[PROPS][<?=$arProp["ID"]?>]"><?=$arProp["VALUE"]?></textarea>
                        <?
                        break;
                    case "LOCATION":
                        echo AdminHelper::SelectBoxLocations(LANGUAGE_ID, "FIELDS[PROPS][".$arProp["ID"]."]", $arProp['VALUE']);
                        break;
                    case "RADIO":
                        foreach($arProp["variants"] as $variant){
                            ?><input type="radio" name="FIELDS[PROPS][<?=$arProp["ID"]?>]" value="<?=$variant['VALUE']?>" <?=(($variant['VALUE'] === $arProp["VALUE"])?" checked":"")?>><?=htmlspecialchars($variant["NAME"])?><?
                        }
                        break;
                    default:
                        break;
                }
            ?>
        </td>
		<?/*<td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>></td>*/?>
	</tr>
    <?endforeach?>
<?
$tabControl->Buttons(
	array(
		"disabled"=>($POST_RIGHT<"W"),
		"back_url"=>"ws.saleuserprofilesplus_list.php?lang=".LANG,

	)
);
?>
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID>0 && !$bCopy):?>
	<input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
$tabControl->End();
?>

<?
$tabControl->ShowWarnings("post_form", $message);
?>

<?echo BeginNote();?>
<span class="required">*</span><?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>