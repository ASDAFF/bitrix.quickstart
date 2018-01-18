<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      	<title><?$APPLICATION->ShowTitle()?></title>
        <meta name="viewport" content="width=device-width">
		<?$APPLICATION->ShowHead();?>   
        
        
        <!-- favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="<?=SITE_TEMPLATE_PATH?>/favicon.ico" />

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/grid.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/grid.responsive.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/normalize.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Pontano+Sans&amp;subset=latin,latin-ext">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/main.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/core.css">
        <link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/colors.css">

        <!--[if IE 8]> <link rel="stylesheet" href="css/ie8.css"> <![endif]-->        
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.8.2.min.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/plugins.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.flexslider.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.nivo.slider.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.cookie.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.easing-1.3.pack.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.fancybox1.3.4.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.player.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body class="theme-orange">
    <?$APPLICATION->ShowPanel();?> 
        <!--[if lt IE 7]>
            <p class="chromeframe"> �� ����������� <strong>����������</strong> �������. ����������, <a href="http://browsehappy.com/">�������� �������</a> ��� �������� <a href="http://www.google.com/chromeframe/?redirect=true">Google Chrome</a>.</p>
        <![endif]-->

        <!-- stylesheet switcher begin
        
       �������, ���� �� �� ���������� ������������ ���������
         ������������ ������ � ����������� �� ������ ������������.
        -->

      
	    <!-- stylesheet switcher end -->

        <header id="page-header">
        	<div class="container">
        		<div class="row-fluid">
        			<div class="span12">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "logo",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
        			
        				
<?$APPLICATION->IncludeComponent("bitrix:menu", "horizontal_multilevel", Array(
	"ROOT_MENU_TYPE" => "top",	// ��� ���� ��� ������� ������
	"MENU_CACHE_TYPE" => "N",	// ��� �����������
	"MENU_CACHE_TIME" => "3600",	// ����� ����������� (���.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
	"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
	"MAX_LEVEL" => "2",	// ������� ����������� ����
	"CHILD_MENU_TYPE" => "left",	// ��� ���� ��� ��������� �������
	"USE_EXT" => "N",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
	"DELAY" => "N",	// ����������� ���������� ������� ����
	"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
	),
	false
);?>
        			</div>
        		</div><!-- /row-fluid -->
        	</div>
        </header><!-- /page-header -->     
	        
	 