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

$arFilterIblock = Array(
		'ACTIVE'=>'Y',
	);
if($FilterSiteId){
	$arFilterIblock["SITE_ID"] = $FilterSiteId;
}

\CModule::IncludeModule("iblock");
$arIblock = array();
$res = \CIBlock::GetList(
	Array(), 
	$arFilterIblock, true
);
while($ar_res = $res->Fetch())
{
	$i++;
	$arIblock[$ar_res['ID']] = "[".$ar_res['IBLOCK_TYPE_ID']."] [".$ar_res['CODE']."] ".$ar_res['NAME'];
}

$iblockId = 0;
if(intval($_REQUEST["iblock"])>0){
	$iblockId = $_REQUEST["iblock"];
}elseif(intval($_REQUEST["iblockid"])>0) {
	$iblockId = $_REQUEST["iblockid"];
}

?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
  
	$SORT = intval($_REQUEST["SORT"]);
	$TEMPLATE_TITLE = trim($_REQUEST["TEMPLATE_TITLE"]);
	$TEMPLATE_KEY = trim($_REQUEST["TEMPLATE_KEY"]);
	$TEMPLATE_DESC = trim($_REQUEST["TEMPLATE_DESC"]);
	$TEMPLATE_NAME = trim($_REQUEST["TEMPLATE_NAME"]);
	$TEMPLATE_TEXT = trim($_REQUEST["TEMPLATE_TEXT"]);
	$CATEGORYID = $_REQUEST['category'];
	$PROPERTYID = $_REQUEST['property'];

	$arFields = Array(
		"SORT" => $SORT,
		"TEMPLATE_TITLE" => $TEMPLATE_TITLE,
		"TEMPLATE_KEY" => $TEMPLATE_KEY,
		"TEMPLATE_DESC" => $TEMPLATE_DESC,
		"TEMPLATE_NAME" => $TEMPLATE_NAME,
		"TEMPLATE_TEXT" => $TEMPLATE_TEXT,
		"IBLOCKID" => $iblockId,
	);
	
	$noupdate = false;
	
	if(count($errorAr)==0){
		
		// сохранение данных
		if($ID > 0){
			if(!$noupdate)
				$res = \Mlife\Asz\MetafilterTable::update($ID,$arFields);
		}
		else{
			if(!$noupdate)
				$res = \Mlife\Asz\MetafilterTable::add($arFields);
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
					$res2 = \Mlife\Asz\MetafiltercatTable::deleterow($ID);
					$res2 = \Mlife\Asz\MetafilterpropTable::deleterow($ID);
					$ID_new = $ID;
				}else{
					$ID_new = $res->getId();
				}
					
				if(!empty($CATEGORYID)){
					foreach($CATEGORYID as $cat){
						$addAr = array("ID"=>$ID_new,"CATID"=>$cat);
						\Mlife\Asz\MetafiltercatTable::add($addAr);
					}
				}
				if(!empty($PROPERTYID)){
					foreach($PROPERTYID as $cat){
						$addAr = array("ID"=>$ID_new,"PROPID"=>$cat);
						\Mlife\Asz\MetafilterpropTable::add($addAr);
					}
				}
				
				if ($_REQUEST['apply'] != "" && $ID>0){
					LocalRedirect("mlife_asz_metafilter_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
				}
				elseif ($_REQUEST['apply'] != ""){
					LocalRedirect("mlife_asz_metafilter_edit.php?ID=".$ID_new."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
				}
				else{
					LocalRedirect("mlife_asz_metafilter.php?lang=".LANG);
				}
			}
		}else{
			$bVarsFromForm = true;
		}
	}
}

$str_SORT = "500";
$str_TEMPLATE_TITLE = "";
$str_TEMPLATE_KEY = "";
$str_TEMPLATE_DESC = "";
$str_TEMPLATE_NAME = "";
$str_TEMPLATE_TEXT = "";
$str_CATEGORYID = array();
$str_PROPERTYID = array();

if($ID>0)
{
	$dataAr = \Mlife\Asz\MetafilterTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$iblockId = $dataAr["IBLOCKID"];
		$str_SORT = $dataAr["SORT"];
		$str_TEMPLATE_TITLE = $dataAr["TEMPLATE_TITLE"];
		$str_TEMPLATE_KEY = $dataAr["TEMPLATE_KEY"];
		$str_TEMPLATE_DESC = $dataAr["TEMPLATE_DESC"];
		$str_TEMPLATE_NAME = $dataAr["TEMPLATE_NAME"];
		$str_TEMPLATE_TEXT = $dataAr["TEMPLATE_TEXT"];
		
		$data2Ob = \Mlife\Asz\MetafiltercatTable::getList(array(
			'select' => array('CATID','ID'),
			'filter' => array("ID"=>$ID),
		));
		while($data2ar = $data2Ob->Fetch()){
			$str_CATEGORYID[$data2ar["CATID"]] = $data2ar["CATID"];
		}
		
		$data2Ob = \Mlife\Asz\MetafilterpropTable::getList(array(
			'select' => array('PROPID','ID'),
			'filter' => array("ID"=>$ID),
		));
		while($data2ar = $data2Ob->Fetch()){
			$str_PROPERTYID[$data2ar["PROPID"]] = $data2ar["PROPID"];
		}
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_METAFILTEREL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_METAFILTEREL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_METAFILTEREL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_METAFILTEREL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_metafilter_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_METAFILTEREL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_METAFILTEREL_SAVED"), "TYPE"=>"OK"));
  
if(count($errorAr)>0){
	CAdminMessage::ShowMessage(implode(', ',$errorAr));
}

$arCategory = array();
$arProps = array();

if($iblockId>0) {
	
	$rsSect = \CIBlockSection::GetList(array('left_margin' => 'asc'),array('IBLOCK_ID' => $iblockId));
	while ($arSect = $rsSect->GetNext()){
	  $arCategory[$arSect["ID"]] = str_replace(array(1,2,3,4,5),array("-","--","---","----","-----"),$arSect["DEPTH_LEVEL"]).' ['.$arSect["ID"].'] - '.$arSect["NAME"];
	}
	
	$rsProp = \CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("IBLOCK_ID"=>$iblockId, "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"]=="N" || $arr["PROPERTY_TYPE"]=="L")
			$arProps[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	}
	
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
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_FILTEREL_PARAM_IBLOCK")?></td>
		<td width="60%">
			<select name="iblockid" onChange="location = '<?echo $APPLICATION->GetCurPage()?>?ID=<?=$ID?>&iblock='+this.value;">
				<option value="0"><?=Loc::getMessage("MLIFE_ASZ_FILTEREL_PARAM_NOIBLOCK")?></option>
				<?foreach($arIblock as $ibId=>$ibname){?>
				<option value="<?=$ibId?>" <?if($iblockId==$ibId){?> selected="selected"<?}?>><?=$ibname?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_FILTEREL_PARAM_CATEGORY")?></td>
		<td width="60%">
			<select name="category[]" multiple="multiple" size="20">
				<?foreach($arCategory as $ibId=>$ibname){?>
				<option value="<?=$ibId?>"<?if(in_array($ibId,$str_CATEGORYID)){?> selected="selected"<?}?>><?=$ibname?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_FILTEREL_PARAM_PROPS")?></td>
		<td width="60%">
			<select name="property[]" multiple="multiple" size="20">
				<?foreach($arProps as $ibId=>$ibname){?>
				<option value="<?=$ibId?>"<?if(in_array($ibId,$str_PROPERTYID)){?> selected="selected"<?}?>><?=$ibname?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM_SORT")?></td>
		<td width="60%">
			<input type="text" name="SORT" value="<?=$str_SORT?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM_TEMPLATE_TITLE")?></td>
		<td width="60%">
			<textarea cols="70" type="text" name="TEMPLATE_TITLE"><?=$str_TEMPLATE_TITLE?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM_TEMPLATE_KEY")?></td>
		<td width="60%">
			<textarea cols="70" type="text" name="TEMPLATE_KEY"><?=$str_TEMPLATE_KEY?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM_TEMPLATE_DESC")?></td>
		<td width="60%">
			<textarea cols="70" type="text" name="TEMPLATE_DESC"><?=$str_TEMPLATE_DESC?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM_TEMPLATE_NAME")?></td>
		<td width="60%">
			<textarea cols="70" type="text" name="TEMPLATE_NAME"><?=$str_TEMPLATE_NAME?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_METAFILTEREL_PARAM_TEMPLATE_TEXT")?></td>
		<td width="60%">
			<textarea cols="70" type="text" name="TEMPLATE_TEXT"><?=$str_TEMPLATE_TEXT?></textarea>
		</td>
	</tr>
<?
$tabControl->Buttons(
  array(
    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>"mlife_asz_metafilter.php?lang=".LANG,
    
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

<?echo BeginNote();?>
<?echo Loc::getMessage("MLIFE_ASZ_METAFILTEREL_NOTICE")?>
<?echo EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>