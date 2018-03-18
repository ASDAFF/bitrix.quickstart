</div>
                
                
				
</div>

</div>
</div>
 
 <?IncludeTemplateLangFile(__FILE__);?>               

	<div class="RightMenu">
				<div class="rm1">
                
                <!--
					<h1 class="f16">Заказать звонок</h1>
                    -->
                    
                    <h1 class="f16"><?=GetMessage('CALL')?></h1>
				
                 
					<div class="bord"></div>
                    <?
            $APPLICATION->IncludeComponent("design2u:orderform", "devtemp", Array(
	
	),
	false
);?>
        
                    
                  </div>
                
                
                
                
				<div class="c33333">
                
                    <!--
                	<h1 class="f16">Последние проекты</h1>
                    -->
                    <h1 class="f16"><?=GetMessage('LAST_PROJECT')?></h1>
                  
                  
                                   <?
  CModule::IncludeModule('iblock');

  //Вытаскиваем ID информационного блока                                     
  $res = CIBlock::GetList(Array(), Array('TYPE'=>'el_news'),true);
  while($ar_res = $res->Fetch())
  {
 	 $aridnn[]=$ar_res["ID"];
  }


?>
   
                  
                    
                                 <?
                             $APPLICATION->IncludeComponent("design2u:news.list", "devtemp1", array(
	"IBLOCK_TYPE" => "el_news",
	"IBLOCK_ID" =>$aridnn[0],
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "ncdate",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);
					      ?>
                    
         
                    
                    
                  </div>
                
				</div>
				
			
		</div>
		<div class="Conteiner8">
			<div class="Left">
            <!--
			<h1 class="f18">Контактная информация</h1>
            -->
            
             <h1 class="f18"><?=GetMessage("CONTACT")?></h1>
	
			<table class="tabl1">
				<tr>
                        <!--
            
					<td class="tabl1t"><span class="s">Адрес :</span></td>
                    -->          
          
                    <td class="tabl1t"><span class="s"><?=GetMessage('ADRESS')?></span></td>
                    
					
					<td class="tabl1t"><span class="s1">
                    
                    <?$APPLICATION->IncludeFile(SITE_DIR."modules/adress-macros.php", Array(), Array("MODE" => "html"));?>

                    </span></td>
				</tr>
				<tr>
                          <!--
          
					<td ><span class="s">Тел :</span></td>
                    	-->
			
                    
                    <td ><span class="s"><?=GetMessage('TEL')?></span></td>
					<td><span class="s1"> 
                    <?$APPLICATION->IncludeFile(SITE_DIR."modules/phone-macros.php", Array(), Array("MODE" => "html"));?>

                    
                    </span></td>
				</tr>
				<tr>
                        <!--
            
					<td ><span class="s">E-mаil :</span></td>
                             -->
			
                    <td ><span class="s"><?=GetMessage('EMAIL')?></span></td>
					<td><span class="s1">
                    <?$APPLICATION->IncludeFile(SITE_DIR."modules/email-macros.php", Array(), Array("MODE" => "html"));?>

                    
                    </span></td>
				</tr>
                
                	<tr>
					<td class="tabl1t"><img src="<?=SITE_TEMPLATE_PATH?>/images/11.png" border="0"></td>
	<td class="tabl1t"><span class="s2"><?$APPLICATION->IncludeFile(SITE_DIR."modules/skype-macros.php", Array(), Array("MODE" => "text"));?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?=SITE_TEMPLATE_PATH?>/images/111.png" border="0"><span>
     <?$APPLICATION->IncludeFile(SITE_DIR."modules/icq-macros.php", Array(), Array("MODE" => "html"));?>  
    </span></td>
				
					
				</tr>
					</table>
			</div>
			<div class="center">
				<table>
					<tr><td>
                    <!--
                    <a href="#<?//=SITE_DIR.'/company'?>">О компании</a>
                    -->
                    </td></tr>
					<tr><td>
                    <!--<a href="#">Генеральное партнерство</a>-->
                    </td></tr>
					<tr><td>
                    <!--
                    <a href="#">Пользовательское соглашение</a>
                    -->
                    </td></tr>
				</table>
			</div>
			<div class="Right">
				<div ><span class="R"><?=GetMessage('DOG')?>                 <?
                  $APPLICATION->IncludeFile(SITE_DIR."modules/copyright-macros.php", Array(), Array("MODE" => "text"));
				  ?>
                
                </span>  <?
                     $APPLICATION->IncludeFile(SITE_DIR."modules/cname-macros.php", Array(), Array("MODE" => "text"));
					 
					 ?>
            </div>
                <!--
            
				<div class="r1"><span class="R">Все авторские права защишены.</span></div>
                -->
                <div class="r1"><span class="R"><?=GetMessage('AUTHORS')?></span></div>
        
                <!--
                
				<div class="r2"><span class="R">Создание сайта -</span> <a href="Design2u.ru">Design2u.ru</a></div>
        --> 
         
               
                	<div class="r2"><span class="R"><?=GetMessage('CREATE_SITE')?></span> 
  <a href="http://design2u.ru/benefits/creation-of-sites/1c-bitrix/"><?=GetMessage('CREATE_SITE_VALUE')?></a></div>
				
			</div>
		</div>
	</div>
	
</div>

</div>
</body>
</html>