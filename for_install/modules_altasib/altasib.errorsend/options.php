<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: Evgeniy Pedan                    #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2010 ALTASIB             #
#################################################
?>
<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

if(!$USER->IsAdmin()) return;

$module_id = "altasib_errorsend";
$strWarning = "";



$defEmail = COption::GetOptionString("main", "email_from", "error@".str_replace("www.","",$_SERVER["SERVER_NAME"]));
$valLogo = COption::GetOptionString($module_id, "logo", "/bitrix/images/altasib.errorsend/altasib.errorsend.png");
if(!$valLogo)
        $valLogo = "/bitrix/images/altasib.errorsend/altasib.errorsend.png";
$defIP = COption::GetOptionInt($module_id, "limit_ip", 30);


$arAllOptions = array(
        "main" => Array(
                Array("email_from", GetMessage("ALTASIB_ERROR_SEND_OPTIONS_EMAIL_FROM"), $defEmail, Array("text", 30)),
                Array("email_to", GetMessage("ALTASIB_ERROR_SEND_OPTIONS_EMAIL_TO"), $defEmail, Array("text", 30)),
                Array("logo", GetMessage("ALTASIB_ERROR_SEND_OPTIONS_LOGO"), $valLogo, Array("file")),
                Array("limit_ip", GetMessage("ALTASIB_ERROR_SEND_OPTIONS_LIMIT_IP"), $defIP, Array("text", 30)),
        ),
);
$aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
        array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "altasib_comments_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);

//Restore defaults
if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
        COption::RemoveOption("altasib_errorsend");
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

function __AdmSettingsDrawRowFile($module_id, $Option)
{
        $bFileman = CModule::IncludeModule('fileman');

        $arControllerOption = CControllerClient::GetInstalledOptions($module_id);
        $val = COption::GetOptionString($module_id, $Option[0], $Option[2]);

        $type = $Option[3];
        $Option[0] = $Option[0];
        ?>
                <tr>
                        <td valign="top" width="50%"><?

                                echo $Option[1];

                                if (strlen($sup_text) > 0)
                                {
                                        ?><span class="required"><sup><?=$sup_text?></sup></span><?
                                }
                                        ?></td>
                        <td valign="middle" width="50%"><?
                        if($type[0]=="file"):
                                if($bFileman):
                                        echo CMedialib::InputFile(
                                                "logo", $val,
                                                array(
                                                        "IMAGE" => "N",
                                                        "PATH" => "Y",
                                                        "FILE_SIZE" => "Y",
                                                        "DIMENSIONS" => "Y",
                                                        "IMAGE_POPUP"=>"Y",
                                                        "MAX_SIZE" => array("W" => 200, "H"=>200)
                                                        ), //info
                                                false, //file
                                                array(), //server
                                                array(), //media lib
                                                false, //descr
                                                false //delete
                                        );

                                        $Module = CModule::CreateModuleObject("fileman");
                                        if(CheckVersion("9.5.4", $Module->MODULE_VERSION)):
                                                $arFile = CFile::_GetImgParams($val);
                                                echo CFile::ShowImage($arFile["SRC"], 200, 200, "border=0", "", true);
                                        endif;
                                else:

                                        $arFile = CFile::_GetImgParams($val);
                                        echo CFile::InputFile("logo", 20, "", false, 0, "IMAGE", "", 0);
                                        echo "<br>";
                                        echo CFile::ShowImage($arFile["SRC"], 200, 200, "border=0", "", true);
                                endif;
                        endif;
                        ?></td>
                </tr>
        <?
}

function ShowParamsHTMLByArray($arParams)
{
        foreach($arParams as $Option)
        {
                 if($Option[3][0] == "file")
                        __AdmSettingsDrawRowFile("altasib_errorsend", $Option);
                 else
                        __AdmSettingsDrawRow("altasib_errorsend", $Option);
        }
}

//Save options
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
        if(strlen($RestoreDefaults)>0)
        {
                COption::RemoveOption("altasib_errorsend");
        }
        else
        {
                foreach($arAllOptions as $aOptGroup)
                {
                        foreach($aOptGroup as $option)
                        {
                                if($option[0] == "logo" && strlen($_POST["logo"])>0)
                                {
                                        $ABS_FILE_NAME = $_SERVER["DOCUMENT_ROOT"].$_POST["logo"];
                                        $file_name = $_POST["logo"];

                                        if(is_file($ABS_FILE_NAME) && file_exists($ABS_FILE_NAME))
                                        {
                                                if(!CFile::IsImage($file_name))
                                                {

                                                        $strError = GetMessage("ALTASIB_ERROR_NO_IMG");
                                                }
                                        }
                                        else
                                        {
                                                $strError = GetMessage("ALTASIB_ERROR_NO_FILE");
                                        }
                                }
                        }
                }


                if(!$strError)
                {
                        foreach($arAllOptions as $aOptGroup)
                        {
                                foreach($aOptGroup as $option)
                                {
                                        if($option[0] == "logo" && strlen($_POST["logo"])==0)
                                                continue;
                                        __AdmSettingsSaveOption($module_id, $option);
                                }
                        }
                }
        }
        if(!$strError)
        {
                if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
                        LocalRedirect($_REQUEST["back_url_settings"]);
                else
                        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
        }
        else
        {
                $APPLICATION->ThrowException($strError);
        }
}
?>
<?
if ($e = $APPLICATION->GetException())
        $message = new CAdminMessage(GetMessage("ALTASIB_ERROR_SAVING"), $e);

if($message)
        echo $message->Show();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
        ?>
<div style="background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;"><div style="background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff">
        <a href="http://www.is-market.ru?param=cl" target="_blank"><img src="/bitrix/images/altasib.errorsend/is-market.gif" style="float: left; margin-right: 15px;" border="0" /></a>
        <div style="margin: 13px 0px 0px 0px">
                <a href="http://www.is-market.ru?param=cl" target="_blank" style="color: #fff; font-size: 10px; text-decoration: none"><?=GetMessage("ALTASIB_IS")?></a>
        </div>
</div></div>
        <?
        ShowParamsHTMLByArray($arAllOptions["main"]);
        ?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
        if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<div align="left">
        <input type="hidden" name="Update" value="Y">
        <input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
        <input type="reset" <?if(!$USER->IsAdmin())echo " disabled ";?> name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
        <input type="button" <?if(!$USER->IsAdmin())echo " disabled ";?>  type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
</div>
<?$tabControl->End();?>
<?=bitrix_sessid_post();?>
</form>
