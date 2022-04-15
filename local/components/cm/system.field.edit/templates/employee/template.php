<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['CAN_EDIT_USER'] = false;
if ($USER->CanDoOperation('edit_all_users'))
{
	$arResult['CAN_EDIT_USER'] = true;
}
if (!$arResult['CAN_EDIT_USER'])
{
	$APPLICATION->IncludeComponent(
		"bitrix:system.field.view",
		$arParams["arUserField"]["USER_TYPE"]["USER_TYPE_ID"],
		array("arUserField" => $arParams["arUserField"]), null, array("HIDE_ICONS"=>"Y")
	);
	return;
}

$name = $arParams["arUserField"]["~FIELD_NAME"];
$namex = preg_replace("/([^a-z0-9])/is", "x", $name);
?>
<table id="table_<?=$name?>" width="100%" cellpadding="0" cellspacing="0">
<?
foreach ($arResult["VALUE"] as $i => $ID)
{
	$name_c = ($arParams["arUserField"]["MULTIPLE"] == "Y"? $name.'['.$i.']' : $name);
	$name_x = preg_replace("/([^a-z0-9])/is", "x", $name_c);
?>
	<tr>
		<td>
		<input type="text" name="<?=$name_c?>" id="<?echo $name_x?>" value="<?echo intval($ID) > 0 ? intval($ID) : ''?>" size="3" class="typeinput" />
		<?
		$GLOBALS['APPLICATION']->IncludeComponent(
			'bitrix:intranet.user.search',
			'',
			array(
				'INPUT_NAME' => $name_x,
				'MULTIPLE' => 'N',
				'SHOW_BUTTON' => 'Y'
			),
			null,
			array('HIDE_ICONS' => 'Y')
		)
?>
		<IFRAME style="width:0px; height:0px; border: 0px; display: none;" src="javascript:void(0)" name="hiddenframe<?=$name_c?>" id="hiddenframe<?=$name_x?>"></IFRAME>
		<br /><span id="div_<?=$name_x?>"></span>

		<script>
		var value_<?=$name_x?> = '';
		function Ch<?=$name_x?>()
		{
			var DV_<?=$name_x?> = document.getElementById("div_<?=$name_x?>");
			if (document.getElementById('<?echo $name_x?>'))
			{
				var old_value = value_<?=$name_x?>;
				value_<?=$name_x?>=parseInt(document.getElementById('<?echo $name_x?>').value);
				if (value_<?=$name_x?> > 0)
				{
					if (old_value != value_<?=$name_x?>)
					{
						DV_<?=$name_x?>.innerHTML = '<i><? echo CUtil::JSEscape(GetMessage("MAIN_WAIT"))?></i>';
						if (value_<?=$name_x?> != <?echo intVal($USER->GetID())?><?$bAllowCurrentUser ? '' : ' || true'?>)
						{
							document.getElementById("hiddenframe<?=$name_x?>").src='/bitrix/admin/get_user.php?ID=' + value_<?=$name_x?>+'&strName=<?=$name_x?>&lang=<? echo LANG.(defined("ADMIN_SECTION") && ADMIN_SECTION===true?"&admin_section=Y":"")?>';
						}
						else
						{
							DV_<?=$name_x?>.innerHTML = '[<a title="<?echo CUtil::JSEscape(GetMessage("MAIN_EDIT_USER_PROFILE"))?>" class="tablebodylink" href="/bitrix/admin/user_edit.php?ID=<?echo $USER->GetID()?>&lang=<?echo LANG?>"><?echo $USER->GetID()?></a>] (<?echo CUtil::JSEscape(htmlspecialcharsbx($USER->GetLogin()))?>) <? echo CUtil::JSEscape(htmlspecialcharsbx($USER->GetFirstName().' '.$USER->GetLastName()))?>';
						}
					}

				}
				else
				{
					DV_<?=$name_x?>.innerHTML = '';
				}
			}
			setTimeout(function(){Ch<?=$name_x?>()},1000);
		}
		Ch<?=$name_x?>();
		//-->
		</script>
		</td>
	</tr>
<?
}
?>
<?
if($arParams["arUserField"]["MULTIPLE"] == "Y"):
?>
<tr>
	<td>
		<?echo CAllUserTypeManager::ShowScript();?>
		<input type="button" value="<?=GetMessage("USER_TYPE_PROP_ADD")?>" onClick="addNewRow('table_<?=$name?>', /(<?=$name?>|<?=$name?>_old_id|<?=$namex?>)[x\[]([0-9]*)[x\]]/gi, 2)">
	</td>
</tr>
<?
endif; //multiple
?>
<script type="text/javascript">
	BX.addCustomEvent('onAutoSaveRestore',
	function(ob, data)
	{
		for (var i in data)
		{
			if (i.substring(0,<?=(strlen($name)+1)?>)=='<?=CUtil::JSEscape($name)?>[')
			{
				addNewRow('table_<?=$name?>', /(<?=$name?>|<?=$name?>_old_id|<?=$name_x?>)\[([0-9]*)\]/g, 2)
			}
		}
	})
</script>
</table>

