<?
$templateFolder = SITE_TEMPLATE_PATH;
$templatePath = $_SERVER['DOCUMENT_ROOT'].$templateFolder;
include($templatePath.'/inc/functions.php');
IncludeTemplateLangFile(__FILE__);
?><!DOCTYPE html>
<html>
<head>

    <meta charset="<?=BX_UTF?'utf-8':'cp-1251'?>">
	<meta name="viewport" content="width=device-width; initial-scale=0.85; maximum-scale=0.85; user-scalable=0;">
	
    <title><?=$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowMeta('keywords')?>
    <?$APPLICATION->ShowMeta('description')?>

    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/styles/base.css")?>
    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/styles/media-queries.css")?>
    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/royalslider/default/rs-default.css")?>
    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/js/royalslider/royalslider.css")?>
    <?$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/styles/iarga.css")?>

    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-1.8.2.min.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/jquery-ui-1.9.2.custom.min.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/custom-form-elements.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/royalslider/jquery.royalslider.min.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/base.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/func.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/shop.js")?>
    <?$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH."/js/iarga.js")?>

   


	<link rel="shortcut icon" href="<?=SITE_TEMPLATE_PATH?>/images/favicon.ico" />
    <script>var SITE_DIR = "<?=SITE_DIR?>";</script>

    <?$APPLICATION->ShowHead()?>

    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!--[if lte IE 7]>
    <link href="<?=SITE_TEMPLATE_PATH?>/styles/base.ie.css" rel="stylesheet">
    <![endif]-->
    <!--[if lt IE 9]>
        <script src="<?=SITE_TEMPLATE_PATH?>/js/respond.min.js"></script>
    <![endif]-->
    
</head>

<body>
<?$APPLICATION->ShowPanel()?>

	<header>
    	
        <div class="bg">
        
            <section class="wrapper">
                
                <?if(!is_file($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'inc/parts/logo.jpg')):?>
					 <p class="logo"><a <?=CSite::InDir(SITE_DIR."index.php")?'':'href="'.SITE_DIR.'"'?>><?$APPLICATION->IncludeFile(SITE_DIR.'inc/parts/company_name.php')?></a></p>
				<?else:?>
					 <p class="logo"><a <?=CSite::InDir(SITE_DIR."index.php")?'':'href="'.SITE_DIR.'"'?>><img src="<?=SITE_DIR.'inc/parts/logo.jpg'?>"></a></p>
				<?endif;?>
				
                <div class="total-box">
                    <p class="phone"><?$APPLICATION->IncludeFile(SITE_DIR.'/inc/parts/telephone.php')?></p>
                    <i class="sep"></i>
                    <div class="white-space">
                        <p class="call-order"><a href="#" class="openpopup" data-rel="ordercall"><span><?=GetMessage("ORDER_BACKCALL")?></span></a></p>
                        <i class="sep"></i>
                        <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "auth", Array(
	
	),
	false
);?>

                    </div><!--.white-space-end-->
                </div><!--.total-box-end-->
                
            </section><!--.wrapper-end-->
        
        </div><!--.bg-end-->
        
    </header>


    <section class="content">
    	
        <div class="wrapper">
        	
			<?if(!CSite::InDir("/index.php")):?>
				<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", "breadcrumb", Array(
	
	),
	false
);?>
			<?endif;?>
 <?$APPLICATION->IncludeComponent("bitrix:menu", "sectmenu", Array(
	
	),
	false
);?>