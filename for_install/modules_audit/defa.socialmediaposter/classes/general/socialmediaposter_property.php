<?
IncludeModuleLangFile(__FILE__);

class DSocialMediaPosterCIBlockProperty {

	function GetUserTypeDescription() {
		return array(
			"PROPERTY_TYPE"			=> "S",
			"USER_TYPE"				=> "SocialMediaPoster",
			"DESCRIPTION"			=> GetMessage("IBLOCK_PROP_SOCIALMEDIAPOSTER_DESC"),
			"GetPublicViewHTML"		=> array("DSocialMediaPosterCIBlockProperty", "GetPublicViewHTML"),
			"GetPublicEditHTML"		=> array("DSocialMediaPosterCIBlockProperty", "GetPublicEditHTML"),
			"GetAdminListViewHTML"	=> array("DSocialMediaPosterCIBlockProperty", "GetAdminListViewHTML"),
			"GetPropertyFieldHtml"	=> array("DSocialMediaPosterCIBlockProperty", "GetPropertyFieldHtml"),
			"PrepareSettings"		=> array("DSocialMediaPosterCIBlockProperty", "PrepareSettings"),
			"GetSettingsHTML"		=> array("DSocialMediaPosterCIBlockProperty", "GetSettingsHTML"),
			"ConvertToDB"			=> array("DSocialMediaPosterCIBlockProperty", "ConvertToDB"),
			"ConvertFromDB"			=> array("DSocialMediaPosterCIBlockProperty", "ConvertFromDB"),
			"GetLength"				=> array("DSocialMediaPosterCIBlockProperty", "GetLength")
		);
	}

	function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName)
	{
		if (!is_array($arValue["VALUE"]) && !empty($arValue["VALUE"]))
			$arValue["VALUE"] = array($arValue["VALUE"]);

		foreach($arValue["VALUE"] as $k => $v)
			$arValue["VALUE"][$k] = GetMessage("SOCIALMEDIAPOSTER_ENTITY_".ToUpper($v));

		return implode(" / ", $arValue["VALUE"]);
	}

	function GetPublicEditHTML($arProperty, $value, $strHTMLControlName) {

		$lEntities = array();
		$str = '';

		$arEntities = $arProperty["USER_TYPE_SETTINGS"];

		if (is_set($arProperty["USER_TYPE_SETTINGS"], "ENTITIES"))
			$arEntities = $arProperty["USER_TYPE_SETTINGS"]["ENTITIES"];

		$list = DSocialPosterEntityFactory::GetEntityList();
		$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"] = 0;

		if (!is_array($value["VALUE"]))
			$value["VALUE"] = array();

		for($list->rewind(); $list->valid(); $list->next())
		{
			$entity = $list->current();
			$str .= '<label for="'.htmlspecialchars($strHTMLControlName["VALUE"].'['.$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"].']').'"><nobr><input type="hidden" name="'.htmlspecialchars($strHTMLControlName["VALUE"].'['.$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"].']').'" value="">'.InputType('checkbox', htmlspecialchars($strHTMLControlName["VALUE"].'['.$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"].']'), $entity->GetId(), htmlspecialcharsex($value["VALUE"]), false, $entity->GetName(), $arEntities[$entity->GetId()]["IS_USE"]!="Y"?'disabled="disabled" class="bxedtbutton-disabled"':(in_array($entity->GetId(), $value["VALUE"])?'checked="checked"':''))."</nobr></label><br />";
			$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"]++;
		}
		return  $str;
	}

	function GetLength($arProperty, $value)
	{
		if(is_array($value) && array_key_exists("VALUE", $value))
			return count($value["VALUE"]);
		else
			return 0;
	}

	function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName)
	{
		if (!is_array($arValue["VALUE"]) && !empty($arValue["VALUE"]))
			$arValue["VALUE"] = array($arValue["VALUE"]);

		foreach($arValue["VALUE"] as $k => $v)
			$arValue["VALUE"][$k] = '<nobr><input type="checkbox" disabled=true checked=true>'.GetMessage("SOCIALMEDIAPOSTER_ENTITY_".ToUpper($v))."</nobr>";

		return implode("<br />", $arValue["VALUE"]);
	}

	function ConvertToDB($arProperty, $value)
	{

		$return = array("VALUE" => array());

		foreach ($value["VALUE"] as $k => $v)
			if (empty($v))
				unset($value["VALUE"][$k]);

		$return["VALUE"] = serialize($value["VALUE"]);

		return $return;
	}

	function ConvertFromDB($arProperty, $value)
	{
		$return = array("VALUE" => array());

		if (strlen($value["VALUE"]) > 0 && ($unserializedArray = unserialize($value["VALUE"])))
			$return["VALUE"] = $unserializedArray;

		return $return;
	}

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) {

		/**
		 * @var $prop_edit bool
		 * true - display send buttons,
		 * false - do NOT display send buttons
		 */
		$b = ($strHTMLControlName['FORM_NAME'] === 'form_element_' . $arProperty['IBLOCK_ID'] . '_form' && (int) $_REQUEST["ID"] !== 0) ? true : false;

		CJSCore::Init(array('defa_smp'));

		$params_array = array(
			'ID' => intval($_REQUEST['ID']),
			'IBLOCK_ID' => intval($_REQUEST['IBLOCK_ID'])
		);

		$params = http_build_query($params_array);

		$str = "<script>var __defa_ajax_post_link = '" . $GLOBALS['APPLICATION']->GetCurPage() . '?' . $params . "';</script>";

		$lEntities = array();

		$arEntities = $arProperty["USER_TYPE_SETTINGS"];

		if (is_set($arProperty["USER_TYPE_SETTINGS"], "ENTITIES"))
			$arEntities = $arProperty["USER_TYPE_SETTINGS"]["ENTITIES"];

		$list = DSocialPosterEntityFactory::GetEntityList();
		$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"] = 0;

		if (!is_array($value["VALUE"]))
			$value["VALUE"] = array();

		$str .= "<table class='defa__smp__iblock_element_edit";
		$str .= $b ? " adm-detail-toolbar'>" : "'>";

		for($list->rewind(); $list->valid(); $list->next())
		{
			$entity = $list->current();
			$str .= '<tr data-socnet-row="' . $entity->getId() . '">';
			$str .= '<td><label for="'.htmlspecialchars($strHTMLControlName["VALUE"].'['.$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"].']').'"></td>';
			$str .= '<td><input type="hidden" name="'.htmlspecialchars($strHTMLControlName["VALUE"].'['.$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"].']').'" value="">'.InputType('checkbox', htmlspecialchars($strHTMLControlName["VALUE"].'['.$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"].']'), $entity->GetId(), htmlspecialcharsex($value["VALUE"]), false, $entity->GetName(), "style=\"margin: 3px!important\" ".($arEntities[$entity->GetId()]["IS_USE"]!="Y"?'disabled="disabled" class="bxedtbutton-disabled"':(in_array($entity->GetId(), $value["VALUE"])?'checked="checked"':'')))."</label></td>";
			if (!$b) {
				$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"]++;
				$str .= "<td>&nbsp;</td></tr>";
				continue;
			}
			if ($arEntities[$entity->GetId()]["IS_USE"] != "Y" || !in_array($entity->GetId(), $value["VALUE"])) {
				$but_disabled = true;
			} else {
				$but_disabled = false;
			}

			$str .= '<td><button type="button" name="save" class="button adm-btn defa-smp-but';

			if ($but_disabled) {
				$str .= " adm-btn-load";
			}

			$str .= '"';

			if ($but_disabled) {
				$str .= " disabled='disabled'";
			}
			$str .= ' value="Y">' . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_SEND_NOW') .'</button>';

			$str .='</td>';
			$GLOBALS["SOCIALMEDIAPOSTER_PROPERTY_ITERATOR"]++;
			$str .= "<td class='defa-status-info'>&nbsp;</td></tr>";
		}

		if ($b) {
			$str .= "<tr data-socnet-row='all'><td colspan='4'><a class='adm-btn defa-smp-but-all'>" . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_SEND_ALL') ."</a></td></tr>";
		}

		$str .= "</table>";
		return $str;
	}

	public static function GetSettings()
	{
		return array(
			array("CHECK_DATES", GetMessage("SOCIALMEDIAPOSTER_SETTING_PROPERTY_CHECK_DATES"), "Y", array("checkbox")),
			array("POST_TYPE", GetMessage("SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE"), "TEXT", array("selectbox", array("TEXT" => GetMessage("SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE_TEXT"), "PHOTO" => GetMessage("SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE_PHOTO"), "VIDEO" => GetMessage("SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE_VIDEO")))),
		);
	}

	function PrepareSettings($arFields, $bGetFull = false)
	{
		if (!is_array($arFields["USER_TYPE_SETTINGS"]))
			$arFields["USER_TYPE_SETTINGS"] = array();

		$arEntitiesSettings = $arFields["USER_TYPE_SETTINGS"];
		if (is_set($arFields["USER_TYPE_SETTINGS"], "ENTITIES"))
			$arEntitiesSettings = $arFields["USER_TYPE_SETTINGS"]["ENTITIES"];

		$arSettings = array();
		foreach (self::GetSettings() as $option)
			$arSettings[$option[0]] = is_set($arFields["USER_TYPE_SETTINGS"], $option[0])?$arFields["USER_TYPE_SETTINGS"][$option[0]]:$option[2];

		$list = DSocialPosterEntityFactory::GetEntityList();
		for($list->rewind(); $list->valid(); $list->next())
		{
			$entity = $list->current();

			$entitySettings = array();

			if ($bGetFull)
				$entitySettings["IS_USE"] = array("VALUE" => $arEntitiesSettings[$entity->GetID()]["IS_USE"]=="Y"?"Y":"N");
			else
				$entitySettings["IS_USE"] = $arEntitiesSettings[$entity->GetID()]["IS_USE"]=="Y"?"Y":"N";

			foreach ($entity->GetSettingsMap() as $key => $value) {

				if ($bGetFull)
					$entitySettings[$key] = array_merge($value, array("VALUE" => $arEntitiesSettings[$entity->GetID()][$key]));
				else
					$entitySettings[$key] = $arEntitiesSettings[$entity->GetID()][$key];
			}

			$arSettings["ENTITIES"][$entity->GetID()] = $entitySettings;
		}

		return $arSettings;
	}

	function GetSettingsHTML($arFields, $strHTMLControlName, &$arPropertyFields) {

		$str = "";
		$arPropertyFields = array(
			"HIDE" => array("ROW_COUNT", "COL_COUNT", /*"DEFAULT_VALUE", */"MULTIPLE_CNT", "WITH_DESCRIPTION", "FILTRABLE", "SEARCHABLE"),
			"USER_TYPE_SETTINGS_TITLE" => GetMessage("SOCIALMEDIAPOSTER_SETTING_TITLE"),
		);

		$arSettings = self::PrepareSettings($arFields, true);

		$arSettingsEntities = $arSettings["ENTITIES"];
		unset($arSettings["ENTITIES"]);

		foreach (self::GetSettings() as $option)
		{
			$code = $option[0];
			$option[0] = $strHTMLControlName["NAME"].'['.$code.']';
			$str .= self::__SettingsGetRow((is_array($option) && is_set($arSettings, $code) ? $arSettings[$code] : null), $option);
		}


		/* DIAGNOSTICS */
		$str .= '<tr class="heading"><td colspan="2">'.GetMessage("SOCIALMEDIAPOSTER_ENTITIES_CHECK").'</td></tr>';

		/* curl */
		$str .= '<tr><td valign="top">' . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_CURL_TITLE') . '</td><td>';

		if (function_exists('curl_exec')) {
			$str .= "<strong style='color: green'>" . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_CURL_GREEN') . "</strong>";
		} else {
			$str .= "<strong style='color: red'>" . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_CURL_RED') . "</strong>";
		}
		$str .= '</td></tr>';


		/* bx_crontab_support */
		$str .= '<tr><td valign="top">' . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_BX_CRONTAB_TITLE') . '</td><td>';

		if (!defined('BX_CRONTAB')) {
			$str .= "<strong style='color: green'>" . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_BX_CRONTAB_GREEN') . "</strong>";
		} else {
			$str .= "<strong style='color: red'>" . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_BX_CRONTAB_RED') . "</strong>";
		}
		$str .= '</td></tr>';

		/* manual link */
		$str .= "<tr><td valign='top'>" . GetMessage('SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_MANUAL_LINK');
		$str .= "</td><td><a href='http://smposter.idefa.ru' target='_blank'>http://smposter.idefa.ru</a></td></tr>";

		/* SOCNET PARAMS */
		$str .= '<tr class="heading"><td colspan="2">'.GetMessage("SOCIALMEDIAPOSTER_ENTITIES_SETTING_TITLE").'</td></tr>';

		foreach ($arSettingsEntities as $entity => $settings) {

			$strSettings = $strAddtitonalSettings = '';

			foreach ($settings as $sName => $sParams) {
				if (substr($sName, 0, 9) == "TEMPLATE_") {
					$strAddtitonalSettings .= '<tr><td><div style="padding: 5px 0 0 0"><strong>'.$sParams["NAME"].':</strong></div>';

					if (strlen($sParams["VALUE"]) <= 0)
						$sParams["VALUE"] = $sParams["DEFAULT_VALUE"];

					if ($sParams["TYPE"] == "textarea")
						$strAddtitonalSettings .= '<textarea cols="'.($sParams["COLS"]>0?$sParams["COLS"]:"43").'" rows="'.($sParams["ROWS"]>0?$sParams["ROWS"]:"3").'" name="'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']['.$sName.']">'.$sParams["VALUE"].'</textarea>'.ShowJSHint($sParams["DESCRIPTION"], array("return" => true));
					else
						$strAddtitonalSettings .= '<input size="'.($sParams["COLS"]>0?$sParams["COLS"]:"58").'" type="'.$sParams["TYPE"].'" name="'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']['.$sName.']" value="'.$sParams["VALUE"].'">'.ShowJSHint($sParams["DESCRIPTION"], array("return" => true));

					$strAddtitonalSettings .= '</td></tr>';
					unset($settings[$sName]);
				}
			}

			if (strlen($strAddtitonalSettings) > 0) {
				$strAddtitonalSettings = '<div><div align="center"><a href="#" onclick="jsUtils.ToggleDiv(\'block_add_'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']'.'\'); return false;">'.GetMessage("SOCIALMEDIAPOSTER_SETTING_TEMPLATES_TITLE").'</a></div><div id="block_add_'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']'.'" style="display: none;"><table style="border-bottom: 1px solid #E0E4F1">'.$strAddtitonalSettings.'</table></div>';
			}

			$strSettings .= '<div id="block_'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']'.'" style="display: '.($settings["IS_USE"]["VALUE"]=="Y"?"block":"none").'">';
			$strSettings .= '<table>';

			foreach ($settings as $sName => $sParams) {
				if ($sName == "IS_USE") continue;
				if ($sParams['TYPE'] == 'selectbox' && is_array($sParams['SELECT_VALUES'])) {
					$strSettings .= '<tr><td align="right" width="150">'.$sParams["NAME"].':</td><td>'
						.'<select name="'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']['.$sName.']" value="'.$sParams["VALUE"].'">';

					foreach ($sParams['SELECT_VALUES'] as $value_code => $value_name) {
						$strSettings .= "<option value='${value_code}'".($sParams["VALUE"] == $value_code ? 'selected' : '').">${value_name}</option>";
					}

					$strSettings .= '</select>'.ShowJSHint($sParams["DESCRIPTION"], array("return" => true)).'</td></tr>';
				} elseif ($sParams['TYPE'] == 'selectbox') {
					$strSettings .= '<tr><td align="right" width="150">'.$sParams["NAME"].':</td><td>'
						.'<select name="'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']['.$sName.']" value="'.$sParams["VALUE"].'">'
						.'<option></option>'
						.'</select>'.ShowJSHint($sParams["DESCRIPTION"], array("return" => true)).'</td></tr>';
				} else {
					$strSettings .= '<tr><td align="right" width="150">'.$sParams["NAME"].':</td><td>'.'<input size="'.($sParams["COLS"]>0?$sParams["COLS"]:"33").'" type="'.$sParams["TYPE"].'" name="'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']['.$sName.']" value="'.$sParams["VALUE"].'">'.ShowJSHint($sParams["DESCRIPTION"], array("return" => true)).'</td></tr>';
				}
			}

			$strSettings .= '</table>';
			$strSettings .= $strAddtitonalSettings;

			$strSettings .= '</div></div>';

			$str .= '<tr>
						<td valign="top"><label for="'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.'][IS_USE]'.'">'.GetMessage("SOCIALMEDIAPOSTER_ENTITY_".ToUpper($entity)).'</label></td>
						<td valign="top">'.
							InputType('checkbox',$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.'][IS_USE]','Y',$settings["IS_USE"]["VALUE"], false, "", 'onclick="BX(\'block_'.$strHTMLControlName["NAME"].'[ENTITIES]['.$entity.']'.'\').style.display=this.checked?\'block\':\'none\'"').
							$strSettings.
						'</td>
					</tr>';
		}

		return $str;
	}

	private static function __SettingsGetRow($val, $Option)
	{
		ob_start();

		if(!is_array($Option)):
		?>
			<tr class="heading">
				<td valign="top" colspan="2" align="center"><b><?=$Option?></b></td>
			</tr>
		<?
		elseif(isset($Option["note"])):
		?>
			<tr>
				<td valign="top" colspan="2" align="center">
					<?echo BeginNote('align="center"');?>
					<?=$Option["note"]?>
					<?echo EndNote();?>
				</td>
			</tr>
		<?
		else:
			$type = $Option[3];
			$disabled = array_key_exists(4, $Option) && $Option[4] == 'Y' ? ' disabled' : '';
			$sup_text = array_key_exists(5, $Option) ? $Option[5] : '';
		?>
			<tr>
				<td valign="top" width="50%" class="field-name"><?
					if($type[0]=="checkbox")
						echo "<label for='".$Option[0]."'>".$Option[1]."</label>";
					else
						echo $Option[1];
					if (strlen($sup_text) > 0)
					{
						?><span class="required"><sup><?=$sup_text?></sup></span><?
					}
						?></td>
				<td valign="middle" width="50%"><?
				if($type[0]=="checkbox"):
					?><input type="hidden" name="<?echo $Option[0]?>" value="N" />
					<input type="checkbox" id="<?echo $Option[0]?>" name="<?echo $Option[0]?>" value="Y"<?if($val=="Y")echo" checked";?><?=$disabled?><?if($type[2]<>'') echo " ".$type[2]?>><?
				elseif($type[0]=="text" || $type[0]=="password"):
					?><input type="<?echo $type[0]?>" size="<?echo $type[1]?>" maxlength="255" value="<?echo $val?>" name="<?echo $Option[0]?>"<?=$disabled?><?=($type[0]=="password"? ' autocomplete="off"':'')?>><?
				elseif($type[0]=="selectbox"):
					$arr = $type[1];
					if(!is_array($arr))
						$arr = array();
					$arr_keys = array_keys($arr);
					?><select name="<?echo $Option[0]?>" <?=$disabled?>><?
						for($j=0; $j<count($arr_keys); $j++):
							?><option value="<?echo $arr_keys[$j]?>"<?if($val==$arr_keys[$j])echo" selected"?>><?echo $arr[$arr_keys[$j]]?></option><?
						endfor;
						?></select><?
				elseif($type[0]=="multiselectbox"):
					$arr = $type[1];
					if(!is_array($arr))
						$arr = array();
					$arr_keys = array_keys($arr);
					$arr_val = explode(",",$val);
					?><select size="5" multiple name="<?echo $Option[0]?>[]"<?=$disabled?>><?
						for($j=0; $j<count($arr_keys); $j++):
							?><option value="<?echo $arr_keys[$j]?>"<?if(in_array($arr_keys[$j],$arr_val)) echo " selected"?>><?echo $arr[$arr_keys[$j]]?></option><?
						endfor;
					?></select><?
				elseif($type[0]=="textarea"):
					?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo $Option[0]?>"<?=$disabled?>><?echo $val?></textarea><?
	            elseif($type[0]=="statictext"):
	                echo $val;
	            elseif($type[0]=="statichtml"):
	                echo $val;
				endif;

			$hint = GetMessage("SOCIALMEDIAPOSTER_SETTING_PROPERTY_".ToUpper($Option[0])."_DESC");
			if(strlen($hint)) ShowJSHint($hint);
				?></td>
			</tr>
		<?
		endif;

		return ob_get_clean();
	}
}

?>
