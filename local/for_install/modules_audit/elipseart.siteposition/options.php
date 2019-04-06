<?
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "elipseart.siteposition";

$STAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($STAT_RIGHT < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

CModule::IncludeModule($module_id);

$ssDB = CEASitePositionSearchSystem::GetList(array(),array("ACTIVE"=>"Y"));
while($res = $ssDB->Fetch())
{
	$arSearchSystem[$res["NAME"]] = GetMessage("GRAPH_SS_".$res["NAME"]);
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arOPTIONS = array(
	"TAB1" => array(
		//array("MAX_REQUEST_DAY", GetMessage("MAX_REQUEST_DAY"), array("text", 5)),
		array("INTERVAL", GetMessage("INTERVAL"), array("text", 5), "int"),
		array("LIMIT", GetMessage("LIMIT"), array("text", 5), "int"),
		array("TIME_FROM", GetMessage("TIME_FROM"), array("text", 5)),
		array("TIME_TO", GetMessage("TIME_TO"), array("text", 5)),
		array("GRAPH_WEIGHT", GetMessage("GRAPH_WEIGHT"), array("text", 5), "int"),
		array("GRAPH_HEIGHT", GetMessage("GRAPH_HEIGHT"), array("text", 5), "int"),
		array("GRAPH_TYPE", GetMessage("GRAPH_TYPE"), array("select", array("STD"=>GetMessage("GRAPH_TYPE_STD"),"TOP10"=>GetMessage("GRAPH_TYPE_TOP10")))),
		array("GRAPH_SS", GetMessage("GRAPH_SS"), array("select", $arSearchSystem, "int")),
		array("LIST_TOP_SIZE", GetMessage("LIST_TOP_SIZE"), array("select", array("1"=>1,"2"=>2,"3"=>3,"4"=>4,"5"=>5,"6"=>6,"7"=>7,"8"=>8,"9"=>9,"10"=>10), "int")),
	),
	"TAB1_1" => array(
		array("YANDEX_LOGIN", GetMessage("YANDEX_LOGIN"), array("text", 40)),
		array("YANDEX_KEY", GetMessage("YANDEX_KEY"), array("text", 40)),
	),
	"TAB1_2" => array(
		array("GOOGLE_KEY", GetMessage("GOOGLE_KEY"), array("textM", 40)),
	),
	"TAB1_3" => array(
		array("BING_KEY", GetMessage("BING_KEY"), array("text", 40)),
	),
);

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["Update"].$_POST["Apply"].$_POST["RestoreDefaults"] <> '' && check_bitrix_sessid())
{
	if(strlen($RestoreDefaults)>0)
	{
		COption::RemoveOption($module_id);
		/*
		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
		*/
	}
	else
	{
		foreach($arOPTIONS as $arOp)
		{
				foreach($arOp as $arOption)
				{
					if (is_array($arOption))
					{
						$type = $arOption[2][0];
						$type2 = $arOption[2][2];
						
						if($type=="textM")
						{
							$count_name = $arOption[0]."_COUNT";
							$count = $$count_name;
							
							for($x=0;$x<$count;++$x)
							{
								$ar["NAME"] = $arOption[0]."_".$x;
								$ar["VALUE"] = $$ar["NAME"];
								if(!empty($ar["VALUE"]))
									$arTextM[] = $ar;
							}
							
							foreach($arTextM as $key=>$val)
							{
								$name = $arOption[0]."_".$key;
								COption::SetOptionString($module_id, $name, $val["VALUE"]);
							}
							
							$nCount = $count;
							
							for($x=0;$x<($count-count($arTextM));++$x)
							{
								echo $arOption[0]."_".($count - ($x + 1))."<br>";
								$name = $arOption[0]."_".($count - ($x + 1));
								COption::RemoveOption($module_id, $name);
								--$nCount;
							}
							
							$count = $nCount;
							
							$name_new = $arOption[0]."_NEW";
							$val_new = $$name_new;
							
							$name = $arOption[0]."_".$count;
							
							if(!empty($val_new))
							{
								COption::SetOptionString($module_id, $name, $val_new);
								COption::SetOptionString($module_id, $count_name, ++$count);
							}
							else
							{
								COption::SetOptionString($module_id, $count_name, $count);
							}
						}
						else
						{
							$name=$arOption[0];
							$val=$$name;
							
							if($type=="checkbox" && $val!="Y")
								$val="N";
							
							if($name == "GRAPH_TYPE" && ($val != "STD" && $val != "TOP10"))
								$val = "STD";
							
							if($name == "LIST_TOP_SIZE" && $val > 10)
								$val = 10;
							if($name == "LIST_TOP_SIZE" && $val < 1)
								$val = 1;
							
							if($name == "TIME_FROM" && !preg_match('/^([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/', $val))
								$strError[]["text"] = GetMessage("TIME_FROM_ERROR");
							
							if($name == "TIME_TO" && !preg_match('/^([01]?[0-9]|2[0-3])(:|\.)[0-5][0-9]$/', $val))
								$strError[]["text"] = GetMessage("TIME_TO_ERROR");
							
							if($type2 == "int")
								$val = intval($val);
							
							if($strError == "")
							{
								COption::SetOptionString($module_id, $name, $val);
								if (${$name."_clear"}=="Y")
								{
									$func=$arOption[3];
									eval($func);
								}
							}
						}
					}
				}
		}
		
		CAgent::RemoveAgent("CEASitePositionUpdate::Update();",$module_id);
		CAgent::AddAgent("CEASitePositionUpdate::Update();", $module_id, "N", COption::GetOptionString($module_id, "INTERVAL"), "", "Y");

	}
	
	//ob_start();
	$Update = $_POST["Update"].$_POST["Apply"];
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	//ob_end_clean();
	
	if($strError == "")
	{
		if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
			LocalRedirect($_REQUEST["back_url_settings"]);
		else
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
	}
	
}

if(count($strError) > 0)
{
	$e = new CAdminException($strError);
	$GLOBALS["APPLICATION"]->ThrowException($e);
	$message = new CAdminMessage(GetMessage("FORM_ERROR_SAVE"), $e);
	echo $message->Show();
}

$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
<?=bitrix_sessid_post();?>
<?
	$tabControl->BeginNextTab();
	foreach($arOPTIONS["TAB1"] as $key => $Option)
	{
		if($Option[0] != "TIME_TO")
		{
			$val = COption::GetOptionString($module_id, $Option[0]);
			$type = $Option[2];
			?>
			<tr>
				<td valign="top" width="50%"><label for="<?echo htmlspecialchars($Option[0])?>"><?echo $Option[1]?>:</label></td>
				<td valign="top" nowrap width="50%">
					<?if($type[0]=="checkbox"):?>
						<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
					<?elseif($type[0]=="text"):?>
						<?if($Option[0] == "TIME_FROM"):
						
							echo GetMessage("TIME_FROM_FROM")." ";
							
							$timeFrom = $val;
							if(empty($timeFrom))
								$timeFrom = "00:00";
							
							$APPLICATION->IncludeComponent(
								"bitrix:main.clock",
								"",
								Array(
									"INPUT_ID" => "",
									"INPUT_NAME" => $Option[0],
									"INPUT_TITLE" => GetMessage("TIME_FROM_FROM_FULL"),
									"INIT_TIME" => $timeFrom,
									"STEP" => "0"
								),
							false
							);
							
							echo " ".GetMessage("TIME_TO")." ";
							
							$timeFrom = COption::GetOptionString($module_id, "TIME_TO");
							if(empty($timeFrom))
								$timeFrom = "00:00";
							
							$APPLICATION->IncludeComponent(
								"bitrix:main.clock",
								"",
								Array(
									"INPUT_ID" => "",
									"INPUT_NAME" => "TIME_TO",
									"INPUT_TITLE" => GetMessage("TIME_TO_FULL"),
									"INIT_TIME" => $timeFrom,
									"STEP" => "0"
								),
							false
							);
							
						else:?>
							<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
						<?endif;?>
					<?elseif($type[0]=="textarea"):?>
						<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
					<?elseif($type[0]=="select"):?>
						<select name="<?echo htmlspecialchars($Option[0])?>">
						<?foreach($type[1] as $optKey=>$optVal) {
							if($val == $optKey)
								$selected = "selected";
							else
								$selected = "";
							?><option value="<?=htmlspecialchars($optKey)?>" <?=$selected;?>><?=htmlspecialchars($optVal)?></option><?
						}?>
						</select>
					<?endif;?>
				</td>
			</tr>
			<?
		}
	}
	?>
	
	<tr class="heading">
		<td align="center" colspan="2" nowrap><?=GetMessage("YANDEX_SETUP")?></td>
	</tr>
	
	<?foreach($arOPTIONS["TAB1_1"] as $key => $Option):
		$val = COption::GetOptionString($module_id, $Option[0]);
		$type = $Option[2];
		?>
		<tr>
			<td valign="top" width="50%"><label for="<?echo htmlspecialchars($Option[0])?>"><?echo $Option[1]?>:</label></td>
			<td valign="top" nowrap width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
				<?elseif($type[0]=="textarea"):?>
					<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
				<?endif;?>
			</td>
		</tr>
	<?endforeach;?>
	
	<tr>
		<td colspan="2" align="center">	
		<?echo BeginNote();?>
		<?=GetMessage("YANDEX_NOTE")?>
		<?echo EndNote();?>
		</td>
	</tr>
	
	<tr class="heading">
		<td align="center" colspan="2" nowrap><?=GetMessage("GOOGLE_SETUP")?></td>
	</tr>
	
	<?foreach($arOPTIONS["TAB1_2"] as $key => $Option):
		
		$type = $Option[2];
		
		if($type[0]=="textM")
		{
			$val = array();
			$paramCount = COption::GetOptionString($module_id, $Option[0]."_COUNT");
			for($x=0;$x<$paramCount;++$x)
			{
				$val[$x] = COption::GetOptionString($module_id, $Option[0]."_".$x);
			}
		}
		else
			$val = COption::GetOptionString($module_id, $Option[0]);
		
		?>
		<tr>
			<td valign="top" width="50%"><label for="<?echo htmlspecialchars($Option[0])?>"><?echo $Option[1]?>:</label></td>
			<td valign="top" nowrap width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
				<?elseif($type[0]=="textarea"):?>
					<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
				<?elseif($type[0]=="textM"):
					for($x=0;$x<$paramCount;++$x)
					{
						?><input name="<?echo /*htmlspecialchars(*/$Option[0]/*)*/?>_<?=$x?>" type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo /*htmlspecialchars(*/$val[$x]/*)*/?>"><br /><?
					}
					?><input name="<?echo /*htmlspecialchars(*/$Option[0]/*)*/?>_COUNT" type="hidden" value="<?echo count($val)?>"><?
					?><input name="<?echo /*htmlspecialchars(*/$Option[0]/*)*/?>_NEW" type="text" size="<?echo $type[1]?>" maxlength="255" value="<?/*echo htmlspecialchars($val)*/?>"><?
				endif;?>
			</td>
		</tr>
	<?endforeach;?>
	
	<tr>
		<td colspan="2" align="center">	
		<?echo BeginNote();?>
		<?=GetMessage("GOOGLE_NOTE")?>
		<?echo EndNote();?>
		</td>
	</tr>
	
	<tr class="heading">
		<td align="center" colspan="2" nowrap><?=GetMessage("BING_SETUP")?></td>
	</tr>
	
	<?foreach($arOPTIONS["TAB1_3"] as $key => $Option):
		$val = COption::GetOptionString($module_id, $Option[0]);
		$type = $Option[2];
		?>
		<tr>
			<td valign="top" width="50%"><label for="<?echo htmlspecialchars($Option[0])?>"><?echo $Option[1]?>:</label></td>
			<td valign="top" nowrap width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>>
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>">
				<?elseif($type[0]=="textarea"):?>
					<textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea>
				<?endif;?>
			</td>
		</tr>
	<?endforeach;?>
	
	<tr>
		<td colspan="2" align="center">	
		<?echo BeginNote();?>
		<?=GetMessage("BING_NOTE")?>
		<?echo EndNote();?>
		</td>
	</tr>
	
	<?
	$tabControl->EndTab();
	
	$tabControl->BeginNextTab();
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");

$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
