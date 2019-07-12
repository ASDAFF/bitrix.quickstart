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
   //��������� � ��������� �����
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
	"ROOT_MENU_TYPE" => "top",	// ��� ���� ��� ������� ������
	"MENU_CACHE_TYPE" => "N",	// ��� �����������
	"MENU_CACHE_TIME" => "3600",	// ����� ����������� (���.)
	"MENU_CACHE_USE_GROUPS" => "N",	// ��������� ����� �������
	"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
	"MAX_LEVEL" => "1",	// ������� ����������� ����
	"CHILD_MENU_TYPE" => "",	// ��� ���� ��� ��������� �������
	"USE_EXT" => "N",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
	"DELAY" => "N",	// ����������� ���������� ������� ����
	"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
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
	"NUM_CATEGORIES" => "1",	// ���������� ��������� ������
	"TOP_COUNT" => "5",	// ���������� ����������� � ������ ���������
	"ORDER" => "date",	// ���������� �����������
	"USE_LANGUAGE_GUESS" => "Y",	// �������� ��������������� ��������� ����������
	"CHECK_DATES" => "N",	// ������ ������ � �������� �� ���� ����������
	"SHOW_OTHERS" => "Y",	// ���������� ��������� "������"
	"PAGE" => "#SITE_DIR#search/index.php",	// �������� ������ ����������� ������ (�������� ������ #SITE_DIR#)
	"CATEGORY_OTHERS_TITLE" => "������",	// �������� ���������
	"CATEGORY_0_TITLE" => "�������",	// �������� ���������
	"CATEGORY_0" => array(	// ����������� ������� ������
		0 => "iblock_catalog",
	),
	"CATEGORY_0_iblock_catalog" => array(	// ������ � �������������� ������ ���� "iblock_catalog"
		0 => "6",
	),
	"SHOW_INPUT" => "Y",	// ���������� ����� ����� ���������� �������
	"INPUT_ID" => "title-search-input",	// ID ������ ����� ���������� �������
	"CONTAINER_ID" => "title-search",	// ID ����������, �� ������ �������� ����� ���������� ����������
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
								 "change_password"));?>">�����</a></li>
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
	"ROOT_MENU_TYPE" => "left",	// ��� ���� ��� ������� ������
	"MENU_CACHE_TYPE" => "A",	// ��� �����������
	"MENU_CACHE_TIME" => "36000000",	// ����� ����������� (���.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
	"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
	"MAX_LEVEL" => "4",	// ������� ����������� ����
	"CHILD_MENU_TYPE" => "",	// ��� ���� ��� ��������� �������
	"USE_EXT" => "N",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
	"DELAY" => "N",	// ����������� ���������� ������� ����
	"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
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
	"ROOT_MENU_TYPE" => "catalog",	// ��� ���� ��� ������� ������
	"MENU_CACHE_TYPE" => "A",	// ��� �����������
	"MENU_CACHE_TIME" => "36000000",	// ����� ����������� (���.)
	"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
	"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
	"MAX_LEVEL" => "4",	// ������� ����������� ����
	"CHILD_MENU_TYPE" => "left",	// ��� ���� ��� ��������� �������
	"USE_EXT" => "Y",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
	"DELAY" => "N",	// ����������� ���������� ������� ����
	"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
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
	"START_FROM" => "",	// ����� ������, ������� � �������� ����� ��������� ������������� �������
	"PATH" => "",	// ����, ��� �������� ����� ��������� ������������� ������� (�� ���������, ������� ����)
	"SITE_ID" => "-",	// C��� (��������������� � ������ ������������� ������, ����� DOCUMENT_ROOT � ������ ������)
	),
	false
);
					?>
					<div class="title">
	                    <h2><?$APPLICATION->ShowTitle();?></h2>
	            	</div>
				<?}?>