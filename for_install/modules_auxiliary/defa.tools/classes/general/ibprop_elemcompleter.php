<?
IncludeModuleLangFile(__FILE__);

if (!class_exists("DefaTools_IBProp_ElemCompleter"))
{

class DefaTools_IBProp_ElemCompleter
{
		const DEFAULT_NAME_FORMAT = '#NAME# [#ID#]';
		
		function GetUserTypeDescription()
		{
			return array(
				"PROPERTY_TYPE"			=> "E",
				"USER_TYPE"				=> "DefaToolsElemCompleter",
				"DESCRIPTION"			=> GetMessage("DEFATOOLS_PROP_NAME"),
				"GetSettingsHTML"		=> array("DefaTools_IBProp_ElemCompleter","GetSettingsHTML"),
				"PrepareSettings"		=> array("DefaTools_IBProp_ElemCompleter","PrepareSettings"),
				"ConvertToDB"			=> array("DefaTools_IBProp_ElemCompleter","ConvertToDB"),
				"GetPropertyFieldHtml"	=> array("DefaTools_IBProp_ElemCompleter","GetPropertyFieldHtml"),
				"GetPublicEditHTML"		=> array("DefaTools_IBProp_ElemCompleter","GetPublicEditHTML"),
				"GetPublicViewHTML"		=> array("DefaTools_IBProp_ElemCompleter","GetPublicViewHTML"),
			);
		}
	   
		function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
		{
			$arSettings = self::__FormatSettings($arProperty);

			$arPropertyFields = array(
				"USER_TYPE_SETTINGS_TITLE" => GetMessage("DEFATOOLS_SETTINGS_TITLE")
			);
	
			$arr = array("REFERENCE" => array(), "REFERENCE_ID" => array());
			$rs = CIBlock::GetList(array(), array("ACTIVE" => "Y"));
			$arIBlocks = array();
			while($ar = $rs->Fetch()):
				$arr["REFERENCE"][] = $ar["NAME"].' ['.$ar["ID"].']';
				$arr["REFERENCE_ID"][] = $ar["ID"]; 
				$arIBlocks[] = $ar;
			 endwhile;

			$db_iblock_type = CIBlockType::GetList();
			$arIBTypes = array();
			$arIBmain = array();
			while($ar_iblock_type = $db_iblock_type->Fetch()):
			   $ar_iblock_type["NAME"] = '';
			   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG)):
			   		$ar_iblock_type["NAME"] = $arIBType["NAME"];
			  	endif;	
			   $arIBTypes[$ar_iblock_type["ID"]] = $ar_iblock_type; 
			   $arIBmain[$ar_iblock_type["ID"]]['NAME'] = $ar_iblock_type["NAME"];
			endwhile;

			foreach($arIBlocks as $arIBlock):
				$arIBmain[$arIBlock['IBLOCK_TYPE_ID']]['IB'][] = $arIBlock;  
			endforeach;

			return '
			   <script>
						function SelectIBlocks(sel, input)
						{
								var arGroups = [];
								for(var i = 0; i < sel.options.length; i++)
								{
										var opt = sel.options[i];
										if(opt.selected)
												arGroups.push(opt.value);
								}
								input.value = arGroups.join(\',\');
						}
				</script>

				<tr>
					<td>'.GetMessage("DEFATOOLS_INFOBLOCKS").':</td>
					
					<td><input type="hidden" value="'.htmlspecialchars(join(', ', $arSettings["IBLOCKS"])).'" id="'.md5($strHTMLControlName["NAME"].'[IBLOCKS]').'" name="'.$strHTMLControlName["NAME"].'[IBLOCKS]" />'.
					self::DTSelectBoxFromArray(
						$arIBmain, 
						$arSettings["IBLOCKS"], 
						' onchange="SelectIBlocks(this, BX(\''.md5($strHTMLControlName["NAME"].'[IBLOCKS]').'\'))"' 
					)
					.'</td>
					
				</tr>
  				<tr>
					<td>'.GetMessage("DEFATOOLS_PATH_TO_AJAX_FILE").':</td>
					<td><input type="text" value="'.htmlspecialchars($arSettings["SEARCH_URL"]).'" size="40" name="'.$strHTMLControlName["NAME"].'[SEARCH_URL]"></td>
				</tr>
				<tr>
					<td>'.GetMessage("DEFATOOLS_HINT_TEXT").':</td>
					<td><input type="text" value="'.htmlspecialchars($arSettings["HINT"]).'" size="40" name="'.$strHTMLControlName["NAME"].'[HINT]"></td>
				</tr>
				<tr>
					<td>'.GetMessage("DEFATOOLS_HINT_TEXT_NO_RES").':</td>
					<td><input type="text" value="'.htmlspecialchars($arSettings["HINT_ON_EMPTY"]).'" size="40" name="'.$strHTMLControlName["NAME"].'[HINT_ON_EMPTY]"></td>
				</tr>
				<tr>
					<td>'.GetMessage("DEFATOOLS_NAME_TEMPLATE").':</td>
					<td><input type="text" value="'.htmlspecialchars($arSettings["NAME_TEMPLATE"]).'" size="40" name="'.$strHTMLControlName["NAME"].'[NAME_TEMPLATE]" /></td>
				</tr>
				<tr>
					<td>'.GetMessage("DEFATOOLS_MAX_ELEM_NUM").':</td>
					<td><input type="text" value="'.htmlspecialchars($arSettings["TOP_COUNT"]).'" size="10" name="'.$strHTMLControlName["NAME"].'[TOP_COUNT]"></td>
				</tr>';
	}
	 
	 
	function DTSelectBoxFromArray($arIBmain, $ar_selected, $script)
	{
		$strReturnBox = "<select $script multiple name=\"\" id=\"\" size=\"5\">";
		$strReturnBox .= "<option value='0'></option>";

	 	foreach($arIBmain as $ib_type => $arIBtype):
	 		if(empty($arIBtype['IB'])) continue;
	 		$strReturnBox .= '<optgroup label="'.$arIBtype['NAME'].'">';
	 		foreach($arIBtype['IB'] as $key => $arIB):
				$value = $arIB['ID'];
				$sel = (is_array($ar_selected) && in_array($value, $ar_selected)) ? "selected" : "";
				$strReturnBox .= "<option value=\"".htmlspecialchars($value)."\" ".$sel.">". htmlspecialchars($arIB['NAME'])."</option>";
			endforeach;
			$strReturnBox .= '</optgroup>';
		endforeach;

		$strReturnBox .= "</select>";
		return $strReturnBox;
	}
	
		
		
	function __GetFormatName($arElement, $NAME_TEMPLATE='')
	{
				if(empty($NAME_TEMPLATE))
						$NAME_TEMPLATE = self::DEFAULT_NAME_FORMAT;

				return CComponentEngine::MakePathFromTemplate($NAME_TEMPLATE, $arElement);
	}
		function __FormatSettings(&$arProperty)
		{
				$arProperty["USER_TYPE_SETTINGS"]["NAME_TEMPLATE"] = trim($arProperty["USER_TYPE_SETTINGS"]["NAME_TEMPLATE"]);
				if(empty($arProperty["USER_TYPE_SETTINGS"]["NAME_TEMPLATE"]))
						$arProperty["USER_TYPE_SETTINGS"]["NAME_TEMPLATE"] = self::DEFAULT_NAME_FORMAT;
				
				$arProperty["USER_TYPE_SETTINGS"]["HINT_ON_EMPTY"] = trim($arProperty["USER_TYPE_SETTINGS"]["HINT_ON_EMPTY"]);
				
				$arProperty["USER_TYPE_SETTINGS"]["HINT"] = trim($arProperty["USER_TYPE_SETTINGS"]["HINT"]);
				$arProperty["USER_TYPE_SETTINGS"]["SEARCH_URL"] = trim($arProperty["USER_TYPE_SETTINGS"]["SEARCH_URL"]);
				if(empty($arProperty["USER_TYPE_SETTINGS"]["SEARCH_URL"]))
						$arProperty["USER_TYPE_SETTINGS"]["SEARCH_URL"] = '/bitrix/tools/defatools/elemcompliter/ajax_iblock_items_search.php';
			   if(!is_array($arProperty["USER_TYPE_SETTINGS"]["IBLOCKS"]) && !empty($arProperty["USER_TYPE_SETTINGS"]["IBLOCKS"]))
						$arProperty["USER_TYPE_SETTINGS"]["IBLOCKS"] = explode(',', $arProperty["USER_TYPE_SETTINGS"]["IBLOCKS"]);
				if(!is_array($arProperty["USER_TYPE_SETTINGS"]["IBLOCKS"]))
						$arProperty["USER_TYPE_SETTINGS"]["IBLOCKS"] = array();
			
				$arProperty["USER_TYPE_SETTINGS"]["TOP_COUNT"] = intval($arProperty["USER_TYPE_SETTINGS"]["TOP_COUNT"]);
				if(!$arProperty["USER_TYPE_SETTINGS"]["TOP_COUNT"])
						$arProperty["USER_TYPE_SETTINGS"]["TOP_COUNT"] = 20;
						 
				return $arProperty["USER_TYPE_SETTINGS"];
		}
		function PrepareSettings($arFields)
		{
				$arFields["USER_TYPE_SETTINGS"]["NAME_TEMPLATE"] = trim($arFields["USER_TYPE_SETTINGS"]["NAME_TEMPLATE"]);
				$arProperty["USER_TYPE_SETTINGS"]["HINT"] = trim($arProperty["USER_TYPE_SETTINGS"]["HINT"]);
				$arProperty["USER_TYPE_SETTINGS"]["HINT_ON_EMPTY"] = trim($arProperty["USER_TYPE_SETTINGS"]["HINT_ON_EMPTY"]);
				$arFields["USER_TYPE_SETTINGS"]["IBLOCKS"] = trim($arFields["USER_TYPE_SETTINGS"]["IBLOCKS"]);

				return $arFields["USER_TYPE_SETTINGS"];
		}
		function InitScript()
		{
			CJSCore::Init('defa_tools_autocomplete');
		}
		function CreateHint($arProperty, $ID)
		{
				$arSettings = self::__FormatSettings($arProperty);

				$bShowHint = !empty($arSettings["HINT"]) || !empty($arSettings["HINT_ON_EMPTY"]);

				if(!$bShowHint) return;

		?>
				<script type="text/javascript">
				<!--
						if(typeof BX.insertAfter == "undefined")
						{
								BX.insertAfter = function(newElement,targetElement)
								{
										var parent = targetElement.parentNode;
										if(parent.lastchild == targetElement)
												parent.appendChild(newElement);
										else
												parent.insertBefore(newElement, targetElement.nextSibling);
								}
						}
						var addHint = function(el, html)
						{
								var obBXHint = new BXHint(html, null, {'iconSrc': '/bitrix/js/main/core/images/dtautocomplete/i-hint.png'});
								BX.insertAfter(obBXHint.oIcon, el);
								obBXHint.oIcon.style.marginTop = '1px';
								obBXHint.oIcon.style.marginLeft = '5px';
								
								return obBXHint;
						}
						var obHint<? echo $ID ?> = addHint(BX('<? echo $ID ?>'), '<? echo CUtil::JSEscape(!empty($arSettings["HINT"]) ? $arSettings["HINT"] : $arSettings["HINT_ON_EMPTY"]) ?>');
				//-->
				</script>
		<?
		}
	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{		
		$html = "";
		ob_start();

		if(empty($arProperty['LINK_IBLOCK_ID']) && empty($arProperty['USER_TYPE_SETTINGS']['IBLOCKS'])):
			if(empty($GLOBALS['NOTE_DTEC_NOIB_IS_ON_PAGE'])):
				$GLOBALS['NOTE_DTEC_NOIB_IS_ON_PAGE'] = 'Y';
				echo BeginNote(). GetMessage("DEFATOOLS_DTEC_NOIB") .EndNote().'<br />';
			endif;	
		else:
			
			self::InitScript();
			$arSettings = self::__FormatSettings($arProperty);
			$ID = str_replace(array('[', ']', ':'), array('__', '', '_'), $strHTMLControlName["VALUE"]);
					$arItem = self::_GetIBElement($arProperty, $value["VALUE"]);
		

			?><input type="text" name="<? echo $strHTMLControlName["VALUE"] ?>" id="<? echo $ID ?>" value="<? echo htmlspecialchars($value["VALUE"]) ?>" />&nbsp;<span class="complete-choice" id="sp_<? echo $ID ?>"><? echo (is_array($arItem) ? $arItem["NAME"] : "")?></span>

			<script>
			<!--
			<? if ($strHTMLControlName['MODE'] === 'iblock_element_admin') :?>
			top.BX.addCustomEvent('onAjaxSuccess', _defa_init_input_<?= $ID ?>);
			<? else : ?>
			_defa_init_input_<?= $ID ?>();
			<? endif; ?>

			function _defa_init_input_<?= $ID ?>() {
				if (!top.BX('<?= $ID ?>') || typeof window._defa_init_input_ac<? echo $ID ?> !== 'undefined') {
					return false;
				}
				window._defa_init_input_ac<? echo $ID ?> = new top.BX.dtautocomplete('<? echo $ID ?>', {
					'params': {
						'IBLOCK_ID': <? echo (!empty($arSettings["IBLOCKS"]) ? CUtil::PhpToJsObject($arSettings["IBLOCKS"]) : intval($arProperty["LINK_IBLOCK_ID"])); ?>,
						'tc': <? echo intval($arSettings["TOP_COUNT"])?>,
						'nt': '<? echo CUtil::JSEscape($arSettings["NAME_TEMPLATE"])?>',
						'for_list': '<?=$strHTMLControlName["VALUE"]?>'
					},
					'url': '<? echo CUtil::JSEscape($arSettings["SEARCH_URL"]) ?>',
					'onSelect': function (arr, el) {

						var sp = top.BX('sp_' + el.id);
						if (sp) sp.innerHTML = arr['NAME'];
						return arr['ID'];
					}
				});
				return true;
			}

			//-->
			</script><?
				

		endif;	   

		$html .= ob_get_contents();
		ob_end_clean();

		return  $html;
	}
	function _GetIBElement($arProperty, $ID)
	{
				$ID = intval($ID);
				static $CACHE = array();

				if(!array_key_exists($ID, $CACHE))
				{
						$arSettings = self::__FormatSettings($arProperty);
						$IBLOCK_ID = (!empty($arSettings["IBLOCKS"]) ? $arSettings["IBLOCKS"] : $arProperty["LINK_IBLOCK_ID"]);

						$rs = CIBlockElement::GetList(array(), array("ID" => $ID, "IBLOCK_ID" => $IBLOCK_ID), false, false, array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL"));
						$CACHE[$ID] = $rs->GetNext();
				}

				return $CACHE[$ID];
	}
	function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
	{
				$arSettings = self::__FormatSettings($arProperty);
				
				self::InitScript();

				$html = "";
				$ID = str_replace(array('[', ']'), array('__', ''), $strHTMLControlName["VALUE"]);
				$sID = 's'.$ID;

				$arItem = self::_GetIBElement($arProperty, $value["VALUE"]);

				ob_start();

				?><input type="hidden" name="<? echo $strHTMLControlName["VALUE"] ?>" id="i<? echo $ID ?>" value="<? echo htmlspecialchars($value["VALUE"]) ?>" />
				<table cellpadding="0" cellspacing="0" border="0" class="complete-table<?if(is_array($arItem)) echo ' complete-table-active' ?>" id="tbl_<? echo $ID ?>">
						<tr>
								<td id="t<? echo $ID ?>" class="input"><input type="text" name="s<? echo $strHTMLControlName["VALUE"] ?>" id="<? echo $ID ?>" value="" /></td>
								<td id="t2<? echo $ID ?>" class="label"><span id="sp_<? echo $ID ?>"><? echo (is_array($arItem) ? self::__GetFormatName($arItem, $arSettings["NAME_TEMPLATE"]) : "")?></span><i onclick="acc<? echo $ID ?>.resetResult();" title="<? echo GetMessage("MAIN_DELETE") ?>"></i></td>
						</tr>
				</table>

				<script type="text/javascript">
				<!--
						var CJSCompleteController = function(id)
						{
								this.id = id;
								this.setResult = function(val, text)
								{
										this.setTextValue(val, text);
										BX.addClass('tbl_' + this.id, 'complete-table-active');
								}
								this.resetResult = function()
								{
										this.setTextValue('', '');
										BX.removeClass('tbl_' + this.id, 'complete-table-active');
								}
								this.setTextValue = function(val, text)
								{
										this.setValue(val);
										this.setText(text);
								}
								this.setValue = function(val)
								{
										BX('i' + this.id).value = val;
										BX(this.id).value = val;
								}
								this.setText = function(val)
								{
										BX('sp_' + this.id).innerHTML = val;
								}
						}
						var acc<? echo $ID ?> = new CJSCompleteController('<? echo $ID ?>');
						var params = {
								'params': {
										'IBLOCK_ID': <? echo (!empty($arSettings["IBLOCKS"]) ? CUtil::PhpToJsObject($arSettings["IBLOCKS"]) : intval($arProperty["LINK_IBLOCK_ID"])); ?>,
										'tc': <? echo intval($arSettings["TOP_COUNT"])?>,
										'nt': '<? echo CUtil::JSEscape($arSettings["NAME_TEMPLATE"])?>'
								},
								'url': '<? echo $arSettings["SEARCH_URL"] ?>',
								'onSelect':
										function(arr, el, obj)
										{
												acc<? echo $ID ?>.setResult(arr['ID'], arr['NAME']);
												return arr['NAME'];
										}
						};

						<?if(!empty($arProperty["USER_TYPE_SETTINGS"]["HINT_ON_EMPTY"])):?>

						params.afterAjax = function(result)
						{
								if(result.length == 0)
								{
										setTimeout(function() {
												var obBXHint = obHint<? echo $ID ?>;

												var pos = BX.pos(obBXHint.oIcon);
												obBXHint.x = pos.left;
												obBXHint.y = pos.top;
												obBXHint.Show('<? echo CUtil::JsEscape($arProperty["USER_TYPE_SETTINGS"]["HINT_ON_EMPTY"])?>');

										}, 100);
								}
								else
								{
										obHint<? echo $ID ?>.Hide();
								}
								return result;
						}

						<?endif?>

						var ac<? echo $ID ?> = new BX.dtautocomplete('<? echo $ID ?>', params);


						<?if(is_array($arItem)):?>
						BX(
								function()
								{
										acc<? echo $ID ?>.setResult('<? echo $arItem["ID"] ?>', '<? echo self::__GetFormatName($arItem, $arSettings["NAME_TEMPLATE"]) ?>');
								}
						);
						<?endif?>
				//-->
				</script><?

				self::CreateHint($arProperty, $ID);

		$html .= ob_get_contents();
		ob_end_clean();

		return  $html;
	}
	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
				$arItem = false;
				if(strlen($value["VALUE"]) > 0 && ($arItem = self::_GetIBElement($arProperty, $value["VALUE"])))
				{
						return !empty($arItem["DETAIL_PAGE_URL"]) ? '<a href="'.$arItem["DETAIL_PAGE_URL"].'">'.$arItem["NAME"].'</a>' : $arItem["NAME"];
				}
				return "&nbsp;";
	}
	function ConvertToDB($arProperty, $value)
	{
				if(strlen($value["VALUE"]) > 0)
				{
						$value["VALUE"] = intval($value["VALUE"]);
						if(!$value["VALUE"]) $value["VALUE"] = '';
				}
				return $value;
	}
}


} // class exists 
?>
