<?
if (!$USER->IsAdmin())
    return;

IncludeModuleLangFile(__FILE__);

// save changes
if ((isset($_POST['save']) || isset($_POST['apply'])) && check_bitrix_sessid()) {
    if(!empty($_POST["SMS_ENABLE"]))
        COption::SetOptionString("wl.form", "SMS_ENABLE", "Y");
    else
        COption::SetOptionString("wl.form", "SMS_ENABLE", "N");
    
    if(!empty($_POST["SMSC_LOGIN"]))
        COption::SetOptionString("wl.form", "SMSC_LOGIN", $_POST["SMSC_LOGIN"]);
    else
        COption::SetOptionString("wl.form", "SMSC_LOGIN", "");
    
    if(!empty($_POST["SMSC_PASSWORD"]))
        COption::SetOptionString("wl.form", "SMSC_PASSWORD", $_POST["SMSC_PASSWORD"]);
    else
        COption::SetOptionString("wl.form", "SMSC_PASSWORD", "");
    
    if(!empty($_POST["SMSC_CHARSET"]))
        COption::SetOptionString("wl.form", "SMSC_CHARSET", $_POST["SMSC_CHARSET"]);
    else
        COption::SetOptionString("wl.form", "SMSC_CHARSET", "");
    
    if(!empty($_POST["SMSC_PHONE"]))
        COption::SetOptionString("wl.form", "SMSC_PHONE", $_POST["SMSC_PHONE"]);
    else
        COption::SetOptionString("wl.form", "SMSC_PHONE", "");
    
    if(!empty($_POST["CALLBACK_ENABLE"]))
        COption::SetOptionString("wl.form", "CALLBACK_ENABLE", "Y");
    else
        COption::SetOptionString("wl.form", "CALLBACK_ENABLE", "N");
    
    if(!empty($_POST["CALLBACK_KEY"]))
        COption::SetOptionString("wl.form", "CALLBACK_KEY", $_POST["CALLBACK_KEY"]);
    else
        COption::SetOptionString("wl.form", "CALLBACK_KEY", "");
    
    if(!empty($_POST["CALLBACK_SECRET"]))
        COption::SetOptionString("wl.form", "CALLBACK_SECRET", $_POST["CALLBACK_SECRET"]);
    else
        COption::SetOptionString("wl.form", "CALLBACK_SECRET", "");
    
    if(!empty($_POST["CALLBACK_PHONE"]))
        COption::SetOptionString("wl.form", "CALLBACK_PHONE", $_POST["CALLBACK_PHONE"]);
    else
        COption::SetOptionString("wl.form", "CALLBACK_PHONE", "");
}

// get data
$smsEnable = (COption::GetOptionString("wl.form", "SMS_ENABLE") == "Y") ? true : false;
$SMSC_LOGIN = COption::GetOptionString("wl.form", "SMSC_LOGIN", "");
$SMSC_PASSWORD = COption::GetOptionString("wl.form", "SMSC_PASSWORD", "");
$SMSC_CHARSET = COption::GetOptionString("wl.form", "SMSC_CHARSET", "windows-1251");
$SMSC_PHONE = COption::GetOptionString("wl.form", "SMSC_PHONE", "");

$callbackEnable = (COption::GetOptionString("wl.form", "CALLBACK_ENABLE") == "Y") ? true : false;
$CALLBACK_KEY = COption::GetOptionString("wl.form", "CALLBACK_KEY", "");
$CALLBACK_SECRET = COption::GetOptionString("wl.form", "CALLBACK_SECRET", "");
$CALLBACK_PHONE = COption::GetOptionString("wl.form", "CALLBACK_PHONE", "");

// build page
$aTabs = Array(
    Array(
        "DIV" => "edit1",
        "TAB" => GetMessage("TAB_TITLE"),
        "TITLE" => GetMessage("TAB_DESCRIPTION"),
        "ICON" => 'tbIcon'
    )
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<? $tabControl->Begin(); ?>
<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
    <? $tabControl->BeginNextTab(); ?>
    <tr class="heading">
        <td colspan="2"><?= GetMessage("TAB_CALLBACK_HEADER"); ?></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("TAB_CALLBACK_HEADER"); ?>:</td>
        <td width="50%"><input type="checkbox" name="CALLBACK_ENABLE" value="1" <? if($callbackEnable){ ?>checked="checked"<? } ?> /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("CALLBACK_KEY"); ?>:</td>
        <td width="50%"><input type="text" name="CALLBACK_KEY" value="<?=$CALLBACK_KEY;?>" /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("CALLBACK_SECRET"); ?>:</td>
        <td width="50%"><input type="text" name="CALLBACK_SECRET" value="<?=$CALLBACK_SECRET;?>" /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("CALLBACK_PHONE"); ?>:</td>
        <td width="50%"><input type="text" name="CALLBACK_PHONE" value="<?=$CALLBACK_PHONE;?>" /></td>
    </tr>
    <tr>
        <td width="50%">&nbsp;</td>
        <td width="50%"><?= GetMessage("CALLBACK_DESCRIPTION"); ?></td>
    </tr>
    <tr class="heading">
        <td colspan="2"><?= GetMessage("TAB_HEADER"); ?></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("SMS_ENABLE"); ?>:</td>
        <td width="50%"><input type="checkbox" name="SMS_ENABLE" value="1" <? if($smsEnable){ ?>checked="checked"<? } ?> /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("SMSC_LOGIN"); ?>:</td>
        <td width="50%"><input type="text" name="SMSC_LOGIN" value="<?=$SMSC_LOGIN;?>" /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("SMSC_PASSWORD"); ?>:</td>
        <td width="50%"><input type="password" name="SMSC_PASSWORD" value="<?=$SMSC_PASSWORD;?>" /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("SMSC_CHARSET"); ?>:</td>
        <td width="50%"><input type="text" name="SMSC_CHARSET" value="<?=$SMSC_CHARSET;?>" /></td>
    </tr>
    <tr>
        <td width="50%"><?= GetMessage("SMSC_PHONE"); ?>:</td>
        <td width="50%"><input type="text" name="SMSC_PHONE" value="<?=$SMSC_PHONE;?>" /></td>
    </tr>
    <tr>
        <td width="50%">&nbsp;</td>
        <td width="50%"><?= GetMessage("SMS_DESCRIPTION"); ?></td>
    </tr>
    <?
    $tabControl->Buttons(Array(
        "disabled" => false,
        "back_url" => $_REQUEST["back_url"]
    ));
    ?>
    <?= bitrix_sessid_post(); ?>
</form>
<? $tabControl->End(); ?>