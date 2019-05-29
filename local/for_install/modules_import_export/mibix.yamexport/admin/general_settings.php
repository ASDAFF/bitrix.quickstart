<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

$iModuleID = "mibix.yamexport";
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('MIBIX_YAM_GENERAL_SETTING_TITLE'));

$POST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if ($POST_RIGHT == "D")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("MIBIX_YAM_GENERAL_SETTING_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MIBIX_YAM_GENERAL_SETTING_TITLE")),
    array("DIV" => "edit2", "TAB" => GetMessage("MIBIX_YAM_GENERAL_CURRENCY_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MIBIX_YAM_GENERAL_CURRENCY_TITLE")),
    array("DIV" => "edit3", "TAB" => GetMessage("MIBIX_YAM_GENERAL_SLP_TAB"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("MIBIX_YAM_GENERAL_SLP_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID);		// ID редактируемой записи
$arError = array();
$bShowRes = false;
$bVarsFromForm = false;
if(!is_array($USER_GROUP_ID)){
    $USER_GROUP_ID = array();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin.php');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/include.php");

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

// Обработка события "Сохранить"
if($REQUEST_METHOD == "POST" && !empty($save) && $POST_RIGHT >= "W" && check_bitrix_sessid())
{
    // Поля формы в массив
    $generalModel = new CMibixModelGeneral();
    $arFields = Array(
        "name"              => $f_name,
        "company"           => $f_company,
        "salon"             => ($f_salon <> "Y"? "N":"Y"),
        "url"               => $f_url,
        "platform_version"  => ($f_platform_version <> "Y"? "N":"Y"),
        "agency"            => $f_agency,
        "email"             => $f_email,
        "local_delivery_cost" => $f_local_delivery_cost,
        "cpa"               => ($f_cpa <> "1"? "0":"1"),
        "adult"             => ($f_adult <> "Y"? "N":"Y"),
        "utm"               => $f_utm,
        "step_limit"        => $f_step_limit,
        "step_path"         => $f_step_path,
        "step_interval_run" => $f_step_interval_run,
        "currency_rate"     => $f_currency_rate,
        "currency_rub"      => $f_currency_rub,
        "currency_rub_plus" => $f_currency_rub_plus,
        "currency_byr"      => $f_currency_byr,
        "currency_byr_plus" => $f_currency_byr_plus,
        "currency_uah"      => $f_currency_uah,
        "currency_uah_plus" => $f_currency_uah_plus,
        "currency_kzt"      => $f_currency_kzt,
        "currency_kzt_plus" => $f_currency_kzt_plus,
        "currency_usd"      => $f_currency_usd,
        "currency_usd_plus" => $f_currency_usd_plus,
        "currency_eur"      => $f_currency_eur,
        "currency_eur_plus" => $f_currency_eur_plus
    );

    // Обновляем (ID>0) или добавляем новую запись
    if($ID > 0)
    {
        if(!($res = $generalModel->Update($ID, $arFields, $SITE_ID)))
        {
            $arError = $generalModel->getArMsg();
        }
    }
    else
    {
        // Попытка добавить новую запись в таблицу или возврат описания ошибки
        if(!($res=$generalModel->Add($arFields)))
        {
            $arError = $generalModel->getArMsg();
        }
    }

    if($res)
    {
        // если сохранение прошло удачно - перенаправим на новую страницу
        // (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
        if($apply!="")
            LocalRedirect("/bitrix/admin/mibix.yamexport_general_settings.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
        else
            LocalRedirect("/bitrix/admin/mibix.yamexport_general_list.php?lang=".LANG);
    }
    else
    {
        // если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
        if($e = $APPLICATION->GetException())
        {
            $message = new CAdminMessage(GetMessage("MIBIX_YAM_GENERAL_SET_SAVE_ERROR"), $e);
        }
        $bVarsFromForm = true;
    }
}

// Предварительная чистка глобальных перменных с префиксом
ClearVars();

// Получаем записи с настройками из базы и заноси
if($ID > 0)
{
    $generalModel = CMibixModelGeneral::GetByID($ID);
    if(!$generalModel->ExtractFields("str_"))
    {
        $ID=0;
    }
}

// Если во время запроса добавить или обновить данные в таблице не получилось,
// то создаем глобальные переменные из полей таблицы с префиксом str_, чтобы не потерять заполненные данные формы
if($bVarsFromForm)
{
    $DB->InitTableVarsForEdit("b_mibix_yam_general", "", "str_");
}

// Выводим сообщение об успешном сохранении, если
if($_REQUEST["mess"] == "ok" && $ID>0)
    CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("MIBIX_YAM_SAVED"), "TYPE"=>"OK"));

// Выводим ошибки, если они есть
if(count($arError)>0)
{
    $e = new CAdminException($arError);
    $message = new CAdminMessage(GetMessage("MIBIX_YAM_ERROR_TITLE"), $e);
    echo $message->Show();
}

// currency options для настроек валют
function get_currency_options($selected="")
{
    $arSelOptions = array(
        "" => GetMessage("MIBIX_YAM_GENERAL_NOTUSE"),
        "CBRF" => GetMessage("MIBIX_YAM_GENERAL_CBRF"),
        "NBU" => GetMessage("MIBIX_YAM_GENERAL_NBU"),
        "NBK" => GetMessage("MIBIX_YAM_GENERAL_NBK"),
        "СВ" => GetMessage("MIBIX_YAM_GENERAL_СВ")
    );

    if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
        $arSelOptions["MODULE"] = GetMessage("MIBIX_YAM_GENERAL_USEMODV");

    foreach ($arSelOptions as $keyOpt => $valOpt)
    {
        $is_selected=($selected==$keyOpt)? " selected":"";
        echo "<option value=\"".$keyOpt."\"".$is_selected.">".$valOpt."</option>";
    }
}

// currency plus options для настроек валют
function get_currency_plus_options($selected=0)
{
    for($i=0;$i<30;$i++)
    {
        $is_selected=($selected==$i)? " selected":"";
        echo "<option value=\"".$i."\"".$is_selected.">".$i."</option>";
    }
}

$tabControl->Begin();
echo '<form name="'.$iModuleID.'" method="POST" action="'.$APPLICATION->GetCurPage().'?mid='.$iModuleID.'&lang='.LANGUAGE_ID.'" enctype="multipart/form-data">'.bitrix_sessid_post();

$tabControl->BeginNextTab();
?>
    <tr class="heading">
        <td colspan="2"><?=GetMessage("MIBIX_YAM_GENERAL_HMAIN")?></td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l">
            <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_GENERAL_SHOPNAME")?></span>:
        </td>
        <td width="60%">
            <input type="text" size="30" maxlength="20" value="<?=$str_name;?>" name="f_name" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_SHOPNAME_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l">
            <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_GENERAL_FULLNAME")?></span>:
        </td>
        <td width="60%">
            <input type="text" size="50" maxlength="255" value="<?=$str_company;?>" name="f_company" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_FULLNAME_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_SALON");?>:</td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="checkbox" name="f_salon" value="Y"<?if($str_salon=="Y") echo " checked";?>>
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_SALON_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l">
            <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_GENERAL_SHOPURL")?></span>:
        </td>
        <td width="60%"><input type="text" size="50" maxlength="100" value="<?=$str_url;?>" name="f_url" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_SHOPURL_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CMSINFO")?>:</td>
        <td width="60%">
            <input type="checkbox" name="f_platform_version" id="f_platform_version" value="Y"<?if($str_platform_version=="Y") echo " checked";?> />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_CMSINFO_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_AGENCY")?>:</td>
        <td width="60%">
            <input type="text" size="50" maxlength="100" value="<?=$str_agency;?>" name="f_agency" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_AGENCY_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_EMAIL")?>:</td>
        <td width="60%">
            <input type="text" size="50" maxlength="100" value="<?=$str_email;?>" name="f_email" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_EMAIL_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_DELIVERY")?>:</td>
        <td width="60%">
            <input type="text" size="30" maxlength="20" value="<?=$str_local_delivery_cost;?>" name="f_local_delivery_cost" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_DELIVERY_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CPA")?>:</td>
        <td width="60%">
            <input type="checkbox" name="f_cpa" id="cpa" value="1"<?if((integer)$str_cpa==1) echo " checked";?> />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_CPA_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_ADULT")?>:</td>
        <td width="60%">
            <input type="checkbox" name="f_adult" id="f_adult" value="Y"<?if($str_adult=="Y") echo " checked";?> />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_ADULT_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_UTM")?>:</td>
        <td width="60%">
            <input type="text" size="50" maxlength="255" value="<?=$str_utm;?>" name="f_utm" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_UTM_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
<?$tabControl->BeginNextTab();?>
    <tr>
        <td class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_DEFAULT_CURRENCY")?>:</td>
        <td class="adm-detail-content-cell-r">
            <input id="MAIN_CURRENCY_1" name="f_currency_rate" type="radio" value="RUB"<?if($str_currency_rate=="RUB" || empty($str_currency_rate)) echo " checked";?>><label for="MAIN_CURRENCY_1"><?=GetMessage("MIBIX_YAM_GENERAL_RUB")?></label><br />
            <input id="MAIN_CURRENCY_2" name="f_currency_rate" type="radio" value="BYR"<?if($str_currency_rate=="BYR") echo " checked";?>><label for="MAIN_CURRENCY_2"><?=GetMessage("MIBIX_YAM_GENERAL_BYR")?></label><br />
            <input id="MAIN_CURRENCY_3" name="f_currency_rate" type="radio" value="UAH"<?if($str_currency_rate=="UAH") echo " checked";?>><label for="MAIN_CURRENCY_3"><?=GetMessage("MIBIX_YAM_GENERAL_UAH")?></label><br />
            <input id="MAIN_CURRENCY_4" name="f_currency_rate" type="radio" value="KZT"<?if($str_currency_rate=="KZT") echo " checked";?> onclick="DisableControls(false);"><label for="MAIN_CURRENCY_4"><?=GetMessage("MIBIX_YAM_GENERAL_KZT")?></label>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_RUB")?>:</td>
        <td width="60%">
            <select name="f_currency_rub" size="1">
                <?get_currency_options($str_currency_rub);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_PLUS")?>:</td>
        <td width="60%">
            <select name="f_currency_rub_plus" size="1">
                <?get_currency_plus_options($str_currency_rub_plus);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_BYR")?>:</td>
        <td width="60%">
            <select name="f_currency_byr" size="1">
                <?get_currency_options($str_currency_byr);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_PLUS")?>:</td>
        <td width="60%">
            <select name="f_currency_byr_plus" size="1">
                <?get_currency_plus_options($str_currency_byr_plus);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_UAH")?>:</td>
        <td width="60%">
            <select name="f_currency_uah" size="1">
                <?get_currency_options($str_currency_uah);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_PLUS")?>:</td>
        <td width="60%">
            <select name="f_currency_uah_plus" size="1">
                <?get_currency_plus_options($str_currency_uah_plus);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_KZT")?>:</td>
        <td width="60%">
            <select name="f_currency_kzt" size="1">
                <?get_currency_options($str_currency_kzt);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_PLUS")?>:</td>
        <td width="60%">
            <select name="f_currency_kzt_plus" size="1">
                <?get_currency_plus_options($str_currency_kzt_plus);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_USD")?>:</td>
        <td width="60%">
            <select name="f_currency_usd" size="1">
                <?get_currency_options($str_currency_usd);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_PLUS")?>:</td>
        <td width="60%">
            <select name="f_currency_usd_plus" size="1">
                <?get_currency_plus_options($str_currency_usd_plus);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_EUR")?>:</td>
        <td width="60%">
            <select name="f_currency_eur" size="1">
                <?get_currency_options($str_currency_eur);?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_GENERAL_CURRENCY_PLUS")?>:</td>
        <td width="60%">
            <select name="f_currency_eur_plus" size="1">
                <?get_currency_plus_options($str_currency_eur_plus);?>
            </select>
        </td>
    </tr>
<?$tabControl->BeginNextTab();?>
    <tr class="heading">
        <td colspan="2"><?=GetMessage("MIBIX_YAM_GENERAL_SLP_TITLE")?></td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l">
            <?=GetMessage("MIBIX_YAM_GENERAL_SLP_INTERVAL")?>:
        </td>
        <td width="60%">
            <input type="text" size="20" maxlength="20" value="<?=$str_step_interval_run;?>" name="f_step_interval_run" />
            <div class="adm-info-message-wrap">
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_GENERAL_SLP_INTERVAL_NOTE")?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?echo GetMessage("MIBIX_YAM_GENERAL_SLP_EXPORT_LIMIT")?>:
        </td>
        <td width="60%">
            <input type="text" size="6" maxlength="10" value="<?=$str_step_limit;?>" name="f_step_limit">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?echo GetMessage("MIBIX_YAM_GENERAL_SLP_EXPORT_PATH")?>:
        </td>
        <td width="60%">
            <input type="text" size="44" maxlength="255" value="<?=$str_step_path;?>" name="f_step_path">
        </td>
    </tr>
<?
$tabControl->Buttons();

echo '<input type="submit" name="save" value="'.GetMessage("MIBIX_YAM_GENERAL_SUBMIT_SAVE").'" class="adm-btn-save" />
          <input type="reset" name="reset" value="'.GetMessage("MIBIX_YAM_GENERAL_SUBMIT_CANCEL").'" />
          <input type="hidden" name="update" value="Y" />
          <input type="hidden" name="lang" value="'.LANG.'">';
if($ID>0) echo '<input type="hidden" name="ID" value="'.$ID.'">';
echo '</form>';

bitrix_sessid_post();
$tabControl->End();

//    $opt = new CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $showRightsTab);
//    $opt->ShowHTML();
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>