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
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
  
	$CODE2 = trim($_REQUEST["CODE2"]);
	$CODE3 = trim($_REQUEST["CODE3"]);
	$NAME = trim($_REQUEST["NAME"]);
	$ACTIVE = ($_REQUEST["ACTIVE"]=="Y") ? "Y" : "N";
	$SITEID = trim($_REQUEST["SITEID"]);
	if(!$SITEID) $SITEID = null;

	$arFields = Array(
		"CODE2" => $CODE2,
		"CODE3" => $CODE3,
		"SITEID" => $SITEID,
		"NAME" => $NAME,
		"ACTIVE" => $ACTIVE,
	);
	
	$noupdate = false;
	
	if(!$noupdate){
		$baseCheck = Asz\CountryTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array("CODE3"=>$CODE3,"!ID"=>$ID,"SITEID"=>$SITEID),
				'limit' => 1,
			)
		);
		
		if(!$baseCheck->Fetch()){
		}else{
			$errorAr[] = Loc::getMessage('MLIFE_ASZ_COUNTRYEL_ERROR_CODE');
			$noupdate = true;
		}
	}
	
	// сохранение данных
	if($ID > 0){
		if(!$noupdate)
			$res = Asz\CountryTable::update($ID,$arFields);
	}
	else{
		if(!$noupdate)
			$res = Asz\CountryTable::add($arFields);
	}
	
	if(!$noupdate){
		if($res->isSuccess() && count($errorAr)==0){
			if ($_REQUEST['apply'] != "" && $ID>0){
				LocalRedirect("mlife_asz_country_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			elseif ($_REQUEST['apply'] != ""){
				LocalRedirect("mlife_asz_country_edit.php?ID=".$res->getId()."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect("mlife_asz_country.php?lang=".LANG);
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

$str_CODE2 = "";
$str_CODE3 = "";
$str_NAME = "";
$str_ACTIVE = "N";
$str_SITEID = "";

if($ID>0)
{
	$dataAr = Asz\CountryTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_CODE2 = $dataAr["CODE2"];
		$str_CODE3 = $dataAr["CODE3"];
		$str_NAME = $dataAr["NAME"];
		$str_ACTIVE = $dataAr["ACTIVE"];
		$str_SITEID = $dataAr["SITEID"];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_COUNTRYEL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_COUNTRYEL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_COUNTRYEL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_COUNTRYEL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_country_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_COUNTRYEL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_COUNTRYEL_SAVED"), "TYPE"=>"OK"));
  
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
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM_NAME")?></td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM_CODE2")?></td>
		<td width="60%">
			<input type="text" name="CODE2" value="<?=$str_CODE2?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM_CODE3")?></td>
		<td width="60%">
			<input type="text" name="CODE3" value="<?=$str_CODE3?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM_ACTIVE")?></td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_COUNTRYEL_PARAM_SITE")?></td>
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
    "back_url"=>"country.php?lang=".LANG,
    
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