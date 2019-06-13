<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/admin_lib.php");
global $MESS, $APPLICATION, $DB;

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("altasib.ping");

CUtil::JSPostUnescape();
$siteid = htmlspecialcharsEx($_REQUEST['siteid']);
$pageURL = htmlspecialcharsEx($_REQUEST['backurl']);
$pagename = htmlspecialcharsEx($_REQUEST['pagename']);

$popupWindow = new CJSPopup("", array("SUFFIX"=>($_GET['subdialog'] == 'Y'? 'subdialog':'')));

$popupWindow->ShowTitlebar(GetMessage("PING_TITLE_STAT"));
$popupWindow->StartContent();

//$detail_page = $_REQUEST['backurl'];

$siteURL = "http://".str_replace("http://", "", $ar["SERVER_NAME"]);

?>
<style>
.popwintext{
	padding-left:15px;
	line-height: 1.5 !important;
}
</style>
<?
$reping = false;
if($_REQUEST['ping']=='Y'){
	$rs = CSite::GetByID($siteid);
	if($ar = $rs->Fetch()){
	
	$urls = COption::GetOptionString("altasib.ping", "send_blog_ping_address");
	$arUrls = explode("\r\n", $urls);

	$result = CAltasibping::SendPing($pagename,$pageURL, $arUrls/* $ar["ID"] */);
	if(!empty($result)){
		$arURL = $result["URL"];
		unset($result["URL"]);
		$j=0;
		foreach ($result as $key => $ping)
		{
			if($ping == "OK"){
				$j++;
			} else {
				$arbadping[] = $arURL[$key];
			}
			$arping = array();
			$arping["NAME"] = $pagename;
			$arping["RESULT"] = $ping;
			$arping["SEACH"] = $arURL[$key];
			$arping["DATE"] = date("Y-m-d"); 
			$arping["TIME"] = date("H:i:s");
			$arping["ID"] = 0;
			$arping["URL"] = $pageURL;
			$arping["SITE_ID"] = $_REQUEST['siteid'];

			//make letters about all result of ping
			$res = $DB->Query("INSERT INTO `altasib_ping_log`(
					ID,
					SITE_ID,
					DATE,
					TIME,
					NAME,
					URL ,
					SEACH,
					RESULT
				)
				VALUES
				(".intval($arping["ID"]).",'".$DB->ForSql($arping["SITE_ID"])."','".$DB->ForSql($arping["DATE"])."', '".$DB->ForSql($arping["TIME"])."', '".$DB->ForSql($arping["NAME"])."', '".$DB->ForSql($arping["URL"])."','".$DB->ForSql($arping["SEACH"])."', '".$DB->ForSql($arping['RESULT'])."')
			");
		}

		if($j==count($result)){?>
			<span class="popwintext" ><font color="green"><?=GetMessage("PING_RESULT_STAT_GOOD")?></font></span>
		<?} else {?>
			<span class="popwintext"><font color="red"><?=GetMessage("PING_RESULT_STAT_BAD")?></font></span>
		<?}

	} else {?>
		<span class="popwintext" >
		<font>
		<?=GetMessage("PING_RESULT_STAT_LINK_ERROR");?>
		</font>
		</span>
	<?}

	} else {?>
		<span class="popwintext">
		<?=GetMessage("PING_RESULT_STAT_LINK_ERROR");?>...
		</span>
	<?
	}
} else {
	//check write ping page in log
	$urls = COption::GetOptionString("altasib.ping", "send_blog_ping_address");
	$urls = explode("\r\n", $urls);
	$alsoping = 0;
	foreach($urls as $key=>$url){
		$resCheck = CAltasibping::CheckPingStr($pagename, $pageURL, $url);
		if($resCheck['RESULT']){
			$alsoping++;
		}
	}

	if(count($urls)==$alsoping){
	$reping = true;
	//format time in current
	$arDate = ParseDateTime($resCheck['LASTPING']['DATE'], "YYYY-MM-DD");?>

		<div class="popwintext">
			<font color="red"><?=GetMessage("PING_RESULT_STAT_LAST_PING")?></font> <?=$arDate["DD"].".".$arDate["MM"].".".$arDate["YYYY"]?> <?=$resCheck['LASTPING']['TIME']?>
			<br />
			<b><?=GetMessage("PING_RESULT_STAT_REPING")?></b>
			<br /><br /> 
			<b><?=GetMessage("PING_STAT_PAGENAME_T")?>:</b> <?=$_REQUEST['pagename']?><br />
			<b><?=GetMessage("PING_STAT_PAGEADRESS_T")?></b>: <?=$pageURL?><br />
		</div>
		<?
	}else{?>
		<div class="popwintext"><?=GetMessage("PING_RESULT_STAT_SEND")?><br />
		<br />
			<b><?=GetMessage("PING_STAT_PAGENAME_T")?></b>: <?=urldecode($_REQUEST['pagename'])?><br />
			<b><?=GetMessage("PING_STAT_PAGEADRESS_T")?></b>: <?=$pageURL?><br />
		</div>
	<?}
}

$arPostParams = array(
	"backurl" => $pageURL,
	"pagename" => $pagename,
	"siteid" => $siteid,
	"ping" => "Y"
);

	
$popupWindow->EndContent();
$popupWindow->StartButtons();?> 
<?if($_REQUEST['ping']!='Y'){?>
<input type="button" name="pingorder" value="Ping" id="ping" onclick="top.<?=$popupWindow->jsPopup?>.Close();new BX.CDialog({
'content_url':'/bitrix/admin/altasib_statpageping.php',
'content_post':<?=CUtil::PhpToJsObject($arPostParams)?>,
'width':'350',
'height':'100',
'min_width':'350',
'min_height':'100'
}).Show();" />
<?}?>
<?if($_REQUEST['ping']=='Y' || $reping){?>
<input type="button" name="pingorder" value="<?=GetMessage("PING_RESULT_STAT_LINK_ORDER")?>" id="pingorder" onclick="window.open('/bitrix/admin/altasib_ping_log.php?PAGEN_1=1&SIZEN_1=20&lang=ru&by=COUNT&order=desc','_blank');return false;">
<?}?>
<input type="button" name="close" value="<?=GetMessage("PING_RESULT_STAT_LINK_CLOSE")?>" id="close" onclick="top.<?=$popupWindow->jsPopup?>.Close();">

<?
$popupWindow->EndButtons(); 
?>