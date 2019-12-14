<?
/************************************
*
* General class
* last update 26.06.2015
*
************************************/

IncludeModuleLangFile(__FILE__);

class RSMonopoly{
	
	function ShowPanel()
    {
        if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "redsign.monopoly")
        {
            $GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/redsign/monopoly/css/panel.css"); 

            $arMenu = Array(
                Array(        
                    "ACTION" => "jsUtils.Redirect([], '".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=redsign:monopoly&".bitrix_sessid_get())."')",
                    "ICON" => "bx-popup-item-wizard-icon",
                    "TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
                    "TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
                )
            );

            $GLOBALS["APPLICATION"]->AddPanelButton(array(
                "HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=redsign:monopoly&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
                "ID" => "monopoly_wizard",
                "ICON" => "bx-panel-site-wizard-icon",
                "MAIN_SORT" => 2500,
                "TYPE" => "BIG",
                "SORT" => 10,    
                "ALT" => GetMessage("SCOM_BUTTON_DESCRIPTION"),
                "TEXT" => GetMessage("SCOM_BUTTON_NAME"),
                "MENU" => $arMenu,
            ));
        }
    }

	public static function saveSettings() {

		$arHeadType = array('type1','type2','type3',);
		if(isset($_REQUEST['headType']) && in_array($_REQUEST['headType'],$arHeadType)) {
			COption::SetOptionString('redsign.monopoly', 'headType', htmlspecialchars($_REQUEST['headType']) );
		}

		$arHeadStyle = array('style1','style2','style3',);
		if(isset($_REQUEST['headStyle']) && in_array($_REQUEST['headStyle'],$arHeadStyle)) {
			COption::SetOptionString('redsign.monopoly', 'headStyle', htmlspecialchars($_REQUEST['headStyle']) );
		}

		$arFilterStyle = array('ftype0','ftype1','ftype2',);
		if(isset($_REQUEST['filterType']) && in_array($_REQUEST['filterType'],$arFilterStyle)) {
			COption::SetOptionString('redsign.monopoly', 'filterType', htmlspecialchars($_REQUEST['filterType']) );
		}

		$blackMode = 'N';
		if($_REQUEST['blackMode']=='Y') {
			$blackMode = 'Y';
			COption::SetOptionString('redsign.monopoly', 'blackMode', 'Y' );
		}
		COption::SetOptionString('redsign.monopoly', 'blackMode', $blackMode );

		if(isset($_REQUEST['gencolor'])) {
			$gencolor = htmlspecialchars($_REQUEST['gencolor']);
			COption::SetOptionString('redsign.monopoly', 'gencolor', $gencolor );
		} else {
			$gencolor = COption::GetOptionString('redsign.monopoly', 'gencolor', '' );
		}

		$arSidebarPos = array('pos1','pos2',);
		if(isset($_REQUEST['sidebarPos']) && in_array($_REQUEST['sidebarPos'],$arSidebarPos)) {
			COption::SetOptionString('redsign.monopoly', 'sidebarPos', htmlspecialchars($_REQUEST['sidebarPos']) );
		}

		// main page settings

		if($_REQUEST['MSFichi']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSFichi', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSFichi', 'N' );
		}

		if($_REQUEST['MSCatalog']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSCatalog', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSCatalog', 'N' );
		}

		if($_REQUEST['MSService']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSService', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSService', 'N' );
		}

		if($_REQUEST['MSAboutAndReviews']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSAboutAndReviews', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSAboutAndReviews', 'N' );
		}

		if($_REQUEST['MSNews']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSNews', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSNews', 'N' );
		}

		if($_REQUEST['MSPartners']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSPartners', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSPartners', 'N' );
		}

		if($_REQUEST['MSGallery']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSGallery', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSGallery', 'N' );
		}
		
		if($_REQUEST['MSSmallBanners']=='Y') {
			COption::SetOptionString('redsign.monopoly', 'MSSmallBanners', 'Y' );
		} else {
			COption::SetOptionString('redsign.monopoly', 'MSSmallBanners', 'N' );
		}

		// developer mode

		if(isset($_REQUEST['optionFrom'])) {
			if($_REQUEST['optionFrom']=='module') {
				COption::SetOptionString('redsign.monopoly', 'optionFrom', 'module' );
			} elseif ($_REQUEST['optionFrom']=='session') {
				COption::SetOptionString('redsign.monopoly', 'optionFrom', 'session' );
			}
		}

		self::generateCssColorFile();
	}

	public static function getSettings($paramName='',$default='') {
		$return = '';
		$return = $default;
		if($paramName!='') {
			$optionFrom = COption::GetOptionString('redsign.monopoly', 'optionFrom', 'module');
			if($optionFrom=='session') {
				if(isset($_SESSION[$paramName])) {
					$return = $_SESSION[$paramName];
				}
			} else {
				$return = COption::GetOptionString('redsign.monopoly', $paramName, $default);
			}
		}
		return $return;
	}

	public static function addData(&$arItems,$params=array()) {
		if( is_array($arItems) && count($arItems)>0 ) {
			// prepare data
			$arElements = array();
			foreach($arItems as $iKeyItem => $arItem) {
				$arElements[$arItems[$iKeyItem]['ID']] = &$arItems[$iKeyItem];
			}
			// /prepare data
			foreach($arElements as $iElementId => $arElement) {
				// prices
				$arElements[$iElementId]['RS_PRICE'] = RSMONOPOLY_addPrices($arElement,$params);
			}
		}
	}

	public static function generateCssColorFile() {
		$color = COption::GetOptionString('redsign.monopoly', 'gencolor', '0084c9');
		$menuTextColor = COption::GetOptionString('redsign.monopoly', 'textColorMenu', '');
		$reverse = COption::GetOptionString('redsign.monopoly', 'blackMode', 'N');
		$file_path = '/bitrix/templates/monop/styles/color.css';
		$darketPersent = 15;
		list($rr,$gg,$bb) = sscanf($color, '%2x%2x%2x');
	    if( $rr>0 ) { $rr = $rr - ( floor($rr/100*$darketPersent) ); }
	    if( $gg>0 ) { $gg = $gg - ( floor($gg/100*$darketPersent) ); }
	    if( $bb>0 ) { $bb = $bb - ( floor($bb/100*$darketPersent) ); }
	    $darkenColorRR = dechex($rr);
	    $darkenColorGG = dechex($gg);
	    $darkenColorBB = dechex($bb);
	    if( strlen($darkenColorRR)<2 ) { $darkenColorRR = '0'.$darkenColorRR; }
	    if( strlen($darkenColorGG)<2 ) { $darkenColorGG = '0'.$darkenColorGG; }
	    if( strlen($darkenColorBB)<2 ) { $darkenColorBB = '0'.$darkenColorBB; }
	    $darketnColor = $darkenColorRR.$darkenColorGG.$darkenColorBB;

	    $styles = self::getSelectors($color,$darketnColor);
	    //if($menuTextColor!=''){
	    //	$styles.= self::getSelectorsMenuTextColor($menuTextColor);
		//}

		if($reverse=='Y') {
			$reverse = self::blackTheme();
			$styles.= $reverse;
		}
		RewriteFile($_SERVER["DOCUMENT_ROOT"].$file_path, $styles);
	}

	public static function getSelectors($color='0084c9',$darketnColor='006396') {
		$stableColor = 'ebebeb';
		$styles = '/* simple color */
html h4, html .h4, html h6, html .h6,
h2 a:hover,
html .text-primary,
html ul > li:before,
html ol > li:before,
html .btn-link,
html body .aprimary,
html body a.aprimary,
html .wrapper input[type="checkbox"]:checked + label:after,
html .wrapper input[type="radio"]:checked + label:before,
html .fancybox-wrap input[type="checkbox"]:checked + label:after,
html .fancybox-wrap input[type="radio"]:checked + label:before,
html .wrapper input[type="checkbox"]:checked + .bx_filter_param_text:after,
html .wrapper input[type="radio"]:checked + .bx_filter_param_text:before,
html .wrapper .js-compare.checked:after,
html .mainform .calendar-wrap a:hover,
html .mainform .rating .rating-icon:hover,
html .mainform .rating .rating-icon.hover,
html header .contacts .phone span,
html footer .contacts .phone span,
html ul.nav-sidebar li.active > a,
html ul.nav-sidebar li.dropdown-submenu.showed > a,
html .pagination > li > a:hover,
html .pagination > li > a:focus,
html .pagination > li > span:hover,
html .pagination > li > span:focus,
html .catalogsorter .template a:hover,
html .catalogsorter .template a.selected,
html .backshare .detailback:hover i,
header.style2.no-border .navbar-default .navbar-nav > .active > a,
header.style2.border .navbar-default .navbar-nav > .active > a,
header.style4.no-border .navbar-default .navbar-nav > .active > a,
header.style4.border .navbar-default .navbar-nav > .active > a,
html .banner_title_whitewrap,
html .banner_desc_whitewrap {
	color: #'.$color.';
}
@media (max-width: 991px) {
	header .navbar-default .navbar-nav > .active > a,
	header .navbar-default .navbar-nav .open > a,
	header .navbar-default .navbar-nav .open > a:focus {
		color: #'.$color.';
	}
}
html .btn-primary,
html .label-primary,
html .nav-tabs > li.active > a,
html .nav-tabs > li.active > a:hover,
html .nav-tabs > li.active > a:focus,
html .pagination > .active > a,
html .pagination > .active > a:hover,
html .pagination > .active > a:focus,
html .pagination > .active > span,
html .pagination > .active > span:hover,
html .pagination > .active > span:focus,
html .owl-carousel.owl_banners_colors .owl-dots .owl-dot:hover span,
html .owl-carousel.owl_banners_colors .owl-dots .owl-dot.active span,
html .withdots .owl-dots .owl-dot:hover span,
html header.style2.color .main-menu-nav,
html .owlslider .owl-nav div:hover,
html .owl .owl-nav div:hover,
html header.style4.color .main-menu-nav,
html header.color .navbar-nav li,
html header.color .navbar-default .navbar-toggle,
html .smartfilter .bx_ui_slider_pricebar_V,
html .smartfilter .bx_ui_slider_handle:hover,
html .rs-banners-container .owl-theme .owl-dots .owl-dot.active span,
html .rs-banners-container .owl-theme .owl-dots .owl-dot:hover span,
html .rs-banners-container .rs-banners_infowrap .rs-banners_button,
html .rs-banners-container .rs-banners_bottom-line  {
	background-color: #'.$color.';
}
@media (max-width: 991px) {
	html header.color .navbar-default .navbar-nav .dropdown-menu li > a,
	html header.color .navbar-default .navbar-nav .dropdown-menu li.active > a,
	html header.color .navbar-default .navbar-nav .open .dropdown-menu li > a {
		background-color: #'.$color.';
	}
}
html .btn-default,
html .btn-primary,
html .nav-tabs > li.active > a,
html .nav-tabs > li.active > a:hover,
html .nav-tabs > li.active > a:focus,
html .pagination > .active > a,
html .pagination > .active > a:hover,
html .pagination > .active > a:focus,
html .pagination > .active > span,
html .pagination > .active > span:hover,
html .pagination > .active > span:focus,
html header .vertical_blue_line,
html .bx_filter_param_label.active .bx_filter_param_btn,
html .bx_filter_param_label:hover .bx_filter_param_btn,
html .smartfilter .bx_filter_popup_result:hover {
	border-color: #'.$color.';
}
html footer {
	border-top-color: #'.$color.';
}
html .smartfilter .bx_filter_popup_result:hover .arrow {
	border-right-color: #'.$color.';
}
html header.style2,
html header .navbar .search-open,
html h2.coolHeading .secondLine,
html .h2.coolHeading .secondLine,
html footer .footer_logo_wrap,
html .owl_banners_colors,
html .dropdown-menu ul,
html ul.dropdown-menu,
html .shops .search_city ul.cities_list,
html body .popup-window.smartFilterSelectbox .popup-window-content ul {
	border-bottom-color: #'.$color.';
}
@media (min-width: 992px) {
	html header,
	header .main-menu-nav .dropdown-menu,
	html .smartfilter.ftype2 ul .bx_filter_prop > .body {
		border-bottom-color: #'.$color.';
	}
}
/* header "M" */
html .logo .m .m1 {
	border-bottom-color: #'.$color.';
}
html .logo .m .m2 {
	border-right-color: #'.$color.';
}
/***********************************************************************/
/* darken color */
html header .navbar .nav > li > .search:hover {
	color: #'.$darketnColor.';
}
html .btn-default:hover,
html .btn-default:focus,
html .btn-default.focus,
html .btn-default:active,
html .btn-default.active,
html .btn-primary:hover,
html .btn-primary:focus,
html .btn-primary.focus,
html .btn-primary:active,
html .btn-primary.active,
html .open > .btn-primary.dropdown-toggle,
html .owl_banners_colors .owl-dots .owl-dot.active span,
html header.color .navbar-default .navbar-toggle:hover {
	background-color: #'.$darketnColor.';
}
@media (min-width: 992px) {
	header.color .navbar-default .navbar-nav li.active > a {
		background-color: #'.$darketnColor.';
	}
	header.color .navbar-default .navbar-nav li:hover > a,
	header.color .navbar-default .navbar-nav li.active > a:hover {
		background-color: #'.$stableColor.';
	}
}
@media (max-width: 991px) {
	header.color .navbar-default .navbar-nav > li:hover > a,
	header.color .navbar-default .navbar-nav li.active > a,
	header.color .navbar-default .navbar-nav li.open > a,
	header.color .navbar-default .navbar-nav .open li.active > a,
	header.color .navbar-default .navbar-nav .open li.open > a,
	header.color .navbar-default .navbar-nav .dropdown-menu li > a:hover,
	header.color .navbar-default .navbar-nav .dropdown-menu li > a:focus,
	header.color .navbar-default .navbar-nav .dropdown-menu li.active > a:hover,
	header.color .navbar-default .navbar-nav .dropdown-menu li.active > a:focus,
	header.color .navbar-default .navbar-nav .open .dropdown-menu li > a:hover,
	header.color .navbar-default .navbar-nav .open .dropdown-menu li > a:focus,
	header.color .navbar-default .navbar-nav .open .dropdown-menu li.active > a:hover,
	header.color .navbar-default .navbar-nav .open .dropdown-menu li.active > a:focus,
	header.color .navbar-default .navbar-nav .open .dropdown-menu li.open > a,
	header.color .navbar-default .navbar-nav .dropdown-menu li.open > a,
	html header.color .navbar-default .navbar-nav .open .dropdown-menu li.active > a,
	html header.color .navbar-default .navbar-nav .open .dropdown-menu li.active.open > a {
		background-color: #'.$darketnColor.';
	}
}
html .btn-default:hover,
html .btn-default:focus,
html .btn-default.focus,
html .btn-default:active,
html .btn-default.active,
html .btn-primary:hover,
html .btn-primary:focus,
html .btn-primary.focus,
html .btn-primary:active,
html .btn-primary.active,
html .open > .btn-primary.dropdown-toggle {
	border-color: #'.$darketnColor.';
}
';
		return $styles;
	}

	public static function blackTheme() {
		$styles = '/* blackTheme */
/* background color */
html body,
html .btn-default,
html .dropdown-menu,
html body .popup-window.smartFilterSelectbox .popup-window-content ul,
html .gallery .item .data,
html .customerreviews .item .review,
html .js-detail .proptable table td > span,
html .js-detail .proptable table .val > span,
html .nav > li > a:hover,
html .nav > li > a:focus,
html .panel-default > .panel-heading,
html .panel,
html .pagination > li > a,
html .pagination > li > span,
html header .navbar .search-open,
html .form-control,
html .mainform .inner-wrap,
html .fancybox-skin,
html .detail_subs,
html .backshare .detailback i,
html .smartfilter,
html .smartfilter ul .bx_filter_prop > .body {
	background-color: #252525;
}
html .mega-menu .dropdown-menu,
html .main-menu-nav .dropdown > ul.dropdown-menu {
	background-color: transparent;
}
html .footer_copyright,
html .table-striped > tbody > tr:nth-of-type(odd) {
	background-color: #1d1d1e;
}
html ul.nav-sidebar li a:hover,
html ul.nav-sidebar li.dropdown-submenu.showed > a:hover,
html ul.nav-sidebar li.dropdown-submenu.showed > a,
html .navbar-default .navbar-nav > .active > a,
html .navbar-default .navbar-nav > .active > a:hover,
html .navbar-default .navbar-nav > .active > a:focus,
html ul.nav-sidebar .dropdown-submenu > ul,
html .shops .shops_list .item:hover,
html .navbar-default .navbar-nav > .open > a,
html .navbar-default .navbar-nav > .open > a:hover,
html .navbar-default .navbar-nav > .open > a:focus,
html .shops .search_city ul.cities_list > li > a:hover,
html .shops .search_city ul.cities_list > li > a:focus,
html .table-striped > tbody > tr:nth-of-type(odd):hover,
html .table-hover > tbody > tr:hover,
html .navbar-default .navbar-toggle:hover,
html .navbar-default .navbar-toggle:focus,
html .timeline.row:before,
html .timeline.row .item .pointer span,
html .form-control[disabled],
html .form-control[readonly],
html fieldset[disabled] .form-control {
	background-color: #666;
}
/* text color */
html .mainform a:hover {
	text-decoration: none;
}
html body,
html a,
html .input-group-addon,
html .btn-default,
html ul.nav-sidebar li a,
html ul.nav-sidebar li.dropdown-submenu i:before,
html .features .item .description,
html .about_us .item .descr a,
html .panel-default > .panel-heading,
html #breadcrumbs .main a i:before,
html .pagination > li > a,
html .pagination > li > span,
html .shops .search_city ul.cities_list > li > a,
html table.table > tbody > tr > td,
html .open > .btn-default.dropdown-toggle,
html .dropdown-menu > li > a,
html header.style2.border .navbar-default .navbar-nav > li > a,
html header.style4.border .navbar-default .navbar-nav > li > a,
html header.style2.no-border .navbar-default .navbar-nav > li > a,
html header.style4.no-border .navbar-default .navbar-nav > li > a,
html .form-control,
html .fancybox-title-inside-wrap,
html body .popup-window.smartFilterSelectbox .popup-window-content label {
	color: #c3c3c3;
}
html a:hover,
html a:focus,
html .features .item .name,
html .navbar-default .navbar-nav > li > a:focus,
html .docs .item .data .descr a:hover,
html .honors .item .data .descr a:hover,
html .about_us .item .data .descr a:hover,
html .newslistcol .item .data .descr a:hover,
html .panel-default > .panel-heading a:hover,
html .navbar-default .navbar-nav > .open > a,
html .navbar-default .navbar-nav > .open > a:hover,
html .navbar-default .navbar-nav > .open > a:focus,
html .form-control:focus {
	color: #fff;
}
/* border color */
html .features .item a:hover,
html .services .item a:hover,
html .action .item a:hover,
html .honors .item .image a:hover,
html .about_us .item .image a:hover,
html .newslistcol .item .image a:hover,
html .panel-default > .panel-heading a:hover,
html .widgets a:hover,
html .gallery .item a:hover,
html .partners .item a:hover,
html .products .item .in:hover,
html .nav-tabs > li > a,
html .js-detail .thumbs .thumb a:hover,
html .js-detail .thumbs .thumb .checked a,
html .timeline.row .item:hover .body,
html .form-control:focus,
html .thumbs .thumb a:hover {
	border-top-color: #fff;
	border-right-color: #fff;
	border-bottom-color: #fff;
	border-left-color: #fff;
}
html .timeline.row .item:hover .pointer.right {
	border-left-color: #fff;
}
html .timeline.row .item:hover .pointer.left {
	border-right-color: #fff;
}
@media (max-width: 768px) {
	html .timeline.row .item:hover .pointer.left {
		border-left-color: #fff;
	}
}
html ul.nav-sidebar.nav,
html .customerreviews .item .review,
html .panel-default > .panel-heading a,
html .panel-group .panel-heading + .panel-collapse > .panel-body,
html ul.nav-sidebar li a,
html .widgets a,
html .gallery .item a,
html .partners .item a,
html .products .item .in,
html .nav-tabs > li > a,
html .js-detail .thumbs .thumb a,
html .shops .search_city ul.cities_list > li > a,
html .shops .search_city ul.cities_list > li,
html table.table,
html table.table > tbody > tr > td,
html table.table > thead > tr > th,
html table.table > tbody > tr > td,
html .shops .shops_list,
html .timeline.row .item .body,
html .vacancies .filter .btn.btn-default,
html .faq .filter .btn.btn-default,
html .honors .item .image a,
html .about_us .item .image a,
html .newslistcol .item .image a,
html .form-control,
html .mainform .inner-wrap,
html .webform,
html .fancybox-skin,
html .fancybox-opened .fancybox-title,
html .popupgallery,
html .thumbs .thumb a,
html .detail_subs,
html .backshare .detailback i,
html .catalogsorter,
html .catalogsorter .dropdown > .btn-default.dropdown-toggle,
html .comparelist .btn,
html .dropdown-menu > li > a,
html .dropdown-menu,
html .dropdown-menu > li,
html .bx_filter_select_block .bx_filter_select_text,
html body .popup-window.smartFilterSelectbox .popup-window-content label,
html body .popup-window.smartFilterSelectbox .popup-window-content li,
html .smartfilter ul .bx_filter_prop,
html .smartfilter ul .bx_filter_prop.active > .body {
	border-top-color: #666;
	border-right-color: #666;
	border-bottom-color: #666;
	border-left-color: #666;
}
html .mega-menu .dropdown-menu {
	border-top-color: transparent;
	border-top-right: transparent;
	border-top-left: transparent;
}
html .mega-menu .dropdown-menu > li > a,
html .mega-menu .dropdown-menu > li {
	border-color: #ebebeb;
}
html .timeline.row .item .pointer.right {
	border-left-color: #666;
}
html .timeline.row .item .pointer.left {
	border-right-color: #666;
}
html header .navbar .search-open,
html body .popup-window.smartFilterSelectbox .popup-window-content ul {
	border-top-color: #666;
	border-right-color: #666;
	border-left-color: #666;
}
@media (max-width: 768px) {
	html .timeline.row .item .pointer.left {
		border-left-color: #666;
	}
}
html .features .item a,
html .features .item a,
html .services .item a,
html .action .item a,
html .timeline.row .item .pointer span {
	border-top-color: #252525;
	border-right-color: #252525;
	border-bottom-color: #252525;
	border-left-color: #252525;
}
html .customerreviews .item .review .arrow {
	border-top-color: #666;
	border-left-color: #666;
}
html .customerreviews .item .review .arrow > span {
	border-top-color: #252525;
	border-left-color: #252525;
}
html .timeline.row .item .pointer.right > div {
	border-left-color: #252525;
}
html .action.row .item .markers .marker:after,
html .action.owl .item .markers .marker:after,
html .newsdetail .markers .marker:after {
	border-bottom-color: #252525;
}
html .timeline.row .item .pointer.left > div {
	border-right-color: #252525;
}
@media (max-width: 767px) {
	html .timeline.row .item .pointer.left > div {
		border-left-color: #252525;
	}
}
@media (max-width: 991px) {
	html header .navbar-default .navbar-nav .open .dropdown-menu li > a {
		color: #c3c3c3;
	}
	html header .navbar-default .navbar-nav .open .dropdown-menu li > a {
		background-color: #444;
	}
	html .aroundfilter .smartfilter.open {
		border-color: #666;
	}
}
@media (min-width: 992px) {
	html header.style4 .navbar-default .navbar-nav > li:hover > a,
	html header.style4 .navbar-default .navbar-nav > li > a:hover,
	html header.style2 .navbar-default .navbar-nav > li:hover > a,
	html header.style2 .navbar-default .navbar-nav > li > a:hover {
		color: #585f69;
	}
	html header .navbar-default .navbar-collapse,
	html header .main-menu-nav > li > a {
		border-top-color: #666;
	}
	html header.style2 .navbar-default .navbar-collapse {
		border-bottom-color: #666;
	}
	html header .navbar-default .navbar-nav > li,
	html header .main-menu-nav > li > a {
		border-right-color: #666;
		border-left-color: #666;
	}
}
/* other */
html .about_us .item .publish:after {
	background: linear-gradient(to right, rgba(255, 255, 255, 0), #252525 50%);
}
html .popupgallery .preview:before {
	background-image: -webkit-linear-gradient(left, rgba(255, 255, 255, 0), #252525);
	background-image: linear-gradient(to right, rgba(255, 255, 255, 0), #252525);
}
html .form-control:focus {
	-webkit-box-shadow: none;
	box-shadow: none;
}
html .mainform input.almost-filled.form-control {
	background-color: #b5989a;
}
/* header "M" */
html .logo .m .m1 > div {
	border-bottom-color: #252525;
}
html .logo .m .m2 > div {
	border-right-color: #252525;
}
';
		return $styles;
	}

}