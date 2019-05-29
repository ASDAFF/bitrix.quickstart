<?
IncludeModuleLangFile(__FILE__);

class CIMYIELittleAdmin
{
	function OnPageStartHandler()
	{
		global $APPLICATION;
		
		$APPLICATION->SetAdditionalCSS("/bitrix/themes/.default/imyie.littleadmin.css");
	}
	
	function OnAdminContextMenuShowHandler(&$items)
	{
		global $APPLICATION;
		
		$IBLOCK_ID = IntVal($_REQUEST["IBLOCK_ID"]);
		$find_section_section = $_REQUEST["find_section_section"];
		
		if($APPLICATION->GetCurPage()=="/bitrix/admin/iblock_list_admin.php" && CModule::IncludeModule('iblock') && $IBLOCK_ID>0)
		{
			$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);

			if(!$arIBlock["ELEMENT_ADD"])
				$arIBlock["ELEMENT_ADD"] = GetMessage("DEFAULT_ELEMENT_ADD");
			if(!$arIBlock["SECTION_ADD"])
				$arIBlock["SECTION_ADD"] = GetMessage("DEFAULT_SECTION_ADD");
			
			$url_element_add = CIBlock::GetAdminElementEditLink($IBLOCK_ID,0,array(
			'IBLOCK_SECTION_ID'=>$find_section_section,'find_section_section'=>$find_section_section,'from' => 'iblock_list_admin',));
			
			$url_section_add = CIBlock::GetAdminSectionEditLink($IBLOCK_ID,0,array(
			'IBLOCK_SECTION_ID'=>$find_section_section,'find_section_section'=>$find_section_section,'from' => 'iblock_list_admin',));
			
			$items[] = array(
				"TEXT" => $arIBlock["ELEMENT_ADD"],
				"ICON" => "",//btn_new
				"TITLE" => "",
				"LINK" => $url_element_add
			);
			$items[] = array(
				"TEXT" => $arIBlock["SECTION_ADD"],
				"ICON" => "",//btn_new
				"TITLE" => "",
				"LINK" => $url_section_add
			);
		}
	}
	
	function RestoreDefaultSettings()
	{
		//_______________________ different _______________________//
		COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_top',5  );
		COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_right', 0 );
		COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_bot', 5 );
		COption::SetOptionInt('imyie.littleadmin', 'page_title_margin_left', 0 );
		
		COption::SetOptionInt('imyie.littleadmin', 'notes_margin_top', 5 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_margin_right', 10 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_margin_bot', 10 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_margin_left', 0 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_padding_top', 10 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_padding_right', 20 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_padding_bot', 10 );
		COption::SetOptionInt('imyie.littleadmin', 'notes_padding_left', 9 );
		
		COption::SetOptionInt('imyie.littleadmin', 'navi_padding_top', 5 );
		COption::SetOptionInt('imyie.littleadmin', 'navi_padding_right', 0 );
		COption::SetOptionInt('imyie.littleadmin', 'navi_padding_bot', 1 );
		COption::SetOptionInt('imyie.littleadmin', 'navi_padding_left', 0 );
		
		COption::SetOptionString('imyie.littleadmin', 'navi_number_pos', "left" );
	
		COption::SetOptionString('imyie.littleadmin', 'gadget_using', "Y" );
		
		//_______________________ left_menu _______________________//
		COption::SetOptionString('imyie.littleadmin', 'left_menu_using', "Y" );
		
		//_______________________ page_edit _______________________//
		COption::SetOptionInt('imyie.littleadmin', 'page_edit_line_padding_top', 2 );
		COption::SetOptionInt('imyie.littleadmin', 'page_edit_line_padding_bot', 3 );
		
		//_______________________ page_list _______________________//
		COption::SetOptionInt('imyie.littleadmin', 'page_list_minimization_level', 0 );
		
		//_______________________ constrols _______________________//
		COption::SetOptionInt('imyie.littleadmin', 'constrols_input_text_height', 26 );
		COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_border_radius', "Y" );
		COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_box_shadow', "Y" );
		COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_hover_shadow', "Y" );
		COption::SetOptionString('imyie.littleadmin', 'constrols_input_text_focus_shadow', "Y" );
		
		COption::SetOptionInt('imyie.littleadmin', 'constrols_btn_size', 1 );
		COption::SetOptionString('imyie.littleadmin', 'constrols_btn_border_radius', "Y" );
		
		//_______________________ not_style _______________________//
		$not_style_add_buttons_OLD = COption::GetOptionString('imyie.littleadmin', 'not_style_add_buttons', "Y");
		if($not_style_add_buttons_OLD!="Y")
		{
			COption::SetOptionString('imyie.littleadmin', 'not_style_add_buttons', "Y" );
			RegisterModuleDependences("main", "OnAdminContextMenuShow", "imyie.littleadmin", "CIMYIELittleAdmin", "OnAdminContextMenuShowHandler", "500");
		}
		
		return TRUE;
	}
}
?>
