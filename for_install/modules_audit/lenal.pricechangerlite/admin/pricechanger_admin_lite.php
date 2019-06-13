<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
CJSCore::Init(array("popup", "ajax", "util", "window"));
$APPLICATION->SetTitle(GetMessage("MODULE_ADMIN_TITLE_DESC"));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

AddEventHandler('main', 'OnEventLogGetAuditTypes', 'ASD_OnEventLogGetAuditTypes');

function ASD_OnEventLogGetAuditTypes() {
    return array('LL_PRICE_UPDATE' => GetMessage("LL_LOG_TYPE"));
}

COption::SetOptionString("lenal.pricechanger_lite", "persent", 0);
$persent = COption::GetOptionString("lenal.pricechanger", "persent");

$cur_iblock_id = COption::GetOptionString("catalog", "iblock_id");
CModule::IncludeModule('iblock');
$iblock_res = CIBlock::GetList(
                Array(), Array(
            'ACTIVE' => 'Y',
                ), true
);
$POST_RIGHT = 'W';

$arError = array();
$bShowRes = false;

if ($REQUEST_METHOD == "POST" && !empty($Import) && $POST_RIGHT >= "W" && check_bitrix_sessid()) {
    CModule::IncludeModule("iblock");
    $iblock = intval($_REQUEST["iblock_id"]);
    $PROP_CODE = $_REQUEST["iblock_opt_prop"];
    $PROP_CODE_VAL = $_REQUEST["iblock_opt_prop_val"];

    $arFilter_upd = Array(
        "PROPERTY_$PROP_CODE" => $PROP_CODE_VAL,
    );
    if ($_REQUEST["iblock_subdir"] > 0)
        $arFilter_upd["SECTION_ID"] = $_REQUEST["iblock_subdir"];

    if ($_REQUEST["subdir"] == 'Y')
        $arFilter_upd["INCLUDE_SUBSECTIONS"] = $_REQUEST["subdir"];
    else
        $arFilter_upd["INCLUDE_SUBSECTIONS"] = 'N';

    $el = CIBlockElement::GetList(array(), $arFilter_upd, false, false, array("ID"));
    $count_el = $el->SelectedRowsCount();

    $i = 1;
    $nError = 0;
    $nSuccess = 0;
    $torgovie = 0;

    $ResultBlock = '<tr><td><b>#</b></td><td><b>' . GetMessage("LL_NAME_ITEM") . '</b></td><td><b>ID</b></td><td><b>' . GetMessage("LL_PRICE_ITEM") . '</b></td></tr>';
    $percent = $_REQUEST["persent_val"];
    $price_prop = 'PROPERTY_' . $_REQUEST['iblock_price_opt'];
    $el = CIBlockElement::GetList(array(), $arFilter_upd, false, false, array("ID","NAME", $price_prop));
    while ($ob = $el->Fetch()):
        //$get_price = GetCatalogProductPrice($ob["ID"], 1);
        $priceIpercent = '';
        $price = $price_prop.'_VALUE';
        $name = $ob['NAME'];
        //print_r($ob[$price]);
        if ($ob[$price]>0){
        if ($_REQUEST["top-down-price"] == 'plus')
            $priceIpercent = $ob[$price] + $ob[$price] * $percent / 100;
        else
            $priceIpercent = $ob[$price] - $ob[$price] * $percent / 100;
        
        CIBlockElement::SetPropertyValuesEx($ob["ID"], false, array($price_prop => $priceIpercent));
            
            $ResultBlock .= '<tr><td>' . $i++ . '. </td><td>' . $name . '</td><td>[' . $ob["ID"] . '] </td><td><b>' . $ob[$price]  . '</b> => <b>' .$priceIpercent . '</b></td></tr>';
            $nSuccess++;
            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "LL_PRICE_UPDATE",
                "MODULE_ID" => "lenal.pricechanger",
                "ITEM_ID" => $ob["ID"],
                "DESCRIPTION" => $ob[$price] . ' => ' . $priceIpercent . ' ' . $_REQUEST["comment"],
            ));
        } else {
            $ResultBlock .= '<tr style="background:red; color:#fff"><td>' . $i++ . '. </td><td>' . $name . '</td><td>[' . $ob["ID"] . '] </td><td><b>' . $ob[$price] . '</b> => <b>' . $priceIpercent . '</b></td></tr>';
            $nError++;
        }
        /*if ($_REQUEST["iblock_SKU"] > 0):
/////////////////////////////////////////////
///                                        //
///          торговые                      //
///                                        //
////////////////////////////////////////////
            $IBLOCK_ID = $iblock;
            $ID = $ob["ID"];
            $arInfo = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
            if (is_array($arInfo)) {
                $rsOffers = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $ID));
                while ($arOffer = $rsOffers->GetNext()) {
                    $get_price = GetCatalogProductPrice($arOffer["ID"], 1);
                    $price = $get_price["PRICE"];

                    if ($_REQUEST["top-down-price"] == 'plus')
                        $priceIpercent = $price + $price * $percent / 100;
                    else
                        $priceIpercent = $price - $price * $percent / 100;
                    $CURRENCY = $get_price["CURRENCY"];

                    $arFields = Array("PRODUCT_ID" => $arOffer["ID"], "CATALOG_GROUP_ID" => 1, "PRICE" => $priceIpercent, "CURRENCY" => $CURRENCY);
                    $res = CPrice::GetList(array(), array("PRODUCT_ID" => $arOffer["ID"], "CATALOG_GROUP_ID" => 1));

                    if ($arr = $res->Fetch()) {
                        CPrice::Update($arr["ID"], $arFields);
                    } else {
                        CPrice::Add($arFields);
                    }

                    $torgovie++;
                }
            }
        endif;*/
    endwhile;
    $bShowRes = true;
}
if ($POST_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if ($bShowRes):
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MODULE_PARAMETERS_TITLE"), "ICON" => "main_user_edit", "TITLE" => GetMessage("MODULE_PARAMETERS_DESC")),
        array("DIV" => "edit2", "TAB" => GetMessage("MODULE_PARAMETERS_TITLE2"), "ICON" => "main_user_edit", "TITLE" => GetMessage("MODULE_PARAMETERS_DESC2")),
    );
else:
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MODULE_PARAMETERS_TITLE"), "ICON" => "main_user_edit", "TITLE" => GetMessage("MODULE_PARAMETERS_DESC")),
    );
endif;
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);

if (count($arError) > 0) {
    $e = new CAdminException($arError);
    $message = new CAdminMessage(GetMessage("LL_error"), $e);
    echo $message->Show();
}
?>
<? if ($bShowRes): ?>
    <?
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => GetMessage("imp_results"),
        "DETAILS" => GetMessage("imp_results_total") . ' <b>' . $count_el . '</b><br>'
        . GetMessage("imp_results_added") . ' <b>' . $nSuccess . '</b><br>'
        . GetMessage("imp_results_err") . ' <b>' . $nError . '</b>',
        "HTML" => true,
        "TYPE" => "PROGRESS",
    ));
    ?>
<? endif; ?>


<form enctype="multipart/form-data" action="<?= $APPLICATION->GetCurPage(); ?>" method="POST" name="pricechanger_form">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%"><label><?= GetMessage("IBLOCK_DESC") ?></label>:</td>											
        <td width="60%">
            <select required="" class="iblock_text" name="iblock_id" onchange="select_iblock_prop($(this).val())">
                <option disabled="" selected=""><?= GetMessage("LL_SELECT_IBLOCK") ?></option>
                <? while ($iblock_ar_res = $iblock_res->Fetch()): ?>
                    <option value="<?= $iblock_ar_res['ID'] ?>" <?= ($iblock_ar_res['ID'] == $cur_iblock_id) ? 'selected' : '' ?> ><?= $iblock_ar_res['NAME'] ?></option>
                <? endwhile; ?>
            </select>
        </td>
    </tr>							
    <tr>
        <td width="40%"><label><?= GetMessage("LL_SUBDIR") ?></label>:</td>											
        <td width="60%">
            <select name="iblock_subdir" class="iblock_text" disabled onchange="load_item()">
                <option selected=""><?= GetMessage("LL_ALL_SUBDIR") ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("LL_SUBDIR_CHILD") ?></label>:</td>											
        <td width="60%">
            <label><input type="checkbox" checked="" name="subdir" value="Y" onchange="load_item()"/> <?= GetMessage("LL_YES") ?></label>
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("IBLOCK_OPTION_DESC") ?></label>:</td>											
        <td width="60%">
            <select class="iblock_text" name="iblock_opt_prop" disabled onchange="select_iblock_prop_val($(this).val())">
                <option disabled="" selected=""><?= GetMessage("LL_SELECT_IBLOCK") ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("IBLOCK_OPTION_VAL_DESC") ?></label>:</td>											
        <td width="60%">
            <select class="iblock_text" name="iblock_opt_prop_val" disabled onchange="load_item()">
                <option disabled="" selected=""><?= GetMessage("LL_SELECT_IBLOCK") ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("LL_PRICE") ?></label>:</td>											
        <td width="60%">
            <select name="iblock_price_opt" class="iblock_text" disabled="" required="">
                <option selected=""><?= GetMessage("LL_SELECT_IBLOCK") ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("LL_PRICE_UD") ?></label>:</td>											
        <td width="60%">
            <select name="top-down-price">
                <option value="plus"><?= GetMessage("LL_PRICE_U") ?></option>
                <option value="minus"><?= GetMessage("LL_PRICE_D") ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("PERSENT_DESC") ?></label>:</td>											
        <td width="60%">
            <input class="persent_val" name="persent_val" value="<?= $persent ?>">
        </td>
    </tr>
    <? /* <tr>
      <td width="40%"><label>Округлить до</label>:</td>
      <td width="60%">
      <input class="ceil" name="ceil" value="5">
      </td>
      </tr> */ ?>
    <tr id="load_item">
        <td width="40%"><label><?= GetMessage("LL_FIND") ?></label></td>											
        <td width="60%" id="load_item_count">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td width="40%"><label><?= GetMessage("LL_COMMENT") ?></label>:</td>											
        <td width="60%">
            <textarea name="comment"></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="2" width="100%">
            <div class="load_item_upd"></div>
        </td>
    </tr>
    <? if ($bShowRes): ?>
        <? $tabControl->BeginNextTab(); ?>
        <?= $ResultBlock ?>
    <? endif; ?>
    <?
    $tabControl->Buttons();
    ?>
    <input<? if ($POST_RIGHT < "W") echo " disabled"; ?> class="button" type="submit" name="Import" value="<?= GetMessage("BUTTON_VAL_DESC") ?>">
    <input type="hidden" name="lang" value="<?= LANG ?>">
    <input name="action" type="hidden" value="load_item" />
    <? if ($ID > 0 && !$bCopy): ?>
        <input type="hidden" name="ID" value="<?= $ID ?>">
    <? endif; ?>
    <?= bitrix_sessid_post(); ?>
    <?
    $tabControl->End();
    ?>
</form>
<?
$tabControl->ShowWarnings("impform", $message);
?>

<div id="loading" style="display: none;">
    <span><?= GetMessage("LL_LOADING") ?></span>
</div>
<style>
    .edit-form{
        position:relative
    }
    .edit-form td{
        padding: 5px 10px
    }
    td.head 
    { 
        font-weight: bold;
    }
    .parametr_disc
    {
        width:50%;		
    }
    .parametr_act
    {
        width:10%;
        text-align: center;
    }
    .parametr_value
    {
        width:40%;
    }
    .parametr_value select
    {
        width: 100%
    }
    .iblock_text{
        width: 250px
    }
    #load_item {
        font-size: 25px;
    }
    #loading{
        background: rgba(0, 0, 0, 0.1);
        display: block;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }
    #loading > span {
        left: 50%;
        margin-left: -30px;
        position: absolute;
        top: 40%;
    }
    #edit2 tr:hover{
        background: #fff
    }
    .persent_val, .ceil{
        text-align: center;
        width: 35px;
    }
</style>
<? //echo ceil(16 / 5) * 5; ?>
<? //echo floor(16 / 5) * 5;  ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script>
                function select_iblock_prop($id) {
                    var f = {id: $id, action: 'GetIblockProp'};
                    $.ajax({
                        url: '/bitrix/js/lenal.pricechangerlite/lenal_pricechanger_lite.php',
                        type: 'post',
                        data: f,
                        success: function(result) {

                            //alert(result);
                            //$('#modal_to_top .close').click();
                            $('select[name="iblock_opt_prop"]').attr('disabled', false);
                            $('select[name="iblock_opt_prop"]').html('<option disabled="" selected=""><?= GetMessage("LL_SELECT_OPTION") ?></option>' + result);
                        }
                    });
                    var f3 = {id: $id, action: 'GetSubdir'};
                    $.ajax({
                        url: '/bitrix/js/lenal.pricechangerlite/lenal_pricechanger_lite.php',
                        type: 'post',
                        data: f3,
                        success: function(result) {

                            //alert(result);
                            $('select[name="iblock_subdir"]').attr('disabled', false);
                            $('select[name="iblock_subdir"]').html('<option selected="" value="0"><?= GetMessage("LL_ALL_SUBDIR") ?></option>' + result);
                            //$('#modal_to_top .close').click();
                            //$('select[name="iblock_opt_prop"]').attr('disabled', false);
                            //$('select[name="iblock_opt_prop"]').html('<option disabled="" selected=""><?= GetMessage("LL_SELECT_OPTION") ?></option>' + result);
                        }
                    });
                    var f2 = {id: $id, action: 'GetPriceProp'};
                    $.ajax({
                        url: '/bitrix/js/lenal.pricechangerlite/lenal_pricechanger_lite.php',
                        type: 'post',
                        data: f2,
                        success: function(result) {

                            $('select[name="iblock_price_opt"]').attr('disabled', false);
                            $('select[name="iblock_price_opt"]').html('<option disabled="" selected=""><?= GetMessage("LL_SELECT_OPTION") ?></option>' + result);

                        }
                    });
                    return false;
                }
                function select_iblock_prop_val($code) {

                    $("select[name='iblock_opt_prop'] option:selected").each(function() {
                        str = $(this).data('iblock');
                    });
                    var f = {code: $code, action: 'GetIblockPropVal', iblock: str};
                    $.ajax({
                        url: '/bitrix/js/lenal.pricechangerlite/lenal_pricechanger_lite.php',
                        type: 'post',
                        data: f,
                        success: function(result) {
                            //alert(result);
                            //$('#modal_to_top .close').click();
                            $('select[name="iblock_opt_prop_val"]').attr('disabled', false);
                            $('select[name="iblock_opt_prop_val"]').html('<option disabled="" selected=""><?= GetMessage("LL_SELECT_OPTION") ?></option>' + result);

                        }
                    });
                    return false;
                }
                function load_item() {
                    var f = $('form[name="pricechanger_form"]').serialize();
                    $.ajax({
                        url: '/bitrix/js/lenal.pricechangerlite/lenal_pricechanger_lite.php',
                        type: 'post',
                        data: f,
                        success: function(result) {
                            $('#load_item_count').html(result);
                            $('.buttons').show();
                        }
                    });
                    return false;
                }
                $(function() {
                    $('#loading').ajaxSend(function() {
                        ShowWaitWindow();
                    })
                    $('#loading').ajaxStart(function() {
                        ShowWaitWindow();
                    })
                    $('#loading').ajaxComplete(function() {
                        CloseWaitWindow();
                    })
                    $('#loading').ajaxSuccess(function() {
                        CloseWaitWindow();
                    })
                })
</script>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>