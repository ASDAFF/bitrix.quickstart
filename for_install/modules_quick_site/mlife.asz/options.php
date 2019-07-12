<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$module_id = "mlife.asz";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
$zr = "";
if (! ($MODULE_RIGHT >= "R"))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	
$APPLICATION->SetTitle(GetMessage("MLIFE_ASZ_OPT_TITLE"));

CModule::IncludeModule("mlife.asz");

//список сайтов
$arSites = array();
$obSite = CSite::GetList($by="sort", $order="desc");
while($arResult = $obSite->Fetch()) {
	$arSites[$arResult['ID']] = '['.$arResult['ID'].'] - '.$arResult['NAME'];
}

//группы пользователей
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), array()); 
$arGroups = array();
while($arGroup = $rsGroups->Fetch()){
	$arGroups[$arGroup['ID']] = $arGroup['NAME'];
}

//статусы заказа
$arStatus = array();
$ASZStatus = \Mlife\Asz\OrderStatusTable::getList(
	array(
		'select' => array("ID","NAME","SITEID"),
		'filter' => array("ACTIVE"=>"Y"),
	)
);
while($arData = $ASZStatus->Fetch()){
	$arStatus[$arData["SITEID"]][$arData["ID"]] = $arData["NAME"];
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && $MODULE_RIGHT == "W" && strlen($_REQUEST["Update"]) > 0)
{
	foreach($arSites as $siteid=>$val){
		if($userGroup = $_REQUEST["asz_group_".$siteid]) {
			$baseCheck = \Mlife\Asz\OptionsTable::getList(
				array(
					'select' => array('ID'),
					'filter' => array('CODE'=>'ADMIN','SITEID'=>$siteid),
					'limit' => 1,
				)
			);
			
			if(!$arData = $baseCheck->Fetch()){
				\Mlife\Asz\OptionsTable::add(array(
					"VALUE" => $userGroup,
					"SITEID" => $siteid,
					"CODE" => "ADMIN",
				));
			}else{
				\Mlife\Asz\OptionsTable::update($arData["ID"],array(
					"VALUE" => $userGroup,
					"SITEID" => $siteid,
				));
			}
		}
		
		\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status1", $_REQUEST["asz_status1_".$siteid], $siteid);
		\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status2", $_REQUEST["asz_status2_".$siteid], $siteid);
		\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status3", $_REQUEST["asz_status3_".$siteid], $siteid);
		\Bitrix\Main\Config\Option::set("mlife.asz", "asz_status4", $_REQUEST["asz_status4_".$siteid], $siteid);
	}
}

$gAdmin = \Mlife\Asz\OptionsTable::getList(
	array(
		"select" => array("SITEID","VALUE"),
		"filter" => array("CODE"=>"ADMIN"),
	)
);
$arAdmin = array();
while($resAr = $gAdmin->Fetch()){
	$arAdmin[$resAr["SITEID"]] = $resAr["VALUE"];
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MLIFE_ASZ_OPT_TAB1"), "ICON" => "vote_settings1", "TITLE" => GetMessage("MLIFE_ASZ_OPT_TAB1")),
	array("DIV" => "edit2", "TAB" => GetMessage("MLIFE_ASZ_OPT_TAB2"), "ICON" => "vote_settings2", "TITLE" => GetMessage("MLIFE_ASZ_OPT_TAB2")),
);
$aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("MLIFE_ASZ_OPT_TAB4"), "ICON" => "vote_settings2", "TITLE" => GetMessage("MLIFE_ASZ_OPT_TAB4"));
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>" id="options">
<?
$tabControl = new CAdminTabControl("tabControl", $aTabs,false,true);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>

<?foreach($arSites as $siteid=>$site){?>
<tr>
	<td><?=GetMessage("MLIFE_ASZ_OPT_LABEL_ADMIN")?> <?=$site?></td>
	<td>
		<select id="asz_group_<?=$siteid?>" name="asz_group_<?=$siteid?>">
			<option value=""><?=GetMessage("MLIFE_ASZ_OPT_LABEL_ADMIN_DEFAULT")?></option>
			<?foreach($arGroups as $groupid=>$groupname){?>
				<option value="<?=$groupid?>"<?if(isset($arAdmin[$siteid]) && ($arAdmin[$siteid]==$groupid)){?> selected="selected"<?}?>><?=$groupname?></option>
			<?}?>
		</select>
	</td>
</tr>
<?}?>

<?
$tabControl->BeginNextTab();
?>
<?foreach($arSites as $siteid=>$site){?>
<tr class="heading"><td colspan="2"><?=$site?></td></tr>
<tr>
	<td><?=GetMessage("MLIFE_ASZ_OPT_PARAM1")?></td>
	<td>
	<?$valOpt = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status1", "0", $siteid);?>
		<select name="asz_status1_<?=$siteid?>">
			<option value="0"<?if($valOpt==0){?> selected="selected"<?}?>><?=GetMessage("MLIFE_ASZ_OPT_PARAM2")?></option>
			<?if(is_array($arStatus[$siteid])){?>
			<?foreach($arStatus[$siteid] as $key=>$val){?>
				<option value="<?=$key?>"<?if($valOpt==$key){?> selected="selected"<?}?>><?=$val?></option>
			<?}?>
			<?}?>
		</select>
	</td>
</tr>
<tr>
	<td><?=GetMessage("MLIFE_ASZ_OPT_PARAM3")?></td>
	<td>
	<?$valOpt = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status2", "0", $siteid);?>
		<select name="asz_status2_<?=$siteid?>">
			<option value="0"<?if($valOpt==0){?> selected="selected"<?}?>><?=GetMessage("MLIFE_ASZ_OPT_PARAM3")?></option>
			<?if(is_array($arStatus[$siteid])){?>
			<?foreach($arStatus[$siteid] as $key=>$val){?>
				<option value="<?=$key?>"<?if($valOpt==$key){?> selected="selected"<?}?>><?=$val?></option>
			<?}?>
			<?}?>
		</select>
	</td>
</tr>
<tr>
	<td><?=GetMessage("MLIFE_ASZ_OPT_PARAM4")?></td>
	<td>
	<?$valOpt = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status3", "0", $siteid);?>
		<select name="asz_status3_<?=$siteid?>">
			<option value="0"<?if($valOpt==0){?> selected="selected"<?}?>><?=GetMessage("MLIFE_ASZ_OPT_PARAM2")?></option>
			<?if(is_array($arStatus[$siteid])){?>
			<?foreach($arStatus[$siteid] as $key=>$val){?>
				<option value="<?=$key?>"<?if($valOpt==$key){?> selected="selected"<?}?>><?=$val?></option>
			<?}?>
			<?}?>
		</select>
	</td>
</tr>
<tr>
	<td><?=GetMessage("MLIFE_ASZ_OPT_PARAM5")?></td>
	<td>
	<?
	$valOpt = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status4", "0", $siteid);
	?>
		<select name="asz_status4_<?=$siteid?>">
			<?if(is_array($arStatus[$siteid])){?>
			<?foreach($arStatus[$siteid] as $key=>$val){?>
				<option value="<?=$key?>"<?if($key==$valOpt){?> selected="selected"<?}?>><?=$val?></option>
			<?}?>
			<?}?>
		</select>
	</td>
</tr>
<?}?>

<?
$tabControl->BeginNextTab();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
$tabControl->Buttons();
?>
	<input <?if ($MODULE_RIGHT<"W") echo "disabled" ?> type="submit" class="adm-btn-green" name="Update" value="<?=GetMessage("MLIFE_ASZ_OPT_SEND")?>" />
	<input type="hidden" name="Update" value="Y" />
<?$tabControl->End();
?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?> 