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

$arCountry = array();
$obSite = Asz\CountryTable::getList();
while($arResult = $obSite->Fetch()) {
	if(!$FilterSiteId || (in_array($arResult['SITEID'],$FilterSiteId)))
		$arCountry[$arResult['ID']] = '['.$arResult['SITEID'].'] - '.$arResult['NAME'];
}

?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM")),
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
	$SORT = intval($_REQUEST["SORT"]);
	$ACTIVE = ($_REQUEST["ACTIVE"]=="Y") ? "Y" : "N";
	$COUNTRY = trim($_REQUEST["COUNTRY"]);
	if(!$COUNTRY) $COUNTRY = null;

	$arFields = Array(
		"CODE2" => $CODE2,
		"CODE3" => $CODE3,
		"COUNTRY" => $COUNTRY,
		"NAME" => $NAME,
		"ACTIVE" => $ACTIVE,
		"SORT" => $SORT,
	);
	
	$noupdate = false;
	
	if(!$noupdate){
		$baseCheck = Asz\StateTable::getList(
			array(
				'select' => array('ID'),
				'filter' => array("CODE3"=>$CODE3,"!ID"=>$ID,"COUNTRY"=>$COUNTRY),
				'limit' => 1,
			)
		);
		
		if(!$baseCheck->Fetch()){
		}else{
			$errorAr[] = Loc::getMessage('MLIFE_ASZ_STATEEL_ERROR_CODE');
			$noupdate = true;
		}
	}
	
	// сохранение данных
	if($ID > 0){
		if(!$noupdate)
			$res = Asz\StateTable::update($ID,$arFields);
	}
	else{
		if(!$noupdate)
			$res = Asz\StateTable::add($arFields);
	}
	
	if(!$noupdate){
		if($res->isSuccess() && count($errorAr)==0){
			if ($_REQUEST['apply'] != "" && $ID>0){
				LocalRedirect("mlife_asz_state_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			elseif ($_REQUEST['apply'] != ""){
				LocalRedirect("mlife_asz_state_edit.php?ID=".$res->getId()."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect("mlife_asz_state.php?lang=".LANG);
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
$str_SORT = "500";
$str_ACTIVE = "N";
$str_COUNTRY = "";

if($ID>0)
{
	$dataAr = Asz\StateTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_CODE2 = $dataAr["CODE2"];
		$str_CODE3 = $dataAr["CODE3"];
		$str_NAME = $dataAr["NAME"];
		$str_ACTIVE = $dataAr["ACTIVE"];
		$str_SORT = $dataAr["SORT"];
		$str_COUNTRY = $dataAr["COUNTRY"];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_STATEEL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_STATEEL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_STATEEL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_STATEEL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_state_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_STATEEL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_STATEEL_SAVED"), "TYPE"=>"OK"));
  
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
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM_NAME")?></td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM_CODE2")?></td>
		<td width="60%">
			<input type="text" name="CODE2" value="<?=$str_CODE2?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM_CODE3")?></td>
		<td width="60%">
			<input type="text" name="CODE3" value="<?=$str_CODE3?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM_SORT")?></td>
		<td width="60%">
			<input type="text" name="SORT" value="<?=$str_SORT?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM_ACTIVE")?></td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_STATEEL_PARAM_SITE")?></td>
		<td width="60%">
			<select name="COUNTRY">
				<?foreach($arCountry as $countryid=>$countryname){?>
				<option value="<?=$countryid?>"<?if($str_COUNTRY == $countryid) echo " selected"?>><?=$countryname?></option>
				<?}?>
			</select>
		</td>
	</tr>
<?
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"state.php?lang=".LANG,
    
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