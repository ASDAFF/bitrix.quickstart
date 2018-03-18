<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/livetex.clicktochat/option.php");
IncludeModuleLangFile(__FILE__);
if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');
$APPLICATION->SetTitle(GetMessage("LIVE_TITLE"));

$aTabs = array(
	array("DIV" => "fedit1", "TAB" => "livetex", "ICON" => "main_settings", "TITLE" => GetMessage("LIVE_TITLE")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
if(intval($_REQUEST['liveID'])>0){
    $f = fopen($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/livetex.clicktochat/option.php","w");
    fwrite($f,'<?$option=array("liveID"=>"'.intval($_REQUEST["liveID"]).'",);?>');
    fclose($f);
}
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/livetex.clicktochat/option.php");
?>

<?
$rsSites = CSite::GetList($by="sort", $order="desc", Array("ACTIVE" => "Y"));
while ($arSite = $rsSites->Fetch())
{
}
?>

<form name="captcha_form" method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANG?>">
<?=bitrix_sessid_post()?>
<?$tabControl->BeginNextTab();?>

<img style="display: block; width: 300px;" src="/upload/livetex/logo.png" alt="<?=GetMessage('LIVETEX_ALT')?>"/>

<h2><?=GetMessage("LIVETEX_BLOCK1_TITLE")?></h2>
<?=GetMessage("LIVETEX_BLOCK1_TEXT")?>

<h2><?=GetMessage('LIVETEX_REG_TITLE')?></h2>
<p><?=GetMessage('LIVETEX_REG_INFO')?></p>
<ol style="line-height: 1.4em;"> 
	<li style="margin-top: 10px;"><a href="https://billing.livetex.ru/reg" target="_blank"><?=GetMessage('LIVETEX_REG')?></a> <?=GetMessage('LIVETEX_REG_SITE')?> <a href="http://livetex.ru" target="_blank">livetex.ru</a></li>
	<li style="margin-top: 10px;"><?=GetMessage('LIVETEX_REG_ID')?>: <input type="text" name="liveID" value="<?=$option['liveID']?>"/> <?=GetMessage('LIVETEX_REG_INFO2')?><br />
	<img border="0" width="619" height="291" style="margin: 5px 0;" src="/upload/livetex/livetexid.jpg" />
	</li>
<?=GetMessage('LIVETEX_REG_INFO3')?>
</ol>

<h2><?=GetMessage('LIVETEX_SUPPORT_TITLE')?></h2>
<p style="line-height: 1.4em;">
<?=GetMessage('LIVETEX_SUPPORT_INFO')?>
	+7 (812) 449-49-30<br>
	+7 (495) 644-29-42<br>
	<a href="mailto:support@livetex.ru">support@livetex.ru</a>
</p>

<?
$tabControl->Buttons(array("disabled" => !$isAdmin));
$tabControl->End();
?>
<script>
var x=document.getElementsByName("apply");
x[0].style.display='none'; 
</script>
</form>