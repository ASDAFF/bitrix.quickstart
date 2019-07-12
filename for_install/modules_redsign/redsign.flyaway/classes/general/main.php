<?
/************************************
*
* General class
* last update 26.06.2015
*
************************************/

IncludeModuleLangFile(__FILE__);

class RsFlyaway
{
	public static function saveSettings() {

		$presetsType = array('preset_1','preset_2','preset_3', 'preset_4', 'preset_5', 'preset_6', 'preset_7', 'preset_8', 'preset_9', 'preset_10');
		if(isset($_REQUEST['presets']) && in_array($_REQUEST['presets'],$presetsType)) {
			COption::SetOptionString('redsign.flyaway', 'presets', htmlspecialchars($_REQUEST['presets']) );
		}

		$arFilterStyle = array('left', 'right');
		if(isset($_REQUEST['filterSide']) && in_array($_REQUEST['filterSide'],$arFilterStyle)) {
			COption::SetOptionString('redsign.flyaway', 'filterSide', htmlspecialchars($_REQUEST['filterSide']) );
		}
		$bannerType = array('type1', 'type2', 'type3', 'type4', 'type5');
		if(isset($_REQUEST['bannerType']) && in_array($_REQUEST['bannerType'],$bannerType)) {
			COption::SetOptionString('redsign.flyaway', 'bannerType', htmlspecialchars($_REQUEST['bannerType']) );
		}
		$openMenuType = array('type1', 'type2', 'type3');
		if(isset($_REQUEST['openMenuType']) && in_array($_REQUEST['openMenuType'],$openMenuType)) {
			COption::SetOptionString('redsign.flyaway', 'openMenuType', htmlspecialchars($_REQUEST['openMenuType']) );
		}
		 
		$sidemenuType = array('light', 'dark');
		if(isset($_REQUEST['sidemenuType']) && in_array($_REQUEST['sidemenuType'], $sidemenuType)) {
			COption::SetOptionString('redsign.flyaway', 'sidemenuType', htmlspecialchars($_REQUEST['sidemenuType']) );
		}
		
		if($_REQUEST['StickyHeader']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'StickyHeader', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'StickyHeader', "N");
		}

		/*if($_REQUEST['blackMode']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'blackMode', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'blackMode', "N");
		}*/
		

		if(isset($_REQUEST['gencolor']) && $_REQUEST['gencolor'] != "") {
			$gencolor = htmlspecialchars($_REQUEST['gencolor']);
			COption::SetOptionString('redsign.flyaway', 'gencolor', $gencolor );
		} else {
			$gencolor = COption::GetOptionString('redsign.flyaway', 'gencolor', 'ffe062' );
		}
		if(isset($_REQUEST['secondColor'])) {
			$secondColor = htmlspecialchars($_REQUEST['secondColor']);
			COption::SetOptionString('redsign.flyaway', 'secondColor', $secondColor );
		} else {
			$secondColor = COption::GetOptionString('redsign.flyaway', 'secondColor', '' );
		}

		// main page settings

		if($_REQUEST['Fichi']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'Fichi', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'Fichi', 'N' );
		}

		if($_REQUEST['SmallBanners']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'SmallBanners', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'SmallBanners', 'N' );
		}

		if($_REQUEST['New']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'New', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'New', 'N' );
		}

		if($_REQUEST['PopularItem']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'PopularItem', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'PopularItem', 'N' );
		}

		if($_REQUEST['Service']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'Service', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'Service', 'N' );
		}

		if($_REQUEST['AboutAndReviews']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'AboutAndReviews', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'AboutAndReviews', 'N' );
		}

		if($_REQUEST['News']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'News', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'News', 'N' );
		}

		if($_REQUEST['Partners']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'Partners', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'Partners', 'N' );
		}

		if($_REQUEST['Gallery']=='Y') {
			COption::SetOptionString('redsign.flyaway', 'Gallery', 'Y' );
		} else {
			COption::SetOptionString('redsign.flyaway', 'Gallery', 'N' );
		}
		
		

		// developer mode

		if(isset($_REQUEST['optionFrom'])) {
			if($_REQUEST['optionFrom']=='module') {
				COption::SetOptionString('redsign.flyaway', 'optionFrom', 'module' );
			} elseif ($_REQUEST['optionFrom']=='session') {
				COption::SetOptionString('redsign.flyaway', 'optionFrom', 'session' );
			}
		}

		self::generateCssColorFile();
		BXClearCache(true, "/");
	}

	public static function getSettings($paramName='',$default='') {
		$return = '';
		$return = $default;
		if($paramName!='') {
			$optionFrom = COption::GetOptionString('redsign.flyaway', 'optionFrom', 'module');
			if($optionFrom=='session') {
				if(isset($_SESSION[$paramName])) {
					$return = $_SESSION[$paramName];
				}
			} else {
				$return = COption::GetOptionString('redsign.flyaway', $paramName, $default);
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
				$arElements[$iElementId]['RS_PRICE'] = RSFLYAWAY_addPrices($arElement,$params);
			}
		}
	}

	public static function generateCssColorFile() {
		$color = COption::GetOptionString('redsign.flyaway', 'gencolor', 'ffe062');
		$secondColor = COption::GetOptionString('redsign.flyaway', 'secondColor', '555555');
		$reverse = COption::GetOptionString('redsign.flyaway', 'blackMode', 'N');
		$file_path = '/include/color.css';
		if($color == "ffe062" && $secondColor == "555555") {
			$styles = "";
			RewriteFile($_SERVER["DOCUMENT_ROOT"].$file_path, $styles);
		}
		else {
			$darketPersent = 4;
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

		    $styles = self::getSelectors($color,$secondColor,$darketnColor);

			if($reverse=='Y') {
				$reverse = self::blackTheme();
				$styles.= $reverse;
			}
			RewriteFile($_SERVER["DOCUMENT_ROOT"].$file_path, $styles);
		}
	}

	public static function getSelectors($color='ffe062', $secondColor = 'eeeeee', $darkColor='ffda47') {
		$stableColor = 'ebebeb';
		$styles = '
		.preset_4 .top-menu,
		.preset_5 .top-menu,
		.preset_6 .top-menu,
		.btn2,
		.loss-menu-right .count,
		.color .top-menu,
		.smartfilter .bx_ui_slider_pricebar_V,
		.product .productsku-color__list li.active .colors-cover,
		.form-title,
		.favoriteinhead .descr,
		.basketinhead .descr,
		.smallbanners__decor,
        .hint, .loss-menu-right .count, .abc__letter {
			background-color: #'.$color.';
		}
		h1,
		.mainnav-sub:last-child,
		.views-box:last-child,
		li.lvl1.open > ul,
		.left-menu .nav-side > li.open > ul,
		.dropdown-menu-right,
		.page-h h1,
		.mainmenu .mainmenu__submenu  {
			border-bottom-color:#'.$color.';
		}
		ul > li:before, ol > li:before {
			color:#'.$color.';
		}
		.btn-button.active,
		.btn2,
		.paginator__item_active .paginator__label,
		.colors-list__item.selected .colors-cover,
		.product-buyblock,
		.panel-constructor,
		ul.nav-buttons li.active .btn,
		.personal-basket__sort ul.nav-basket li.active .btn,
		.personal-panel,
		.float-basket,
		.rs_sku-option.checked,
		.reviews__item:hover .reviews__rating,
		.gui-checkbox-input:checked + .gui-checkbox-icon,
		.gui-checkbox.checked .gui-checkbox-icon,
		.reviews__item:hover .reviews__rating:before,
		.is--sidenav.side-light .fly-header-wrap,
		.is--sidenav.side-dark .fly-header-wrap,
		.side-light .fly-header-wrap.__simple, 
		.side-dark .fly-header-wrap.__simple,
		.reviews__item:hover .reviews__rating:after {
			border-color:#'.$color.';
		}
		.reviews__item:hover .reviews__rating {
			box-shadow: 0 0 0 2px #'.$color.';
		}
		.cwp.active .bx_filter_btn_color_icon,
		.cwpal.active .bx_filter_btn_color_icon,
		.basket-table-sku .basket-table-sku__prop-list li.active span {
			box-shadow: 0 0 0 4px #'.$color.';
		}
		.float-basket-order .float-basket-order__arrow {
			border-right-color:#'.$color.';
		}
		.area2darken:before {
			border-color:transparent #'.$color.' #'.$color.';
		}
		.preset_4 .top-menu .mainmenu > li > a:hover,
		.preset_5 .top-menu .mainmenu > li > a:hover,
		.preset_6 .top-menu .mainmenu > li > a:hover,
		.preset_4 .top-menu .mainmenu > li > a:focus,
		.preset_5 .top-menu .mainmenu > li > a:focus,
		.preset_6 .top-menu .mainmenu > li > a:focus,
		.side-dark .mobile-menu .inmenucompare .count, 
		.side-dark .mobile-menu .inmenufavorite .count,
		.btn2:hover,
		.btn2:focus,
		.btn2:active,
		.btn2.active,
		.btn2.active:hover,
		.main-banners .rs-banners_infowrap .rs-banners_button,
		.main-banners .rs-banners-container .owl-theme .owl-dots .owl-dot.active span, 
		.main-banners .rs-banners-container .owl-theme .owl-dots .owl-dot:hover span,
		.color .main-nav .main-nav__label:hover,
		.stores .stores-icon.stores-small:before,
		.stores .stores-icon.stores-small:after,
		.form-title:hover {
			background-color:#'.$darkColor.';
		}
		.btn2:hover,
		.btn2:focus,
		.btn2:active,
		.btn2.active,
		.btn2.active:hover,
		.gui-radiobox-item:checked + .gui-out,
		.loss-menu-right.active .selected,
		.stores .stores-icon.stores-small:before,
		.stores .stores-icon.stores-small:after,
		#set_filter,
		.product-detail-carousel .product-detail-carousel__nav .active.owl-dot {
			border-color:#'.$darkColor.';
		}
		.product-detail-carousel .product-detail-carousel__nav .active.owl-dot {
			outline-color:#'.$darkColor.';
		}


		
		.preset_4 .lvl1 > .element,
		.preset_5 .lvl1 > .element,
		.preset_6 .lvl1 > .element,
		.mainmenu .mainmenu__other .mainmenu__other-link,
		.main-banners .rs-banners_infowrap .rs-banners_button,
		.loss-menu-right .count,
		.smallbanners__info,
		.btn2,
		.preset_4 .lvl1 > .element,
		.preset_5 .lvl1 > .element,
		.preset_6 .lvl1 > .element,
		.mainmenu .mainmenu__other .mainmenu__other-link,
		.main-banners .rs-banners_infowrap .rs-banners_button,
		.loss-menu-right .count,
		.smallbanners__info, .btn2,
		.form .webform-button,
		.form-title,
		.btn2:hover,
		.btn2:focus,
		.btn2:active,
		.btn2.active,
		.btn2.active:hover,
		.form-title:hover,
		.gui-radiobox-item:checked + .gui-out,
		.loss-menu-right.active .selected,
		.stores .stores-icon.stores-small:before,
		.stores .stores-icon.stores-small:after,
		.stores .stores-icon.stores-mal:before,
		.stores .stores-icon.stores-mal:after,
		#set_filter,
		.side-dark .mobile-menu .inmenucompare .count, .side-dark .mobile-menu .inmenufavorite .count,
		.product-detail-carousel .product-detail-carousel__nav .active.owl-dot,
        .hint, .loss-menu-right .count, .abc__letter {
			color:#'.$secondColor.';
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
    
	
	function ShowPanel()
    {
        if ($GLOBALS["USER"]->IsAdmin() && COption::GetOptionString("main", "wizard_solution", "", SITE_ID) == "redsign.flyaway")
        {
            $GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/wizards/redsign/flyaway/css/panel.css"); 

            $arMenu = Array(
                Array(        
                    "ACTION" => "jsUtils.Redirect([], '".CUtil::JSEscape("/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardSiteID=".SITE_ID."&wizardName=redsign:flyaway&".bitrix_sessid_get())."')",
                    "ICON" => "bx-popup-item-wizard-icon",
                    "TITLE" => GetMessage("STOM_BUTTON_TITLE_W1"),
                    "TEXT" => GetMessage("STOM_BUTTON_NAME_W1"),
                )
            );

            $GLOBALS["APPLICATION"]->AddPanelButton(array(
                "HREF" => "/bitrix/admin/wizard_install.php?lang=".LANGUAGE_ID."&wizardName=redsign:flyaway&wizardSiteID=".SITE_ID."&".bitrix_sessid_get(),
                "ID" => "flyaway_wizard",
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
}
