<!doctype html>
 
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<html>
<head>

<?IncludeTemplateLangFile(__FILE__);?>
<?$APPLICATION->ShowHead()?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>

<body>

<?
CModule::IncludeModule("iblock");
?>


<?$APPLICATION->ShowPanel();?>



<div class="br">
<div class="Conteiner1">
	
	<div class="Conteiner2">
		<div class="Conteiner3">
			<div class="MenuTop">
				<div class="logo">
                
                
					<div class="L"><a href="#">
                    <?
                    $APPLICATION->IncludeFile(SITE_DIR."modules/logo-macros.php", Array(), Array("MODE" => "html"));
					?>
            
                    
                    
                    </a></div>
                    
                    <!--
					<div class="N"><a href="#">
                    
                     
                    
                    </a></div>
                    -->
				</div>
			</div>
			<div class="Slog color">
            - <?$APPLICATION->IncludeFile(SITE_DIR."modules/slogan-macros.php", Array(), Array("MODE" => "text"));?>
            
            </div>
			<div class="Telefon color">
				<div class="t1 color">
                <?$APPLICATION->IncludeFile(SITE_DIR."modules/twork-macros.php", Array(), Array("MODE" => "text"));?>
            
                
                
                  </div>
				<div class=" color">
                
                <!--
                <span class="f14 t2">(495)</span><span class="f24 t3">646-0174</span>
                -->
                
                <span class="f24 t3">
                <?$APPLICATION->IncludeFile(SITE_DIR."modules/phone-macros.php", Array(), Array("MODE" => "html"));?>

                </span>
                
                
                </div>
			</div>
			</div>
			<div class="MenuCenter">
				<div class="Menu">
					<ul class="f12">
                     <?
            $APPLICATION->IncludeComponent("bitrix:menu", "devtemplatetop", array(
	"ROOT_MENU_TYPE" => "top",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
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
                    
               	</ul>
				</div>
                
                <div class="Seti">
                <ul class="">
                	<li class="lil1"></li>
			
            		</ul>
				</div>
            
			</div>
			<div class="MenuBottom"><img src="<?=SITE_TEMPLATE_PATH?>/images/bg-menu-3.png" border="0"></div>
		<div class="Conteiner4">
			<div class="Conteiner6">
            
			<div class="LeftMenu">
           
				<div class="lm1">
                
                  
                     <!--
					<h1 class="f16">Каталог оборудования</h1>
                    -->
                    
                    <h1 class="f16"><?=GetMessage("CATALOG_EQIP")?></h1>
					
					<div class="bord"></div>
					<ul class="arrow ">
                    
  <?
  //Вытаскиваем ID информационного блока                                     
  $res = CIBlock::GetList(Array(), Array('TYPE'=>'el_products', ),true);
  while($ar_res = $res->Fetch())
 {
	//echo $ar_res['NAME'].': '.$ar_res['ELEMENT_CNT'];
	$arid[]=$ar_res["ID"];
 }
 ?>
          
                    
                     			
                        <?
            $APPLICATION->IncludeComponent("design2u:catalog.section.list", "mytemplate", array(
	"IBLOCK_TYPE" => "el_products",
	"IBLOCK_ID" =>$arid[0],
	"SECTION_ID" => $_REQUEST["SECTION_ID"],
	"SECTION_CODE" => "",
	"COUNT_ELEMENTS" => "Y",
	"TOP_DEPTH" => "2",
	"SECTION_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"SECTION_URL" => "",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "0",
	"CACHE_GROUPS" => "Y",
	"ADD_SECTIONS_CHAIN" => "Y"
	),
	false
);
            ?>
                    
           
            
				</div>    
			</div>
             
            <?
            $APPLICATION->IncludeComponent("bitrix:menu", "devtemplate", array(
	"ROOT_MENU_TYPE" => "left",
	"MENU_CACHE_TYPE" => "N",
	"MENU_CACHE_TIME" => "3600",
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
			<div class="conteiner7">
			<div class="Content">
				<div class ="c1">
				</div>
                  <?
				  
                $APPLICATION->IncludeComponent("bitrix:breadcrumb", "devtemp", array(
	"START_FROM" => "",
	"PATH" => "",
	"SITE_ID" => "-"
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);

?>
              
               
				<div class ="c2">
                
               
                
               <div class ="c3">
                
                
               