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
                    <div class="bit">
                        <?$APPLICATION->IncludeFile(
                            $APPLICATION->GetTemplatePath("include_areas/copyright.php"),
                            Array(),
                            Array("MODE"=>"html")
                        );?>
                    </div>
                    <div class="info">
                    <?  $APPLICATION->IncludeFile(
                        SITE_DIR."include_areas/footer_name.php",
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
        </div>
	</body>
</html>