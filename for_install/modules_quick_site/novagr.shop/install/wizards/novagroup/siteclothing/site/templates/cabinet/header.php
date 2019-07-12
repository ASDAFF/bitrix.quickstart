<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die(); 

if(!CUser::IsAuthorized()) {

	header('Location: http://' .$_SERVER["HTTP_HOST"]."/?action=auth");
	exit();
}

$PATH_INCLUDE = SITE_DIR."include";

if (strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 6.0") === false
		&& 	strpos($_SERVER["HTTP_USER_AGENT"], "MSIE 7.0") === false
) {
	$ie6 = false;
} else {
	$ie6 = true;
}

IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE HTML>
<html>
<head>
<title><? $APPLICATION->ShowTitle(); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<? $APPLICATION->ShowHead(); ?>
<?php 
CJSCore::Init(array("jquery"));
?>
<script src="/include/js/jquery.form.js"></script>
<script src="/include/js/jquery.cookie.js"></script>

<script src="<?=SITE_TEMPLATE_PATH?>/js/bootstrap-modalmanager.js"></script>
<link href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap/bootstrap.min.css" type="text/css" rel="stylesheet" />
<link href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap/bootstrap-responsive.min.css" type="text/css" rel="stylesheet" />
<link href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap/bootstrap-modal.css" rel="stylesheet" />
<link href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap/trend.css" type="text/css" rel="stylesheet" />

<link href="<?=SITE_TEMPLATE_PATH?>/css/select2.css" type="text/css" rel="stylesheet" />

<script src="<?=$PATH_INCLUDE?>/bootstrap/js/bootstrap.js"></script>

<script>
$(document).ready(function() {
	// тултипы
	$("[rel=tooltip]").tooltip({});
});
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/ie/ie.css" />
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="<?=SITE_TEMPLATE_PATH?>/css/ie/ie8.css" />
<![endif]-->
</head>
<body>
<?php  

if ($ie6 == true) {
	
	$APPLICATION->SetTitle(GetMessage("T_OLD_BROWSER"));
	
	$APPLICATION->IncludeComponent("bitrix:main.include", "",
			array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."/include/ie6.php"),
			false
	);
	die('</body></html>');
}

?>
<div id="panel"><? $APPLICATION->ShowPanel();?></div>
<div class="page-container">
<div class="container officeDealer">

<div class="row header-office">
  <div class="span2">
      <div class="logo"><a href="/"><img width="271" height="35" alt="" src="<?=SITE_TEMPLATE_PATH?>/images/logo-manage.gif"></a></div> 
  </div>
  <div class="span5">
		<div class="seller_account">
		<?php
	global $USER;
	$currentUri = $APPLICATION->GetCurPage();
	
			?>
			<div class="name-header"><h1><?=GetMessage("T_MANAGER_CAB")?></h1></div>
	    	<span class="name-dealer">
	    		
			</span>	
							
		</div>
    </div>
    <div class="span3 blL">
  		
        <span><?=GetMessage("T_COUNT_PRODUCTS")?>: <?=$APPLICATION->ShowProperty("count_products")?> <?=GetMessage("T_COUNT_PRODUCTS2")?>.</span>
    </div>
    
  </div>
  <div class="row header-office">
  <div class="span2 log-user tooltip-demo">
  		<form id="logout" action="">				
			<input type="hidden" name="logout" value="yes">
				<a href="<?=SITE_DIR . 'cabinet/'?>" class="name-user" data-original-title="<?=$USER->GetFullName()?>" data-placement="top" rel="tooltip"><? echo TruncateText($USER->GetFullName(),6);?></a>
				<button class="btn"><?=GetMessage("T_LOG_OUT")?></button>      
		</form>
  	</div>
	<div class="span8">
		<?php 
					
		 
			if (!isManager()) {
				
				?><div class="to-mode">
				<?=GetMessage("T_BAD_RIGHTS")?>
				</div>
				</div></div></div></div></body></html>
				<?php
				die();
			}
		
		?>	
	
		<div class="navbar">
    		<div class="navbar-inner menu-main">
      			<div class="container">
					<div class="nav-collapse">
						<?$APPLICATION->IncludeComponent("bitrix:menu", "dealers_top", array(
				"ROOT_MENU_TYPE" => "top",
				"MENU_CACHE_TYPE" => "Y",
				"MENU_CACHE_TIME" => "86400",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => array(
				),
				"MAX_LEVEL" => "1",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "N",
				"DELAY" => "N",
				"ALLOW_MULTI_SELECT" => "N"
				),
				false
			);?>   
        			</div>
      			</div>
    		</div>
  		</div>
  	</div>
</div>
  
