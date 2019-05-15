<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$module_id = "mlife.smsservices";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
if (! ($MODULE_RIGHT >= "R"))
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
	
$APPLICATION->SetTitle(Loc::getMessage("MLIFESS_BALANCE_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetAdditionalCSS("/bitrix/css/mlife.smsservices/style.css");

\Bitrix\Main\Loader::includeModule($module_id);
$smsServices = new \Mlife\Smsservices\Sender();
$arrBalance = $smsServices->getBalance();
?>
<?foreach($arrBalance as $key=>$val){?>
<?if($val){?>
<div class="balance">
<div class="titleTransport"><?=Loc::getMessage("MLIFESS_BALANCE_TRANSPORT_".ToUpper($key))?></div>
<?
if($val->error) {
?>
<?=Loc::getMessage("MLIFESS_BALANCE_ERR")?>: <?=Loc::getMessage("MLIFESS_BALANCE_ERR_".$val->error_code)?>
<?}else{?>
<?=Loc::getMessage("MLIFESS_BALANCE_OST")?>: <strong><?=$val->balance?></strong>

<?}?>
</div>
<?}?>
<?}?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>