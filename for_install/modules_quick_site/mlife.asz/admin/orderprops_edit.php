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

$arTypes = array();
$arTypes['TEXT'] = Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_TYPES1");
$arTypes['TEXTAREA'] = Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_TYPES2");
$arTypes['LOCATION'] = Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_TYPES3");
$arTypes['EMAIL'] = Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_TYPES4");

?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
  
	$CODE = trim($_REQUEST["CODE"]);
	$NAME = trim($_REQUEST["NAME"]);
	$PARAMS = trim($_REQUEST["PARAMS"]);
	$TYPE = trim($_REQUEST["TYPE"]);
	$SORT = intval($_REQUEST["SORT"]);
	$ACTIVE = ($_REQUEST["ACTIVE"]=="Y") ? "Y" : "N";
	$REQ = ($_REQUEST["REQ"]=="Y") ? "Y" : "N";
	$DELIVERY = ($_REQUEST["DELIVERY"]=="Y") ? "Y" : "N";
	$SITEID = trim($_REQUEST["SITEID"]);
	if(!$SITEID) $SITEID = null;

	$arFields = Array(
		"CODE" => $CODE,
		"SITEID" => $SITEID,
		"NAME" => $NAME,
		"ACTIVE" => $ACTIVE,
		"REQ" => $REQ,
		"DELIVERY" => $DELIVERY,
		"PARAMS" => $PARAMS,
		"TYPE" => $TYPE,
		"SORT" => $SORT,
	);
	
	$noupdate = false;
	
	if(!$noupdate){
		$baseCheck = Asz\OrderpropsTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array("CODE"=>$CODE,"!ID"=>$ID,"SITEID"=>$SITEID),
				'limit' => 1,
			)
		);
		
		if(!$baseCheck->Fetch()){
		}else{
			$errorAr[] = Loc::getMessage('MLIFE_ASZ_ORDERPROPSEL_ERROR_CODE');
			$noupdate = true;
		}
	}
	
	// сохранение данных
	if($ID > 0){
		if(!$noupdate)
			$res = Asz\OrderpropsTable::update($ID,$arFields);
	}
	else{
		if(!$noupdate)
			$res = Asz\OrderpropsTable::add($arFields);
	}
	
	if(!$noupdate){
		if($res->isSuccess() && count($errorAr)==0){
			if ($_REQUEST['apply'] != "" && $ID>0){
				LocalRedirect("mlife_asz_orderprops_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			elseif ($_REQUEST['apply'] != ""){
				LocalRedirect("mlife_asz_orderprops_edit.php?ID=".$res->getId()."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect("mlife_asz_orderprops.php?lang=".LANG);
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
$str_PARAMS = "";
$str_TYPE = "";
$str_SORT = "500";
$str_ACTIVE = "N";
$str_REQ = "N";
$str_DELIVERY = "N";
$str_SITEID = "";

if($ID>0)
{
	$dataAr = Asz\OrderpropsTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_CODE = $dataAr["CODE"];
		$str_NAME = $dataAr["NAME"];
		$str_PARAMS = $dataAr["PARAMS"];
		$str_TYPE = $dataAr["TYPE"];
		$str_SORT = $dataAr["SORT"];
		$str_ACTIVE = $dataAr["ACTIVE"];
		$str_REQ = $dataAr["REQ"];
		$str_SITEID = $dataAr["SITEID"];
		$str_DELIVERY = $dataAr["DELIVERY"];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_orderprops_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_SAVED"), "TYPE"=>"OK"));
  
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
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_NAME")?></td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_CODE")?></td>
		<td width="60%">
			<input type="text" name="CODE" value="<?=$str_CODE?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_ACTIVE")?></td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_REQ")?></td>
		<td width="60%">
			<input type="checkbox" name="REQ" value="Y"<?if($str_REQ == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_SORT")?></td>
		<td width="60%">
			<input type="text" name="SORT" value="<?=$str_SORT?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_DELIVERY")?></td>
		<td width="60%">
			<input type="checkbox" name="DELIVERY" value="Y"<?if($str_DELIVERY == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_TYPE")?></td>
		<td width="60%">
			<select name="TYPE">
				<?foreach($arTypes as $typeid=>$typename){?>
				<option value="<?=$typeid?>"<?if($str_TYPE == $typeid) echo " selected"?>><?=$typename?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_PARAMS")?></td>
		<td width="60%">
			<textarea rows="5" cols="30" name="PARAMS"><?=$str_PARAMS?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_ORDERPROPSEL_PARAM_SITE")?></td>
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
    "back_url"=>"orderprops.php?lang=".LANG,
    
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