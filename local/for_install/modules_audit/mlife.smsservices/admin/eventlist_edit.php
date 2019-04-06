<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$module_id = "mlife.smsservices";
\Bitrix\Main\Loader::includeModule($module_id);

$POST_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($POST_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$APPLICATION->SetAdditionalCSS("/bitrix/css/".$module_id."/style.css");

$errorAr = array();

$arSites = array();
$obSite = CSite::GetList($by="sort", $order="desc");
while($arResult = $obSite->Fetch()) {
	if(!$FilterSiteId || (in_array($arResult['ID'],$FilterSiteId)))
		$arSites[$arResult['ID']] = '['.$arResult['ID'].'] - '.$arResult['NAME'];
}

$eventList = \Mlife\Smsservices\Events::getList();
//print_r($eventList);

global $USER;
$isAdmin = $USER->CanDoOperation('lpa_template_edit');
$isUserHavePhpAccess = $USER->CanDoOperation('edit_php');
?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
	
	$noupdate = false;
	
	$TEMPLATE = trim($_REQUEST['TEMPLATE']);
	
	/*if(!$isUserHavePhpAccess)
	{
		$MESSAGE_OLD = false;
		if($ID>0)
		{
			$emOldDb = \Mlife\Smsservices\EventlistTable::getRowById($ID);
			if($emOld = $emOldDb->Fetch())
			{
				$MESSAGE_OLD = $emOld['MESSAGE'];
			}
		}

		$TEMPLATE = LPA::Process($TEMPLATE, $MESSAGE_OLD);
	}*/
	
	$SENDER = trim($_REQUEST["SENDER"]);
	$SITE_ID = trim($_REQUEST["SITE_ID"]);
	$EVENT = trim($_REQUEST["EVENT"]);
	$PARAMS = trim($_REQUEST["PARAMS"]);
	$NAME = trim($_REQUEST["NAME"]);
	$ACTIVE = (trim($_REQUEST["ACTIVE"]) <> "Y" ? "N" : "Y");

	$arFields = Array(
		"SENDER" => $SENDER,
		"SITE_ID" => $SITE_ID,
		"EVENT" => $EVENT,
		"PARAMS" => $PARAMS,
		"ACTIVE" => $ACTIVE,
		"TEMPLATE" => $TEMPLATE,
		"NAME" => $NAME,
	);
	
	$eventDefault = $eventList[$arFields['EVENT']];
	
	if (is_callable($eventDefault['FIELD']['BEFORE_SAVE'][0], $eventDefault['FIELD']['BEFORE_SAVE'][1])){
		$arFields = call_user_func(array($eventDefault['FIELD']['BEFORE_SAVE'][0], $eventDefault['FIELD']['BEFORE_SAVE'][1]), $arFields);
	}
	
	if($ID > 0){
		if(!$noupdate) {
			$res = \Mlife\Smsservices\EventlistTable::update($ID,$arFields);
		}
	}else{
		if(!$noupdate) {
			$res = \Mlife\Smsservices\EventlistTable::add($arFields);
		}
	}
	
	if(!$noupdate){
		if(!$res->isSuccess() && count($errorAr)==0){
				foreach($res->getErrors() as $error){
					 $errorAr[] = $error->getMessage();
				}
				$bVarsFromForm = true;
		}else{
			
			if($ID>0) {
				$ID_new = $ID;
			}else{
				$ID_new = $res->getId();
			}
			
			if ($_REQUEST['apply'] != "" && $ID>0){
				LocalRedirect("mlife_smsservices_eventlist_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			elseif ($_REQUEST['apply'] != ""){
				LocalRedirect("mlife_smsservices_eventlist_edit.php?ID=".$ID_new."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect("mlife_smsservices_eventlist.php?lang=".LANG);
			}
		
		}
	}else{
		$bVarsFromForm = true;
	}
	
}

$str_SITE_ID = "";
$str_SENDER = "";
$str_EVENT = ($_REQUEST['EVENT']) ? trim($_REQUEST['EVENT']) : "";
$str_TEMPLATE = ($TEMPLATE) ? $TEMPLATE : "";
$str_PARAMS = "";
$str_ACTIVE = "Y";
$str_NAME = "";
$str_BODY_TYPE = $_REQUEST['BODY_TYPE'];

if($ID>0)
{
	$dataAr = \Mlife\Smsservices\EventlistTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_SITE_ID = $dataAr['SITE_ID'];
		$str_SENDER = $dataAr['SENDER'];
		$str_EVENT = $dataAr['EVENT'];
		$str_TEMPLATE = $dataAr['TEMPLATE'];
		$str_PARAMS = $dataAr['PARAMS'];
		$str_ACTIVE = $dataAr['ACTIVE'];
		$str_NAME = $dataAr['NAME'];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

?>
<?
$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_EDIT")." ID = ".$ID : Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_ADD")));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_ADD_CERENCY"),
    "LINK"=> "mlife_smsservices_eventlist_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_SAVED"), "TYPE"=>"OK"));
  
if(count($errorAr)>0){
	CAdminMessage::ShowMessage(implode(', ',$errorAr));
}

$eventDefault = false;

?>
<?if($bVarsShowForm){?>

<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<?if($str_EVENT){?><input type="hidden" name="EVENT" value="<?=$str_EVENT?>"><?}?>
<?
$tabControl->Begin();
?>
<?
$tabControl->BeginNextTab();
?>
<?if(!$str_EVENT){?>
<tr>
	<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_EVENT")?></td>
	<td width="60%">
		<select id="EVENT" name="EVENT" onchange="this.form.submit();">
		<option value=""><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_EVENT_DEF")?></option>
		<?foreach($eventList as $name=>$ev){?>
		<?
		if(is_array($ev['BX_EVENT'])){
		$cn = true;
			foreach($ev['BX_EVENT'] as $cl){
				if(!\Bitrix\Main\Loader::includeModule($cl[0])) {
					$cn = false;
					break;
				}
			}
		}
		if(!$cn) continue;
		?>
		<option value="<?=$name?>">[<?=$name?>] <?=$ev['NAME']?></option>
		<?}?>
		</select>
	</td>
</tr>
<?}else{?>
<tr>
	<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_EVENT")?></td>
	<td width="60%">
		<?foreach($eventList as $name=>$ev){?>
		<?if($str_EVENT == $name){
		$eventDefault = $ev;
		?>
		<b><?=$ev['NAME']?> [<?=$str_EVENT?>]</b>
		<?
		break;
		}?>
		<?}?>
		
	</td>
</tr>
<tr>
<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_NAME")?></td>
<td width="60%">
	<?if(!$str_NAME) $str_NAME = $eventDefault['NAME'];?>
	<input type="text" name="NAME" value="<?=$str_NAME?>"/>
</td>
</tr>
<tr>
<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_ACTIVE")?></td>
<td width="60%">
	<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>/>
</td>
</tr>
<tr>
<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_SITE_ID")?></td>
<td width="60%">
	<select name="SITE_ID" ID="SITE_ID">
		<?foreach($arSites as $siteid=>$sitename){?>
		<option value="<?=$siteid?>"<?if($str_SITE_ID == $siteid) echo " selected"?>><?=$sitename?></option>
		<?}?>
	</select>
</td>
</tr>
<tr>
<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_SENDER")?></td>
<td width="60%">
	<input type="text" name="SENDER" value="<?=$str_SENDER?>"/>
	<br/><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_SENDER_DESC")?>
</td>
</tr>
<tr>
<td width="40%"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_TEMPLATE")?></td>
<td width="60%">
	<?CFileMan::AddHTMLEditorFrame(
		"TEMPLATE",
		$str_TEMPLATE,
		"BODY_TYPE",
		$str_BODY_TYPE,
		array(
			'height' => 150,
			'width' => '100%'
		),
		"N",
		0,
		"",
		"onfocus=\"t=this\"",
		false,
		!$isUserHavePhpAccess,
		false,
		array(
			//'saveEditorKey' => $IBLOCK_ID,
			//'site_template_type' => 'mail',
			'templateID' => 'TEMPLATE',
			'componentFilter' => array('TYPE' => 'mail'),
			'limit_php_access' => !$isUserHavePhpAccess
		)
	);?>
	<br/><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_TEMPLATE_DESC")?>
</td>
</tr>

<?if($eventDefault){
?>
<tr class="heading"><td colspan="2"><?=Loc::getMessage("MLIFE_SMSSERVICES_EVENTLIST_ADMIN_PARAM_PARAM_TITLE")?></td></tr>
<?
if (is_callable($eventDefault['FIELD']['HTML'][0], $eventDefault['FIELD']['HTML'][1])){
echo call_user_func(array($eventDefault['FIELD']['HTML'][0], $eventDefault['FIELD']['HTML'][1]), $str_PARAMS);
}
?>
<?}?>

<?}?>
<?
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"mlife_smsservices_eventlist.php?lang=".LANG,
    
  )
);
?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?
$tabControl->End();
?>

<?
$tabControl->ShowWarnings("post_form", $message);
?>

<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>