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
Loc::loadMessages(__FILE__);

require_once("check_right.php");

$errorAr = array();

$arSites = array();
$obSite = CSite::GetList($by="sort", $order="desc");
while($arResult = $obSite->Fetch()) {
	if(!$FilterSiteId || (in_array($arResult['ID'],$FilterSiteId)))
		$arSites[$arResult['ID']] = '['.$arResult['ID'].'] - '.$arResult['NAME'];
}

$arUsersGroups = array();
$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", array("ACTIVE"=>"Y"));
$arUsersGroups = array();
if(intval($rsGroups->SelectedRowsCount()) > 0)
{
   while($arGroups = $rsGroups->Fetch())
   {
      $arUsersGroups[$arGroups["ID"]] = $arGroups["NAME"];
   }
}

?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
  
	$CODE = trim($_REQUEST["CODE"]);
	$NAME = trim($_REQUEST["NAME"]);
	$BASE = (trim($_REQUEST["BASE"]) <> "Y" ? "N" : "Y");
	$SITEID = trim($_REQUEST["SITEID"]);
	$GROUPID = $_REQUEST["GROUPID"];
	if(!is_array($GROUPID)) $GROUPID = array();
	if(!$SITEID) $SITEID = null;

	$arFields = Array(
		"CODE" => $CODE,
		"NAME" => $NAME,
		"BASE" => $BASE,
		"SITE_ID" => $SITEID,
		"GROUP" => $GROUPID,
	);
	
	if(strlen($CODE)!=4){
		$errorAr[] = Loc::getMessage('MLIFE_ASZ_PRICEEL_ERROR_CODER');
	}
	if(!$NAME){
		$errorAr[] = Loc::getMessage('MLIFE_ASZ_PRICEEL_ERROR_NAME2');
	}
	elseif(strlen($NAME)>255){
		$errorAr[] = Loc::getMessage('MLIFE_ASZ_PRICEEL_ERROR_NAME');
	}
	
	$noupdate = false;
	
	if(count($errorAr)==0){
		
		if($BASE=="Y"){
			$baseCheck = \Mlife\Asz\PricetipTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array("BASE"=>"Y","!ID"=>$ID,"=SITE_ID"=>($SITEID===null) ? false : $SITEID),
					'limit' => 1,
				)
			);
			
			if(!$baseCheck->Fetch()){
			}else{
				$errorAr[] = Loc::getMessage('MLIFE_ASZ_PRICEEL_ERROR_BASE');
				$noupdate = true;
			}
		}
		
		if(!$noupdate){
				$baseCheck = \Mlife\Asz\PricetipTable::getList(
					array(
						'select' => array('ID'),
						'filter' => array("CODE"=>$CODE,"!ID"=>$ID,"=SITE_ID"=>($SITEID===null) ? false : $SITEID),
						'limit' => 1,
					)
				);
				
				if(!$baseCheck->Fetch()){
				}else{
					$errorAr[] = Loc::getMessage('MLIFE_ASZ_PRICEEL_ERROR_CODE');
					$noupdate = true;
				}
		}
		
		// сохранение данных
		if($ID > 0){
			if(!$noupdate)
				$res = \Mlife\Asz\PricetipTable::update($ID,$arFields);
		}
		else{
			if(!$noupdate)
				$res = \Mlife\Asz\PricetipTable::add($arFields);
		}
		
		if(!$noupdate){
			if(!$res->isSuccess() && count($errorAr)==0){
				foreach($res->getErrors() as $error){
					 $errorAr[] = $error->getMessage();
				}
				$bVarsFromForm = true;
			}else{
				//добавление прав на цену
				if($ID>0) {
					$res2 = \Mlife\Asz\PricetiprightTable::deleteright($ID);
					$ID_new = $ID;
				}else{
					$ID_new = $res->getId();
				}
					
				if(count($GROUPID)==0){
					$addAr = array("IDTIP"=>$ID_new,"IDGROUP"=>null);
					\Mlife\Asz\PricetiprightTable::add($addAr);
				}else{
					foreach($GROUPID as $group){
						$addAr = array("IDTIP"=>$ID_new,"IDGROUP"=>$group);
						\Mlife\Asz\PricetiprightTable::add($addAr);
					}
					unset($group);
				}
				
				if ($_REQUEST['apply'] != "" && $ID>0){
					LocalRedirect("mlife_asz_price_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
				}
				elseif ($_REQUEST['apply'] != ""){
					LocalRedirect("mlife_asz_price_edit.php?ID=".$ID_new."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
				}
				else{
					LocalRedirect("mlife_asz_price.php?lang=".LANG);
				}
			}
		}else{
			$bVarsFromForm = true;
		}
	}
}

$str_NAME = "";
$str_CODE = "";
$str_BASE = "N";
$str_SITEID = "";
$str_GROUPID = array();

if($ID>0)
{
	$dataAr = \Mlife\Asz\PricetipTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_CODE = $dataAr["CODE"];
		$str_NAME = $dataAr["NAME"];
		$str_BASE = $dataAr["BASE"];
		$str_SITEID = $dataAr["SITE_ID"];
		$str_GROUPID = $dataAr["GROUP"];
		
		/*$data2Ob = \Mlife\Asz\PricetiprightTable::getList(array(
			'select' => array('IDTIP','IDGROUP'),
			'filter' => array("IDTIP"=>$ID),
		));
		while($data2ar = $data2Ob->Fetch()){
			$str_GROUPID[$data2ar["IDGROUP"]] = $data2ar["IDGROUP"];
		}*/
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_PRICEEL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_PRICEEL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_PRICEEL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_PRICEEL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_price_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_PRICEEL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_PRICEEL_SAVED"), "TYPE"=>"OK"));
  
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
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM_NAME")?></td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM_CODE")?></td>
		<td width="60%">
			<input type="text" name="CODE" value="<?=$str_CODE?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM_BASE")?></td>
		<td width="60%">
			<input type="checkbox" name="BASE" value="Y"<?if($str_BASE == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM_SITE")?></td>
		<td width="60%">
			<select name="SITEID">
				<option value=""<?if($str_SITEID == "") echo " selected"?>><?=Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM_SITE_ALL")?></option>
				<?foreach($arSites as $siteid=>$sitename){?>
				<option value="<?=$siteid?>"<?if($str_SITEID == $siteid) echo " selected"?>><?=$sitename?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_PRICEEL_PARAM_USERGROUP")?></td>
		<td width="60%">
			<select name="GROUPID[]" size="5" multiple>
				<?foreach($arUsersGroups as $groupid=>$groupname){?>
				<option value="<?=$groupid?>"<?if(in_array($groupid,$str_GROUPID)) echo " selected"?>><?=$groupname?></option>
				<?}?>
			</select>
		</td>
	</tr>
<?
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"price.php?lang=".LANG,
    
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

<?echo BeginNote();?>
<?=Loc::getMessage("MLIFE_ASZ_PRICEEL_NOTE")?>
<?echo EndNote();?>
<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>