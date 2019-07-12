<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=LANGUAGE_ID?>" lang="<?=LANGUAGE_ID?>">
<head>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/script.js"></script>
<?$APPLICATION->ShowHead();?>
<?
$site_name=file_get_contents($_SERVER['DOCUMENT_ROOT'].SITE_DIR."include/company_name.php");
$browserTitle=$APPLICATION->GetPageProperty("browser_title");
if(!$browserTitle)
{
	$APPLICATION->SetPageProperty("browser_title", $APPLICATION->GetPageProperty("title"));
}
?>
<title><?=$APPLICATION->ShowTitle("browser_title")?> - <?=$site_name?></title>

<link href="<?=SITE_TEMPLATE_PATH?>/layout.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(document).ready(function() {   
    $('#center .filter form .dropdown select').change(function () {
    	this.parentNode.getElementsByTagName('input')[0].value = this.options[this.selectedIndex].innerHTML;
    });
   //подсказка в текстовых полях
    $('.input-text input[type="text"]').each(function(){
        if($(this).val() != '') $(this).prev().addClass('hide');
    });
    $('.input-text input[type="text"]').blur(function() {
        if ($(this).val() == '') $(this).prev().removeClass('hide');
    });
    $('.input-text input[type="text"]').focus(function() {
        $(this).prev().addClass('hide');
    });
    $('.input-text input[type="text"]').mouseover(function() {
        if ($(this).val() != '') $(this).prev().addClass('hide');
    });
});
function CorrectHeader() {
	var text = $('#logo h1');
	var textLen = text.html().length;
	if (textLen > 20) {
		text[0].style.paddingTop="33px";
		text[0].style.paddingBottom="15px";
	}
}
</script>
</head>

<body id="page1">
<div style="position: relative; min-width: 1000px;">
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
</div>
<div id="layer">
	<div id="main" style="position: relative;">
	
		<!-- header -->
		<div id="header">
			<div class="row-1">
         	<div class="fleft">
                <a href="<?=SITE_DIR?>">
				<div id="logo">
                    <h1><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?></h1>
				</div>
				</a>
            </div>
            <div class="fright">
            	<div id="site-nav">
				    <?$APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
	"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
	"MENU_CACHE_TYPE" => "N",	// Тип кеширования
	"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
	"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
	"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
	"MAX_LEVEL" => "1",	// Уровень вложенности меню
	"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
	"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	"DELAY" => "N",	// Откладывать выполнение шаблона меню
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);?>
<script>CorrectHeader()</script>
               </div>
               <div class="head_info">
               		<div class="contacts">
               			<div class="phone"><em><?=GetMessage('OUR_PHONE')?></em> <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></div>
						<div class="clear"></div>
						<div class="icq"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/icq.php"), false);?></div>
						<div class="clear"></div>
					</div>
					<div id="cart_line">
					    <?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", ".default", array(
	"PATH_TO_BASKET" => "/personal/cart/",
	"PATH_TO_ORDER" => "/personal/order/make/"
	),
	false
);?>
                    </div>
               		<div style="clear:both"></div>
               </div>
            </div>
         </div>
			<div class="row-2">
         	<div id="search-box">
            	<div class="left">
               	<div class="right">
                  	<div class="inner">
                     	<div class="fleft">
                        	<?$APPLICATION->IncludeComponent("bitrix:search.title", "header", Array(
	"NUM_CATEGORIES" => "1",	// Количество категорий поиска
	"TOP_COUNT" => "5",	// Количество результатов в каждой категории
	"ORDER" => "date",	// Сортировка результатов
	"USE_LANGUAGE_GUESS" => "Y",	// Включить автоопределение раскладки клавиатуры
	"CHECK_DATES" => "N",	// Искать только в активных по дате документах
	"SHOW_OTHERS" => "Y",	// Показывать категорию "прочее"
	"PAGE" => "#SITE_DIR#search/index.php",	// Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
	"CATEGORY_OTHERS_TITLE" => "Прочее",	// Название категории
	"CATEGORY_0_TITLE" => "Каталог",	// Название категории
	"CATEGORY_0" => array(	// Ограничение области поиска
		0 => "iblock_catalog",
	),
	"CATEGORY_0_iblock_catalog" => array(	// Искать в информационных блоках типа "iblock_catalog"
		0 => "6",
	),
	"SHOW_INPUT" => "Y",	// Показывать форму ввода поискового запроса
	"INPUT_ID" => "title-search-input",	// ID строки ввода поискового запроса
	"CONTAINER_ID" => "title-search",	// ID контейнера, по ширине которого будут выводиться результаты
	),
	false
);?>
                        </div>
						<?global $USER;
						if ($USER->IsAuthorized()){?>
						<ul class="extra-search">
                        	<li><a href="<?=SITE_DIR?>personal/"><?=GetMessage('PRSONAL')?></a></li>
                            <li><a href="<?echo $APPLICATION->GetCurPageParam("logout=yes", array(
								 "login",
								 "logout",
								 "register",
								 "forgot_password",
								 "change_password"));?>">Выход</a></li>
                        </ul>						
							<?							
							if(!$USER->GetEmail()):?>
								<div class="withoutEmail">
									<div class="cloud">
										<div class="text"><?=str_replace('#SITE_DIR#',SITE_DIR,GetMessage('WITHOUT_EMAIL'))?></div>
									</div>
								</div>
							<?endif;
						}
						else{?>
						<ul class="extra-search">
                        	<li><a href="<?=SITE_DIR?>login/"><?=GetMessage('AUTH')?></a></li>
                            <li><a href="<?=SITE_DIR?>login/?register=yes"><?=GetMessage('REGISTER')?></a></li>
                        </ul>
						<?}?>
                        
                     </div>
                  </div>
               </div>
            </div>
         </div>
		</div>
		<!-- content -->
		<div id="content">
			<div class="wrapper">
            <div id="sidebar">
            	<div class="box">
               	<div class="left-top-corner">
                  	<div class="right-top-corner">
                     	<div class="right-bot-corner">
                        	<div class="left-bot-corner">
                           	<div class="inner">
							    <div class="box1 boxleftmenu">
									<?$APPLICATION->IncludeComponent("bitrix:menu", "vertical_multilevel", Array(
	"ROOT_MENU_TYPE" => "left",	// Тип меню для первого уровня
	"MENU_CACHE_TYPE" => "A",	// Тип кеширования
	"MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
	"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
	"MAX_LEVEL" => "4",	// Уровень вложенности меню
	"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
	"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	"DELAY" => "N",	// Откладывать выполнение шаблона меню
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);?>
                                 </div>
                              	<!-- title begin -->
                              	<div class="title-box">
                                 	<div class="left">
                                    	<div class="right">
                                       		<h2><?=GetMessage('CATALOG_TITLE')?></h2>
                                       	</div>
                                    </div>
                                 </div>
                                 <!-- title end -->
                                 <div class="box1">
									<?$APPLICATION->IncludeComponent("bitrix:menu", "vertical_multilevel", Array(
	"ROOT_MENU_TYPE" => "catalog",	// Тип меню для первого уровня
	"MENU_CACHE_TYPE" => "A",	// Тип кеширования
	"MENU_CACHE_TIME" => "36000000",	// Время кеширования (сек.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
	"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
	"MAX_LEVEL" => "4",	// Уровень вложенности меню
	"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
	"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	"DELAY" => "N",	// Откладывать выполнение шаблона меню
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);?>
                                 </div>
                                 <?$APPLICATION->IncludeComponent(
												"bitrix:main.include",
												"",
												Array(
													"AREA_FILE_SHOW" => "file",
													"PATH" => SITE_DIR."include/youHaveSeen.php"
												)
											);?>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="center">
			    <?if($APPLICATION->GetCurDir()!=SITE_DIR){?>
				    <div class="teamplate">
				    <?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", Array(
	"START_FROM" => "",	// Номер пункта, начиная с которого будет построена навигационная цепочка
	"PATH" => "",	// Путь, для которого будет построена навигационная цепочка (по умолчанию, текущий путь)
	"SITE_ID" => "-",	// Cайт (устанавливается в случае многосайтовой версии, когда DOCUMENT_ROOT у сайтов разный)
	),
	false
);
					?>
					<div class="title">
	                    <h2><?$APPLICATION->ShowTitle();?></h2>
	            	</div>
				<?}?>