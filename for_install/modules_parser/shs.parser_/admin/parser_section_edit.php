<?
use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\ExpressionField;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');

$module_id = "shs.parser";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/admin_lib.php");

CModule::IncludeModule('shs.parser');
$parentID = 0;
if(isset($_REQUEST["parent"]) && $_REQUEST["parent"])
{
    $parentID = $_REQUEST["parent"];
}
$ID = intval($ID);
$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT <= "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
$bCopy = ($action == "copy");

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid())
{
    $arFields = Array(
        "NAME"    => $NAME,
        "SORT"    => $SORT,
        "ACTIVE"    => ($ACTIVE <> "Y"? "N":"Y"),
        "DESCRIPTION"    => $DESCRIPTION,
        "PARENT_CATEGORY_ID"    => $PARENT_CATEGORY_ID,
    );
    //printr(array("ID"=>$ID));
    //die("SSS");
    if($ID>0)
    {
        $result = ShsParserSectionTable::Update($ID, $arFields);
        if (!$result->isSuccess())
        {
            $errors = $result->getErrorMessages();
            $res = false;
        }else
            $res = true;
    }
    else
    {
        $arFields["DATE_CREATE"] = new Bitrix\Main\Type\DateTime(date('Y-m-d H:i:s',time()),'Y-m-d H:i:s');
        $result = ShsParserSectionTable::add($arFields);

        if ($result->isSuccess())
        {
            $ID = $result->getId();
            $res = ($ID>0);
        }else{
            $errors = $result->getErrorMessages();
            $res = false;
        }

    }


    if($res)
    {
        if($apply!="")
            LocalRedirect("/bitrix/admin/parser_section_edit.php?ID=".$ID."&parent=".$parentID."&lang=".LANG);
        else
            LocalRedirect("/bitrix/admin/list_parser_admin.php?parent=".$parentID."&lang=".LANG);
    }
    else
    {
        if(isset($errors) && !empty($errors))
        {
            foreach($errors as $error)
            {
                CAdminMessage::ShowMessage($error);
            }

        }
        $bVarsFromForm = true;
    }
}

if(isset($_REQUEST["ID"]) || $copy)
{
    $ID = (int)$_REQUEST["ID"];
    if($copy)
        $arDataTable = ShsParserSectionTable::GetByID($copy)->Fetch();
    else
        $arDataTable = ShsParserSectionTable::GetByID($ID)->Fetch();

}



$aTabs = array(
        array(
            "DIV" => "edit1",
            "TAB" => GetMessage("shs_parser_category_name"),
            "ICON" => "shs_parser_category_icon",
            "TITLE" => GetMessage("shs_parser_category_name")
        ),
);


$aMenu = array(
    array(
        "TEXT"=>GetMessage("parser_list"),
        "TITLE"=>GetMessage("parser_list_title"),
        "LINK"=>"list_parser_admin.php?parent=".$parentID."&lang=".LANG,
        "ICON"=>"btn_list",
    )
);
if($ID>0)
{
    $aMenu[] = array("SEPARATOR"=>"Y");
    $aMenu[] = array(
        "TEXT"=>GetMessage("parser_section_add"),
        "TITLE"=>GetMessage("parser_section_add_title"),
        "LINK"=>"parser_section_edit.php?parent=".$parentID."&lang=".LANG,
        "ICON"=>"btn_new",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("parser_section_copy"),
        "TITLE"=>GetMessage("parser_section_copy_title"),
        "LINK"=>"parser_section_edit.php?copy=".$ID."&lang=".LANG,
        "ICON"=>"btn_copy",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("parser_section_delete"),
        "TITLE"=>GetMessage("parser_section_delete_title"),
        "LINK"=>"javascript:if(confirm('".GetMessage("parser_mnu_del_conf")."'))window.location='list_parser_admin.php?ID=".$ID."&action=delete_sect&parent=".$parentID."&lang=".LANG."&".bitrix_sessid_get()."';",
        "ICON"=>"btn_delete",
    );
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

$rsSection = ShsParserSectionTable::getList(array(
    'limit' =>null,
    'offset' => null,
    'select' => array("*"),
    "filter" => array()
));

while($arSection = $rsSection->Fetch())
{
    if($ID>0 && $ID==$arSection["ID"]) continue 1;
    $arCategory['REFERENCE'][] = "[".$arSection["ID"]."] ".$arSection["NAME"];
    $arCategory['REFERENCE_ID'][] = $arSection["ID"];
}


$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>
<form method="POST" id="shs-parser" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?
$tabControl->Begin();

$tabControl->BeginNextTab();
?>
    <tr>
        <td width="40%"><?echo GetMessage("parser_section_act")?>:</td>
        <td width="60%"><input type="checkbox" name="ACTIVE" value="Y"<?if($arDataTable["ACTIVE"] == "Y" || !$ID) echo " checked"?>>
        </td>
    </tr>
    <tr>
        <td><?echo GetMessage("parser_section_sort")?></td>
        <td><input type="text" name="SORT" value="<?echo !$ID?"500":$arDataTable["SORT"];?>" size="4"></td>
    </tr>
    <tr>
        <td><span class="required">*</span><?echo GetMessage("parser_section_name")?></td>
        <td><input type="text" name="NAME" value="<?echo $arDataTable["NAME"];?>" size="40" maxlength="250"></td>
    </tr>
    <tr>
        <td><?echo GetMessage("parser_category_title")?></td>
        <td><?=SelectBoxFromArray('PARENT_CATEGORY_ID', $arCategory, isset($arDataTable["PARENT_CATEGORY_ID"])?$arDataTable["PARENT_CATEGORY_ID"]:$parentID, GetMessage("parser_category_select"), "id='category' style='width:262px'");?></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><?echo GetMessage("parser_category_description")?>:</td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <textarea cols="60" rows="15"  name="DESCRIPTION" style="width:100%"><?echo $arDataTable["DESCRIPTION"]?></textarea>
            <?=BeginNote();?>
            <?echo GetMessage("parser_section_descr")?>
            <?=EndNote();?>
        </td>
    </tr>
    <?echo bitrix_sessid_post();?>
    <input type="hidden" name="lang" value="<?=LANG?>">
    <?if($ID>0 && !$bCopy):?>
    <input type="hidden" name="ID" value="<?=$ID?>">
    <?endif;?>
    <input type="hidden" name="parent" value="<?=$parentID?>">
<?
$tabControl->End();

$tabControl->Buttons(
    array(
        "disabled"=>($POST_RIGHT<"W"),
        "back_url"=>"list_parser_admin.php?parent=".$parentID."&lang=".LANG,

    )
);

$APPLICATION->SetTitle(($ID>0? GetMessage("shs_parser_section_title_edit") : GetMessage("shs_parser_section_title_add")));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>