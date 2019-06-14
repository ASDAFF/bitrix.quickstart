<?
/////////////////////////////
//INTIS LLC. 2013          //
//Tel.: 8 800-333-12-02    //
//www.sms16.ru             //
//Ruslan Semagin           //
//Skype: pixel365          //
/////////////////////////////

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

IncludeModuleLangFile(__FILE__);

$module_id = "intis.twofactorauthenticationlite";

$POST_RIGHT = $APPLICATION->GetGroupRight("intis.twofactorauthenticationlite");

if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule("main");
CModule::IncludeModule($module_id);

$class = new CIntisTwoFactorAuthentificationLite();

//get users group
$filterGroup = Array
(
    "ACTIVE"         => "Y",
);
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="asc"), $filterGroup);

//get user fields
$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
$keys = $arUser;
$keysRest = $arUser;
ksort($keys);
reset($keys);
ksort($keysRest);
reset($keysRest);

if ($_POST['AUTH_TOKEN']==true)
{
    COption::SetOptionString($module_id, "TOKEN_PARAM", htmlspecialchars($_POST['AUTH_TOKEN']));
    COption::SetOptionString($module_id, "ONE_TIME_PASSWORD_TEMPLATE_FIELD", htmlspecialchars($_POST['ONE_TIME_PASSWORD_TEMPLATE_SELECT']));
    COption::SetOptionString($module_id, "ONE_TIME_PASSWORD_TEMPLATE_SYMBOL_FIELD", htmlspecialchars($_POST['ONE_TIME_PASSWORD_TEMPLATE_SYMBOL_SELECT']));
    COption::SetOptionString($module_id, "ONE_TIME_REGISTER_TEMPLATE_FIELD", htmlspecialchars($_POST['ONE_TIME_REGISTER_TEMPLATE_SELECT']));
    COption::SetOptionString($module_id, "ONE_TIME_REGISTER_TEMPLATE_SYMBOL_FIELD", htmlspecialchars($_POST['ONE_TIME_REGISTER_TEMPLATE_SYMBOL_SELECT']));
    COption::SetOptionString($module_id, "SELECT_USER_PHONE_IN_FIELDS_FIELD", htmlspecialchars($_POST['SELECT_USER_PHONE_IN_FIELDS']));
    COption::SetOptionString($module_id, "BINDING_TO_IP_CHECK", htmlspecialchars($_POST['BINDING_TO_IP']));
    COption::SetOptionString($module_id, "IP_BLOCK_CHECK", htmlspecialchars($_POST['IP_BLOCK']));
    COption::SetOptionString($module_id, "ADMIN_PHONE_ID", htmlspecialchars($_POST['ADMIN_PHONE']));
    COption::SetOptionString($module_id, "CURRENT_ORIGINATOR_FIELD", htmlspecialchars($_POST['SELECT_ORIGINATOR']));
    COption::SetOptionString($module_id, "PROTOCOL_FIELD", htmlspecialchars($_POST['PROTOCOL']));
    COption::SetOptionString($module_id, "HELLO", htmlspecialchars($_POST['HELLO']));

    $vl = "";
    foreach ($_POST["GET_GROUP"] as $key => $value) {
        $vl .= $value.",";
    }
    COption::SetOptionString($module_id, "USER_GROUPS", substr($vl, 0, strlen($vl)-1)); //set group

    LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&amp;lang=".urlencode(LANGUAGE_ID)); //redirect on current page for $_REQUEST['POST'] destroy
}
$OTRTPLFLDVAL = $class->GetRegTemplateSelect();
$OTRTPLSMBVAL = $class->GetRegTemplateSymbol();
$HELLO = $class->HelloMessage();

$OTPTPLFLDVAL = $class->GetPassTemplateSelect();
$OTPTPLSMBVAL = $class->GetPassTemplateSymbol();
$SELUSPHINFLD = $class->GetUserPhoneField();
$BINDINGTOIP = $class->BindingIpCheck();
$IPBLOCK = $class->IpBlockCheck();
$ADMINPHONE = $class->GetAdminPhone();
$CURRENTORIGINATOR = $class->GetCurrentOriginator();
$GROUP = $class->GetGroup();
$PROTOCOL = $class->GetProtocol();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?
$aTabs = array();
$aTabs[] = array("DIV" => "edit1", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB1"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit2", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB2"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit3", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit31", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB31"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB4"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit5", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB5"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit6", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB6"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit7", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB7"), "ICON" => "", "TITLE" => "");
$aTabs[] = array("DIV" => "edit8", "TAB" => GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB8"), "ICON" => "", "TITLE" => "");

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
<script>
    function set_value(text, val)
    {
        text = document.getElementById(text);
        text.focus();
        text.value += val;
    }
</script>
<form method="POST" name="socserv_settings" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?=urlencode(LANGUAGE_ID)?>" autocomplete="off">
<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB1_HEADING")?></td>
    </tr>

    <tr>
        <td>
            <input type="text" name="AUTH_TOKEN" id="TOKEN_PARAM" value="<?=$class->GetTokenField()?>" style="width:300px;">
        </td>
    </tr>
<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB2_HEADING1")?></td>
    </tr>

    <tr>
        <td>
            <select name="ONE_TIME_PASSWORD_TEMPLATE_SELECT">
                <option value="123456789"<?if ($OTPTPLFLDVAL=='123456789'):?> selected="selected"<?endif;?>>123456789</option>
                <option value="ABCDEFGHIJKLMNOPQRSTUVWXYZ"<?if ($OTPTPLFLDVAL=='ABCDEFGHIJKLMNOPQRSTUVWXYZ'):?> selected="selected"<?endif;?>>ABCDEFGHIJKLMNOPQRSTUVWXYZ</option>
                <option value="123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"<?if ($OTPTPLFLDVAL=='123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'):?> selected="selected"<?endif;?>>123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ</option>
            </select>
            <input type="hidden" id="ONE_TIME_PASSWORD_TEMPLATE_FIELD" value="<?=$OTPTPLFLDVAL?>" readonly="readonly">
        </td>
    </tr>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB2_HEADING2")?></td>
    </tr>

    <tr>
        <td>
            <select name="ONE_TIME_PASSWORD_TEMPLATE_SYMBOL_SELECT">
                <option value="5"<?if ($OTPTPLSMBVAL=='5'):?> selected="selected"<?endif;?>>5<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="6"<?if ($OTPTPLSMBVAL=='6'):?> selected="selected"<?endif;?>>6<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="7"<?if ($OTPTPLSMBVAL=='7'):?> selected="selected"<?endif;?>>7<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="8"<?if ($OTPTPLSMBVAL=='8'):?> selected="selected"<?endif;?>>8<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="9"<?if ($OTPTPLSMBVAL=='9'):?> selected="selected"<?endif;?>>9<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="10"<?if ($OTPTPLSMBVAL=='10'):?> selected="selected"<?endif;?>>10<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="11"<?if ($OTPTPLSMBVAL=='11'):?> selected="selected"<?endif;?>>11<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="12"<?if ($OTPTPLSMBVAL=='12'):?> selected="selected"<?endif;?>>12<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="13"<?if ($OTPTPLSMBVAL=='13'):?> selected="selected"<?endif;?>>13<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="14"<?if ($OTPTPLSMBVAL=='14'):?> selected="selected"<?endif;?>>14<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="15"<?if ($OTPTPLSMBVAL=='15'):?> selected="selected"<?endif;?>>15<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
                <option value="20"<?if ($OTPTPLSMBVAL=='20'):?> selected="selected"<?endif;?>>20<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            </select>
            <input type="hidden" id="ONE_TIME_PASSWORD_TEMPLATE_SYMBOL_FIELD" value="<?=$OTPTPLSMBVAL?>" readonly="readonly">
        </td>
    </tr>

<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING00")?></td>
    </tr>
    <tr>
        <td>
            <select name="PROTOCOL">
                <option value="https://"<?if ($PROTOCOL=="https://"):?> selected<?endif;?>>https://</option>
                <option value="http://"<?if ($PROTOCOL=="http://"):?> selected<?endif;?>>http://</option>
            </select>
            <input type="hidden" id="PROTOCOL_FIELD" value="<?=$PROTOCOL?>" readonly>
        </td>
    </tr>

    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING0")?></td>
    </tr>
    <tr>
        <td>
            <?=$class->GetOriginator($class->GetTokenField())?>
            <input type="hidden" id="CURRENT_ORIGINATOR_FIELD" value="<?=$CURRENTORIGINATOR?>" readonly>
        </td>
    </tr>

    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING1")?></td>
    </tr>

    <tr>
        <td>
            <?
            $selectedGroup = explode(",", substr($GROUP, 0, strlen($GROUP)-0));
            ?>
            <select multiple="multiple" name="GET_GROUP[]" size="7">
                <?
                while($rsGroups->NavNext(true, "f_")) :
                    if (in_array($f_ID, $selectedGroup))
                    {
                        $selected = " selected='selected'";
                    }else{
                        $selected = "";
                    }
                    echo "<option value='".$f_ID."'".$selected.">[".$f_ID."] ".$f_NAME."</option>";
                endwhile;
                ?>
            </select>
            <input type="hidden" id="USER_GROUPS" value="<?=$GROUP?>" readonly>
        </td>
    </tr>

    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING2")?></td>
    </tr>
    <tr>
        <td>
            <select name="SELECT_USER_PHONE_IN_FIELDS">
                <?
                echo "<option value='none'>".GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SELECT_FIELD")."</option>";
                while (list($key, $val) = each($keysRest)) {
                    if(substr($key, 0, 8) == "PERSONAL" || substr($key, 0, 4) == "WORK" || substr($key, 0, 2) == "UF" || $key == "LOGIN")
                    {
                        if ($SELUSPHINFLD==$key)
                        {
                            $selectedOption = " selected='selected'";
                        }else{
                            $selectedOption = "";
                        }
                        echo "<option value='".$key."'".$selectedOption.">".$key."</option>";
                    }
                }
                ?>
            </select>
            <input type="hidden" id="SELECT_USER_PHONE_IN_FIELDS_FIELD" value="<?=$SELUSPHINFLD?>" readonly="readonly">
        </td>
    </tr>

    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING3")?></td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" name="BINDING_TO_IP"<?if ($BINDINGTOIP=="on"):?> checked<?endif;?>> <?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_ON")?>
            <input type="hidden" id="BINDING_TO_IP_CHECK" value="<?=$BINDINGTOIP?>" readonly="readonly">
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_BINDING_TO_IP_NOTICE")?>
        </td>
    </tr>

    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING4")?></td>
    </tr>
    <?if (IsModuleInstalled("security")):?>
        <tr>
            <td>
                <input type="checkbox" name="IP_BLOCK"<?if ($IPBLOCK=="on"):?> checked<?endif;?>> <?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_ON")?>
                <input type="hidden" id="IP_BLOCK_CHECK" value="<?=$IPBLOCK?>" readonly="readonly">
                <input type="hidden" id="IBLOCK_WITH_DATA" value="<?=$class->CreateIblockId()?>" readonly="readonly">
            </td>
        </tr>
        <tr>
            <td>
                <?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_IP_BLOCK_NOTICE")?>
            </td>
        </tr>
    <?else:?>
        <tr>
            <td style="color:#ff0000;">
                <?=GetMessage('TWOFACTORAUTHENTIFICATIONLITE_SECURITY_NOT_INSTALL')?>
            </td>
        </tr>
    <?endif;?>

    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING5")?></td>
    </tr>
    <tr>
        <td>
            <label><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_LABEL_FOR_ADMIN_PHONE")?></label> <input type="text" name="ADMIN_PHONE" id="ADMIN_PHONE_ID" value="<?=$ADMINPHONE?>">
        </td>
    </tr>
<?
$tabControl->BeginNextTab();
?>

<tr class="heading">
    <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_REGISTER_S")?></td>
</tr>

<tr>
    <td>
        <select name="ONE_TIME_REGISTER_TEMPLATE_SELECT">
            <option value="123456789"<?if ($OTRTPLFLDVAL=='123456789'):?> selected="selected"<?endif;?>>123456789</option>
            <option value="ABCDEFGHIJKLMNOPQRSTUVWXYZ"<?if ($OTRTPLFLDVAL=='ABCDEFGHIJKLMNOPQRSTUVWXYZ'):?> selected="selected"<?endif;?>>ABCDEFGHIJKLMNOPQRSTUVWXYZ</option>
            <option value="123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"<?if ($OTRTPLFLDVAL=='123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'):?> selected="selected"<?endif;?>>123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ</option>
        </select>
        <input type="hidden" id="ONE_TIME_REGISTER_TEMPLATE_FIELD" value="<?=$OTRTPLFLDVAL?>" readonly="readonly">
    </td>
</tr>
<tr class="heading">
    <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_REGISTER_L")?></td>
</tr>

<tr>
    <td>
        <select name="ONE_TIME_REGISTER_TEMPLATE_SYMBOL_SELECT">
            <option value="5"<?if ($OTRTPLSMBVAL=='5'):?> selected="selected"<?endif;?>>5<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="6"<?if ($OTRTPLSMBVAL=='6'):?> selected="selected"<?endif;?>>6<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="7"<?if ($OTRTPLSMBVAL=='7'):?> selected="selected"<?endif;?>>7<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="8"<?if ($OTRTPLSMBVAL=='8'):?> selected="selected"<?endif;?>>8<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="9"<?if ($OTRTPLSMBVAL=='9'):?> selected="selected"<?endif;?>>9<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="10"<?if ($OTRTPLSMBVAL=='10'):?> selected="selected"<?endif;?>>10<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="11"<?if ($OTRTPLSMBVAL=='11'):?> selected="selected"<?endif;?>>11<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="12"<?if ($OTRTPLSMBVAL=='12'):?> selected="selected"<?endif;?>>12<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="13"<?if ($OTRTPLSMBVAL=='13'):?> selected="selected"<?endif;?>>13<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="14"<?if ($OTRTPLSMBVAL=='14'):?> selected="selected"<?endif;?>>14<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="15"<?if ($OTRTPLSMBVAL=='15'):?> selected="selected"<?endif;?>>15<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
            <option value="20"<?if ($OTRTPLSMBVAL=='20'):?> selected="selected"<?endif;?>>20<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SYM")?></option>
        </select>
        <input type="hidden" id="ONE_TIME_REGISTER_TEMPLATE_SYMBOL_FIELD" value="<?=$OTRTPLSMBVAL?>" readonly="readonly">
    </td>
</tr>

<tr class="heading">
    <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_REGISTER_T")?></td>
</tr>

<tr>
    <td>
        <textarea rows="6" cols="100" id="HELLO" name="HELLO"><?=$HELLO?></textarea><br />
        <a href="javascript:set_value('HELLO', '#LOGIN#')">#LOGIN#</a> <?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_PUT_LOGIN")?>
    </td>
</tr>
<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING6")?></td>
    </tr>
    <tr>
        <td>
            <b>CIntisTwoFactorAuthentificationLite::GetPassTemplateSelect()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_1")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetPassTemplateSymbol()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_2")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GenPass($lenght)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_3")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetUserPhoneField()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_4")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetTokenField()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_5")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::BindingIpCheck()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_6")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::IpBlockCheck()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_7")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::CreateIblockId()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_8")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetAdminPhone()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_9")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetGroup()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_10")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetCurrentOriginator()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_11")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetOriginator($secretKey)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_12")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::ValidatePhone($phone)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_13")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::DeleteElement($name, $iblockId)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_14")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::__GetElement($ip, $login, $iblockId, $adminAlert)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_15")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::DelayedLocking()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_16")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::AddIpToStopList($ip, $activeFrom, $activeTo, $name, $status)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_17")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::Send($message, $phone, $secretKey, $oneTimePass)</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_18")?><br /><br />
            <b>CIntisTwoFactorAuthentificationLite::GetBalance()</b><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_API_19")?><br /><br />
        </td>
    </tr>

<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING7").ConvertTimeStamp(time(), "FULL")?></td>
    </tr>
    <tr>
        <td>
            <?=$class->GetBalance()?> <a href="javascript:window.location.reload()" class="i_login_button"><?=GetMessage("TWOFACTORAUTHENTIFICATION_RELOAD")?></a>
        </td>
    </tr>
<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING8")?></td>
    </tr>
    <tr>
        <td>
            <ol>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL1")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL2")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL3")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL4")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL5")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL6")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL7")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL8")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL9")?></li>
                <li><?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL11")?></li>
            </ol><hr />
        </td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("TWOFACTORAUTHENTIFICATION_MANUAL10")?>
        </td>
    </tr>
<?
$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td><?echo GetMessage("TWOFACTORAUTHENTIFICATIONLITE_TAB3_HEADING9")?></td>
    </tr>
    <tr>
        <td>
            <?=GetMessage("TWOFACTORAUTHENTIFICATION_CONTACT_OOO_INTIS")?>
            <?=GetMessage("TWOFACTORAUTHENTIFICATION_CONTACT_PHONE")?>
            <?=GetMessage("TWOFACTORAUTHENTIFICATION_CONTACT_EMAIL")?>
            <?=GetMessage("TWOFACTORAUTHENTIFICATION_CONTACT_SKYPE")?>
        </td>
    </tr>

<?
$tabControl->BeginNextTab();
?>
<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)> 0 && check_bitrix_sessid())
{
    if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}
?>
<?$tabControl->Buttons();?>
    <input type="submit" name="<?=GetMessage("MAIN_OPT_APPLY")?>" value="<?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SAVE")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>"> <?if ($class->GetTokenField()==false):?><?=GetMessage("TWOFACTORAUTHENTIFICATIONLITE_SAVE_NOTICE")?><?endif;?>
<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>