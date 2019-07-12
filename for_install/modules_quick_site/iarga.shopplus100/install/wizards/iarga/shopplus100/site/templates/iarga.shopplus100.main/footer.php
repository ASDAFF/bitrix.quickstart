           <?include($templatePath.'/inc/parts/infocard.php')?>
        </div><!--.wrapper-end-->
        <?if(CSite::InDir(SITE_DIR."index.php")) include($templatePath.'/inc/parts/index_bottom.php')?>
    </section><!--.content-end-->


	<footer>
    	<div class="wrapper">
        	<div class="bg">
                <address>
                    <p class="phone"><?$APPLICATION->IncludeFile(SITE_DIR.'/inc/parts/telephone.php')?></p>
                    <i class="sep"></i>
                    <p class="call-order"><a href="#" class="openpopup" data-rel="ordercall"><span><?=GetMessage("ORDER_BACKCALL")?></span></a></p>
                </address>
                <p class="copyright">&copy; <?=date("Y")?> <?=COption::GetOptionString("main","site_name")?></p>
				<?$APPLICATION->IncludeComponent("bitrix:menu", "botmenu", Array(
					"ROOT_MENU_TYPE" => "top",	// Тип меню для первого уровня
					"MENU_CACHE_TYPE" => "N",	// Тип кеширования
					"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
					"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
					"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
					"MAX_LEVEL" => "1",	// Уровень вложенности меню
					"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
					"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
					"DELAY" => "N",	// Откладывать выполнение шаблона меню
					"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
					),
					false
				);?>
                
            </div><!--.bg-end-->
        </div><!--.wrapper-end-->
    </footer>
	<div class="popup"></div>
	<input id="rootfolder" value="<?=SITE_TEMPLATE_PATH?>" type="hidden">
</body>
</html>