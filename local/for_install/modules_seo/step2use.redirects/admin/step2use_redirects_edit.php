<?
$sModuleId = 'step2use.redirects';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

CJSCore::Init(array("jquery")); 

global $DBType;
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/$sModuleId/config.php");
CModule::IncludeModule($sModuleId);

// @todo HELP_FILE
//define("HELP_FILE", "settings/s2u_redirect_edit.php");

// lang
IncludeModuleLangFile(__FILE__);

// check access
/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));*/

// is admin
$isAdmin = S2uRedirects::canAdminThisModule() || $USER->CanDoOperation('edit_php');

if(!$isAdmin) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

// form message
$message = null;

// get vars from form?
$bVarsFromForm = false;

// no adding without SITE_ID or rule ID
if (strlen($ADD) > 0 && StrLen($site_id) <= 0 || (strlen($Update) > 0 && !$_REQUEST['ID']))
	LocalRedirect("/bitrix/admin/step2use_redirects_list.php?lang=" . LANG);

// validation
$bOLD_LINK = false;
$bNEW_LINK = false;
if(isset($OLD_LINK)) {
    $OLD_LINK = trim($OLD_LINK);
    $bOLD_LINK = true;
}
if(isset($NEW_LINK)) {
    $NEW_LINK = trim($NEW_LINK);
    $bNEW_LINK = true;
}
//if($OLD_LINK && substr($OLD_LINK, 0, 1)!='/') $OLD_LINK = '/'.$OLD_LINK;
//if($NEW_LINK && substr($NEW_LINK, 0, 1)!='/') $NEW_LINK = '/'.$NEW_LINK;

if (strlen($ADD) > 0) {
    $sameUrl = S2uRedirectsRulesDB::GetList(array(
        'OLD_LINK'=>$OLD_LINK, 
        'SITE_ID'=>htmlspecialcharsbx($site_id)
    ));
}

if($REQUEST_METHOD == "POST") {
    // no urls
    if(!$OLD_LINK || (!$NEW_LINK && $STATUS!=410)) {
        $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ERROR_NO_URL'),
    		'TYPE' => 'ERROR',
    		'DETAILS' => '',
    		'HTML' => true
        ));
    }
    /*elseif((($_REQUEST['USE_REGEXP'])? Y: N)==N && (($OLD_LINK && substr($OLD_LINK, 0, 1)!='/') || ($NEW_LINK && substr($NEW_LINK, 0, 1)!='/'))) {
        $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ERROR_NOSLASH_URL'),
        	'TYPE' => 'ERROR',
    		'DETAILS' => '',
            'HTML' => true
        ));
    }*/
    // duplicate OLD_LINK
    elseif(strlen($ADD) > 0 && count($sameUrl)>0) {    
        $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('S2U_ERROR_DUPLICATE_OLD_LINK'),
        	'TYPE' => 'ERROR',
        	'DETAILS' => '',
        	'HTML' => true
        ));
    }
    // duplicate url
    elseif($OLD_LINK==$NEW_LINK) {
        $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ERROR_DUPLICATE_URL'),
    		'TYPE' => 'ERROR',
    		'DETAILS' => '',
    		'HTML' => true
        ));
    }
    // valid url
    elseif(COption::GetOptionString($sModuleId, 'VALIDATE_URL_BY_RFC2396', 'Y')=='Y' && (filter_var('http://example.com'.$NEW_LINK, FILTER_VALIDATE_URL)===false || filter_var('http://example.com'.$OLD_LINK, FILTER_VALIDATE_URL)===false)) {
        if((($_REQUEST['USE_REGEXP'])? Y: N)==N) {
            $message = new CAdminMessage(array(
                'MESSAGE' => GetMessage('ERROR_INVALID_URL'),
        		'TYPE' => 'ERROR',
        		'DETAILS' => GetMessage('ERROR_INVALID_URL_DESC'),
        		'HTML' => true
            ));
        }
    }
    elseif($OLD_LINK && $NEW_LINK && $STATUS==410) {
        $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ERROR_410'),
    		'TYPE' => 'ERROR',
    		'DETAILS' => '',
    		'HTML' => true
        ));
    }
}

// check demo
if(strlen($ADD) > 0 && (CModule::IncludeModuleEx($sModuleId)==MODULE_DEMO or CModule::IncludeModuleEx($sModuleId)==MODULE_DEMO_EXPIRED) && S2uRedirectsRulesDB::GetCountRules() >= 5) {    
    $message = new CAdminMessage(array(
        'MESSAGE' => GetMessage('ERROR_DEMO'),
    	'TYPE' => 'ERROR',
    	'DETAILS' => '',
    	'HTML' => true
    ));
}

// save for ADD
if ($REQUEST_METHOD == "POST" && strlen($ADD) > 0 && $isAdmin && check_bitrix_sessid()) {    
    
	if (!$message) {
		$actv = ($ACTIVE != '') ? 'Y' : 'N';
        $WI = ($WITH_INCLUDES != '') ? 'Y': 'N';
        $UR = ($USE_REGEXP != '') ? 'Y': 'N';
		$Res__ = S2uRedirectsRulesDB::Add(array(
								'OLD_LINK' => trim($OLD_LINK),
								'NEW_LINK' => trim($NEW_LINK),
								'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
								'STATUS' => $STATUS,
								'ACTIVE' => $actv,
								'COMMENT' => $COMMENT,
                                'SITE_ID' => htmlspecialcharsbx($site_id),
                                'WITH_INCLUDES' => htmlspecialcharsbx($WI),
                                'USE_REGEXP' => htmlspecialcharsbx($UR),
							));
		if ($Res__ && isset($save)) {
			LocalRedirect("/bitrix/admin/step2use_redirects_list.php?lang=" . LANG);
		}
	}
    else {
        $bVarsFromForm = true;
    }
}

// save for UPDATE
if ($REQUEST_METHOD == "POST" && strlen($Update) > 0 && $isAdmin && check_bitrix_sessid()) {
	if (!$message) {
		$actv = ($ACTIVE != '') ? 'Y': 'N';
        $WI = ($WITH_INCLUDES != '') ? 'Y': 'N';
        $UR = ($USE_REGEXP != '') ? 'Y': 'N';
		$rr = S2uRedirectsRulesDB::Update($_REQUEST['ID'],
										array(
												"OLD_LINK" => trim($OLD_LINK),
												"NEW_LINK" => trim($NEW_LINK),
												"DATE_TIME_CREATE" => $DATE_TIME_CREATE,
												"STATUS" => $STATUS,
												"ACTIVE" => $actv,
												'COMMENT' => $COMMENT,
                                                'SITE_ID' => htmlspecialcharsbx($site_id),
                                                'WITH_INCLUDES' => htmlspecialcharsbx($WI),
                                                'USE_REGEXP' => htmlspecialcharsbx($UR),
										)
		);
	
        if($rr) {
            if (strlen($apply) <= 0) {
    			LocalRedirect("/bitrix/admin/step2use_redirects_list.php?lang=" . LANG . "&" . GetFilterParams("filter_", false));
    		};
    	} else {
    		$message = new CAdminMessage(array(
                'MESSAGE' => GetMessage('SAE_ERROR'),
        		'TYPE' => 'ERROR',
        		'DETAILS' => '',
        		'HTML' => true
            ));
    		$bVarsFromForm = true;
    	}
    }
    else {
        $bVarsFromForm = true;
    }
}


//--------PREPARE THE FORM DATA.
// browser's title
$APPLICATION->SetTitle((isset($OLD_LINK) && StrLen($OLD_LINK) > 0) ? GetMessage("MURL_EDIT") : GetMessage("MURL_ADD"));

// indlude admin core
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
echo S2uRedirects::getLicenseRenewalBanner();

// find redirect by ID
if(intval($ID) > 0){
	$arResultList = S2uRedirectsRulesDB::GetList(array("ID" => trim($ID), 'SITE_ID'=>htmlspecialcharsbx($site_id)/* ,'ADRESS'=>' ','DATE_TIME_CREATE'=>' ','ACTIVE'=>' ', 'COMMENT'=>' ' */));	
}
if (count($arResultList) <= 0) {
	$arResult = array();
	$str_OLD_LINK = (isset($OLD_LINK))? $OLD_LINK: "/";
	$str_NEW_LINK = (isset($NEW_LINK))? $NEW_LINK: "/";
	$str_ACTIVE = ($_REQUEST['ACTIVE'])? Y: N;
    if(!isset($_REQUEST['ACTIVE'])) $str_ACTIVE = Y;
    $str_WITH_INCLUDES = ($_REQUEST['WITH_INCLUDES'])? Y: N;
    $str_USE_REGEXP = ($_REQUEST['USE_REGEXP'])? Y: N;
	$str_DATE_CREATE = "";
	$str_STATUS = ($_REQUEST['STATUS'])? $_REQUEST['STATUS']: "";
	$str_COMMENT = ($_REQUEST['COMMENT'])? $_REQUEST['COMMENT']: "";
} else {
	$arResult = $arResultList[0];
	$str_OLD_LINK = ($bOLD_LINK)? $OLD_LINK: htmlspecialcharsbx($arResult["OLD_LINK"]);
	$str_NEW_LINK = ($bNEW_LINK)? $NEW_LINK: htmlspecialcharsbx($arResult["NEW_LINK"]);
	$str_ACTIVE = htmlspecialcharsbx($arResult["ACTIVE"]);
    $str_WITH_INCLUDES = htmlspecialcharsbx($arResult["WITH_INCLUDES"]);
    $str_USE_REGEXP = htmlspecialcharsbx($arResult["USE_REGEXP"]);
	$str_STATUS = htmlspecialcharsbx($arResult["STATUS"]);
	$str_COMMENT = htmlspecialcharsbx($arResult["COMMENT"]);
	$str_DATE_CREATE = htmlspecialcharsbx($arResult["DATE_CREATE"]);
    $site_id = ($arResult["SITE_ID"])? $arResult["SITE_ID"]: $_REQUEST['site_id'];
}
//var_dump($WITH_INCLUDES);
if ($bVarsFromForm) {
	$str_OLD_LINK = htmlspecialcharsbx($str_OLD_LINK);
	$str_NEW_LINK = htmlspecialcharsbx($str_NEW_LINK);
	$str_ACTIVE = ($ACTIVE)? Y: N;
    $str_WITH_INCLUDES = ($WITH_INCLUDES)? Y: N;
    $str_USE_REGEXP = ($USE_REGEXP)? Y: N;
	$str_COMMENT = htmlspecialcharsbx($COMMENT);
	$str_STATUS = htmlspecialcharsbx($STATUS);
	$str_DATE_CREATE = htmlspecialcharsbx($DATE_CREATE);
}
?>

<?
// prepare menu
$aMenu = array(array(
    "TEXT" => GetMessage("MURL_2_LIST"),
	"LINK" => "/bitrix/admin/step2use_redirects_list.php?lang=" . LANG . "&filter_site_id=" . UrlEncode($site_id) . "&" . GetFilterParams("filter_", false),
	"ICON" => "btn_list",
	"TITLE" => GetMessage("MURL_2_LIST_ALT"),
));
// prepare additional menu for UPDATE
if(StrLen($Update) > 0) {
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
			"TEXT" => GetMessage("MURL_ACT_DEL"),
			"LINK" => "javascript:if(confirm('" . GetMessage("MURL_ACT_DEL_CONF") . "')) window.location='/bitrix/admin/step2use_redirects_list.php?ID=" . urlencode(urlencode($GENERATE_LINK)) . "&filter_site_id=" . urlencode($site_id) . "&action=delete&lang=" . LANG . "&" . bitrix_sessid_get() . "';",
			"WARNING" => "Y",
			"ICON" => "btn_delete"
	);
}
// create & show menu
$context = new CAdminContextMenu($aMenu);
$context->Show();

// show messages (errors and ok's)
if($message) echo $message->Show();

// form HTML code
?>
<form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" name="form1">
	<? echo GetFilterHiddens("filter_"); ?>
	<? if ($_REQUEST['ADD'] != 'Y'): ?>
		<input type="hidden" name="Update" value="Y">
	<? else: ?>
		<input type="hidden" name="ADD" value="Y">
	<? endif; ?>
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<input type="hidden" name="site_id" value="<?= htmlspecialcharsbx($site_id) ?>">
	<input type="hidden" name="ID" value="<?= htmlspecialcharsbx($_REQUEST['ID']) ?>">

	<?= bitrix_sessid_post() ?>

	<?
    
    // prepare tabs
	$aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MURL_TAB"), "TITLE" => GetMessage("MURL_TAB_ALT"))
    );
    
    // create and show tabs
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();
	
    // 1st tab
	$tabControl->BeginNextTab();
	?>

	<tr>
		<td><span class="required">*</span><?= GetMessage('MURL_OLD_LINK') ?>:</td>
		<td>
            <input type="text" name="OLD_LINK" size="50" maxlength="250" value="<?= $str_OLD_LINK ?>">
            <label id="s2u-redirects-edit-with-includes">
                <input type="checkbox" name="WITH_INCLUDES" <? if ($str_WITH_INCLUDES == 'Y'): ?><?= 'checked' ?><? endif ?>>
                <?= GetMessage("S2U_WITH_INCLUDES") ?>
                <img src="/bitrix/js/main/core/images/hint.gif" onmouseover="BX.hint(this, '<?= GetMessage("S2U_WITH_INCLUDES_TIP") ?>');">
            </label>
        </td>
	</tr>

    <tr id="s2u-redirects-edit-tr-new-link">
		<td></span><?= GetMessage('MURL_NEW_LINK') ?>:</td>
		<td><input type="text" name="NEW_LINK" id="s2u-redirects-edit-new-link" size="50" maxlength="250" value="<?= $str_NEW_LINK ?>"></td>
	</tr>

	<tr>
		<td><?= GetMessage("MURL_RULE") ?>:</td>
		<td><input type="checkbox" name="ACTIVE" size="50" maxlength="250" <? if ($str_ACTIVE == 'Y'): ?><?= 'checked' ?><? endif ?>></td>
	</tr>
    
	<tr>
		<td><?= GetMessage("MURL_STATUS") ?>:</td>
		<td>
			<select name="STATUS" id="s2u-redirects-edit-status">
				<option value="301" <? if ($str_STATUS == "301"): ?>selected<? endif ?>><?= GetMessage("STATUS_301") ?></option>
				<option value="302" <? if ($str_STATUS == "302"): ?>selected<? endif ?>><?= GetMessage("STATUS_302") ?></option>
                <option value="303" <? if ($str_STATUS == "303"): ?>selected<? endif ?>><?= GetMessage("STATUS_303") ?></option>
                <option value="410" <? if ($str_STATUS == "410"): ?>selected<? endif ?>><?= GetMessage("STATUS_410") ?></option>
			</select>
		</td>
	</tr>
    
	<tr>
		<td><?= GetMessage("MURL_COMMENT") ?>:</td>
		<td>
			<textarea cols="50" rows="10" name="COMMENT" ><? echo htmlspecialcharsbx($str_COMMENT); ?></textarea>
		</td>
	</tr>
    
    <tr>
		<td><?= GetMessage("S2U_USE_REGEXP") ?>:<br/><?= GetMessage("S2U_USE_REGEXP_DESC") ?></td>
		<td><input type="checkbox" name="USE_REGEXP" id="s2u-redirects-edit-use-regexp" <? if ($str_USE_REGEXP == 'Y'): ?><?= 'checked' ?><? endif ?>></td>
	</tr>

	<?
    // end 1st tab
	$tabControl->EndTab();
	
    // add control buttons
	$tabControl->Buttons(
        array(
            "disabled" => !$isAdmin,
			"back_url" => "/bitrix/admin/step2use_redirects_list.php?lang=" . LANG . "&" . GetFilterParams("filter_", false)
        )
    );
	
    // end of tabs
	$tabControl->End();
	?>
</form>
<?
$tabControl->ShowWarnings("form1", $message);
?>

<? echo BeginNote(); ?>
<span class="required">*</span> <? echo GetMessage("REQUIRED_FIELDS") ?>
<? echo EndNote(); ?>

<script>
BX.ready(function() {
    var status = BX('s2u-redirects-edit-status');
    BX.bind(status, 'change', BX.proxy(function(e) {
        //console.log(e.target.value);
        var newLink = BX('s2u-redirects-edit-tr-new-link');
        if(e.target.value=='410') {
            BX('s2u-redirects-edit-new-link').value = '';
            BX.hide(newLink);
        }
        else BX.show(newLink);
    }, this));
    //BX('s2u-redirects-edit-status')
    BX.fireEvent(status, 'change');
    
    var useRegexp = $('#s2u-redirects-edit-use-regexp'),
        withInclude = $('#s2u-redirects-edit-with-includes');
    useRegexp.change(function() {
        if(useRegexp.is(':checked')) {
            withInclude.hide();
            withInclude.find('input:first').attr('checked', false);
        }
        else {
            withInclude.show();
        }
    });
    useRegexp.change();
});
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
