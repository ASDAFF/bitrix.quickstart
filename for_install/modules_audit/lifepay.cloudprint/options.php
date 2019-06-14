<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/interface/admin_lib.php");

if (!$USER->IsAdmin())
    return;

CJSCore::Init(array('jquery'));

$module_id = 'lifepay.cloudprint';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loader::includeModule($module_id);
Loader::includeModule('sale');

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);



//пл. системы
$resPS = CSalePaySystem::GetList($arOrder = array("SORT"=>"ASC", "PSA_NAME"=>"ASC"), array("LID"=>SITE_ID, "ACTIVE"=>"Y"));
while ($arPS = $resPS->Fetch())
{
    $arPaySystem[$arPS['ID']] = $arPS['NAME']." [".$arPS['ID']."]";
}



$arrTabs = array(
    array("DIV" => "ore1", "TAB" =>GetMessage("LIFEPAY_CLOUDPRINT_OBSIE_NASTROYKI"),  "TITLE" => GetMessage("LIFEPAY_CLOUDPRINT_NASTROYKI")),
);

$arrNewOptions = [
    [
        'key' => 'test_mode',
        'name' => GetMessage("LIFEPAY_OPTION_TEST_MODE"),
        'type' => 'select',
        'description' => '',
        'values' => [
            '0' => GetMessage("LIFEPAY_CLOUDPRINT_VYKLUCEN"),
            '1' => GetMessage("LIFEPAY_CLOUDPRINT_VKLUCEN")
        ]
    ],
    [
        'key' => 'api_key',
        'name' => 'Api '.GetMessage("LIFEPAY_CLOUDPRINT_KLUC"),
        'type' => 'text',
        'description' => GetMessage("LIFEPAY_CLOUDPRINT_DOSTUPEN_V_LICNOM_KA"),
    ],
    [
        'key' => 'api_login',
        'name' => GetMessage("LIFEPAY_OPTION_API_LOGIN"),
        'type' => 'text',
        'description' => GetMessage("LIFEPAY_CLOUDPRINT_NOMER_TELEFONA_NA_K"),
    ],
    [
        'key' => 'printer_number',
        'name' => GetMessage("LIFEPAY_CLOUDPRINT_PRINTERA"),
        'type' => 'text',
        'description' => GetMessage("LIFEPAY_OPTION_PRINTER_NUMBER"),
    ],
    [
        'key' => 'printer_mode',
        'name' => GetMessage("LIFEPAY_OPTION_PRINTER_MODE"),
        'type' => 'select',
        'description' => GetMessage("LIFEPAY_CLOUDPRINT_VYBERITE_REJIM_RABOT"),
        'values' => [
            'email' => GetMessage("LIFEPAY_CLOUDPRINT_OTPRAVKA_NA_TE"),
            // 'print_email' => GetMessage("LIFEPAY_CLOUDPRINT_PECATQ_I_OTPRAVKA_NA")
        ]
    ],
    [
        'key' => 'pay_systems',
        'name' => GetMessage("LIFEPAY_OPTION_PAY_SYSTEMS"),
        'type' => 'hidden',
        'description' => '',
    ]
];


foreach ($arrNewOptions as $key => $item) {
    if(isset($_REQUEST[$item['key']])) {
        if($item['key'] == 'pay_systems') {
            $_REQUEST[$item['key']] = serialize($_REQUEST[$item['key']]);
        }
        Option::set($module_id, $item['key'], $_REQUEST[$item['key']]);
    }
}

$tabControl = new CAdminTabControl("tabControl",$arrTabs);
$tabControl->Begin();

?>

<form method="POST" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>" name="setting_form">
    <? $tabControl->BeginNextTab(); ?>
    <table border="0" cellspacing="0" cellpadding="0" class="adm-detail-content-table edit-table">
        <tbody>

            <?foreach ($arrNewOptions as $arOption) { ?>
                <?
                    $val = Option::get($module_id, $arOption['key'], $arOption['type']);
                ?>

                <? if($arOption['type'] == 'text') {  ?>
                    <tr>
                        <td width="30%" class="adm-detail-content-cell-l" valign="top">
                            <label style="margin-top:6px; display:block;">
                                <?= $arOption['name']; ?>
                            </label>
                        </td>
                        <td width="70%" class="adm-detail-content-cell-r" valign="top">
                            <input size="40" maxlength="50" value="<? echo $val ?>" name="<?=$arOption['key']?>" type="text">
                        </td>
                    </tr>
                <? } ?>

                <? if($arOption['type'] == 'select') {  ?>
                    <tr>
                        <td width="30%" class="adm-detail-content-cell-l" valign="top">
                            <label style="margin-top:6px; display:block;">
                                <?= $arOption['name']; ?>
                            </label>
                        </td>
                        <td width="70%" class="adm-detail-content-cell-r" valign="top">
                            <select name="<?=$arOption['key']?>"  size="1">
                                <? foreach($arOption['values'] as $key => $itemValue) { ?>
                                    <option value="<?=$key;?>" <?=($key == $val ? 'selected' : '')?> ><?=$itemValue?></option>
                                <? } ?>
                            </select>
                        </td>
                    </tr>
                <? } ?>


            <? } ?>

            <tr>
                <td width="30%" class="adm-detail-content-cell-l" valign="top">
                    <label style="margin-top:6px; display:block;"><?=GetMessage("LIFEPAY_CLOUDPRINT_PLATEJNAA_SISTEMA")?></label>
                </td>
                <td class="adm-detail-content-cell-r">
                    <?
                        $savePaySystem = Option::get($module_id, 'pay_systems', '');                      
                        if(strlen($savePaySystem) > 0)
                            $arSavePaySystem = unserialize($savePaySystem);
                        else
                            $arSavePaySystem = array();
                    ?>

                    <select name="pay_systems[]" multiple size="5">
                        <? foreach($arPaySystem as $id => $val) { ?>
                            <option value="<?=$id?>" <?=(in_array($id, $arSavePaySystem) ? 'selected' : '')?>> <?=$val?> </option>
                        <? } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" valign="top"></td>
                <td width="60%" valign="top">
                    <input type="submit" name="Apply" value="<?=GetMessage("LIFEPAY_CLOUDPRINT_SOHRANITQ")?>" title="12">
                </td>
            </tr>
            

        </tbody>
    </table>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
    
</form>
