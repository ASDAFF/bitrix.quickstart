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


$arType = array(
	"1" => Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TYPE1"),
	"2" => Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TYPE2"),
	"3" => Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TYPE3"),
);
?>
<?
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($_REQUEST['ID']);
$message = null;
$bVarsFromForm = false;
$bVarsShowForm = true;

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid()){
  
	$IBLOCK_ID = $iblockId;
	$CATEGORY_ID = intval($_REQUEST["category"]) ? intval($_REQUEST["category"]) : false;
	$PRODUCT_ID = intval($_REQUEST["TOVAR"]) ? intval($_REQUEST["TOVAR"]) : false;
	
	if(intval($_REQUEST["TYPETOVAR"])==1) {
		$CATEGORY_ID = 0;
	}else{
		$PRODUCT_ID = null;
	}
	
	$TIP = intval($_REQUEST["TIP"]);
	$PRIOR = intval($_REQUEST["PRIOR"]);
	$NAME = trim($_REQUEST["NAME"]);
	$DESC = trim($_REQUEST["DESC"]);
	$VALUE = doubleval($_REQUEST["VALUE"]);
	$DATE_START = \Bitrix\Main\Type\DateTime::createFromUserTime($_REQUEST["DATE_START"]);
	$DATE_END = \Bitrix\Main\Type\DateTime::createFromUserTime($_REQUEST["DATE_END"]);
	$MAXSUMM = doubleval($_REQUEST["MAXSUMM"]);
	$ACTIVE = ($_REQUEST["ACTIVE"]=="Y") ? "Y" : "N";
	$PRIORFIX = ($_REQUEST["PRIORFIX"]=="Y") ? "Y" : "N";
	$GROUPS = (is_array($_REQUEST["GROUPID"])) ? $_REQUEST["GROUPID"] : array();

	$arFields = Array(
		"IBLOCK_ID" => $IBLOCK_ID,
		"CATEGORY_ID" => $CATEGORY_ID,
		"PRODUCT_ID" => $PRODUCT_ID,
		"TIP" => $TIP,
		"PRIOR" => $PRIOR,
		"NAME" => $NAME,
		"DESC" => $DESC,
		"VALUE" => $VALUE,
		"DATE_START" => $DATE_START,
		"DATE_END" => $DATE_END,
		"MAXSUMM" => $MAXSUMM,
		"ACTIVE" => $ACTIVE,
		"PRIORFIX" => $PRIORFIX,
		"GROUPS" => $GROUPS,
	);
	
	$noupdate = false;
	
	// сохранение данных
	if($ID > 0){
		if(!$noupdate)
			$res = Asz\DiscountTable::update($ID,$arFields);
	}
	else{
		if(!$noupdate)
			$res = Asz\DiscountTable::add($arFields);
	}
	
	if(!$noupdate){
		if($res->isSuccess() && count($errorAr)==0){
			if ($_REQUEST['apply'] != "" && $ID>0){
				LocalRedirect("mlife_asz_discount_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			elseif ($_REQUEST['apply'] != ""){
				LocalRedirect("mlife_asz_discount_edit.php?ID=".$res->getId()."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
			}
			else{
				LocalRedirect("mlife_asz_discount.php?lang=".LANG);
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

$str_CATEGORY = "";
$str_TOVAR = "";
$str_NAME = "";
$str_DESC = "";
$str_ACTIVE = "N";
$str_TIP = "1";
$str_VALUE = "0.00";
$str_MAXSUMM = "0.00";
$str_PRIOR = "1";
$str_PRIORFIX = "Y";
$str_DATE_START = ConvertTimeStamp(false,"FULL");
$str_DATE_END = ConvertTimeStamp(false,"FULL");
$str_GROUPID = array();
$str_TOVAR = (intval($_REQUEST["tovar"])>0) ? intval($_REQUEST["tovar"]) : "";

if($ID>0)
{
	$dataAr = Asz\DiscountTable::getRowById($ID);
	
	if(is_array($dataAr)){
		$str_CATEGORY = $dataAr["CATEGORY_ID"];
		$str_TOVAR = $dataAr["PRODUCT_ID"];
		$str_NAME = $dataAr["NAME"];
		$str_DESC = $dataAr["DESC"];
		$str_ACTIVE = $dataAr["ACTIVE"];
		$str_TIP = $dataAr["TIP"];
		$str_VALUE = $dataAr["VALUE"];
		$str_MAXSUMM = $dataAr["MAXSUMM"];
		$str_PRIOR = $dataAr["PRIOR"];
		$str_PRIORFIX = $dataAr["PRIORFIX"];
		$str_DATE_START = $dataAr["DATE_START"];
		$str_DATE_END = $dataAr["DATE_END"];
		$str_GROUPID = $dataAr["GROUPS"];
		//$str_TOVAR = $dataAr["TOVAR"];
		if(!$iblockId) $iblockId = $dataAr["IBLOCK_ID"];
		
		$bVarsFromForm = true;
		
	}else{
		$errorAr[] = Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_ERROR_ID");
		$bVarsShowForm = false;
	}

}else{
	
	$bVarsShowForm = true;
	
}

$APPLICATION->SetTitle(($ID>0? Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_EDIT").$ID : Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_ADD")));

?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aContext = array(
  array(
    "TEXT"=> Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_ADD_CERENCY"),
    "LINK"=> "mlife_asz_discount_edit.php?lang=".LANG,
    "TITLE"=> Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_ADD_CERENCY"),
    "ICON"=> "btn_new",
  ),
);

$context = new CAdminContextMenu($aContext);

$context->Show();

if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_SAVED"), "TYPE"=>"OK"));
  
if(count($errorAr)>0){
	CAdminMessage::ShowMessage(implode(', ',$errorAr));
}

$arCategory = array();

if($iblockId>0) {
	
	$rsSect = \CIBlockSection::GetList(array('left_margin' => 'asc'),array('IBLOCK_ID' => $iblockId));
	while ($arSect = $rsSect->GetNext()){
	  $arCategory[$arSect["ID"]] = str_replace(array(1,2,3,4,5),array("-","--","---","----","-----"),$arSect["DEPTH_LEVEL"]).' ['.$arSect["ID"].'] - '.$arSect["NAME"];
	}
	
}

?>
<?if($bVarsShowForm){?>
<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?echo bitrix_sessid_post();?>
<input type="hidden" name="lang" value="<?=LANG?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<?
$valType = 2;
if($str_TOVAR) $valType = 1;
\CUtil::InitJSCore('jquery');
?>
<script>
	$(document).ready(function(){
		$("#showtovar, #showcategory").css({'display':'none'});
		<?if($valType==1){?>
		$("#showtovar").css({'display':'table-row'});
		<?}elseif($valType==2){?>
		$("#showcategory").css({'display':'table-row'});
		<?}?>
		$(document).on('click','.showtype_cat',function(e){
			e.preventDefault();
			$("#showtovar, #showcategory").css({'display':'none'});
			$("#showcategory").css({'display':'table-row'});
			$("#TYPETOVAR").val("2");
		});
		$(document).on('click','.showtype_prod',function(e){
			e.preventDefault();
			$("#showtovar, #showcategory").css({'display':'none'});
			$("#showtovar").css({'display':'table-row'});
			$("#TYPETOVAR").val("1");
		});
	});
</script>
<input type="hidden" name="TYPETOVAR" id="TYPETOVAR" value="<?=$valType?>">
<?
$tabControl->Begin();
?>
<?
$tabControl->BeginNextTab();
?>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_IBLOCK")?></td>
		<td width="60%">
			<select name="iblockid" onChange="location = '<?echo $APPLICATION->GetCurPage()?>?ID=<?=$ID?>&iblock='+this.value;">
				<option value="0"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_NOIBLOCK")?></option>
				<?foreach($arIblock as $ibId=>$ibname){?>
				<option value="<?=$ibId?>" <?if($iblockId==$ibId){?> selected="selected"<?}?>><?=$ibname?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_SHOWTYPE")?></td>
		<td width="60%">
			<a href="#" class="showtype_cat"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_SHOWTYPE1")?></a> | 
			<a href="#" class="showtype_prod"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_SHOWTYPE2")?></a>
		</td>
	</tr>
	<tr id="showcategory">
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_CATEGORY")?></td>
		<td width="60%">
			<select name="category">
				<option value="0"<?if($str_CATEGORY==null){?> selected="selected"<?}?>><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_NOCATEGORY")?></option>
				<?foreach($arCategory as $ibId=>$ibname){?>
				<option value="<?=$ibId?>"<?if($str_CATEGORY==$ibId){?> selected="selected"<?}?>><?=$ibname?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr id="showtovar">
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TOVAR")?></td>
		<td width="60%">
			<input type="text" name="TOVAR" value="<?=$str_TOVAR?>"/>
			<?if($str_TOVAR){
				$tov = \Mlife\Asz\ElementTable::getById($str_TOVAR)->Fetch();
			}?>
			<?if($tov["NAME"]){?>
				<?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TOVAR_1")?>: <?=$tov["NAME"]?>
			<?}else{?>
				<?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TOVAR_2")?>
			<?}?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_NAME")?></td>
		<td width="60%">
			<input type="text" name="NAME" value="<?=$str_NAME?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_DESC")?></td>
		<td width="60%">
			<textarea type="text" name="DESC"><?=$str_DESC?></textarea>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_ACTIVE")?></td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y"<?if($str_ACTIVE == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_TYPE")?></td>
		<td width="60%">
			<select name="TIP">
				<?foreach($arType as $ibId=>$ibname){?>
				<option value="<?=$ibId?>"<?if($str_TIP==$ibId){?> selected="selected"<?}?>><?=$ibname?></option>
				<?}?>
			</select>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_VALUE")?></td>
		<td width="60%">
			<input type="text" name="VALUE" value="<?=$str_VALUE?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_MAXSUMM")?></td>
		<td width="60%">
			<input type="text" name="MAXSUMM" value="<?=$str_MAXSUMM?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_PRIOR")?></td>
		<td width="60%">
			<input type="text" name="PRIOR" value="<?=$str_PRIOR?>"/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_PRIORFIX")?></td>
		<td width="60%">
			<input type="checkbox" name="PRIORFIX" value="Y"<?if($str_PRIORFIX == "Y") echo " checked"?>/>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_DATE_START")?></td>
		<td width="60%">
			<?=\CAdminCalendar::CalendarDate("DATE_START", $str_DATE_START, 20)?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_DATE_END")?></td>
		<td width="60%">
			<?=\CAdminCalendar::CalendarDate("DATE_END", $str_DATE_END, 20)?>
		</td>
	</tr>
	<tr>
		<td width="40%"><?=Loc::getMessage("MLIFE_ASZ_DISCOUNTEL_PARAM_USERGROUP")?></td>
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
    "back_url"=>"discount.php?lang=".LANG,
    
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