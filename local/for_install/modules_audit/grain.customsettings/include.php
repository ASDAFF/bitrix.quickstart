<?

/*patchvalidationmutatormark1*/

class CGrain_CustomSettingsOptions {

	function IsLinksInstalled() {
	
		//return false;
	
		static $bInstalled;
		
		if($bInstalled===true) {
			return true;
		} elseif($bInstalled===false) {
			return false;
		} else {
			$bInstalled=IsModuleInstalled("grain.links");
			return $bInstalled;
		}
	
	}

	function ShowTab($gks_tab_id,$tab=Array(),$set_variables=true) {
	
		global $APPLICATION;
		global $arLang;
	
		ob_start();
	
	
		?>
		
		<div class="gcustomsettings-settings-tab" id="gks_tab_<?=$gks_tab_id?>">
		
			<table class="gcustomsettings-settings-tab-headers" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_NAME")?></td>
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_HEADER")?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<table cellspacing="0" cellpadding="0">
								<?foreach($arLang as $lang_id => $lang):?>
									<tr>
										<td><?=$lang_id?></td>
										<td><input type="text" name="TABS[<?=$gks_tab_id?>][LANG][<?=$lang_id?>][NAME]" value="<?=htmlspecialchars($tab["LANG"][$lang_id]["NAME"])?>" /></td>
									</tr>
								<?endforeach;?>
							</table>
						</td>
						<td>
							<table cellspacing="0" cellpadding="0">
								<?foreach($arLang as $lang_id => $lang):?>
									<tr>
										<td><?=$lang_id?></td>
										<td><input type="text" name="TABS[<?=$gks_tab_id?>][LANG][<?=$lang_id?>][TITLE]" value="<?=htmlspecialchars($tab["LANG"][$lang_id]["TITLE"])?>" /></td>
									</tr>
								<?endforeach;?>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		
			<?if($set_variables):?>
			<script type="text/javascript">
		
				gks_options[<?=$gks_tab_id?>]=0;
				gks_selectvalues[<?=$gks_tab_id?>]=[];
		
			</script>
			<?endif?>
		
			<div class="gcustomsettings-settings-tab-options" id="gks_tab_options_<?=$gks_tab_id?>">
		
				<?
					$option_count=1;
					foreach($tab["FIELDS"] as $option_data) {
						echo self::ShowOption($gks_tab_id,$option_count,$option_data,true);
						$option_count++;
					}
				?>
		
			</div>
			
			<div class="gcustomsettings-settings-option-add">
				<a href="#" onclick="gksAddOption('<?=$gks_tab_id?>'); return false;"><img src="/bitrix/images/grain.customsettings/gcustomsettings_options_option_icon_add.gif" width="24" height="24" border="0" />&nbsp;&nbsp;<span><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_ADD_OPTION")?></span></a>
			</div>
		
			<a class="gcustomsettings-settings-tab-remove" href="#" onclick="if(confirm('<?echo AddSlashes(GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_REMOVE_TAB_WARNING"))?>')) gksRemoveTab('<?=$gks_tab_id?>'); return false"><img src="/bitrix/images/grain.customsettings/gcustomsettings_options_tab_icon_remove.gif" width="30" height="15" border="0" />&nbsp;&nbsp;<span><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_REMOVE_TAB")?></span></a>
		
		</div>
		
		<?
	
		$s .= ob_get_contents();
		ob_end_clean();
	
		return $s;
	
	}
	

	function ShowOption($gks_tab_id,$gks_option_id,$option=Array(),$set_variables=true) {
	
		global $APPLICATION;
		global $arLang;
	
		ob_start();
	
	
		?>
		
		<div class="gcustomsettings-settings-option" id="gks_option_<?=$gks_tab_id?>_<?=$gks_option_id?>">
		
			<?if($set_variables):?>
			<script type="text/javascript">
		
				gks_options[<?=$gks_tab_id?>]++;
		
			</script>
			<?endif?>
		
		
			<table cellspacing="0" cellpadding="0" class="gcustomsettings-settings-option-table">
				<thead>
					<tr>
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_ID")?></td>		
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_NAME")?></td>
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TOOLTIP")?></td>
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE")?></td>
						<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_CUSTOM")?></td>
						<td>&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][NAME]" value="<?=$option["NAME"]?>" /></td>
						<td>
							<table cellspacing="0" cellpadding="0">
								<?foreach($arLang as $lang_id => $lang):?>
									<tr>
										<td><?=$lang_id?></td>
										<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][LANG][<?=$lang_id?>][NAME]" value="<?=htmlspecialchars($option["LANG"][$lang_id]["NAME"])?>" /></td>
									</tr>
								<?endforeach;?>
							</table>
						</td>
						<td>
							<table cellspacing="0" cellpadding="0">
								<?foreach($arLang as $lang_id => $lang):?>
									<tr>
										<td><?=$lang_id?></td>
										<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][LANG][<?=$lang_id?>][TOOLTIP]" value="<?=htmlspecialchars($option["LANG"][$lang_id]["TOOLTIP"])?>" /></td>
									</tr>
								<?endforeach;?>
							</table>
						</td>
						<td>
							<select name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][TYPE]" onChange="gksOptionChangeType('<?=$gks_tab_id?>','<?=$gks_option_id?>',this);">
								<option value="text"<?if($option["TYPE"]=="text"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_TEXT")?></option>
								<option value="textarea"<?if($option["TYPE"]=="textarea"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_TEXTAREA")?></option>
								<option value="checkbox"<?if($option["TYPE"]=="checkbox"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_CHECKBOX")?></option>
								<option value="select"<?if($option["TYPE"]=="select"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_SELECT")?></option>
								<option value="date"<?if($option["TYPE"]=="date"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_DATE")?></option>
								<option value="link"<?if($option["TYPE"]=="link"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_LINK")?></option>
							</select>
						</td>
						<td width="99%">
							<div class="gcustomsettings-settings-option-custom" id="gks_tab_option_custom_<?=$gks_tab_id?>_<?=$gks_option_id?>">
				
								<?=self::ShowOptionCustom($gks_tab_id,$gks_option_id,$option)?>
				
							</div>
						</td>
						<td>
				
							<a class="gcustomsettings-settings-option-remove" href="#" onclick="gksRemoveOption('<?=$gks_tab_id?>','<?=$gks_option_id?>'); return false" title="<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_REMOVE_OPTION")?>"><img src="/bitrix/images/grain.customsettings/gcustomsettings_options_option_icon_remove.gif" width="24" height="24" border="0" /></a>
				
						</td>
					</tr>
				</tbody>
			</table>
		
		</div>
		
		<?
	
		$s .= ob_get_contents();
		ob_end_clean();
	
		return $s;
	
	}
	
	
	function ShowOptionCustom($gks_tab_id,$gks_option_id,$option,$set_variables=true) {
	
		global $APPLICATION;
		global $arLang;
	
		ob_start();
	
		switch($option["TYPE"]):
	
		case "text":
		?>
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_CUSTOM_DEFAULT_VALUE")?></td>
				<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][DEFAULT_VALUE]" value="<?=htmlspecialchars($option["DEFAULT_VALUE"])?>" /></td>
			</tr></table>
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_TEXT_SIZE")?></td>
				<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][SIZE]" size="4" value="<?=htmlspecialchars($option["SIZE"])?>" /></td>
			</tr></table>
		<?
		break;
	
		case "textarea":
		?>
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_CUSTOM_DEFAULT_VALUE")?></td>
				<td><textarea name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][DEFAULT_VALUE]"><?=htmlspecialchars($option["DEFAULT_VALUE"])?></textarea></td>
			</tr></table>
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_TYPE_TEXT_SIZE")?></td>
				<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][COLS]" size="4" value="<?=htmlspecialchars($option["COLS"])?>" /></td>
				<td>x</td>
				<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][ROWS]" size="4" value="<?=htmlspecialchars($option["ROWS"])?>" /></td>
			</tr></table>
		<?
		break;
	
		case "checkbox":
		?>
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_CUSTOM_DEFAULT_VALUE")?></td>
				<td><input type="checkbox" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][DEFAULT_VALUE]" value="Y"<?if($option["DEFAULT_VALUE"]=="Y"):?> checked="checked"<?endif?> /></td>
			</tr></table>
		<?
		break;
	
		case "select":
		?>
	
			<?if($set_variables):?>
			<script type="text/javascript">
	
				gks_selectvalues[<?=$gks_tab_id?>][<?=$gks_option_id?>]=0;
	
			</script>
			<?endif?>
	
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_CUSTOM_DEFAULT_VALUE")?></td>
				<td>
					<select name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][DEFAULT_VALUE]">
						<?foreach($option["VALUES"]as $value):?>
							<option value="<?=htmlspecialchars($value["VALUE"])?>"<?if($option["DEFAULT_VALUE"]==$value["VALUE"]):?> selected="selected"<?endif?>><?=$value["LANG"][LANGUAGE_ID]?></option>
						<?endforeach?>
					</select>
				</td>
			</tr></table>
			<table class="gcustomsettings-settings-selectvalue-table">
				<thead>
					<tr>
						<td class="gcustomsettings-settings-selectvalue-col-value"><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_SELECTVALUE_TD_VALUE")?></td>
						<td class="gcustomsettings-settings-selectvalue-col-name"><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_SELECTVALUE_TD_NAME")?></td>
						<td class="gcustomsettings-settings-selectvalue-col-remove">&nbsp;</td>
					</tr>
				</thead>
			</table>
			
			<div id="gks_option_selectvalues_<?=$gks_tab_id?>_<?=$gks_option_id?>">
			<?$gks_selectvalue_id=1;foreach($option["VALUES"] as $value):?>
				<div id="gks_selectvalue_<?=$gks_tab_id?>_<?=$gks_option_id?>_<?=$gks_selectvalue_id?>">
					<?=self::ShowOptionSelectvalue($gks_tab_id,$gks_option_id,$gks_selectvalue_id,$value,true)?>
				</div>
			<?$gks_selectvalue_id++;endforeach?>
			</div>
			<div class="gcustomsettings-settings-selectvalue-add">
				<a href="#" onclick="gksAddSelectValue('<?=$gks_tab_id?>','<?=$gks_option_id?>'); return false;"><img src="/bitrix/images/grain.customsettings/gcustomsettings_options_option_icon_add.gif" width="16" height="16" border="0" />&nbsp;&nbsp;<span><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_SELECTVALUE_ADD")?></span></a>
			</div>
			
		<?
		break;
	
		case "date":
		?>
			<table cellspacing="0" cellpadding="0"><tr>
				<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_TAB_OPTION_CUSTOM_DEFAULT_VALUE")?></td>
				<td><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][DEFAULT_VALUE]" value="<?=htmlspecialchars($option["DEFAULT_VALUE"])?>" /></td>
				<td><?=Calendar(htmlspecialchars("TABS[".$gks_tab_id."][FIELDS][".$gks_option_id."][DEFAULT_VALUE]"),"gcs_settings_form")?></td>
			</tr></table>
		<?
		break;

		case "link":
		?>
			<?if(self::IsLinksInstalled()):?>
				<input type="button" style="margin: 5px 0" onclick="gksShowLinksDataSourcePopup('TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][LINK]','gks_column_linkparams_<?=$gks_tab_id?>_<?=$gks_option_id?>'); return false;" value="<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_SET_UP_DATA_SOURCE")?>" />
				<div style="margin: 5px 0">
					<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_INTERFACE_TYPE")?>:<br />
					<select name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][INTERFACE]">
						<option value="ajax"<?if($option["INTERFACE"]=="ajax"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_INTERFACE_AJAX")?></option>
						<option value="select"<?if($option["INTERFACE"]=="select"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_INTERFACE_SELECT")?></option>
						<option value="selectsearch"<?if($option["INTERFACE"]=="selectsearch"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_INTERFACE_SELECTSEARCH")?></option>
						<option value="search"<?if($option["INTERFACE"]=="search"):?> selected="selected"<?endif?>><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_INTERFACE_SEARCH")?></option>
					</select>
				</div>
				<label><table cellspacing="0" cellpadding="0"><tr>
					<td><?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_INTERFACE_SHOW_URL")?></td>
					<td><input type="checkbox" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][SHOW_URL]" value="Y"<?if($option["SHOW_URL"]=="Y"):?> checked="checked"<?endif?> /></td>
				</tr></table></label>
				<div id="gks_column_linkparams_<?=$gks_tab_id?>_<?=$gks_option_id?>">
					<?if(is_array($option["LINK"])):foreach($option["LINK"] as $param_name=>$param_value):?>
						<?if(is_array($param_value)):?>
							<?foreach($param_value as $k=>$v):?>
								<input type="hidden" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][LINK][<?=$param_name?>][<?=$k?>]" value="<?=htmlspecialchars($v)?>" />
							<?endforeach?>
						<?else:?>
							<input type="hidden" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][LINK][<?=$param_name?>]" value="<?=htmlspecialchars($param_value)?>" />
						<?endif?>
					<?endforeach;endif?>
				</div>
			<?else:?>
				<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_LINK_NOT_INSTALLED")?>
			<?endif?>
		<?
		break;
	
		default:	
		
			echo "Wrong type";
	
		endswitch;
	
		$s .= ob_get_contents();
		ob_end_clean();
	
		return $s;
	
	}


	function ShowOptionSelectvalue($gks_tab_id,$gks_option_id,$gks_selectvalue_id,$value=Array(),$set_variables=true) {

		global $APPLICATION;
		global $arLang;
	
		ob_start();
	
		?>
	
		<?if($set_variables):?>
		<script type="text/javascript">
	
			gks_selectvalues[<?=$gks_tab_id?>][<?=$gks_option_id?>]++;
	
		</script>
		<?endif?>
	
		<table cellspacing="0" cellpadding="0" class="gcustomsettings-settings-selectvalue-table">
	    	<tr>
	    		<td class="gcustomsettings-settings-selectvalue-col-value"><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][VALUES][<?=$gks_selectvalue_id?>][VALUE]" value="<?=htmlspecialchars($value["VALUE"])?>" /></td>
	    		<td class="gcustomsettings-settings-selectvalue-col-name">
	    			<table cellspacing="0" cellpadding="0">
	    				<?foreach($arLang as $lang_id => $lang):?>
	    					<tr>
	    						<td class="gcustomsettings-settings-selectvalue-col-name-lang-id"><?=$lang_id?></td>
	    						<td class="gcustomsettings-settings-selectvalue-col-name-name"><input type="text" name="TABS[<?=$gks_tab_id?>][FIELDS][<?=$gks_option_id?>][VALUES][<?=$gks_selectvalue_id?>][LANG][<?=$lang_id?>]" value="<?=htmlspecialchars($value["LANG"][$lang_id])?>" /></td>
	    					</tr>
	    				<?endforeach;?>
	    			</table>
	    		</td>
	    		<td class="gcustomsettings-settings-selectvalue-col-remove"><a href="#" onclick="gksRemoveSelectValue('<?=$gks_tab_id?>','<?=$gks_option_id?>','<?=$gks_selectvalue_id?>'); return false;" title="<?=GetMessage("GRAIN_CUSTOMSETTINGS_OPTIONS_SETTINGS_SELECTVALUE_REMOVE")?>"><img src="/bitrix/images/grain.customsettings/gcustomsettings_options_option_icon_remove.gif" width="16" height="16" border="0" /></a></td>
	    	</tr>
	    </table>
	
		<?


		$s .= ob_get_contents();
		ob_end_clean();
	
		return $s;
	
	
	
	}

}

/*patchvalidationmutatormark2*/

?>