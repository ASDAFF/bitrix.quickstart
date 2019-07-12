                     <?$APPLICATION->IncludeFile(
                          $APPLICATION->GetTemplatePath("include_areas/center_bottom.php"),
                          Array(),
                          Array("MODE"=>"html")
                      );?>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="footerInner clearfix">
                    <div class="info">
                    <?  $APPLICATION->IncludeFile(
                        SITE_DIR."include_areas/footer_name.php",
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                    </div>
                    <div class="footerR">
                        <div class="counters"><?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/counters.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?></div>
                        <div class="btx"><?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/copyright.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?></div>
                    </div>
                    <?$APPLICATION->IncludeFile(
                        $APPLICATION->GetTemplatePath("include_areas/buttons.php"),
                        Array(),
                        Array("MODE"=>"html")
                    );?>
                </div>
            </div>
        </div>
	</body>
</html>