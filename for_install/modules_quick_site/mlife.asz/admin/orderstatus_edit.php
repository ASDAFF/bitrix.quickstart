<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("mlife.asz");
use Bitrix\Main\Localization\Loc;
use Mlife\Asz;
Loc::loadMessages(__FILE__);

require_once("check_right.php");

$errorAr = array();

$arSites = array();
$obSite = CSite::GetList($by="sort", $order="desc");
while($arResult = $obSite->Fetch()) {
	if(!$FilterSiteId || (in_array($arResult['ID'],$FilterSiteId)))
		$arSites[$arResult['ID']] = '['.$arResult['ID'].'] - '.$arResult['NAME'];
}

?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_OSEL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_OSEL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
  
	$CODE = trim($_REQUEST["CODE"]);
	$NAME = trim($_REQUEST["NAME"]);
	$DESC = trim($_REQUEST["DESC"]);
	$ACTIVE = ($_REQUEST["ACTIVE"]=="Y") ? "Y" : "N";
	$SITEID = trim($_REQUEST["SITEID"]);
	if(!$SITEID) $SITEID = null;

	$arFields = Array(
		"CODE" => $CODE,
		"SITEID" => $SITEID,
		"NAME" => $NAME,
		"ACTIVE" => $ACTIVE,
		"DESC" => $DESC,
	);
	
	$noupdate = false;
	
	if(!$noupdate){
		$baseCheck = Asz\OrderStatusTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array("CODE"=>$CODE,"!ID"=>$ID,"SITEID"=>$SITEID),
				'limit' => 1,
			)
		);
		
		if(!$baseCheck->Fetch()){
		}else{
			$errorAr[] = Loc::getMessage('MLIFE_ASZ_OSEL_ERROR_CODE');
			$noupdate = true;
		}
	}
	
	// сохранение данных
	if($ID > 0){
		if(!$noupdate)
			$res = Asz\OrderStatusTable::update($ID,$arFields);
	}
	else{
		if(!$noupdate)
			$res = Asz\OrderStatusTable::add($arFields);
	}
	
	if(!$noupdate){
		if($res->isSuccess() && count($errorAr)==0){
			if ($_REQUEST['apply'] != "" && $ID>0){
				LocalRedirect("mlife_asz_orderstatus_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			elseif ($_REQUEST['apply'] != ""){
				LocalRedirect("mlife_asz_orderstatus_edit.php?ID=".$res->getId()."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect("mlife_asz_orderstatus.php?lang=".LANG);
			}
		}else{
			
			if(!$noupdate){
				foreach($res->getErrors() as $error){
					 $errorAr[] = $error->getMessage();
				}
			}

			$bVarsFromForm = true;

		}
	}else{
		$bVarsFromForm = true;
	}
}

$str_CODE = "";
$str_NAME = "";
$str_ACTIVE = "N";
$str_DESC = "";
$str_SITEID = "";

if($ID>0)
{
	$dataAr = Asz\OrderStatusTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_CODE = $dataAr["CODE"];
		$str_NAME = $dataAr["NAME"];
		$str_ACTIVE = $dataAr["ACTIVE"];
		$str_DESC = $dataAr["DESC"];
		$str_SITEID = $dataAr["SITEID"];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_OSEL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_OSEL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_OSEL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_OSEL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_orderstatus_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_OSEL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_OSEL_SAVED"), "TYPE"=>"OK"));
  
if(count($errorAr)>0){
	CAdminMessage::ShowMessage(implode(', ',$errorAr));
}

?>
<?if($bVarsShowForm){?>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<?
$tabControl->Begin();
?>
<?
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_OSEL_PARAM_NAME")?></td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_OSEL_PARAM_DESC")?></td>
		<td width="60%">
			<textarea rows="5" cols="30" name="DESC"><?=$str_DESC?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_OSEL_PARAM_CODE")?></td>
		<td width="60%">
			<input type="text" name="CODE" value="<?=$str_CODE?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_OSEL_PARAM_ACTIVE")?></td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_OSEL_PARAM_SITE")?></td>
		<td width="60%">
			<select name="SITEID">
				<?foreach($arSites as $siteid=>$sitename){?>
				<option value="<?=$siteid?>"<?if($str_SITEID == $siteid) echo " selected"?>><?=$sitename?></option>
				<?}?>
			</select>
		</td>
	</tr>
<?
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"orderstatus.php?lang=".LANG,
    
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