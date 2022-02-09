<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult["NEED_AUTH"] == "Y")
	$APPLICATION->AuthForm("");
elseif (strlen($arResult["FatalError"]) > 0)
{
	?><span class='errortext'><?=$arResult["FatalError"]?></span><br /><br /><?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?><span class='errortext'><?=$arResult["ErrorMessage"]?></span><br /><br /><?
	}

	if ($arResult["ShowForm"] == "Input")
	{
		?><script language="JavaScript">
		<!--
			function SoNetSwitchBody(chk, type)
			{
				var el = document.getElementById(type + "_body");
				if (el)
				{
					if (chk)
						el.style.display = "";
					else
						el.style.display = "none";
				}
				
				var el = document.getElementById(type + "_lbl");
				if (el)
				{
					if (chk)
						el.innerHTML = BX.message('sonetF_' + type + '_on');
					else
						el.innerHTML = BX.message('sonetF_' + type + '_off');
				}
			}
		//-->
		</script>
		<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<?foreach ($arResult["Features"] as $feature => $arFeature):?>
				<table class="sonet-message-form data-table" cellspacing="0" cellpadding="0">
					<tr>
						<th colspan="2">
						<script language="JavaScript">
						<!--
							BX.message({
								sonetF_<?=$feature?>_on: '<?=CUtil::JSEscape(str_replace("#NAME#", (array_key_exists("title", $GLOBALS["arSocNetFeaturesSettings"][$feature]) && strlen($GLOBALS["arSocNetFeaturesSettings"][$feature]["title"]) > 0 ? $GLOBALS["arSocNetFeaturesSettings"][$feature]["title"] : GetMessage("SONET_FEATURES_".$feature)) , GetMessage("SONET_C4_FUNC_TITLE_ON")))?>',
								sonetF_<?=$feature?>_off: '<?=CUtil::JSEscape(str_replace("#NAME#", (array_key_exists("title", $GLOBALS["arSocNetFeaturesSettings"][$feature]) && strlen($GLOBALS["arSocNetFeaturesSettings"][$feature]["title"]) > 0 ? $GLOBALS["arSocNetFeaturesSettings"][$feature]["title"] : GetMessage("SONET_FEATURES_".$feature)) , GetMessage("SONET_C4_FUNC_TITLE_OFF")))?>'
							});	
						//-->
						</script>
						<?if(!($feature == "blog" && $arParams["PAGE_ID"] != "group_features"))
						{
							?><input type="checkbox" id="<?= $feature ?>_active_id" name="<?= $feature ?>_active" value="Y"<?= ($arFeature["Active"] ? " checked" : "") ?> onclick="SoNetSwitchBody(this.checked, '<?= $feature ?>')"> <?
						}?>
						<label for="<?= $feature ?>_active_id" id="<?= $feature ?>_lbl"><?= str_replace("#NAME#", (array_key_exists("title", $GLOBALS["arSocNetFeaturesSettings"][$feature]) && strlen($GLOBALS["arSocNetFeaturesSettings"][$feature]["title"]) > 0 ? $GLOBALS["arSocNetFeaturesSettings"][$feature]["title"] : GetMessage("SONET_FEATURES_".$feature)) , GetMessage("SONET_C4_FUNC_TITLE_".($arFeature["Active"] ? "ON" : "OFF"))) ?></label>
						</th>
					</tr>
					<tbody id="<?= $feature ?>_body"<?if(!$arFeature["Active"]):?> style="display:none;"<?endif?>>
						<tr>
							<td valign="top" align="right" width="50%"><?= GetMessage("SONET_FEATURES_NAME") ?>:</td>
							<td valign="top" width="50%">
								<input type="text" style="width:300px" name="<?= $feature ?>_name" value="<?= $arFeature["FeatureName"] ?>">
							</td>
						</tr>
						<? if (isset($arFeature['note'])) { ?>
							<tr>
								<td>
								</td>
								<td>
									<div style="border:1px solid #ffc34f; background: #fffdbe;padding:1em;">
										<?=htmlspecialcharsbx($arFeature['note'])?>
									</div>
								</td>
							</tr>
						<? } ?>
						<?
						if (!array_key_exists("hide_operations_settings", $GLOBALS["arSocNetFeaturesSettings"][$feature]) || !$GLOBALS["arSocNetFeaturesSettings"][$feature]["hide_operations_settings"])
						{
							foreach ($arFeature["Operations"] as $operation => $perm):
								if (
									$feature == "tasks"
									&& (
										$operation == "modify_folders" 
										|| $operation === 'modify_common_views'
										)
									&& COption::GetOptionString("intranet", "use_tasks_2_0", "N") == "Y"
								):
									?><input type="hidden" name="<?= $feature ?>_<?= $operation ?>_perm" value="<?=$perm?>"><?
								else:
									?><tr>
										<td valign="top" align="right" width="50%"><?=(array_key_exists("operation_titles", $GLOBALS["arSocNetFeaturesSettings"][$feature]) && array_key_exists($operation, $GLOBALS["arSocNetFeaturesSettings"][$feature]["operation_titles"]) && strlen($GLOBALS["arSocNetFeaturesSettings"][$feature]["operation_titles"][$operation]) > 0 ? $GLOBALS["arSocNetFeaturesSettings"][$feature]["operation_titles"][$operation] : GetMessage("SONET_FEATURES_".$feature."_".$operation))?>:</td>
										<td valign="top" width="50%">
											<select style="width:300px" name="<?= $feature ?>_<?= $operation ?>_perm">
												<?foreach ($arResult["PermsVar"] as $key => $value):
													if (
														!array_key_exists("restricted", $GLOBALS["arSocNetFeaturesSettings"][$feature]["operations"][$operation]) 
														|| !in_array($key, $GLOBALS["arSocNetFeaturesSettings"][$feature]["operations"][$operation]["restricted"][$arResult["ENTITY_TYPE"]])
													):
														?><option value="<?= $key ?>"<?= ($key == $perm) ? " selected" : "" ?>><?= $value ?></option><?
													endif;
												endforeach;?>
											</select>
										</td>
									</tr><?
								endif;
							endforeach;
						}
						?>
					</tbody>
				</table>
				<br><br>
			<?endforeach;?>
			<input type="hidden" name="SONET_USER_ID" value="<?= $arParams["USER_ID"] ?>">
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arParams["GROUP_ID"] ?>">
			<?=bitrix_sessid_post()?>
			<br />
			<input type="submit" name="save" value="<?= GetMessage("SONET_C4_SUBMIT") ?>">
			<input type="reset" name="cancel" value="<?= GetMessage("SONET_C4_T_CANCEL") ?>" OnClick="window.location='<?= (($arParams["PAGE_ID"] == "group_features") ? $arResult["Urls"]["Group"] : $arResult["Urls"]["User"]) ?>'">
		</form>
		<?
	}
	else
	{
		if ($arParams["PAGE_ID"] == "group_features"):?>
			<?= GetMessage("SONET_C4_GR_SUCCESS") ?>
			<br><br>
			<a href="<?= $arResult["Urls"]["Group"] ?>"><?= $arResult["Group"]["NAME"]; ?></a><?
		else:
			?><?= GetMessage("SONET_C4_US_SUCCESS") ?>
			<br><br>
			<a href="<?= $arResult["Urls"]["User"] ?>"><?= $arResult["User"]["NAME_FORMATTED"]; ?></a><?
		endif;
	}
}
?>
