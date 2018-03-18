<?
class CIMYIELittleAdminStyle
{
	function GeneratorCSSAndRewrite()
	{
		$fullCSS = "";
		$fullCSS .= CIMYIELittleAdminStyle::LeftMenu();
		$fullCSS .= CIMYIELittleAdminStyle::PageTitle();
		$fullCSS .= CIMYIELittleAdminStyle::Notes();
		$fullCSS .= CIMYIELittleAdminStyle::PageEdit();
		$fullCSS .= CIMYIELittleAdminStyle::TableListActionCheckbox();
		$fullCSS .= CIMYIELittleAdminStyle::TableListCheckbox();
		$fullCSS .= CIMYIELittleAdminStyle::TableListCell();
		$fullCSS .= CIMYIELittleAdminStyle::TableListFooter();
		$fullCSS .= CIMYIELittleAdminStyle::Navigation();
		$fullCSS .= CIMYIELittleAdminStyle::Gadgets();
		$fullCSS .= CIMYIELittleAdminStyle::Inputs();
		$fullCSS .= CIMYIELittleAdminStyle::SelectBox();
		$fullCSS .= CIMYIELittleAdminStyle::Checkbox();
		
		RewriteFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/imyie.littleadmin.css", $fullCSS);
	}
	
	//_______________________ left_menu _______________________//
	function LeftMenu()
	{
		$left_menu_using = COption::GetOptionString('imyie.littleadmin', 'left_menu_using', "Y");
		
		if($left_menu_using=="Y")
		{
$css = '
.adm-submenu-items-title{
	margin-bottom:5px;
}
.adm-submenu-item-name{
	margin:0;
}
.adm-submenu-item-name,
.adm-submenu-item-name:hover,
.adm-submenu-item-name-link,
.adm-submenu-item-name:hover .adm-submenu-item-name-link,
.adm-submenu-current-fav,
.adm-submenu-current-fav .adm-submenu-item-name-link,
.adm-submenu-item-active > .adm-submenu-item-name > .adm-submenu-item-name-link,
.adm-submenu-item-active > .adm-submenu-item-name,
.adm-favorites-sub-menu-wrap,
.adm-favorites-sub-menu-wrap .adm-submenu-item-name{
	height:auto !important;
}
.adm-submenu-item-name-link-text{
	padding-top:3px;
	padding-bottom:2px;
}
.adm-submenu-item-arrow{
	top:-5px;
}
.adm-submenu-item-link-icon{
	margin-top:-1px;
}
';
		} else {
			$css = '';
		}
		return $css;
	}
	
	//_______________________ page_title _______________________//
	function PageTitle()
	{
		$page_title_margin_top = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_top', 5);
		$page_title_margin_right = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_right', 0);
		$page_title_margin_bot = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_bot', 5);
		$page_title_margin_left = COption::GetOptionInt('imyie.littleadmin', 'page_title_margin_left', 0);
$css = '
.adm-title{
	margin:'.$page_title_margin_top.'px '.$page_title_margin_right.'px '.$page_title_margin_bot.'px '.$page_title_margin_left.'px;
}
';
		return $css;
	}
	
	//_______________________ notes _______________________//
	function Notes()
	{
		$notes_margin_top = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_top', 5);
		$notes_margin_right = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_right', 10);
		$notes_margin_bot = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_bot', 10);
		$notes_margin_left = COption::GetOptionInt('imyie.littleadmin', 'notes_margin_left', 0);
		
		$notes_padding_top = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_top', 10);
		$notes_padding_right = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_right', 20);
		$notes_padding_bot = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_bot', 10);
		$notes_padding_left = COption::GetOptionInt('imyie.littleadmin', 'notes_padding_left', 9);
$css = '
#bx-admin-prefix .adm-info-message-wrap{
	position:static !important;
}
#bx-admin-prefix .adm-info-message-wrap .adm-info-message{
	margin:'.$notes_margin_top.'px '.$notes_margin_right.'px '.$notes_margin_bot.'px '.$notes_margin_left.'px;
	padding:'.$notes_padding_top.'px '.$notes_padding_right.'px '.$notes_padding_bot.'px '.$notes_padding_left.'px;
}
';
		return $css;
	}
	
	//_______________________ element and section edit _______________________//
	function PageEdit()
	{
		$page_edit_line_padding_top = COption::GetOptionInt('imyie.littleadmin', 'page_edit_line_padding_top', 2);
		$page_edit_line_padding_bot = COption::GetOptionInt('imyie.littleadmin', 'page_edit_line_padding_bot', 3);
$css = '
#adm-workarea .adm-detail-content-cell-l,
#adm-workarea .adm-detail-content-cell-r {
	padding-top:'.$page_edit_line_padding_top.'px !important;
	padding-bottom:'.$page_edit_line_padding_bot.'px !important;
}
#adm-workarea .adm-input-file-control{
	margin-top:5px;
	margin-bottom:0px !important;
}
';
		return $css;
	}
	
	//_______________________ table_list ==>> action_menu _______________________//
	function TableListActionCheckbox()
	{
		$page_list_minimization_level = COption::GetOptionInt('imyie.littleadmin', 'page_list_minimization_level', 0);
$css = '
.adm-list-table-cell.adm-list-table-popup-block{
	padding:'.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px '.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px;
}
';
		return $css;
	}

	//_______________________ table_list ==>> checkbox _______________________//
	function TableListCheckbox()
	{
		$page_list_minimization_level = COption::GetOptionInt('imyie.littleadmin', 'page_list_minimization_level', 0);
$css = '
.adm-list-table-cell.adm-list-table-checkbox{
	padding:'.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px '.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px;
}
';
		return $css;
	}

	//_______________________ table_list ==>> cell _______________________//
	function TableListCell()
	{
		$page_list_minimization_level = COption::GetOptionInt('imyie.littleadmin', 'page_list_minimization_level', 0);
$css = '
.adm-list-table-cell{
	padding:'.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px '.(5+$page_list_minimization_level).'px '.(10+$page_list_minimization_level).'px;
}
.adm-list-table-cell.align-center {
	padding:'.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px '.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px;
}
.adm-list-table-cell.align-right{
	padding:'.(5+$page_list_minimization_level).'px '.(10+$page_list_minimization_level).'px '.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px;
}
.adm-list-table-cell.align-left{
	padding:'.(5+$page_list_minimization_level).'px '.($page_list_minimization_level).'px '.(5+$page_list_minimization_level).'px '.(10+$page_list_minimization_level).'px;
}
';
		return $css;
	}

	//_______________________ table_list ==>> footer _______________________//
	function TableListFooter()
	{
		$page_list_minimization_level = COption::GetOptionInt('imyie.littleadmin', 'page_list_minimization_level', 0);
		switch ($page_list_minimization_level) {
			case 0:
				$val = 4;
				break;
			case 1:
				$val = 4;
				break;
			case 2:
				$val = 5;
				break;
			case 3:
				$val = 6;
				break;
			case 4:
				$val = 7;
				break;
			case 5:
				$val = 7;
				break;
			default:
				$val = 4;
				break;
		}
$css = '
.adm-list-table-footer{
	padding-top:'.$val.'px;
}
';
		return $css;
	}

	//_______________________ navigation _______________________//
	function Navigation()
	{
		$navi_padding_top = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_top', 5);
		$navi_padding_right = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_right', 0);
		$navi_padding_bot = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_bot', 1);
		$navi_padding_left = COption::GetOptionInt('imyie.littleadmin', 'navi_padding_left', 0);
		
		$navi_number_pos = COption::GetOptionString('imyie.littleadmin', 'navi_number_pos', "left");
$css = '
.adm-navigation{
	padding:'.$navi_padding_top.'px '.$navi_padding_right.'px '.$navi_padding_bot.'px '.$navi_padding_left.'px;
}
.adm-navigation .adm-nav-pages-block{
	min-width:0px;
}
.adm-nav-pages-number-block{
	text-align:'.$navi_number_pos.';
}
';
		return $css;
	}

	//_______________________ gadgets _______________________//
	function Gadgets()
	{
		$gadget_using = COption::GetOptionString('imyie.littleadmin', 'gadget_using', "Y");
		
		if($gadget_using=="Y")
		{
$css = '
.gadgetholder .bx-gadgets-top-center{
	height:37px;
}
.gadgetholder .bx-gadgets-top-title{
	font-size:19px;
	padding:5px 0px 0px 12px;
}
.gadgetholder .bx-gadgets-top-button{
	padding: 5px 11px 0px 0px;
}
';
		} else {
			$css = '';
		}
		return $css;
	}
	
	//_______________________ inputs _______________________//
	function Inputs()
	{
		$constrols_input_text_height = COption::GetOptionInt('imyie.littleadmin', 'constrols_input_text_height', 26);
		$constrols_input_text_border_radius = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_border_radius', "Y");
		$constrols_input_text_box_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_box_shadow', "Y");
		$constrols_input_text_hover_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_hover_shadow', "Y");
		$constrols_input_text_focus_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_focus_shadow', "Y");
		
$css = '
#adm-workarea .adm-input,
#adm-workarea input[type="text"],
#adm-workarea input[type="password"],
#adm-workarea input[type="email"]{
	height:'.$constrols_input_text_height.'px;
	'.($constrols_input_text_border_radius=="Y" ? "border-radius:0px;" : "border-radius:4px;").'
	'.($constrols_input_text_box_shadow=="Y" ? "-webkit-box-shadow:0 0 0 0 rgba(255, 255, 255, 0.3), inset 0 0 0 0 rgba(180, 188, 191, 0.7);" : "").'
	'.($constrols_input_text_box_shadow=="Y" ? "box_shadow:0 0 0 0 rgba(255, 255, 255, 0.3), inset 0 0px 0px 0px rgba(180, 188, 191, 0.7);" : "").'
	margin-left:0px;
}
#adm-workarea .adm-input-wrap .adm-input{
	height:'.$constrols_input_text_height.'px;
	'.($constrols_input_text_border_radius=="Y" ? "border-radius:0px;" : "border-radius:4px;").'
	margin-left:0px;
}';
		if($constrols_input_text_hover_shadow=="Y")
		{
$css.= '
#adm-workarea .adm-input:hover,
#adm-workarea input[type="text"]:hover,
#adm-workarea input[type="password"]:hover,
#adm-workarea input[type="email"]:hover{
	-moz-box-shadow: 0 0 4px 0 #B5B5B5;
	-webkit-box-shadow: 0 0 4px 0 #B5B5B5;
	box-shadow: 0 0 4px 0 #B5B5B5;
}
';
		}
		if($constrols_input_text_focus_shadow=="Y")
		{
$css.= '
#adm-workarea .adm-input:focus,
#adm-workarea input[type="text"]:focus,
#adm-workarea input[type="password"]:focus,
#adm-workarea input[type="email"]:focus{
	-moz-box-shadow: 0 0 4px 0 #B5B5B5;
	-webkit-box-shadow: 0 0 4px 0 #B5B5B5;
	box-shadow: 0 0 4px 0 #B5B5B5;
}
';
		}
		return $css;
	}

	//_______________________ function_name _______________________//
	function SelectBox()
	{
		$constrols_input_text_height = COption::GetOptionInt('imyie.littleadmin', 'constrols_input_text_height', 26) + 1;
		$constrols_input_text_border_radius = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_border_radius', "Y");
		$constrols_input_text_box_shadow = COption::GetOptionString('imyie.littleadmin', 'constrols_input_text_box_shadow', "Y");
		
		switch($constrols_input_text_height) {
			case 23:
				$padding = 1;
				$filtr_arrow_top = 8;
				$calendar_icon_top = 4;
				$calendar_icon_right = 6;
				break;
			case 25:
				$padding = 2;
				$filtr_arrow_top = 8;
				$calendar_icon_top = 5;
				$calendar_icon_right = 7;
				break;
			case 27:
				$padding = 3;
				$filtr_arrow_top = 9;
				$calendar_icon_top = 6;
				$calendar_icon_right = 8;
				break;
			case 29:
				$padding = 4;
				$filtr_arrow_top = 9;
				$calendar_icon_top = 7;
				$calendar_icon_right = 9;
				break;
			case 31:
				$padding = 5;
				$filtr_arrow_top = 10;
				$calendar_icon_top = 7;
				$calendar_icon_right = 10;
				break;
			default:
				$padding = 5;
				$filtr_arrow_top = 10;
				$calendar_icon_top = 7;
				$calendar_icon_right = 10;
				break;
		}
$css = '
#adm-workarea select{
	height:'.$constrols_input_text_height.'px;
	padding:'.$padding.'px;
	'.($constrols_input_text_border_radius=="Y" ? "border-radius:0px;" : "border-radius:4px;").'
	'.($constrols_input_text_box_shadow=="Y" ? "-webkit-box-shadow:0 0 0 0 rgba(255, 255, 255, 0.3), inset 0 0 0 0 rgba(180, 188, 191, 0.7);" : "").'
	'.($constrols_input_text_box_shadow=="Y" ? "box_shadow:0 0 0 0 rgba(255, 255, 255, 0.3), inset 0 0px 0px 0px rgba(180, 188, 191, 0.7);" : "").'
	margin-left:0px;
}
#adm-workarea select[multiple], #adm-workarea select[size] {
	height:auto;
	min-height:29px;
}
#bx-admin-prefix .adm-input-wrap-calendar .adm-calendar-icon{
	right:'.$calendar_icon_right.'px;
	top:'.$calendar_icon_top.'px;
}
#adm-workarea .adm-select-wrap::after{
	top:'.$filtr_arrow_top.'px;
}
';
		return $css;
	}
	
	//_______________________ checkbox _______________________//
	function Checkbox()
	{
$css = '

';
		return $css;
	}
	
	//_______________________ function_name _______________________//
	function FunctionName()
	{
$css = '

';
		return $css;
	}
}
?>
