<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
                        <?if ($APPLICATION->GetCurPage() == SITE_DIR):?>
                         <?$APPLICATION->IncludeFile(
                          $APPLICATION->GetTemplatePath("include_areas/center_bottom.php"),
                          Array(),
                          Array("MODE"=>"html")
                         );?>
                         <?endif;?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer clearfix">
			<div class="contentBL"></div>
			<div class="contentBR"></div>
			<div class="shadowBot"></div>            
                <div class="info">
                    <?  $APPLICATION->IncludeFile(
                        SITE_DIR."include_areas/footer_name.php",
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                </div>
                <div class="bit">
                    <?$APPLICATION->IncludeFile(
                        $APPLICATION->GetTemplatePath("include_areas/copyright.php"),
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                </div>
                <div class="contacts">
                    <?$APPLICATION->IncludeFile(
                        $APPLICATION->GetTemplatePath("include_areas/buttons.php"),
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                    <?  $APPLICATION->IncludeFile(
                        SITE_DIR."include_areas/schooladdress.php",
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                </div>
            </div>
        </div>
	</body>
</html>
