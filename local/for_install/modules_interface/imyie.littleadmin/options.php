<?if(!$USER->IsAdmin()) return;

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('imyie.littleadmin');

if($_REQUEST["RestoreDefaults"]!="" && check_bitrix_sessid())
{
	CIMYIELittleAdmin::RestoreDefaultSettings();
	CIMYIELittleAdminStyle::GeneratorCSSAndRewrite();
} elseif(isset($_REQUEST["save"]) && check_bitrix_sessid())
{
	//_______________________ different _______________________//
	COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_top', IntVal($_REQUEST["page_title_margin_top"]) );
	COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_right', IntVal($_REQUEST["page_title_margin_right"]) );
	COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_bot', IntVal($_REQUEST["page_title_margin_bot"]) );
	COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_left', IntVal($_REQUEST["page_title_margin_left"]) );
	
	COption::SetOptionInt('imyie.littleadmin', 'notes_margin_top', IntVal($_REQUEST["notes_margin_top"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_margin_right', IntVal($_REQUEST["notes_margin_right"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_margin_bot', IntVal($_REQUEST["notes_margin_bot"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_margin_left', IntVal($_REQUEST["notes_margin_left"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_padding_top', IntVal($_REQUEST["notes_padding_top"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_padding_right', IntVal($_REQUEST["notes_padding_right"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_padding_bot', IntVal($_REQUEST["notes_padding_bot"]) );
	COption::SetOptionInt('imyie.littleadmin', 'notes_padding_left', IntVal($_REQUEST["notes_padding_left"]) );
	
	COption::SetOptionInt('imyie.littleadmin', 'navi_padding_top', IntVal($_REQUEST["navi_padding_top"]) );
	COption::SetOptionInt('imyie.littleadmin', 'navi_padding_right', IntVal($_REQUEST["navi_padding_right"]) );
	COption::SetOptionInt('imyie.littleadmin', 'navi_padding_bot', IntVal($_REQUEST["navi_padding_bot"]) );
	COption::SetOptionInt('imyie.littleadmin', 'navi_padding_left', IntVal($_REQUEST["navi_padding_left"]) );
	
	COption::SetOptionString('imyie.littleadmin', 'navi_number_pos', htmlspecialchars($_REQUEST["navi_number_pos"]) );
	
	COption::SetOptionString('imyie.littleadmin', 'gadget_using', ($_REQUEST["gadget_using"]=="Y" ? "Y" : "N") );
	
	//_______________________ left_menu _______________________//
	COption::SetOptionString('imyie.littleadmin', 'left_menu_using', ($_REQUEST["left_menu_using"]=="Y" ? "Y" : "N") );
	
	//_______________________ page_edit _______________________//
	COption::SetOptionInt('imyie.littleadmin', 'page_edit_line_padding_top', IntVal($_REQUEST["page_edit_line_padding_top"]) );
	COption::SetOptionInt('imyie.littleadmin', 'page_edit_line_padding_bot', IntVal($_REQUEST["page_edit_line_padding_bot"]) );
	
	//_______________________ page_list _______________________//
	COption::SetOptionInt('imyie.littleadmin', 'page_list_minimization_level', IntVal($_REQUEST["page_list_minimization_level"]) );
	
	//_______________________ constrols _______________________//
	COption::SetOptionInt('imyie.littleadmin', 'constrols_input_text_height', IntVal($_REQUEST["constrols_input_text_height"]) );
	COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_border_radius', ($_REQUEST["constrols_input_text_border_radius"]=="Y" ? "Y" : "N") );
	COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_box_shadow', ($_REQUEST["constrols_input_text_box_shadow"]=="Y" ? "Y" : "N") );
	COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_hover_shadow', ($_REQUEST["constrols_input_text_hover_shadow"]=="Y" ? "Y" : "N") );
	COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_focus_shadow', ($_REQUEST["constrols_input_text_focus_shadow"]=="Y" ? "Y" : "N") );
	
	//_______________________ not_style _______________________//
	$not_style_add_buttons_OLD = COption::GetOptionString('imyie.littleadmin', 'not_style_add_buttons', "Y");
	COption::SetOptionString('imyie.littleadmin', 'not_style_add_buttons', ($_REQUEST["not_style_add_buttons"]=="Y" ? "Y" : "N") );
	if($_REQUEST["not_style_add_buttons"]=="Y" && $not_style_add_buttons_OLD!="Y")
	{
		RegisterModuleDependences("main", "OnAdminContextMenuShow", "imyie.littleadmin", "CIMYIELittleAdmin", "OnAdminContextMenuShowHandler", "500");
	} elseif($_REQUEST["not_style_add_buttons"]!="Y" && $not_style_add_buttons_OLD=="Y") {
		UnRegisterModuleDependences("main", "OnAdminContextMenuShow", "imyie.littleadmin", "CIMYIELittleAdmin", "OnAdminContextMenuShowHandler");
	}
	
	//*********************** Generate CSS and rewrite file ***********************//
	CIMYIELittleAdminStyle::GeneratorCSSAndRewrite();
}

$aTabs = array(
	array("DIV" => "imyie_tab_different", "TAB" => GetMessage("IMYIE_TAB_DIFFERENT"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TAB_DIFFERENT_TITLE")),
	array("DIV" => "imyie_tab_left_menu", "TAB" => GetMessage("IMYIE_TAB_LEFT_MENU"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TAB_LEFT_MENU_TITLE")),
	array("DIV" => "imyie_tab_page_edit", "TAB" => GetMessage("IMYIE_TAB_PAGE_EDIT"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TAB_PAGE_EDIT_TITLE")),
	array("DIV" => "imyie_tab_page_list", "TAB" => GetMessage("IMYIE_TAB_PAGE_LIST"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TAB_PAGE_LIST_TITLE")),
	array("DIV" => "imyie_tab_controls", "TAB" => GetMessage("IMYIE_TAB_CONSTROLS"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TAB_CONSTROLS_TITLE")),
	array("DIV" => "imyie_tab_not_style", "TAB" => GetMessage("IMYIE_TAB_NOT_STYLE"), "ICON" => "settings", "TITLE" => GetMessage("IMYIE_TAB_NOT_STYLE_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">


	<?//_______________________ different _______________________//?>
	<?$tabControl->BeginNextTab();?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_HEADING_PAGE_TITLE")?></td>
	</tr>
	<?
	$page_title_margin_top = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_top', 5);
	$page_title_margin_right = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_right', 0);
	$page_title_margin_bot = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_bot', 5);
	$page_title_margin_left = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_left', 0);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_PAGE_TITLE_MARGIN")?></td>
		<td valign="top" width="50%">
			<table border="0" cellspacing="0" cellpadding="5" style="width:1%;">
				<tr>
					<td nowrap><?=GetMessage("IMYIE_TOP")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_RIGHT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_BOT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_LEFT")?> (px)</td>
				</tr>
				<tr>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,30,"page_title_margin_top",$page_title_margin_top)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,30,"page_title_margin_right",$page_title_margin_right)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,30,"page_title_margin_bot",$page_title_margin_bot)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,30,"page_title_margin_left",$page_title_margin_left)?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_HEADING_NOTES")?></td>
	</tr>
	<?
	$notes_margin_top = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_top', 5);
	$notes_margin_right = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_right', 10);
	$notes_margin_bot = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_bot', 10);
	$notes_margin_left = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_left', 0);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_NOTES_MARGIN")?></td>
		<td valign="top" width="50%">
			<table border="0" cellspacing="0" cellpadding="5" style="width:1%;">
				<tr>
					<td nowrap><?=GetMessage("IMYIE_TOP")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_RIGHT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_BOT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_LEFT")?> (px)</td>
				</tr>
				<tr>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_margin_top",$notes_margin_top)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_margin_right",$notes_margin_right)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_margin_bot",$notes_margin_bot)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_margin_left",$notes_margin_left)?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?
	$notes_padding_top = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_top', 10);
	$notes_padding_right = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_right', 20);
	$notes_padding_bot = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_bot', 10);
	$notes_padding_left = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_left', 9);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_NOTES_PADDING")?></td>
		<td valign="top" width="50%">
			<table border="0" cellspacing="0" cellpadding="5" style="width:1%;">
				<tr>
					<td nowrap><?=GetMessage("IMYIE_TOP")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_RIGHT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_BOT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_LEFT")?> (px)</td>
				</tr>
				<tr>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_padding_top",$notes_padding_top)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_padding_right",$notes_padding_right)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_padding_bot",$notes_padding_bot)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,50,"notes_padding_left",$notes_padding_left)?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_NAVI")?></td>
	</tr>
	<?
	$navi_padding_top = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_top', 5);
	$navi_padding_right = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_right', 0);
	$navi_padding_bot = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_bot', 1);
	$navi_padding_left = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_left', 0);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_NAVI_PADDING")?></td>
		<td valign="top" width="50%">
			<table border="0" cellspacing="0" cellpadding="5" style="width:1%;">
				<tr>
					<td nowrap><?=GetMessage("IMYIE_TOP")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_RIGHT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_BOT")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_LEFT")?> (px)</td>
				</tr>
				<tr>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,15,"navi_padding_top",$navi_padding_top)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,15,"navi_padding_right",$navi_padding_right)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,15,"navi_padding_bot",$navi_padding_bot)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,15,"navi_padding_left",$navi_padding_left)?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?
	$navi_number_pos = COption::GetOptionString('imyie.littleadmin', 'navi_number_pos', "left");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_NAVI_NUMBER")?></td>
		<td valign="top" width="50%">
			<select name="navi_number_pos">
				<option value="left"<?if($navi_number_pos=="left"):?> selected <?endif;?>><?=GetMessage("IMYIE_NAVI_NUMBER_POST_LEFT")?></option>
				<option value="center"<?if($navi_number_pos=="center"):?> selected <?endif;?>><?=GetMessage("IMYIE_NAVI_NUMBER_POST_CENTER")?></option>
				<option value="right"<?if($navi_number_pos=="right"):?> selected <?endif;?>><?=GetMessage("IMYIE_NAVI_NUMBER_POST_RIGHT")?></option>
			</select>
		</td>
	</tr>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_GADGET")?></td>
	</tr>
	<?
	$gadget_using = COption::GetOptionString('imyie.littleadmin', 'gadget_using', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_GADGET_USING")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="gadget_using" value="Y"<?if($gadget_using=="Y"):?> checked<?endif;?> /></td>
	</tr>
	
	
	<?//_______________________ left_menu _______________________//?>
	<?$tabControl->BeginNextTab();?>
	<?
	$left_menu_using = COption::GetOptionString('imyie.littleadmin', 'left_menu_using', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_LEFT_MENU_USING")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="left_menu_using" value="Y"<?if($left_menu_using=="Y"):?> checked<?endif;?> /></td>
	</tr>
	
	
	<?//_______________________ page_edit _______________________//?>
	<?$tabControl->BeginNextTab();?>
	<?
	$page_edit_line_padding_top = COption::GetOptionInt('imyie.littleadmin', 'page_edit_line_padding_top', 2);
	$page_edit_line_padding_bot = COption::GetOptionInt('imyie.littleadmin', 'page_edit_line_padding_bot', 3);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_PAGE_EDIT_PADDING")?></td>
		<td valign="top" width="50%">
			<table border="0" cellspacing="0" cellpadding="5" style="width:1%;">
				<tr>
					<td nowrap><?=GetMessage("IMYIE_TOP")?> (px)</td>
					<td nowrap><?=GetMessage("IMYIE_BOT")?> (px)</td>
				</tr>
				<tr>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,10,"page_edit_line_padding_top",$page_edit_line_padding_top)?></td>
					<td><?CIMYIELittleAdminUtils::ShowIntSelect(0,10,"page_edit_line_padding_bot",$page_edit_line_padding_bot)?></td>
				</tr>
			</table>
		</td>
	</tr>
	
	
	<?//_______________________ page_list _______________________//?>
	<?$tabControl->BeginNextTab();?>
	<?
	$page_list_minimization_level = COption::GetOptionInt('imyie.littleadmin', 'page_list_minimization_level', 0);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_PAGE_LIST_MINIMIZATION_LEVEL")?></td>
		<td valign="top" width="50%"><?CIMYIELittleAdminUtils::ShowIntSelect(0,6,"page_list_minimization_level",$page_list_minimization_level)?></td>
	</tr>
	
	
	<?//_______________________ constrols _______________________//?>
	<?$tabControl->BeginNextTab();?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("IMYIE_CONSTROLS_INPUT_TEXT")?></td>
	</tr>
	<?
	$constrols_input_text_height = COption::GetOptionInt('imyie.littleadmin', 'constrols_input_text_height', 26);
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_CONSTROLS_INPUT_TEXT_HEIGHT")?></td>
		<td valign="top" width="50%">
			<select name="constrols_input_text_height">
				<option value="22"<?if($constrols_input_text_height==22):?> selected <?endif;?>>22</option>
				<option value="24"<?if($constrols_input_text_height==24):?> selected <?endif;?>>24</option>
				<option value="26"<?if($constrols_input_text_height==26):?> selected <?endif;?>>26</option>
				<option value="28"<?if($constrols_input_text_height==28):?> selected <?endif;?>>28</option>
				<option value="30"<?if($constrols_input_text_height==30):?> selected <?endif;?>>30</option>
			</select>
		</td>
	</tr>
	<?
	$constrols_input_text_border_radius = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_border_radius', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_CONSTROLS_INPUT_TEXT_BORDER_RADIUS_USING")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="constrols_input_text_border_radius" value="Y"<?if($constrols_input_text_border_radius=="Y"):?> checked<?endif;?> /></td>
	</tr>
	<?
	$constrols_input_text_box_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_box_shadow', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_CONSTROLS_INPUT_TEXT_BOX_SHADOW_USING")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="constrols_input_text_box_shadow" value="Y"<?if($constrols_input_text_box_shadow=="Y"):?> checked<?endif;?> /></td>
	</tr>
	<?
	$constrols_input_text_hover_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_hover_shadow', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_CONSTROLS_INPUT_TEXT_HOVER_SHADOW_USING")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="constrols_input_text_hover_shadow" value="Y"<?if($constrols_input_text_hover_shadow=="Y"):?> checked<?endif;?> /></td>
	</tr>
	<?
	$constrols_input_text_focus_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_focus_shadow', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_CONSTROLS_INPUT_TEXT_FOCUS_SHADOW_USING")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="constrols_input_text_focus_shadow" value="Y"<?if($constrols_input_text_focus_shadow=="Y"):?> checked<?endif;?> /></td>
	</tr>
	
	
	<?//_______________________ not_style _______________________//?>
	<?$tabControl->BeginNextTab();?>
	<?
	$not_style_add_buttons = COption::GetOptionString('imyie.littleadmin', 'not_style_add_buttons', "Y");
	?>
	<tr>
		<td width="50%"><?=GetMessage("IMYIE_NOT_STYLE_ADD_BUTTONS")?></td>
		<td valign="top" width="50%"><input type="checkbox" name="not_style_add_buttons" value="Y"<?if($not_style_add_buttons=="Y"):?> checked<?endif;?> /></td>
	</tr>
	
	
<?$tabControl->Buttons();?>
	<script type="text/javascript">
	function RestoreDefaults()
	{
		if(confirm('<?=GetMessage("IMYIE_BTN_RESTORE_DEFAULT_NOTE")?>'))
			window.location = "<?=$APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANG?>&mid=<?=urlencode($mid)?>&<?=bitrix_sessid_get()?>";
	}
	</script>
	<input type="submit" name="save" value="<?=GetMessage("IMYIE_BTN_SAVE")?>" class="adm-btn-save">
	<input type="button" name="save" value="<?=GetMessage("IMYIE_BTN_RESTORE_DEFAULT")?>" onclick="RestoreDefaults();">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>