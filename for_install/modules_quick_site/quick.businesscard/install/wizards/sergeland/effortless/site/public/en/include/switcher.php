<?
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "SWITCHER" ]) )
{	
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	
	if(empty($_POST["SWITCHER"])) return;
	$arr["MESSAGE"]["ERROR"] = 0;
	
	if(!defined("SITE_ID")) 
		define("SITE_ID", $_POST["SWITCHER"]["SITE_ID"]);
		
	$_SESSION["SERGELAND_THEME"][SITE_ID] = $_POST["SWITCHER"];	
	echo json_encode($arr);
	return;
}
if(!empty($_POST["SWITCHER"]))
{
	if(array_key_exists("SUBMIT", $_POST["SWITCHER"]) && $USER->IsAdmin())
		foreach($_POST["SWITCHER"] as $NAME=>$VALUE)
			COption::SetOptionString("effortless", "SERGELAND_THEME_".$NAME, $VALUE, false, SITE_ID);
	
	$_SESSION["SERGELAND_THEME"][SITE_ID] = array();	
	LocalRedirect($APPLICATION->GetCurPage(false));
}
?>
<!-- Style Switcher
================================================== -->
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/switcher/switcher.css" />
<script src="<?=SITE_TEMPLATE_PATH?>/switcher/switcher.js"></script>
<?if(!$arParams["DEMO"]):?><style>#style-switcher{top:163px}#style-switcher form{height:415px}</style><?endif?>
<div id="style-switcher">
	<div>Customize the appearance<i class="fa fa-gear label-default"></i></div>
	<form action="<?=POST_FORM_ACTION_URI?>" method="post" name="SWITCHER" enctype="multipart/form-data">
		<input type="hidden" name="SWITCHER[COLOR]" value="<?=(!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["COLOR"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["COLOR"] : COption::GetOptionString("effortless", "SERGELAND_THEME_COLOR", "red", SITE_ID))?>" id="COLOR">
		<input type="hidden" name="SWITCHER[BACKGROUND]" value="<?=(!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["BACKGROUND"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["BACKGROUND"] : COption::GetOptionString("effortless", "SERGELAND_THEME_BACKGROUND", "background0", SITE_ID))?>" id="BACKGROUND">
		<input type="hidden" name="SWITCHER[SITE_DIR]" value="<?=SITE_DIR?>" id="SITE_DIR">
		<input type="hidden" name="SWITCHER[SITE_ID]" value="<?=SITE_ID?>">
		<h3 class="page-title">Preset colors</h3>
		<ul class="list-unstyled list-inline color">
			<li id="blue" data-color="blue" data-toggle="tooltip" data-placement="top" title="Blue"></li>
			<li id="green" data-color="green" data-toggle="tooltip" data-placement="top" title="Green"></li>
			<li id="red" data-color="red" data-toggle="tooltip" data-placement="top" title="Red"></li>
			<li id="orange" data-color="orange" data-toggle="tooltip" data-placement="top" title="Orange"></li>
			<li id="yellow" data-color="yellow" data-toggle="tooltip" data-placement="top" title="Yellow"></li>
			<li id="purple" data-color="purple" data-toggle="tooltip" data-placement="top" title="Purple"></li>		
			<li id="brown" data-color="brown" data-toggle="tooltip" data-placement="top" title="Brown"></li>
			<li id="dark_cyan" data-color="dark_cyan" data-toggle="tooltip" data-placement="top" title="Dark Cyan"></li>
			<li id="dark_gray" data-color="dark_gray" data-toggle="tooltip" data-placement="top" title="Dark Gray"></li>
			<li id="dark_red" data-color="dark_red" data-toggle="tooltip" data-placement="top" title="Dark Red"></li>
			<li id="light_blue" data-color="light_blue" data-toggle="tooltip" data-placement="top" title="Light Blue"></li>
			<li id="light_green" data-color="light_green" data-toggle="tooltip" data-placement="top" title="Light Green"></li>
			<li id="pink" data-color="pink" data-toggle="tooltip" data-placement="top" title="Pink"></li>		
		</ul>
		<div class="error">Error passing parameter.</div>
		<h3>The type of layout</h3>
		<div class="layout-style">
			<select name="SWITCHER[BOXED]">
				<option value="standard" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["BOXED"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["BOXED"] : COption::GetOptionString("effortless", "SERGELAND_THEME_BOXED", "standard", SITE_ID)) == "standard"):?>selected<?endif?> />Standard			
				<option value="boxed" 	 <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["BOXED"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["BOXED"] : COption::GetOptionString("effortless", "SERGELAND_THEME_BOXED", "standard", SITE_ID)) == "boxed"):?>selected<?endif?> />Boxed
				<option value="rubber" 	 <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["BOXED"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["BOXED"] : COption::GetOptionString("effortless", "SERGELAND_THEME_BOXED", "standard", SITE_ID)) == "rubber"):?>selected<?endif?> />Rubber
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Background (only for boxed layout)</h3>
		<ul class="list-unstyled list-inline background">
			<li class="background0" data-background="background0"></li>
			<li class="background1" data-background="background1"></li>
			<li class="background2" data-background="background2"></li>
			<li class="background3" data-background="background3"></li>
			<li class="background4" data-background="background4"></li>
			<li class="background5" data-background="background5"></li>
			<li class="background6" data-background="background6"></li>
			<li class="background7" data-background="background7"></li>
			<li class="background8" data-background="background8"></li>
			<li class="background9" data-background="background9"></li>
			<li class="background10" data-background="background10"></li>
			
			<li class="background11" data-background="background11"></li>
			<li class="background12" data-background="background12"></li>
			<li class="background13" data-background="background13"></li>
			<li class="background14" data-background="background14"></li>
			<li class="background15" data-background="background15"></li>
			<li class="background16" data-background="background16"></li>
			<li class="background17" data-background="background17"></li>
			<li class="background18" data-background="background18"></li>
			<li class="background19" data-background="background19"></li>
			<li class="background20" data-background="background20"></li>
			
			<li class="background21" data-background="background21"></li>
			<li class="background22" data-background="background22"></li>
			<li class="background23" data-background="background23"></li>
			<li class="background24" data-background="background24"></li>
		</ul>
		<div class="error">Error passing parameter.</div>
		<h3>The gray bar with contacts at the top</h3>
		<div class="layout-style">
			<select name="SWITCHER[LINE]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LINE"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LINE"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LINE", "N", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LINE"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LINE"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LINE", "N", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The site header</h3>
		<div class="layout-style">
			<select name="SWITCHER[HEADER_BG]">
				<option value="white"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_BG", "white", SITE_ID)) == "white")  :?>selected<?endif?> />White
				<option value="gray"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_BG", "white", SITE_ID)) == "gray")   :?>selected<?endif?> />Gray
				<option value="dark"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_BG", "white", SITE_ID)) == "dark")   :?>selected<?endif?> />Dark
				<option value="default" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_BG", "white", SITE_ID)) == "default"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Slider</mark></h3><hr>
		<h3>View slider</h3>
		<div class="layout-style">
			<select name="SWITCHER[SLIDER]">
				<option value="standart" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER", "standart", SITE_ID)) == "standart"):?>selected<?endif?> />STANDART
				<option value="boxed" 	 <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER", "standart", SITE_ID)) == "boxed"):?>selected<?endif?> />BOXED
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Frame (for slider STANDART)</h3>
		<div class="layout-style">
			<select name="SWITCHER[SLIDER_STANDART_BOXED]">
				<option value="slideshow-boxed" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_STANDART_BOXED"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_STANDART_BOXED"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_STANDART_BOXED", "slideshow", SITE_ID)) == "slideshow-boxed"):?>selected<?endif?> />Yes
				<option value="slideshow" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_STANDART_BOXED"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_STANDART_BOXED"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_STANDART_BOXED", "slideshow", SITE_ID)) == "slideshow"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background of the slider</h3>
		<div class="layout-style">
			<select name="SWITCHER[SLIDER_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_BG", "gray-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_BG", "gray-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_BG", "gray-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_BG", "gray-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Scrolling slider</h3>
		<div class="layout-style">
			<select name="SWITCHER[SLIDER_SCROLLING]">
				<option value="slider-banner" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID)) == "slider-banner"):?>selected<?endif?> />with the timeline
				<option value="slider-banner-2 bullets-with-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID)) == "slider-banner-2 bullets-with-bg"):?>selected<?endif?> />with the time bar (the buttons on the bottom on a white background)
				<option value="slider-banner-fullscreen" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID)) == "slider-banner-fullscreen"):?>selected<?endif?> />no strip time
				<option value="slider-banner-fullscreen bullets-with-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID)) == "slider-banner-fullscreen bullets-with-bg"):?>selected<?endif?> />no strip time (the buttons on the bottom on a white background)
				<option value="slider-banner-3" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SLIDER_SCROLLING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SLIDER_SCROLLING", "slider-banner", SITE_ID)) == "slider-banner-3"):?>selected<?endif?> />without buttons at the bottom, the background is parallax
			</select>
			<div class="error">Error passing parameter.</div>
		</div>		
		<h3><mark>Top Menu</mark></h3><hr>
		<h3>Type the top menu</h3>
		<div class="layout-style">
			<select name="SWITCHER[MENU]">
				<option value="float" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["MENU"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["MENU"] : COption::GetOptionString("effortless", "SERGELAND_THEME_MENU", "float", SITE_ID)) == "float"):?>selected<?endif?> />Floating
				<option value="fixed" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["MENU"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["MENU"] : COption::GetOptionString("effortless", "SERGELAND_THEME_MENU", "float", SITE_ID)) == "fixed"):?>selected<?endif?> />Fixed
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Menu transparency</h3>
		<div class="layout-style">
			<select name="SWITCHER[MENU_TRANSPARENT]">
				<option value="menu-transparent" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TRANSPARENT"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TRANSPARENT"] : COption::GetOptionString("effortless", "SERGELAND_THEME_MENU_TRANSPARENT", "menu-transparent", SITE_ID)) == "menu-transparent"):?>selected<?endif?> />Yes
				<option value="non-transparent"  <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TRANSPARENT"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TRANSPARENT"] : COption::GetOptionString("effortless", "SERGELAND_THEME_MENU_TRANSPARENT", "menu-transparent", SITE_ID)) == "non-transparent"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Color menu</h3>
		<div class="layout-style">
			<select name="SWITCHER[HEADER_MENU_BG]">
				<option value="white"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_MENU_BG", "white", SITE_ID)) == "white")  :?>selected<?endif?> />White
				<option value="gray"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_MENU_BG", "white", SITE_ID)) == "gray")   :?>selected<?endif?> />Gray
				<option value="dark"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_MENU_BG", "white", SITE_ID)) == "dark")   :?>selected<?endif?> />Dark
				<option value="default" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["HEADER_MENU_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_HEADER_MENU_BG", "white", SITE_ID)) == "default"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The location menu</h3>
		<div class="layout-style">
			<select name="SWITCHER[MENU_TOP_SLIDER]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TOP_SLIDER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TOP_SLIDER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_MENU_TOP_SLIDER", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Above the slider
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TOP_SLIDER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["MENU_TOP_SLIDER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_MENU_TOP_SLIDER", "Y", SITE_ID)) == "N"):?>selected<?endif?> />Under the slider
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Internal menu</mark></h3><hr>
		<h3>The location menu</h3>
		<div class="layout-style">
			<select name="SWITCHER[SIDEBAR]">
				<option value="left"  <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SIDEBAR"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SIDEBAR"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SIDEBAR", "left", SITE_ID)) == "left"):?>selected<?endif?> />Left
				<option value="right" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SIDEBAR"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SIDEBAR"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SIDEBAR", "left", SITE_ID)) == "right"):?>selected<?endif?> />Right
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Banner on internal pages</mark></h3><hr>
		<h3>Auto scroll banner</h3>
		<div class="layout-style">
			<select name="SWITCHER[BANNER_AUTOPLAY]">
				<option value="content-slider-with-controls-bottom-autoplay" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["BANNER_AUTOPLAY"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["BANNER_AUTOPLAY"] : COption::GetOptionString("effortless", "SERGELAND_THEME_BANNER_AUTOPLAY", "content-slider-with-controls-bottom-autoplay", SITE_ID)) == "content-slider-with-controls-bottom-autoplay"):?>selected<?endif?> />Yes
				<option value="content-slider-with-controls-bottom" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["BANNER_AUTOPLAY"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["BANNER_AUTOPLAY"] : COption::GetOptionString("effortless", "SERGELAND_THEME_BANNER_AUTOPLAY", "content-slider-with-controls-bottom-autoplay", SITE_ID)) == "content-slider-with-controls-bottom"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>The tag cloud in articles</mark></h3><hr>
		<h3>Appearance</h3>
		<div class="layout-style">
			<select name="SWITCHER[TAGS_VER]">
				<option value="articles-ver-1" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TAGS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TAGS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TAGS_VER", "articles-ver-1", SITE_ID)) == "articles-ver-1"):?>selected<?endif?> />Ver 1
				<option value="articles-ver-2" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TAGS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TAGS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TAGS_VER", "articles-ver-1", SITE_ID)) == "articles-ver-2"):?>selected<?endif?> />Ver 2
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>The basement website</mark></h3><hr>
		<h3>The color of the basement</h3>
		<div class="layout-style">
			<select name="SWITCHER[FOOTER_BG]">
				<option value="light"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["FOOTER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["FOOTER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_FOOTER_BG", "dark", SITE_ID)) == "light")  :?>selected<?endif?> />Light
				<option value="dark"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["FOOTER_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["FOOTER_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_FOOTER_BG", "dark", SITE_ID)) == "dark")   :?>selected<?endif?> />Dark
			</select>
			<div class="error">Error passing parameter.</div>
		</div>		
		<h3><mark>Company benefits</mark></h3><hr>
		<h3>Block "Company Benefits" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[EXTRA]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The unit "Company Benefits"</h3>
		<div class="layout-style">
			<select name="SWITCHER[EXTRA_VER]">
				<option value="extra-ver-1" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_VER", "extra-ver-1", SITE_ID)) == "extra-ver-1"):?>selected<?endif?> />Ver 1
				<option value="extra-ver-2" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_VER", "extra-ver-1", SITE_ID)) == "extra-ver-2"):?>selected<?endif?> />Ver 2
				<option value="extra-ver-3" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_VER", "extra-ver-1", SITE_ID)) == "extra-ver-3"):?>selected<?endif?> />Ver 3
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the block "Company Benefits"</h3>
		<div class="layout-style">
			<select name="SWITCHER[EXTRA_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_BG", "gray-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_BG", "gray-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_BG", "gray-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["EXTRA_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_EXTRA_BG", "gray-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Free consultation</mark></h3><hr>
		<h3>Block "Free consultation" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[WARNING]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the block "Free consultation"</h3>
		<div class="layout-style">
			<select name="SWITCHER[WARNING_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WARNING_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WARNING_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>The company's services</mark></h3><hr>
		<h3>Block "The company's services" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[SERVICES]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The unit "The company's services"</h3>
		<div class="layout-style">
			<select name="SWITCHER[SERVICES_VER]">
				<option value="services-ver-1" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_VER", "services-ver-1", SITE_ID)) == "services-ver-1"):?>selected<?endif?> />Ver 1
				<option value="services-ver-2" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_VER", "services-ver-1", SITE_ID)) == "services-ver-2"):?>selected<?endif?> />Ver 2
				<option value="services-ver-3" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_VER", "services-ver-1", SITE_ID)) == "services-ver-3"):?>selected<?endif?> />Ver 3
				<option value="services-ver-4" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_VER", "services-ver-1", SITE_ID)) == "services-ver-4"):?>selected<?endif?> />Ver 4
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the block "The company's services"</h3>
		<div class="layout-style">
			<select name="SWITCHER[SERVICES_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The size of the icons block "The company's services"</h3>
		<div class="layout-style">
			<select name="SWITCHER[SERVICES_ICONS_VIEW]">				
				<option value="box-style-3" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_ICONS_VIEW"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_ICONS_VIEW"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_ICONS_VIEW", "box-style-2", SITE_ID)) == "box-style-3"):?>selected<?endif?> />Small
				<option value="box-style-2" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_ICONS_VIEW"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["SERVICES_ICONS_VIEW"] : COption::GetOptionString("effortless", "SERGELAND_THEME_SERVICES_ICONS_VIEW", "box-style-2", SITE_ID)) == "box-style-2"):?>selected<?endif?> />Big
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>About the company</mark></h3><hr>
		<h3>Block "About the company" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[ABOUT]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Show in a block "About the company"</h3>
		<div class="layout-style">
			<select name="SWITCHER[ABOUT_VER]">
				<option value="about-news"  <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_VER", "about-news", SITE_ID)) == "about-news"):?>selected<?endif?> />News
				<option value="about-faq"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_VER", "about-news", SITE_ID)) == "about-faq"):?>selected<?endif?> />Questions and answers
				<option value="about-photo" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_VER", "about-news", SITE_ID)) == "about-photo"):?>selected<?endif?> />Photo
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The size of the icons (just for News)</h3>
		<div class="layout-style">
			<select name="SWITCHER[NEWS_ICONS_VIEW]">				
				<option value="box-style-3" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["NEWS_ICONS_VIEW"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["NEWS_ICONS_VIEW"] : COption::GetOptionString("effortless", "SERGELAND_THEME_NEWS_ICONS_VIEW", "box-style-3", SITE_ID)) == "box-style-3"):?>selected<?endif?> />Small
				<option value="box-style-2" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["NEWS_ICONS_VIEW"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["NEWS_ICONS_VIEW"] : COption::GetOptionString("effortless", "SERGELAND_THEME_NEWS_ICONS_VIEW", "box-style-3", SITE_ID)) == "box-style-2"):?>selected<?endif?> />Big
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the block "About the company"</h3>
		<div class="layout-style">
			<select name="SWITCHER[ABOUT_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["ABOUT_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_ABOUT_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Ask question</mark></h3><hr>
		<h3>Block "Ask question" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[CALLBACK]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the block "Ask question"</h3>
		<div class="layout-style">
			<select name="SWITCHER[CALLBACK_BG]">
				<option value="light-translucent-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK_BG", "light-translucent-bg", SITE_ID)) == "light-translucent-bg")   :?>selected<?endif?> />Light
				<option value="dark-translucent-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK_BG", "light-translucent-bg", SITE_ID)) == "dark-translucent-bg")    :?>selected<?endif?> />Dark
				<option value="default-translucent-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK_BG", "light-translucent-bg", SITE_ID)) == "default-translucent-bg") :?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>A button to block "Ask question"</h3>
		<div class="layout-style">
			<select name="SWITCHER[CALLBACK_BUTTON]">
				<option value="btn-white" 	<?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BUTTON"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BUTTON"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK_BUTTON", "btn-default", SITE_ID)) == "btn-white"):?>selected<?endif?> />Light
				<option value="btn-default" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BUTTON"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["CALLBACK_BUTTON"] : COption::GetOptionString("effortless", "SERGELAND_THEME_CALLBACK_BUTTON", "btn-default", SITE_ID)) == "btn-default"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Catalog</mark></h3><hr>
		<h3>Catalog on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[PRODUCTS]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the Catalog on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[PRODUCTS_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Popular products</mark></h3><hr>
		<h3>Popular products on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[PRODUCTS_POPULAR]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Autoplay Popular products</h3>
		<div class="layout-style">
			<select name="SWITCHER[PRODUCTS_POPULAR_AUTOPLAY]">
				<option value="carousel-autoplay" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_AUTOPLAY"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_AUTOPLAY"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR_AUTOPLAY", "carousel", SITE_ID)) == "carousel-autoplay"):?>selected<?endif?> />Yes
				<option value="carousel" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_AUTOPLAY"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_AUTOPLAY"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR_AUTOPLAY", "carousel", SITE_ID)) == "carousel"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the Popular products on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[PRODUCTS_POPULAR_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PRODUCTS_POPULAR_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PRODUCTS_POPULAR_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Portfolio</mark></h3><hr>		
		<h3>Block "Portfolio" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[PHOTO]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The background for the block "Portfolio"</h3>
		<div class="layout-style">
			<select name="SWITCHER[PHOTO_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["PHOTO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_PHOTO_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Reviews</mark></h3><hr>
		<h3>Reviews on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[TESTIMONIALS]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Navigation reviews</h3>
		<div class="layout-style">
			<select name="SWITCHER[TESTIMONIALS_VER]">
				<option value="content-slider-with-controls" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_VER", "content-slider-with-controls-autoplay", SITE_ID)) == "content-slider-with-controls"):?>selected<?endif?> />ver1
				<option value="content-slider-with-controls-autoplay" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_VER", "content-slider-with-controls-autoplay", SITE_ID)) == "content-slider-with-controls-autoplay"):?>selected<?endif?> />ver1 (with auto scroll)
				<option value="content-slider-with-controls-bottom" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_VER", "content-slider-with-controls-autoplay", SITE_ID)) == "content-slider-with-controls-bottom"):?>selected<?endif?> />ver2
				<option value="content-slider-with-controls-bottom-autoplay" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_VER", "content-slider-with-controls-autoplay", SITE_ID)) == "content-slider-with-controls-bottom-autoplay"):?>selected<?endif?> />ver2 (with auto scroll)
				<option value="content-slider" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_VER", "content-slider-with-controls-autoplay", SITE_ID)) == "content-slider"):?>selected<?endif?> />ver3 (without navigation)
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Round the pictures in the reviews</h3>
		<div class="layout-style">
			<select name="SWITCHER[TESTIMONIALS_IMG]">
				<option value="img-circle" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_IMG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_IMG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_IMG", "img-circle-no", SITE_ID)) == "img-circle"):?>selected<?endif?> />Yes
				<option value="img-circle-no" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_IMG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_IMG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_IMG", "img-circle-no", SITE_ID)) == "img-circle-no"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Background for reviews on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[TESTIMONIALS_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_BG", "gray-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_BG", "gray-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_BG", "gray-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["TESTIMONIALS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_TESTIMONIALS_BG", "gray-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Our projects</mark></h3><hr>
		<h3>Block "Our projects" on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[WORKS]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Autoscroll projects</h3>
		<div class="layout-style">
			<select name="SWITCHER[WORKS_AUTOPLAY]">
				<option value="carousel-autoplay" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_AUTOPLAY"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_AUTOPLAY"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS_AUTOPLAY", "carousel", SITE_ID)) == "carousel-autoplay"):?>selected<?endif?> />Yes
				<option value="carousel" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_AUTOPLAY"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_AUTOPLAY"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS_AUTOPLAY", "carousel", SITE_ID)) == "carousel"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>Background for projects on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[WORKS_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS_BG", "white-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS_BG", "white-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS_BG", "white-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["WORKS_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_WORKS_BG", "white-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3><mark>Logos</mark></h3><hr>
		<h3>Logos on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[LOGO]">
				<option value="Y" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO", "Y", SITE_ID)) == "Y"):?>selected<?endif?> />Yes
				<option value="N" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO", "Y", SITE_ID)) == "N"):?>selected<?endif?> />No
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<h3>The unit Logos</h3>
		<div class="layout-style">
			<select name="SWITCHER[LOGO_VER]">
				<option value="logo-ver-1" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_VER", "logo-ver-1", SITE_ID)) == "logo-ver-1"):?>selected<?endif?> />Ver 1
				<option value="logo-ver-2" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_VER"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_VER"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_VER", "logo-ver-1", SITE_ID)) == "logo-ver-2"):?>selected<?endif?> />Ver 2
			</select>
			<div class="error">Error passing parameter.</div>
		</div>		
		<h3>Background for Logos on the main</h3>
		<div class="layout-style">
			<select name="SWITCHER[LOGO_BG]">
				<option value="white-bg"   <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_BG", "gray-bg", SITE_ID)) == "white-bg")  :?>selected<?endif?> />White
				<option value="gray-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_BG", "gray-bg", SITE_ID)) == "gray-bg")   :?>selected<?endif?> />Gray
				<option value="dark-bg"    <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_BG", "gray-bg", SITE_ID)) == "dark-bg")   :?>selected<?endif?> />Dark
				<option value="default-bg" <?if((!empty($_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"]) ? $_SESSION["SERGELAND_THEME"][SITE_ID]["LOGO_BG"] : COption::GetOptionString("effortless", "SERGELAND_THEME_LOGO_BG", "gray-bg", SITE_ID)) == "default-bg"):?>selected<?endif?> />Color
			</select>
			<div class="error">Error passing parameter.</div>
		</div>
		<hr>
		<div class="mt-10 mb-20">
		<input type="submit" class="btn btn-sm btn-default" name="SWITCHER[CLEAR]" value="Reset">
		<?if(!$arParams["DEMO"]):?>
		<div class="mb-5"></div>
		<input type="submit" class="btn btn-sm btn-default" name="SWITCHER[SUBMIT]" value="To apply">
		<?endif?>
		</div>
	</form>
</div>
<!-- Style Switcher /End -->