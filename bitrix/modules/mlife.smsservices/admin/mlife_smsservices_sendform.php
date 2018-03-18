<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$module_id = "mlife.smsservices";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
$zr = "";
$phone = '';
if (! ($MODULE_RIGHT >= "R"))
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
?>
<?
$APPLICATION->SetTitle(Loc::getMessage("MLIFESS_SENDFORM_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetAdditionalCSS("/bitrix/css/mlife.smsservices/style.css");

\Bitrix\Main\Loader::includeModule($module_id);
$smsServices = new \Mlife\Smsservices\Sender();
$senderOptions = $smsServices->getAllSender();

$orderId = false;

if($_REQUEST['event'] && strpos($_REQUEST['event'],'MSMS_ORDER_')!==false){
	$res = \Mlife\Smsservices\ListTable::getList(array(
		'select' => array("*"),
		'filter' => array("=EVENT"=>htmlspecialcharsbx($_REQUEST['event'])),
		'order' => array("TIME"=>"DESC"),
		'limit' => 1
	));
	$phoneAr = array();
	while ($arData = $res->fetch()){
		$phone = $arData['PHONE'];
		$phoneAr[preg_replace("/([^0-9])/is","",$arData['PHONE'])] = $arData['PHONE'];
	}
	$orderId = true;
	
}

//TODO можно добавить в следующий релиз, опционально выбирать отправителя
//$senderOptions = $smsServices->getAllSender();
if ($_SERVER["REQUEST_METHOD"] == "POST" && $MODULE_RIGHT == "W" && strlen($_REQUEST["Send"]) > 0)
{
	$message = strip_tags($_REQUEST['message']);
	$smsServices->translit = $_REQUEST['translit'] ? true : false;
	$smsServices->app = $_REQUEST['app'] ? true : false;
	
	if($_REQUEST['event']) $smsServices->event = htmlspecialcharsbx($_REQUEST['event']);
	
	if(!$_REQUEST['phone'] or !$_REQUEST['message']) {
		\ShowNote(Loc::getMessage("MLIFESS_SENDFORM_ERR_REQ"),'mlifeerror');
	}
	else{
		
		$phoneCheck = $smsServices->checkPhoneNumber($_REQUEST['phone']);
		$phone = $phoneCheck['phone'];
		
		
		
		if($phoneCheck['check']) {
			$resp = $smsServices->sendSms($phone, $message, MakeTimeStamp($datesend, "DD.MM.YYYY HH:MI:SS"),trim($_REQUEST['sender']),Loc::getMessage("MLIFESS_SENDFORM_PRIM"));
			if(!$resp->error){
				\ShowNote(Loc::getMessage("MLIFESS_SENDFORM_NOTICE_SEND"),'mlifenoticeok');
				$phone = false;
				$message = false;
				if($orderId) LocalRedirect('/bitrix/admin/sale_order_view.php?ID='.preg_replace("/([^0-9])/is","",$_REQUEST['event']).'&lang='.LANGUAGE_ID);
			}else{
				\ShowNote(Loc::getMessage("MLIFESS_SENDFORM_ERR_".$resp->error_code).', ERROR: '.$resp->error,'mlifeerror');
			}
		}
		else{
			\ShowNote(Loc::getMessage("MLIFESS_SENDFORM_ERR_PHONE").', '.$phone,'mlifeerror');
		}
		
	}
	
}


$aTabs = array(
	array("DIV" => "edit3", "TAB" => Loc::getMessage("MLIFESS_SENDFORM_TAB"), "ICON" => "vote_settings", "TITLE" => Loc::getMessage("MLIFESS_SENDFORM_TAB")),
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&event=<?=$_REQUEST['event']?>" id="FORMACTION">
<?
$tabControl->BeginNextTab();
?>
	
	<?if($_REQUEST['event']){?>
	<tr><td colspan="2" style="text-align:center;font-weight:bold;"><?=Loc::getMessage("MLIFESS_SENDFORM_ORDER")?> <?=$_REQUEST['event']?><br/><br/></td></tr>
	<?}?>
	<?
	if($orderId){
	
		if(count($phoneAr)>1){
			?>
			<tr><td colspan="2" style="text-align:center;font-weight:bold;"><?=Loc::getMessage("MLIFESS_SENDFORM_ORDERPHONE")?>: <?=implode(',',$phoneAr)?><br/><br/></td></tr>
			<?
		}
	
	}
	?>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_SENDFORM_SENDER")?>:</td>
		<td>
			<?
			$current = ($_REQUEST['sender']) ? trim($_REQUEST['sender']) : \Bitrix\Main\Config\Option::get($module_id, "sender", "","");
			if(!$senderOptions->error && count($senderOptions)>1 && false){?>
			<select name="sender" id="sender">
				<?
				$cn = 0;
				foreach($senderOptions as $sender){
				$cn++;
				?>
				<option value="<?=$sender->sender?>"<?if($sender->sender == $current){?> selected="selected"<?}?>><?=$sender->sender?></option>

				<?}
				if($cn == 0) {
				?>
				<option value="<?=$current?>" selected="selected"><?=$current?></option>
				<?
				}
				?>
			</select>
			<?}else{
			?>
			<input type="text" size="28" maxlength="255" value="<?=$current?>" name="sender" autocomplete="off">
			<?
			}?>
		</td>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_SENDFORM_PHONE")?>*:</td>
		<td>
			<input type="text" size="28" maxlength="255" value="<?=$phone?>" name="phone">
		</td>
		</td>
	</tr>
	<tr>
		<td><?=Loc::getMessage("MLIFESS_SENDFORM_MESS")?>*:</td>
		<td>
			<textarea cols="27" rows="5" name="message"><?=$message?></textarea>
		</td>
	</tr>

	<tr>
		<td><?=Loc::getMessage("MLIFESS_SENDFORM_DATE")?>:</td>
		<td>
			<?echo \CalendarDate("datesend", $_REQUEST['datesend'], "datesend", "25", "class=\"date\"")?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFESS_SENDFORM_TRANSLIT")?></td>
		<td width="60%">
			<input type="checkbox" name="translit" value="Y"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFESS_SENDFORM_APP")?></td>
		<td width="60%">
			<input type="checkbox" name="app" value="Y"/>
		</td>
	</tr>
	<?
$tabControl->Buttons();
?>
	<input <?if ($MODULE_RIGHT<"W") echo "disabled" ?> type="submit" class="adm-btn-green" name="Sendform" value="<?=Loc::getMessage("MLIFESS_SENDFORM_SEND")?>" />
	<input type="hidden" name="Send" value="Y" />
<?$tabControl->End();
?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>