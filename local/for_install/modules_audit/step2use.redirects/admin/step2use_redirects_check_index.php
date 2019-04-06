<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

$moduleID = 'step2use.redirects';

if(!CModule::IncludeModule($moduleID)) die('no module '.$moduleID);

// lang
IncludeModuleLangFile(__FILE__);

// check access
if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// is admin
$isAdmin = $USER->CanDoOperation('edit_php');

if(isset($_REQUEST['Reindex']) && $_REQUEST['Reindex']=='Y') {
    
    
    $site = CSite::GetByID($_REQUEST['NS']['site_id']);
    $site = $site->Fetch();
    if(!isset($site['SERVER_NAME']) || !$site['SERVER_NAME']) {
        $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('S2U_REINDEX_NO_SITE'),
            'DETAILS' => GetMessage('S2U_REINDEX_NO_SITE_DESC', array('#SITE_ID#'=>$site['LID'])),
        	'TYPE' => 'ERROR',
        	'HTML' => true
        ));
        echo $message->Show();
        exit;
    }
    
    $query = 'site:'.$site['SERVER_NAME'];
    
    set_time_limit(0);

    $page = (isset($_REQUEST['NS']['page']))? (int) $_REQUEST['NS']['page']: 0;
    $urlCnt = (isset($_REQUEST['NS']['cnt']))? (int) $_REQUEST['NS']['cnt']: 0;
    $sleepMin = (isset($_REQUEST['NS']['sleep_min']))? (int) $_REQUEST['NS']['sleep_min']: 5;
    $sleepMax = (isset($_REQUEST['NS']['sleep_max']))? (int) $_REQUEST['NS']['sleep_max']: 15;
    
    $endOfResults = false;
    while(true) {
        $result = file_get_contents('http://yandex.ru/yandsearch?text='.$query.'&p='.$page);
        $document = phpQuery::newDocument($result);
        $docs = $document->find('a.b-serp-item__title-link');
        
        if(!count($docs)) {
            $endOfResults = true;
            break;
        }
        
        foreach ($docs as $doc) {
            $pq = pq($doc); // Это аналог $ в jQuery
            $url = $pq->attr('href');
            file_get_contents($url); // context???
            $urlCnt++;
        }
        sleep(mt_rand($sleepMin, $sleepMax));
        $page++;
        COption::SetOptionInt($moduleID, 'INDEX_TANDEX_LAST_PAGE_N', $page);
        break;
    }
    
    $message = new CAdminMessage(array(
            'MESSAGE' => ($endOfResults)? GetMessage('S2U_REINDEX_RESULT_TITLE_END'): GetMessage('S2U_REINDEX_RESULT_TITLE'),
        	'TYPE' => 'OK',
        	'DETAILS' => GetMessage('S2U_REINDEX_RESULT', array('#CNT#'=>$urlCnt, '#LAST#'=>$url, '#PAGE#'=>$page)),
        	'HTML' => true
        ));
    echo $message->Show();
    
    if(!$endOfResults) echo '<div id="continue_href">&nbsp;</div>';
?>
<script>
    CloseWaitWindow();
	DoNext({'NS':{'cnt':<? echo $urlCnt ?>, 'page':<? echo $page; ?>}});
</script>
        <?
    
    exit;
}

//--------PREPARE THE FORM DATA.
// browser's title
$APPLICATION->SetTitle((isset($OLD_LINK) && StrLen($OLD_LINK) > 0) ? GetMessage("MURL_EDIT") : GetMessage("MURL_ADD"));

// indlude admin core
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage('S2U_CHECK_INDEX'), "ICON" => ""),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">

<? $tabControl->Begin(); ?>
<style>
    .field-name {
        width: 200px;
    }
</style>

<?
// основные настройки
$pageN = COption::GetOptionInt($moduleID, 'INDEX_TANDEX_LAST_PAGE_N', 0);
// sites list
$ref = $ref_id = array();
$rs = CSite::GetList(($v1="sort"), ($v2="asc"));
while ($ar = $rs->Fetch()) {
	$ref[] = "[".$ar["ID"]."] ".$ar["NAME"]." ({$ar["SERVER_NAME"]})";
	$ref_id[] = $ar["ID"];
}
$arSiteDropdown = array("reference" => $ref, "reference_id" => $ref_id);

$tabControl->BeginNextTab();
?>
<tr><td colspan="2" id="reindex_result_div"></td></tr>
<tr>
    <td class="field-name"><? echo GetMessage('S2U_SITE'); ?></td>
    <td>
        <?echo SelectBoxFromArray("s2u_site_id", $arSiteDropdown);?>
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('S2U_PAGE_N'); ?></td>
    <td>
        <input name="page" id="s2u_page" value="<? echo $pageN; ?>">
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('S2U_SLEEP'); ?></td>
    <td>
        <? echo GetMessage('S2U_SLEEP_MIN'); ?>&nbsp;&nbsp;<input name="sleep_min" id="s2u_sleep_min" value="5"><br/>
        <? echo GetMessage('S2U_SLEEP_MAX'); ?>&nbsp;<input name="sleep_max" id="s2u_sleep_max" value="15">
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('S2U_SE'); ?></td>
    <td>
        <select name="searchengine" id="searchengine">
            <option value="yandex"><? echo GetMessage('S2U_YANDEX'); ?></option>
        </select>
    </td>
</tr>

<? $tabControl->Buttons();?>
<!--<input type="submit" <?if ($MODULE_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">-->
    <input type="hidden" id="site_id" value="s1">
    <input type="button" id="start_button" value="<? echo GetMessage('S2U_REINDEX'); ?>" onclick="StartReindex();">
	<input type="button" id="stop_button" value="<? echo GetMessage('S2U_REINDEX_STOP'); ?>" onclick="StopReindex();" disabled="">
	<input type="button" id="continue_button" value="<? echo GetMessage('S2U_REINDEX_CONTINUE'); ?>" onclick="ContinueReindex();" disabled="">
<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

<? echo BeginNote(); ?>
<? echo GetMessage("S2U_DESCR") ?>
<? echo EndNote(); ?>

<script language="JavaScript">
var savedNS;
var stop;
var interval = 0;
function StartReindex()
{
	stop=false;
	document.getElementById('reindex_result_div').innerHTML='';
	document.getElementById('stop_button').disabled=false;
	document.getElementById('start_button').disabled=true;
	document.getElementById('continue_button').disabled=true;
	DoNext();
}
function DoNext(NS)
{
	var queryString = 'Reindex=Y'
		+ '&lang=ru';
    queryString += '&NS[sleep_min]=' + document.getElementById('s2u_sleep_min').value;
    queryString += '&NS[sleep_max]=' + document.getElementById('s2u_sleep_max').value;
    queryString += '&NS[site_id]=' + document.getElementById('s2u_site_id').value;

	if(!NS)
	{
		//interval = document.getElementById('max_execution_time').value;
        interval = 0;
		queryString += '&sessid=<?=bitrix_sessid();?>'
        queryString += '&NS[page]=' + document.getElementById('s2u_page').value;
        
        site_id = document.getElementById('site_id').value;
        if(site_id != 'NOT_REF')
            queryString += '&site_id=' + site_id;
	}

	savedNS = NS;

	if(!stop)
	{
		ShowWaitWindow();
		BX.ajax.post(
			'step2use_redirects_check_index.php?'+queryString,
			NS,
			function(result){
				document.getElementById('reindex_result_div').innerHTML = result;
				var href = document.getElementById('continue_href');
				if(!href)
				{
					CloseWaitWindow();
					StopReindex();
				}
			}
		);
	}

	return false;
}
function StopReindex()
{
	stop=true;
	document.getElementById('stop_button').disabled=true;
	document.getElementById('start_button').disabled=false;
	document.getElementById('continue_button').disabled=false;
}
function ContinueReindex()
{
	stop=false;
	document.getElementById('stop_button').disabled=false;
	document.getElementById('start_button').disabled=true;
	document.getElementById('continue_button').disabled=true;
	DoNext(savedNS);
}
function EndReindex()
{
	stop=true;
	document.getElementById('stop_button').disabled=true;
	document.getElementById('start_button').disabled=false;
	document.getElementById('continue_button').disabled=true;
}
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>