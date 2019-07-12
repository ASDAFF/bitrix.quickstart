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
					"ROOT_MENU_TYPE" => "top",	// ��� ���� ��� ������� ������
					"MENU_CACHE_TYPE" => "N",	// ��� �����������
					"MENU_CACHE_TIME" => "3600",	// ����� ����������� (���.)
					"MENU_CACHE_USE_GROUPS" => "Y",	// ��������� ����� �������
					"MENU_CACHE_GET_VARS" => "",	// �������� ���������� �������
					"MAX_LEVEL" => "1",	// ������� ����������� ����
					"CHILD_MENU_TYPE" => "left",	// ��� ���� ��� ��������� �������
					"USE_EXT" => "N",	// ���������� ����� � ������� ���� .���_����.menu_ext.php
					"DELAY" => "N",	// ����������� ���������� ������� ����
					"ALLOW_MULTI_SELECT" => "N",	// ��������� ��������� �������� ������� ������������
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